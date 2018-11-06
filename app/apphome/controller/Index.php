<?php
namespace app\apphome\controller;
use think\Db;
class Index extends Common{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $this->assign('title','教育');
        return $this->fetch();
    }

}