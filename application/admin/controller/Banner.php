<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
class Banner extends Base
{
    public function Index()
    {
    	
    	//查询多条数据 降序 分页
    	$list=db('Banner')->order('id desc')->paginate(10);
          $listarr= $this->httpimg($list);//调用多图片路径判断方法
         
    	$this->assign(['list'=>$list,'listarr'=>$listarr]);//将数据传递到页面
    	// dump($list);die;
        return $this->fetch();//加载视图
    }
    //删除单
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');
            $find= db('banner')->where('id',$iid)->find();
    		if ($find['is_show']==1) {
                $data['code']=0;
                $data['msg']="轮播图展示中无法删除.";
            }else{
                $res=db('Banner')->delete($iid);
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
            $find= db('banner')->where('is_show',1)->where('id','in',$ids)->find();
            //echo db('banner')->getlastsql();die;//打印sql语句
            if ($find) {
                $data['code']=0;
                $data['msg']="轮播图展示中无法删除.";
            }else{
                $res=db('Banner')->where('id','in',$ids)->delete();
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
         
           $res=db('Banner')->field(true)->insert($post);
          
           $data= $this->ajaxreturn($res);
           return json($data);   
        }
      
        return $this->fetch();//加载视图
    }
    //修改
    public function edit()
    {
        if (request()->isPost()) {
            $post=json_decode(input('data'),true);//字符串转数组
            $res= db('Banner')->field(true)->where('id',$post['id'])->update($post);
            
            $data=$this->ajaxreturn($res);
            return json($data);
        }
        $id= input('id');
        $find=db('Banner')->where('id',$id)->find();//查一个
        
        $find= $this->httpimgone($find);//单图片处理
        $find['pic']=$find['pic'][0];//把pic覆盖掉就ok了
            
        $this->assign(['find'=>$find]);//将数据传递到页面
        return $this->fetch();
    }
    //图片上传
    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
       if($info){
           // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
          $data['code']=1;
          $data['msg']= $info->getSaveName();
        
        }else{
          // 上传失败获取错误信息
          $data['code']=0;
          $data['msg']= $file->getError();
        }
        return json($data);
    }
    //是否显示
    public function recommend()
    {
        if (request()->isPost()) {
            $post= input('post.');//拿到post的所有
          
              $res= db('banner')->where('id',$post['id'])->update(['is_show'=>$post['is_show']]);//修改update
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