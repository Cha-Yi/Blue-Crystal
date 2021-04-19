<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Brand extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('brand')->order('id desc')->paginate();
    	$listarr= $list->toArray();//转数组
    	
    	$this->assign(['list'=>$list,'listarr'=>$listarr['data']]);//抛入页面
        return $this->fetch();//加载视图
    }
    //更改推荐状态
    public function recommend()
    {
    	if (request()->isPost()) {
    		$post= input('post.');//拿到post的所有
    		
    		$res= db('brand')->where('id',$post['id'])->update(['index'=>$post['index']]);//修改update
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
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
    		$find= db('brand')->where('id',$iid)->find();
    		
    		if ($find['index']==1) {
    			$date['code']=0;
    			$date['msg']='推荐分类不能直接删除';
    		}else{
    				$res=db('brand')->delete($iid);
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
            $find= db('brand')->where('id','in',$ids)->find();
    		if ($find['index']==1) {
    			$date['code']=0;
    			$date['msg']='推荐分类不能直接删除';
    		}else{
				$res=db('brand')->where('id','in',$ids)->delete();
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
    		$find= db('brand')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if ($find) {
    			$date['code']=0;
    			$date['msg']='品牌以存在';
			}else{
    			$res=db('brand')->field(true)->insert($post);//insert添加数据
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
    	$find= db('brand')->where('id',$id)->find();
    	if (request()->isPost()) {
    		$post= json_decode(input('data'),true);//post传过来的
    		$find= db('brand')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if($find['name']){
                $data['code'] = 0;
                $data['msg'] = '存在';
            }else{
                $res= db('brand')->where('id',$post['id'])->update($post);
                $data = $this->ajaxreturn($res);
            }
    		return json($data);
    	}
    	
    	$this->assign('find',$find);
    	return $this->fetch();
    }
}
