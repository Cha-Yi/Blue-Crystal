<?php
namespace app\index\controller;
use zfb\AlipaySubmit;
use zfb\Config;
use zfb\Notify;
class Alipay extends Base
{
	//发起支付
    public function pay()
    {
    	if (!$this->checklogin()) {
    		$this->redirect('User/login');
    	}
    	/*判断session里有没有定单号*/
    	if (session('?order_num')) {
    		/**************************请求参数**************************/
	        //商户订单号，商户网站订单系统中唯一订单号，必填
	        $out_trade_no = session('order_num');
	        //订单名称，必填
	        $subject = '蓝水晶_'.session('order_num');
	        //付款金额，必填
	        $total_fee = session('sumtotal');

	        $obj = new Config();
	        $alipay_config = $obj->payconfig();
	        //构造要请求的参数数组，无需改动
	        $parameter = array(
	        "service"       => $alipay_config['service'],
	        "partner"       => $alipay_config['partner'],
	        "seller_id"  => $alipay_config['seller_id'],
	        "payment_type"  => $alipay_config['payment_type'],
	        "notify_url"  => $alipay_config['notify_url'],
	        "return_url"  => $alipay_config['return_url'],

	        "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
	        "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
	        "out_trade_no"  => $out_trade_no,
	        "subject" => $subject,
	        "total_fee" => $total_fee,
	        //"body"  => $body,
	        "_input_charset"  => trim(strtolower($alipay_config['input_charset']))
	        //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
	        //如"参数名"=>"参数值"

	        );
	        //建立请求
	        $alipaySubmit = new AlipaySubmit($alipay_config);
	        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	        echo $html_text;
    	}else{
    		$this->error('系统错误');
    	}
    }
    //支付后通知函数
    public function notify()
    {
    	$obj = new Config();
	     $alipay_config = $obj->payconfig();

		//计算得出通知验证结果
		$alipayNotify = new Notify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {
		   //验证成功			
			//请在这里加上商户的业务逻辑程序代			
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
		    if($_POST['trade_status'] == 'TRADE_FINISHED') {		
				//注意：
				//失败时的处理
		       
		    }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//注意：
				//成功时的处理（更改订单状态）			
		        db('order')->where('order_num',$out_trade_no)->setFiled('status',2);
		    }
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——		        
			echo "success";		//请不要修改或删除
			
			
		}else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
    }
    //支付后的回跳地址
    public function return_url()
    {
    	//计算得出通知验证结果
		 $obj = new Config();
	     $alipay_config = $obj->payconfig();

		//计算得出通知验证结果
		$alipayNotify = new Notify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			//请在这里加上商户的业务逻辑程序代码
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
			//支付宝交易号
			$trade_no = $_GET['trade_no'];
			//交易状态
			$trade_status = $_GET['trade_status'];
		    if($_GET['trade_status'] == 'TRADE_FINISHED') {
				$this->error('支付失败');//错误
		    }else if($_GET['trade_status'] == 'TRADE_SUCCESS'){
		        $this->success('支付成功','Order/index');//成功
		    }
			
		}else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    echo "验证失败";
		}
    }
}
