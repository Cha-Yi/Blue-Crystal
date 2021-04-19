<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Sign extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页       
    	$list= db('sign_in')->alias('a')->field('a.*,b.nickname,b.headimgurl,b.sex')->join('tp5_wx_user b','a.uid = b.id')->paginate(10);
        // dump($list);die;
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
}