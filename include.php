<?php
//include
include_once('../../mainfile.php');
include_once(XOOPS_ROOT_PATH . '/class/template.php');
include_once('function.php');
include_once('class/mdhead.php');
include_once('class/dbase.php');
include_once('class/user.php');
include_once('class/periodical.php');

//init
func_setoff_magic_quotes_gpc();
//init global var glb_error_messages
func_set_glb_error_messages();
//init global var xoops_module_header
$mdheader = new DGPDMDHead;
$mdheader -> addElement(new DGPDMDHeadElement('css'));
$dgpduser = new DGPDUser;
$periodical = new DGPDPeriodical;
?>