# School Bus Tracking

Laravel API + React Native mobile starter for school bus GPS tracking.

## Backend

Create the MySQL database:

```sql
CREATE DATABASE school_bus_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Copy `.env.example` to `.env`, set your database credentials, then run:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

For quick local testing without MySQL, the generated `.env` currently uses SQLite. The production-ready sample config in `.env.example` is MySQL.

## IoT GPS API

ESP32 devices send GPS points here:

```http
POST /api/devices/{deviceId}/locations
Authorization: Bearer {deviceToken}
Content-Type: application/json
Accept: application/json
```

Example payload:

```json
{
  "latitude": -3.3869,
  "longitude": 36.683,
  "speed_kmh": 35.5,
  "heading": 180,
  "recorded_at": "2026-10-12T09:20:00Z"
}
```

The seeded demo bus is `BUS-001`. The default local token is `test-device-token`. Set `DEMO_DEVICE_TOKEN` in `.env` before running `php artisan db:seed` to use your own token.

Parent app read endpoints:

```http
GET /api/buses/1/location
GET /api/buses/1/history?limit=25
```

## Mobile App

The React Native/Expo starter lives in `mobile-app`.

```bash
cd mobile-app
npm install
EXPO_PUBLIC_API_BASE_URL=http://YOUR_COMPUTER_IP:8000/api npm start
```

Use your computer's LAN IP for a real phone. `127.0.0.1` only works from the same machine.
