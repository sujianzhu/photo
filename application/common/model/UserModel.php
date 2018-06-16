<?php
namespace app\common\model;
use think\Model;

class UserModel extends Model{

    protected $pk = 'uid';

    public function getLevelAttr($value,$data){
        return floor((sqrt(49+4*($data['integral'])/10))-7)/2 + 1;
    }

    protected function setHeadPicAttr($value){
        if(strpos($value,'http://') || strpos($value,'https://')){
            return $value;
        }else{
            return config('app_host').'/'.$value;
        }
    }


    //获取成就称号
    public function getLevelName($level){
        $arr = array('岁月静好','迈步前行','追求技巧','刻月铭心','点睛神笔');
        $key = $level/10;
        return $arr[$key];
    }

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
        $data = $this->field('user.*,accountType.atid, accountType.name as atname')->alias('user')->where('token',$token)->join('user_account','user.uid=user_account.uid')->join('accountType','accountType.atid=user_account.atid')->find();
        $data['level_name'] = $this->getLevelName($data['level']);
        return $data;
    }

    //修改用户参数
    public function saveUserParam($uid,$key,$value){
        if($key){
            return $this->allowField($key)->save(array($key=>$value),array('uid'=>$uid));
        }
    }

    //关注
    public function confirmFollow($uid, $f_uid){
        $check_r = db('user_follow')->where(array('uid'=>$uid,'f_uid'=>$f_uid))->count();
        if($check_r>0){
            return array('code'=>0,'msg'=>'无须重复关注！','data'=>'');
        }else{
            $r = db('user_follow')->insert(array('uid'=>$uid,'f_uid'=>$f_uid));
            $total = db('user_follow')->where('f_uid',$f_uid)->count();
            if($r){
                //增加用户积分
                $userModel = new UserModel();
                $userModel->addIntegral($f_uid,2);
                return array('code'=>1,'msg'=>'已关注！','data'=>$total);
            }else{
                return array('code'=>0,'msg'=>'操作失败！','data'=>'');
            }
        }
    }

    //取消关注
    public function cancelFollow($uid, $f_uid){
        $check_r = db('user_follow')->where(array('uid'=>$uid,'f_uid'=>$f_uid))->count();
        if($check_r>0){
            $r = db('user_follow')->where(array('uid'=>$uid,'f_uid'=>$f_uid))->delete();
            if($r){
                //减少用户积分
                $userModel = new UserModel();
                $userModel->reduceIntegral($f_uid,2);
                return array('code'=>1,'msg'=>'已取消关注！','data'=>'');
            }else{
                return array('code'=>0,'msg'=>'操作失败！','data'=>'');
            }
        }else{
            return array('code'=>0,'msg'=>'已取消！','data'=>'');
        }
    }

    //获取用户关注数
    public function getFollowCount($uid='', $f_uid=''){
        $where = array();
        $uid!='' && $where['uid'] = $uid;
        $f_uid!='' && $where['f_uid'] = $f_uid;
        return db('user_follow')->where($where)->count();
    }

    //获取相互关注用户id
    public function getMuTualFollow($uid, $f_uid=''){
        $where = array('uid'=>$uid);
        $f_uid!='' && $where['f_uid'] = $f_uid;
        $a = db('user_follow')->where($where)->column('f_uid');
        $b = array();
        if($a){
            return db('user_follow')->where('uid','in',$a)->column('uid');
        }
        return $b;
    }

    //增加积分
    public function addIntegral($uid, $amount){
        $user = $this->find($uid);
        $user->integral = array('inc',$amount);
        return $user->save();
    }

    //减少积分
    public function reduceIntegral($uid, $amount){
        $user = $this->find($uid);
        $user->integral = array('dec',$amount);
        return $user->save();
    }

    //根据积分计算用户等级
    public function getCurrentLevel($uid){
        $user = $this->find($uid);
        return floor((sqrt(49+4*(($user->integral)/10))-7)/2 + 1);
    }
}