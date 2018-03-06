<?php
/**
 * Created by PhpStorm.
 * User: Jaccob
 * Date: 16/8/27
 * Time: 下午7:02
 * kindleAssistant接口
 */

header('Content-Type:text/html;charset=utf-8');
$mysqlLink = mysqli_connect('localhost','newRoot','ppz7long');
mysqli_set_charset($mysqlLink,'utf8');

if(!$mysqlLink)
{
    die('数据库连接失败:'.mysqli_error($mysqlLink));
}
mysqli_select_db($mysqlLink,'Binder')or die('不能选定指定数据库 Binder:'.mysqli_error($mysqlLink));

//coreseek
require_once('sphinxapi.php');


/** http请求类 */
class request{
    /** post请求方法 */

    public function send_post($post_url,$post_data)
    {
        $postContent = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type:application/x-www-form-urlencoded',
                'content' => $postContent,
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($post_url,false,$context);
        return $result;
    }
}
//计算文件大小
function getFileSize($subFile)
{
    if($subFile >= pow(2,40))
    {
        $return = round($subFile / pow(1024,4), 2);
        $suffix = "TB";
    }elseif($subFile >= pow(2,30))
    {
        $return = round($subFile / pow(1024,3), 2);
        $suffix = "GB";
    }elseif($subFile >= pow(2,20))
    {
        $return = round($subFile / pow(1024,2), 2);
        $suffix = "MB";
    }elseif($subFile >= pow(2,10))
    {
        $return = round($subFile / pow(1024,1), 2);
        $suffix = "KB";
    }else
    {
        $return = $subFile;
        $suffix = "Byte";
    }
    return $return." ".$suffix;
}

function getFileName($subFile)
{
    return pathinfo($subFile,PATHINFO_FILENAME);
}
function getFileSuffix($subFile)
{
    return pathinfo($subFile, PATHINFO_EXTENSION);
}

//function trimall($str)//删除空格
//{
//    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
//    return str_replace($qian,$hou,$str);
//}
/*
function q_encode($str)
{
    $data = array_filter(explode(" ",$str));
    $data = array_flip(array_flip($data));
    $data_code = "";
     foreach ($data as $ss)
     {
        if (strlen($ss)>1 )
        {
          $data_code .= str_replace("%","",urlencode($ss)) . " ";
        }
     }
    $data_code = trim($data_code);
    return $data_code;
}

function tag_encode($str)
{
    $data = array_filter(explode(",",$str));
    $data = array_flip(array_flip($data));
    $data_code = "";
     foreach ($data as $ss)
     {
        if (strlen($ss)>1 )
        {
          $data_code .= str_replace("%","",urlencode($ss)) . " ";
        }
     }
    $data_code = trim($data_code);
    return $data_code;
}
*/




if( $_POST["book"])
{
	$bookRecord = $_POST["bookRecord"];
	$bookRecord = (int)$bookRecord;
	
	
    $book = trim($_POST["book"]);
	
	$sph = new SphinxClient();
	$sph->SetServer('localhost',9312);
	$sph->SetMatchMode(SPH_MATCH_ANY);
	$sph->SetSortMode(SPH_SORT_RELEVANCE);
	$sph->SetArrayResult(false);
	
	$result = $sph->Query($book,'normalQuery');
	
	if(!array_key_exists('matches',$result)){
        exit(json_encode(array("error" => "没有搜索结果")));
		return;
		
	}
	$arr_key = array_keys($result['matches']);
	$arrayCount = count($arr_key);
	
	if($bookRecord == 0)
	{
		if($arrayCount <= 15)
		{
			//不作任何处理
			//$arr_key = array_slice($arr_key,$bookRecord,$arrayCount);
		}else{
			$arr_key = array_slice($arr_key,$bookRecord,15);
		}
	}else{
		if($arrayCount/$bookRecord > 1)
		{
			$arr_key = array_slice($arr_key,$bookRecord,15);
		}else{
			$arr_key = array_slice($arr_key,$bookRecord,$arrayCount - $bookRecord);
		}
	}
	
	
	
	$bookResultArray = array();
	foreach($arr_key as $id)
	{
		
		$query = "select * from bt_book where id ={$id} ";
		$bookQueryResult = mysqli_query($mysqlLink,$query);
		
		$bookResult = mysqli_fetch_assoc($bookQueryResult);
	
		array_push($bookResultArray,$bookResult);
	
	}

	
	
    if(count($bookResultArray) > 0)
    {
        exit(json_encode(array("books" => $bookResultArray)));
    }else{
        exit(json_encode(array("error" => "没有搜索结果")));
    }
	
}



