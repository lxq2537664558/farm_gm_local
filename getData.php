<?php
    header('Content-type: text/html; charset=utf8');
    require_once ('conf.php');
    $res = $_POST['aoData'];
    $iDisplayStart = 0; // 起始索引
    $iDisplayLength = 0;//分页长度
    $jsonarray= json_decode($res) ;
    foreach($jsonarray as $value){
        if($value->name=="sEcho"){
            $sEcho=$value->value;
        }
        if($value->name=="iDisplayStart"){
            $iDisplayStart=$value->value;
        }
        if($value->name=="iDisplayLength"){
            $iDisplayLength=$value->value;
        }
        if($value->name=="sSearch"){
            $sSearch=$value->value;
        }
    }
   // echo $sSearch;
    $data = array();
    $Array = Array();
    $sql = "select * from user ";
    $sumSqlWhere =" where id LIKE '%".$sSearch."%' || username LIKE '%".$sSearch."%' || phone LIKE '%".$sSearch."%' || idcard LIKE '%".$sSearch."%' || father_id LIKE '%".$sSearch."%' || grandfather_id LIKE '%".$sSearch."%' || user_type LIKE '%".$sSearch."%' || login_time LIKE '%".$sSearch."%' || register_time LIKE '%".$sSearch."%'";
    if($sSearch){
        $recordsFilteredResult = mysql_query($sql.$sumSqlWhere) or die('失败');
        $count = mysql_num_rows($recordsFilteredResult);
        while($row = mysql_fetch_array($recordsFilteredResult)) {
            $data = array($row['id'], $row['username'], $row['phone'], $row['idcard'], $row['father_id'], $row['grandfather_id'],$row['user_type'],$row['login_time'],$row['register_time']);
            Array_push($Array,$data); 
        }
    }else{
        $result = mysql_query($sql) or die('失败');
        $count = mysql_num_rows($result);
        while($row = mysql_fetch_array($result)) {
            $data = array($row['id'], $row['username'], $row['phone'], $row['idcard'], $row['father_id'], $row['grandfather_id'],$row['user_type'],$row['login_time'],$row['register_time']);
            Array_push($Array,$data); 
        }
    }
    // var_dump($iDisplayStart,$iDisplayLength);die;
    $json_data = array (
		'sEcho'=>$sEcho,
		'iTotalRecords'=>$count,//总记录数
		'iTotalDisplayRecords'=>$count,//总记录数
		'aaData'=>array_slice($Array,$iDisplayStart,$iDisplayLength)//结果集，开始点，截取长度
	);  //按照datatable的当前页和每页长度返回json数据
    // var_dump($json_data);
      $obj=json_encode($json_data);
      echo $obj;
