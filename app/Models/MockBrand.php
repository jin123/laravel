<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockBrand extends Model {

    protected $connection = 'mysql';
    
    
    protected $table = 'mock_brand';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';

}
