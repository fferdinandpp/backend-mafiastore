<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class PublicBannerController extends Controller
{
    public function index()
    {
        return response()->json(
            Banner::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'title', 'image_path', 'is_active', 'sort_order'])
        );
    }
}
