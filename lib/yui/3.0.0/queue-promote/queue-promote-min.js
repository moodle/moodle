/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("queue-promote",function(A){A.mix(A.Queue.prototype,{indexOf:function(B){return A.Array.indexOf(this._q,B);},promote:function(C){var B=this.indexOf(C);if(B>-1){this._q.unshift(this._q.splice(B,1));}},remove:function(C){var B=this.indexOf(C);if(B>-1){this._q.splice(B,1);}}});},"3.0.0",{requires:["yui-base"]});