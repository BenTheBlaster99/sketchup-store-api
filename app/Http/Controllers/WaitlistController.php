<?php

namespace App\Http\Controllers;

use App\Mail\WaitlistLaunchNotification;
use App\Models\WaitlistEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:100',
        ]);

        $entry = WaitlistEntry::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'] ?? null],
        );

        $alreadyJoined = ! $entry->wasRecentlyCreated;

        return response()->json([
            'message' => $alreadyJoined
                ? 'You are already on the waitlist.'
                : 'You have been added to the waitlist!',
            'already_joined' => $alreadyJoined,
        ], $alreadyJoined ? 200 : 201);
    }

    public function notifyAll(): JsonResponse
    {
        $entries = WaitlistEntry::whereNull('notified_at')->get();

        foreach ($entries as $entry) {
            Mail::to($entry->email)->queue(new WaitlistLaunchNotification($entry));
            $entry->update(['notified_at' => now()]);
        }

        return response()->json([
            'message' => 'Launch email sent to '.$entries->count().' people.',
        ]);
    }
}
