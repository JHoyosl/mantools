<?php

namespace Gsys;

class centros{

	//Variable globalr para usar la bd
	private $db;
	
	//Se declara la variable de respeusta
	private $send = array("");
	function __construct(){

		$this->db = new db();


	}

	function getCentrosList($info){

		$str = "SELECT
				centers.ID,
				UPPER(centers.`NAME`) AS DESCRIPCION
				FROM
				centers
				ORDER BY NAME ASC";

		$query = $this->db->query($str);	

		$send["status"] = true;
		$send["info"] = $query;

		return $send;

	}

}