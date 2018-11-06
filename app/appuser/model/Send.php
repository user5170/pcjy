<?php
namespace app\appuser\model;

use think\Model;
use think\Db;
class Send extends Model
{
    public function login($data){
        $user=db('users')->where('email',$data['email'])->find();
        if($user){
            if($user['password'] == md5($data['password'])){
                session('nickname',$user['nickname']);
                session('uid',$user['user_id']);
                return 1; //信息正确
            }else{
                return -1; //密码错误
            }
        }else{
            return -1; //用户不存在
        }
    }

    public function sendmessage($mobile,$msg,$type){

        //更新验证表
        $where = [];
        $where['mobile'] = $mobile;
        $data = [];
        $data['is_use'] = 0;
        $change = db('verify')->where($where)->update($data);

        //组织返回信息
        $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
        );
        $smsapi = "http://www.smsbao.com/"; //短信网关
        $user = "yasu69";                   //短信平台帐号
        $pass = md5("hu19920609");          //短信平台密码
        $content="【云仓联】本次动态验证码为".$msg."（10分钟内有效）若非本人操作，请忽略本短信。";//要发送的短信内容
        $phone = $mobile;                   //要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl);
//  $result = 0;
        //返回状态
        if($result != 0){
            $status = 1;
        }else{
            $status = 0;
            //插入记录
            $data = [];
            $data['mobile'] = $mobile;
            $data['code'] = $msg;
            $data['w_time'] = time();
            $data['is_use'] = 1;
            $data['info'] = $statusStr[$result];
            $data['type'] = $type;
            $int = db('verify')->insert($data);
            if(!$int){
                $status = 1;
            }
        }
        return array('status'=>$status);
    }
}