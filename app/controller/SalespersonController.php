<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\{Sales, Store, PhoneBrand, PhoneModel};
use think\facade\{Request, Cache};
use think\{Response, exception\ValidateException};
use app\service\NotificationService;

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

        // 将name等于'其它'的项排在最后
        $brands = $brands->toArray();
        usort($brands, function($a, $b) {
            return $a['name'] === '其它' ? 1 : ($b['name'] === '其它' ? -1 : 0);
        });

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
    public function salesSubmit(): Response
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
            // 发送销售通知
            try {
                $notificationService = new NotificationService();
                $notificationService->sendSalesNotification([
                    'id' => $sales->id,
                    'store_name' => $sales->store_name,
                    'salesperson_name' => $sales->salesperson_name,
                    'phone_brand_name' => $sales->phone_brand_name,
                    'phone_model_name' => $sales->phone_model_name,
                    'customer_name' => $sales->customer_name,
                    'customer_phone' => $sales->customer_phone,
                    'create_time' => $sales->create_time
                ]);
            } catch (\Exception $e) {
                // 记录错误但不影响主流程
                \think\facade\Log::error('发送销售通知失败：' . $e->getMessage());
            }

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

    /**
     * 获取今日销售记录
     * @return Response
     */
    public function todaySales(): Response
    {
        // 从请求中获取已登录的销售员信息
        $request = Request::instance();
        if (!isset($request->salesperson)) {
            return json(['code' => 1, 'msg' => '获取销售员信息失败']);
        }
        $salesperson = $request->salesperson;

        // 获取今日开始和结束时间
        $today = date('Y-m-d');
        $startTime = $today . ' 00:00:00';
        $endTime = $today . ' 23:59:59';

        try {
            // 查询今日销售记录
            $sales = Sales::where('salesperson_id', $salesperson->id)
                ->whereTime('create_time', 'between', [$startTime, $endTime])
                ->order('create_time', 'desc')
                ->select()
                ->map(function ($item) {
                    // 处理图片URL
                    $photoUrls = !empty($item->photo_url) ? explode(',', $item->photo_url) : [];
                    
                    return [
                        'id' => $item->id,
                        'store_name' => $item->store_name,
                        'phone_brand_name' => $item->phone_brand_name,
                        'phone_model_name' => $item->phone_model_name,
                        'imei' => $item->imei,
                        'customer_name' => $item->customer_name,
                        'customer_phone' => $item->customer_phone,
                        'photo_url' => $photoUrls,
                        'remark' => $item->remark,
                        'create_time' => $item->create_time
                    ];
                });

            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'total' => count($sales),
                    'list' => $sales
                ]
            ]);
        } catch (\Exception $e) {
            // 记录错误日志
            \think\facade\Log::error('获取销售记录失败：' . $e->getMessage());
            return json(['code' => 1, 'msg' => '获取销售记录失败，请稍后重试']);
        }
    }
} 