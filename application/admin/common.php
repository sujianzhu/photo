<?php

//获取后台菜单地址
function getMenuUrl($url,$id){
    return url($url,'menuid='.$id);
}