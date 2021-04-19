<?php
namespace app\wx\controller;//命名空间

use think\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Music;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\ShortVideo;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use app\wx\controller\Msg;//引入Msg类

class Base extends Controller
{
	//受保护的属性
	protected $app;
	protected $config = [
			    'app_id' => 'wx8a94cc0a73742011',
			    'secret' => '3a130aa60bdd5a894aa3210b0b059805',
			    'token' => 'sangsir',
			    'response_type' => 'array'
			    //...
			];
	protected $access_token = null;
	//构造函数	
	public function __construct()
	{
		parent::__construct();
		$this->app = Factory::officialAccount($this->config);
	}
	//消息回复
    public function handeltext($message)
    {
    	switch ($message['Content']) {
    		case '你好':
        		$data = 'Hollow';
        		break;
    		case '背景':
        		$data = new Image('eKBhiqIFvPTkpHkKSpe0jOM_sqWjdy487uEci3GvBFndN08D48VNHC7v7JmOJz-7');
        		break;
        	case '音乐':
        		$data = 'try home';
        		break;
        	case 'try':
        		$arr =  $this->getmusicarr('Try','p!nk','http://tp5.sangchen.work/music/Try.mp3');
        		$data = $this->handelmusic($arr);
        		break;
    		case 'home':
	    		$arr =  $this->getmusicarr('Home','Blaze U Remix','http://tp5.sangchen.work/music/Home.mp3');
	    		$data = $this->handelmusic($arr);
	    		break;
	    	case '视频':
	    		$mediaId = ['zmC-aapeAD11HQUdHKxL6Ocepwvg37lH5R8AC_lgXlQ4xV-BpuKLudPYgCkK5_12','Pjc2qYJKhh7AxcZ2gXVlRB4SSlUpSSOhHicv7RPHqCWOGZdRSSnySVqbAxzl0ddI'];
	    		$i = mt_rand(0,count($mediaId)-1);
	    		if ($i == 0) {
	    			$title = '王者';
			        $description = '我无敌你随意';
	    		}else{
	    			$title = '耀';
			        $description = '星空闪耀夜空';
	    		}
	        	$data = new Video($mediaId[$i], [
			        'title' => $title,
			        'description' => $description
			    ]);
	    		break;
	    	case '小视频':
	    		$mediaId = 'zmC-aapeAD11HQUdHKxL6Ocepwvg37lH5R8AC_lgXlQ4xV-BpuKLudPYgCkK5_12';
	    		$arr =[
	    			'title' => '秀',
	    			'description' => '素材测试',
	    			'thumb_media_id' =>'b0CF5U3pogtgGAeGqzvJ8vh680qo1YtF1WMpWF2fxxszGLAN4EhsWz3BNrtfFWdB',
	    		];
	    		$data = new ShortVideo($mediaId,$arr);
	    		break;
	    	case '图文':
	    		$items = [
				    new NewsItem([
				        'title'       => '时政微周刊丨总书记的一周',
				        'description' => '总书记的一周（8月31日—9月6日）',
				        'url'         => 'https://view.inews.qq.com/wxn/WXN20200907005471030?refer=nwx&bat_id=1111117669&cur_pos=0&grp_type=region&rg=3&grp_index=1&grp_id=1311117671&rate=0&ft=0&pushid=2020090701&bkt=0&openid=o04IBAIqwJB4JRRH9Pk3HT06tPMs&tbkt=G&groupid=1599456627&msgid=0&key=8e5ed336c1c2e4272fa93e747e470c623b58ed3e337d13bb78b56c35e2addeab4938f22b91457d7c2514a0c6898697cb488ccd39a2a5ad8f8ce1bf90fa60f5f23e62e5e64413059719e5e5c0c9c5b0d6257a188375357b916d47d00b752dbe5624191b520ad864ea0b81db714ace367bfc10de28691e704e4605f4afb7f58d0e&version=62090538&devicetype=Windows%2010%20x64&wuid=oDdoCtxBs9bfJVokNBN-SEmHdpEk&cv=0x62090538&dt=15&lang=zh_CN&pass_ticket=qcLPEJWtrlZ%2BphiQ0wSylr1fAWR1GkHPAyLw4G089Q7z83rOzJk5vx5PvFOU71ne',
				        'image'       => 'https://tp5.sangchen.work/static/index/images/wxlogo/wek.jpg',
				        // ...
				    ]),
				];
	    		$data = new News($items);;
	    		break;
    		default:
                $url = "http://api.qingyunke.com/api.php?key=free&appid=0&msg=".$message['Content'];
                $arr = json_decode(file_get_contents($url),true);
                $data = $arr['content'];
                break;
	    }
	    return $data;
    }

    //音乐回复
    public function handelmusic($arr)
    {  	
		$music = new Music($arr);
		return $music;
    }
    //获取音乐数组
    public function getmusicarr($title,$description,$url)
    {
    	$arr = ['title'=>$title,
		        'description'=>$description,
		        'url'=>$url,
		        'hq_url'=>$url
		       ];
		return $arr;		
    }
    
    //获取access_token
    public function getaccess_token()
    {
    	//自动获取access_token
    	$appid = 'wx8a94cc0a73742011';//appid
    	$secret = '3a130aa60bdd5a894aa3210b0b059805';//app秘钥

    	$accurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

    	$json = json_decode(file_get_contents($accurl),true);//正常返回json格式需转json数组

    	$this->access_token = $json['access_token'];//access_token
    	return $this->access_token;
    }
    //事件处理
    public function Replyevent($message)
    {
    	$msg = new Msg;//实例化类
    	switch ($message['Event']) {
    		case 'subscribe':
    			$user = $this->app->user->get($message['FromUserName']);
    			$find = db('wx_user')->where('openid',$user['openid'])->find();
    			
    			if (!$find) {
    			    db('wx_user')->field(true)->insert($user);

    			}else{
    			    db('wx_user')->field(true)->where('openid',$user['openid'])->update($user);
    			}
    			$data = $msg->follow_msg($message['FromUserName']);//关注
    			break;
    		case 'CLICK':
    			$data = $this->click_btn($message,$msg);//点击
    			break;
    		default:
    			$data = "收到其他事件消息";
    			break;
    	}
    	return $data;
    }
	//自定义点击事件
	public function click_btn($message,$msg)
	{		
		switch ($message['EventKey']) {
			case 'MUSIC':
				$data = 'try home';
				break;
			case 'BGimg':
				$data = new Image('XjG4Xm2j2TiKaJTTITHsEF16lYkxpjd3ileH1loOZqzc5I3QAoX_D2GetNx4AtR9');
				break;
			case 'FL':
				$data = $msg->tem_msg($message['FromUserName']);
				break;
			case 'Vid':
				$data = new Video('Pjc2qYJKhh7AxcZ2gXVlRB4SSlUpSSOhHicv7RPHqCWOGZdRSSnySVqbAxzl0ddI', [
							        'title' => "哈哈皮",
							        'description' => "来抓我啊!!!!"
							    ]);
				break;
			case 'QD':
				$data = $this->sign($message['FromUserName']);//签到
				break;
			default:
				$data = "收到其他点击事件";
				break;
		}
		return $data;
	}
	//签到
	public function sign($openid)
	{
		$start_time = strtotime(date("Y-m-d",time()));
		$end_time = ($start_time+60*60*24)-1;
		$find = db('wx_user')->where('openid',$openid)->find();
		$res = db('sign_in')->where('uid',$find['id'])->where('time','between',[$start_time,$end_time])->find();
		if ($res) {
			$data = '已签到';
		}else{
			$insert = db('sign_in')->insert(['uid'=>$find['id'],'time'=>time()]);
			if ($insert) {
				$data = '签到成功';
			}else{
				$data = '系统繁忙,请稍后重试';
			}			
		}
		return $data;
	}
}
		