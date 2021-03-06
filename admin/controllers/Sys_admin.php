<?php
class Sys_admin extends MY_Controller {
	/* Index */
	public function index(){
		$this->lang->load('system/sys_admin',$this->Lang);
		$this->load->library('inc');
		$this->load->helper('my');
		$data = $this->inc->Page($this,array('url'=>'sys_admin/index.html','model'=>'sys_admin_m'));
		$data['LoadJS'] = array('system/sys_admin.js');
		$data['Menus'] = $this->inc->getMenuAdmin($this);
		if($this->IsMobile) {
			$this->inc->adminView($this,'system/admin/index_mo',$data);
		}else {
			$this->inc->adminView($this,'system/admin/index',$data);
		}
	}
	/* Search */
	public function search(){
		$this->lang->load('inc',$this->Lang);
		$this->lang->load('system/sys_admin',$this->Lang);
		$this->load->view('system/admin/sea');
	}
	/* Add */
	public function add(){
		$this->lang->load('inc',$this->Lang);
		$this->lang->load('system/sys_admin',$this->Lang);
		$this->load->view('system/admin/add');
	}
	public function addData(){
		$this->lang->load('msg',$this->Lang);
		$this->load->model('sys_admin_m');
		echo $this->sys_admin_m->add()?'{"status":"y"}':'{"status":"n","title":"'.$this->lang->line('msg_title').'","msg":"'.$this->lang->line('msg_err').'","text":"'.$this->lang->line('msg_auto_close').'"}';
	}
	/* Edit */
	public function edit(){
		$this->lang->load('inc',$this->Lang);
		$this->lang->load('system/sys_admin',$this->Lang);
		$this->load->model('sys_admin_m');
		$data['edit'] = $this->sys_admin_m->getOne();
		$this->load->view('system/admin/edit',$data);
	}
	public function editData(){
		$this->lang->load('msg',$this->Lang);
		$this->load->model('sys_admin_m');
		echo $this->sys_admin_m->update()?'{"status":"y"}':'{"status":"n","title":"'.$this->lang->line('msg_title').'","msg":"'.$this->lang->line('msg_err').'","text":"'.$this->lang->line('msg_auto_close').'"}';
	}
	/* Delete */
	public function delData(){
		$this->lang->load('msg',$this->Lang);
		$this->load->model('sys_admin_m');
		echo $this->sys_admin_m->del()?'{"status":"y","title":"'.$this->lang->line('msg_title').'","msg":"'.$this->lang->line('msg_suc').'","text":"'.$this->lang->line('msg_auto_close').'"}':'{"status":"n","title":"'.$this->lang->line('msg_title').'","msg":"'.$this->lang->line('msg_err').'","text":"'.$this->lang->line('msg_auto_close').'"}';
	}
	/* UserName */
	public function uname(){
		$this->load->model('sys_admin_m');
		echo $this->sys_admin_m->uname()?'该用户存在！':'y';
	}
	/* EditPerm */
	public function editPerm(){
		$this->lang->load('inc',$this->Lang);
		$this->lang->load('menu',$this->Lang);
		$this->load->model('sys_menus_m');
		$this->load->model('sys_menus_action_m');
		$actionM = $this->sys_menus_action_m->getAll();
		$permArr = $this->splitPerm($this->input->post('perm'));
		
		$html = '';
		$menu1 = $this->sys_menus_m->getMenus('0');
		foreach($menu1 as $m1){
			$ck = isset($permArr[$m1->id])?'checked':'';
			$title1 = $this->lang->line($m1->title)?$this->lang->line($m1->title):$m1->title;
			$html .= '<div id="oneMenuPerm" class="perm">';
			$html .= '    <span class="text1"><input type="checkbox" value="'.$m1->id.'" '.$ck.' /></span>';
			$html .= '    <span>[<a href="#">-</a>] '.$title1.'</span>';
			$html .= '</div>';
			$menu2 = $this->sys_menus_m->getMenus($m1->id);
			foreach($menu2 as $m2){
				$ck = isset($permArr[$m2->id])?'checked':'';
				$title2 = $this->lang->line($m2->title)?$this->lang->line($m2->title):$m2->title;
				$html .= '<div id="twoMenuPerm" class="perm">';
				$html .= '    <span class="text2"><input type="checkbox" value="'.$m2->id.'" '.$ck.' /></span>';
				$html .= '    <span>[<a href="#">-</a>] '.$title2.'</span>';
				$html .= '</div>';
				$menu3 = $this->sys_menus_m->getMenus($m2->id);
				foreach($menu3 as $m3){
					$ck = isset($permArr[$m3->id])?'checked':'';
					$title3 = $this->lang->line($m3->title)?$this->lang->line($m3->title):$m3->title;
					$html .= '<div id="threeMenuPerm" class="perm perm_action">';
					$html .= '      <span class="text3"><input type="checkbox" name="threeMenuPerm" value="'.$m3->id.'" '.$ck.' /></span>';
					$html .= '      <span>[<a href="#">-</a>] '.$title3.'</span>';
					$html .= '  <span id="actionPerm_'.$m3->id.'"> ( ';
					foreach($actionM as $val){
						if(intval($m3->perm) & intval($val->perm)){
							$ck = @$permArr[$m3->id]&intval($val->perm)?'checked':'';
							$name = $this->lang->line($val->name)?$this->lang->line($val->name):$val->name;
							$html .= '<span><input type="checkbox" value="'.$val->perm.'" '.$ck.' /></span><span class="text">'.$name.'</span>';
						}
					}
					$html .= ')</span>';
					$html .= '</div>';
				}
			}
		}
		$data['menusHtml'] = $html;
		$this->load->view('system/admin/perm',$data);
	}
	/* SplitPerm */
	public function splitPerm($perm){
		if($perm){
			$arr = explode(' ', $perm);
			foreach($arr as $val) {
				$num = explode(':', $val);
				$permArr[$num[0]]= $num[1];
			}
			return $permArr;
		}
	}
	/* Update */
	public function permData(){
		$this->lang->load('msg',$this->Lang);
		$this->load->model('sys_admin_m');
		echo $this->sys_admin_m->updatePerm()?'{"status":"y"}':'{"status":"n","title":"'.$this->lang->line('msg_title').'","msg":"'.$this->lang->line('msg_err').'","text":"'.$this->lang->line('msg_auto_close').'"}';
	}
}