define([], function(){

    return{
		
		uploadAjax: function(servClass, servMethod, data, form, handler){
			
			data = this.dataEncode(servClass,servMethod,data);
			data = data.replace("info=","");
			form.append("info",data);
			
			$.ajax({
				url: 'app/server/init.php',
				data: form,
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST', // For jQuery < 1.9
				success : function(response){ 
					
					try{
						
						var resp = JSON.parse(atob(response));	
						console.log(resp);
						if(resp.data == "000"){
							
							// this.chkLogin();

						}else{
							
							handler(resp);

						}

					}catch(e){

						
						console.log(response);
						console.log(e.message);
						
					}
				}
			});
			
		},
    	//Request de ajax
     	ajaxCom: function (servClass, servMethod, data, handler, asyncOpt) {
        		console.log(servClass);
        		if (typeof(async)==='undefined') async = true;
		        $.ajax({
			        // la URL para la petición
			        url : 'app/server/init.php',

			        //Se carga api.target
			        target:this.target,
			     
			        // la información a enviar
			        // (también es posible utilizar una cadena de datos)
			        data : this.dataEncode(servClass,servMethod,data),
			     	
			     	// Determina si se hace sync o async
			     	//por defecto está en true
			     	async:asyncOpt,
			        // especifica si será una petición POST o GET
			        type : 'POST',
			     
			        // el tipo de información que se espera de respuesta
			        dataType : 'html',
			     
			        // código a ejecutar si la petición es satisfactoria;
			        // la respuesta es pasada como argumento a la función
			        success : function(response){ 

			        	try{
							
							var resp = JSON.parse(atob(response));	
							
							if(resp.data == "000"){
								
								this.target("");

							}else{
								
								handler(resp);

							}

						}catch(e){

							
							console.log(response);
							console.log(e.message);
							
						}
			        	

			        },
			     
			        // código a ejecutar si la petición falla;
			        // son pasados como argumentos a la función
			        // el objeto de la petición en crudo y código de estatus de la petición
			        error : function(XMLHttpRequest, textStatus, errorThrown) {
			            console.log('status:' + XMLHttpRequest.status + ', status text: ' + XMLHttpRequest.statusText);
			        },
			     
			        // código a ejecutar sin importar si la petición falló o no
			        complete : function(xhr, status) {
			            //console.log('Petición realizada');
			        }
			    });

			},
		//Encode data to send 
		dataEncode: function(servClass, servMethod, data){

			var info = {};
			info.className = servClass;
			info.methodName = servMethod;
			info.data = data;

			return "info="+btoa(JSON.stringify(info));

		},
		//email validator
		emailValidator: function(email){

			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    		return re.test(email);
		},
		//Consume del servidor la interfaz solicitada
		target: function(target){

			
			var data = {};
			data.target = target;

			this.ajaxCom("ui", "load", data, function(response){
				
				var resp = response.data;
				var info = resp.info;
				
				$(".content-wrapper").html(info);
				history.pushState("", target, "?"+target);
				
				require([target], function(module){
			  
				  module.initialize();

				});

			},false);
		},
		
		toastMsn : function (msn){
			
			// console.log("entrada");
			$("#toastText").html(msn);
			$('#toastMsn').stop().fadeIn(400).delay(2000).fadeOut(400); //fade out after 2 seconds
			
		},
		smallModal : function(titulo, contenido, textBoton, handler){
			
			$("#smallModalTitle").html(titulo);
			$("#messageContent").html(contenido);
			$("#aceptarSmallModal").html(textBoton);
			$("#aceptarSmallModal").click( function(){
				handler();
				$("#smallModalMsg").modal("hide");
				});
			
			$("#smallModalMsg").modal("show");
			
		},
		getTarget: function(){

			var a = location.href; 
			var b = a.substring(a.indexOf("?")+1);

			if(a == b){

				return "home";
			}else{

				return b;
			}
			

		},
		OptList: function(ID, DESCRIPCION){

			this.id = ID;
        	this.descripcion = DESCRIPCION;


		},
		chkLogin: function(){

			var self = this;
			this.ajaxCom("user","chkLogin",{},function(response){

				if(response.data.status){
					
					$('#login-box').hide();
					$('#menusWrapper').show();
					
					$('body').removeClass('login').addClass('nav-md');
					
					self.target(self.getTarget());

				}else{
					
					$('body').removeClass('nav-md').addClass('login');
					$('#menusWrapper').hide(); 
					$('#login-box').show();
					
					self.getTarget("no session");	
					
				}

				
			},false);
		},
		trueFalse: function(value){
			
			if(value == 1){
				
				return true;
				
			}else{
				
				return false;
				
			}
			
		},
		initFile: function(inputId){
			
			var inputObj = "";
			inputId.before(
				function() {
					if ( ! $(this).prev().hasClass('input-ghost') ) {
						var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
						element.attr("name",$(this).attr("name"));
						element.change(function(){
							element.next(element).find('input').val((element.val()).split('\\').pop());
						});
						$(this).find("button.btn-choose").click(function(){
							element.click();
						});
						$(this).find("button.btn-reset").click(function(){
							element.val(null);
							$(this).parents(".input-file").find('input').val('');
						});
						
						$(this).find('input').css("cursor","pointer");
						$(this).find('input').mousedown(function() {
							$(this).parents('.input-file').prev().click();
							return false;
						});
						inputObj = element;
						return element;
					}
				}
			);
			
			return inputObj;
		}

    }
});