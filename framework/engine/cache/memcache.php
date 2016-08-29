<?php
/***********************************************************
	Filename: {weburl}/engine/cache/memcache.php
	Note	: Memcache引挈
	Version : 4.0
	Web		: www.gzwebcreate.com
	Author  : ryante <ryante@163.com>
	Update  : 2013年7月21日
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class cache_memcache extends cache
{
	private $server = 'localhost';
	private $port = '11211';
	private $conn;
	private $config_data;
	public function __construct($config)
	{
		$this->config_data = $config;
		parent::__construct($config);
		$this->config($config);
	}

	private function config($config)
	{
		$this->server = $config["server"] ? $config["server"] : "localhost";
		$this->port = $config["port"] ? $config["port"] : "11211";
		if($this->status && !$this->conn){
			$this->start();
		}
	}


	public function __destruct()
	{
		parent::__destruct();
		if($this->conn){
			$this->conn->close();
		}
		unset($this);
	}
	
	private function start()
	{
		if(class_exists('Memcache')){
			$this->conn = new Memcache;
			@$this->conn->connect($this->server,$this->port);
			$info = $this->conn->getExtendedStats();
			$str = $this->server.':'.$this->port;
			if(!$info || !$info[$str]){
				$this->error("连接Memcache服务器失败，请检查");
				$this->status(false);
				$this->__destruct();
			}
		}else{
			$this->status(false);
		}
	}

	//设置缓存状态
	public function status($status="")
	{
		if($status != ""){
			$this->status = $status ? true : false;
			if($this->status && !$this->conn){
				$this->start();
			}
		}
		return $this->status;
	}

	//写入缓存信息
	public function save($id,$content)
	{
		if(!$this->status){
			return false;
		}
		if(!$this->conn){
			$this->config($this->config_data);
			if(!$this->conn){
				return false;
			}
		}
		$this->_time();
		$this->conn->set($id,$content,MEMCACHE_COMPRESSED,$this->timeout);
		$this->_time();
		$this->_count();
		if($GLOBALS['app']->db){
			$this->key_list($id,$GLOBALS['app']->db->cache_index($id));
		}
		return true;
	}

	public function get($key)
	{
		if(!$this->status){
			return false;
		}
		if(!$this->conn){
			$this->config($this->config_data);
			if(!$this->conn){
				return false;
			}
		}
		$this->_time();
		$content = $this->conn->get($key);
		$this->_time();
		$this->_count();
		if($content){
			return $content;
		}
		return false;
	}

	public function delete($id)
	{
		if($this->conn){
			$this->conn->delete($id);
		}
		if($this->key_list && $this->key_list){
			unset($this->key_list[$id]);
		}
		
		return true;
	}

	public function clear()
	{
		if(!$this->conn){
			$this->config($this->config_data);
			if(!$this->conn){
				return false;
			}
		}
		$this->conn->flush();
		return true;
	}

	public function expired()
	{
		return true;
	}
}
?>