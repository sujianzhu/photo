<?php
namespace app\api\controller;

class User extends Base{

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
                //授权操作
                $userinfo = db('user')->where(array('unionid'=>$logininfo['unionId']))->find();
                if($userinfo){
                    //用户已存在，更新token
                    $token = sha1($logininfo['unionId'].microtime());
                    $status = db('user')->where(array('unionid'=>$logininfo['unionId']))->update(array('token'=>$token));
                    if($status>0){
                        return jsonTo(1,'',$token);
                    }else{
                        return jsonTo(0,'token更新失败');
                    }
                }else{
                    //永不不存在，创建
                    $acc_num = sha1($logininfo['unionId'].microtime());
                    $insert_data = array(
                        'nickname' => $logininfo['nickName'],
                        'gender' => $logininfo['gender'],
                        'headpic' => $logininfo['avatarUrl'],
                        'unionid' => $logininfo['unionId'],
                        'acc_num' => $acc_num,
                        'original_id' => 'KT'.substr($acc_num,-6).'_'.substr($logininfo['unionId'],-6),
                        'create_time' => time(),
                        'token' => sha1($logininfo['unionId'].microtime())
                    );
                    $insert_id = db('user')->insert($insert_data);
                    if($insert_id){
                        return jsonTo(1,'',$insert_data['token']);
                    }else{
                        return jsonTo(0,'操作失败');
                    }
                }
            }else{
                echo $errCode;
            }
        }
    }

    //上传图片
    public function upload(){
        $file = request()->file('pics');
        $info = $file->validate(['size'=>25*1024*1024,'ext'=>'jpg,png,gif'])->move( 'uploads/forum/'.$this->loginuser['original_id']);
        if($info){
            $filepath = 'uploads/forum/'.$this->loginuser['original_id'].'/'.$info->getSaveName();
            return json($filepath);
        }else{
            return json(0);
        }
    }

    //发布图片
    public function publish(){
        print_r(input());
    }
}
