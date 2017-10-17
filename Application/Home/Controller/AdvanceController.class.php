<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class AdvanceController extends BaseController {
    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
//        var_dump($data);
        $return = json_decode($data,true);
        return $return;
    }


    //金币充值
    public function goldRecharge(){
        $post = I('post.');
        if($post){
            $url = 'http://'.C('SERVER_IP').'/AddGold';

            $params = 'showId='.$post['showId'].'&num='.$post['num'];
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);

            if($lists['ret'] == 1){
                $user_info = session('AdminInfo');
                //记录到数据库
                $r_data = array(
                    'uid'=>$post['showId'],
                    'money'=>$post['num'],
                    'time'=>time(),
                    'type'=>2,
                    'admin_id'=>$user_info['uid'],
                );
                $res = $this->insAndUpdate('gold_record','',$r_data);
                if($res['state']){
                    $this->success('充值成功！',U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
                    die;
                }else{
                    $this->error('写入数据库失败！',U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
                    die;
                }
            }else{
                $this->error('充值失败！');
                die;
            }

        }else {
            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
            $this->display();
        }
    }

    //道具赠送（非交易所产品）
    public function propsGift(){
        $post = I('post.');
        if($post){
            $url = 'http://'.C('SERVER_IP').'/AddItem';

            $params = 'itemId='.$post['itemId'].'&showId='.$post['showId'].'&num='.$post['num'];
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
            $this->success('操作成功！',U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
        }else {
            //非交易所
            $json = '[{"id":1.0,"type":"种子","IconPath":"Texture/seed_icon/icon_seed1","land":1.0,"CostNumber":100.0,"growtime":1800.0,"directGrowUpConsume":5.0,"SinglePurchaseQuantity":1000.0,"price":200.0,"TradingWay":1.0,"":null},{"id":2.0,"type":"鱼饵","IconPath":"Texture/seed_icon/icon_seed2","land":2.0,"CostNumber":100.0,"growtime":1800.0,"directGrowUpConsume":25.0,"SinglePurchaseQuantity":1000.0,"price":500.0,"TradingWay":1.0,"":null},{"id":3.0,"type":"伐木卷","IconPath":"Texture/seed_icon/icon_seed3","land":3.0,"CostNumber":100.0,"growtime":1800.0,"directGrowUpConsume":50.0,"SinglePurchaseQuantity":500.0,"price":500.0,"TradingWay":1.0,"":null},{"id":4.0,"type":"炸药","IconPath":"Texture/seed_icon/icon_seed4","land":4.0,"CostNumber":100.0,"growtime":1800.0,"directGrowUpConsume":100.0,"SinglePurchaseQuantity":500.0,"price":1000.0,"TradingWay":1.0,"":null},{"id":201,"type":"加速符"},{"id":202,"type":"狗粮"}]';
            $json_array = json_decode($json,true);
//            echo '<pre>';
//            print_r($json_array);
//            echo '</pre>';
            $this->assign('json_array',$json_array);

            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
            $this->display();
        }
    }

    //产品充值
    public function productRecharge(){
        $post = I('post.');
        if($post){
            $url = 'http://'.C('SERVER_IP').'/AddItem';

            $params = 'itemId='.$post['itemId'].'&showId='.$post['showId'].'&num='.$post['num'];
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
            $this->success('操作成功！',U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
        }else {
            //交易所
            $json = '[{"id":101.0,"type":"水稻","IconPath":"Texture/farm_icon/icon_xm,Texture/Products/CropProduct/xiaomai1,Texture/Products/CropProduct/xiaomai2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":600.0,"exchangeBait":1500.0,"exchangeCutting":3000.0,"exchangeBomb":6000.0,"exchangeDogFood":300.0,"TradingWay":2.0},{"id":102.0,"type":"玉米","IconPath":"Texture/farm_icon/icon_ym,Texture/Products/CropProduct/yumi1,Texture/Products/CropProduct/yumi2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":300.0,"exchangeBait":750.0,"exchangeCutting":1500.0,"exchangeBomb":3000.0,"exchangeDogFood":150.0,"TradingWay":2.0},{"id":103.0,"type":"苹果","IconPath":"Texture/farm_icon/icon_pg,Texture/Products/CropProduct/pingguo1,Texture/Products/CropProduct/pingguo2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":120.0,"exchangeBait":300.0,"exchangeCutting":600.0,"exchangeBomb":1200.0,"exchangeDogFood":60.0,"TradingWay":2.0},{"id":104.0,"type":"草莓","IconPath":"Texture/farm_icon/icon_cm,Texture/Products/CropProduct/caomei1,Texture/Products/CropProduct/caomei2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":60.0,"exchangeBait":150.0,"exchangeCutting":300.0,"exchangeBomb":600.0,"exchangeDogFood":30.0,"TradingWay":2.0},{"id":105.0,"type":"南瓜","IconPath":"Texture/farm_icon/icon_ng,Texture/Products/CropProduct/nangua1,Texture/Products/CropProduct/nangua2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":25.0,"exchangeBait":62.5,"exchangeCutting":125.0,"exchangeBomb":250.0,"exchangeDogFood":12.5,"TradingWay":2.0},{"id":106.0,"type":"猕猴桃","IconPath":"Texture/farm_icon/icon_mht,Texture/Products/CropProduct/mihoutao1,Texture/Products/CropProduct/mihoutao2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":107.0,"type":"榴莲","IconPath":"Texture/farm_icon/icon_ll,Texture/Products/CropProduct/liulian1,Texture/Products/CropProduct/liulian2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":8.0,"exchangeBait":20.0,"exchangeCutting":40.0,"exchangeBomb":80.0,"exchangeDogFood":4.0,"TradingWay":2.0},{"id":108.0,"type":"人参","IconPath":"Texture/farm_icon/icon_rs,Texture/Products/CropProduct/renshen1,Texture/Products/CropProduct/renshen2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":5.0,"exchangeBait":12.5,"exchangeCutting":25.0,"exchangeBomb":50.0,"exchangeDogFood":2.5,"TradingWay":2.0},{"id":109.0,"type":"鲫鱼","IconPath":"Texture/farm_icon/icon_jy,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":250.0,"exchangeBait":625.0,"exchangeCutting":1250.0,"exchangeBomb":2500.0,"exchangeDogFood":125.0,"TradingWay":2.0},{"id":110.0,"type":"皮皮虾","IconPath":"Texture/farm_icon/icon_ppx,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":80.0,"exchangeBait":200.0,"exchangeCutting":400.0,"exchangeBomb":800.0,"exchangeDogFood":40.0,"TradingWay":2.0},{"id":111.0,"type":"帝王蟹","IconPath":"Texture/farm_icon/icon_dwx,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":20.0,"exchangeBait":50.0,"exchangeCutting":100.0,"exchangeBomb":200.0,"exchangeDogFood":10.0,"TradingWay":2.0},{"id":112.0,"type":"河豚","IconPath":"Texture/farm_icon/icon_ht,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":113.0,"type":"海马","IconPath":"Texture/farm_icon/icon_hm,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":7.0,"exchangeBait":17.5,"exchangeCutting":35.0,"exchangeBomb":70.0,"exchangeDogFood":3.5,"TradingWay":2.0},{"id":114.0,"type":"象拔蚌","IconPath":"Texture/farm_icon/icon_xbb,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":3.0,"exchangeBait":7.5,"exchangeCutting":15.0,"exchangeBomb":30.0,"exchangeDogFood":1.5,"TradingWay":2.0},{"id":115.0,"type":"乌木","IconPath":"Texture/farm_icon/icon_wm,Texture/Products/WoodProduct/wumu1,Texture/Products/WoodProduct/wumu2,Texture/Products/WoodProduct/wumu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":120.0,"exchangeBait":300.0,"exchangeCutting":600.0,"exchangeBomb":1200.0,"exchangeDogFood":60.0,"TradingWay":2.0},{"id":116.0,"type":"金丝楠木","IconPath":"Texture/farm_icon/icon_jsnm,Texture/Products/WoodProduct/nanmu1,Texture/Products/WoodProduct/nanmu2,Texture/Products/WoodProduct/nanmu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":117.0,"type":"紫檀木","IconPath":"Texture/farm_icon/icon_ztm,Texture/Products/WoodProduct/zitanmu1,Texture/Products/WoodProduct/zitanmu2,Texture/Products/WoodProduct/zitanmu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":5.0,"exchangeBait":12.5,"exchangeCutting":25.0,"exchangeBomb":50.0,"exchangeDogFood":2.5,"TradingWay":2.0},{"id":118.0,"type":"黄花梨","IconPath":"Texture/farm_icon/icon_hhl,Texture/Products/WoodProduct/huanghuali1,Texture/Products/WoodProduct/huanghuali2，Texture/Products/WoodProduct/huanghuali3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":3.0,"exchangeBait":7.5,"exchangeCutting":15.0,"exchangeBomb":30.0,"exchangeDogFood":1.5,"TradingWay":2.0},{"id":119.0,"type":"水晶","IconPath":"Texture/farm_icon/icon_ss,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":50.0,"exchangeBait":125.0,"exchangeCutting":250.0,"exchangeBomb":500.0,"exchangeDogFood":25.0,"TradingWay":2.0},{"id":120.0,"type":"和田玉","IconPath":"Texture/farm_icon/icon_hty,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":6.0,"exchangeBait":15.0,"exchangeCutting":30.0,"exchangeBomb":60.0,"exchangeDogFood":3.0,"TradingWay":2.0},{"id":121.0,"type":"钻石","IconPath":"Texture/farm_icon/icon_zs,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":2.0,"exchangeBait":5.0,"exchangeCutting":10.0,"exchangeBomb":20.0,"exchangeDogFood":1.0,"TradingWay":2.0}]';
            $json_array = json_decode($json,true);
//            var_dump($json_array);
            $this->assign('json_array',$json_array);

            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
            $this->display();
        }
    }

    //钻石充值
    public function diamondRecharge(){
        $post = I('post.');
        if($post){
            $url = 'http://'.C('SERVER_IP').'/AddDiamond';

            $params = 'showId='.$post['showId'].'&num='.$post['num'];
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;
//var_dump($url);die;

            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
            $this->success('操作成功！',U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
            die;
        }else {
            //交易所
//            $json = '[{"id":101.0,"type":"水稻","IconPath":"Texture/farm_icon/icon_xm,Texture/Products/CropProduct/xiaomai1,Texture/Products/CropProduct/xiaomai2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":600.0,"exchangeBait":1500.0,"exchangeCutting":3000.0,"exchangeBomb":6000.0,"exchangeDogFood":300.0,"TradingWay":2.0},{"id":102.0,"type":"玉米","IconPath":"Texture/farm_icon/icon_ym,Texture/Products/CropProduct/yumi1,Texture/Products/CropProduct/yumi2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":300.0,"exchangeBait":750.0,"exchangeCutting":1500.0,"exchangeBomb":3000.0,"exchangeDogFood":150.0,"TradingWay":2.0},{"id":103.0,"type":"苹果","IconPath":"Texture/farm_icon/icon_pg,Texture/Products/CropProduct/pingguo1,Texture/Products/CropProduct/pingguo2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":120.0,"exchangeBait":300.0,"exchangeCutting":600.0,"exchangeBomb":1200.0,"exchangeDogFood":60.0,"TradingWay":2.0},{"id":104.0,"type":"草莓","IconPath":"Texture/farm_icon/icon_cm,Texture/Products/CropProduct/caomei1,Texture/Products/CropProduct/caomei2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":60.0,"exchangeBait":150.0,"exchangeCutting":300.0,"exchangeBomb":600.0,"exchangeDogFood":30.0,"TradingWay":2.0},{"id":105.0,"type":"南瓜","IconPath":"Texture/farm_icon/icon_ng,Texture/Products/CropProduct/nangua1,Texture/Products/CropProduct/nangua2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":25.0,"exchangeBait":62.5,"exchangeCutting":125.0,"exchangeBomb":250.0,"exchangeDogFood":12.5,"TradingWay":2.0},{"id":106.0,"type":"猕猴桃","IconPath":"Texture/farm_icon/icon_mht,Texture/Products/CropProduct/mihoutao1,Texture/Products/CropProduct/mihoutao2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":107.0,"type":"榴莲","IconPath":"Texture/farm_icon/icon_ll,Texture/Products/CropProduct/liulian1,Texture/Products/CropProduct/liulian2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":8.0,"exchangeBait":20.0,"exchangeCutting":40.0,"exchangeBomb":80.0,"exchangeDogFood":4.0,"TradingWay":2.0},{"id":108.0,"type":"人参","IconPath":"Texture/farm_icon/icon_rs,Texture/Products/CropProduct/renshen1,Texture/Products/CropProduct/renshen2","area":1.0,"growTime":1800.0,"pice":1500.0,"directGrowUpConsume":50.0,"exchangeSeed":5.0,"exchangeBait":12.5,"exchangeCutting":25.0,"exchangeBomb":50.0,"exchangeDogFood":2.5,"TradingWay":2.0},{"id":109.0,"type":"鲫鱼","IconPath":"Texture/farm_icon/icon_jy,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":250.0,"exchangeBait":625.0,"exchangeCutting":1250.0,"exchangeBomb":2500.0,"exchangeDogFood":125.0,"TradingWay":2.0},{"id":110.0,"type":"皮皮虾","IconPath":"Texture/farm_icon/icon_ppx,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":80.0,"exchangeBait":200.0,"exchangeCutting":400.0,"exchangeBomb":800.0,"exchangeDogFood":40.0,"TradingWay":2.0},{"id":111.0,"type":"帝王蟹","IconPath":"Texture/farm_icon/icon_dwx,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":20.0,"exchangeBait":50.0,"exchangeCutting":100.0,"exchangeBomb":200.0,"exchangeDogFood":10.0,"TradingWay":2.0},{"id":112.0,"type":"河豚","IconPath":"Texture/farm_icon/icon_ht,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":113.0,"type":"海马","IconPath":"Texture/farm_icon/icon_hm,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":7.0,"exchangeBait":17.5,"exchangeCutting":35.0,"exchangeBomb":70.0,"exchangeDogFood":3.5,"TradingWay":2.0},{"id":114.0,"type":"象拔蚌","IconPath":"Texture/farm_icon/icon_xbb,Texture/Products/FishProduct/yutong1,Texture/Products/FishProduct/yutong2","area":2.0,"growTime":3600.0,"pice":1500.0,"directGrowUpConsume":100.0,"exchangeSeed":3.0,"exchangeBait":7.5,"exchangeCutting":15.0,"exchangeBomb":30.0,"exchangeDogFood":1.5,"TradingWay":2.0},{"id":115.0,"type":"乌木","IconPath":"Texture/farm_icon/icon_wm,Texture/Products/WoodProduct/wumu1,Texture/Products/WoodProduct/wumu2,Texture/Products/WoodProduct/wumu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":120.0,"exchangeBait":300.0,"exchangeCutting":600.0,"exchangeBomb":1200.0,"exchangeDogFood":60.0,"TradingWay":2.0},{"id":116.0,"type":"金丝楠木","IconPath":"Texture/farm_icon/icon_jsnm,Texture/Products/WoodProduct/nanmu1,Texture/Products/WoodProduct/nanmu2,Texture/Products/WoodProduct/nanmu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":15.0,"exchangeBait":37.5,"exchangeCutting":75.0,"exchangeBomb":150.0,"exchangeDogFood":7.5,"TradingWay":2.0},{"id":117.0,"type":"紫檀木","IconPath":"Texture/farm_icon/icon_ztm,Texture/Products/WoodProduct/zitanmu1,Texture/Products/WoodProduct/zitanmu2,Texture/Products/WoodProduct/zitanmu3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":5.0,"exchangeBait":12.5,"exchangeCutting":25.0,"exchangeBomb":50.0,"exchangeDogFood":2.5,"TradingWay":2.0},{"id":118.0,"type":"黄花梨","IconPath":"Texture/farm_icon/icon_hhl,Texture/Products/WoodProduct/huanghuali1,Texture/Products/WoodProduct/huanghuali2，Texture/Products/WoodProduct/huanghuali3","area":3.0,"growTime":7200.0,"pice":1500.0,"directGrowUpConsume":200.0,"exchangeSeed":3.0,"exchangeBait":7.5,"exchangeCutting":15.0,"exchangeBomb":30.0,"exchangeDogFood":1.5,"TradingWay":2.0},{"id":119.0,"type":"水晶","IconPath":"Texture/farm_icon/icon_ss,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":50.0,"exchangeBait":125.0,"exchangeCutting":250.0,"exchangeBomb":500.0,"exchangeDogFood":25.0,"TradingWay":2.0},{"id":120.0,"type":"和田玉","IconPath":"Texture/farm_icon/icon_hty,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":6.0,"exchangeBait":15.0,"exchangeCutting":30.0,"exchangeBomb":60.0,"exchangeDogFood":3.0,"TradingWay":2.0},{"id":121.0,"type":"钻石","IconPath":"Texture/farm_icon/icon_zs,,","area":4.0,"growTime":14400.0,"pice":1500.0,"directGrowUpConsume":400.0,"exchangeSeed":2.0,"exchangeBait":5.0,"exchangeCutting":10.0,"exchangeBomb":20.0,"exchangeDogFood":1.0,"TradingWay":2.0}]';
//            $json_array = json_decode($json,true);
////            var_dump($json_array);
//            $this->assign('json_array',$json_array);

            $this->assign('url', U(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));
            $this->display();
        }
    }

}