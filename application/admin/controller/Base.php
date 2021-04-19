<?php
namespace app\admin\controller;//命名空间
use think\Controller;//引入底层控制器

class Base extends Controller
{
	// 构造函数
	public function __construct(){
		parent::__construct();//将父亲的构造拿进来
		$this->checklogin();//检查登录
    $conname=strtolower(request()->controller());//获取当前的控制器名转小写
   
    $model= db('menu')->where('class_name',$conname)->find();//查表里名字与控制器名相同
  
    $menu = $this->getmenu();//获取菜单
    $this->checklevel($model,$conname);//验证权限
    $this->assign(['menu'=>$menu,'pid'=>$model['pid']]);//将值抛入页面

	}
	//验证登录
    public function checklogin()
    {
    	//判断session有没有admin
    	if(!session('?admin')){
    		$this->redirect('User/login');//重定向
    	}
    }
    //验证权限
    public function checklevel($model,$conname)
    {
      if (!in_array($model['id'],session('level'))&&$conname!='index') {
        $this->redirect('User/logout');//重定向直接退出
      }
    }

    //多图片路径判断
    public function httpimg($list,$img='pic',$page=true)
   {
      $domain = request()->domain();//域名
      if($page){
        $arr = $list->toArray();  //将字符串转数组
        $listarr = $arr['data']; 
      }else{
        $listarr = $list;
      }
      foreach ($listarr as $k => $v) {
          $pic = explode('|',$v[$img]);
          $reg = '/(http(?:s?):\/\/)|\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})/';
          if(preg_match($reg,$pic[0]))//php匹配正则符合
          {
              $listarr[$k][$img] = $pic[0];
          }else{
              $pic[0] = str_replace('\\', '/', $pic[0]);//替换
              $listarr[$k][$img] = $domain.'/uploads/'.$pic[0];
          }
      }
      return $listarr;
   }
    //单条数据中图片路径判断
    public function httpimgone($find,$img='pic')
   {
      $domain = request()->domain();//域名
      if ($find[$img]!='') {
        $pic=explode("|",$find[$img]);//将字符串分解数组
        foreach ($pic as $k => $v) {      
          $reg = '/(http(?:s?):\/\/)|\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})/';
          if(preg_match($reg,$v))//php匹配正则符合
          {
              $pic[$k] = $v;
          }else{
              $pic[$k] = str_replace('\\', '/', $v);//替换
              $pic[$k] = $domain.'/uploads/'.$pic[$k];//处理
          }
        }
      }else{
        $pic='';
      }
      $find[$img]=$pic;
      return $find;//返回数组
   }
   //ajax返回数组
   public function ajaxreturn($res,$smsg="操作成功",$fmsg='操作失败')
   {
    if ($res) {
          $data['code']=1;
          $data['msg']=$smsg;
      }else{
          $data['code']=0;
          $data['msg']=$fmsg;
      }
      return $data;
   }
   //获取菜单
   public function getmenu(){
    $menu = db('menu')->field('id,menu_name,sort')->where(['pid'=>0,'type'=>0])->order('sort')->select();//排序用sort默认asc
    foreach ($menu as $key => $value) {
       $child = db('menu')->field('id,menu_name,class_name,sort')->where('pid',$value['id'])->order('sort')->select();
       foreach ($child as $k => $v) {
         $child[$k]['class_name']= $v['class_name'].'/index';
       }
       $menu[$key]['child'] = $child;
    }
    // dump($menu);die;
    return $menu;
   }

   //图片上传
    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
       if($info){
          $path= str_replace('\\','/',$info->getSaveName());//字符串替换
          $data['code']=1;
          $data['msg']= $path;
        
        }else{
          // 上传失败获取错误信息
          $data['code']=0;
          $data['msg']= $file->getError();
        }
        return json($data);
    }
}
