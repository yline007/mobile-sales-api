# 移动端后台API文档

## 简介

本文档是移动端后台API接口说明，所有接口均遵循RESTful规范。

## 基础信息

- 接口基础路径: `/api/admin`
- 请求方式: 主要使用GET、POST、PUT、DELETE等HTTP方法
- 数据格式: 所有请求和响应均使用JSON格式

## 通用响应格式

```json
{
  "code": 0,       // 状态码，0表示成功，非0表示失败
  "msg": "success", // 状态信息
  "data": {}       // 响应数据，具体格式根据接口而定
}
```

## 认证方式

系统使用JWT (JSON Web Token) 进行身份验证。

1. 调用登录接口获取token
2. 在后续请求中，将token放在请求头中：
   - `Authorization: Bearer {token}`

## 目录

1. [认证相关接口](./auth_api.md)
2. [管理员相关接口](./admin_api.md)
3. [销售记录相关接口](./sales_api.md)
4. [门店相关接口](./store_api.md)
5. [销售员相关接口](./salesperson_api.md)
6. [手机相关接口](./phone_api.md)
7. [系统设置相关接口](./system_api.md)
8. [文件上传相关接口](./upload_api.md)
9. [仪表盘相关接口](./dashboard_api.md) 