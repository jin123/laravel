<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockBrandLink extends Model {

    protected $connection = 'mysql';
    protected $table = 'mock_brand_link';

    #  protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['brand_id', 'link_id'];

    /**
     * 获取与用户相关的电话记录。
     */
    public function mockBrandLink() {
        return $this->hasOne('App\Models\MockBrand', "id", "brand_id");
    }

}
