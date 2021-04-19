<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Price extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('Price')->order('id desc')->paginate();
    	
    	
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
  
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
	
            $res=db('Price')->delete($iid);
            if ($res) {
                $date['code']=1;
                $date['msg']='操作成功';
            }else{
                $date['code']=0;
                $date['msg']='操作失败';
            }
           
    		return json($date);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids=input('ids');
            
            $res=db('Price')->where('id','in',$ids)->delete();
            if ($res) {
                $date['code']=1;
                $date['msg']='操作成功';
            }else{
                $date['code']=0;
                $date['msg']='操作失败';
            }
            
            return json($date);
        }
    }
    //添加
    public function add()
    {	
    	if (request()->isPost()) {
    		$post= json_decode(input('date'),true);//post传过来的
            
    		$find= db('Price')->where('price',$post['price'])->find();//库里的名与加的名进行判断
            
    		if ($find) {
    			$date['code']=0;
    			$date['msg']='名称以存在';
			}else{
                $res = db('price')->where('sort',$post['sort'])->find();
                if ($res) {
                    db('price')->where('sort','egt',$post['sort'])->setInc('sort'); 
                }
    			$res=db('Price')->field(true)->insert($post);//insert添加数据
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
    	$find= db('Price')->where('id',$id)->find();
    	if (request()->isPost()) {
    		$post= json_decode(input('data'),true);//post传过来的
    		$find= db('Price')->where('price',$post['price'])->find();//库里的名与加的名进行判断
    		if($find['price']){
                $sort= db('price')->where('sort',$post['sort'])->find();
                if ($sort) {
                    db('price')->where('sort','egt',$post['sort'])->setInc('sort',1); 
                }
                $res= db('Price')->where('id',$post['id'])->update($post);
                $data=$this->ajaxreturn($res);
            }else{
                $sort = db('price')->where('sort',$post['sort'])->find();
                if ($sort) {
                    db('price')->where('sort','egt',$post['sort'])->setInc('sort',1); 
                }
                $res= db('Price')->where('id',$post['id'])->update($post);
                $data = $this->ajaxreturn($res);
            }
    		return json($data);
    	}
    	
    	$this->assign('find',$find);
    	return $this->fetch();
    }
}
