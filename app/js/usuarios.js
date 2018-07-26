define(['api','jquery','ko','moment','validate','boostrapDatePicker','bootstrap','ui','moment/locale/es',], function(api,$,ko,moment){
    
    return {
        

        //Se crea un modelo para control

        usuariosModel: function(){
            // --  global cotrol---//
            self.buttonEnable = ko.observable(true);
			self.userList = ko.observableArray();
			self.userAction = ko.observableArray();
			self.actions = ko.observableArray();
            // --  global cotrol---//
            //----- editar usuario var----- //
            self.formEditId = ko.observable("");
            self.formEditMail = ko.observable("");
            self.formEditNombres = ko.observable("");
            self.formEditApelldios = ko.observable("");
            self.formEditTelefono = ko.observable("");
			
			self.editarUsuario = function(user){

                $( "#editUserModal" ).modal( "show");

                self.buttonEnable(true);

                self.formEditId(user.ID);
                self.formEditMail(user.EMAIL);
                self.formEditNombres(user.NAMES);
                self.formEditApelldios(user.LASTNAME);
                self.formEditTelefono(user.PHONE);
                
                
            }
			
			self.guardarEditarUsuario = function(){

                var info = {};

                info.ID = self.formEditId();
                info.EMAIL = self.formEditMail();
                info.NAMES = self.formEditNombres();
                info.LASTNAME = self.formEditApelldios();
                info.PHONE = self.formEditTelefono();

                self.buttonEnable(false);
                api.ajaxCom("user","editUser",info,function(response){
                    
                    self.buttonEnable(true);
                    console.log(response.data.message);
                        
                });


            }
            //----- editar usuario var END----- //

            //----- crear usuario var----- //
            self.formId = ko.observable("");
            self.formEmail = ko.observable().extend({required: { message: 'Ingrese un correo válido' }});
            self.formTelefono = ko.observable("");
            self.formNombres = ko.observable("");
            self.formApelldios = ko.observable("");
            self.formPssw = ko.observable("");
			self.formRePssw = ko.observable("");
			
			self.addUserShow = function(){
				
				self.formId("");
				self.formEmail("");
				self.formTelefono("");
				self.formNombres("");
				self.formApelldios("");
				self.formPssw("");
				self.formRePssw("");
				self.self.selectedCentro("");
                $( "#addUserModal" ).modal( "show");
            };
			
			self.guardarUsuario = function(){
				
                var info = {};

                info.ID = self.formId();
                info.EMAIL = self.formEmail();
                info.PHNONE = self.formTelefono();
                info.NAMES = self.formNombres();
                info.LASTNAME = self.formApelldios();
                info.PSSW = self.formPssw();
                info.CENTER = self.selectedCentro().id;
				console.log(info);
				
				if(info.PSSW != self.formRePssw()){
						
					alert("Las contraseñas no coinciden");
					return;
				}
				api.ajaxCom("user","nuevoUsuario",info,function(response){
                    
					if(response.data.status){
						
						alert(response.data.message);
						$( "#addUserModal" ).modal( "hide");
						self.loadUserTable();
					}else{
						
						alert(response.data.message);
					}
					
                });
				
				
			}
            //----- crear usuario END----- //
			
			//------ ELIMINAR USUARIO ----------//
			self.eliminarUsuario = function(user){
				
				var contentmsn = "Si elimina el usuario: "+user.ID+" no podrá ser recuperado, ¿Desea Continuar?";
				api.smallModal("Eliminar Usuario",contentmsn,"Continuar", function(){
					
					info = {};
					info.ID = user.ID;
					api.ajaxCom("user","eliminarUsuario",info,function(response){
						
						self.loadUserTable();
						alert(response.data.message);
						
					});
					
				});
				
				
			}
			
			//------ ELIMINAR USUARIO END ----------//
			
			//----- OPTIONS ----------------- //
				
			self.centrosList = ko.observableArray();
			self.selectedCentro = ko.observable();
			
			self.loadMaestros = function(){
				
                api.ajaxCom("centros","getCentrosList",{},function(response){
                    
					var tmpOpt = response.data.info;
					var opciones = [];
					
					for(var i = 0; i<tmpOpt.length; i++){
						
						opciones.push(new api.OptList(tmpOpt[i]["ID"],tmpOpt[i]["DESCRIPCION"]));
						
					}
					self.centrosList(opciones);
                });

            }			
			//----- OPTIONS END ----------------//
            
			//---- GENERAL ---//
			
			self.loadUserTable = function(){
				
				api.ajaxCom("user","getUserList",{},function(response){
					
					if(!response.data.status){
							
						alert("Error cargando Información");
						return;
					}
					
					var table = response.data.message;
					
                    // for(var i = 0; i < table.length; i++){

                    //     self.myItems.push(table[i]);

                    // }
                    // table[i]["GESTION"] = table[i];

					self.userList(table);
					

				});
				
			}			
			
			self.cargarFirma = function(){
				
				alert("cargue de firma");
				
			}
			//---- GENERAL END --//
			
        },
        initialize: function(){

        	
        	ko.applyBindings(new this.usuariosModel(), document.getElementById("usuariosModel"));   
			
			self.loadUserTable();
			self.loadMaestros();
			$('#fechaNac').datetimepicker({format: 'YYYY-MM-DD',locale: 'ru'});
        },

    }

})