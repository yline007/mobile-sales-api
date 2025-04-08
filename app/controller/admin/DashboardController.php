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
        $storeTotalCount = '-';
        
        // 获取销售员总数
        $salespersonTotalCount = Salesperson::where('status', 1)->count();
        
        // 获取当月销售额（演示用，实际中可能需要计算价格）
        $monthSalesAmount = '-';
        
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'today_sales_count' => $todaySalesCount,
                'store_total_count' => $storeTotalCount,
                'salesperson_total_count' => $salespersonTotalCount,
                'month_sales_amount' => $monthSalesAmount
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
            'code' => 0,
            'msg' => 'success',
            'data' => $data
        ]);
    }
    
    /**
     * 获取每日销售统计数据
     *
     * @param Request $request
     * @return Response
     */
    public function dailySalesStatistics(Request $request): Response
    {
        // 获取查询天数，默认7天
        $days = $request->param('days', 7);
        
        // 计算开始日期
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        $endDate = date('Y-m-d');
        
        // 查询每日销售数据
        $dailySales = Sales::field([
            'DATE(create_time) as date',
            'COUNT(*) as total_sales'
        ])
        ->whereTime('create_time', 'between', [$startDate, $endDate . ' 23:59:59'])
        ->group('DATE(create_time)')
        ->order('date ASC')
        ->select();
        
        // 构建完整的日期范围数据
        $result = [];
        $currentDate = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        
        while ($currentDate <= $endTimestamp) {
            $dateStr = date('Y-m-d', $currentDate);
            $found = false;
            
            foreach ($dailySales as $sale) {
                if ($sale['date'] == $dateStr) {
                    $result[] = [
                        'date' => $dateStr,
                        'total_sales' => intval($sale['total_sales'])
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $result[] = [
                    'date' => $dateStr,
                    'total_sales' => 0
                ];
            }
            
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'list' => $result,
                'total' => [
                    'sales' => array_sum(array_column($result, 'total_sales'))
                ]
            ]
        ]);
    }
} 