<?php
namespace app\xcx\controller;

//小程序购物车接口
class Cart extends Base
{	
	//小程序加入购物车
    public function add()
    { 
       if(request()->isPost()){
       	  $post = input('post.');
       	  $cart = db('xcxcart')->where(['uid'=>$post['uid'],'gid'=>$post['gid'],'aid'=>$post['aid']])->find();
       	  $count = db('goods')->where('id',$post['gid'])->find();
       	  if($cart){
       	  	 $num = $cart['num']+1;
             if($count['count']<$num){
             	$data['code'] = 0;
             	$data['msg'] = '库存不足';
             }else{
             	$res = db('xcxcart')->where(['uid'=>$post['uid'],'gid'=>$post['gid'],'aid'=>$post['aid']])->setInc('num');
             	if($res){
             		$data['code'] = 1;
             	    $data['msg'] = '加入成功';
             	}else{
             		$data['code'] = 0;
             	    $data['msg'] = '加入失败';
             	}
             }
       	  }else{
             if($count['count']<1){
             	$data['code'] = 0;
             	$data['msg'] = '库存不足';
             }else{
             	$res = db('xcxcart')->field(true)->insert($post);
             	if($res){
             		$data['code'] = 1;
             	    $data['msg'] = '加入成功';
             	}else{
             		$data['code'] = 0;
             	    $data['msg'] = '加入失败';
             	}
             }
       	  }
       	  return json($data);
       }
    }
   //小程序购物车列表
    public function index()
    { 
        if(request()->isPost()){
            $uid = input('uid');
            db('xcxcart')->where('uid',$uid)->setField('selected',0);
            $cart = db('xcxcart')->where('uid',$uid)->select();
            foreach ($cart as $key => $value) {
                  $goods = db('goods')->field('name,price,pic,cid')->where('id',$value['gid'])->find();
                  $goods = $this->httpimgone($goods);
                  $cart[$key]['goods'] = $goods;
                  $cart[$key]['sum'] = $goods['price']*$value['num'];
            }
            return $this->ajaxreturn($cart);
        }
    }
    //小程序购物车数量编辑
    public function edit()
    { 
        if(request()->isPost()){
            $post = input('post.');
            
            $cart = db('xcxcart')->where('id',$post['id'])->find();
            $count = db('goods')->where('id',$cart['gid'])->value('count');
           
            if($post['num']>$count){
                  $data['code'] = 0;
                  $data['msg'] = '库存不足';
            }else{
                  $res = db('xcxcart')->where('id',$post['id'])->setField('num',$post['num']);
                  if($res){
                        $data['code'] = 1;
                        $data['msg'] = '成功';
                  }else{
                        $data['code'] = 0;
                        $data['msg'] = '系统繁忙';
                  }
            }           
            return json($data);
        }
    }
    //购物车结算
    public function nowpay()
    {
        if(request()->isPost()){
            $cartstr = input('cartstr');
            $res = db('xcxcart')->where('id','in',$cartstr)->setField('selected',1);
            return $this->ajaxreturn($res);
        }
    }
}