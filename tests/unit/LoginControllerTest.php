<?php

namespace tests\unit;

use app\controller\admin\LoginController;
use app\model\Admin;
use jwt\JWT;
use Mockery;
use tests\TestCase;
use think\Request;

class LoginControllerTest extends TestCase
{
    protected $loginController;
    protected $mockRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginController = new LoginController();
        $this->mockRequest = Mockery::mock(Request::class);
    }

    public function testLoginWithEmptyCredentials()
    {
        // 模拟空的用户名和密码
        $this->mockRequest->shouldReceive('param')
            ->with('username')
            ->andReturn('');
        $this->mockRequest->shouldReceive('param')
            ->with('password')
            ->andReturn('');
        
        $response = $this->loginController->login($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('用户名和密码不能为空', $responseData['msg']);
    }

    public function testLoginWithNonExistingUser()
    {
        // 模拟用户名和密码
        $this->mockRequest->shouldReceive('param')
            ->with('username')
            ->andReturn('nonexisting');
        $this->mockRequest->shouldReceive('param')
            ->with('password')
            ->andReturn('password');
        
        // 模拟Admin::where()->find()返回null
        $mockWhere = Mockery::mock('alias:app\model\Admin');
        $mockWhere->shouldReceive('where->find')->andReturn(null);
        
        $response = $this->loginController->login($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('用户不存在', $responseData['msg']);
    }

    public function testLoginWithWrongPassword()
    {
        // 模拟用户名和密码
        $this->mockRequest->shouldReceive('param')
            ->with('username')
            ->andReturn('admin');
        $this->mockRequest->shouldReceive('param')
            ->with('password')
            ->andReturn('wrong');
        
        // 创建模拟Admin对象
        $mockAdmin = Mockery::mock();
        $mockAdmin->password = md5('correct');
        
        // 模拟Admin::where()->find()返回模拟对象
        $mockWhere = Mockery::mock('alias:app\model\Admin');
        $mockWhere->shouldReceive('where->find')->andReturn($mockAdmin);
        
        $response = $this->loginController->login($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('密码错误', $responseData['msg']);
    }

    public function testLoginWithDisabledAccount()
    {
        // 模拟用户名和密码
        $this->mockRequest->shouldReceive('param')
            ->with('username')
            ->andReturn('admin');
        $this->mockRequest->shouldReceive('param')
            ->with('password')
            ->andReturn('password');
        
        // 创建模拟Admin对象 - 禁用状态
        $mockAdmin = Mockery::mock();
        $mockAdmin->password = md5('password');
        $mockAdmin->status = 0;
        
        // 模拟Admin::where()->find()返回模拟对象
        $mockWhere = Mockery::mock('alias:app\model\Admin');
        $mockWhere->shouldReceive('where->find')->andReturn($mockAdmin);
        
        $response = $this->loginController->login($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['code']);
        $this->assertEquals('账号已被禁用', $responseData['msg']);
    }

    public function testLoginSuccess()
    {
        // 模拟用户名和密码
        $this->mockRequest->shouldReceive('param')
            ->with('username')
            ->andReturn('admin');
        $this->mockRequest->shouldReceive('param')
            ->with('password')
            ->andReturn('password');
        
        // 创建模拟Admin对象
        $mockAdmin = Mockery::mock();
        $mockAdmin->id = 1;
        $mockAdmin->username = 'admin';
        $mockAdmin->nickname = 'Admin User';
        $mockAdmin->avatar = '/avatar.jpg';
        $mockAdmin->role = 'admin';
        $mockAdmin->password = md5('password');
        $mockAdmin->status = 1;
        
        // 模拟Admin::where()->find()返回模拟对象
        $mockWhere = Mockery::mock('alias:app\model\Admin');
        $mockWhere->shouldReceive('where->find')->andReturn($mockAdmin);
        
        $response = $this->loginController->login($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(0, $responseData['code']);
        $this->assertEquals('登录成功', $responseData['msg']);
        $this->assertArrayHasKey('access_token', $responseData['data']);
        $this->assertArrayHasKey('refresh_token', $responseData['data']);
        $this->assertEquals(1, $responseData['data']['user']['id']);
        $this->assertEquals('admin', $responseData['data']['user']['username']);
    }

    public function testRefreshToken()
    {
        // 创建有效的刷新令牌
        $refreshToken = JWT::generateRefreshToken(1);
        
        // 模拟请求头
        $this->mockRequest->shouldReceive('header')
            ->with('Authorization')
            ->andReturn('Bearer ' . $refreshToken);
        
        // 创建模拟Admin对象
        $mockAdmin = Mockery::mock();
        $mockAdmin->id = 1;
        $mockAdmin->username = 'admin';
        $mockAdmin->role = 'admin';
        
        // 模拟Admin::find()返回模拟对象
        $mockFind = Mockery::mock('alias:app\model\Admin');
        $mockFind->shouldReceive('find')->andReturn($mockAdmin);
        
        $response = $this->loginController->refreshToken($this->mockRequest);
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(0, $responseData['code']);
        $this->assertEquals('刷新令牌成功', $responseData['msg']);
        $this->assertArrayHasKey('access_token', $responseData['data']);
    }
} 