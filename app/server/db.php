<?php

namespace Gsys;

class db
{
	private $pg;
	function __construct()
	{
		try
		{
			$cfg = parse_ini_file("cfg/cfg.ini",true);
			$dbInfo = $cfg["mysqlDb"];
			
			$host = $dbInfo["host"];
			$db = $dbInfo["db"];
			$user = $dbInfo["user"];
			$pssw = $dbInfo["pssw"];

			$this->pg = new \PDO('mysql:host='.$host.';dbname='.$db.'', $user, $pssw, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			
		}
		catch(\PDOException $e)
		{
			echo  "Error!: " . $e->getMessage() . "<br/>";	
		}
	}
	
	
	function beginTransaction()
	{
		$this->pg->beginTransaction();	
	}
	
	function commit()
	{
		$this->pg->commit();	
	}
	
	function rollBack()
	{
		$this->pg->rollBack();	
	}

	function query($string)
	{

		$resp = $this->pg->query($string);
		$error = $this->pg->errorInfo();
		if(empty($error[1]))
		{
			$resp->setFetchMode(\PDO::FETCH_ASSOC);
			$consulta = array();
			
			while ($row = $resp->fetch())
			{
				$consulta[] = $row;	
			}
			return $consulta;
		}
		else
		{

			throw new \Exception(implode($error," "), 1);
	
		}
	}
}

?>