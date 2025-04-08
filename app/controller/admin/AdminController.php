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
        $page = (int)Request::param('page', 1);
        $limit = (int)Request::param('limit', 10);
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
            validate([
                'username|用户名' => 'require|length:3,20',
                'password|密码' => 'require|length:6,20',
                'nickname|昵称' => 'require|length:2,20',
                'role|角色' => 'require|in:admin,editor'
            ])->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证用户名是否已存在
        if (Admin::where('username', $data['username'])->find()) {
            return json(['code' => 1, 'msg' => '用户名已存在']);
        }

        // 密码加密
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

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
        
        if ($id <= 0) {
            return json(['code' => 1, 'msg' => '无效的管理员ID']);
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
        if ($id <= 0) {
            return json(['code' => 1, 'msg' => '无效的管理员ID']);
        }

        $admin = Admin::find($id);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在']);
        }

         // 获取当前登录管理员信息
         $currentAdmin = Request::instance()->adminInfo;
         // 不允许删除自己的账号
        if ($currentAdmin->id === $id) {
             return json(['code' => 1, 'msg' => '不能删除当前登录的管理员账号']);
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
        // 获取当前登录管理员信息
        $currentAdmin = Request::instance()->adminInfo;
        // 不允许禁用自己的账号
        if ($currentAdmin->id === $id) {
            return json(['code' => 1, 'msg' => '不能禁用当前登录的管理员账号']);
        }

        $status = (int)Request::param('status');
        if (!in_array($status, [0, 1])) {
            return json(['code' => 1, 'msg' => '状态值只能是0或1']);
        }

        $admin = Admin::find($id);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在']);
        }

        $admin->status = $status;
        if ($admin->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
    }

    /**
     * 更新管理员密码
     * @return Response
     */
    public function updatePassword(): Response
    {
        $data = Request::only(['old_password', 'new_password', 'confirm_password']);
        
        try {
            validate([
                'old_password|原密码' => 'require|length:6,20',
                'new_password|新密码' => 'require|length:6,20',
                'confirm_password|确认密码' => 'require|confirm:new_password'
            ])->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 获取当前登录管理员信息
        $admin = Request::instance()->adminInfo;
        if (!$admin) {
            return json(['code' => 1, 'msg' => '未登录或登录已过期']);
        }

        // 验证原密码
        if (!$admin->verifyPassword($data['old_password'])) {
            return json(['code' => 1, 'msg' => '原密码错误']);
        }

        // 更新密码（会自动使用password_hash加密）
        $admin->password = $data['new_password'];
        
        if ($admin->save()) {
            return json(['code' => 0, 'msg' => '密码修改成功']);
        }
        return json(['code' => 1, 'msg' => '密码修改失败']);
    }
} 