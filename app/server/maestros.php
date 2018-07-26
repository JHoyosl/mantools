<?php

namespace Gsys;

class maestros{

	private $db;
	function __construct(){

		$this->db = new db();


	}
	
	function getTipoEquipo($info){
		
		$str = "";
		
	}
	
	function maestroGenero($info){
		
		$str = "SELECT
				maestro_genero.ID,
				maestro_genero.DESCRIPCION
				FROM
				maestro_genero";
		
		$resp = $this->db->query($str);

		$send["status"] = true;
		$send["info"] = $resp;

		return $send;
	}
	
	function maestroTipoDocumento($info){
		
		$str = "SELECT
				maestro_tipo_doc.ID,
				maestro_tipo_doc.DESCRIPCION
				FROM
				maestro_tipo_doc";
		
		$resp = $this->db->query($str);

		$send["status"] = true;
		$send["info"] = $resp;

		return $send;
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