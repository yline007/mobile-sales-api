# 销售员小程序端API文档

## 销售员认证相关

### 销售员注册

- **接口URL**: `/api/salesperson/register`
- **请求方式**: POST
- **接口描述**: 注册一个新的销售员账号

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 是 | 销售员姓名(2-50字符) |
| phone | string | 是 | 手机号码(唯一) |
| password | string | 是 | 登录密码(6-20位) |

#### 返回参数

```json
{
    "code": 0,
    "msg": "注册成功",
    "data": {
        "id": 1,
        "name": "张三",
        "phone": "13911112222",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

### 销售员登录

- **接口URL**: `/api/salesperson/login`
- **请求方式**: POST
- **接口描述**: 销售员账号登录

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| phone | string | 是 | 手机号码 |
| password | string | 是 | 登录密码 |

#### 返回参数

```json
{
    "code": 0,
    "msg": "登录成功",
    "data": {
        "id": 1,
        "name": "张三",
        "phone": "13911112222",
        "store_id": 1,
        "employee_id": "EMP001",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

### 修改密码

- **接口URL**: `/api/salesperson/password`
- **请求方式**: PUT
- **接口描述**: 修改当前登录销售员的密码
- **请求头**:
  ```
  Authorization: Bearer <token>
  ```

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| old_password | string | 是 | 原密码 |
| new_password | string | 是 | 新密码(6-20位) |

#### 返回参数

```json
{
    "code": 0,
    "msg": "密码修改成功"
}
```

### 重置密码

- **接口URL**: `/api/salesperson/password/reset`
- **请求方式**: POST
- **接口描述**: 通过短信验证码重置密码

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| phone | string | 是 | 手机号码 |
| code | string | 是 | 短信验证码 |
| new_password | string | 是 | 新密码(6-20位) |

#### 返回参数

```json
{
    "code": 0,
    "msg": "密码重置成功"
}
```

## 销售记录管理

### 创建销售记录

- **接口URL**: `/api/salesperson/sales`
- **请求方式**: POST
- **接口描述**: 创建新的销售记录
- **请求头**:
  ```
  Authorization: Bearer <token>
  ```

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| store_id | int | 否 | 门店ID（与store_name二选一） |
| store_name | string | 否 | 门店名称（与store_id二选一） |
| phone_brand_id | int | 否 | 手机品牌ID（与phone_brand_name二选一） |
| phone_brand_name | string | 否 | 手机品牌名称（与phone_brand_id二选一） |
| phone_model_id | int | 否 | 手机型号ID（与phone_model_name二选一） |
| phone_model_name | string | 否 | 手机型号名称（与phone_model_id二选一） |
| imei | string | 是 | 手机串码（15-17位字母数字） |
| customer_name | string | 是 | 客户姓名（2-50字符） |
| customer_phone | string | 是 | 客户电话（手机号格式） |
| photo_url | string | 否 | 手机照片URL |
| remark | string | 否 | 备注（最多500字符） |

#### 返回参数

```json
{
    "code": 0,
    "msg": "创建成功",
    "data": {
        "id": 1,
        "store": "总店",
        "salesperson": "张三",
        "phone_brand": "Apple",
        "phone_model": "iPhone 15",
        "imei": "IMEI123456789",
        "customer_name": "王先生",
        "customer_phone": "13911112222",
        "create_time": "2025-04-07 10:30:45"
    }
}
```

### 获取我的销售记录列表

- **接口URL**: `/api/salesperson/sales`
- **请求方式**: GET
- **接口描述**: 获取当前登录销售员的销售记录列表
- **请求头**:
  ```
  Authorization: Bearer <token>
  ```

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页数量，默认10 |
| keyword | string | 否 | 搜索关键词（客户姓名/电话/IMEI） |
| start_date | string | 否 | 开始日期筛选 |
| end_date | string | 否 | 结束日期筛选 |

#### 返回参数

```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "total": 45,
        "list": [
            {
                "id": 1,
                "store": "总店",
                "salesperson": "张三",
                "phone_brand": "Apple",
                "phone_model": "iPhone 15",
                "imei": "IMEI123456789",
                "customer_name": "王先生",
                "customer_phone": "13911112222",
                "create_time": "2025-04-07 10:30:45"
            }
        ]
    }
}
```

### 获取销售记录详情

- **接口URL**: `/api/salesperson/sales/:id`
- **请求方式**: GET
- **接口描述**: 获取销售记录详情
- **请求头**:
  ```
  Authorization: Bearer <token>
  ```

#### 返回参数

```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "id": 1,
        "store": "总店",
        "salesperson": "张三",
        "phone_brand": "Apple",
        "phone_model": "iPhone 15",
        "imei": "IMEI123456789",
        "customer_name": "王先生",
        "customer_phone": "13911112222",
        "create_time": "2025-04-07 10:30:45"
    }
}
```

## 基础数据接口

### 获取门店列表

- **接口URL**: `/api/salesperson/stores`
- **请求方式**: GET
- **接口描述**: 获取所有可用的门店列表

#### 返回参数

```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "总店",
            "address": "北京市朝阳区xxx",
            "phone": "010-12345678"
        }
    ]
}
```

### 获取手机品牌列表

- **接口URL**: `/api/salesperson/phone_brands`
- **请求方式**: GET
- **接口描述**: 获取所有可用的手机品牌列表

#### 返回参数

```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "Apple",
            "logo_url": "https://example.com/images/apple.png"
        }
    ]
}
```

### 获取手机型号列表

- **接口URL**: `/api/salesperson/phone_models`
- **请求方式**: GET
- **接口描述**: 获取指定品牌下的手机型号列表

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| brand_id | int | 是 | 手机品牌ID |

#### 返回参数

```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "iPhone 15",
            "brand_id": 1,
            "brand_name": "Apple"
        }
    ]
}
``` 