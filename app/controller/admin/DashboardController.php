<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use app\model\PhoneBrand;
use app\model\PhoneModel;
use app\model\Sales;
use app\model\Salesperson;
use app\model\Store;
use think\facade\Db;
use think\Request;
use think\Response;

/**
 * 仪表盘控制器
 * Class DashboardController
 * @package app\controller\admin
 */
class DashboardController extends BaseController
{
    /**
     * 获取销售统计数据
     *
     * @return Response
     */
    public function statistics(): Response
    {
        // 获取当日销量
        $todaySalesCount = Sales::whereDay('create_time')->count();
        
        // 获取门店总数
        $storeTotalCount = Store::where('status', 1)->count();
        
        // 获取销售员总数
        $salespersonTotalCount = Salesperson::where('status', 1)->count();
        
        // 获取当月销售额（演示用，实际中可能需要计算价格）
        $monthSalesAmount = 189650;
        
        return json([
            'success' => true,
            'data' => [
                'todaySalesCount' => $todaySalesCount,
                'storeTotalCount' => $storeTotalCount,
                'salespersonTotalCount' => $salespersonTotalCount,
                'monthSalesAmount' => $monthSalesAmount
            ]
        ]);
    }
    
    /**
     * 获取销售趋势数据
     *
     * @param Request $request
     * @return Response
     */
    public function salesTrend(Request $request): Response
    {
        // 获取时间范围类型
        $type = $request->param('type', 'week');
        
        // 根据不同类型获取不同时间范围的数据
        $data = [];
        $labels = [];
        
        switch ($type) {
            case 'week':
                // 获取过去7天的数据
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-{$i} days"));
                    $count = Sales::whereDay('create_time', $date)->count();
                    $labels[] = date('m-d', strtotime($date));
                    $data[] = $count;
                }
                break;
            case 'month':
                // 获取本月每天的数据
                $daysInMonth = date('t');
                $currentMonth = date('Y-m');
                
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = $currentMonth . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $count = Sales::whereDay('create_time', $date)->count();
                    $labels[] = $i . '日';
                    $data[] = $count;
                }
                break;
            case 'year':
                // 获取今年每月的数据
                $currentYear = date('Y');
                
                for ($i = 1; $i <= 12; $i++) {
                    $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $count = Sales::whereMonth('create_time', "{$currentYear}-{$month}")->count();
                    $labels[] = $i . '月';
                    $data[] = $count;
                }
                break;
        }
        
        return json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }
    
    /**
     * 获取品牌销量统计
     *
     * @return Response
     */
    public function brandStatistics(): Response
    {
        // 获取所有品牌
        $brands = PhoneBrand::where('status', 1)->select();
        
        $data = [];
        foreach ($brands as $brand) {
            $count = Sales::where('phone_brand_id', $brand->id)->count();
            $data[] = [
                'name' => $brand->name,
                'value' => $count
            ];
        }
        
        return json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * 获取门店销量统计
     *
     * @return Response
     */
    public function storeStatistics(): Response
    {
        // 获取所有门店
        $stores = Store::where('status', 1)->select();
        
        $data = [];
        $labels = [];
        foreach ($stores as $store) {
            $count = Sales::where('store_id', $store->id)->count();
            $labels[] = $store->name;
            $data[] = $count;
        }
        
        return json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }
    
    /**
     * 获取最新销售记录
     *
     * @param Request $request
     * @return Response
     */
    public function latestSalesRecords(Request $request): Response
    {
        // 获取条数限制
        $limit = $request->param('limit', 5);
        
        // 获取最新销售记录
        $records = Sales::with(['store', 'salesperson', 'phoneModel'])
            ->order('create_time', 'desc')
            ->limit($limit)
            ->select();
            
        // 格式化数据
        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'id' => $record->id,
                'storeName' => $record->store->name,
                'salesperson' => $record->salesperson->name,
                'phoneModel' => $record->phoneModel->name,
                'imei' => $record->imei,
                'customerName' => $record->customer_name,
                'createTime' => $record->create_time
            ];
        }
        
        return json([
            'success' => true,
            'data' => $data
        ]);
    }
} 