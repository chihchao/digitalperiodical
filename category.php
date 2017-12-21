<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);
$cid = empty($_GET['cid']) ? 0 : intval($_GET['cid']);

//main
$isu = new DGPDIssue;
if (!$isu -> setIssue($iid)) func_redirect('index.php', 5, _MD_DGPD_NOISSUE);
$ctg = new DGPDCategory;
if (!$ctg -> setCategory($cid, $iid)) func_redirect('issue_articles.php?iid=' . $iid, 5, _MD_DGPD_NOCATEGORY);

$tplvar['title'] = $ctg -> ctitle;
$tplvar['issue'] = $isu;
$tplvar['category'] = $ctg;
$tplvar['articles'] = $ctg -> getArticles();
$ctgr = new DGPDCategory(0, $isu -> iid);
$tplvar['tree_categories'] = $isu -> getCategories($ctgr, array($ctgr));
$tplvar['parents'] = $ctg -> getParents();
$tplvar['children'] = $ctg -> getSubcategories();

$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
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
	$xoopsOption['template_main'] = 'dgpd_category.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_category.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>