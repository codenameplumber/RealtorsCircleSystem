<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Human-facing web pages behind Laravel's signed-URL protection — these are
 * the links n8n sends over WhatsApp (via the Api\Whatsapp\* controllers) so a
 * realtor can complete KYC or attach property photos from their phone browser
 * without ever needing a password. The signature itself is the credential;
 * anyone without a valid, unexpired link cannot reach these forms.
 */
class WhatsappUploadController extends Controller
{
    public function showKyc(Request $request, int $id)
    {
        $user = UsersTable::findOrFail($id);

        return view('whatsapp.kyc-upload', ['user' => $user]);
    }

    public function submitKyc(Request $request, int $id)
    {
        $user = UsersTable::findOrFail($id);

        $request->validate([
            'kyc_id_type' => 'required|string|max:60',
            'kyc_front'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kyc_back'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'kyc_front.required' => 'Please upload the front of your ID.',
        ]);

        // Store the object key rather than a public URL: the kycaws disk is
        // private, and these documents are read back through short-lived
        // presigned links (UsersTable::getKycFrontLinkAttribute).
        $frontKey = $request->file('kyc_front')->store('front', 'kycaws');

        $backKey = $user->kyc_back_url;
        if ($request->hasFile('kyc_back')) {
            $backKey = $request->file('kyc_back')->store('back', 'kycaws');
        }

        $user->update([
            'kyc_id_type'   => $request->kyc_id_type,
            'kyc_front_url' => $frontKey,
            'kyc_back_url'  => $backKey,
            'kyc_status'    => 'pending',
        ]);

        return view('whatsapp.kyc-upload-success', ['user' => $user]);
    }

    public function showPropertyPhotos(Request $request, int $id)
    {
        $property = PropertiesTable::findOrFail($id);

        return view('whatsapp.property-photos-upload', ['property' => $property]);
    }

    public function submitPropertyPhotos(Request $request, int $id)
    {
        $property = PropertiesTable::findOrFail($id);

        $request->validate([
            'photos'   => 'required|array|min:1|max:20',
            'photos.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'photos.required' => 'Please choose at least one photo.',
        ]);

        $existing = is_array($property->photos) ? $property->photos : [];

        foreach ($request->file('photos') as $photo) {
            $path       = $photo->store('photos', 'photoaws');
            $existing[] = Storage::disk('photoaws')->url($path);
        }

        $property->update(['photos' => $existing]);

        return view('whatsapp.property-photos-success', ['property' => $property]);
    }
}
