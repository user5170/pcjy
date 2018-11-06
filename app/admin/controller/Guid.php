<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use app\admin\model\Users as UsersModel;
class Guid extends Common{

    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $where = [];
            $where['g.uid|u.username'] = array('like',"%".$key."%");
            $list=db('guid')->alias('g')
                ->join('users u','u.id = g.uid','left')
                ->field('g.*,u.username')
                ->where($where)
                ->order('g.w_time desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['w_time'] = date('Y-m-d H:s',$v['w_time']);
                $data = Db::connect('db2')->table('clt_warehouse')->where('id',$v['w_id'])->find();
                $list['data'][$k]['name'] = $data['name'];
                if($v['status'] == 1){
                    $list['data'][$k]['status'] = '<span style="color:#5FB878">已审核</span>';
                }elseif($v['status'] == 2){
                    $list['data'][$k]['status'] = '<span style="color:#c2c2c2">已拒绝</span>';
                }else{
                    $list['data'][$k]['status'] = '<span style="color:#FF5722">待审核</span>';
                }
            }   
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function check(){
        $type = input('type');
        $id = input('pid');
        $yz = db('guid')->where('id',$id)->find();
        $save = [];
        if($type == 'yes' && $yz){
            $save['status'] = 1;
        }elseif($type == 'no' && $yz){
            $save['status'] = 2;
        }else{
            $this->error('未找到信息');
        }
        $rs = db('guid')->where('id',$id)->update($save);
        if($rs){
            $this->error('审核成功');
        }else{
            $this->error('审核失败');            
        }
    }

    public function grant(){
        $id = input('pid');
        $yz = db('guid')->where('id',$id)->find();
        if(!$yz){
            $this->error('未找到信息');
        }
        $money = input('money');
        if($money <= 0){
            $this->error('请输入有效的奖励金');
        }
        $uid = $yz['uid'];
        $wallet = db('wallet')->where('uid',$uid)->value('money');
        $remain = $wallet + $money;
        $datas = [];
        $datas['uid'] = $uid;
        $datas['w_time'] = time();
        $datas['account_type'] = 4;
        $datas['number'] = $money;
        $datas['remark'] = '会员编号'.$uid.'，带看仓库成功，获得奖励金'.$money.'元';
        $datas['type'] = 'money';
        $datas['remain'] = $remain;
        $datas['status'] = 1;
        $ins = db('money_log')->insert($datas);
        if($ins){
            $save = [];
            $save['money'] = $remain;
            db('wallet')->where('uid',$uid)->update($save);
            $userinfo = db('users')->where('id',$uid)->value('li_money');
            $moneys = $userinfo + $money;
            $save = [];
            $save['li_money'] = $moneys;
            db('users')->where('id',$uid)->update($save);
            $result['msg'] = '奖励金发放成功！';
            $result['code'] = 1;
            $result['money'] = $money;
            return $result;
        }else{
            $this->error('奖励金发放失败！');
        }
    }
    public function usersDel(){
        db('users')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map['id'] =array('in',input('param.ids/a'));
        db('users')->where($map)->delete();

        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

}