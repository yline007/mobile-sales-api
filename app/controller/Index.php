<?php

namespace app\controller;

use app\BaseController;
use think\Response;

class Index extends BaseController
{
    public function index()
    {
        $data = [
            'code' => 200,
            'msg' => 'API service is running',
            'data' => [
                'name' => 'Mobile Backend API',
                'version' => \think\facade\App::version(),
                'timestamp' => time()
            ]
        ];
        return json($data);
    }
}
