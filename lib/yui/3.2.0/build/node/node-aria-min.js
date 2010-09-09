/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("node-aria",function(A){A.Node.re_aria=/^(?:role$|aria-)/;A.Node.prototype._addAriaAttr=function(B){this.addAttr(B,{getter:function(){return A.Node.getDOMNode(this).getAttribute(B,2);},setter:function(C){A.Node.getDOMNode(this).setAttribute(B,C);return C;}});};},"3.2.0",{requires:["node-base"]});