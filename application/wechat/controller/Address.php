<?php
namespace app\index\controller;

class Address extends Base
{
	//选择地址
    public function index()
    {   
    	if (!$this->checklogin()) {
    		$this->redirect('User/login');
    	}
    	$list = db('address')->where('uid',session('user.id'))->select();
    	// dump($list);die;
    	$provinces = db('provinces')->select();
    	$this->assign(['list'=>$list,'provinces'=>$provinces]);
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
    //添加地址
    public function add()
    {
    	if (request()->isPost()) {
    		if (!$this->checklogin()) {
    			$data['code'] = 0;
    			$data['msg'] = "请登录";
    		}else{
    			$post = json_decode(input('data'),true);
    			$count = db('address')->where('uid',session('user.id'))->count();
    			if ($count>=4) {
    				$data['code']= 0;
    				$data['msg'] = "地址已达上限,请删除或修改后再操作";
    			}else{
    				if ($post['curruct']== 1) {
    					db('address')->where('uid',session('user.id'))->setField('curruct',0);//默认
    				}
    				$post['uid'] = session('user.id');
    				$res = db('address')->field(true)->insert($post);
    				$data = $this->ajaxreturn($res);
    			}
    		}
    		return json($data);
    	}
    }
    //修改默认地址
    public function edit()
    {
    	if (request()->isPost()) {
    		$id = input('id');
    		db('address')->where('uid',session('user.id'))->setField('curruct',0);//默认
    		$res = db('address')->where('id',$id)->setField('curruct',1);
    		return json($this->ajaxreturn($res));
    	}
    }
    //删除已有地址
    public function del()
    {
    	if (request()->isPost()) {
    		$id = input('id');
    		$res = db('address')->delete($id);
    		return json($this->ajaxreturn($res));
    	}
    }
}