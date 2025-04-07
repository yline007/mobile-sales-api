<?php
declare (strict_types = 1);

namespace app\service;

use think\facade\Cache;
use think\facade\Config;

class WxService
{
    /**
     * 获取微信小程序openid
     * @param string $code
     * @return string
     * @throws \Exception
     */
    public function getOpenid(string $code): string
    {
        // 从缓存获取
        $cacheKey = 'wx_openid_' . $code;
        $openid = Cache::get($cacheKey);
        if ($openid) {
            return $openid;
        }

        // 获取小程序配置
        $appid = Config::get('wx.appid');
        $secret = Config::get('wx.secret');
        if (empty($appid) || empty($secret)) {
            throw new \Exception('微信小程序配置错误');
        }

        // 请求微信接口
        $url = sprintf(
            'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
            $appid,
            $secret,
            $code
        );

        $response = file_get_contents($url);
        if ($response === false) {
            throw new \Exception('请求微信接口失败');
        }

        $data = json_decode($response, true);
        if (empty($data['openid'])) {
            throw new \Exception($data['errmsg'] ?? '获取openid失败');
        }

        // 缓存openid（5分钟）
        Cache::set($cacheKey, $data['openid'], 300);

        return $data['openid'];
    }
} 