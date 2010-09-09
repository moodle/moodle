/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("editor-tab",function(C){var B=function(){B.superclass.constructor.apply(this,arguments);},A="host";C.extend(B,C.Base,{_onNodeChange:function(E){var D="indent";if(E.changedType==="tab"){if(!E.changedNode.test("li, li *")){E.changedEvent.halt();E.preventDefault();if(E.changedEvent.shiftKey){D="outdent";}this.get(A).execCommand(D,"");}}},initializer:function(){this.get(A).on("nodeChange",C.bind(this._onNodeChange,this));}},{NAME:"editorTab",NS:"tab",ATTRS:{host:{value:false}}});C.namespace("Plugin");C.Plugin.EditorTab=B;},"3.2.0",{skinnable:false,requires:["editor-base"]});