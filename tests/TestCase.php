<?php

namespace tests;

use \Mockery;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit\Framework\TestCase as BaseTestCase;
use think\facade\Config;

/**
 * 测试基类
 * 
 * 提供通用测试方法和辅助函数
 */
abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;
    
    /**
     * 清除测试过程中的Mockery实例
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * 模拟静态方法
     *
     * @param string $class  类名
     * @param string $method 方法名
     * @param mixed  $return 返回值
     * @return Mockery\MockInterface
     */
    protected function mockStaticMethod(string $class, string $method, $return)
    {
        $mock = Mockery::mock("overload:$class");
        $mock->shouldReceive($method)->andReturn($return);
        return $mock;
    }
    
    /**
     * 设置配置项
     *
     * @param string $key   配置键
     * @param mixed  $value 配置值
     * @return void
     */
    protected function setConfig(string $key, $value): void
    {
        Config::set([$key => $value]);
    }
    
    /**
     * 设置多个配置项
     *
     * @param array  $configs 配置项数组
     * @param string $name    配置分组名
     * @return void
     */
    protected function setConfigs(array $configs, string $name = null): void
    {
        Config::set($configs, $name);
    }
} 