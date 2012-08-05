/*
YUI 3.6.0 (build 5521)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("node-event-delegate",function(a){a.Node.prototype.delegate=function(d){var c=a.Array(arguments,0,true),b=(a.Lang.isObject(d)&&!a.Lang.isArray(d))?1:2;c.splice(b,0,this._node);return a.delegate.apply(a,c);};},"3.6.0",{requires:["node-base","event-delegate"]});