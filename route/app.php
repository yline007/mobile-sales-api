<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('/', 'Index/index');

// 登录相关路由
Route::group('api/admin', function () {
    // 原有的POST登录处理路由
    Route::post('login', 'admin.LoginController/login');
    Route::post('refresh_token', 'admin.LoginController/refreshToken');
    
});

// 需要登录验证的路由
Route::group('api/admin', function () {
    // 获取管理员信息
    Route::get('info', 'admin.LoginController/getInfo');

     // 仪表盘相关路由
     Route::get('dashboard/statistics', 'admin.DashboardController/statistics');
     Route::get('dashboard/brand_statistics', 'admin.DashboardController/brandStatistics');
     Route::get('dashboard/daily_sales', 'admin.DashboardController/dailySalesStatistics');
    
    // 管理员管理
    Route::get('admins', 'admin.AdminController/index');
    Route::post('admin', 'admin.AdminController/create');
    Route::put('admin/:id/status', 'admin.AdminController/updateStatus');
    Route::put('admin/:id', 'admin.AdminController/update');
    Route::delete('admin/:id', 'admin.AdminController/delete');
    Route::post('password/update', 'admin.AdminController/updatePassword');
    
    // 销售记录管理
    Route::get('sales/:id', 'admin.SalesController/detail');
    Route::get('sales', 'admin.SalesController/index');
    Route::delete('sales/:id', 'admin.SalesController/delete');
    
    // 门店管理
    Route::get('stores', 'admin.StoreController/index');
    Route::post('store', 'admin.StoreController/create');
    Route::put('store/:id', 'admin.StoreController/update');
    Route::delete('store/:id', 'admin.StoreController/delete');
    Route::put('store/:id/status', 'admin.StoreController/updateStatus');
    
    // 销售员管理
    Route::get('salespersons', 'admin.SalespersonController/index');
    Route::put('salesperson/:id/status', 'admin.SalespersonController/updateStatus');
    
    // 手机品牌和型号管理
    Route::get('phone_brands', 'admin.PhoneBrandController/index');
    Route::get('phone_models', 'admin.PhoneModelController/index');
    Route::post('phone_model', 'admin.PhoneModelController/create');
    Route::put('phone_model/:id', 'admin.PhoneModelController/update');
    Route::delete('phone_model/:id', 'admin.PhoneModelController/delete');
    Route::put('phone_model/:id/status', 'admin.PhoneModelController/updateStatus');
    
    // 系统设置
    Route::get('settings', 'admin.SystemSettingController/getSettings');
    Route::post('settings', 'admin.SystemSettingController/updateSettings');
    
})->middleware(\app\middleware\Auth::class);

// 销售员认证相关路由
Route::post('api/salesperson/register', 'SalespersonAuthController/register');
Route::post('api/salesperson/login', 'SalespersonAuthController/login');

// 需要认证的销售员接口
Route::group('api/salesperson', function () {
    // 密码相关
    Route::put('password', 'SalespersonAuthController/updatePassword');
    Route::post('password/reset', 'SalespersonAuthController/resetPassword');
    
    // 基础数据接口
    Route::get('stores', 'SalespersonController/stores');
    Route::get('phone_brands', 'SalespersonController/phoneBrands');
    Route::get('phone_models', 'SalespersonController/phoneModels');

    // 图片上传接口
    Route::post('upload_images', 'SalespersonUploadController/uploadImages');
    Route::post('delete_image', 'SalespersonUploadController/deleteImage');
    
    // 销售记录提交接口
    Route::post('sales_submit', 'SalespersonController/salesSubmit');
    // 获取今日销售记录
    Route::get('today_sales', 'SalespersonController/todaySales');

    // 销售信息修改
    Route::put('update_profile', 'SalespersonAuthController/updateProfile');
    
})->middleware(\app\middleware\SalespersonAuth::class);

// API文档路由
Route::get('api/docs', function() {
    return redirect('/docs/index.html');
});

// 测试接口路由
Route::group('api', function () {
    Route::post('test/notification', 'TestController/sendTestNotification');
});
