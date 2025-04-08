# 仪表盘相关接口

## 1. 获取销售统计数据

- **接口URL**: `/api/admin/statistics`
- **请求方式**: GET
- **接口描述**: 获取销售统计数据，包括当日销量、门店总数、销售员总数和当月销售额

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| success | boolean | 请求是否成功 |
| data | object | 返回数据 |
| data.todaySalesCount | int | 当日销量 |
| data.storeTotalCount | int | 门店总数 |
| data.salespersonTotalCount | int | 销售员总数 |
| data.monthSalesAmount | int | 当月销售额 |

### 响应示例

```json
{
  "success": true,
  "data": {
    "todaySalesCount": 42,
    "storeTotalCount": 15,
    "salespersonTotalCount": 87,
    "monthSalesAmount": 189650
  }
}
```

## 2. 获取销售趋势数据

- **接口URL**: `/api/admin/sales_trend`
- **请求方式**: GET
- **接口描述**: 获取销售趋势数据，可按周、月、年查询

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| type | string | 否 | 时间范围类型，可选值：week(周)、month(月)、year(年)，默认为week |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| success | boolean | 请求是否成功 |
| data | object | 返回数据 |
| data.labels | array | 时间标签数组 |
| data.data | array | 对应时间的销量数据数组 |

### 响应示例

```json
{
  "success": true,
  "data": {
    "labels": ["04-10", "04-11", "04-12", "04-13", "04-14", "04-15", "04-16"],
    "data": [5, 7, 10, 8, 12, 9, 6]
  }
}
```

## 3. 获取品牌销量统计

- **接口URL**: `/api/admin/brand_statistics`
- **请求方式**: GET
- **接口描述**: 获取各品牌销量统计数据

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| success | boolean | 请求是否成功 |
| data | array | 返回数据，品牌销量数组 |
| data[].name | string | 品牌名称 |
| data[].value | int | 品牌销量 |

### 响应示例

```json
{
  "success": true,
  "data": [
    {
      "name": "Apple",
      "value": 120
    },
    {
      "name": "Samsung",
      "value": 95
    },
    {
      "name": "Xiaomi",
      "value": 78
    },
    {
      "name": "Huawei",
      "value": 105
    }
  ]
}
```

## 4. 获取门店销量统计

- **接口URL**: `/api/admin/store_statistics`
- **请求方式**: GET
- **接口描述**: 获取各门店销量统计数据

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| success | boolean | 请求是否成功 |
| data | object | 返回数据 |
| data.labels | array | 门店名称数组 |
| data.data | array | 对应门店的销量数据数组 |

### 响应示例

```json
{
  "success": true,
  "data": {
    "labels": ["北京旗舰店", "上海中心店", "广州天河店", "深圳南山店"],
    "data": [45, 38, 32, 41]
  }
}
```

## 5. 获取最新销售记录

- **接口URL**: `/api/admin/latest_sales`
- **请求方式**: GET
- **接口描述**: 获取最新销售记录

### 请求头

| 参数名 | 必填 | 描述 |
|--------|-----|------|
| Authorization | 是 | Bearer + 空格 + 访问令牌 |

### 请求参数

| 参数名 | 类型 | 必填 | 描述 |
|--------|------|-----|------|
| limit | int | 否 | 返回记录数量，默认为5 |

### 响应参数

| 参数名 | 类型 | 描述 |
|--------|------|------|
| success | boolean | 请求是否成功 |
| data | array | 返回数据，销售记录数组 |
| data[].id | int | 销售记录ID |
| data[].storeName | string | 门店名称 |
| data[].salesperson | string | 销售员姓名 |
| data[].phoneModel | string | 手机型号 |
| data[].imei | string | 手机IMEI码 |
| data[].customerName | string | 顾客姓名 |
| data[].createTime | string | 创建时间 |

### 响应示例

```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "storeName": "北京旗舰店",
      "salesperson": "张三",
      "phoneModel": "iPhone 13 Pro",
      "imei": "123456789012345",
      "customerName": "李四",
      "createTime": "2023-04-16 15:30:45"
    },
    {
      "id": 122,
      "storeName": "上海中心店",
      "salesperson": "王五",
      "phoneModel": "Galaxy S22",
      "imei": "987654321098765",
      "customerName": "赵六",
      "createTime": "2023-04-16 14:25:10"
    }
  ]
}
```

## 获取每日销售统计数据

### 接口说明

获取指定天数范围内的每日销售统计数据，包括销售订单数和销售金额。

### 请求信息

- 请求路径：`/admin/dashboard/daily-sales-statistics`
- 请求方法：`GET`
- 权限要求：需要管理员登录授权

### 请求参数

#### Query Parameters

| 参数名 | 类型   | 必填 | 默认值 | 说明                 |
|--------|--------|------|--------|---------------------|
| days   | Number | 否   | 7      | 要查询的天数范围     |

### 响应信息

#### 响应参数

| 参数名              | 类型   | 说明                           |
|--------------------|--------|--------------------------------|
| code               | Number | 返回状态码，0表示成功           |
| msg                | String | 返回信息                       |
| data               | Object | 返回数据                       |
| data.list          | Array  | 每日统计数据列表               |
| data.list[].date   | String | 日期（格式：YYYY-MM-DD）       |
| data.list[].total_sales | Number | 当日销售订单总数          |
| data.list[].total_amount | Number | 当日销售总金额          |
| data.total         | Object | 统计期间总计数据               |
| data.total.sales   | Number | 期间总订单数                   |
| data.total.amount  | Number | 期间总销售金额                 |

#### 成功响应示例

```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "list": [
            {
                "date": "2024-03-01",
                "total_sales": 10,
                "total_amount": 5000.00
            },
            {
                "date": "2024-03-02",
                "total_sales": 15,
                "total_amount": 7500.00
            }
        ],
        "total": {
            "sales": 25,
            "amount": 12500.00
        }
    }
}
```

#### 错误响应示例

```json
{
    "code": 401,
    "msg": "未登录或登录已过期",
    "data": null
}
```

### 错误码说明

| 错误码 | 说明                |
|--------|-------------------|
| 401    | 未登录或登录已过期  |
| 500    | 服务器内部错误     |

### 注意事项

1. 返回的数据按日期升序排序
2. 无销售数据的日期会自动补充，销售数据为0
3. 日期范围包含开始日期和结束日期
4. 金额单位为元，保留2位小数 