<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\SystemSetting;
use think\facade\Request;
use think\Response;

class SystemController
{
    /**
     * 获取系统设置
     * @return Response
     */
    public function getSettings(): Response
    {
        $settings = SystemSetting::select();
        $data = [];
        foreach ($settings as $setting) {
            $data[$setting->key] = $setting->value;
        }

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $data
        ]);
    }

    /**
     * 更新系统设置
     * @return Response
     */
    public function updateSettings(): Response
    {
        $settings = Request::param('settings');
        if (!is_array($settings)) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }

        try {
            foreach ($settings as $key => $value) {
                SystemSetting::where('key', $key)->update(['value' => $value]);
            }
            return json(['code' => 0, 'msg' => '更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => '更新失败：' . $e->getMessage()]);
        }
    }
} 