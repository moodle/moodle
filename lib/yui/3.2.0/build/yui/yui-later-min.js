/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("yui-later",function(a){(function(){var b=a.Lang,c=function(e,k,g,j,i){e=e||0;var d=g,h,l;if(k&&b.isString(g)){d=k[g];}h=!b.isUndefined(j)?function(){d.apply(k,a.Array(j));}:function(){d.call(k);};l=(i)?setInterval(h,e):setTimeout(h,e);return{id:l,interval:i,cancel:function(){if(this.interval){clearInterval(l);}else{clearTimeout(l);}}};};a.later=c;b.later=c;})();},"3.2.0",{requires:["yui-base"]});