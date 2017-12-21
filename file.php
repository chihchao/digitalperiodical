<?php
//include
include_once('include.php');
include_once('class/file.php');
//parameter
$fid = empty($_GET['fid']) ? 0 : intval($_GET['fid']);

//main
$fl = new DGPDFile;
if (!$fl -> setFile($fid)) func_redirect('index.php', 5, _MD_DGPD_NOFILE);
//authority
	if (!$fl -> addCounter()) exit();
	$path = $fl -> getFilepath();

	/*
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	header('Content-Description: File Transfer');
	header('Content-type: ' . $file['file_type']);
	header('Content-Disposition: inline; filename=' . $file['file_name']);
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . $file['file_size']);
	*/
	//header("Content-Type: application/force-download");

	header('Expires: 0');
	header('Content-Type: ' . $fl -> file_type);
	if (preg_match("/MSIE ([0-9]\.[0-9]{1,2})/", $HTTP_USER_AGENT)) {
		header('Content-Disposition: inline; filename="' . iconv('utf-8', 'big5', $fl -> file_name) . '"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else {
		header('Content-Disposition: inline; filename="' . iconv('utf-8', 'big5', $fl -> file_name) . '"');
		header('Pragma: no-cache');
	}
	header("Content-Transfer-Encoding: binary");

	readfile($path);
	

exit();
?>