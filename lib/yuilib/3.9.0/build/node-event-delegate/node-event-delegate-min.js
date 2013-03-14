/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("node-event-delegate",function(e,t){e.Node.prototype.delegate=function(t){var n=e.Array(arguments,0,!0),r=e.Lang.isObject(t)&&!e.Lang.isArray(t)?1:2;return n.splice(r,0,this._node),e.delegate.apply(e,n)}},"3.9.0",{requires:["node-base","event-delegate"]});
