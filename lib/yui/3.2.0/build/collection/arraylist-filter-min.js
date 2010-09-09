/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("arraylist-filter",function(a){a.mix(a.ArrayList.prototype,{filter:function(c){var b=[];a.Array.each(this._items,function(e,d){e=this.item(d);if(c(e)){b.push(e);}},this);return new this.constructor(b);}});},"3.2.0",{requires:["arraylist"]});