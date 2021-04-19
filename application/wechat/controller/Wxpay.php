<?php
namespace app\wechat\controller;

class Wxpay extends Base
{
	//发起支付
    public function pay()
    {
    	
    	header("Content-type:text/html;charset=utf-8"); 
        require EXTEND_PATH.'/wxpay/WxPay.Api.php'; //引入微信支付
        $input = new \WxPayUnifiedOrder();//统一下单
        $config = new \WxPayConfig();//配置参数
        $out_trade_no = input('order_num'); //商户订单号(自定义)            
        $paymoney = input('sumtotal'); //支付金额(分)
        //echo $paymoney;die;
        $goods_name = '扫码支付'.$paymoney.'元'; //商品名称(自定义)
        $input->SetBody($goods_name);
        $input->SetAttach($goods_name);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($paymoney*100);//金额乘以100
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("水果先锋微店");
        $input->SetNotify_url("http://www.php46tp5.com/index/wechat/notify"); //回调地址
        $input->SetTrade_type("JSAPI");//支付方式
        $input->SetProduct_id($out_trade_no);//商品id
        $input->Setopenid('oH_fM4hp1yeEAgQZBl_jYNAoqaQs');
        $result = \WxPayApi::unifiedOrder($config, $input);
        $result['timestamp']=time();
       return json($result);
    }
    //支付后通知函数
    public function notify()
    {
    	 //$xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
		 $xml = file_get_contents("php://input");
		 //$results = db('fund') -> where('id',1) -> update(['a'=>$xml]);exit();
		  $xmlArr = $this->Init($xml);
		  file_put_contents(dirname(__FILE__).'/xml.txt',$xml); //记录日志 支付成功后查看xml.txt文件是否有内容 如果有xml格式文件说明回调成功
		   
		  $out_trade_no=$xmlArr['out_trade_no']; //订单号
		  $total_fee=$xmlArr['total_fee']/100; //回调回来的xml文件中金额是以分为单位的
		  $result_code=$xmlArr['result_code']; //状态
		  //$result = db('order') -> where(['order' => $out_trade_no]) -> find();
		  //if($result['price'] == $total_fee){
		   if($result_code=='SUCCESS'){ //数据库操作
		    //处理数据库操作 例如修改订单状态 给账户充值等等 
		     db('order')->where('order_num',$out_trade_no)->setField('status',2);//修改订单状态
		    exit; 
		   }else{ //失败
		    return false;
		    exit;
		   }	   
		   
	}
	public function Init($xml)
	{
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}
}
