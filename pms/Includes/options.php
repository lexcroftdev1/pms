<?php
	class Options
	{
		private $conn;
		public function __construct()
		{
			global $conn;
			global $database_conn;
			$this->conn = $conn;
			mysql_select_db($database_conn, $conn);
		}
		public function __get($name)
		{
			$sql = sprintf("SELECT value from options where name=%s limit 1",common::GetSQLValueString($name, "text"));
			$rs = mysql_query($sql, $this->conn) or die(mysql_error());
			$row = mysql_fetch_assoc($rs);
			return $row["value"];
    	}
		public function __set($name,$value)
		{
			$sql = sprintf("update options set value=%s  where name=%s",common::GetSQLValueString($value, "text"),common::GetSQLValueString($name, "text"));
			$rs = mysql_query($sql, $this->conn) or die(mysql_error());
			$row = mysql_fetch_assoc($rs);
			return $row["value"];
    	}
	}
?>