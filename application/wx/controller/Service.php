<?php
namespace app\wx\controller;//命名空间

class Service extends Base
{
	//创建客服
    public function kfadd()
    {
    	if ($this->access_token == '') {
    		$this->access_token = $this->getaccess_token();
    	}
    	
    	$url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=".$this->access_token;
    	// $data['kf_account'] = 'php46@gh_8bb44720dd70';
    	// $data['nickname'] = '客服1';
    	// $data['password'] = '123';单客服
    	
    	// 多客服
    	for ($i=0; $i < 9; $i++) { 
    		$data[$i]['kf_account'] = "php46{$i}@gh_8bb44720dd70";
	    	$data[$i]['nickname'] = "客服{$i}";
	    	$data[$i]['password'] = "sang{$i}";
    	}
    	//print_r($data);die;
    	$json = json($data);//转json对象
    	$res = send_post($url,$json);//返回json对象
    	$arr = json_decode($res,true);//转数组
    	print_r($arr);
    }
}