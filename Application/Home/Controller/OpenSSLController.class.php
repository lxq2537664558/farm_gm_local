<?php
namespace Home\Controller;
//OpenSSL控制器
class OpenSSLController{
    /* 获取私钥 */
    public function getPrivateKey(){
        $rsaPrivateKey = './Public/data/RSA/rsa_private_key.pem';//私钥文件的路径
        if(!file_exists($rsaPrivateKey)){
            echo '私钥文件路径错误！';die;
        }
        $content = file_get_contents($rsaPrivateKey);//获得文本
        $priKey = openssl_pkey_get_private($content);
        return $priKey;
    }

    /* 获取公钥 */
    public function getPublicKey(){
        $rsaPublicKey = './Public/data/RSA/rsa_public_key.pem';//公钥文件的路径
        if(!file_exists($rsaPublicKey)){
            echo '公钥文件路径错误！';die;
        }
        $content = file_get_contents($rsaPublicKey);//获得文本
        $priKey = openssl_pkey_get_public($content);
        return $priKey;
    }

    /**
     * 对数据签名，并使用base64加密
     * @param $data 需要加密的数据
     * @param $private_key 私钥
     * @return mixed 返回签名数据
     */
    public function signData($data,$private_key){
        openssl_sign($data,$sign,$private_key);
        $sign = base64_encode($sign);//使用base64加密方便传输
        openssl_free_key($private_key);//释放密钥资源
        return $sign;
    }

    /**
     * 用私钥加密数据
     * @param $data 需要加密的数据
     * @param $private_key 私钥
     * @return mixed
     */
    public function encryptData($data,$private_key){
        $res = openssl_private_encrypt($data,$sign,$private_key);
        openssl_free_key($private_key);//释放密钥资源
        if($res) {
            $return = base64_encode($sign);//使用base64加密方便传输
//            $return = urlencode($return);
            return $return;
        }
    }

    /**
     * 用公钥解密数据
     * @param $data 需要解密的数据
     * @param $public_key 公钥
     * @return mixed
     */
    public function decryptData($data,$public_key){
        $data = base64_decode($data);//base64解密数据
        openssl_free_key($public_key);//释放密钥资源
        $res = openssl_public_decrypt($data,$decryptData,$public_key);
        if($res) {
            return $decryptData;
        }
    }

    /**
     * 发送POST方式的HTTP请求接口数据
     * @param $url 目标http地址
     * @param $data 需要传输的数据
     * @return mixed
     */
    public function postData($url, $data)
    {
        $con  = curl_init();
        curl_setopt($con ,CURLOPT_URL,$url);//url
        curl_setopt($con, CURLOPT_POST,1);//POST方式
        curl_setopt($con, CURLOPT_RETURNTRANSFER,1);//翻译返回数据
        curl_setopt($con, CURLOPT_POSTFIELDS, $data);//发送的数据
        $info = curl_exec($con);//执行请求
        curl_close($con);
        return $info;
    }

    public function getData($url){
        //初始化
        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);
//        print_r($output);
        //释放curl句柄
        curl_close($ch);

        //打印获得的数据
        return $output;
    }

}