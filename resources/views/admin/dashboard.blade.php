<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LittleMove School Admin</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1f2937;
            background: #eef0f4;
        }
        button, input, select, textarea { font: inherit; }
        a { color: inherit; }
        .app {
            display: grid;
            grid-template-columns: 230px 1fr;
            min-height: 100vh;
            background: #fff;
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            background: #3c3637;
            color: #f8fafc;
        }
        .brand {
            height: 96px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            background: #fff2dc;
            color: #18181b;
            font-weight: 900;
        }
        .brand-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #ffbf2f;
            color: #3c3637;
        }
        .brand small {
            display: block;
            color: #f59e0b;
            font-size: 12px;
            font-weight: 900;
        }
        .nav {
            padding: 20px 16px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            height: 54px;
            padding: 0 14px;
            margin-bottom: 8px;
            border-radius: 8px;
            color: #ddd6d6;
            text-decoration: none;
            font-weight: 800;
        }
        .nav-link.active {
            color: #111827;
            background: #ffbd2e;
        }
        .nav-icon {
            width: 22px;
            text-align: center;
            font-weight: 900;
        }
        .help {
            margin-top: auto;
            padding: 22px;
            color: #e5e7eb;
            font-weight: 800;
        }
        .main {
            min-width: 0;
            background: #f8fafc;
        }
        .topbar {
            height: 96px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 32px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }
        .greeting strong {
            display: block;
            margin-top: 4px;
            font-size: 26px;
            color: #334155;
        }
        .bell {
            position: relative;
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 22px;
        }
        .bell::after {
            content: "";
            position: absolute;
            right: 8px;
            top: 8px;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #ef4444;
        }
        .content {
            padding: 26px;
        }
        .flash, .errors {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-weight: 800;
        }
        .flash { color: #166534; background: #dcfce7; }
        .errors { color: #991b1b; background: #fee2e2; }
        .tracking-layout {
            display: grid;
            grid-template-columns: 420px minmax(520px, 1fr);
            min-height: calc(100vh - 96px);
            padding: 0;
        }
        .tracking-panel {
            z-index: 2;
            margin: 14px 0 0 14px;
            align-self: start;
            padding: 22px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, .18);
        }
        .tracking-tabs {
            display: flex;
            gap: 28px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        .tracking-tabs button {
            padding: 0 0 12px;
            border: 0;
            border-bottom: 4px solid transparent;
            background: transparent;
            font-weight: 900;
        }
        .tracking-tabs button.active {
            border-color: #ffbd2e;
        }
        .track-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .track-table th, .track-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
        }
        .track-table th {
            color: #6b7280;
            background: #f3f4f6;
            font-size: 12px;
        }
        .track-btn {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border: 0;
            border-radius: 8px;
            background: #ffbd2e;
            color: #111827;
            font-weight: 900;
        }
        .map-stage {
            position: relative;
            min-height: calc(100vh - 96px);
            overflow: hidden;
            background:
                radial-gradient(circle at 48% 48%, rgba(255,255,255,.65) 0 3px, transparent 4px),
                linear-gradient(90deg, transparent 0 46%, rgba(59, 130, 246, .38) 47% 49%, transparent 50%),
                linear-gradient(32deg, transparent 0 48%, rgba(251, 146, 60, .5) 49% 50%, transparent 51%),
                linear-gradient(122deg, transparent 0 48%, rgba(203, 213, 225, .9) 49% 51%, transparent 52%),
                #e9edf6;
            background-size: 120px 120px, 320px 320px, 210px 210px, 180px 180px, auto;
        }
        .route-polyline {
            position: absolute;
            inset: 16% 8% 12% 8%;
            border-left: 6px solid #ff4b2f;
            border-bottom: 6px solid #ff4b2f;
            border-radius: 0 0 0 60px;
        }
        .route-polyline::before,
        .route-polyline::after {
            content: "";
            position: absolute;
            border: 6px solid #ff4b2f;
            border-left: 0;
            border-bottom: 0;
        }
        .route-polyline::before {
            left: -6px;
            top: 0;
            width: 52%;
            height: 150px;
            transform: skewY(-20deg);
        }
        .route-polyline::after {
            right: 0;
            bottom: 0;
            width: 42%;
            height: 170px;
            border-top: 0;
            border-right: 6px solid #ff4b2f;
            border-bottom: 6px solid #ff4b2f;
        }
        .pin {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 6px solid #ffff00;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            background: #ccff00;
            box-shadow: 0 5px 12px rgba(15, 23, 42, .25);
        }
        .pin.red { border-color: #ef4444; background: #ef4444; }
        .pin.one { left: 9%; top: 48%; }
        .pin.two { left: 34%; top: 57%; }
        .pin.three { left: 46%; top: 57%; }
        .pin.four { left: 55%; top: 60%; }
        .pin.five { left: 74%; top: 76%; }
        .pin.six { left: 90%; top: 58%; }
        .pin.red { left: 78%; top: 20%; }
        .map-actions {
            position: absolute;
            right: 24px;
            bottom: 24px;
            display: flex;
            gap: 10px;
        }
        .map-actions button {
            height: 46px;
            border: 0;
            border-radius: 8px;
            padding: 0 18px;
            background: #ffbd2e;
            font-weight: 900;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 18px;
        }
        .card {
            grid-column: span 4;
            padding: 20px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }
        .card.wide { grid-column: span 8; }
        .card.full { grid-column: 1 / -1; }
        .card h2 {
            margin: 0 0 16px;
            font-size: 19px;
        }
        .kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        .kpi {
            padding: 18px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        }
        .kpi span {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }
        .kpi strong {
            display: block;
            margin-top: 8px;
            font-size: 30px;
        }
        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        label {
            color: #475569;
            font-size: 13px;
            font-weight: 900;
        }
        input, select, textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
        }
        textarea { min-height: 86px; resize: vertical; }
        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 42px;
            padding: 0 16px;
            border: 0;
            border-radius: 8px;
            color: #111827;
            background: #ffbd2e;
            font-weight: 900;
            cursor: pointer;
        }
        .btn.blue { color: #fff; background: #2563eb; }
        .btn.green { color: #fff; background: #0f766e; }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .table th, .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .pill {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 999px;
            color: #166534;
            background: #dcfce7;
            font-size: 12px;
            font-weight: 900;
        }
        .pill.blue { color: #1d4ed8; background: #dbeafe; }
        .pill.gray { color: #475569; background: #f1f5f9; }
        .muted { color: #64748b; font-size: 13px; }
        @media (max-width: 1100px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .nav { display: flex; overflow-x: auto; }
            .nav-link { min-width: 150px; }
            .help { display: none; }
            .tracking-layout { grid-template-columns: 1fr; }
            .tracking-panel { margin: 18px; }
            .map-stage { min-height: 540px; }
            .card, .card.wide { grid-column: 1 / -1; }
            .kpis { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 680px) {
            .content { padding: 16px; }
            .topbar { padding: 0 16px; }
            .form-row, .kpis { grid-template-columns: 1fr; }
            .greeting strong { font-size: 22px; }
        }
    </style>
</head>
<body>
@php
    $menu = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'D', 'route' => 'admin.dashboard'],
        ['key' => 'database', 'label' => 'Database', 'icon' => 'DB', 'route' => 'admin.database'],
        ['key' => 'route', 'label' => 'Route', 'icon' => 'R', 'route' => 'admin.route'],
        ['key' => 'tracking', 'label' => 'Tracking', 'icon' => 'T', 'route' => 'admin.tracking'],
        ['key' => 'requests', 'label' => 'Requests', 'icon' => 'Q', 'route' => 'admin.requests'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'S', 'route' => 'admin.settings'],
    ];
@endphp

<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">LM</div>
            <div><small>LittleMove</small>SCHOOL</div>
        </div>
        <nav class="nav">
            @foreach ($menu as $item)
                <a class="nav-link {{ $section === $item['key'] ? 'active' : '' }}" href="{{ route($item['route']) }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="help">Need Help ?</div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="greeting">
                Good Morning,
                <strong>LittleMove</strong>
            </div>
            <div class="bell">N</div>
        </header>

        @if ($section === 'tracking')
            <section class="tracking-layout">
                <div class="tracking-panel">
                    <div class="tracking-tabs">
                        <button class="active">Route <span class="pill gray">{{ $routes->count() }}</span></button>
                        <button>Track</button>
                    </div>
                    <table class="track-table">
                        <thead>
                        <tr>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($buses as $bus)
                            <tr>
                                <td>{{ $bus->routes->first()?->name ?? 'NO ROUTE' }}</td>
                                <td>{{ $bus->registration_number }}</td>
                                <td>{{ $bus->driver?->name ?? 'Unassigned' }}</td>
                                <td><button class="track-btn">Track</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No vehicles found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="map-stage">
                    <div class="route-polyline"></div>
                    <span class="pin one"></span>
                    <span class="pin two"></span>
                    <span class="pin three"></span>
                    <span class="pin four"></span>
                    <span class="pin five"></span>
                    <span class="pin six"></span>
                    <span class="pin red"></span>
                    <div class="map-actions">
                        <button>Refresh</button>
                        <button>Locate</button>
                    </div>
                </div>
            </section>
        @else
            <section class="content">
                @if (session('status'))
                    <div class="flash">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">{{ $errors->first() }}</div>
                @endif

                @if ($section === 'dashboard')
                    <div class="kpis">
                        <div class="kpi"><span>Buses</span><strong>{{ $buses->count() }}</strong></div>
                        <div class="kpi"><span>Drivers</span><strong>{{ $drivers->count() }}</strong></div>
                        <div class="kpi"><span>Students</span><strong>{{ $students->count() }}</strong></div>
                        <div class="kpi"><span>Active Trips</span><strong>{{ $report['active_trips'] }}</strong></div>
                    </div>

                    <div class="grid" style="margin-top:18px">
                        <div class="card wide">
                            <h2>Ongoing Trips</h2>
                            <table class="table">
                                <thead><tr><th>Bus</th><th>Route</th><th>Status</th><th>Students</th><th>ETA</th></tr></thead>
                                <tbody>
                                @forelse ($activeTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->bus?->registration_number }}</td>
                                        <td>{{ $trip->route?->name ?? 'No route' }}</td>
                                        <td><span class="pill blue">{{ $trip->status }}</span></td>
                                        <td>{{ $trip->students_picked }} picked / {{ $trip->students_remaining }} left</td>
                                        <td>{{ $trip->predicted_eta_minutes ?? '-' }} min</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No active trips yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card">
                            <h2>ETA Performance</h2>
                            <p class="muted">Average ETA variance</p>
                            <strong style="font-size:42px">{{ $report['avg_eta_variance'] }}m</strong>
                            <p class="muted">Average trip duration: {{ $report['avg_duration'] }} minutes</p>
                        </div>
                    </div>
                @endif

                @if ($section === 'database')
                    <div class="grid">
                        <div class="card">
                            <h2>Add Bus</h2>
                            <form method="post" action="{{ route('admin.buses.store') }}">
                                @csrf
                                <div class="field"><label>Registration</label><input name="registration_number" placeholder="T456 XYZ" required></div>
                                <div class="field"><label>Device ID</label><input name="device_id" placeholder="BUS-002" required></div>
                                <div class="field"><label>Device Token</label><input name="device_token" placeholder="Optional"></div>
                                <div class="field"><label>Capacity</label><input type="number" name="capacity" value="30" required></div>
                                <div class="field">
                                    <label>Driver</label>
                                    <select name="driver_id">
                                        <option value="">No driver yet</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn" type="submit">Save Bus</button>
                            </form>
                        </div>

                        <div class="card">
                            <h2>Add Driver</h2>
                            <form method="post" action="{{ route('admin.drivers.store') }}">
                                @csrf
                                <div class="field"><label>Name</label><input name="name" required></div>
                                <div class="field"><label>Email</label><input type="email" name="email" required></div>
                                <div class="field"><label>Password</label><input name="password" placeholder="Default: password"></div>
                                <button class="btn" type="submit">Save Driver</button>
                            </form>
                        </div>

                        <div class="card">
                            <h2>Add Student</h2>
                            <form method="post" action="{{ route('admin.students.store') }}">
                                @csrf
                                <div class="field"><label>Admission Number</label><input name="admission_number" required></div>
                                <div class="form-row">
                                    <div class="field"><label>First Name</label><input name="first_name" required></div>
                                    <div class="field"><label>Last Name</label><input name="last_name" required></div>
                                </div>
                                <div class="field"><label>Class</label><input name="class_name"></div>
                                <button class="btn" type="submit">Save Student</button>
                            </form>
                        </div>

                        <div class="card full">
                            <h2>Database Records</h2>
                            <table class="table">
                                <thead><tr><th>Bus</th><th>Driver</th><th>Route</th><th>Students</th><th>Latest GPS</th></tr></thead>
                                <tbody>
                                @foreach ($buses as $bus)
                                    <tr>
                                        <td>{{ $bus->registration_number }}<br><span class="muted">{{ $bus->device_id }}</span></td>
                                        <td>{{ $bus->driver?->name ?? 'No driver' }}</td>
                                        <td>{{ $bus->routes->pluck('name')->join(', ') ?: 'No route' }}</td>
                                        <td>{{ $bus->students->pluck('first_name')->join(', ') ?: 'No students' }}</td>
                                        <td>{{ $bus->latestLocation ? $bus->latestLocation->latitude.', '.$bus->latestLocation->longitude : 'No GPS yet' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($section === 'route')
                    <div class="grid">
                        <div class="card">
                            <h2>Add Route</h2>
                            <form method="post" action="{{ route('admin.routes.store') }}">
                                @csrf
                                <div class="field"><label>Route Name</label><input name="name" placeholder="Route 02" required></div>
                                <div class="field"><label>Start</label><input name="start_location" placeholder="Northside"></div>
                                <div class="field"><label>End</label><input name="end_location" placeholder="School"></div>
                                <div class="field">
                                    <label>Assign Bus</label>
                                    <select name="bus_id">
                                        <option value="">No bus yet</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">{{ $bus->registration_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn" type="submit">Save Route</button>
                            </form>
                        </div>
                        <div class="card">
                            <h2>Add Bus Stop</h2>
                            <form method="post" action="{{ route('admin.stops.store') }}">
                                @csrf
                                <div class="field">
                                    <label>Route</label>
                                    <select name="route_id" required>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field"><label>Stop Name</label><input name="name" required></div>
                                <div class="form-row">
                                    <div class="field"><label>Latitude</label><input name="latitude" value="-3.3869" required></div>
                                    <div class="field"><label>Longitude</label><input name="longitude" value="36.6830" required></div>
                                </div>
                                <div class="field"><label>Order</label><input type="number" name="stop_order" value="1" required></div>
                                <button class="btn" type="submit">Save Stop</button>
                            </form>
                        </div>
                        <div class="card">
                            <h2>Assign Student</h2>
                            <form method="post" action="{{ route('admin.assignments.store') }}">
                                @csrf
                                <div class="field">
                                    <label>Student</label>
                                    <select name="student_id" required>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Bus</label>
                                    <select name="bus_id" required>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">{{ $bus->registration_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Pickup Stop</label>
                                    <select name="bus_stop_id">
                                        <option value="">No stop</option>
                                        @foreach ($stops as $stop)
                                            <option value="{{ $stop->id }}">{{ $stop->route?->name }} - {{ $stop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn blue" type="submit">Assign</button>
                            </form>
                        </div>
                        <div class="card full">
                            <h2>Routes and Stops</h2>
                            <table class="table">
                                <thead><tr><th>Route</th><th>Start</th><th>End</th><th>Buses</th><th>Stops</th></tr></thead>
                                <tbody>
                                @foreach ($routes as $route)
                                    <tr>
                                        <td>{{ $route->name }}</td>
                                        <td>{{ $route->start_location }}</td>
                                        <td>{{ $route->end_location }}</td>
                                        <td>{{ $route->buses->pluck('registration_number')->join(', ') ?: 'No bus' }}</td>
                                        <td>{{ $route->stops->pluck('name')->join(', ') ?: 'No stops' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($section === 'requests')
                    <div class="grid">
                        <div class="card">
                            <h2>Create Trip</h2>
                            <form method="post" action="{{ route('admin.trips.store') }}">
                                @csrf
                                <div class="field">
                                    <label>Bus</label>
                                    <select name="bus_id" required>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">{{ $bus->registration_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Route</label>
                                    <select name="route_id">
                                        <option value="">No route</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="field">
                                        <label>Status</label>
                                        <select name="status"><option value="scheduled">Scheduled</option><option value="active">Active</option><option value="completed">Completed</option></select>
                                    </div>
                                    <div class="field"><label>ETA Minutes</label><input type="number" name="predicted_eta_minutes" value="45"></div>
                                </div>
                                <button class="btn" type="submit">Save Trip</button>
                            </form>
                        </div>
                        <div class="card wide">
                            <h2>Open Requests / Ongoing Trips</h2>
                            <table class="table">
                                <thead><tr><th>Bus</th><th>Route</th><th>Status</th><th>ETA</th><th>Complete</th></tr></thead>
                                <tbody>
                                @forelse ($activeTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->bus?->registration_number }}</td>
                                        <td>{{ $trip->route?->name ?? 'No route' }}</td>
                                        <td><span class="pill blue">{{ $trip->status }}</span></td>
                                        <td>{{ $trip->predicted_eta_minutes ?? '-' }} min</td>
                                        <td>
                                            <form method="post" action="{{ route('admin.trips.complete', $trip) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="actual_duration_minutes" value="{{ $trip->predicted_eta_minutes ?? 45 }}" min="1" style="max-width:90px">
                                                <button class="btn green" type="submit">Done</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No open requests.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($section === 'settings')
                    <div class="grid">
                        <div class="card">
                            <h2>System Settings</h2>
                            <div class="field"><label>School Name</label><input value="LittleMove School"></div>
                            <div class="field"><label>SMS Gateway</label><input value="Not connected"></div>
                            <div class="field"><label>Google Maps Key</label><input placeholder="Add API key later"></div>
                            <button class="btn">Save Settings</button>
                        </div>
                        <div class="card wide">
                            <h2>Completed Trips Report</h2>
                            <table class="table">
                                <thead><tr><th>Bus</th><th>Route</th><th>Completed</th><th>Duration</th><th>ETA Variance</th><th>Performance</th></tr></thead>
                                <tbody>
                                @forelse ($completedTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->bus?->registration_number }}</td>
                                        <td>{{ $trip->route?->name ?? 'No route' }}</td>
                                        <td>{{ $trip->completed_at?->format('d M, H:i') }}</td>
                                        <td>{{ $trip->actual_duration_minutes ?? '-' }} min</td>
                                        <td>{{ $trip->eta_variance_minutes ?? '-' }} min</td>
                                        <td><span class="pill {{ abs((int) $trip->eta_variance_minutes) <= 5 ? '' : 'blue' }}">{{ abs((int) $trip->eta_variance_minutes) <= 5 ? 'Accurate' : 'Review' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No completed trips yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </main>
</div>
</body>
</html>
