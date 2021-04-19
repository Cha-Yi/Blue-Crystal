<?php
namespace app\wx\controller;//命名空间

class Msg extends Base
{
	//活动模板消息发送主动发送
    public function tem_msg($openid)
    {
    	
    	$starttime = date('Y-m-d',time()+86400);//开始时间
    	$endtime = date('Y-m-d',time()+86400*3);//结束时间
    	
    		$this->app->template_message->send([
		        'touser' => $openid,
		        'template_id' => 'i_ej-yg6nXt1_oK-D2iQQkXE-XZGuBmUjT9XpCGP9AM',
		        'url' => 'http://tp5.sangchen.work',
		        'data' => [
		            "first" => [
			            	"value" => "庆祝水晶商场开业三周年",
			            	"color" => "#FF0000"
		            	],
		            "keywords1" => [
				            "value" => "商场内所有饰品八折",
				            "color" => "#9c3"
			            ],
		            "keywords2" => [
				            "value" => "电子正街,开运商城二楼水晶饰品店",
				            "color" => "#000"
			            ],
		            "remark" => [
		            		"value" => "{$starttime}至{$endtime}",
		            		"color" => "#0000FF"
		            		]
		        ],
    		]);
    	
    	
    }
    //关注模板消息发送
    public function follow_msg($openid)
    {
    	$user = $this->app->user->get($openid);
    	// dump($user);die;
    	$usrname = $user['nickname'];
		$res = $this->app->template_message->send([
	        'touser' => $openid,
	        'template_id' => 'qFYS60Zff6har_edaLXAt2wDxpSrgM7-q6XwF2mUgP0',
	        'url' => 'http://tp5.sangchen.work',
	        'data' => [
	            "first" => [
		            	"value" => $usrname,
		            	"color" => "#FF0000"
	            	],
	            "keywords1" => [
	            		"value" => "水晶首饰",
	            		"color" => "#6495ED"
	            		]
	        ],
		]);
    	return $res;
    	
    }
    //主动发送消息(群发)
    public function broadcasting()
    {
    	$this->app->broadcasting->sendText("大家好！欢迎关注小sang测试平台。");
    }//测试功能不支持

}