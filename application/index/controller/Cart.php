<?php
namespace app\index\controller;

class Cart extends Base
{
	//加入购物车
    public function add()
    {   
        if (request()->isPost()) {
            if (!$this->checklogin()) {
                $data['code'] = 0;
                $data['msg'] = '请登录';
            }else{
                $post = input('post.');
                $post['uid']  = session('user.id');
                $post['aid'] = rtrim($post['aid'],'|');
                $find = db('cart')->where(['gid'=>$post['gid'],'uid'=>$post['uid'],'aid'=>$post['aid']])->find();
                $num  = $find['num']+$post['num'];
                if ($post['count']<$num) {
                    $data['code'] = 0;
                    $data['msg'] = "库存不足";
                }else{
                    if ($find) {
                        $res = db('cart')->field(true)->where('id',$find['id'])->setInc('num',$post['num']);//增加
                    }else{
                        $res = db('Cart')->field(true)->insert($post);    
                    }
                    $data=$this->ajaxreturn($res,"加入成功");
                }               
            }
            return json($data);
        }
    }

    //购物车列表
    public function index()
    {
        if (!$this->checklogin()) {
            $this->redirect('User/login');//重定向
        }
        $cart= db('cart')->where('uid',session('user.id'))->select();
        foreach ($cart as $key => $value) {
            $goods = db('goods')->field('id,name,pic,price,count')->where('id',$value['gid'])->find();
            $goods = $this->httpimgone($goods);
            $cart[$key]['goods']= $goods;
            $foll = db('collection')->where(['gid'=>$value['gid'],'aid'=>$value['aid'],'is_show'=>1])->find();
            $cart[$key]['type'] = $foll?1:0;
             // echo db('collection')->getlastsql();die;
        }
        // dump($cart);die;
        $this->assign('cart',$cart);
        return $this->fetch();
    }
    // 购物车列表数量修改
    public function edit()
    {
        if (request()->isPost()) {
            $post = input('post.');
            // dump($post);die;
            if ($post['num']>$post['count']) {
                $data['code'] = 0;
                $data['msg'] ="库存不足";
            }else{
                $res = db('cart')->where('id',$post['cartid'])->setField('num',$post['num']);
                $data = $this->ajaxreturn($res);
            }
            return json($data);
        }
    }
    //确认订单
    public function conform()
    {
        if (request()->isPost()) {
            $cartid = input('cartid');
                $res = db('cart')->where('id','in',$cartid)->setField('selected',1);
                $res1 = db('cart')->where('id','not in',$cartid)->setField('selected',0); 
            return json($this->ajaxreturn(1));
        }
        $cart= db('cart')->where(['uid'=>session('user.id'),'selected'=>1])->select();
        // dump($Cart);die;
        $num = 0;
        $total= 0; 
        foreach ($cart as $key => $value) {
            $goods = db('goods')->field('id,name,pic,price,count')->where('id',$value['gid'])->find();
            $goods = $this->httpimgone($goods);
            $cart[$key]['goods']= $goods;
            $num += $value['num'];
            $total += $value['num']*$goods['price'];

        }
        $this->assign(['cart'=>$cart,'num'=>$num,'total'=>$total]);
        return $this->fetch();
    }
    //购物车商品单条删除
    public function del()
    {
        if (request()->isPost()) {
            $id = input('id');
            $res = db('cart')->delete($id);
            $data = $this->ajaxreturn($res);
            return json($data);
        }
    }
    //购物车商品多条删除
    public function delall()
    {
        if (request()->isPost()) {
            $ids = rtrim(input('ids'),',');
            $res = db('cart')->where('id','in',$ids)->delete();
            $data = $this->ajaxreturn($res);
            return json($data);
        }
    }
    //立即购买
    public function nowbuy()
    {
        if (request()->isPost()) {
            if (!$this->checklogin()) {
                $data['code'] = 0;
                $data['msg'] = '请登录';
            }else{                
                $post = input('post.');
                $post['aid'] = rtrim($post['aid'],'|');
                if ($post['count']<$post['num']) {
                    $data['code'] = 0;
                    $data['msg'] = "库存不足";
                }else{
                    session('nowbuy',$post);
                    $data['code'] = 1;
                    $data['msg'] = "成功";
                }

            }
            return json($data);
        }

    }
}
