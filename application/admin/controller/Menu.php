<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
class Menu extends Base
{
    public function Index()
    {
    	
    	//查询多条数据 降序 分页
    	$list=db('Menu')->order('id desc')->paginate(10);//对象
        // dump($list);die;
        $listarr=$list->toArray();//数组
        // dump($listarr);die;
        foreach ($listarr['data'] as $key => $value) {
            $arr= db('menu')->field('menu_name')->where('id',$value['pid'])->find();
            if ($arr) {
               $listarr['data'][$key]['pid']=$arr['menu_name'];
            }else{
                $listarr['data'][$key]['pid']="顶级菜单";
            }
            
        }    
    	$this->assign(['list'=>$list,'listarr'=>$listarr['data']]);//将数据传递到页面
    	// dump($list);die;
        return $this->fetch();//加载视图
    }
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
    		
            $find=db('menu')->where('pid',$iid)->find();
            if ($find) {
                $data['code']=0;
                $data['msg']="当前菜单存在子菜单无法删除";
            }else{
                $res=db('Menu')->delete($iid);
                 $data=$this->ajaxreturn($res);
            }
    		return json($data);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids= input('ids');
            $find=db('menu')->where('pid','in',$ids)->find();
            if ($find) {
                $data['code']=0;
                $data['msg']="选中菜单存在子菜单无法删除";
            }else{
                $res=db('Menu')->where('id','in',$ids)->delete();
                $data=$this->ajaxreturn($res);
            }
            return json($data);
        }
    }
    //添加
    public function add()
    {   
        if (request()->isPost()) {
             
           $post= json_decode(input('data'),true);//将json字符串装数组
           
           $find= db('menu')->where(['menu_name'=>$post['menu_name'],'type'=>$post['type']])->find();
           $class=db('menu')->where('class_name',$post['class_name'])->find();
           if ($find) {
               $data['code']=0;
               $data['msg']="菜单名称已存在";
           }else if($class){
                $data['code']=0;
               $data['msg']="控制器已存在";
           }else{

                $arr = db('menu')->where('sort',$post['sort'])->find();
                if ($arr) {
                    db('menu')->where('sort','egt',$post['sort'])->setInc('sort',1);//自增1
                }
                $res=db('Menu')->field(true)->insertGetid($post);//入库自增主键
                $level = db('level')->where('id',1)->find();//查权限表
                $str = $level['menu_level']."|".$res;//追加
                $update= db('level')->where('id',1)->setField('menu_level',$str);//给超级管理员加权限
                $data= $this->ajaxreturn($res&&$update,"操作成功,请重新登录");
           }
           return json($data);   
        }
        $parent=db('menu')->where(['pid'=>0,'type'=>0])->select();//父级菜单
      
        $this->assign(['parent'=>$parent]);//将数据传递到页面
        return $this->fetch();//加载视图
    }
    //修改
    public function edit()
    {
        if (request()->isPost()) {
            $post=json_decode(input('data'),true);//字符串转数组
            // dump($post);die;
            $self=db('menu')->where('id',$post['id'])->find();//自身一条

            if ($self['menu_name']!=$post['menu_name']&&$self['type']!=$post['type']) {
                $find= db('menu')->where(['menu_name'=>$post['menu_name'],'type'=>$post['type']])->find();
                if ($find) {
                    $data['code']=0;
                    $data['msg']="菜单名称已存在";
                }else if ($self['class_name']!=$post['class_name']) {
                    $class = db('menu')->where(['class_name'=>$post['class_name']])->find();
                    if ($class) {
                        $data['code']=0;
                        $data['msg']="控制器名称已存在";
                    }else{
                        if ($self['sort']!=$post['sort']) {
                           $sort= db('menu')->where('sort',$post['sort'])->find();
                           if ($sort) {   
                                db('menu')->where('sort','egt',$post['sort'])->setInc('sort',1);//自增1 
                            }
                        }
                        $res= db('Menu')->field(true)->where('id',$post['id'])->update($post);
                        $data=$this->ajaxreturn($res);
                    }
                }else{
                    if ($self['sort']!=$post['sort']) {
                           $sort= db('menu')->where('sort',$post['sort'])->find();
                           if ($sort) {   
                                db('menu')->where('sort','egt',$post['sort'])->setInc('sort',1);//自增1 
                            }
                        }
                    $res= db('Menu')->field(true)->where('id',$post['id'])->update($post);
                    $data=$this->ajaxreturn($res);
                }
            }else{
                if ($self['class_name']!=$post['class_name']) {
                    $class = db('menu')->where(['class_name'=>$post['class_name']])->find();
                    if ($class) {
                        $data['code']=0;
                        $data['msg']="控制器名称已存在";
                    }else{
                        if ($self['sort']!=$post['sort']) {
                            $sort = db('menu')->where('sort',$post['sort'])->find();
                            if ($sort) {
                                db('menu')->where('sort','egt',$post['sort'])->setInc('sort',1);//自增1 
                            }  
                        }
                        $res= db('Menu')->field(true)->where('id',$post['id'])->update($post);
                        $data=$this->ajaxreturn($res);
                    }
                }else{
                    if ($self['sort']!=$post['sort']) {
                        $sort = db('menu')->where('sort',$post['sort'])->find();
                        if ($sort) {
                            db('menu')->where('sort','egt',$post['sort'])->setInc('sort',1);//自增1 
                        }  
                    }
                    $res= db('Menu')->field(true)->where('id',$post['id'])->update($post);
                    $data=$this->ajaxreturn($res);
                }
            }
            return json($data);
        }
        
        $id= input('id');
        $find=db('Menu')->where('id',$id)->find();//查一个
     
        $parent=db('menu')->where(['pid'=>0,'type'=>$find['type']])->select();//父级菜单
        
        $this->assign(['find'=>$find,'parent'=>$parent,]);//将数据传递到页面
        return $this->fetch();
    }
    //获取前后台父菜单
    public function getchild()
    {
        if (request()->isPost()) {
            $type=input('type');
            $list= db('menu')->where(['pid'=>0,'type'=>$type])->select();
            if ($list) {
                $data['code']=1;
                $data['msg']=$list;
            }else{
                $data['code']=0;
                $data['msg']="暂无数据";
            }
            return json($data);
        }
    }


}