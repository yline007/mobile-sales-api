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
        $page = (int)Request::param('page', 1);
        $limit = (int)Request::param('limit', 10);
        $keyword = Request::param('keyword', '');
        $store_id = (int)Request::param('store_id', 0);

        $query = Salesperson::with(['store']);
        
        if (!empty($keyword)) {
            $query->where('name|phone|employee_id', 'like', "%{$keyword}%");
        }
        if ($store_id > 0) {
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
     * 更新销售员状态
     * @param int $id
     * @return Response
     */
    public function updateStatus(int $id): Response
    {
        if ($id <= 0) {
            return json(['code' => 1, 'msg' => '无效的销售员ID']);
        }

        $status = (int)Request::param('status');
        if (!in_array($status, [0, 1])) {
            return json(['code' => 1, 'msg' => '状态值只能是0或1']);
        }

        $salesperson = Salesperson::find($id);
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '销售员不存在']);
        }

        $salesperson->status = $status;
        if ($salesperson->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
    }
} 