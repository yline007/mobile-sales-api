<?php
namespace jwt;

class JWT
{
    /**
     * 生成Token
     *
     * @param array $payload 数据信息
     * @param int $expire 过期时间(秒)
     * @return string
     */
    public static function generateToken(array $payload, int $expire = 7200): string
    {
        $base64header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        
        // 设置过期时间
        $payload['exp'] = time() + $expire;
        
        // 添加签发者和接收者
        $payload['iss'] = config('jwt.issuer') ?: 'mobile_backend';
        $payload['aud'] = config('jwt.audience') ?: 'mobile_app';
        
        $base64payload = self::base64UrlEncode(json_encode($payload));
        
        // 获取签名秘钥
        $secret = config('jwt.secret') ?: 'abcdefg';
        
        // 生成签名
        $signature = self::signature($base64header . '.' . $base64payload, $secret);
        $base64signature = self::base64UrlEncode($signature);
        
        // 组合token
        $token = $base64header . '.' . $base64payload . '.' . $base64signature;
        
        return $token;
    }
    
    /**
     * 生成刷新Token
     * 
     * @param int $userId 用户ID
     * @param int $expire 过期时间(秒)
     * @return string
     */
    public static function generateRefreshToken(int $userId, int $expire = 604800): string
    {
        $payload = [
            'user_id' => $userId,
            'is_refresh' => true
        ];
        
        return self::generateToken($payload, $expire);
    }
    
    /**
     * 验证Token
     *
     * @param string $token
     * @return array|bool
     */
    public static function verifyToken(string $token)
    {
        // 获取签名秘钥
        $secret = config('jwt.secret') ?: 'abcdefg';
        
        // 分解token
        $tokens = explode('.', $token);
        if (count($tokens) != 3) {
            return false;
        }
        
        list($base64header, $base64payload, $base64signature) = $tokens;
        
        // 检查签名
        $signature = self::signature($base64header . '.' . $base64payload, $secret);
        $base64sigCheck = self::base64UrlEncode($signature);
        
        if ($base64signature != $base64sigCheck) {
            return false;
        }
        
        // 解码payload
        $payload = json_decode(self::base64UrlDecode($base64payload), true);
        
        // 检查是否过期
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * 从请求头中获取Token
     *
     * @param string $authorization
     * @return string
     */
    public static function getTokenFromHeader(string $authorization = ''): string
    {
        if (empty($authorization)) {
            return '';
        }
        
        // 检查是否是Bearer token
        if (strpos($authorization, 'Bearer ') === 0) {
            return substr($authorization, 7);
        }
        
        return $authorization;
    }
    
    /**
     * Base64 URL安全编码
     *
     * @param string $input
     * @return string
     */
    private static function base64UrlEncode(string $input): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
    }
    
    /**
     * Base64 URL安全解码
     *
     * @param string $input
     * @return string
     */
    private static function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $input));
    }
    
    /**
     * 生成签名
     *
     * @param string $input
     * @param string $key
     * @return string
     */
    private static function signature(string $input, string $key): string
    {
        return hash_hmac('sha256', $input, $key, true);
    }
} 