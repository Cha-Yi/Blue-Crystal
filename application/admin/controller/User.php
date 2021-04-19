<?php
namespace app\admin\controller;//命名空间
use think\Controller;//引入底层控制器

class User extends Controller
{
	//用户登录
	public function login()
	{
		if (request()->isPost()){
			$post=input('post.data'); //获取提交内容
			// dump($post);die;
			$post=json_decode($post,true);//将json转换为数组
			
			$post['pwd']=md5($post['pwd']);//密码加密
			$res=db('admin')->where(['user'=>$post['user'],'pwd'=>$post['pwd'],'allow'=>1])->find();//数据查询
		// echo db('admin')->getlastsql();die;输出sql
		// dump($res);die;
		
			if($res){
				session('admin',$res); //session初始
				$level = db('level')->where('id',$res['level'])->find();//拿出admin中level
				$arr = explode("|",$level['menu_level']);//把menu_level拆分数组
				foreach ($arr as $key => $value) {
					$menu = db('menu')->where('id',$value)->find();//利用menu_level查nemu
					array_push($arr,$menu['pid']);
				}
				// dump($arr);die;
				session('level',$arr);//存入session
				$data['code']=1;
				$data['msg']='成功';
			}else{
				// $this->error('账户或密码错误');//错误弹窗
				$data['code']=0;
				$data['msg']='账户或密码错误/账户状态异常';
			}
			return json($data);//数组转换字符串

		}
		return $this->fetch();//加载视图
	}
	//退出登录
	public function logout(){
		session(null);  //清空session
		$this->redirect('User/login');//重定向
	}
}
