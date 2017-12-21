<?php
class DGPDPeriodical {
	var $title;
	var $introduction;
	var $footer;
	function DGPDPeriodical() { $this -> init(); }
	function init() {
		global $xoopsModuleConfig;
		$this -> title = $xoopsModuleConfig['title'];
		$this -> introduction = nl2br($xoopsModuleConfig['introduction']);
		$this -> footer = nl2br($xoopsModuleConfig['footer']);
	}
	function getIssues($ipublished = -1) {
		$isu = new DGPDIssueDBase;
		$rs = $isu -> getIssues($ipublished);
		foreach ($rs as $key => $val) $rs[$key] = new DGPDIssue($val);
		return $rs;
	}
	function getIssueLast() {
		$isu = new DGPDIssueDBase;
		$rs = $isu -> getIssueLast();
		$rs = (empty($rs)) ? '' : new DGPDIssue($rs);
		return $rs;
	}
}
?>