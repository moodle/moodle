/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("io-queue",function(B){var A=new B.Queue(),G,L=1;function F(){var M=A.next();G=M.id;L=0;B.io(M.uri,M.cfg,M.id);}function D(M){A.promote(M);}function I(M,O){var N={uri:M,id:B.io._id(),cfg:O};A.add(N);if(L===1){F();}return N;}function C(M){L=1;if(G===M&&A.size()>0){F();}}function K(M){A.remove(M);}function E(){L=1;if(A.size()>0){F();}}function H(){L=0;}function J(){return A.size();}I.size=J;I.start=E;I.stop=H;I.promote=D;I.remove=K;B.on("io:complete",function(M){C(M);},B.io);B.mix(B.io,{queue:I},true);},"3.2.0",{requires:["io-base","queue-promote"]});