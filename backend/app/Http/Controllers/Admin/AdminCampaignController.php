<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCampaignController extends Controller
{
    public function index(): JsonResponse
    {
        $campaigns = Campaign::with(['user', 'donations.user'])->get();
        return response()->json(['data' => $campaigns]);
    }

    public function show(Campaign $campaign): JsonResponse
    {
        $campaign->load(['user', 'donations.user']);
        $campaign->loadCount('donations');
        $campaign->loadSum('donations', 'amount');
        
        return response()->json([
            'campaign' => $campaign,
            'donations' => $campaign->donations
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'goal_amount' => $request->goal_amount,
            'user_id' => $user->id,
        ]);

        return response()->json($campaign, 201);
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $campaign->delete();
        return response()->json(null, 204);
    }
}
