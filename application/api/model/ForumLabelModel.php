<?php
namespace app\api\model;
use think\Model;
use app\api\model\ForumModel;
use app\api\model\ForumToLabelModel;

class ForumLabelModel extends Model{

    protected $pk = 'label_id';

    //关联forum表
    public function forum(){
        return $this->belongsToMany('ForumModel','ForumToLabel','fid','lid');
    }
}