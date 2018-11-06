<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
class Money extends Common{
    //会员列表
    public function record(){
        if(request()->isPost()){
            $account_type = db('account_type')->column('id,name');
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $where = [];
            $where['m.uid|u.username'] = array('like',"%".$key."%");
            $list=db('money_log')->alias('m')
                ->join('users u','u.id = m.uid','left')
                ->field('m.*,u.username')
                ->where($where)
                ->order('m.w_time desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['w_time'] = date('Y-m-d H:s',$v['w_time']);
                $list['data'][$k]['account_type'] = $account_type[$v['account_type']];
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function delall(){
        $map['id'] =array('in',input('param.ids/a'));
        db('money_log')->where($map)->delete();

        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('record');
        return $result;
    }

    public function withdraw(){
        if(request()->isPost()){
            $account_type = db('account_type')->column('id,name');
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $where = [];
            $where['account_type'] = 1;
            $where['m.uid|u.username'] = array('like',"%".$key."%");
            $list=db('money_log')->alias('m')
                ->join('users u','u.id = m.uid','left')
                ->field('m.*,u.username')
                ->where($where)
                ->order('m.w_time desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['w_time'] = date('Y-m-d H:s',$v['w_time']);
                if($v['status'] == 1){
                    $list['data'][$k]['status'] = '<span style="color:green">已审核</span>';
                }elseif($v['status'] == 2){
                    $list['data'][$k]['status'] = '<span style="color:red">已拒绝</span>';
                }else{
                    $list['data'][$k]['status'] = '<span style="color:blue">待审核</span>';
                }
            }   
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function check(){
        $type = input('type');
        $id = input('pid');
        $yz = db('money_log')->where('id',$id)->find();
        $save = [];
        if($type == 'yes' && $yz){
            $save['status'] = 1;
        }elseif($type == 'no' && $yz){
            $save['status'] = 2;
        }else{
            $this->error('未找到信息');
        }
        $rs = db('money_log')->where('id',$id)->update($save);
        if($rs){
            $this->error('审核成功');
        }else{
            $this->error('审核失败');            
        }

    }

}