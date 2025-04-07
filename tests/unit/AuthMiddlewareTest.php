<?php

namespace tests\unit;

use app\middleware\Auth;
use app\model\Admin;
use jwt\JWT;
use Mockery;
use tests\TestCase;
use think\Request;
use think\Response;
use think\response\Json;

class AuthMiddlewareTest extends TestCase
{
    protected $authMiddleware;
    protected $mockRequest;
    protected $mockNext;
    protected $mockResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authMiddleware = new Auth();
        $this->mockRequest = Mockery::mock(Request::class);
        $this->mockNext = function (Request $request) {
            return new Json(['success' => true]);
        };
        $this->mockResponse = new Json(['success' => true]);
    }

    public function testHandleWithoutToken()
    {
        // 模拟请求没有Authorization头
        $this->mockRequest->shouldReceive('header')
            ->with('Authorization')
            ->andReturn('');

        $response = $this->authMiddleware->handle($this->mockRequest, $this->mockNext);
        
        $this->assertSame(401, $response->getCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('未登录或登录已过期', $responseData['msg']);
    }

    public function testHandleWithInvalidToken()
    {
        // 模拟请求有无效的Token
        $this->mockRequest->shouldReceive('header')
            ->with('Authorization')
            ->andReturn('Bearer invalid_token');

        $response = $this->authMiddleware->handle($this->mockRequest, $this->mockNext);
        
        $this->assertSame(401, $response->getCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('Token验证失败，请重新登录', $responseData['msg']);
    }

    public function testHandleWithValidToken()
    {
        // 创建有效的token
        $payload = [
            'admin_id' => 1,
            'username' => 'admin',
            'role' => 'admin'
        ];
        $token = JWT::generateToken($payload);
        
        // 模拟Admin模型
        $mockAdmin = Mockery::mock(Admin::class);
        $mockAdmin->status = 1;
        
        // 模拟静态方法调用
        $this->mockStaticMethod('app\model\Admin', 'find', $mockAdmin);
        
        // 模拟请求有有效的Token
        $this->mockRequest->shouldReceive('header')
            ->with('Authorization')
            ->andReturn('Bearer ' . $token);
        
        $response = $this->authMiddleware->handle($this->mockRequest, $this->mockNext);
        
        $this->assertSame(200, $response->getCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
    }

    public function testHandleWithDisabledAccount()
    {
        // 创建有效的token
        $payload = [
            'admin_id' => 1,
            'username' => 'admin',
            'role' => 'admin'
        ];
        $token = JWT::generateToken($payload);
        
        // 模拟Admin模型-已禁用状态
        $mockAdmin = Mockery::mock(Admin::class);
        $mockAdmin->status = 0;
        
        // 模拟静态方法调用
        $this->mockStaticMethod('app\model\Admin', 'find', $mockAdmin);
        
        // 模拟请求有有效的Token
        $this->mockRequest->shouldReceive('header')
            ->with('Authorization')
            ->andReturn('Bearer ' . $token);
        
        $response = $this->authMiddleware->handle($this->mockRequest, $this->mockNext);
        
        $this->assertSame(403, $response->getCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('账号已被禁用', $responseData['msg']);
    }

    /**
     * 模拟静态方法
     *
     * @param string $class 类名
     * @param string $method 方法名
     * @param mixed $return 返回值
     * @return void
     */
    protected function mockStaticMethod(string $class, string $method, $return): void
    {
        // 此方法实现依赖于具体的测试框架和mock库
        // 实际使用时需要根据您使用的测试框架调整实现
    }
} 