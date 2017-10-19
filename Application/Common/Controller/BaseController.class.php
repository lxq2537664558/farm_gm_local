<?php
namespace Common\Controller;
use Think\Controller;
use Home\Controller\OpenSSLController;
class BaseController extends Controller {
    public $carData = array();
    public $pageSize = 100;
    public $mobile = 0;
    public $userInfo = array();
    public $adminInfo = array();
    public $deviceType = 'PC';

//    //手机端判断
//    Public function _initialize(){
//        //移动设备浏览，则切换模板
//        if (ismobile()) {
//            //设置默认默认主题为 Mobile
//            C('DEFAULT_THEME','Mobile');
//            $this->mobile = 1;
//        }
//    }

    public function __construct(){
        parent::__construct();
        $this->get_device_type();
        //后台权限验证
        if((CONTROLLER_NAME != 'Public') && (MODULE_NAME != 'Agent')){
//            if(!(CONTROLLER_NAME == 'User' && ACTION_NAME == 'addUser')){
            $this->checkAdmin();
//            }
        }
        $this->behaviorRecords();
    }

    //公共加密方法-备后期修改加密算法之用
    public function publicEncrypt($params){
        $encrypt_type = C('ENCRYPT_TYPE');

        if($encrypt_type == 'encrypt') {
            $ssl = new OpenSSLController();
            $private_key = $ssl->getPrivateKey();
            $params = $ssl->encryptData($params, $private_key);
        }else {
            $params = base64_encode($params);
        }

        $params = str_replace('+','_',$params);
        return $params;
    }

    /*
     * 获取游戏名称列表
     * @param string $info = '*'|fields 字段信息
     * @return array || boolean
     */
    public function getGameNames($info = ''){
        switch ($info){
            case '' :$field = 'name';break;
            case '*' : $field = '*';break;
            default:$field = $info;break;
        }
        return $this->getAll('game_info','','id',$field);//获取游戏名称列表
    }

    /*
     * 获取设备类型
     */
    function get_device_type()
    {
        if (ismobile()) {//确认为手机端
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);//全部变成小写字母
            $type = 'other';

            //分别进行判断
            if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
                $type = 'ios';
            }

            if (strpos($agent, 'android')) {
                $type = 'android';
            }
            $this->deviceType = $type;
        }
        return ;
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串
    function check_verify($code){
        $verify_code = strtolower(session('captcha_code'));
        $flag = (strtolower($code) == $verify_code);
        return $flag;
    }

    //上传文件
    public function uploadFile($fileid='pic') {
        $savePath = $fileid.'/' . date('Y') . '/' . date('m').'/';
        $file_upload_path = C('UPLOAD_FILE_PATH').$savePath;
        if(!file_exists($file_upload_path)){
            mkdir($file_upload_path,0777,true);
        }

        $upload = new \Think\Upload();
        $upload->rootPath = C('UPLOAD_FILE_PATH');
        $upload->savePath = $savePath;
        $upload->autoSub = false;

        $info = $upload->upload();

        if (!$info) {// 上传错误提示错误信息
            return array('state' => 0, 'msg' => $upload->getError());
        } else {// 上传成功
            return array('state' => 1, 'msg' => '上传成功', 'data' => $info);
        }
    }

    //前台登录验证
    public function checkUser(){
        if($this->mobile) {
            $loginInfo = session('mobile_login');
        }else {
            $loginInfo = session('loginInfo');
        }

        $isLogin = $loginInfo['isLogin'];
        if(!$isLogin){
            session('loginInfo',NULL);//清理session
        }else{
            $this->userInfo = $loginInfo;
            $this->assign('userInfo',$loginInfo);
            return $loginInfo;
        }
    }

    //后台权限验证
    public function checkAdmin(){
//        var_dump($_GET);die;
        $qrcode_pass = I('get.qrcode_pass','');
        $code = I('get.extension_code','');

        $loginInfo = session('AdminInfo');
        $isLogin = $loginInfo['isAdmin']?$loginInfo['isAdmin']:$loginInfo['isAgent'];//管理员或者代理商
        if(!$isLogin){//未登录，跳转到登录页面
            session('AdminInfo',NULL);//清理session
            $this->redirect('Home/Public/adminLogin',array('extension_code'=>$code,'qrcode_pass'=>$qrcode_pass));
            die;
        }else{
            $this->sidebar($loginInfo['group']);//左侧导航菜单
            $this->adminInfo = $loginInfo;
            $this->assign('group',$loginInfo['group']);
        }
    }

    //左侧导航菜单
    public function sidebar($group){
        //根据group查询所有的node
        $nWhere['role_id'] = $group;
        $nodes = $this->getAll('role_node',$nWhere,'id','node_id');
        //根据nodes查询菜单
        $mWhere['is_navi'] = 1;
        $mWhere['id'] = array('in',array_values($nodes));
        $menu_temp = $this->getAll('node',$mWhere,'id');//全部子菜单
        foreach ($menu_temp as $key=>$val){
            $menu[$val['controller_name']][$key] = $val;
        }

        $main_menu = $this->getAll('node',$mWhere,'id','',array('g'=>'main_menu','o'=>'id'));//主菜单

        foreach ($main_menu as $k=>$v){
            $main_menu[$k]['js'] = "{target: '#collapse-nav".$k."'}";
        }
        $this->assign('menu',$menu);
        $this->assign('main_menu',$main_menu);

        //菜单
        $game_names = $this->getGameNames();//获取游戏类型列表
        $this->assign('game_names',$game_names);
    }











    /**
     * 统计字段
     */
    public function countField($data,$field,$val){
        $i = 0;
        foreach($data as $v){
            if($v[$field] == $val){
                $i++;
            }
        }
        return $i;
    }

    /**
     * 查询数据,数据分页方法
     */
    public function getAll($table,$where='',$pk='',$field='*',$order=array(),$page=0,$debug=0){
        $model = $this->getModel($table);
        if(!$model){
            return false;
        }
        $field = ($field == '')?'*':$field;

        $sort = 0;
        if($pk == ''){
            $pk = $model->getPk();
        }else{
            $sort = 1;
        }

        $allField = $this->addPkToField($field,$pk);

        if($page){
            $pager = $this->getPage($model,$page,$where,$allField,$order,$debug);
            return $pager;
        }

        if($order){
            if(is_array($order)){
                $method_letter = array_keys($order);
                $temp = $model->field($allField)->where($where);

                if(in_array('g',$method_letter) || in_array('group',$method_letter)){
                    $group_condition = $order['g']?$order['g']:$order['group'];
                    $temp = $temp->group($group_condition);
                }

                if(in_array('h',$method_letter) || in_array('having',$method_letter)){
                    $having_condition = $order['h']?$order['h']:$order['having'];
                    $temp = $temp->having($having_condition);
                }

                if(in_array('o',$method_letter) || in_array('order',$method_letter)){
                    $order_condition = $order['o']?$order['o']:$order['order'];
                    $temp = $temp->order($order_condition);
                }

                if(in_array('l',$method_letter) || in_array('limit',$method_letter)){
                    $limit_condition = $order['l']?$order['l']:$order['limit'];
                    $temp = $temp->limit($limit_condition);
                }
                $res = $temp->select();
            }else {
                $res = $model->field($allField)->where($where)->order($order)->select();
            }
        }else{
            $res = $model->field($allField)->where($where)->select();
        }

        if($debug){
            $this->debug($model);
        }

        if($sort){
            $data = $this->sortInfoById($res,$pk,$field);
        }else{
            $data = $res;
        }
        return $data;
    }

    public function debug($model){
        echo $model->_sql().'<br/>';
        $DbError = $model->getDbError();
        if($DbError) {
            var_dump($DbError);
        }
    }

    public function sortInfoById($res,$pk,$field=''){
        $field = $field?$field:'*';

        foreach($res as $k=>$v){
            if(!$pk) {
                $info[$k] = $v[$field];
            }else {
                if ($v[$pk]) {
                    if ($field == 'no') {
                        $info[$v[$pk]] = $v[$pk];
                    } elseif (strpos($field, ',') === false && $field != '*') {
                        $info[$v[$pk]] = $v[$field];
                    } else {
                        $info[$v[$pk]] = $v;
                    }
                }
            }
        }
        return $info;
    }

    public function addPkToField($field,$pk){
        $field = $field?$field:'*';
        if($field == '*') {
            $allField = '*';
        }else{
            if($pk) {
                $str = $field.','.$pk;
                $allField = explode(',',$str);
                $allField = array_unique($allField);
            }else {
                $allField = $field;
            }
            if(false !== strpos($allField,',')) {
                $allField = join(',', $allField);
            }
        }

        return $allField;
    }

    /**
     * 分页方法
     */
    public function getPage($model,$page,$where,$field,$order='',$debug,$db=array()){
        $InPageSize = $this->pageSize;
        $configSize = $InPageSize?$InPageSize:C('PAGESIZE');
        $pageSize = $configSize?$configSize:20;
        if(is_array($page)){
            $pageSize = $page['pageSize'];
            $page = $page['page'];
        }

        if(is_object($model)) {
            if($db){
                if ($order) {
                    $data = $model->db($db['id'],$db['connect'])->where($where)->field($field)->order($order)->limit($pageSize)->page($page)->select();
                } else {
                    $data = $model->db($db['id'],$db['connect'])->where($where)->field($field)->limit($pageSize)->page($page)->select();
                }
            }else {
                if ($order) {
                    $data = $model->where($where)->field($field)->order($order)->limit($pageSize)->page($page)->select();
                } else {
                    $data = $model->where($where)->field($field)->limit($pageSize)->page($page)->select();
                }
            }
            $count = $model->where($where)->count();

        }else{
            return false;
        }

        if($debug){
            $this->debug($model);
        }

        $basicPageInfo = array(
            'totalPage' => ceil($count/$pageSize),
            'page'		=> $page,
            'count'     => intval($count),
        );
        $allPageInfo = $this->pageInfo($basicPageInfo,$pageSize);
        $pager = array(
            'page'=>$allPageInfo,
            'data'		=> $data
        );

        return $pager;
    }

    public function cutPage($data,$page,$pageSize){
        $count = count($data);
        $data = array_values($data);
        $start = $pageSize*($page-1);
        $end = $start+$pageSize-1;
        for ($i = $start;$i<=$end;$i++){
            $returnData[] = $data[$i];
        }

        $pager = array(
            'page'=>array(
                'totalPage' => ceil($count/$pageSize),//总页数
                'page'		=> $page,//当前页
                'count'     => intval($count),//总记录数
            ),
            'data'		=> $returnData,//数据
        );
        return $pager;
    }

    /**
     * 分页组件
     */
    public function pageInfo($pageInfo,$pageSize,$pageshow=3){
        $pageInfo['back'] = (($pageInfo['page']-1)<=1)?1:$pageInfo['page']-1;
        $pageInfo['next'] = (($pageInfo['page']+1)>=$pageInfo['totalPage'])?$pageInfo['totalPage']:$pageInfo['page']+1;

        $pageInfo['start'] = (($pageInfo['page']-$pageshow)<=0)?1:$pageInfo['page']-$pageshow;
        $pageInfo['end'] = (($pageInfo['page']+$pageshow)>=$pageInfo['totalPage'])?$pageInfo['totalPage']:$pageInfo['page']+$pageshow;
        $pageInfo['pageSize'] = $pageSize?$pageSize:$this->pageSize;
        return $pageInfo;
    }

    /**
     * 单字段，单条件验证,数组中非空写在重复之前，否则可能验证不到
     */
    public function validateField($vMap){
        foreach ($vMap as $v){
            switch ($v[0]){
                case 'blank':
                    if (empty($v[1])) {
                        $msg = $v[2] ? $v[2] : '不能为空！';
                        return array('msg'=>$msg);
                    }
                    break;
                case 'repeat':
                    $repeat = $this->getAll($v[1],$v[2],'','','',0);
                    if($repeat){
                        $msg = $v[3]?$v[3]:'重复的名称！';
                        return array('msg'=>$msg);
                    }
                    break;
                case 'type':
                    switch ($v[2]){
                        case 'number':
                            if(!is_numeric($v[1])){
                                $msg = $v[3]?$v[3]:'错误的数据类型！';
                                return array('msg'=>$msg);
                            }
                            break;
                    }
                    break;
                default :
                    $res = $this->getAll($v[1],$v[2],$v[3]);
                    if(!$res){
                        $msg = $v[4]?$v[4]:'非法的数据！';
                        return array('msg'=>$msg);
                    }
                    break;
            }
        }
        return array('state'=>1);
    }

    /**
     * 获得model
     */
    public function getModel($table){
        $configName = C('simpleNameToModel');
        foreach ($configName as $k=>$v){
            foreach ($v as $val){
                $modelName[$val] = $k;
            }
        }
        switch ($table){
            case is_object($table):
                $model = $table;
                break;
            case array_key_exists($table,$modelName):
                $name = lcfirst($modelName[$table]);
                $model = D($name);
                break;
            default :
                $model = D($table);
                break;
        }

        if(!$model){
            return false;
        }
        return $model;
    }

    /**
     * 批量添加
     */
    public function addAllData($table,$where,$data,$replace=false){
        $model = $this->getModel($table);
        $res = $model->addAll($data,$where,$replace);
        return $res;
    }

    /**
     * 添加/编辑操作
     */
    public function insAndUpdate($table,$where,$data,$msg='',$debug=0){
        if(!$data){
            return array('msg'=>'没有要修改的数据','state'=>2);
        }

        $model = $this->getModel($table);
        if($where){
            $res = $model->where($where)->save($data);
            $str = $msg?$msg:'修改';
            $return['msg'] = $res ?$str.'成功！' :$str.'失败!';
            $return['state'] = $res ?1 :0;
        }else{
            $model->add($data);
            $lastId = $model->getLastInsID();
            $str = $msg?$msg:'添加';
            $return['msg'] = $lastId?$str.'成功！':$str.'失败!';
            $return['state'] = $lastId?1 :0;
            $return['id'] = $lastId;
        }

        if($debug) {
            return $model->_sql();
        }
        return $return;
    }

    /**
     * 删除数据
     */
    public function deleteInfo($table,$where){
        $model = $this->getModel($table);
        if($where){
            $res = $model->where($where)->delete();
            $return['msg'] = $res ?'删除成功！':'删除失败!';
            $return['state'] = $res ?1 :0;
        }else{
            return false;
        }
        return $return;
    }

    //生成订单号
    public function getOrderNumber(){
        $orderNmuber = (int)date('Ymd',time())*10;
        $orderNmuber = 'ny'.($orderNmuber).substr(microtime(),3,7);
        return $orderNmuber;
    }

    //生成验证码
    public function getVerify(){
        $code = strtoupper(substr(md5(rand()),0,4));
        session('captcha_code',$code);
        return $code;
    }

    /**
     * 下载excel 公共方法
     * @param unknown $excel_name
     * @param unknown $title
     * @param array $data
     */
    function downLoadExcel($excel_name, $title, $data = array()) {
        $sheetTitle = $excel_name;
        $downExcelService = new DownloadExcelFileController();
        $objPHPExcel = $downExcelService->createExcelFile($sheetTitle, $title, $data);
        $xlsTitle = $excel_name;//.date('ymdHis');

        $xlsTitle = str_replace(" ", "", $xlsTitle);
        $encode = urlencode($xlsTitle);
        $encode = iconv('UTF-8', 'GBK//IGNORE', $encode);
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.0") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 7.0") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.0")){
            header("Content-Disposition:attachment;filename*=utf8''$encode.xls");
        }else {
            header("Content-Disposition:attachment;filename=$xlsTitle.xls"); //attachment新窗口打印inline本窗口打印
        }
        //        header("Content-Disposition:attachment;filename=$xlsTitle.xls"); //attachment新窗口打印inline本窗口打印
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel, 'Excel2005');
        $objWriter->save('php://output');
    }















    //后台记录用户行为方法
    public function behaviorRecords(){
        $loginInfo = session('AdminInfo');
        if($loginInfo) {
            //有登录信息才处理数据
            //POST数据
            $post_data = I('post.');
            //GET数据
            $get_data = I('get.');
            $record_data = '';
            if($post_data) {
                $record_data .= 'POST数据:';
                foreach ($post_data as $pk => $pv) {
                    $record_data .= '"'.$pk.'"=>"'.$pv.'"';
                }
                $record_data .= '--------';
            }

            if($get_data) {
                $record_data .= 'GET数据:';
                foreach ($get_data as $gk => $gv) {
                    $record_data .= '"'.$gk.'"=>"'.$gv.'"';
                }
                $record_data .= '--------';
            }

            $time = time();
            $data = array(
                'uid' => $loginInfo['uid'],
                'module_name' => MODULE_NAME,
                'controller_name' => CONTROLLER_NAME,
                'action_name' => ACTION_NAME,
                'time' => $time,
                'month' => date('m'),
                'day' => date('d'),
                'data' => $record_data,
                'ip'=>$_SERVER["REMOTE_ADDR"],
            );

            $file_data = '用户'.$loginInfo['username'].'['.$loginInfo['uid'].',IP:'.$_SERVER["REMOTE_ADDR"].']于'.date('Y-m-d H:i:s',$time);
            if($post_data){
                $file_data .= '处理了数据：'.$record_data.'\n';
            }else{
                $file_data .= '访问了页面'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.PHP_EOL;
            }

            //写文件
            $filename = 'user_behavior_'.date('Ymd').'.txt';//以日期命名
            file_put_contents($filename,$file_data,FILE_APPEND);

            //插入数据
            D('user_behavior')->add($data);
        }
    }
}