/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("dataschema-base",function(b){var a=b.Lang,c={apply:function(d,e){return e;},parse:function(d,e){if(e.parser){var f=(a.isFunction(e.parser))?e.parser:b.Parsers[e.parser+""];if(f){d=f.call(this,d);}else{}}return d;}};b.namespace("DataSchema").Base=c;b.namespace("Parsers");},"3.4.1",{requires:["base"]});