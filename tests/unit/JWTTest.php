<?php

namespace tests\unit;

use jwt\JWT;
use tests\TestCase;

class JWTTest extends TestCase
{
    public function testGenerateToken()
    {
        $payload = [
            'admin_id' => 1,
            'username' => 'admin',
            'role' => 'admin'
        ];

        $token = JWT::generateToken($payload);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        return $token;
    }

    /**
     * @depends testGenerateToken
     */
    public function testVerifyToken(string $token)
    {
        $payload = JWT::verifyToken($token);
        $this->assertIsArray($payload);
        $this->assertEquals(1, $payload['admin_id']);
        $this->assertEquals('admin', $payload['username']);
        $this->assertEquals('admin', $payload['role']);
        $this->assertEquals('test_issuer', $payload['iss']);
        $this->assertEquals('test_audience', $payload['aud']);
    }

    public function testVerifyInvalidToken()
    {
        $result = JWT::verifyToken('invalid_token');
        $this->assertFalse($result);
    }

    public function testGenerateRefreshToken()
    {
        $userId = 1;
        $token = JWT::generateRefreshToken($userId);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        $payload = JWT::verifyToken($token);
        $this->assertIsArray($payload);
        $this->assertEquals($userId, $payload['user_id']);
        $this->assertTrue($payload['is_refresh']);
    }

    public function testGetTokenFromHeader()
    {
        $token = 'test_token';
        $header = 'Bearer ' . $token;
        $result = JWT::getTokenFromHeader($header);
        $this->assertEquals($token, $result);

        // 测试无效的头
        $this->assertNull(JWT::getTokenFromHeader(''));
        $this->assertNull(JWT::getTokenFromHeader('Invalid test_token'));
    }
} 