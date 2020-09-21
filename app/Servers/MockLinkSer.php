<?php

namespace App\Servers;

use App\Servers\BaseSer;
use Illuminate\Support\Facades\DB;

class MockLinkSer extends BaseSer {

    public $contentType = [
        "application/x-www-form-urlencoded" => "application/x-www-form-urlencoded",
        "application/json" => "application/json",
        "multipart/form-data" => "multipart/form-data"
    ];
    public $methods = [
        "post" => "post",
        "get" => "get"
    ];

    public function creatOrUpdate(int $brandId, string $title, string $contentType, string $requestUrl, string $method, int $isD, string $content, int $status, $linkId = 0) {

        DB::beginTransaction();
        try {
            $model = empty($linkId) ? app("\App\Models\MockLink") : app("\App\Models\MockLink")->find($linkId);
            $model->title = $title;
            $model->content_type = $contentType;
            $model->request_url = $requestUrl;
            $model->method = $method;
            $model->is_dynamic = $isD;
            $model->content = $content;
            $model->status = $status;
            $res = $model->save();
            if (empty($res)) {
                throw new \Exception("新建或者创建失败");
            }
            $brandLindModel = app("\App\Models\MockBrandLink");

            $linkId = empty($linkId) ? $model->id : $linkId;
            $time = date("Y-m-d H:i:s", time());
            $addBrandLink = DB::insert('insert into mock_brand_link (brand_id, link_id,created_at,updated_at) values (?, ?,?,?) on duplicate key update link_id = VALUES(link_id),brand_id = VALUES(brand_id),created_at = VALUES(created_at), updated_at=VALUES(updated_at) ', [$brandId, $linkId, $time, $time]);
            if (empty($addBrandLink)) {
                throw new \Exception("新建或者编辑关联失败");
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            echo "*************\n";
            var_dump($e->getMessage());
            echo "*************\n";
            DB::rollback(); //事务回滚
            return false;
        }
        return true;
    }

}
