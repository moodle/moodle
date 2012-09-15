/*
YUI 3.6.0 (build 5521)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("anim-xy",function(b){var a=Number;b.Anim.behaviors.xy={set:function(f,d,i,h,c,g,e){f._node.setXY([e(c,a(i[0]),a(h[0])-a(i[0]),g),e(c,a(i[1]),a(h[1])-a(i[1]),g)]);},get:function(c){return c._node.getXY();}};},"3.6.0",{requires:["anim-base","node-screen"]});