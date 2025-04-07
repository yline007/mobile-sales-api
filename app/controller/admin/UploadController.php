<?php
declare (strict_types = 1);

namespace app\controller\admin;

use think\facade\Filesystem;
use think\facade\Request;
use think\Response;

/**
 * 上传控制器
 * Class UploadController
 * @package app\controller\admin
 */
class UploadController
{
    /**
     * 上传图片
     * 
     * @return Response
     */
    public function uploadImage(): Response
    {
        // 获取上传的文件
        $file = Request::file('file');
        
        // 验证文件
        try {
            validate(['file' => [
                'fileSize' => 10 * 1024 * 1024, // 限制文件大小10M
                'fileExt' => 'jpg,jpeg,png,gif', // 限制文件类型
                'fileMime' => 'image/jpeg,image/png,image/gif', // 限制文件MIME类型
            ]])->check(['file' => $file]);
        } catch (\think\exception\ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
        
        // 上传到服务器
        try {
            $saveName = Filesystem::disk('public')->putFile('uploads/images', $file);
            $url = '/storage/' . $saveName;
            
            return json([
                'code' => 0,
                'msg' => '上传成功',
                'data' => [
                    'url' => $url,
                    'name' => $file->getOriginalName()
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => '上传失败: ' . $e->getMessage()]);
        }
    }
} 