<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Vip extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('user')->alias('a')->join('tp5_user_info b','a.id = b.uid','left')->paginate(10);
        $listarr = $this->httpimg($list,'pic',true);
    	// dump($list);die;
    	$this->assign(['list'=>$list,'listarr'=>$listarr]);//抛入页面
        return $this->fetch();//加载视图
    }
    //更改推荐状态
    public function recommend()
    {
    	if (request()->isPost()) {
    		$post= input('post.');//拿到post的所有
              $res= db('user')->where('id',$post['id'])->update(['allow'=>$post['allow']]);//修改update
                if ($res) {
                    $data['code']=1;
                    $data['msg']="操作成功";
                }else{
                    $data['code']=0;
                    $data['msg']="操作失败";
                }
                return json($data);  
    	}
    }
     //查看
    public function edit()
    {	
    	$id=input('id');
    	$find= db('user')->where('id',$id)->find();
    	$info = db('user_info')->where('uid',$id)->find();
        // dump($info);die;
    	$this->assign(['find'=>$find,'info'=>$info]);//反馈页面
    	return $this->fetch();
    }
}
