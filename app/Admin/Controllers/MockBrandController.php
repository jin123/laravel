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
use Illuminate\Support\MessageBag;

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


        $grid = new Grid(app("\App\Models\MockBrand"));
        $grid->model()
                ->orderBy('id', 'desc')
                ->paginate(15);
        $grid->column('id', 'ID');
        $grid->column('name', '品类名字')->editable();
        $grid->column('proj_type', '品牌类型');
        // $grid->disableActions();
        $grid->disableColumnSelector();

        // 全部关闭
        // $grid->disableActions();
        // $grid->column('status', '状态')->using(['1' => '启用', '0' => '禁用']);
        $states = [
            'on' => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ];
        // $grid->column('status',"状态")->switch($states);
        $grid->column('status', "状态")->editable('select', [1 => '开启', 0 => '关闭']);
        $grid->column('created_at', '创建时间')->display(function ($ctime) {
            return date("Y-m-d H:i:s", strtotime($ctime));
        });
        $grid->column('updated_at', '更新时间')->display(function ($utime) {
            return !empty($utime) ? date("Y-m-d H:i:s", strtotime($utime)) : '';
        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
            $editButton = app("App\Admin\Actions\MockBrand\BrandEdit");
            $actions->add($editButton);
        });
        $grid->disableExport();
        $grid->disableFilter();
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
        $form = new Form(app("\App\Models\MockBrand"));
        $form->display('id', 'ID');
        $form->text('name', "名字");
        $directors = [
            1 => '开启',
            0 => '关闭',
        ];
        $form->select('status', '开关')->options($directors);
        return $form;
    }

    public function editBrand(Request $request) {
        $id = $request->input('id');
        $get = app("\App\Models\MockBrand")->where('id', $id)->first();
        $get = !empty($get) ? $get->toArray() : [];
        $form = new Form(app("App\Models\MockBrand"));
        $form->hidden("brand_id")->value($id);
        $form->text('name', '名字')->value($get["name"] ?? '');
        $projType = app("\App\Servers\MockBrandSer")->projectType;
        $form->select('proj_type', '品牌类型')->options($projType)->value($get["proj_type"]);
        $form->select("status", "是否启用")->options([1 => '启用', 0 => '禁用'])->value($get["status"] ?? 0);
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        // $form->isUpdating();
        $form->confirm('确定更新吗？', 'edit');
        $form->setTitle("编辑");
        $form->setAction('/admin/mockBrand/editBrandOpt');
        $content = new Content();
        return $content
                        ->title("编辑mock地址")
                        ->body($form);
    }

    public function rowsUpdate(Request $request) {
        $field = $request->input('name');
        $id = $request->input('pk');
        $data[$field] = $request->input('value');
        $get = app("\App\Models\MockBrand")->find($id);
        $get->$field = $request->input('value');
        $res = $get->save();
        /// $res = app("\App\Models\MockBrand")->where('id', $id)->update($data);
        if (!empty($res)) {
            return response()->json([
                        'status' => 0,
                        'message' => 'success',
            ]);
        } else {
            return response()->json([
                        'status' => 100,
                        'message' => 'false',
            ]);
        }
    }

    public function createBrand(Content $content) {
        $form = new Form(app("\App\Models\MockBrand"));
        $form->text('name', '名字');
        $projType = app("\App\Servers\MockBrandSer")->projectType;
        $form->select('proj_type', '品牌类型')->options($projType)->value("supplier");
        $directors = [
            1 => '开启',
            0 => '关闭',
        ];
        $form->select('status', '开关')->options($directors);
        $form->tools(
                function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        }
        );

        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        $form->isCreating();
        $form->confirm('确定创建吗？', 'create');
        $form->setAction(admin_url('mockBrand/addOpt'));

        $form->setTitle("新建品类");
        return $content
                        ->title("新建品类")
                        ->body($form);
    }

    public function addRes(Request $request) {
        $name = $request->input('name');
        $projType = $request->input("proj_type");
        $status = $request->input('status');
        $createdAt = date("Y-m-d H:i:s", time());
        $result = app("\App\Models\MockBrand")->insert(
                [
                    "name" => $name,
                    "status" => $status,
                    "proj_type" => $projType,
                    "created_at" => $createdAt
                ]
        );
        if (!empty($result)) {
            $success = new MessageBag([
                'title' => '提示',
                'message' => '创建成功',
            ]);
            return redirect(admin_url('mockBrand/index'));
        } else {
            abort(500, "新增失败");
        }
    }

    public function editBrandOpt(Request $request) {
        $id = $request->input('brand_id');
        $name = $request->input('name');
        $projType = $request->input("proj_type");
        $status = $request->input('status');
        $createdAt = date("Y-m-d H:i:s", time());
        $result = app("\App\Models\MockBrand")->where("id", $id)->update(
                [
                    "name" => $name,
                    "status" => $status,
                    "proj_type" => $projType,
                    "created_at" => $createdAt
                ]
        );
        if (!empty($result)) {
            $success = new MessageBag([
                'title' => '提示',
                'message' => '编辑成功',
            ]);
            return redirect(admin_url('mockBrand/index'));
        } else {
            abort(500, "编辑失败或者没有可编辑项");
        }
    }

    public function post() {
        
    }

}
