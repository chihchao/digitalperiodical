<?php
class DGPDUser {
	var $uid;
	var $groups;
	var $uname;
	function DGPDUser() {
		global $xoopsUser;
		$this -> uid = is_object($xoopsUser) ? $xoopsUser -> getVar('uid') : 0;
		$this -> groups = is_object($xoopsUser) ? $xoopsUser -> getGroups() : array(XOOPS_GROUP_ANONYMOUS);
		$this -> uname = is_object($xoopsUser) ? $xoopsUser -> uname() : '';
	}
	function auth($type = 'admin', $uids = 0) {
		global $xoopsModuleConfig;
		if ($this -> isAdmin()) return true;
		//editors return true for all
		if (!empty($xoopsModuleConfig['editors'][0]) && in_array($this -> uid, $xoopsModuleConfig['editors'])) return true;
		if ($type == 'editors') if (!empty($xoopsModuleConfig['editors'][0]) && in_array($this -> uid, $xoopsModuleConfig['editors'])) return true;
		if ($type == 'reviewers') {
			//editors
			if (!empty($xoopsModuleConfig['editors'][0]) && in_array($this -> uid, $xoopsModuleConfig['editors'])) return true;
			//reviewers
			foreach ($this -> groups as $val) if (in_array($val, $xoopsModuleConfig['reviewers'])) return true;
		}
		if ($type == 'teachers') {
			//teachers
			if ($xoopsModuleConfig['teacher_review']) foreach ($this -> groups as $val) if (in_array($val, $xoopsModuleConfig['teachers'])) return true;
		}
		if ($type == 'review') {
			if ($this -> auth('reviewers')) return true;
			if ($this -> auth('teachers')) return true;
		}
		if ($type == 'contributors') foreach ($this -> groups as $val) if (in_array($val, $xoopsModuleConfig['contributors'])) return true;
		return false;
	}
	function isAdmin() {
		global $xoopsUser;
		if (is_object($xoopsUser) && $xoopsUser -> isAdmin()) {
			return true;
		} else {
			return false;
		}
	}
}
class DGPDUsers {
	function DGPDUsers() {}
	function getUsersByGroup($groupids) {
		$dus = new DGPDUserDBase;
		$rs = $dus -> getUsersByGroup($groupids);
		foreach ($rs as $key => $val) $rs[$key] = array('uid' => $val['uid'], 'uname' => $val['uname']);
		return $rs;
	}
}
class DGPDUserDBase extends DGPDDBase {
	function DGPDUserDBase() {
		$this -> init();
	}
	function getUsersByGroup($groupids) {
		if (empty($groupids)) return array();
		if (!is_array($groupids)) return false;
		$tb_users = $this -> dbase -> prefix('users');
		$tb_links = $this -> dbase -> prefix('groups_users_link');
		$this -> setTable('groups_users_link left join ' . $tb_users . ' On ' . $this -> dbase -> prefix('groups_users_link') . '.uid = ' . $tb_users . '.uid');
		foreach ($groupids as $key => $val) $groupids[$key] = intval($val);
		$where = implode (' OR ' . $tb_links . '.groupid = "', $groupids);
		$fields = $tb_users . '.uid, ' . $tb_users . '.uname';
		$where = '(' . $tb_links . '.groupid = "' . $where . '") and ' . $tb_users . '.level > 0 Group By ' . $tb_links . '.uid Order by ' . $tb_users . '.uname';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
}
?>