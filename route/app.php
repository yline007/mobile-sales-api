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

// 登录相关路由
Route::group('api/admin', function () {
    Route::post('login', 'admin.LoginController/login');
    Route::post('refresh_token', 'admin.LoginController/refreshToken');
});

// 需要登录验证的路由
Route::group('api/admin', function () {
    // 获取管理员信息
    Route::get('info', 'admin.LoginController/getInfo');
    
    // 仪表盘数据
    Route::get('statistics', 'admin.DashboardController/statistics');
    Route::get('sales_trend', 'admin.DashboardController/salesTrend');
    Route::get('brand_statistics', 'admin.DashboardController/brandStatistics');
    Route::get('store_statistics', 'admin.DashboardController/storeStatistics');
    Route::get('latest_sales', 'admin.DashboardController/latestSalesRecords');
    
    // 管理员管理
    Route::get('admins', 'admin.AdminController/index');
    Route::post('admin', 'admin.AdminController/create');
    Route::put('admin/:id', 'admin.AdminController/update');
    Route::delete('admin/:id', 'admin.AdminController/delete');
    Route::put('admin/:id/status', 'admin.AdminController/updateStatus');
    
    // 销售记录管理
    Route::get('sales', 'admin.SalesController/index');
    Route::post('sales', 'admin.SalesController/create');
    Route::put('sales/:id', 'admin.SalesController/update');
    Route::delete('sales/:id', 'admin.SalesController/delete');
    Route::get('sales/:id', 'admin.SalesController/detail');
    
    // 门店管理
    Route::get('stores', 'admin.StoreController/index');
    Route::post('store', 'admin.StoreController/create');
    Route::put('store/:id', 'admin.StoreController/update');
    Route::delete('store/:id', 'admin.StoreController/delete');
    Route::put('store/:id/status', 'admin.StoreController/updateStatus');
    
    // 销售员管理
    Route::get('salespersons', 'admin.SalespersonController/index');
    Route::post('salesperson', 'admin.SalespersonController/create');
    Route::put('salesperson/:id', 'admin.SalespersonController/update');
    Route::delete('salesperson/:id', 'admin.SalespersonController/delete');
    Route::put('salesperson/:id/status', 'admin.SalespersonController/updateStatus');
    
    // 手机品牌和型号管理
    Route::get('phone_brands', 'admin.PhoneBrandController/index');
    Route::post('phone_brand', 'admin.PhoneBrandController/create');
    Route::put('phone_brand/:id', 'admin.PhoneBrandController/update');
    Route::delete('phone_brand/:id', 'admin.PhoneBrandController/delete');
    Route::put('phone_brand/:id/status', 'admin.PhoneBrandController/updateStatus');
    
    Route::get('phone_models', 'admin.PhoneModelController/index');
    Route::post('phone_model', 'admin.PhoneModelController/create');
    Route::put('phone_model/:id', 'admin.PhoneModelController/update');
    Route::delete('phone_model/:id', 'admin.PhoneModelController/delete');
    Route::put('phone_model/:id/status', 'admin.PhoneModelController/updateStatus');
    
    // 系统设置
    Route::get('settings', 'admin.SystemSettingController/getSettings');
    Route::post('settings', 'admin.SystemSettingController/updateSettings');
    
    // 文件上传
    Route::post('upload/image', 'admin.UploadController/uploadImage');
})->middleware(\app\middleware\Auth::class);

// API文档路由
Route::get('api/docs', function() {
    return redirect('/docs/index.html');
});
