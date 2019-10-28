<?php
namespace app\common\service;

class Upload
{
    private $user_id;
    protected $fsizeLimit=0;//文件上传大小

    protected $need_login=false;

    private $root_path;
    public function __construct($user_id=0)
    {
        $this->user_id = $user_id;
        $this->root_path = \think\facade\Env::get('root_path');
        $this->fsizeLimit = 1024*1024*10;
    }



    //获取上传凭证
    public function info($type='image')
    {
        $data = [
            'token' => '',
            'preview_domain' => request()->domain(),
            'url' => url('upload/upload',[],false,true).'?type='.$type,
        ];

        return $data;

    }


    //上传
    public function upload($type='image')
    {

//        return $this->_resData(0,json_encode($_FILES));exit;
        $upload_file_key=key($_FILES);
        // 获取表单上传文件 例如上传了001.jpg
        $files = request()->file($upload_file_key);
        empty($files) && abort(0,'请选择上传文件');
        //上传路径
        $upload_data = [];
        if(is_array($files)){
            foreach($files as $file){
                $this->_uploadFile($upload_data,$type,$file);
                return $upload_data;
            }
        }else{
            $this->_uploadFile($upload_data,$type,$files);
            return $upload_data[0];
        }
    }

    private function _uploadFile(array &$upload_data,$type,$file)
    {
        $save_path = '/uploads/'.$type.'/'.date('Ymd').'/';
//        !$open_dir_month && $save_path = $save_path.date('Yhm');
        // 移动到框架应用根目录/uploads/ 目录下
        $user_id = $this->user_id;
        $mine_type = 'file';

        $info = $file
            ->validate(['size'=>$this->fsizeLimit])
            ->rule(function($obj)use(&$mine_type,$user_id){
                $file_info = $obj->getInfo();
                isset($file_info['type']) && $mine_type = $file_info['type'];
                return (empty($user_id)?'':$user_id).'U'.md5(json_encode($file_info));
            })
            ->move( $this->root_path.$save_path);
        $data = [
            'key'=>'',
            'fsize' => 0,
            'ext' => '',
            'mime_type' => $mine_type,
            'preview_domain' => request()->domain(),
        ];
        if($info){
            // 成功上传后 获取上传信息
            $data['key'] =str_replace('\\','/',$save_path.$info->getSaveName());
            $data['fsize'] =$info->getSize();
            $data['ext'] =$info->getExtension();
        }else{
            $data['error_msg']=$info->getError();
        }
        array_push($upload_data,$data);
    }
}