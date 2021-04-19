<?php
namespace app\wechat\controller;

use think\Controller;//引入底层Controller
use EasyWeChat\Factory;

class Base extends Controller
{	

  //受保护的属性
    protected $app;
    protected $config = [
            'app_id' => 'wx8a94cc0a73742011',
            'secret' => '3a130aa60bdd5a894aa3210b0b059805',
            'token' => 'sangsir',
            'response_type' => 'array'
            //...
        ];
	//构造函数
    public function __construct()
    {
    	parent::__construct();//将父的构造拿过来解决重载
      $this->app = Factory::officialAccount($this->config);
      $arr = [
          'openLocation',
          'getLocation',
          'chooseImage',
          'chooseWXPay',
          'scanQRCode'
          ];
      $json = $this->app->jssdk->buildConfig($arr,true);//共4个,参数true为开发模式

    	$cateall = db('cate')->field('id,name')->where('pid',0)->select();//下拉
    	$nav = db('menu')->field('id,menu_name')->where(['type'=>2,'pid'=>0])->order('sort')->limit(5)->select();//导航
    	$cname = strtolower(request()->controller());//当前页面控制器名

      $shoopnum = db('cart')->where('uid',session('user.id'))->count();//购物车商品数
      $domain = request()->domain();//域名
    	$this->assign(['cateall'=>$cateall,'nav'=>$nav,'domain'=>$domain,'shoopnum'=>$shoopnum,'cname'=>$cname,'json'=>$json]);//抛入页面
    	
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
  //获取商品楼层
   public function getcategoods($num,$goodnum,$order="id")
   {
   		$floor= db('cate')->field('id,name')->where('index',1)->order($order)->limit($num)->select();
      $n = 10;
      foreach ($floor as $k => $v) {
            $child = db('goods')->field('id,name,pic,price')->where('cid',$v['id'])->limit($goodnum)->select();
            $floor[$k]['child']=$this->httpimg($child,'pic',false);
        
        $flooradv = db('advince')->where('id','in',"$n")->order('id')->select();

        foreach ($flooradv as $key => $value) {
        $domain = request()->domain();//域名
        $pic=explode("|",$value['pic']);//将字符串分解数组
          foreach ($pic as $keys => $vals) {      
            $reg = '/(http(?:s?):\/\/)|\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})/';
            if(preg_match($reg,$vals))//php匹配正则符合
            {
                $pic[$keys] = $vals;
            }else{
                $pic[$keys] = str_replace('\\', '/', $vals);//替换
                $pic[$keys] = $domain.'/uploads/'.$pic[$keys];//处理
            }
          }
          $flooradv[$key]['pic']=$pic;

          $floor[$k]['nav']=$flooradv;
        }
        $n++;
      }
       
      // dump($floor);die;
   		return $floor;
   }
  //ajax返回数组
   public function ajaxreturn($res,$smsg="操作成功",$fmsg='操作失败')
   {
    if ($res) {
          $data['code']=1;
          $data['info']=$res;
          $data['msg']=$smsg;
      }else{
          $data['code']=0;
          $data['msg']=$fmsg;
      }
      return $data;
    }
  //验证登录
  public function checklogin()
  {
    if (!session('?wechat')) {
      $stat=false;
    }else{
      $stat=true;
    }
    return $stat;
  }
  //获取当前用户商品信息
  public function getcateinfo()
  {
    $sumtotal = 0;//总计初始0
    $totalnum = 0;//数量初始0
    $cart= db('cart')->where(['uid'=>session('user.id'),'selected'=>1])->select();
    foreach ($cart as $key => $value) {
        $goods = db('goods')->field('id,name,pic,price,count')->where('id',$value['gid'])->find();
        $goods = $this->httpimgone($goods);
        $cart[$key]['goods']= $goods;
        $cart[$key]['total']= $goods['price']*$value['num'];//总价
        $sumtotal += $cart[$key]['total'];
        $totalnum += $value['num'];//数量
    }
    return ['cartarr'=>$cart,'sumtotal'=>$sumtotal,'totalnum'=>$totalnum];
  }
  //生成唯一随机数 订单号
  public function getordernum()
  {
    $str = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    return $str;
  }
  //网页授权发起授权
  public function send_empower($url)
  {
    $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect($url);
    return $response;
  }
}
