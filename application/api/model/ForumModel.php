<?php

namespace app\api\model;
use think\Model;
use app\api\validate\ForumValidate;

/**
 * 发帖模型
 */
class ForumModel extends Model{

    protected $pk = 'fid';
    protected $insert  = array('create_time');

    protected function setCreateTimeAttr(){
        return time();
    }

    //关联user表
    public function user(){
        return $this->belongsTo('UserModel');
    }

    public function getList(){
        return $this->with('user')->order('create_time desc')->paginate(4);
    }

    public function publish($uid,$data){
        $data['uid'] = $uid;
        $validate = new ForumValidate;
        if(!$validate->check($data)) {
            return array('code'=>0,'msg'=>$validate->getError());
        }
        $fid = $this->allowField(true)->save($data);
        if($fid){
            return array('code'=>1,'msg'=>'发布成功');
        }else{
            return array('code'=>0,'msg'=>'发布失败');
        }

    }
}