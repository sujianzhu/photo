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
                $url = url('Index/index','menuid=1');
                return jsonTo(1,'登陆成功',$url);
            }else{
                return jsonTo(0,'登陆失败');
            }
        }
        $this->view->engine->layout(false);
        return view('login');
    }

    //推出登陆
    public function logout(){
        cookie('token',null);
        return redirect(url('login'));
    }

    //管理后台首页
    public function index(){
        return view ('main');
    }
}
