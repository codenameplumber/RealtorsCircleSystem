<?php

namespace App\Http\Controllers\Api\Users\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertiesTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // For generating unique file names

class PropertyController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255', // e.g., "Apartment", "House", "Land"
            'description' => 'required|string',
            'listing_type' => 'required|string|in:For Sale,For Rent,Shortlet', // Ensure specific types
            'street' => 'required|string|max:255',
            'neigborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'localarea' => 'required|string|max:255', // Assuming this can be optional
            'country' => 'required|string|max:255',
            'amenities' => 'required', // Can be an empty array
            'amenities.*' => 'string|max:255', // Each amenity is a string
            'price' => 'required|numeric|min:0',
            'photos' => 'required|min:5', // Minimum 5 photos
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10250', // Each photo must be an image, max 2MB
            'videos' => 'required',
            'videos.*' => 'required|file|mimes:mp4,mov,ogg,qt|max:51250', // Each video can be a file (max 20MB) OR a URL
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        #dd($request->file('photos'),$request->file('videos'));

        // 2. Handle Photo Uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
             foreach ($request->file('photos') as $photo) {
                // Generate a unique file name
                $fileName = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                $path = Storage::disk('photoaws')->putFile('photos', $photo); // 'public' ensures public readability on S3
                $photoPaths[] = rtrim(config('filesystems.disks.photoaws.url'), '/')."/properties/".$path; // Get the public URL for the stored image
           }
        }

        // 3. Handle Video Uploads or URLs
        $videoSources = [];
        if ($request->has('videos')) {
            foreach ($request->videos as $video) {
                if ($video instanceof \Illuminate\Http\UploadedFile) {
                    // It's a file upload
                    $fileName = time() . '_' . Str::random(10) . '.' . $video->getClientOriginalExtension();
                    $path = Storage::disk('videoaws')->putFile('videos', $video); // 'public' ensures public readability on S3
                    $videoSources[] = rtrim(config('filesystems.disks.videoaws.url'), '/')."/properties/".$path; // Get the public URL for the stored video

                } elseif (is_string($video) && filter_var($video, FILTER_VALIDATE_URL)) {
                    // It's a URL
                    $videoSources[] = $video;
                } else {
                    // Handle invalid video input if necessary, maybe skip or log
                    // For now, it will be caught by the validator's 'url' rule if it's not a file
                    continue;
                }
            }
        }


        // 4. Create the Property Record
        try {
            $property = PropertiesTable::create([
                'user_id' => Auth::id(), // Get authenticated user's ID
                'title' => $request->title,
                'type' => $request->type,
                'description' => $request->description,
                'listing_type' => $request->listing_type,
                'street' => $request->street,
                'neigborhood' => $request->neigborhood,
                'city' => $request->city,
                'state' => $request->state,
                'localarea' => $request->localarea,
                'country' => $request->country,
                'amenities' => $request->amenities ?? [], // Store as JSON array, default to empty
                'price' => $request->price,
                'photos' => $photoPaths, // Store array of photo URLs
                'videos' => $videoSources, // Store array of video URLs/paths
                'listing_status' => 'Available', // Default status, adjust as needed
                'approval_status' => 'Pending', // Default status, adjust as needed
                'views' => 0, // Initial views
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Property uploaded successfully',
                'property' => $property
            ], 201); // 201 Created

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error uploading property: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload property. Please try again.',
            ], 500); // 500 Internal Server Error
        }
    }
}