<?php
namespace app\api\controller;

use app\api\model\ForumModel;
use app\api\model\UserModel;

class UserController extends BaseController{

    public function initialize(){
        parent::initialize();
        $this->appsecret = '9080a361787b49f77eb05a5c7d0c23ec';
        $this->appid = 'wx84584a1b56779c13';
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
        $info = $file->validate(['size'=>25*1024*1024,'ext'=>'jpg,png,gif'])->move($savepath);
        if($info){
            return jsonTo(1,'上传成功',$savepath.'/'.$info->getSaveName());
        }else{
            return jsonTo(0,'');
        }
    }

    //发布图片
    public function publish(){
        $forumModel = new ForumModel;
        $res = $forumModel->publish($this->loginuser['uid'],input('post.'));
        return json($res);
    }
}
