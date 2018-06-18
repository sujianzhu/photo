<?php
namespace app\common\model;
use think\Model;

class AdminModel extends Model{

    protected $pk = 'admin_id';

    //管理员登陆
    public function doLogin($name,$pwd){
        $admin = $this->where(array('admin_name'=>$name,'admin_pwd'=>sha1($pwd)))->find();
        if($admin['admin_id']){
            $token = sha1Microtime();
            $admin->token = $token;
            $admin->last_ip = request()->ip();
            $admin->last_time = time();
            if($admin->save()){
                return $token;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //获取管理员登陆信息
    public function getLoginAdmin($token){
        return $this->where(array('token'=>$token))->find();
    }


}