<?php
namespace Home\Controller;
use Think\Controller;
class CheckMoneyController extends Controller {
    public function index(){
        $this->assign('url',U('Home/CheckMoney/checkMoney'));
        $this->display();
    }

    //处理列表,显示有金币记录但是没有提现记录的
    public function checkMoney(){
        $post = I('post.');
        $uid = $post['uid'];

        //获取用户信息
        $user_info = D('user')->where(array('id'=>$uid))->select();
        $user_info = current($user_info);
        $this->assign('user_info',$user_info);

        //获取提现记录
        $withdraw_records = D('withdrawals')->where(array('uid'=>$uid))->select();
        $header1 = array_keys($withdraw_records[0]);
        $this->assign('header1',$header1);
        $this->assign('withdraws',$withdraw_records);

        //获取金币记录
        $gold_records = D('gold_record')->where(array('uid'=>$uid,'type'=>0))->select();
        $header2 = array_keys($gold_records[0]);
        foreach ($gold_records as $val){
            $gold_times[$val['time']] = $val['id'];
        }

        foreach ($withdraw_records as $k=>$v){
            for ($i=$v['time']-5;$i<=$v['time']+5;$i++){
                if($gold_times[$i]){
                    $remark_w[$k] = 1;
                    $remark_g[$gold_times[$i]] = 1;
                }
            }
        }
//        var_dump($remark_w,$remark_g,$gold_times);
//        var_dump($remark_g);
//        die;
        $this->assign('remark_w',$remark_w);
        $this->assign('remark_g',$remark_g);
        $this->assign('header2',$header2);
        $this->assign('gold_records',$gold_records);
        $this->display();
    }
    
    //添加提现记录到数据库
    public function addToDatabase(){
        $gid = I('get.gid',0);

        $data = array(
            'uid'=>1,
            'money'=>1,
            'collection_account'=>1,
            'bank'=>1,
            'realname'=>1,
            'yingfu'=>1,

            'state'=>0,
            'time'=>1,
            'payee_name'=>'',
        );
    }

    //处理列表
    public function checkMoney(){
        $post = I('post.');
        $uid = $post['uid'];

        //获取用户信息
        $user_info = D('user')->where(array('id'=>$uid))->select();
        $user_info = current($user_info);
        $this->assign('user_info',$user_info);

        //获取提现记录
        $withdraw_records = D('withdrawals')->where(array('uid'=>$uid))->select();
        $header1 = array_keys($withdraw_records[0]);
        $this->assign('header1',$header1);
        $this->assign('withdraws',$withdraw_records);

        //获取金币记录
        $gold_records = D('gold_record')->where(array('uid'=>$uid,'type'=>0))->select();
        $header2 = array_keys($gold_records[0]);
        foreach ($gold_records as $val){
            $gold_times[$val['time']] = $val['id'];
        }

        foreach ($withdraw_records as $k=>$v){
            for ($i=$v['time']-5;$i<=$v['time']+5;$i++){
                if($gold_times[$i]){
                    $remark_w[$k] = 1;
                    $remark_g[$gold_times[$i]] = 1;
                }
            }
        }
//        var_dump($remark_w,$remark_g,$gold_times);
//        var_dump($remark_g);
//        die;
        $this->assign('remark_w',$remark_w);
        $this->assign('remark_g',$remark_g);
        $this->assign('header2',$header2);
        $this->assign('gold_records',$gold_records);
        $this->display();
    }
}