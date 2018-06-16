<?php
namespace app\common\model;
use think\Model;
use app\common\model\ForumModel;
use app\common\model\ForumToLabelModel;

class ForumLabelModel extends Model{

    protected $pk = 'label_id';

    //关联forum表
    public function forum(){
        return $this->belongsToMany('ForumModel','ForumToLabel','fid','lid');
    }
}