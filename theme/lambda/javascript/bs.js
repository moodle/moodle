require(['core/first'], function() {
    require(['theme_lambda/bootstrap', 'core/log'], function(bootstrap, log) {
        jQuery('[data-toggle="tooltip"]').tooltip();
		jQuery('[data-toggle="popover"]').popover();
    });
});