/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("editor-tab",function(c){var b=function(){b.superclass.constructor.apply(this,arguments);},a="host";c.extend(b,c.Base,{_onNodeChange:function(f){var d="indent";if(f.changedType==="tab"){if(!f.changedNode.test("li, li *")){f.changedEvent.halt();f.preventDefault();if(f.changedEvent.shiftKey){d="outdent";}this.get(a).execCommand(d,"");}}},initializer:function(){this.get(a).on("nodeChange",c.bind(this._onNodeChange,this));}},{NAME:"editorTab",NS:"tab",ATTRS:{host:{value:false}}});c.namespace("Plugin");c.Plugin.EditorTab=b;},"3.4.1",{skinnable:false,requires:["editor-base"]});