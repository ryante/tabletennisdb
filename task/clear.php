<?php
/*****************************************************************************************
	文件： task/clear.php
	备注： 清空缓存
	版本： 4.x
	网站： www.gzwebcreate.com
	作者： ryante <ryatne@163.com>
	时间： 2015年10月11日 11时35分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
$this->cache->clear();
return true;
?>