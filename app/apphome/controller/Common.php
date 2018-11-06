<?php
namespace app\apphome\controller;
use think\Input;
use think\Db;
use clt\Leftnav;
use think\Request;
use think\Controller;
class Common extends Controller{
    static public  $guid = '';
    static $AUTH_CHECK = true; //默认必须登录

    public function _initialize(){
        
    }

}