<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
use Home\Controller\OpenSSLController;
class IndexController extends BaseController {
    public function index(){
//        var_dump($_SESSION);
//        $post = I('post.');
//
//        $username = $post['username'];
//        $password = $post['password'];
//
//        if(!$username || !$password){
//            $this->error('用户名或密码不能为空！');
//            die;
//        }
//
//        $where['username'] = $username;
//        $where['password'] = md5($password);
//
//        $is_true = $this->getAll('user',$where);
//        if(!$is_true){
//            //登录失败-用户名、密码错误
//            $return = array('state'=>0,'msg'=>'用户名或密码错误！','data'=>NULL);
//            return $return;
//        }else{
//            //用户被封号
//            $user_info = current($is_true);
//            if($user_info['state']){
//                $return = array('state'=>0,'msg'=>'用户被封号！','data'=>NULL);
//                return $return;
//            }
//
//            //如果存在登录时间不处理
//            if(!$user_info['login_time']) {
//                //否则为第一次登录，处理第一次登录时间,发送至接口
//                $where['id'] = $user_info['id'];
//                $login_time = time();
//                $data['login_time'] = $login_time;
//                $this->insAndUpdate('user', $where, $data);
//
//                $return = array('state'=>1,'msg'=>'登录成功！','data'=>array('login_time'=>$login_time),'id'=>$user_info['id']);
//                return $return;
//            }
//
//            $return = array('state'=>1,'msg'=>'登录成功！','data'=>array('id'=>$user_info['id']));
//            return $return;
//        }

        $this->display();
    }

    //OPENSSL加密解密测试
    public function test(){
        $openssl = new OpenSSLController();
        $private = $openssl->getPrivateKey();
        $public = $openssl->getPublicKey();
        echo '---获取公钥和私钥---';
        var_dump($private,$public);//获取公钥和私钥

        $data = '123456';
        $sign = $openssl->signData($data,$private);
        echo '<br/><br/><br/>---验证签名是否成功---';
        var_dump($sign);//验证签名是否成功

//        $data2 = 'abcdef';
//        $encodeData = $openssl->encryptData($data2,$private);
//        echo '<br/><br/><br/>---加密数据---';
//        var_dump($encodeData);//加密数据
//
//        $decodeData = $openssl->decryptData($encodeData,$public);
//        echo '<br/><br/><br/>---解密数据---';
//        var_dump($decodeData);//解密数据
    }

}