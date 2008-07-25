/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.5.2
*/
/**
 * The YUI Uploader Control
 * @module uploader
 * @description <p>YUI Uploader provides file upload functionality that goes beyond the basic browser-based methods. 
 * Specifically, the YUI Uploader allows for:
 * <ol>
 * <li> Multiple file selection in a single "Open File" dialog.</li>
 * <li> File extension filters to facilitate the user's selection.</li>
 * <li> Progress tracking for file uploads.</li>
 * <li> A range of file metadata: filename, size, date created, date modified, and author.</li>
 * <li> A set of events dispatched on various aspects of the file upload process: file selection, upload progress, upload completion, etc.</li>
 * <li> Inclusion of additional data in the file upload POST request.</li>
 * <li> Faster file upload on broadband connections due to the modified SEND buffer size.</li>
 * <li> Same-page server response upon completion of the file upload.</li>
 * </ol>
 * </p>
 * @title Uploader
 * @namespace YAHOO.widget
 * @requires yahoo, dom, element, event
 */
/*!
 * SWFObject v1.5: Flash Player detection and embed - http://blog.deconcept.com/swfobject/
 *
 * SWFObject is (c) 2007 Geoff Stearns and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
if(typeof deconcept == "undefined") var deconcept = new Object();
if(typeof deconcept.util == "undefined") deconcept.util = new Object();
if(typeof deconcept.SWFObjectUtil == "undefined") deconcept.SWFObjectUtil = new Object();
deconcept.SWFObject = function(swf, id, w, h, ver, c, quality, xiRedirectUrl, redirectUrl, detectKey) {
	if (!document.getElementById) { return; }
	this.DETECT_KEY = detectKey ? detectKey : 'detectflash';
	this.skipDetect = deconcept.util.getRequestParameter(this.DETECT_KEY);
	this.params = new Object();
	this.variables = new Object();
	this.attributes = new Array();
	if(swf) { this.setAttribute('swf', swf); }
	if(id) { this.setAttribute('id', id); }
	if(w) { this.setAttribute('width', w); }
	if(h) { this.setAttribute('height', h); }
	if(ver) { this.setAttribute('version', new deconcept.PlayerVersion(ver.toString().split("."))); }
	this.installedVer = deconcept.SWFObjectUtil.getPlayerVersion();
	if (!window.opera && document.all && this.installedVer.major > 7) {
		// only add the onunload cleanup if the Flash Player version supports External Interface and we are in IE
		deconcept.SWFObject.doPrepUnload = true;
	}
	if(c) { this.addParam('bgcolor', c); }
	var q = quality ? quality : 'high';
	this.addParam('quality', q);
	this.setAttribute('useExpressInstall', false);
	this.setAttribute('doExpressInstall', false);
	var xir = (xiRedirectUrl) ? xiRedirectUrl : window.location;
	this.setAttribute('xiRedirectUrl', xir);
	this.setAttribute('redirectUrl', '');
	if(redirectUrl) { this.setAttribute('redirectUrl', redirectUrl); }
}
deconcept.SWFObject.prototype = {
	useExpressInstall: function(path) {
		this.xiSWFPath = !path ? "expressinstall.swf" : path;
		this.setAttribute('useExpressInstall', true);
	},
	setAttribute: function(name, value){
		this.attributes[name] = value;
	},
	getAttribute: function(name){
		return this.attributes[name];
	},
	addParam: function(name, value){
		this.params[name] = value;
	},
	getParams: function(){
		return this.params;
	},
	addVariable: function(name, value){
		this.variables[name] = value;
	},
	getVariable: function(name){
		return this.variables[name];
	},
	getVariables: function(){
		return this.variables;
	},
	getVariablePairs: function(){
		var variablePairs = new Array();
		var key;
		var variables = this.getVariables();
		for(key in variables){
			variablePairs[variablePairs.length] = key +"="+ variables[key];
		}
		return variablePairs;
	},
	getSWFHTML: function() {
		var swfNode = "";
		if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) { // netscape plugin architecture
			if (this.getAttribute("doExpressInstall")) {
				this.addVariable("MMplayerType", "PlugIn");
				this.setAttribute('swf', this.xiSWFPath);
			}
			swfNode = '<embed type="application/x-shockwave-flash" src="'+ this.getAttribute('swf') +'" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'" style="'+ this.getAttribute('style') +'"';
			swfNode += ' id="'+ this.getAttribute('id') +'" name="'+ this.getAttribute('id') +'" ';
			var params = this.getParams();
			 for(var key in params){ swfNode += [key] +'="'+ params[key] +'" '; }
			var pairs = this.getVariablePairs().join("&");
			 if (pairs.length > 0){ swfNode += 'flashvars="'+ pairs +'"'; }
			swfNode += '/>';
		} else { // PC IE
			if (this.getAttribute("doExpressInstall")) {
				this.addVariable("MMplayerType", "ActiveX");
				this.setAttribute('swf', this.xiSWFPath);
			}
			swfNode = '<object id="'+ this.getAttribute('id') +'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'" style="'+ this.getAttribute('style') +'">';
			swfNode += '<param name="movie" value="'+ this.getAttribute('swf') +'" />';
			var params = this.getParams();
			for(var key in params) {
			 swfNode += '<param name="'+ key +'" value="'+ params[key] +'" />';
			}
			var pairs = this.getVariablePairs().join("&");
			if(pairs.length > 0) {swfNode += '<param name="flashvars" value="'+ pairs +'" />';}
			swfNode += "</object>";
		}
		return swfNode;
	},
	write: function(elementId){
		if(this.getAttribute('useExpressInstall')) {
			// check to see if we need to do an express install
			var expressInstallReqVer = new deconcept.PlayerVersion([6,0,65]);
			if (this.installedVer.versionIsValid(expressInstallReqVer) && !this.installedVer.versionIsValid(this.getAttribute('version'))) {
				this.setAttribute('doExpressInstall', true);
				this.addVariable("MMredirectURL", escape(this.getAttribute('xiRedirectUrl')));
				document.title = document.title.slice(0, 47) + " - Flash Player Installation";
				this.addVariable("MMdoctitle", document.title);
			}
		}
		if(this.skipDetect || this.getAttribute('doExpressInstall') || this.installedVer.versionIsValid(this.getAttribute('version'))){
			var n = (typeof elementId == 'string') ? document.getElementById(elementId) : elementId;
			n.innerHTML = this.getSWFHTML();
			return true;
		}else{
			if(this.getAttribute('redirectUrl') != "") {
				document.location.replace(this.getAttribute('redirectUrl'));
			}
		}
		return false;
	}
}

/* ---- detection functions ---- */
deconcept.SWFObjectUtil.getPlayerVersion = function(){
	var PlayerVersion = new deconcept.PlayerVersion([0,0,0]);
	if(navigator.plugins && navigator.mimeTypes.length){
		var x = navigator.plugins["Shockwave Flash"];
		if(x && x.description) {
			PlayerVersion = new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/, "").replace(/(\s+r|\s+b[0-9]+)/, ".").split("."));
		}
	}else if (navigator.userAgent && navigator.userAgent.indexOf("Windows CE") >= 0){ // if Windows CE
		var axo = 1;
		var counter = 3;
		while(axo) {
			try {
				counter++;
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+ counter);
//				document.write("player v: "+ counter);
				PlayerVersion = new deconcept.PlayerVersion([counter,0,0]);
			} catch (e) {
				axo = null;
			}
		}
	} else { // Win IE (non mobile)
		// do minor version lookup in IE, but avoid fp6 crashing issues
		// see http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/
		try{
			var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		}catch(e){
			try {
				var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
				PlayerVersion = new deconcept.PlayerVersion([6,0,21]);
				axo.AllowScriptAccess = "always"; // error if player version < 6.0.47 (thanks to Michael Williams @ Adobe for this code)
			} catch(e) {
				if (PlayerVersion.major == 6) {
					return PlayerVersion;
				}
			}
			try {
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			} catch(e) {}
		}
		if (axo != null) {
			PlayerVersion = new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));
		}
	}
	return PlayerVersion;
}
deconcept.PlayerVersion = function(arrVersion){
	this.major = arrVersion[0] != null ? parseInt(arrVersion[0]) : 0;
	this.minor = arrVersion[1] != null ? parseInt(arrVersion[1]) : 0;
	this.rev = arrVersion[2] != null ? parseInt(arrVersion[2]) : 0;
}
deconcept.PlayerVersion.prototype.versionIsValid = function(fv){
	if(this.major < fv.major) return false;
	if(this.major > fv.major) return true;
	if(this.minor < fv.minor) return false;
	if(this.minor > fv.minor) return true;
	if(this.rev < fv.rev) return false;
	return true;
}
/* ---- get value of query string param ---- */
deconcept.util = {
	getRequestParameter: function(param) {
		var q = document.location.search || document.location.hash;
		if (param == null) { return q; }
		if(q) {
			var pairs = q.substring(1).split("&");
			for (var i=0; i < pairs.length; i++) {
				if (pairs[i].substring(0, pairs[i].indexOf("=")) == param) {
					return pairs[i].substring((pairs[i].indexOf("=")+1));
				}
			}
		}
		return "";
	}
}
/* fix for video streaming bug */
deconcept.SWFObjectUtil.cleanupSWFs = function() {
	var objects = document.getElementsByTagName("OBJECT");
	for (var i = objects.length - 1; i >= 0; i--) {
		objects[i].style.display = 'none';
		for (var x in objects[i]) {
			if (typeof objects[i][x] == 'function') {
				objects[i][x] = function(){};
			}
		}
	}
}
// fixes bug in some fp9 versions see http://blog.deconcept.com/2006/07/28/swfobject-143-released/
if (deconcept.SWFObject.doPrepUnload) {
	if (!deconcept.unloadSet) {
		deconcept.SWFObjectUtil.prepUnload = function() {
			__flash_unloadHandler = function(){};
			__flash_savedUnloadHandler = function(){};
			window.attachEvent("onunload", deconcept.SWFObjectUtil.cleanupSWFs);
		}
		window.attachEvent("onbeforeunload", deconcept.SWFObjectUtil.prepUnload);
		deconcept.unloadSet = true;
	}
}
/* add document.getElementById if needed (mobile IE < 5) */
if (!document.getElementById && document.all) { document.getElementById = function(id) { return document.all[id]; }}

/* add some aliases for ease of use/backwards compatibility */
var getQueryParamValue = deconcept.util.getRequestParameter;
var FlashObject = deconcept.SWFObject; // for legacy support
var SWFObject = deconcept.SWFObject;

/**
 * Wraps Flash embedding functionality and allows communication with SWF through
 * attributes.
 *
 * @namespace YAHOO.widget
 * @class FlashAdapter
 * @uses YAHOO.util.AttributeProvider
 */
YAHOO.widget.FlashAdapter = function(swfURL, containerID, attributes)
{
	// set up the initial events and attributes stuff
	this._queue = this._queue || [];
	this._events = this._events || {};
	this._configs = this._configs || {};
	attributes = attributes || {};
	
	//the Flash Player external interface code from Adobe doesn't play nicely
	//with the default value, yui-gen, in IE
	this._id = attributes.id = attributes.id || YAHOO.util.Dom.generateId(null, "yuigen");
	attributes.version = attributes.version || "9.0.45";
	attributes.backgroundColor = attributes.backgroundColor || "#ffffff";
	
	//we can't use the initial attributes right away
	//so save them for once the SWF finishes loading
	this._attributes = attributes;
	
	this._swfURL = swfURL;
	
	//embed the SWF file in the page
	this._embedSWF(this._swfURL, containerID, attributes.id, attributes.version, attributes.backgroundColor, attributes.expressInstall);
	
	/**
	 * Fires when the SWF is initialized and communication is possible.
	 * @event contentReady
	 */
	this.createEvent("contentReady");
};

YAHOO.extend(YAHOO.widget.FlashAdapter, YAHOO.util.AttributeProvider,
{
	/**
	 * The URL of the SWF file.
	 * @property _swfURL
	 * @type String
	 * @private
	 */
	_swfURL: null,

	/**
	 * A reference to the embedded SWF file.
	 * @property _swf
	 * @private
	 */
	_swf: null,

	/**
	 * The id of this instance.
	 * @property _id
	 * @type String
	 * @private
	 */
	_id: null,
	
	/**
	 * The initializing attributes are stored here until the SWF is ready.
	 * @property _attributes
	 * @type Object
	 * @private
	 */
	_attributes: null, //the intializing attributes

	/**
	 * Public accessor to the unique name of the FlashAdapter instance.
	 *
	 * @method toString
	 * @return {String} Unique name of the FlashAdapter instance.
	 */
	toString: function()
	{
		return "FlashAdapter " + this._id;
	},

	/**
	 * Embeds the SWF in the page and associates it with this instance.
	 *
	 * @method _embedSWF
	 * @private
	 */
	_embedSWF: function(swfURL, containerID, swfID, version, backgroundColor, expressInstall)
	{
		//standard SWFObject embed
		var swfObj = new deconcept.SWFObject(swfURL, swfID, "100%", "100%", version, backgroundColor, expressInstall);

		//make sure we can communicate with ExternalInterface
		swfObj.addParam("allowScriptAccess", "always");
		
		//again, a useful ExternalInterface trick
		swfObj.addVariable("allowedDomain", document.location.hostname);

		//tell the SWF which HTML element it is in
		swfObj.addVariable("elementID", swfID);

		// set the name of the function to call when the swf has an event
		swfObj.addVariable("eventHandler", "YAHOO.widget.FlashAdapter.eventHandler");

		var container = YAHOO.util.Dom.get(containerID);
		var result = swfObj.write(container);
		if(result)
		{
			this._swf = YAHOO.util.Dom.get(swfID);
			//if successful, let's add an owner property to the SWF reference
			//this will allow the event handler to communicate with a YAHOO.widget.FlashAdapter
			this._swf.owner = this;
		}
	},

	/**
	 * Handles or re-dispatches events received from the SWF.
	 *
	 * @method _eventHandler
	 * @private
	 */
	_eventHandler: function(event)
	{
		var type = event.type;
		switch(type)
		{
			case "swfReady":
			{
   				this._loadHandler();
				return;
			}
			case "log":
			{
				return;
			}
		}
		
		//be sure to return after your case or the event will automatically fire!
		this.fireEvent(type, event);
	},

	/**
	 * Called when the SWF has been initialized.
	 *
	 * @method _loadHandler
	 * @private
	 */
	_loadHandler: function()
	{
		this._initAttributes(this._attributes);
		this.setAttributes(this._attributes, true);
		this._attributes = null;
		
		this.fireEvent("contentReady");
	},
	
	/**
	 * Initializes the attributes.
	 *
	 * @method _initAttributes
	 * @private
	 */
	_initAttributes: function(attributes)
	{
		//should be overridden if other attributes need to be set up
		
		/**
		 * @attribute swfURL
		 * @description Absolute or relative URL to the SWF displayed by the FlashAdapter.
		 * @type String
		 */
		this.getAttributeConfig("swfURL",
		{
			method: this._getSWFURL
		});
	},
	
	/**
	 * Getter for swfURL attribute.
	 *
	 * @method _getSWFURL
	 * @private
	 */
	_getSWFURL: function()
	{
		return this._swfURL;
	}
});

/**
 * Receives event messages from SWF and passes them to the correct instance
 * of FlashAdapter.
 *
 * @method YAHOO.widget.FlashAdapter.eventHandler
 * @static
 * @private
 */
YAHOO.widget.FlashAdapter.eventHandler = function(elementID, event)
{
	var loadedSWF = YAHOO.util.Dom.get(elementID);
	if(!loadedSWF.owner)
	{
		//fix for ie: if owner doesn't exist yet, try again in a moment
		setTimeout(function() { YAHOO.widget.FlashAdapter.eventHandler( elementID, event ); }, 0);
	}
	else loadedSWF.owner._eventHandler(event);
};
/**
 * Uploader class for the YUI Uploader component.
 *
 * @namespace YAHOO.widget
 * @class Uploader
 * @uses YAHOO.widget.FlashAdapter
 * @constructor
 * @param containerId {HTMLElement} Container element for the Flash Player instance.
 */
YAHOO.widget.Uploader = function(containerId)
{
	YAHOO.widget.Uploader.superclass.constructor.call(this, YAHOO.widget.Uploader.SWFURL, containerId, null);
	
	/**
	 * Fires when the user has finished selecting files in the "Open File" dialog.
	 *
	 * @event fileSelect
	 * @param event.type {String} The event type
	 * @param event.fileList {Array} An array of objects with file information
	 * @param event.fileList[].size {Number} File size in bytes for a specific file in fileList
	 * @param event.fileList[].cDate {Date} Creation date for a specific file in fileList
	 * @param event.fileList[].mDate {Date} Modification date for a specific file in fileList
	 * @param event.fileList[].name {String} File name for a specific file in fileList
	 * @param event.fileList[].id {String} Unique file id of a specific file in fileList
	 */
	this.createEvent("fileSelect");

	/**
	 * Fires when an upload of a specific file has started.
	 *
	 * @event uploadStart
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file that's started to upload
	 */
	this.createEvent("uploadStart");

	/**
	 * Fires when new information about the upload progress for a specific file is available.
	 *
	 * @event uploadProgress
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file with which the upload progress data is associated
	 * @param bytesLoaded {Number} The number of bytes of the file uploaded so far
	 * @param bytesTotal {Number} The total size of the file
	 */
	this.createEvent("uploadProgress");
	
	/**
	 * Fires when an upload for a specific file is cancelled.
	 *
	 * @event uploadCancel
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file with which the upload has been cancelled.
	 */	
	this.createEvent("uploadCancel");

	/**
	 * Fires when an upload for a specific file is complete.
	 *
	 * @event uploadComplete
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file for which the upload has been completed.
	 */	
	this.createEvent("uploadComplete");

	/**
	 * Fires when the server sends data in response to a completed upload.
	 *
	 * @event uploadCompleteData
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file for which the upload has been completed.
	 * @param event.data {String} The raw data returned by the server in response to the upload.
	 */	
	this.createEvent("uploadCompleteData");
	
	/**
	 * Fires when an upload error occurs.
	 *
	 * @event uploadError
	 * @param event.type {String} The event type
	 * @param event.id {String} The id of the file that was being uploaded when the error has occurred.
	 * @param event.status {String} The status message associated with the error.
	 */	
	this.createEvent("uploadError");
}

/**
 * Location of the Uploader SWF
 *
 * @property Chart.SWFURL
 * @private
 * @static
 * @final
 * @default "assets/uploader.swf"
 */
YAHOO.widget.Uploader.SWFURL = "assets/uploader.swf";

YAHOO.extend(YAHOO.widget.Uploader, YAHOO.widget.FlashAdapter,
{
/**
 * Invokes the "Open File" dialog and allows the user to select the files for upload
 *
 * @param allowMultiple {Boolean} If true, allows for multiple file selection; if false, only a single file can be selected. False by default.
 * @param extensionFilterArray {Array} An array of key-value pairs for permissible file extensions. The array elements should 
 * be of the form: {description: "Images", extensions: "*.jpg; *.gif; *.png"}.
 */
	browse: function(allowMultiple,extensionFilterArray)
	{
		this._swf.browse(allowMultiple,extensionFilterArray);
	},
	
/**
 * Starts the upload of the file specified by fileID to the location specified by uploadScriptPath.
 *
 * @param fileID {String} The id of the file to start uploading.
 * @param uploadScriptPath {String} The URL of the upload location.
 * @param method {String} Either "GET" or "POST", specifying how the variables accompanying the file upload POST request should be submitted. "GET" by default.
 * @param vars {Object} The object containing variables to be sent in the same request as the file upload.
 * @param fieldName {String} The name of the variable in the POST request containing the file data. "Filedata" by default.
 */
	upload: function(fileID, uploadScriptPath, method, vars, fieldName)
	{
		this._swf.upload(fileID, uploadScriptPath, method, vars, fieldName);
	},
	
/**
 * Starts uploading all files in the queue. If this function is called, the upload queue is automatically managed.
 *
 * @param uploadScriptPath {String} The URL of the upload location.
 * @param method {String} Either "GET" or "POST", specifying how the variables accompanying the file upload POST request should be submitted. "GET" by default.
 * @param vars {Object} The object containing variables to be sent in the same request as the file upload.
 * @param fieldName {String} The name of the variable in the POST request containing the file data. "Filedata" by default.
 */
	uploadAll: function(uploadScriptPath, method, vars, fieldName)
	{
		this._swf.uploadAll(uploadScriptPath, method, vars, fieldName);
	},

/**
 * Cancels the upload of a specified file. If no file id is specified, all ongoing uploads are cancelled.
 *
 * @param fileID {String} The ID of the file whose upload should be cancelled.
 */
	cancel: function(fileID)
	{
		this._swf.cancel(fileID);
	},

/**
 * Clears the list of files queued for upload.
 *
 */
	clearFileList: function()
	{
		this._swf.clearFileList();
	},
	
/**
 * Removes the specified file from the upload queue. 
 *
 * @param fileID {String} The id of the file to remove from the upload queue. 
 */
	removeFile: function (fileID) 
	{
		this._swf.removeFile(fileID);
	}
});
YAHOO.register("uploader", YAHOO.widget.Uploader, {version: "2.5.2", build: "1076"});
