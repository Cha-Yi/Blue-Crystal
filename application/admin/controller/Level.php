<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Level extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('Level')->order('id desc')->paginate();
        $listarr=$list->toArray(); //对象转数组
        foreach ($listarr['data'] as $key => $value) {
            $level= explode('|',$value['menu_level']);
            $menu_name='';
            foreach ($level as $k => $v) {
                $arr = db('menu')->field('menu_name')->where(['type'=>0,'id'=>$v])->find();
                if ($arr) {
                    $menu_name.=$arr['menu_name']."|";
                } 
            }
            $listarr['data'][$key]['menu_level']=rtrim($menu_name,"|");
        }
    	// dump($listarr);die;
    	$this->assign(['list'=>$list,'listarr'=>$listarr['data']]);//抛入页面
        return $this->fetch();//加载视图
    }
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
            $find= db('admin')->where('level',$iid)->find();
            if ($iid==1||$find) {
               $data['code']=0;
               $data['msg']='管理员角色正在使用中/无法删除';
            }else{
				$res=db('Level')->delete($iid);
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
            $find= db('admin')->where('level','in',$ids)->find();
            if (in_array(1, $ids)||$find) {
                $data['code']=0;
                $data['msg']='管理员角色正在使用中/无法删除';
            }else{
				$res=db('Level')->where('id','in',$ids)->delete();
	            $data= $this->ajaxreturn($res);
		    }
            return json($data);
        }
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= input('post.');
            $find=db('Level')->where('level_name',$post['level_name'])->find();
            if ($find) {
                $data['code']=0;
                $data['msg']='管理员角色已存在';
            }else{
                $post['menu_level']=implode("|",$post['menu_level']);
                $res = db('Level')->field(true)->insert($post);//添加数据
                $data = $this->ajaxreturn($res);   
            }
    		return json($data);
    	}
    	return $this->fetch();
    }
     //编辑
    public function edit()
    {	
    	$id=input('id');
    	
    	if (request()->isPost()) {
    		$post= input('post.');

            $res = db('Level')->field(true)->where('id',$post['id'])->setField('menu_level',$post['menu_level']);//修改menu_level字段
            $data = $this->ajaxreturn($res);   
            
            return json($data);
    	}
        if ($id==1) {
            $this->redirect('Level/index');//重定向
        }
    	$find= db('Level')->where('id',$id)->find();
        $find['menu_level']= explode('|',$find['menu_level']);//拆分字符串
    	$this->assign(['find'=>$find]);//反馈页面
    	return $this->fetch();
    }
}
