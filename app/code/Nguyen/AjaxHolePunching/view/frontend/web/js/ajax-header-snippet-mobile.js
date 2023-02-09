define(['jquery','modernizr/modernizr'], function($){
	return function(config, element){
		$.get({
			url: '/nguyen-ajax-header-snippet/block',
			cache: false,
			data:{
				moduleName: config.moduleName,
				device: 'mobile'
			},
			success: function(result){
				element.innerHTML = result;
			}
		});	
	}
});