<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\Sales;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;

class SalesController
{
    /**
     * 获取销售记录列表
     * @return Response
     */
    public function index(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');
        $store_id = Request::param('store_id', '');
        $start_date = Request::param('start_date', '');
        $end_date = Request::param('end_date', '');

        $query = Sales::with(['store', 'salesperson', 'phoneBrand', 'phoneModel']);

        if (!empty($keyword)) {
            $query->where('customer_name|customer_phone|imei', 'like', "%{$keyword}%");
        }
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        if (!empty($start_date)) {
            $query->whereTime('create_time', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $query->whereTime('create_time', '<=', $end_date);
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->order('create_time', 'desc')->select();

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
     * 创建销售记录
     * @return Response
     */
    public function create(): Response
    {
        $data = Request::only([
            'store_id', 'salesperson_id', 'phone_brand_id', 'phone_model_id',
            'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'
        ]);

        try {
            validate(\app\validate\Sales::class)
                ->scene('create')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证IMEI是否已存在
        if (Sales::where('imei', $data['imei'])->find()) {
            return json(['code' => 1, 'msg' => '该IMEI号已存在']);
        }

        $sales = new Sales($data);
        if ($sales->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新销售记录
     * @param int $id
     * @return Response
     */
    public function update(int $id): Response
    {
        $data = Request::only([
            'store_id', 'salesperson_id', 'phone_brand_id', 'phone_model_id',
            'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'
        ]);

        try {
            validate(\app\validate\Sales::class)
                ->scene('update')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $sales = Sales::find($id);
        if (!$sales) {
            return json(['code' => 1, 'msg' => '销售记录不存在']);
        }

        // 如果修改了IMEI，需要验证新的IMEI是否已存在
        if ($data['imei'] !== $sales->imei && Sales::where('imei', $data['imei'])->find()) {
            return json(['code' => 1, 'msg' => '该IMEI号已存在']);
        }

        if ($sales->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除销售记录
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $sales = Sales::find($id);
        if (!$sales) {
            return json(['code' => 1, 'msg' => '销售记录不存在']);
        }

        if ($sales->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 获取销售记录详情
     * @param int $id
     * @return Response
     */
    public function detail(int $id): Response
    {
        $sales = Sales::with(['store', 'salesperson', 'phoneBrand', 'phoneModel'])->find($id);
        if (!$sales) {
            return json(['code' => 1, 'msg' => '销售记录不存在']);
        }

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $sales
        ]);
    }
} 