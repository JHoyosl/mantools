 
define(['api','jquery','ko'], function(api,$,ko){
    
    return {
        

        //Se crea un modelo para control

        menusViewModel: function(){


            self.usaurioNombre = ko.observable();
        	self.logout = function(){
				

	        	api.ajaxCom("user","logout",{},function(response){
                console.log(response);
                
					$resp = api.chkLogin();

				},false);
	        };

            self.loadUserName = function(){

                api.ajaxCom("user","getUserSession",{},function(response){

                    console.log(response);
                    if(response.data.status){
                        
                        resp = response.data.info;
                        self.usaurioNombre(resp.names);
                        console.log(resp);
                          
                    }
                },false);

            }


            self.loadHome = function () {
                api.target("home");
                 
            };
			self.loadEquipos = function(){
				
				api.target("equipos");
				
			};
            self.loadUsuario = function () {
                api.target("usuarios");
                 
            };
			
			self.loadMantenimientos = function () {
                api.target("mantenimiento");
                 
            };
	
			
            loadUserName();
        },

        loginViewModel: function(){

        	self.user = ko.observable("admingsys");
            self.pssw = ko.observable("12345");
            
            self.login = function(){

            	var info = {};

            	info.user = self.user();
            	info.pssw = self.pssw();

            	if(info.user == ""){

            		alert("Debe ingresar un usuario");
            		return;
            	}
            	if(info.pssw == ""){

            		alert("Debe ingresar una contraseña");
            		return;
            	}
            	
	        	api.ajaxCom("user","login",info,function(response){


					if(response.data.status){
						
						self.user("");	
						self.pssw("");	
						api.chkLogin();
                        loadUserName();
					}else{
						
						alert("Usuario y/o Contraseña Incorrectos");
						
						
					}
				},false);

	        }; 

	        
        },
        initialize: function(){
			
        	ko.applyBindings(new this.menusViewModel(), document.getElementById("menusWrapper"));        	
        	ko.applyBindings(new this.loginViewModel(), document.getElementById("login-box"));        	
        	

			api.chkLogin();

			// ko.cleanNode(document.getElementById("login-box"));
        },
        


    }


});