<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        return response()->json(
            Banner::query()
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('image')->store('banners', 'public');

        $banner = Banner::create([
            'title' => $data['title'] ?? null,
            'image_path' => $path,
            'is_active' => (bool)($data['is_active'] ?? true),
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Banner created', 'banner' => $banner], 201);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'title'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active'  => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'starts_at'  => ['sometimes', 'nullable', 'date'],
            'ends_at'    => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if (count($data) === 0) {
            return response()->json([
                'message' => 'No data received. For update banner meta, send JSON body (application/json) OR use POST + _method=PUT if using form-data.'
            ], 422);
        }

        $banner->update($data);

        return response()->json([
            'message' => 'Banner updated',
            'banner' => $banner->refresh(),
        ]);
    }

    public function updateImage(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $path = $request->file('image')->store('banners', 'public');
        $banner->update(['image_path' => $path]);

        return response()->json(['message' => 'Banner image updated', 'banner' => $banner]);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner deleted']);
    }
}
