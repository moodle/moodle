/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("node-style",function(A){(function(C){var B=["getStyle","getComputedStyle","setStyle","setStyles"];C.Node.importMethod(C.DOM,B);C.NodeList.importMethod(C.Node.prototype,B);})(A);A.mix(A.Node.ATTRS,{offsetHeight:{setter:function(B){A.DOM.setHeight(this._node,B);return B;},getter:function(){return this._node.offsetHeight;}},offsetWidth:{setter:function(B){A.DOM.setWidth(this._node,B);return B;},getter:function(){return this._node.offsetWidth;}}});A.mix(A.Node.prototype,{sizeTo:function(B,C){var D;if(arguments.length<2){D=A.one(B);B=D.get("offsetWidth");C=D.get("offsetHeight");}this.setAttrs({offsetWidth:B,offsetHeight:C});}});},"3.1.1",{requires:["dom-style","node-base"]});