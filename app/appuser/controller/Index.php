<?php
namespace app\appuser\controller;
use think\Input;
use tkink\Db;
use app\appuser\controller\Common;
class Index extends Common{
    public function _initialize(){
        if(empty(session('user.id'))){
            $this->error('请先登录',url('/login'));
        }
        parent::_initialize();
    }
    public function index(){
        $uid = session('user.id');
        $user_info = db('users')->where('id',$uid)->find();

        $approve = db('user_info')->where('uid',$uid)->value('status');

        if(!$approve){
            $user_info['approve'] = 0;
        }else{
            $user_info['approve'] = $approve + 1;
        }
        $where = [];
        $where['uid'] = $uid;
        $where['type'] = 'money';
        $where['number'] = array('gt',0);
        $user_info['count_money'] = db('money_log')->where($where)->sum('number');

        $b_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $e_time = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['w_time'] = array('between',array($b_time,$e_time));
        $user_info['day_money'] = db('money_log')->where($where)->sum('number');

        $user_info['money'] = db('wallet')->where('uid',$uid)->value('money');

        $user_info['system'] = db('user_msg')->where(array('uid'=>$uid,'is_read'=>0))->count('id');

        $this->assign('userinfo',$user_info);
        $this->assign('title','会员中心');
        return $this->fetch();
    }
    
    //退出登陆
    public function setting(){

        return $this->fetch();

//        session('user',null);
//        $this->redirect(url('/login'));
    }
    public function loginout(){
        session('user',null);
        $this->redirect('appuser/login/index');
    }

}