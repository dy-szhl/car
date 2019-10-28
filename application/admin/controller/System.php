<?php
namespace app\admin\controller;

class System extends Common
{
    //各种图
    public function image()
    {
        $type = input('type',0,'intval');
        $where = [];
        $where[]=['type','=',$type];
        $list = \app\common\model\Image::where($where)->order('sort asc')->paginate();
        // 获取分页显示
        $page = $list->render();
        return view('image',[
            'type' => $type,
            'list' => $list,
            'page'=>$page
        ]);
    }

    //
    public function imageAdd()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\Image();

        //表单提交
        if($this->request->isAjax()){
            $php_input = $this->request->param();

            $validate = new \app\common\validate\Image();
            try{
                $model->actionAdd($php_input,$validate);//调用BaseModel中封装的添加/更新操作
            }catch (\Exception $e){
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
            return json(['code'=>1,'msg'=>'操作成功']);
        }

        $model = $model->get($id);

        return view('imageAdd',[
            'model' => $model
        ]);

    }

    //删除数据
    public function imageDel()
    {
        $id = $this->request->param('id',0,'int');
        $model = new \app\common\model\Image();
        return $model->actionDel(['id'=>$id]);
    }

    public function roles()
    {
        $list = \app\common\model\SysRole::paginate();
        // 获取分页显示
        $page = $list->render();
        return view('roles',[
            'list' => $list,'page'=>$page
        ]);
    }

    //
    public function rolesAdd()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\SysRole();

        //表单提交
        if($this->request->isAjax()){
            $php_input = $this->request->param();

            $validate = new \app\common\validate\SysRole();
            try{
                $model->actionAdd($php_input,$validate);//调用BaseModel中封装的添加/更新操作
            }catch (\Exception $e){
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
            return json(['code'=>1,'msg'=>'操作成功']);
        }

        $model = $model->get($id);

        return view('rolesAdd',[
            'model' => $model
        ]);

    }

    //删除数据
    public function rolesDel()
    {
        $id = $this->request->param('id',0,'int');
        $model = new \app\common\model\SysRole();
        return $model->actionDel(['id'=>$id]);
    }

    public function manager()
    {
        $list = \app\common\model\SysManager::with('linkRole')->paginate();
        // 获取分页显示
        $page = $list->render();
        return view('manager',[
            'list' => $list,'page'=>$page
        ]);
    }

    //
    public function managerAdd()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\SysManager();

        //表单提交
        if($this->request->isAjax()){
            $php_input = $this->request->param();
            if(empty($php_input['password']) && isset($php_input['password'])) unset($php_input['password']);

            $validate = new \app\common\validate\SysManager();
            try{

                $model->actionAdd($php_input,$validate);//调用BaseModel中封装的添加/更新操作
            }catch (\Exception $e){
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
            return json(['code'=>1,'msg'=>'操作成功']);
        }
        $model = $model->get($id);
        //查询角色
//        $role_list = \app\common\model\SysRole::where(['status'=>1])->select();
        return view('managerAdd',[
            'model' => $model,
//            'role_list' => $role_list,
        ]);

    }

    //删除数据
    public function managerDel()
    {
        $id = $this->request->param('id',0,'int');
        $model = new \app\common\model\SysManager();
        return $model->actionDel(['id'=>$id]);
    }


    //系统设置
    public function setting()
    {

        return view('setting',[

        ]);
    }

    //系统设置
    public function settingSave()
    {
        $type = $this->request->param('type');
        $content = $this->request->param('content');
        try{
            \app\common\model\SysSetting::setContent($type, $content);
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
        return $this->_resData(1,'操作成功');
    }


    //注册协议
    public function regProtocol()
    {
        return view('regProtocol',[
            'content' =>  $this->_allProtocol('reg_protocol'),
        ]);
    }
    //查询协议
    private function _allProtocol($type)
    {
        return $content = \app\common\model\SysSetting::getContent($type);
    }

    public function feedback()
    {
        $keyword = input('keyword','','trim');
        $where = [];
        !empty($keyword) && $where[] = ['name','like','%'.$keyword.'%'];
        $list = \app\common\model\UserFeedback::where($where)->order('id desc')->paginate();
        // 获取分页显示
        $page = $list->render();
        return view('feedback',[
            'keyword' => $keyword,
            'list' => $list,
            'page'=>$page
        ]);
    }


    //删除数据
    public function notice()
    {
        if($this->request->isAjax()){
            $content = input('content','','trim');
            empty($content) && exception('请输入发送内容');
            \app\common\model\UserNotice::recordLog('平台通知',$content);
            return $this->_resData(1,'发送成功');
        }
        return view('notice');
    }

}
