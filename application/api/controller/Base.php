<?php
namespace app\api\controller;
use think\Controller;

class Base extends Controller{

    protected $loginuser;

    public function initialize(){
        $this->checkAuthLogin();
    }

    //检查登陆状态
    protected function checkAuthLogin(){
        $token = input('post.token');
        $this->loginuser = db('user')->where(array('token'=>$token))->find();
        if($token && empty($this->loginuser)){
            exit(json_encode(array('code'=>0,'msg'=>'')));
        }
    }

}
