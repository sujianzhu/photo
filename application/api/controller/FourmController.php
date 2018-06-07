<?php
namespace app\api\controller;
use app\api\model\ForumModel;

class FourmController{

    public function getFourmList(){
        /*$fourmModel = new ForumModel();
        $data = $fourmModel->getList();
        return jsonTo(1,'', $data);*/
        $data = ForumModel::with('user')->select();
        print_r($data);
    }
}
