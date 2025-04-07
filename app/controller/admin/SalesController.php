<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\Sales;
use app\model\Store;
use app\model\Salesperson;
use app\model\PhoneBrand;
use app\model\PhoneModel;
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
                'store' => $item->store ? $item->store->name : '',
                'salesperson' => $item->salesperson ? $item->salesperson->name : '',
                'phone_brand' => $item->phoneBrand ? $item->phoneBrand->name : '',
                'phone_model' => $item->phoneModel ? $item->phoneModel->name : '',
                'imei' => $item->imei,
                'customer_name' => $item->customer_name,
                'customer_phone' => $item->customer_phone,
                'create_time' => $item->create_time,
                // 可以根据需要添加更多字段
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
     * 创建销售记录
     * @return Response
     */
    public function create(): Response
    {
        $data = Request::only([
            'store_id', 'store_name', 
            'salesperson_id', 'salesperson_name',
            'phone_brand_id', 'phone_brand_name',
            'phone_model_id', 'phone_model_name',
            'imei', 'customer_name', 'customer_phone', 
            'photo_url', 'remark'
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

        // 如果提供了ID，则自动获取对应的名称
        if (!empty($data['store_id'])) {
            $store = Store::find($data['store_id']);
            if (!$store) {
                return json(['code' => 1, 'msg' => '选择的门店不存在']);
            }
            $data['store_name'] = $store->name;
        }

        if (!empty($data['salesperson_id'])) {
            $salesperson = Salesperson::find($data['salesperson_id']);
            if (!$salesperson) {
                return json(['code' => 1, 'msg' => '选择的销售员不存在']);
            }
            $data['salesperson_name'] = $salesperson->name;
        }

        if (!empty($data['phone_brand_id'])) {
            $brand = PhoneBrand::find($data['phone_brand_id']);
            if (!$brand) {
                return json(['code' => 1, 'msg' => '选择的手机品牌不存在']);
            }
            $data['phone_brand_name'] = $brand->name;
        }

        if (!empty($data['phone_model_id'])) {
            $model = PhoneModel::find($data['phone_model_id']);
            if (!$model) {
                return json(['code' => 1, 'msg' => '选择的手机型号不存在']);
            }
            $data['phone_model_name'] = $model->name;
        }

        $sales = new Sales($data);
        if ($sales->save()) {
            return json([
                'code' => 0, 
                'msg' => '创建成功',
                'data' => [
                    'id' => $sales->id,
                    'store' => $sales->store_name,
                    'salesperson' => $sales->salesperson_name,
                    'phone_brand' => $sales->phone_brand_name,
                    'phone_model' => $sales->phone_model_name,
                    'imei' => $sales->imei,
                    'customer_name' => $sales->customer_name,
                    'customer_phone' => $sales->customer_phone,
                    'create_time' => $sales->create_time
                ]
            ]);
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
        $sales = Sales::find($id);
        if (!$sales) {
            return json(['code' => 1, 'msg' => '销售记录不存在']);
        }

        $data = Request::only([
            'store_id', 'store_name', 
            'salesperson_id', 'salesperson_name',
            'phone_brand_id', 'phone_brand_name',
            'phone_model_id', 'phone_model_name',
            'imei', 'customer_name', 'customer_phone', 
            'photo_url', 'remark'
        ]);

        try {
            validate(\app\validate\Sales::class)
                ->scene('update')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 如果修改了IMEI，需要验证新的IMEI是否已存在
        if (!empty($data['imei']) && $data['imei'] !== $sales->imei && Sales::where('imei', $data['imei'])->find()) {
            return json(['code' => 1, 'msg' => '该IMEI号已存在']);
        }

        // 如果提供了ID，则自动获取对应的名称
        if (!empty($data['store_id'])) {
            $store = Store::find($data['store_id']);
            if (!$store) {
                return json(['code' => 1, 'msg' => '选择的门店不存在']);
            }
            $data['store_name'] = $store->name;
        }

        if (!empty($data['salesperson_id'])) {
            $salesperson = Salesperson::find($data['salesperson_id']);
            if (!$salesperson) {
                return json(['code' => 1, 'msg' => '选择的销售员不存在']);
            }
            $data['salesperson_name'] = $salesperson->name;
        }

        if (!empty($data['phone_brand_id'])) {
            $brand = PhoneBrand::find($data['phone_brand_id']);
            if (!$brand) {
                return json(['code' => 1, 'msg' => '选择的手机品牌不存在']);
            }
            $data['phone_brand_name'] = $brand->name;
        }

        if (!empty($data['phone_model_id'])) {
            $model = PhoneModel::find($data['phone_model_id']);
            if (!$model) {
                return json(['code' => 1, 'msg' => '选择的手机型号不存在']);
            }
            $data['phone_model_name'] = $model->name;
        }

        if ($sales->save($data)) {
            return json([
                'code' => 0, 
                'msg' => '更新成功',
                'data' => [
                    'id' => $sales->id,
                    'store' => $sales->store_name,
                    'salesperson' => $sales->salesperson_name,
                    'phone_brand' => $sales->phone_brand_name,
                    'phone_model' => $sales->phone_model_name,
                    'imei' => $sales->imei,
                    'customer_name' => $sales->customer_name,
                    'customer_phone' => $sales->customer_phone,
                    'create_time' => $sales->create_time
                ]
            ]);
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

        // 格式化详情数据，只返回需要的字段
        $detailData = [
            'id' => $sales->id,
            'store' => $sales->store ? $sales->store->name : '',
            'salesperson' => $sales->salesperson ? $sales->salesperson->name : '',
            'phone_brand' => $sales->phoneBrand ? $sales->phoneBrand->name : '',
            'phone_model' => $sales->phoneModel ? $sales->phoneModel->name : '',
            'imei' => $sales->imei,
            'customer_name' => $sales->customer_name,
            'customer_phone' => $sales->customer_phone,
            'create_time' => $sales->create_time
        ];

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $detailData
        ]);
    }
} 