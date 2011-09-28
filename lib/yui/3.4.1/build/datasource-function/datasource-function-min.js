/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datasource-function",function(b){var a=b.Lang,c=function(){c.superclass.constructor.apply(this,arguments);};b.mix(c,{NAME:"dataSourceFunction",ATTRS:{source:{validator:a.isFunction}}});b.extend(c,b.DataSource.Local,{_defRequestFn:function(h){var f=this.get("source"),g=h.details[0];if(f){try{g.data=f(h.request,this,h);}catch(d){g.error=d;}}else{g.error=new Error("Function data failure");}this.fire("data",g);return h.tId;}});b.DataSource.Function=c;},"3.4.1",{requires:["datasource-local"]});