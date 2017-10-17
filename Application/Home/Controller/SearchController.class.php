<?php
namespace Home\Controller;
use Common\Controller\BaseController;
class SearchController extends BaseController {
    //搜索公共方法
    public function searchAll($return = 0,$condition=array()){
        $page_screen = C('PAGE_SCREEN');//筛选列表

        //ajax请求需要发送的参数
        $page_name = I('post.page_name');//需要检索的页面
        $search = I('post.search','','trim');//用户输入信息
        $table = I('post.table');//需要检索的数据表
        $page = I('post.page',1);//当前是第几页，默认为1

        $screen = $page_screen[$page_name];

        $fields = array_keys($screen);//检索字段

        if(!$table){
            echo json_encode('缺少数据表参数');
            die;
        }

        //模糊查询SQL拼接
        $search_field = I('post.field','');//定向筛选字段
        if($search_field){
            $where[$search_field] = array('like','%'.$search.'%');
        }else{
            if($search) {
                foreach ($fields as $v) {
                    $where[$v] = array('like','%'.$search.'%');
                }
                $where['_logic'] = 'or';//模糊查询逻辑为或
            }
        }

//        var_dump($search,$where);die;
        //其他条件
        if($condition){
            //拼接其他方法发送过来的条件
            foreach ($condition as $k=>$v) {
                $where2[$k] = $v;
            }

            //其他方法的条件逻辑为和
            if($where) {
                $where_main['_complex'] = array(
                    $where, $where2,
                    '_logic' => 'and'
                );
                $where = $where_main;
            }else{//如果前面模糊查询条件为空，使用组合逻辑会报错
                $where = $where2;
            }
        }

        //数据分页配置
        $pager = array('page'=>$page,'pageSize'=>20);

        //数据查询
        $results = $this->getAll($table,$where,'id','','id asc',$pager);

        //字段翻译的映射
        $translate_fields = C('TRANSLATE_FIELDS');
        $translate = $translate_fields[$table];

        //处理状态数据
        foreach ($results['data'] as $k=>$v){
            //清除NULL值，替换为空字符串
            foreach ($v as $key=>$val){
                if($val == NULL){
                    $v[$key] = '';
                }
            }

            $temp = $v;

            //字段翻译，状态数字转为文字
            foreach ($translate as $field=>$string){
                if($string == 'to_time'){
                    $temp[$field] = date('Y-m-d H:i:s',$v[$field]);
                }else {
                    $temp[$field] = $string[$v[$field]]?$string[$v[$field]]:'';
                }
            }
            $data[$k] = $temp;
        }

        $json['data'] = $data;
        $json['page'] = $results['page'];

        if($return){
            return $json;
        }

        echo json_encode($json);
        die;
    }

    //提现管理
    public function withdrawManagement(){
        $egt = I("post.egt",'');
        $elt = I("post.elt",'');

        $where = '';
        if($elt && $egt){
            $where[] = 'money >= '.$egt.' and money <= '.$elt;
        }else{
            $egt?$where['money'] = array('egt',$egt):'';
            $elt?$where['money'] = array('elt',$elt):'';
        }
        $json = $this->searchAll(1,$where);
        
        echo json_encode($json);
        die;
    }

    //提现完成
    public function withdrawComplete(){
        $json = $this->searchAll(1,array('state'=>3));

        echo json_encode($json);
        die;
    }

    //提现审核
    public function withdrawExamine(){
        $egt = I("post.egt",'');
        $elt = I("post.elt",'');
//        $page = I('post.page',1);
//        //数据分页配置
//        $pager = array('page'=>$page,'pageSize'=>20);
//        $json = $this->getAll('withdrawals','','id','','id asc',$pager);
//        $where = array('state'=>array('in',array(0,1)));
        $where = array('state'=>0);//现在修改为只要审核中的
        if($elt && $egt){
            $where[] = 'money >= '.$egt.' and money <= '.$elt;
        }else{
            $egt?$where['money'] = array('egt',$egt):'';
            $elt?$where['money'] = array('elt',$elt):'';
        }
        $json = $this->searchAll(1,$where);
        
        echo json_encode($json);
        die;
    }

    //付款确认
    public function withdrawConfirm(){
        $egt = I("post.egt",'');
        $elt = I("post.elt",'');
//        $page = I('post.page',1);
//        //数据分页配置
//        $pager = array('page'=>$page,'pageSize'=>20);
//        $json = $this->getAll('withdrawals','','id','','id asc',$pager);
//        $where = array('state'=>array('in',array(0,1)));
        $where = array('state'=>1);//现在修改为只要审核中的
        if($elt && $egt){
            $where[] = 'money >= '.$egt.' and money <= '.$elt;
        }else{
            $egt?$where['money'] = array('egt',$egt):'';
            $elt?$where['money'] = array('elt',$elt):'';
        }
        $json = $this->searchAll(1,$where);

        echo json_encode($json);
        die;
    }

    //付款确认
    public function withdrawBlackList(){
        $egt = I("post.egt",'');
        $elt = I("post.elt",'');
//        $page = I('post.page',1);
//        //数据分页配置
//        $pager = array('page'=>$page,'pageSize'=>20);
//        $json = $this->getAll('withdrawals','','id','','id asc',$pager);
//        $where = array('state'=>array('in',array(0,1)));
        $where = array('state'=>2);//现在修改为只要审核中的
        if($elt && $egt){
            $where[] = 'money >= '.$egt.' and money <= '.$elt;
        }else{
            $egt?$where['money'] = array('egt',$egt):'';
            $elt?$where['money'] = array('elt',$elt):'';
        }
        $json = $this->searchAll(1,$where);

        echo json_encode($json);
        die;
    }

    //推广设置
    public function promotionSettings(){
        $user_type = I('post.user_type','');
        if($user_type){
            $where = array('user_type'=>$user_type);
        }else{
            $where = array('user_type'=>array('in',array(1,2)));
        }

        $json = $this->searchAll(1,$where);

        echo json_encode($json);
        die;
    }
}