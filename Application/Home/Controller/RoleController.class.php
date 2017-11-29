<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
use Home\Controller\UserController;
use Home\Controller\OpenSSLController;
use Home\Controller\AdvanceController;
class RoleController extends BaseController {
    //节点权限管理界面
    public function roleController(){
        $group_id = I('get.group_id',1);//用户组
        //所有用户组
//        $groups = $this->getAll('admin','','group_id','group_id',array('g'=>'group_id'));

        //所有的节点
        $nodes = $this->getAll('node','','id');
        $all_node_ids = array_keys($nodes);//所有节点id

        //当前用户组对应的权限
        $group_id?$rWhere['role_id'] = $group_id:'';
        $role_node = $this->getAll('role_node',$rWhere);
        foreach ($role_node as $rn){
            $rules[$rn['role_id']][] = $rn['node_id'];
        }
        $added_node_ids = $rules[$group_id];//当前已添加的节点ids
        sort($added_node_ids);
        if($added_node_ids) {
            $diff_node_ids = array_values(array_diff($all_node_ids, $added_node_ids));
        }else{
            //数组为空，diff函数将不能求差集
            $diff_node_ids = $all_node_ids;
        }
//var_dump($all_node_ids,$added_node_ids,$diff_node_ids);

        ksort($rules);

        $this->assign('group_id',$group_id);
        $this->assign('nodes',$nodes);
        $this->assign('rules',$rules[$group_id]);
        $this->assign('diff_node_ids',$diff_node_ids);
//        var_dump($rules);
        $this->display();
    }

    //添加节点
    public function addNode(){
        $role_id = I('get.group_id',0);
        $node_id = I('get.node_id',0);
        $node_ids = I('post.node_ids');
        if($node_ids){
            foreach ($node_ids as $ni){
                $data[] = array('role_id'=>$role_id,'node_id'=>$ni);
            }
//            var_dump($data);die;
            D('role_node')->addAll($data);
        }else{
            $data = array('role_id'=>$role_id,'node_id'=>$node_id);
            $this->insAndUpdate('role_node','',$data);
        }

        $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/roleController',array('group_id'=>$role_id)));
    }

    //删除节点
    public function delNode(){
        $group_id = I('get.group_id');
        $node_id = I('get.node_id');
        $where['role_id'] = $group_id;
        $where['node_id'] = $node_id;

        $this->deleteInfo('role_node',$where);
        $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/roleController',array('group_id'=>$group_id)));
    }
}