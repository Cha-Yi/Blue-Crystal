<?php
namespace app\index\controller;

class Vip extends Base
{
	//页面
    public function index()
    {	
    	if (!$this->checklogin()) {
    		$this->redirect('User/login');//重定向
    	}
        $select = input('select');
        $ip = request()->ip();//获取客户端ip
        if ($select) {
            $info = db('cart')->where(['uid'=>session('user.id'),'selected'=>1])->find();
            $user_info = db('user_info')->where('uid',session('user.id'))->find();
            $user_info = $this->httpimgone($user_info);
            $info['nickname'] =$user_info['nickname'];
            $info['pic'] = $user_info['pic'];
            $history = db('cart')->field('gid,pic,name,price')->join('tp5_goods','tp5_goods.id = tp5_cart.gid')->where('uid',session('user.id'))->order('tp5_cart.id desc')->limit(4)->select();
            $history = $this->httpimg($history,'pic',false);
            $title = "我的收藏";
            // dump($history);die;
        }else{
        	$info = db('user_info')->where('uid',session('user.id'))->find();
        	$info = $this->httpimgone($info);
        	$history = db('history')->field('gid,pic,name,price')->join('tp5_goods','tp5_goods.id = tp5_history.gid','left')->where('ip',$ip)->order('tp5_history.id desc')->limit(4)->select();
            $history = $this->httpimg($history,'pic',false);
            $title = "历史记录";
        }
        // dump($history);die;
    	$this->assign(['info'=>$info,'history'=>$history,'title'=>$title]);
    	return $this->fetch();
    }
    // //用户信息/省份
    public function info()
    {
         if (!$this->checklogin()) {
            $this->redirect('User/login');
        }
        if (request()->isPost()) {
            $data= input('data');
            $data =json_decode($data,true);
            $data['address'] = $data['province'].$data['city'].$data['area'].$data['road'];
            $res = db('user_info')->field(true)->where('uid',session('user.id'))->update($data);
            return json($this->ajaxreturn($res));
        }
        $list = db('address')->where('uid',session('user.id'))->select();
        // dump($list);die;
        $provinces = db('provinces')->select();
        $info = db('user_info')->where('uid',session('user.id'))->find();
        $info = $this->httpimgone($info);
        $this->assign(['list'=>$list,'provinces'=>$provinces,'info'=>$info]);
        return $this->fetch();
    }
    //密码修改
    public function edit()
    {
        if (!$this->checklogin()) {
            $this->redirect('User/login');
        }
        if (request()->isPost()) {
           $post = json_decode(input('data'),true);
           // dump($post);die;
           $find = db('user')->where('id',session('user.id'))->find();
           if ($post['oldpwd']!='') {
               if ($find['pwd'] ==md5($post['oldpwd']) ) {
                   $pwd = md5($post['pwd']);
                   $res = db('user')->field(true)->where('id',session('user.id'))->setField('pwd',$pwd);
                   $data = $this->ajaxreturn($res);
               }else{
                    $data['code']=0;
                    $data['msg']="原密码错误,请重新输入";
               }
           }else{
            $data['code'] =0;
            $data['msg'] ="请输入原密码"; 
           }
           return json($data);
        }
        $info = db('user_info')->where('uid',session('user.id'))->find();
        $info = $this->httpimgone($info);
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }
    //AJAX获取城市/区县
    public function getcity()
    {
        if (request()->isPost()) {
            $provinceid = input('provinceid');
            $cityid = input('cityid');
            if ($provinceid) {
                $list = db('cities')->where('provinceid',$provinceid)->select();
            }else{
                $list = db('areas')->where('cityid',$cityid)->select();
            }
            // dump($list);die;
            return json($this->ajaxreturn($list));
        }
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