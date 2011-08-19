/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("escape",function(c){var a={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","/":"&#x2F;","`":"&#x60;"},b={html:function(d){return(d+"").replace(/[&<>"'\/`]/g,b._htmlReplacer);},regex:function(d){return(d+"").replace(/[\-#$\^*()+\[\]{}|\\,.?\s]/g,"\\$&");},_htmlReplacer:function(d){return a[d];}};b.regexp=b.regex;c.Escape=b;},"3.4.0");