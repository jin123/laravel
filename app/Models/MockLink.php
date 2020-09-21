<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockLink extends Model {

    protected $connection = 'mysql';
    protected $table = 'mock_link';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


}
