<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\Salesperson;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;

class SalespersonController
{
    /**
     * 获取销售员列表
     * @return Response
     */
    public function index(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');
        $store_id = Request::param('store_id', '');

        $query = Salesperson::with(['store']);
        
        if (!empty($keyword)) {
            $query->where('name|phone|employee_id', 'like', "%{$keyword}%");
        }
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
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
     * 创建销售员
     * @return Response
     */
    public function create(): Response
    {
        $data = Request::only(['name', 'phone', 'store_id', 'employee_id']);
        
        try {
            validate(\app\validate\Salesperson::class)
                ->scene('create')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证工号是否已存在
        if (!empty($data['employee_id']) && Salesperson::where('employee_id', $data['employee_id'])->find()) {
            return json(['code' => 1, 'msg' => '工号已存在']);
        }

        $salesperson = new Salesperson($data);
        if ($salesperson->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新销售员信息
     * @param int $id
     * @return Response
     */
    public function update(int $id): Response
    {
        $data = Request::only(['name', 'phone', 'store_id', 'employee_id']);
        
        try {
            validate(\app\validate\Salesperson::class)
                ->scene('update')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $salesperson = Salesperson::find($id);
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '销售员不存在']);
        }

        // 如果修改了工号，需要验证新的工号是否已存在
        if (!empty($data['employee_id']) && $data['employee_id'] !== $salesperson->employee_id && 
            Salesperson::where('employee_id', $data['employee_id'])->find()) {
            return json(['code' => 1, 'msg' => '工号已存在']);
        }

        if ($salesperson->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除销售员
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $salesperson = Salesperson::find($id);
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '销售员不存在']);
        }

        // 检查销售员是否有关联的销售记录
        if ($salesperson->sales()->count() > 0) {
            return json(['code' => 1, 'msg' => '该销售员存在销售记录，无法删除']);
        }

        if ($salesperson->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 更新销售员状态
     * @param int $id
     * @return Response
     */
    public function updateStatus(int $id): Response
    {
        $data = ['status' => Request::param('status')];
        
        try {
            validate(\app\validate\Salesperson::class)
                ->scene('status')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $salesperson = Salesperson::find($id);
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '销售员不存在']);
        }

        $salesperson->status = $data['status'];
        if ($salesperson->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
    }
} 