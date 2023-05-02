YUI.add('moodle-filter_oembed-powerbiloader', function (Y) {
    var ModulenameNAME = 'powerbiloader';
    var token = '';
    var MODULENAME = function () {
        MODULENAME.superclass.constructor.apply(this, arguments);
    };
    Y.extend(MODULENAME, Y.Base, {
        initializer: function (config) {
            if (document.getElementsByClassName('token').length > 0)
            {
                token = document.getElementsByClassName('token')[0].value;
                var iframes = document.getElementsByClassName('powerbi_iframe');
                for (var i = 0; i < iframes.length; i++) {
                    iframes[i].onload = postActionLoadReport;
                }
            }
        }
    }, {
        NAME: ModulenameNAME,
        ATTRS: {
            aparam: {}
        }
    });
    M.filter_oembed = M.filter_oembed || {};

    M.filter_oembed.init_powerbiloader = function (config) {
        return new MODULENAME(config);
    };

    // Post the auth token to the iFrame.
    postActionLoadReport = function () {
        var m = {action: "loadReport", accessToken: token};
        message = JSON.stringify(m);
        // Push the message.
        this.contentWindow.postMessage(message, "*");
    };
}, '@VERSION@', {
    requires: ['base']
});