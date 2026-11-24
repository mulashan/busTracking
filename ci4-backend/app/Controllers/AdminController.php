<?php

namespace App\Controllers;

use App\Models\BusModel;
use App\Models\BusStopModel;
use App\Models\GpsLocationModel;
use App\Models\RouteModel;
use App\Models\StudentModel;
use App\Models\TripModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdminController extends BaseController
{
    public function dashboard(string $section = 'dashboard'): string
    {
        $allowedSections = ['dashboard', 'database', 'route', 'tracking', 'requests', 'settings'];
        if (! in_array($section, $allowedSections, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = db_connect();
        $buses = $db->table('buses b')->select('b.*, u.name as driver_name')->join('users u', 'u.id = b.driver_id', 'left')->orderBy('b.id', 'DESC')->get()->getResultArray();
        foreach ($buses as &$bus) {
            $bus['routes'] = $db->table('routes r')->select('r.name')->join('bus_route br', 'br.route_id = r.id')->where('br.bus_id', $bus['id'])->get()->getResultArray();
            $bus['students'] = $db->table('students s')->select('s.first_name')->join('student_bus sb', 'sb.student_id = s.id')->where('sb.bus_id', $bus['id'])->get()->getResultArray();
            $bus['latest_location'] = (new GpsLocationModel())->where('bus_id', $bus['id'])->orderBy('recorded_at', 'DESC')->first();
        }
        unset($bus);

        $routes = (new RouteModel())->orderBy('id', 'DESC')->findAll();
        foreach ($routes as &$route) {
            $route['stops'] = (new BusStopModel())->where('route_id', $route['id'])->orderBy('stop_order')->findAll();
            $route['buses'] = $db->table('buses b')->select('b.registration_number')->join('bus_route br', 'br.bus_id = b.id')->where('br.route_id', $route['id'])->get()->getResultArray();
        }
        unset($route);

        $activeTrips = $this->trips(['scheduled', 'active']);
        $completedTrips = $this->trips(['completed'], 10);

        return view('admin/dashboard', [
            'section' => $section,
            'buses' => $buses,
            'drivers' => (new UserModel())->where('role', 'driver')->orderBy('id', 'DESC')->findAll(),
            'students' => (new StudentModel())->orderBy('id', 'DESC')->findAll(),
            'routes' => $routes,
            'stops' => (new BusStopModel())->orderBy('route_id')->orderBy('stop_order')->findAll(),
            'activeTrips' => $activeTrips,
            'completedTrips' => $completedTrips,
            'report' => ['total_trips' => (new TripModel())->countAll(), 'active_trips' => (new TripModel())->where('status', 'active')->countAllResults(), 'completed_trips' => (new TripModel())->where('status', 'completed')->countAllResults(), 'avg_eta_variance' => round((float) $db->table('trips')->selectAvg('eta_variance_minutes')->get()->getRow()->eta_variance_minutes, 1), 'avg_duration' => round((float) $db->table('trips')->selectAvg('actual_duration_minutes')->get()->getRow()->actual_duration_minutes, 1)],
        ]);
    }

    public function storeBus(): RedirectResponse { return $this->store('bus'); }
    public function storeDriver(): RedirectResponse { return $this->store('driver'); }
    public function storeStudent(): RedirectResponse { return $this->store('student'); }
    public function storeRoute(): RedirectResponse { return $this->store('route'); }
    public function storeStop(): RedirectResponse { return $this->store('stop'); }
    public function assignStudent(): RedirectResponse { return $this->store('assignment'); }
    public function storeTrip(): RedirectResponse { return $this->store('trip'); }

    public function completeTrip(int $tripId): RedirectResponse
    {
        $trip = (new TripModel())->find($tripId);
        if ($trip === null) return redirect()->back()->with('error', 'Trip not found.');
        $duration = (int) $this->request->getPost('actual_duration_minutes');
        if ($duration < 1 || $duration > 600) return redirect()->back()->with('error', 'Duration must be between 1 and 600 minutes.');
        (new TripModel())->update($tripId, ['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s'), 'actual_duration_minutes' => $duration, 'eta_variance_minutes' => $trip['predicted_eta_minutes'] === null ? null : $duration - (int) $trip['predicted_eta_minutes'], 'students_picked' => (int) ($this->request->getPost('students_picked') ?: $trip['students_picked']), 'students_remaining' => (int) ($this->request->getPost('students_remaining') ?: $trip['students_remaining'])]);
        return redirect()->back()->with('status', 'Trip completed and ETA report updated.');
    }

    private function store(string $type): RedirectResponse
    {
        $post = $this->request->getPost();
        try {
            switch ($type) {
                case 'bus':
                    if (empty($post['registration_number']) || empty($post['device_id']) || ! is_numeric($post['capacity'] ?? null)) throw new \RuntimeException('Registration, device ID, and capacity are required.');
                    (new BusModel())->insert(['registration_number' => $post['registration_number'], 'device_id' => $post['device_id'], 'device_token_hash' => password_hash($post['device_token'] ?: bin2hex(random_bytes(24)), PASSWORD_DEFAULT), 'capacity' => (int) $post['capacity'], 'driver_id' => $post['driver_id'] ?: null, 'is_active' => 1]); break;
                case 'driver':
                    if (empty($post['name']) || ! filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL)) throw new \RuntimeException('A valid driver name and email are required.');
                    (new UserModel())->insert(['name' => $post['name'], 'email' => $post['email'], 'password' => password_hash($post['password'] ?: 'password', PASSWORD_DEFAULT), 'role' => 'driver']); break;
                case 'student':
                    if (empty($post['admission_number']) || empty($post['first_name']) || empty($post['last_name'])) throw new \RuntimeException('Admission number and student names are required.');
                    (new StudentModel())->insert(['admission_number' => $post['admission_number'], 'first_name' => $post['first_name'], 'last_name' => $post['last_name'], 'class_name' => $post['class_name'] ?: null, 'is_active' => 1]); break;
                case 'route':
                    if (empty($post['name'])) throw new \RuntimeException('Route name is required.');
                    $routeId = (new RouteModel())->insert(['name' => $post['name'], 'start_location' => $post['start_location'] ?: null, 'end_location' => $post['end_location'] ?: null, 'is_active' => 1], true); if (! empty($post['bus_id'])) db_connect()->table('bus_route')->insert(['bus_id' => $post['bus_id'], 'route_id' => $routeId]); break;
                case 'stop':
                    if (empty($post['route_id']) || empty($post['name']) || ! is_numeric($post['latitude'] ?? null) || ! is_numeric($post['longitude'] ?? null) || ! is_numeric($post['stop_order'] ?? null)) throw new \RuntimeException('Complete bus stop details are required.');
                    $stopModel = new BusStopModel();
                    $stop = $stopModel->where('route_id', $post['route_id'])->where('stop_order', $post['stop_order'])->first();
                    $stopData = ['route_id' => $post['route_id'], 'name' => $post['name'], 'latitude' => $post['latitude'], 'longitude' => $post['longitude'], 'stop_order' => $post['stop_order']];
                    $stop === null ? $stopModel->insert($stopData) : $stopModel->update($stop['id'], $stopData); break;
                case 'assignment':
                    if (empty($post['student_id']) || empty($post['bus_id'])) throw new \RuntimeException('Student and bus are required.');
                    db_connect()->table('student_bus')->ignore(true)->insert(['student_id' => $post['student_id'], 'bus_id' => $post['bus_id'], 'bus_stop_id' => $post['bus_stop_id'] ?: null]); break;
                case 'trip':
                    if (empty($post['bus_id']) || ! in_array($post['status'] ?? '', ['scheduled', 'active', 'completed'], true)) throw new \RuntimeException('Bus and valid trip status are required.');
                    (new TripModel())->insert(['bus_id' => $post['bus_id'], 'route_id' => $post['route_id'] ?: null, 'status' => $post['status'], 'started_at' => $post['status'] === 'active' ? date('Y-m-d H:i:s') : null, 'completed_at' => $post['status'] === 'completed' ? date('Y-m-d H:i:s') : null, 'predicted_eta_minutes' => $post['predicted_eta_minutes'] ?: null]); break;
            }
        } catch (\Throwable $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage()); }
        return redirect()->back()->with('status', ucfirst($type) . ' saved successfully.');
    }

    /** @param list<string> $statuses */
    private function trips(array $statuses, ?int $limit = null): array
    {
        $query = db_connect()->table('trips t')->select('t.*, b.registration_number, r.name as route_name')->join('buses b', 'b.id = t.bus_id')->join('routes r', 'r.id = t.route_id', 'left')->whereIn('t.status', $statuses)->orderBy('t.id', 'DESC');
        if ($limit !== null) $query->limit($limit);
        return $query->get()->getResultArray();
    }
}
