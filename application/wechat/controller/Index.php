<?php
namespace app\wechat\controller;
 // use think\Controller;//引入底层Controller

class Index extends Base
{
    public function index()
    {
        if (!$this->checklogin()) {
            // session('target_url',url('Index/index'));
            // $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect('https://tp5.sangchen.work/wechat/index/callback');
            // header("location:$response");
            $response = $this->send_empower('https://tp5.sangchen.work/wechat/index/callback');
            return $response;
        }
        $user = session('wechat');//用户信息

        $find = db('wx_user')->where('openid',$user['original']['openid'])->find();
        
        if (!$find) {
            db('wx_user')->field(true)->insert($user);

        }else{
            db('wx_user')->field(true)->where('openid',$user['original']['openid'])->update($user);
        }

    	$bannerlist=db('Banner')->field('pic')->where('type',2)->select();

    	$bannerlist = $this->httpimg($bannerlist,'pic',false);//图片路径处理
   
        $brand = db('brand')->limit(3)->select();
        
        $floor = $this->getcategoods(3,3,'id desc');
       // dump($floor);die;
        $this->assign(['bannerlist'=>$bannerlist,'floor'=>$floor,'brand'=>$brand]);
        return $this->fetch();//加载页面
    }
    //回调方法
    public function callback()
    {
        $oauth = $this->app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        session('wechat',$user->toArray());//用户信息存储session
        
        $this->redirect('Index/index');
    }
    //退出登录
    public function logout()
    {
        session(null);
    }
}
