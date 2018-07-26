<?php

namespace Gsys;

class user{

	//Variable globalr para usar la bd
	private $db;
	
	//Se declara la variable de respeusta
	private $send = array("");
	function __construct(){

		$this->db = new db();


	}

	function chkLogin($info){

		
		if(isset($_SESSION["user"])){

			// $str = "SELECT
			// 		centers.`NAME`
			// 		FROM usuarios INNER JOIN centers ON users.CENTER = centers.ID
			// 		WHERE 
			// 		usuarios.ID = '".$_SESSION["user"]."'";

			// $center = $this->db->query($str);

			$send["status"] = true;
			$send["info"] = "900862658-9";

			return $send;

		}else{

			$send["status"] = false;
			$send["info"] = "No session";

			return $send;
		}


	}

	//Validación par inicio de sesión en la web
	function login($info){
		

		$str = "SELECT
				users.ID,
				users.`NAMES` AS NOMBRES,
				users.LASTNAME AS APELLIDOS,
				users.PHONE,
				users.ADDRESS,
				users.PSSW,
				users.MAIL,
				users.CENTER
				FROM
				users
				WHERE
				users.ID = '".$info["user"]."' AND
				users.PSSW = '".md5($info["pssw"])."'";
		
		
		$query = $this->db->query($str);	
		
		if(count($query) > 0){
			
			$resp["message"] = "";
			$resp["status"] = true;
			
			$this->sessionInit($query[0]);
			
			$resp["message"] = "SUCCCESS";
			$resp["status"] = true; 

		}else{
			
			$resp["message"] = "Usuario y/o contraseña incorrectos";
			$resp["status"] = false; 
		}
		
		
		return $resp;
	}


	function getUserSession($info){
		
		if(count($_SESSION) == 0){

			$send["status"] = false;

		}else{

			$send["status"] = true;
			$send["info"] = $_SESSION;

		}
		

		return $send;
	}
	
	function sessionInit($info){

		
		$_SESSION["user"] = $info["ID"];
		$_SESSION["names"] = $info["NOMBRES"]." ".$info["APELLIDOS"];
		$_SESSION["center"] = $info["CENTER"];
		$_SESSION["editEquipo"] = null;
		
		return $info;

	}

	function logout($info){

		session_destroy();

		$send["status"] = true;
		$send["info"] = $info;

		return $send;
	}

	function nuevoUsuario($info){

		$str = "SELECT
				users.ID
				FROM
				users
				WHERE
				users.ID = '".$info["ID"]."'";

		$query = $this->db->query($str);

		if(count($query) > 0){

			$resp["message"] = "Ya existe un usuario con el ID: ".$info["ID"];
			$resp["status"] = false;

		}else{


			$str = "INSERT INTO users 
					(ID, NAMES, LASTNAME, PHONE, PSSW, MAIL, CENTER)
					VALUES
					('".$info["ID"]."','".$info["NAMES"]."','".$info["LASTNAME"]."','".$info["PHNONE"]."',
					 '".md5($info["PSSW"])."','".$info["EMAIL"]."','".$info["CENTER"]."')";
			
			$query = $this->db->query($str);			

			$resp["message"] = "Usuario creado exitosamente";
			$resp["status"] = true;

		}

		return $resp;
	}
	
	function editUser($info){

		$str = "UPDATE users 
				SET 
				NAMES = '".$info["NAMES"]."',
				LASTNAME = '".$info["LASTNAME"]."',
				PHONE = '".$info["PHONE"]."',
				MAIL = '".$info["EMAIL"]."'
				WHERE
				ID = '".$info["ID"]."'";

		$query = $this->db->query($str);

		$resp["message"] = $str;
		$resp["status"] = true;

		return $resp;

	}
	
	function eliminarUsuario($info){
		
		$str = "DELETE FROM users WHERE ID = '".$info["ID"]."'";
		
		$this->db->query($str);
		
		$resp["message"] = "Usuario eliminado exitosamente";
		$resp["status"] = true;

		return $resp;
		
	}
	
	function getUserList($info){
		
		$str = "SELECT
				centers.`NAME` AS CENTER,
				users.ID,
				users.`NAMES`,
				users.LASTNAME,
				users.PHONE,
				users.ADDRESS,
				users.MAIL AS EMAIL
				FROM
				users
				JOIN centers
				ON users.CENTER = centers.ID
				ORDER BY ID ASC";
				
		$query = $this->db->query($str);	
		
		$resp["message"] = $query;
		$resp["status"] = true;

		return $resp;
		
	}
	
}