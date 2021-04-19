<?php
namespace app\wechat\controller;

class Pay extends Base
{
	//页面
    public function index()
    {   
        
    	if (request()->isPost()) {
    		$pay = input('pay',1);
    		if ($pay == 1) {
    			// $this->redirect('Alipay/pay');
    		}else if($pay == 2){
    			$this->redirect('Wechat/pay');
    		}
    	}
    	return $this->fetch();
    }
}
