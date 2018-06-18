<?php
namespace app\admin\controller;

use app\common\model\AdminModel;


class IndexController extends BaseController{

    public function initialize(){
        parent::initialize();
    }

    //管理员登陆
    public function login(){
        if(request()->isPost()){
            $adminModel = new AdminModel();
            $name = input('admin_name');
            $pwd = input('admin_pwd');
            if($token = $adminModel->doLogin($name, $pwd)){
                cookie('token',$token);
                return jsonTo(1,'登陆成功');
            }else{
                return jsonTo(0,'登陆失败');
            }
        }
        return view('login');
    }

    //管理后台首页
    public function index(){

    }
}
