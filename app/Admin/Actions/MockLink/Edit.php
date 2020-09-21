<?php

namespace App\Admin\Actions\MockLink;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Edit extends RowAction {

    public $name = '编辑';

    public function handle(Model $model) {


        return $this->response()->success('Success message.')->refresh();
    }

    /**
     * @return  string
     */
    public function href() {
        return "/admin/mock/editMockLink?id=" . $this->getKey();
    }

}
