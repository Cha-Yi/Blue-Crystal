<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
class News extends Base
{
    public function Index()
    {
    	
    	//查询多条数据 降序 分页
    	$list=db('News')->alias('a')->field('a.id,cid,title,time,author,name bname')->join('tp5_newscate b','cid = b.id','left' )->order('a.id desc')->paginate(10);

        $this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
    		
    		$res=db('News')->delete($iid);
    		
           $data=$this->ajaxreturn($res);
    		return json($data);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids= input('ids');
            $res=db('News')->where('id','in',$ids)->delete();
            
            $data=$this->ajaxreturn($res);
            return json($data);
        }
    }
    //添加
    public function add()
    {   
        if (request()->isPost()) {
             
           $post= json_decode(input('data'),true);//将json字符串装数组
           $post['author']=session('admin.user');
           $post['time']=time();
           $res=db('News')->field(true)->insert($post);
       
           $data= $this->ajaxreturn($res);
           return json($data);   
        }
        $newscate=db('newscate')->select();
        $this->assign(['newscate'=>$newscate]);//将数据传递到页面
        return $this->fetch();//加载视图
    }
    //修改
    public function edit()
    {
        if (request()->isPost()) {
            $post=json_decode(input('data'),true);//字符串转数组

            $res= db('News')->field(true)->where('id',$post['id'])->update($post);
            $data=$this->ajaxreturn($res);
            return json($data);
        }
        $id= input('id');
        $find=db('News')->where('id',$id)->find();//查一个
     
        $newscate=db('newscate')->select();
        $this->assign(['find'=>$find,'newscate'=>$newscate]);//将数据传递到页面
        return $this->fetch();
    }
    


}