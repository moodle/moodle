/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("datasource-io",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NAME:"dataSourceIO",ATTRS:{io:{value:B.io,cloneDefaultValue:false}}});B.extend(A,B.DataSource.Local,{initializer:function(C){this._queue={interval:null,conn:null,requests:[]};},_queue:null,_defRequestFn:function(F){var E=this.get("source"),G=this.get("io"),D=F.request,C=B.mix(F.cfg,{on:{success:function(J,H,I){this.fire("data",B.mix({data:H},I));},failure:function(J,H,I){I.error=new Error("IO data failure");this.fire("error",B.mix({data:H},I));this.fire("data",B.mix({data:H},I));}},context:this,arguments:F});if(B.Lang.isString(D)){if(C.method&&(C.method.toUpperCase()==="POST")){C.data=C.data?C.data+D:D;}else{E+=D;}}G(E,C);return F.tId;}});B.DataSource.IO=A;},"3.1.1",{requires:["datasource-local","io"]});