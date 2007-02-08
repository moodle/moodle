/*
Copyright (c) 2006, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 0.12.2
*/
/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The LogMsg class defines a single log message.
 *
 * @class LogMsg
 * @constructor
 * @param oConfigs {Object} Object literal of configuration params.
 */
 YAHOO.widget.LogMsg = function(oConfigs) {
    // Parse configs
    if (typeof oConfigs == "object") {
        for(var param in oConfigs) {
            this[param] = oConfigs[param];
        }
    }
 };
 
/////////////////////////////////////////////////////////////////////////////
//
// Public member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Log message.
 *
 * @property msg
 * @type String
 */
YAHOO.widget.LogMsg.prototype.msg = null;
 
/**
 * Log timestamp.
 *
 * @property time
 * @type Date
 */
YAHOO.widget.LogMsg.prototype.time = null;

/**
 * Log category.
 *
 * @property category
 * @type String
 */
YAHOO.widget.LogMsg.prototype.category = null;

/**
 * Log source. The first word passed in as the source argument.
 *
 * @property source
 * @type String
 */
YAHOO.widget.LogMsg.prototype.source = null;

/**
 * Log source detail. The remainder of the string passed in as the source argument, not
 * including the first word (if any).
 *
 * @property sourceDetail
 * @type String
 */
YAHOO.widget.LogMsg.prototype.sourceDetail = null;

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The LogWriter class provides a mechanism to log messages through
 * YAHOO.widget.Logger from a named source.
 *
 * @class LogWriter
 * @constructor
 * @param sSource {String} Source of LogWriter instance.
 */
YAHOO.widget.LogWriter = function(sSource) {
    if(!sSource) {
        YAHOO.log("Could not instantiate LogWriter due to invalid source.",
            "error", "LogWriter");
        return;
    }
    this._source = sSource;
 };

/////////////////////////////////////////////////////////////////////////////
//
// Public methods
//
/////////////////////////////////////////////////////////////////////////////

 /**
 * Public accessor to the unique name of the LogWriter instance.
 *
 * @method toString
 * @return {String} Unique name of the LogWriter instance.
 */
YAHOO.widget.LogWriter.prototype.toString = function() {
    return "LogWriter " + this._sSource;
};

/**
 * Logs a message attached to the source of the LogWriter.
 *
 * @method log
 * @param sMsg {String} The log message.
 * @param sCategory {String} Category name.
 */
YAHOO.widget.LogWriter.prototype.log = function(sMsg, sCategory) {
    YAHOO.widget.Logger.log(sMsg, sCategory, this._source);
};

/**
 * Public accessor to get the source name.
 *
 * @method getSource
 * @return {String} The LogWriter source.
 */
YAHOO.widget.LogWriter.prototype.getSource = function() {
    return this._sSource;
};

/**
 * Public accessor to set the source name.
 *
 * @method setSource
 * @param sSource {String} Source of LogWriter instance.
 */
YAHOO.widget.LogWriter.prototype.setSource = function(sSource) {
    if(!sSource) {
        YAHOO.log("Could not set source due to invalid source.", "error", this.toString());
        return;
    }
    else {
        this._sSource = sSource;
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Source of the LogWriter instance.
 *
 * @property _source
 * @type String
 * @private
 */
YAHOO.widget.LogWriter.prototype._source = null;



/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The LogReader class provides UI to read messages logged to YAHOO.widget.Logger.
 *
 * @class LogReader
 * @constructor
 * @param elContainer {HTMLElement} (optional) DOM element reference of an existing DIV.
 * @param elContainer {String} (optional) String ID of an existing DIV.
 * @param oConfigs {Object} (optional) Object literal of configuration params.
 */
YAHOO.widget.LogReader = function(elContainer, oConfigs) {
    var oSelf = this;
    this._sName = YAHOO.widget.LogReader._index;
    YAHOO.widget.LogReader._index++;

    // Parse config vars here
    if (typeof oConfigs == "object") {
        for(var param in oConfigs) {
            this[param] = oConfigs[param];
        }
    }

    // Attach container...
    if(elContainer) {
        if(typeof elContainer == "string") {
            this._elContainer = document.getElementById(elContainer);
        }
        else if(elContainer.tagName) {
            this._elContainer = elContainer;
        }
        this._elContainer.className = "yui-log";
    }
    // ...or create container from scratch
    if(!this._elContainer) {
        if(YAHOO.widget.LogReader._elDefaultContainer) {
            this._elContainer =  YAHOO.widget.LogReader._elDefaultContainer;
        }
        else {
            this._elContainer = document.body.appendChild(document.createElement("div"));
            this._elContainer.id = "yui-log";
            this._elContainer.className = "yui-log";

            YAHOO.widget.LogReader._elDefaultContainer = this._elContainer;
        }

        // If implementer has provided container values, trust and set those
        var containerStyle = this._elContainer.style;
        if(this.width) {
            containerStyle.width = this.width;
        }
        if(this.right) {
            containerStyle.right = this.right;
        }
        if(this.top) {
            containerStyle.top = this.top;
        }
         if(this.left) {
            containerStyle.left = this.left;
            containerStyle.right = "auto";
        }
        if(this.bottom) {
            containerStyle.bottom = this.bottom;
            containerStyle.top = "auto";
        }
       if(this.fontSize) {
            containerStyle.fontSize = this.fontSize;
        }
        if(window.opera) {
            document.body.style += '';
        }
    }

    if(this._elContainer) {
        // Create header
        if(!this._elHd) {
            this._elHd = this._elContainer.appendChild(document.createElement("div"));
            this._elHd.id = "yui-log-hd" + this._sName;
            this._elHd.className = "yui-log-hd";

            this._elCollapse = this._elHd.appendChild(document.createElement("div"));
            this._elCollapse.className = "yui-log-btns";

            this._btnCollapse = document.createElement("input");
            this._btnCollapse.type = "button";
            this._btnCollapse.style.fontSize =
                YAHOO.util.Dom.getStyle(this._elContainer,"fontSize");
            this._btnCollapse.className = "yui-log-button";
            this._btnCollapse.value = "Collapse";
            this._btnCollapse = this._elCollapse.appendChild(this._btnCollapse);
            YAHOO.util.Event.addListener(
                oSelf._btnCollapse,'click',oSelf._onClickCollapseBtn,oSelf);

            this._title = this._elHd.appendChild(document.createElement("h4"));
            this._title.innerHTML = "Logger Console";

            // If Drag and Drop utility is available...
            // ...and this container was created from scratch...
            // ...then make the header draggable
            if(YAHOO.util.DD &&
            (YAHOO.widget.LogReader._elDefaultContainer == this._elContainer)) {
                var ylog_dd = new YAHOO.util.DD(this._elContainer.id);
                ylog_dd.setHandleElId(this._elHd.id);
                this._elHd.style.cursor = "move";
            }
        }
        // Ceate console
        if(!this._elConsole) {
            this._elConsole =
                this._elContainer.appendChild(document.createElement("div"));
            this._elConsole.className = "yui-log-bd";

            // If implementer has provided console, trust and set those
            if(this.height) {
                this._elConsole.style.height = this.height;
            }
        }
        // Don't create footer if disabled
        if(!this._elFt && this.footerEnabled) {
            this._elFt = this._elContainer.appendChild(document.createElement("div"));
            this._elFt.className = "yui-log-ft";

            this._elBtns = this._elFt.appendChild(document.createElement("div"));
            this._elBtns.className = "yui-log-btns";

            this._btnPause = document.createElement("input");
            this._btnPause.type = "button";
            this._btnPause.style.fontSize =
                YAHOO.util.Dom.getStyle(this._elContainer,"fontSize");
            this._btnPause.className = "yui-log-button";
            this._btnPause.value = "Pause";
            this._btnPause = this._elBtns.appendChild(this._btnPause);
            YAHOO.util.Event.addListener(
                oSelf._btnPause,'click',oSelf._onClickPauseBtn,oSelf);

            this._btnClear = document.createElement("input");
            this._btnClear.type = "button";
            this._btnClear.style.fontSize =
                YAHOO.util.Dom.getStyle(this._elContainer,"fontSize");
            this._btnClear.className = "yui-log-button";
            this._btnClear.value = "Clear";
            this._btnClear = this._elBtns.appendChild(this._btnClear);
            YAHOO.util.Event.addListener(
                oSelf._btnClear,'click',oSelf._onClickClearBtn,oSelf);

            this._elCategoryFilters = this._elFt.appendChild(document.createElement("div"));
            this._elCategoryFilters.className = "yui-log-categoryfilters";
            this._elSourceFilters = this._elFt.appendChild(document.createElement("div"));
            this._elSourceFilters.className = "yui-log-sourcefilters";
        }
    }

    // Initialize internal vars
    if(!this._buffer) {
        this._buffer = []; // output buffer
    }
    // Timestamp of last log message to console
    this._lastTime = YAHOO.widget.Logger.getStartTime(); 
    
    // Subscribe to Logger custom events
    YAHOO.widget.Logger.newLogEvent.subscribe(this._onNewLog, this);
    YAHOO.widget.Logger.logResetEvent.subscribe(this._onReset, this);

    // Initialize category filters
    this._categoryFilters = [];
    var catsLen = YAHOO.widget.Logger.categories.length;
    if(this._elCategoryFilters) {
        for(var i=0; i < catsLen; i++) {
            this._createCategoryCheckbox(YAHOO.widget.Logger.categories[i]);
        }
    }
    // Initialize source filters
    this._sourceFilters = [];
    var sourcesLen = YAHOO.widget.Logger.sources.length;
    if(this._elSourceFilters) {
        for(var j=0; j < sourcesLen; j++) {
            this._createSourceCheckbox(YAHOO.widget.Logger.sources[j]);
        }
    }
    YAHOO.widget.Logger.categoryCreateEvent.subscribe(this._onCategoryCreate, this);
    YAHOO.widget.Logger.sourceCreateEvent.subscribe(this._onSourceCreate, this);

    this._filterLogs();
    YAHOO.log("LogReader initialized", null, this.toString());
};

/////////////////////////////////////////////////////////////////////////////
//
// Public member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Whether or not the log reader is enabled to output log messages.
 *
 * @property logReaderEnabled
 * @type Boolean
 * @default true
 */
YAHOO.widget.LogReader.prototype.logReaderEnabled = true;

/**
 * Public member to access CSS width of the log reader container.
 *
 * @property width
 * @type String
 */
YAHOO.widget.LogReader.prototype.width = null;

/**
 * Public member to access CSS height of the log reader container.
 *
 * @property height
 * @type String
 */
YAHOO.widget.LogReader.prototype.height = null;

/**
 * Public member to access CSS top position of the log reader container.
 *
 * @property top
 * @type String
 */
YAHOO.widget.LogReader.prototype.top = null;

/**
 * Public member to access CSS left position of the log reader container.
 *
 * @property left
 * @type String
 */
YAHOO.widget.LogReader.prototype.left = null;

/**
 * Public member to access CSS right position of the log reader container.
 *
 * @property right
 * @type String
 */
YAHOO.widget.LogReader.prototype.right = null;

/**
 * Public member to access CSS bottom position of the log reader container.
 *
 * @property bottom
 * @type String
 */
YAHOO.widget.LogReader.prototype.bottom = null;

/**
 * Public member to access CSS font size of the log reader container.
 *
 * @property fontSize
 * @type String
 */
YAHOO.widget.LogReader.prototype.fontSize = null;

/**
 * Whether or not the footer UI is enabled for the log reader.
 *
 * @property footerEnabled
 * @type Boolean
 * @default true
 */
YAHOO.widget.LogReader.prototype.footerEnabled = true;

/**
 * Whether or not output is verbose (more readable). Setting to true will make
 * output more compact (less readable).
 *
 * @property verboseOutput
 * @type Boolean
 * @default true
 */
YAHOO.widget.LogReader.prototype.verboseOutput = true;

/**
 * Whether or not newest message is printed on top.
 *
 * @property newestOnTop
 * @type Boolean
 */
YAHOO.widget.LogReader.prototype.newestOnTop = true;

/**
 * Maximum number of messages a LogReader console will display.
 *
 * @property thresholdMax
 * @type Number
 * @default 500
 */
YAHOO.widget.LogReader.prototype.thresholdMax = 500;

/**
 * When a LogReader console reaches its thresholdMax, it will clear out messages
 * and print out the latest thresholdMin number of messages.
 *
 * @property thresholdMin
 * @type Number
 * @default 100
 */
YAHOO.widget.LogReader.prototype.thresholdMin = 100;

/////////////////////////////////////////////////////////////////////////////
//
// Public methods
//
/////////////////////////////////////////////////////////////////////////////

 /**
 * Public accessor to the unique name of the LogReader instance.
 *
 * @method toString
 * @return {String} Unique name of the LogReader instance.
 */
YAHOO.widget.LogReader.prototype.toString = function() {
    return "LogReader instance" + this._sName;
};
/**
 * Pauses output of log messages. While paused, log messages are not lost, but
 * get saved to a buffer and then output upon resume of log reader.
 *
 * @method pause
 */
YAHOO.widget.LogReader.prototype.pause = function() {
    this._timeout = null;
    this.logReaderEnabled = false;
};

/**
 * Resumes output of log messages, including outputting any log messages that
 * have been saved to buffer while paused.
 *
 * @method resume
 */
YAHOO.widget.LogReader.prototype.resume = function() {
    this.logReaderEnabled = true;
    this._printBuffer();
};

/**
 * Hides UI of log reader. Logging functionality is not disrupted.
 *
 * @method hide
 */
YAHOO.widget.LogReader.prototype.hide = function() {
    this._elContainer.style.display = "none";
};

/**
 * Shows UI of log reader. Logging functionality is not disrupted.
 *
 * @method show
 */
YAHOO.widget.LogReader.prototype.show = function() {
    this._elContainer.style.display = "block";
};

/**
 * Updates title to given string.
 *
 * @method setTitle
 * @param sTitle {String} New title.
 */
YAHOO.widget.LogReader.prototype.setTitle = function(sTitle) {
    this._title.innerHTML = this.html2Text(sTitle);
};

/**
 * Gets timestamp of the last log.
 *
 * @method getLastTime
 * @return {Date} Timestamp of the last log.
 */
YAHOO.widget.LogReader.prototype.getLastTime = function() {
    return this._lastTime;
};

/**
 * Formats message string to HTML for output to console.
 *
 * @method formatMsg
 * @param oLogMsg {Object} Log message object.
 * @return {String} HTML-formatted message for output to console.
 */
YAHOO.widget.LogReader.prototype.formatMsg = function(oLogMsg) {
    var category = oLogMsg.category;
    
    // Label for color-coded display
    var label = category.substring(0,4).toUpperCase();

    // Calculate the elapsed time to be from the last item that passed through the filter,
    // not the absolute previous item in the stack

    var time = oLogMsg.time;
    if (time.toLocaleTimeString) {
        var localTime  = time.toLocaleTimeString();
    }
    else {
        localTime = time.toString();
    }

    var msecs = time.getTime();
    var startTime = YAHOO.widget.Logger.getStartTime();
    var totalTime = msecs - startTime;
    var elapsedTime = msecs - this.getLastTime();

    var source = oLogMsg.source;
    var sourceDetail = oLogMsg.sourceDetail;
    var sourceAndDetail = (sourceDetail) ?
        source + " " + sourceDetail : source;
        
    // Escape HTML entities in the log message itself for output to console
    var msg = this.html2Text(oLogMsg.msg);

    // Verbose output includes extra line breaks
    var output =  (this.verboseOutput) ?
        ["<p><span class='", category, "'>", label, "</span> ",
        totalTime, "ms (+", elapsedTime, ") ",
        localTime, ": ",
        "</p><p>",
        sourceAndDetail,
        ": </p><p>",
        msg,
        "</p>"] :

        ["<p><span class='", category, "'>", label, "</span> ",
        totalTime, "ms (+", elapsedTime, ") ",
        localTime, ": ",
        sourceAndDetail, ": ",
        msg,"</p>"];

    return output.join("");
};

/**
 * Converts input chars "<", ">", and "&" to HTML entities.
 *
 * @method html2Text
 * @param sHtml {String} String to convert.
 * @private
 */
YAHOO.widget.LogReader.prototype.html2Text = function(sHtml) {
    if(sHtml) {
        sHtml += "";
        return sHtml.replace(/&/g, "&#38;").replace(/</g, "&#60;").replace(/>/g, "&#62;");
    }
    return "";
};

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Internal class member to index multiple log reader instances.
 *
 * @property _memberName
 * @static
 * @type Number
 * @default 0
 * @private
 */
YAHOO.widget.LogReader._index = 0;

/**
 * Name of LogReader instance.
 *
 * @property _sName
 * @type String
 * @private
 */
YAHOO.widget.LogReader.prototype._sName = null;

/**
 * A class member shared by all log readers if a container needs to be
 * created during instantiation. Will be null if a container element never needs to
 * be created on the fly, such as when the implementer passes in their own element.
 *
 * @property _elDefaultContainer
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader._elDefaultContainer = null;

/**
 * Buffer of log message objects for batch output.
 *
 * @property _buffer
 * @type Object[]
 * @private
 */
YAHOO.widget.LogReader.prototype._buffer = null;

/**
 * Number of log messages output to console.
 *
 * @property _consoleMsgCount
 * @type Number
 * @default 0
 * @private
 */
YAHOO.widget.LogReader.prototype._consoleMsgCount = 0;

/**
 * Date of last output log message.
 *
 * @property _lastTime
 * @type Date
 * @private
 */
YAHOO.widget.LogReader.prototype._lastTime = null;

/**
 * Batched output timeout ID.
 *
 * @property _timeout
 * @type Number
 * @private
 */
YAHOO.widget.LogReader.prototype._timeout = null;

/**
 * Array of filters for log message categories.
 *
 * @property _categoryFilters
 * @type String[]
 * @private
 */
YAHOO.widget.LogReader.prototype._categoryFilters = null;

/**
 * Array of filters for log message sources.
 *
 * @property _sourceFilters
 * @type String[]
 * @private
 */
YAHOO.widget.LogReader.prototype._sourceFilters = null;

/**
 * Log reader container element.
 *
 * @property _elContainer
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elContainer = null;

/**
 * Log reader header element.
 *
 * @property _elHd
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elHd = null;

/**
 * Log reader collapse element.
 *
 * @property _elCollapse
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elCollapse = null;

/**
 * Log reader collapse button element.
 *
 * @property _btnCollapse
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._btnCollapse = null;

/**
 * Log reader title header element.
 *
 * @property _title
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._title = null;

/**
 * Log reader console element.
 *
 * @property _elConsole
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elConsole = null;

/**
 * Log reader footer element.
 *
 * @property _elFt
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elFt = null;

/**
 * Log reader buttons container element.
 *
 * @property _elBtns
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elBtns = null;

/**
 * Container element for log reader category filter checkboxes.
 *
 * @property _elCategoryFilters
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elCategoryFilters = null;

/**
 * Container element for log reader source filter checkboxes.
 *
 * @property _elSourceFilters
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._elSourceFilters = null;

/**
 * Log reader pause button element.
 *
 * @property _btnPause
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._btnPause = null;

/**
 * Clear button element.
 *
 * @property _btnClear
 * @type HTMLElement
 * @private
 */
YAHOO.widget.LogReader.prototype._btnClear = null;

/////////////////////////////////////////////////////////////////////////////
//
// Private methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Creates the UI for a category filter in the log reader footer element.
 *
 * @method _createCategoryCheckbox
 * @param sCategory {String} Category name.
 * @private
 */
YAHOO.widget.LogReader.prototype._createCategoryCheckbox = function(sCategory) {
    var oSelf = this;

    if(this._elFt) {
        var elParent = this._elCategoryFilters;
        var filters = this._categoryFilters;

        var elFilter = elParent.appendChild(document.createElement("span"));
        elFilter.className = "yui-log-filtergrp";
            // Append el at the end so IE 5.5 can set "type" attribute
            // and THEN set checked property
            var chkCategory = document.createElement("input");
            chkCategory.id = "yui-log-filter-" + sCategory + this._sName;
            chkCategory.className = "yui-log-filter-" + sCategory;
            chkCategory.type = "checkbox";
            chkCategory.category = sCategory;
            chkCategory = elFilter.appendChild(chkCategory);
            chkCategory.checked = true;

            // Add this checked filter to the internal array of filters
            filters.push(sCategory);
            // Subscribe to the click event
            YAHOO.util.Event.addListener(chkCategory,'click',oSelf._onCheckCategory,oSelf);

            // Create and class the text label
            var lblCategory = elFilter.appendChild(document.createElement("label"));
            lblCategory.htmlFor = chkCategory.id;
            lblCategory.className = sCategory;
            lblCategory.innerHTML = sCategory;
    }
};

/**
 * Creates a checkbox in the log reader footer element to filter by source.
 *
 * @method _createSourceCheckbox
 * @param sSource {String} Source name.
 * @private
 */
YAHOO.widget.LogReader.prototype._createSourceCheckbox = function(sSource) {
    var oSelf = this;

    if(this._elFt) {
        var elParent = this._elSourceFilters;
        var filters = this._sourceFilters;

        var elFilter = elParent.appendChild(document.createElement("span"));
        elFilter.className = "yui-log-filtergrp";

        // Append el at the end so IE 5.5 can set "type" attribute
        // and THEN set checked property
        var chkSource = document.createElement("input");
        chkSource.id = "yui-log-filter" + sSource + this._sName;
        chkSource.className = "yui-log-filter" + sSource;
        chkSource.type = "checkbox";
        chkSource.source = sSource;
        chkSource = elFilter.appendChild(chkSource);
        chkSource.checked = true;

        // Add this checked filter to the internal array of filters
        filters.push(sSource);
        // Subscribe to the click event
        YAHOO.util.Event.addListener(chkSource,'click',oSelf._onCheckSource,oSelf);

        // Create and class the text label
        var lblSource = elFilter.appendChild(document.createElement("label"));
        lblSource.htmlFor = chkSource.id;
        lblSource.className = sSource;
        lblSource.innerHTML = sSource;
    }
};

/**
 * Reprints all log messages in the stack through filters.
 *
 * @method _filterLogs
 * @private
 */
YAHOO.widget.LogReader.prototype._filterLogs = function() {
    // Reprint stack with new filters
    if (this._elConsole !== null) {
        this._clearConsole();
        this._printToConsole(YAHOO.widget.Logger.getStack());
    }
};

/**
 * Clears all outputted log messages from the console and resets the time of the
 * last output log message.
 *
 * @method _clearConsole
 * @private
 */
YAHOO.widget.LogReader.prototype._clearConsole = function() {
    // Clear the buffer of any pending messages
    this._timeout = null;
    this._buffer = [];
    this._consoleMsgCount = 0;

    // Reset the rolling timer
    this._lastTime = YAHOO.widget.Logger.getStartTime();

    var elConsole = this._elConsole;
    while(elConsole.hasChildNodes()) {
        elConsole.removeChild(elConsole.firstChild);
    }
};

/**
 * Sends buffer of log messages to output and clears buffer.
 *
 * @method _printBuffer
 * @private
 */
YAHOO.widget.LogReader.prototype._printBuffer = function() {
    this._timeout = null;

    if(this._elConsole !== null) {
        var thresholdMax = this.thresholdMax;
        thresholdMax = (thresholdMax && !isNaN(thresholdMax)) ? thresholdMax : 500;
        if(this._consoleMsgCount < thresholdMax) {
            var entries = [];
            for (var i=0; i<this._buffer.length; i++) {
                entries[i] = this._buffer[i];
            }
            this._buffer = [];
            this._printToConsole(entries);
        }
        else {
            this._filterLogs();
        }
        
        if(!this.newestOnTop) {
            this._elConsole.scrollTop = this._elConsole.scrollHeight;
        }
    }
};

/**
 * Cycles through an array of log messages, and outputs each one to the console
 * if its category has not been filtered out.
 *
 * @method _printToConsole
 * @param aEntries {Object[]} Array of LogMsg objects to output to console.
 * @private
 */
YAHOO.widget.LogReader.prototype._printToConsole = function(aEntries) {
    // Manage the number of messages displayed in the console
    var entriesLen = aEntries.length;
    var thresholdMin = this.thresholdMin;
    if(isNaN(thresholdMin) || (thresholdMin > this.thresholdMax)) {
        thresholdMin = 0;
    }
    var entriesStartIndex = (entriesLen > thresholdMin) ? (entriesLen - thresholdMin) : 0;
    
    // Iterate through all log entries 
    var sourceFiltersLen = this._sourceFilters.length;
    var categoryFiltersLen = this._categoryFilters.length;
    for(var i=entriesStartIndex; i<entriesLen; i++) {
        // Print only the ones that filter through
        var okToPrint = false;
        var okToFilterCats = false;

        // Get log message details
        var entry = aEntries[i];
        var source = entry.source;
        var category = entry.category;

        for(var j=0; j<sourceFiltersLen; j++) {
            if(source == this._sourceFilters[j]) {
                okToFilterCats = true;
                break;
            }
        }
        if(okToFilterCats) {
            for(var k=0; k<categoryFiltersLen; k++) {
                if(category == this._categoryFilters[k]) {
                    okToPrint = true;
                    break;
                }
            }
        }
        if(okToPrint) {
            var output = this.formatMsg(entry);

            // Verbose output uses <code> tag instead of <pre> tag (for wrapping)
            var container = (this.verboseOutput) ? "CODE" : "PRE";
            var oNewElement = (this.newestOnTop) ?
                this._elConsole.insertBefore(
                    document.createElement(container),this._elConsole.firstChild):
                this._elConsole.appendChild(document.createElement(container));

            oNewElement.innerHTML = output;
            this._consoleMsgCount++;
            this._lastTime = entry.time.getTime();
        }
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Private event handlers
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Handles Logger's categoryCreateEvent.
 *
 * @method _onCategoryCreate
 * @param sType {String} The event.
 * @param aArgs {Object[]} Data passed from event firer.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onCategoryCreate = function(sType, aArgs, oSelf) {
    var category = aArgs[0];
    if(oSelf._elFt) {
        oSelf._createCategoryCheckbox(category);
    }
};

/**
 * Handles Logger's sourceCreateEvent.
 *
 * @method _onSourceCreate
 * @param sType {String} The event.
 * @param aArgs {Object[]} Data passed from event firer.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onSourceCreate = function(sType, aArgs, oSelf) {
    var source = aArgs[0];
    if(oSelf._elFt) {
        oSelf._createSourceCheckbox(source);
    }
};

/**
 * Handles check events on the category filter checkboxes.
 *
 * @method _onCheckCategory
 * @param v {HTMLEvent} The click event.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onCheckCategory = function(v, oSelf) {
    var newFilter = this.category;
    var filtersArray = oSelf._categoryFilters;

    if(!this.checked) { // Remove category from filters
        for(var i=0; i<filtersArray.length; i++) {
            if(newFilter == filtersArray[i]) {
                filtersArray.splice(i, 1);
                break;
            }
        }
    }
    else { // Add category to filters
        filtersArray.push(newFilter);
    }
    oSelf._filterLogs();
};

/**
 * Handles check events on the category filter checkboxes.
 *
 * @method _onCheckSource
 * @param v {HTMLEvent} The click event.
 * @param oSelf {Object} The log reader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onCheckSource = function(v, oSelf) {
    var newFilter = this.source;
    var filtersArray = oSelf._sourceFilters;

    if(!this.checked) { // Remove category from filters
        for(var i=0; i<filtersArray.length; i++) {
            if(newFilter == filtersArray[i]) {
                filtersArray.splice(i, 1);
                break;
            }
        }
    }
    else { // Add category to filters
        filtersArray.push(newFilter);
    }
    oSelf._filterLogs();
};

/**
 * Handles click events on the collapse button.
 *
 * @method _onClickCollapseBtn
 * @param v {HTMLEvent} The click event.
 * @param oSelf {Object} The LogReader instance
 * @private
 */
YAHOO.widget.LogReader.prototype._onClickCollapseBtn = function(v, oSelf) {
    var btn = oSelf._btnCollapse;
    if(btn.value == "Expand") {
        oSelf._elConsole.style.display = "block";
        if(oSelf._elFt) {
            oSelf._elFt.style.display = "block";
        }
        btn.value = "Collapse";
    }
    else {
        oSelf._elConsole.style.display = "none";
        if(oSelf._elFt) {
            oSelf._elFt.style.display = "none";
        }
        btn.value = "Expand";
    }
};

/**
 * Handles click events on the pause button.
 *
 * @method _onClickPauseBtn
 * @param v {HTMLEvent} The click event.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onClickPauseBtn = function(v, oSelf) {
    var btn = oSelf._btnPause;
    if(btn.value == "Resume") {
        oSelf.resume();
        btn.value = "Pause";
    }
    else {
        oSelf.pause();
        btn.value = "Resume";
    }
};

/**
 * Handles click events on the clear button.
 *
 * @method _onClickClearBtn
 * @param v {HTMLEvent} The click event.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onClickClearBtn = function(v, oSelf) {
    oSelf._clearConsole();
};

/**
 * Handles Logger's newLogEvent.
 *
 * @method _onNewLog
 * @param sType {String} The event.
 * @param aArgs {Object[]} Data passed from event firer.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onNewLog = function(sType, aArgs, oSelf) {
    var logEntry = aArgs[0];
    oSelf._buffer.push(logEntry);

    if (oSelf.logReaderEnabled === true && oSelf._timeout === null) {
        oSelf._timeout = setTimeout(function(){oSelf._printBuffer();}, 100);
    }
};

/**
 * Handles Logger's resetEvent.
 *
 * @method _onReset
 * @param sType {String} The event.
 * @param aArgs {Object[]} Data passed from event firer.
 * @param oSelf {Object} The LogReader instance.
 * @private
 */
YAHOO.widget.LogReader.prototype._onReset = function(sType, aArgs, oSelf) {
    oSelf._filterLogs();
};
 /**
 * The Logger widget provides a simple way to read or write log messages in
 * JavaScript code. Integration with the YUI Library's debug builds allow
 * implementers to access under-the-hood events, errors, and debugging messages.
 * Output may be read through a LogReader console and/or output to a browser
 * console.
 *
 * @module logger
 * @requires yahoo, event, dom
 * @optional dragdrop
 * @namespace YAHOO.widget
 * @title Logger Widget
 */

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The singleton Logger class provides core log management functionality. Saves
 * logs written through the global YAHOO.log function or written by a LogWriter
 * instance. Provides access to logs for reading by a LogReader instance or
 * native browser console such as the Firebug extension to Firefox or Safari's
 * JavaScript console through integration with the console.log() method.
 *
 * @class Logger
 * @static
 */
YAHOO.widget.Logger = {
    // Initialize members
    loggerEnabled: true,
    _browserConsoleEnabled: false,
    categories: ["info","warn","error","time","window"],
    sources: ["global"],
    _stack: [], // holds all log msgs
    maxStackEntries: 2500,
    _startTime: new Date().getTime(), // static start timestamp
    _lastTime: null // timestamp of last logged message
};

/////////////////////////////////////////////////////////////////////////////
//
// Public methods
//
/////////////////////////////////////////////////////////////////////////////
/**
 * Saves a log message to the stack and fires newLogEvent. If the log message is
 * assigned to an unknown category, creates a new category. If the log message is
 * from an unknown source, creates a new source.  If browser console is enabled,
 * outputs the log message to browser console.
 *
 * @method log
 * @param sMsg {String} The log message.
 * @param sCategory {String} Category of log message, or null.
 * @param sSource {String} Source of LogWriter, or null if global.
 */
YAHOO.widget.Logger.log = function(sMsg, sCategory, sSource) {
    if(this.loggerEnabled) {
        if(!sCategory) {
            sCategory = "info"; // default category
        }
        else {
            sCategory = sCategory.toLocaleLowerCase();
            if(this._isNewCategory(sCategory)) {
                this._createNewCategory(sCategory);
            }
        }
        var sClass = "global"; // default source
        var sDetail = null;
        if(sSource) {
            var spaceIndex = sSource.indexOf(" ");
            if(spaceIndex > 0) {
                // Substring until first space
                sClass = sSource.substring(0,spaceIndex);
                // The rest of the source
                sDetail = sSource.substring(spaceIndex,sSource.length);
            }
            else {
                sClass = sSource;
            }
            if(this._isNewSource(sClass)) {
                this._createNewSource(sClass);
            }
        }

        var timestamp = new Date();
        var logEntry = new YAHOO.widget.LogMsg({
            msg: sMsg,
            time: timestamp,
            category: sCategory,
            source: sClass,
            sourceDetail: sDetail
        });

        var stack = this._stack;
        var maxStackEntries = this.maxStackEntries;
        if(maxStackEntries && !isNaN(maxStackEntries) &&
            (stack.length >= maxStackEntries)) {
            stack.shift();
        }
        stack.push(logEntry);
        this.newLogEvent.fire(logEntry);

        if(this._browserConsoleEnabled) {
            this._printToBrowserConsole(logEntry);
        }
        return true;
    }
    else {
        return false;
    }
};

/**
 * Resets internal stack and startTime, enables Logger, and fires logResetEvent.
 *
 * @method reset
 */
YAHOO.widget.Logger.reset = function() {
    this._stack = [];
    this._startTime = new Date().getTime();
    this.loggerEnabled = true;
    this.log("Logger reset");
    this.logResetEvent.fire();
};

/**
 * Public accessor to internal stack of log message objects.
 *
 * @method getStack
 * @return {Object[]} Array of log message objects.
 */
YAHOO.widget.Logger.getStack = function() {
    return this._stack;
};

/**
 * Public accessor to internal start time.
 *
 * @method getStartTime
 * @return {Date} Internal date of when Logger singleton was initialized.
 */
YAHOO.widget.Logger.getStartTime = function() {
    return this._startTime;
};

/**
 * Disables output to the browser's global console.log() function, which is used
 * by the Firebug extension to Firefox as well as Safari.
 *
 * @method disableBrowserConsole
 */
YAHOO.widget.Logger.disableBrowserConsole = function() {
    YAHOO.log("Logger output to the function console.log() has been disabled.");
    this._browserConsoleEnabled = false;
};

/**
 * Enables output to the browser's global console.log() function, which is used
 * by the Firebug extension to Firefox as well as Safari.
 *
 * @method enableBrowserConsole
 */
YAHOO.widget.Logger.enableBrowserConsole = function() {
    this._browserConsoleEnabled = true;
    YAHOO.log("Logger output to the function console.log() has been enabled.");
};

/////////////////////////////////////////////////////////////////////////////
//
// Public events
//
/////////////////////////////////////////////////////////////////////////////

 /**
 * Fired when a new category has been created.
 *
 * @event categoryCreateEvent
 * @param sCategory {String} Category name.
 */
YAHOO.widget.Logger.categoryCreateEvent =
    new YAHOO.util.CustomEvent("categoryCreate", this, true);

 /**
 * Fired when a new source has been named.
 *
 * @event sourceCreateEvent
 * @param sSource {String} Source name.
 */
YAHOO.widget.Logger.sourceCreateEvent =
    new YAHOO.util.CustomEvent("sourceCreate", this, true);

 /**
 * Fired when a new log message has been created.
 *
 * @event newLogEvent
 * @param sMsg {String} Log message.
 */
YAHOO.widget.Logger.newLogEvent = new YAHOO.util.CustomEvent("newLog", this, true);

/**
 * Fired when the Logger has been reset has been created.
 *
 * @event logResetEvent
 */
YAHOO.widget.Logger.logResetEvent = new YAHOO.util.CustomEvent("logReset", this, true);

/////////////////////////////////////////////////////////////////////////////
//
// Private methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Creates a new category of log messages and fires categoryCreateEvent.
 *
 * @method _createNewCategory
 * @param sCategory {String} Category name.
 * @private
 */
YAHOO.widget.Logger._createNewCategory = function(sCategory) {
    this.categories.push(sCategory);
    this.categoryCreateEvent.fire(sCategory);
};

/**
 * Checks to see if a category has already been created.
 *
 * @method _isNewCategory
 * @param sCategory {String} Category name.
 * @return {Boolean} Returns true if category is unknown, else returns false.
 * @private
 */
YAHOO.widget.Logger._isNewCategory = function(sCategory) {
    for(var i=0; i < this.categories.length; i++) {
        if(sCategory == this.categories[i]) {
            return false;
        }
    }
    return true;
};

/**
 * Creates a new source of log messages and fires sourceCreateEvent.
 *
 * @method _createNewSource
 * @param sSource {String} Source name.
 * @private
 */
YAHOO.widget.Logger._createNewSource = function(sSource) {
    this.sources.push(sSource);
    this.sourceCreateEvent.fire(sSource);
};

/**
 * Checks to see if a source already exists.
 *
 * @method _isNewSource
 * @param sSource {String} Source name.
 * @return {Boolean} Returns true if source is unknown, else returns false.
 * @private
 */
YAHOO.widget.Logger._isNewSource = function(sSource) {
    if(sSource) {
        for(var i=0; i < this.sources.length; i++) {
            if(sSource == this.sources[i]) {
                return false;
            }
        }
        return true;
    }
};

/**
 * Outputs a log message to global console.log() function.
 *
 * @method _printToBrowserConsole
 * @param oEntry {Object} Log entry object.
 * @private
 */
YAHOO.widget.Logger._printToBrowserConsole = function(oEntry) {
    if(window.console && console.log) {
        var category = oEntry.category;
        var label = oEntry.category.substring(0,4).toUpperCase();

        var time = oEntry.time;
        if (time.toLocaleTimeString) {
            var localTime  = time.toLocaleTimeString();
        }
        else {
            localTime = time.toString();
        }

        var msecs = time.getTime();
        var elapsedTime = (YAHOO.widget.Logger._lastTime) ?
            (msecs - YAHOO.widget.Logger._lastTime) : 0;
        YAHOO.widget.Logger._lastTime = msecs;

        var output =
            localTime + " (" +
            elapsedTime + "ms): " +
            oEntry.source + ": " +
            oEntry.msg;

        console.log(output);
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Private event handlers
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Handles logging of messages due to window error events.
 *
 * @method _onWindowError
 * @param sMsg {String} The error message.
 * @param sUrl {String} URL of the error.
 * @param sLine {String} Line number of the error.
 * @private
 */
YAHOO.widget.Logger._onWindowError = function(sMsg,sUrl,sLine) {
    // Logger is not in scope of this event handler
    try {
        YAHOO.widget.Logger.log(sMsg+' ('+sUrl+', line '+sLine+')', "window");
        if(YAHOO.widget.Logger._origOnWindowError) {
            YAHOO.widget.Logger._origOnWindowError();
        }
    }
    catch(e) {
        return false;
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Enable handling of native JavaScript errors
// NB: Not all browsers support the window.onerror event
//
/////////////////////////////////////////////////////////////////////////////

if(window.onerror) {
    // Save any previously defined handler to call
    YAHOO.widget.Logger._origOnWindowError = window.onerror;
}
window.onerror = YAHOO.widget.Logger._onWindowError;

/////////////////////////////////////////////////////////////////////////////
//
// First log
//
/////////////////////////////////////////////////////////////////////////////

YAHOO.widget.Logger.log("Logger initialized");

