<?php
namespace app\api\controller;

class PayController extends BaseController{

    //支付下单
    public function doPay(){
        $atid = input('upg_atid');
        $pay_cost = input('pay_cost');
        $pay_cost_time = input('pay_cost_time');
        if($atid<$this->loginuser['atid']){
            return jsonTo('0','不能低于当前用户账户类型');
        }else{
            /*$data = array(
                'uid' => $this->loginuser['uid'],
                'atid' => $atid,
                'stime' => time(),
                'etime' => strtotime(' +'.$pay_cost_time.' month',time()),
            );*/

            /* 更新账户类型 */

            /* 写入升级日志*/
            $log_d = array(
                'uid' => $this->loginuser['uid'],
                'type' =>  $this->loginuser['atid'] == $atid ? 1 : 2,
                'exp' => $pay_cost_time,
                'pay_cost' => $pay_cost,
                'pay_time' => time(),
            );
            $r = db('user_upg_log')->insert($log_d);
        }


        /*接入微信支付*/

    }

    //支付通知
    public function notify(){

    }


}
