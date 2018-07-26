<?php

namespace Gsys;

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("America/Bogota");
header('Content-Type: text/html; charset=utf-8');

class entryPoint{

	private $params;
	
	function __construct($info){
		

		// var_dump(utf8_encode(base64_decode($_POST["info"])));
		if(isset($_POST["info"])){
			
			// $this->params = json_decode($_POST["info"], true);
			$this->params = json_decode(utf8_encode(base64_decode($_POST["info"])), true);
			
			
		}else{
			
			// $this->params = json_decode(file_get_contents("php://input"), true);
			$this->params = json_decode(utf8_encode(base64_decode(file_get_contents("php://input"))), true);
			
		}
		
	}
	
	function start(){
		
		$logs = "";
		
		session_start();
		
		$mName = $this->params["methodName"];

		require_once("db.php");
		require_once("mailer.php");
		require_once($this->params["className"].".php");

		$class = __NAMESPACE__ . '\\'.$this->params["className"];
		
		$instancia = new $class(); 
		
		$method = $this->params["methodName"];

		try{
			
			$exec = null;
			
			$exec = $instancia->$method($this->params["data"]);
			
			$resp = array(	"data"=>$exec,
							"exception"=>"");
			return $resp;

		}catch(Exception $e){
			$resp = array(	"data"=>$exec,
							"exception"=>$e->getMessage());
			return $resp;	
		}
		
 
	}
}


$entry = new entryPoint($_POST);

echo base64_encode(json_encode($entry->start()));
// echo json_encode($entry->start());
?>