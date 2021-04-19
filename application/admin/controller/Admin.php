<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Admin extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('admin')->field('a.id,user,allow,level_name')->alias('a')->join('tp5_level b','a.level = b.id','left')->paginate();
    	// dump($list);die;
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
    //更改推荐状态
    public function recommend()
    {
    	if (request()->isPost()) {
    		$post= input('post.');//拿到post的所有
    		if ($post['id']!=1) {
              $res= db('admin')->where('id',$post['id'])->update(['allow'=>$post['allow']]);//修改update
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
    }
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
    		if ($iid==1) {
               $data['code']=0;
               $data['msg']='无权操作';
    		}else{
				$res=db('admin')->delete($iid);
				$data = $this->ajaxreturn($res);
		    }
    		return json($data);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids=input('ids');
            $ids= explode(',', $ids);
            if (in_array(1, $ids)) {
                $data['code']=0;
                $data['msg']='无权操作';
            }else{
				$res=db('admin')->where('id','in',$ids)->delete();
	            $data= $this->ajaxreturn($res);
		    }
            return json($data);
        }
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('data'),true);//post传过来的
            $find=db('admin')->where('user',$post['user'])->find();
            if ($find) {
                $data['code']=0;
                $data['msg']='账户已存在';
            }else{
                $post['pwd']=md5($post['pwd']);
                $res = db('admin')->field(true)->insert($post);//添加数据
                $data = $this->ajaxreturn($res);   
            }
    		return json($data);
    	}
    	$level= db('level')->where('id','neq',1)->select();
    	$this->assign('level',$level);
    	return $this->fetch();
    }
     //编辑
    public function edit()
    {	
    	$id=input('id');
        if ($id==1) {
            $this->redirect('Admin/index');//重定向
        }
    	$find= db('admin')->where('id',$id)->find();
    	if (request()->isPost()) {
    		$post= json_decode(input('data'),true);//post传过来的 

    		$find= db('admin')->where('id',$post['id'])->find();//库里的名与加的名进行判断
            if ($post['oldpwd']!='') {
                if ($find['pwd']==md5($post['oldpwd'])) {
                    $post['pwd']=md5($post['pwd']);
                    $res= db('admin')->field(true)->where('id',$post['id'])->update($post);
                    $data= $this->ajaxreturn($res);
                }else{
                    $data['code']=0;
                    $data['msg']="原密码与新密码不一致,请重新输入";
                }
            }else{
                $res= db('admin')->field(true)->where('id',$post['id'])->setField('level', $post['level']);//修改某一个字段
                $data= $this->ajaxreturn($res);
            }
    		return json($data);
    	}
    	
    	$level= db('level')->where('id','neq',1)->select();//查询
    	$this->assign(['level'=>$level,'find'=>$find]);//反馈页面
    	return $this->fetch();
    }
}
