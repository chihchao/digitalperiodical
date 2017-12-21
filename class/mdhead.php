<?php
class DGPDMDHead {
	var $elements;
	var $elements_ready_action;
	function DGPDMDHead() {
		$this -> elements = array();
		$this -> elements_ready_action = array();
	}
	function addElement($element) {
		if ($element -> getHeadstring() != '') array_push($this -> elements, $element -> getHeadstring());
		if ($element -> getReadyaction() != '') array_push($this -> elements_ready_action, $element -> getReadyaction());
	}
	function getModuleheader() {
		$mh = (empty($this -> elements_ready_action)) ? '' : '
		<script type="text/javascript">
		$(document).ready(function(e){
		' . implode(chr(13) . chr(10), $this -> elements_ready_action) . '
		});
		</script>
		';
		return implode(chr(13) . chr(10), $this -> elements) . chr(13) . chr(10) . $mh;
	}
}
class DGPDMDHeadElement {
	var $head_string;
	var $ready_action;
	function DGPDMDHeadElement($type, $value = '') {
		$this -> head_string = '';
		$this -> ready_action = '';
		$this -> setElement($type, $value);
	}
	function setElement($type, $value = '') {
		if ($type == 'css') {
			$this -> head_string = '<link rel="stylesheet" type="text/css" media="screen" href="theme/style.css" />';
		} elseif ($type == 'jquery') {
			$this -> head_string = '<script type="text/javascript" src="js/jquery.min.js"></script>';
		} elseif ($type == 'datepicker') {
			$this -> head_string = '
			<link rel="stylesheet" href="js/css/redmond/jquery.ui.datepicker.css" type="text/css" media="all" />
			<script type="text/javascript" src="js/jquery.ui.datepicker.min.js"></script>
			<script type="text/javascript" src="js/jquery.ui.datepicker-zh-TW.js"></script>
			<script type="text/javascript" src="js/datepicker.js"></script>
			';
			$this -> ready_action = 'ra_datepicker();';
		} elseif ($type == 'accordion') {
			$this -> head_string = '
			<link rel="stylesheet" href="js/css/redmond/jquery.ui.accordion.css" type="text/css" media="all" />
			<script type="text/javascript" src="js/jquery.ui.accordion.min.js"></script>
			<script type="text/javascript" src="js/accordion.js"></script>
			';
			$this -> ready_action = 'ra_accordion();';
		} elseif ($type == 'jqueryslidemenu') {
			$this -> head_string = '
			<link rel="stylesheet" href="js/css/jqueryslidemenu.css" type="text/css" media="all" />
			<script type="text/javascript" src="js/jqueryslidemenu.js"></script>
			<!--[if lte IE 7]>
			<style type="text/css">
			html .jquerycssmenu{height: 1%;} /*Holly Hack for IE7 and below*/
			</style>
			<![endif]-->
			';
			$this -> ready_action = 'ra_jqueryslidemenu();';
		} elseif ($type == 'accordiontree') {
			$this -> head_string = '
			<link rel="stylesheet" href="js/css/accordiontree.css" type="text/css" media="all" />
			<script type="text/javascript" src="js/accordiontree.js"></script>
			';
			$this -> ready_action = 'ra_accordiontree();';
		} elseif ($type == 'tinymce') {
			$this -> head_string = '
			<script language="javascript" type="text/javascript" src="tinymce/tiny_mce.js"></script>
			<script type="text/javascript" src="js/tinymce.js"></script>
			';
			$this -> ready_action = (empty($value)) ? 'ra_tinymce(true);' : 'ra_tinymce(false);';
		} elseif ($type == 'cantcontribute') {
			$this -> head_string = '
			<script type="text/javascript" src="js/cantcontribute.js"></script>
			';
			$this -> ready_action = '
			setCantcontributeVar("' . _MD_DGPD_MANAGECONTRIBUTE_ERRMS_CTGCANT . '");
			$("#DGPDForm").submit( checkCategory );
			';
		} elseif ($type == 'checkempty') {
			$this -> head_string = '
			<script type="text/javascript" src="js/checkempty.js"></script>
			';
			$this -> ready_action = '
			setCheckemptyVar("' . $value[0] . '", "' . $value[1] . '");
			$( "#DGPDForm" ).submit( checkEmpty );
			';
		} elseif ($type == 'filefield') {
			$this -> head_string = '
			<script type="text/javascript" src="js/filefield.js"></script>
			';
			$this -> ready_action = '
			setFilefieldVar("' . _MD_DGPD_MANAGECONTRIBUTE_FILESELECT . '", "' . _MD_DGPD_MANAGECONTRIBUTE_FILEDESCRIPTION . '");
			$("#DGPDFormFilefield > *").remove();
			$("#DGPDFormFilefieldAdd").click(addFileField);
			';
		}
	}
	function getReadyaction() {
		return $this -> ready_action;
	}
	function getHeadstring() {
		return $this -> head_string;
	}
}
?>