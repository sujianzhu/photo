<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\AdminModel;

class BaseController extends Controller{

    protected $admin;

    protected $exemption = array('Index'=>'login');

    public function initialize(){
        $this->checkLogin();
    }

    //检查登陆token
    public function checkLogin(){
        $token = cookie('token');
        /*判断免检模块*/
        $ctrl = request()->controller();
        $act = request()->action();
        if(!(array_key_exists($ctrl,$this->exemption) && in_array($act,explode(',',$this->exemption[$ctrl])))){
            if($token==''){
                $this->error('未登录！',url('index/login'));
            }else{
                $adminModel = new AdminModel();
                $this->admin = $adminModel->getLoginAdmin($token);
                if($this->admin==''){
                    $this->error('未登录！',url('index/login'));
                }
            }
        }

    }
}
