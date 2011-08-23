/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("arraylist-add",function(a){a.mix(a.ArrayList.prototype,{add:function(d,c){var b=this._items;if(a.Lang.isNumber(c)){b.splice(c,0,d);}else{b.push(d);}return this;},remove:function(e,d,b){b=b||this.itemsAreEqual;for(var c=this._items.length-1;c>=0;--c){if(b.call(this,e,this.item(c))){this._items.splice(c,1);if(!d){break;}}}return this;},itemsAreEqual:function(d,c){return d===c;}});},"3.4.0",{requires:["arraylist"]});