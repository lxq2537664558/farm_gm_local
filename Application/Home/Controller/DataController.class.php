<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class DataController extends BaseController {

    //安卓统计
    public function dataAndorid(){
        $this->display();
    }

    //IOS统计
    public function iosIOS(){
        $this->display();
    }
}