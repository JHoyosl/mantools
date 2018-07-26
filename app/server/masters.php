<?php

namespace Gsys;

class masters{

	private $db;
	function __construct(){

		$this->db = new db();


	}

	function getDeptos($info){

		$str = "SELECT 
				master_deptos.DEPTO, 
					master_deptos.`NAME`
				FROM master_deptos";

		$resp = $this->db->query($str);

		$send["status"] = true;
		$send["info"] = $resp;

		return $send;
		

	}

	function getCities($info){

		$str = "SELECT master_cities.`NAME`, 
					master_cities.CITY
				FROM master_cities
				WHERE master_cities.DEPTO = '".$info["deptoCode"]."'
				ORDER BY master_cities.`NAME` ASC";
		

		$resp = $this->db->query($str);

		$send["status"] = true;
		$send["info"] = $resp;

		return $send;
		

	}
}