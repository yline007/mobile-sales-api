<?php

return [
    // JWT密钥
    'secret' => 'mobile_sales_management_system_secret_key',
    // 默认时间单位为秒
    'expire_time' => 7200, // 2小时过期
    'refresh_expire' => 604800, // 刷新token7天内有效
    // 算法类型 HS256、HS384、HS512、RS256、RS384、RS512、ES256、ES384、ES512、PS256、PS384、PS512
    'algorithms' => 'HS256',
    // 签发者
    'issuer' => 'mobile_sales',
    // 接收者
    'audience' => 'mobile_admin',
]; 