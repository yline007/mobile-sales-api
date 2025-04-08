<?php
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;
use Predis\Client as RedisClient;

require_once __DIR__ . '/../vendor/autoload.php';

// 加载环境变量
if (is_file(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env', true);
    foreach ($env as $key => $val) {
        $name = strtoupper($key);
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $item = $name . '_' . strtoupper($k);
                putenv("$item=$v");
            }
        } else {
            putenv("$name=$val");
        }
    }
}

// 获取 WebSocket 配置
$ws_host = getenv('WEBSOCKET_HOST') ?: '0.0.0.0';
$ws_port = getenv('WEBSOCKET_PORT') ?: '8085';

// 创建 websocket 服务
$ws_worker = new Worker("websocket://{$ws_host}:{$ws_port}");

// 设置进程数
$ws_worker->count = 1;

// 设置协议
$ws_worker->protocol = 'Workerman\\Protocols\\Websocket';

// Redis 客户端
$redis = null;

// 当 Worker 进程启动时
$ws_worker->onWorkerStart = function($worker) use ($ws_host, $ws_port) {
    global $redis;
    try {
        // 从环境变量获取 Redis 配置
        $redisConfig = [
            'scheme' => getenv('REDIS_SCHEME') ?: 'tcp',
            'host'   => getenv('REDIS_HOST') ?: '127.0.0.1',
            'port'   => getenv('REDIS_PORT') ?: 6379,
        ];
        
        // 如果设置了密码，添加到配置中
        if ($password = getenv('REDIS_PASSWORD')) {
            $redisConfig['password'] = $password;
        }

        // 创建 Redis 连接
        $redis = new RedisClient($redisConfig);
        
        // 测试连接
        $redis->ping();
        echo "Redis连接成功\n";
        echo "WebSocket服务启动在 websocket://{$ws_host}:{$ws_port}\n";
        
        // 使用定时器定期检查 Redis 列表中的消息
        Timer::add(0.1, function() use ($redis) {
            try {
                // 从 Redis 列表中获取消息
                $message = $redis->lpop('sales_notifications');
                if ($message) {
                    echo "收到消息: $message\n";
                    sendToAdmins(json_decode($message, true));
                }
            } catch (\Exception $e) {
                echo "处理消息错误: " . $e->getMessage() . "\n";
            }
        });

    } catch (\Exception $e) {
        echo "Redis连接错误: " . $e->getMessage() . "\n";
    }
};

// 当有客户端连接时
$ws_worker->onConnect = function(TcpConnection $connection) {
    echo "新的客户端连接，远程地址: " . $connection->getRemoteAddress() . "\n";
    
    // 设置WebSocket握手回调
    $connection->onWebSocketConnect = function($connection) {
        echo "WebSocket握手完成\n";
    };
};

// 当有数据发送时
$ws_worker->onMessage = function(TcpConnection $connection, $data) {
    try {
        echo "收到客户端消息: $data\n";
        $message = json_decode($data, true);
        if (!$message) {
            echo "消息解析失败\n";
            return;
        }

        // 处理绑定操作
        if ($message['type'] === 'bind' && !empty($message['admin_id'])) {
            $connection->admin_id = $message['admin_id'];
            echo "管理员 {$message['admin_id']} 绑定成功\n";
            
            // 发送绑定成功消息
            $response = json_encode([
                'type' => 'bind',
                'status' => 'success',
                'message' => '绑定成功'
            ]);
            echo "发送响应: $response\n";
            $connection->send($response);
        }
    } catch (\Exception $e) {
        echo "错误: " . $e->getMessage() . "\n";
        // 发送错误响应给客户端
        $connection->send(json_encode([
            'type' => 'error',
            'message' => $e->getMessage()
        ]));
    }
};

// 当连接断开时
$ws_worker->onClose = function(TcpConnection $connection) {
    echo "客户端断开连接\n";
};

// 当发生错误时
$ws_worker->onError = function(TcpConnection $connection, $code, $msg) {
    echo "错误: $code $msg\n";
    // 发送错误响应给客户端
    $connection->send(json_encode([
        'type' => 'error',
        'code' => $code,
        'message' => $msg
    ]));
};

// 向所有管理员推送消息
function sendToAdmins($message) {
    global $ws_worker;
    foreach ($ws_worker->connections as $conn) {
        if (isset($conn->admin_id)) {
            try {
                echo "向管理员 {$conn->admin_id} 发送消息\n";
                $conn->send(json_encode($message));
            } catch (\Exception $e) {
                echo "发送消息失败: " . $e->getMessage() . "\n";
            }
        }
    }
}

// 运行worker
Worker::runAll(); 