<?php

namespace Gsys;

class equipos{

	//Variable globalr para usar la bd
	private $db;
	
	//Se declara la variable de respeusta
	private $send = array("");
	function __construct(){

		$this->db = new db();


	}

	
	function getTipoEquipos(){


		$str = "SELECT
				appmanto.equipment_list.EQUIPMENT
				FROM
				appmanto.equipment_list";

		$query = $this->db->query($str);

		$resp["message"] = $query;
		$resp["status"] = true;
  
		return $resp;


	}
	
	function loadEquipo($info){
		
		if($_SESSION["editEquipo"] != ""){
			
			$str = "SELECT
				CONSECUTIVE,CENTER,DESCRIPTION,COD,PLACE,BUY,MODEL,BRAND,SERIAL,WARANTY,VOLT,VOLT_MIN,TEMP,CURR,PRESS,POT,WEIGHT,FREC,
				SPEED,TYPE,DIAGNOSIS,LABORATORY,TREATMENT,REHAB,PREVENCION,FUNCT,MAINTENANCE,REPLACEMENT,DESPIECE,ELECTRIC,ELECTROMECHANICAL,
				ELECTRICITY,AIR,HYDRAULIC,OIL,WATER,MECANIC,PNEUMATIC,GAS,OTHERSOURCE,STEAM,OTHERSISTEMS,OBSERVATION,RISK,ACQUISITION,
				REGISTRO_TYPE,REGISTRO,DISTINTIVO,FECHA_INSTALACION,FECHA_RECIBIDO,FECHA_INICIO,FECHA_FABRICACION,COSTO,VIDA_UTIL,FABRICANTE,
				VAPOR2,ESTADO_EQUIPO, PROVEEDOR
				FROM
				equipment 
				WHERE
				equipment.CONSECUTIVE = '".$_SESSION["editEquipo"]."' 
				AND equipment.CENTER = '".$_SESSION["center"]."'";
				
			$query = $this->db->query($str);
			
			
			$resp["message"] = $query[0];
			$resp["status"] = true;
			
		}else{
			
			$resp["message"] = "";
			$resp["status"] = false;
		}
		
		
  
		return $resp;
		
	}
	
	function getEquipos($info){

		$filters = array();
		
		$filters[] = "CENTER = '".$_SESSION['center']."'";
		
		// if($info["CONSECUTIVE"] != ""){
			
			// $filters[] = "CONSECUTIVE LIKE '%".$info["CONSECUTIVE"]."%'";
		// }
		// if($info["COD"] != ""){
			
			// $filters[] = "COD LIKE '%".$info["COD"]."%'";
			
		// }
		// if($info["DESCRIPTION"] != ""){
			
			// $filters[] = "UPPER(DESCRIPTION) LIKE UPPER('%".$info["DESCRIPTION"]."%')";
			
		// }
		// if($info["COD_PRESTADOR"] != ""){
			
			// $filters[] = "UPPER(COD_PRESTADOR) LIKE UPPER('%".$info["COD_PRESTADOR"]."%')";
			
		// }
		
		$where = " WHERE ".implode(" AND ",$filters);
		
		 $str = "SELECT
				count(*) as QTY
				FROM
				equipment
				LEFT OUTER JOIN centers ON equipment.CENTER = centers.ID
				".$where."
				ORDER BY equipment.CONSECUTIVE";
		

		$qty = $this->db->query($str);
		 
		$str = "SELECT
				equipment.CONSECUTIVE,
				equipment.DESCRIPTION, 
				equipment.COD,
				equipment.PLACE,
				equipment.COD_PRESTADOR,
				equipment.SERIAL,
				'' AS CALIBRATION,
				'' AS MAINTENANCE,
				'' AS ACTIONS
				FROM
				equipment
				LEFT OUTER JOIN centers ON equipment.CENTER = centers.ID
				".$where."
				ORDER BY equipment.CONSECUTIVE LIMIT 50";
		
		$query = $this->db->query($str);
		
		
		for($i = 0; $i < count($query); $i++){
			
			
			$str = "SELECT
					maintenance.DATE,
					maintenance.CONSECUTIVE,
					maintenance.`NAME`,
					maintenance.CENTER,
					maintenance.PATH
					FROM
					maintenance
					WHERE
					maintenance.CONSECUTIVE = '".$query[$i]["CONSECUTIVE"]."' AND
					maintenance.TYPE = 'M' AND
					maintenance.CENTER = '".$_SESSION["center"]."'
					ORDER BY
					maintenance.DATE DESC
					LIMIT 3";
			
			$maintenance = $this->db->query($str);
			
			if(count($maintenance) > 0){

				$query[$i]["MAINTENANCE"] = $maintenance;
					
			}else{

				$query[$i]["CALIBRATION"] = null;

			}

			$str = "SELECT
					maintenance.DATE,
					maintenance.CONSECUTIVE,
					maintenance.`NAME`,
					maintenance.CENTER,
					maintenance.PATH
					FROM
					maintenance
					WHERE
					maintenance.CONSECUTIVE = '".$query[$i]["CONSECUTIVE"]."' AND
					maintenance.TYPE = 'C' AND
					maintenance.CENTER = '".$_SESSION["center"]."'
					ORDER BY
					maintenance.DATE DESC
					LIMIT 1"; 

			$calibration = $this->db->query($str);
			
			if(count($calibration) > 0){

				$query[$i]["CALIBRATION"] = $calibration;
					
			}else{

				$query[$i]["CALIBRATION"] = null;

			}
			
			
		}

		$resp["message"] = array("info"=>$query,"qty"=>$qty[0]["QTY"]);
		$resp["status"] = true;
  
		return $resp;
	}
	
	function updateEquipment($info){
		
		$str = "UPDATE equipment
				SET
				DESCRIPTION = '".$info['tipo']."',COD = '".$info['id']."',
				PLACE= '".$info['ubicacion']."' ,BUY='".$info['fechaCompra']."',MODEL='".$info['modelo']."',
				BRAND='".$info['marca']."',SERIAL='".$info['serial']."',WARANTY='".$info['fechaGarantia']."',
				VOLT='".$info['voltajeMax']."',VOLT_MIN='".$info['voltajeMin']."',TEMP='".$info['temperatura']."',
				CURR='".$info['corriente']."',PRESS='".$info['presion']."',POT='".$info['potencia']."',
				WEIGHT='".$info['peso']."',FREC='".$info['frecuencia']."',SPEED='".$info['velocidad']."',
				TYPE='".$info['movilidad']."',DIAGNOSIS='".$info['diagnostico']."',LABORATORY='".$info['analisisLab']."',
				TREATMENT='".$info['ttoMtto']."',REHAB='".$info['rehabilitacion']."',PREVENCION='".$info['prevencion']."',
				FUNCT='".$info['oepracion']."',MAINTENANCE='".$info['mantenimiento']."',REPLACEMENT='".$info['partes']."',
				DESPIECE='".$info['despiece']."',ELECTRIC='".$info['planosElectrico']."',ELECTROMECHANICAL='".$info['electromecanico']."',
				ELECTRICITY='".$info['electrico']."',AIR='".$info['aire']."',HYDRAULIC='".$info['hidraulico']."',
				OIL='".$info['petroleo']."',WATER='".$info['agua']."',MECANIC='".$info['mecanico']."',PNEUMATIC='".$info['planosNeumatico']."',
				GAS='".$info['gas']."',OTHERSOURCE='".$info['tecnologiaOtro']."',STEAM='".$info['fuenteVapor']."',
				OTHERSISTEMS='".$info['fuenteOtro']."',OBSERVATION='".$info['observaciones']."',RISK='".$info['clasRiesgo']."',
				ACQUISITION='".$info['adquisicion']."',DISTINTIVO='".$info['distintivo']."',FECHA_INSTALACION='".$info['fechaInstalacion']."',
				FECHA_RECIBIDO='".$info['fechaRecibido']."',FECHA_FABRICACION='".$info['fechaFabricacion']."',COSTO='".$info['costo']."',
				VIDA_UTIL='".$info['vidaUtil']."',FABRICANTE='".$info['fabricante']."', VAPOR2='".$info['tecVapor']."', 
				ESTADO_EQUIPO='".$info['proveedor']."',PROVEEDOR ='".$info['estadoEquipo']."'
				WHERE
				equipment.CONSECUTIVE = '".$_SESSION["editEquipo"]."' 
				AND equipment.CENTER = '".$_SESSION["center"]."' ";
		
		$update = $this->db->query($str);
			
		$resp["message"] = $info;
		$resp["status"] = true;
   
		return $resp;
			
		
	}
	
	function eliminarEquipo($info){
		
		$str = "DELETE FROM equipment 
				WHERE
				equipment.CONSECUTIVE = '".$info["id"]."' AND
				equipment.CENTER = '".$_SESSION['center']."'";
		
		$eliminar = $this->db->query($str);
		
		$resp["message"] = $info;
		$resp["status"] = true;
  
		return $resp;
	}
	
	function crearEquipo($info){
		
		

		$str = "SELECT
				equipment.CONSECUTIVE
				FROM
				equipment
				WHERE
				equipment.CONSECUTIVE = '".$info["id"]."' AND
				equipment.CENTER = '".$_SESSION['center']."'";
		
		$equipment = $this->db->query($str);
		
		if(count($equipment) > 0){
			
			$resp["message"] = $info;
			$resp["status"] = false;
	  
			return $resp;
			
		}else{
			
			$str = "INSERT INTO equipment 
					(CONSECUTIVE,CENTER,DESCRIPTION,COD,PLACE,BUY,MODEL,BRAND,SERIAL,WARANTY,VOLT,VOLT_MIN,TEMP,CURR,PRESS,POT,WEIGHT,FREC,
					SPEED,TYPE,DIAGNOSIS,LABORATORY,TREATMENT,REHAB,PREVENCION,FUNCT,MAINTENANCE,
					REPLACEMENT,DESPIECE,ELECTRIC,ELECTROMECHANICAL,ELECTRICITY,AIR,HYDRAULIC,OIL,WATER,MECANIC,PNEUMATIC,GAS,OTHERSOURCE,
					STEAM,OTHERSISTEMS,OBSERVATION,RISK,ACQUISITION,REGISTRO_TYPE,REGISTRO,
					DISTINTIVO,FECHA_INSTALACION,FECHA_RECIBIDO,FECHA_INICIO,FECHA_FABRICACION,COSTO,VIDA_UTIL,
					FABRICANTE, VAPOR2, ESTADO_EQUIPO,PROVEEDOR)
					VALUES
					('".$info['id']."','".$_SESSION['center']."','".$info['tipo']."','".$info['id']."','".$info['ubicacion']."',
					 '".$info['fechaCompra']."','".$info['modelo']."','".$info['marca']."','".$info['serial']."','".$info['fechaGarantia']."',
					 '".$info['voltajeMax']."','".$info['voltajeMin']."','".$info['temperatura']."','".$info['corriente']."','".$info['presion']."',
					 '".$info['potencia']."','".$info['peso']."','".$info['frecuencia']."','".$info['velocidad']."',
					 '".$info['movilidad']."','".$info['diagnostico']."', 
					 '".$info['analisisLab']."','".$info['ttoMtto']."','".$info['rehabilitacion']."','".$info['prevencion']."',
					 '".$info['oepracion']."','".$info['mantenimiento']."',
					 '".$info['partes']."','".$info['despiece']."','".$info['planosElectrico']."','".$info['electromecanico']."','".$info['electrico']."',
					 '".$info['aire']."','".$info['hidraulico']."','".$info['petroleo']."','".$info['agua']."','".$info['mecanico']."',
					 '".$info['planosNeumatico']."','".$info['gas']."','".$info['tecnologiaOtro']."','".$info['fuenteVapor']."','".$info['fuenteOtro']."',
					 '".$info['observaciones']."','".$info['clasRiesgo']."','".$info['adquisicion']."','0','0','".$info['distintivo']."',
					 '".$info['fechaInstalacion']."','".$info['fechaRecibido']."','".$info['fechaInstalacion']."','".$info['fechaFabricacion']."','".$info['costo']."',
					 '".$info['vidaUtil']."','".$info['fabricante']."','".$info['tecVapor']."', '".$info['estadoEquipo']."','".$info['proveedor']."')";

			try{
				
				$insert = $this->db->query($str);
			
				$mensaje = file_get_contents("../recursos/emails/nuevoEquipo.html");
			
				$mensaje = str_replace("!@codigo","id",$mensaje);
				$mensaje = str_replace("!@user",$_SESSION["user"],$mensaje);
				
				$mailSend = $this->sendMail("jhoyosl@globalsys.co",$mensaje,"Nuevo Equipo");
				
				if($mailSend == "Message sent!"){
			
					$resp["status"] = true;
					$resp["message"] = "";


				}else{
					
					$resp["status"] = false;
					$resp["message"] = "";
					
					
				}
				
			}catch(\Exception $e){
				
				
				$resp["message"] = $e->getMessage();
				$resp["status"] = true;
		   
				return $resp;
			}

			$resp["message"] = $info;
			$resp["status"] = true;
	   
			return $resp;
		}
		
	}
	
	function setEquipoEdit($info){
		
		if($info["id"] == ""){
			
			$resp["message"] = $info;
			$resp["status"] = false;
			
		}else{
			
			$_SESSION["editEquipo"] = $info["id"];
			
			$resp["message"] = $_SESSION["editEquipo"];
			$resp["status"] = true;
			
		}
		
		
		return $resp;
		
	}
	
	function getUrl($info){
		
		
		$cfg = parse_ini_file("cfg/cfg.ini",true);
		
		$resp["status"] = true;
		$resp["message"] = $cfg["rutas"][$info["ruta"]];
		
		return $resp;
		
	}
	
	/******************* CALIBRACIONES **************************/
	function cargarCalibracion($info){
		
		$allowedExts = array("pdf","PDF","gif", "jpeg", "jpg", "png", "PNG");

		$temp = explode(".", $_FILES[0]["name"]);
		
		$extension = end($temp);
		
		switch ($_FILES[0]["type"]){

			case 'image/gif':
				$ext = "gif";
				break;
			case 'image/jpeg':
				$ext = "jpeg";
				break;
			case 'image/jpg':
				$ext = "jpg";
				break;
			case 'image/png':
				$ext = "png";
				break;
			case 'application/pdf':
				$ext = "pdf";
				break;
		}
		
		$nameMd5 = md5($info["fecha"].$_FILES[0]["name"].$_SESSION["user"].$_SESSION["center"].$info["id"]."M").".".$ext;

		if ($_FILES[0]["error"] > 0){
			
			$resp["message"] = $_FILES[0]["error"];
			$resp["status"] = false;

		}else{
			
			
			$cfg = parse_ini_file("cfg/cfg.ini",true);
			$destino = $cfg["rutas"]["calibraciones"].$nameMd5;
				
			if(file_exists($destino)){
				
				$resp["message"] = "EXISTS";
				$resp["status"] = false;
				
			}else{

				if(move_uploaded_file($_FILES[0]["tmp_name"],$destino)){
					
					$str = "INSERT INTO maintenance
							(DATE, NAME, USER, CENTER, CONSECUTIVE, TYPE, PATH)
							VALUES
							('".date("Y-m-d",strtotime($info["fecha"]))."', '".$_FILES[0]["name"]."', '".$_SESSION["user"]."',
							 '".$_SESSION["center"]."','".$info["id"]."','C','".$nameMd5."')";

					$query = $this->db->query($str); 
					
					$mensaje = file_get_contents("../recursos/emails/nuevaCalibracion.html");
			
					$mensaje = str_replace("!@id",$info["id"],$mensaje);
					$mensaje = str_replace("!@user",$_SESSION["user"],$mensaje);

					
					$mailSend = $this->sendMail("jhoyosl@globalsys.co",$mensaje,"Nueva Calibración");
					
					if($mailSend == "Message sent!"){
				
						$resp["message"] = $str;
						$resp["status"] = true;


					}else{
						
						$resp["status"] = false;
						$resp["message"] = "";
						
						
					}
					
				}else{
					
					$resp["message"] = "No se pudo subir el archivo, intentelo más tarde";
					$resp["status"] = false;
					
				}
				
				
			}
	
		}
		
		return $resp;

		
	}
	
	
	
	/******************* MANTENIMIENTOS **************************/
	function getMantenimientos($info){
		
		
		
		$filters = array();
		$where = "";
		
		$filters[] = "maintenance.CENTER = '".$_SESSION['center']."'";
		$filters[] = "maintenance.TYPE = 'M'";
		// $filters[] = "DATE >= '".$info["FROM"]."'";
		// $filters[] = "DATE <= '".$info["TO"]."'";

		// if($info["CONSECUTIVE"] != ""){
			
			// $filters[] = "CONSECUTIVE LIKE '%".$info["CONSECUTIVE"]."%'";
			
		// }
		// if($info["CHECKED"] != ""){
			
			// $filters[] = "CHECKED LIKE '%".$info["CHECKED"]."%'";
			
		// }
		
		$where = " WHERE ".implode(" AND ",$filters);
		
		 $str = "SELECT
				count(*) as QTY
				FROM
				maintenance
				".$where."
				ORDER BY maintenance.DATE";
		
		
		$qty = $this->db->query($str);
		
		$str = "SELECT
				maintenance.DATE,
				maintenance.CONSECUTIVE,
				equipment.DESCRIPTION,
				maintenance.`NAME`,
				maintenance.`USER`,
				maintenance.CENTER,
				maintenance.CHECKED,
				maintenance.VERIFYBY,
				maintenance.COST,
				maintenance.TYPE,
				maintenance.PATH,
				maintenance.MANT_TYPE
				FROM
				maintenance
				INNER JOIN equipment ON maintenance.CONSECUTIVE = equipment.CONSECUTIVE AND maintenance.CENTER = equipment.CENTER
				".$where."
				ORDER BY maintenance.DATE DESC";
				// LIMIT ".$info['limits']['lower'].",".$info['limits']['upper']."";
		
		// return $str;
		$query = $this->db->query($str);

		 
		$resp["message"] = array("info"=>$query,"qty"=>$qty[0]["QTY"]);
		$resp["status"] = true;
		
		return $resp;
		

	}
	
	
	function cargarMantenimiento($info){
		
		$allowedExts = array("pdf","PDF","gif", "jpeg", "jpg", "png", "PNG");

		$temp = explode(".", $_FILES[0]["name"]);
		
		$extension = end($temp);
		
		switch ($_FILES[0]["type"]){

			case 'image/gif':
				$ext = "gif";
				break;
			case 'image/jpeg':
				$ext = "jpeg";
				break;
			case 'image/jpg':
				$ext = "jpg";
				break;
			case 'image/png':
				$ext = "png";
				break;
			case 'application/pdf':
				$ext = "pdf";
				break;
		}
		
		$nameMd5 = md5($info["fecha"].$_FILES[0]["name"].$_SESSION["user"].$_SESSION["center"].$info["id"]."M").".".$ext;
		
		
		
		if ($_FILES[0]["error"] > 0){
			
			$resp["message"] = $_FILES[0]["error"];
			$resp["status"] = false;

		}else{
			
			
			$cfg = parse_ini_file("cfg/cfg.ini",true);
			$destino = $cfg["rutas"]["mantenimiento"].$nameMd5;
				
			if(file_exists($destino)){
				
				$resp["message"] = "EXISTS";
				$resp["status"] = false;
				
			}else{

				if(move_uploaded_file($_FILES[0]["tmp_name"],$destino)){
					
					$str = "INSERT INTO maintenance
							(DATE, NAME, USER, CENTER, CONSECUTIVE, TYPE, PATH, MANT_TYPE)
							VALUES
							('".date("Y-m-d",strtotime($info["fecha"]))."', '".$_FILES[0]["name"]."', '".$_SESSION["user"]."',
							 '".$_SESSION["center"]."','".$info["id"]."','M','".$nameMd5."','".$info["tipo"]."')";

					$query = $this->db->query($str); 
					
					$mensaje = file_get_contents("../recursos/emails/nuevoMantenimiento.html");
			
					$mensaje = str_replace("!@codigo",$info["id"],$mensaje);
					$mensaje = str_replace("!@user",$_SESSION["user"],$mensaje);
					$mensaje = str_replace("!@tipo",strtoupper($info["tipo"]),$mensaje);
					
					$mailSend = $this->sendMail("jhoyosl@globalsys.co",$mensaje,"Nuevo Equipo");
					
					if($mailSend == "Message sent!"){
				
						$resp["message"] = $str;
						$resp["status"] = true;


					}else{
						
						$resp["status"] = false;
						$resp["message"] = "";
						
						
					}
					
				}else{
					
					$resp["message"] = "No se pudo subir el archivo, intentelo más tarde";
					$resp["status"] = false;
					
				}
				
				
			}
	
		}
		
		return $resp;

		
	}

	function sendMail($userEmail, $html, $subject){
		

		$cfg = parse_ini_file("cfg/cfg.ini",true);	
		
		require_once('libs/PHPMailer/class.phpmailer.php');

		require_once("libs/PHPMailer/class.smtp.php");

		
		$mailInfo = $cfg["mail"];
		
		$mail             = new \PHPMailer();
		$address = $userEmail;
		

		$body             = htmlentities($html); //file_get_contents('contents.html');
		//$body             = $info["HTML"]; //file_get_contents('contents.html');
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP(); // telling the class to use SMTP

		$mail->Host       = $mailInfo["Host"]; // SMTP server
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only


		$mail->SMTPAuth   = $mailInfo["SMTPAuth"];               // enable SMTP authentication
		$mail->SMTPSecure = $mailInfo["SMTPSecure"];;                 // sets the prefix to the servier
		$mail->Host       = $mailInfo["Host"];    // sets GMAIL as the SMTP server
		$mail->Port       = $mailInfo["Port"];                  // set the SMTP port for the GMAIL server
		$mail->Username   = $mailInfo["Username"];  // GMAIL username
		$mail->Password   = $mailInfo["Password"];           // GMAIL password

		$mail->SetFrom($mailInfo["SetFrom"], $mailInfo["SetFromText"]);

		// $mail->AddReplyTo("AddReplyTo@yourdomain.com","First Last");

		$mail->Subject    = $subject;

		$mail->AltBody    = "Para ver el mensaje, porfavor utilice un visor de correo con compatibilidad HTML"; // optional, comment out and test

		// $mail->MsgHTML($body);
		$mail->Body = $html;


		$mail->IsHTML(true);
		
		$mail->AddAddress($address, "Test");
		$mail->AddAddress("juan.vidal@indesap.com", "Test");

		// $mail->AddAttachment("images/phpmailer.gif");      // attachment
		// $mail->AddAttachment("images/phpmailer_mini.gif"); // attachment


		if(!$mail->Send()) {
		  // echo "Mailer Error: " . $mail->ErrorInfo;
		  return "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  // echo "Message sent!";
		  return "Message sent!";
		}
		
			
		
	}
	
	
}