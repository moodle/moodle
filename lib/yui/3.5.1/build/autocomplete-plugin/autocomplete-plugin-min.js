/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("autocomplete-plugin",function(b){var a=b.Plugin;function c(d){d.inputNode=d.host;if(!d.render&&d.render!==false){d.render=true;}c.superclass.constructor.apply(this,arguments);}b.extend(c,b.AutoCompleteList,{},{NAME:"autocompleteListPlugin",NS:"ac",CSS_PREFIX:b.ClassNameManager.getClassName("aclist")});a.AutoComplete=c;a.AutoCompleteList=c;},"3.5.1",{requires:["autocomplete-list","node-pluginhost"]});