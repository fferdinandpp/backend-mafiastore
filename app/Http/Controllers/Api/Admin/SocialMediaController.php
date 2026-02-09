<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialMediaController extends Controller
{
    public function index()
    {
        return response()->json(
            SocialMedia::orderBy('sort_order')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'value' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $social = SocialMedia::create([
            ...$data,
            'is_active' => (bool)($data['is_active'] ?? true),
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Social media created', 'social_media' => $social], 201);
    }

    public function show($id)
    {
        return response()->json(SocialMedia::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $social = SocialMedia::findOrFail($id);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'value' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $social->update($data);

        $social->refresh();

        return response()->json(['message' => 'Social media updated', 'social_media' => $social]);
    }

    public function destroy($id)
    {
        $social = SocialMedia::findOrFail($id);
        $social->delete();

        return response()->json(['message' => 'Social media deleted']);
    }
}
