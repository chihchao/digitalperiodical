<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$aid = empty($_GET['aid']) ? 0 : intval($_GET['aid']);

//main
$atc = new DGPDArticle;
if (!$atc -> setArticle($aid)) func_redirect('index.php', 5, _MD_DGPD_NOARTICLE);
$isu = new DGPDIssue;
if (!$isu -> setIssue(intval($atc -> iid))) func_redirect('index.php', 5, _MD_DGPD_NOISSUE);
$ctg = new DGPDCategory;
if (!$ctg -> setCategory(intval($atc -> cid), intval($atc -> iid))) func_redirect('issue_articles.php?iid=' . $iid, 5, _MD_DGPD_NOCATEGORY);
$atc -> addCounter();
$tplvar['title'] = $atc -> atitle;
$tplvar['issue'] = $isu;
$tplvar['category'] = $ctg;
$tplvar['article'] = $atc;
$tplvar['articles'] = $ctg -> getArticles();

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
	$xoopsOption['template_main'] = 'dgpd_article.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_article.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>