<?php
namespace app\xcx\controller;

//小程序发货地址接口
class Order extends Base
{	
	//小程序订单提交
    public function add()
    { 
       if(request()->isPost()){
          $post = input('post.');
          $find = db('xcxorder')->where(['uid'=>$post['uid'],'status'=>1])->find();
            if($find){
               $data['code'] = 0;
               $data['msg'] = '您有未支付订单';
            }else{ 
                $cart = $this->getcartinfo($post['uid']);
                $order['address_id'] = $post['address_id'];
                $order['uid'] = $post['uid'];
                $order['order_num'] = $this->getordernum();
                $order['sumtotal'] = $cart['sumtotal'];
                $order['time'] = time();
                $res = db('xcxorder')->insert($order);
                if($res){                 
                  $num = 0;
                  foreach ($cart['cartarr'] as $key => $value) {
                    $order_info['order_num'] = $order['order_num'];
                    $order_info['gid'] = $value['gid'];
                    $order_info['price'] = $value['goods']['price'];
                    $order_info['pic'] = $value['goods']['pic'][0];
                    $order_info['name'] = $value['goods']['name'];
                    $order_info['aid'] = $value['aid'];
                    $order_info['num'] = $value['num'];
                    $num += db('xcxorder_info')->insert($order_info);
                    //db('xcxcart')->where('id',$value['id'])->delete();
                  }
                }                              
              if($res&&$num){
                 $data['code'] = 1;
                 $data['res'] = [
                     'order_num'=>$order['order_num'],
                     'sumtotal'=>$order['sumtotal']
                    ];
                 $data['msg'] = '下单成功';
              }else{
                 $data['code'] = 0;
                 $data['res'] = [];
                 $data['msg'] = '系统繁忙';
              }             
            }
             return json($data);
       }
    }
  //小程序确认支付信息
    public function ordersure()
    { 
         if(request()->isPost()){
            $uid = input('uid');           
            $cart = db('xcxcart')->where(['uid'=>$uid,'selected'=>1])->select();
            foreach ($cart as $key => $value) {
                  $goods = db('goods')->field('name,price,pic,cid')->where('id',$value['gid'])->find();
                  $goods = $this->httpimgone($goods);
                  $cart[$key]['goods'] = $goods;
                  $cart[$key]['sum'] = $goods['price']*$value['num'];
            }
            return $this->ajaxreturn($cart);
        }
    }
}