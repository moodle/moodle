/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("node-style",function(a){(function(b){b.mix(b.Node.prototype,{setStyle:function(c,d){b.DOM.setStyle(this._node,c,d);return this;},setStyles:function(c){b.DOM.setStyles(this._node,c);return this;},getStyle:function(c){return b.DOM.getStyle(this._node,c);},getComputedStyle:function(c){return b.DOM.getComputedStyle(this._node,c);}});b.NodeList.importMethod(b.Node.prototype,["getStyle","getComputedStyle","setStyle","setStyles"]);})(a);},"3.5.1",{requires:["dom-style","node-base"]});