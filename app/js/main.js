// Filename: main.js

// Require.js allows us to configure shortcut alias
// There usage will become more apparent further along in the tutorial.
require.config({
	packages: [{
		name: 'moment',
		// This location is relative to baseUrl. Choose bower_components
		// or node_modules, depending on how moment was installed.
		location: 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/',
		main: 'moment'
	  }],
    baseUrl: "app/js/", 
    paths: {
    	'jquery': 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min',
    	'ui': 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min',
        'ko': 'https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min',
        'boostrapDatePicker': 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min',
        'bootstrap': 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min',
		'fastclick':'https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min',
		'nprogress':'https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min',
		'chart':'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min',
		'sparkline':'https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min',
		'flot_flot':'https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min',
		'flot_pie':'https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.pie.min',
		'flot_time':'https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min',
		'flot_stack':'https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.stack.min',
		'flot_resize':'https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js',
		'flot_orderBars':'../../theme/vendors/flot.orderbars/js/jquery.flot.orderBars',
		'flot_spline':'../../theme/vendors/flot-spline/js/jquery.flot.spline.min',
		'flot_curvedLines':'../../theme/vendors/flot.curvedlines/curvedLines',
		'date':'../../theme/vendors/DateJS/build/date',
		'daterangepicker':'https://cdnjs.cloudflare.com/ajax/libs/jquery-date-range-picker/0.18.0/jquery.daterangepicker.min',
		'theme':'../../theme/build/js/custom.min',
		'moment_es':'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/locale/es',
		'validate':'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate'
    	
    },
	 
    shim: {
    	bootstrap:['jquery'],  
        validate:['jquery'],    
        ui:['jquery'],    
    	'boostrapDatePicker': {
            deps: ['moment', 'jquery','ui']
        },
		'theme':{
			deps:['jquery','ui','moment','bootstrap','fastclick','nprogress','chart','sparkline']
		}
    },
}); 

require(['_ini_','theme'], function(App){

  // The "app" dependency is passed in as "App"
  App.initialize();
});