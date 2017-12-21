<?php
//deal slashes problem, set magic_quotes_gpc off
function func_setoff_magic_quotes_gpc() {
	if (get_magic_quotes_gpc()) {
		function traverse(&$arr) {
			if (!is_array($arr)) return;
			foreach ($arr as $key => $val) is_array ($arr[$key]) ? traverse($arr[$key]) : ($arr[$key] = stripslashes ($arr[$key]));
		}
		$gpc = array( &$_GET, &$_POST, &$_COOKIE );
		traverse($gpc);
	}
}

//escape string for array data
function func_escape_string($string) {
	if (function_exists('mysql_real_escape_string')) {
		$string = mysql_real_escape_string($string);
	} elseif (function_exists('mysql_escape_string')) {
		$string = mysql_escape_string($string);
	} else {
		$string = addslashes($string);
	}
	return $string;
}
function func_escape_string_arr_trv(&$arr) {
	if (!is_array($arr)) return;
	foreach ($arr as $key => $val) is_array($arr[$key]) ? func_escape_string_arr_trv($arr[$key]) : ($arr[$key] = func_escape_string($arr[$key]));
}
function func_escape_string_arr(&$arr) { func_escape_string_arr_trv($arr); }

//ºIÂ_¦r¦êÁ×§K±N¤¤¤å¦rºIÂ_
function func_md_substr($content, $bg, $ed) {
	return mb_substr($content, $bg, $ed, _CHARSET);
}

//for redirect header, because xoops 2.5 does not show the redirect message
function func_redirect($url, $time, $message) {
	global $xoops_module_header;
	redirect_header($url, $time, $message);
	/*
	echo('
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '" dir="ltr">
		<head>
		<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '}>" />
		<meta http-equiv="Content-Language" content="' . _LANGCODE . '" />
		<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '" />
		<title>Alert Message</title>
		' . $xoops_module_header . '
		</head>
		<body id="DGPDBody">
		<div id="DGPDAlertmessage">
		<p>' . $message . '</p>
		<a href="' . $url . '" title="' . _MD_DGPD_REDIRECT . '">' . _MD_DGPD_REDIRECT . '</a>
		</div>
		</body>
		</html>
	');
	exit();
	*/
}
function func_confirm() {
}

//set global var glb_error_messages
function func_set_glb_error_messages($var = '') {
	global $glb_error_messages;
	if (empty($var)) {
		$glb_error_messages = '';
	} else {
		if (empty($glb_error_messages)) {
			$glb_error_messages = $var;
		} else {
			$glb_error_messages .= chr(13) . chr(10) . $var;
		}
	}
}
function func_get_glb_error_messages() {
	global $glb_error_messages;
	return nl2br($glb_error_messages);
}

function func_view_file_embed_html($file) {
	$file_name = strtolower(substr(strrchr($file['file_name'], '.'), 1));
	$url = DGPDFile::getFilePath('url', $file['real_name']);
	if ($file_name == 'jpg' || $file_name == 'jpeg' || $file_name == 'gif' || $file_name == 'png') {
		return '<img src="file.php?fid=' . $file['fid'] . '" title="' . $file['description'] . '" width="100%" />';
	} elseif ($file_name == 'mpg' || $file_name == 'avi' || $file_name == 'wmv') {
		return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="480" height="360">
			<param name="url" value="' . $url . '">
			<param name="uiMode" value="mini">
			<param name="autoStart" value="false">
			<embed src="' . $url . '" type="video/x-ms-wmv" width="480" height="360" autoStart="0" showControls="1"></embed>
			</object>';
		
	} elseif ($file_name == 'mp4' || $file_name == 'flv') {
		return '<embed flashvars="file=' . $url . '&autostart=false" src="jwplayer/player.swf" width="480" height="360" allowfullscreen="true" allowscripaccess="always" />';
	} elseif ($file_name == 'mp3' || $file_name == 'wav' || $file_name == 'wma' || $file_name == 'mid') {
		return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" type="application/x-oleobject" height="45" width="150">
			<param name="url" value="' . $url . '">
			<param name="autoStart" value="false">
			<param name="loop" value="false">
			<param name="uiMode" value="mini">
			<param name="volume" value="50">
			<embed src="' . $url . '" type="video/x-ms-wmv" height="45" width="150" autoStart="0" loop="0" volume="50" ShowPositionControls="0" showControls="1">
			</embed>
			</object>';
	} else {
		return false;
	}
}
?>