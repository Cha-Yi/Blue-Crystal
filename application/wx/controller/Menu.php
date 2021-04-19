<?php
namespace app\wx\controller;//命名空间

use app\wx\controller\Card;

class Menu extends Base
{
	//菜单
	public function add()
	{
		$card = new Card;
		
		$link = $card->createhj();
		$buttons = [
		    [
		        "type" => "click",
		        "name" => "今日歌曲",
		        "key"  => "MUSIC"
		    ],
		    [
		        "name"       => "菜单",
			        "sub_button" => [
			            [
			                "type" => "view",
			                "name" => "我的小微",
			                "url"  => "https://tp5.sangchen.work/wechat"
			            ],
			            [
			                "type" => "click",
			                "name" => "短视频",
			                "key"  => "Vid"
			            ],
			            [
			                "type" => "click",
			                "name" => "背景图",
			                "key" => "BGimg"
			            ],
			        ],
		    ],
		    [
		        "name"       => "个人中心",
			        "sub_button" => [
			            [
			                "type" => "click",
			                "name" => "签到",
			                "key"  => "QD"
			            ],
			            [
			                "type" => "click",
			                "name" => "福利",
			                "key"  => "FL"
			            ],
			            [
			                "type" => "view",
			                "name" => "卡券",
			                "url"  => $link
			            ],
			        ],
		    ],
		];
		$res = $this->app->menu->create($buttons);
		dump($res);
	}
}