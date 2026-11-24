<?php

namespace App\Models;

use CodeIgniter\Model;

class BusModel extends Model
{
    protected $table = 'buses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['registration_number', 'device_id', 'device_token_hash', 'capacity', 'driver_id', 'is_active'];
}
