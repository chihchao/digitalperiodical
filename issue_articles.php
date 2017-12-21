<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);

//main
$isu = new DGPDIssue;
if (!$isu -> setIssue($iid)) func_redirect('index.php', 5, _MD_DGPD_NOISSUE);
$tplvar['title'] = $isu -> ititle;
$tplvar['issue'] = $isu;
$ctgr = new DGPDCategory(0, $isu -> iid);
$ctgr -> countCategoryArticles = $ctgr -> countCategoryArticles();
$ctgr -> getArticles = $ctgr -> getArticles();
$tplvar['category'] = $ctgr;
$tplvar['categories'] = $ctgr -> getSubcategories();
foreach ($tplvar['categories'] as $key => $val) {
	$tplvar['categories'][$key] -> tree_subcategories = $isu -> getCategories($val, array($val));
	foreach ($tplvar['categories'][$key] -> tree_subcategories as $k => $v) $tplvar['categories'][$key] -> tree_subcategories[$k] -> getArticles = $tplvar['categories'][$key] -> tree_subcategories[$k] -> getArticles();
}
$tplvar['tree_categories'] = $isu -> getCategories($ctgr, array($ctgr));

$tplvar['issues_published'] = $periodical -> getIssues(1);

$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
$mdheader -> addElement(new DGPDMDHeadElement('accordion'));
$mdheader -> addElement(new DGPDMDHeadElement('accordiontree'));

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
	$xoopsOption['template_main'] = 'dgpd_issue_articles.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_issue_articles.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>