<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;

class PublicSocialMediaController extends Controller
{
    public function index()
    {
        return response()->json(
            SocialMedia::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'type', 'value', 'sort_order'])
        );
    }
}
