<?php
namespace app\admin\controller;//命名空间

class Index extends Base
{
    public function Index()
    {
    
        return $this->fetch();//加载视图
    }
}
