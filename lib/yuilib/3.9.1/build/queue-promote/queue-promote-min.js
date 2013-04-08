/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("queue-promote",function(e,t){e.mix(e.Queue.prototype,{indexOf:function(t){return e.Array.indexOf(this._q,t)},promote:function(e){var t=this.indexOf(e);t>-1&&this._q.unshift(this._q.splice(t,1)[0])},remove:function(e){var t=this.indexOf(e);t>-1&&this._q.splice(t,1)}})},"3.9.1",{requires:["yui-base"]});
