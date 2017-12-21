<?php
class DGPDDBase {
	var $dbase;
	var $table;
	var $insert_last_id;
	function DGPDDBase() {}
	function init() {
		global $xoopsDB;
		if (!is_object($xoopsDB)) die('No xoopsDB!');
		$this -> dbase = $xoopsDB;
	}
	function setTable($table) {
		$this -> table = $this -> dbase -> prefix($table);
	}
	function query($query, $force = false) {
		if ($force) {
			$result = $this -> dbase -> queryF($query) or die ('Invalid query in line ' . __LINE__ . '<br />Error: ' . mysql_error() . '<br />Query: ' . $query . '<br />File: ' . $_SERVER['PHP_SELF']);
		} else {
			$result = $this -> dbase -> query($query) or die ('Invalid query in line ' . __LINE__ . '<br />Error: ' . mysql_error() . '<br />Query: ' . $query . '<br />File: ' . $_SERVER['PHP_SELF']);
		}
		return $result;
	}
	function setLastID($id) {
		$this -> insert_last_id = $id;
	}
	function getLastID() {
		return $this -> insert_last_id;
	}
	//insert into
	function insert($fields, $values) {
		$query = 'INSERT INTO ' . $this -> table . ' (' . $fields . ') VALUES(' . $values . ')';
		if($this -> query($query)) {
			$this -> setLastID($this -> dbase -> getInsertId());
			return true;
		} else {
			return false;
		}
	}
	//normal select
	function select($fields, $where) {
		$recordset = array();
		$query = 'SELECT ' . $fields . ' FROM ' . $this -> table . ' WHERE ' . $where;
		$result = $this -> query($query);
		while ($record = mysql_fetch_array($result)) array_push($recordset, $record);
		return $recordset;
	}
	//delete from
	function delete($where) {
		$query = 'DELETE FROM ' . $this -> table . ' WHERE ' . $where;
		return $this -> query($query);
	}
	//update
	function update($values, $where) {
		$query = 'UPDATE ' . $this -> table . ' SET ' . $values . ' WHERE ' . $where;
		return $this -> query($query, true);
	}
}
?>