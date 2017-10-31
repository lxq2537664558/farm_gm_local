<?php
namespace Agent\Controller;
use Think\Controller;
use Home\Controller\OpenSSLController;
use Common\Controller\BaseController;
class UserController extends BaseController {
    public function __construct()
    {
        parent::__construct();
        if(ACTION_NAME != 'login') {
            $this->checkAgent();
        }
    }

    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
//        var_dump($data);
        $return = json_decode($data,true);
        return $return;
    }

    //检测登录
    public function checkAgent(){
        $loginInfo = session('AdminInfo');
        if(!$loginInfo['isAgent']){
            session('AdminInfo',NULL);
            $this->redirect(U('Agent/User/login'));
            die;
        }
    }

    //代理商登录入口
    public function login(){
        $post = I('post.');
        if($post){
            $username = $post['username'];
            $password = $post['password'];
            //帐号或手机
            $where1['account'] = $username;
            $where1['phone'] = $username;
            $where1['_logic'] = 'or';

            //密码
            $where2['password'] = base64_encode($password);
            $where2['user_type'] = array('in',array(1,2));

            //条件逻辑
            $where_main['_complex'] = array(
                $where1, $where2,
                '_logic' => 'and'
            );

            if(!$username || !$password){
                $this->error('用户名或密码不能为空！');
                die;
            }

//            $user_exists = $this->getAll('user',$where_main,'','','',0,1);
            $user_exists = $this->getAll('user',$where_main);
//            die;
            if(!$user_exists){
                $this->error('用户名或密码错误！');
                die;
            }

            $user_info = current($user_exists);

            //写入SESSION
            $loginInfo = array(
                'isAgent' => 1,
                'uid'=>$user_info['id'],
                'username'=>$user_info['username'],
                'group'=>$user_info['group'],
            );
            session('AdminInfo',$loginInfo);

            $this->success('登录成功！',U('Agent/User/index'));
        }else{
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            $this->display();
        }
    }

    //代理商退出方法
    public function logout(){
        session('AdminInfo',NULL);
        $this->redirect(U('Agent/User/login'));
        die;
    }

    //用户列表
    public function index(){
        $post = I('post.');

        if($post) {
            $search = I('post.search','','trim');
            $table = I('post.table');
            $page = I('post.page', 1);
            $pageSize = 20;
            $pager = array('page' => $page, 'pageSize' => $pageSize);

            //当前UID
            $user_info = session('AdminInfo');
            $uid = $user_info['uid'];


            //根据推广列表的数据来筛选需要显示的UID
            $url = 'http://' . C('SERVER_IP') . '/GetGeneralizeList';

            $start = ($page - 1) * $pageSize;
            $end = $pageSize;
//            $params = 'index=' . $start . '&num=' . $end . '&showId=' . $uid;
            $params = 'showId=' . $uid;

            $params = $this->publicEncrypt($params);
            $url .= '?data=' . $params;

            $lists3 = $this->getHTTPData($url);
            $promotion_list_users = $lists3['users'];
            $promotion_uids = $this->sortInfoById($promotion_list_users,'id','id');
            $promotion_uids = array_values($promotion_uids);


            //查下级单位
            $where1['father_id'] = $uid;
//            $where1['login_time'] = array('gt',0);
            $where1['_logic'] = 'and';
            $where1['id'] = array('in',$promotion_uids);//根据推广列表的数据来筛选需要显示的UID

            //模糊搜索
            if($search){
                $where['id'] = array('like','%'.$search.'%');
                $where['phone'] = array('like','%'.$search.'%');
                $where['idcard'] = array('like','%'.$search.'%');
                $where['_logic'] = 'or';

                //条件逻辑
                $where_main['_complex'] = array(
                    $where1, $where,
                    '_logic' => 'and'
                );
            }else{
                $where_main = $where1;
            }

            $users = $this->getAll('user',$where_main, 'id', '', '', $pager);

            $user_ids = $this->sortInfoById($users['data'],'id','id');

//            $user_ids[$uid] = $uid;
//            $user_ids = array_keys($users['data']);
            $uid_string = join('_', $user_ids);

            $url = 'http://'.C('SERVER_IP').'/GetUserData';

            $params = 'showIds=' . $uid_string . '&type=0';
//            var_dump($params);
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);

//            var_dump($url);
//            var_dump($lists);
//            die;
            //序列化接口请求的数据
            $http_user_data = $this->sortInfoById($lists['users'],'showId');
//            var_dump($http_user_data);die;

            $state_array = array('正常','封号','永久封号');

            //组装数据
            foreach ($users['data'] as $k=>$v){
                $users['data'][$k]['gold'] = $http_user_data[$v['id']]['gold']?$http_user_data[$v['id']]['gold']:0;//金币
//                $users['data'][$k]['father_id'] = $http_user_data[$v['id']]['generalizeId1']?$http_user_data[$v['id']]['generalizeId1']:0;//一级推广id
                $users['data'][$k]['father_id'] = $v['father_id']?$v['father_id']:0;//改为自表查询，不再显示接口的数据
//                $users['data'][$k]['grandfather_id'] = $http_user_data[$v['id']]['generalizeId2']?$http_user_data[$v['id']]['generalizeId2']:0;//二级推广id
                $users['data'][$k]['login_time'] = $http_user_data[$v['id']]['lastLoginTime']?$http_user_data[$v['id']]['lastLoginTime']:0;//上次登陆时间
                $users['data'][$k]['mid'] = $http_user_data[$v['id']]['id']?$http_user_data[$v['id']]['id']:0;//mongodb的id
//                $users['data'][$k]['idcard'] = $http_user_data[$v['id']]['idcard']?$http_user_data[$v['id']]['idcard']:'';//身份证
                $users['data'][$k]['idcard'] = $v['idcard']?$v['idcard']:'';//身份证

                $users['data'][$k]['username'] = $v['username']?$v['username']:'';//用户名
                $users['data'][$k]['phone'] = $v['phone']?$v['phone']:'';//手机
//                var_dump($users['data'][$k]['state'],$v['state']);
//                $users['data'][$k]['gold'] = $http_user_data[$v['id']]['gold']?$http_user_data[$v['id']]['gold']:0;//注册时间
            }

            //字段翻译
            $translate_fields = C('TRANSLATE_FIELDS');
            $translate = $translate_fields[$table];

            //处理状态数据
            foreach ($users['data'] as $k=>$v){
                $temp = $v;
                foreach ($translate as $field=>$string){
                    if($string == 'to_time'){
                        $temp[$field] = date('Y-m-d',$v[$field]);
                    }else {
                        $temp[$field] = $string[$v[$field]];
                    }
                }
                $data[$k] = $temp;
            }
            $users['data'] = $data;

            echo json_encode($users);die;
        }

        $this->display();
    }


//    //用户列表
//    public function index(){
////        $this->redirect(U('Agent/User/promotionList'));//现在又改需求不需要跳转了。。。。。。
//    }

    //获取推广码
    public function getExtensionCode($post){
        //当用户被选择到了代理商或者商会长后后台就自动为此用户生产一个推广码
        //推广码为5位数字，用户与用户之间没有相同的5位数字
        $code = 0;

        if($post['user_type']){//1和2
            $max_code = $this->getAll('user','','','max(extension_code)');
            $max_code = current($max_code);
            $code =$max_code['max(extension_code)'];
            if(!$code){
                $code = 10001;
            }else{
                $code += 1;
            }
        }
        return $code;
    }



//    //渠道管理
//    public function channelManagement(){
//        $post = I('post.');
//        if($post){
//            $page = I('post.page',1);
//            $search = I('post.search','');
//
//            $pager = array('page' => $page, 'pageSize' => 20);
//
//            //模糊查询
//            if($search) {
//                $search_fields = array();//检索字段
//                foreach ($search_fields as $v) {
//                    $where[$v] = array('like', '%' . $search . '%');
//                }
//                $where['_logic'] = 'or';
//                $where2['user_type'] = array('in',array(1,2));
//                $where_main['_complex'] = array(
//                    $where, $where2,
//                    '_logic' => 'and'
//                );
//            }else{
//                $where_main['user_type'] = array('in',array(1,2));
//            }
//
//            //用户分页列表
//            $users = $this->getAll('user',$where_main, '', '', '', $pager);
//            $user_ids = $this->sortInfoById($users['data'],'id','id');
//            $ids_string = join('_',array_values($user_ids));
//
//            $url = 'http://'.C('SERVER_IP').'/GetUserGameData';
//
//            $params = 'showIds='.$ids_string;
//            $params = $this->publicEncrypt($params);
//            $url .= '?data='.$params;
//
//            $lists = $this->getHTTPData($url);
//            $http_data = $lists['data'];
//
//            $sort_data = $this->sortInfoById($http_data,'showId');
//
//            //用户登录时间数据
//            $url2 = 'http://'.C('SERVER_IP').'/GetUserData';
//
//            $params = 'showIds=' . $ids_string . '&type=0';
//            $params = $this->publicEncrypt($params);
//            $url2 .= '?data='.$params;
//
//            $lists2 = $this->getHTTPData($url2);
//            $sort_data2 = $this->sortInfoById($lists2['users'],'showId');
//
//            $user_state = array('正常','封号','永久封号');
//            $user_type = array('普通用户','代理商','商会长');
//
//            //下属玩家统计
//            $sql = 'SELECT p.id,count(s.username) AS children_count FROM user AS p LEFT JOIN user AS s ON s.father_id = p.id GROUP BY p.username ORDER BY p.id';
//            $counts = D('user')->query($sql);
//            foreach ($counts as $c){
//                $count_data[$c['id']] = $c['children_count'];
//            }
//
//            foreach ($users['data'] as $k=>$v){
//                $combine[$k] = array(
//                    'uid'=>$v['id'],
//                    'username'=>$v['username'],
//                    'state'=>$user_state[$v['state']],
//                    'user_type'=>$user_type[$v['user_type']],
//                    'diamond'=>$sort_data[$v['id']]['diamond'],
//                    'player_num'=>$count_data[$v['id']],
//                    'login_time'=>$sort_data2[$v['id']]['login_time']?date('Y-m-d H:i:s',$sort_data2[$v['id']]['login_time']):'-',
//                    'register_time'=>$v['register_time']?date('Y-m-d H:i:s',$v['register_time']):'-',
//                    'mid' => $sort_data2[$v['id']]['id'],//mongodb的id
//                );
//
//                foreach ($combine[$k] as $key=>$val){
//                    if(!$val){
//                        $combine[$k][$key] = 0;
//                    }
//                }
//            }
//
//            $json = array('data'=>$combine,'page'=>$users['page']);
//            echo json_encode($json);
//            die;
//        }
//        $this->display();
//    }
//
//    //挂单记录
//    public function pendingOrder(){
//        $post = I('post.');
//        if($post) {
//            $page = I('post.page', 1);
//            $pageSize = 20;
//            $order_type = array('买','卖');//定义挂单类型
//            $order_state = array('挂单','完成','撤销');//定义挂单类型
////            var_dump($post);
//            $mid = I('post.mid');//mongodb的用户数据id
//            $uid = I('post.uid');
//            $where['id'] = $uid;
//            $start = $pageSize * ($page - 1);
//
//            //获取该用户的信息
//            $userInfo = $this->getAll('user',$where);
//            $userInfo = current($userInfo);
//
//            //请求接口数据
////            $mid = '59968489da19462530deacf8';//测试固定数据
//            $url = 'http://'.C('SERVER_IP').'/GetTradeRecord';
//
//            $params = 'index='. $start .'&num=' . $pageSize  . '&owner=' . $mid . '&sort=price&type=2';
//            $params = $this->publicEncrypt($params);
//            $url .= '?data='.$params;
//
//
//            $lists = $this->getHTTPData($url);
////            var_dump($url,$lists);die;
//
//            //组装数据
//            foreach ($lists['trades'] as $v){
//                $data[] = array(
//                    'id'=>$uid,
//                    'product_id'=>$v['itemId'],
//                    'product_name'=>$v['itemName'],
//                    'order_type'=>$order_type[$v['type']],
//                    'order_num'=>$v['entrustNum'],
//                    'price'=>$v['price'],
//                    'order_time'=>$v['startTime'],
//                    'order_state'=>$order_state[$v['state']],
//                    'end_time'=>$v['tradeTime'],
//                    'sell_num'=>array_sum($v['tradeNum']),
//                    'trade_uid'=>join(',',$v['tradeUserId']),
//                );
//            }
//            $json = array('data'=>$data,'page'=>array('page'=>$page,'totalPage'=>ceil($lists['totalNum']/$pageSize)));
//            echo json_encode($json);
//            die;
//        }
//
//        $this->display();
//    }
//
    //金币记录
    public function goldRecords(){
        $post = I('post.');
        if($post) {
            $page = I('post.page',1);
            $pager = array('page'=>$page,'pageSize'=>20);
            $action_type = array('提现','兑换钻石','GM');//行为定义
            $uid = I('post.uid');
            $where['uid'] = $uid;
            $records = $this->getAll('gold_record',$where,'','','',$pager);//记录信息

            $user_info = $this->getAll('user',array('id'=>$uid),'id','username');//用户信息
            $username = current($user_info);

            foreach ($records['data'] as $k=>$v){
                $records['data'][$k]['username'] = $username;
                $records['data'][$k]['type'] = $action_type[$v['type']];
                $records['data'][$k]['time'] = date('Y-m-d',$v['time']);
            }

            $json = array('data'=>$records['data'],'page'=>$records['page']);
            echo json_encode($json);
            die;
        }

        $this->display();
    }
//
//    //物品清单
//    public function itemsList(){
//        $post = I('post.');
//        if($post) {
//            $uid = I('post.uid');
//            $url = 'http://'.C('SERVER_IP').'/GetItemList';
//
//            $params = 'showId='.$uid;
//            $params = $this->publicEncrypt($params);
//            $url .= '?data='.$params;
//
//
//            $lists = $this->getHTTPData($url);
//            $items = $lists['items'];
////            var_dump($items);die;
//            $json = array('data'=>$items,'page'=>array());
//            echo json_encode($json);
//            die;
//        }
//        $this->display();
//    }

    //推广列表
    public function promotionList(){
        $post = I('post.');
        if($post){
            set_time_limit(0);
            
            //SESSION的用户数据
            $user_info = session('AdminInfo');
            $uid = $user_info['uid'];

            
            
            
            
            
            
            
            
            
            
            
            //分页配置
            $page = I('post.page',1);
            $pageSize = 10;

            //搜索的筛选条件
            $start_year = I('post.start_year');
            $start_month = I('post.start_month');
            $end_year = I('post.end_year');
            $end_month = I('post.end_month');
            $start_day = I('post.start_day');
            $end_day = I('post.end_day');

            //周的处理
            $week = I('post.week');//周的序号
            $first_week_start_timestamp = strtotime('2017-10-9');//第一周开始时间
            $first_week_end_timestamp = strtotime('2017-10-15 23:59');//第一周结束时间

            $search_data = 1;//是否启用本地数据
            //如果有周，以周为准
            if($week == 1){//只有第一周使用新接口
                //查询该用户该周是否有本地数据
                $uWhere['father_id'] = $uid;
                $uWhere['week'] = $week;
                $res = $this->getAll('user_promotion_list',$uWhere);
                $lists['users'] = $res;

                if(!$lists['users']) {
                    $search_data = 0;
                    //使用新接口查以前的数据
//                    $start = ($page - 1) * $pageSize;
//                    $end = $pageSize;
//                    $params = 'index=' . $start . '&num=' . $end . '&showId=' . $uid;
                    $params = 'showId=' . $uid;
                    $url = 'http://' . C('SERVER_IP') . '/GetOldGeneralizeList';
                    $params = $this->publicEncrypt($params);
                    $url .= '?data=' . $params;
                    $lists = $this->getHTTPData($url);
                }
            }else{
                //查询该用户该周是否有本地数据
                $uWhere['father_id'] = $uid;
                $uWhere['week'] = $week;
                $res = $this->getAll('user_promotion_list',$uWhere);
                $lists['users'] = $res;

                if(!$lists['users']) {
                    $search_data = 0;
                    //其他全部走老接口
                    if ($week) {//周的时间处理
                        $search_week = $week - 1;//使用临时变量，week用于后面，不能参加计算
                        $start_time = strtotime('+' . $search_week . ' week', $first_week_start_timestamp);//开始时间
                        $end_time = strtotime('+' . $search_week . ' week', $first_week_end_timestamp);//结束时间
                    } else {
                        //否则处理日期
                        if ($start_year) {//有日期处理日期，否则查询全部
                            //判断参数是否完整
                            $time_params = array('start_year', 'start_month', 'start_day', 'end_year', 'end_month', 'end_day');
                            foreach ($time_params as $v) {
                                if (!$$v) {
                                    echo json_encode(array('msg' => '参数不完整'));
                                    die;
                                }
                            }
                            $start_time = strtotime($start_year . $start_month . $start_day);//开始时间
                            $end_time = strtotime($end_year . $end_month . $end_day . ' 23:59');//结束时间
                        }
                    }
                    $url = 'http://' . C('SERVER_IP') . '/GetGeneralizeList';
//                    $start = ($page - 1) * $pageSize;
//                    $end = $pageSize;
//                    $params = 'index=' . $start . '&num=' . $end . '&showId=' . $uid;//分页信息
                    $params = 'showId=' . $uid;//分页信息
                    $start_time ? $params .= '&startTime=' . $start_time . '&endTime=' . $end_time : '';//时间信息，如果有才添加参数
                    $params = $this->publicEncrypt($params);//处理参数
                    $url .= '?data=' . $params;
                    $lists = $this->getHTTPData($url);
                }
            }

            //处理接口数据
            $data = $lists['users'];
//            var_dump($data);
            foreach ($data as $v){
                $ids[] = $v['id'];
            }
            $ids[] = $uid;//获取所有的UID
            $where['id'] = array('in',$ids);
            $user_info = $this->getAll('user',$where,'id');//根据UID获取本地用户的基本信息

            //用户等级
            $user_level = 1;//目前没有分，默认为1
            $user_level_string = array(0,'普通代理商','中级代理商','高级代理商');
            $cost_commission_point2 = array(0,0.08,0.09,0.1);//消费佣金比例
            $service_commission_point2 = array(0,0.8,0.9,1);//手续费佣金比例----手续费暂时不分，为1的情况为0.8

            //数据组装计算
            $users = array();
            $recharge = $serviceCharge = $cost = $commission = $commission1 = $commission2 = $cost_money = $service = 0;
            foreach($data as $i=>$v) {
                //初始化数据，没有则为0，便于计算
                $temp_recharge = $data[$i]['recharge'] ? $data[$i]['recharge'] : 0;//充值总额
                $temp_cost = $data[$i]['cost'] ? $data[$i]['cost'] : 0;//消费总额
                $temp_serviceCharge = $data[$i]['serviceCharge'] ? $data[$i]['serviceCharge'] : 0;//手续费
                //佣金的计算根据不同的查询条件不同
                if ($week == 1) {
                    $temp_cost_money = $temp_cost * 0.06;//消费佣金
                    $temp_service_money = $temp_serviceCharge * 0.8;//手续费佣金
                } else {
                    $temp_cost_money = $temp_cost * $cost_commission_point2[$user_level];//消费佣金
                    $temp_service_money = $temp_serviceCharge * $service_commission_point2[$user_level];//手续费佣金
                }

                if($search_data == 1){//启用本地数据的数据组装
                    $users[$v['id']] = array(
                        'uid'=>($search_data == 1)?$v['uid']:$v['id'],
                        'username'=>$v['phone'],
                        'register_time'=>date('Y-m-d H:i:s',$user_info[$v['id']]['register_time']),
                        'recharge'=>$temp_recharge,//充值总额
                        'cost'=>$temp_cost,//消费总额
                        'cost_commission'=>$temp_cost_money,//消费佣金
                        'serviceCharge'=>$temp_serviceCharge,//手续费
                        'service_commission'=>$temp_service_money,//手续费佣金
                        'commission'=>$temp_cost_money+$temp_service_money,//佣金总额
                    );
                }else{
                    $users[$v['id']] = array(
                        'uid'=>($search_data == 1)?$v['uid']:$v['id'],
                        'username'=>$user_info[$v['id']]['phone'],
                        'register_time'=>date('Y-m-d H:i:s',$user_info[$v['id']]['register_time']),
                        'recharge'=>$temp_recharge,//充值总额
                        'cost'=>$temp_cost,//消费总额
                        'cost_commission'=>$temp_cost_money,//消费佣金
                        'serviceCharge'=>$temp_serviceCharge,//手续费
                        'service_commission'=>$temp_service_money,//手续费佣金
                        'commission'=>$temp_cost_money+$temp_service_money,//佣金总额
                    );
                }

                //底部数据总计
                $recharge += $temp_recharge;//充值总额
                $cost += $temp_cost;//消费总额
                $cost_money += $temp_cost_money;//消费佣金
                $serviceCharge += $temp_serviceCharge;//手续费
                $service += $temp_service_money;//手续费佣金
                $commission += ($temp_cost_money+$temp_service_money);//佣金总额
            }

            $total_num = count($users);
            //以ID排序
            ksort($users);
            $users = array_values($users);

            //底部数据总计
            $users[] = array(
                'uid'=>'总计：',
                'username'=>$total_num,
                'register_time'=>'-',
                'recharge'=>$recharge,//充值总额
                'cost'=>$cost,//消费总额
                'cost_commission'=>$cost_money,//消费佣金
                'serviceCharge'=>$serviceCharge,//手续费
                'service_commission'=>$service,//手续费佣金
                'commission'=>$commission?$commission:0,//佣金总额
            );

//            $json = array('data'=>$users,'page'=>array('page'=>$page,'totalPage'=>ceil($lists['totalNum']/$pageSize)),'level'=>$user_level_string[$user_level]);
            $json = array('data'=>$users,'level'=>$user_level_string[$user_level]);
            echo json_encode($json);
            die;
        }
        $this->display();
    }



//    //获取用户游戏数据
//    public function gameData(){
//        $post = I('post.');
//        if($post){
//            $page = I('post.page',1);
//            $pager = array('page' => $page, 'pageSize' => 20);
//
//            //当前UID
//            $user_info = session('AdminInfo');
//            $uid = $user_info['uid'];
//
//            //查下级单位
//            $where1['id'] = $uid;//保留当前用户数据
//            $where1['father_id'] = $uid;
//            $where1['grandfather_id'] = $uid;
//            $where1['_logic'] = 'or';
//
//            //用户分页列表
//            $users = $this->getAll('user',$where1, '', '', '', $pager);
//
//            $user_ids = $this->sortInfoById($users['data'],'id','id');
//            $ids_string = join('_',array_values($user_ids));
//
//            $url = 'http://'.C('SERVER_IP').'/GetUserGameData';
//
//            $params = 'showIds='.$ids_string;
//            $params = $this->publicEncrypt($params);
//            $url .= '?data='.$params;
//
////            $url = 'http://'.C('SERVER_IP').'/GetUserGameData?showIds=64_67';//测试数据
//            $lists = $this->getHTTPData($url);
//            $http_data = $lists['data'];
////            var_dump($http_data);die;
//            $sort_data = $this->sortInfoById($http_data,'showId');
//
//            foreach ($users['data'] as $k=>$v){
//                $combine[$k] = array(
//                    'uid'=>$v['id'],
//                    'username'=>$sort_data[$v['id']]['userName'],
//                    'diamond'=>floor($sort_data[$v['id']]['diamond']),
//                    'treasure'=>$sort_data[$v['id']]['treasure'],
//                    'recharge'=>$sort_data[$v['id']]['recharge'],//充值总额
//                    'cost'=>$sort_data[$v['id']]['cost'],//下线总额cost
//                    'difference'=>$sort_data[$v['id']]['recharge']-$sort_data[$v['id']]['cost'],//上下差值
//                    'depotLevel'=>$sort_data[$v['id']]['depotLevel'],
//                    'stealTotalValue'=>$sort_data[$v['id']]['stealTotalValue'],
//                    'beStolenTotalValue'=>$sort_data[$v['id']]['beStolenTotalValue'],
//                    'steal_difference'=>$sort_data[$v['id']]['stealTotalValue']-$sort_data[$v['id']]['beStolenTotalValue'],//偷取差值
//                    'dogNum'=>$sort_data[$v['id']]['dogNum'],
//                    'dogFoodNum'=>$sort_data[$v['id']]['dogFoodNum'],
//                    'speedUpItemNum'=>$sort_data[$v['id']]['speedUpItemNum'],
//                    'consecrateNum'=>$sort_data[$v['id']]['consecrateNum'],
//                    'farms'=>join(',',$sort_data[$v['id']]['farms']),
//                    'fishs'=>join(',',$sort_data[$v['id']]['fishs']),
//                    'forests'=>join(',',$sort_data[$v['id']]['forests']),
//                    'mines'=>join(',',$sort_data[$v['id']]['mines']),
//                );
//
//                foreach ($combine[$k] as $key=>$val){
//                    if(!$val){
//                        $combine[$k][$key] = 0;
//                    }
//                }
//            }
////            var_dump($http_data,$sort_data);die;
//
//            $json = array('data'=>$combine,'page'=>$users['page']);
//            echo json_encode($json);
//            die;
//        }
//        $this->display();
//    }
//
//    //删除用户
//    public function deleteUser(){
//        $uid = I('get.uid',0);
//        if(!$uid){
//            $this->error('删除失败，参数不完整！');
//            die;
//        }
//
//        $where['id'] = $uid;
//        $res = $this->deleteInfo('user',$where);
//        if($res['state']){
//            $this->redirect(U('Home/User/index'));
//            die;
//        }else{
//            $this->error('删除失败！');
//            die;
//        }
//    }
}