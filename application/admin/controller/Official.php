<?php
namespace app\admin\controller;//命名空间

class Official extends Base 
{
    //列表
    public function index()
    {   
        
        $list = db('wxuser')->order('id desc')->paginate(10);       
        $this->assign(['list'=>$list]);
        return $this->fetch();//加载视图
    }
}