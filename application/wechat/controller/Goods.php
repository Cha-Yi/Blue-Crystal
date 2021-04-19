<?php
namespace app\wechat\controller;
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
        $cateall = db('cate')->field('id,name')->where('pid',0)->select();//下拉
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
            'section'=>$section,
            'cateall'=>$cateall
            ]);
        return $this->fetch();//加载页面
    }
    //详情
    public function info()
    {
    	$id= input('id');
        $find = db('goods')->alias('a')->field('a.*,b.name bname')->join('tp5_brand b','a.cid = b.id')->where('a.id',$id)->find();
        $goodinfo = $this->httpimgone($find);
        // dump($goodinfo);die;
        $this->assign(['goodinfo'=>$goodinfo]);
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
