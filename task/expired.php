<?php
/*****************************************************************************************
	文件： task/expired.php
	备注： 自动删除过期
	版本： 4.x
	网站： www.gzwebcreate.com
	作者： ryante <ryatne@163.com>
	时间： 2015年11月17日 09时38分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
$this->cache->expired();
return true;
?>