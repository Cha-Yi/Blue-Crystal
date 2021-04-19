<?php
namespace app\xcx\controller;

//小程序发货地址接口
class Address extends Base
{	
	//小程序用户发货地址
    public function index()
    { 
       if(request()->isPost()){
          $uid = input('uid');
          $address = db('xcxaddress')->where('uid',$uid)->order('curruct desc')->select();
          foreach ($address as $key => $value) {
          	 $address[$key]['address'] = $value['province'].$value['city'].$value['area'].$value['road'];
          }
          return $this->ajaxreturn($address);
       }
    }
}