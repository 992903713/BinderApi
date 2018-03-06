<?php
/**
 * Created by PhpStorm.
 * User: Jaccob
 * Date: 16/8/30
 * Time: 下午12:40
 * //文件下载
 */




header('Content-Type:text/html;charset=utf-8');
set_time_limit(0);
$mysqlLink = mysqli_connect("localhost","newRoot","ppz7long");

mysqli_set_charset($mysqlLink,'utf8');
if(!$mysqlLink)
{
    die('数据库连接失败:'.mysqli_error($mysqlLink));
}
mysqli_select_db($mysqlLink,'Binder')or die('不能选定指定数据库 Binder:'.mysqli_error($mysqlLink));

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
/*
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
//读取xml

$doc = new DOMDocument();
$doc->load("C:\Users\Jaccob\Desktop\catalog.xml");

$books = $doc->getElementsByTagName("record");
foreach($books as $book)
{
    $titles = $book->getElementsByTagName("title");
	if($titles)
	{
		  $title = $titles->item(0)->nodeValue;
	}else{
		$title = "";
	}
	
	
    $paths = $book->getElementsByTagName("format");
    $path = $paths->item(0)->nodeValue;
	if($paths)
	{
		  $path = $paths->item(0)->nodeValue;
	}else{
		$path = "";
	}
    $suffix = pathinfo($path, PATHINFO_EXTENSION);

	
    $authors = $book->getElementsByTagName("author");
	if($authors)
	{
		 $author = $authors->item(0)->nodeValue;
	}else{
		$author = '';
	}

	
    $categorys = $book->getElementsByTagName("comments");
	if($categorys)
	{
		$category = $categorys->item(0)->nodeValue;
	}else{
		$category = '';
	}
	

	

    $sizes = $book->getElementsByTagName("size");
	if($sizes)
	{
		$size = $sizes->item(0)->nodeValue;
	}else{
		$size = '';
	}
	



    $publish_times = $book->getElementsByTagName("pubdate");
	if($publish_times)
	{
		$publish_time = $publish_times->item(0)->nodeValue;

	}else{
		$publish_time = '';
	}
	

    $publishers = $book->getElementsByTagName("publisher");
	if($publishers)
	{
		$publisher = $publishers->item(0)->nodeValue;
	}else{
		$publisher = '';
	}

    $cover_paths = $book->getElementsByTagName("cover");
	if($cover_paths)
	{
		$cover_path = $cover_paths->item(0)->nodeValue;

	}

    $bookIds = $book->getElementsByTagName("uuid");
	if($bookIds)
	{
		$bookId = $bookIds->item(0)->nodeValue;
	}else{
		$bookId = '';
	}
	
	$tags = $book->getElementsByTagName("tags");
	if($tags)
	{
		$tag = $tags->item(0)->nodeValue;
	}else{
		$tag = '';
	}
	//$tag_index = q_encode($tag);
	
	$ratings = $book->getElementsByTagName("rating");
	if($ratings)
	{
		$rating = $ratings->item(0)->nodeValue;
	}else{
		$rating = '';
	}
	


    //写入数据库

    $mysqlQuery = "INSERT INTO bt_book (bt_bookId,bt_title,bt_suffix,bt_author,bt_path,bt_category,bt_size,bt_publisher,bt_publish_time,bt_cover_path,bt_tag,bt_rating)
 VALUES ('$bookId','$title','$suffix','$author','$path','$category','$size','$publisher','$publish_time','$cover_path','$tag','$rating')";

    $mysqlQueryResult =  mysqli_query($mysqlLink,$mysqlQuery);
    if( mysqli_affected_rows($mysqlLink) && $mysqlQueryResult)
    {
        echo 200;
        echo "</br>";
    }else{
        echo mysqli_error($mysqlLink);
        echo "</br>";
    }

}



