<?php
/*****************************************************************************************
	文件： payment/unionpay/submit.php
	备注： 生成提交按钮
	版本： 4.x
	网站： www.gzwebcreate.com
	作者： ryante <ryatne@163.com>
	时间： 2015年08月28日 14时06分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class unionpay_submit
{
	private $param;
	private $order;
	private $paydir;
	private $baseurl;
	private $app;
	private $dir_root;
	public function __construct($order,$param)
	{
		$this->app = $GLOBALS['app'];
		$this->param = $param;
		$this->order = $order;
		$this->paydir = $this->app->dir_root.'gateway/payment/unionpay/';
		$this->baseurl = $this->app->url;
		$this->dir_root = $this->app->dir_root;
		include_once($this->paydir."unionpay.php");
	}

	public function submit()
	{
		$payment = new unionpay_lib();
		$payment->sign_cert_pwd($this->param['param']['sign_cert_pwd']);
		$payment->set_cert_id($this->dir_root.$this->param['param']['sign_cert_file']);
		$payment->set_channel_type($this->param['wap']);
		$payment->txn_sub_type($this->param['param']['txn_sub_type']);
		$payment->form_param('merId',$this->param['param']['mer_id']);
		$payment->form_param('reqReserved',$this->order['passwd']);
		$currency = $this->app->model('currency')->get_one($this->order['currency_id']);
		if($currency && ($currency['code_num'] != '165' || $currency['code'] != 'CNY')){
			$payment->form_param('currencyCode',$currency['code_num']);
		}
		$payment->form_param('orderId',$this->order['sn']);
		$payment->form_param('txnTime',date("YmdHis",$this->app->time));
		$return_url = $this->app->url('payment','notice','id='.$this->order['id'],'www',true);
		$payment->form_param('frontUrl',$return_url);
		$payment->form_param('backUrl',$this->baseurl."gateway/payment/unionpay/notify_url.php");
		$payment->form_param('txnAmt',intval($this->order['price'] * 100));
		$payment->form_param('signature',$payment->sign());
		if($this->param['param']['trans_url_type'] == 'front'){
			$payment->param('form_url',$this->param['param']['trans_url']);
			echo $payment->create_html();
			exit;
		}
	}
}
?>