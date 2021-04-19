<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
class Advince extends Base
{
    public function Index()
    {
    	
    	//查询多条数据 降序 分页
    	$list=db('Advince')->order('id desc')->paginate(10);
          $listarr= $this->httpimg($list);//调用多图片路径判断方法
         
    	$this->assign(['list'=>$list,'listarr'=>$listarr]);//将数据传递到页面
    	// dump($list);die;
        return $this->fetch();//加载视图
    }
    //添加
    public function add()
    {   
        if (request()->isPost()) {
             
           $post= json_decode(input('data'),true);//将json字符串装数组
         
           $res=db('Advince')->field(true)->insert($post);
          
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
            $res= db('Advince')->field(true)->where('id',$post['id'])->update($post);
            
            $data=$this->ajaxreturn($res);
            return json($data);
        }
        $id= input('id');
        $find=db('Advince')->where('id',$id)->find();//查一个
        
        $find['img']=$find['pic'];
        $find= $this->httpimgone($find);//处理图片路径
        // dump($find);die;   
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
  
    
}