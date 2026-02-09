<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountImageController extends Controller
{
    public function store(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $startOrder = (int) $account->images()->max('sort_order');
        $startOrder = is_numeric($startOrder) ? $startOrder + 1 : 0;

        $saved = [];

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('accounts', 'public');

            $img = $account->images()->create([
                'path' => $path,
                'sort_order' => $startOrder + $i,
            ]);

            $saved[] = [
                'id' => $img->id,
                'path' => $img->path,
                'image_url' => $img->image_url,
                'sort_order' => $img->sort_order,
            ];
        }

        return response()->json([
            'message' => 'Images uploaded',
            'images' => $saved
        ], 201);
    }

    public function destroy($imageId)
    {
        $image = AccountImage::findOrFail($imageId);

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }
}
