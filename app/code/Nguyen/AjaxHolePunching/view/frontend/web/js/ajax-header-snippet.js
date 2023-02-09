define(['jquery','modernizr/modernizr'], function($){
	return function(config, element){
		$.get({
			url: '/nguyen-ajax-header-snippet/block',
			cache: false,
			data:{
				moduleName: config.moduleName,
				device: 'desktop'
			},
			success: function(result){
				element.innerHTML = result;

				// build dropdown dialog
				var panelHeader = $('.page-header .panel.wrapper .panel.header');

				try {
					// make effect
			        if (Modernizr.mq('(max-width: 768px)')) {
		        		// do nothing
			        }else{
			        	// css hide service wrapper for desktop
			        	// so js check to show
						panelHeader.find('.service-wrapper').show();
			        }

			        panelHeader.find('.login-wrapper-fpc').show();

			        panelHeader.find('.login-wrapper-fpc .login-block').dropdownDialog({
			         	appendTo: ".page-header .panel.wrapper .panel.header .login-wrapper[data-block=login-dropdown]",
			            triggerTarget: ".page-header .panel.wrapper .panel.header .login-wrapper #login-select[data-trigger=login-trigger]",
			            timeout: 100,
			            closeOnMouseLeave: false,
			            closeOnEscape: true,
			            autoOpen: false,
			            triggerClass: "active",
			            parentClass: "active",
			            buttons: []
			        });
		        }
				catch(err) {
					console.log('catch error', err);
				}
				finally {
				  	panelHeader.find('.login-wrapper-fpc').show();
				}	
			}
		});	
	}
});