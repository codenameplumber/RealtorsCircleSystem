<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Users\Auth\RegisterController; //Registration API
use App\Http\Controllers\Api\Users\Profile; //Profile Update Controller
use App\Http\Controllers\Api\Users\Property\PropertyController; //Properties Upload controller
use App\Http\Controllers\Api\Whatsapp\RealtorController as WaRealtorController;
use App\Http\Controllers\Api\Whatsapp\AgentController as WaAgentController;
use App\Http\Controllers\Api\Whatsapp\AgencyController as WaAgencyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('rc/v1/auth')->group(function () {
    // Public routes for registration steps and login
    Route::post('/register', [RegisterController::class, 'step1'])->middleware('throttle:register');
    Route::post('/login', [RegisterController::class, 'login'])->middleware('throttle:login');
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])
        ->middleware('throttle:api')
        ->name('users.auth.verification.verify');

    //Password Management
     // Password Reset Routes
    Route::post('/forgot-password', [RegisterController::class, 'forgotPassword'])->middleware('throttle:password-reset'); // For requesting reset email
    Route::post('/reset-password', [RegisterController::class, 'resetPassword'])->middleware('throttle:api');

    // Protected routes (require authentication using the 'api' guard)
    Route::middleware(['auth:api', 'throttle:api-auth'])->group(function () {

        #======UPDATE USERS PROFILE INFORMATION======#
        Route::post('/register/step2', [Profile::class, 'step2']);
        Route::post('/register/step3', [Profile::class, 'step3']);

        #===========UPLOAD PROPERTIES========#
        Route::post('/properties/add', [PropertyController::class, 'store'])->name('properties.store');

        // Example: User data route for authenticated users
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});

/*
|--------------------------------------------------------------------------
| WhatsApp / n8n-facing API
|--------------------------------------------------------------------------
|
| Called exclusively by our n8n workflows, never a browser or mobile app.
| Gated by a single shared X-API-Key header (see VerifyWhatsappApiKey) —
| individual identity within these routes is resolved by WhatsApp phone
| number, since WhatsApp itself is the auth factor on the n8n side.
|
*/
Route::prefix('wa/v1')->middleware(['whatsapp.key', 'throttle:whatsapp-api'])->group(function () {

    Route::prefix('realtor')->group(function () {
        Route::post('/register', [WaRealtorController::class, 'register']);
        Route::post('/kyc-link', [WaRealtorController::class, 'kycLink']);
        Route::post('/identify', [WaRealtorController::class, 'identify']);
        Route::post('/properties', [WaRealtorController::class, 'createProperty']);
        Route::post('/properties/{id}/photo-link', [WaRealtorController::class, 'photoLink']);
        Route::patch('/properties/{id}/status', [WaRealtorController::class, 'updatePropertyStatus']);
        Route::get('/appointments', [WaRealtorController::class, 'appointments']);
        Route::post('/appointments/{id}/approve', [WaRealtorController::class, 'approveAppointment']);
        Route::post('/appointments/{id}/reject', [WaRealtorController::class, 'rejectAppointment']);
        Route::get('/search', [WaRealtorController::class, 'search']);
        Route::get('/stats', [WaRealtorController::class, 'stats']);
        Route::get('/weekly-report', [WaRealtorController::class, 'weeklyReport']);
    });

    Route::prefix('agent')->group(function () {
        Route::post('/identify', [WaAgentController::class, 'identify']);
        Route::get('/realtors/pending', [WaAgentController::class, 'pendingRealtors']);
        Route::post('/realtors/{id}/approve', [WaAgentController::class, 'approveRealtor']);
        Route::post('/realtors/{id}/reject', [WaAgentController::class, 'rejectRealtor']);
        Route::get('/properties/pending', [WaAgentController::class, 'pendingProperties']);
        Route::post('/properties/{id}/approve', [WaAgentController::class, 'approveProperty']);
        Route::post('/properties/{id}/reject', [WaAgentController::class, 'rejectProperty']);
        Route::patch('/properties/{id}/status', [WaAgentController::class, 'updatePropertyStatus']);
        Route::get('/appointments', [WaAgentController::class, 'appointments']);
        Route::get('/stats/weekly', [WaAgentController::class, 'weeklyStats']);
    });

    Route::prefix('agency')->group(function () {
        Route::post('/identify', [WaAgencyController::class, 'identify']);
        Route::get('/network', [WaAgencyController::class, 'network']);
        Route::get('/earnings', [WaAgencyController::class, 'earnings']);
        Route::get('/stats/weekly', [WaAgencyController::class, 'weeklyStats']);
    });
});