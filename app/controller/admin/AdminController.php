<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\Admin;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;

class AdminController
{
    /**
     * 获取管理员列表
     * @return Response
     */
    public function index(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');

        $query = Admin::where('1=1');
        if (!empty($keyword)) {
            $query->where('username|nickname|email', 'like', "%{$keyword}%");
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total' => $total,
                'list' => $list
            ]
        ]);
    }

    /**
     * 创建管理员
     * @return Response
     */
    public function create(): Response
    {
        $data = Request::only(['username', 'password', 'nickname', 'email', 'role']);
        
        try {
            validate(\app\validate\Admin::class)
                ->scene('create')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证用户名是否已存在
        if (Admin::where('username', $data['username'])->find()) {
            return json(['code' => 1, 'msg' => '用户名已存在']);
        }

        $admin = new Admin($data);
        if ($admin->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新管理员信息
     * @param int $id
     * @return Response
     */
    public function update(int $id): Response
    {
        $data = Request::only(['nickname', 'email', 'role']);
        
        try {
            validate(\app\validate\Admin::class)
                ->scene('update')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $admin = Admin::find($id);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在']);
        }

        if ($admin->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除管理员
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在']);
        }

        if ($admin->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 更新管理员状态
     * @param int $id
     * @return Response
     */
    public function updateStatus(int $id): Response
    {
        $data = ['status' => Request::param('status')];
        
        try {
            validate(\app\validate\Admin::class)
                ->scene('status')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $admin = Admin::find($id);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在']);
        }

        $admin->status = $data['status'];
        if ($admin->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
    }
} 