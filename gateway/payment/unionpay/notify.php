<?php
/*****************************************************************************************
	文件： payment/unionpay/notify.php
	备注： 异步通知
	版本： 4.x
	网站： www.gzwebcreate.com
	作者： ryante <ryatne@163.com>
	时间： 2015年08月30日 12时12分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class unionpay_notify
{
	private $paydir;
	private $order;
	private $payment;
	public function __construct($order,$payment)
	{
		$this->order = $order;
		$this->param = $payment;
		$this->paydir = $GLOBALS['app']->dir_root."gateway/payment/unionpay/";
		include_once($this->paydir."unionpay.php");
	}

	public function submit()
	{
		if($this->order['status']){
			return true;
		}
		global $app;
		$payment = new unionpay_lib();
		$payment->set_verify_id($app->dir_root.$this->param['param']['verify_cert_file']);
		$array = array($app->config['ctrl_id'],$app->config['func_id'],'sn');
		$params = $_GET;
		foreach($array as $key=>$value){
			unset($params[$value]);
		}
		if($params['respMsg'] != 'success'){
			exit('fail');
		}
		$chk = $payment->verify($params);
		if(!$chk){
			exit('fail');
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
		exit('success');
	}
}
?>