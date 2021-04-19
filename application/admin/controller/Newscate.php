<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Newscate extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('Newscate')->order('id desc')->paginate();
    	
    	
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
  
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
	       $find= db('News')->where('cid',$iid)->find();
           if ($find) {
              $data['code']=0;
              $date['msg']="分类使用中无法删除";
           }else{
                $res=db('Newscate')->delete($iid);
                if ($res) {
                    $date['code']=1;
                    $date['msg']='操作成功';
                }else{
                    $date['code']=0;
                    $date['msg']='操作失败';
                }
           }
			
    		return json($date);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids=input('ids');
            $find= db('News')->where('cid','in',$ids)->find();
            if ($find) {
              $data['code']=0;
              $date['msg']="分类使用中无法删除";
            }else{
                $res=db('Newscate')->where('id','in',$ids)->delete();
                if ($res) {
                    $date['code']=1;
                    $date['msg']='操作成功';
                }else{
                    $date['code']=0;
                    $date['msg']='操作失败';
                }
            }

            return json($date);
        }
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
    		$find= db('Newscate')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if ($find) {
    			$date['code']=0;
    			$date['msg']='文章标题以存在';
			}else{
    			$res=db('Newscate')->field(true)->insert($post);//insert添加数据
    			if ($res) {
	                $date['code']=1;
	                $date['msg']='操作成功';
    	        }else{
	                $date['code']=0;
	                $date['msg']='操作失败';
    	       }
    		}
    		return json($date);
    	}
    	
    	
    	return $this->fetch();
    }
     //编辑
    public function edit()
    {	
    	$id=input('id');
    	$find= db('Newscate')->where('id',$id)->find();
    	if (request()->isPost()) {
    		$post= json_decode(input('data'),true);//post传过来的
    		$find= db('Newscate')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if($find['name']){
                $data['code'] = 0;
                $data['msg'] = '文章标题存在';
            }else{
                $res= db('Newscate')->where('id',$post['id'])->update($post);
                $data = $this->ajaxreturn($res);
            }
    		return json($data);
    	}
    	
    	$this->assign('find',$find);
    	return $this->fetch();
    }
}
