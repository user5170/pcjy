<?php
namespace app\vx\controller;
use think\Input;
use tkink\Db;
use app\vx\controller\Common;
use app\vx\model\WXBizDataCrypt;
class Index extends Common{
    public function _initialize(){
        parent::_initialize();
    }
    public function login(){
        
        $code   =   input('code');
        $encryptedData   =   input('encryptedData');
        $iv   =   input('iv');
        $appid  =  "wx80466c6fcedf0585" ;
        $secret =   "4620292a685832c3e892e4e2cf501a3b";

        $URL = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";

        $apiData=file_get_contents($URL);

        if(!isset($apiData['errcode'])){
            $sessionKey = json_decode($apiData)->session_key;
            $userifo = new WXBizDataCrypt($appid, $sessionKey);
            $errCode = $userifo->decryptData($encryptedData, $iv, $data );
            if ($errCode == 0) {
                return ($data . "\n");
            } else {
                return false;
            }
        }
    }
    public function index(){
        $this->assign('userinfo',$user_info);
        $this->assign('title','微信');
        return $this->fetch();
    }
    
    //退出登陆
    public function setting(){
        return $this->fetch();
    }

    public function sss(){
        return $this->fetch('sss');
    }

}