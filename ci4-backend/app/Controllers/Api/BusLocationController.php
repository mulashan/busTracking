<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BusModel;
use App\Models\GpsLocationModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeImmutable;
use DateTimeZone;

class BusLocationController extends BaseController
{
    public function show(int $busId): ResponseInterface
    {
        $bus = (new BusModel())->find($busId);
        if ($bus === null) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bus not found.']);
        }

        $location = (new GpsLocationModel())->where('bus_id', $busId)->orderBy('recorded_at', 'DESC')->first();

        return $this->response->setJSON(['data' => ['bus' => $this->formatBus($bus), 'location' => $location === null ? null : $this->formatLocation($location)]]);
    }

    public function history(int $busId): ResponseInterface
    {
        if ((new BusModel())->find($busId) === null) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bus not found.']);
        }

        $limit = max(1, min((int) ($this->request->getGet('limit') ?? 50), 200));
        $locations = (new GpsLocationModel())->where('bus_id', $busId)->orderBy('recorded_at', 'DESC')->findAll($limit);

        return $this->response->setJSON(['data' => array_map(fn (array $location): array => $this->formatLocation($location), $locations)]);
    }

    /** @param array<string, mixed> $bus */
    private function formatBus(array $bus): array
    {
        return ['id' => (int) $bus['id'], 'registration_number' => $bus['registration_number'], 'device_id' => $bus['device_id'], 'capacity' => (int) $bus['capacity'], 'is_active' => (bool) $bus['is_active']];
    }

    /** @param array<string, mixed> $location */
    private function formatLocation(array $location): array
    {
        return ['id' => (int) $location['id'], 'bus_id' => (int) $location['bus_id'], 'latitude' => (float) $location['latitude'], 'longitude' => (float) $location['longitude'], 'speed_kmh' => $location['speed_kmh'] === null ? null : (float) $location['speed_kmh'], 'heading' => $location['heading'] === null ? null : (float) $location['heading'], 'recorded_at' => (new DateTimeImmutable((string) $location['recorded_at'], new DateTimeZone('UTC')))->format('Y-m-d\\TH:i:s.000000\\Z')];
    }
}
