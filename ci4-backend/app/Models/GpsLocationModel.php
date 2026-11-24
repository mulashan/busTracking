<?php

namespace App\Models;

use CodeIgniter\Model;

class GpsLocationModel extends Model
{
    protected $table = 'gps_locations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['bus_id', 'latitude', 'longitude', 'speed_kmh', 'heading', 'recorded_at'];
}
