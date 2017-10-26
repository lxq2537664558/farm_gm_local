<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class UserController extends BaseController {
    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
        $return = json_decode($data,true);
        return $return;
    }

    //用户列表
    public function index(){
        $post = I('post.');

        if($post) {
            $search = I('post.search','','trim');
            $table = I('post.table');
            $page = I('post.page', 1);
            $pager = array('page' => $page, 'pageSize' => 20);

            //模糊搜索
            if($search){
                $where['id'] = array('like','%'.$search.'%');
                $where['username'] = array('like','%'.$search.'%');
                $where['phone'] = array('like','%'.$search.'%');
                $where['idcard'] = array('like','%'.$search.'%');
                $where['_logic'] = 'or';
            }

            $users = $this->getAll('user',$where, 'id', '','id asc', $pager);
//            var_dump(is_array(array('o'=>'id')));
//            die;

            $user_ids = $this->sortInfoById($users['data'],'id','id');
            $uid_string = join('_', $user_ids);

            $url = 'http://'.C('SERVER_IP').'/GetUserData';

            $params = 'showIds=' . $uid_string . '&type=0';
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;
            $lists = $this->getHTTPData($url);


            //序列化接口请求的数据
            $http_user_data = $this->sortInfoById($lists['users'],'showId');

            //组装数据
            foreach ($users['data'] as $k=>$v){
                $users['data'][$k]['gold'] = $http_user_data[$v['id']]['gold']?floor($http_user_data[$v['id']]['gold']):0;//金币
//                $users['data'][$k]['father_id'] = $http_user_data[$v['id']]['generalizeId1']?$http_user_data[$v['id']]['generalizeId1']:0;//一级推广id
                $users['data'][$k]['father_id'] = $v['father_id']?$v['father_id']:0;//改为自表查询，不再显示接口的数据
                $users['data'][$k]['login_time'] = $http_user_data[$v['id']]['lastLoginTime']?$http_user_data[$v['id']]['lastLoginTime']:0;//上次登陆时间
                $users['data'][$k]['mid'] = $http_user_data[$v['id']]['id']?$http_user_data[$v['id']]['id']:0;//mongodb的id
                $users['data'][$k]['idcard'] = $v['idcard']?$v['idcard']:'';//身份证
                $users['data'][$k]['username'] = $v['username']?$v['username']:'';//用户名
                $users['data'][$k]['phone'] = $v['phone']?$v['phone']:'';//手机
                $users['data'][$k]['state'] = ($v['state'] == -1)?2:$v['state'];//状态
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
            ksort($data);
            $users['data'] = $data;

            echo json_encode($users);die;
        }

        $this->display();
    }

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

    //添加用户
    public function addUser(){
        $post = I('post.');
        if($post){
            if(!$post['password'] || !$post['phone']){
                $this->error('帐号或密码不能为空！');
                die;
            }

            $post['username'] = $post['phone'];
            //帐号唯一性验证
            $eWhere['phone'] = $post['phone'];
            $eWhere['username'] = $post['username'];
            $eWhere['_logic'] = 'or';
            $account_exists = $this->getAll('user',$eWhere);
            if($account_exists){
                $this->error('该帐号已经被注册！');
                die;
            }

            $post['password'] = base64_encode($post['password']);
            $post['register_time'] = time();
            $res = $this->insAndUpdate('user','',$post);
            if($res['state']){
                $this->success('操作成功！',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
                die;
            }else{
                $this->error('添加失败！');
                die;
            }
        }else{
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            $this->display();
        }
    }

    //编辑用户
    public function editUser(){
        $post = I('post.');
        $id = I('get.uid');
        $where['id'] = $id;

        if($post){
            $uid = $post['uid'];
            $uWhere['id'] = $uid;
            unset($post['uid']);

            //密码为空则不修改密码
            if($post['password']){
                $post['password'] = base64_encode($post['password']);
            }else{
                unset($post['password']);
            }

            $user_info = $this->getAll('user',$uWhere);
            $user_info = current($user_info);

            $model = D();
            $model->startTrans();

//            var_dump($user_info);
//            var_dump(in_array($post['user_type'],array(1,2)) && (!$user_info['extension_code']));
            //当用户被修改为代理商层级且用户之前不是代理商层级[没有推广码]时获取新的推广码
            if(in_array($post['user_type'],array(1,2)) && (!$user_info['extension_code'])){
                //推广码
                $code = $this->getExtensionCode($post);
                $post['extension_code'] = $code;
            }
//            var_dump($post);die;

            if(($post['user_type'] == 0) && $user_info['extension_code']){
                //检测代理商关系
                $aWhere['father_id'] = $uid;
                $counts = D('user')->where($aWhere)->count();
                if($counts>0) {
                    //代理商被修改为普通用户删除代理关系
                    $dWhere['father_id'] = $uid;
                    $dData['father_id'] = 0;
                    $res1 = $this->insAndUpdate('user', $dWhere, $dData);
                    if (!$res1['state']) {
                        $model->rollback();
                        echo json_encode(array('state' => 0, 'msg' => '修改失败！'));
                        die;
                    }
                }
                $post['extension_code'] = '';
            }
//            die;
            $res = $this->insAndUpdate('user',$uWhere,$post);
//            echo D('user')->_sql();
//            die;
            if($res['state']){
                $model->commit();
                echo json_encode(array('state'=>1,'msg'=>'修改成功！'));
                die;
            }else{
//                echo D('user')->_sql();
//                var_dump($res1);
                $model->rollback();
                echo json_encode(array('state'=>0,'msg'=>'修改失败！'));
                die;
            }
        }else{
            $user_info = $this->getAll('user',$where);
            $user_info = current($user_info);
            echo json_encode($user_info);die;
        }
    }

    //渠道管理
    public function channelManagement(){
        $post = I('post.');
        if($post){
            set_time_limit(0);
            $page = I('post.page',1);
            $search = I('post.search','');

            $pager = array('page' => $page, 'pageSize' => 10);

            //模糊查询
            if($search) {
                $search_fields = array();//检索字段
                foreach ($search_fields as $v) {
                    $where[$v] = array('like', '%' . $search . '%');
                }
                $where['_logic'] = 'or';
                $where2['user_type'] = array('in',array(1,2));
                $where_main['_complex'] = array(
                    $where, $where2,
                    '_logic' => 'and'
                );
            }else{
                $where_main['user_type'] = array('in',array(1,2));
            }

            //用户分页列表
            $users = $this->getAll('user',$where_main, '', '', '', $pager);
            $user_ids = $this->sortInfoById($users['data'],'id','id');
            $ids_string = join('_',array_values($user_ids));

//            $url = 'http://'.C('SERVER_IP').'/GetUserGameData';
//
//            $params = 'showIds='.$ids_string;
//            $params = $this->publicEncrypt($params);
//            $url .= '?data='.$params;
////            var_dump($url);die;
//            $lists = $this->getHTTPData($url);
//
//            $http_data = $lists['data'];
//
//            $sort_data = $this->sortInfoById($http_data,'showId');


            $url = 'http://'.C('SERVER_IP').'/GetUserData';

            $params = 'showIds=' . $ids_string . '&type=1';
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);
//            var_dump($url,$lists);die;
            $sort_data = $this->sortInfoById($lists['users'],'showId');

            $user_state = array('正常','封号','永久封号');
            $user_type = array('普通用户','代理商','商会长');

//            //下属玩家统计
//            $sql = 'SELECT p.id,count(s.username) AS children_count FROM user AS p LEFT JOIN user AS s ON s.father_id = p.id GROUP BY p.username ORDER BY p.id';
//            $counts = D('user')->query($sql);
//            foreach ($counts as $c){
//                $count_data[$c['id']] = $c['children_count'];
//            }

            foreach ($users['data'] as $k=>$v){
                $combine[$k] = array(
                    'uid'=>$v['id'],
                    'username'=>$v['username'],
                    'state'=>$user_state[$v['state']],
                    'user_type'=>$user_type[$v['user_type']],
                    'diamond'=>floor($sort_data[$v['id']]['diamond']),
                    'player_num'=>$sort_data[$v['id']]['num'],//$count_data[$v['id']],
                    'login_time'=>$sort_data[$v['id']]['lastLoginTime']?date('Y-m-d H:i:s',$sort_data[$v['id']]['lastLoginTime']):'-',
                    'register_time'=>$v['register_time']?date('Y-m-d H:i:s',$v['register_time']):'-',
                    'mid' => $sort_data[$v['id']]['id'],//mongodb的id
                );

                //空数据默认显示0
                foreach ($combine[$k] as $key=>$val){
                    if(!$val){
                        $combine[$k][$key] = 0;
                    }
                }
            }

            $json = array('data'=>$combine,'page'=>$users['page']);
            echo json_encode($json);
            die;
        }
        $this->display();
    }

    //挂单记录
    public function pendingOrder(){
        $post = I('post.');
        if($post) {
            $page = I('post.page', 1);
            $pageSize = 20;
            $order_type = array('买','卖');//定义挂单类型
            $order_state = array('挂单','完成','撤销');//定义挂单类型
//            var_dump($post);
            $mid = I('post.mid');//mongodb的用户数据id
            $uid = I('post.uid');
            $where['id'] = $uid;
            $start = $pageSize * ($page - 1);

            //获取该用户的信息
            $userInfo = $this->getAll('user',$where);
            $userInfo = current($userInfo);

            //请求接口数据
//            $mid = '59968489da19462530deacf8';//测试固定数据
            $url = 'http://'.C('SERVER_IP').'/GetTradeRecord';

            $params = 'index='. $start .'&num=' . $pageSize  . '&owner=' . $mid . '&sort=startTime&type=2';
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;


            $lists = $this->getHTTPData($url);
//            var_dump($url,$lists);die;

            //组装数据
            foreach ($lists['trades'] as $v){
                $data[$v['startTime']] = array(
                    'id'=>$uid,
                    'product_id'=>$v['itemId'],
                    'product_name'=>$v['itemName'],
                    'order_type'=>$order_type[$v['type']],
                    'order_num'=>$v['entrustNum'],
                    'price'=>round($v['price'],4),
                    'order_time'=>$v['startTime'],
                    'order_state'=>$order_state[$v['state']],
                    'end_time'=>$v['tradeTime'],
                    'sell_num'=>array_sum($v['tradeNum']),
                    'trade_uid'=>join(',',$v['tradeUserId']),
                );
            }

            //字段翻译
            $translate_fields = C('TRANSLATE_FIELDS');
            $translate = $translate_fields['user'];

            //处理状态数据
            foreach ($data as $k=>$v){
                $temp = $v;
                foreach ($translate as $field=>$string){
                    if($string == 'to_time'){
                        $temp[$field] = date('Y-m-d H:i:s',$v[$field]);
                    }else {
                        $temp[$field] = $string[$v[$field]];
                    }
                }
                $data[$k] = $temp;
            }

            ksort($data);
            $data = array_values($data);
            $json = array('data'=>$data,'page'=>array('page'=>$page,'totalPage'=>ceil($lists['totalNum']/$pageSize)));

            echo json_encode($json);
            die;
        }

        $this->display();
    }

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
                $records['data'][$k]['time'] = date('Y-m-d H:i:s',$v['time']);
            }

            $json = array('data'=>$records['data'],'page'=>$records['page']);
            echo json_encode($json);
            die;
        }

        $this->display();
    }

    //物品清单
    public function itemsList(){
        $post = I('post.');
        if($post) {
            $uid = I('post.uid');
            $url = 'http://'.C('SERVER_IP').'/GetItemList';

            $params = 'showId='.$uid;
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;


            $lists = $this->getHTTPData($url);
            $items = $lists['items'];
            $json = array('data'=>$items,'page'=>array());
            echo json_encode($json);
            die;
        }
        $this->display();
    }

    //推广列表
    public function promotionList(){
        $post = I('post.');
        if($post){
            set_time_limit(0);
            $uid = I('post.uid');

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

            //如果有周，以周为准
            if($week == 1){//只有第一周使用新接口
                //使用新接口查以前的数据
                $start = ($page-1)*$pageSize;
                $end = $pageSize;
                $params = 'index='.$start.'&num='.$end.'&showId='.$uid;
                $url = 'http://'.C('SERVER_IP').'/GetOldGeneralizeList';
                $params = $this->publicEncrypt($params);
                $url .= '?data='.$params;
                $lists = $this->getHTTPData($url);
            }else{
                //其他全部走老接口
                if($week){//周的时间处理
                    $search_week = $week-1;//使用临时变量，week用于后面，不能参加计算
                    $start_time = strtotime('+'.$search_week.' week',$first_week_start_timestamp);//开始时间
                    $end_time = strtotime('+'.$search_week.' week',$first_week_end_timestamp);//结束时间
//                    var_dump(date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time));
//                    die;
                }else {
                    //否则处理日期
                    if($start_year) {//有日期处理日期，否则查询全部
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
                $url = 'http://'.C('SERVER_IP').'/GetGeneralizeList';
                $start = ($page-1)*$pageSize;
                $end = $pageSize;
                $params = 'index='.$start.'&num='.$end.'&showId='.$uid;//分页信息
                $start_time?$params .= '&startTime='.$start_time.'&endTime='.$end_time:'';//时间信息，如果有才添加参数
                $params = $this->publicEncrypt($params);//处理参数
                $url .= '?data='.$params;
                $lists = $this->getHTTPData($url);
            }

            //处理接口数据
            $data = $lists['users'];
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

                $users[$v['id']] = array(
                    'uid'=>$v['id'],
                    'username'=>$user_info[$v['id']]['username'],
                    'register_time'=>date('Y-m-d H:i:s',$user_info[$v['id']]['register_time']),
                    'recharge'=>$temp_recharge,//充值总额
                    'cost'=>$temp_cost,//消费总额
                    'cost_commission'=>$temp_cost_money,//消费佣金
                    'serviceCharge'=>$temp_serviceCharge,//手续费
                    'service_commission'=>$temp_service_money,//手续费佣金
                    'commission'=>$temp_cost_money+$temp_service_money,//佣金总额
                );

                //底部数据总计
                $recharge += $temp_recharge;//充值总额
                $cost += $temp_cost;//消费总额
                $cost_money += $temp_cost_money;//消费佣金
                $serviceCharge += $temp_serviceCharge;//手续费
                $service += $temp_service_money;//手续费佣金
                $commission += ($temp_cost_money+$temp_service_money);//佣金总额
            }

            //以ID排序
            ksort($users);
            $users = array_values($users);

            //底部数据总计
            $users[] = array(
                'uid'=>'总计：',
                'username'=>'-',
                'register_time'=>'-',
                'recharge'=>$recharge,//充值总额
                'cost'=>$cost,//消费总额
                'cost_commission'=>$cost_money,//消费佣金
                'serviceCharge'=>$serviceCharge,//手续费
                'service_commission'=>$service,//手续费佣金
                'commission'=>$commission?$commission:0,//佣金总额
            );

            $json = array('data'=>$users,'page'=>array('page'=>$page,'totalPage'=>ceil($lists['totalNum']/$pageSize)),'level'=>$user_level_string[$user_level]);
            echo json_encode($json);
            die;
        }
        $this->display();
    }

    //获取用户游戏数据
    public function gameData(){
        $post = I('post.');

        $uid = I('post.uid',0);
        $page = I('post.page',1);
        $pager = array('page' => $page, 'pageSize' => 20);
        $search = I('post.search','','trim');

        if($search){
            //用户分页列表
//            $where = $uid?array('id'=>$uid):'';
            $where['id'] = $search;
            $users = $this->getAll('user',$where, '', '', '', $pager);
            $user_ids = $this->sortInfoById($users['data'],'id','id');
            $ids_string = join('_',array_values($user_ids));

            $url = 'http://'.C('SERVER_IP').'/GetUserGameData';

            $params = 'showIds='.$ids_string;
//            var_dump($params);
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

//            $url = 'http://'.C('SERVER_IP').'/GetUserGameData?showIds=64_67';//测试数据
            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
            $http_data = $lists['data'];
            if($http_data) {

                $sort_data = $this->sortInfoById($http_data, 'showId');

                foreach ($users['data'] as $k => $v) {
//                $combine[$k] = array(
                    $combine[$v['id']] = array(
                        'uid' => $v['id'],
                        'username' => $sort_data[$v['id']]['userName'],
                        'diamond' => floor($sort_data[$v['id']]['diamond']),
                        'treasure' => $sort_data[$v['id']]['treasure'],
                        'recharge' => $sort_data[$v['id']]['recharge'],//充值总额
                        'cost' => $sort_data[$v['id']]['cost'],//下线总额cost
                        'difference' => $sort_data[$v['id']]['recharge'] - $sort_data[$v['id']]['cost'],//上下差值
                        'depotLevel' => $sort_data[$v['id']]['depotLevel'],
                        'stealTotalValue' => $sort_data[$v['id']]['stealTotalValue'],
                        'beStolenTotalValue' => $sort_data[$v['id']]['beStolenTotalValue'],
                        'steal_difference' => $sort_data[$v['id']]['stealTotalValue'] - $sort_data[$v['id']]['beStolenTotalValue'],//偷取差值
                        'dogNum' => $sort_data[$v['id']]['dogNum'],
                        'dogFoodNum' => $sort_data[$v['id']]['dogFoodNum'],
                        'speedUpItemNum' => $sort_data[$v['id']]['speedUpItemNum'],
                        'consecrateNum' => $sort_data[$v['id']]['consecrateNum'],
                        'farms' => join(',', $sort_data[$v['id']]['farms']),
                        'fishs' => join(',', $sort_data[$v['id']]['fishs']),
                        'forests' => join(',', $sort_data[$v['id']]['forests']),
                        'mines' => join(',', $sort_data[$v['id']]['mines']),
                    );

                    foreach ($combine[$k] as $key => $val) {
                        if (!$val) {
                            $combine[$k][$key] = 0;
                        }
                    }
                }
                ksort($combine);
                $combine = array_values($combine);
            }else{
                $combine = NULL;
            }

//            var_dump($http_data,$sort_data);die;

            $json = array('data'=>$combine,'page'=>$users['page']);
            echo json_encode($json);
            die;
        }

        $this->display();
    }

    //删除用户
    public function deleteUser(){
        $uid = I('get.uid',0);
        if(!$uid){
            $this->error('删除失败，参数不完整！');
            die;
        }

        $where['id'] = $uid;
        $res = $this->deleteInfo('user',$where);
        if($res['state']){
            $this->redirect(U('Home/User/index'));
            die;
        }else{
            $this->error('删除失败！');
            die;
        }
    }



    //保存用户推广列表数据功能
    public function savePromotionList(){
        set_time_limit(0);
        $uid = I('post.uid');
        $week = I('post.week');//周的序号

        //周的处理
        $first_week_start_timestamp = strtotime('2017-10-9');//第一周开始时间
        $first_week_end_timestamp = strtotime('2017-10-15');//第一周结束时间

        //如果有周，以周为准
        if($week == 1){//只有第一周使用新接口
            //使用新接口查以前的数据
            $params = 'showId='.$uid;
            $url = 'http://'.C('SERVER_IP').'/GetOldGeneralizeList';
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;
            $lists = $this->getHTTPData($url);
        }else{
            //其他全部走老接口
            $search_week = $week-1;//使用临时变量，week用于后面，不能参加计算
            $start_time = strtotime('+'.$search_week.' week',$first_week_start_timestamp);//开始时间
            $end_time = strtotime('+'.$search_week.' week',$first_week_end_timestamp);//结束时间

            $url = 'http://'.C('SERVER_IP').'/GetGeneralizeList';
            $params = 'showId='.$uid;
            $start_time?$params .= '&startTime='.$start_time.'&endTime='.$end_time:'';//时间信息，如果有才添加参数
            $params = $this->publicEncrypt($params);//处理参数
            $url .= '?data='.$params;
            $lists = $this->getHTTPData($url);
        }

        //处理接口数据
        $data = $lists['users'];
        foreach ($data as $v){
            $ids[] = $v['id'];
        }
        $ids[] = $uid;//获取所有的UID
        $where['id'] = array('in',$ids);
        $user_info = $this->getAll('user',$where,'id');//根据UID获取本地用户的基本信息

        //用户等级
        $user_level = 1;//目前没有分，默认为1
        $cost_commission_point2 = array(0,0.08,0.09,0.1);//消费佣金比例
        $service_commission_point2 = array(0,0.8,0.9,1);//手续费佣金比例----手续费暂时不分，为1的情况为0.8

        //数据组装计算
        $users = array();
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

            $users[$v['id']] = array(
                'uid'=>$v['id'],
                'phone'=>$user_info[$v['id']]['username'],
                'register_time'=>date('Y-m-d H:i:s',$user_info[$v['id']]['register_time']),
                'recharge'=>$temp_recharge,//充值总额
                'cost'=>$temp_cost,//消费总额
                'cost_commission'=>$temp_cost_money,//消费佣金
                'serviceCharge'=>$temp_serviceCharge,//手续费
                'service_commission'=>$temp_service_money,//手续费佣金
                'commission'=>$temp_cost_money+$temp_service_money,//佣金总额
                'week'=>$week,
                'father_id'=>$uid,
            );
        }

        //以ID排序
        ksort($users);
        $users = array_values($users);

        var_dump($users);

        //插入数据表
//        D('user_promotion_list')->addAll($users);
    }
}