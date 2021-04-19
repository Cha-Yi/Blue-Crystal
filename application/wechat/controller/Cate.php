<?php
namespace app\wechat\controller;

class Cate extends Base
{
	//页面
    public function index()
    {
        $cid= input('cid');
        $list= db('goods')->where('cid',$cid)->select();
        $listarr=$this->httpimg($list,'pic',false);
        $this->assign(['list'=>$list,'listarr'=>$listarr]);
        return $this->fetch('goods:index');//加载页面
    }
}
