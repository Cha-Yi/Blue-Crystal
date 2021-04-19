<?php
namespace app\xcx\controller;

//小程序首页接口
class Index extends Base
{	
	//小程序banner
    public function banner()
    {        	
	    $banner = db('banner')->field('pic')->where(['type'=>3,'is_show'=>1])->select();
	    $banner = $this->httpimg($banner,'pic',false);
	    return $this->ajaxreturn($banner);
    }   
    //小程序导航
    public function nav()
    {        	
	    $nav = db('menu')->field('id,menu_name')->where(['type'=>3,'pid'=>0])->order('sort')->limit(4)->select();	   
	    return $this->ajaxreturn($nav);
    }  
    //获取首页商品分类
    public function getcate()
    {
        $catelist = db('cate')->where('index',1)->limit(4)->select();
        return $this->ajaxreturn($catelist);
    }
    //获取对应分类商品
    public function getcategoods()
    {
        $cateid = input('cateid');
        $goods = db('goods')->field('id,pic,name,price')->where('cid',$cateid)->paginate(10);
        $goods = $this->httpimg($goods);
        return $this->ajaxreturn($goods);
    }
}
