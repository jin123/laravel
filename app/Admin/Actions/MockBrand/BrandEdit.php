<?php

namespace App\Admin\Actions\MockBrand;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class BrandEdit extends RowAction {

    public $name = '编辑';

    public function handle(Model $model) {
        // $model ...
        $model->replicate()->save();

        return $this->response()->success('Success message.')->refresh();
    }

    /**
     * @return  string
     */
    public function href() {
        return "/admin/mockBrand/editBrand?id=" . $this->getKey();
    }

}
