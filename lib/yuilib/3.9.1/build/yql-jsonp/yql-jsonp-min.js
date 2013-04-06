/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("yql-jsonp",function(e,t){e.YQLRequest.prototype._send=function(t,n){n.allowCache!==!1&&(n.allowCache=!0),this._jsonp?(this._jsonp.url=t,n.on&&n.on.success&&(this._jsonp._config.on.success=n.on.success),this._jsonp.send()):this._jsonp=e.jsonp(t,n)}},"3.9.1",{requires:["jsonp","jsonp-url"]});
