<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');

//parameter
$option = (empty($_GET['option']) || (!in_array($_GET['option'], array('edt', 'del')))) ? 'add' : $_GET['option'];
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);
$cid = empty($_GET['cid']) ? 0 : intval($_GET['cid']);
$submitted = empty($_POST['submitted']) ? false : true;

//main
if (!$dgpduser -> auth('editors')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
if (!$isu = new DGPDIssue($iid)) func_redirect('manage.php?option=mng', 5, _MD_DGPD_NOISSUE);

switch ($option) {
	case 'add':
		if ($submitted) ($isu -> addCategory()) ? header('location:manage_issue.php?option=mng&iid=' . $iid) : func_redirect(xoops_getenv('REQUEST_URI'), 5, func_get_glb_error_messages() . _MD_DGPD_MANAGECATEGORY_ADDFAIL);
		$tplvar['title'] = _MD_DGPD_MANAGECATEGORY_ADDCATEGORY;
		$tplvar['categories'] = $isu -> getCategories(new DGPDCategory(0, $iid), array());
		array_unshift($tplvar['categories'], new DGPDCategory(0, $iid));
		$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
		$mdheader -> addElement(new DGPDMDHeadElement('tinymce'));
	break;
	case 'edt':
		$ctg = new DGPDCategory;
		if ($cid ==0 || !$ctg -> setCategory($cid)) func_redirect('manage_issue.php?option=mng&iid=' . $iid, 5, _MD_DGPD_NOCATEGORY);
		if ($submitted) ($ctg -> updateCategory()) ? header('location:manage_issue.php?option=mng&iid=' . $iid) : func_redirect(xoops_getenv('REQUEST_URI'), 5, func_get_glb_error_messages() . _MD_DGPD_MANAGECATEGORY_EDTFAIL);
		$tplvar['title'] = _MD_DGPD_EDIT . $ctg -> ctitle;
		$tplvar['categories'] = $isu -> getCategories(new DGPDCategory(0, $iid), array());
		array_unshift($tplvar['categories'], new DGPDCategory(0, $iid));
		$tplvar['category'] = $ctg;
		$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
		$mdheader -> addElement(new DGPDMDHeadElement('tinymce'));
	break;
	case 'del':
		$ctg = new DGPDCategory;
		if ($cid ==0 || !$ctg -> setCategory($cid)) func_redirect('manage_issue.php?option=mng&iid=' . $iid, 5, _MD_DGPD_NOCATEGORY);
		if ($submitted) ($ctg -> delCategory()) ? header('location:manage_issue.php?option=mng&iid=' . $iid) : func_redirect('manage_issue.php?option=mng&iid=' . $iid, 5, func_get_glb_error_messages() . _MD_DGPD_MANAGECATEGORY_DELFAIL);
		include(XOOPS_ROOT_PATH . "/header.php");
		xoops_confirm(array('submitted' => 'true'), $_SERVER['REQUEST_URI'], _MD_DGPD_MANAGECATEGORY_DELCONFIRM);
		include(XOOPS_ROOT_PATH . "/footer.php");
		exit();
	break;
}

$tplvar['issue'] = $isu;
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
	$xoopsOption['template_main'] = 'dgpd_manage_category.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_manage_category.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>