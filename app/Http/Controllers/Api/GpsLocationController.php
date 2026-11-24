<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\GpsLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GpsLocationController extends Controller
{
    public function store(Request $request, string $deviceId)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Device token is required.'], 401);
        }

        $bus = Bus::query()
            ->where('device_id', $deviceId)
            ->where('is_active', true)
            ->first();

        if (! $bus || ! Hash::check($token, $bus->device_token_hash)) {
            return response()->json(['message' => 'Invalid device credentials.'], 401);
        }

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed_kmh' => ['nullable', 'numeric', 'between:0,300'],
            'heading' => ['nullable', 'numeric', 'between:0,360'],
            'recorded_at' => ['required', 'date'],
        ]);

        $recordedAt = Carbon::parse($data['recorded_at']);

        if (abs(now()->timestamp - $recordedAt->timestamp) > 300) {
            throw ValidationException::withMessages([
                'recorded_at' => 'GPS timestamp must be within 5 minutes of server time.',
            ]);
        }

        $location = GpsLocation::create([
            'bus_id' => $bus->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'speed_kmh' => $data['speed_kmh'] ?? null,
            'heading' => $data['heading'] ?? null,
            'recorded_at' => $recordedAt,
        ]);

        return response()->json([
            'message' => 'GPS location saved successfully.',
            'data' => [
                'bus_id' => $bus->id,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'speed_kmh' => $location->speed_kmh,
                'heading' => $location->heading,
                'recorded_at' => $location->recorded_at->toISOString(),
            ],
        ], 201);
    }
}
