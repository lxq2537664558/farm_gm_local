<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class AuthorityController extends BaseController {
    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
//        var_dump($data);
        $return = json_decode($data,true);
        return $return;
    }

    //权限管理
    public function authorityManagement(){
        $admin = $this->getAll('admin');
        $state_array = array('启用','禁用');
        $group_array = array(0,'最高管理员','代理管理员','佣金管理员','客服管理员','秩序管理员','策划管理员');
//        var_dump($admin);
        foreach ($admin as $k=>$v){
            $admin[$k]['state'] = $state_array[$v['state']];
            $admin[$k]['group'] = $group_array[$v['group_id']];
        }

        $this->assign('admin',$admin);
        $this->display();
    }

    //支付列表
    public function authorityPayList(){
        $post = I('post.');
        if($post) {
            $search = I('post.search',0);//支付状态
            $data2 = I('post.data',0);//支付状态标识，辨识search为0无法分辨的状态
            $uid = I('post.uid',0);
            $gte = I('post.gt','');//大于
            $lte = I('post.lt','');//小于
            $gte *= 100;
            $lte *= 100;
            $pageSize = I('post.pageSize',0);
            $pageSize = $pageSize?$pageSize:20;

            //处理分页
            $page = I('post.page',1);
//            $pageSize = 20;
            $start = $pageSize*($page-1);

            //状态标识
            $state_array =  array('待支付','已完成');
            $type_array = array('快钱','支付宝','微信');

            //请求接口数据
            $page = $post['page']?$post['page']:1;
            $url = 'http://'.C('SERVER_IP').'/GetPayList';

            $params = 'index='.$start.'&num='.$pageSize;
            if($data2){
                $params .= '&state='.$search;
            }

            //拼接筛选条件
            $uid?$params.='&showId='.$uid:'';
            $gte?$params.='&gte='.$gte:'';
            $lte?$params.='&lte='.$lte:'';

//            var_dump($params);die;
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
            $data = $lists['pays'];

            //获取uids
            foreach ($data as $v) {
                $uids[] = $v['showId'];
            }

            //用户名列表
            $where['id'] = array('in', $uids);
            $user_names = $this->getAll('user', $where, 'id', 'username');

            $temp_num = 0;

            foreach ($data as $k => $v) {
                $data[$k]['username'] = $user_names[$v['showId']]?$user_names[$v['showId']]:'';
                $data[$k]['state'] = $state_array[$v['state']];
                $data[$k]['type'] = $type_array[$v['type']];
                $data[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
                $data[$k]['num'] = $v['num']/100;

                $temp_num += $v['num'];
            }


            $data[] = array(
                'showId'=>'总计：',
                'username'=>'-',
                'tradeId'=>'-',
                'num'=>$temp_num/100,
                'type'=>'-',
                'time'=>'-',
                'state'=>'-',
            );
//            var_dump($lists['totalNum']);die;
            echo json_encode(array('data' => $data, 'page' => array('totalPage'=>ceil($lists['totalNum']/$pageSize),'page'=>$page)));
            die;
        }

        $this->display();
    }

    //添加用户
    public function addUser(){
        $post = I('post.');
        if($post) {
            $post['password'] = base64_encode($post['password']);
            $post['add_time'] = time();
            $res = $this->insAndUpdate('admin','',$post);
            if($res['state']){
                $this->success('操作成功！',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/authorityManagement'));
            }else{
                $this->error('添加失败！');
            }
        }else{
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            $this->display();
        }
    }

    //修改用户
    public function editUser(){
        $post = I('post.');
        $id = I('get.id');
        $where['id'] = $id;
        if($post) {
            $post['password'] = base64_encode($post['password']);
            $post['add_time'] = time();

            $res = $this->insAndUpdate('admin',$where,$post);
            if($res['state']){
                $this->success('操作成功！',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/authorityManagement'));
            }else{
                $this->error('修改失败！');
            }
        }else{
            $info = $this->getAll('admin',$where);
            $info = current($info);
            $this->assign('info',$info);
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,array('id'=>$id)));
            $this->display();
        }
    }

    //删除用户
    public function deleteUser(){
        $id = I('get.id');
        $where['id'] = $id;
        $res = $this->deleteInfo('admin',$where);
        if($res['state']){
            $this->success('操作成功！',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/authorityManagement'));
        }else{
            $this->error('删除失败！');
        }
    }

    public function test(){
        $data = array(
            array(1,2,3,4),
            array(1,2,3,4),
            array(1,2,3,4),
            array(1,2,3,4),
        );
        $this->downLoadExcel('test','',$data);
    }

}