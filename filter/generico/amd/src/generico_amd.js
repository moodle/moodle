/* jshint ignore:start */
define(['jquery', 'core/log'], function ($, log) {

    "use strict"; // jshint ;_;

    log.debug('Filter Generico initialising');

    return {

        allopts: {},

        extscripts: {},

        csslinks: Array(),

        jslinks: Array(),

        appendjspath: function (jslink, theprefix) {
            require.config({
                paths: {
                    theprefix: jslink
                }
            });
        },

        injectcss: function (csslink) {
            var link = document.createElement("link");
            link.href = csslink;
            if (csslink.toLowerCase().lastIndexOf('.html') == csslink.length - 5) {
                link.rel = 'import';
            } else {
                link.type = "text/css";
                link.rel = "stylesheet";
            }
            document.getElementsByTagName("head")[0].appendChild(link);
        },

        // load all generico stuff and stash all our variables
        loadgenerico: function (opts) {


            //pick up opts from html
            var theid = '#filter_generico_amdopts_' + opts['AUTOID'];
            var optscontrol = $(theid).get(0);
            if (optscontrol) {
                opts = JSON.parse(optscontrol.value);
                //remove the hidden form element, in case it is really part of a form
                $(theid).remove();
            }
            log.debug(opts);

            //load our css in head if required
            //only do it once per extension though
            if (opts['CSSLINK']) {
                if (this.csslinks.indexOf(opts['CSSLINK']) < 0) {
                    this.csslinks.push(opts['CSSLINK']);
                    this.injectcss(opts['CSSLINK']);
                }
            }
            //load our css in head if required
            //only do it once per extension though
            if (opts['CSSUPLOAD']) {
                if (this.csslinks.indexOf(opts['CSSUPLOAD']) < 0) {
                    this.csslinks.push(opts['CSSUPLOAD']);
                    this.injectcss(opts['CSSUPLOAD']);
                }
            }

            //load our css in head if required
            //only do it once per extension though
            if (opts['CSSCUSTOM']) {
                if (this.csslinks.indexOf(opts['CSSCUSTOM']) < 0) {
                    this.csslinks.push(opts['CSSCUSTOM']);
                    this.injectcss(opts['CSSCUSTOM']);
                }
            }

            //if we did get a template id then proceed
            //we might not get one if the html was generated, but never sent to 
            //the page. Sometimes this happens in the assignment and probably 
            //elsewhere
            if (typeof opts['TEMPLATEID'] != 'undefined') {
                //here require, then load the template scripts and js
                require(['filter_generico_d' + opts['TEMPLATEID']], function (d) {
                    d(opts);
                });
            }
        }//end of function

    }
});
/* jshint ignore:end */