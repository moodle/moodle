/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("yui-throttle",function(b){
/*! Based on work by Simon Willison: http://gist.github.com/292562 */
var a=function(d,c){c=(c)?c:(b.config.throttleTime||150);if(c===-1){return(function(){d.apply(null,arguments);});}var e=(new Date()).getTime();return(function(){var f=(new Date()).getTime();if(f-e>c){e=f;d.apply(null,arguments);}});};b.throttle=a;},"3.2.0",{requires:["yui-base"]});