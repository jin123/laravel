<?php

namespace App\Admin\Actions\MockBrand;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Add extends RowAction {

    public $name = '新建';

    public function handle(Model $model) {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    /**
     * @return string
     */
    public function href() {
        return "/admin/mockBrand/add";
    }

}
