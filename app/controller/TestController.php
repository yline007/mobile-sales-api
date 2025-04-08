<?php
declare (strict_types = 1);

namespace app\controller;

use app\service\NotificationService;
use think\facade\Log;

class TestController
{
    public function sendTestNotification()
    {
        try {
            // 获取请求数据
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                throw new \Exception('无效的请求数据');
            }

            Log::info('收到测试通知请求：' . json_encode($data, JSON_UNESCAPED_UNICODE));
            
            // 创建通知服务实例
            $notificationService = new NotificationService();
            
            // 发送通知
            $notificationService->sendSalesNotification($data);
            
            Log::info('测试通知发送成功');
            
            return json([
                'code' => 0,
                'msg' => '测试通知发送成功',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('测试通知发送失败：' . $e->getMessage());
            
            return json([
                'code' => 1,
                'msg' => '测试通知发送失败：' . $e->getMessage()
            ]);
        }
    }
} 