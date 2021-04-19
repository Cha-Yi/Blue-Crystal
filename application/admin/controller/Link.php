<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Link extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('Link')->order('id desc')->paginate();
    	$listarr= $this->httpimg($list);//调用多图片路径判断方法
        $this->assign(['list'=>$list,'listarr'=>$listarr]);//将数据传递到页面
        return $this->fetch();//加载视图
    }
    
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
			$res=db('Link')->delete($iid);
			if ($res) {
    			$data['code']=1;
    			$data['msg']='操作成功';
			}else{
    			$data['code']=0;
    			$data['msg']='操作失败';
    		}
            return json($data);
		  }
    		
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids=input('ids');
            $res=db('Link')->where('id','in',$ids)->delete();
                if ($res) {
                    $data['code']=1;
                    $data['msg']='操作成功';
                }else{
                    $data['code']=0;
                    $data['msg']='操作失败';
                }
            return json($data);
        }
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
    		$find= db('Link')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if ($find) {
    			$data['code']=0;
    			$data['msg']='链接已存在';
			}else{
    			$res=db('Link')->field(true)->insert($post);//insert添加数据
    			 if ($res) {
    	                $data['code']=1;
    	                $data['msg']='操作成功';
    	            }else{
    	                $data['code']=0;
    	                $data['msg']='操作失败';
    	            }
    		}
    		return json($data);
    	}
    	
    	
    	return $this->fetch();
    }
     //编辑
    public function edit()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
    		$find= db('Link')->where('id',$post['id'])->find();//库里的名与加的名进行判断
            if ($post['name']!=$find['name']) {
                $row = db('link')->where('name',$post['name'])->find();
                if ($row) {
                    $data['code']=0;
                    $data['msg']='链接已存在';
                }else{
                    $res=db("Link")->field(true)->where('id',$post['id'])->update($post);//修改
                    $data=$this->ajaxreturn($res);
                }  
            }else{
                $res=db("Link")->field(true)->where('id',$post['id'])->update($post);//修改
                $data=$this->ajaxreturn($res);
            }

    		return json($data);
    	}
    	$id=input('id');
        $find= db('Link')->where('id',$id)->find();
    	$find= $this->httpimgone($find);//单图片处理
        $find['pic']=$find['pic'][0];//把pic覆盖掉就ok了
    	$this->assign(['find'=>$find]);
    	return $this->fetch();
    }
}
