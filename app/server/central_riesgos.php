<?php
namespace Gsys;

class central_riesgos{

	private $db;
	function __construct(){

		$this->db = new db();


	}
	
	function cancelProcess(){

		session_destroy();
		return;
	}

	function testEmail($info){

		$imgs = array();
		$imgs[] = array("name"=>"banner","ext"=>"JPG");
		$imgs[] = array("name"=>"logo","ext"=>"png");
		$imgs[] = array("name"=>"security","ext"=>"png");
		$imgs[] = array("name"=>"seguridad","ext"=>"jpg");

		return $this->sendMail($info["email"],"TESTING2",$imgs);


		
	}



	function validarCodigo($info){
		
		$str = "SELECT * FROM procesos 
				WHERE 
				APPROVE_CODE = '".$info["approve_code"]."' AND 
				SOL_ID = '".$_SESSION["SOL_ID"]."'";
		

		$codCount = $this->db->query($str);

		if(count($codCount) == 1){
			
			$send["status"] = true;
			$send["info"] = count($codCount);
			
			//$_SESSION["SOL_ID"] = "";
		}else{
			
			$send["status"] = false;
			$send["info"] = "No se pudo validar el código, verfique nuevamente";
			
		}
		
		
		return $send;
		
		
	}
	
	function sendMail($userEmail, $code, $embeddedFiles = array()){
		

		$cfg = parse_ini_file("cfg/cfg.ini",true);	
		
		require_once('libs/PHPMailer/class.phpmailer.php');

		require_once("libs/PHPMailer/class.smtp.php");

		
		$mailInfo = $cfg["mail"];
		
		$mail             = new \PHPMailer();
		$address = $userEmail;
		

		//$body             = htmlentities("Su código es: ".$code); //file_get_contents('html/mail_code.html');
		$body             = str_replace("COD_REPLACE_INPUT", $code, file_get_contents('html/mail_code.html'));
		//$body             = $info["HTML"]; //file_get_contents('contents.html');
		
		foreach ($embeddedFiles as $value) {
			
			$mail->AddEmbeddedImage('html/img/'.$value["name"].".".$value["ext"],$value["name"]);

		}

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

		$mail->Subject    = "Codigo de aprobacion";

		$mail->AltBody    = "Para ver el mensaje, porfavor utilice un visor de correo con compatibilidad HTML"; // optional, comment out and test

		$mail->MsgHTML($body);

		
			$mail->AddAddress($address, "pruebas2");

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
	
	function extra_info($info){
		
		$approve_code = $this->generateRandomString();
		
		$str = "UPDATE procesos 
				SET
				APPROVE_CODE ='".$approve_code."'
				WHERE 
				SOL_ID = '".$_SESSION["SOL_ID"]."'";
				
		$this->db->query($str);
		
		$imgs = array();
		$imgs[] = array("name"=>"banner","ext"=>"JPG");
		$imgs[] = array("name"=>"logo","ext"=>"png");
		$imgs[] = array("name"=>"security","ext"=>"png");
		$imgs[] = array("name"=>"seguridad","ext"=>"jpg");

		$mailSend = $this->sendMail($info["email"],$approve_code,$imgs);
		
		if($mailSend == "Message sent!"){
			
			$send["status"] = true;
			$send["info"] = "";


		}else{
			
			$send["status"] = false;
			$send["info"] = "";
			
			
		}
		
		return $send;
		
	}
	
	function generateRandomString($length = 6) {
		
		
		
		
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	function consultar($info){

		
		if(isset($_SESSION["SOL_ID"]) && $_SESSION["SOL_ID"] != 0){

			$send["status"] = true;
			$send["info"] = $_SESSION["SOL_ID"];

		}

		$send = array();
		$SOL_ID = uniqid();
		$C_DATE = date("Y-m-d H:i:s");
		
		$strCifin = "SELECT 
					cifin_tercero.ID, 
					cifin_tercero.CREATE_DATE, 
					cifin_tercero.NUM_OBLIGACION, 
					cifin_tercero.SOL_ID, 
					cifin_tercero.PAQUETE_INFO, 
					cifin_tercero.ARCHIVO, 
					cifin_tercero.TIPO_IDENTIFICACION, 
					cifin_tercero.NUMRO_IDENTIFIACION, 
					cifin_tercero.NOMBRE_TITULAR, 
					cifin_tercero.LUGAR_EXPEDICION, 
					cifin_tercero.FECHA_EXPEDICION, 
					cifin_tercero.ESTADO, 
					cifin_tercero.RANGO_EDAD, 
					cifin_tercero.CODIGO_SCORE, 
					cifin_tercero.INDICADO_DEFAULT, 
					cifin_tercero.INDICADOR_SCORE, 
					cifin_tercero.OBSERVACION, 
					cifin_tercero.PUNTAJE, 
					cifin_tercero.SUB_POBLACION, 
					cifin_tercero.TASA_MOROSIDAD, 
					cifin_tercero.TIPO_SCORE
				FROM cifin_tercero
				WHERE NUMRO_IDENTIFIACION = '".$info["docNum"]."' AND 
				DATE_ADD('2017-06-15', INTERVAL - 30 DAY) < CREATE_DATE
				ORDER BY CREATE_DATE DESC
				LIMIT 1";	
		
		$tercero = $this->db->query($strCifin);
		
		$str = "INSERT INTO procesos 
				(SOL_ID,DATE_CREATED,DOC_NUM)
				VALUES 
				('".$SOL_ID."','".$C_DATE."','".$info["docNum"]."')
				";
		
		
		$this->db->query($str);
		

		if(count($tercero) == 0){

			$respuestaCentral = $this->cifinRequest($info["docNum"],"1");
			
			

			if(!$respuestaCentral){


				$send["status"] = false;
				$send["info"] = "No se pudo realizar el proceso, intentlo más tarde";
				return $send;

			}

			// return $respuestaCentral["xml"]->Tercero->FechaExpedicion;

			$respuestaCentral["SOL_ID"] = $SOL_ID;
			$respuestaCentral["DATE"] = $C_DATE;
			
			$this->saveInfoComercial_Test($respuestaCentral);

			$tercero = $this->db->query($strCifin);
	

		}

		if($tercero[0]["ID"] == ""){


			$send["status"] = false;
			$send["info"] = "Lo sentimos, en este momento no podemos procesar tu solicitud, vuelve aintentarlo en 3 Meses";
			return $send;

		}

		if($tercero[0]["PUNTAJE"] < 800){


			$send["status"] = false;
			$send["info"] = "Lo sentimos, en este momento no podemos procesar tu solicitud, vuelve aintentarlo en 6 Meses";
			return $send;

		}

		if($info["fecahInput"] != $tercero[0]["FECHA_EXPEDICION"]){

			$send["status"] = false;
			$send["info"] = "Inconsitencia con la fecha de Expedición";
			return $send;
		}

		if("VIGENTE" != $tercero[0]["ESTADO"]){

			$send["status"] = false;
			$send["info"] = "Inconsitencia con la vigencia del documento";
			return $send;
		}

		$send["status"] = true;
		$send["info"] = $SOL_ID;

		$_SESSION["tercero"] = $tercero;
		$_SESSION["SOL_ID"] = $SOL_ID;

		return $send;

		
	}


	function cifinRequest($num_doc, $type_doc){

		$parameters = "383394 Dacor5 1 2042 24 ".$type_doc." ".$num_doc." http://cifinpruebas.asobancaria.com/InformacionComercialWS/services/InformacionComercial";
		
		$resp = shell_exec('java -jar libs/ws_cifin/ClienteInformacionComercialHabeasData.jar '.$parameters);
		
		if($resp == false){
			
			return false;		
		}

		$pos = strpos($resp,"<");

		$string = substr($resp, $pos);
						
		@$xml = simplexml_load_string($string);

		return array("xml"=>$xml);
	}


	function saveInfoComercial_Test($info){


		$date = date("Y-m-d H:i:s");

		//SE VALIDA LA EXISTENCIA DEL INDICE
		$xml = $info["xml"];
		
		$archivo  = (array) $xml->attributes();

		if(!empty($xml->Tercero->Consolidado->ResumenPrincipal)){

			$rows = array();

			
			foreach ($xml->Tercero->Consolidado->ResumenPrincipal as $key => $Registro) {
				
				foreach($Registro as $obligacion){

					 $rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$obligacion->PaqueteInformacion."',
					 	  '".$obligacion->CantidadObligacionesMora."','".$obligacion->CuotaObligacionesDia."',
					 	  '".$obligacion->CuotaObligacionesMora."','".$obligacion->NumeroObligaciones."',
					 	  '".$obligacion->NumeroObligacionesDia."','".$obligacion->ParticipacionDeuda."',
					 	  '".$obligacion->SaldoObligacionesDia."','".$obligacion->SaldoObligacionesMora."',
					 	  '".$obligacion->TotalSaldo."','".$obligacion->ValorMora."','".$info["SOL_ID"]."',
					 	  'RP'
					 	)";
				}
			   
			}

			foreach ($xml->Tercero->Consolidado->ResumenDiferentePrincipal as $key => $Registro) {
				
				foreach($Registro as $obligacion){

					 $rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$obligacion->PaqueteInformacion."',
					 	  '".$obligacion->CantidadObligacionesMora."','".$obligacion->CuotaObligacionesDia."',
					 	  '".$obligacion->CuotaObligacionesMora."','".$obligacion->NumeroObligaciones."',
					 	  '".$obligacion->NumeroObligacionesDia."','".$obligacion->ParticipacionDeuda."',
					 	  '".$obligacion->SaldoObligacionesDia."','".$obligacion->SaldoObligacionesMora."',
					 	  '".$obligacion->TotalSaldo."','".$obligacion->ValorMora."','".$info["SOL_ID"]."',
					 	  'RDP'
					 	)";
				}
			   
			}
			
			foreach ($xml->Tercero->Consolidado->Registro as $key => $totalOblig) {
				
				$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$totalOblig->PaqueteInformacion."',
					 	  '".$totalOblig->CantidadObligacionesMora."','".$totalOblig->CuotaObligacionesDia."',
					 	  '".$totalOblig->CuotaObligacionesMora."','".$totalOblig->NumeroObligaciones."',
					 	  '".$totalOblig->NumeroObligacionesDia."','".$totalOblig->ParticipacionDeuda."',
					 	  '".$totalOblig->SaldoObligacionesDia."','".$totalOblig->SaldoObligacionesMora."',
					 	  '".$totalOblig->TotalSaldo."','".$totalOblig->ValorMora."','".$info["SOL_ID"]."',
					 	  'TT'
					 	)";

				
			   
			}

			
			if(count($rows) > 0){

				$cor = "INSERT INTO cifin_obligaciones_resumen
					(ID, CREATE_DATE, PAQUETE_INFO, OBLIGACION_MORA, OBLIGACION_DIA, CUOTA_MORA,
				     QTY_OBLIGACIONES_MORA, QTY_OBLIGACIONES_DIA, PARTICIPACION_DEUDA, SALDO_OBLIG_DIA,
				     SALDO_OBLIG_MORA, TOTAL_SALDO, VALOR_MORA, SOL_ID,TIPO)
				     VALUES".implode(",", $rows);

				
				$this->db->query($cor);
			}
			

			if(!empty($xml->Tercero->CuentasVigentes)){

				$rows = array();
				
				foreach ($xml->Tercero->CuentasVigentes->Obligacion as $key => $cuentasVigentes) {

					//SI NO EXISTE EL INDICE SE LLENA CON VALOR VÁLIDO EN BD
					$cuentasVigentes->ChequesDevueltos[0] = empty($cuentasVigentes->ChequesDevueltos)?"31-12-9999":$cuentasVigentes->ChequesDevueltos;
					
					$cuentasVigentes->FechaTerminacion[0] = empty($cuentasVigentes->FechaTerminacion)?"31-12-9999":$cuentasVigentes->FechaTerminacion;

					$cuentasVigentes->ValorInicial[0] = empty($cuentasVigentes->ValorInicial)?"0":$cuentasVigentes->ValorInicial;

					$cuentasVigentes->ValorInicial[0] = empty($cuentasVigentes->ValorInicial)?"0":$cuentasVigentes->ValorInicial;

					$cuentasVigentes->FechaPermanencia[0] = empty($cuentasVigentes->FechaPermanencia)?"31-12-9999":$cuentasVigentes->FechaPermanencia;
					
					$cuentasVigentes->DiasCartera[0] = empty($cuentasVigentes->DiasCartera)?"0":$cuentasVigentes->DiasCartera;
					
					$cuentasVigentes->ValorInicial[0] = empty($cuentasVigentes->ValorInicial)?"0":$cuentasVigentes->ValorInicial;

					
					$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$cuentasVigentes->NumeroObligacion."',
								'".$info["SOL_ID"]."','".$cuentasVigentes->PaqueteInformacion."',
								'".$cuentasVigentes->IdentificadorLinea."','".$cuentasVigentes->TipoContrato."',
								'".$cuentasVigentes->TipoEntidad."','".$cuentasVigentes->NombreEntidad."',
								'".$cuentasVigentes->Ciudad."','".$cuentasVigentes->Sucursal."',
								'".$cuentasVigentes->EstadoObligacion."','".$cuentasVigentes->FechaApertura."',
								'".$cuentasVigentes->FechaTerminacion."','".$cuentasVigentes->ValorInicial."',
								'".$cuentasVigentes->Comportamientos."','".$cuentasVigentes->FechaCorte."',
								'".$cuentasVigentes->FechaPermanencia."','".$cuentasVigentes->ChequesDevueltos."',
								'".$cuentasVigentes->DiasCartera."'
					 	)";	

				}

				if(count($rows) > 0){

					$cocv = "INSERT INTO cifin_cuentas_vigentes 
							(ID, CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
							TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO,FECHA_APERTURA,FECHA_TERMINACIÓN,
							VALOR_INICIAL,COMPORTAMIENTO,FECHA_CORTE,FECHA_PERMANENCIA,CHEQUES_DEVUELTOS,DIAS_CARTERA)
							VALUES".implode(",", $rows);	

					$this->db->query($cocv);
				}

			}

			
			if(!empty($xml->Tercero->CuentasExtinguidas)){
			
				
				$rows = array();

				//SE RECORREN LOS REGISTROS EXISTENTES
				foreach ($xml->Tercero->CuentasExtinguidas->Obligacion as $key => $cuentasExtinguidas) {
				

					$cuentasExtinguidas->FechaPermanencia[0] = empty($cuentasExtinguidas->FechaPermanencia)?"31-12-9999":$cuentasExtinguidas->FechaPermanencia;

					$cuentasExtinguidas->FechaTerminacion[0] = empty($cuentasExtinguidas->FechaTerminacion)?"31-12-9999":$cuentasExtinguidas->FechaTerminacion;					

					$cuentasExtinguidas->ValorInicial[0] = empty($cuentasExtinguidas->ValorInicial)?"31-12-9999":$cuentasExtinguidas->ValorInicial;										

					$cuentasExtinguidas->ChequesDevueltos[0] = empty($cuentasExtinguidas->ChequesDevueltos)?"0":$cuentasExtinguidas->ChequesDevueltos;														

					$cuentasExtinguidas->DiasCartera[0] = empty($cuentasExtinguidas->DiasCartera)?"0":$cuentasExtinguidas->DiasCartera;																			

					$cuentasExtinguidas->FechaApertura[0] = empty($cuentasExtinguidas->FechaApertura)?"0":$cuentasExtinguidas->FechaApertura;																								

					$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$cuentasExtinguidas->NumeroObligacion."',
								'".$info["SOL_ID"]."','".$cuentasExtinguidas->PaqueteInformacion."',
								'".$cuentasExtinguidas->IdentificadorLinea."','".$cuentasExtinguidas->TipoContrato."',
								'".$cuentasExtinguidas->TipoEntidad."','".$cuentasExtinguidas->NombreEntidad."',
								'".$cuentasExtinguidas->Ciudad."','".$cuentasExtinguidas->Sucursal."',
								'".$cuentasExtinguidas->EstadoObligacion."','".$cuentasExtinguidas->FechaApertura."',
								'".$cuentasExtinguidas->FechaTerminacion."','".$cuentasExtinguidas->ValorInicial."',
								'".$cuentasExtinguidas->Comportamientos."','".$cuentasExtinguidas->FechaCorte."',
								'".$cuentasExtinguidas->FechaPermanencia."','".$cuentasExtinguidas->ChequesDevueltos."',
								'".$cuentasExtinguidas->DiasCartera."'
					 	)";	

				}

				$coce = "INSERT INTO cifin_cuentas_extintas 
						(ID, CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
						TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO,FECHA_APERTURA,FECHA_TERMINACIÓN,
						VALOR_INICIAL,COMPORTAMIENTO,FECHA_CORTE,FECHA_PERMANENCIA,CHEQUES_DEVUELTOS,DIAS_CARTERA)
						VALUES".implode(",", $rows);


				$this->db->query($coce);
				
			}

			
			if(!empty($xml->Tercero->SectorFinancieroAlDia)){
			

				$rows = array();

				//SE RECORREN LOS REGISTROS EXISTENTES
				foreach ($xml->Tercero->SectorFinancieroAlDia->Obligacion as $key => $sectorFinancieroAlDia) {


					$sectorFinancieroAlDia->EntidadOriginadoraCartera[0] = empty($sectorFinancieroAlDia->EntidadOriginadoraCartera)?"_":$sectorFinancieroAlDia->EntidadOriginadoraCartera;
					
					$sectorFinancieroAlDia->TipoEntidadOriginadoraCartera[0] = empty($sectorFinancieroAlDia->TipoEntidadOriginadoraCartera)?"_":$sectorFinancieroAlDia->TipoEntidadOriginadoraCartera;
					
					$sectorFinancieroAlDia->TipoGarantia[0] = empty($sectorFinancieroAlDia->TipoGarantia)?"-_-":$sectorFinancieroAlDia->TipoGarantia;
					
					$sectorFinancieroAlDia->FechaTerminacion[0] = empty($sectorFinancieroAlDia->FechaTerminacion)?"31-12-9999":$sectorFinancieroAlDia->FechaTerminacion;

					$sectorFinancieroAlDia->CubrimientoGarantia[0] = empty($sectorFinancieroAlDia->CubrimientoGarantia)?"0":$sectorFinancieroAlDia->CubrimientoGarantia;

					$sectorFinancieroAlDia->Periodicidad[0] = empty($sectorFinancieroAlDia->Periodicidad)?"_":$sectorFinancieroAlDia->Periodicidad;

					$sectorFinancieroAlDia->Calificacion[0] = empty($sectorFinancieroAlDia->Calificacion)?"-_-":$sectorFinancieroAlDia->Calificacion;

					$sectorFinancieroAlDia->TipoMoneda[0] = empty($sectorFinancieroAlDia->TipoMoneda)?"-_-":$sectorFinancieroAlDia->TipoMoneda;

					$sectorFinancieroAlDia->ProbabilidadNoPago[0] = empty($sectorFinancieroAlDia->ProbabilidadNoPago)?"-_-":$sectorFinancieroAlDia->ProbabilidadNoPago;

					$sectorFinancieroAlDia->FechaPago[0] = empty($sectorFinancieroAlDia->FechaPago)?"31-12-9999":$sectorFinancieroAlDia->FechaPago;

					$sectorFinancieroAlDia->FechaPermanencia[0] = empty($sectorFinancieroAlDia->FechaPermanencia)?"31-12-9999":$sectorFinancieroAlDia->FechaPermanencia;

					$sectorFinancieroAlDia->ModoExtincion[0] = empty($sectorFinancieroAlDia->ModoExtincion)?"-_-":$sectorFinancieroAlDia->ModoExtincion;

					$sectorFinancieroAlDia->MoraMaxima[0] = empty($sectorFinancieroAlDia->MoraMaxima)?"0":$sectorFinancieroAlDia->MoraMaxima;

					$sectorFinancieroAlDia->NaturalezaReestructuracion[0] = empty($sectorFinancieroAlDia->NaturalezaReestructuracion)?"0":$sectorFinancieroAlDia->NaturalezaReestructuracion;

					$sectorFinancieroAlDia->NumeroCuotasMora[0] = empty($sectorFinancieroAlDia->NumeroCuotasMora)?"0":$sectorFinancieroAlDia->NumeroCuotasMora;

					$sectorFinancieroAlDia->Reestructurado[0] = empty($sectorFinancieroAlDia->Reestructurado)?"0":$sectorFinancieroAlDia->Reestructurado;

					$sectorFinancieroAlDia->TipoPago[0] = empty($sectorFinancieroAlDia->TipoPago)?"-_-":$sectorFinancieroAlDia->TipoPago;

					$sectorFinancieroAlDia->NumeroCuotasPactadas[0] = empty($sectorFinancieroAlDia->NumeroCuotasPactadas)?"-_-":$sectorFinancieroAlDia->NumeroCuotasPactadas;

					$sectorFinancieroAlDia->EstadoContrato[0] = empty($sectorFinancieroAlDia->EstadoContrato)?"-_-":$sectorFinancieroAlDia->EstadoContrato;

					$sectorFinancieroAlDia->FechaApertura[0] = empty($sectorFinancieroAlDia->FechaApertura)?"31-12-9999":$sectorFinancieroAlDia->FechaApertura;

					$sectorFinancieroAlDia->CuotasCanceladas[0] = empty($sectorFinancieroAlDia->CuotasCanceladas)?"31-12-9999":$sectorFinancieroAlDia->CuotasCanceladas;

					$sectorFinancieroAlDia->NumeroReestructuraciones[0] = empty($sectorFinancieroAlDia->NumeroReestructuraciones)?"0":$sectorFinancieroAlDia->NumeroReestructuraciones;

					

				$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$sectorFinancieroAlDia->NumeroObligacion."',
							'".$info["SOL_ID"]."','".$sectorFinancieroAlDia->PaqueteInformacion."',
							'".$sectorFinancieroAlDia->IdentificadorLinea."','".$sectorFinancieroAlDia->TipoContrato."',
							'".$sectorFinancieroAlDia->TipoEntidad."','".$sectorFinancieroAlDia->NombreEntidad."',
							'".$sectorFinancieroAlDia->Ciudad."','".$sectorFinancieroAlDia->Sucursal."',
							'".$sectorFinancieroAlDia->EstadoContrato."','".$sectorFinancieroAlDia->FechaApertura."',
							'".$sectorFinancieroAlDia->FechaTerminacion."','".$sectorFinancieroAlDia->Calidad."',
							'".$sectorFinancieroAlDia->EstadoObligacion."','".$sectorFinancieroAlDia->ModalidadCredito."',
							'".$sectorFinancieroAlDia->LineaCredito."','".$sectorFinancieroAlDia->Periodicidad."',
							'".$sectorFinancieroAlDia->Calificacion."','".$sectorFinancieroAlDia->ValorInicial."',
							'".$sectorFinancieroAlDia->SaldoObligacion."','".$sectorFinancieroAlDia->ValorMora."',
							'".$sectorFinancieroAlDia->ValorCuota."','".$sectorFinancieroAlDia->TipoMoneda."',
							'".$sectorFinancieroAlDia->CuotasCanceladas."','".$sectorFinancieroAlDia->TipoGarantia."',
							'".$sectorFinancieroAlDia->CubrimientoGarantia."','".$sectorFinancieroAlDia->MoraMaxima."',
							'".$sectorFinancieroAlDia->Comportamientos."','".$sectorFinancieroAlDia->ParticipacionDeuda."',
							'".$sectorFinancieroAlDia->ProbabilidadNoPago."','".$sectorFinancieroAlDia->FechaCorte."',
							'".$sectorFinancieroAlDia->ModoExtincion."','".$sectorFinancieroAlDia->FechaPago."',
							'".$sectorFinancieroAlDia->FechaPermanencia."','".$sectorFinancieroAlDia->NumeroReestructuraciones."',
							'".$sectorFinancieroAlDia->NaturalezaReestructuracion."',
							'".$sectorFinancieroAlDia->TipoEntidadOriginadoraCartera."',
							'".$sectorFinancieroAlDia->EntidadOriginadoraCartera."','".$sectorFinancieroAlDia->TipoPago."',
							'".$sectorFinancieroAlDia->EstadoTitular."','".$sectorFinancieroAlDia->NumeroCuotasPactadas."',
							'".$sectorFinancieroAlDia->NumeroCuotasMora."','".$sectorFinancieroAlDia->Reestructurado."'
							
				 	)";	
				}


				$sfad = "INSERT INTO cifin_sector_finan_aldia 
						(ID,CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
						 TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO_CONTRATO,FECHA_APERTURA,FECHA_TERMINACIÓN,
						 CALIDAD,ESTADO_OBLIGACION,MODALIDAD_CREDITO,LINEA_CREDITO,PERIODICIDAD,
						 CALIFICACION,VALOR_INICIAL,SALDO_OBLIGACION,VALOR_MORA,VALOR_CUOTA,TIPO_MONEDA,
						 CUOTAS_CANCELADAS,TIPO_GARANTIA,CUBRIMIENTO_GARANTIA,MORA_MAXIMA,COMPORTAMIENTO,
						 PATICIPACION_DEUDA,PROBABILIDAD_NOPAGO,FECHA_CORTE,MODO_EXTINCION,FECHA_PAGO,
						 FECHA_PERMANENCIA,NUM_REESTRUCTURA,NATURALEZA_REESTRUCTURA,TIPO_ENTIDAD_ORIGIN_CARTERA,
						 ENTIDAD_ORGIN_CARTERA,TIPO_PAGO,ESTADO_TITULAR,NUMERO_CUOTAS_PACTADAS,NUM_CUOTAS_MORA,
						 REESTRUCTURADO)
						VALUES".implode(",", $rows);

				
				$this->db->query($sfad);
			}

			
			//CADA RESULTADO ESTÁ EN XML SE CONVIERTE EN ARRAY
			if(!empty($xml->Tercero->SectorFinancieroExtinguidas)){
			

				$rows = array();
				
				
				foreach ($xml->Tercero->SectorFinancieroExtinguidas->Obligacion as $key => $secFinExt) {
				
					$secFinExt->EntidadOriginadoraCartera[0] = empty($secFinExt->EntidadOriginadoraCartera)?"-_-":$secFinExt->EntidadOriginadoraCartera;

					$secFinExt->TipoEntidadOriginadoraCartera[0] = empty($secFinExt->TipoEntidadOriginadoraCartera)?"-_-":$secFinExt->TipoEntidadOriginadoraCartera;

					$secFinExt->TipoGarantia[0] = empty($secFinExt->TipoGarantia)?"_":$secFinExt->TipoGarantia;
					
					$secFinExt->FechaTerminacion[0] = empty($secFinExt->FechaTerminacion)?"31-12-9999":$secFinExt->FechaTerminacion;
					
					$secFinExt->CubrimientoGarantia[0] = empty($secFinExt->CubrimientoGarantia)?"0":$secFinExt->CubrimientoGarantia;

					$secFinExt->Periodicidad[0] = empty($secFinExt->Periodicidad)?"-_-":$secFinExt->Periodicidad;
					
					$secFinExt->Calificacion[0] = empty($secFinExt->Calificacion)?"-_-":$secFinExt->Calificacion;

					$secFinExt->TipoMoneda[0] = empty($secFinExt->TipoMoneda)?"-_-":$secFinExt->TipoMoneda;
					
					$secFinExt->ProbabilidadNoPago[0] = empty($secFinExt->ProbabilidadNoPago)?"-_-":$secFinExt->ProbabilidadNoPago;

					$secFinExt->FechaPago[0] = empty($secFinExt->FechaPago)?"31-12-9999":$secFinExt->FechaPago;

					$secFinExt->FechaPermanencia[0] = empty($secFinExt->FechaPermanencia)?"31-12-9999":$secFinExt->FechaPermanencia;
					
					$secFinExt->ModoExtincion[0] = empty($secFinExt->ModoExtincion)?"-_-":$secFinExt->ModoExtincion;

					$secFinExt->MoraMaxima[0] = empty($secFinExt->MoraMaxima)?"0":$secFinExt->MoraMaxima;
					
					$secFinExt->NaturalezaReestructuracion[0] = empty($secFinExt->NaturalezaReestructuracion)?"0":$secFinExt->NaturalezaReestructuracion;

					$secFinExt->NumeroReestructuraciones[0] = empty($secFinExt->NumeroReestructuraciones)?"0":$secFinExt->NumeroReestructuraciones;

					$secFinExt->NumeroCuotasMora[0] = empty($secFinExt->NumeroCuotasMora)?"0":$secFinExt->NumeroCuotasMora;
					
					$secFinExt->Reestructurado[0] = empty($secFinExt->Reestructurado)?"0":$secFinExt->Reestructurado;

					$secFinExt->TipoPago[0] = empty($secFinExt->TipoPago)?"-_-":$secFinExt->TipoPago;

					$secFinExt->NumeroCuotasPactadas[0] = empty($secFinExt->NumeroCuotasPactadas)?"-_-":$secFinExt->NumeroCuotasPactadas;

					$secFinExt->EstadoContrato[0] = empty($secFinExt->EstadoContrato)?"-_-":$secFinExt->EstadoContrato;

					$secFinExt->FechaApertura[0] = empty($secFinExt->FechaApertura)?"31-12-9999":$secFinExt->FechaApertura;

					$secFinExt->CuotasCanceladas[0] = empty($secFinExt->CuotasCanceladas)?"0":$secFinExt->CuotasCanceladas;

					$secFinExt->EstadoTitular[0] = empty($secFinExt->EstadoTitular)?"-_-":$secFinExt->EstadoTitular;

					$secFinExt->ParticipacionDeuda[0] = empty($secFinExt->ParticipacionDeuda)?"0":$secFinExt->ParticipacionDeuda;


				//ACA VOY REEMPLAZAR LAS VARIABLES CON EL OBJETO XML

					$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$secFinExt->NumeroObligacion."',
								'".$info["SOL_ID"]."','".$secFinExt->PaqueteInformacion."',
								'".$secFinExt->IdentificadorLinea."','".$secFinExt->TipoContrato."',
								'".$secFinExt->TipoEntidad."','".$secFinExt->NombreEntidad."',
								'".$secFinExt->Ciudad."','".$secFinExt->Sucursal."',
								'".$secFinExt->EstadoContrato."','".$secFinExt->FechaApertura."',
								'".$secFinExt->FechaTerminacion."','".$secFinExt->Calidad."',
								'".$secFinExt->EstadoObligacion."','".$secFinExt->ModalidadCredito."',
								'".$secFinExt->LineaCredito."','".$secFinExt->Periodicidad."',
								'".$secFinExt->Calificacion."','".$secFinExt->ValorInicial."',
								'".$secFinExt->SaldoObligacion."','".$secFinExt->ValorMora."',
								'".$secFinExt->ValorCuota."','".$secFinExt->TipoMoneda."',
								'".$secFinExt->CuotasCanceladas."','".$secFinExt->TipoGarantia."',
								'".$secFinExt->CubrimientoGarantia."','".$secFinExt->MoraMaxima."',
								'".$secFinExt->Comportamientos."','".$secFinExt->ParticipacionDeuda."',
								'".$secFinExt->ProbabilidadNoPago."','".$secFinExt->FechaCorte."',
								'".$secFinExt->ModoExtincion."','".$secFinExt->FechaPago."',
								'".$secFinExt->FechaPermanencia."','".$secFinExt->NumeroReestructuraciones."',
								'".$secFinExt->NaturalezaReestructuracion."',
								'".$secFinExt->TipoEntidadOriginadoraCartera."',
								'".$secFinExt->EntidadOriginadoraCartera."','".$secFinExt->TipoPago."',
								'".$secFinExt->EstadoTitular."','".$secFinExt->NumeroCuotasPactadas."',
								'".$secFinExt->NumeroCuotasMora."','".$secFinExt->Reestructurado."'
								
					 	)";	
				}


				$sfex = "INSERT INTO cifin_sector_finan_extintas 
						(ID,CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
						 TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO_CONTRATO,FECHA_APERTURA,FECHA_TERMINACIÓN,
						 CALIDAD,ESTADO_OBLIGACION,MODALIDAD_CREDITO,LINEA_CREDITO,PERIODICIDAD,
						 CALIFICACION,VALOR_INICIAL,SALDO_OBLIGACION,VALOR_MORA,VALOR_CUOTA,TIPO_MONEDA,
						 CUOTAS_CANCELADAS,TIPO_GARANTIA,CUBRIMIENTO_GARANTIA,MORA_MAXIMA,COMPORTAMIENTO,
						 PATICIPACION_DEUDA,PROBABILIDAD_NOPAGO,FECHA_CORTE,MODO_EXTINCION,FECHA_PAGO,
						 FECHA_PERMANENCIA,NUM_REESTRUCTURA,NATURALEZA_REESTRUCTURA,TIPO_ENTIDAD_ORIGIN_CARTERA,
						 ENTIDAD_ORGIN_CARTERA,TIPO_PAGO,ESTADO_TITULAR,NUMERO_CUOTAS_PACTADAS,NUM_CUOTAS_MORA,
						 REESTRUCTURADO)
						VALUES".implode(",", $rows);

				
				$this->db->query($sfex);
				
			}
			
			
			if(!empty($xml->Tercero->SectorRealAlDia)){

				$rows = array();
				
				
				foreach ($xml->Tercero->SectorRealAlDia->Obligacion as $key => $secRealAldia) {
				
					$secRealAldia->EntidadOriginadoraCartera[0] = empty($secRealAldia->EntidadOriginadoraCartera)?"-_-":$secRealAldia->EntidadOriginadoraCartera;
					$secRealAldia->TipoEntidadOriginadoraCartera[0] = empty($secRealAldia->TipoEntidadOriginadoraCartera)?"-_-":$secRealAldia->TipoEntidadOriginadoraCartera;
					$secRealAldia->TipoGarantia[0] = empty($secRealAldia->TipoGarantia)?"-_-":$secRealAldia->TipoGarantia;
					$secRealAldia->FechaTerminacion[0] = empty($secRealAldia->FechaTerminacion)?"31-12-9999":$secRealAldia->FechaTerminacion;
					$secRealAldia->CubrimientoGarantia[0] = empty($secRealAldia->CubrimientoGarantia)?"-_-":$secRealAldia->CubrimientoGarantia;
					$secRealAldia->Periodicidad[0] = empty($secRealAldia->Periodicidad)?"-_-":$secRealAldia->Periodicidad;
					$secRealAldia->Calificacion[0] = empty($secRealAldia->Calificacion)?"-_-":$secRealAldia->Calificacion;
					$secRealAldia->TipoMoneda[0] = empty($secRealAldia->TipoMoneda)?"-_-":$secRealAldia->TipoMoneda;
					$secRealAldia->ProbabilidadNoPago[0] = empty($secRealAldia->ProbabilidadNoPago)?"-_-":$secRealAldia->ProbabilidadNoPago;
					$secRealAldia->FechaPago[0] = empty($secRealAldia->FechaPago)?"-_-":$secRealAldia->FechaPago;
					$secRealAldia->FechaPermanencia[0] = empty($secRealAldia->FechaPermanencia)?"31-12-9999":$secRealAldia->FechaPermanencia;
					$secRealAldia->ModoExtincion[0] = empty($secRealAldia->ModoExtincion)?"-_-":$secRealAldia->ModoExtincion;
					$secRealAldia->MoraMaxima[0] = empty($secRealAldia->MoraMaxima)?"-_-":$secRealAldia->MoraMaxima;
					$secRealAldia->NumeroCuotasMora[0] = empty($secRealAldia->NumeroCuotasMora)?"-_-":$secRealAldia->NumeroCuotasMora;
					$secRealAldia->Reestructurado[0] = empty($secRealAldia->Reestructurado)?"-_-":$secRealAldia->Reestructurado;
					$secRealAldia->TipoPago[0] = empty($secRealAldia->TipoPago)?"-_-":$secRealAldia->TipoPago;
					$secRealAldia->NumeroCuotasPactadas[0] = empty($secRealAldia->NumeroCuotasPactadas)?"0":$secRealAldia->NumeroCuotasPactadas;
					$secRealAldia->EstadoContrato[0] = empty($secRealAldia->EstadoContrato)?"-_-":$secRealAldia->EstadoContrato;
					$secRealAldia->FechaApertura[0] = empty($secRealAldia->FechaApertura)?"31-12-9999":$secRealAldia->FechaApertura;
					$secRealAldia->CuotasCanceladas[0] = empty($secRealAldia->CuotasCanceladas)?"0":$secRealAldia->CuotasCanceladas;
					$secRealAldia->EstadoTitular[0] = empty($secRealAldia->EstadoTitular)?"0":$secRealAldia->EstadoTitular;
					$secRealAldia->ParticipacionDeuda[0] = empty($secRealAldia->ParticipacionDeuda)?"0":$secRealAldia->ParticipacionDeuda;
					$secRealAldia->ChequesDevueltos[0] = empty($secRealAldia->ChequesDevueltos)?"0":$secRealAldia->ChequesDevueltos;
					

					$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$secRealAldia->NumeroObligacion."',
								'".$info["SOL_ID"]."','".$secRealAldia->PaqueteInformacion."',
								'".$secRealAldia->IdentificadorLinea."','".$secRealAldia->TipoContrato."',
								'".$secRealAldia->TipoEntidad."','".$secRealAldia->NombreEntidad."',
								'".$secRealAldia->Ciudad."','".$secRealAldia->Sucursal."',
								'".$secRealAldia->EstadoContrato."','".$secRealAldia->FechaApertura."',
								'".$secRealAldia->FechaTerminacion."','".$secRealAldia->Calidad."',
								'".$secRealAldia->EstadoObligacion."',
								'".$secRealAldia->LineaCredito."','".$secRealAldia->Periodicidad."',
								'".$secRealAldia->Calificacion."','".$secRealAldia->ValorInicial."',
								'".$secRealAldia->SaldoObligacion."','".$secRealAldia->ValorMora."',
								'".$secRealAldia->ValorCuota."','".$secRealAldia->TipoMoneda."',
								'".$secRealAldia->CuotasCanceladas."','".$secRealAldia->TipoGarantia."',
								'".$secRealAldia->CubrimientoGarantia."','".$secRealAldia->MoraMaxima."',
								'".$secRealAldia->Comportamientos."','".$secRealAldia->ParticipacionDeuda."',
								'".$secRealAldia->ProbabilidadNoPago."','".$secRealAldia->FechaCorte."',
								'".$secRealAldia->ModoExtincion."','".$secRealAldia->FechaPago."',
								'".$secRealAldia->FechaPermanencia."','".$secRealAldia->TipoEntidadOriginadoraCartera."',
								'".$secRealAldia->EntidadOriginadoraCartera."','".$secRealAldia->TipoPago."',
								'".$secRealAldia->EstadoTitular."','".$secRealAldia->NumeroCuotasPactadas."',
								'".$secRealAldia->NumeroCuotasMora."','".$secRealAldia->Reestructurado."',
								'".$secRealAldia->ChequesDevueltos."'
								
					 	)";	
				}


				$srad = "INSERT INTO cifin_sector_real_aldia 
						(ID,CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
						 TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO_CONTRATO,FECHA_APERTURA,FECHA_TERMINACIÓN,
						 CALIDAD,ESTADO_OBLIGACION,LINEA_CREDITO,PERIODICIDAD,
						 CALIFICACION,VALOR_INICIAL,SALDO_OBLIGACION,VALOR_MORA,VALOR_CUOTA,TIPO_MONEDA,
						 CUOTAS_CANCELADAS,TIPO_GARANTIA,CUBRIMIENTO_GARANTIA,MORA_MAXIMA,COMPORTAMIENTO,
						 PATICIPACION_DEUDA,PROBABILIDAD_NOPAGO,FECHA_CORTE,MODO_EXTINCION,FECHA_PAGO,
						 FECHA_PERMANENCIA,TIPO_ENTIDAD_ORIGIN_CARTERA,
						 ENTIDAD_ORGIN_CARTERA,TIPO_PAGO,ESTADO_TITULAR,NUMERO_CUOTAS_PACTADAS,NUM_CUOTAS_MORA,
						 REESTRUCTURADO, CHEQUES_DEVUELTOS)
						VALUES".implode(",", $rows);

				$this->db->query($srad);
			}
			
			
			
			if(!empty($xml->Tercero->SectorRealExtinguidas)){
			

				$rows = array();

				foreach ($xml->Tercero->SectorRealExtinguidas->Obligacion as $key => $secRealEx) {
				
					
					$secRealEx->EntidadOriginadoraCartera[0] = empty($secRealEx->EntidadOriginadoraCartera)?"-_-":$secRealEx->EntidadOriginadoraCartera;
					$secRealEx->TipoEntidadOriginadoraCartera[0] = empty($secRealEx->TipoEntidadOriginadoraCartera)?"-_-":$secRealEx->TipoEntidadOriginadoraCartera;
					$secRealEx->TipoGarantia[0] = empty($secRealEx->TipoGarantia)?"-_-":$secRealEx->TipoGarantia;
					$secRealEx->FechaTerminacion[0] = empty($secRealEx->FechaTerminacion)?"31-12-9999":$secRealEx->FechaTerminacion;
					$secRealEx->CubrimientoGarantia[0] = empty($secRealEx->CubrimientoGarantia)?"0":$secRealEx->CubrimientoGarantia;
					$secRealEx->Periodicidad[0] = empty($secRealEx->Periodicidad)?"-_-":$secRealEx->Periodicidad;
					$secRealEx->Calificacion[0] = empty($secRealEx->Calificacion)?"-_-":$secRealEx->Calificacion;
					$secRealEx->TipoMoneda[0] = empty($secRealEx->TipoMoneda)?"-_-":$secRealEx->TipoMoneda;
					$secRealEx->ProbabilidadNoPago[0] = empty($secRealEx->ProbabilidadNoPago)?"-_-":$secRealEx->ProbabilidadNoPago;					
					$secRealEx->FechaPago[0] = empty($secRealEx->FechaPago)?"31-12-9999":$secRealEx->FechaPago;
					$secRealEx->FechaPermanencia[0] = empty($secRealEx->FechaPermanencia)?"31-12-9999":$secRealEx->FechaPermanencia;
					$secRealEx->ModoExtincion[0] = empty($secRealEx->ModoExtincion)?"-_-":$secRealEx->ModoExtincion;
					$secRealEx->MoraMaxima[0] = empty($secRealEx->MoraMaxima)?"0":$secRealEx->MoraMaxima;
					$secRealEx->NumeroCuotasMora[0] = empty($secRealEx->NumeroCuotasMora)?"0":$secRealEx->NumeroCuotasMora;
					$secRealEx->Reestructurado[0] = empty($secRealEx->Reestructurado)?"0":$secRealEx->Reestructurado;	
					$secRealEx->TipoPago[0] = empty($secRealEx->TipoPago)?"-_-":$secRealEx->TipoPago;				
					$secRealEx->NumeroCuotasPactadas[0] = empty($secRealEx->NumeroCuotasPactadas)?"-_-":$secRealEx->NumeroCuotasPactadas;		
					$secRealEx->EstadoContrato[0] = empty($secRealEx->EstadoContrato)?"-_-":$secRealEx->EstadoContrato;			
					$secRealEx->FechaApertura[0] = empty($secRealEx->FechaApertura)?"31-12-9999":$secRealEx->FechaApertura;			
					$secRealEx->CuotasCanceladas[0] = empty($secRealEx->CuotasCanceladas)?"0":$secRealEx->CuotasCanceladas;
					$secRealEx->EstadoTitular[0] = empty($secRealEx->EstadoTitular)?"0":$secRealEx->EstadoTitular;
					$secRealEx->ParticipacionDeuda[0] = empty($secRealEx->ParticipacionDeuda)?"0":$secRealEx->ParticipacionDeuda;
					$secRealEx->ChequesDevueltos[0] = empty($secRealEx->ChequesDevueltos)?"0":$secRealEx->ChequesDevueltos;

					$rows[] = "('".$xml->Tercero->NumeroInforme."','".$date."','".$secRealEx->NumeroObligacion."',
								'".$info["SOL_ID"]."','".$secRealEx->PaqueteInformacion."',
								'".$secRealEx->IdentificadorLinea."','".$secRealEx->TipoContrato."',
								'".$secRealEx->TipoEntidad."','".$secRealEx->NombreEntidad."',
								'".$secRealEx->Ciudad."','".$secRealEx->Sucursal."',
								'".$secRealEx->EstadoContrato."','".$secRealEx->FechaApertura."',
								'".$secRealEx->FechaTerminacion."','".$secRealEx->Calidad."',
								'".$secRealEx->EstadoObligacion."',
								'".$secRealEx->LineaCredito."','".$secRealEx->Periodicidad."',
								'".$secRealEx->Calificacion."','".$secRealEx->ValorInicial."',
								'".$secRealEx->SaldoObligacion."','".$secRealEx->ValorMora."',
								'".$secRealEx->ValorCuota."','".$secRealEx->TipoMoneda."',
								'".$secRealEx->CuotasCanceladas."','".$secRealEx->TipoGarantia."',
								'".$secRealEx->CubrimientoGarantia."','".$secRealEx->MoraMaxima."',
								'".$secRealEx->Comportamientos."','".$secRealEx->ParticipacionDeuda."',
								'".$secRealEx->ProbabilidadNoPago."','".$secRealEx->FechaCorte."',
								'".$secRealEx->ModoExtincion."','".$secRealEx->FechaPago."',
								'".$secRealEx->FechaPermanencia."','".$secRealEx->TipoEntidadOriginadoraCartera."',
								'".$secRealEx->EntidadOriginadoraCartera."','".$secRealEx->TipoPago."',
								'".$secRealEx->EstadoTitular."','".$secRealEx->NumeroCuotasPactadas."',
								'".$secRealEx->NumeroCuotasMora."','".$secRealEx->Reestructurado."',
								'".$secRealEx->ChequesDevueltos."'
								
					 	)";	
				}


				$srex = "INSERT INTO cifin_sector_real_extintas 
						(ID,CREATE_DATE,NUM_OBLIGACION,SOL_ID,PAQUETE_INFO,IDENTIFICADOR_LINEA,TIPO_CONTRATO,
						 TIPO_ENTIDAD,NOMBRE_ENTIDAD,CIUDAD,SUCURSAL,ESTADO_CONTRATO,FECHA_APERTURA,FECHA_TERMINACIÓN,
						 CALIDAD,ESTADO_OBLIGACION,LINEA_CREDITO,PERIODICIDAD,
						 CALIFICACION,VALOR_INICIAL,SALDO_OBLIGACION,VALOR_MORA,VALOR_CUOTA,TIPO_MONEDA,
						 CUOTAS_CANCELADAS,TIPO_GARANTIA,CUBRIMIENTO_GARANTIA,MORA_MAXIMA,COMPORTAMIENTO,
						 PATICIPACION_DEUDA,PROBABILIDAD_NOPAGO,FECHA_CORTE,MODO_EXTINCION,FECHA_PAGO,
						 FECHA_PERMANENCIA,TIPO_ENTIDAD_ORIGIN_CARTERA,
						 ENTIDAD_ORGIN_CARTERA,TIPO_PAGO,ESTADO_TITULAR,NUMERO_CUOTAS_PACTADAS,NUM_CUOTAS_MORA,
						 REESTRUCTURADO, CHEQUES_DEVUELTOS)
						VALUES".implode(",", $rows);
				
				
				$this->db->query($srex);
				
				
			}
			

		}

		
		
		
		$xml->Tercero->LugarExpedicion[0] = empty($xml->Tercero->LugarExpedicion) ? '-_' : $xml->Tercero->LugarExpedicion;
		$xml->Tercero->NombreTitular[0] = empty($xml->Tercero->NombreTitular) ? '-_-' : $xml->Tercero->NombreTitular;
		$xml->Tercero->Estado[0] = empty($xml->Tercero->Estado) ? '-_-' : $xml->Tercero->Estado;
		$xml->Tercero->RangoEdad[0] = empty($xml->Tercero->RangoEdad) ? '-_-' : $xml->Tercero->RangoEdad;
		$xml->Tercero->FechaExpedicion[0] = empty($xml->Tercero->FechaExpedicion) ? '31-12-9999' : $xml->Tercero->FechaExpedicion;
		
			
		$archivo  = (array) $xml->attributes();

		$str = "INSERT INTO cifin_tercero
				(ID,CREATE_DATE,SOL_ID,
				 ARCHIVO,TIPO_IDENTIFICACION,NUMRO_IDENTIFIACION,NOMBRE_TITULAR, LUGAR_EXPEDICION,FECHA_EXPEDICION,
				 ESTADO,RANGO_EDAD,CODIGO_SCORE, INDICADO_DEFAULT, INDICADOR_SCORE, OBSERVACION, PUNTAJE, SUB_POBLACION,
				 TASA_MOROSIDAD, TIPO_SCORE)
				 VALUES
				 ('".$xml->Tercero->NumeroInforme."','".$date."','".$info["SOL_ID"]."','".$archivo["@attributes"]["archivo"]."',
				  '".$xml->Tercero->TipoIdentificacion."','".$xml->Tercero->NumeroIdentificacion."',
				  '".$xml->Tercero->NombreTitular."','".$xml->Tercero->LugarExpedicion."',
				  '".$this->fixDate($xml->Tercero->FechaExpedicion)."','".$xml->Tercero->Estado."',
				  '".$xml->Tercero->RangoEdad."', '".$xml->Tercero->Score->CodigoScore."', '".$xml->Tercero->Score->IndicadorDefault."', 
				  '".$xml->Tercero->Score->IndicadorScore."', '".$xml->Tercero->Score->Observacion."', '".$xml->Tercero->Score->Puntaje."', 
				  '".$xml->Tercero->Score->SubPoblacion."', '".$xml->Tercero->Score->TasaMorosidad."', '".$xml->Tercero->Score->TipoScore."' 
				 )";
		
		$this->db->query($str);
		

			
		
		return $xml;
	}

	function fixDate($date){

		$dateArray = explode("/", $date);

		if(count($dateArray)>2){
			
			return $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];

		}else{ 

			return "31-12-9999";
		}
		

	}

}