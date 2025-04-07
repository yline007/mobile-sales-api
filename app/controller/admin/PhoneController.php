<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\PhoneBrand;
use app\model\PhoneModel;
use think\facade\Request;
use think\Response;

class PhoneController
{
    /**
     * 获取手机品牌列表
     * @return Response
     */
    public function brandList(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');

        $query = PhoneBrand::where('1=1');
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total' => $total,
                'list' => $list
            ]
        ]);
    }

    /**
     * 创建手机品牌
     * @return Response
     */
    public function createBrand(): Response
    {
        $data = Request::only(['name', 'logo']);
        
        // 验证品牌名称是否已存在
        if (PhoneBrand::where('name', $data['name'])->find()) {
            return json(['code' => 1, 'msg' => '品牌名称已存在']);
        }

        $brand = new PhoneBrand($data);
        if ($brand->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新手机品牌
     * @param int $id
     * @return Response
     */
    public function updateBrand(int $id): Response
    {
        $data = Request::only(['name', 'logo']);
        
        $brand = PhoneBrand::find($id);
        if (!$brand) {
            return json(['code' => 1, 'msg' => '品牌不存在']);
        }

        // 如果修改了品牌名称，需要验证新的名称是否已存在
        if ($data['name'] !== $brand->name && PhoneBrand::where('name', $data['name'])->find()) {
            return json(['code' => 1, 'msg' => '品牌名称已存在']);
        }

        if ($brand->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除手机品牌
     * @param int $id
     * @return Response
     */
    public function deleteBrand(int $id): Response
    {
        $brand = PhoneBrand::find($id);
        if (!$brand) {
            return json(['code' => 1, 'msg' => '品牌不存在']);
        }

        // 检查品牌是否有关联的型号
        if ($brand->models()->count() > 0) {
            return json(['code' => 1, 'msg' => '该品牌存在手机型号，无法删除']);
        }

        if ($brand->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 获取手机型号列表
     * @return Response
     */
    public function modelList(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');
        $brand_id = Request::param('brand_id', '');

        $query = PhoneModel::with(['brand']);
        
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }
        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->select();

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total' => $total,
                'list' => $list
            ]
        ]);
    }

    /**
     * 创建手机型号
     * @return Response
     */
    public function createModel(): Response
    {
        $data = Request::only(['brand_id', 'name', 'image']);
        
        // 验证型号名称是否已存在
        if (PhoneModel::where('name', $data['name'])->where('brand_id', $data['brand_id'])->find()) {
            return json(['code' => 1, 'msg' => '该品牌下已存在相同型号名称']);
        }

        $model = new PhoneModel($data);
        if ($model->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新手机型号
     * @param int $id
     * @return Response
     */
    public function updateModel(int $id): Response
    {
        $data = Request::only(['brand_id', 'name', 'image']);
        
        $model = PhoneModel::find($id);
        if (!$model) {
            return json(['code' => 1, 'msg' => '型号不存在']);
        }

        // 如果修改了型号名称或品牌，需要验证是否已存在
        if (($data['name'] !== $model->name || $data['brand_id'] !== $model->brand_id) && 
            PhoneModel::where('name', $data['name'])->where('brand_id', $data['brand_id'])->find()) {
            return json(['code' => 1, 'msg' => '该品牌下已存在相同型号名称']);
        }

        if ($model->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除手机型号
     * @param int $id
     * @return Response
     */
    public function deleteModel(int $id): Response
    {
        $model = PhoneModel::find($id);
        if (!$model) {
            return json(['code' => 1, 'msg' => '型号不存在']);
        }

        // 检查型号是否有关联的销售记录
        if ($model->sales()->count() > 0) {
            return json(['code' => 1, 'msg' => '该型号存在销售记录，无法删除']);
        }

        if ($model->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }
} 