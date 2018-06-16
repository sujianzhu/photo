<?php

namespace app\common\model;

use think\Model;
use app\api\validate\ForumValidate;
use app\common\model\ForumToLabelModel;
use app\common\model\UserModel;

/**
 * 主题模型
 */
class ForumModel extends Model{

    protected $pk = 'fid';
    protected $insert  = array('create_time');

    protected function setCreateTimeAttr(){
        return time();
    }

    protected function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    //图片地址自动添加http
    public function getPicAttr($value){
        return config('app_host').'/'.$value;
    }

    //关联user表
    public function user(){
        return $this->belongsTo('UserModel','uid','uid')->field('uid,nickname,headpic');
    }

    //关联label表
    public function label(){
        return $this->belongsToMany('ForumLabelModel','ForumToLabel','lid','fid');
    }

    //关联forum_like表
    public function getLike(){
        return $this->belongsToMany('UserModel','ForumLike','uid','fid')->field('nickname,headpic');
    }


    //获取主题列表
    public function getList($where){
        return $this->relation('user')->where($where)->order('create_time desc')->paginate(4);
    }

    //获取主题详情
    public function getInfo($fid){
        return $this->relation('getLike,user')->find($fid);
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
            //增加用户积分
            $userModel = new UserModel();
            $userModel->addIntegral($uid,10);
            return array('code'=>1,'msg'=>'发布成功');
        }else{
            return array('code'=>0,'msg'=>'发布失败');
        }
    }

    //获取用户发布图片数
    public function getForumCount($uid){
        return $this->where(array('uid'=>$uid))->count();
    }

    //获取主题点赞数量
    public function getLikeCount($fid,$uid=''){
        $where = array('fid'=>$fid);
        $uid!='' && $where['uid'] = $uid;
        return db('forum_like')->where($where)->count();
    }

    //用户获得的总点赞数
    public function getLikeToatlCount($uid){
        return $this->alias('forum')->where(array('forum.uid'=>$uid))->join('forum_like','forum_like.fid=forum.fid')->count();
    }

    //获取主题评论数
    public function getCommentCount($fid,$uid=''){
        $where = array('fid'=>$fid);
        $uid!='' && $where['uid'] = $uid;
        return db('forum_comment')->where(array('uid'=>$uid,'fid'=>$fid))->count();
    }

    //用户获得的评论数
    public function getCommentTotalCount($uid){
        return $this->alias('forum')->where(array('forum.uid'=>$uid))->join('forum_comment','forum.fid=forum_comment.fid')->count();
    }


}