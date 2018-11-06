<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use app\admin\model\Users as UsersModel;
class News extends Common{
    //会员列表
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('user_msg')->alias('m')
                ->join('users u','u.id = m.uid','left')
                ->field('m.*,u.username')
                ->where('m.title|u.username','like',"%".$key."%")
                ->order('m.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['w_time'] = date('Y-m-d H:s',$v['w_time']);
                if($v['is_read'] == 1){
                    $list['data'][$k]['read'] = '<span style="color:green">已读</span>' ;
                }else{
                    $list['data'][$k]['read'] = '<span style="color:red">未读</span>' ;
                }
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function usersDel(){
        db('user_msg')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map['id'] =array('in',input('param.ids/a'));
        db('user_msg')->where($map)->delete();

        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
    public function add(){
        if(request()->isPost()){
            $data = input('post.');
            if($data['push'] == '3' && !empty($data['userids'])){
                $uids = explode(',', $data['userids']);
                $where = [];
                $where['id'] = array('in',$uids);
                $uids = db('users')->where($where)->column('id');
            }elseif($data['push'] == '2' && !empty($data['level'])){
                $level =explode(':',$data['level']);
                $where = [];
                $where['level'] = $level[1];
                $uids = db('users')->where($where)->column('id');
            }elseif($data['push'] == '1'){
                $uids = db('users')->column('id');
            }
            if(empty($uids)){
                $result['msg'] = '没有可以推送的会员!';
                $result['code'] = 0;
                return $result;
            }
            $date = [];
            foreach ($uids as $key => $value) {
                $info = [];
                $info['uid'] = $value;
                $info['w_time'] = time();
                $info['title'] = $data['title'];
                $info['msg'] = $data['content'];
                $date[] = $info;
            }
            $ins = db('user_msg')->insertAll($date);
            if ($ins) {
                $result['msg'] = '消息推送成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = '消息推送失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $user_level=db('user_level')->order('sort')->select();
            $this->assign('user_level',json_encode($user_level,true));
            return $this->fetch();
        }
    }

}