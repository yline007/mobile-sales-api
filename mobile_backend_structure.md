# 手机销售管理系统后端架构设计

## 后端项目结构规划

```
mobile_backend/
├── app/                           // 应用目录
│   ├── controller/                // 控制器目录
│   │   ├── admin/                 // 管理员相关控制器
│   │   │   ├── AdminController.php     // 管理员管理
│   │   │   ├── DashboardController.php // 仪表盘数据
│   │   │   ├── LoginController.php     // 登录相关
│   │   │   ├── SalesController.php     // 销售记录管理
│   │   │   ├── StoreController.php     // 门店管理
│   │   │   ├── SalespersonController.php // 销售员管理
│   │   │   ├── PhoneController.php     // 手机型号管理
│   │   │   └── SystemController.php    // 系统设置
│   ├── model/                     // 数据模型目录
│   │   ├── Admin.php              // 管理员模型
│   │   ├── Sales.php              // 销售记录模型
│   │   ├── Store.php              // 门店模型
│   │   ├── Salesperson.php        // 销售员模型
│   │   ├── PhoneBrand.php         // 手机品牌模型
│   │   └── PhoneModel.php         // 手机型号模型
│   ├── middleware/                // 中间件目录
│   │   ├── Auth.php               // 身份验证中间件
│   │   └── Log.php                // 日志中间件
│   ├── validate/                  // 验证器目录
│   │   ├── Admin.php              // 管理员验证器
│   │   ├── Sales.php              // 销售记录验证器
│   │   └── ...                    // 其他验证器
├── config/                        // 配置目录
│   ├── app.php                    // 应用配置
│   ├── database.php               // 数据库配置
│   └── jwt.php                    // JWT配置
├── database/                      // 数据库迁移目录
│   ├── migrations/                // 迁移文件
│   └── seeds/                     // 数据填充
├── extend/                        // 扩展目录
│   └── jwt/                       // JWT扩展
├── public/                        // 公共资源目录
│   ├── uploads/                   // 上传文件目录
│   │   └── sales/                 // 销售记录相关上传
│   └── index.php                  // 入口文件
├── route/                         // 路由配置目录
│   └── app.php                    // 应用路由
├── .env                           // 环境变量配置
├── composer.json                  // Composer配置
└── think                          // ThinkPHP命令行工具
```

## 数据库表设计

### 1. 管理员表 (admin)
```
- id             int           主键，自增
- username       varchar(50)   用户名，唯一
- password       varchar(255)  密码（加密存储）
- nickname       varchar(50)   昵称
- avatar         varchar(255)  头像URL
- email          varchar(100)  邮箱
- status         tinyint(1)    状态 1-启用 0-禁用
- role           varchar(20)   角色 super-admin/admin
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

### 2. 门店表 (store)
```
- id             int           主键，自增
- name           varchar(100)  门店名称
- address        varchar(255)  门店地址
- phone          varchar(20)   联系电话
- manager        varchar(50)   店长姓名
- status         tinyint(1)    状态 1-启用 0-禁用
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

### 3. 销售员表 (salesperson)
```
- id             int           主键，自增
- name           varchar(50)   销售员姓名
- phone          varchar(20)   联系电话
- store_id       int           所属门店ID
- employee_id    varchar(50)   工号
- status         tinyint(1)    状态 1-启用 0-禁用
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

### 4. 手机品牌表 (phone_brand)
```
- id             int           主键，自增
- name           varchar(50)   品牌名称
- logo           varchar(255)  品牌LOGO
- status         tinyint(1)    状态 1-启用 0-禁用
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

### 5. 手机型号表 (phone_model)
```
- id             int           主键，自增
- brand_id       int           所属品牌ID
- name           varchar(100)  型号名称
- image          varchar(255)  型号图片
- status         tinyint(1)    状态 1-启用 0-禁用
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

### 6. 销售记录表 (sales)
```
- id             int           主键，自增
- store_id       int           门店ID
- salesperson_id int           销售员ID
- phone_brand_id int           手机品牌ID
- phone_model_id int           手机型号ID
- imei           varchar(50)   手机串码
- customer_name  varchar(50)   客户姓名
- customer_phone varchar(20)   客户电话
- photo_url      varchar(255)  手机照片URL
- remark         text          备注
- create_time    datetime      创建时间（销售时间）
- update_time    datetime      更新时间
```

### 7. 系统设置表 (system_setting)
```
- id             int           主键，自增
- key            varchar(50)   设置键名
- value          text          设置值
- remark         varchar(255)  备注
- create_time    datetime      创建时间
- update_time    datetime      更新时间
```

## API接口设计

### 1. 登录认证接口

- **POST /api/login** - 管理员登录
  - 参数：username, password
  - 返回：token, 用户信息

- **GET /api/admin/info** - 获取当前管理员信息
  - 参数：token（Header中）
  - 返回：用户信息

- **POST /api/admin/password/update** - 修改管理员密码
  - 参数：old_password, new_password
  - 返回：成功/失败消息

### 2. 仪表盘接口

- **GET /api/admin/dashboard/statistics** - 获取销售统计数据
  - 参数：无
  - 返回：销售总数、门店总数、销售员总数、手机型号总数

- **POST /api/admin/dashboard/salesTrend** - 获取销售趋势数据
  - 参数：type (week/month/year)
  - 返回：时间序列销售数据

- **GET /api/admin/dashboard/brandStatistics** - 获取品牌销量统计
  - 参数：无
  - 返回：各品牌销售数量统计

- **GET /api/admin/dashboard/storeStatistics** - 获取门店销量统计
  - 参数：无
  - 返回：各门店销售数量统计


### 3. 管理员管理接口

- **GET /api/admin/user** - 获取管理员列表
  - 参数：page, limit, keyword（可选）
  - 返回：管理员列表和总数

- **POST /api/admin/user** - 创建管理员
  - 参数：username, password, nickname, email, role
  - 返回：成功/失败消息

- **PUT /api/admin/user/{id}** - 更新管理员
  - 参数：nickname, email, role
  - 返回：成功/失败消息

- **DELETE /api/admin/user/{id}** - 删除管理员
  - 参数：无
  - 返回：成功/失败消息

- **POST /api/admin/user/{id}/status** - 更新管理员状态
  - 参数：status
  - 返回：成功/失败消息

### 4. 销售记录管理接口

- **GET /api/admin/sales** - 获取销售记录列表
  - 参数：page, limit, keyword, store_id, start_date, end_date（可选）
  - 返回：销售记录列表和总数

- **POST /api/admin/sales** - 创建销售记录
  - 参数：store_id, salesperson_id, phone_brand_id, phone_model_id, imei, customer_name, customer_phone, photo_url（可选）, remark（可选）
  - 返回：成功/失败消息

- **PUT /api/admin/sales/{id}** - 更新销售记录
  - 参数：store_id, salesperson_id, phone_brand_id, phone_model_id, imei, customer_name, customer_phone, photo_url（可选）, remark（可选）
  - 返回：成功/失败消息

- **DELETE /api/admin/sales/{id}** - 删除销售记录
  - 参数：无
  - 返回：成功/失败消息

- **GET /api/admin/sales/{id}** - 获取销售记录详情
  - 参数：无
  - 返回：销售记录详细信息

### 5. 门店管理接口

- **GET /api/admin/store** - 获取门店列表
  - 参数：page, limit, keyword（可选）
  - 返回：门店列表和总数

- **POST /api/admin/store** - 创建门店
  - 参数：name, address, phone, manager
  - 返回：成功/失败消息

- **PUT /api/admin/store/{id}** - 更新门店
  - 参数：name, address, phone, manager
  - 返回：成功/失败消息

- **DELETE /api/admin/store/{id}** - 删除门店
  - 参数：无
  - 返回：成功/失败消息

- **POST /api/admin/store/{id}/status** - 更新门店状态
  - 参数：status
  - 返回：成功/失败消息

### 6. 销售员管理接口

- **GET /api/admin/salesperson** - 获取销售员列表
  - 参数：page, limit, keyword, store_id（可选）
  - 返回：销售员列表和总数

- **POST /api/admin/salesperson/{id}/status** - 更新销售员状态
  - 参数：status
  - 返回：成功/失败消息

### 7. 手机品牌和型号管理接口

- **GET /api/admin/phone/brand** - 获取手机品牌列表
  - 参数：page, limit, keyword（可选）
  - 返回：品牌列表和总数

- **GET /api/admin/phone/model** - 获取手机型号列表
  - 参数：page, limit, keyword, brand_id（可选）
  - 返回：型号列表和总数

- **POST /api/admin/phone/model** - 创建手机型号
  - 参数：brand_id, name, image（可选）
  - 返回：成功/失败消息

- **PUT /api/admin/phone/model/{id}** - 更新手机型号
  - 参数：brand_id, name, image（可选）
  - 返回：成功/失败消息

- **DELETE /api/admin/phone/model/{id}** - 删除手机型号
  - 参数：无
  - 返回：成功/失败消息

### 8. 系统设置接口

- **GET /api/admin/system/setting** - 获取系统设置
  - 参数：无
  - 返回：系统设置列表

- **POST /api/admin/system/setting** - 更新系统设置
  - 参数：settings（键值对对象）
  - 返回：成功/失败消息

### 9. 文件上传接口

- **POST /api/upload** - 通用文件上传接口
  - 参数：file（文件）, type（可选，文件类型）
  - 返回：文件URL

## 后端实现流程建议

1. 创建ThinkPHP项目基础结构
2. 配置数据库连接
3. 设计和创建数据库表
4. 实现用户身份认证系统（JWT）
5. 实现各个模块的控制器、模型和验证器
6. 编写路由配置
7. 编写中间件（权限验证等）
8. 实现文件上传功能
9. 编写单元测试
10. 配置跨域等相关设置 