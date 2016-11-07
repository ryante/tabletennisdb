<?php
/***********************************************************
	Filename: {weburl}/www/index_control.php
	Note	: 网站首页及APP的封面页
	Version : 4.0
	Web		: www.gzwebcreate.com
	Author  : ryante <ryante@163.com>
	Update  : 2015年06月06日 09时09分
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class index_control extends phpok_control
{
	public function __construct()
	{
		parent::control();
	}

	public function index_f()
	{
		$this->model('task')->set_title_status();
		$tmp = $this->model('id')->id('index',$this->site['id'],true);
		$tplfile = 'index';
		if($tmp){
			$pid = $tmp['id'];
			$page_rs = $this->call->phpok('_project',array('pid'=>$pid));
			$this->phpok_seo($page_rs);
			$this->assign("page_rs",$page_rs);
			if($page_rs["tpl_index"] && $this->tpl->check_exists($page_rs["tpl_index"])){
				$tplfile = $page_rs["tpl_index"];
			}
			unset($page_rs);
		}
		$hotMaterial = $this->hot_material();
		$this->assign('hotMaterial',$hotMaterial);
		$this->view($tplfile);
	}

	public function tips_f()
	{
		$info = $this->get('info');
		$backurl = $this->get('back');
		if(!$info){
			$info = P_Lang('友情提示');
		}
		if(!$backurl){
			$backurl = $this->url;
		}
		$this->assign('url',$backurl);
		$this->assign('tips',$info);
		$this->view('tips');
	}

	//推荐链执行
	public function linker($code)
	{
		$rs = $this->model('user')->code_one($code);
		if(!$rs || !$rs['user_id']){
			$this->_location('index.php');
		}
		//增加点击率
		$this->model('user')->code_addhits($rs['id']);
		//已登录的会员，跳过
		if($_SESSION['user_id']){

			if($rs['link']){
				$this->_location($rs['link']);
			}
			$this->_location('index.php');
		}
		$user = $this->model('user')->get_one($rs['user_id']);
		if(!$user){
			$this->_location('index.php');
		}
		$_SESSION['introducer'] = $user['id'];
	}

	public function error404_f()
	{
		if(file_exists(ROOT.'phpinc/404.php')){
			require(ROOT.'phpinc/404.php');
		}
		header("HTTP/1.0 404 Not Found");
		header('Status: 404 Not Found');
		exit;
	}

	//首页显示热门器材
	public function hot_material(){
		$condition = "project_id between 385 and 392 AND parent_id=0 AND attr='h' ";
		$sql = "SELECT * FROM tb_list WHERE $condition ORDER BY sort ASC limit 2";//所有器材里面找到标注为热门的而且排在前面的两条数据
		$hotMaterial = $this->db->get_all($sql,"id");
		foreach ($hotMaterial as $key => $value){
			$project = $this->db->get_one("SELECT id,title FROM tb_project WHERE id={$value['project_id']}");//项目信息
			$hotMaterial[$key]['project'] = $project;

			$cate = $this->db->get_one("SELECT id,title,identifier FROM tb_cate WHERE id={$value['cate_id']}");//分类信息
			$hotMaterial[$key]['cate'] = $cate;

			$showField = $this->db->get_all("SELECT * FROM tb_module_fields WHERE module_id={$value['module_id']} AND index_show=1 ORDER BY index_show_sort ASC limit 4");//器材所对应的模块允许显示的前四个字段名称及标识

            $field = '';
			$property = '';
			if(!empty($showField)){
				foreach ($showField as $k => $v){
					if($v['identifier']){
						$field[] = $v['identifier'];
                        $v['title'] =  str_replace(['用户评估的','制造商标明的','(1-10)','(1-100)','制造商的','用户的'],'',$v['title']);
						$property[$v['identifier']]['title'] = $v['title'];
                        switch ($k){
                            case '0':
                                $progressStyle = 'success';
                                break;
                            case '1':
                                $progressStyle = 'warning';
                                break;
                            case '2':
                                $progressStyle = 'info';
                                break;
                            case '3':
                                $progressStyle = 'danger';
                                break;
                        }
                        $property[$v['identifier']]['progress_style'] = $progressStyle;//进度条样式
					}
				}
				$fieldStr = implode($field,',');
			} else {
				$fieldStr = '*';
			}

			$fieldValue = $this->db->get_one("SELECT thumb,label,content,{$fieldStr} FROM tb_list_{$value['module_id']} WHERE id={$value['id']}");//通过上面找到允许显示的标识，查出其相关的内容
			foreach ($fieldValue as $k => $val){
				if(in_array($k,$field)){
					$property[$k]['val'] = $val;
				}
			}

			$labels = unserialize($fieldValue['label']);
			foreach ($labels as $label){
				$rs = $this->db->get_one("SELECT title FROM tb_opt WHERE group_id=22 AND val={$label}");//查询出其对应的标签名,关联到的group_id为固定值=>22
				$labelName = $rs['title'];
				$hotMaterial[$key]['label'][$label] = $labelName;
			}

            $picInfo = $this->db->get_one("SELECT filename FROM tb_res WHERE id={$fieldValue['thumb']}");

            $hotMaterial[$key]['pic'] = $picInfo['filename'];
			$hotMaterial[$key]['property'] = $property;
			$hotMaterial[$key]['content'] = $fieldValue['content'];
		}
		return $hotMaterial;
	}

}
?>