-- 创建数据库
CREATE DATABASE IF NOT EXISTS `mobile_backend` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 使用创建的数据库
USE `mobile_backend`;

-- 1. 管理员表
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像URL',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-启用 0-禁用',
  `role` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '角色 super-adminadmin',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- 2. 门店表
CREATE TABLE IF NOT EXISTS `store` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '门店ID',
  `name` varchar(100) NOT NULL COMMENT '门店名称',
  `address` varchar(255) DEFAULT NULL COMMENT '门店地址',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `manager` varchar(50) DEFAULT NULL COMMENT '店长姓名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-启用 0-禁用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='门店表';

-- 3. 销售员表
CREATE TABLE `salesperson` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售员ID',
  `name` varchar(50) NOT NULL COMMENT '销售员姓名',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `store_id` int(11) DEFAULT NULL COMMENT '所属门店ID',
  `employee_id` varchar(50) DEFAULT NULL COMMENT '工号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-启用 0-禁用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `password` varchar(64) DEFAULT NULL COMMENT '登录密码(加密后)',
  `salt` varchar(32) DEFAULT NULL COMMENT '密码盐值',
  `openid` varchar(64) DEFAULT NULL COMMENT '微信openid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_openid` (`openid`),
  UNIQUE KEY `uk_phone` (`phone`),
  KEY `idx_store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='销售员表';

-- 4. 手机品牌表
CREATE TABLE IF NOT EXISTS `phone_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '品牌ID',
  `name` varchar(50) NOT NULL COMMENT '品牌名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '品牌LOGO',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-启用 0-禁用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='手机品牌表';

-- 5. 手机型号表
CREATE TABLE IF NOT EXISTS `phone_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '型号ID',
  `brand_id` int(11) NOT NULL COMMENT '所属品牌ID',
  `name` varchar(100) NOT NULL COMMENT '型号名称',
  `image` varchar(255) DEFAULT NULL COMMENT '型号图片',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手机价格',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-启用 0-禁用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_brand_id` (`brand_id`),
  CONSTRAINT `fk_phone_model_brand` FOREIGN KEY (`brand_id`) REFERENCES `phone_brand` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='手机型号表';

-- 6. 销售记录表
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售记录ID',
  `store_id` int(11) DEFAULT NULL COMMENT '门店ID',
  `store_name` varchar(50) DEFAULT NULL COMMENT '门店名称',
  `salesperson_id` int(11) DEFAULT NULL COMMENT '销售员ID',
  `salesperson_name` varchar(50) DEFAULT NULL COMMENT '销售员姓名',
  `phone_brand_id` int(11) DEFAULT NULL COMMENT '手机品牌ID',
  `phone_brand_name` varchar(50) DEFAULT NULL COMMENT '手机品牌名称',
  `phone_model_id` int(11) DEFAULT NULL COMMENT '手机型号ID',
  `phone_model_name` varchar(100) DEFAULT NULL COMMENT '手机型号名称',
  `imei` varchar(50) NOT NULL COMMENT '手机串码',
  `customer_name` varchar(50) NOT NULL COMMENT '客户姓名',
  `customer_phone` varchar(20) DEFAULT NULL COMMENT '客户电话',
  `photo_url` text COMMENT '手机照片URL',
  `remark` text COMMENT '备注',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间（销售时间）',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_imei` (`imei`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_salesperson_id` (`salesperson_id`),
  KEY `idx_phone_model_id` (`phone_model_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COMMENT='销售记录表';

-- 7. 系统设置表
CREATE TABLE IF NOT EXISTS `system_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `key` varchar(50) NOT NULL COMMENT '设置键名',
  `value` text COMMENT '设置值',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置表';

-- 添加超级管理员账号 (使用password_hash加密  初始密码: admin123)
INSERT INTO `admin` (`username`,`password`,`nickname`,`role`) VALUES
('admin','$2y$10$SImzMOYYfb0XYkHTonYw5u4R05R5ytQsAqIPfUt0p6FJXVSWLj1b.','超级管理员','super-admin');

-- 初始化手机品牌数据
INSERT INTO `phone_brand` (`name`) VALUES
('苹果 (Apple)'),
('三星 (Samsung)'),
('华为 (Huawei)'),
('小米 (Xiaomi)'),
('OPPO'),
('VIVO'),
('一加 (OnePlus)'),
('荣耀 (Honor)'),
('魅族 (MEIZU)'),
('联想 (Lenovo)'),
('真我 (realme)'),
('坚果 (Smartisan)'),
('360手机'),
('华硕 (ASUS)'),
('谷歌 (Google) Pixel'),
('索尼 (Sony)'),
('诺基亚 (Nokia)'),
('摩托罗拉 (Motorola)'),
('LG'),
('松下 (Panasonic)'),
('夏普 (Sharp)'),
('飞利浦 (Philips)'),
('努比亚 (nubia)'),
('黑鲨 (Black Shark)'),
('酷派 (Coolpad)'),
('其它');

