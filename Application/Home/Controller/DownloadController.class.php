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

    //显示行为
    public function showBehavior(){
        $monitor_login = session('monitor_login_info');
        if(!$monitor_login){
            session('monitor_login_info',NULL);
            die;
        }

        $pageSize = 50;
        $page = I('get.page',1);
        $pageshow = 3;

        $model = D('user_behavior');
        $behavior = $model->limit($pageSize)->page($page)->select();
        $count = $model->count();

        $pageInfo = array(
            'totalPage' => ceil($count/$pageSize),
            'page'		=> $page,
            'count'     => intval($count),
        );
        $pageInfo['back'] = (($pageInfo['page']-1)<=1)?1:$pageInfo['page']-1;
        $pageInfo['next'] = (($pageInfo['page']+1)>=$pageInfo['totalPage'])?$pageInfo['totalPage']:$pageInfo['page']+1;

        $pageInfo['start'] = (($pageInfo['page']-$pageshow)<=0)?1:$pageInfo['page']-$pageshow;
        $pageInfo['end'] = (($pageInfo['page']+$pageshow)>=$pageInfo['totalPage'])?$pageInfo['totalPage']:$pageInfo['page']+$pageshow;
        $pageInfo['pageSize'] = $pageSize?$pageSize:$this->pageSize;

        $current = current($behavior);
        $header = array_keys($current);

        $this->assign('header',$header);
        $this->assign('behavior',$behavior);
        $this->assign('page',$pageInfo);
        $this->display();
    }

    public function monitorLogin(){
        $logout = I('get.logout',0);
        if($logout){
            session('monitor_login_info',NULL);
        }else {
            $key = I('post.key', '');
            $key = md5($key);
            if ($key == 'e10adc3949ba59abbe56e057f20f883e') {
                session('monitor_login_info', 1);
                $this->redirect(U('Home/Download/showBehavior'));
            } else {
                session('monitor_login_info', NULL);
                echo '<form action="' . U('Home/Download/monitorLogin') . '" method="post"><input type="text" name="key" /><input type="submit" /></form>';
            }
        }
    }
}