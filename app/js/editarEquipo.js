define(['api','jquery','ko','moment','validate','boostrapDatePicker','bootstrap','ui','moment/locale/es',], function(api,$,ko,moment){
    
    return {
        

        //Se crea un modelo para control

        editarEquipoModel: function(){
			

			//----- arrays selects ----------------- //
			self.ubicacionList = ["ODONTOLOGÍA","CONSULTA EXTERNA","HOSPITALIZACIÓN","LABORATORIO CLÍNICO","CENTRO DE MATERIALES","URGENCIAS","AMBULANCIA","IMAGENOLOGÍA","FISIOTERAPIA","CIRUGÍA","RADIOLOGÍA","CENTRO DE SALUD","COCINA","LAVANDERIA","PARTOS","ADMINISTRACION","MANTENIMIENTO","SUBESTACION ELECTRICA","CLINICA ODONTOLOGICA","DOCENCIA Y SERVICIO","TANQUES DE ALMACENAMIENTO","CUARTO INTELIGENTE","CUARTO TECNICO"];

						
			//----- END arrays selects ----------------- //
			
			
			//----- variables ----------------- //
				
			self.id = ko.observable().extend({ uppercase: true });
			self.distintivo = ko.observable().extend({ uppercase: true });
			self.marca = ko.observable().extend({ uppercase: true });
			self.serial = ko.observable().extend({ uppercase: true });
			self.modelo = ko.observable().extend({ uppercase: true });
			
			self.fechaCompra = ko.observable();
			
			self.vidaUtil = ko.observable().extend({ uppercase: true });
			self.proveedor = ko.observable().extend({ uppercase: true });
			self.fabricante = ko.observable().extend({ uppercase: true });
			self.costo = ko.observable().extend({ uppercase: true });
			
			/*fuente alimentación*/
			self.electricidad = ko.observable(false);
			self.petroleo = ko.observable(false);
			self.gas = ko.observable(false);
			self.aire = ko.observable(false);
			self.agua = ko.observable(false);
			self.fuenteVapor = ko.observable(false);
			self.fuenteOtro = ko.observable(false);
			
			/*clasificación biomedica*/
			self.diagnostico = ko.observable(false);
			self.prevencion = ko.observable(false);
			self.rehabilitacion = ko.observable(false);
			self.analisisLab = ko.observable(false);
			self.ttoMtto = ko.observable(false);
		
			/*tecnología predominante*/
			self.electrico = ko.observable(false);
			self.electronico = ko.observable(false);
			self.mecanico = ko.observable(false);
			self.tecVapor = ko.observable(false);
			self.electromecanico = ko.observable(false);
			self.hidraulico = ko.observable(false);
			self.neumatico = ko.observable(false);
			self.tecnologiaOtro = ko.observable(false);
			
			self.clasRiesgo = ko.observable("0");
			self.estadoEquipo = ko.observable("0");
			
			self.voltajeMin = ko.observable();
			self.voltajeMax = ko.observable();
			self.corriente = ko.observable();
			self.potencia = ko.observable();
			self.temperatura = ko.observable();
			self.frecuencia = ko.observable();
			self.presion = ko.observable();
			self.velocidad = ko.observable();
			self.peso = ko.observable();
			
			/*otros manuales*/
			self.oepracion = ko.observable(false);
			self.mantenimiento = ko.observable(false);
			self.partes = ko.observable(false);
			self.despiece = ko.observable(false);
			
			/*otros planos*/
			self.planosElectronico = ko.observable(false);
			self.planosElectrico = ko.observable(false);
			self.planosMecanico = ko.observable(false);
			self.planosNeumatico = ko.observable(false);
			self.planosHidraulico = ko.observable(false);
			self.planosElectroMecanico = ko.observable(false);
			
			self.observaciones = ko.observable().extend({ uppercase: true });
			
			
			//----- variables ----------------- //
			
        	//----- OPTIONS ----------------- //
			self.movilidadList = ko.observableArray();
			self.selectedMovilidad = ko.observable();
			
			self.adquisicionList = ko.observableArray();
			self.selectedAdquisicion = ko.observable("");
			
			self.ubicacion = ko.observableArray(self.ubicacionList.sort());
			self.selectedUbicacion = ko.observable();
			
			self.tipoEquipos = ko.observableArray();
			self.selectedTipoEquipos = ko.observable(); 
			
			
			/************** FUNCIONES ***************/
			
			self.loadMaestros = function(){
				api.ajaxCom("equipos","getTipoEquipos",{},function(response){
					
					var tmpOpt = response.data.message;
					var opciones = [];
					
					for(var i = 0; i<tmpOpt.length; i++){
						
						opciones.push(tmpOpt[i]["EQUIPMENT"]);
						
					}
					self.tipoEquipos(opciones);

				},false);
				
				var opciones = [];
				
				opciones.push(new api.OptList("0","COMPRA"));
				opciones.push(new api.OptList("1","DONACIÓN"));
				opciones.push(new api.OptList("2","COMODATO"));
				
				self.adquisicionList(opciones);
				
				opciones = [];
				
				opciones.push(new api.OptList("0","MÓVIL"));
				opciones.push(new api.OptList("1","FIJO"));
				
				
				self.movilidadList(opciones);
			}
			
			
			self.loadEquipo = function(){
				
				api.ajaxCom("equipos","loadEquipo",{},function(response){

					if(!response.data.status){
						
						api.target("equipos");
					}
					
					var data = response.data.message;	
					
					console.log(data);
					
				
					self.selectedTipoEquipos(data.DESCRIPTION);
					self.selectedMovilidad(data.PLACE);
					self.selectedAdquisicion(data.ACQUISITION);
					self.selectedUbicacion(data.PLACE);
					self.selectedMovilidad(data.TYPE);
					
					
					
					self.id(data.CONSECUTIVE);
					self.distintivo(data.DISTINTIVO);
					self.marca(data.BRAND);
					self.serial(data.SERIAL);
					self.modelo(data.MODEL);
					
					$("#fechaCompra").val(data.BUY);
					$('#fechaInstalacion').val(data.FECHA_INSTALACION);
					$('#fechaGarantia').val(data.WARANTY);
					$('#fechaRecibido').val(data.FECHA_RECIBIDO);
					$('#fechaFabricacion').val(data.FECHA_FABRICACION);
					
					self.vidaUtil(data.VIDA_UTIL);
					self.proveedor(data.PROVEEDOR);
					self.fabricante(data.MODEL);
					self.costo(data.COSTO);
					
					/*fuente alimentación*/
					self.electricidad(api.trueFalse(data.ELECTRICITY));
					self.petroleo(api.trueFalse(data.OIL));
					self.gas(api.trueFalse(data.GAS));
					self.aire(api.trueFalse(data.AIR));
					self.agua(api.trueFalse(data.WATER));
					self.fuenteVapor(api.trueFalse(data.STEAM));
					self.fuenteOtro(api.trueFalse(data.OTHERSOURCE));
					
					/*clasificación biomedica*/
					self.diagnostico(api.trueFalse(data.DIAGNOSIS));
					self.prevencion(api.trueFalse(data.PREVENCION));
					self.rehabilitacion(api.trueFalse(data.REHAB));
					self.analisisLab(api.trueFalse(data.LABORATORY));
					self.ttoMtto(api.trueFalse(data.TREATMENT));
				
					/*tecnología predominante*/
					self.electrico(api.trueFalse(data.ELECTRIC));
					// self.electronico(api.trueFalse(data.OTHERSOURCE));
					self.mecanico(api.trueFalse(data.MECANIC));
					self.tecVapor(api.trueFalse(data.VAPOR2));
					self.electromecanico(api.trueFalse(data.ELECTROMECHANICAL));
					self.hidraulico(api.trueFalse(data.HYDRAULIC));
					self.neumatico(api.trueFalse(data.PNEUMATIC));
					self.tecnologiaOtro(api.trueFalse(data.OTHERSISTEMS));
					
					self.clasRiesgo(data.RISK);
					self.estadoEquipo(data.ESTADO_EQUIPO);
					
					self.voltajeMin(data.VOLT_MIN);
					self.voltajeMax(data.VOLT);
					self.corriente(data.CURR);
					self.potencia(data.POT);
					self.temperatura(data.TEMP);
					self.frecuencia(data.FREC);
					self.presion(data.PRESS);
					self.velocidad(data.SPEED);
					self.peso(data.WEIGHT);
					
					/*otros manuales*/
					self.oepracion(api.trueFalse(data.FUNCT));
					self.mantenimiento(api.trueFalse(data.MAINTENANCE));
					self.partes(api.trueFalse(data.REPLACEMENT));
					self.despiece(api.trueFalse(data.DESPIECE));
					
					/*otros planos*/
					// self.planosElectronico(api.trueFalse(data.ELECTRIC));
					// self.planosElectrico(api.trueFalse(data.OTHERSOURCE));
					// self.planosMecanico(api.trueFalse(data.OTHERSOURCE));
					self.planosNeumatico(api.trueFalse(data.PNEUMATIC));
					// self.planosHidraulico(api.trueFalse(data.OTHERSOURCE));
					// self.planosElectroMecanico(api.trueFalse(data.OTHERSOURCE));
					
					self.observaciones(data.OBSERVATION);

				},false);
				
			}
			
			
			self.guardarEquipo = function(){
				
				var info = {};
				
				info.id = self.id();
				info.distintivo = self.distintivo();
				info.marca = self.marca();
				info.serial = self.serial();
				info.movilidad = self.selectedMovilidad();
				info.adquisicion = self.selectedAdquisicion();
				info.ubicacion = self.selectedUbicacion();
				info.tipo = self.selectedTipoEquipos();
				info.modelo = self.modelo();
				
				info.fechaCompra = $("#fechaCompra").val();
				info.fechaInstalacion = $("#fechaInstalacion").val();
				info.fechaGarantia = $("#fechaGarantia").val();
				info.fechaRecibido = $("#fechaRecibido").val();
				info.fechaFabricacion = $("#fechaFabricacion").val();

				info.vidaUtil = self.vidaUtil();
			    info.proveedor = self.proveedor();
			    info.fabricante = self.fabricante();
			    info.costo = self.costo();
				
				/*fuente alimentación*/
				info.electricidad = self.electricidad();
				info.petroleo = self.petroleo();
				info.gas = self.gas();
				info.aire = self.aire();
				info.agua = self.agua();
				info.fuenteVapor = self.fuenteVapor();
				info.fuenteOtro = self.fuenteOtro();
				
				/*clasificación biomedica*/
				info.diagnostico = self.diagnostico();
				info.prevencion = self.prevencion();
				info.rehabilitacion = self.rehabilitacion();
				info.analisisLab = self.analisisLab();
				info.ttoMtto = self.ttoMtto();

				/*tecnología predominante*/
				info.electrico = self.electrico();
				info.electronico = self.electronico();
				info.mecanico = self.mecanico();
				info.tecVapor = self.tecVapor();
				info.electromecanico = self.electromecanico();
				info.hidraulico = self.hidraulico();
				info.neumatico = self.neumatico();
				info.tecnologiaOtro = self.tecnologiaOtro();

				info.estadoEquipo = self.estadoEquipo();
				info.clasRiesgo = self.clasRiesgo();
				
				info.voltajeMin = self.voltajeMin();
				info.voltajeMax = self.voltajeMax();
				info.corriente = self.corriente();
				info.potencia = self.potencia();
				info.temperatura = self.temperatura();
				info.frecuencia = self.frecuencia();
				info.presion = self.presion();
				info.velocidad = self.velocidad();
				info.peso = self.peso();
			
				/*otros manuales*/
				info.oepracion = self.oepracion();
				info.mantenimiento = self.mantenimiento();
				info.partes = self.partes();
				info.despiece = self.despiece();
				
				/*otros planos*/
				info.planosElectronico = self.planosElectronico();
				info.planosElectrico = self.planosElectrico();
				info.planosMecanico = self.planosMecanico();
				info.planosNeumatico = self.planosNeumatico();
				info.planosHidraulico = self.planosHidraulico();
				info.planosElectroMecanico = self.planosElectroMecanico();
				
				info.observaciones = self.observaciones();
				
				// console.log(info);
				
				api.ajaxCom("equipos","updateEquipment",info,function(response){
						
					console.log(response);
					
					resp = response.data;
					
					if(resp.status){
						
						alert("Registro guardado exitosamente");
						api.target("equipos");
						
					}else{
						
						alert("Se presentó un error, intentelo nuevamente más tarde");
						
					}

				});
				
				
			}
			/************** END FUNCIONES ***************/
        },
        initialize: function(){

        	
        	ko.applyBindings(new this.editarEquipoModel(), document.getElementById("editarEquipoModel"));   
			
			self.loadMaestros();
			
			// self.loadEquiposTabla();
			// self.loadMaestros();
			$('#fechaCompra').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaInstalacion').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaGarantia').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaRecibido').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaFabricacion').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			self.loadEquipo();
        },

    }

})