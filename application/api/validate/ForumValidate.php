<?php

namespace app\api\validate;
use think\Validate;

/**
 * 发帖验证
 */
class ForumValidate extends Validate{

    protected $rule = array(
        'cid' => 'require',
        'title' => 'max:8',
        'summary' => 'max:140',
        'pic' => 'require',
        'uid' => 'require',
    );

    protected $message = array(
        'cid.require' => '请选择图片分类！',
        'title.max' => '标题不超过8个文字！',
        'summary.max' => '描述不超过140个文字！',
        'pic.require' => '图片未上传！',
        'uid.require' => '用户未登陆！'
    );
}