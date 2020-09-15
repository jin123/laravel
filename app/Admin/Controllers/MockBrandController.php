<?php

namespace App\Admin\Controllers;

#use App\Models\MockBrand;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager as Session;

class MockBrandController extends AdminController {

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '品类管理';
    protected $columns = [
        'id' => 'ID',
        'title' => '显示',
        'url' => '地址',
        'status' => '状态',
        'create_at' => '创建时间',
        'update_at' => '更新时间',
    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {


        $grid = new Grid(new MockBrand());
        $grid->model()
                ->orderBy('id', 'desc')
                ->paginate(15);
        $grid->column('id', 'ID')->sortable();
        $grid->column('name', '品类名字')->editable();
        $grid->column('created_at', '创建时间');
        //   $grid->column('updated_at','更新时间');
        // 全部关闭
        // $grid->disableActions();
        $grid->column('status', '状态')->using(['1' => '启用', '0' => '禁用']);
        $grid->actions(function ($actions) {

            // 去掉删除
            //  $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            $editButton = app("App\Admin\Actions\MockBrand\BrandEdit");
            $actions->add($editButton);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id) {
        $show = new Show(MockBrand::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new MockBrand());



        return $form;
    }

    public function editBrand(Request $request) {
        $id = $request->input('id');
        $get = \App\Models\MockBrand::where('id', $id)->first();
        $get = !empty($get) ? $get->toArray() : [];
        $form = new Form(app("App\Models\MockBrand"));

// 显示记录id
// 添加text类型的input框
        $form->text('name', '名字')->value($get["name"] ?? '');


        $form->select("status", "是否启用")->options([1 => '启用', 0 => '禁用'])->value($get["status"] ?? 0);


        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            //  $footer->disableReset();
            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            //$footer->disableCreatingCheck();
        });
        $form->isUpdating();
        $form->confirm('确定更新吗？', 'edit');
        $form->setAction('/admin/mockBrand/editBrandOpt');
        return $form;

        //
    }

}
