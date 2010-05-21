/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("yui-later",function(A){(function(){var B=A.Lang,C=function(K,E,L,G,H){K=K||0;E=E||{};var F=L,J=A.Array(G),I,D;if(B.isString(L)){F=E[L];}if(!F){}I=function(){F.apply(E,J);};D=(H)?setInterval(I,K):setTimeout(I,K);return{id:D,interval:H,cancel:function(){if(this.interval){clearInterval(D);}else{clearTimeout(D);}}};};A.later=C;B.later=C;})();},"3.1.1",{requires:["yui-base"]});