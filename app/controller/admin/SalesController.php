<?php

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Sales;
use think\facade\Request;
use think\Response;

class SalesController {
    
    /**
     * 获取销售记录列表
     * @return Response
     */
    public function index(): Response {
        $page = (int)Request::param('page', 1);
        $limit = (int)Request::param('limit', 10);
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

        // 格式化列表数据
        $formattedList = [];
        foreach ($list as $item) {
            $formattedList[] = [
                'id' => $item->id,
                'store' => $item->store ? $item->store->name :  $item->store_name,
                'salesperson' => $item->salesperson ? $item->salesperson->name : $item->salesperson_name,
                'phone_brand' => $item->phoneBrand ? $item->phoneBrand->name : $item->phone_brand_name,
                'phone_model' => $item->phoneModel ? $item->phoneModel->name : $item->phone_model_name,
                'imei' => $item->imei,
                'customer_name' => $item->customer_name,
                'customer_phone' => $item->customer_phone,
                'create_time' => $item->create_time
            ];
        }

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total' => $total,
                'list' => $formattedList
            ]
        ]);
    }

    /**
     * 删除销售记录
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response {
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
    public function detail(int $id): Response {
        $sales = Sales::with(['store', 'salesperson', 'phoneBrand', 'phoneModel'])->find($id);
        if (!$sales) {
            return json(['code' => 1, 'msg' => '销售记录不存在']);
        }

        // 处理照片URL
        $photoUrls = !empty($sales->photo_url) ? explode(',', $sales->photo_url) : [];

        // 格式化详情数据，只返回需要的字段
        $detailData = [
            'id' => $sales->id,
            'store' => $sales->store ? $sales->store->name :  $sales->store_name,
            'salesperson' => $sales->salesperson ? $sales->salesperson->name : $sales->salesperson_name,
            'phone_brand' => $sales->phoneBrand ? $sales->phoneBrand->name : $sales->phone_brand_name,
            'phone_model' => $sales->phoneModel ? $sales->phoneModel->name : $sales->phone_model_name,
            'imei' => $sales->imei,
            'customer_name' => $sales->customer_name,
            'customer_phone' => $sales->customer_phone,
            'photo_url' => $photoUrls,
            'remark' => $sales->remark,
            'create_time' => $sales->create_time
        ];

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $detailData
        ]);
    }
}
