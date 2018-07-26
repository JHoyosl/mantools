<?php

namespace Gsys;

class ui{

	private $db;
	function __construct(){

		$this->db = new db();


	}

	function load($info){

		
		$send = array(); 
		
		$html = file_get_contents("../html/".$info["target"].".html");

		$send["status"] = true;
		$send["info"] = $html;

		return $send;
		

	}
}