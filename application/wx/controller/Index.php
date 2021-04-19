<?php
namespace app\wx\controller;//命名空间

use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Music;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Transfer;

class Index extends Base
{
    public function Index()
    {
   
	//称闭包方法里面传另一个方法
	$this->app->server->push(function ($message) {
		
		//return new Transfer();
	    switch ($message['MsgType']) {
	        case 'event':
	        	return $this->Replyevent($message);//事件处理	            
	            break;
	        case 'text':
	        	return $this->handeltext($message);//消息回复            
	            break;
	        case 'image':
	        	$mediaId = ['eKBhiqIFvPTkpHkKSpe0jOM_sqWjdy487uEci3GvBFndN08D48VNHC7v7JmOJz-7','PIbX_CEj-sC9V_tU4GNVVItavQtNkikxxj8pc4LKYJ_zmJOLz34ecSiQiwhfSFKD','5pIWurl7N_Yw1q4VcT44MwDvHAeYKybgGDvvlPA3LJvSlwhM2L35r4g0LnLvdVop'];
	        	$i = mt_rand(0,count($mediaId)-1);//随机取出,从0下标开始 总数量-1
	        	$image = new Image($mediaId[$i]);
	            return $image;
	            break;
	        case 'voice':
	        	$mediaId = 'RnOh1w2q7r14uBazqCd5VQkyYyhPCNtbkT7tW1wWD7v7NAbO22hxZFCET-hXFYw0';
	        	$voice = new Voice($mediaId);
	            return $voice;
	            break;
	        case 'video':
	        	$mediaId = 'Pjc2qYJKhh7AxcZ2gXVlRB4SSlUpSSOhHicv7RPHqCWOGZdRSSnySVqbAxzl0ddI';
	        	$video = new Video($mediaId, [
			        'title' => '耀',
			        'description' => '星光闪耀夜空',
			    ]);
	            return $video;
	            break;
	        case 'location':
	            return '收到坐标消息';
	            break;
	        case 'link':
	            return '收到链接消息';
	            break;
	        case 'file':
	            return '收到文件消息';
	        // ... 其它消息
	        default:
	            return '收到其它消息';
	            break;
	    }

    		//...
	});

	$response = $this->app->server->serve();

	// 将响应输出
	return $response->send();  

	}
	//用户信息
	public function user_info()
	{
		$user = $this->app->user->get('o7I306SdcQi0fkU_8sBbaNdLLG3M');
		dump($user);die;
	}
}
 