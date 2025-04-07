# 认证相关接口

## 1. 管理员登录

- **接口URL**: `/api/admin/login`
- **请求方式**: POST
- **接口描述**: 管理员登录获取访问令牌

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| username | string | 是 | 用户名 |
| password | string | 是 | 密码 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.access_token | string | 访问令牌 |
| data.refresh_token | string | 刷新令牌 |
| data.user | object | 用户信息 |
| data.user.id | int | 用户ID |
| data.user.username | string | 用户名 |
| data.user.nickname | string | 昵称 |
| data.user.avatar | string | 头像URL |
| data.user.role | string | 角色 |

### 响应示例

```json
{
  "code": 0,
  "msg": "登录成功",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "nickname": "管理员",
      "avatar": "/uploads/avatar/admin.png",
      "role": "admin"
    }
  }
}
```

## 2. 刷新令牌

- **接口URL**: `/api/admin/refresh_token`
- **请求方式**: POST
- **接口描述**: 使用刷新令牌获取新的访问令牌

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 刷新令牌 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.access_token | string | 新的访问令牌 |

### 响应示例

```json
{
  "code": 0,
  "msg": "刷新令牌成功",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

## 3. 获取当前管理员信息

- **接口URL**: `/api/admin/info`
- **请求方式**: GET
- **接口描述**: 获取当前登录的管理员信息

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.id | int | 用户ID |
| data.username | string | 用户名 |
| data.nickname | string | 昵称 |
| data.avatar | string | 头像URL |
| data.email | string | 邮箱 |
| data.role | string | 角色 |
| data.createTime | string | 创建时间 |
| data.updateTime | string | 更新时间 |

### 响应示例

```json
{
  "code": 0,
  "msg": "success",
  "data": {
    "id": 1,
    "username": "admin",
    "nickname": "管理员",
    "avatar": "/uploads/avatar/admin.png",
    "email": "admin@example.com",
    "role": "admin",
    "createTime": "2023-01-01 00:00:00",
    "updateTime": "2023-01-01 00:00:00"
  }
}
```

## 销售员认证

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