<?php
namespace app\index\controller;
 // use think\Controller;//引入底层Controller

class Goods extends Base
{
	//页面
    public function index()
    {
        $nid= input('nid',19);
        $order=input('order','count');//默认用第二个参数
        $max=input('max');
        $min=input('min');
        $bid=input('bid');
        $cid=input('cid');
        $peoplearr=input('people/a');//转换数组
        $section=input('section');
        $name = trim(input('name'));//搜索框处理去掉空格
        // dump($name);die;
        $where['nid']=$nid;
        if (isset($bid)) {
            $where['bid']=$bid;
        }
        if (isset($cid)) {
            $where['cid']=$cid;
        }
        if (isset($peoplearr)&&!empty($peoplearr)) {
           // dump($peoplearr);die;
            $str= count($peoplearr)==1?$peoplearr[0]:$peoplearr;
            
            $where['peo_id']=['in',$str];
            $peoplearr = implode(',', $peoplearr);
        }
        if (isset($section)) {
            $arr =explode('-', $section);//拆分判断数组长度
            if (count($arr)==1) {
                $num= intval($section);
                $where['price']=['egt',$num];//大于num
            }else{
                $where['price']=['between',$arr];//区间查询  
            }
        }
        //搜索框条件
        if (isset($name)) {
            $where['name'] = ['like',"%$name%"];
        }

        if (isset($min)&&isset($max)) {
            //处理最大与最小数
            if ($min>$max) {
                $numprice=$min;
                $min=$max;
                $max=$numprice;
            }
            $list= db('goods')->where($where)->where('price','between',[$min,$max])->order($order.' desc')->paginate(15,false,['query'=>request()->param()]);
            // dump($list);die;
        }else{
            $list= db('goods')->where($where)->order($order.' desc')->paginate(15,false,['query'=>request()->param()]);//带上参数下一页也会是同一个条件的商品
        }
        
        $listarr=$this->httpimg($list);
        $brand=db('brand')->select();
        $cate=db('cate')->select();
        $people=db('people')->select();
        $price=db('price')->order('sort')->select();

        // dump($list);die;
        $this->assign([
            'list'=>$list,
            'listarr'=>$listarr,
            'nid'=>$nid,
            'order'=>$order,
            'brand'=>$brand,
            'cate'=>$cate,
            'people'=>$people,
            'price'=>$price,
            'bid'=>$bid,
            'cid'=>$cid,
            'peoplearr'=>$peoplearr,
            'section'=>$section
            ]);
        return $this->fetch();//加载页面
    }
    //详情
    public function info()
    {
    	$id= input('id');
        $ip = request()->ip();//获取客户端ip
        $history = db('history')->where(['gid'=>$id,'ip'=>$ip])->find();//历史记录
        if (!$history) {
             db('history')->insert(['gid'=>$id,'ip'=>$ip]);
        }
    	db('goods')->where('id',$id)->setInc('hits');
    	$find= db('goods')->where('id',$id)->find();
        // dump($find);die;
    	$find= $this->httpimgone($find);
    	$sales=	db('goods')->field('id,pic,price,sales')->order('sales desc')->limit(3)->select();
    
    	$salesarr= $this->httpimg($sales,'pic',false);
        $count = db('collection')->where(['gid'=>$find['id'],'uid'=>session('user.id')])->count();//统计
        if ($this->checklogin()) {
            $foll = db('collection')->where(['gid'=>$find['id'],'uid'=>session('user.id'),'is_show'=>1])->find();
            if ($foll) {
                $type=1;
            }else{
                $type=0;
            }
        }else{
            $type=0;
        }
    	// dump($salesarr);die;
        $attr = db('attribute')->where('gid',$id)->select();//属性查询
        foreach ($attr as $key => $value) {
            $attr[$key]['val']=explode('|',$value['val']);
        }
        // dump($attr);die;
    	$this->assign(['find'=>$find,'salesarr'=>$salesarr,'count'=>$count,'type'=>$type,'attr'=>$attr]);
    	
    	return $this->fetch();//加载页面
    }
    //添加收藏
    public function collect()
    {
        if (request()->isPost()) {
            $gid=input('gid');
            $aid=input('aid');

            $type=input('type');
             // session('user',['id'=>1]);//模拟登陆
            
            if ($this->checklogin()) {
                $foll = db('collection')->where(['gid'=>$gid,'aid'=>$aid,'uid'=>session('user.id')])->find();
                // echo db('collection')->getlastsql();

                $num = $type==0?1:0;//
                if($foll){
                    $res = db('collection')->where(['gid'=>$gid,'aid'=>$aid,'uid'=>session('user.id')])->setField('is_show',$num);//修改
                    // echo db('collection')->getlastsql();die;
                }else{                   
                    $res =  db('collection')->insert(['gid'=>$gid,'aid'=>$aid,'uid'=>session('user.id')]);//添加
                }
                    if ($res) {                    
                        $data['code']=1;
                        $data['msg']=$num;
                    }else{
                        $data['code']=0;
                        $data['msg']=$num;
                    }
            }else{
                $data['code']=0;
                $data['msg']='请登录';
            }
            return json($data); 
        }
    }
}
