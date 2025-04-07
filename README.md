![](https://www.thinkphp.cn/uploads/images/20230630/300c856765af4d8ae758c503185f8739.png)

ThinkPHP 8
===============

## 特性

* 基于PHP`8.0+`重构
* 升级`PSR`依赖
* 依赖`think-orm`3.0+版本
* 全新的`think-dumper`服务，支持远程调试
* 支持`6.0`/`6.1`无缝升级

> ThinkPHP8的运行环境要求PHP8.0+

现在开始，你可以使用官方提供的[ThinkChat](https://chat.topthink.com/)，让你在学习ThinkPHP的旅途中享受私人AI助理服务！

![](https://www.topthink.com/uploads/assistant/20230630/4d1a3f0ad2958b49bb8189b7ef824cb0.png)

ThinkPHP生态服务由[顶想云](https://www.topthink.com)（TOPThink Cloud）提供，为生态提供专业的开发者服务和价值之选。

## 文档

[完全开发手册](https://doc.thinkphp.cn)


## 赞助

全新的[赞助计划](https://www.thinkphp.cn/sponsor)可以让你通过我们的网站、手册、欢迎页及GIT仓库获得巨大曝光，同时提升企业的品牌声誉，也更好保障ThinkPHP的可持续发展。

[![](https://www.thinkphp.cn/sponsor/special.svg)](https://www.thinkphp.cn/sponsor/special)

[![](https://www.thinkphp.cn/sponsor.svg)](https://www.thinkphp.cn/sponsor)

## 安装

~~~
composer create-project topthink/think tp
~~~

启动服务

~~~
cd tp
php think run
~~~

然后就可以在浏览器中访问

~~~
http://localhost:8000
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~

## 命名规范

`ThinkPHP`遵循PSR-2命名规范和PSR-4自动加载规范。

## 参与开发

直接提交PR或者Issue即可

## 版权信息

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2024 by ThinkPHP (http://thinkphp.cn) All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)

# 手机销售管理系统后端

## 项目概述

本项目是一个基于ThinkPHP 8.0的手机销售管理系统后端API，提供手机销售业务的完整管理功能。

## 功能特性

- 管理员账户管理和权限控制
- JWT身份认证机制
- 销售记录管理
- 门店管理
- 销售员管理
- 手机品牌和型号管理
- 系统设置
- 文件上传

## 技术栈

- PHP 8.0+
- ThinkPHP 8.0
- MySQL 数据库
- JWT认证
- RESTful API

## 安装与配置

1. 克隆项目

```bash
git clone https://github.com/yourusername/mobile_project.git
cd mobile_project/mobile_backend
```

2. 安装依赖

```bash
composer install
```

3. 配置环境变量

复制`.env.example`文件为`.env`，并根据实际情况修改配置：

```bash
cp .env.example .env
```

4. 数据库迁移

```bash
php think migrate:run
```

5. 启动服务

```bash
php think run
```

## 目录结构

```
mobile_backend/
├── app/                           // 应用目录
│   ├── controller/                // 控制器目录
│   │   ├── admin/                 // 管理员相关控制器
│   ├── model/                     // 数据模型目录
│   ├── middleware/                // 中间件目录
│   ├── validate/                  // 验证器目录
├── config/                        // 配置目录
├── extend/                        // 扩展目录
│   └── jwt/                       // JWT扩展
├── public/                        // 公共资源目录
├── route/                         // 路由配置目录
├── tests/                         // 测试目录
├── .env                           // 环境变量配置
├── composer.json                  // Composer配置
└── think                          // ThinkPHP命令行工具
```

## API文档

API文档请参考 `/docs/api.md`

## 单元测试

项目使用PHPUnit进行单元测试，测试用例位于`tests`目录下。

### 运行测试

```bash
composer test
```

### 编写测试

测试用例需要继承`tests\TestCase`基类，可以使用Mockery进行模拟。

示例：

```php
<?php

namespace tests\unit;

use app\model\Admin;
use tests\TestCase;

class AdminTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
```

## 线上部署

1. 配置Web服务器（Nginx/Apache）指向`public`目录
2. 确保`runtime`目录可写
3. 优化自动加载：`composer dump-autoload -o`
4. 生产环境关闭调试模式：修改`.env`中的`APP_DEBUG=false`

## 许可证

MIT
