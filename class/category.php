<?php
class DGPDCategory {
	var $cid;
	var $iid;
	var $ucid;
	var $ctitle;
	var $cintroduction;
	var $corder;
	var $ccanctb;
	var $level;
	var $path_tag;
	function DGPDCategory($category = '', $iid = 0) {
		if (isset($category)) $this -> setCategory($category, $iid);
	}
	function setCategory($category = '', $iid = 0) {
		global $xoopsModuleConfig;
		if (is_array($category)) {
			$var_array = array('cid','iid','ucid','ctitle','cintroduction','corder','ccanctb','level','path_tag');
			foreach ($category as $key => $val) if (in_array($key, $var_array)) $this -> $key = $val;
		} elseif ($category == 0) {
			if ($iid) {
				$isu = new DGPDIssue($iid);
				$ctitle = (isset($isu -> ititle)) ? $isu -> ititle : _MD_DGPD_MANAGECATEGORY_ROOT;
			} else {
				$ctitle = _MD_DGPD_MANAGECATEGORY_ROOT;
			}
			$this -> cid = 0;
			$this -> iid = $iid;
			$this -> ucid = 0;
			$this -> ctitle = $ctitle;
			$this -> cintroduction = '';
			$this -> corder = 0;
			$this -> ccanctb = $xoopsModuleConfig['root_canctb'];
			$this -> level = 0;
		} elseif (is_int($category)) {
			$cgdb = new DGPDCategoryDBase;
			$cg = $cgdb -> getCategory($category);
			if (empty($cg)) {
				return false;
			} else {
				$this -> setCategory($cg);
			}
		}
		return true;
	}
	function setPathtag($path_tag = array()) {
		if (is_array($path_tag)) {
			$this -> path_tag = $path_tag;
		} else {
			$this -> path_tag = array();
			$cdb = new DGPDCategoryDBase;
			$ucid = $this -> ucid;
			while ($ucid > 0) {
				$rs = $cdb -> getCategory($ucid);
				array_unshift($this -> path_tag, $rs['ctitle']);
				$ucid = $rs['cid'];
			}
		}
	}
	function updateCategory() {
		if (ltrim($_POST['ctitle']) == '') {
			func_set_glb_error_messages(_MD_DGPD_MANAGECATEGORY_ERRMS_NOTITLE);
			return false;
		}
		if (in_array(intval($_POST['ucid']), $this -> getAllSubcategories($this, array($this -> cid)))) {
			func_set_glb_error_messages(_MD_DGPD_MANAGECATEGORY_ERRMS_SUBCATEGORY);
			return false;
		}
		$cdb = new DGPDCategoryDBase;
		return $cdb -> updateCategory($this -> cid);
	}
	function delCategory() {
		//find category and subcategories
		$as = $this -> getAllSubcategories($this, array($this -> cid));
		//delete articles belong to the category and subcategories
		$adb = new DGPDArticleDBase;
		$ra = $adb -> getArticlesBycid($this -> iid, $as);
		foreach ($ra as $key => $val) {
			$ra[$key] = new DGPDArticle($val);
			$ra[$key] -> delete();
		}
		$cdb = new DGPDCategoryDBase;
		return $cdb -> delCategory($as);
	}
	function getParents() {
		$parents = array($this);
		$ucid = $this -> ucid;
		while ($ucid > 0) {
			$uc = new DGPDCategory(intval($ucid));
			array_unshift($parents, $uc);
			$ucid = $uc -> ucid;
		}
		if ($this -> cid != 0) {
			$uc = new DGPDCategory(0, $this -> iid);
			array_unshift($parents, $uc);
		}
		return $parents;
	}
	function getSubcategories() {
		$db = new DGPDCategoryDBase;
		$rs = $db -> getSubcategories($this -> cid, $this -> iid);
		$lv = $this -> level + 1;
		$pt = is_array($this -> path_tag) ? $this -> path_tag : array();
		array_push($pt, $this -> ctitle);
		foreach ($rs as $key => $val) {
			$val['level'] = $lv;
			$rs[$key] = new DGPDCategory($val);
			$rs[$key] -> setPathtag($pt);
		}
		return $rs;
	}
	function getAllSubcategories($category, $all_subcategories) {
		$rs = $category -> getSubcategories();
		foreach ($rs as $val) {
			array_push($all_subcategories, $val -> cid);
			$all_subcategories = $this -> getAllSubcategories($val, $all_subcategories);
		}
		return $all_subcategories;
	}
	function countCategoryArticles() {
		$adb = new DGPDArticleDBase;
		return $adb -> countCategoryArticles($this -> iid, $this -> cid);
	}
	function getCategoryArticle() {
		$adb = new DGPDArticleDBase;
		$rs = $adb -> getCategoryArticle($this -> iid, $this -> cid);
		$rs = empty($rs) ? '' : new DGPDArticle($rs);
		return $rs;
	}
	function getArticles() {
		$adb = new DGPDArticleDBase;
		$rs = $adb -> getCategoryArticles($this -> iid, $this -> cid);
		$pt = is_array($this -> path_tag) ? $this -> path_tag : array();
		array_push($pt, $this -> ctitle);
		foreach ($rs as $key => $val) {
			$rs[$key] = new DGPDArticle($val);
			$rs[$key] -> setPathtag($pt);
		}
		return $rs;
	}
}
class DGPDCategoryDBase extends DGPDDBase {
	function DGPDCategoryDBase() {
		$this -> init();
		$this -> setTable('dgpd_categories');
	}
	function getSubcategories($cid, $iid) {
		$fields = '*';
		$where = 'ucid = \'' . intval($cid) . '\' and iid = \'' . intval($iid) . '\' Order By ucid ASC, corder ASC, cid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function getCategory($cid) {
		$fields = '*';
		$where = 'cid = \'' . intval($cid) . '\'';
		$rs = $this -> select($fields, $where);
		$rs = (empty($rs)) ? array() : $rs[0];
		return $rs;
	}
	function addCategory($iid) {
		func_escape_string_arr($_POST);
		$_POST['corder'] = empty($_POST['corder']) ? 0 : intval($_POST['corder']);
		$fields = 'iid, ucid, ctitle, cintroduction, corder, ccanctb';
		$values = '\'' . intval($iid) . '\', \'' . intval($_POST['ucid']) . '\', \'' . htmlspecialchars($_POST['ctitle']) . '\', \'' . $_POST['cintroduction'] . '\', \'' . $_POST['corder'] . '\', \'' . intval($_POST['ccanctb']) . '\'';
		return $this -> insert($fields, $values);
	}
	function updateCategory($cid) {
		func_escape_string_arr($_POST);
		$_POST['corder'] = empty($_POST['corder']) ? 0 : intval($_POST['corder']);
		$values = 'ucid = \'' . intval($_POST['ucid']) . '\',';
		$values .= 'ctitle = \'' . htmlspecialchars($_POST['ctitle']) . '\',';
		$values .= 'cintroduction = \'' . $_POST['cintroduction'] . '\',';
		$values .= 'corder = \'' . $_POST['corder'] . '\',';
		$values .= 'ccanctb = \'' . intval($_POST['ccanctb']) . '\'';
		$where = 'cid = \'' . intval($cid) . '\'';
		return $this -> update($values, $where);
	}
	function delCategory($cid) {
		$where = (is_array($cid)) ? 'cid = \'' . implode('\' OR cid = \'', $cid) . '\'' : 'cid = \'' . intval($cid) . '\'';
		return $this -> delete($where);
	}
}
?>