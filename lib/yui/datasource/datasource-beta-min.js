/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/

YAHOO.util.DataSource=function(oLiveData,oConfigs){if(oConfigs&&(oConfigs.constructor==Object)){for(var sConfig in oConfigs){if(sConfig){this[sConfig]=oConfigs[sConfig];}}}
if(!oLiveData){return;}
if(oLiveData.nodeType&&oLiveData.nodeType==9){this.dataType=YAHOO.util.DataSource.TYPE_XML;}
else if(YAHOO.lang.isArray(oLiveData)){this.dataType=YAHOO.util.DataSource.TYPE_JSARRAY;}
else if(YAHOO.lang.isString(oLiveData)){this.dataType=YAHOO.util.DataSource.TYPE_XHR;}
else if(YAHOO.lang.isFunction(oLiveData)){this.dataType=YAHOO.util.DataSource.TYPE_JSFUNCTION;}
else if(oLiveData.nodeName&&(oLiveData.nodeName.toLowerCase()=="table")){this.dataType=YAHOO.util.DataSource.TYPE_HTMLTABLE;}
else if(YAHOO.lang.isObject(oLiveData)){this.dataType=YAHOO.util.DataSource.TYPE_JSON;}
else{this.dataType=YAHOO.util.DataSource.TYPE_UNKNOWN;}
this.liveData=oLiveData;this._oQueue={interval:null,conn:null,requests:[]};var maxCacheEntries=this.maxCacheEntries;if(!YAHOO.lang.isNumber(maxCacheEntries)||(maxCacheEntries<0)){maxCacheEntries=0;}
if(maxCacheEntries>0&&!this._aCache){this._aCache=[];}
this._sName="DataSource instance"+YAHOO.util.DataSource._nIndex;YAHOO.util.DataSource._nIndex++;this.createEvent("cacheRequestEvent");this.createEvent("cacheResponseEvent");this.createEvent("requestEvent");this.createEvent("responseEvent");this.createEvent("responseParseEvent");this.createEvent("responseCacheEvent");this.createEvent("dataErrorEvent");this.createEvent("cacheFlushEvent");};YAHOO.augment(YAHOO.util.DataSource,YAHOO.util.EventProvider);YAHOO.util.DataSource.TYPE_UNKNOWN=-1;YAHOO.util.DataSource.TYPE_JSARRAY=0;YAHOO.util.DataSource.TYPE_JSFUNCTION=1;YAHOO.util.DataSource.TYPE_XHR=2;YAHOO.util.DataSource.TYPE_JSON=3;YAHOO.util.DataSource.TYPE_XML=4;YAHOO.util.DataSource.TYPE_TEXT=5;YAHOO.util.DataSource.TYPE_HTMLTABLE=6;YAHOO.util.DataSource.ERROR_DATAINVALID="Invalid data";YAHOO.util.DataSource.ERROR_DATANULL="Null data";YAHOO.util.DataSource._nIndex=0;YAHOO.util.DataSource._nTransactionId=0;YAHOO.util.DataSource.prototype._sName=null;YAHOO.util.DataSource.prototype._aCache=null;YAHOO.util.DataSource.prototype._oQueue=null;YAHOO.util.DataSource.prototype.maxCacheEntries=0;YAHOO.util.DataSource.prototype.liveData=null;YAHOO.util.DataSource.prototype.dataType=YAHOO.util.DataSource.TYPE_UNKNOWN;YAHOO.util.DataSource.prototype.responseType=YAHOO.util.DataSource.TYPE_UNKNOWN;YAHOO.util.DataSource.prototype.responseSchema=null;YAHOO.util.DataSource.prototype.connMgr=null;YAHOO.util.DataSource.prototype.connXhrMode="allowAll";YAHOO.util.DataSource.prototype.connMethodPost=false;YAHOO.util.DataSource.prototype.connTimeout=0;YAHOO.util.DataSource.parseString=function(oData){if(!YAHOO.lang.isValue(oData)){return null;}
var string=oData+"";if(YAHOO.lang.isString(string)){return string;}
else{return null;}};YAHOO.util.DataSource.parseNumber=function(oData){var number=oData*1;if(YAHOO.lang.isNumber(number)){return number;}
else{return null;}};YAHOO.util.DataSource.convertNumber=function(oData){return YAHOO.util.DataSource.parseNumber(oData);};YAHOO.util.DataSource.parseDate=function(oData){var date=null;if(!(oData instanceof Date)){date=new Date(oData);}
else{return oData;}
if(date instanceof Date){return date;}
else{return null;}};YAHOO.util.DataSource.convertDate=function(oData){return YAHOO.util.DataSource.parseDate(oData);};YAHOO.util.DataSource.prototype.toString=function(){return this._sName;};YAHOO.util.DataSource.prototype.getCachedResponse=function(oRequest,oCallback,oCaller){var aCache=this._aCache;var nCacheLength=(aCache)?aCache.length:0;var oResponse=null;if((this.maxCacheEntries>0)&&aCache&&(nCacheLength>0)){this.fireEvent("cacheRequestEvent",{request:oRequest,callback:oCallback,caller:oCaller});for(var i=nCacheLength-1;i>=0;i--){var oCacheElem=aCache[i];if(this.isCacheHit(oRequest,oCacheElem.request)){oResponse=oCacheElem.response;aCache.splice(i,1);this.addToCache(oRequest,oResponse);this.fireEvent("cacheResponseEvent",{request:oRequest,response:oResponse,callback:oCallback,caller:oCaller});break;}}}
return oResponse;};YAHOO.util.DataSource.prototype.isCacheHit=function(oRequest,oCachedRequest){return(oRequest===oCachedRequest);};YAHOO.util.DataSource.prototype.addToCache=function(oRequest,oResponse){var aCache=this._aCache;if(!aCache){return;}
while(aCache.length>=this.maxCacheEntries){aCache.shift();}
var oCacheElem={request:oRequest,response:oResponse};aCache.push(oCacheElem);this.fireEvent("responseCacheEvent",{request:oRequest,response:oResponse});};YAHOO.util.DataSource.prototype.flushCache=function(){if(this._aCache){this._aCache=[];this.fireEvent("cacheFlushEvent");}};YAHOO.util.DataSource.prototype.sendRequest=function(oRequest,oCallback,oCaller){var oCachedResponse=this.getCachedResponse(oRequest,oCallback,oCaller);if(oCachedResponse){oCallback.call(oCaller,oRequest,oCachedResponse);return null;}
return this.makeConnection(oRequest,oCallback,oCaller);};YAHOO.util.DataSource.prototype.makeConnection=function(oRequest,oCallback,oCaller){this.fireEvent("requestEvent",{request:oRequest,callback:oCallback,caller:oCaller});var oRawResponse=null;var tId=YAHOO.util.DataSource._nTransactionId++;switch(this.dataType){case YAHOO.util.DataSource.TYPE_JSFUNCTION:oRawResponse=this.liveData(oRequest);this.handleResponse(oRequest,oRawResponse,oCallback,oCaller,tId);break;case YAHOO.util.DataSource.TYPE_XHR:var oSelf=this;var oConnMgr=this.connMgr||YAHOO.util.Connect;var oQueue=this._oQueue;var _xhrSuccess=function(oResponse){if(oResponse&&(this.connXhrMode=="ignoreStaleResponses")&&(oResponse.tId!=oQueue.conn.tId)){return null;}
else if(!oResponse){this.fireEvent("dataErrorEvent",{request:oRequest,callback:oCallback,caller:oCaller,message:YAHOO.util.DataSource.ERROR_DATANULL});oCallback.call(oCaller,oRequest,oResponse,true);return null;}
else{this.handleResponse(oRequest,oResponse,oCallback,oCaller,tId);}};var _xhrFailure=function(oResponse){this.fireEvent("dataErrorEvent",{request:oRequest,callback:oCallback,caller:oCaller,message:YAHOO.util.DataSource.ERROR_DATAINVALID});if((this.liveData.lastIndexOf("?")!==this.liveData.length-1)&&(oRequest.indexOf("?")!==0)){}
oCallback.call(oCaller,oRequest,oResponse,true);return null;};var _xhrCallback={success:_xhrSuccess,failure:_xhrFailure,scope:this};if(YAHOO.lang.isNumber(this.connTimeout)){_xhrCallback.timeout=this.connTimeout;}
if(this.connXhrMode=="cancelStaleRequests"){if(oQueue.conn){if(oConnMgr.abort){oConnMgr.abort(oQueue.conn);oQueue.conn=null;}
else{}}}
if(oConnMgr&&oConnMgr.asyncRequest){var sLiveData=this.liveData;var isPost=this.connMethodPost;var sMethod=(isPost)?"POST":"GET";var sUri=(isPost)?sLiveData:sLiveData+oRequest;var sRequest=(isPost)?oRequest:null;if(this.connXhrMode!="queueRequests"){oQueue.conn=oConnMgr.asyncRequest(sMethod,sUri,_xhrCallback,sRequest);}
else{if(oQueue.conn){oQueue.requests.push({request:oRequest,callback:_xhrCallback});if(!oQueue.interval){oQueue.interval=setInterval(function(){if(oConnMgr.isCallInProgress(oQueue.conn)){return;}
else{if(oQueue.requests.length>0){sUri=(isPost)?sLiveData:sLiveData+oQueue.requests[0].request;sRequest=(isPost)?oQueue.requests[0].request:null;oQueue.conn=oConnMgr.asyncRequest(sMethod,sUri,oQueue.requests[0].callback,sRequest);oQueue.requests.shift();}
else{clearInterval(oQueue.interval);oQueue.interval=null;}}},50);}}
else{oQueue.conn=oConnMgr.asyncRequest(sMethod,sUri,_xhrCallback,sRequest);}}}
else{oCallback.call(oCaller,oRequest,null,true);}
break;default:oRawResponse=this.liveData;this.handleResponse(oRequest,oRawResponse,oCallback,oCaller,tId);break;}
return tId;};YAHOO.util.DataSource.prototype.handleResponse=function(oRequest,oRawResponse,oCallback,oCaller,tId){this.fireEvent("responseEvent",{request:oRequest,response:oRawResponse,callback:oCallback,caller:oCaller,tId:tId});var xhr=(this.dataType==YAHOO.util.DataSource.TYPE_XHR)?true:false;var oParsedResponse=null;var bError=false;oRawResponse=this.doBeforeParseData(oRequest,oRawResponse);switch(this.responseType){case YAHOO.util.DataSource.TYPE_JSARRAY:if(xhr&&oRawResponse.responseText){oRawResponse=oRawResponse.responseText;}
oParsedResponse=this.parseArrayData(oRequest,oRawResponse);break;case YAHOO.util.DataSource.TYPE_JSON:if(xhr&&oRawResponse.responseText){oRawResponse=oRawResponse.responseText;}
oParsedResponse=this.parseJSONData(oRequest,oRawResponse);break;case YAHOO.util.DataSource.TYPE_HTMLTABLE:if(xhr&&oRawResponse.responseText){oRawResponse=oRawResponse.responseText;}
oParsedResponse=this.parseHTMLTableData(oRequest,oRawResponse);break;case YAHOO.util.DataSource.TYPE_XML:if(xhr&&oRawResponse.responseXML){oRawResponse=oRawResponse.responseXML;}
oParsedResponse=this.parseXMLData(oRequest,oRawResponse);break;case YAHOO.util.DataSource.TYPE_TEXT:if(xhr&&oRawResponse.responseText){oRawResponse=oRawResponse.responseText;}
oParsedResponse=this.parseTextData(oRequest,oRawResponse);break;default:break;}
if(oParsedResponse){oParsedResponse.tId=tId;oParsedResponse=this.doBeforeCallback(oRequest,oRawResponse,oParsedResponse);this.fireEvent("responseParseEvent",{request:oRequest,response:oParsedResponse,callback:oCallback,caller:oCaller});this.addToCache(oRequest,oParsedResponse);}
else{this.fireEvent("dataErrorEvent",{request:oRequest,callback:oCallback,caller:oCaller,message:YAHOO.util.DataSource.ERROR_DATANULL});oParsedResponse={error:true};}
oCallback.call(oCaller,oRequest,oParsedResponse);};YAHOO.util.DataSource.prototype.doBeforeParseData=function(oRequest,oRawResponse){return oRawResponse;};YAHOO.util.DataSource.prototype.doBeforeCallback=function(oRequest,oRawResponse,oParsedResponse){return oParsedResponse;};YAHOO.util.DataSource.prototype.parseArrayData=function(oRequest,oRawResponse){if(YAHOO.lang.isArray(oRawResponse)&&YAHOO.lang.isArray(this.responseSchema.fields)){var oParsedResponse={results:[]};var fields=this.responseSchema.fields;for(var i=oRawResponse.length-1;i>-1;i--){var oResult={};for(var j=fields.length-1;j>-1;j--){var field=fields[j];var key=(YAHOO.lang.isValue(field.key))?field.key:field;var data=(YAHOO.lang.isValue(oRawResponse[i][j]))?oRawResponse[i][j]:oRawResponse[i][key];if(!field.parser&&field.converter){field.parser=field.converter;}
if(field.parser){data=field.parser.call(this,data);}
if(data===undefined){data=null;}
oResult[key]=data;}
oParsedResponse.results.unshift(oResult);}
return oParsedResponse;}
else{return null;}};YAHOO.util.DataSource.prototype.parseTextData=function(oRequest,oRawResponse){var oParsedResponse={};if(YAHOO.lang.isString(oRawResponse)&&YAHOO.lang.isArray(this.responseSchema.fields)&&YAHOO.lang.isString(this.responseSchema.recordDelim)&&YAHOO.lang.isString(this.responseSchema.fieldDelim)){oParsedResponse.results=[];var recDelim=this.responseSchema.recordDelim;var fieldDelim=this.responseSchema.fieldDelim;var fields=this.responseSchema.fields;if(oRawResponse.length>0){var newLength=oRawResponse.length-recDelim.length;if(oRawResponse.substr(newLength)==recDelim){oRawResponse=oRawResponse.substr(0,newLength);}
var recordsarray=oRawResponse.split(recDelim);for(var i=recordsarray.length-1;i>-1;i--){var oResult={};for(var j=fields.length-1;j>-1;j--){var fielddataarray=recordsarray[i].split(fieldDelim);var data=fielddataarray[j];if(data.charAt(0)=="\""){data=data.substr(1);}
if(data.charAt(data.length-1)=="\""){data=data.substr(0,data.length-1);}
var field=fields[j];var key=(YAHOO.lang.isValue(field.key))?field.key:field;if(!field.parser&&field.converter){field.parser=field.converter;}
if(field.parser){data=field.parser.call(this,data);}
if(data===undefined){data=null;}
oResult[key]=data;}
oParsedResponse.results.unshift(oResult);}}}
else{oParsedResponse.error=true;}
return oParsedResponse;};YAHOO.util.DataSource.prototype.parseXMLData=function(oRequest,oRawResponse){var bError=false;var oParsedResponse={};var xmlList=(this.responseSchema.resultNode)?oRawResponse.getElementsByTagName(this.responseSchema.resultNode):null;if(!xmlList||!YAHOO.lang.isArray(this.responseSchema.fields)){bError=true;}
else{oParsedResponse.results=[];for(var k=xmlList.length-1;k>=0;k--){var result=xmlList.item(k);var oResult={};for(var m=this.responseSchema.fields.length-1;m>=0;m--){var field=this.responseSchema.fields[m];var key=(YAHOO.lang.isValue(field.key))?field.key:field;var data=null;var xmlAttr=result.attributes.getNamedItem(key);if(xmlAttr){data=xmlAttr.value;}
else{var xmlNode=result.getElementsByTagName(key);if(xmlNode&&xmlNode.item(0)&&xmlNode.item(0).firstChild){data=xmlNode.item(0).firstChild.nodeValue;}
else{data="";}}
if(!field.parser&&field.converter){field.parser=field.converter;}
if(field.parser){data=field.parser.call(this,data);}
if(data===undefined){data=null;}
oResult[key]=data;}
oParsedResponse.results.unshift(oResult);}}
if(bError){oParsedResponse.error=true;}
else{}
return oParsedResponse;};YAHOO.util.DataSource.prototype.parseJSONData=function(oRequest,oRawResponse){var oParsedResponse={};if(oRawResponse&&YAHOO.lang.isArray(this.responseSchema.fields)){var fields=this.responseSchema.fields;var bError=false;oParsedResponse.results=[];var jsonObj,jsonList;if(YAHOO.lang.isString(oRawResponse)){var isNotMac=(navigator.userAgent.toLowerCase().indexOf('khtml')==-1);if(oRawResponse.parseJSON&&isNotMac){jsonObj=oRawResponse.parseJSON();if(!jsonObj){bError=true;}}
else if(window.JSON&&JSON.parse&&isNotMac){jsonObj=JSON.parse(oRawResponse);if(!jsonObj){bError=true;}}
else{try{while(oRawResponse.length>0&&(oRawResponse.charAt(0)!="{")&&(oRawResponse.charAt(0)!="[")){oRawResponse=oRawResponse.substring(1,oRawResponse.length);}
if(oRawResponse.length>0){var objEnd=Math.max(oRawResponse.lastIndexOf("]"),oRawResponse.lastIndexOf("}"));oRawResponse=oRawResponse.substring(0,objEnd+1);jsonObj=eval("("+oRawResponse+")");if(!jsonObj){bError=true;}}
else{jsonObj=null;bError=true;}}
catch(e){bError=true;}}}
else if(oRawResponse.constructor==Object){jsonObj=oRawResponse;}
else{bError=true;}
if(jsonObj&&jsonObj.constructor==Object){try{jsonList=eval("jsonObj."+this.responseSchema.resultsList);}
catch(e){bError=true;}}
if(bError||!jsonList){oParsedResponse.error=true;}
if(jsonList&&!YAHOO.lang.isArray(jsonList)){jsonList=[jsonList];}
else if(!jsonList){jsonList=[];}
for(var i=jsonList.length-1;i>=0;i--){var oResult={};var jsonResult=jsonList[i];for(var j=fields.length-1;j>=0;j--){var field=fields[j];var key=(YAHOO.lang.isValue(field.key))?field.key:field;var data=eval("jsonResult."+key);if(!field.parser&&field.converter){field.parser=field.converter;}
if(field.parser){data=field.parser.call(this,data);}
if(data===undefined){data=null;}
oResult[key]=data;}
oParsedResponse.results.unshift(oResult);}}
else{oParsedResponse.error=true;}
return oParsedResponse;};YAHOO.util.DataSource.prototype.parseHTMLTableData=function(oRequest,oRawResponse){var bError=false;var elTable=oRawResponse;var fields=this.responseSchema.fields;var oParsedResponse={};oParsedResponse.results=[];for(var i=0;i<elTable.tBodies.length;i++){var elTbody=elTable.tBodies[i];for(var j=elTbody.rows.length-1;j>-1;j--){var elRow=elTbody.rows[j];var oResult={};for(var k=fields.length-1;k>-1;k--){var field=fields[k];var key=(YAHOO.lang.isValue(field.key))?field.key:field;var data=elRow.cells[k].innerHTML;if(!field.parser&&field.converter){field.parser=field.converter;}
if(field.parser){data=field.parser.call(this,data);}
if(data===undefined){data=null;}
oResult[key]=data;}
oParsedResponse.results.unshift(oResult);}}
if(bError){oParsedResponse.error=true;}
else{}
return oParsedResponse;};YAHOO.register("datasource",YAHOO.util.DataSource,{version:"2.3.0",build:"442"});