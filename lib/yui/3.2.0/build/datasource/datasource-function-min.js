/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datasource-function",function(B){var A=B.Lang,C=function(){C.superclass.constructor.apply(this,arguments);};B.mix(C,{NAME:"dataSourceFunction",ATTRS:{source:{validator:A.isFunction}}});B.extend(C,B.DataSource.Local,{_defRequestFn:function(G){var F=this.get("source"),D;if(F){try{D=F(G.request,this,G);this.fire("data",B.mix({data:D},G));}catch(E){G.error=E;this.fire("data",G);}}else{G.error=new Error("Function data failure");this.fire("data",G);}return G.tId;}});B.DataSource.Function=C;},"3.2.0",{requires:["datasource-local"]});