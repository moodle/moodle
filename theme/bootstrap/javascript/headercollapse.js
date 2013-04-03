YUI().use('node', function(Y) {
    var toggleShow = function(e) {
        // Toggle the active class on both the clicked .btn-navbar and the .nav-collapse.
        // Our CSS will set the height for these
        var togglemenu = Y.one('.nav-collapse');
        togglemenu.toggleClass('active');
        this.toggleClass('active');
    };
    Y.delegate('click', toggleShow, Y.config.doc, '.btn-navbar');
});
