<?php
//include
include_once('include.php');
include_once('class/issue.php');

//parameter
$option = (empty($_GET['option']) || (!in_array($_GET['option'], array('mng', 'rvw')))) ? 'ctb' : $_GET['option'];
$iid = empty($_GET['iid']) ? 0 : intval($_GET['iid']);

//main
switch($option) {
	case 'mng':
		if (!$dgpduser -> auth('editors')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
		$tplvar['title'] = _MD_DGPD_MANAGE_MNGISSUE;
		$tplvar['issues'] = $periodical -> getIssues(-1);
	break;
	case 'rvw':
		if (!$dgpduser -> auth('review')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
		$tplvar['title'] = _MD_DGPD_MANAGE_RVWISSUE;
		$tplvar['issues'] = $periodical -> getIssues(-1);
	break;
	case 'ctb':
		if (!$dgpduser -> auth('contributors')) func_redirect('index.php', 5, _MD_DGPD_NOAUTHORITY);
		$tplvar['title'] = _MD_DGPD_MANAGE_CTBISSUE;
		$tplvar['issues'] = ($dgpduser -> auth('editors')) ? $periodical -> getIssues(-1) : $periodical -> getIssues(0);
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
	$xoopsOption['template_main'] = 'dgpd_manage.htm';
	include(XOOPS_ROOT_PATH . '/header.php');
	$xoopsTpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$xoopsTpl -> assign('tplvar', $tplvar);
	include(XOOPS_ROOT_PATH . '/footer.php');
} else {
	$tpl = new XoopsTpl();
	$tpl -> assign('xoops_template_main', 'dgpd_manage.htm');
	$tpl -> assign('xoops_module_header', $mdheader -> getModuleheader());
	$tpl -> assign('tplvar', $tplvar);
	$tpl -> display('db:dgpd.htm');
}
?>