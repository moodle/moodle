/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("attribute-complex",function(B){var A=B.Object,C=".";B.Attribute.Complex=function(){};B.Attribute.Complex.prototype={_normAttrVals:function(G){var I={},H={},J,D,F,E;if(G){for(E in G){if(G.hasOwnProperty(E)){if(E.indexOf(C)!==-1){J=E.split(C);D=J.shift();F=H[D]=H[D]||[];F[F.length]={path:J,value:G[E]};}else{I[E]=G[E];}}}return{simple:I,complex:H};}else{return null;}},_getAttrInitVal:function(K,I,M){var E=(I.valueFn)?I.valueFn.call(this):I.value,D,F,H,G,N,L,J;if(!I.readOnly&&M){D=M.simple;if(D&&D.hasOwnProperty(K)){E=D[K];}F=M.complex;if(F&&F.hasOwnProperty(K)){J=F[K];for(H=0,G=J.length;H<G;++H){N=J[H].path;L=J[H].value;A.setValue(E,N,L);}}}return E;}};B.mix(B.Attribute,B.Attribute.Complex,true,null,1);},"3.0.0",{requires:["attribute-base"]});