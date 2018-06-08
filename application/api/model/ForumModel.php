<?php

namespace app\api\model;
use think\Model;
use app\api\validate\ForumValidate;
use app\api\model\ForumLabelModel;
use app\api\model\ForumToLabelModel;

/**
 * 发帖模型
 */
class ForumModel extends Model{

    protected $pk = 'fid';
    protected $insert  = array('create_time');

    protected function setCreateTimeAttr(){
        return time();
    }

    //图片地址自动添加http
    public function getPicAttr($value){
        return config('app_host').'/'.$value;
    }

    //关联user表
    public function user(){
        return $this->belongsTo('UserModel','uid','uid')->field('nickname,headpic');
    }

    //关联label表
    public function label(){
        return $this->belongsToMany('ForumLabelModel','ForumToLabel','lid','fid');
    }


    //获取主题列表
    public function getList($where){
        return $this->relation('user')->where($where)->order('create_time desc')->paginate(4);
    }

    //发布主题
    public function publish($uid,$data){
        $data['uid'] = $uid;
        $label_ids = explode(',',$data['label_ids']);
        $validate = new ForumValidate;
        if(!$validate->check($data)) {
            return array('code'=>0,'msg'=>$validate->getError());
        }
        $res = $this->allowField(true)->save($data) && $this->label()->saveAll($label_ids);
        if($res){
            return array('code'=>1,'msg'=>'发布成功');
        }else{
            return array('code'=>0,'msg'=>'发布失败');
        }

    }
}