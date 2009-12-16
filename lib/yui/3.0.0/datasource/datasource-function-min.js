/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datasource-function",function(B){var A=B.Lang,C=function(){C.superclass.constructor.apply(this,arguments);};B.mix(C,{NAME:"dataSourceFunction",ATTRS:{source:{validator:A.isFunction}}});B.extend(C,B.DataSource.Local,{_defRequestFn:function(G){var F=this.get("source"),D;if(F){try{D=F(G.request,this,G);this.fire("data",B.mix({data:D},G));}catch(E){G.error=E;this.fire("error",G);}}else{G.error=new Error("Function data failure");this.fire("error",G);}return G.tId;}});B.DataSource.Function=C;},"3.0.0",{requires:["datasource-local"]});