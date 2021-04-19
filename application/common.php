<?php
use Sms\SmsSingleSender;
require_once('../vendor/phpmailer/class.phpmailer.php');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 短信
function sms($tel,$code)
{
	// 短信应用 SDK AppID
	$appid = 1400412564; // SDK AppID 以1400开头
	// 短信应用 SDK AppKey
	$appkey = "3200816bd085012879cf28668796ace6";
	// 需要发送短信的手机号码
	//$phoneNumbers = [$tel];
	// 短信模板 ID，需要在短信控制台中申请
	$templateId = 692543;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
	$smsSign = "奈茶良"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
	try{
	  $ssender = new SmsSingleSender($appid, $appkey);	  
	  $params = [$code];
	  $result = $ssender->sendWithParam("86", $tel, $templateId,
	      $params, $smsSign, "", "");
	  $res = json_decode($result,true);
	  if($res['result']==0){
		  $data['code'] = 1;
		  $data['msg'] = '成功';
	  }else{
	  	 $data['code'] = 0;
	     $data['msg'] = $res['errmsg'];
	  }
	} catch(\Exception $e) {
	  $data['code'] = 0;
	  $data['msg'] = $e;
	}
	return $data;
}
//邮箱
function email($email,$code){
		$mail = new PHPMailer();                              // Passing `true` enables exceptions
	try {
	    //服务器配置
	    $mail->IsSMTP(); // telling the class to use SMTP 使用smtp协议发送（固定要的）
		$mail->CharSet       = "utf-8"; //编码
		$mail->SMTPDebug  = 0;                     // enables SMTP debug 
		$mail->SMTPAuth   = true;                  // enable SMTP authentication 允许smtp通信（固定）
		$mail->Host       = "smtp.126.com"; // sets the SMTP server //发送邮件的邮箱的服务器地址
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server //邮箱服务器进程的默认端口
		$mail->Username   = "dodidodi123@126.com"; // SMTP account username //发送邮箱的帐号及密码
		$mail->Password   = "dodidodi";        // SMTP account password
		$mail->SetFrom('dodidodi123@126.com', 'sangsang');//设置接收来源

		$mail->AddReplyTo("dodidodi123@126.com","First Last");//回复邮箱

		$mail->Subject    = '蓝水晶商城' . time();//标题

		$mail->AltBody    = "您的客户端不支持此内容显示"; // optional, comment out and test

		$mail->MsgHTML('<h1>您的注册验证码：</h1>' . $code);//内容使用html格式

		$mail->AddAddress($email);//有多个邮箱地址，使用多次  
	    if ($mail->Send()) {
	    	$data['code'] = 1;
	    	$data['msg'] = "发送成功";
	    }else{
	    	$data['code'] = 0;
	    	$data['msg'] = "发送失败";
	    }	   
	} catch (Exception $e) {
			$data['code'] = 0;
	    	$data['msg'] = "邮件发送失败原因是:".$mail->ErrorInfo;
	}
		return $data;
}

/**

 * 发送post请求

 * @param string $url 请求地址

 * @param array $post_data post键值对数据

 * @return string

 */

function send_post($url, $post_data) {   

  $postdata = http_build_query($post_data);

  $options = array(

    'http' => array(

      'method' => 'POST',

      'header' => 'Content-type:application/x-www-form-urlencoded',

      'content' => $postdata,

      'timeout' => 15 * 60 // 超时时间（单位:s）

    )

  );

  $context = stream_context_create($options);

  $result = file_get_contents($url, false, $context);   

  return $result;
}
