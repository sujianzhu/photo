<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\AdminModel;

class BaseController extends Controller{

    protected $admin;

    protected $exemption = array('Index'=>'login,logout');

    protected $menu;

    public function initialize(){
        $this->checkLogin();
        $this->getMenu();
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

    //获取后台菜单
    public function getMenu(){
        $menu = array(
            array(
                'id' => 1,
                'name' => '首页',
                'url' => 'Index/index',
                'son' => array(
                    array(
                        'id' => 11,
                        'name' => '欢迎页',
                        'url' => 'Index/main',
                        'son' => '',
                    ),
                ),
            ),
            array(
                'id' => 2,
                'name' => '会员',
                'url' => 'User/index',
                'son' => array(
                    array(
                        'id' => 21,
                        'name' => '会员列表',
                        'url' => 'User/index',
                        'son' => '',
                    ),
                    array(
                        'id' => 22,
                        'name' => '会员类型',
                        'url' => 'User/type',
                        'son' => '',
                    ),
                ),
            ),
        );
        $menuid = input('menuid');
        $menu_id1 = 0;
        $menu_id2 = 0;
        $cur_menu = array();
        if($menuid){
            foreach($menu as $r){
                if($r['id']==$menuid){
                    $cur_menu = $r;
                    $menu_id1 = $r['id'];
                    $menu_id2 = reset($r['son'])['id'];
                    break;
                }else{
                    foreach($r['son'] as $rr){
                        if($rr['id']==$menuid){
                            $cur_menu = $r;
                            $menu_id1 = $r['id'];
                            $menu_id2 = $rr['id'];
                            break;
                        }
                    }
                }
            }
        }
        $this->assign('menu',$menu);
        $this->assign('cur_menu',$cur_menu);
        $this->assign('menu_id1',$menu_id1);
        $this->assign('menu_id2',$menu_id2);
    }

}
