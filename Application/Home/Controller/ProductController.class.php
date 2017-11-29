<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class ProductController extends BaseController {
    //获取HTTP请求结果
    public function getHTTPData($url){
        $openssl = new OpenSSLController();
        $data = $openssl->getData($url);
        $return = json_decode($data,true);
        return $return;
    }

    //产品列表
    public function index(){
        $url = 'http://'.C('SERVER_IP').'/GetTradeItemList';

        $params = 'index=0&num=50';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        //gm_num  gm的数量
        //num 其他玩家的数量
//        var_dump($url);die;
        $products = $this->getHTTPData($url);


        $this->assign('products',$products['items']);

        $this->display();
    }

    public function downProductList(){
        $url = 'http://'.C('SERVER_IP').'/GetTradeItemList';

        $params = 'index=0&num=50';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $products = $this->getHTTPData($url);

        $products = $products['items'];
        $title = array('id','产品名称','初始价格','状态');
//        var_dump($title);die;
        foreach ($products as $v){
            $temp = array();
            foreach ($v as $val){
                $temp[] = $val;
            }
            $temp[] = '交易所产品';
            $data[] = $temp;
        }

        $this->downLoadExcel('ProductList',$title,$data);
    }

    public function downPendingOrder(){
        $page = I('get.page', 1);
        $pageSize = 20;
        $start = $pageSize * ($page - 1);

        $url = 'http://'.C('SERVER_IP').'/GetTradeRecord';

        $params = 'index=' . $start . '&num=' . $pageSize . '&sort=price&type=2&sort=-startTime';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);

        $trade_type = array('买','卖');
        $trade_state = array('挂单','完成','撤销');

        foreach ($lists['trades'] as $v){
//                var_dump($v['userId']);
            $data[] = array(
                $v['userId'],
                $v['itemId'],
                $v['itemName'],
                $trade_type[$v['type']],
                $v['entrustNum'],
                $v['price'],
                $v['startTime']?date('Y-m-d H:i:s',$v['startTime']):'-',
                $v['tradeTime']?date('Y-m-d H:i:s',$v['tradeTime']):'-',
                $trade_state[$v['state']],
                $v['tradeNum'],//数量
                join(',',$v['tradeUserId']),
            );
        }

        $title = array('用户id',	'产品id',	'产品名称',	'挂单类型',	'挂单数量',	'挂单价格',	'挂单时间',	'结束时间',	'挂单状态',	'成交数量',	'成交用户id');
        $this->downLoadExcel('PendingOrder',$title,$data);
    }

    //挂单列表
    public function pendingOrder(){
        $post = I('post.');
        if($post) {
            $page = I('post.page', 1);
            $pageSize = 20;
            $start = $pageSize * ($page - 1);
//        $end = $start+$pageSize-1;//-1？？？
            $url = 'http://'.C('SERVER_IP').'/GetTradeRecord';

            $params = 'index=' . $start . '&num=' . $pageSize . '&sort=price&type=2&sort=-startTime';
            $params = $this->publicEncrypt($params);
            $url .= '?data='.$params;

            $lists = $this->getHTTPData($url);

            $trade_type = array('买','卖');
            $trade_state = array('挂单','完成','撤销');

            foreach ($lists['trades'] as $v){
                $data[$v['userId']] = array(
                    'uid'=>$v['userId'],
                    'itemId'=>$v['itemId'],
                    'itemName'=>$v['itemName'],
                    'type'=>$trade_type[$v['type']],
                    'entrustNum'=>$v['entrustNum'],
                    'price'=>$v['price']?round($v['price'],4):0,
                    'startTime'=>$v['startTime']?date('Y-m-d H:i:s',$v['startTime']):'-',
                    'tradeTime'=>$v['tradeTime']?date('Y-m-d H:i:s',$v['tradeTime']):'-',
                    'state'=>$trade_state[$v['state']],
                    'tradeNum'=>$v['tradeNum'],//数量
                    'tradeUserId'=>join(',',$v['tradeUserId']),
                );
            }

            ksort($data);
            $data = array_values($data);
            $json = array('data'=>$data,'page'=>array('page'=>$page,'totalPage'=>ceil($lists['totalNum']/$pageSize)));
            echo json_encode($json);
            die;
        }

        $this->display();
    }

    //统计中心
    public function statisticsCenter(){
        $url = 'http://'.C('SERVER_IP').'/GetTradeDetail';

        $params = 'index=0&num=50&sort=-price';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
//        var_dump($lists);die;
        $data = array();
        foreach ($lists['details'] as $k=>$v){
            $data[$v['itemId']] = $v;
        }
        ksort($data);

        $this->assign('products',$data);

        $this->display();
    }

    public function downStatisticsCenter(){
        $url = 'http://'.C('SERVER_IP').'/GetTradeDetail';

        $params = 'index=0&num=50&sort=-price';
        $params = $this->publicEncrypt($params);
        $url .= '?data='.$params;

        $lists = $this->getHTTPData($url);
        $data = array();
        $fields = array('itemId',	'itemName',	'totalNum',	'tradeNum',	'finishNum',	'buyNum',	'sellNum',	'charge',);
        ksort($lists['details']);
        foreach ($lists['details'] as $k=>$v){
            $temp = array();
            foreach ($fields as $val){
                $temp[] = $v[$val];
            }
            $data[] = $temp;
        }

        $title = array('用户id',	'产品名称',	'累计交易',	'累计挂单',	'累计完成',	'累计买入',	'累计卖出',	'累计手续费');
        $this->downLoadExcel('StatisticsCenter',$title,$data);
    }

    //新增产品
    public function addProduct(){
        $this->display();
    }

    //编辑产品
    public function editProduct(){
        $this->display();
    }
}