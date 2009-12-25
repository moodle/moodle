/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datasource-polling",function(C){var A=C.Lang,B=function(){this._intervals={};};B.prototype={_intervals:null,setInterval:function(F,E,G){var D=C.later(F,this,this.sendRequest,[E,G],true);this._intervals[D.id]=D;return D.id;},clearInterval:function(E,D){E=D||E;if(this._intervals[E]){this._intervals[E].cancel();delete this._intervals[E];}},clearAllIntervals:function(){C.each(this._intervals,this.clearInterval,this);}};C.augment(C.DataSource.Local,B);},"3.0.0",{requires:["datasource-local"]});