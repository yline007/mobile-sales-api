<?php

// 载入Composer自动加载文件
require __DIR__ . '/../vendor/autoload.php';

// 初始化ThinkPHP应用
$app = new \think\App();
$app->initialize();

// 手动加载必要的服务
$app->bind('config', \think\Config::class);

// 设置测试环境
$app->config->set([
    'app' => [
        'debug' => true,
    ],
    'jwt' => [
        'secret' => 'test_secret_key',
        'expire_time' => 7200,
        'refresh_expire' => 604800,
        'algorithms' => 'HS256',
        'issuer' => 'test_issuer',
        'audience' => 'test_audience',
    ]
]);

// 返回应用实例
return $app; 