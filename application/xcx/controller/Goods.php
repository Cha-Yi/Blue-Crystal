<?php
namespace app\xcx\controller;

//小程序商品接口
class Goods extends Base
{	
    //商品详情
    public function info()
    {
    	$id = input('id');
    	$find = db('goods')->where('id',$id)->find();
    	$find = $this->httpimgone($find);
    	return $this->ajaxreturn($find);
    }
}