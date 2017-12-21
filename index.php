<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//main
$tplvar['issue'] = $periodical -> getIssueLast();
if (!empty($tplvar['issue'])) {
	$ctgr = new DGPDCategory(0, $tplvar['issue'] -> iid);
	$tplvar['category'] = $ctgr;
	$tplvar['categories'] = $ctgr -> getSubcategories();
}
$tplvar['issues_published'] = $periodical -> getIssues(1);
$tplvar['issues_unpublish'] = $periodical -> getIssues(0);

$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
$mdheader -> addElement(new DGPDMDHeadElement('accordion'));

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
	$xoopsOption['template_main'] = 'dgpd_index.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_index.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>