define(['api','jquery','ko','moment','validate','boostrapDatePicker','bootstrap','ui','moment/locale/es',], function(api,$,ko,moment){
    
    return {
        

        //Se crea un modelo para control

        equiposModel: function(){
            // --  global cotrol---//
			self.equiposList = ko.observableArray();
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
								$("#guardarCalibracion").modal( "hide");
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
			
			
			
			self.eliminarEquipo = function(equipo){
				
				api.smallModal("Eliminar Equipo", 
					"Seguro desea Eliminar el equipo: "+equipo.CONSECUTIVE, 
					"Eliminar", function(){
						
						info = {};
				
						info.id = equipo.CONSECUTIVE;
						
						api.ajaxCom("equipos","eliminarEquipo",info,function(response){
							
							
							if(response.data.status){
								
								self.loadEquiposTabla();
								alert("Regsitro eliminado exitosamente");
								
								
							}else{
								
								alert("Se ha presentado un error, intentelo nuevaemnte más tarde");
							}
							
							

						});
						
						
					})
					
					
				
				
				
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
			
			self.loadEquiposTabla = function(){
				
				api.ajaxCom("equipos","getEquipos",{},function(response){
					
					console.log(response.data.message);
					if(!response.data.status){
							
						alert("Error cargando Información");
						return;
					}
					
					var table = response.data.message.info;

					self.equiposList(table);
					

				});
				
			}			
			
			self.goCrearEquipo = function(){

				api.target("crearEquipo");

			}
			//---- GENERAL END --//
			
        },
        initialize: function(){

        	
        	ko.applyBindings(new this.equiposModel(), document.getElementById("equiposModel"));   
			
			self.loadEquiposTabla();
			self.loadMaestros();
			$('#fechaMantenimiento').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			$('#fechaCalibracion').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
			
        },

    }

})