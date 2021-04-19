<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Officialuser extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页       
    	$list= db('wx_user')->order('id desc')->paginate(10);
        
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
}