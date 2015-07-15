<?php
	/** 
	* @file addon document_permisson_control_by_author.addon.php 
	* @author Gunmania (d.gunmania@gmail.com) 
	* @brief 게시물의 열람 레벨을 작성자가 지정하는 애드온
	**/ 
	if(!defined('__XE__'))	exit();

	$logged_info = Context::get('logged_info');

	if($called_position == 'before_module_proc') {
		if(Context::get('document_srl')) {
			//관리자는 예외
			if($logged_info->is_admin == 'Y')	return;

			$document_srl = Context::get('document_srl');
			$oDocumentModel = &getModel('document');
			$oDocument = $oDocumentModel->getDocument($document_srl, $this->grant->manager);

			//작성자 본인 예외
			if($oDocument->get('member_srl') == $logged_info->member_srl)	return;

			$read_level = $oDocument->getExtraEidValue('read_level');

			$member_srl = $logged_info->member_srl;

			$oPointModel = &getModel('point');
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('point');

			$member_point = $oPointModel->getPoint($member_srl);
			$member_level = $oPointModel->getLevel($member_point, $config->level_step);
			

			//레벨 기준을 만족할 때
			if ($member_level >= $read_level) {
				return;
			}
	
			//레벨 기준을 만족하지 못할 때
			else {
				$ref = $_SERVER['HTTP_REFERER'];
				header("Content-Type: text/html; charset=UTF-8");
				echo '<script>alert("작성자가 지정한 읽기 레벨보다 레벨이 낮아 글을 읽을 수 없습니다.\n\n읽기 가능 레벨 : '.$read_level.'");</script>';
				if($ref)	echo '<script>window.location.href = "'.$ref.'";</script>';
				else	echo '<script>window.location.href = "/";</script>';
				exit();
			}
		}
	}
?>
