<?php
namespace app\api\controller;

class PayController extends BaseController{

    //支付下单
    public function doPay(){
        $atid = input('upg_atid');
        $pay_cost = input('pay_cost');
        $pay_cost_time = input('pay_cost_time');
        $uid = $this->loginuser['uid'];
        if($atid<$this->loginuser['atid']){
            return jsonTo('0','不能低于当前用户账户类型');
        }else{

            /* 更新账户类型 */
            $acc_d = array(
                'uid' => $uid,
                'atid' => $atid,
                'stime' => time(),
                'etime' => strtotime(' +'.$pay_cost_time.' month',time())
            );
            $r = db('userAccount')->update($acc_d);
            if($r){
                /* 写入升级日志*/
                $log_d = array(
                    'uid' => $uid,
                    'type' =>  $uid == $atid ? 1 : 2,
                    'exp' => $pay_cost_time,
                    'pay_cost' => $pay_cost,
                    'pay_time' => time(),
                );
                db('user_upg_log')->insert($log_d);
            }


        }

        /*接入微信支付*/

    }

    //支付通知
    public function notify(){

    }


}
