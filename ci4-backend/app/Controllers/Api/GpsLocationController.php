<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BusModel;
use App\Models\GpsLocationModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class GpsLocationController extends BaseController
{
    public function store(string $deviceId): ResponseInterface
    {
        $token = $this->request->getHeaderLine('Authorization');
        $token = preg_replace('/^Bearer\s+/i', '', $token ?? '');

        if ($token === '') {
            return $this->fail('Device token is required.', 401);
        }

        $bus = (new BusModel())
            ->where('device_id', $deviceId)
            ->where('is_active', 1)
            ->first();

        if ($bus === null || ! password_verify($token, $bus['device_token_hash'])) {
            return $this->fail('Invalid device credentials.', 401);
        }

        $data = $this->request->getJSON(true);

        if (! is_array($data)) {
            return $this->fail('A JSON request body is required.', 422);
        }

        $rules = [
            'latitude' => 'required|decimal|greater_than_equal_to[-90]|less_than_equal_to[90]',
            'longitude' => 'required|decimal|greater_than_equal_to[-180]|less_than_equal_to[180]',
            'speed_kmh' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[300]',
            'heading' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[360]',
            'recorded_at' => 'required|valid_date',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Invalid GPS location.', 'errors' => $this->validator->getErrors()]);
        }

        try {
            $recordedAt = new DateTimeImmutable((string) $data['recorded_at']);
        } catch (Exception) {
            return $this->fail('recorded_at must be a valid ISO-8601 timestamp.', 422);
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        if (abs($now->getTimestamp() - $recordedAt->getTimestamp()) > 300) {
            return $this->fail('GPS timestamp must be within 5 minutes of server time.', 422);
        }

        $location = [
            'bus_id' => $bus['id'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'speed_kmh' => $data['speed_kmh'] ?? null,
            'heading' => $data['heading'] ?? null,
            'recorded_at' => $recordedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        ];

        $locationModel = new GpsLocationModel();
        $location['id'] = $locationModel->insert($location, true);

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'GPS location saved successfully.',
            'data' => $this->formatLocation($location),
        ]);
    }

    private function fail(string $message, int $status): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON(['message' => $message]);
    }

    /** @param array<string, mixed> $location */
    private function formatLocation(array $location): array
    {
        return [
            'bus_id' => (int) $location['bus_id'],
            'latitude' => (float) $location['latitude'],
            'longitude' => (float) $location['longitude'],
            'speed_kmh' => $location['speed_kmh'] === null ? null : (float) $location['speed_kmh'],
            'heading' => $location['heading'] === null ? null : (float) $location['heading'],
            'recorded_at' => (new DateTimeImmutable((string) $location['recorded_at'], new DateTimeZone('UTC')))->format('Y-m-d\\TH:i:s.v\\Z'),
        ];
    }
}
