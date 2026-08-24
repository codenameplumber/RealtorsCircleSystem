<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersTable;
use App\Models\BankInformation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\Users\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth; // For Auth::attempt
use GuzzleHttp\Client; // For IP Geolocation

class Profile extends Controller
{
     #==========STEP 2 VERIFICATION========#
    public function step2(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->email_verified_at) {
            return response()->json(['message' => 'Unauthorized or previous step not completed.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'Address' => ['required', 'string', 'max:255'],
            'State' => ['required', 'string', 'max:255'],
            'Local_area' => ['required', 'string', 'max:255'],
            'Country' => ['required', 'string', 'max:255'],
            'Specialization' => ['required', 'string', 'max:255'],
            'Yrs_experience' => ['required', 'string', 'max:255'],
            'Bio' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $ipAddress = $request->ip();
            $country = $request->Country;

            if (!$country || $country === 'Unknown') {
                try {
                    $client = new Client();
                    $response = $client->get("http://ip-api.com/json/{$ipAddress}");
                    $data = json_decode($response->getBody(), true);

                    if ($data['status'] == 'success' && isset($data['country'])) {
                        $country = $data['country'];
                    } else {
                        $country = 'Nigeria'; // Default to Nigeria
                    }
                } catch (\Exception $e) {
                    \Log::warning("IP Geolocation failed for IP: {$ipAddress}. Error: " . $e->getMessage());
                    $country = 'Nigeria';
                }
            }

            $user->update([
                'Address' => $request->Address,
                'State' => $request->State,
                'Local_area' => $request->Local_area,
                'Country' => $country,
                'Specialization' => $request->Specialization,
                'Yrs_experience' => $request->Yrs_experience,
                'Bio' => $request->Bio,
                'steps_signup' => 'step2_completed',
            ]);

            // **EMAIL FOR STEP 2 COMPLETION**
            Mail::to($user->Email)->send(new EmailVerificationMail(
                $user,
                null,
                'Address Details Confirmed: Almost There!', // Encouraging subject
                "Excellent work, " . $user->First_name . "! Your address and contact information have been successfully updated. You're making great progress! There's just one more quick step to complete your registration: providing your bank details." // Affirmative and guiding message
            ));

            return response()->json([
                'message' => 'Registration step 2 successful. Address information saved.',
                'user_id' => $user->id,
                'next_step' => 'step3_bank_information',
                'status' => 'step2_completed'
            ], 200);

        } catch (\Exception $e) {
            #\Log::error("Registration Step 2 Error: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred during step 2. Please try again.'], 500);
        }
    }


    #========STEP 3 BANK INFORMATION==========#
    public function step3(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->steps_signup != 'step2_completed') {
            return response()->json(['message' => 'Unauthorized or previous step not completed.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'Account_Name' => ['required', 'string', 'max:255'],
            'Bvn' => ['required', 'string', 'digits:11', 'unique:bank_information'],
            'Bank_Name' => ['required', 'string', 'max:255'],
            'Bank_Account' => ['required', 'string', 'digits:10', 'unique:bank_information'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            BankInformation::create([
                'user_id' => $user->id,
                'Account_Name' => $request->Account_Name,
                'Bvn' => $request->Bvn,
                'Bank_Name' => $request->Bank_Name,
                'Bank_Account' => $request->Bank_Account,
                'status' => 'pending_verification',
            ]);

            // Completing the bank-details step must NOT activate the account.
            // Every other registration path leaves Status as 'pending' for an
            // admin to approve; self-activation here let an API caller skip
            // review entirely and go straight to listing properties.
            $user->update([
                'steps_signup' => 'registration_complete',
            ]);

            // **EMAIL FOR STEP 3: REGISTRATION COMPLETE**
            Mail::to($user->Email)->send(new EmailVerificationMail(
                $user,
                null,
                'Success! Your Registration is Complete and You\'re Ready to Go!', // Enthusiastic completion subject
                "Congratulations, " . $user->First_name . "! You have successfully completed all steps of your registration process with " . config('app.name') . ". Welcome to our vibrant community! You can now fully access all features and services, and your journey with us officially begins. We're excited to have you!" // Warm and welcoming message
            ));

            return response()->json([
                'message' => 'Registration complete. Bank information saved. Your account is pending admin approval.',
                'user_id' => $user->id,
                'status' => 'registration_complete',
                'account_status' => $user->Status,
            ], 200);

        } catch (\Exception $e) {
            #\Log::error("Registration Step 3 Error: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred during step 3. Please try again.'], 500);
        }
    }
}
