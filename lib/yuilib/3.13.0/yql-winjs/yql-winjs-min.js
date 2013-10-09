/*
YUI 3.13.0 (build 508226d)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("yql-winjs",function(e,t){e.YQLRequest.prototype._send=function(e,t){var n=new XMLHttpRequest,r;n.open("GET",e,!0),n.onreadystatechange=function(){n.readyState===4&&(clearTimeout(r),t.on.success(JSON.parse(n.responseText)))},n.send(),r=setTimeout(function(){n.abort(),t.on.timeout("script timeout")},t.timeout||3e4)}},"3.13.0");
