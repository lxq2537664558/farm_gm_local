<?php
namespace Home\Controller;
use Think\Controller;
class DownloadController extends Controller {
    //下载界面
    public function downloadGame(){
        //移动设备浏览，则切换模板
        if (ismobile()) {
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);//全部变成小写字母

            //分别进行判断
            if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
                $type = 'ios';
                $download_url = 'http://www.dafuvip.com/eUZfmq';
            }

            if (strpos($agent, 'android')) {
                $type = 'android';
                $download_url = 'http://www.dafuvip.com/vy6ZRz';
            }
//var_dump($download_url,$type);
//            die;
//            $this->redirect($download_url);
            header('location:'.$download_url);
            die;
        }
        
    }

}