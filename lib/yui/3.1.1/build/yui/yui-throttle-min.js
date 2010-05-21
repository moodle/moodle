/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("yui-throttle",function(Y){
/* Based on work by Simon Willison: http://gist.github.com/292562 */
var throttle=function(fn,ms){ms=(ms)?ms:(Y.config.throttleTime||150);if(ms===-1){return(function(){fn.apply(null,arguments);});}var last=(new Date()).getTime();return(function(){var now=(new Date()).getTime();if(now-last>ms){last=now;fn.apply(null,arguments);}});};Y.throttle=throttle;},"3.1.1",{requires:["yui-base"]});