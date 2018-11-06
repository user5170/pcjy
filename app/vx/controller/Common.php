<?php
namespace app\vx\controller;
use think\Input;
use think\Controller;
use think\Db;
class Common extends Controller{
    static $AUTH_CHECK = true; //默认必须登录
    public function _initialize(){
      
        //登录处理
        if(session('user.id')) {
            //非app时(web|mobile|...)，从session读取登录用户
            $openid = session('user.id');
            
        } else {
            //app时，根据guid登录用户
            if(static::$AUTH_CHECK) {
                //需要登录的接口，guid必须
                $openid = input('openId') ?:'';
                if(empty($openid)) {
                    $this->redirect('vx/login/index');
                }else{
                    $openid = db('users')->where('openid',$openid)->value('id');
                    if($openid) {
                        //有guid时重建session
                        $user = db('users')->field('id,username')->where("id", $guid)->find();
                        session('user',$user);
                    }
                }
            }
            if($openid) {
                //有guid时重建session
                $user = db('users')->field('id,username')->where("id", $openid)->find();
                session('user',$user);
            }else{
                return '登录信息失效';
            }
        }
        
        if (!session('user.id')) {
            $this->error('请先登录',url('/login'));
        }
        
    }
}