
define(['api','jquery','ko'], function(api,$,ko){
    
	return {

		//Toma la url para determinar que acción tomar. Separa url, ruta y parámetros

	    initialize: function(){
	    	
			
			//EXTEND KO UPPER
	    	ko.extenders.uppercase = function(target, option) {
				target.subscribe(function(newValue) {
					if(newValue == null){
						
						newValue = "";
					}
				   target(newValue.toUpperCase());
				});
				return target;
			};
			
		
			///////////////////////////////////
			
	    	require(['index'], function(module){
				  
			  module.initialize(); 

			});
	    	
	    },
	}

});