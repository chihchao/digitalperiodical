<?php
//modules basic
$modversion['name'] = _MI_DGPD_NAME;
$modversion['version'] = 1.0;
$modversion['description'] = _MI_DGPD_DESC;
$modversion['credits'] = 'atlas(ch.ch.hsu@gmail.com)';
$modversion['author'] = 'atlas(ch.ch.hsu@gmail.com)';
$modversion['license'] = 'GPL see LICENSE';
$modversion['image'] = 'images/logo.png';
$modversion['dirname'] = 'digitalperiodical';

//database
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = array(
	'dgpd_articles',
	'dgpd_categories',
	'dgpd_files',
	'dgpd_issues',
);

//admin
$modversion['hasAdmin'] = 1;

//mainmenu
$modversion['hasMain'] = 1;

//templates
$modversion['templates'] = array(
	array('file' => 'dgpd_header.htm', 'description' => ''),
	array('file' => 'dgpd_footer.htm', 'description' => ''),
	array('file' => 'dgpd_index.htm', 'description' => ''),
	array('file' => 'dgpd_manage.htm', 'description' => ''),
	array('file' => 'dgpd_manage_issue.htm', 'description' => ''),
	array('file' => 'dgpd_manage_category.htm', 'description' => ''),
	array('file' => 'dgpd_manage_contribute.htm', 'description' => ''),
	array('file' => 'dgpd_manage_review.htm', 'description' => ''),
	array('file' => 'dgpd_issue_articles.htm', 'description' => ''),
	array('file' => 'dgpd_category.htm', 'description' => ''),
	array('file' => 'dgpd_article.htm', 'description' => ''),
	array('file' => 'dgpd.htm', 'description' => ''),
);

//config
$modversion['config'][] = array(
	'name' => 'title',
	'title' => '_MI_DGPD_CFG_TITLE',
	'description' => '_MI_DGPD_CFG_TITLE_DESC',
	'formtype' => 'text',
	'valuetype' => 'text',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'introduction',
	'title' => '_MI_DGPD_CFG_INTRODUCTION',
	'description' => '_MI_DGPD_CFG_INTRODUCTION_DESC',
	'formtype' => 'textarea',
	'valuetype' => 'text',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'footer',
	'title' => '_MI_DGPD_CFG_FOOTER',
	'description' => '_MI_DGPD_CFG_FOOTER_DESC',
	'formtype' => 'textarea',
	'valuetype' => 'text',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'editors',
	'title' => '_MI_DGPD_CFG_EDITORS',
	'description' => '_MI_DGPD_CFG_EDITORS_DESC',
	'formtype' => 'user_multi',
	'valuetype' => 'array',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'reviewers',
	'title' => '_MI_DGPD_CFG_REVIEWERS',
	'description' => '_MI_DGPD_CFG_REVIEWERS_DESC',
	'formtype' => 'group_multi',
	'valuetype' => 'array',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'teachers',
	'title' => '_MI_DGPD_CFG_TEACHERS',
	'description' => '_MI_DGPD_CFG_TEACHERS_DESC',
	'formtype' => 'group_multi',
	'valuetype' => 'array',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'contributors',
	'title' => '_MI_DGPD_CFG_CONTRIBUTORS',
	'description' => '_MI_DGPD_CFG_CONTRIBUTORS_DESC',
	'formtype' => 'group_multi',
	'valuetype' => 'array',
	'default' => ''
);
$modversion['config'][] = array(
	'name' => 'teacher_review',
	'title' => '_MI_DGPD_CFG_TEACHERREVIEW',
	'description' => '_MI_DGPD_CFG_TEACHERREVIEW_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1
);
$modversion['config'][] = array(
	'name' => 'root_canctb',
	'title' => '_MI_DGPD_CFG_ROOTCANCTB',
	'description' => '_MI_DGPD_CFG_ROOTCANCTB_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0
);
$modversion['config'][] = array(
	'name' => 'layout_with_web',
	'title' => '_MI_DGPD_CFG_LAYOUTWITHWEB',
	'description' => '_MI_DGPD_CFG_LAYOUTWITHWEB_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0
);
$modversion['config'][] = array(
	'name' => 'order_asc',
	'title' => '_MI_DGPD_CFG_ORDERASC',
	'description' => '_MI_DGPD_CFG_ORDERASC_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0
);
$modversion['config'][] = array(
	'name' => 'filetype_ok',
	'title' => '_MI_DGPD_CFG_FILETYPEOK',
	'description' => '_MI_DGPD_CFG_FILETYPEOK_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'jpg|jpeg|png|gif|doc|xls|ppt|odt|ods|odp|mp3|wma|mpg|wmv|avi|pdf|flv'
);
?>