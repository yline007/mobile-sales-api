<?php

declare(strict_types=1);

namespace app\controller;

use think\facade\Filesystem;
use think\facade\Request;
use think\Response;

class SalespersonUploadController {
    /**
     * 上传图片
     * @return Response
     */
    public function uploadImages(): Response {
        // 获取上传的文件
        $files = Request::file('images');
        // if (empty($files)) {
        //     return json(['code' => 1, 'msg' => '请选择要上传的图片']);
        // }

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
            // 验证文件
            try {
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
            } catch (\think\exception\ValidateException $e) {
                $errors[] = $file->getOriginalName() . ' ' . $e->getMessage();
            }
        }

        if (empty($urls)) {
            return json(['code' => 1, 'msg' => '图片上传失败', 'errors' => $errors]);
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
    }

    /**
     * 删除图片
     * @return Response
     */
    public function deleteImage(): Response {
        $url = Request::param('url');
        $currentUrls = Request::param('current_urls', ''); // 当前所有图片URL，逗号分隔

        if (empty($url)) {
            return json(['code' => 1, 'msg' => '请指定要删除的图片']);
        }

        // 从URL中获取文件路径
        $path = str_replace('/storage/', '', $url);
        if (empty($path)) {
            return json(['code' => 1, 'msg' => '图片路径无效']);
        }

        try {
            // 删除文件
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
            return json(['code' => 1, 'msg' => '删除失败']);
        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => '删除失败：' . $e->getMessage()]);
        }
    }
}
