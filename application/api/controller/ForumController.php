<?php
namespace app\api\controller;
use app\api\model\ForumModel;
use app\api\model\ForumLabelModel;
use app\api\model\ForumClassModel;

class ForumController extends BaseController{

    //获取相册数据
    public function getFourmList(){
        $forumModel = new ForumModel();
        $where = array();
        $join1 = array('user.uid=forum.uid');
        $join2 = array('forum.fid=forum_to_label.fid');
        input('class_id') != '' && $where['cid'] = input('class_id');
        input('label_ids') !='' && $join[] = 'forum_to_label.lid in ('.input('label_ids').')';
        $data = $forumModel->field('forum.*,user.nickname,user.headpic')->alias('forum')->where($where)->join('user',implode(' AND ',$join1))->join('forum_to_label',implode(' AND ',$join2))->paginate(4);
        return jsonTo(1,'', $data);
    }

    //获取标签数据
    public function getLableList(){
        $labelModel = new ForumLabelModel();
        $data = $labelModel->order('sort')->select();
        foreach($data as $k=>$v){
            $data[$k]['choose'] = false;
        }
        if($data){
            return jsonTo(1,'',$data);
        }else{
            return jsonTo(0,'获取失败',$data);
        }
    }

    //获取分类数据
    public function getClassList(){
        $classModel = new ForumClassModel();
        $data = $classModel->select();
        if($data){
            return jsonTo(1,'',$data);
        }else{
            return jsonTo(0,'获取失败',$data);
        }
    }

    //发布主题
    public function publish(){
        $forumModel = new ForumModel;
        $res = $forumModel->publish($this->loginuser['uid'],input('post.'));
        return json($res);
    }
}
