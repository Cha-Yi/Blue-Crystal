<?php
namespace app\index\controller;
 // use think\Controller;//引入底层Controller

class Index extends Base
{
    public function index()
    {
    	$bannerlist=db('Banner')->field('pic')->where(['type'=>1,'is_show'=>1])->select();
    	// dump($bannerlist);exit;
    	$bannerlist = $this->httpimg($bannerlist,'pic',false);//图片路径处理
    	//dump($bannerlist);die;
    	// $cate= db('cate')->field('id,name')->where('index',1)->limit(3)->select();
    	// foreach ($cate as $k => $v) {
     //        $child = db('goods')->field('id,name,pic,price')->where('cid',$v['id'])->limit(5)->select();
     //        $cate[$k]['child']=$this->httpimg($child,'pic',false);
     //    }
        $cate= $this->getcategoods(3,5);
        
        $floor = $this->getcategoods(4,6,'id desc');
       // dump($floor);die;
        $this->assign(['bannerlist'=>$bannerlist,'cate'=>$cate,'floor'=>$floor]);
        return $this->fetch();//加载页面
    }
}
