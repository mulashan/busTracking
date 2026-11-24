<?php

namespace App\Models;

use CodeIgniter\Model;

class TripModel extends Model
{
    protected $table = 'trips';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['bus_id', 'route_id', 'status', 'scheduled_start_at', 'started_at', 'completed_at', 'predicted_eta_minutes', 'actual_duration_minutes', 'eta_variance_minutes', 'students_picked', 'students_remaining', 'distance_km', 'notes'];
}
