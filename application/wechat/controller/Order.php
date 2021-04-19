<?php
namespace app\index\controller;

class Order extends Base
{
	//确认订单
    public function add()
    {
    	if (request()->isPost()) {
    		if (!$this->checklogin()) {
    			$data['code'] = 0;
    			$data['msg'] = "请登录";
    		}else{
    			$find = db('order')->where(['uid'=>session('user.id'),'status'=>1])->find();//查询当前用户的订单
    			if ($find) {
    				$data['code'] = 0;
    				$data['msg'] = "您有未支付订单";
    			}else{
    				$address = db('address')->where(['uid'=>session('user.id')])->find();

                    if (session('?nowbuy')) {
                        $goods = db('goods')->field('id,name,pic,price,count')->where('id',session('nowbuy.gid'))->find();//商品
                        $goods = $this->httpimgone($goods);
                        $cart['cartarr'][0]['goods']= $goods;
                        $cart['cartarr'][0]['aid']= session('nowbuy.aid');
                        $cart['cartarr'][0]['gid']= session('nowbuy.gid'); 
                        $cart['cartarr'][0]['num']= session('nowbuy.num');
                        $cart['sumtotal']= $goods['price']*session('nowbuy.num');//总价
                        $cart['totalnum'] = session('nowbuy.num');//数量
                    }else{
                        $cart = $this->getcateinfo();
                    }
    				
    				$order['address_id'] = $address['id'];//地址
    				$order['uid'] = session('user.id');
    				$order['sumtotal'] = $cart['sumtotal'];//订单价钱
    				$order['time'] = time();
    				$order['order_num'] = $this->getordernum();//订单号
    				$res = db('order')->insert($order);

    				if ($res) {
    					session('sumtotal',$order['sumtotal']);
    					session('order_num',$order['order_num']);
    					$num = 0;
    					foreach ($cart['cartarr'] as $key => $value) {
    						$order_info['order_num'] = $order['order_num'];
    						$order_info['gid'] = $value['gid'];
    						$order_info['price'] = $value['goods']['price'];
    						$order_info['pic'] = $value['goods']['pic'][0];
    						$order_info['num'] =  $value['num'];
    						$order_info['name'] = $value['goods']['name'];
    						$order_info['aid'] = $value['aid'];
    						$num += db('order_info')->insert($order_info);
    						// db('cart')->where('id',$value['id'])->delete();
    					}
    				}
    				// dump($_SESSION);die;
                    if ($res&&$num)session('nowbuy',null);//清除session
                    $data = $this->ajaxreturn($res&&$num);
    			}
    		}
    		return json($data);
    	}
    }
    //confrim确认地址->下一页面提交订单
    public function confrim()
    {	
    	if (!$this->checklogin()) {
    		$this->redirect('User/login');
    	}
        if (session('?nowbuy')) {
            $goods = db('goods')->field('id,name,pic,price,count')->where('id',session('nowbuy.gid'))->find();//商品
            $goods = $this->httpimgone($goods);
            $cart['cartarr'][0]['goods']= $goods;
            $cart['cartarr'][0]['aid']= session('nowbuy.aid'); 
            $cart['cartarr'][0]['num']= session('nowbuy.num');
            $cart['sumtotal']= $goods['price']*session('nowbuy.num');//总价
            $cart['totalnum'] = session('nowbuy.num');//数量
        }else{
            $cart = $this->getcateinfo();
        }
    	// dump($cart);die;
    	$address = db('address')->where(['uid'=>session('user.id'),'curruct'=>1])->find();
    	$this->assign(['cart'=>$cart,'address'=>$address]);
    	return $this->fetch();
    }
    //订单中心
    public function index()
    {
        if (!$this->checklogin()) {
            $thihs->redirect('User/login');
        }
        $order_num = trim(input('order_num'));//订单号查询
        if (!empty($order_num)) {
            $where['order_num'] = ['like',"%$order_num%"];//模糊查询
        }

        $status = input('status');//状态判断组装where条件 
        if (empty($status)) {
            $where['tp5_order.uid'] = session('user.id');
            $status = 0;
        }else{
            $where['tp5_order.uid'] = session('user.id');
            $where['status'] = $status;
        }
        $order = db('order')->field('tp5_order.*,a_name')->join('tp5_address a','a.id = tp5_order.address_id','left')->where($where)->order('tp5_order.id desc')->paginate(3,true);//简洁分页
        $count = db('order')->field('tp5_order.*,a_name')->join('tp5_address a','a.id = tp5_order.address_id','left')->where($where)->order('tp5_order.id desc')->count();//统计

        $orderarr = $order->toArray();//$order是个对象所以转数组后自带['data']
        foreach ($orderarr['data'] as $key => $value) {
           $order_info = db('order_info')->where('order_num',$value['order_num'])->select();
           $orderarr['data'][$key]['order_info'] = $order_info;
        }
         //dump($orderarr);die;
        $this->assign(['order'=>$order,'orderarr'=>$orderarr['data'],'status'=>$status,'count'=>$count]);
        return $this->fetch();
    }
    //订单取消
    public function cancel()
    {
       if (request()->isPost()) {
            if (!$this->checklogin()) {
                $data['code'] = 0;
                $data['msg'] = "请登录";
            }else{
                $id = input('iid');
                $res = db('order')->where('id',$id)->setField('status',6);
                $data = $this->ajaxreturn($res);
            }
            return json($data);
        }
    }
}