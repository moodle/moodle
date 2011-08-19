/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("queue-promote",function(a){a.mix(a.Queue.prototype,{indexOf:function(b){return a.Array.indexOf(this._q,b);},promote:function(c){var b=this.indexOf(c);if(b>-1){this._q.unshift(this._q.splice(b,1)[0]);}},remove:function(c){var b=this.indexOf(c);if(b>-1){this._q.splice(b,1);}}});},"3.4.0",{requires:["yui-base"]});