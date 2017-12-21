<?php
class DGPDArticle {
	var $aid, $iid, $cid, $atitle, $asubtitle, $actb_uid, $acontributor, $atch_uid, $ateacher, $aauthor, $acontent, $aorder, $acounter, $adate_time, $ruid, $rcomment, $rdate_time, $rpass, $file, $path_tag;
	function DGPDArticle($article = '') {
		if (isset($article)) $this -> setArticle($article);
	}
	function setArticle($article) {
		if (is_array($article)) {
			$var_array = array('aid','iid','cid','atitle','asubtitle','actb_uid','acontributor','atch_uid','ateacher','aauthor','acontent','aorder','acounter','adate_time','ruid','rcomment','rdate_time','rpass','file','path_tag');
			foreach ($article as $key => $val) if (in_array($key, $var_array)) $this -> $key = $val;
			$this -> acontributor = ($article['actb_uid'] == 0) ? ($article['acontributor']) : (XoopsUser::getUnameFromId($article['actb_uid'], 0));
			$this -> ateacher = ($article['atch_uid'] == 0) ? ($article['ateacher']) : (XoopsUser::getUnameFromId($article['atch_uid'], 0));
			$this -> reviewer = ($article['ruid'] == 0) ? '' : (XoopsUser::getUnameFromId($article['ruid'], 0));
			if (!isset($this -> file)) $this -> file = $this -> getFiles();
			$this -> thumbnail = $this -> getThumbnail();
			$this -> acontent_substr = strip_tags($article['acontent']);
			$this -> acontent_substr = empty($this -> acontent_substr) ? '' : (func_md_substr($this -> acontent_substr, 0, 30) . '...');
			return true;
		} elseif (is_int($article)) {
			$adb = new DGPDArticleDBase;
			$rs = $adb -> getArticle($article);
			if (empty($rs)) return false;
			$this -> setArticle($rs);
			return true;
		}
		return false;
	}
	function setPathtag($path_tag) {
		if (is_array($path_tag)) {
			$this -> path_tag = $path_tag;
		}
	}
	function addCounter() {
		$adb = new DGPDArticleDBase;
		return $adb -> addCounter($this -> aid);
	}
	function updateOrder($odr) {
		$adb = new DGPDArticleDBase;
		return $adb -> updateArticleOrder($this -> aid, $odr);
	}
	function delete() {
		foreach($this -> file as $val) $val -> delete();
		$adb = new DGPDArticleDBase;
		return $adb -> deleteArticle($this -> aid);
	}
	function update() {
		global $dgpduser;
		if (ltrim($_POST['atitle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGECONTRIBUTE_ERRMS_NOTITLE);
			return false;
		}
		$ctgr = new DGPDCategory(intval($_POST['cid']));
		if (!$dgpduser -> auth('editors') && !$ctgr -> ccanctb) {
			func_set_glb_error_messages(_MD_DGPD_MANAGECONTRIBUTE_ERRMS_CTGCANT);
			return false;
		}
		$_POST['atch_uid'] = empty($_POST['atch_uid']) ? 0 : intval($_POST['atch_uid']);
		$adb = new DGPDArticleDBase;
		if (!$adb -> updateArticle($this -> aid, $dgpduser -> uid)) return false;
		//delete or update the files of article
		foreach($this -> file as $key => $val) {
			if (!empty($_POST['fl_delete']) && in_array($val -> fid, $_POST['fl_delete'])) {
				$val -> delete();
			}else{
				$val -> updateDescription($_POST['fl_description'][$key]);
			}
		}
		if (empty($_FILES) || !is_array($_FILES)) return true;
		return $this -> addFiles();
	}
	function getFiles() {
		$fdb = new DGPDFileDBase;
		$rs = $fdb -> getFiles($this -> iid, $this -> aid);
		foreach ($rs as $key => $val) $rs[$key] = new DGPDFile($val);
		return $rs;
	}
	function getThumbnail() {
		if (is_array($this -> file)) {
			foreach ($this -> file as $val) {
				$fph = new DGPDFilePath($val -> real_name);
				if (file_exists($fph -> getThumbnailpath())) return $fph -> getThumbnailpath(true);
			}
		}
		return '';
	}
	function addFiles() {
		global $xoopsModuleConfig;
		$filetype_image = array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png');
		$thumbnail_size = 100;
		$upload_path = DGPDFilePath::getUploadpath();
		if (!is_dir($upload_path) || !is_writeable($upload_path)) {
			if (!is_dir($upload_path) && !mkdir($upload_path)) {
				func_set_glb_error_messages(_MD_DGPD_NOUPLOADPATH);
				return false;
			}
		}
		foreach($_FILES['file']['tmp_name'] as $key => $val) {
			if (is_uploaded_file($_FILES['file']['tmp_name'][$key])) {
			//file type
			$filetype_ok = empty($xoopsModuleConfig['filetype_ok']) ? array() : explode('|', strtolower($xoopsModuleConfig['filetype_ok']));
			$filetype = strtolower(substr(strrchr($_FILES['file']['name'][$key], '.'), 1));
			if (empty($filetype_ok) || in_array($filetype, $filetype_ok)) {
			//file values
			$file = array();
			$file['file_name'] = func_escape_string($_FILES['file']['name'][$key]);
			$file['file_type'] = func_escape_string($_FILES['file']['type'][$key]);
			$file['file_size'] = func_escape_string($_FILES['file']['size'][$key]);
			//the func_escape_string has executed in DGPDArticleDBase's addArticle
			//$_POST['description'][$key] = (ltrim($_POST['description'][$key]) == '') ? '' : func_escape_string($_POST['description'][$key]);
			$file['description'] = htmlspecialchars($_POST['description'][$key]);
			//the real name of file saved in server
			$fph = new DGPDFilePath;
			$fph -> setRealname($_FILES['file']['name'][$key], true);
			$file['real_name'] = $fph -> real_name;
			//creat thumbnail and put file to upload path
			if (in_array($file['file_type'], $filetype_image)) func_resizeImage($_FILES['file']['tmp_name'][$key], $fph -> getThumbnailpath(), $thumbnail_size, $thumbnail_size);
			if (!move_uploaded_file($_FILES['file']['tmp_name'][$key], $fph -> getFilepath())) {
				func_set_glb_error_messages('[' . $_FILES['file']['name'][$key] . '] Can not move tmp_file to upload path.' . chr(13) . chr(10));
				continue;
			}
			$fdb = new DGPDFileDBase;
			if ($fdb -> addFile($this -> iid, $this -> aid, $file)) {
				func_set_glb_error_messages('[' . $_FILES['file']['name'][$key] . '] Upload success.' . chr(13) . chr(10));
			} else {
				func_set_glb_error_messages('[' . $_FILES['file']['name'][$key] . '] MySQL insert fail.' . chr(13) . chr(10));
			}
			//if filetype_ok else
			} else {
				func_set_glb_error_messages('[' . $_FILES['file']['name'][$key] . '] File type error.' . chr(13) . chr(10));
			}
			//if is_uploaded_file else
			} else {
				func_set_glb_error_messages('[file ' . $key . '] No upload file.' . chr(13) . chr(10));
			}
		}
		return true;
	}
}
class DGPDArticleDBase extends DGPDDBase {
	function DGPDArticleDBase() {
		$this -> init();
		$this -> setTable('dgpd_articles');
	}
	function addArticle($iid, $article) {
		func_escape_string_arr($article);
		$fields = 'iid, cid, atitle, asubtitle, actb_uid, acontributor, atch_uid, ateacher, aauthor, acontent, aorder, acounter, adate_time';
		$values = '\'' . intval($iid) . '\',';
		$values .= '\'' . intval($article['cid']) . '\',';
		$values .= '\'' . htmlspecialchars($article['atitle']) . '\',';
		$values .= '\'' . htmlspecialchars($article['asubtitle']) . '\',';
		$values .= '\'' . intval($article['actb_uid']) . '\',';
		$values .= '\'' . htmlspecialchars($article['acontributor']) . '\',';
		$values .= '\'' . intval($article['atch_uid']) . '\',';
		$values .= '\'\',';
		$values .= '\'' . htmlspecialchars($article['aauthor']) . '\',';
		$values .= '\'' . $article['acontent'] . '\',';
		$values .= '\'0\',';
		$values .= '\'0\',';
		$values .= '\'' . time() . '\'';
		return $this -> insert($fields, $values);
	}
	function getArticle($aid) {
		$fields = '*';
		$where = 'aid = \'' . intval($aid) . '\'';
		$rs = $this -> select($fields, $where);
		return $rs[0];
	}
	function getArticlesByiid($iid) {
		$fields = '*';
		$where = 'iid = \'' . intval($iid) . '\' Order By aorder ASC, aid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function getArticlesByiidtch($iid, $uid) {
		$fields = '*';
		$where = 'iid = \'' . intval($iid) . '\' And atch_uid = \'' . intval($uid) . '\' Order By aorder ASC, aid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function getArticlesBycid($iid, $cid) {
		$fields = '*';
		$where = (is_array($cid)) ? 'cid = \'' . implode('\' OR cid = \'', $cid) . '\'' : 'cid = \'' . intval($cid) . '\'';
		$where = 'iid = \'' . intval($iid) . '\' And (' . $where . ') Order By aorder ASC, aid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function updateArticle($aid, $uid) {
		func_escape_string_arr($_POST);
		$values = 'cid = \'' . intval($_POST['cid']) . '\',';
		$values .= 'atitle = \'' . htmlspecialchars($_POST['atitle']) . '\',';
		$values .= 'asubtitle = \'' . htmlspecialchars($_POST['asubtitle']) . '\',';
		$values .= 'atch_uid = \'' . intval($_POST['atch_uid']) . '\',';
		$values .= 'aauthor = \'' . htmlspecialchars($_POST['aauthor']) . '\',';
		$values .= 'acontent = \'' . $_POST['acontent'] . '\',';
		$values .= 'ruid = \'' . intval($uid) . '\',';
		$values .= 'rcomment = \'' . $_POST['rcomment'] . '\',';
		$values .= 'rdate_time = \'' . time() . '\',';
		$values .= 'rpass = \'' . intval($_POST['rpass']) . '\'';
		$where = 'aid = \'' . intval($aid) . '\'';
		return $this -> update($values, $where);
	}
	function updateArticleOrder($aid, $odr) {
		$values = 'aorder = \'' . intval($odr) . '\'';
		$where = 'aid = \'' . intval($aid) . '\'';
		return $this -> update($values, $where);
	}
	function addCounter($aid) {
		$values = 'acounter = acounter + 1';
		$where = 'aid = \'' . intval($aid) . '\'';
		return $this -> update($values, $where);
	}
	function deleteArticle($aid) {
		$where = 'aid = \'' . intval($aid) . '\'';
		return $this -> delete($where);
	}
	function countCategoryArticles($iid, $cid) {
		$fields = 'count(*)';
		$where = (is_array($cid)) ? 'cid = \'' . implode('\' OR cid = \'', $cid) . '\'' : 'cid = \'' . intval($cid) . '\'';
		$where = 'iid = \'' . intval($iid) . '\' And (' . $where . ') And rpass = 2';
		$rs = $this -> select($fields, $where);
		return $rs[0]['count(*)'];
	}
	function getCategoryArticle($iid, $cid) {
		$fields = '*';
		$where = (is_array($cid)) ? 'cid = \'' . implode('\' OR cid = \'', $cid) . '\'' : 'cid = \'' . intval($cid) . '\'';
		$where = 'iid = \'' . intval($iid) . '\' And (' . $where . ') And rpass = 2 Order By acounter DESC, aid ASC Limit 0, 1';
		$rs = $this -> select($fields, $where);
		return $rs[0];
	}
	function getCategoryArticles($iid, $cid) {
		$fields = '*';
		$where = (is_array($cid)) ? 'cid = \'' . implode('\' OR cid = \'', $cid) . '\'' : 'cid = \'' . intval($cid) . '\'';
		$where = 'iid = \'' . intval($iid) . '\' And (' . $where . ') And rpass = 2 Order By acounter DESC, aid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
}
?>