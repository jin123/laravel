<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockBrand extends Model {

    protected $connection = 'mysql';
    protected $table = 'mock_brand';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function mockBrand() {
        return $this->belongsTo("\App\Models\MockBrandLink");
    }

}
