<?php
/**
 * Created by PhpStorm.
 * User: Jaccob
 * Date: 16/9/24
 * Time: 上午11:46
 */

header('Content-Type:text/html;charset=utf-8');

 require_once("phpmailer/class.phpmailer.php");   
 require_once("phpmailer/class.smtp.php");
 
$bookPath;
$kindleEmail;
$convert;


if($_POST["bt_path"])
{
    $bookPath = 'C:/wamp/www'.$_POST["bt_path"];
}


if($_POST["kindleEmail"])
{
    $kindleEmail = $_POST["kindleEmail"];
}
if($_POST["convert"])
{
    $convert = $_POST["convert"];
}else{
	
	$convert = 'normalFormat';
}




$mail = new PHPMailer(); //建立邮件发送类
$mail->CharSet = "UTF-8";
$address = $kindleEmail;
$mail->IsSMTP(); // 使用SMTP方式发送
$mail->Host = "smtp.qq.com"; // 您的企业邮局域名
$mail->SMTPAuth = true; // 启用SMTP验证功能
$mail->Username = "992903713@qq.com"; // 邮局用户名(请填写完整的email地址)
$mail->Password = "qducjybkmsxdbegd"; // 邮局密码
$mail->Port=25;
$mail->From = "992903713@qq.com"; //邮件发送者email地址
$mail->FromName = "Binder";
$mail->AddAddress("$address");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
//$mail->AddReplyTo("", "");

$mail->AddAttachment($bookPath); // 添加附件
$mail->IsHTML(false); // set email format to HTML //是否使用HTML格式

$mail->Subject = $convert; //邮件标题
$mail->Body = "new book"; //邮件内容，上面设置HTML，则可以是HTML

if(!$mail->Send())
{

	exit(json_encode(array("error"=> $mail->ErrorInfo)));
}else{
	exit(json_encode(array("success"=> "邮件发送成功")));

}





