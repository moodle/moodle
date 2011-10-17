/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("dom-size",function(a){a.mix(a.DOM,{setWidth:function(c,b){a.DOM._setSize(c,"width",b);},setHeight:function(c,b){a.DOM._setSize(c,"height",b);},_setSize:function(c,e,d){d=(d>0)?d:0;var b=0;c.style[e]=d+"px";b=(e==="height")?c.offsetHeight:c.offsetWidth;if(b>d){d=d-(b-d);if(d<0){d=0;}c.style[e]=d+"px";}}});},"3.4.1",{requires:["dom-core"]});