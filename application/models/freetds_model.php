<?php
define('ETOWNUSERNAME', 'etownreader');
define('ETOWNPWD', 'fishing22');

class Freetds_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function checkServer($ip)
	{
		try
		{
			$connect = new PDO('odbc:Driver=FreeTDS; Server='.$ip.'; Port=1433; Database=et_main; UID='.ETOWNUSERNAME.'; PWD='.ETOWNPWD.';');
			return true;
		}
		catch(PDOException $e)
		{
			return false;
		}
	}

	public function executeScript($sql, $ip){
		$connect = new PDO('odbc:Driver=FreeTDS; Server='.$ip.'; Port=1433; Database=et_main; UID='.ETOWNUSERNAME.'; PWD='.ETOWNPWD.';');
		$result = $connect->query($sql);

		if(!$result)
			return NULL;
		return $result->fetchAll();
	}
}