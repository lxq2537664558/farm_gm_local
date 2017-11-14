<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
use Home\Controller\UserController;
use Home\Controller\OpenSSLController;
class PublicController extends BaseController {
    //此类不受登录权限验证限制

    //统一接口请求方法
    public function publicInterface(){
        //接口发送的数据
        $get = I('get.data');
        $http_request_data = base64_decode($get);
        $http_request_data = json_decode($http_request_data,true);

        $method = $http_request_data['method'];//方法名
//var_dump($http_request_data,$method);die;
        $result = $this->$method($http_request_data);
        unset($http_request_data['method']);

        //返回给接口结果
        $json = json_encode($result);
        echo $json;
        die;
    }

    //登录
    public function login($http_request_data){
        //接口请求的数据
        $username = $http_request_data['username'];
        $password = base64_encode($http_request_data['password']);

        if(!$username || !$password){
            $return = array('state'=>0,'msg'=>'用户名或密码不能为空！','data'=>NULL);
            return $return;
        }

        $where['phone'] = trim($username);//手机号
        $where['password'] = $password;//密码
        $is_true = $this->getAll('user',$where);

        if(!$is_true){
            //登录失败-用户名、密码错误
            $return = array('state'=>0,'msg'=>'用户名或密码错误！','data'=>NULL);
            return $return;
        }else{
            //用户被封号
            $user_info = current($is_true);
            if($user_info['state']){
                $return = array('state'=>0,'msg'=>'用户被封号！','data'=>NULL);
                return $return;
            }

            $realname_check = 0;
            if($user_info['idcard'] && $user_info['realname']){
                $realname_check = 1;
            }

            //如果存在登录时间不处理
            if(!$user_info['login_time']) {
                //否则为第一次登录，处理第一次登录时间,发送至接口
                $where['id'] = $user_info['id'];
                $login_time = time();
                $data['login_time'] = $login_time;
                $this->insAndUpdate('user', $where, $data);
                $post['user_type'] = $user_info['user_type'];

                $return = array(
                    'state'=>1,
                    'msg'=>'登录成功！',
                    'data'=>array(
                        'login_time'=>$login_time,
                        'id'=>$user_info['id'],
                        'father_id'=>$user_info['father_id'],
                        'realname_check'=>$realname_check,
                        'is_admin'=>$user_info['is_admin'],
                    ),
                );
                return $return;
            }

            //获取上一级的邀请码
            $fWhere['id'] = $user_info['father_id'];
            $father_info = $this->getAll('user',$fWhere);
            $father_info = current($father_info);

            $return = array(
                'state'=>1,
                'msg'=>'登录成功！',
                'data'=>array(
                    'id'=>$user_info['id'],
                    'father_id'=>$user_info['father_id'],
                    'is_admin'=>$user_info['is_admin'],
                    'realname_check'=>$realname_check,
                    'father_extension_code'=>$father_info['extension_code'],
                ),
            );
            return $return;
        }
    }

    //查询推广码接口
    public function getExtensionCode($http_request_data){
        //接口请求的数据
        $extension_code = $http_request_data['extension_code'];
        $where['extension_code'] = $extension_code;
        $user_info = $this->getAll('user',$where);

        if($user_info){
            $user_info = current($user_info);
            $return = array('state'=>1,'msg'=>'查询成功！','data'=>array('father_id'=>$user_info['father_id'],'grandfather_id'=>$user_info['grandfather_id']));
        }else{
            $return = array('state'=>0,'msg'=>'查询失败！','data'=>NULL);
        }

        return $return;
    }

    //设置推广码
    public function setExtensionCode($http_request_data){
        //接口请求的数据
        $extension_code = $http_request_data['extension_code'];
        $uid = $http_request_data['uid'];

        //绑定推广码
        if($extension_code == 0){
            $return = array('state'=>0,'msg'=>'推广码必须为数字,且不能为0，绑定失败！');
            return $return;
        }

        $where['extension_code'] = $extension_code;

        $user_info = $this->getAll('user',$where);
        $user_info = current($user_info);//上级信息

        $data['father_id'] = $user_info['id'];
        $data['grandfather_id'] = $user_info['father_id'];
        $uWhere['id'] = $uid;
        $res = $this->insAndUpdate('user',$uWhere,$data);
        //记录推广码变动
        $rData = array(
            'uid'=>$uid,
            'extension_code'=>$extension_code,
            'father_id'=>$user_info['id'],
            'month'=>date('m'),
            'day'=>date('d'),
        );
        $this->insAndUpdate('extension_change','',$rData);

        if($res['state']){
            $return = array('state'=>1,'msg'=>'绑定成功！','father_id'=>$user_info['id'],'grandfather_id'=>$user_info['father_id']);
        }else{
            $return = array('state'=>0,'msg'=>'绑定失败！');
        }

        return $return;
    }

    //注册
    public function register($http_request_data){
        //接口请求的数据
        $username = $http_request_data['username'];//用户名
        $account = $http_request_data['account'];//帐号
        $password = base64_encode($http_request_data['password']);//密码
        $phone = $http_request_data['phone'];//手机

        if(!$phone){
            $return = array('state'=>0,'msg'=>'注册失败，手机号不能为空！','data'=>NULL);
            return $return;
        }

        //帐号唯一性验证
        $aWhere['phone'] = $account;
        $account_exists = $this->getAll('user',$aWhere);
        if($account_exists){
            $return = array('state'=>0,'msg'=>'注册失败，该帐号已被注册！','data'=>NULL);
            return $return;
        }

        //昵称唯一性验证
        $aWhere['username'] = $account;
        $account_exists2 = $this->getAll('user',$aWhere);
        if($account_exists2){
            $return = array('state'=>0,'msg'=>'注册失败，已使用的昵称！','data'=>NULL);
            return $return;
        }

        $data = array(
            'username'=>$username,
            'account'=>$account,
            'password'=>$password,
            'register_time'=>time(),
            'phone'=>$phone,
        );

        $res = $this->insAndUpdate('user','',$data);
        if($res['state']){
            $return = array('state'=>1,'msg'=>'注册成功！','data'=>NULL,'data'=>array('id'=>$res['id']));
        }else{
            $return = array('state'=>0,'msg'=>'注册失败，系统错误！','data'=>NULL);
        }

        return $return;
    }

    //请求提现，记录结果
    public function cashRegister($http_request_data){
        $type = $http_request_data['type'];
        $uid = $http_request_data['uid'];
        $data = array(
            'uid' => $uid,
            'money'=>$http_request_data['money'],
            'time'=>time(),
            'type'=>$type,
            'admin_id'=>$http_request_data['admin_id'],
        );

        $model = D();//实例化空模型
        $model->startTrans();//开始事务

        $res = $this->insAndUpdate('gold_record','',$data);//金币记录写入
        if(!$res['state']){
            $model->rollback();//回滚操作
            $return = array('state'=>0,'msg'=>'系统错误，金币记录写入失败！','data'=>NULL);
            return $return;
        }

        if($type == 0){
            //查找该UID对应的信息
            $sWhere['id'] = $uid;
            if(!$uid){
                $return = array('state'=>0,'msg'=>'系统错误，用户UID丢失！','data'=>NULL);
                return $return;
            }
            $user_info = $this->getAll('user',$sWhere);
            $user_info = current($user_info);

            $check_array = array('collection_account','bank','realname','phone');
            if(!$user_info){
                $return = array('state'=>0,'msg'=>'系统错误，用户信息查询失败！','data'=>NULL);
                return $return;
            }
            foreach ($check_array as $v){
                if(!$user_info[$v]){
                    $return = array('state'=>0,'msg'=>'系统错误，用户信息参数不完整，缺少'.$v.'！','data'=>NULL);
                    return $return;
                }
            }
            //github 111111111
            //存到提现表
            $tData = array(
                'uid'=>$uid,
                'money'=>$http_request_data['money']/10,
                'collection_account'=>$user_info['collection_account'],
                'bank'=>$user_info['opening_bank'],
                'realname'=>$user_info['realname'],
                'payee_name'=>$user_info['phone'],
                'time'=>time(),
//                'yingfu'=>($http_request_data['money']-$http_request_data['money']*0.005)/10,
            //又改了需求，改成2%
                'yingfu'=>($http_request_data['money']-$http_request_data['money']*0.02)/10,
            );
            $res2 = $this->insAndUpdate('withdrawals','',$tData);
            if(!$res2){
                $model->rollback();//回滚操作
                $return = array('state'=>0,'msg'=>'系统错误，提现记录写入失败！','data'=>NULL);
                return $return;
            }else{
                $model->commit();//提交事务
                $return = array('state'=>1,'msg'=>'添加成功！','data'=>NULL);
                return $return;
            }
        }

        $return = array('state'=>1,'msg'=>'添加成功！','data'=>NULL);
        return $return;
    }

    //找回密码
    public function resetPassword($http_request_data){
        $where['phone'] = $http_request_data['phone'];
        $data['password'] = base64_encode($http_request_data['password']);
        $res = $this->insAndUpdate('user',$where,$data);
        $state = $http_request_data['state'];

        if($state != '666'){
            $return = array('state'=>0,'msg'=>'找回失败，非法操作！','data'=>NULL);
            return $return;
        }

        if($res['state']){
            $return = array('state'=>1,'msg'=>'操作成功！');
        }else{
            $return = array('state'=>0,'msg'=>'密码找回失败','data'=>NULL);
        }

        return $return;
    }

    //根据CDK查询礼包方法
    public function getPacksInfoByCDK($http_request_data){
        $key_code = $http_request_data['key_code'];
        $uid = $http_request_data['uid'];
        $kWhere['key_code'] = $key_code;
        if(!$key_code){
            $return = array('state'=>0,'msg'=>'系统错误，空的CDK！','data'=>NULL);
            return $return;
        }

        //根据CDK查询礼包id
//        $key_info = D('gift_cdk')->query('select * from gift_cdk where binary key_code = '.$key_code);
        $key_info = D('gift_cdk')->where($kWhere)->select();
        if(!$key_info){
            $return = array('state'=>0,'msg'=>'无效的CDK！','data'=>NULL);
            return $return;
        }
        $key_info = current($key_info);
        $gid = $key_info['gid'];//礼包id

        //每个用户相对与每个礼包只能使用一次
        $uWhere['uid'] = $uid;
        $uWhere['gid'] = $gid;
        $uWhere['state'] = 1;
        $user_exists = $this->getAll('gift_cdk',$uWhere);
        if($user_exists){
            $return = array('state'=>3,'msg'=>'该用户已领过该礼包');
            return $return;
        }

        $cdk_state = $key_info['state'];//CDK状态

        if($cdk_state == 1){
            $return = array('state'=>2,'msg'=>'CDK失效！','data'=>NULL);
            return $return;
        }

        //修改CDK使用状态
        $kData['state'] = 1;
        $kData['uid'] = $uid;
        $res = $this->insAndUpdate('gift_cdk',$kWhere,$kData);

        if(!$res['state']){
            $return = array('state'=>0,'msg'=>'系统错误，修改状态失败！','data'=>NULL);
            return $return;
        }

        //根据礼包id获取礼包信息
        $gWhere['id'] = $gid;
        $pack_info = $this->getAll('gifts',$gWhere);
        $pack_info = current($pack_info);
        //id-数量，id-数量
        $gift_id = explode(',',$pack_info['gift_id']);
        $gift_number = explode(',',$pack_info['gift_number']);
        $item = '';
        foreach ($gift_id as $k=>$v){
            $item .= $v.','.$gift_number[$k].',';
        }
        $pack_info['item'] = rtrim($item,',');

        unset($pack_info['state']);

        if($pack_info){
            $return = array('state'=>1,'msg'=>'验证成功！','data'=>$pack_info);
        }else{
            $return = array('state'=>0,'msg'=>'验证失败！','data'=>NULL);
        }

        return $return;
    }


    //管理员登录方法
    public function adminLogin(){
        $qrcode_pass = I('get.qrcode_pass','');
        $code = I('get.extension_code','');

        $post = I('post.');
        if($post){
            $username = trim($post['username']);
            $password = base64_encode($post['password']);

            $where['_string']='BINARY username = "'.$username.'" AND password = "'.$password.'"';

            if(!$username || !$password){
                $this->error('用户名或密码不能为空！');
                die;
            }

            $user_exists = $this->getAll('admin',$where);

            if(!$user_exists){
                $this->error('用户名或密码错误！');
                die;
            }

            $user_info = current($user_exists);

            //写入SESSION
            $loginInfo = array(
                'isAdmin' => 1,
                'uid'=>$user_info['id'],
                'username'=>$user_info['username'],
                'group'=>$user_info['group'],
            );
            session('AdminInfo',$loginInfo);

            if($qrcode_pass){
                $this->success('登录成功！',U('Home/User/addUser',array('extension_code'=>$code,'qrcode_pass'=>$qrcode_pass)));
            }else{
                $this->success('登录成功！',U('Home/Index/index'));
            }
        }else{
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            $this->display();
        }
    }

    //管理员退出方法
    public function adminLogout(){
        session('AdminInfo',NULL);
        $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/adminLogin'));
        die;
    }

    //修改密码
    public function changePassword($http_request_data){
        $uid = $http_request_data['uid'];
        $password = base64_encode($http_request_data['password']);
        $state = $http_request_data['state'];

        if($state){
            $data['password'] = $password;
            $where['id'] = $uid;
            $res = $this->insAndUpdate('user',$where,$data);

            if($res['state'] == '666'){
                $return = array('state'=>1,'msg'=>'修改成功！');
            }else{
                $return = array('state'=>0,'msg'=>'验证失败！');
            }
        }

        return $return;
    }






    //电信单独添加钻石接口,访问方式不同，不需要经过统一接口路径
    //cui
    public function AddDiamonds(){
        $http_request = I('post.data');//获取POST数据

        $ssl = new OpenSSLController();
        $public_key = $ssl->getPublicKey();//获取公钥
        $request_data = $ssl->decryptData($http_request,$public_key);//解密数据
        $request_data = json_decode($request_data,true);//转换json的数组

        //组装数据
        $diamonds = $request_data['diamonds'];
        $phone = $request_data['phone'];
        $user_info = $this->getAll('user',array('phone'=>$phone));
        $user_info = current($user_info);

        $data = array(
            'diamonds'=>$diamonds,
            'uid'=>$user_info['uid'],
            'type'=>1,//电信端添加
        );

        //添加到数据库
        $res = $this->insAndUpdate('diamonds','',$data);
        if($res['state']){
            $return = array('state'=>1,'msg'=>'充值成功！');
        }else{
            $return = array('state'=>0,'msg'=>'系统错误，充值失败！');
        }

        //输出json格式的结果
        echo json_encode($return);
    }

    //获取用户实名认证信息
    public function getUserRealInfo($http_request_data){
        $uid = $http_request_data['uid'];

        //获取用户信息
        $where['id'] = $uid;
        $user_info = D('user')->where($where)->select();

        $user_info = current($user_info);
        $idcard = $user_info['idcard'];
        $realname = $user_info['realname'];
        $collection_account = $user_info['collection_account'];

        if ($idcard && $realname && $collection_account) {
            $return = array('state'=>1,'msg'=>'该用户已实名认证！');
        }else{
            $return = array('state'=>0,'msg'=>'该用户未实名认证！');
        }

        return $return;
    }




}