# 文件上传相关接口

## 1. 上传图片

- **接口URL**: `/api/admin/upload/image`
- **请求方式**: POST
- **接口描述**: 上传图片文件并返回图片URL
- **Content-Type**: multipart/form-data

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| file | file | 是 | 要上传的图片文件 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.url | string | 上传成功后的图片URL |
| data.path | string | 上传成功后的图片存储路径 |

### 响应示例

```json
{
  "code": 0,
  "msg": "上传成功",
  "data": {
    "url": "/uploads/images/20230101/abc123.jpg",
    "path": "uploads/images/20230101/abc123.jpg"
  }
}
```

### 错误码说明

| 错误码 | 描述 |
|--------|------|
| 1 | 未上传文件 |
| 2 | 文件类型不允许 |
| 3 | 文件大小超过限制 |
| 4 | 上传失败 | 