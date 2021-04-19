<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Cate extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('cate')->order('id desc')->paginate();
    	$listarr= $list->toArray();//转数组
    	foreach ($listarr['data'] as $key => $value) {
    		$pname= db('cate')->field('name')->where('id',$value['pid'])->find();
    		
    		$listarr['data'][$key]['pname'] = $pname?$pname['name']:"顶级分类";
    		
    	}
    	$this->assign(['list'=>$list,'listarr'=>$listarr['data']]);//抛入页面
        return $this->fetch();//加载视图
    }
    //更改推荐状态
    public function recommend()
    {
    	if (request()->isPost()) {
    		$post= input('post.');//拿到post的所有
    		
    		$res= db('cate')->where('id',$post['id'])->update(['index'=>$post['index']]);//修改update
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
    		$find= db('cate')->where('id',$iid)->find();
    		$chlid= db('cate')->where('pid',$iid)->find();
    		if ($find['index']==1) {
    			$data['code']=0;
    			$data['msg']='推荐分类不能直接删除';
    		}else if ($chlid) {
    			$data['code']=0;
    			$data['msg']='该分类存在子分类不能直接删除';
    			}else{
    				$res=db('cate')->delete($iid);
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
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids=input('ids');
            $find= db('cate')->where('index',1)->where('id','in',$ids)->find();
    		$chlid= db('cate')->where('pid','in',$ids)->find();
    		if ($find) {
    			$data['code']=0;
    			$data['msg']='推荐分类不能直接删除';
    		}else if ($chlid) {
    			$data['code']=0;
    			$data['msg']='该分类存在子分类不能直接删除';
    			}else{
    				$res=db('cate')->where('id','in',$ids)->delete();
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
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
    		$find= db('cate')->where('name',$post['name'])->find();//库里的名与加的名进行判断
    		if ($find) {
    			$data['code']=0;
    			$data['msg']='改分类以存在';
			}else{
    			$res=db('cate')->field(true)->insert($post);//insert添加数据
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
    	$parent= db('cate')->where('pid',0)->select();
    	$this->assign('parent',$parent);
    	return $this->fetch();
    }
     //编辑
    public function edit()
    {	
    	$id=input('id');
    	$find= db('cate')->where('id',$id)->find();
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
          
    		$find= db('cate')->where('id',$post['id'])->find();//库里的名与加的名进行判断
            if ($find['index']==1) {
                $data['code']=0;
                $data['msg']='推荐分类不能修改';
            }else{
                $child=db('cate')->where('pid',$find['id'])->find();
                if ($child) {
                    $data['code']=0;
                    $data['msg']='存在子分类不能修改';
                }else if($post['name']==$find['name']){
                    $data['code']=0;
                    $data['msg']='分类已存在';

                    }else{
                        $res=db("cate")->field(true)->where('id',$post['id'])->update($post);//修改
                        $data=$this->ajaxreturn($res);
                    }
            }

    		return json($data);
    	}
    	
    	$parent= db('cate')->where('pid',0)->select();
    	$this->assign(['parent'=>$parent,'find'=>$find]);
    	return $this->fetch();
    }
}
