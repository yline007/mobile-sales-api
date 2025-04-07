<?php

return [
    // JWT密钥
    'secret' => env('JWT_SECRET', 'uJPNdLqZQNz5r9yHGAZAkTfudg1MGjt9'),
    
    // 令牌过期时间，单位秒，默认2小时
    'expire' => env('JWT_EXPIRE', 7200),
    
    // 刷新令牌过期时间，单位秒，默认7天
    'refresh_expire' => env('JWT_REFRESH_EXPIRE', 604800),
    // 算法类型 HS256、HS384、HS512、RS256、RS384、RS512、ES256、ES384、ES512、PS256、PS384、PS512
    'algorithms' => 'HS256',
    // 签发者
    'issuer' => env('JWT_ISSUER', 'mobile_backend'),
    // 接收者
    'audience' => env('JWT_AUDIENCE', 'mobile_app'),
]; 