<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\UserModel;

class BaseController extends Controller{

    protected $loginuser;

    public function initialize(){
        $this->checkAuthLogin();
    }

    //检查登陆状态
    protected function checkAuthLogin(){
        $token = input('token');
        $userModel = new UserModel();
        $this->loginuser = $userModel->getLoginUser($token);
        if(isset($token) && empty($this->loginuser)){
            exit(json_encode(array('code'=>0,'msg'=>'未登陆，请先登陆')));
        }
    }

}
