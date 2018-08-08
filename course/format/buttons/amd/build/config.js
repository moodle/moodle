define(['jquery'], function ($) {
    window.requirejs.config({
        paths: {
            "slick": M.cfg.wwwroot + '/course/format/buttons/slick/slick.min'
        },
        shim: {
            'slick': {exports: 'Slick'}
        }
    });
});
