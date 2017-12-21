<?php
class DGPDFile {
	var $fid, $iid, $aid, $file_name, $file_type, $file_size, $real_name, $description, $counter, $date_time, $filepath, $iconpath;
	function DGPDFile($file = '') {
		if (isset($file)) $this -> setFile($file);
	}
	function setFile($file) {
		if (is_array($file)) {
			$var_array = array('fid','iid','aid','file_name','file_type','file_size','real_name','description','counter','date_time','filepath','iconpath');
			foreach ($file as $key => $val) if (in_array($key, $var_array)) $this -> $key = $val;
			if (!isset($this -> filepath)) $this -> filepath = $this -> getFilepath(true);
			if (!isset($this -> iconpath)) $this -> iconpath = $this -> getIconpath();
			return true;
		} elseif (is_int($file)) {
			$fdb = new DGPDFileDBase;
			$rs = $fdb -> getFile($file);
			if (empty($rs)) return false;
			$this -> setFile($rs);
			return true;
		}
		return false;
	}
	function addCounter() {
		$fdb = new DGPDFileDBase;
		return $fdb -> addCounter($this -> fid);
	}
	function delete() {
		if (file_exists($this -> getFilepath()) && unlink($this -> getFilepath())) {
			if (file_exists($this -> getThumbnailpath())) unlink($this -> getThumbnailpath());
			$fdb = new DGPDFileDBase;
			if ($fdb -> deleteFile($this -> fid)) {
				return true;
			} else {
				func_set_glb_error_messages('MySQL delete fail.' . chr(13) . chr(10));
				return false;
			}
		} else {
			func_set_glb_error_messages('Delete file fail.' . chr(13) . chr(10));
			return false;
		}
	}
	function updateDescription($description) {
		$fdb = new DGPDFileDBase;
		return $fdb -> updateFileDescription($this -> fid, htmlspecialchars($description));
	}
	function getFilepath($url = false) {
		$fph = new DGPDFilePath($this -> real_name);
		return $fph -> getFilepath($url);
	}
	function getThumbnailpath($url = false) {
		$fph = new DGPDFilePath($this -> real_name);
		return $fph -> getThumbnailpath($url);
	}
	function getIconpath() {
		$fph = new DGPDFilePath($this -> real_name);
		return $fph -> getIconpath();
	}
	function getSubfilename() {
		return strtolower(substr(strrchr($this -> real_name, '.'), 1));
	}
}
class DGPDFilePath {
	var $real_name;
	function DGPDFilePath($real_name = '') {
		if (isset($real_name)) $this -> setRealname($real_name);
	}
	function setRealname($real_name, $generate = false) {
		if ($generate) {
			$real_name = strtolower(substr(strrchr($real_name, '.'), 1));
			$real_name = function_exists('microtime') ? str_replace('0.', '', str_replace(' ', '_', microtime())) . '.' . $real_name : time() . '.' . $real_name;
		}
		$this -> real_name = $real_name;
	}
	function getUploadpath($url = false) {
		if ($url) {
			return XOOPS_URL . '/uploads/digitalperiodical';
		} else {
			return XOOPS_ROOT_PATH . '/uploads/digitalperiodical';
		}
	}
	function getThumbnailname() {
		if (empty($this -> real_name)) return false;
		return 'tn_' . substr($this -> real_name, 0, strrpos($this -> real_name, '.')) . '.jpg';
	}
	function getThumbnailpath($url = false) {
		return $this -> getUploadpath($url) . '/' . $this -> getThumbnailname();
	}
	function getFilepath($url = false) {
		return $this -> getUploadpath($url) . '/' . $this -> real_name;
	}
	function getIconpath() {
		if (file_exists($this -> getThumbnailpath())) return $this -> getThumbnailpath(true);
		$ft = strtolower(substr(strrchr($this -> real_name, '.'), 1));
		if (file_exists('images/icon_' . $ft . '.png')) return 'images/icon_' . $ft . '.png';
		return 'images/icon_default.png';
	}
}
class DGPDFileDBase extends DGPDDBase {
	function DGPDFileDBase() {
		$this -> init();
		$this -> setTable('dgpd_files');
	}
	function addFile($iid, $aid, $file) {
		$fields = 'iid, aid, file_name, file_type, file_size, real_name, description, counter, date_time';
		$values = '\'' . intval($iid) . '\',';
		$values .= '\'' . intval($aid) . '\',';
		$values .= '\'' . func_escape_string($file['file_name']) . '\',';
		$values .= '\'' . func_escape_string($file['file_type']) . '\',';
		$values .= '\'' . intval($file['file_size']) . '\',';
		$values .= '\'' . func_escape_string($file['real_name']) . '\',';
		$values .= '\'' . $file['description'] . '\',';
		$values .= '\'' . 0 . '\',';
		$values .= '\'' . time() . '\'';
		return $this -> insert($fields, $values);
	}
	function getFiles($iid, $aid) {
		$fields = '*';
		$where = 'iid = \'' . intval($iid) . '\' and aid = \'' . intval($aid) . '\' Order By fid ASC';
		$rs = $this -> select($fields, $where);
		return $rs;
	}
	function getFile($fid) {
		$fields = '*';
		$where = 'fid = \'' . intval($fid) . '\'';
		$rs = $this -> select($fields, $where);
		return $rs[0];
	}
	function updateFileDescription($fid, $description) {
		$values = 'description = \'' . $description . '\'';
		$where = 'fid = \'' . intval($fid) . '\'';
		return $this -> update($values, $where);
	}
	function addCounter($fid) {
		$values = 'counter = counter + 1';
		$where = 'fid = \'' . intval($fid) . '\'';
		return $this -> update($values, $where);
	}
	function deleteFile($fid) {
		$where = 'fid = \'' . intval($fid) . '\'';
		return $this -> delete($where);
	}
}


//縮圖函式，傳入原圖路徑、縮圖路徑、最大寬度、最大高度
function func_resizeImage($source, $thumbnail, $max_width, $max_height){
	if (file_exists($source) && !empty($thumbnail)){
		$source_size = getimagesize($source); //圖檔大小
		if ($source_size[0] < $max_width && $source_size[1] < $max_height) {
			//圖檔寬、高都小於縮圖大小
			$thumbnail_size[0] = $source_size[0];
			$thumbnail_size[1] = $source_size[1];
		} else {
			$source_ratio = $source_size[0] / $source_size[1]; // 計算寬/高
			$thumbnail_ratio = $max_width / $max_height;
			if ($thumbnail_ratio > $source_ratio) {
				$thumbnail_size[1] = $max_height;
				$thumbnail_size[0] = $max_height * $source_ratio;
			}else{
				$thumbnail_size[0] = $max_width;
				$thumbnail_size[1] = $max_width / $source_ratio;
			}
		}
		if (function_exists('imagecreatetruecolor')) {
			$thumbnail_img = imagecreatetruecolor($thumbnail_size[0], $thumbnail_size[1]);
		} else {
			$thumbnail_img = imagecreate($thumbnail_size[0], $thumbnail_size[1]);
		}
		switch ($source_size[2]) {
			case 1:
				$source_img = imagecreatefromgif($source);
				break;
			case 2:
				$source_img = imagecreatefromjpeg($source);
				break;
			case 3:
				$source_img = imagecreatefrompng($source);
				break; 
			default:
				return false;
				break;
		}
		imagecopyresized($thumbnail_img, $source_img, 0, 0, 0, 0, $thumbnail_size[0], $thumbnail_size[1], $source_size[0], $source_size[1]);
		imagejpeg($thumbnail_img, $thumbnail, 100);
		imagedestroy($source_img);
		imagedestroy($thumbnail_img);
		return true;
	}else{
		return false;
	}
}
?>