<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
class Order extends Base
{
	//列表
    public function Index()
    {	
        //查询->降序->分页
    	$list= db('Order')->alias('a')->field('a.*,b.user')->join('tp5_user b','a.uid = b.id','left')->paginate(10);//记得申明id否则会冲突
    	// dump($list);die;
    	$this->assign(['list'=>$list]);//抛入页面
        return $this->fetch();//加载视图
    }
     //查看
    public function edit()
    {	
    	$id=input('id');
    	$order= db('Order')->alias('a')->field('a.*,c.user,b.a_name,b.tel,b.province,b.city,b.area,b.road')->join('tp5_address b','a.address_id = b.id','left')->join('tp5_user c','a.uid = c.id','left')->where('a.id',$id)->find();
    	$order_info = db('order_info')->where('order_num',$order['order_num'])->select();

    	$this->assign(['order'=>$order,'order_info'=>$order_info]);//反馈页面
    	return $this->fetch();
    }
    //修改订单状态
    public function change()
    {   
        if (request()->isPost()) {
            $id = input('id');
            $res = db('order')->where('id',$id)->setField('status',3);
            return json($this->ajaxreturn($res));
        }      
    }
}
