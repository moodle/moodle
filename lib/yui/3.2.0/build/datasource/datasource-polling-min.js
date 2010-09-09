/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datasource-polling",function(B){function A(){this._intervals={};}A.prototype={_intervals:null,setInterval:function(D,E){var C=B.later(D,this,this.sendRequest,[E],true);this._intervals[C.id]=C;return C.id;},clearInterval:function(D,C){D=C||D;if(this._intervals[D]){this._intervals[D].cancel();delete this._intervals[D];}},clearAllIntervals:function(){B.each(this._intervals,this.clearInterval,this);}};B.augment(B.DataSource.Local,A);},"3.2.0",{requires:["datasource-local"]});