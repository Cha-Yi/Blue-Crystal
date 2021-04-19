<?php
namespace app\admin\controller;//命名空间
// use think\Controller;//引入底层控制器
// 继承base底层
class Goods extends Base
{
    public function Index()
    {
    	
    	//查询多条数据 降序 分页
    	$list=db('goods')->alias('a')->field('a.id,a.name,price,pic,cid,bid,count,time,sales,b.name cname,c.name bname')->join('tp5_cate b','a.cid = b.id','left')->join('tp5_brand c','a.bid = c.id','left')->order('a.id desc')->paginate(10);
        /*$listarr= $list->toArray();//将字符串转数组
        foreach ($listarr['data'] as $key => $value) {
            $arr= explode("|",$value['pic']);//将字符串分解数组
            $listarr['data'][$key]['pic']=$arr[0];//取出数组第一个值
          }*/
          $listarr= $this->httpimg($list);//调用多图片路径判断方法
         
    	$this->assign(['list'=>$list,'listarr'=>$listarr]);//将数据传递到页面
    	// dump($list);die;
        return $this->fetch();//加载视图
    }
    //删除一条
    public function del()
    {
    	if (request()->isPost()) {
    		$iid=input('iid');	
    		$res=db('goods')->delete($iid);
    		$att=db('attribute')->where('gid',$iid)->delete();
           $data=$this->ajaxreturn($res&&$att);
    		return json($data);
    	}
    }
    //删除多条
    public function delall()
    {
        if (request()->isPost()) {
            $ids= input('ids');
            $res=db('goods')->where('id','in',$ids)->delete();
            $att= db('attribute')->where('gid','in',$ids)->delete();
            $data=$this->ajaxreturn($res&&$att);
            return json($data);
        }
    }
    //添加
    public function add()
    {   
        if (request()->isPost()) {
             
           $post= json_decode(input('data'),true);//将json字符串装数组
           //dump($post);die;
           $post['time']=time();
           $gid=db('goods')->field(true)->insertGetId($post);
           for ($i=0; $i <3 ; $i++) { 
                if (!empty($post['attrname['.$i.']'])) {
                    $data['name']= $post['attrname['.$i.']'];
                    $data['val']= $post['val['.$i.']'];
                    $data['gid']= $gid;
                    db('attribute')->insert($data);
                }
           }
           $data= $this->ajaxreturn($gid);
           return json($data);   
        }
        $cate=db('cate')->select();
        $brand=db('brand')->select();
        $people=db('people')->select();
        $nav=db('menu')->where('type',1)->select();
        $this->assign(['cate'=>$cate,'brand'=>$brand,'nav'=>$nav,'people'=>$people]);//将数据传递到页面
        return $this->fetch();//加载视图
    }
    //修改
    public function edit()
    {
        if (request()->isPost()) {
            $post=json_decode(input('data'),true);//字符串转数组
            $len = intval(input('len'));
            // dump($post);die;
            $num = 0;
            $idarr= [];//提交过来的商品属性id数组
            for ($i=0; $i < $len; $i++) { 
                if (!empty($post['attrname['.$i.']'])) {                   
                    if (isset($post['attrid['.$i.']'])) {
                        $id= $post['attrid['.$i.']'];
                        array_push($idarr, $post['attrid['.$i.']']);
                        $data['name'] = $post['attrname['.$i.']'];
                        $data['val'] = $post['val['.$i.']'];
                        $num += db('attribute')->where('id',$id)->update($data);//修改
                    }else{
                        $data['name'] = $post['attrname['.$i.']'];
                        $data['val'] = $post['val['.$i.']'];
                        $data['gid'] = $post['id'];

                        $num += db('attribute')->insert($data);//添加
                        
                    }
                }else{
                    $attrribute = db('attribute')->where('gid',$post['gid'])->select();
                    $dbarr = [];//数据库中当前商品属性id数组
                    foreach ($attrribute as $key => $value) {
                        $dbarr[]=$value['id'];
                    }
                    $arr = array_diff($dbarr , $idarr) ;//计算差级
                    $num += db('attribute')->where('gid',$post['id'])->where('id','in',$arr)->delete();//删除
                }
            }
            $res= db('goods')->field(true)->where('id',$post['id'])->update($post);
            $data=$this->ajaxreturn($res||$num);
            return json($data);
        }
        $id= input('id');
        $find=db('goods')->where('id',$id)->find();//查一个
        $find['img']=$find['pic'];
        $find= $this->httpimgone($find);//单图片处理
     
        $cate=db('cate')->select();
        $brand=db('brand')->select();
        $people=db('people')->select();
        $nav=db('menu')->where('type',1)->select();
        $attr=db('attribute')->where('gid',$id)->select();
        $this->assign(['find'=>$find,'cate'=>$cate,'brand'=>$brand,'nav'=>$nav,'attr'=>$attr,'people'=>$people]);//将数据传递到页面
        return $this->fetch();
    }
    
    //自动采集
    public function auto()
    {
        set_time_limit(0);
        $res = $this->caiji();
        $count =ceil($res / 50);
        for ($i=2; $i <=$count; $i++) { 
            sleep(10);
            $this->caiji($i);
        }
    }
    //采集数据
    public function caiji($page=1)
    {
        //接口地址

        $host = 'https://openapi.dataoke.com/api/goods/get-goods-list';

        $appKey = '5f4ef000ca318';//应用的key

        $appSecret = 'e27db13844f0bfeeb17a3c736ba481ea';//应用的Secret

        //默认必传参数

        $data = [

            'appKey' => $appKey,

            'version' => 'v1.2.3',

            'pageId' =>$page

        ];

        //加密的参数

        $data['sign'] =$this->makeSign($data,$appSecret);

        //拼接请求地址

        $url = $host .'?'. http_build_query($data);

        

        //执行请求获取数据
        $output = file_get_contents($url);
        $output = json_decode($output,true);
        $arr = $output['data']['list'];
        foreach ($arr as $key => $value) {
            $datarr[$key]['name'] = $value['title'];
            $datarr[$key]['dname'] = $value['dtitle'];
            $datarr[$key]['time'] = strtotime($value['createTime']);
            $datarr[$key]['cid'] = $value['cid'];
            $datarr[$key]['pic'] = $value['mainPic'];
            $datarr[$key]['price'] = $value['originalPrice'];
            $datarr[$key]['into'] = $value['desc'];

        }
        db('caiji')->field(true)->insertAll($datarr);
    }

    /**参数加密

    * @param $data

    * @param $appSecret

    * @return string

    */
    function makeSign($data, $appSecret)
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {

            $str .= '&' . $k . '=' . $v;
        }
        $str = trim($str, '&');
        $sign = strtoupper(md5($str . '&key=' . $appSecret));
        return $sign;
    }
}