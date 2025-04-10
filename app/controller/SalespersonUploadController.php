<?php

declare(strict_types=1);

namespace app\controller;

use think\facade\Filesystem;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;

class SalespersonUploadController {
    /**
     * 上传图片
     * @return Response
     */
    public function uploadImages(): Response {
        try {
            // 获取上传的文件
            $files = Request::file('images');
            
            if (empty($files)) {
                return json(['code' => 1, 'msg' => '请选择要上传的图片']);
            }
            
            // 如果是单个文件，转换为数组
            if (!is_array($files)) {
                $files = [$files];
            }

            // 限制最多上传9张图片
            if (count($files) > 9) {
                return json(['code' => 1, 'msg' => '最多只能上传9张图片']);
            }

            $uploadPath = 'uploads/sales/' . date('Ymd');
            $urls = [];
            $errors = [];

            foreach ($files as $file) {
                try {
                    // 验证文件大小和扩展名
                    validate(['image' => 'fileSize:10485760|fileExt:jpg,jpeg,png,gif'])
                        ->check(['image' => $file]);

                    // 上传到本地服务器
                    $savename = Filesystem::disk('public')->putFile($uploadPath, $file);
                    
                    if ($savename) {
                        // 生成可访问的URL
                        $urls[] = '/storage/' . $savename;
                    } else {
                        $errors[] = $file->getOriginalName() . ' 上传失败';
                    }
                } catch (ValidateException $e) {
                    $errors[] = $file->getOriginalName() . ' ' . $e->getMessage();
                } catch (\Exception $e) {
                    $errors[] = $file->getOriginalName() . ' 上传失败：' . $e->getMessage();
                }
            }

            if (empty($urls)) {
                return json([
                    'code' => 1, 
                    'msg' => '图片上传失败', 
                    'errors' => $errors
                ]);
            }

            // 将多个URL用逗号连接
            $urlString = implode(',', $urls);

            return json([
                'code' => 0,
                'msg' => !empty($errors) ? '部分图片上传成功' : '上传成功',
                'data' => [
                    'urls' => $urls,           // 数组格式，方便前端展示
                    'url_string' => $urlString, // 字符串格式，用于保存到数据库
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'msg' => '上传失败：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 删除图片
     * @return Response
     */
    public function deleteImage(): Response {
        try {
            $url = Request::param('url');
            $currentUrls = Request::param('current_urls', ''); // 当前所有图片URL，逗号分隔

            if (empty($url)) {
                return json(['code' => 1, 'msg' => '请指定要删除的图片']);
            }

            // 处理完整URL，提取相对路径
            $path = parse_url($url, PHP_URL_PATH); // 获取URL的路径部分
            $path = str_replace(config('filesystem.disks.public.url') . '/', '', $path); // 使用配置文件中的url参数
            
            if (empty($path)) {
                return json(['code' => 1, 'msg' => '图片路径无效']);
            }

            // 获取完整的文件路径
            $fullPath = config('filesystem.disks.public.root') . '/' . $path;
            
            // 检查文件是否存在
            if (!file_exists($fullPath)) {
                // 尝试使用Filesystem检查文件
                if (!Filesystem::disk('public')->has($path)) {
                    // 记录详细的错误信息
                    error_log("File not found. Path: " . $fullPath);
                    error_log("URL: " . $url);
                    error_log("Relative path: " . $path);
                    return json(['code' => 1, 'msg' => '图片文件不存在，请检查文件路径是否正确']);
                }
            }

            // 尝试删除文件
            if (Filesystem::disk('public')->delete($path)) {
                // 如果提供了当前图片列表，则返回更新后的URL字符串
                if (!empty($currentUrls)) {
                    $urlArray = explode(',', $currentUrls);
                    $urlArray = array_filter($urlArray, function ($item) use ($url) {
                        return $item !== $url;
                    });
                    $newUrlString = implode(',', $urlArray);

                    return json([
                        'code' => 0,
                        'msg' => '删除成功',
                        'data' => [
                            'urls' => $urlArray,           // 数组格式，方便前端展示
                            'url_string' => $newUrlString  // 字符串格式，用于保存到数据库
                        ]
                    ]);
                }

                return json(['code' => 0, 'msg' => '删除成功']);
            }

            return json(['code' => 1, 'msg' => '删除失败，请检查文件权限']);
        } catch (\Exception $e) {
            // 记录详细的错误信息
            error_log("Delete image error: " . $e->getMessage());
            error_log("URL: " . $url);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            return json(['code' => 1, 'msg' => '删除失败：' . $e->getMessage()]);
        }
    }
}
