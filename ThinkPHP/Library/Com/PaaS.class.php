<?php
/**
 * PaaS平台接口
 * @author Jiang Chun Yi
 *
 * 配置文件参数：
 *		SSO_SIGN_KEY
 *		SSO_APP_KEY
 *		SSO_API
 */

namespace Com;
class PaaS {
	private $version = '1.0';
	private $appKey;
	private $signKey;	//加密签名

	public function __construct(){
		$this->signKey = C('SSO_SIGN_KEY');
        $this->appKey = C('SSO_APP_KEY');
	}

	/**
	 * 获取登陆Session
	 */
	public function getSessionID($username, $password){
		$arrParams = array(
			'method' => 'user.logon',
			'appKey' => $this->appKey,
			'v'		 => $this->version,
			'userName' => urlencode($username),
			'password' => urlencode($password),
			'messageFormat' => 'json'
		);

		$result = $this->sendQuery($arrParams);
		if(isset($result['code'])){
			return $result;	
		}
		else{
			//TokenID做Session存储
			$_SESSION['tokenid'] = $result['sessionId'];

			return $result['sessionId'];
		}
	}

	/**
	 * 获取用户信息
	 */
	public function getUser($token){
		$arrParams = array(
			'method' => 'user.info',
			'appKey' => $this->appKey,
			'v'		 => $this->version,
			'messageFormat' => 'json',
			'sessionId' => $token
		);
		$user = $this->sendQuery($arrParams);

		return $user;
	}
	
	/**
	 * 根据用户id获取用户信息
	 */
	public function getUserInfoByUid($uid, $token)
	{
	
	    $arrParams = array(
	            'method' => 'old.GetUserIconByUid',
	            'appKey' => $this->appKey,
			     'v'	 => $this->version,
			     'messageFormat' => 'json',
	            'sessionId' => $token,
	            'userCode' => $uid
	    );
	
	    $user = $this->sendQuery($arrParams);
		return $user;
	}

	/**
         * 调用sendQuery
         * @param type $arrParams
         * @return type
         */
        public function callSendQuery($arrParams){
            $arr = array(
                'appKey' => C('SSO_APP_KEY'),
                'v' => $this->version,
                'messageFormat' => 'json',
            );
            $sendArr = array_merge($arr, $arrParams);
            return $this->sendQuery($sendArr);
        }

        /**
	 * 发送接口请求
	 */
	private function sendQuery($arrParams){
        //获取查询参数
        $strParams = $this->getQueryString($arrParams);
        //$arrPostData = $this->getQueryData($arrParams);//post数组方式提交

        $this_header = array(
            "content-type: application/x-www-form-urlencoded; 
             charset=UTF-8"
        );
        $api = C('SSO_API'); 

        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);    //注意，毫秒超时一定要设置这个
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);  //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strParams);

        $data = curl_exec($ch); 
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_errno > 0){
            $result = array(
                'code' => -1,
                'msg' => 'sso timeout'
            );
        } 
		else{
            $result = json_decode($data, true);
        }

        return $result;
    }

	//生成接口参数字符串
	private function getQueryString($arrParams){
		$strQuery = '';
		foreach($arrParams as $k => $v){
			$strQuery .= $k . '=' . $v . '&';
		}

		//生成签名
		$strSign = $this->getSign($arrParams);

		$strQuery .= 'sign=' . $strSign;
		
		return $strQuery;
	}

	private function getQueryData($arrParams){
		//生成签名
		$strSign = $this->getSign($arrParams);
		$arrParams['sign'] = $strSign;

		return $arrParams;
	}

	//生成签名
	private function getSign($arrParams){
		ksort($arrParams);
		$strSignParams = $this->signKey; 
		foreach($arrParams as $k => $v){
			$strSignParams .= $k . $v;
		}
		$strSignParams .= $this->signKey;	
		$strSign = strtoupper(sha1($strSignParams));

		return $strSign;	
	}
	
	/////////////////////////积分相关接口////////////////
	/**
	 * 查询用户积分
	 * @param string $uid
	 */
	public function getCreditByUid($uid, $tokenid){
        $arrParams = array(
            'appKey' => $this->appKey,
            'method' => 'user.iscreditpass',
            'v'		 => $this->version,
            'messageFormat' => 'json',
			'uid' => $uid,
			'sessionId' => $tokenid
        );
        $result = $this->sendQuery($arrParams);

        return $result;
	}

	/**
	 * 设置用户已完成活动任务
	 */
	public function setCreditLog($action, $tokenid){
        $arrParams = array(
            'appKey' => $this->appKey,
            'method' => 'user.creditLog',
            'v'		 => $this->version,
            'messageFormat' => 'json',
			'action' => $action,
			'sessionId' => $tokenid
        );
        $result = $this->sendQuery($arrParams);

        return $result;
	}
	
	/**
	 * 联通账号登陆，获取联通用户信息
	 */
	public function bizcomLogin($token){
		$arrParams = array(
			'method' => 'bizcom.logon',
			'appKey' => $this->appKey,
			'v'		 => $this->version,
			'messageFormat' => 'json',
			'sessionId' => $token,
			'token' => $token,
		);
		//C('debug_bizcom',true);
		$user = $this->sendQuery($arrParams);

		return $user;
	}

	/**
	 * 刷新接口用户缓存
	 */
	public function refreshUser($token, $uid){
		$arrParams = array(
			'method' => 'user.Refresh',
			'appKey' => $this->appKey,
			'v'		 => $this->version,
			'messageFormat' => 'json',
			'sessionId' => $token,
			'uid' => $uid
		);
		$user = $this->sendQuery($arrParams);

		return $user;
	}
}
