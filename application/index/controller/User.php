<?php
namespace app\index\controller;//命名空间
use think\Controller;//引入底层控制器

class User extends Base
{
	//用户登录
	public function login()
	{
		if (request()->isPost()){
			$post=input('post.data'); //获取提交内容
			// dump($post);die;
			$post=json_decode($post,true);//将json转换为数组
			
			$post['pwd']=md5($post['pwd']);//密码加密
			if(!captcha_check($post['code'])){
				$data['code']=0;
				$data['msg']="验证码错误";
			}else{
				$res=db('user')->where(['user'=>$post['user'],'pwd'=>$post['pwd'],'allow'=>1])->find();//数据查询
				if($res){
					session('user',$res); //session初始
					$data['code']=1;
					$data['msg']='成功';
				}else{
					$data['code']=0;
					$data['msg']='账户或密码错误';
				}
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
	//用户手机注册
    public function reg(){
    	if (request()->isPost()){
			$post=input('post.data'); //获取提交内容
			$post=json_decode($post,true);//将json转换为数组
			if ($post['code']!=session('smscode')) {
				$data['code']=0;
				$data['msg']="验证码错误";
			}else{
				$find = db('user')->where('user',$post['user'])->find();
				if ($find) {
					$data['code'] = 0;
					$data['msg'] = "账户已注册";
				}else if($post['pwd']!=$post['cpwd']){
					$data['code'] = 0;
					$data['msg'] = "密码不一致";
				}else{
					$post['pwd']= md5($post['pwd']);
					$insid = db('user')->field(true)->insertGetId($post);//返回主键
					$res = db('user_info')->field(true)->insert(['uid'=>$insid,'tel'=>$post['tel'],'pic'=>'758293770.jpg']);//默认头像
					$data = $this->ajaxreturn($res&&$insid);
				}
			}
			return json($data);
    	}
    	return $this->fetch();
    }
    //短信发送
    public function sendsms()
    {
    	if (request()->isPost()) {
    		$tel= input('tel');
    		$code = mt_rand(1111,9999);//随机数
    		session('smscode',$code);
    		$data = sms($tel,$code);//短信验证
    		return $data;
    	}
    }
    //邮箱发送
    public function sendemali()
    {
    	if (request()->isPost()) {
    		$email= input('email');
    		$code = mt_rand(1111,9999);//随机数
    		session('ecode',$code);
    		$data = email($email,$code);//短信验证
    		return $data;
    	}
    }
    //邮箱注册
    public function regemail()
    {
    	if (request()->isPost()){
			$post=input('post.data'); //获取提交内容
			$post=json_decode($post,true);//将json转换为数组
			if ($post['code']!=session('ecode')) {
				$data['code']=0;
				$data['msg']="验证码错误";
			}else{
				$find = db('user')->where('user',$post['user'])->find();
				if ($find) {
					$data['code'] = 0;
					$data['msg'] = "邮箱已注册";	
				}else if($post['pwd']!=$post['cpwd']){
					$data['code'] = 0;
					$data['msg'] = "密码不一致";
				}else{
					$post['pwd']= md5($post['pwd']);
					$insid = db('user')->field(true)->insertGetId($post);//返回主键
					$res = db('user_info')->field(true)->insert(['uid'=>$insid,'tel'=>$post['tel']]);
					$data = $this->ajaxreturn($res&&$insid);
				}
			}
			return json($data);
    	}
    }
}
