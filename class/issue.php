<?php
class DGPDIssue {
	var $iid;
	var $ititle;
	var $idate;
	var $iintroduction;
	var $ipublished;
	function DGPDIssue($issue = '') {
		if (!empty($issue)) $this -> setIssue($issue);
	}
	function setIssue($issue) {
		if (is_array($issue)) {
			$var_array = array('iid','ititle','idate','iintroduction','ipublished');
			foreach ($issue as $key => $val) if (in_array($key, $var_array)) $this -> $key = $val;
			return true;
		} elseif (is_int($issue)) {
			$isudb = new DGPDIssueDBase;
			$isu = $isudb -> getIssue($issue);
			if (empty($isu)) {
				return false;
			} else {
				$this -> setIssue($isu);
				return true;
			}
		}
		return false;
	}
	function addIssue() {
		if (ltrim($_POST['ititle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGEISSUE_ERRMS_NOTITLE);
			return false;
		}
		$cd = explode('-', $_POST['idate']);
		if (!checkdate(intval($cd[1]), intval($cd[2]), intval($cd[0]))) {
			func_set_glb_error_messages(_MD_DGPD_MANAGEISSUE_ERRMS_DATEERROR);
			return false;
		}
		$_POST['idate'] = intval($cd[0]) . '-' . intval($cd[1]) . '-' .intval($cd[2]);
		$isudb = new DGPDIssueDBase;
		return $isudb -> addIssue();
	}
	function updateIssue($iid) {
		if (ltrim($_POST['ititle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGEISSUE_ERRMS_NOTITLE);
			return false;
		}
		$cd = explode('-', $_POST['idate']);
		if (!checkdate(intval($cd[1]), intval($cd[2]), intval($cd[0]))) {
			func_set_glb_error_messages('set', _MD_DGPD_MANAGEISSUE_ERRMS_DATEERROR);
			return false;
		}
		$_POST['idate'] = intval($cd[0]) . '-' . intval($cd[1]) . '-' .intval($cd[2]);
		$isudb = new DGPDIssueDBase;
		return $isudb -> updateIssue($iid);
	}
	function delIssue($iid) {
		//delete categories belong to the issue
		$ctg = new DGPDCategory(0, $iid);
		if (!$ctg -> delCategory()) return false;
		//delete issue
		$isudb = new DGPDIssueDBase;
		return $isudb -> delIssue($iid);
	}
	function getCategories($category, $isu_categories) {
		if (!is_array($isu_categories)) return false;
		$rs = $category -> getSubcategories();
		foreach ($rs as $val) {
			array_push($isu_categories, $val);
			$isu_categories = $this -> getCategories($val, $isu_categories);
		}
		return $isu_categories;
	}
	function addCategory() {
		if (ltrim($_POST['ctitle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGECATEGORY_ERRMS_NOTITLE);
			return false;
		}
		$cdb = new DGPDCategoryDBase;
		return $cdb -> addCategory($this -> iid);
	}
	function addArticle() {
		global $dgpduser;
		if (ltrim($_POST['atitle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGECONTRIBUTE_ERRMS_NOTITLE);
			return false;
		}
		$ctgr = new DGPDCategory(intval($_POST['cid']), $this -> iid);
		if (!$dgpduser -> auth('editors') && !$ctgr -> ccanctb) {
			func_set_glb_error_messages(_MD_DGPD_MANAGECONTRIBUTE_ERRMS_CTGCANT);
			return false;
		}
		$_POST['actb_uid'] = $dgpduser -> uid;
		$_POST['acontributor'] = ($dgpduser -> uid == 0) ? ($_POST['acontributor']) : ($dgpduser -> uname);
		$_POST['atch_uid'] = empty($_POST['atch_uid']) ? 0 : intval($_POST['atch_uid']);
		$_POST['acontent'] = str_replace(chr(13) . chr(10), '</p>' . chr(13) . chr(10) . '<p>', $_POST['acontent']);
		$_POST['acontent'] = '<p>' . $_POST['acontent'] . '</p>';
		$adb = new DGPDArticleDBase;
		if (!$adb -> addArticle($this -> iid, $_POST)) return false;
		if (empty($_FILES) || !is_array($_FILES)) return true;
		$atc = new DGPDArticle($adb -> insert_last_id);
		return $atc -> addFiles();
	}
	function getArticles() {
		global $dgpduser;
		$adb = new DGPDArticleDBase;
		$rs = ($dgpduser -> auth('reviewers')) ? ($adb -> getArticlesByiid($this -> iid)) : ($adb -> getArticlesByiidtch($this -> iid, $dgpduser -> uid));
		foreach ($rs as $key => $val) $rs[$key] = new DGPDArticle($val);
		return $rs;
	}
}
class DGPDIssueDBase extends DGPDDBase {
	function DGPDIssueDBase() {
		$this -> init();
		$this -> setTable('dgpd_issues');
	}
	function getIssues($ipublished = -1) {
		global $xoopsModuleConfig;
		$fields = '*';
		$odr = ($xoopsModuleConfig['order_asc']) ? 'ASC' : 'DESC';
		$where = ($ipublished == 1) ? 'ipublished = 1' : (($ipublished == 0) ? 'ipublished = 0' : '1');
		$where = $where . ' Order By idate ' . $odr;
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function getIssue($iid) {
		$fields = '*';
		$where = 'iid = ' . intval($iid);
		$rs = $this -> select($fields, $where);
		return $rs[0];
	}
	function getIssueLast() {
		$fields = '*';
		$where = 'ipublished = 1 Order By idate DESC Limit 0, 1';
		$rs = $this -> select($fields, $where);
		return $rs[0];
	}
	function addIssue() {
		func_escape_string_arr($_POST);
		$fields = 'ititle, idate, iintroduction, ipublished';
		$values = '\'' . htmlspecialchars($_POST['ititle']) . '\', \'' . $_POST['idate'] . '\', \'' . $_POST['iintroduction'] . '\', \'' . intval($_POST['ipublished']) . '\'';
		return $this -> insert($fields, $values);
	}
	function updateIssue($iid) {
		func_escape_string_arr($_POST);
		$values = 'ititle = \'' . htmlspecialchars($_POST['ititle']) . '\',';
		$values .= 'idate = \'' . $_POST['idate'] . '\',';
		$values .= 'iintroduction = \'' . $_POST['iintroduction'] . '\',';
		$values .= 'ipublished = \'' . intval($_POST['ipublished']) . '\'';
		$where = 'iid = \'' . intval($iid) . '\'';
		return $this -> update($values, $where);
	}
	function delIssue($iid) {
		$where = 'iid = \'' . intval($iid) . '\'';
		return $this -> delete($where);
	}
}
?>