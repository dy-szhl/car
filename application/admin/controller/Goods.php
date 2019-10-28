<?php
namespace app\admin\controller;

use \app\common\model\SysManager;
use think\Request;

class Goods extends Common
{

    public function index()
    {
        $keyword = input('keyword','','trim');

        $where=[];
        !empty($keyword) && $where[]=['name','like','%'.$keyword.'%'];
        $list = \app\common\model\Goods::with(['linkCate'])->where($where)->paginate();
        // 获取分页显示
        $page = $list->render();
//        dump($list);exit;
        return view('index',[
            'list' => $list,
            'page'=>$page,
            'keyword'=>$keyword,
        ]);
    }

    public function add()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\Goods();

        //表单提交
        if($this->request->isAjax()){
            $php_input = $this->request->param();
            $spu_name = $this->request->param('spu_name',[],'trim');
            $spu_value = $this->request->param('spu_value',[],'trim');
            //商品属性
            $php_input['spu'] = [];
            foreach ($spu_name as $key=>$item){
                if(!empty($item)){
                    $php_input['spu'][]=[
                        'name' => $item,
                        'value' => $spu_value[$key]
                    ];
                }
            }

            try{
                $validate = new \app\common\validate\Goods();
                $model->actionAdd($php_input,$validate);//调用BaseModel中封装的添加/更新操作
            }catch (\Exception $e){
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
            return $this->_resData(1,'操作成功');
        }
        $model = $model->get($id);

        //分类
        $cate_list=\app\common\model\GoodsCate::with(['linkChild'=>function($query){
            $query->where(['status'=>1]);
        }])->where(['pid'=>0,'status'=>1])->order('sort asc')->select();
        //选择spu
        $goods_spu = ($model['id'] || $model['spu']) ? $model['spu']:\app\common\model\Goods::getPropInfo('fields_default_spu');

        return view('add',[
            'model' => $model,
            'cate_list' => $cate_list,
            'goods_spu' => $goods_spu,
        ]);
    }

    //删除数据
    public function del()
    {
        $id = $this->request->param('id',0,'int');
        $model = new \app\common\model\Goods();
        return $model->actionDel(['id'=>$id]);
    }


    public function cate()
    {
        $keyword = input('keyword','','trim');

        $where[]=['pid','=',0];//一级分类
        !empty($keyword) && $where[]=['name','like','%'.$keyword.'%'];

        $list = \app\common\model\GoodsCate::with(['linkChild'])->where($where)->paginate();
        // 获取分页显示
        $page = $list->render();
        return view('cate',[
            'list' => $list,
            'page'=>$page,
            'keyword'=>$keyword,
        ]);
    }

    public function cateAdd()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\GoodsCate();

        //表单提交
        if($this->request->isAjax()){
            $php_input = $this->request->param();

            $validate = new \app\common\validate\GoodsCate();
            try{
                $model->actionAdd($php_input,$validate);//调用BaseModel中封装的添加/更新操作
            }catch (\Exception $e){
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
            return json(['code'=>1,'msg'=>'操作成功']);
        }
        $model = $model->get($id);
        //分类
        $cate_list=\app\common\model\GoodsCate::where(['pid'=>0,'status'=>1])->order('sort asc')->select();
        return view('cateAdd',[
            'model' => $model,
            'cate_list' => $cate_list,
        ]);
    }

    //删除数据
    public function cateDel()
    {
        $id = $this->request->param('id',0,'int');
        $model = new \app\common\model\GoodsCate();
        return $model->actionDel(['id'=>$id]);
    }


}
