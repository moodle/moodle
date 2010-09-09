/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("dd-gestures",function(A){A.DD.Drag.START_EVENT="gesturemovestart";A.DD.Drag.prototype._prep=function(){this._dragThreshMet=false;var C=this.get("node"),B=A.DD.DDM;C.addClass(B.CSS_PREFIX+"-draggable");C.on(A.DD.Drag.START_EVENT,A.bind(this._handleMouseDownEvent,this),{minDistance:0,minTime:0});C.on("gesturemoveend",A.bind(this._handleMouseUp,this),{standAlone:true});C.on("dragstart",A.bind(this._fixDragStart,this));};A.DD.DDM._setupListeners=function(){var B=A.DD.DDM;this._createPG();this._active=true;A.one(A.config.doc).on("gesturemove",A.throttle(A.bind(B._move,B),B.get("throttleTime")),{standAlone:true});};},"3.2.0",{skinnable:false,requires:["dd-drag","event-synthetic","event-gestures"]});