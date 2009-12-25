/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datatype-number-parse",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Number"),{parse:function(D){var C=(D===null)?D:+D;if(A.isNumber(C)){return C;}else{return null;}}});B.namespace("Parsers").number=B.DataType.Number.parse;},"3.0.0");YUI.add("datatype-number-format",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Number"),{format:function(I,E){if(A.isNumber(I)){E=E||{};var D=(I<0),F=I+"",M=E.decimalPlaces,C=E.decimalSeparator||".",L=E.thousandsSeparator,K,G,J,H;if(A.isNumber(M)&&(M>=0)&&(M<=20)){F=I.toFixed(M);}if(C!=="."){F=F.replace(".",C);}if(L){K=F.lastIndexOf(C);K=(K>-1)?K:F.length;G=F.substring(K);for(J=0,H=K;H>0;H--){if((J%3===0)&&(H!==K)&&(!D||(H>1))){G=L+G;}G=F.charAt(H-1)+G;J++;}F=G;}F=(E.prefix)?E.prefix+F:F;F=(E.suffix)?F+E.suffix:F;return F;}else{return(A.isValue(I)&&I.toString)?I.toString():"";}}});},"3.0.0");YUI.add("datatype-number",function(A){},"3.0.0",{use:["datatype-number-parse","datatype-number-format"]});