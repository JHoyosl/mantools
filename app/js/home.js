 
define(['api','jquery','ko'], function(api,$,ko){
    
    return {
        

        //Se crea un modelo para control

        homeViewModel: function(){



        },
	    initialize: function(){

	    	ko.applyBindings(new this.homeViewModel(), document.getElementById("mainHome"));        	
	    	
	    },
	}

})