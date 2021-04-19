<?php
namespace app\xcx\controller;

use think\Controller;
use EasyWeChat\Factory;


class Base extends Controller
{
	  private $appid = 'wxaf9cc48a6d81ba78';
	  private $secret = 'bcd11134d6c6f2d3da84314fcb1e0823';
	  //获取小程序用户openid
	  public function getopenid()
	  {
	  	 if(request()->isPost()){
	  	 	 $code = input('code');
	  	 	 $api = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->secret}&js_code={$code}&grant_type=authorization_code";
	  	 	 $json = file_get_contents($api);
	  	 	 $arr = json_decode($json,true);
	  	 	 $find = db('xcxuser')->where('openid',$arr['openid'])->find();
	  	 	 if(!$find){
	  	 	 	$ids = db('xcxuser')->field(true)->insertGetId($arr);
	  	 	 }else{
	  	 	 	$ids = $find['id'];
	  	 	 }
             $arr['uid'] = $ids;
	  	 	 return json($arr);
	  	 }
	  }	  
	   //Ajax返回
	   public function  ajaxreturn($arr)
	   {
          if($arr){
          	 $data['code'] = 1;
          	 $data['res'] = $arr;
          	 $data['msg'] = '请求成功';
          }else{
          	 $data['code'] = 0;
          	 $data['res'] = null;
          	 $data['msg'] = '暂无数据';
          }
          return json($data);
	   }
           //多图片路径判断
	   
	   public function httpimg($list,$img='pic',$page=true)
	   {
	      $domain = request()->domain();
	      if($page){
	        $arr = $list->toArray();  
	        $listarr = $arr['data'];
	      }else{
	        $listarr = $list;
	      }
	      foreach ($listarr as $k => $v) {
	          $pic = explode('|',$v[$img]);
	          $reg = '/(http(?:s?):\/\/)|\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})/';
	          if(preg_match($reg,$pic[0]))
	          {
	              $listarr[$k][$img] = $pic[0];
	          }else{
	              $pic[0] = str_replace('\\', '/', $pic[0]);
	              $listarr[$k][$img] = $domain.'/uploads/'.$pic[0];
	          }
	      }
	      return $listarr;
	   }
	   //单条数据中图片路径判断
	   public function httpimgone($find,$img='pic')
	   {
	      $domain = request()->domain();
	      if($find[$img]!=''){
	         $pic = explode('|',$find[$img]);
	         foreach ($pic as $k => $v) {          
	            $reg = '/(http(?:s?):\/\/)|\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})/';
	            if(preg_match($reg,$v))
	            {
	                $pic[$k] = $v;
	            }else{
	                $pic[$k] = str_replace('\\', '/', $v);
	                $pic[$k] = $domain.'/uploads/'.$pic[$k];
	            }
	         }
	      }else{
	         $pic = '';
	      }
	      $find[$img] = $pic;
	      return $find;
	   }
	     //获取当前用户购物车商品信息
   public function getcartinfo($uid)
   {
      $sumtotal = 0;
      $totalnum = 0;
      $cart = db('xcxcart')->where(['uid'=>$uid,'selected'=>1])->select();
      foreach ($cart as $key => $value) {
        $goods = db('goods')->field('id,name,pic,price,count')->where('id',$value['gid'])->find();
        $goods = $this->httpimgone($goods);
        $cart[$key]['goods'] = $goods;
        $cart[$key]['total'] = $goods['price']*$value['num'];
        $sumtotal += $cart[$key]['total'];
        $totalnum += $value['num'];
      }
      return ['cartarr'=>$cart,'sumtotal'=>$sumtotal,'totalnum'=>$totalnum];
   }
   //生成唯一订单号
   public function  getordernum()
   {
      $str = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);//生成唯一订单号
      return $str;
   }
}