# 手机相关接口

## 1. 获取手机品牌列表

- **接口URL**: `/api/admin/phone_brands`
- **请求方式**: GET
- **接口描述**: 获取手机品牌列表

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页数量，默认10 |
| keyword | string | 否 | 搜索关键词 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.total | int | 总记录数 |
| data.list | array | 品牌列表 |
| data.list[].id | int | 品牌ID |
| data.list[].name | string | 品牌名称 |
| data.list[].logo | string | 品牌logo URL |

### 响应示例

```json
{
  "code": 0,
  "msg": "success",
  "data": {
    "total": 100,
    "list": [
      {
        "id": 1,
        "name": "Apple",
        "logo": "/uploads/brand/apple.png"
      },
      {
        "id": 2,
        "name": "Samsung",
        "logo": "/uploads/brand/samsung.png"
      }
    ]
  }
}
```

## 2. 创建手机品牌

- **接口URL**: `/api/admin/phone_brand`
- **请求方式**: POST
- **接口描述**: 创建新的手机品牌

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| name | string | 是 | 品牌名称 |
| logo | string | 否 | 品牌logo URL |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "创建成功"
}
```

## 3. 更新手机品牌

- **接口URL**: `/api/admin/phone_brand/{id}`
- **请求方式**: PUT
- **接口描述**: 更新手机品牌信息

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 路径参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| id | int | 是 | 品牌ID |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| name | string | 是 | 品牌名称 |
| logo | string | 否 | 品牌logo URL |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "更新成功"
}
```

## 4. 删除手机品牌

- **接口URL**: `/api/admin/phone_brand/{id}`
- **请求方式**: DELETE
- **接口描述**: 删除手机品牌

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 路径参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| id | int | 是 | 品牌ID |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "删除成功"
}
```

## 5. 获取手机型号列表

- **接口URL**: `/api/admin/phone_models`
- **请求方式**: GET
- **接口描述**: 获取手机型号列表

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页数量，默认10 |
| keyword | string | 否 | 搜索关键词 |
| brand_id | int | 否 | 品牌ID，筛选特定品牌的型号 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |
| data | object | 返回数据 |
| data.total | int | 总记录数 |
| data.list | array | 型号列表 |
| data.list[].id | int | 型号ID |
| data.list[].name | string | 型号名称 |
| data.list[].image | string | 型号图片URL |
| data.list[].brand_id | int | 所属品牌ID |
| data.list[].brand | object | 品牌信息 |
| data.list[].brand.id | int | 品牌ID |
| data.list[].brand.name | string | 品牌名称 |

### 响应示例

```json
{
  "code": 0,
  "msg": "success",
  "data": {
    "total": 100,
    "list": [
      {
        "id": 1,
        "name": "iPhone 13",
        "image": "/uploads/model/iphone13.png",
        "brand_id": 1,
        "brand": {
          "id": 1,
          "name": "Apple"
        }
      },
      {
        "id": 2,
        "name": "Galaxy S21",
        "image": "/uploads/model/galaxys21.png",
        "brand_id": 2,
        "brand": {
          "id": 2,
          "name": "Samsung"
        }
      }
    ]
  }
}
```

## 6. 创建手机型号

- **接口URL**: `/api/admin/phone_model`
- **请求方式**: POST
- **接口描述**: 创建新的手机型号

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| brand_id | int | 是 | 所属品牌ID |
| name | string | 是 | 型号名称 |
| image | string | 否 | 型号图片URL |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "创建成功"
}
```

## 7. 更新手机型号

- **接口URL**: `/api/admin/phone_model/{id}`
- **请求方式**: PUT
- **接口描述**: 更新手机型号信息

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 路径参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| id | int | 是 | 型号ID |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| brand_id | int | 是 | 所属品牌ID |
| name | string | 是 | 型号名称 |
| image | string | 否 | 型号图片URL |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "更新成功"
}
```

## 8. 删除手机型号

- **接口URL**: `/api/admin/phone_model/{id}`
- **请求方式**: DELETE
- **接口描述**: 删除手机型号

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 路径参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| id | int | 是 | 型号ID |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| code | int | 状态码，0表示成功，非0表示失败 |
| msg | string | 状态信息 |

### 响应示例

```json
{
  "code": 0,
  "msg": "删除成功"
}
``` 