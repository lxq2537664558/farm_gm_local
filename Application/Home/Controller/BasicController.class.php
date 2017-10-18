<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class BasicController extends BaseController {

    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
        $return = json_decode($data,true);
        return $return;
    }

    //供奉设置-农场
    public function basicWorshipFarmland(){
//        1农场，2鱼塘,3伐木场，4矿场
        $post = I("post.");

        //获得当前服务器供奉期次
        $url = 'http://'.C('SERVER_IP').'/GetCurrentOblation';

        $params = 'Type=1';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
        $selectid = $lists['selectid']?$lists['selectid']:0;
        $this->assign('selectid',$selectid);

        //获得序列号,最大序列号，且当前期次未完结
        $sql = 'select max(serial) from worship where state = 0 and area = 1';
        $mRes = D('worship')->query($sql);
        $temp_sid = $mRes[0]['max(serial)'];
        //最大的序列号
        if($temp_sid){
            $max_sid = $temp_sid;
        }else{
            //如果没有则认为是第一期或者最大期次+1
            $sql = 'select max(serial) from worship where state = 1 and area = 1';
            $mRes = D('worship')->query($sql);
            $temp_sid = $mRes[0]['max(serial)'];
            $max_sid = $temp_sid+1;
        }
        $this->assign('max_sid',$max_sid);

        if($post){
            $data = array(
                'name'=>$post['name'],
                'number'=>$post['number'],
                'day'=>$post['day'],
                'area'=>1,
                'serial'=>$post['max_sid'],
            );

            $this->insAndUpdate('worship','',$data);
            $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            die;
        }

        //获取收盘价        
        $url = 'http://'.C('SERVER_IP').'/GetClosePrice';
        $lists = $this->getHTTPData($url);
        $this->assign('close_price',$lists['items']);

        //获取供奉清单
        $where['area'] = 1;
        $worship = $this->getAll('worship',$where,'','','id');
        $this->assign('worship',$worship);

        //获取物品清单
        $products = $this->getAll('product');
        $this->assign('products',$products);

        $this->display();
    }

    //供奉请求接口 修改设置方法
    public function worshipSettings(){
        //查询数据
        $area = I('get.type',1);
        $method = I('get.method','basicWorshipFarmland');

        $current_sid = I('get.sid');
        $sid = $current_sid + 1;

        $where['area'] = $area;
        $where['serial'] = $current_sid;
//        $sets = $this->getAll('worship',$where,'','','',0,1);
        $sets = $this->getAll('worship',$where);

        if($sets){
            //没有数据则不请求接口
            $day = 0;

            foreach ($sets as $v){
                $names = explode(',',str_replace('，',',',$v['name']));
                $numbers = explode(',',str_replace('，',',',$v['number']));

                $all_name[] = array('data'=>array($names[0],$numbers[0],$names[1],$numbers[1],$names[2],$numbers[2]));
//                $all_name[] = array($names[0],$numbers[0],$names[1],$numbers[1],$names[2],$numbers[2]);
                $day = $v['day']>$day?$v['day']:$day;
            }

            $param = array(
//            'OblationData'=>array(array('data'=>array(101,100,102,100,103,100)),array('data'=>array(101,100,102,100,103,100))),
                'OblationData'=>$all_name,
                'Type'=>$area,
                'Sid'=>$sid,//序列号
                'day'=>$day,//int 天数[默认为该组第一个的天数]
            );

            //修改状态
            $where2['serial'] = $current_sid;
            $where2['area'] = $area;
            $data2['state'] = 1;
            D('worship')->where($where2)->save($data2);//修改数据

            $params = json_encode($param);
//            var_dump($params);die;
//            $params = base64_encode(serialize($param));
//            $params = base64_encode($param);
            $url = 'http://'.C('SERVER_IP').'/InsertOblation';


//            $params = 'data='.$data;
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

//            var_dump(base64_decode($params));
//            var_dump($url);
//            die;
            $lists = $this->getHTTPData($url);
//            var_dump($lists);die;
        }


//        var_dump(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$method));die;

        if($lists['ret'] == 1){
            $this->success('操作成功！',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$method));
        }else{
            $this->error('操作失败！');
        }
    }

    //供奉设置-鱼塘
    public function basicWorshipFishpond(){
//        1农场，2鱼塘,3伐木场，4矿场
        $post = I("post.");

        //获得当前服务器供奉期次
        $url = 'http://'.C('SERVER_IP').'/GetCurrentOblation';

        $params = 'Type=2';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
        $selectid = $lists['selectid']?$lists['selectid']:0;
        $this->assign('selectid',$selectid);

        //获得序列号,最大序列号，且当前期次未完结
        $sql = 'select max(serial) from worship where state = 0 and area = 2';
        $mRes = D('worship')->query($sql);
        $temp_sid = $mRes[0]['max(serial)'];
        //最大的序列号
        if($temp_sid){
            $max_sid = $temp_sid;
        }else{
            //如果没有则认为是第一期或者最大期次+1
            $sql = 'select max(serial) from worship where state = 1 and area = 2';
            $mRes = D('worship')->query($sql);
            $temp_sid = $mRes[0]['max(serial)'];
            $max_sid = $temp_sid+1;
        }
//        var_dump($mRes,$max_sid);
//        var_dump($max_sid);
        $this->assign('max_sid',$max_sid);

        if($post){
//            var_dump($post);die;
            $data = array(
                'name'=>$post['name'],
                'number'=>$post['number'],
                'day'=>$post['day'],
                'area'=>2,
                'serial'=>$post['max_sid'],
            );
//            var_dump($post);die;
            $this->insAndUpdate('worship','',$data);
            $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            die;
        }

        //获取收盘价
        $url = 'http://'.C('SERVER_IP').'/GetClosePrice';
        $lists = $this->getHTTPData($url);
        $this->assign('close_price',$lists['items']);

        //获取供奉清单
        $where['area'] = 2;
        $worship = $this->getAll('worship',$where,'','','id');
        $this->assign('worship',$worship);

        //获取物品清单
        $products = $this->getAll('product');
        $this->assign('products',$products);
//        var_dump($worship);

        $this->display();
    }

    //供奉设置-伐木场
    public function basicWorshipForest(){
//        1农场，2鱼塘,3伐木场，4矿场
        $post = I("post.");

        //获得当前服务器供奉期次
        $url = 'http://'.C('SERVER_IP').'/GetCurrentOblation';

        $params = 'Type=3';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
        $selectid = $lists['selectid']?$lists['selectid']:0;
        $this->assign('selectid',$selectid);

        //获得序列号,最大序列号，且当前期次未完结
        $sql = 'select max(serial) from worship where state = 0 and area = 3';
        $mRes = D('worship')->query($sql);
        $temp_sid = $mRes[0]['max(serial)'];
        //最大的序列号
        if($temp_sid){
            $max_sid = $temp_sid;
        }else{
            //如果没有则认为是第一期或者最大期次+1
            $sql = 'select max(serial) from worship where state = 1 and area = 3';
            $mRes = D('worship')->query($sql);
            $temp_sid = $mRes[0]['max(serial)'];
            $max_sid = $temp_sid+1;
        }
//        var_dump($mRes,$max_sid);
//        var_dump($max_sid);
        $this->assign('max_sid',$max_sid);

        if($post){
//            var_dump($post);die;
            $data = array(
                'name'=>$post['name'],
                'number'=>$post['number'],
                'day'=>$post['day'],
                'area'=>3,
                'serial'=>$post['max_sid'],
            );
//            var_dump($post);die;
            $this->insAndUpdate('worship','',$data);
            $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            die;
        }

        //获取收盘价
        $url = 'http://'.C('SERVER_IP').'/GetClosePrice';
        $lists = $this->getHTTPData($url);
        $this->assign('close_price',$lists['items']);

        //获取供奉清单
        $where['area'] = 3;
        $worship = $this->getAll('worship',$where,'','','id');
        $this->assign('worship',$worship);

        //获取物品清单
        $products = $this->getAll('product');
        $this->assign('products',$products);
//        var_dump($worship);

        $this->display();
    }

    //供奉设置-矿场
    public function basicWorshipMine(){
//        1农场，2鱼塘,3伐木场，4矿场
        $post = I("post.");

        //获得当前服务器供奉期次
        $url = 'http://'.C('SERVER_IP').'/GetCurrentOblation';

        $params = 'Type=4';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
        $selectid = $lists['selectid']?$lists['selectid']:0;
        $this->assign('selectid',$selectid);

        //获得序列号,最大序列号，且当前期次未完结
        $sql = 'select max(serial) from worship where state = 0 and area = 4';
        $mRes = D('worship')->query($sql);
        $temp_sid = $mRes[0]['max(serial)'];
        //最大的序列号
        if($temp_sid){
            $max_sid = $temp_sid;
        }else{
            //如果没有则认为是第一期或者最大期次+1
            $sql = 'select max(serial) from worship where state = 1 and area = 4';
            $mRes = D('worship')->query($sql);
            $temp_sid = $mRes[0]['max(serial)'];
            $max_sid = $temp_sid+1;
        }
//        var_dump($mRes,$max_sid);
//        var_dump($max_sid);
        $this->assign('max_sid',$max_sid);

        if($post){
//            var_dump($post);die;
            $data = array(
                'name'=>$post['name'],
                'number'=>$post['number'],
                'day'=>$post['day'],
                'area'=>4,
                'serial'=>$post['max_sid'],
            );
//            var_dump($post);die;
            $this->insAndUpdate('worship','',$data);
            $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));
            die;
        }

        //获取收盘价
        $url = 'http://'.C('SERVER_IP').'/GetClosePrice';
        $lists = $this->getHTTPData($url);
        $this->assign('close_price',$lists['items']);
        
        //获取供奉清单
        $where['area'] = 4;
        $worship = $this->getAll('worship',$where,'','','id');
        $this->assign('worship',$worship);

        //获取物品清单
        $products = $this->getAll('product');
        $this->assign('products',$products);
//        var_dump($worship);

        $this->display();
    }

    //礼包设置
    public function basicPacksSettings(){
        $this->display();
    }

    //公告设置
    public function basicAnnounceSettings(){
        $this->display();
    }

    //产出设置
    public function basicOutputSettings(){
        $this->display();
    }

    //成就设置
    public function basicAchievementSettings(){
        $this->display();
    }

    //狗粮兑换设置
    public function basicDogfoodSettings(){
        $this->display();
    }

    //产出表
    public function basicOutputList(){
        $url = 'http://'.C('SERVER_IP').'/GetTable';

        $params = 'table=ProductTypeTable';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;


        $lists = $this->getHTTPData($url);
//        var_dump($lists);
//        die;
        $items = $lists['items'];
        $this->assign('items',$items);
        $this->display();
    }

    //产出表1
    public function basicOutputList1(){
        $url = 'http://'.C('SERVER_IP').'/GetTable';

        $params = 'table=ProductProbabilityTable1';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;


        $lists = $this->getHTTPData($url);
//        var_dump($lists['items'][0]);//[0]['lv1LandNumber']);
//        die;
        $items = $lists['items'];
        $items = $this->sortInfoById($items,'id');
        ksort($items);

        $this->assign('items',$items);
        $this->display();
    }

    //产出表2
    public function basicOutputList2(){
        $url = 'http://'.C('SERVER_IP').'/GetTable';

        $params = 'table=ProductProbabilityTable2';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;


        $lists = $this->getHTTPData($url);
//        var_dump($lists);
//        die;
        $items = $lists['items'];
        $items = $this->sortInfoById($items,'id');
        ksort($items);

        $this->assign('items',$items);
        $this->display();
    }

    //产出表3
    public function basicOutputList3(){
        $url = 'http://'.C('SERVER_IP').'/GetTable';

        $params = 'table=ProductProbabilityTable3';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;


        $lists = $this->getHTTPData($url);
//        var_dump($lists);
//        die;
        $items = $lists['items'];
        $items = $this->sortInfoById($items,'id');
        ksort($items);

        $this->assign('items',$items);
        $this->display();
    }



    //编辑产出表
    public function editOutputList(){
        $table = 'ProductTypeTable';

        $post = I('post.');
        $mid = $post['mid'];

//        var_dump($post);

        $unset = array('mid','area');
        foreach ($unset as $v){
            unset($post[$v]);
        }

        $url = 'http://'.C('SERVER_IP').'/ModifyTable';

        $params = 'table='.$table.'&_id='.$mid;
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;


        foreach ($post as $key=>$val){
            $url .= '&'.$key.'='.$val;
        }

        $lists = $this->getHTTPData($url);
//        var_dump($lists);
        echo 1;
        die;
    }

    //编辑产出表1
    public function editOutputList1(){
        $table = 'ProductProbabilityTable1';

        $post = I('post.');
        $mid = $post['mid'];

        $unset = array('mid','area');
        foreach ($unset as $v){
            unset($post[$v]);
        }

        //中括号
        $Bracket = array('lv1LandNumber','lv2LandNumber','lv3LandNumber');

//        var_dump($post);
        $url = 'http://'.C('SERVER_IP').'/ModifyTable?';

        $params = 'table='.$table.'&_id='.$mid;

        foreach ($post as $key=>$val){
            if(in_array($key,$Bracket)){
                $params .= '&' . $key . '=[' . $val.']';
            }else {
                $params .= '&' . $key . '=' . $val;
            }
        }

        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;
//        var_dump($url);die;
        $lists = $this->getHTTPData($url);
//        var_dump($lists);
        echo 1;
        die;
    }

    //编辑产出表2
    public function editOutputList2(){
        $table = 'ProductProbabilityTable2';

        $post = I('post.');
        $mid = $post['mid'];

        $unset = array('mid','area');
        foreach ($unset as $v){
            unset($post[$v]);
        }

        //中括号
        $Bracket = array('lv1LandMainNumber','lv1LandMinorNumber','lv2LandMainNumber','lv2LandMinorNumber','lv3LandMainNumber','lv3LandMinorNumber');

        $url = 'http://'.C('SERVER_IP').'/ModifyTable';

        $params = 'table='.$table.'&_id='.$mid;

        foreach ($post as $key=>$val){
            if(in_array($key,$Bracket)){
                $params .= '&' . $key . '=[' . $val.']';
            }else {
                $params .= '&' . $key . '=' . $val;
            }
        }

        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;
        $lists = $this->getHTTPData($url);
        echo 1;
        die;
    }

    //编辑产出表3
    public function editOutputList3(){
        $table = 'ProductProbabilityTable3';

        $post = I('post.');
        $mid = $post['mid'];

        $unset = array('mid','area');
        foreach ($unset as $v){
            unset($post[$v]);
        }

        //中括号
        $Bracket = array('lv1LandMainNumber','lv1LandMinor1Number','lv1LandMinor2Number','lv2LandMainNumber','lv2LandMinor1Number','lv2LandMinor2Number','lv3LandMainNumber','lv3LandMinor1Number','lv3LandMinor2Number');

        $url = 'http://'.C('SERVER_IP').'/ModifyTable';

        $params = 'table='.$table.'&_id='.$mid;

        foreach ($post as $key=>$val){
            if(in_array($key,$Bracket)){
                $params .= '&' . $key . '=[' . $val.']';
            }else {
                $params .= '&' . $key . '=' . $val;
            }
        }

        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;
        $lists = $this->getHTTPData($url);
        echo 1;
        die;
    }

    //礼包
    public function basicPacks(){
        $gifts = $this->getAll('gifts');
        //剩余数量-未使用的CDK
        $sql = 'select gid,count(*)as count from gift_cdk where state = 0 GROUP BY gid';
        $res = D('gift_cdk')->query($sql);
        $res = $this->sortInfoById($res,'gid','count');
        foreach ($gifts as $k=>$v){
            $gifts[$k]['left_num'] = $res[$v['id']]?$res[$v['id']]:0;
        }

        $this->assign('gifts',$gifts);
        $this->display();
    }

    //添加/编辑礼包
    public function basicAddPacks(){
        $post = I('post.');
        $id = I('get.id',0);
        $id?$where['id'] = $id:'';
        if($post){
            set_time_limit(0);
            ini_set('memory_limit','1024M');
            $post['gift_id'] = str_replace('，',',',$post['gift_id']);
            $post['gift_number'] = str_replace('，',',',$post['gift_number']);
            $res = $this->insAndUpdate('gifts',$where,$post);

            if($res['state']){
                $string = join('',range('a','z')).join('',range('A','Z')).join('',range(0,9));//编码string
                $gid = $res['id']?$res['id']:$id;

                //查询CDK是否已经生成过
                $cWhere['gid'] = $gid;
                $cdk_exists = $this->getAll('gift_cdk',$cWhere);

                if($cdk_exists){
//                    var_dump($post['number'],count($cdk_exists));die;
                    if($post['number']>count($cdk_exists)){
                        $cdk_number = $post['number'] - count($cdk_exists);
                        //生成CDK
                        for ($i = 1; $i <= $cdk_number; $i++) {
                            $temp_str = '';
                            for ($j = 1; $j <= 11; $j++) {
                                $temp_number = mt_rand(0, 61);
                                $temp_str .= $string{$temp_number};
                            }
                            $data[] = array('key_code' => $temp_str, 'gid' => $gid);
                            unset($temp_str);
                        }
                        D('gift_cdk')->addAll($data);
                    }
                }else {
                    //生成CDK
                    $cdk_number = $post['number'];//礼包数量
//                    var_dump($cdk_number);die;
                    for ($i = 1; $i <= $cdk_number; $i++) {
                        $temp_str = '';
                        for ($j = 1; $j <= 11; $j++) {
                            $temp_number = mt_rand(0, 61);
                            $temp_str .= $string{$temp_number};
                        }
                        $data[] = array('key_code' => $temp_str, 'gid' => $gid);
                        unset($temp_str);
                    }
                    D('gift_cdk')->addAll($data);
                }

                unset($data);
                $this->success('操作成功！',U('Home/Basic/basicPacks'));
                die;
            }else{
                $this->error('系统错误，操作失败！');
                die;
            }
        }else{
            if($id){
                $info = $this->getAll('gifts',$where);
                $info = current($info);
                $this->assign('info',$info);
            }

            //物品列表
            $products = $this->getAll('product');
            $this->assign('products',$products);

            $this->assign('url',U('Home/Basic/basicAddPacks',array('id'=>$id)));
            $this->display();
        }
    }

    //查看礼包
    public function viewPacks(){
        $id = I('get.id',0);
        $where['id'] = $id;
        $gWhere['gid'] = $id;
        if(!$id){
            $this->error('系统错误，礼包id丢失！');
            die;
        }

        $cdks = $this->getAll('gift_cdk',$gWhere);
        $this->assign('cdks',$cdks);
        $info = $this->getAll('gifts',$where);
        $info = current($info);
        $this->assign('info',$info);
        $this->display();
    }

    //改变礼包状态
    public function changePacksState(){
        $id = I('get.id',0);
        $where['id'] = $id;
        $state = I('get.state',0);
        if(!$id){
            echo json_encode(array('state'=>0,'msg'=>'系统错误，礼包id丢失！'));
            die;
        }

        //礼包状态
        $data['state'] = $state;
        $res = $this->insAndUpdate('gifts',$where,$data);

        if($res['state']){
            echo json_encode(array('state'=>1,'msg'=>'修改成功！'));
            die;
        }else{
            echo json_encode(array('state'=>0,'msg'=>'修改失败！'));
            die;
        }
    }

    //邮件设置
    public function basicMail(){
        $mails = $this->getAll('email');

        $this->assign('mails',$mails);
        $this->display();
    }

    //添加/编辑邮件
    public function basicAddMail(){
        $post = I('post.');
        $id = I('get.id',0);

        if($post){
            $lenth = strlen($post['content']);
            if($lenth >= 65530){
                $this->error('文章最长不能超过65530个字节');
            }

            $id?$where['id'] = $id:'';
            $res = $this->insAndUpdate('email',$where,$post);
//            $res = $this->insAndUpdate('email',$where,$post,'',1);
//            echo D('email')->_sql();
//            die;
            if($res['state']){
                $this->redirect(U('Home/Basic/basicMail'));
                die;
            }else {
                $this->error('系统错误，操作失败！');
                die;
            }
        }else{
            if($id){//编辑
                $where['id'] = $id;
                $info = $this->getAll('email',$where);
                $info = current($info);
                //处理特殊字符
                $this->assign('info',$info);
            }

            //物品列表
            $products = $this->getAll('product');
            $this->assign('products',$products);

            $this->assign('url',U('Home/Basic/basicAddMail',array('id'=>$id)));
            $this->display();
        }
    }

    //删除邮件
    public function deleteMail(){
        $id = I('get.id',0);
        $where['id'] = $id;
        $res = $this->deleteInfo('email',$where);
        if($res['state']){
            $this->redirect(U('Home/Basic/basicMail'));
            die;
        }else {
            $this->error('系统错误，操作失败！');
            die;
        }
    }

    //发送邮件
    public function sendMail(){
        $id = I('get.id',0);
        $where['id'] = $id;
        $info = $this->getAll('email',$where);
        $info = current($info);

//        var_dump($info['content']);
        $info['content'] = strip_tags($info['content']);
//        $info['content'] = urldecode($info['content']);
//        var_dump($info['content']);die;
        $url = 'http://'.C('SERVER_IP').'/SendNotice';

        $params = 'Title='.$info['title'].'&Text='.$info['content'].'&time='.time();

        //id-数量，id-数量 的格式
        $gift_id = explode(',',$info['item_ids']);
        $gift_number = explode(',',$info['item_number']);
        $item = '';
        foreach ($gift_id as $k=>$v){
            $item .= $v.','.$gift_number[$k].',';
        }
        $params .= '&item='.rtrim($item,',');

        if($info['uid']){
            $params .= '&uidVal='.$info['uid'];
        }

//        var_dump($params);
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;
//        var_dump(base64_decode('VGl0bGU954Ku5ZOl6K+05LqG5LuA5LmI77yfJlRleHQ95YaF5a655piv5LuA5LmI5YaF5a655piv5LuA5LmI5YaF5a655piv5LuA5LmIJnRpbWU9MTUwNTgxNDc4OCZpdGVtPTIwNCwx'));
//        echo '<br/>';
//var_dump($url);die;
        $lists = $this->getHTTPData($url);
//        var_dump($lists);die;
        if($lists['ret'] == 1){
            $this->success('发送成功！');
            die;
        }else{
            $this->error('发送失败!');
            die;
        }
    }

    //发行量设置
    public function basicPublic(){
        $url = 'http://'.C('SERVER_IP').'/GetReleaseInfo';
        $lists = $this->getHTTPData($url);
//        var_dump($url);
//        foreach ($lists['infos'] as $k=>$v){
////            var_dump($v['leftNum']);
//            $temp = $v['leftNum'];
////            var_dump($temp);
//            $lists['infos'][$k]['leftNum2'] = $temp;
//            $lists['infos'][$k]['leftNum'] = 1-(float)$temp;
//        }
//        echo '<pre>';
//        print_r($lists['infos']);
//        echo '</pre>';
//die;
        $info = $this->sortInfoById($lists['infos'],'itemId');

        ksort($info);
        $this->assign('info',$info);

        $products = $this->getAll('product','','pid','name');

//        var_dump($products);
        $this->assign('products',$products);

        $this->display();
    }

    //编辑发行量
    public function publicEdit(){
        $post = I('post.');
        $id = I('get.id',0);
        $where['id'] = $id;

        if($post){
//            var_dump($post);
            $url = 'http://'.C('SERVER_IP').'/SetReleaseCount';

            $params = 'itemId='.$post['itemId'].'&num='.$post['releaseCount'];
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);
            if($lists['ret'] == 1){
                $this->success('设置成功！',U('Home/Basic/basicPublic'));
                die;
            }else{
                $this->success('设置失败！');
                die;
            }
//            var_dump($lists);
        }else{
            $url = 'http://'.C('SERVER_IP').'/GetReleaseInfo';
            $lists = $this->getHTTPData($url);
            $info = $lists['infos'];
            $info = $this->sortInfoById($info,'itemId');
            $this->assign('info',$info[$id]);
//            var_dump($info);
            $products = $this->getAll('product','','pid','name');
            $this->assign('products',$products);
            $this->assign('url',U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,array('id'=>$id)));
            $this->display();
        }
    }

    //删除未提交的供奉
    public function deleteWorship(){
        $actions = array(0,'basicWorshipFarmland','basicWorshipFishpond','basicWorshipForest','basicWorshipMine');

        $id = I('get.id',0);
        $area = I('get.area',1);
        if(!$id){
            $this->error('非法操作，参数丢失！');
            die;
        }

        $where['id'] = $id;
        $res = $this->deleteInfo('worship',$where);
        if($res['state']){
            $this->redirect(U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$actions[$area]));
            die;
        }else{
            $this->error('系统错误，删除失败！');
            die;
        }
    }
}