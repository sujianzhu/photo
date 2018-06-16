<?php
namespace app\api\controller;
use app\common\model\ForumModel;
use app\common\model\ForumLabelModel;
use app\common\model\ForumClassModel;
use app\common\model\ForumCommentModel;
use app\common\model\UserModel;

class ForumController extends BaseController{

    //获取主题列表
    public function getFourmList(){
        $forumModel = new ForumModel();
        $userModel = new UserModel();
        $where = array();
        $order = $d_order = array('time'=>'create_time desc','rec'=>'rec_deg desc');
        $join1 = array('user.uid=forum.uid');
        $join2 = array('forum.fid=forum_to_label.fid');
        input('class_id') != '' && $where['cid'] = input('class_id');
        input('label_ids') !='' && $join[] = 'forum_to_label.lid in ('.input('label_ids').')';
        if($sort_id = input('sort_id')){
            unset($order[$sort_id]);
            array_unshift($order, $d_order[$sort_id]);
        }
        $select = $forumModel->field('forum.*,user.nickname,user.headpic')->alias('forum')->where($where)->order(implode(' ',$order))->join('user',implode(' AND ',$join1));
        if(input('label_ids')){
            $data =$select->join('forum_to_label',implode(' AND ',$join2))->group('forum.fid')->paginate(4);
        }else{
            $data = $select->paginate(4);
        }
        foreach($data as $k=>$v){
            $data[$k]['is_like'] = $forumModel->getLikeCount($v['fid'],$this->loginuser['uid']) > 0 ? 1:0;
            $data[$k]['is_follow'] = $userModel->getFollowCount($this->loginuser['uid'],$v['uid']) > 0 ? 1:0;
            $data[$k]['is_author'] = intval($this->loginuser['uid']==$v['uid']);
            if($data[$k]['is_author']){
                $data[$k]['like_me_count'] = $forumModel->getLikeCount($v['fid']);
                $data[$k]['follow_me_count'] = $userModel->getFollowCount('',$this->loginuser['uid']);
            }
        }
        return jsonTo(1,'', $data);
    }

    //获取用户主题列表
    public function getUserFourmList(){
        $forumModel = new ForumModel();
        $data = $forumModel->where('uid','=',$this->loginuser['uid'])->order('create_time desc')->paginate(10);
        foreach($data as $k=>$v){
            $data[$k]['like_count'] = $forumModel->getLikeCount($v['fid']);
        }
        if($data){
            return jsonTo(1,'获取成功', $data);
        }else{
            return jsonTo(1,'获取失败');
        }
    }

    //获取主题详情
    public function getForumInfo(){
        $forumModel = new ForumModel();
        $userModel = new UserModel();
        $fid = input('fid',0);
        if($fid){
            $data = $forumModel->getInfo($fid);
            $data['follow_count'] = $userModel->getFollowCount($this->loginuser['uid'],$data['uid']);
            $data['like_me_count'] = $forumModel->getLikeCount($data['fid']);
            $data['is_follow'] = $data['follow_count']>0 ? 1:0;
            $data['is_like'] = $forumModel->getLikeCount($data['fid'],$this->loginuser['uid']) > 0 ? 1:0;
            $data['is_comment'] = $forumModel->getCommentCount($data['fid'],$this->loginuser['uid']) > 0 ? 1:0;
            $data['is_mutual_follow'] = in_array($data['uid'],$userModel->getMuTualFollow($this->loginuser['uid'],$data['uid'])) ? 1 : 0;
            $data['is_author'] = intval($this->loginuser['uid'] == $data['uid']);
            if($data){
                return jsonTo(1,'获取成功',$data);
            }else{
                return jsonTo(0,'获取失败');
            }
        }else{
            return jsonTo(0,'获取失败');
        }
    }

    //获取标签数据
    public function getLableList(){
        $labelModel = new ForumLabelModel();
        $data = $labelModel->order('sort desc')->select();
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
        $data = $classModel->order('sort desc')->select();
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

    //点赞主题
    public function doLike(){
        if($fid = input('fid')){
            $forumModel = new ForumModel();
            $forum = $forumModel->find($fid);
            if($forum['uid']==$this->loginuser['uid']){
                return jsonTo(0,'不能给自己点赞！');
            }else{
                foreach($forum->getLike as $v){
                    if($v['uid']==$this->loginuser['uid']){
                        return jsonTo(0,'不能重复点赞！');
                    }
                }
                $r = $forum->getLike()->save($this->loginuser['uid']);
                if($r){
                    //增加用户积分
                    $userModel = new UserModel();
                    $userModel->addIntegral($forum->uid,1);
                    return jsonTo(1,'已点赞！');
                }else{
                    return jsonTo(0,'操作失败！');
                }
            }
        }else{
            return jsonTo(0,'缺少参数！');
        }
    }

    //获取用户收到的点赞
    public function getUserLikeList(){
        $uid = $this->loginuser['uid'];
        $forumModel = new ForumModel();
        $data = $forumModel->alias('forum')->where('forum.uid','=',$uid)->field('user.headpic,user.nickname,forum.pic,forum.fid')->join('forum_like','forum_like.fid=forum.fid')->join('user','user.uid=forum_like.uid')->paginate(10);
        if($data){
            return jsonTo(1,'获取成功',$data);
        }else{
            return jsonTo(0,'获取失败');
        }
    }

    //提交评论
    public function saveComment(){
       $data = array();
       $data['content'] = input('content');
       $data['fid'] = input('fid');
       $data['uid'] = $this->loginuser['uid'];
       $commentModel = new ForumCommentModel();
       $r = $commentModel->where(array('uid'=>$data['uid'],'fid'=>$data['fid']))->count();
       if($r>0){
           return jsonTo(0,'不能重复评论');
       }else{
           $r =  $commentModel->sbmit($data);
           if($r){
               return jsonTo(1,'提交成功');
           }else{
               return jsonTo(0,'提交失败');
           }
       }
    }

    //提交回复
    public function saveReply(){
        $com_id = input('com_id');
        $content = input('content');
        $commentModel = new ForumCommentModel();
        $comment = $commentModel->getInfo($com_id);
        $data = array(
            'rpy_id' => $com_id,
            'uid' => $this->loginuser['uid'],
            'fid' => $comment->fid,
            'content' => $content,
        );
        $r = $commentModel->submit($data);
        if($r){
            return jsonTo(1,'回复成功',$data);
        }else{
            return jsonTo(0,'提交失败');
        }

    }

    //获取用户评论
    public function getUserCommentList(){
        $uid = $this->loginuser['uid'];
        $forumCommentModel = new ForumCommentModel();
        $forumModel = new ForumModel();
        $fids = $forumModel->where('uid','=',$uid)->column('fid');
        $data =  $forumCommentModel->relation('getUser,getForum,getReply')->whereIn('fid',$fids)->where('rpy_id','=',0)->order('create_time desc')->paginate(10);
        if($data){
            return jsonTo(1,'获取成功',$data);
        }else{
            return jsonTo(0,'获取失败');
        }

    }

    //删除主题
    public function delForum(){
        $fid = input('fid');
        $forumModel = new ForumModel;
        $userModel = new UserModel();
        $forum = $forumModel->find($fid);
        $pic = $forum->getData('pic');
        if($r = $forum->delete()){
            //删除积分
            $userModel->reduceIntegral($this->loginuser['uid'],10 + $forumModel->getLikeCount($fid));
            //删除评论
            db('forum_comment')->where('fid','=',$fid)->delete();
            //删除点赞数据
            db('forum_like')->where('fid','=',$fid)->delete();
            //删除标签数据
            db('forum_to_label')->where('fid','=',$fid)->delete();
            //删除主题图片
            unlink('./'.$pic);
            return jsonTo(1,'删除成功');
        }else{
            return jsonTo(0,'删除失败');
        }
    }
}
