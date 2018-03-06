
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
*/
if( $_POST["bookTag"])
{
    $bookTag = trim($_POST["bookTag"]);
	
	
	$bookRecord = $_POST["bookRecord"];
	$bookRecord = (int)$bookRecord;
	$bookResultArray = array();

	if($bookTag == "全部")
	{
		$getAllBooksQuery = "SELECT * FROM bt_book limit $bookRecord,15";
		$getAllBooksQueryResult = mysqli_query($mysqlLink,$getAllBooksQuery);
		while($bookResult = mysqli_fetch_assoc($getAllBooksQueryResult))
		{
			array_push($bookResultArray,$bookResult);
		}
		
		 if(count($bookResultArray) > 0)
    {
        exit(json_encode(array("books" => $bookResultArray)));
    }else{
        exit(json_encode(array("error" => "没有搜索结果")));
    }
	}
	
	
	$sph = new SphinxClient();
	$sph->SetServer('localhost',9312);
	$sph->SetMatchMode(SPH_MATCH_ANY);
	$sph->SetSortMode(SPH_SORT_RELEVANCE);
	$sph->SetArrayResult(false);
	
	$result = $sph->Query($bookTag,'tagQuery');
	
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
	
	
	
	
	
	foreach($arr_key as $id)
	{
		
		$query = "select * from bt_book where id ={$id}  ";
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
