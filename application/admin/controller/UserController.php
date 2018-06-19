<?php
namespace app\admin\controller;
use app\common\model\UserModel;

class UserController extends BaseController{

    public function initialize(){
        parent::initialize();
    }

    //会员列表
    public function index(){
        if(request()->isAjax()){

        }
        return view('user/userlist');
    }
}