<?php
namespace app\wx\controller;//命名空间

class Card extends Base
{
	//创建卡券
    public function create()
    {
    	$card = $this->app->card;

	    $cardType = 'GROUPON';

	    $attributes = [
	      'base_info' => [
	          'brand_name' => '微信餐厅',
	          'code_type' => 'CODE_TYPE_TEXT',
	          'title' => '辣嘴串串',
	          "logo_url" => 'http://tp5.sangchen.work/static/index/images/wxlogo/9p.jpg',
	          "color" =>"Color010",
	          "date_info" => [
	              "type" => "DATE_TYPE_FIX_TERM",
	              "fixed_term" => 15 ,
	              "fixed_begin_term" => 0
	          ],
	           "sku"=> [
              		"quantity"=> 60
          		],
	          //...
	      ],
	      'advanced_info' => [
	          'use_condition' => [
	              'accept_category' => '鞋类',
	              'reject_category' => '阿迪达斯',
	              'can_use_with_other_discount' => true,
	          ],
	          //...
	      ],
	      "deal_detail" => "以下锅底2选1（有菌王锅、麻辣锅、大骨锅、番茄锅、清补凉锅、酸 菜鱼锅可选）：\n大锅1份 20元\n小锅2份 16元 "
	    ];

	    $openids = [];
	    $arr = db('wx_user')->field('openid')->limit(10)->select();
	    foreach ($arr as $k => $v) {
	    	$openids[] = $v['openid'];
	    }
	    $setTest = $card->setTestWhitelist($openids);//添加测试白名单

		$result = $card->create($cardType, $attributes);//创建成功card_id
		$cards = [
		    'action_name' => 'QR_CARD',
		    'expire_seconds' => 1800,
		    'action_info' => [
		      'card' => [
		        'card_id' => $result['card_id'],
		        'is_unique_code' => false,
		        'outer_id' => 1,
		      ],
		    ],
		  ];

		$result = $card->createQrCode($cards);
		$getQrCode = $card->getQrCodeUrl($result['ticket']);//换取二维码链接

		return $getQrCode;
	}
	//货架
	public function createhj()
	{
		$card = $this->app->card;
		$result = $card->list(0, 10, 'CARD_STATUS_NOT_VERIFY');//待审核
		$banner = 'http://tp5.sangchen.work/static/index/images/wxlogo/timg.jpg';
		$pageTitle = '蓝水晶优惠大派送';
		$canShare  = false;
		$scene = 'SCENE_NEAR_BY';
		$cardList = [];
		foreach ($result['card_id_list'] as $k => $v) {
			$cardList[] =[
				'card_id' => $v,
		    	'thumb_url' => 'http://tp5.sangchen.work/static/index/images/wxlogo/kq.jpg',
			];
		}
		$res = $card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList);
		$url = isset($res['url'])?$res['url']:"https://baidu.com";
		return $url;
	}
	//卡券code
	public function code()
	{
		
	}
	//删除卡券
	public function delete()
	{
		$card = $this->app->card;
		$result = $card->list(0, 10, 'CARD_STATUS_NOT_VERIFY');//待审核获取全部卡券
		foreach ($result['card_id_list'] as $k => $v) {
			$card->delete($v);//一次只能删除一个
		}
		echo "ok";
	}
	//核销
	public function HX()
	{

	}
}