/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("array-invoke",function(a){a.Array.invoke=function(b,e){var d=a.Array(arguments,2,true),f=a.Lang.isFunction,c=[];a.Array.each(a.Array(b),function(h,g){if(h&&f(h[e])){c[g]=h[e].apply(h,d);}});return c;};},"3.5.1",{requires:["yui-base"]});