/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("scrollview-base-ie",function(a){a.mix(a.ScrollView.prototype,{_fixIESelect:function(c,b){this._cbDoc=b.get("ownerDocument");this._nativeBody=a.Node.getDOMNode(a.one("body",this._cbDoc));b.on("mousedown",function(){this._selectstart=this._nativeBody.onselectstart;this._nativeBody.onselectstart=this._iePreventSelect;this._cbDoc.once("mouseup",this._ieRestoreSelect,this);},this);},_iePreventSelect:function(){return false;},_ieRestoreSelect:function(){this._nativeBody.onselectstart=this._selectstart;}},true);},"3.4.1",{requires:["scrollview-base"]});