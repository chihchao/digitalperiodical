<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);
$submitted = empty($_POST['submitted']) ? false : true;

//main
$isu = new DGPDIssue;
if (!$isu -> setIssue($iid)) func_redirect('manage.php?option=ctb', 5, _MD_DGPD_NOISSUE);
if (!$dgpduser -> auth('contributors')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
if (!$dgpduser -> auth('editors') && $issue['ipublished']) func_redirect('manage.php?option=ctb', 5, _MD_DGPD_MANAGECONTRIBUTE_PUBLISHEDCTBCANT);
if ($submitted) {
	if ($isu -> addArticle()) {
		$message = func_get_glb_error_messages();
		$message = empty($message) ? _MD_DGPD_MANAGECONTRIBUTE_CTBSUCCESS1 : _MD_DGPD_MANAGECONTRIBUTE_CTBSUCCESS2 . '<br />' . $message;
		func_redirect('manage_contribute.php?iid=' . $iid, 10, _MD_DGPD_MANAGECONTRIBUTE_CTBSUCCESS0 . $message . '<br />' . _MD_DGPD_MANAGECONTRIBUTE_CTBSUCCESS3);
	} else {
		func_redirect(xoops_getenv('REQUEST_URI'), 10, func_get_glb_error_messages() . _MD_DGPD_MANAGECONTRIBUTE_CTBFAIL);
	}
}

$tplvar['title'] = _MD_DGPD_MANAGECONTRIBUTE_CTB . $isu -> ititle;
$tplvar['categories'] = $isu -> getCategories(new DGPDCategory(0, $iid), array());
array_unshift($tplvar['categories'], new DGPDCategory(0, $iid));
$tplvar['teachers'] = DGPDUsers::getUsersByGroup($xoopsModuleConfig['teachers']);
$tplvar['tchreview'] = $xoopsModuleConfig['teacher_review'];
$tplvar['filetyp_string'] = (empty($xoopsModuleConfig['filetype_ok'])) ? _MD_DGPD_MANAGECONTRIBUTE_FILE_ALL : _MD_DGPD_MANAGECONTRIBUTE_FILE_TYPE . str_replace('|', ', ', $xoopsModuleConfig['filetype_ok']);

$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
if (!$dgpduser -> auth('editors')) $mdheader -> addElement(new DGPDMDHeadElement('cantcontribute'));
$mdheader -> addElement(new DGPDMDHeadElement('checkempty', array('#DGPDFormTitle', _MD_DGPD_MANAGECONTRIBUTE_ERRMS_NOTITLE)));
$mdheader -> addElement(new DGPDMDHeadElement('filefield'));

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
	$xoopsOption['template_main'] = 'dgpd_manage_contribute.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_manage_contribute.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>