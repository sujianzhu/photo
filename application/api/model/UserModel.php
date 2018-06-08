<?php
namespace app\api\model;
use think\Model;

class UserModel extends Model{


    //授权登陆
    public function login($wxuserinfo){
        $userinfo = $this->where('unionid',$wxuserinfo['unionId'])->find();
        if($userinfo){
            //用户已存在，更新token
            $token = sha1($wxuserinfo['unionId'].microtime());
            $status = $this->where('unionid',$wxuserinfo['unionId'])->update(array('token'=>$token));
            if($status>0){
                //return jsonTo(1,'',$token);
                return array('code'=>1,'msg'=>'授权成功','data'=>$token);
            }else{
                return array('code'=>0,'msg'=>'授权失败');
            }
        }else{
            //用户不存在，创建
            $acc_num = sha1($wxuserinfo['unionId'].microtime());
            $insert_data = array(
                'nickname' => $wxuserinfo['nickName'],
                'gender' => $wxuserinfo['gender'],
                'headpic' => $wxuserinfo['avatarUrl'],
                'unionid' => $wxuserinfo['unionId'],
                'acc_num' => $acc_num,
                'original_id' => 'KT'.substr($acc_num,-6).'_'.substr($wxuserinfo['unionId'],-6),
                'create_time' => time(),
                'token' => sha1($wxuserinfo['unionId'].microtime())
            );
            $insert_id = $this->allowField(true)->insert($insert_data);
            if($insert_id){
                return array('code'=>1,'msg'=>'授权成功','data'=>$insert_data['token']);
            }else{
                return array('code'=>0,'msg'=>'授权失败');
            }
        }
    }

    //根据token获取用户信息
    public function getLoginUser($token){
        return $this->where('token',$token)->find();
    }
}