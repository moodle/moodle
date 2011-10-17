/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("node-event-delegate",function(a){a.Node.prototype.delegate=function(d){var c=a.Array(arguments,0,true),b=(a.Lang.isObject(d)&&!a.Lang.isArray(d))?1:2;c.splice(b,0,this._node);return a.delegate.apply(a,c);};},"3.4.1",{requires:["node-base","event-delegate"]});