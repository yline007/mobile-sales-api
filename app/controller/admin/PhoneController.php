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
        $page = (int)Request::param('page', 1);
        $limit = (int)Request::param('limit', 10);
        $keyword = Request::param('keyword', '');

        $query = PhoneBrand::where('1=1');
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $total = $query->count();
        $list = $query->page($page, $limit)->select();

        // 将name等于'其它'的项排在最后
        $list = $list->toArray();
        usort($list, function($a, $b) {
            return $a['name'] === '其它' ? 1 : ($b['name'] === '其它' ? -1 : 0);
        });

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
     * 获取手机型号列表
     * @return Response
     */
    public function modelList(): Response
    {
        $page = (int)Request::param('page', 1);
        $limit = (int)Request::param('limit', 10);
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

        foreach ($list as $item) {
            $item->brand_name = $item->brand ? $item->brand->name : '';
        }
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
        $data = Request::only(['brand_id', 'name', 'image', 'price']);
        
        // 验证价格字段
        if (isset($data['price']) && !is_numeric($data['price'])) {
            return json(['code' => 1, 'msg' => '价格必须是数字']);
        }
        if (isset($data['price']) && $data['price'] < 0) {
            return json(['code' => 1, 'msg' => '价格不能为负数']);
        }
        
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
        $data = Request::only(['brand_id', 'name', 'image', 'price']);
        
        // 验证价格字段
        if (isset($data['price']) && !is_numeric($data['price'])) {
            return json(['code' => 1, 'msg' => '价格必须是数字']);
        }
        if (isset($data['price']) && $data['price'] < 0) {
            return json(['code' => 1, 'msg' => '价格不能为负数']);
        }
        
        $model = PhoneModel::find($id);
        if (!$model) {
            return json(['code' => 1, 'msg' => '型号不存在']);
        }

        // 只有在修改了型号名称或品牌时，才需要验证是否存在重复
        if ((isset($data['name']) && $data['name'] !== $model->name) || 
            (isset($data['brand_id']) && $data['brand_id'] !== $model->brand_id)) {
            if (PhoneModel::where('name', $data['name'] ?? $model->name)
                ->where('brand_id', $data['brand_id'] ?? $model->brand_id)
                ->where('id', '<>', $id)
                ->find()) {
                return json(['code' => 1, 'msg' => '该品牌下已存在相同型号名称']);
            }
        }

        if ($model->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 更新手机型号状态
     * @param int $id
     * @return Response
     */
    public function updateStatus(int $id): Response
    {
        $status = (int)Request::param('status');
        
        // 验证状态值
        if (!in_array($status, [0, 1])) {
            return json(['code' => 1, 'msg' => '状态值无效']);
        }
        
        $model = PhoneModel::find($id);
        if (!$model) {
            return json(['code' => 1, 'msg' => '型号不存在']);
        }

        // 如果当前已经是目标状态，直接返回成功
        if ($model->status === $status) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }

        // 如果要禁用，检查是否有进行中的销售
        if ($status === 0 && $model->sales()->count() > 0) {
            return json(['code' => 1, 'msg' => '该型号存在进行中的销售记录，无法禁用']);
        }

        $model->status = $status;
        if ($model->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
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