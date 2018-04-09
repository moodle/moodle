
require(['core/first'], function() {
    require(['theme_bootstrapbase/bootstrap', 'core/log'], function(bootstrap, log) {
        log.debug('Bootstrap initialised');
    });
});
