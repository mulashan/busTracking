<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

class BusLocationController extends Controller
{
    public function show(Bus $bus)
    {
        $bus->load('latestLocation');

        return response()->json([
            'data' => [
                'bus' => [
                    'id' => $bus->id,
                    'registration_number' => $bus->registration_number,
                    'device_id' => $bus->device_id,
                    'capacity' => $bus->capacity,
                    'is_active' => $bus->is_active,
                ],
                'location' => $bus->latestLocation,
            ],
        ]);
    }

    public function history(Request $request, Bus $bus)
    {
        $limit = min((int) $request->integer('limit', 50), 200);

        return response()->json([
            'data' => $bus->gpsLocations()
                ->latest('recorded_at')
                ->limit($limit)
                ->get(),
        ]);
    }
}
