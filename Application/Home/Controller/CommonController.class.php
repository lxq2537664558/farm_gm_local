<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $login_info = session('user_bindInfo_login_page');
        $session_uid = $login_info['uid'];
        if(in_array(ACTION_NAME,array('checkIDCard','bindIDCard','bindBank','Authentication','manageAccount','editAccount'))){
            $uid = I('get.uid',0);
            if(!$login_info || ($session_uid != $uid)){
                $this->redirect(U('Home/Common/userLogin'));
                die;
            }
        }
    }

    //登录
    public function userLogin(){
        $post = I('post.');
        if($post){
            $username = $post['phone'];
            $password = base64_encode($post['password']);
            //帐号或用户名或手机号
            $where1['account'] = $username;
            $where1['username'] = $username;
            $where1['phone'] = $username;
            $where1['_logic'] = 'or';

            //密码
            $where2['password'] = $password;

            //条件逻辑
            $where_main['_complex'] = array(
                $where1, $where2,
                '_logic' => 'and'
            );

            $is_true = D('user')->where($where_main)->select();
//            echo D('user')->_sql();die;
            if($is_true){
                $is_true = current($is_true);
                session('user_bindInfo_login_page',array('is_login'=>1,'uid'=>$is_true['id']));
                $this->redirect(U('Home/Common/checkIDCard',array('uid'=>$is_true['id'])));
                die;
            }else{
                session('user_bindInfo_login_page',NULL);
                $this->error('用户名或密码不正确');
                die;
            }
        }else{
            $this->assign('url',U('Home/Common/userLogin'));
            $this->display();
        }
    }

    //认证
    public function checkIDCard()
    {
        $uid = I('get.uid', 0);

        //获取用户信息，指定页面状态
        $where['id'] = $uid;
        $user_info = D('user')->where($where)->select();

        $user_info = current($user_info);
        $idcard = $user_info['idcard'];
        $realname = $user_info['realname'];
        $check = 0;
        if ($idcard && $realname) {
            $check = substr($idcard, 0, 6) . '************';
        }

        $collection_account = $user_info['collection_account'] ? $user_info['collection_account'] : 0;

        $this->assign('uid', $uid);
        $this->assign('idcard', $check);//实名认证
        $this->assign('collection_account', $collection_account);//银行卡绑定
        $this->assign('bank', $user_info['bank']);
        $this->display();
    }

    public function bindIDCard()
    {
        $uid = I('get.uid', 0);

        $post = I('post.');
        if ($post) {
            $realname = $post['realname'];
            $idcard = $post['idcard'];
            if (!$realname || !$idcard) {
                $this->error('姓名或身份证号不能为空！');
                die;
            }

            //身份证验证
            $is_true = validation_filter_id_card($idcard);
            if (!$is_true) {
                $this->error('身份证格式不正确！');
                die;
            }

            $where['id'] = $uid;
            $data['realname'] = $realname;
            $data['idcard'] = $idcard;
            D('user')->where($where)->save($data);
            $this->redirect(U('Home/Common/checkIDCard', array('uid' => $uid)));
        } else {
            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, array('uid' => $uid)));
            $this->display();
        }
    }

    public function bindBank()
    {
        $uid = I('get.uid', 0);

        $post = I('post.');
        if ($post) {
            $realname = $post['realname'];
            $collection_account = $post['collection_account'];
            $bank = $post['bank'];

            if ((!$realname) || (!$collection_account) || (!$bank) || (!$post['opening_bank'])) {
                $this->error('姓名、账户银行、账户帐号、开户行均不能为空！');
                die;
            }

            preg_match('/^([1-9]{1})(\d{14}|\d{18})$/',$collection_account,$match);
            if(!$match[0]){
                $this->error('账户银行格式不正确！');
                die;
            }

            //验证姓名是否一致
            $where['realname'] = $realname;
            $where['id'] = $uid;
            $user_exists = D('user')->where($where)->select();
            if (!$user_exists) {
                $this->error('姓名与注册信息不一致，请检查！');
                die;
            }

            $uWhere['id'] = $uid;
            $data = array('realname' => $realname, 'bank' => $bank, 'collection_account' => $collection_account,'opening_bank'=>$post['opening_bank']);
            D('user')->where($uWhere)->save($data);
            $this->redirect(U('Home/Common/checkIDCard', array('uid' => $uid)));
        } else {
            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, array('uid' => $uid)));
            $this->display();
        }
    }

    //查看认证
    public function Authentication()
    {
        $uid = I('get.uid', 0);

        $where['id'] = $uid;
        $user_info = D('user')->where($where)->select();
        $user_info = current($user_info);

        $this->assign('realname', $user_info['realname']);
        $this->assign('idcard', $user_info['idcard']);

        $this->display();
    }

    //管理账户
    public function manageAccount()
    {
        $uid = I('get.uid', 0);

        $where['id'] = $uid;
        $user_info = D('user')->where($where)->select();
        $user_info = current($user_info);
        $this->assign('realname', $user_info['realname']);
        $this->assign('bank', $user_info['bank']);
        $this->assign('opening_bank', $user_info['opening_bank']);
        $this->assign('collection_account', $user_info['collection_account']);
        $this->assign('uid', $uid);

        $this->display();
    }

    //修改账户-不安全，会被其他用户篡改，暂时不开放修改用户信息的通道
    public function editAccount()
    {
        $uid = I('get.uid','');

        $post = I('post.');
        if ($post) {
            $where['id'] = $uid;
            $res = D('user')->where($where)->save($post);
            if($res){
                $this->redirect(U('Home/Common/checkIDCard'), array('uid' => $uid));
            }else{
                $this->error('修改失败！');
            }
        } else {
            $where['id'] = $uid;
            $user_info = D('user')->where($where)->select();
            $user_info = current($user_info);

            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, array('uid' => $uid)));
            $this->assign('realname', $user_info['realname']);
            $this->assign('bank', $user_info['bank']);
            $this->assign('opening_bank', $user_info['opening_bank']);
            $this->assign('collection_account', $user_info['collection_account']);
            $this->display('bindBank');
        }
    }

    //用户自助注册界面
    public function userRegister()
    {
//        $post = I('post.');
//
//        if ($post) {
//            $extension_code = $post['extension_code'];
//            $account = $post['account'];
//            $password = $post['password'];
//            $repass = $post['repass'];
//            $code = $post['code'];
//
//            //字段验证
//            if (!$password) {
//                $this->error('密码不能为空！');
//                die;
//            }
//            if ($password != $repass) {
//                $this->error('两次密码输入不一致！');
//                die;
//            }
//
//            //手机号唯一性
//            $pWhere['phone'] = $post['phone'];
//            $phone_exists = D('user')->where($pWhere)->select();
//            if ($phone_exists) {
//                $this->error('该手机号已被注册！');
//                die;
//            }
//
//            //短信发送成功的时候写入到SESSION中
//            $session = session('messege_code');
//            $session_code = $session['code'];
//            $time = $session['time'];
//
//            if ($session_code != $code) {
//                $this->error('验证码错误！');
//                die;
//            }
//            //验证码5分钟内有效
//            if (time() > ($time + 5 * 60)) {
//                $this->error('验证码已失效，请重新获取！');
//                die;
//            }
//
//            //获取father_id和grandfather_id
//            $where['extension_code'] = $extension_code;
//            $father = D('user')->where($where)->select();
//            $father = current($father);
//            $father_id = $father['id'];
//
//            $data = array(
//                'account' => $account,
//                'password' => base64_encode($password),
//                'father_id' => $father_id,
//                'grandfather_id' => $father['father_id'],
//                'phone' => $post['phone'],
//                'username' => $post['phone'],
//                'register_time' => time(),
//            );
//            $res = D('user')->add($data);
//            if ($res) {
//                //移动设备浏览，则切换模板
//                if (ismobile()) {
//                    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);//全部变成小写字母
//
//                    //分别进行判断
//                    if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
//                        $type = 'ios';
//                        $download_url = 'http://dafuvip.com/vy6ZRz';
//                    }
//
//                    if (strpos($agent, 'android')) {
//                        $type = 'android';
//                        $download_url = 'http://dafuvip.com/eUZfmq';
//                    }
//                }
//
//                header('location:'.$download_url);//跳转到成功页面
//                die;
//            } else {
//                $this->error('注册失败，请联系管理员！');
//                die;
//            }
//        } else {
//            $extension_code = I('get.extension_code', '');
//            $this->assign('sendMessageUrl', 'http://' . $_SERVER['HTTP_HOST'] . '/farm/sms_send.php');
//            $this->assign('url', U('Home/Common/userRegister'));
//            $this->assign('extension_code', $extension_code);
//            $this->display();
//        }
        //临时关闭该接口
        $this->display();
    }

    //将验证码存入SESSION
    public function saveCode()
    {
        $code = I('post.code');
        $time = I('post.time');
        $session = array('code' => $code, 'time' => $time);
        session('messege_code', $session);
        if (!$code || !$time) {
            echo 0;
        } else {
            echo 1;
        }
    }

    //下载游戏
    public function downloadGame()
    {
        $this->display();
    }

    //用户通过手机获取兑换码
    public function getExchangeCode(){
        $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
        $this->display();
    }

    //AJAX验证兑换码
    public function checkCode(){
        $post = I('post.');

        $phone = $post['phone'];
        //验证用户是否存在
        $where['phone'] = $phone;
        $user_exists = D('user')->where($where)->select();
        if(!$user_exists){
            echo json_encode(array('state'=>0,'msg'=>'该用户不存在！'));
            die;
        }
        $user_info = current($user_exists);

        //一个用户只能领取一次
        $oWhere['uid'] = $user_info['id'];
        $is_get = D('gift_cdk')->where($oWhere)->select();
        if($is_get){
            echo json_encode(array('state'=>0,'msg'=>'每个户只能领取一次！'));
            die;
        }

        //获取CDK-新手礼包的gids
        $s_g_where['novice'] = 1;
        $s_gift = D('gifts')->where($s_g_where)->select();
        foreach ($s_gift as $v){
            $gift_ids[] = $v['id'];
        }



        //高并发处理
        $model = D('gift_cdk');
        $model->startTrans();

        $gift_ids = join(',',$gift_ids);
        $sql = 'SELECT * FROM gift_cdk WHERE gid IN ('.$gift_ids.') AND state = 0 AND uid IS NULL LIMIT 0,1 FOR UPDATE';
        $res = $model->query($sql);
        $cdk_info = current($res);
        $code = $cdk_info['key_code'];

        //绑定CDK
        $data = array('uid'=>$user_info['id']);
        $kWhere['key_code'] = $code;
        $res = D('gift_cdk')->where($kWhere)->save($data);
//        $msg = D('gift_cdk')->_sql();
//        echo json_encode(array('state'=>0,'msg'=>$sql));die;
        if($res){
            $model->commit();
            echo json_encode(array('state'=>1,'code'=>$code));
            die;
        }else{
            $model->rollback();
            echo json_encode(array('state'=>0,'msg'=>'系统错误，数据更新失败！'));
            die;
        }





//        $sWhere['gid'] = array('in',$gift_ids);
//        $sWhere['state'] = 0;
//        $sWhere['uid'] = array('EXP','IS NULL');
//        $cdk = D('gift_cdk')->where($sWhere)->limit(0,1)->select();
//
//        $cdk = current($cdk);
//        $code = $cdk['key_code'];
//
//        //绑定CDK
//        $data = array('uid'=>$user_info['id']);
//        $kWhere['key_code'] = $code;
//        D('gift_cdk')->where($kWhere)->save($data);

//        echo json_encode(array('state'=>1,'code'=>$code));
//        die;
    }

}