<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\{Sales, Store, PhoneBrand, PhoneModel};
use think\facade\{Request, Cache};
use think\{Response, exception\ValidateException};

class SalespersonController
{
    /**
     * 获取门店列表
     * @return Response
     */
    public function stores(): Response
    {
        $stores = Store::where('status', 1)
            ->field(['id', 'name', 'address', 'phone'])
            ->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $stores
        ]);
    }

    /**
     * 获取手机品牌列表
     * @return Response
     */
    public function phoneBrands(): Response
    {
        $brands = PhoneBrand::where('status', 1)
            ->field(['id', 'name', 'logo as logo_url'])
            ->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $brands
        ]);
    }

    /**
     * 获取手机型号列表
     * @return Response
     */
    public function phoneModels(): Response
    {
        $brandId = Request::param('brand_id');
        if (empty($brandId)) {
            return json(['code' => 1, 'msg' => '请选择手机品牌']);
        }

        $models = PhoneModel::where('status', 1)
            ->where('brand_id', $brandId)
            ->field(['id', 'name', 'image'])
            ->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $models
        ]);
    }

    /**
     * 提交销售记录
     * @return Response
     */
    public function submitSales(): Response
    {
        // 从请求中获取已登录的销售员信息
        $request = Request::instance();
        if (!isset($request->salesperson)) {
            return json(['code' => 1, 'msg' => '获取销售员信息失败']);
        }
        $salesperson = $request->salesperson;
        
        $data = Request::only([
            'store_id', 'store_name',
            'phone_brand_id', 'phone_brand_name',
            'phone_model_id', 'phone_model_name',
            'imei', 'customer_name', 'customer_phone',
            'photo_url', 'remark'
        ]);

        // 处理照片URL
        if (isset($data['photo_url']) && is_array($data['photo_url'])) {
            $data['photo_url'] = implode(',', $data['photo_url']);
        }

        // 添加销售员信息
        $data['salesperson_id'] = $salesperson->id;
        $data['salesperson_name'] = $salesperson->name;

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

        // 验证门店
        if (!empty($data['store_id'])) {
            $store = Store::find($data['store_id']);
            if (!$store) {
                return json(['code' => 1, 'msg' => '选择的门店不存在']);
            }
            $data['store_name'] = $store->name;
        }

        // 验证手机品牌
        if (!empty($data['phone_brand_id'])) {
            $brand = PhoneBrand::find($data['phone_brand_id']);
            if (!$brand) {
                return json(['code' => 1, 'msg' => '选择的手机品牌不存在']);
            }
            $data['phone_brand_name'] = $brand->name;
        }

        // 验证手机型号
        if (!empty($data['phone_model_id'])) {
            $model = PhoneModel::find($data['phone_model_id']);
            if (!$model) {
                return json(['code' => 1, 'msg' => '选择的手机型号不存在']);
            }
            $data['phone_model_name'] = $model->name;
        }

        $sales = new Sales($data);
        if ($sales->save()) {
            // 返回时将photo_url转换回数组
            $photoUrls = !empty($sales->photo_url) ? explode(',', $sales->photo_url) : [];
            
            return json([
                'code' => 0,
                'msg' => '提交成功',
                'data' => [
                    'id' => $sales->id,
                    'store' => $sales->store_name,
                    'phone_brand' => $sales->phone_brand_name,
                    'phone_model' => $sales->phone_model_name,
                    'imei' => $sales->imei,
                    'customer_name' => $sales->customer_name,
                    'customer_phone' => $sales->customer_phone,
                    'photo_url' => $photoUrls,
                    'create_time' => $sales->create_time
                ]
            ]);
        }
        return json(['code' => 1, 'msg' => '提交失败']);
    }
} 