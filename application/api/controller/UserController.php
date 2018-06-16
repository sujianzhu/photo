<?php
namespace app\api\controller;

use app\common\model\UserModel;
use app\common\model\AccountTypeModel;
use app\common\model\ForumModel;

class UserController extends BaseController{

    public function initialize(){
        parent::initialize();
        $this->appsecret = '9080a361787b49f77eb05a5c7d0c23ec';
        $this->appid = 'wx84584a1b56779c13';
    }

    //获取用户信息
    public function getInfo(){
        $userModel = new UserModel();
        $forumModel = new ForumModel();
        $user = $this->loginuser;
        //关注我的
        $user['follow_me_count'] = $userModel->getFollowCount('',$user['uid']);
        //我关注的
        $user['follow_count'] = $userModel->getFollowCount($user['uid']);
        //收到的点赞
        $user['like_me_count'] = $forumModel->getLikeToatlCount($user['uid']);
        //收到的评论
        $user['comment_me_count'] = $forumModel->getCommentTotalCount($user['uid']);
        //主图总数
        $user['forum_count'] = $forumModel->getForumCount($user['uid']);
        if($user){
            return jsonTo(1,'获取成功', $user);
        }else{
            return jsonTo(0,'未登陆');
        }
    }

    //授权登录处理
    public function authLogin(){
        $post = input('post.');
        $code = $post['code'];
        $encryptedData = $post['encryptedData'];
        $iv = $post['iv'];
        if($code){
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->appsecret.'&js_code='.$code.'&grant_type=authorization_code';
            $res = json_decode(curl_get($url),true);
            $pc = new \org\wxapplet\wxBizDataCrypt($this->appid, $res['session_key']);
            $errCode = $pc->decryptData($encryptedData, $iv, $data );
            if($errCode==0){
                $logininfo = json_decode($data,true);
                $userModel = new UserModel();
                $res = $userModel->login($logininfo);
                return json($res);
            }else{
                return $errCode;
            }
        }
    }

    //上传图片
    public function upload(){
        $file = request()->file('pics');
        $savepath = 'uploads/forum/'.$this->loginuser['original_id'];
        $info = $file->validate(['size'=>25*1024*1024,'ext'=>'jpg,png'])->rule('sha1Microtime')->move($savepath);
        if($info){
            return jsonTo(1,'上传成功',$savepath.'/'.$info->getSaveName());
        }else{
            return jsonTo(0,'');
        }
    }

    //修改头像
    public function saveHeadPic(){
        $file = request()->file('pics');
        $savepath = 'uploads/headpic/'.$this->loginuser['original_id'];
        $info = $file->validate(['size'=>10*1024*1024,'ext'=>'jpg,png'])->rule('sha1Microtime')->move($savepath);
        if($info){
            $path = $savepath.'/'.$info->getSaveName();
            $userModel = new UserModel();
            $r = $userModel->saveUserParam($this->loginuser['uid'],'headpic',$path);
            if($r){
                return jsonTo(1,'上传成功',config('app_host').'/'.$path);
            }else{
                return jsonTo(0,'上传失败');
            }
        }else{
            return jsonTo(0,'上传失败');
        }
    }

    //修改用户单项参数
    public function saveUserParam($key,$value){
        if($key){
            $userModel = new UserModel();
            $r = $userModel->saveUserParam($this->loginuser['uid'],$key,$value);
            if($r!==false){
                return jsonTo(1,'修改成功');
            }else{
                return jsonTo(0,'修改失败');
            }
        }else{
            return jsonTo(0,'参数错误');
        }
    }

    //关注或取消关注
    public function doFollow(){
        $fuid = input('fuid');
        if($fuid && $this->loginuser['uid']!=$fuid){
            $type = input('type');
            $userModel = new UserModel();
            if($type==1){
                $r = $userModel->confirmFollow($this->loginuser['uid'],$fuid);
            }else{
                $r = $userModel->cancelFollow($this->loginuser['uid'],$fuid);
            }
            //判断是否相互关注
            $is_mutual_follow = in_array($fuid,$userModel->getMuTualFollow($this->loginuser['uid'],$fuid)) ? 1 : 0;
            $data = array('c'=>$r['data'],'is_mf'=>$is_mutual_follow);
            return jsonTo($r['code'],$r['msg'],$data);
        }else{
            return jsonTo(0,'缺少参数！');
        }

    }

    //获取账户类型
    public function getAccountTypeList(){
        $accTypeModel = new AccountTypeModel();
        $data = $accTypeModel->order('grade')->select();
        if($data){
            return jsonTo(1,'获取成功',$data);
        }else{
            return jsonTo(0,'获取失败');
        }
    }

    //


}
