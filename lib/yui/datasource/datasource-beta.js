/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/
/**
 * The DataSource utility provides a common configurable interface for widgets
 * to access a variety of data, from JavaScript arrays to online servers over
 * XHR.
 *
 * @namespace YAHOO.util
 * @module datasource
 * @requires yahoo, event
 * @optional connection
 * @title DataSource Utility
 * @beta
 */

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The DataSource class defines and manages a live set of data for widgets to
 * interact with. Examples of live databases include in-memory
 * local data such as a JavaScript array, a JavaScript function, or JSON, or
 * remote data such as data retrieved through an XHR connection.
 *
 * @class DataSource
 * @uses YAHOO.util.EventProvider
 * @constructor
 * @param oLiveData {Object} Pointer to live database.
 * @param oConfigs {Object} (optional) Object literal of configuration values.
 */
YAHOO.util.DataSource = function(oLiveData, oConfigs) {
    // Set any config params passed in to override defaults
    if(oConfigs && (oConfigs.constructor == Object)) {
        for(var sConfig in oConfigs) {
            if(sConfig) {
                this[sConfig] = oConfigs[sConfig];
            }
        }
    }
    
    if(!oLiveData) {
        return;
    }

    if(oLiveData.nodeType && oLiveData.nodeType == 9) {
        this.dataType = YAHOO.util.DataSource.TYPE_XML;
    }
    else if(YAHOO.lang.isArray(oLiveData)) {
        this.dataType = YAHOO.util.DataSource.TYPE_JSARRAY;
    }
    else if(YAHOO.lang.isString(oLiveData)) {
        this.dataType = YAHOO.util.DataSource.TYPE_XHR;
    }
    else if(YAHOO.lang.isFunction(oLiveData)) {
        this.dataType = YAHOO.util.DataSource.TYPE_JSFUNCTION;
    }
    else if(oLiveData.nodeName && (oLiveData.nodeName.toLowerCase() == "table")) {
        this.dataType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
    }
    else if(YAHOO.lang.isObject(oLiveData)) {
        this.dataType = YAHOO.util.DataSource.TYPE_JSON;
    }
    else {
        this.dataType = YAHOO.util.DataSource.TYPE_UNKNOWN;
    }

    this.liveData = oLiveData;
    this._oQueue = {interval:null, conn:null, requests:[]};


    // Validate and initialize public configs
    var maxCacheEntries = this.maxCacheEntries;
    if(!YAHOO.lang.isNumber(maxCacheEntries) || (maxCacheEntries < 0)) {
        maxCacheEntries = 0;
    }

    // Initialize local cache
    if(maxCacheEntries > 0 && !this._aCache) {
        this._aCache = [];
    }

    this._sName = "DataSource instance" + YAHOO.util.DataSource._nIndex;
    YAHOO.util.DataSource._nIndex++;


    /////////////////////////////////////////////////////////////////////////////
    //
    // Custom Events
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Fired when a request is made to the local cache.
     *
     * @event cacheRequestEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     */
    this.createEvent("cacheRequestEvent");

    /**
     * Fired when data is retrieved from the local cache.
     *
     * @event getCachedResponseEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.response {Object} The response object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     * @param oArgs.tId {Number} Transaction ID.
     */
    this.createEvent("cacheResponseEvent");

    /**
     * Fired when a request is sent to the live data source.
     *
     * @event requestEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     */
    this.createEvent("requestEvent");

    /**
     * Fired when live data source sends response.
     *
     * @event responseEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.response {Object} The raw response object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     */
    this.createEvent("responseEvent");

    /**
     * Fired when response is parsed.
     *
     * @event responseParseEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.response {Object} The parsed response object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     */
    this.createEvent("responseParseEvent");

    /**
     * Fired when response is cached.
     *
     * @event responseCacheEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.response {Object} The parsed response object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     */
    this.createEvent("responseCacheEvent");
    /**
     * Fired when an error is encountered with the live data source.
     *
     * @event dataErrorEvent
     * @param oArgs.request {Object} The request object.
     * @param oArgs.callback {Function} The callback function.
     * @param oArgs.caller {Object} The parent object of the callback function.
     * @param oArgs.message {String} The error message.
     */
    this.createEvent("dataErrorEvent");

    /**
     * Fired when the local cache is flushed.
     *
     * @event cacheFlushEvent
     */
    this.createEvent("cacheFlushEvent");
};

YAHOO.augment(YAHOO.util.DataSource, YAHOO.util.EventProvider);

/////////////////////////////////////////////////////////////////////////////
//
// Public constants
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Type is unknown.
 *
 * @property TYPE_UNKNOWN
 * @type Number
 * @final
 * @default -1
 */
YAHOO.util.DataSource.TYPE_UNKNOWN = -1;

/**
 * Type is a JavaScript Array.
 *
 * @property TYPE_JSARRAY
 * @type Number
 * @final
 * @default 0
 */
YAHOO.util.DataSource.TYPE_JSARRAY = 0;

/**
 * Type is a JavaScript Function.
 *
 * @property TYPE_JSFUNCTION
 * @type Number
 * @final
 * @default 1
 */
YAHOO.util.DataSource.TYPE_JSFUNCTION = 1;

/**
 * Type is hosted on a server via an XHR connection.
 *
 * @property TYPE_XHR
 * @type Number
 * @final
 * @default 2
 */
YAHOO.util.DataSource.TYPE_XHR = 2;

/**
 * Type is JSON.
 *
 * @property TYPE_JSON
 * @type Number
 * @final
 * @default 3
 */
YAHOO.util.DataSource.TYPE_JSON = 3;

/**
 * Type is XML.
 *
 * @property TYPE_XML
 * @type Number
 * @final
 * @default 4
 */
YAHOO.util.DataSource.TYPE_XML = 4;

/**
 * Type is plain text.
 *
 * @property TYPE_TEXT
 * @type Number
 * @final
 * @default 5
 */
YAHOO.util.DataSource.TYPE_TEXT = 5;

/**
 * Type is an HTML TABLE element.
 *
 * @property TYPE_HTMLTABLE
 * @type Number
 * @final
 * @default 6
 */
YAHOO.util.DataSource.TYPE_HTMLTABLE = 6;

/**
 * Error message for invalid dataresponses.
 *
 * @property ERROR_DATAINVALID
 * @type String
 * @final
 * @default "Invalid data"
 */
YAHOO.util.DataSource.ERROR_DATAINVALID = "Invalid data";

/**
 * Error message for null data responses.
 *
 * @property ERROR_DATANULL
 * @type String
 * @final
 * @default "Null data"
 */
YAHOO.util.DataSource.ERROR_DATANULL = "Null data";



/////////////////////////////////////////////////////////////////////////////
//
// Private variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Internal class variable to index multiple DataSource instances.
 *
 * @property DataSource._nIndex
 * @type Number
 * @private
 * @static
 */
YAHOO.util.DataSource._nIndex = 0;

/**
 * Internal class variable to assign unique transaction IDs.
 *
 * @property DataSource._nTransactionId
 * @type Number
 * @private
 * @static
 */
YAHOO.util.DataSource._nTransactionId = 0;

/**
 * Name of DataSource instance.
 *
 * @property _sName
 * @type String
 * @private
 */
YAHOO.util.DataSource.prototype._sName = null;

/**
 * Local cache of data result object literals indexed chronologically.
 *
 * @property _aCache
 * @type Object[]
 * @private
 */
YAHOO.util.DataSource.prototype._aCache = null;

/**
 * Local queue of request connections, enabled if queue needs to be managed.
 *
 * @property _oQueue
 * @type Object
 * @private
 */
YAHOO.util.DataSource.prototype._oQueue = null;

/////////////////////////////////////////////////////////////////////////////
//
// Private methods
//
/////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////
//
// Public member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Max size of the local cache.  Set to 0 to turn off caching.  Caching is
 * useful to reduce the number of server connections.  Recommended only for data
 * sources that return comprehensive results for queries or when stale data is
 * not an issue.
 *
 * @property maxCacheEntries
 * @type Number
 * @default 0
 */
YAHOO.util.DataSource.prototype.maxCacheEntries = 0;

 /**
 * Pointer to live database.
 *
 * @property liveData
 * @type Object
 */
YAHOO.util.DataSource.prototype.liveData = null;

/**
 * Where the live data is held.
 *
 * @property dataType
 * @type Number
 * @default YAHOO.util.DataSource.TYPE_UNKNOWN
 *
 */
YAHOO.util.DataSource.prototype.dataType = YAHOO.util.DataSource.TYPE_UNKNOWN;

/**
 * Format of response.
 *
 * @property responseType
 * @type Number
 * @default YAHOO.util.DataSource.TYPE_UNKNOWN
 */
YAHOO.util.DataSource.prototype.responseType = YAHOO.util.DataSource.TYPE_UNKNOWN;

/**
 * Response schema object literal takes a combination of the following properties:
 *
 * <dl>
 * <dt>resultsList</dt> <dd>Pointer to array of tabular data</dd>
 * <dt>resultNode</dt> <dd>Pointer to node name of row data (XML data only)</dd>
 * <dt>recordDelim</dt> <dd>Record delimiter (text data only)</dd>
 * <dt>fieldDelim</dt> <dd>Field delimiter (text data only)</dd>
 * <dt>fields</dt> <dd>Array of field names (aka keys), or array of object literals
 * such as: {key:"fieldname",parser:YAHOO.util.DataSource.parseDate}</dd>
 * </dl>
 *
 * @property responseSchema
 * @type Object
 */
YAHOO.util.DataSource.prototype.responseSchema = null;

 /**
 * Alias to YUI Connection Manager. Allows implementers to specify their own
 * subclasses of the YUI Connection Manager utility.
 *
 * @property connMgr
 * @type Object
 * @default YAHOO.util.Connect
 */
YAHOO.util.DataSource.prototype.connMgr = null;

 /**
 * If data is accessed over XHR via Connection Manager, this setting defines
 * request/response management in the following manner:
 * <dl>
 *     <dt>queueRequests</dt>
 *     <dd>If a request is already in progress, wait until response is returned
 *     before sending the next request.</dd>
 *
 *     <dt>cancelStaleRequests</dt>
 *     <dd>If a request is already in progress, cancel it before sending the next
 *     request.</dd>
 *
 *     <dt>ignoreStaleResponses</dt>
 *     <dd>Send all requests, but handle only the response for the most recently
 *     sent request.</dd>
 *
 *     <dt>allowAll</dt>
 *     <dd>Send all requests and handle all responses.</dd>
 *
 * </dl>
 *
 * @property connXhrMode
 * @type String
 * @default "allowAll"
 */
YAHOO.util.DataSource.prototype.connXhrMode = "allowAll";

 /**
 * If data is accessed over XHR via Connection Manager, true if data should be
 * sent via POST, otherwise data will be sent via GET.
 *
 * @property connMethodPost
 * @type Boolean
 * @default false
 */
YAHOO.util.DataSource.prototype.connMethodPost = false;

 /**
 * If data is accessed over XHR via Connection Manager, the connection timeout
 * defines how many  milliseconds the XHR connection will wait for a server
 * response. Any non-zero value will enable the Connection utility's
 * Auto-Abort feature.
 *
 * @property connTimeout
 * @type Number
 * @default 0
 */
YAHOO.util.DataSource.prototype.connTimeout = 0;

/////////////////////////////////////////////////////////////////////////////
//
// Public static methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Converts data to type String.
 *
 * @method DataSource.parseString
 * @param oData {String | Number | Boolean | Date | Array | Object} Data to parse.
 * The special values null and undefined will return null.
 * @return {Number} A string, or null.
 * @static
 */
YAHOO.util.DataSource.parseString = function(oData) {
    // Special case null and undefined
    if(!YAHOO.lang.isValue(oData)) {
        return null;
    }
    
    //Convert to string
    var string = oData + "";

    // Validate
    if(YAHOO.lang.isString(string)) {
        return string;
    }
    else {
        return null;
    }
};

/**
 * Converts data to type Number.
 *
 * @method DataSource.parseNumber
 * @param oData {String | Number | Boolean | Null} Data to convert. Beware, null
 * returns as 0.
 * @return {Number} A number, or null if NaN.
 * @static
 */
YAHOO.util.DataSource.parseNumber = function(oData) {
    //Convert to number
    var number = oData * 1;
    
    // Validate
    if(YAHOO.lang.isNumber(number)) {
        return number;
    }
    else {
        return null;
    }
};
// Backward compatibility
YAHOO.util.DataSource.convertNumber = function(oData) {
    return YAHOO.util.DataSource.parseNumber(oData);
};

/**
 * Converts data to type Date.
 *
 * @method DataSource.parseDate
 * @param oData {Date | String | Number} Data to convert.
 * @return {Date} A Date instance.
 * @static
 */
YAHOO.util.DataSource.parseDate = function(oData) {
    var date = null;
    
    //Convert to date
    if(!(oData instanceof Date)) {
        date = new Date(oData);
    }
    else {
        return oData;
    }
    
    // Validate
    if(date instanceof Date) {
        return date;
    }
    else {
        return null;
    }
};
// Backward compatibility
YAHOO.util.DataSource.convertDate = function(oData) {
    return YAHOO.util.DataSource.parseDate(oData);
};

/////////////////////////////////////////////////////////////////////////////
//
// Public methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Public accessor to the unique name of the DataSource instance.
 *
 * @method toString
 * @return {String} Unique name of the DataSource instance.
 */
YAHOO.util.DataSource.prototype.toString = function() {
    return this._sName;
};

/**
 * Overridable method passes request to cache and returns cached response if any,
 * refreshing the hit in the cache as the newest item. Returns null if there is
 * no cache hit.
 *
 * @method getCachedResponse
 * @param oRequest {Object} Request object.
 * @param oCallback {Function} Handler function to receive the response.
 * @param oCaller {Object} The Calling object that is making the request.
 * @return {Object} Cached response object or null.
 */
YAHOO.util.DataSource.prototype.getCachedResponse = function(oRequest, oCallback, oCaller) {
    var aCache = this._aCache;
    var nCacheLength = (aCache) ? aCache.length : 0;
    var oResponse = null;

    // If cache is enabled...
    if((this.maxCacheEntries > 0) && aCache && (nCacheLength > 0)) {
        this.fireEvent("cacheRequestEvent", {request:oRequest,callback:oCallback,caller:oCaller});

        // Loop through each cached element
        for(var i = nCacheLength-1; i >= 0; i--) {
            var oCacheElem = aCache[i];

            // Defer cache hit logic to a public overridable method
            if(this.isCacheHit(oRequest,oCacheElem.request)) {
                // Grab the cached response
                oResponse = oCacheElem.response;
                // The cache returned a hit!
                // Remove element from its original location
                aCache.splice(i,1);
                // Add as newest
                this.addToCache(oRequest, oResponse);
                this.fireEvent("cacheResponseEvent", {request:oRequest,response:oResponse,callback:oCallback,caller:oCaller});
                break;
            }
        }
    }
    return oResponse;
};

/**
 * Default overridable method matches given request to given cached request.
 * Returns true if is a hit, returns false otherwise.  Implementers should
 * override this method to customize the cache-matching algorithm.
 *
 * @method isCacheHit
 * @param oRequest {Object} Request object.
 * @param oCachedRequest {Object} Cached request object.
 * @return {Boolean} True if given request matches cached request, false otherwise.
 */
YAHOO.util.DataSource.prototype.isCacheHit = function(oRequest, oCachedRequest) {
    return (oRequest === oCachedRequest);
};

/**
 * Adds a new item to the cache. If cache is full, evicts the stalest item
 * before adding the new item.
 *
 * @method addToCache
 * @param oRequest {Object} Request object.
 * @param oResponse {Object} Response object to cache.
 */
YAHOO.util.DataSource.prototype.addToCache = function(oRequest, oResponse) {
    var aCache = this._aCache;
    if(!aCache) {
        return;
    }

    //TODO: check for duplicate entries

    // If the cache is full, make room by removing stalest element (index=0)
    while(aCache.length >= this.maxCacheEntries) {
        aCache.shift();
    }

    // Add to cache in the newest position, at the end of the array
    var oCacheElem = {request:oRequest,response:oResponse};
    aCache.push(oCacheElem);
    this.fireEvent("responseCacheEvent", {request:oRequest,response:oResponse});
};

/**
 * Flushes cache.
 *
 * @method flushCache
 */
YAHOO.util.DataSource.prototype.flushCache = function() {
    if(this._aCache) {
        this._aCache = [];
        this.fireEvent("cacheFlushEvent");
    }
};

/**
 * First looks for cached response, then sends request to live data.
 *
 * @method sendRequest
 * @param oRequest {Object} Request object.
 * @param oCallback {Function} Handler function to receive the response.
 * @param oCaller {Object} The Calling object that is making the request.
 * @return {Number} Transaction ID, or null if response found in cache.
 */
YAHOO.util.DataSource.prototype.sendRequest = function(oRequest, oCallback, oCaller) {
    // First look in cache
    var oCachedResponse = this.getCachedResponse(oRequest, oCallback, oCaller);
    if(oCachedResponse) {
        oCallback.call(oCaller, oRequest, oCachedResponse);
        return null;
    }

    // Not in cache, so forward request to live data
    return this.makeConnection(oRequest, oCallback, oCaller);
};

/**
 * Overridable method provides default functionality to make a connection to
 * live data in order to send request. The response coming back is then
 * forwarded to the handleResponse function. This method should be customized
 * to achieve more complex implementations.
 *
 * @method makeConnection
 * @param oRequest {Object} Request object.
 * @param oCallback {Function} Handler function to receive the response.
 * @param oCaller {Object} The Calling object that is making the request.
 * @return {Number} Transaction ID.
 */
YAHOO.util.DataSource.prototype.makeConnection = function(oRequest, oCallback, oCaller) {
    this.fireEvent("requestEvent", {request:oRequest,callback:oCallback,caller:oCaller});
    var oRawResponse = null;
    var tId = YAHOO.util.DataSource._nTransactionId++;

    // How to make the connection depends on the type of data
    switch(this.dataType) {
        // If the live data is a JavaScript Function
        // pass the request in as a parameter and
        // forward the return value to the handler
        case YAHOO.util.DataSource.TYPE_JSFUNCTION:
            oRawResponse = this.liveData(oRequest);
            this.handleResponse(oRequest, oRawResponse, oCallback, oCaller, tId);
            break;
        // If the live data is over Connection Manager
        // set up the callback object and
        // pass the request in as a URL query and
        // forward the response to the handler
        case YAHOO.util.DataSource.TYPE_XHR:
            var oSelf = this;
            var oConnMgr = this.connMgr || YAHOO.util.Connect;
            var oQueue = this._oQueue;

            /**
             * Define Connection Manager success handler
             *
             * @method _xhrSuccess
             * @param oResponse {Object} HTTPXMLRequest object
             * @private
             */
            var _xhrSuccess = function(oResponse) {
                // If response ID does not match last made request ID,
                // silently fail and wait for the next response
                if(oResponse && (this.connXhrMode == "ignoreStaleResponses") &&
                        (oResponse.tId != oQueue.conn.tId)) {
                    return null;
                }
                // Error if no response
                else if(!oResponse) {
                    this.fireEvent("dataErrorEvent", {request:oRequest,
                            callback:oCallback, caller:oCaller,
                            message:YAHOO.util.DataSource.ERROR_DATANULL});

                    // Send error response back to the caller with the error flag on
                    oCallback.call(oCaller, oRequest, oResponse, true);

                    return null;
                }
                // Forward to handler
                else {
                    this.handleResponse(oRequest, oResponse, oCallback, oCaller, tId);
                }
            };

            /**
             * Define Connection Manager failure handler
             *
             * @method _xhrFailure
             * @param oResponse {Object} HTTPXMLRequest object
             * @private
             */
            var _xhrFailure = function(oResponse) {
                this.fireEvent("dataErrorEvent", {request:oRequest,
                        callback:oCallback, caller:oCaller,
                        message:YAHOO.util.DataSource.ERROR_DATAINVALID});

                // Backward compatibility
                if((this.liveData.lastIndexOf("?") !== this.liveData.length-1) &&
                    (oRequest.indexOf("?") !== 0)){
                }

                // Send failure response back to the caller with the error flag on
                oCallback.call(oCaller, oRequest, oResponse, true);
                return null;
            };

            /**
             * Define Connection Manager callback object
             *
             * @property _xhrCallback
             * @param oResponse {Object} HTTPXMLRequest object
             * @private
             */
             var _xhrCallback = {
                success:_xhrSuccess,
                failure:_xhrFailure,
                scope: this
            };

            // Apply Connection Manager timeout
            if(YAHOO.lang.isNumber(this.connTimeout)) {
                _xhrCallback.timeout = this.connTimeout;
            }

            // Cancel stale requests
            if(this.connXhrMode == "cancelStaleRequests") {
                    // Look in queue for stale requests
                    if(oQueue.conn) {
                        if(oConnMgr.abort) {
                            oConnMgr.abort(oQueue.conn);
                            oQueue.conn = null;
                        }
                        else {
                        }
                    }
            }

            // Get ready to send the request URL
            if(oConnMgr && oConnMgr.asyncRequest) {
                var sLiveData = this.liveData;
                var isPost = this.connMethodPost;
                var sMethod = (isPost) ? "POST" : "GET";
                var sUri = (isPost) ? sLiveData : sLiveData+oRequest;
                var sRequest = (isPost) ? oRequest : null;

                // Send the request right away
                if(this.connXhrMode != "queueRequests") {
                    oQueue.conn = oConnMgr.asyncRequest(sMethod, sUri, _xhrCallback, sRequest);
                }
                // Queue up then send the request
                else {
                    // Found a request already in progress
                    if(oQueue.conn) {
                        // Add request to queue
                        oQueue.requests.push({request:oRequest, callback:_xhrCallback});

                        // Interval needs to be started
                        if(!oQueue.interval) {
                            oQueue.interval = setInterval(function() {
                                // Connection is in progress
                                if(oConnMgr.isCallInProgress(oQueue.conn)) {
                                    return;
                                }
                                else {
                                    // Send next request
                                    if(oQueue.requests.length > 0) {
                                        sUri = (isPost) ? sLiveData : sLiveData+oQueue.requests[0].request;
                                        sRequest = (isPost) ? oQueue.requests[0].request : null;
                                        oQueue.conn = oConnMgr.asyncRequest(sMethod, sUri, oQueue.requests[0].callback, sRequest);

                                        // Remove request from queue
                                        oQueue.requests.shift();
                                    }
                                    // No more requests
                                    else {
                                        clearInterval(oQueue.interval);
                                        oQueue.interval = null;
                                    }
                                }
                            }, 50);
                        }
                    }
                    // Nothing is in progress
                    else {
                        oQueue.conn = oConnMgr.asyncRequest(sMethod, sUri, _xhrCallback, sRequest);
                    }
                }
            }
            else {
                // Send null response back to the caller with the error flag on
                oCallback.call(oCaller, oRequest, null, true);
            }

            break;
        // Simply forward the entire data object to the handler
        default:
            /* accounts for the following cases:
            YAHOO.util.DataSource.TYPE_UNKNOWN:
            YAHOO.util.DataSource.TYPE_JSARRAY:
            YAHOO.util.DataSource.TYPE_JSON:
            YAHOO.util.DataSource.TYPE_HTMLTABLE:
            YAHOO.util.DataSource.TYPE_XML:
            */
            oRawResponse = this.liveData;
            this.handleResponse(oRequest, oRawResponse, oCallback, oCaller, tId);
            break;
    }
    return tId;
};

/**
 * Handles raw data response from live data source. Sends a parsed response object
 * to the callback function in this format:
 *
 * fnCallback(oRequest, oParsedResponse)
 *
 * where the oParsedResponse object literal with the following properties:
 * <ul>
 *     <li>tId {Number} Unique transaction ID</li>
 *     <li>results {Array} Array of parsed data results</li>
 *     <li>error {Boolean} True if there was an error</li>
 * </ul>
 *
 * @method handleResponse
 * @param oRequest {Object} Request object
 * @param oRawResponse {Object} The raw response from the live database.
 * @param oCallback {Function} Handler function to receive the response.
 * @param oCaller {Object} The calling object that is making the request.
 * @param tId {Number} Transaction ID.
 */
YAHOO.util.DataSource.prototype.handleResponse = function(oRequest, oRawResponse, oCallback, oCaller, tId) {
    this.fireEvent("responseEvent", {request:oRequest, response:oRawResponse,
            callback:oCallback, caller:oCaller, tId: tId});
    var xhr = (this.dataType == YAHOO.util.DataSource.TYPE_XHR) ? true : false;
    var oParsedResponse = null;
    var bError = false;

    // Access to the raw response before it gets parsed
    oRawResponse = this.doBeforeParseData(oRequest, oRawResponse);

    switch(this.responseType) {
        case YAHOO.util.DataSource.TYPE_JSARRAY:
            if(xhr && oRawResponse.responseText) {
                oRawResponse = oRawResponse.responseText;
            }
            oParsedResponse = this.parseArrayData(oRequest, oRawResponse);
            break;
        case YAHOO.util.DataSource.TYPE_JSON:
            if(xhr && oRawResponse.responseText) {
                oRawResponse = oRawResponse.responseText;
            }
            oParsedResponse = this.parseJSONData(oRequest, oRawResponse);
            break;
        case YAHOO.util.DataSource.TYPE_HTMLTABLE:
            if(xhr && oRawResponse.responseText) {
                oRawResponse = oRawResponse.responseText;
            }
            oParsedResponse = this.parseHTMLTableData(oRequest, oRawResponse);
            break;
        case YAHOO.util.DataSource.TYPE_XML:
            if(xhr && oRawResponse.responseXML) {
                oRawResponse = oRawResponse.responseXML;
            }
            oParsedResponse = this.parseXMLData(oRequest, oRawResponse);
            break;
        case YAHOO.util.DataSource.TYPE_TEXT:
            if(xhr && oRawResponse.responseText) {
                oRawResponse = oRawResponse.responseText;
            }
            oParsedResponse = this.parseTextData(oRequest, oRawResponse);
            break;
        default:
            //var contentType = oRawResponse.getResponseHeader["Content-Type"];
            break;
    }


    if(oParsedResponse) {
        // Last chance to touch the raw response or the parsed response
        oParsedResponse.tId = tId;
        oParsedResponse = this.doBeforeCallback(oRequest, oRawResponse, oParsedResponse);
        this.fireEvent("responseParseEvent", {request:oRequest,
                response:oParsedResponse, callback:oCallback, caller:oCaller});
        // Cache the response
        this.addToCache(oRequest, oParsedResponse);
    }
    else {
        this.fireEvent("dataErrorEvent", {request:oRequest, callback:oCallback,
                caller:oCaller, message:YAHOO.util.DataSource.ERROR_DATANULL});
        
        // Send response back to the caller with the error flag on
        oParsedResponse = {error:true};
    }
    
    // Send the response back to the caller
    oCallback.call(oCaller, oRequest, oParsedResponse);
};

/**
 * Overridable method gives implementers access to the original raw response
 * before the data gets parsed. Implementers should take care not to return an
 * unparsable or otherwise invalid raw response.
 *
 * @method doBeforeParseData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Raw response for parsing.
 */
YAHOO.util.DataSource.prototype.doBeforeParseData = function(oRequest, oRawResponse) {
    return oRawResponse;
};

/**
 * Overridable method gives implementers access to the original raw response and
 * the parsed response (parsed against the given schema) before the data
 * is added to the cache (if applicable) and then sent back to callback function.
 * This is your chance to access the raw response and/or populate the parsed
 * response with any custom data.
 *
 * @method doBeforeCallback
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @param oParsedResponse {Object} The parsed response to return to calling object.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.doBeforeCallback = function(oRequest, oRawResponse, oParsedResponse) {
    return oParsedResponse;
};

/**
 * Overridable method parses raw array data into a response object.
 *
 * @method parseArrayData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.parseArrayData = function(oRequest, oRawResponse) {
    if(YAHOO.lang.isArray(oRawResponse) && YAHOO.lang.isArray(this.responseSchema.fields)) {
        var oParsedResponse = {results:[]};
        var fields = this.responseSchema.fields;
        for(var i=oRawResponse.length-1; i>-1; i--) {
            var oResult = {};
            for(var j=fields.length-1; j>-1; j--) {
                var field = fields[j];
                var key = (YAHOO.lang.isValue(field.key)) ? field.key : field;
                var data = (YAHOO.lang.isValue(oRawResponse[i][j])) ? oRawResponse[i][j] : oRawResponse[i][key];
                // Backward compatibility
                if(!field.parser && field.converter) {
                    field.parser = field.converter;
                }
                if(field.parser) {
                    data = field.parser.call(this, data);
                }
                // Safety measure
                if(data === undefined) {
                    data = null;
                }
                oResult[key] = data;
            }
            oParsedResponse.results.unshift(oResult);
        }
        return oParsedResponse;
    }
    else {
        return null;
    }
};

/**
 * Overridable method parses raw plain text data into a response object.
 *
 * @method parseTextData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.parseTextData = function(oRequest, oRawResponse) {
    var oParsedResponse = {};
    if(YAHOO.lang.isString(oRawResponse) &&
            YAHOO.lang.isArray(this.responseSchema.fields) &&
            YAHOO.lang.isString(this.responseSchema.recordDelim) &&
            YAHOO.lang.isString(this.responseSchema.fieldDelim)) {
        oParsedResponse.results = [];
        var recDelim = this.responseSchema.recordDelim;
        var fieldDelim = this.responseSchema.fieldDelim;
        var fields = this.responseSchema.fields;
        if(oRawResponse.length > 0) {
            // Delete the last line delimiter at the end of the data if it exists
            var newLength = oRawResponse.length-recDelim.length;
            if(oRawResponse.substr(newLength) == recDelim) {
                oRawResponse = oRawResponse.substr(0, newLength);
            }
            // Split along record delimiter to get an array of strings
            var recordsarray = oRawResponse.split(recDelim);
            // Cycle through each record, except the first which contains header info
            for(var i = recordsarray.length-1; i>-1; i--) {
                var oResult = {};
                for(var j=fields.length-1; j>-1; j--) {
                    // Split along field delimter to get each data value
                    var fielddataarray = recordsarray[i].split(fieldDelim);

                    // Remove quotation marks from edges, if applicable
                    var data = fielddataarray[j];
                    if(data.charAt(0) == "\"") {
                        data = data.substr(1);
                    }
                    if(data.charAt(data.length-1) == "\"") {
                        data = data.substr(0,data.length-1);
                    }
                    var field = fields[j];
                    var key = (YAHOO.lang.isValue(field.key)) ? field.key : field;
                    // Backward compatibility
                    if(!field.parser && field.converter) {
                        field.parser = field.converter;
                    }
                    if(field.parser) {
                        data = field.parser.call(this, data);
                    }
                    // Safety measure
                    if(data === undefined) {
                        data = null;
                    }
                    oResult[key] = data;
                }
                oParsedResponse.results.unshift(oResult);
            }
        }
    }
    else {
        oParsedResponse.error = true;
    }
    return oParsedResponse;
};

/**
 * Overridable method parses raw XML data into a response object.
 *
 * @method parseXMLData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.parseXMLData = function(oRequest, oRawResponse) {
        var bError = false;
        var oParsedResponse = {};
        var xmlList = (this.responseSchema.resultNode) ?
                oRawResponse.getElementsByTagName(this.responseSchema.resultNode) :
                null;
        if(!xmlList || !YAHOO.lang.isArray(this.responseSchema.fields)) {
            bError = true;
        }
        // Loop through each result
        else {
            oParsedResponse.results = [];
            for(var k = xmlList.length-1; k >= 0 ; k--) {
                var result = xmlList.item(k);
                var oResult = {};
                // Loop through each data field in each result using the schema
                for(var m = this.responseSchema.fields.length-1; m >= 0 ; m--) {
                    var field = this.responseSchema.fields[m];
                    var key = (YAHOO.lang.isValue(field.key)) ? field.key : field;
                    var data = null;
                    // Values may be held in an attribute...
                    var xmlAttr = result.attributes.getNamedItem(key);
                    if(xmlAttr) {
                        data = xmlAttr.value;
                    }
                    // ...or in a node
                    else {
                        var xmlNode = result.getElementsByTagName(key);
                        if(xmlNode && xmlNode.item(0) && xmlNode.item(0).firstChild) {
                            data = xmlNode.item(0).firstChild.nodeValue;
                        }
                        else {
                               data = "";
                        }
                    }
                    // Backward compatibility
                    if(!field.parser && field.converter) {
                        field.parser = field.converter;
                    }
                    if(field.parser) {
                        data = field.parser.call(this, data);
                    }
                    // Safety measure
                    if(data === undefined) {
                        data = null;
                    }
                    oResult[key] = data;
                }
                // Capture each array of values into an array of results
                oParsedResponse.results.unshift(oResult);
            }
        }
        if(bError) {
            oParsedResponse.error = true;
        }
        else {
        }
        return oParsedResponse;
};

/**
 * Overridable method parses raw JSON data into a response object.
 *
 * @method parseJSONData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.parseJSONData = function(oRequest, oRawResponse) {
    var oParsedResponse = {};
    if(oRawResponse && YAHOO.lang.isArray(this.responseSchema.fields)) {
        var fields = this.responseSchema.fields;
        var bError = false;
        oParsedResponse.results = [];
        var jsonObj,jsonList;

        // Parse JSON object out if it's a string
        if(YAHOO.lang.isString(oRawResponse)) {
            // Check for latest JSON lib but divert KHTML clients
            var isNotMac = (navigator.userAgent.toLowerCase().indexOf('khtml')== -1);
            if(oRawResponse.parseJSON && isNotMac) {
                // Use the new JSON utility if available
                jsonObj = oRawResponse.parseJSON();
                if(!jsonObj) {
                    bError = true;
                }
            }
            // Check for older JSON lib but divert KHTML clients
            else if(window.JSON && JSON.parse && isNotMac) {
                // Use the JSON utility if available
                jsonObj = JSON.parse(oRawResponse);
                if(!jsonObj) {
                    bError = true;
                }
            }
            // No JSON lib found so parse the string
            else {
                try {
                    // Trim leading spaces
                    while (oRawResponse.length > 0 &&
                            (oRawResponse.charAt(0) != "{") &&
                            (oRawResponse.charAt(0) != "[")) {
                        oRawResponse = oRawResponse.substring(1, oRawResponse.length);
                    }

                    if(oRawResponse.length > 0) {
                        // Strip extraneous stuff at the end
                        var objEnd = Math.max(oRawResponse.lastIndexOf("]"),oRawResponse.lastIndexOf("}"));
                        oRawResponse = oRawResponse.substring(0,objEnd+1);

                        // Turn the string into an object literal...
                        // ...eval is necessary here
                        jsonObj = eval("(" + oRawResponse + ")");
                        if(!jsonObj) {
                            bError = true;
                        }

                    }
                    else {
                        jsonObj = null;
                        bError = true;
                    }
                }
                catch(e) {
                    bError = true;
               }
            }
        }
        // Response must already be a JSON object
        else if(oRawResponse.constructor == Object) {
            jsonObj = oRawResponse;
        }
        // Not a string or an object
        else {
            bError = true;
        }
        // Now that we have a JSON object, parse a jsonList out of it
        if(jsonObj && jsonObj.constructor == Object) {
            try {
                // eval is necessary here since schema can be of unknown depth
                jsonList = eval("jsonObj." + this.responseSchema.resultsList);
            }
            catch(e) {
                bError = true;
            }
        }

        if(bError || !jsonList) {
            oParsedResponse.error = true;
        }
        if(jsonList && !YAHOO.lang.isArray(jsonList)) {
            jsonList = [jsonList];
        }
        else if(!jsonList) {
            jsonList = [];
        }

        // Loop through the array of all responses...
        for(var i = jsonList.length-1; i >= 0 ; i--) {
            var oResult = {};
            var jsonResult = jsonList[i];
            // ...and loop through each data field value of each response
            for(var j = fields.length-1; j >= 0 ; j--) {
                var field = fields[j];
                var key = (YAHOO.lang.isValue(field.key)) ? field.key : field;
                // ...and capture data into an array mapped according to the schema...
                // eval is necessary here since schema can be of unknown depth
                var data = eval("jsonResult." + key);
                
                // Backward compatibility
                if(!field.parser && field.converter) {
                    field.parser = field.converter;
                }
                if(field.parser) {
                    data = field.parser.call(this, data);
                }
                // Safety measure
                if(data === undefined) {
                    data = null;
                }
                oResult[key] = data;
            }
            // Capture the array of data field values in an array of results
            oParsedResponse.results.unshift(oResult);
        }
    }
    else {
        oParsedResponse.error = true;
    }
    return oParsedResponse;
};

/**
 * Overridable method parses raw HTML TABLE element data into a response object.
 *
 * @method parseHTMLTableData
 * @param oRequest {Object} Request object.
 * @param oRawResponse {Object} The raw response from the live database.
 * @return {Object} Parsed response object.
 */
YAHOO.util.DataSource.prototype.parseHTMLTableData = function(oRequest, oRawResponse) {
        var bError = false;
        var elTable = oRawResponse;
        var fields = this.responseSchema.fields;
        var oParsedResponse = {};
        oParsedResponse.results = [];

        // Iterate through each TBODY
        for(var i=0; i<elTable.tBodies.length; i++) {
            var elTbody = elTable.tBodies[i];

            // Iterate through each TR
            for(var j=elTbody.rows.length-1; j>-1; j--) {
                var elRow = elTbody.rows[j];
                var oResult = {};
                
                for(var k=fields.length-1; k>-1; k--) {
                    var field = fields[k];
                    var key = (YAHOO.lang.isValue(field.key)) ? field.key : field;
                    var data = elRow.cells[k].innerHTML;

                    // Backward compatibility
                    if(!field.parser && field.converter) {
                        field.parser = field.converter;
                    }
                    if(field.parser) {
                        data = field.parser.call(this, data);
                    }
                    // Safety measure
                    if(data === undefined) {
                        data = null;
                    }
                    oResult[key] = data;
                }
                oParsedResponse.results.unshift(oResult);
            }
        }

        if(bError) {
            oParsedResponse.error = true;
        }
        else {
        }
        return oParsedResponse;
};

YAHOO.register("datasource", YAHOO.util.DataSource, {version: "2.3.0", build: "442"});
