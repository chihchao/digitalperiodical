<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$option = (empty($_GET['option']) || (!in_array($_GET['option'], array('add', 'edt', 'del')))) ? 'mng' : $_GET['option'];
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);
$submitted = empty($_POST['submitted']) ? false : true;

//main
if (!$dgpduser -> auth('editors')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
$isu = new DGPDIssue;
switch($option) {
	case 'add':
		if ($submitted) ($isu -> addIssue()) ? header('location:manage.php?option=mng') : func_redirect(xoops_getenv('REQUEST_URI'), 5, func_get_glb_error_messages() . _MD_DGPD_MANAGEISSUE_ADDFAIL);
		$tplvar['title'] = _MD_DGPD_MANAGEISSUE_ADDISSUE;
		$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
		$mdheader -> addElement(new DGPDMDHeadElement('datepicker'));
		$mdheader -> addElement(new DGPDMDHeadElement('tinymce'));
	break;
	case 'edt':
		if ($submitted) ($isu -> updateIssue($iid)) ? header('location:manage_issue.php?option=mng&iid=' . $iid) : func_redirect(xoops_getenv('REQUEST_URI'), 5, func_get_glb_error_messages() . _MD_DGPD_MANAGEISSUE_EDTFAIL);
		if (!$isu -> setIssue($iid)) func_redirect('manage.php?option=mng', 5, _MD_DGPD_NOISSUE);
		$tplvar['title'] = _MD_DGPD_EDIT . $isu -> ititle;
		$tplvar['issue'] = $isu;
		$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
		$mdheader -> addElement(new DGPDMDHeadElement('datepicker'));
		$mdheader -> addElement(new DGPDMDHeadElement('tinymce'));
	break;
	case 'del':
		if ($submitted) ($isu -> delIssue($iid)) ? header('location:manage.php?option=mng') : func_redirect('manage.php?option=mng', 5, _MD_DGPD_MANAGEISSUE_DELFAIL);
		include(XOOPS_ROOT_PATH . "/header.php");
		xoops_confirm(array('submitted' => 'true'), $_SERVER['REQUEST_URI'], _MD_DGPD_MANAGEISSUE_DELCONFIRM);
		include(XOOPS_ROOT_PATH . "/footer.php");
		exit();
	break;
	case 'mng':
		if (!$isu -> setIssue($iid)) func_redirect('manage.php?option=mng', 5, _MD_DGPD_NOISSUE);
		$tplvar['issue'] = $isu;
		$tplvar['title'] = $isu -> ititle;
		$tplvar['categories'] = $isu -> getCategories(new DGPDCategory(0, $iid), array());
	break;
	default:
}



$tplvar['option'] = $option;
$tplvar['auth'] = array(
	'editor' => $dgpduser -> auth('editors'),
	'reviewer' => $dgpduser -> auth('review'),
	'contributor' => $dgpduser -> auth('contributors'),
);
$tplvar['user'] = array(
	'uid' => $dgpduser -> uid,
	'uname' => $dgpduser -> uname,
);
$tplvar['dgpd'] = array(
	'title' => $periodical -> title,
	'introduction' => $periodical -> introduction,
	'footer' => $periodical -> footer,
);

//template
if ($xoopsModuleConfig['layout_with_web']) {
	$xoopsOption['template_main'] = 'dgpd_manage_issue.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_manage_issue.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>