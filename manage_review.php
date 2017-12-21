<?php
//include
include_once('include.php');
include_once('class/issue.php');
include_once('class/category.php');
include_once('class/article.php');
include_once('class/file.php');

//parameter
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);
$aid = empty($_GET['aid']) ? 0 : intval($_GET['aid']);
$submitted = empty($_POST['submitted']) ? false : true;

//main
$isu = new DGPDIssue;
if (!$isu -> setIssue($iid)) func_redirect('manage.php?option=ctb', 5, _MD_DGPD_NOISSUE);
if (!$dgpduser -> auth('review')) func_redirect('manage.php?option=rvw', 5, _MD_DGPD_NOAUTHORITY);
if ($aid) {
	$atc = new DGPDArticle;
	if (!$atc -> setArticle($aid)) func_redirect('manage_review.php?iid=' . $iid, 5, _MD_DGPD_NOARTICLE);
	if (!$dgpduser -> auth('reviewers') && $atc -> atch_uid != $dgpduser -> uid) func_redirect('manage.php?option=rvw', 5, _MD_DGPD_NOAUTHORITY);

	if ($submitted) ($atc -> update()) ? func_redirect('manage_review.php?iid=' . $iid, 5, _MD_DGPD_MANAGEREVIEW_RVWSUCCESS . '<br />' . func_get_glb_error_messages()) : func_redirect(xoops_getenv('REQUEST_URI'), 5, func_get_glb_error_messages() . _MD_DGPD_MANAGEREVIEW_RVWFAIL);

	$tplvar['title'] = _MD_DGPD_MANAGEREVIEW_RVW . $atc -> atitle;
	$tplvar['categories'] = $isu -> getCategories(new DGPDCategory(0, $iid), array());
	array_unshift($tplvar['categories'], new DGPDCategory(0, $iid));
	$tplvar['option'] = 'rvw';
	$tplvar['issue'] = $isu;
	$tplvar['article'] = $atc;
	$tplvar['teachers'] = DGPDUsers::getUsersByGroup($xoopsModuleConfig['teachers']);
	$tplvar['filetyp_string'] = (empty($xoopsModuleConfig['filetype_ok'])) ? _MD_DGPD_MANAGECONTRIBUTE_FILE_ALL : _MD_DGPD_MANAGECONTRIBUTE_FILE_TYPE . str_replace('|', ', ', $xoopsModuleConfig['filetype_ok']);

	$mdheader -> addElement(new DGPDMDHeadElement('jquery'));
	if (!$dgpduser -> auth('editors')) $mdheader -> addElement(new DGPDMDHeadElement('cantcontribute'));
	$mdheader -> addElement(new DGPDMDHeadElement('checkempty', array('#DGPDFormTitle', _MD_DGPD_MANAGECONTRIBUTE_ERRMS_NOTITLE)));
	$mdheader -> addElement(new DGPDMDHeadElement('tinymce'));
	$mdheader -> addElement(new DGPDMDHeadElement('filefield'));
} else {
	if ($submitted) {
		if (!$dgpduser -> auth('editors')) func_redirect('manage_review.php?iid=' . $iid, 5, _MD_DGPD_NOAUTHORITY);
		foreach($_POST['article'] as $key => $val) {
			$val = intval($val);
			$atc = new DGPDArticle($val);
			if (in_array($val, $_POST['article_delete'])) {
				$atc -> delete();
			} else {
				$atc -> updateOrder($_POST['article_order'][$key]);
			}
		}
	}
	$tplvar['title'] = _MD_DGPD_MANAGEREVIEW_RVW . $isu -> ititle;
	$tplvar['option'] = 'lst';
	$tplvar['articles'] = $isu -> getArticles();
	$tplvar['is_editors'] = $dgpduser -> auth('editors');
}


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
	$xoopsOption['template_main'] = 'dgpd_manage_review.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_manage_review.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>