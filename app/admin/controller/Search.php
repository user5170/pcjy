<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use app\admin\model\Users as UsersModel;
class Search extends Common{
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('taken')->alias('t')
                ->join('users u','u.id = t.u_id','left')
                ->field('t.*,u.username')
                ->where('t.w_id|u.username','like',"%".$key."%")
                ->order('t.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['b_time'] = date('Y-m-d',$v['b_time']);
                $list['data'][$k]['e_time'] = date('Y-m-d',$v['e_time']);
                $list['data'][$k]['price'] = $v['price'].'元/天/㎡';
                $list['data'][$k]['area'] = $v['area'].'㎡';
                $list['data'][$k]['money'] = $v['money'].'元';
                $data = Db::connect('db2')->table('clt_warehouse')->where('id',$v['w_id'])->find();
                $list['data'][$k]['name'] = $data['name'];
                if($v['status'] == 1){
                    $list['data'][$k]['status'] = '<span style="color:green">已发放</span>';
                }elseif($v['status'] == 2){
                    $list['data'][$k]['status'] = '<span style="color:red">暂未发放</span>';
                }
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function grant(){
        $map = [];
        $ids = input('param.ids/a');
        $map['id'] = array('in',$ids);
        $map['status'] = 1;
        $info = db('taken')->where($map)->select();
        if(!empty($info)){
            $this->error('选中的信息中存在已发放');
        }else{
            $map['status'] = 2;
            $info = db('taken')->where($map)->select();
        }
        foreach ($info as $key => $value) {
            $w_id = $value['w_id'];
            $w_name = Db::connect('db2')->table('clt_warehouse')->where('id',$w_id)->value('name');
            if(!$w_name){
                $this->error('未找到该仓库');
            }
            $wallet = db('wallet')->where('uid',$value['id'])->value('money');
            $remain = $wallet + $value['money'];
            $datas = [];
            $datas['uid'] = $value['id'];
            $datas['w_time'] = time();
            $datas['account_type'] = 2;
            $datas['number'] = $value['money'];
            $datas['remark'] = '会员编号'.$value['id'].'，成功出租-'.$w_name.'，面积'.$value['area'].'㎡，价格'.$value['price'].'元/天/㎡，奖励'.$value['money'].'元';
            $datas['type'] = 'money';
            $datas['remain'] = $remain;
            $datas['status'] = 1;
            $ins = db('money_log')->insert($datas);

            $userinfo = db('users')->where('id',$value['id'])->value('li_money');
            $moneys = $userinfo + $value['money'];
            $save = [];
            $save['li_money'] = $moneys;
            db('users')->where('id',$value['id'])->update($save);

            if($ins){
                $save = [];
                $save['money'] = $remain;
                db('wallet')->where('uid',$value['id'])->update($save);
            }else{
                $this->error('奖励金发放失败！');
            }
            $save = [];
            $save['status'] = 1;
            db('taken')->where('id',$value['id'])->update($save);
        }

        $result['msg'] = '奖励金发放成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
    public function takenview(){
        $id = input('id');
        $info = db('taken t')->join('pact p','p.num = t.pact')->where('t.id',$id)->field('t.*,p.pdfs')->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function pactview(){
        $id = input('id');
        $info = db('taken t')->join('pact p','p.num = t.pact')->where('t.id',$id)->field('p.pdfs')->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function match(){
        if(request()->isPost()){
            $data = input('post.');
            unset($data['file']);
            foreach ($data as $key => $value) {
                if(empty($value)){
                    $this->error('信息不完整');
                }
            }
            $u_id = $data['u_id'];
            $u_id = db('users')->where('id',$u_id)->value('id');
            if(!$u_id){
                $this->error('未找到该会员');
            }
            $w_id = $data['w_id'];
            $w_name = Db::connect('db2')->table('clt_warehouse')->where('id',$w_id)->value('name');
            if(!$w_name){
                $this->error('未找到该仓库');
            }

            $data['b_time'] = strtotime($data['b_time']);
            $data['e_time'] = strtotime($data['e_time']);
            if(!empty($data['pact'])){
                $ins = [];
                $ins['num'] = rand('100000','999999');
                $ins['pdfs'] = $data['pact'];
                db('pact')->insert($ins);
            }
            unset($data['pact']);
            
            $data['pact'] = $ins['num'];
            if($data['status'] == 1 && $data['money'] > 0){
                $wallet = db('wallet')->where('uid',$u_id)->value('money');
                $remain = $wallet + $data['money'];
                $datas = [];
                $datas['uid'] = $u_id;
                $datas['w_time'] = time();
                $datas['account_type'] = 2;
                $datas['number'] = $data['money'];
                $datas['remark'] = '会员编号'.$u_id.'，成功出租-'.$w_name.'，面积'.$data['area'].'㎡，价格'.$data['price'].'元/天/㎡，奖励'.$data['money'].'元';
                $datas['type'] = 'money';
                $datas['remain'] = $remain;
                $datas['status'] = 1;
                $ins = db('money_log')->insert($datas);

                $userinfo = db('users')->where('id',$u_id)->value('li_money');
                $moneys = $userinfo + $data['money'];
                $save = [];
                $save['li_money'] = $moneys;
                db('users')->where('id',$u_id)->update($save);
                
                if($ins){
                    $save = [];
                    $save['money'] = $remain;
                    db('wallet')->where('uid',$u_id)->update($save);
                }else{
                    $this->error('奖励金发放失败！');
                }
            }
            $inster = db('taken')->insert($data);
            if ($inster) {
                $result['msg'] = '仓库信息匹配成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $this->error('奖励金发放失败！');
            }
            return $result;
        }else{
            return $this->fetch();
        }
    }

    public function claim(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $where = [];
            if(!empty($key)){
                $where['u.username'] = $key;
            }
            $list = db('claim')->alias('c')
                    ->join('users u','u.id = c.u_id','left')
                    ->where($where)
                    ->field('c.*,u.username')
                    ->order('id desc')
                    ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                    ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function view(){
        $id = input('id');
        $info = [];
        $info['claim'] = db('claim c')->where('c.id',$id)->join('users u','c.u_id = u.id','left')->field('c.*,u.username')->find();
        if(!$info['claim']){
            $this->error('信息错误');
        }
        $where = [];
        $w_id = explode(',', $info['claim']['w_id']);
        $where['id'] = array('in', $w_id);
        $info['data'] = Db::connect('db2')->table('clt_warehouse')->where($where)->select();
        $cat = Db::connect('db2')->table('clt_cat')->column('id,name');
        $region = db('region')->column('id,name');
        foreach ($info['data'] as $k=>$v){
            $info['data'][$k]['type'] = $cat[$v['type']];
            $info['data'][$k]['leave'] = $cat[$v['leave']];
            $info['data'][$k]['addresss'] = $region[$v['province']].'-'.$region[$v['city']].'-'.$region[$v['district']].'-'.$list['data'][$k]['address'];
            $info['data'][$k]['w_time'] = date('Y-m-d',$v['w_time']);
            $pic = explode(',', $v['pics']);
            $info['data'][$k]['pics'] = $pic[0];
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function editwids(){
        $ids = input('ids');
        $save = [];
        $save['w_id'] = input('wids');
        db('claim')->where('id',$ids)->update($save);
        $result['msg'] = '修改成功！';
        return $result;
    }

    public function delall(){
        $map['id'] =array('in',input('param.ids/a'));
        db('claim')->where($map)->delete();

        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

}