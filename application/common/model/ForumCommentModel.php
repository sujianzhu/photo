<?php
namespace app\common\model;
use think\Model;

class ForumCommentModel extends Model{

    protected $pk = 'com_id';
    protected $insert  = array('create_time');

    public function getForum(){
        return $this->belongsTo('ForumModel','fid');
    }

    public function getUser(){
        return $this->belongsTo('UserModel','uid');
    }

    public function getReply(){
        return $this->hasOne('ForumCommentModel','rpy_id');
    }

    protected function getCreateTimeAttr($value){
        return date('m月d日 H:i',$value);
    }

    protected function setCreateTimeAttr(){
        return time();
    }

    //获取单条评论
    public function getInfo($id){
        return $this->relation('getForum,getUser')->find($id);
    }

    //提交评论
    public function submit($data){
        return $this->allowField(true)->save($data);
    }

}