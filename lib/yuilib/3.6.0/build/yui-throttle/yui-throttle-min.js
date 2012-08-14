/*
YUI 3.6.0 (build 5521)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("yui-throttle",function(a){
/*! Based on work by Simon Willison: http://gist.github.com/292562 */
a.throttle=function(c,b){b=(b)?b:(a.config.throttleTime||150);if(b===-1){return(function(){c.apply(null,arguments);});}var d=a.Lang.now();return(function(){var e=a.Lang.now();if(e-d>b){d=e;c.apply(null,arguments);}});};},"3.6.0",{requires:["yui-base"]});