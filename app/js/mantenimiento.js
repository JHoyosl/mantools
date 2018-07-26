define(['api','jquery','ko','moment','validate','boostrapDatePicker','bootstrap','ui','moment/locale/es',], function(api,$,ko,moment){
    
    return {
        

        //Se crea un modelo para control

        mantenimientosModel: function(){
            // --  global cotrol---//
			self.mantenimientoList = ko.observableArray();
			self.buttonEnable = ko.observable(true);
			self.mantenimientoFile = "";
			
			/* agregar mantenimiento */
			self.agregarMantId = ko.observable();
			self.agregarCaltId = ko.observable();


			
            // --  global cotrol---//

			
			//----- OPTIONS ----------------- //
			
			self.tipoMantenimiento = ko.observableArray();
			self.selectedTipoMantenimiento = ko.observable();
			
			
			// self.centrosList = ko.observableArray();
			// self.selectedCentro = ko.observable();
			
				
			//----- OPTIONS END ----------------//
            
			//---- GENERAL ---//
			
			self.descargarMantenimiento = function(mantenimiento){
				
				urlBase = window.location.protocol + "//" + window.location.host+window.location.pathname;
				
				info = {};
				info.ruta = "mantenimiento";
				
				api.ajaxCom("equipos","getUrl",info,function(response){
					
					ruta = response.data.message;
					ruta = urlBase+ruta.replace("../","app/");
					
					window.open(ruta+mantenimiento.PATH);
					
				});
				
				// function openCalibration(file)
				// {
					// console.log(window.location.pathname);
					// if(file != ""){
						// window.open("http://appmanto.co/uploads/calibration/"+file, "_blank", "fullscreen=yes");
					// }
					
				// }
					
					
				
				
				
			}
			
			self.loadAgregarCalibracion = function(equipo){
				
				self.calibracionFile = api.initFile($("#archivoCalibracion"));
				
				self.agregarCaltId(equipo.CONSECUTIVE);
				$("#guardarCalibracion").modal("show");
				
			}
			self.guardarCalibracion = function(){
				
				
				var extArray = ["pdf","PDF","gif", "jpeg", "jpg", "png", "PNG","docx"];				
				console.log();
				if(self.calibracionFile[0].files.length > 0){
					
					var fileTmp = self.calibracionFile[0].files[0];
					var info = {};
					
					var extension  = fileTmp.name.substr( (fileTmp.name.lastIndexOf('.') +1) );
					
					console.log(extension);
					if(extArray.includes(extension)){
						
						var form = new FormData();
						$.each(self.calibracionFile[0].files, function(i, file) {
							form.append(i, file);
						});
							
						info.id = self.agregarCaltId();
						info.fecha = $("#fechaCalibracion").val();
						
						
						console.log(info);
						// return;
						self.buttonEnable(false);
						api.uploadAjax("equipos","cargarCalibracion",info, form, function(response){
							
							self.buttonEnable(true);
							if(response.data.status){
								$("#agregarMantenimiento").modal( "hide");
								alert("Calibración Cargada exitosamente");
								self.selectedTipoMantenimiento(null);
								$("#fechaCalibracion").val("");
							}else{

								alert(response.data.message);
								
							}
							
						});
						
					}else{

						alert("Solo se permiten: "+extArray.join());
						
					}
					
					
					
				}
				
				
				
			}
			
			self.loadAgregarMantenimiento = function(equipo){
				
				
				self.mantenimientoFile = api.initFile($("#archivoMantenimiento"));
				
				
				self.agregarMantId(equipo.CONSECUTIVE);
				$("#agregarMantenimiento").modal("show");
				
			}
			
			self.guardarMantenimiento = function(){
				
				
				var extArray = ["pdf","PDF","gif", "jpeg", "jpg", "png", "PNG","docx"];				
				console.log();
				if(self.mantenimientoFile[0].files.length > 0){
					
					var fileTmp = self.mantenimientoFile[0].files[0];
					var info = {};
					
					var extension  = fileTmp.name.substr( (fileTmp.name.lastIndexOf('.') +1) );
					
					console.log(extension);
					if(extArray.includes(extension)){
						
						var form = new FormData();
						$.each(self.mantenimientoFile[0].files, function(i, file) {
							form.append(i, file);
						});
							
						info.id = self.agregarMantId();
						info.fecha = $("#fechaMantenimiento").val();
						info.tipo = self.selectedTipoMantenimiento().id;
						
						console.log(info);
						// return;
						self.buttonEnable(false);
						api.uploadAjax("equipos","cargarMantenimiento",info, form, function(response){
							
							self.buttonEnable(true);
							if(response.data.status){
								$("#agregarMantenimiento").modal( "hide");
								alert("Mantenimiento Cargado exitosamente");
								self.selectedTipoMantenimiento(null);
								$("#fechaMantenimiento").val("");
							}else{

								alert(response.data.message);
								
							}
							
						});
						
					}else{

						alert("Solo se permiten: "+extArray.join());
						
					}
					
					
					
				}
				
				
				
			}
			
			self.loadMaestros = function(){
				
				var opciones = [];
				
				opciones.push(new api.OptList("Correctivo","CORRECTIVO"));
				opciones.push(new api.OptList("Preventivo","PREVENTIVO"));

				
				self.tipoMantenimiento(opciones);

            }		
			
			
			
			
			
			self.editarEquipo = function(equipo){
				
				info = {};
				
				info.id = equipo.CONSECUTIVE;
				
				api.ajaxCom("equipos","setEquipoEdit",info,function(response){
					
					console.log(response);
					if(response.data.status){
							
						api.target("editarEquipo");
						
					}else{
						
						alert("Se ha presentado un error, intentelo nuevaemnte más tarde");
					}
					
					

				});
				
			}
			
			self.loadMantenimientosTabla = function(){
				
				api.ajaxCom("equipos","getMantenimientos",{},function(response){
					
					console.log(response.data.message);
					if(!response.data.status){
							
						alert("Error cargando Información");
						return;
					}
					
					var table = response.data.message.info;

					self.mantenimientoList(table);
					

				});
				
			}			
			
			self.goCrearEquipo = function(){

				api.target("crearEquipo");

			}
			//---- GENERAL END --//
			
        },
        initialize: function(){

        	
        	ko.applyBindings(new this.mantenimientosModel(), document.getElementById("mantenimientosModel"));   
			
			self.loadMantenimientosTabla();
		
			$('#fechaMantenimiento').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaCalibracion').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			
        },

    }

})