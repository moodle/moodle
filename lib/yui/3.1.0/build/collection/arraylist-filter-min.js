/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.0
build: 2026
*/
YUI.add("arraylist-filter",function(A){A.mix(A.ArrayList.prototype,{filter:function(C){var B=[];A.Array.each(this._items,function(E,D){E=this.item(D);if(C(E)){B.push(E);}},this);return new this.constructor(B);}});},"3.1.0",{requires:["arraylist"]});