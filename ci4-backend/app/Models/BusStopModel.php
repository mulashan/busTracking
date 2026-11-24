<?php

namespace App\Models;

use CodeIgniter\Model;

class BusStopModel extends Model
{
    protected $table = 'bus_stops';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['route_id', 'name', 'latitude', 'longitude', 'stop_order'];
}
