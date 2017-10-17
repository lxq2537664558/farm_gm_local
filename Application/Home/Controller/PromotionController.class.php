<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class PromotionController extends BaseController {

    //推广设置
    public function promotionSettings(){
        $this->display();
    }

    //查看推广码
    public function viewCode(){
        $post = I('post.');
        if($post){
            $uid = $post['uid'];
            $where['id'] = $uid;
            $user_info = $this->getAll('user',$where,'id','extension_code');

            //扫推广码→注册页面，下载页面下载前将下载的用户与推广码绑定
            //扫推广码→注册页面，下载页面下载前将下载的用户与推广码绑定//扫推广码→注册页面，下载页面下载前将下载的用户与推广码绑定
            //downloadGame() GET参数 uid下载者UID extension_code当前用户推广码
            $code = current($user_info);

            $url = 'http://'.$_SERVER['HTTP_HOST'].U('Home/Promotion/qrcode',array('data'=>urlencode('http://'.$_SERVER['HTTP_HOST'].U('Home/Common/userRegister',array('extension_code'=>$code,'qrcode_pass'=>1)))));
            $addUrl = 'http://'.$_SERVER['HTTP_HOST'].U('Home/Common/userRegister',array('extension_code'=>$code,'qrcode_pass'=>1));

            $json = array('code'=>$code,'addUrl'=>$addUrl,'url'=>$url);
            echo json_encode($json);
            die;
        }

        $this->display();
    }

    //处理二维码
    public function qrcode(){
        $data = I('get.data');
        $data = urldecode($data);

        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
//        $url = 'http://'.$_SERVER['HTTP_HOST'].$data;
//        $url = 123;
        $url = $data;
//        var_dump($url);die;
        $level=3;
        $size=4;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    //下载方法
    public function downloadGame(){
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        //扫推广码→下载页面，下载页面下载前将下载的用户与推广码绑定
        $extension_code = I('get.extension_code','');//推广码
        $download_uid = I('get.uid',0);//下载者的UID

        if(!$download_uid){
            $this->error('获取用户信息失败！');
            die;
        }

        if(!$extension_code){
            $this->error('下载失败，请获取推广码！');
            die;
        }

        //获取该推广码的用户uid
        $where['extension_code'] = $extension_code;
        $user_info = $this->getAll('user',$where);
        $user_info = current($user_info);
        $uid = $user_info['id'];

        //将下载者与推广用户uid绑定
        $bWhere['id'] = $download_uid;
        $bData['father_id'] = $uid;
        $res = $this->insAndUpdate('user',$bWhere,$bData);
        if($res['state']){
            //执行下载
            //执行下载
            //执行下载
            //执行下载
            //执行下载
            //执行下载
            $path = __ROOT__.'/Public/data/game.exe';//修改文件名
            //修改文件名
            //修改文件名
            //修改文件名
            //修改文件名
            //修改文件名
            //修改文件名
            $this->downloadFile($path);//下载文件方法
        }else{
            //报错
            $this->error('用户信息写入失败，请联系管理员！');
            die;
        }
    }

    //下载文件方法
    public function downloadFile($filename){
        if(!$filename){
            die;
        }
        //执行文件下载
        $file = realpath($filename);
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . basename($file));
        readfile($file);
    }
}