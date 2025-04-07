<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\model\Store;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;

class StoreController
{
    /**
     * 获取门店列表
     * @return Response
     */
    public function index(): Response
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $keyword = Request::param('keyword', '');

        $query = Store::where('1=1');
        if (!empty($keyword)) {
            $query->where('name|address|phone|manager', 'like', "%{$keyword}%");
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
     * 创建门店
     * @return Response
     */
    public function create(): Response
    {
        $data = Request::only(['name', 'address', 'phone', 'manager']);
        
        try {
            validate(\app\validate\Store::class)
                ->scene('create')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证门店名称是否已存在
        if (Store::where('name', $data['name'])->find()) {
            return json(['code' => 1, 'msg' => '门店名称已存在']);
        }

        $store = new Store($data);
        if ($store->save()) {
            return json(['code' => 0, 'msg' => '创建成功']);
        }
        return json(['code' => 1, 'msg' => '创建失败']);
    }

    /**
     * 更新门店信息
     * @param int $id
     * @return Response
     */
    public function update(int $id): Response
    {
        $data = Request::only(['name', 'address', 'phone', 'manager']);
        
        try {
            validate(\app\validate\Store::class)
                ->scene('update')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $store = Store::find($id);
        if (!$store) {
            return json(['code' => 1, 'msg' => '门店不存在']);
        }

        // 如果修改了门店名称，需要验证新的名称是否已存在
        if ($data['name'] !== $store->name && Store::where('name', $data['name'])->find()) {
            return json(['code' => 1, 'msg' => '门店名称已存在']);
        }

        if ($store->save($data)) {
            return json(['code' => 0, 'msg' => '更新成功']);
        }
        return json(['code' => 1, 'msg' => '更新失败']);
    }

    /**
     * 删除门店
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $store = Store::find($id);
        if (!$store) {
            return json(['code' => 1, 'msg' => '门店不存在']);
        }

        // 检查门店是否有关联的销售记录
        if ($store->sales()->count() > 0) {
            return json(['code' => 1, 'msg' => '该门店存在销售记录，无法删除']);
        }

        // 检查门店是否有关联的销售员
        if ($store->salespersons()->count() > 0) {
            return json(['code' => 1, 'msg' => '该门店存在销售员，无法删除']);
        }

        if ($store->delete()) {
            return json(['code' => 0, 'msg' => '删除成功']);
        }
        return json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 更新门店状态
     * @param int $id
     * @return Response
     */
    public function updateStatus(int $id): Response
    {
        $data = ['status' => Request::param('status')];
        
        try {
            validate(\app\validate\Store::class)
                ->scene('status')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        $store = Store::find($id);
        if (!$store) {
            return json(['code' => 1, 'msg' => '门店不存在']);
        }

        $store->status = $data['status'];
        if ($store->save()) {
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        return json(['code' => 1, 'msg' => '状态更新失败']);
    }
} 