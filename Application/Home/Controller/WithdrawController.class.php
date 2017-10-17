<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class WithdrawController extends BaseController {

    //提现管理
    public function withdrawManagement(){
        $this->display();
    }

    //提现完成
    public function withdrawComplete(){
        $this->display();
    }

    //提现审核
    public function withdrawExamine(){
        $this->display();
    }

    //付款确认
    public function withdrawConfirm(){
        $this->display();
    }

    //黑名单
    public function withdrawBlackList(){
        $this->display();
    }

    //批量审核
    public function batchCheck(){
        $post = I('post.');
        $ids = $post['id_arr'];//批量修改的id
        $state = $post['state'];//状态

        //组装SQL
        $display_order = $ids;
        $ids = implode(',', array_values($ids));
        $sql = "UPDATE withdrawals SET state = CASE id ";
        foreach ($display_order as $id) {
            $sql .= sprintf("WHEN %d THEN %d ", $id, $state);
        }
        $sql .= "END WHERE id IN ($ids)";
//var_dump($sql);
//        die;
        D('withdrawals')->query($sql);//执行修改
        
        $json = array('state'=>1,'msg'=>'修改成功！');

        echo json_encode($json);
        die;
    }
}