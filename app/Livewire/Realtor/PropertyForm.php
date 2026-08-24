<?php

namespace App\Livewire\Realtor;

use App\Data\NigeriaLocations;
use App\Models\PropertiesTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class PropertyForm extends Component
{
    use WithFileUploads;

    public ?int $propertyId = null;

    // Basic Info
    public string $title        = '';
    public string $type         = '';
    public string $listing_type = '';
    public string $price        = '';
    public string $description  = '';

    // Location
    public string $state        = '';
    public string $lga          = '';
    public string $city         = '';
    public string $street       = '';
    public string $neighborhood = '';
    public string $country      = 'Nigeria';

    // Amenities
    public array $amenities = [];

    // Media — existing (URLs already on S3) and new uploads
    public array $existingPhotos = [];
    public array $existingVideos = [];
    public array $photos         = [];
    public array $videos         = [];

    public bool   $saved     = false;
    public string $saveError = '';

    public array $availableAmenities = [
        'Swimming Pool', 'Generator', 'Water Supply', 'Security/Guard',
        'CCTV', 'Gym/Fitness Center', 'Parking Space', 'Elevator',
        'Air Conditioning', 'Internet/WiFi', 'Boys Quarter', 'Furnished',
        'Garden/Lawn', 'Fence/Wall', 'Solar Power', 'Laundry Room',
        'Balcony', 'Smart Home', 'Fire Safety System', 'Intercom',
    ];

    public function mount(?int $propertyId = null): void
    {
        $this->propertyId = $propertyId;

        if ($propertyId) {
            $property = PropertiesTable::where('user_id', Auth::id())
                ->findOrFail($propertyId);

            $this->title         = $property->title;
            $this->type          = $property->type;
            $this->listing_type  = $property->listing_type;
            $this->price         = $property->price;
            $this->description   = $property->description;
            $this->state         = $property->state;
            $this->lga           = $property->localarea ?? '';
            $this->city          = $property->city;
            $this->street        = $property->street;
            $this->neighborhood  = $property->neigborhood ?? '';
            $this->country       = $property->country;
            $this->amenities     = $property->amenities ?? [];
            $this->existingPhotos = $property->photos ?? [];
            $this->existingVideos = $property->videos ?? [];
        }
    }

    #[Computed]
    public function states(): array
    {
        return NigeriaLocations::states();
    }

    #[Computed]
    public function lgas(): array
    {
        return $this->state ? NigeriaLocations::lgasFor($this->state) : [];
    }

    public function updatedState(): void
    {
        $this->lga = '';
    }

    public function removeExistingPhoto(int $index): void
    {
        array_splice($this->existingPhotos, $index, 1);
    }

    public function removeExistingVideo(int $index): void
    {
        array_splice($this->existingVideos, $index, 1);
    }

    public function removePhoto(int $index): void
    {
        array_splice($this->photos, $index, 1);
    }

    public function removeVideo(int $index): void
    {
        array_splice($this->videos, $index, 1);
    }

    public function submit(): void
    {
        $this->saveError = '';
        $isEdit = (bool) $this->propertyId;

        $this->validate([
            'title'        => 'required|string|max:200',
            'type'         => 'required|string',
            'listing_type' => 'required|string',
            'price'        => 'required|string|max:50',
            'description'  => 'required|string|min:20',
            'state'        => 'required|string',
            'lga'          => 'required|string',
            'city'         => 'required|string|max:100',
            'street'       => 'required|string|max:200',
            'neighborhood' => 'required|string|max:100',
            'country'      => 'required|string',
            // Photos required only when creating and no existing photos
            'photos'       => $isEdit ? 'nullable|array' : 'required|array|min:1',
            'photos.*'     => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'videos'       => 'nullable|array',
            'videos.*'     => 'nullable|file|mimes:mp4,mov,avi,webm|max:102400',
        ], [
            'photos.required' => 'At least one property photo is required.',
            'photos.min'      => 'At least one property photo is required.',
            'photos.*.max'    => 'Each photo must not exceed 10 MB.',
            'videos.*.max'    => 'Each video must not exceed 100 MB.',
        ]);

        // Additional check for edit: must have at least one photo total
        if ($isEdit && empty($this->existingPhotos) && empty($this->photos)) {
            $this->addError('photos', 'At least one property photo is required.');
            return;
        }

        try {
            // Upload new photos and merge with kept existing ones
            $finalPhotos = $this->existingPhotos;
            foreach ($this->photos as $photo) {
                $path = $photo->store('photos', 'photoaws');
                $finalPhotos[] = Storage::disk('photoaws')->url($path);
            }

            $finalVideos = $this->existingVideos;
            foreach ($this->videos as $video) {
                $path = $video->store('videos', 'videoaws');
                $finalVideos[] = Storage::disk('videoaws')->url($path);
            }

            $data = [
                'title'          => $this->title,
                'type'           => $this->type,
                'listing_type'   => $this->listing_type,
                'price'          => $this->price,
                'description'    => $this->description,
                'state'          => $this->state,
                'localarea'      => $this->lga,
                'city'           => $this->city,
                'street'         => $this->street,
                'neigborhood'    => $this->neighborhood,
                'country'        => $this->country,
                'amenities'      => $this->amenities,
                'photos'         => $finalPhotos,
                'videos'         => $finalVideos,
            ];

            if ($isEdit) {
                PropertiesTable::where('user_id', Auth::id())
                    ->where('id', $this->propertyId)
                    ->update($data);
            } else {
                PropertiesTable::create(array_merge($data, [
                    'user_id'         => Auth::id(),
                    'listing_status'  => 'active',
                    'approval_status' => 'pending',
                    'views'           => 0,
                ]));
            }

            $this->reset(['photos', 'videos']);
            $this->saved = true;

        } catch (\Throwable $e) {
            $this->saveError = 'Something went wrong. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.realtor.property-form');
    }
}
