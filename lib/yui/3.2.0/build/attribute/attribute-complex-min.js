/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("attribute-complex",function(B){var A=B.Object,C=".";B.Attribute.Complex=function(){};B.Attribute.Complex.prototype={_normAttrVals:function(G){var I={},H={},J,D,F,E;if(G){for(E in G){if(G.hasOwnProperty(E)){if(E.indexOf(C)!==-1){J=E.split(C);D=J.shift();F=H[D]=H[D]||[];F[F.length]={path:J,value:G[E]};}else{I[E]=G[E];}}}return{simple:I,complex:H};}else{return null;}},_getAttrInitVal:function(K,I,N){var E=I.value,M=I.valueFn,D,F,H,G,O,L,J;if(M){if(!M.call){M=this[M];}if(M){E=M.call(this);}}if(!I.readOnly&&N){D=N.simple;if(D&&D.hasOwnProperty(K)){E=D[K];}F=N.complex;if(F&&F.hasOwnProperty(K)){J=F[K];for(H=0,G=J.length;H<G;++H){O=J[H].path;L=J[H].value;A.setValue(E,O,L);}}}return E;}};B.mix(B.Attribute,B.Attribute.Complex,true,null,1);},"3.2.0",{requires:["attribute-base"]});