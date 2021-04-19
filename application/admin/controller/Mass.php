<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
use app\wx\controller\Msg;

class Mass extends Base
{
    public function Index()
    {
    	$msg = new Msg;
    	if(request()->isPost()){
    		$wechatmsg = input('data');
    		$res = $msg->broadcasting($wechatmsg);
    		$data = $this->ajaxreturn($res);
    		return json($data);
    	}
    		
    	return $this->fetch();
    }


}