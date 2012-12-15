/*
YUI 3.8.0 (build 5744)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("widget-skin",function(e,t){var n="boundingBox",r="contentBox",i="skin",s=e.ClassNameManager.getClassName;e.Widget.prototype.getSkinName=function(){var e=this.get(r)||this.get(n),t=new RegExp("\\b"+s(i)+"-(\\S+)"),o;return e&&e.ancestor(function(e){return o=e.get("className").match(t),o}),o?o[1]:null}},"3.8.0",{requires:["widget-base"]});
