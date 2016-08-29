<?php
/*****************************************************************************************
	文件： payment/unionpay/notice.php
	备注： 支付通知页
	版本： 4.x
	网站： www.gzwebcreate.com
	作者： ryante <ryatne@163.com>
	时间： 2014年5月3日
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class unionpay_notice
{
	private $paydir;
	private $order;
	private $payment;
	public function __construct($order,$payment)
	{
		$this->paydir = $GLOBALS['app']->dir_root.'gateway/payment/unionpay/';
		$this->order = $order;
		$this->param = $payment;
		include_once($this->paydir."unionpay.php");
	}

	//获取订单信息
	public function submit()
	{
		global $app;
		if($this->order['status']){
			return true;
		}
		$payment = new unionpay_lib();
		$payment->set_verify_id($app->dir_root.$this->param['param']['verify_cert_file']);
		$params = $_POST;
		if($params['respCode'] != '00'){
			error("付款失败，错误信息：".$params['respMsg'],'','error');
		}
		$chk = $payment->verify($params);
		if(!$chk){
			error('付款签名验证失败，请登录支付平台检查','','error');
		}
		if($params['respMsg'] == 'success'){
			$pay_date = $app->time;
			$price = round(($params['settleAmt']/100),2);
			//
			$data = array();
			$data['traceNo'] = $params['traceNo'];
			$data['traceTime'] = $params['traceTime'];
			$data['queryId'] = $params['queryId'];
			$data['currencyCode'] = $params['currencyCode'];
			$array = array('status'=>1,'ext'=>serialize($data));
			$app->db->update_array($array,'payment_log',array('id'=>$this->order['id']));
			if($this->order['type'] == 'order'){
				$order = $app->model('order')->get_one_from_sn($this->order['sn']);
				if($order){
					$app->model('order')->update_order_status($order['id'],'paid');
					$param = 'id='.$order['id']."&status=paid";
					$app->model('task')->add_once('order',$param);
					$note = P_Lang('订单支付完成，编号：{sn}',array('sn'=>$order['sn']));
					$log = array('order_id'=>$order['id'],'addtime'=>$app->time,'who'=>$app->user['user'],'note'=>$note);
					$app->model('order')->log_save($log);
					//增加order_payment
					$array = array('order_id'=>$order['id'],'payment_id'=>$this->param['id']);
					$array['title'] = $this->param['title'];
					$array['price'] = $price;
					$array['dateline'] = $app->time;
					$array['ext'] = serialize($data);
					$order_payment = $app->model('order')->order_payment($order['id']);
					if(!$order_payment){
						$app->model('order')->save_payment($array);
					}else{
						$app->model('order')->save_payment($array,$order_payment['id']);
					}
				}
			}
		}
		return true;
	}
}
?>