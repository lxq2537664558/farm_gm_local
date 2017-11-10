<?php
return array(
    // 数据库常用配置
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'localhost', // 数据库服务器地址
    'DB_NAME' => 'farm_local', // 数据库名
    'DB_USER' => 'root', // 数据库用户名
    'DB_PWD' =>'olwn_N382loi',//数据库密码
    'DB_PORT' => 3306, // 数据库端口
    'DB_PREFIX' => '', // 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
    'DB_CHARSET' => 'utf8', // 数据库编码
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DATA_CACHE_TIME' => 30,//缓存
    'URL_MODEL' => 0,

    'TOKEN_ON'      =>    true,//令牌验证
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true

    'VAR_FILTERS'=>'htmlspecialchars',
    'SESSION_OPTIONS'=>array('name'=>array('AdminInfo','loginInfo'),'expire'=>24*3600,'use_trans_sid'=>1,'use_only_cookies'=>0),
    'PHPExcelColumns'=>range('A','Z'),

    'FILE_UPLOAD_PATH'=>'./Public/Uploads/',
    'DOWNLOAD_PATH'=>'./Public/Downloads/',
    'UID_EXISTS'=>true,//是否允许非微信用户，在没有获得用户ID的情况下访问内部页面



    //各页面筛选字段映射
    'PAGE_SCREEN' => array(
        //页面名字=》需要检索的字段
        'channelManagement'=>array(//渠道管理
            'id'=>'UID',
            'username'=>'用户名'
        ),
        'withdrawManagement'=>array(//提现页面
            'id'=>'ID',
            'uid'=>'UID',
            'money'=>'金额',
            'collection_account'=>'收款人账号',
//            'username'=>'收款人名称',
            'bank'=>'收款人开户行',
            'province'=>'收款人所在省',
            'area'=>'收款人所在县',
            'transfer_type'=>'转账类型',
            'remarks'=>'汇款用途',
//            'state'=>'审核状态',
//            'time'=>'提现时间',
            'payee_name'=>'收款人名称',
        ),
        'promotionSettings'=>array(//推广设置
            'account'=>'用户帐号',
            'uid'=>'UID',
            'nickname'=>'用户昵称',
            'user_type'=>'用户组',
        ),
    ),

    //字段翻译映射
    'TRANSLATE_FIELDS' => array(
        'user'=>array(
            'user_type'=>array('普通用户','代理商','商会长'),
            'state'=>array('正常','封号','永久封号'),
            'login_time'=>'to_time',//to_time表示需要时间戳转日期
            'register_time'=>'to_time',
            'close_time'=>'to_time',
        ),
        'withdrawals'=>array(
            'state'=>array('审核中','已通过','未通过','已完成'),
            'time'=>'to_time',
        ),
    ),

     'SERVER_IP'=>'172.168.1.49:9999',
);