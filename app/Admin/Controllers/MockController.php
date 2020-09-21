<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
#use App\Http\Controllers\Controller;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Illuminate\Http\Request;

class MockController extends AdminController {

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'mock接口列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid(app("\App\Models\MockLink"));
        $grid->model()
                ->orderBy('id', 'desc')
                ->paginate(15);
        $grid->column('id', "所属品类")->display(function($id) {
            $result = app("\App\Models\MockBrandLink")->where("link_id", $id)->first()->mockBrandLink;
            return !empty($result) ? $result->name : '';
        });
        $grid->column('title', '接口名字');
        $grid->column('request_url', 'mock接口地址');
        $grid->column('method', '请求方式');
        $grid->column('is_dynamic', '动态变化数据')->display(function($idD) {
            return !empty($idD) ? '是' : '否';
        });
        $grid->column('content_type', 'conntent-type');
        // $grid->disableActions();
        $grid->disableColumnSelector();
        $states = [
            'on' => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ];
        $grid->column('status', "状态")->editable('select', [1 => '开启', 0 => '关闭']);

        $grid->column('created_at', '创建时间')->display(function ($ctime) {
            return date("Y-m-d H:i:s", strtotime($ctime));
        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
            $editButton = app("App\Admin\Actions\MockLink\Edit");
            $actions->add($editButton);
        });
        $grid->disableExport();
        $grid->disableFilter();
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id) {
        $show = new Show(ExampleModel::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new ExampleModel);

        $form->display('id', __('ID'));
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        return $form;
    }

    public function add(Content $content) {
        $brands = app("\App\Models\MockBrand")->where("status", 1)->get()->toArray();
        $brands = array_column($brands, "name", "id");
        $contentTypes = app("\App\Servers\MockLinkSer")->contentType;
        $form = new Form(app("\App\Models\MockLink"));
        $form->text('title', '接口名字')->placeholder('请输入接口名字');
        $form->select('content_type', 'content-type')->options($contentTypes);
        $status = [
            1 => '开启',
            0 => '关闭',
        ];
        $form->select('status', '开关')->options($status)->value(1);
        $form->select('brand_id', '所属品类')->options($brands);
        $form->text("request_url", "mock地址");
        $form->select('method', '请求方式')->options(app("\App\Servers\MockLinkSer")->methods);
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            //$footer->formatAttribute(["autocomplate" => "off"]);
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        $form->isCreating();
        $isDynamic = [
            '1' => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            '0' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];

        $form->switch("is_dynamic", "动态返回数据")->states($isDynamic);
        $form->textarea("content", "mock返回的内容")->placeholder('mock地址返回的内容');
        $form->confirm('确定创建吗？', 'create');
        $form->setAction(admin_url('mock/addLink'));

        $form->setTitle("新建mock地址");
        return $content
                        ->title("新建mock地址")
                        ->body($form);
    }

    public function addLink(Request $req) {

        $brandId = intval($req->input("brand_id"));
        $title = $req->input("title");
        $contentType = $req->input("content_type");
        $requestUrl = $req->input("request_url");
        $method = $req->input("method");
        $isD = intval($req->input("is_dynamic"));
        $content = $req->input("content");
        $status = intval($req->input("status"));
        $result = app("\App\Servers\MockLinkSer")->creatOrUpdate($brandId, $title, $contentType, $requestUrl, $method, $isD, $content, $status);
        if (!empty($result)) {
            return redirect(admin_url('mock/index'));
        }

        abort(500, '新建失败新建失败新建失败新建失败');
    }

    public function rowsUpdate(Request $request) {
        $field = $request->input('name');
        $id = $request->input('pk');
        $data[$field] = $request->input('value');
        $get = app("\App\Models\MockLink")->find($id);
        $get->$field = $request->input('value');
        $res = $get->save();
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

    public function editMockLink(Request $request) {
        $id = $request->input("id");
        $linkModel = app("\App\Models\MockLink");
        $get = $linkModel->find($id);
        $brands = app("\App\Models\MockBrand")->where("status", 1)->get()->toArray();
        $brands = array_column($brands, "name", "id");
        $contentTypes = app("\App\Servers\MockLinkSer")->contentType;
        $form = new Form($get);
        $get = $get->toArray();
        $form->hidden("link_id")->value($get["id"]);

        $form->text('title', '接口名字')->placeholder('请输入接口名字')->value($get["title"]);
        $form->select('content_type', 'content-type')->options($contentTypes)->value($get["content_type"]);
        $status = [
            1 => '开启',
            0 => '关闭',
        ];
        $form->select('status', '开关')->options($status)->value($get["status"]);
        $getBrand = app("\App\Models\MockBrandLink")->where("link_id", $get["id"])->first()->mockBrandLink;

        $form->select('brand_id', '所属品类')->options($brands)->value($getBrand["id"]);
        $form->text("request_url", "mock地址")->value($get["request_url"]);
        $form->select('method', '请求方式')->options(app("\App\Servers\MockLinkSer")->methods)->value($get["method"]);
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            //$footer->formatAttribute(["autocomplate" => "off"]);
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        // $form->isEditing();
        $isDynamic = [
            '1' => "打开",
            '0' => "关闭",
        ];
        $form->select('is_dynamic', '动态返回数据')->options($isDynamic)->value($get["is_dynamic"]);
        $form->textarea("content", "mock返回的内容")->placeholder('mock地址返回的内容')->value($get["content"]);
        $form->setAction(admin_url('mock/editOpt'));
        $form->setTitle("编辑mock地址");
        $content = new Content();
        return $content
                        ->title("编辑mock地址")
                        ->body($form);
    }

    public function editOpt(Request $req) {
        $id = $req->input("link_id");
        $brandId = intval($req->input("brand_id"));
        $title = $req->input("title");
        $contentType = $req->input("content_type");
        $requestUrl = $req->input("request_url");
        $method = $req->input("method");
        $isD = intval($req->input("is_dynamic"));
        $content = $req->input("content");
        $status = intval($req->input("status"));
        $result = app("\App\Servers\MockLinkSer")->creatOrUpdate($brandId, $title, $contentType, $requestUrl, $method, $isD, $content, $status, $id);

        // $res = $get->save();
        /// $res = app("\App\Models\MockBrand")->where('id', $id)->update($data);
        if (!empty($result)) {
            return redirect(admin_url('mock/index'));
        } else {
            abort(500, '编辑失败');
        }
    }

}
