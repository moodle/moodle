/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("node-pluginhost",function(A){A.Node.plug=function(){var B=A.Array(arguments);B.unshift(A.Node);A.Plugin.Host.plug.apply(A.Base,B);return A.Node;};A.Node.unplug=function(){var B=A.Array(arguments);B.unshift(A.Node);A.Plugin.Host.unplug.apply(A.Base,B);return A.Node;};A.mix(A.Node,A.Plugin.Host,false,null,1);A.NodeList.prototype.plug=function(){var B=arguments;A.NodeList.each(this,function(C){A.Node.prototype.plug.apply(A.one(C),B);});};A.NodeList.prototype.unplug=function(){var B=arguments;A.NodeList.each(this,function(C){A.Node.prototype.unplug.apply(A.one(C),B);});};},"3.0.0",{requires:["node-base","pluginhost"]});