<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Villa;
use App\Models\VillaPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VillaController extends Controller
{
    /**
     * Public listing with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Villa::with(['owner:id,name,avatar', 'photos', 'amenities'])
            ->approved();

        // Search by city / keyword
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                  ->orWhere('city', 'like', "%{$request->q}%")
                  ->orWhere('country', 'like', "%{$request->q}%")
                  ->orWhere('address', 'like', "%{$request->q}%");
            });
        }

        // Filters
        if ($request->filled('city'))          $query->where('city', $request->city);
        if ($request->filled('country'))       $query->where('country', $request->country);
        if ($request->filled('min_price'))     $query->where('price_per_night', '>=', $request->min_price);
        if ($request->filled('max_price'))     $query->where('price_per_night', '<=', $request->max_price);
        if ($request->filled('guests'))        $query->where('max_guests', '>=', $request->guests);
        if ($request->filled('bedrooms'))      $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->filled('bathrooms'))     $query->where('bathrooms', '>=', $request->bathrooms);
        if ($request->filled('min_rating'))    $query->where('rating', '>=', $request->min_rating);

        // Availability check
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereDoesntHave('reservations', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'approved'])
                  ->where('check_in', '<', $request->check_out)
                  ->where('check_out', '>', $request->check_in);
            });
        }

        // Amenities filter
        if ($request->filled('amenities')) {
            $ids = explode(',', $request->amenities);
            foreach ($ids as $id) {
                $query->whereHas('amenities', fn($q) => $q->where('amenities.id', $id));
            }
        }

        // Sorting
        $sort = match($request->get('sort', 'latest')) {
            'price_asc'  => ['price_per_night', 'asc'],
            'price_desc' => ['price_per_night', 'desc'],
            'rating'     => ['rating', 'desc'],
            default      => ['created_at', 'desc'],
        };
        $query->orderBy(...$sort);

        return response()->json($query->paginate(12));
    }

    /**
     * Public show single villa.
     */
    public function show(Villa $villa): JsonResponse
    {
        abort_unless($villa->status === Villa::STATUS_APPROVED, 404);

        $villa->load(['owner:id,name,avatar,bio,created_at', 'photos', 'amenities', 'tags',
            'reviews' => fn($q) => $q->with('user:id,name,avatar')->approved()->latest()->limit(10)
        ]);

        return response()->json($villa);
    }

    /**
     * Owner: list my villas.
     */
    public function myVillas(Request $request): JsonResponse
    {
        $villas = $request->user()->villas()
            ->with([
                'photos' => fn($q) => $q->orderByDesc('is_cover')->orderBy('sort_order'),
                'amenities:id,name,price,owner_id',
            ])
            ->withCount(['reservations', 'reviews'])
            ->latest()
            ->paginate(10);

        return response()->json($villas);
    }

    /**
     * Owner: create villa.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'               => 'required|string|max:200',
            'description'         => 'required|string|min:50',
            'address'             => 'required|string',
            'city'                => 'required|string|max:100',
            'country'             => 'required|string|max:100',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'listing_type'        => 'required|in:vente,location',
            'price'               => 'required|numeric|min:1',
            'price_per_night'     => 'required|numeric|min:1',
            'weekend_price'       => 'nullable|numeric|min:1',
            'cleaning_fee'        => 'nullable|numeric|min:0',
            'security_deposit'    => 'nullable|numeric|min:0',
            'max_guests'          => 'required|integer|min:1|max:50',
            'bedrooms'            => 'required|integer|min:0',
            'bathrooms'           => 'required|integer|min:1',
            'surface'             => 'nullable|numeric|min:1',
            'rooms_count'         => 'nullable|integer|min:1',
            'has_pool'            => 'boolean',
            'has_garden'          => 'boolean',
            'has_garage'          => 'boolean',
            'has_air_conditioning'=> 'boolean',
            'has_wifi'            => 'boolean',
            'exceptional_equipments' => 'nullable|array',
            'exceptional_equipments.*.code' => 'required_with:exceptional_equipments|string|max:100',
            'exceptional_equipments.*.label' => 'required_with:exceptional_equipments|string|max:120',
            'exceptional_equipments.*.price' => 'required_with:exceptional_equipments|numeric|min:0',
            'exceptional_equipments_total' => 'nullable|numeric|min:0',
            'min_nights'          => 'required|integer|min:1',
            'max_nights'          => 'nullable|integer|min:1',
            'cancellation_policy' => 'required|in:flexible,moderate,strict',
            'rules'               => 'nullable|string',
            'amenity_ids'         => 'nullable|array',
            'amenity_ids.*'       => 'exists:amenities,id',
            'tag_ids'             => 'nullable|array',
            'tag_ids.*'           => 'exists:tags,id',
        ]);

        $villa = $request->user()->villas()->create(array_merge($data, [
            // The owner form doesn't always provide coordinates yet.
            'latitude' => $data['latitude'] ?? 0,
            'longitude' => $data['longitude'] ?? 0,
            'slug'   => \Illuminate\Support\Str::slug($data['title']) . '-' . uniqid(),
            'status' => Villa::STATUS_PENDING,
        ]));

        if (!empty($data['amenity_ids'])) {
            $count = Amenity::query()
                ->where('owner_id', $request->user()->id)
                ->whereIn('id', $data['amenity_ids'])
                ->count();
            abort_if($count !== count(array_unique($data['amenity_ids'])), 422, 'Un ou plusieurs equipements inclus sont invalides.');
            $villa->amenities()->sync($data['amenity_ids']);
        }
        if (!empty($data['tag_ids'])) {
            $villa->tags()->sync($data['tag_ids']);
        }

        return response()->json(['message' => 'Villa créée et en attente d\'approbation.', 'villa' => $villa], 201);
    }

    /**
     * Owner: update villa.
     */
    public function update(Request $request, Villa $villa): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403, 'This action is unauthorized.');

        $data = $request->validate([
            'title'               => 'sometimes|string|max:200',
            'description'         => 'sometimes|string|min:50',
            'address'             => 'sometimes|string',
            'city'                => 'sometimes|string|max:100',
            'country'             => 'sometimes|string|max:100',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'listing_type'        => 'sometimes|in:vente,location',
            'price'               => 'sometimes|numeric|min:1',
            'price_per_night'     => 'sometimes|numeric|min:1',
            'weekend_price'       => 'nullable|numeric|min:1',
            'cleaning_fee'        => 'nullable|numeric|min:0',
            'security_deposit'    => 'nullable|numeric|min:0',
            'max_guests'          => 'sometimes|integer|min:1',
            'bedrooms'            => 'sometimes|integer|min:0',
            'bathrooms'           => 'sometimes|integer|min:1',
            'surface'             => 'nullable|numeric|min:1',
            'rooms_count'         => 'nullable|integer|min:1',
            'has_pool'            => 'boolean',
            'has_garden'          => 'boolean',
            'has_garage'          => 'boolean',
            'has_air_conditioning'=> 'boolean',
            'has_wifi'            => 'boolean',
            'exceptional_equipments' => 'nullable|array',
            'exceptional_equipments.*.code' => 'required_with:exceptional_equipments|string|max:100',
            'exceptional_equipments.*.label' => 'required_with:exceptional_equipments|string|max:120',
            'exceptional_equipments.*.price' => 'required_with:exceptional_equipments|numeric|min:0',
            'exceptional_equipments_total' => 'nullable|numeric|min:0',
            'min_nights'          => 'sometimes|integer|min:1',
            'max_nights'          => 'nullable|integer|min:1',
            'cancellation_policy' => 'sometimes|in:flexible,moderate,strict',
            'rules'               => 'nullable|string',
            'amenity_ids'         => 'nullable|array',
            'tag_ids'             => 'nullable|array',
        ]);

        $villa->update($data);

        if (isset($data['amenity_ids'])) {
            $count = Amenity::query()
                ->where('owner_id', $request->user()->id)
                ->whereIn('id', $data['amenity_ids'])
                ->count();
            abort_if($count !== count(array_unique($data['amenity_ids'])), 422, 'Un ou plusieurs equipements inclus sont invalides.');
            $villa->amenities()->sync($data['amenity_ids']);
        }
        if (isset($data['tag_ids']))     $villa->tags()->sync($data['tag_ids']);

        return response()->json(['message' => 'Villa mise à jour.', 'villa' => $villa->fresh()]);
    }

    /**
     * Owner: delete villa.
     */
    public function destroy(Request $request, Villa $villa): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403, 'This action is unauthorized.');
        $villa->delete();
        return response()->json(['message' => 'Villa supprimée.']);
    }

    /**
     * Upload photo.
     */
    public function uploadPhoto(Request $request, Villa $villa): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403, 'This action is unauthorized.');
        $request->validate(['photo' => 'required|image|max:5120']);

        $cloudinaryAvailable = class_exists('Cloudinary\\Cloudinary')
            && env('CLOUDINARY_CLOUD_NAME')
            && env('CLOUDINARY_API_KEY')
            && env('CLOUDINARY_API_SECRET');

        $url = null;
        $cloudinaryId = null;

        if ($cloudinaryAvailable) {
            $cloudinaryClass = '\\Cloudinary\\Cloudinary';
            $cloudinary = new $cloudinaryClass([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $result = $cloudinary->uploadApi()->upload(
                $request->file('photo')->getRealPath(),
                ['folder' => 'villa-reserv/villas']
            );

            $url = $result['secure_url'];
            $cloudinaryId = $result['public_id'];
        } else {
            $path = $request->file('photo')->store('villas', 'public');
            $url = $request->getSchemeAndHttpHost() . '/storage/' . ltrim($path, '/');
            $cloudinaryId = 'local:' . $path;
        }

        // Latest uploaded image becomes cover to ensure correct image is displayed.
        $villa->photos()->update(['is_cover' => false]);

        $photo = $villa->photos()->create([
            'url'           => $url,
            'cloudinary_id' => $cloudinaryId,
            'is_cover'      => true,
            'sort_order'    => $villa->photos()->count(),
        ]);

        return response()->json(['message' => 'Photo uploadée.', 'photo' => $photo], 201);
    }

    /**
     * Delete photo.
     */
    public function deletePhoto(Request $request, Villa $villa, VillaPhoto $photo): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403, 'This action is unauthorized.');
        abort_unless($photo->villa_id === $villa->id, 404);

        if ($photo->cloudinary_id && str_starts_with($photo->cloudinary_id, 'local:')) {
            $path = substr($photo->cloudinary_id, 6);
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        } elseif (
            $photo->cloudinary_id
            && class_exists('Cloudinary\\Cloudinary')
            && env('CLOUDINARY_CLOUD_NAME')
            && env('CLOUDINARY_API_KEY')
            && env('CLOUDINARY_API_SECRET')
        ) {
            $cloudinaryClass = '\\Cloudinary\\Cloudinary';
            $cloudinary = new $cloudinaryClass([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);
            $cloudinary->uploadApi()->destroy($photo->cloudinary_id);
        }

        $photo->delete();
        return response()->json(['message' => 'Photo supprimée.']);
    }

    /**
     * Admin: approve villa.
     */
    public function adminApprove(Villa $villa): JsonResponse
    {
        $villa->update(['status' => Villa::STATUS_APPROVED]);
        // Notify owner
        $villa->owner->notify(new \App\Notifications\VillaApproved($villa));
        return response()->json(['message' => 'Villa approuvée.']);
    }

    /**
     * Admin: reject villa.
     */
    public function adminReject(Request $request, Villa $villa): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);
        $villa->update(['status' => Villa::STATUS_REJECTED]);
        return response()->json(['message' => 'Villa rejetée.']);
    }
}