<?php
$con=@mysql_connect('localhost','root','olwn_N382loi') or die('数据库连接失败'.mysql_error());
mysql_select_db('farm',$con) or die('选择数据库失败');
mysql_query('set names utf8');
