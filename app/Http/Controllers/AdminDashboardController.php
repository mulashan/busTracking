<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Route as SchoolRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(string $section = 'dashboard'): View
    {
        $allowedSections = ['dashboard', 'database', 'route', 'tracking', 'requests', 'settings'];
        abort_unless(in_array($section, $allowedSections, true), 404);

        $buses = Bus::query()
            ->with(['driver', 'latestLocation', 'routes', 'students'])
            ->latest()
            ->get();

        $drivers = User::query()
            ->where('role', 'driver')
            ->latest()
            ->get();

        $students = Student::query()
            ->with(['buses', 'parents'])
            ->latest()
            ->get();

        $routes = SchoolRoute::query()
            ->with(['stops', 'buses'])
            ->latest()
            ->get();

        $stops = BusStop::query()
            ->with('route')
            ->orderBy('route_id')
            ->orderBy('stop_order')
            ->get();

        $activeTrips = Trip::query()
            ->with(['bus.driver', 'route'])
            ->whereIn('status', ['scheduled', 'active'])
            ->latest()
            ->get();

        $completedTrips = Trip::query()
            ->with(['bus.driver', 'route'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->limit(10)
            ->get();

        $report = [
            'total_trips' => Trip::count(),
            'active_trips' => Trip::where('status', 'active')->count(),
            'completed_trips' => Trip::where('status', 'completed')->count(),
            'avg_eta_variance' => round((float) Trip::whereNotNull('eta_variance_minutes')->avg('eta_variance_minutes'), 1),
            'avg_duration' => round((float) Trip::whereNotNull('actual_duration_minutes')->avg('actual_duration_minutes'), 1),
        ];

        return view('admin.dashboard', compact(
            'activeTrips',
            'buses',
            'completedTrips',
            'drivers',
            'report',
            'routes',
            'section',
            'stops',
            'students',
        ));
    }

    public function storeBus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:50', 'unique:buses,registration_number'],
            'device_id' => ['required', 'string', 'max:80', 'unique:buses,device_id'],
            'device_token' => ['nullable', 'string', 'min:8'],
            'capacity' => ['required', 'integer', 'min:1', 'max:120'],
            'driver_id' => ['nullable', 'exists:users,id'],
        ]);

        Bus::create([
            'registration_number' => $data['registration_number'],
            'device_id' => $data['device_id'],
            'device_token_hash' => Hash::make($data['device_token'] ?: Str::random(48)),
            'capacity' => $data['capacity'],
            'driver_id' => $data['driver_id'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Bus added successfully.');
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?: 'password',
            'role' => 'driver',
        ]);

        return back()->with('status', 'Driver added successfully.');
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admission_number' => ['required', 'string', 'max:80', 'unique:students,admission_number'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'class_name' => ['nullable', 'string', 'max:80'],
        ]);

        Student::create($data + ['is_active' => true]);

        return back()->with('status', 'Student added successfully.');
    }

    public function storeRoute(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'start_location' => ['nullable', 'string', 'max:160'],
            'end_location' => ['nullable', 'string', 'max:160'],
            'bus_id' => ['nullable', 'exists:buses,id'],
        ]);

        $route = SchoolRoute::create([
            'name' => $data['name'],
            'start_location' => $data['start_location'] ?? null,
            'end_location' => $data['end_location'] ?? null,
            'is_active' => true,
        ]);

        if (! empty($data['bus_id'])) {
            $route->buses()->syncWithoutDetaching([$data['bus_id']]);
        }

        return back()->with('status', 'Route added successfully.');
    }

    public function storeStop(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'route_id' => ['required', 'exists:routes,id'],
            'name' => ['required', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'stop_order' => ['required', 'integer', 'min:1'],
        ]);

        BusStop::updateOrCreate(
            ['route_id' => $data['route_id'], 'stop_order' => $data['stop_order']],
            $data,
        );

        return back()->with('status', 'Bus stop saved successfully.');
    }

    public function assignStudent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'bus_id' => ['required', 'exists:buses,id'],
            'bus_stop_id' => ['nullable', 'exists:bus_stops,id'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
        ]);

        $student = Student::findOrFail($data['student_id']);
        $student->buses()->syncWithoutDetaching([
            $data['bus_id'] => [
                'bus_stop_id' => $data['bus_stop_id'] ?? null,
                'starts_on' => $data['starts_on'] ?? null,
                'ends_on' => $data['ends_on'] ?? null,
            ],
        ]);

        return back()->with('status', 'Student assigned successfully.');
    }

    public function storeTrip(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bus_id' => ['required', 'exists:buses,id'],
            'route_id' => ['nullable', 'exists:routes,id'],
            'status' => ['required', 'in:scheduled,active,completed'],
            'scheduled_start_at' => ['nullable', 'date'],
            'predicted_eta_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'students_picked' => ['nullable', 'integer', 'min:0'],
            'students_remaining' => ['nullable', 'integer', 'min:0'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Trip::create([
            ...$data,
            'started_at' => $data['status'] === 'active' ? now() : null,
            'completed_at' => $data['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('status', 'Trip saved successfully.');
    }

    public function completeTrip(Request $request, Trip $trip): RedirectResponse
    {
        $data = $request->validate([
            'actual_duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'students_picked' => ['nullable', 'integer', 'min:0'],
            'students_remaining' => ['nullable', 'integer', 'min:0'],
        ]);

        $trip->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_duration_minutes' => $data['actual_duration_minutes'],
            'eta_variance_minutes' => $trip->predicted_eta_minutes
                ? $data['actual_duration_minutes'] - $trip->predicted_eta_minutes
                : null,
            'students_picked' => $data['students_picked'] ?? $trip->students_picked,
            'students_remaining' => $data['students_remaining'] ?? $trip->students_remaining,
        ]);

        return back()->with('status', 'Trip completed and ETA report updated.');
    }
}
