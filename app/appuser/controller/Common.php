<?php
namespace app\appuser\controller;
use think\Input;
use think\Controller;
use think\Db;
class Common extends Controller{
    static public  $guid = '';
    static $AUTH_CHECK = true; //默认必须登录

    public function _initialize(){
        $guid = self::selfguid('');
        //登录处理
        if(session('user.id')) {
            //非app时(web|mobile|...)，从session读取登录用户
            $guid = session('user.id');
        } else {
            //app时，根据guid登录用户
            if(static::$AUTH_CHECK) {
                //需要登录的接口，guid必须
     
                $guid = input('guid') ?: '';
                $openid = input('openId') ?:'';
                if(empty($guid) && empty($openid)) {
                    $this->redirect('appuser/login/index');
                }elseif(!empty($openid)){
                    $openid = db('users')->where('openid',$openid)->value('id');
                    if($openid) {
                        //有guid时重建session
                        $user = db('users')->field('id,username')->where("id", $guid)->find();
                        session('user',$user);
                        $this->redirect('vx/index/index');
                    }
                }elseif(!empty($guid)){
                    $guid = db('users')->where('guid',$guid)->value('id');
                }
            } elseif(input('guid')) {
                //非必须登录,传了guid，就接收guid
                $guid = input('guid');
            }
        }
        if($guid) {
            //有guid时重建session
            $user = db('users')->field('id,username')->where("id", $guid)->find();
            session('user',$user);
        }else{
            return '登录信息失效';
        }
        if (!session('user.id')) {
            $this->error('请先登录',url('/login'));
        }
        // if(empty(session('my_city'))){
        //     $infos = $this->getCity();
        //     session('my_city',$infos['city']);
        //     $where = [];
        //     $where['name'] = array('like',$infos['city'].'%');
        //     $city_id = db('region')->where($where)->order('id')->field('id')->find();
           
        //     if(empty($city_id['id'])){
        //         session('my_city_id',927);
        //         session('my_city','苏州');
        //     }else{
        //         session('my_city_id',$city_id['id']);
        //     }
        //     $yz = Db::connect('db2')->table('clt_warehouse')->where('city',$city_id['id'])->find();
        //     if(empty($yz)){
        //         session('my_city_id',927);
        //         session('my_city','苏州');
        //     }
        // }
    }

    public function selfguid($guid){
        if(empty($guid)){
            return self::$guid;
        }else{
            return $guid;
        }
    }
    //退出登陆
    public function logout(){
        session('user',null);
        $this->redirect(url('/login'));
    }
public function getCity($ip = '')
    {
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip=json_decode(file_get_contents($url),true);
        $data = $ip;
       
        return $data;   
    }
}