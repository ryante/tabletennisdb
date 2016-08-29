<?php
/***********************************************************
	Filename: {weburl}/model/temp.php
	Note	: 临时存储器（适用于自动数据保存）
	Version : 4.0
	Web		: www.gzwebcreate.com
	Author  : ryante <ryante@163.com>
	Update  : 2012-12-10 00:04
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class temp_model_base extends phpok_model
{
	function __construct()
	{
		parent::model();
	}

	public function __destruct()
	{
		parent::__destruct();
		unset($this);
	}

	function save($data,$id=0)
	{
		return false;
	}

	function get_one($id)
	{
		return false;
	}

	function chk($tbl,$admin_id)
	{
		return false;
	}

	function clean($tbl,$admin_id)
	{
		return false;
	}
}
?>