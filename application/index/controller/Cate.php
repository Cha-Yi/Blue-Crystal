<?php
namespace app\index\controller;

class Cate extends Base
{
	//页面
    public function index()
    {
        $cid= input('cid');
        $list= db('goods')->where('cid',$cid)->paginate(15);
        $listarr=$this->httpimg($list);
        // dump($list);die;
        $this->assign(['list'=>$list,'listarr'=>$listarr]);
        return $this->fetch('goods:index');//加载页面
    }
}
