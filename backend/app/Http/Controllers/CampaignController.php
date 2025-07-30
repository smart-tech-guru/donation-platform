<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::with('user')
            ->withCount('donations')
            ->withSum('donations', 'amount');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $campaigns = $query->paginate(15);
        return response()->json($campaigns);
    }

    public function show(Campaign $campaign): JsonResponse
    {
        $campaign->load(['user', 'donations.user']);
        return response()->json($campaign);
    }
}
