/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.5.0
*/
/**
 * Mechanism to execute a series of callbacks in a non-blocking queue.  Each callback is executed via setTimout unless configured with a negative timeout, in which case it is run in blocking mode in the same execution thread as the previous callback.  Callbacks can be function references or object literals with the following keys:
 * <ul>
 *    <li><code>method</code> - {Function} REQUIRED the callback function.</li>
 *    <li><code>scope</code> - {Object} the scope from which to execute the callback.  Default is the global window scope.</li>
 *    <li><code>argument</code> - {Array} parameters to be passed to method as individual arguments.</li>
 *    <li><code>timeout</code> - {number} millisecond delay to wait after previous callback completion before executing this callback.  Negative values cause immediate blocking execution.  Default 0.</li>
 *    <li><code>until</code> - {Function} boolean function executed before each iteration.  Return true to indicate completion and proceed to the next callback.</li>
 *    <li><code>iterations</code> - {Number} number of times to execute the callback before proceeding to the next callback in the chain. Incompatible with <code>until</code>.</li>
 * </ul>
 *
 * @namespace YAHOO.util
 * @class Chain
 * @constructor
 * @param callback* {Function|Object} Any number of callbacks to initialize the queue
*/
YAHOO.util.Chain = function () {
    /**
     * The callback queue
     * @property q
     * @type {Array}
     * @private
     */
    this.q = [].slice.call(arguments);
};

YAHOO.util.Chain.prototype = {
    /**
     * Timeout id used to pause or stop execution and indicate the execution state of the Chain.  0 indicates paused or stopped, -1 indicates blocking execution, and any positive number indicates non-blocking execution.
     * @property id
     * @type {number}
     * @private
     */
    id   : 0,

    /**
     * Begin executing the chain, or resume execution from the last paused position.
     * @method run
     * @return {Chain} the Chain instance
     */
    run : function () {
        // Grab the first callback in the queue
        var c  = this.q[0],
            fn;

        // If there is no callback in the queue or the Chain is currently
        // in an execution mode, return
        if (!c || this.id) {
            return this;
        }

        fn = c.method || c;

        if (typeof fn === 'function') {
            var o    = c.scope || {},
                args = c.argument || [],
                ms   = c.timeout || 0,
                me   = this;
                
            if (!(args instanceof Array)) {
                args = [args];
            }

            // Execute immediately if the callback timeout is negative.
            if (ms < 0) {
                this.id = ms;
                if (c.until) {
                    for (;!c.until();) {
                        // Execute the callback from scope, with argument
                        fn.apply(o,args);
                    }
                } else if (c.iterations) {
                    for (;c.iterations-- > 0;) {
                        fn.apply(o,args);
                    }
                } else {
                    fn.apply(o,args);
                }
                this.q.shift();
                this.id = 0;
                return this.run();
            } else {
                // If the until condition is set, check if we're done
                if (c.until) {
                    if (c.until()) {
                        // Shift this callback from the queue and execute the next
                        // callback
                        this.q.shift();
                        return this.run();
                    }
                // Otherwise if either iterations is not set or we're
                // executing the last iteration, shift callback from the queue
                } else if (!c.iterations || !--c.iterations) {
                    this.q.shift();
                }

                // Otherwise set to execute after the configured timeout
                this.id = setTimeout(function () {
                    // Execute the callback from scope, with argument
                    fn.apply(o,args);
                    // Check if the Chain was not paused from inside the callback
                    if (me.id) {
                        // Indicate ready to run state
                        me.id = 0;
                        // Start the fun all over again
                        me.run();
                    }
                },ms);
            }
        }

        return this;
    },
    
    /**
     * Add a callback to the end of the queue
     * @method add
     * @param c {Function|Object} the callback function ref or object literal
     * @return {Chain} the Chain instance
     */
    add  : function (c) {
        this.q.push(c);
        return this;
    },

    /**
     * Pause the execution of the Chain after the current execution of the
     * current callback completes.  If called interstitially, clears the
     * timeout for the pending callback. Paused Chains can be restarted with
     * chain.run()
     * @method pause
     * @return {Chain} the Chain instance
     */
    pause: function () {
        clearTimeout(this.id);
        this.id = 0;
        return this;
    },

    /**
     * Stop and clear the Chain's queue after the current execution of the
     * current callback completes.
     * @method stop
     * @return {Chain} the Chain instance
     */
    stop : function () { 
        this.pause();
        this.q = [];
        return this;
    }
};

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The ColumnSet class defines and manages a DataTable's Columns,
 * including nested hierarchies and access to individual Column instances.
 *
 * @namespace YAHOO.widget
 * @class ColumnSet
 * @uses YAHOO.util.EventProvider
 * @constructor
 * @param aDefinitions {Object[]} Array of object literals that define cells in
 * the THEAD.
 */
YAHOO.widget.ColumnSet = function(aDefinitions) {
    this._sId = "yui-cs" + YAHOO.widget.ColumnSet._nCount;

    // First clone the defs
    aDefinitions = YAHOO.widget.DataTable._cloneObject(aDefinitions);
    this._init(aDefinitions);

    YAHOO.widget.ColumnSet._nCount++;
    YAHOO.log("ColumnSet initialized", "info", this.toString());
};

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Internal class variable to index multiple ColumnSet instances.
 *
 * @property ColumnSet._nCount
 * @type Number
 * @private
 * @static
 */
YAHOO.widget.ColumnSet._nCount = 0;

YAHOO.widget.ColumnSet.prototype = {
    /**
     * Unique instance name.
     *
     * @property _sId
     * @type String
     * @private
     */
    _sId : null,

    /**
     * Array of object literal Column definitions passed to the constructor.
     *
     * @property _aDefinitions
     * @type Object[]
     * @private
     */
    _aDefinitions : null,

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public member variables
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Top-down tree representation of Column hierarchy.
     *
     * @property tree
     * @type YAHOO.widget.Column[]
     */
    tree : null,

    /**
     * Flattened representation of all Columns.
     *
     * @property flat
     * @type YAHOO.widget.Column[]
     * @default []
     */
    flat : null,

    /**
     * Array of Columns that map one-to-one to a table column.
     *
     * @property keys
     * @type YAHOO.widget.Column[]
     * @default []
     */
    keys : null,

    /**
     * ID index of nested parent hierarchies for HEADERS accessibility attribute.
     *
     * @property headers
     * @type String[]
     * @default []
     */
    headers : null,

    /////////////////////////////////////////////////////////////////////////////
    //
    // Private methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes ColumnSet instance with data from Column definitions.
     *
     * @method _init
     * @param aDefinitions {Object[]} Array of object literals that define cells in
     * the THEAD .
     * @private
     */

    _init : function(aDefinitions) {        
        // DOM tree representation of all Columns
        var tree = [];
        // Flat representation of all Columns
        var flat = [];
        // Flat representation of only Columns that are meant to display data
        var keys = [];
        // Array of HEADERS attribute values for all keys in the "keys" array
        var headers = [];

        // Tracks current node list depth being tracked
        var nodeDepth = -1;

        // Internal recursive function to define Column instances
        var parseColumns = function(nodeList, parent) {
            // One level down
            nodeDepth++;

            // Create corresponding tree node if not already there for this depth
            if(!tree[nodeDepth]) {
                tree[nodeDepth] = [];
            }


            // Parse each node at this depth for attributes and any children
            for(var j=0; j<nodeList.length; j++) {
                var currentNode = nodeList[j];

                // Instantiate a new Column for each node
                var oColumn = new YAHOO.widget.Column(currentNode);
                
                // Assign unique ID to Column and cross-reference it back to the
                // original object literal definition
                currentNode.yuiColumnId = oColumn._sId = YAHOO.widget.Column._nCount + "";
                
                // Assign a key if not found
                if(!YAHOO.lang.isValue(oColumn.key)) {
                    oColumn.key = "yui-dt-col" + YAHOO.widget.Column._nCount;
                }
                // Increment counter
                YAHOO.widget.Column._nCount++;

                // Add the new Column to the flat list
                flat.push(oColumn);

                // Assign its parent as an attribute, if applicable
                if(parent) {
                    oColumn.parent = parent;
                }

                // The Column has descendants
                if(YAHOO.lang.isArray(currentNode.children)) {
                    oColumn.children = currentNode.children;

                    // Determine COLSPAN value for this Column
                    var terminalChildNodes = 0;
                    var countTerminalChildNodes = function(ancestor) {
                        var descendants = ancestor.children;
                        // Drill down each branch and count terminal nodes
                        for(var k=0; k<descendants.length; k++) {
                            // Keep drilling down
                            if(YAHOO.lang.isArray(descendants[k].children)) {
                                countTerminalChildNodes(descendants[k]);
                            }
                            // Reached branch terminus
                            else {
                                terminalChildNodes++;
                            }
                        }
                    };
                    countTerminalChildNodes(currentNode);
                    oColumn._nColspan = terminalChildNodes;

                    // Cascade certain properties to children if not defined on their own
                    var currentChildren = currentNode.children;
                    for(var k=0; k<currentChildren.length; k++) {
                        var child = currentChildren[k];
                        if(oColumn.className && (child.className === undefined)) {
                            child.className = oColumn.className;
                        }
                        if(oColumn.editor && (child.editor === undefined)) {
                            child.editor = oColumn.editor;
                        }
                        if(oColumn.editorOptions && (child.editorOptions === undefined)) {
                            child.editorOptions = oColumn.editorOptions;
                        }
                        if(oColumn.formatter && (child.formatter === undefined)) {
                            child.formatter = oColumn.formatter;
                        }
                        if(oColumn.resizeable && (child.resizeable === undefined)) {
                            child.resizeable = oColumn.resizeable;
                        }
                        if(oColumn.sortable && (child.sortable === undefined)) {
                            child.sortable = oColumn.sortable;
                        }
                        if(oColumn.width && (child.width === undefined)) {
                            child.width = oColumn.width;
                        }
                        // Backward compatibility
                        if(oColumn.type && (child.type === undefined)) {
                            child.type = oColumn.type;
                        }
                        if(oColumn.type && !oColumn.formatter) {
                            YAHOO.log("The property type has been" +
                            " deprecated in favor of formatter", "warn", oColumn.toString());
                            oColumn.formatter = oColumn.type;
                        }
                        if(oColumn.text && !YAHOO.lang.isValue(oColumn.label)) {
                            YAHOO.log("The property text has been" +
                            " deprecated in favor of label", "warn", oColumn.toString());
                            oColumn.label = oColumn.text;
                        }
                        if(oColumn.parser) {
                            YAHOO.log("The property parser is no longer supported",
                            "warn", this.toString());
                        }
                        if(oColumn.sortOptions && ((oColumn.sortOptions.ascFunction) ||
                                (oColumn.sortOptions.descFunction))) {
                            YAHOO.log("The properties sortOptions.ascFunction and " +
                            " sortOptions.descFunction have been deprecated in favor " +
                            " of sortOptions.sortFunction", "warn", oColumn.toString());
                        }
                    }

                    // The children themselves must also be parsed for Column instances
                    if(!tree[nodeDepth+1]) {
                        tree[nodeDepth+1] = [];
                    }
                    parseColumns(currentChildren, oColumn);
                }
                // This Column does not have any children
                else {
                    oColumn._nKeyIndex = keys.length;
                    oColumn._nColspan = 1;
                    keys.push(oColumn);
                }

                // Add the Column to the top-down tree
                tree[nodeDepth].push(oColumn);
            }
            nodeDepth--;
        };

        // Parse out Column instances from the array of object literals
        if(YAHOO.lang.isArray(aDefinitions)) {
            parseColumns(aDefinitions);

            // Store the array
            this._aDefinitions = aDefinitions;
        }
        else {
            YAHOO.log("Could not initialize ColumnSet due to invalid definitions","error");
            return null;
        }

        var i;

        // Determine ROWSPAN value for each Column in the tree
        var parseTreeForRowspan = function(tree) {
            var maxRowDepth = 1;
            var currentRow;
            var currentColumn;

            // Calculate the max depth of descendants for this row
            var countMaxRowDepth = function(row, tmpRowDepth) {
                tmpRowDepth = tmpRowDepth || 1;

                for(var n=0; n<row.length; n++) {
                    var col = row[n];
                    // Column has children, so keep counting
                    if(YAHOO.lang.isArray(col.children)) {
                        tmpRowDepth++;
                        countMaxRowDepth(col.children, tmpRowDepth);
                        tmpRowDepth--;
                    }
                    // No children, is it the max depth?
                    else {
                        if(tmpRowDepth > maxRowDepth) {
                            maxRowDepth = tmpRowDepth;
                        }
                    }

                }
            };

            // Count max row depth for each row
            for(var m=0; m<tree.length; m++) {
                currentRow = tree[m];
                countMaxRowDepth(currentRow);

                // Assign the right ROWSPAN values to each Column in the row
                for(var p=0; p<currentRow.length; p++) {
                    currentColumn = currentRow[p];
                    if(!YAHOO.lang.isArray(currentColumn.children)) {
                        currentColumn._nRowspan = maxRowDepth;
                    }
                    else {
                        currentColumn._nRowspan = 1;
                    }
                }

                // Reset counter for next row
                maxRowDepth = 1;
            }
        };
        parseTreeForRowspan(tree);

        // Store tree index values
        for(i=0; i<tree[0].length; i++) {
            tree[0][i]._nTreeIndex = i;
        }

        // Store header relationships in an array for HEADERS attribute
        var recurseAncestorsForHeaders = function(i, oColumn) {
            headers[i].push(oColumn._sId);
            if(oColumn.parent) {
                recurseAncestorsForHeaders(i, oColumn.parent);
            }
        };
        for(i=0; i<keys.length; i++) {
            headers[i] = [];
            recurseAncestorsForHeaders(i, keys[i]);
            headers[i] = headers[i].reverse();
        }

        // Save to the ColumnSet instance
        this.tree = tree;
        this.flat = flat;
        this.keys = keys;
        this.headers = headers;
    },

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Returns unique name of the ColumnSet instance.
     *
     * @method getId
     * @return {String} Unique name of the ColumnSet instance.
     */

    getId : function() {
        return this._sId;
    },

    /**
     * ColumnSet instance name, for logging.
     *
     * @method toString
     * @return {String} Unique name of the ColumnSet instance.
     */

    toString : function() {
        return "ColumnSet instance " + this._sId;
    },

    /**
     * Public accessor to the definitions array.
     *
     * @method getDefinitions
     * @return {Object[]} Array of object literal Column definitions.
     */

    getDefinitions : function() {
        var aDefinitions = this._aDefinitions;
        
        // Internal recursive function to define Column instances
        var parseColumns = function(nodeList, oSelf) {
            // Parse each node at this depth for attributes and any children
            for(var j=0; j<nodeList.length; j++) {
                var currentNode = nodeList[j];
                
                // Get the Column for each node
                var oColumn = oSelf.getColumnById(currentNode.yuiColumnId);
                
                if(oColumn) {    
                    // Update the definition
                    currentNode.abbr = oColumn.abbr;
                    currentNode.className = oColumn.className;
                    currentNode.editor = oColumn.editor;
                    currentNode.editorOptions = oColumn.editorOptions;
                    currentNode.formatter = oColumn.formatter;
                    currentNode.hidden = oColumn.hidden;
                    currentNode.key = oColumn.key;
                    currentNode.label = oColumn.label;
                    currentNode.minWidth = oColumn.minWidth;
                    currentNode.resizeable = oColumn.resizeable;
                    currentNode.selected = oColumn.selected;
                    currentNode.sortable = oColumn.sortable;
                    currentNode.sortOptions = oColumn.sortOptions;
                    currentNode.width = oColumn.width;
                }
                            
                // The Column has descendants
                if(YAHOO.lang.isArray(currentNode.children)) {
                    // The children themselves must also be parsed for Column instances
                    parseColumns(currentNode.children, oSelf);
                }
            }
        };

        parseColumns(aDefinitions, this);
        this._aDefinitions = aDefinitions;
        return aDefinitions;
    },

    /**
     * Returns Column instance with given ID.
     *
     * @method getColumnById
     * @param column {String} Column ID.
     * @return {YAHOO.widget.Column} Column instance.
     */

    getColumnById : function(column) {
        if(YAHOO.lang.isString(column)) {
            var allColumns = this.flat;
            for(var i=allColumns.length-1; i>-1; i--) {
                if(allColumns[i]._sId === column) {
                    return allColumns[i];
                }
            }
        }
        return null;
    },

    /**
     * Returns Column instance with given key or ColumnSet key index.
     *
     * @method getColumn
     * @param column {String | Number} Column key or ColumnSet key index.
     * @return {YAHOO.widget.Column} Column instance.
     */

    getColumn : function(column) {
        if(YAHOO.lang.isNumber(column) && this.keys[column]) {
            return this.keys[column];
        }
        else if(YAHOO.lang.isString(column)) {
            var allColumns = this.flat;
            var aColumns = [];
            for(var i=0; i<allColumns.length; i++) {
                if(allColumns[i].key === column) {
                    aColumns.push(allColumns[i]);
                }
            }
            if(aColumns.length === 1) {
                return aColumns[0];
            }
            else if(aColumns.length > 1) {
                return aColumns;
            }
        }
        return null;
    },

    /**
     * Public accessor returns array of given Column's desendants (if any), including itself.
     *
     * @method getDescendants
     * @parem {YAHOO.widget.Column} Column instance.
     * @return {Array} Array including the Column itself and all descendants (if any).
     */
    getDescendants : function(oColumn) {
        var oSelf = this;
        var allDescendants = [];
        var i;

        // Recursive function to loop thru all children
        var parse = function(oParent) {
            allDescendants.push(oParent);
            // This Column has children
            if(oParent.children) {
                for(i=0; i<oParent.children.length; i++) {
                    parse(oSelf.getColumn(oParent.children[i].key));
                }
            }
        };
        parse(oColumn);

        return allDescendants;
    }
};

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The Column class defines and manages attributes of DataTable Columns
 *
 * @namespace YAHOO.widget
 * @class Column
 * @constructor
 * @param oConfigs {Object} Object literal of definitions.
 */
YAHOO.widget.Column = function(oConfigs) {
    // Object literal defines Column attributes
    if(oConfigs && (oConfigs.constructor == Object)) {
        for(var sConfig in oConfigs) {
            if(sConfig) {
                this[sConfig] = oConfigs[sConfig];
            }
        }
   }
    // Backward compatibility
    if(this.width && !YAHOO.lang.isNumber(this.width)) {
        this.width = null;
        YAHOO.log("The Column property width must be a number", "warn", this.toString());
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

YAHOO.lang.augmentObject(YAHOO.widget.Column, {
    /**
     * Internal class variable to index multiple Column instances.
     *
     * @property Column._nCount
     * @type Number
     * @private
     * @static
     */
    _nCount : 0,

    formatCheckbox : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatCheckbox() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatCheckbox()", "warn",
        "YAHOO.widget.Column.formatCheckbox");
        YAHOO.widget.DataTable.formatCheckbox(elCell, oRecord, oColumn, oData);
    },

    formatCurrency : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatCurrency() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatCurrency()", "warn",
        "YAHOO.widget.Column.formatCurrency");
        YAHOO.widget.DataTable.formatCurrency(elCell, oRecord, oColumn, oData);
    },

    formatDate : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatDate() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatDate()", "warn",
        "YAHOO.widget.Column.formatDate");
        YAHOO.widget.DataTable.formatDate(elCell, oRecord, oColumn, oData);
    },

    formatEmail : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatEmail() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatEmail()", "warn",
        "YAHOO.widget.Column.formatEmail");
        YAHOO.widget.DataTable.formatEmail(elCell, oRecord, oColumn, oData);
    },

    formatLink : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatLink() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatLink()", "warn",
        "YAHOO.widget.Column.formatLink");
        YAHOO.widget.DataTable.formatLink(elCell, oRecord, oColumn, oData);
    },

    formatNumber : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatNumber() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatNumber()", "warn",
        "YAHOO.widget.Column.formatNumber");
        YAHOO.widget.DataTable.formatNumber(elCell, oRecord, oColumn, oData);
    },

    formatSelect : function(elCell, oRecord, oColumn, oData) {
        YAHOO.log("The method YAHOO.widget.Column.formatSelect() has been" +
        " deprecated in favor of YAHOO.widget.DataTable.formatDropdown()", "warn",
        "YAHOO.widget.Column.formatSelect");
        YAHOO.widget.DataTable.formatDropdown(elCell, oRecord, oColumn, oData);
    }
});

YAHOO.widget.Column.prototype = {
    /**
     * Unique String identifier assigned at instantiation.
     *
     * @property _sId
     * @type String
     * @private
     */
    _sId : null,

    /**
     * Object literal definition
     *
     * @property _oDefinition
     * @type Object
     * @private
     */
    _oDefinition : null,

    /**
     * Reference to Column's current position index within its ColumnSet's keys
     * array, if applicable. This property only applies to non-nested and bottom-
     * level child Columns.
     *
     * @property _nKeyIndex
     * @type Number
     * @private
     */
    _nKeyIndex : null,

    /**
     * Reference to Column's current position index within its ColumnSet's tree
     * array, if applicable. This property only applies to non-nested and top-
     * level parent Columns.
     *
     * @property _nTreeIndex
     * @type Number
     * @private
     */
    _nTreeIndex : null,

    /**
     * Number of table cells the Column spans.
     *
     * @property _nColspan
     * @type Number
     * @private
     */
    _nColspan : 1,

    /**
     * Number of table rows the Column spans.
     *
     * @property _nRowspan
     * @type Number
     * @private
     */
    _nRowspan : 1,

    /**
     * Column's parent Column instance, or null.
     *
     * @property _oParent
     * @type YAHOO.widget.Column
     * @private
     */
    _oParent : null,

    /*TODO: remove
     * The DOM reference the associated COL element.
     *
     * @property _elCol
     * @type HTMLElement
     * @private
     */
    //YAHOO.widget.Column.prototype._elCol = null;

    /**
     * The DOM reference to the associated TH element.
     *
     * @property _elTh
     * @type HTMLElement
     * @private
     */
    _elTh : null,

    /**
     * The DOM reference to the associated resizerelement (if any).
     *
     * @property _elResizer
     * @type HTMLElement
     * @private
     */
    _elResizer : null,

    /**
     * For unreg() purposes, a reference to the Column's DragDrop instance.
     *
     * @property _dd
     * @type YAHOO.util.DragDrop
     * @private
     */
    _dd : null,

    /**
     * For unreg() purposes, a reference to the Column resizer's DragDrop instance.
     *
     * @property _ddResizer
     * @type YAHOO.util.DragDrop
     * @private
     */
    _ddResizer : null,

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public member variables
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Associated database field, or null.
     *
     * @property key
     * @type String
     */
    key : null,

    /**
     * Text or HTML for display as Column's label in the TH element.
     *
     * @property label
     * @type String
     */
    label : null,

    /**
     * Column head cell ABBR for accessibility.
     *
     * @property abbr
     * @type String
     */
    abbr : null,

    /**
     * Array of object literals that define children (nested headers) of a Column.
     *
     * @property children
     * @type Object[]
     */
    children : null,

    /**
     * Column width (in pixels).
     *
     * @property width
     * @type Number
     */
    width : null,

    /**
     * Minimum Column width (in pixels).
     *
     * @property minWidth
     * @type Number
     * @default 10
     */
    minWidth : 10,

    /**
     * True if Column is in hidden state.
     *
     * @property hidden
     * @type Boolean
     * @default false     
     */
    hidden : false,

    /**
     * True if Column is in selected state.
     *
     * @property selected
     * @type Boolean
     * @default false     
     */
    selected : false,

    /**
     * Custom CSS class or array of classes to be applied to every cell in the Column.
     *
     * @property className
     * @type String || String[]
     */
    className : null,

    /**
     * Defines a format function.
     *
     * @property formatter
     * @type String || HTMLFunction
     */
    formatter : null,

    /**
     * Defines an editor function, otherwise Column is not editable.
     *
     * @property editor
     * @type String || HTMLFunction
     */
    editor : null,

    /**
     * Defines editor options for Column in an object literal of param:value pairs.
     *
     * @property editorOptions
     * @type Object
     */
    editorOptions : null,

    /**
     * True if Column is resizeable, false otherwise. The Drag & Drop Utility is
     * required to enable this feature. Only bottom-level and non-nested Columns are
     * resizeble. 
     *
     * @property resizeable
     * @type Boolean
     * @default false
     */
    resizeable : false,

    /**
     * True if Column is sortable, false otherwise.
     *
     * @property sortable
     * @type Boolean
     * @default false
     */
    sortable : false,

    /**
     * @property sortOptions.defaultOrder
     * @deprecated Use sortOptions.defaultDir.
     */
    /**
     * Default sort direction for Column: YAHOO.widget.DataTable.CLASS_ASC or YAHOO.widget.DataTable.CLASS_DESC.
     *
     * @property sortOptions.defaultDir
     * @type String
     * @default null
     */
    /**
     * Custom sort handler.
     *
     * @property sortOptions.sortFunction
     * @type Function
     * @default null
     */
    sortOptions : null,















    /////////////////////////////////////////////////////////////////////////////
    //
    // Public methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Returns unique ID string.
     *
     * @method getId
     * @return {String} Unique ID string.
     */
    getId : function() {
        return this._sId;
    },

    /**
     * Column instance name, for logging.
     *
     * @method toString
     * @return {String} Column's unique name.
     */
    toString : function() {
        return "Column instance " + this._sId;
    },

    /**
     * Returns object literal definition.
     *
     * @method getDefinition
     * @return {Object} Object literal definition.
     */
    getDefinition : function() {
        var oDefinition = this._oDefinition;
        
        // Update the definition
        oDefinition.abbr = this.abbr;
        oDefinition.className = this.className;
        oDefinition.editor = this.editor;
        oDefinition.editorOptions = this.editorOptions;
        oDefinition.formatter = this.formatter;
        oDefinition.key = this.key;
        oDefinition.label = this.label;
        oDefinition.minWidth = this.minWidth;
        oDefinition.resizeable = this.resizeable;
        oDefinition.sortable = this.sortable;
        oDefinition.sortOptions = this.sortOptions;
        oDefinition.width = this.width;

        return oDefinition;
    },

    /**
     * Returns unique Column key.
     *
     * @method getKey
     * @return {String} Column key.
     */
    getKey : function() {
        return this.key;
    },

    /**
     * Public accessor returns Column's current position index within its
     * ColumnSet's keys array, if applicable. Only non-nested and bottom-level
     * child Columns will return a value.
     *
     * @method getKeyIndex
     * @return {Number} Position index, or null.
     */
    getKeyIndex : function() {
        return this._nKeyIndex;
    },

    /**
     * Public accessor returns Column's current position index within its
     * ColumnSet's tree array, if applicable. Only non-nested and top-level parent
     * Columns will return a value;
     *
     * @method getTreeIndex
     * @return {Number} Position index, or null.
     */
    getTreeIndex : function() {
        return this._nTreeIndex;
    },

    /**
     * Public accessor returns Column's parent instance if any, or null otherwise.
     *
     * @method getParent
     * @return {YAHOO.widget.Column} Column's parent instance.
     */
    getParent : function() {
        return this._oParent;
    },

    /**
     * Public accessor returns Column's calculated COLSPAN value.
     *
     * @method getColspan
     * @return {Number} Column's COLSPAN value.
     */
    getColspan : function() {
        return this._nColspan;
    },
    // Backward compatibility
    getColSpan : function() {
        YAHOO.log("The method getColSpan() has been" +
        " deprecated in favor of getColspan()", "warn", this.toString());
        return this.getColspan();
    },

    /**
     * Public accessor returns Column's calculated ROWSPAN value.
     *
     * @method getRowspan
     * @return {Number} Column's ROWSPAN value.
     */
    getRowspan : function() {
        return this._nRowspan;
    },

    /**
     * Returns DOM reference to the key TH element.
     *
     * @method getThEl
     * @return {HTMLElement} TH element.
     */
    getThEl : function() {
        return this._elTh;
    },

    /**
     * Returns DOM reference to the resizer element, or null.
     *
     * @method getResizerEl
     * @return {HTMLElement} DIV element.
     */
    getResizerEl : function() {
        return this._elResizer;
    },

    // Backward compatibility
    /**
     * @method getColEl
     * @deprecated Use getThEl
     */
    getColEl : function() {
        YAHOO.log("The method getColEl() has been" +
        " deprecated in favor of getThEl()", "warn",
        this.toString());
        return this.getThEl();
    },
    getIndex : function() {
        YAHOO.log("The method getIndex() has been" +
        " deprecated in favor of getKeyIndex()", "warn",
        this.toString());
        return this.getKeyIndex();
    },
    format : function() {
        YAHOO.log("The method format() has been deprecated in favor of the " +
        "DataTable method formatCell()", "error", this.toString());
    }
};

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * Sort static utility to support Column sorting.
 *
 * @namespace YAHOO.util
 * @class Sort
 * @static
 */
YAHOO.util.Sort = {
    /////////////////////////////////////////////////////////////////////////////
    //
    // Public methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Comparator function for simple case-insensitive string sorting.
     *
     * @method compare
     * @param a {Object} First sort argument.
     * @param b {Object} Second sort argument.
     * @param desc {Boolean} True if sort direction is descending, false if
     * sort direction is ascending.
     */
    compare: function(a, b, desc) {
        if((a === null) || (typeof a == "undefined")) {
            if((b === null) || (typeof b == "undefined")) {
                return 0;
            }
            else {
                return 1;
            }
        }
        else if((b === null) || (typeof b == "undefined")) {
            return -1;
        }

        if(a.constructor == String) {
            a = a.toLowerCase();
        }
        if(b.constructor == String) {
            b = b.toLowerCase();
        }
        if(a < b) {
            return (desc) ? 1 : -1;
        }
        else if (a > b) {
            return (desc) ? -1 : 1;
        }
        else {
            return 0;
        }
    }
};

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * ColumnDD subclasses DragDrop to support rearrangeable Columns.
 *
 * @namespace YAHOO.util
 * @class ColumnDD
 * @extends YAHOO.util.DDProxy
 * @constructor
 * @param oDataTable {YAHOO.widget.DataTable} DataTable instance.
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param elTh {HTMLElement} TH element reference.
 * @param elTarget {HTMLElement} Drag target element.
 */
YAHOO.widget.ColumnDD = function(oDataTable, oColumn, elTh, elTarget) {
    if(oDataTable && oColumn && elTh && elTarget) {
        this.datatable = oDataTable;
        this.table = oDataTable.getTheadEl().parentNode;
        this.column = oColumn;
        this.headCell = elTh;
        this.pointer = elTarget;
        this.newIndex = null;
        this.init(elTh);
        this.initFrame(); // Needed for DDProxy
        this.invalidHandleTypes = {};

        //Set padding to account for children of nested columns
        this.setPadding(10, 0, (this.datatable.getTheadEl().offsetHeight + 10) , 0);
    }
    else {
        YAHOO.log("Column dragdrop could not be created","warn");
    }
};

if(YAHOO.util.DDProxy) {
    YAHOO.extend(YAHOO.widget.ColumnDD, YAHOO.util.DDProxy, {
        initConstraints: function() {
            //Get the top, right, bottom and left positions
            var region = YAHOO.util.Dom.getRegion(this.table),
                //Get the element we are working on
                el = this.getEl(),
                //Get the xy position of it
                xy = YAHOO.util.Dom.getXY(el),
                //Get the width and height
                width = parseInt(YAHOO.util.Dom.getStyle(el, 'width'), 10),
                height = parseInt(YAHOO.util.Dom.getStyle(el, 'height'), 10),
                //Set left to x minus left
                left = ((xy[0] - region.left) + 15), //Buffer of 15px
                //Set right to right minus x minus width
                right = ((region.right - xy[0] - width) + 15);
    
            //Set the constraints based on the above calculations
            this.setXConstraint(left, right);
            this.setYConstraint(10, 10);
            
            YAHOO.util.Event.on(window, 'resize', function() {
                this.initConstraints();
            }, this, true);
        },
        _resizeProxy: function() {
            this.constructor.superclass._resizeProxy.apply(this, arguments);
            var dragEl = this.getDragEl(),
                el = this.getEl();

            YAHOO.util.Dom.setStyle(this.pointer, 'height', (this.table.parentNode.offsetHeight + 10) + 'px');
            YAHOO.util.Dom.setStyle(this.pointer, 'display', 'block');
            var xy = YAHOO.util.Dom.getXY(el);
            YAHOO.util.Dom.setXY(this.pointer, [xy[0], (xy[1] - 5)]);
            
            YAHOO.util.Dom.setStyle(dragEl, 'height', this.datatable.getContainerEl().offsetHeight + "px");
            YAHOO.util.Dom.setStyle(dragEl, 'width', (parseInt(YAHOO.util.Dom.getStyle(dragEl, 'width'),10) + 4) + 'px');
            YAHOO.util.Dom.setXY(this.dragEl, xy);
        },
        onMouseDown: function() {
                this.initConstraints();
                this.resetConstraints();
        },
        clickValidator: function(e) {
            if(!this.column.hidden) {
                var target = YAHOO.util.Event.getTarget(e);
                return ( this.isValidHandleChild(target) &&
                            (this.id == this.handleElId ||
                                this.DDM.handleWasClicked(target, this.id)) );
            }
        },
        onDragOver: function(ev, id) {
            // Validate target
            var target = this.datatable.getColumn(id);
            if(target) {
                var mouseX = YAHOO.util.Event.getPageX(ev),
                targetX = YAHOO.util.Dom.getX(id),
                midX = targetX + ((YAHOO.util.Dom.get(id).offsetWidth)/2),
                currentIndex =  this.column.getTreeIndex(),
                targetIndex = target.getTreeIndex(),
                newIndex = targetIndex;
                
                
                if (mouseX < midX) {
                   YAHOO.util.Dom.setX(this.pointer, targetX);
                } else {
                    var thisWidth = parseInt(target.getThEl().offsetWidth, 10);
                    YAHOO.util.Dom.setX(this.pointer, (targetX + thisWidth));
                    newIndex++;
                }
                if (targetIndex > currentIndex) {
                    newIndex--;
                }
                if(newIndex < 0) {
                    newIndex = 0;
                }
                else if(newIndex > this.datatable.getColumnSet().tree[0].length) {
                    newIndex = this.datatable.getColumnSet().tree[0].length;
                }
                this.newIndex = newIndex;
            }
        },
        onDragDrop: function() {
            if(YAHOO.lang.isNumber(this.newIndex) && (this.newIndex !== this.column.getTreeIndex())) {
                var oDataTable = this.datatable;
                oDataTable._oChain.stop();
                var aColumnDefs = oDataTable._oColumnSet.getDefinitions();
                var oColumn = aColumnDefs.splice(this.column.getTreeIndex(),1)[0];
                aColumnDefs.splice(this.newIndex, 0, oColumn);
                oDataTable._initColumnSet(aColumnDefs);
                oDataTable._initTheadEls();
                oDataTable.render();
            }
        },
        endDrag: function() {
            this.newIndex = null;
            YAHOO.util.Dom.setStyle(this.pointer, 'display', 'none');
        }
    });
}

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * ColumnResizer subclasses DragDrop to support resizeable Columns.
 *
 * @namespace YAHOO.util
 * @class ColumnResizer
 * @extends YAHOO.util.DDProxy
 * @constructor
 * @param oDataTable {YAHOO.widget.DataTable} DataTable instance.
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param elTh {HTMLElement} TH element reference.
 * @param sHandleElId {String} DOM ID of the handle element that causes the resize.
 * @param elProxy {HTMLElement} Resizer proxy element.
 */
YAHOO.util.ColumnResizer = function(oDataTable, oColumn, elTh, sHandleId, elProxy) {
    if(oDataTable && oColumn && elTh && sHandleId) {
        this.datatable = oDataTable;
        this.column = oColumn;
        this.headCell = elTh;
        this.headCellLiner = elTh.firstChild;
        this.init(sHandleId, sHandleId, {dragOnly:true, dragElId: elProxy.id});
        this.initFrame(); // Needed for proxy
    }
    else {
        YAHOO.log("Column resizer could not be created","warn");
    }
};

if(YAHOO.util.DD) {
    YAHOO.extend(YAHOO.util.ColumnResizer, YAHOO.util.DDProxy, {
        /////////////////////////////////////////////////////////////////////////////
        //
        // Public methods
        //
        /////////////////////////////////////////////////////////////////////////////
        /**
         * Resets resizer element.
         *
         * @method resetResizerEl
         */
        resetResizerEl : function() {
            var resizerStyle = YAHOO.util.Dom.get(this.handleElId).style;
            resizerStyle.left = "auto";
            resizerStyle.right = 0;
            resizerStyle.top = "auto";
            resizerStyle.bottom = 0;
        },
    
        /////////////////////////////////////////////////////////////////////////////
        //
        // Public DOM event handlers
        //
        /////////////////////////////////////////////////////////////////////////////
    
        /**
         * Handles mouseup events on the Column resizer.
         *
         * @method onMouseUp
         * @param e {string} The mouseup event
         */
        onMouseUp : function(e) {
            this.resetResizerEl();
            this.datatable.fireEvent("columnResizeEvent", {column:this.column,target:this.headCell});
        },
    
        /**
         * Handles mousedown events on the Column resizer.
         *
         * @method onMouseDown
         * @param e {string} The mousedown event
         */
        onMouseDown : function(e) {
            this.startWidth = this.headCell.firstChild.offsetWidth;
            this.startX = YAHOO.util.Event.getXY(e)[0];
            this.nLinerPadding = (parseInt(YAHOO.util.Dom.getStyle(this.headCellLiner,"paddingLeft"),10)|0) +
                    (parseInt(YAHOO.util.Dom.getStyle(this.headCellLiner,"paddingRight"),10)|0);
        },
    
        /**
         * Custom clickValidator to ensure Column is not in hidden state.
         *
         * @method clickValidator
         * @param {Event} e
         * @private
         */
        clickValidator : function(e) {
            if(!this.column.hidden) {
                var target = YAHOO.util.Event.getTarget(e);
                return ( this.isValidHandleChild(target) &&
                            (this.id == this.handleElId ||
                                this.DDM.handleWasClicked(target, this.id)) );
            }
        },
    
        /**
         * Handles drag events on the Column resizer.
         *
         * @method onDrag
         * @param e {string} The drag event
         */
        onDrag : function(e) {
            var newX = YAHOO.util.Event.getXY(e)[0];
            if(newX > YAHOO.util.Dom.getX(this.headCellLiner)) {
                var offsetX = newX - this.startX;
                var newWidth = this.startWidth + offsetX - this.nLinerPadding;
                this.datatable.setColumnWidth(this.column, newWidth);
            }
        }
    });
}

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * A RecordSet defines and manages a set of Records.
 *
 * @namespace YAHOO.widget
 * @class RecordSet
 * @param data {Object || Object[]} An object literal or an array of data.
 * @constructor
 */
YAHOO.widget.RecordSet = function(data) {
    // Internal variables
    this._sId = "yui-rs" + YAHOO.widget.RecordSet._nCount;
    YAHOO.widget.RecordSet._nCount++;
    this._records = [];
    //this._length = 0;

    if(data) {
        if(YAHOO.lang.isArray(data)) {
            this.addRecords(data);
        }
        else if(data.constructor == Object) {
            this.addRecord(data);
        }
    }

    /**
     * Fired when a new Record is added to the RecordSet.
     *
     * @event recordAddEvent
     * @param oArgs.record {YAHOO.widget.Record} The Record instance.
     * @param oArgs.data {Object} Data added.
     */
    this.createEvent("recordAddEvent");

    /**
     * Fired when multiple Records are added to the RecordSet at once.
     *
     * @event recordsAddEvent
     * @param oArgs.records {YAHOO.widget.Record[]} An array of Record instances.
     * @param oArgs.data {Object[]} Data added.
     */
    this.createEvent("recordsAddEvent");

    /**
     * Fired when a Record is set in the RecordSet.
     *
     * @event recordSetEvent
     * @param oArgs.record {YAHOO.widget.Record} The Record instance.
     * @param oArgs.data {Object} Data added.
     */
    this.createEvent("recordSetEvent");

    /**
     * Fired when multiple Records are set in the RecordSet at once.
     *
     * @event recordsSetEvent
     * @param oArgs.records {YAHOO.widget.Record[]} An array of Record instances.
     * @param oArgs.data {Object[]} Data added.
     */
    this.createEvent("recordsSetEvent");

    /**
     * Fired when a Record is updated with new data.
     *
     * @event recordUpdateEvent
     * @param oArgs.record {YAHOO.widget.Record} The Record instance.
     * @param oArgs.newData {Object} New data.
     * @param oArgs.oldData {Object} Old data.
     */
    this.createEvent("recordUpdateEvent");
    
    /**
     * Fired when a Record is deleted from the RecordSet.
     *
     * @event recordDeleteEvent
     * @param oArgs.data {Object} A copy of the data held by the Record,
     * or an array of data object literals if multiple Records were deleted at once.
     * @param oArgs.index {Object} Index of the deleted Record.
     */
    this.createEvent("recordDeleteEvent");

    /**
     * Fired when multiple Records are deleted from the RecordSet at once.
     *
     * @event recordsDeleteEvent
     * @param oArgs.data {Object[]} An array of data object literals copied
     * from the Records.
     * @param oArgs.index {Object} Index of the first deleted Record.
     */
    this.createEvent("recordsDeleteEvent");
    
    /**
     * Fired when all Records are deleted from the RecordSet at once.
     *
     * @event resetEvent
     */
    this.createEvent("resetEvent");

    /**
     * @event keyUpdateEvent    
     * @deprecated Use recordValueUpdateEvent     
     */
    this.createEvent("keyUpdateEvent");
    /**
     * Fired when a Record value is updated with new data.
     *
     * @event recordValueUpdateEvent
     * @param oArgs.record {YAHOO.widget.Record} The Record instance.
     * @param oArgs.key {String} The updated key.
     * @param oArgs.newData {Object} New data.
     * @param oArgs.oldData {Object} Old data.
     *
     */
    this.createEvent("recordValueUpdateEvent");

    YAHOO.log("RecordSet initialized", "info", this.toString());
};

/**
 * Internal class variable to name multiple Recordset instances.
 *
 * @property RecordSet._nCount
 * @type Number
 * @private
 * @static
 */
YAHOO.widget.RecordSet._nCount = 0;

YAHOO.widget.RecordSet.prototype = {

    /////////////////////////////////////////////////////////////////////////////
    //
    // Private member variables
    //
    /////////////////////////////////////////////////////////////////////////////
    /**
     * Unique String identifier assigned at instantiation.
     *
     * @property _sId
     * @type String
     * @private
     */
    _sId : null,

    /**
     * Internal counter of how many Records are in the RecordSet.
     *
     * @property _length
     * @type Number
     * @private
     * @deprecated No longer used
     */
    //_length : null,

    /////////////////////////////////////////////////////////////////////////////
    //
    // Private methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Adds one Record to the RecordSet at the given index. If index is null,
     * then adds the Record to the end of the RecordSet.
     *
     * @method _addRecord
     * @param oData {Object} An object literal of data.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record} A Record instance.
     * @private
     */
    _addRecord : function(oData, index) {
        var oRecord = new YAHOO.widget.Record(oData);
        
        if(YAHOO.lang.isNumber(index) && (index > -1)) {
            this._records.splice(index,0,oRecord);
        }
        else {
            //index = this.getLength();
            //this._records[index] = oRecord;
            this._records[this._records.length] = oRecord;
        }
        //this._length++;
        return oRecord;
    },

    /**
     * Sets/replaces one Record to the RecordSet at the given index.  Existing
     * Records with higher indexes are not shifted.  If no index specified, the
     * Record is added to the end of the RecordSet.
     *
     * @method _setRecord
     * @param oData {Object} An object literal of data.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record} A Record instance.
     * @private
     */
    _setRecord : function(oData, index) {
        if (!YAHOO.lang.isNumber(index) || index < 0) {
            index = this._records.length;
        }
        return (this._records[index] = new YAHOO.widget.Record(oData));
        /*
        if(YAHOO.lang.isNumber(index) && (index > -1)) {
            this._records[index] = oRecord;
            if((index+1) > this.getLength()) {
                this._length = index+1;
            }
        }
        else {
            this._records[this.getLength()] = oRecord;
            this._length++;
        }
        return oRecord;
        */
    },

    /**
     * Deletes Records from the RecordSet at the given index. If range is null,
     * then only one Record is deleted.
     *
     * @method _deleteRecord
     * @param index {Number} Position index.
     * @param range {Number} (optional) How many Records to delete
     * @private
     */
    _deleteRecord : function(index, range) {
        if(!YAHOO.lang.isNumber(range) || (range < 0)) {
            range = 1;
        }
        this._records.splice(index, range);
        //this._length = this._length - range;
    },

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Returns unique name of the RecordSet instance.
     *
     * @method getId
     * @return {String} Unique name of the RecordSet instance.
     */
    getId : function() {
        return this._sId;
    },

    /**
     * Public accessor to the unique name of the RecordSet instance.
     *
     * @method toString
     * @return {String} Unique name of the RecordSet instance.
     */
    toString : function() {
        return "RecordSet instance " + this._sId;
    },

    /**
     * Returns the number of Records held in the RecordSet.
     *
     * @method getLength
     * @return {Number} Number of records in the RecordSet.
     */
    getLength : function() {
            //return this._length;
            return this._records.length;
    },

    /**
     * Returns Record by ID or RecordSet position index.
     *
     * @method getRecord
     * @param record {YAHOO.widget.Record | Number | String} Record instance,
     * RecordSet position index, or Record ID.
     * @return {YAHOO.widget.Record} Record object.
     */
    getRecord : function(record) {
        var i;
        if(record instanceof YAHOO.widget.Record) {
            for(i=0; i<this._records.length; i++) {
                if(this._records[i] && (this._records[i]._sId === record._sId)) {
                    return record;
                }
            }
        }
        else if(YAHOO.lang.isNumber(record)) {
            if((record > -1) && (record < this.getLength())) {
                return this._records[record];
            }
        }
        else if(YAHOO.lang.isString(record)) {
            for(i=0; i<this._records.length; i++) {
                if(this._records[i] && (this._records[i]._sId === record)) {
                    return this._records[i];
                }
            }
        }
        // Not a valid Record for this RecordSet
        return null;

    },

    /**
     * Returns an array of Records from the RecordSet.
     *
     * @method getRecords
     * @param index {Number} (optional) Recordset position index of which Record to
     * start at.
     * @param range {Number} (optional) Number of Records to get.
     * @return {YAHOO.widget.Record[]} Array of Records starting at given index and
     * length equal to given range. If index is not given, all Records are returned.
     */
    getRecords : function(index, range) {
        if(!YAHOO.lang.isNumber(index)) {
            return this._records;
        }
        if(!YAHOO.lang.isNumber(range)) {
            return this._records.slice(index);
        }
        return this._records.slice(index, index+range);
    },

    /**
     * Returns a boolean indicating whether Records exist in the RecordSet at the
     * specified index range.  Returns true if and only if a Record exists at each
     * index in the range.
     * @method hasRecords
     * @param index
     * @param range
     * @return {Boolean} true if all indices are populated in the RecordSet
     */
    hasRecords : function (index, range) {
        var recs = this.getRecords(index,range);
        for (var i = 0; i < range; ++i) {
            if (typeof recs[i] === 'undefined') {
                return false;
            }
        }
        return true;
    },

    /**
     * Returns current position index for the given Record.
     *
     * @method getRecordIndex
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @return {Number} Record's RecordSet position index.
     */

    getRecordIndex : function(oRecord) {
        if(oRecord) {
            for(var i=this._records.length-1; i>-1; i--) {
                if(this._records[i] && oRecord.getId() === this._records[i].getId()) {
                    return i;
                }
            }
        }
        return null;

    },

    /**
     * Adds one Record to the RecordSet at the given index. If index is null,
     * then adds the Record to the end of the RecordSet.
     *
     * @method addRecord
     * @param oData {Object} An object literal of data.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record} A Record instance.
     */
    addRecord : function(oData, index) {
        if(oData && (oData.constructor == Object)) {
            var oRecord = this._addRecord(oData, index);
            this.fireEvent("recordAddEvent",{record:oRecord,data:oData});
            YAHOO.log("Added Record at index " + index +
                    " with data " + YAHOO.lang.dump(oData), "info", this.toString());
            return oRecord;
        }
        else {
            YAHOO.log("Could not add Record with data" +
                    YAHOO.lang.dump(oData), "info", this.toString());
            return null;
        }
    },

    /**
     * Adds multiple Records at once to the RecordSet at the given index with the
     * given object literal data. If index is null, then the new Records are
     * added to the end of the RecordSet.
     *
     * @method addRecords
     * @param aData {Object[]} An object literal data or an array of data object literals.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record[]} An array of Record instances.
     */
    addRecords : function(aData, index) {
        if(YAHOO.lang.isArray(aData)) {
            var newRecords = [];
            // Can't go backwards bc we need to preserve order
            for(var i=0; i<aData.length; i++) {
                if(aData[i] && (aData[i].constructor == Object)) {
                    var record = this._addRecord(aData[i], index);
                    newRecords.push(record);
                }
           }
            this.fireEvent("recordsAddEvent",{records:newRecords,data:aData});
            YAHOO.log("Added " + newRecords.length + " Record(s) at index " + index +
                    " with data " + YAHOO.lang.dump(aData), "info", this.toString());
           return newRecords;
        }
        else if(aData && (aData.constructor == Object)) {
            var oRecord = this._addRecord(aData);
            this.fireEvent("recordsAddEvent",{records:[oRecord],data:aData});
            YAHOO.log("Added 1 Record at index " + index +
                    " with data " + YAHOO.lang.dump(aData), "info", this.toString());
            return oRecord;
        }
        else {
            YAHOO.log("Could not add Records with data " +
                    YAHOO.lang.dump(aData), "info", this.toString());
        }
    },

    /**
     * Sets or replaces one Record to the RecordSet at the given index. Unlike
     * addRecord, an existing Record at that index is not shifted to preserve it.
     * If no index is specified, it adds the Record to the end of the RecordSet.
     *
     * @method setRecord
     * @param oData {Object} An object literal of data.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record} A Record instance.
     */
    setRecord : function(oData, index) {
        if(oData && (oData.constructor == Object)) {
            var oRecord = this._setRecord(oData, index);
            this.fireEvent("recordSetEvent",{record:oRecord,data:oData});
            YAHOO.log("Set Record at index " + index +
                    " with data " + YAHOO.lang.dump(oData), "info", this.toString());
            return oRecord;
        }
        else {
            YAHOO.log("Could not set Record with data" +
                    YAHOO.lang.dump(oData), "info", this.toString());
            return null;
        }
    },

    /**
     * Sets or replaces multiple Records at once to the RecordSet with the given
     * data, starting at the given index. If index is not specified, then the new
     * Records are added to the end of the RecordSet.
     *
     * @method setRecords
     * @param aData {Object[]} An array of object literal data.
     * @param index {Number} (optional) Position index.
     * @return {YAHOO.widget.Record[]} An array of Record instances.
     */
    setRecords : function(aData, index) {
        if(YAHOO.lang.isArray(aData)) {
            var Rec = YAHOO.widget.Record,
                spliceParams = [index,0],
                i = aData.length - 1,
                r;

            // build up a parameter array for a single call to splice in
            // the new records.
            for(; i >= 0 ; --i) {
                // If the data in aData isn't valid, use the existing
                // record to avoid harming valid records
                r = aData[i] && typeof aData[i] === 'object' ?
                                new Rec(aData[i]) : this._records[i];
                if (r) {
                    spliceParams[i+2] = r;
                }
            }

            // We need to explicitly set the last record because splice doesn't
            // honor start indexes higher than the current length.
            this._records[index + spliceParams.length - 3] =
                spliceParams[spliceParams.length - 1];

            // Set the number of records to change (length minus first 2
            // placeholders).  This must be done here because the end of aData
            // may have contained invalid record data so we avoid increasing
            // _records.length incorrectly.  And we do need to set the last
            // record, although we just did that.
            spliceParams[1] = spliceParams.length - 2;

            // Call splice.apply to simulate a single update to _records
            // rather than looping through the new records and setting them
            // to the index one by one.  Use the spliceParams array to get
            // the splice method to apply.  A bit convoluted, I know.
            spliceParams.splice.apply(this._records,spliceParams);

            this.fireEvent("recordsSet",{records:spliceParams,data:aData});
            YAHOO.log("Set " + spliceParams[1] + " Record(s) at index " +
                      spliceParams[0] + " through " +
                      (spliceParams[0] + spliceParams[1])/* + " with data " + YAHOO.lang.dump(aData)*/, "info", this.toString());

            // return the records that were set.  The first two indexes
            // in spliceParams are the start index and length, so return
            // everything starting at index 2
            return spliceParams.slice(2);
        }
        else if(aData && (aData.constructor == Object)) {
            var oRecord = this._setRecord(aData);
            this.fireEvent("recordsSetEvent",{records:[oRecord],data:aData});
            YAHOO.log("Set 1 Record at index " + index +
                    " with data " + YAHOO.lang.dump(aData), "info", this.toString());
            return oRecord;
        }
        else {
            YAHOO.log("Could not set Records with data " +
                    YAHOO.lang.dump(aData), "info", this.toString());
        }
    },

    /**
     * Updates given Record with given data.
     *
     * @method updateRecord
     * @param record {YAHOO.widget.Record | Number | String} A Record instance,
     * a RecordSet position index, or a Record ID.
     * @param oData {Object} Object literal of new data.
     * @return {YAHOO.widget.Record} Updated Record, or null.
     */
    updateRecord : function(record, oData) {
        var oRecord = this.getRecord(record);
        if(oRecord && oData && (oData.constructor == Object)) {
            // Copy data from the Record for the event that gets fired later
            var oldData = {};
            for(var key in oRecord._oData) {
                oldData[key] = oRecord._oData[key];
            }
            oRecord._oData = oData;
            this.fireEvent("recordUpdateEvent",{record:oRecord,newData:oData,oldData:oldData});
            YAHOO.log("Record at index " + this.getRecordIndex(oRecord) +
                    " updated with data " + YAHOO.lang.dump(oData), "info", this.toString());
            return oRecord;
        }
        else {
            YAHOO.log("Could not update Record " + record, "error", this.toString());
            return null;
        }
    },

    /**
     * @method updateKey
     * @deprecated Use updateRecordValue
     */
    updateKey : function(record, sKey, oData) {
        this.updateRecordValue(record, sKey, oData);
    },
    /**
     * Sets given Record at given key to given data.
     *
     * @method updateRecordValue
     * @param record {YAHOO.widget.Record | Number | String} A Record instance,
     * a RecordSet position index, or a Record ID.
     * @param sKey {String} Key name.
     * @param oData {Object} New data.
     */
    updateRecordValue : function(record, sKey, oData) {
        var oRecord = this.getRecord(record);
        if(oRecord) {
            var oldData = null;
            var keyValue = oRecord._oData[sKey];
            // Copy data from the Record for the event that gets fired later
            if(keyValue && keyValue.constructor == Object) {
                oldData = {};
                for(var key in keyValue) {
                    oldData[key] = keyValue[key];
                }
            }
            // Copy by value
            else {
                oldData = keyValue;
            }

            oRecord._oData[sKey] = oData;
            this.fireEvent("keyUpdateEvent",{record:oRecord,key:sKey,newData:oData,oldData:oldData});
            this.fireEvent("recordValueUpdateEvent",{record:oRecord,key:sKey,newData:oData,oldData:oldData});
            YAHOO.log("Key \"" + sKey +
                    "\" for Record at index " + this.getRecordIndex(oRecord) +
                    " updated to \"" + YAHOO.lang.dump(oData) + "\"", "info", this.toString());
        }
        else {
            YAHOO.log("Could not update key " + sKey + " for Record " + record, "error", this.toString());
        }
    },

    /**
     * Replaces all Records in RecordSet with new object literal data.
     *
     * @method replaceRecords
     * @param data {Object || Object[]} An object literal of data or an array of
     * data object literals.
     * @return {YAHOO.widget.Record || YAHOO.widget.Record[]} A Record instance or
     * an array of Records.
     */
    replaceRecords : function(data) {
        this.reset();
        return this.addRecords(data);
    },

    /**
     * Sorts all Records by given function. Records keep their unique IDs but will
     * have new RecordSet position indexes.
     *
     * @method sortRecords
     * @param fnSort {Function} Reference to a sort function.
     * @param desc {Boolean} True if sort direction is descending, false if sort
     * direction is ascending.
     * @return {YAHOO.widget.Record[]} Sorted array of Records.
     */
    sortRecords : function(fnSort, desc) {
        return this._records.sort(function(a, b) {return fnSort(a, b, desc);});
    },

    /**
     * Reverses all Records, so ["one", "two", "three"] becomes ["three", "two", "one"].
     *
     * @method reverseRecords
     * @return {YAHOO.widget.Record[]} Reverse-sorted array of Records.
     */
    reverseRecords : function() {
        return this._records.reverse();
    },

    /**
     * Removes the Record at the given position index from the RecordSet. If a range
     * is also provided, removes that many Records, starting from the index. Length
     * of RecordSet is correspondingly shortened.
     *
     * @method deleteRecord
     * @param index {Number} Record's RecordSet position index.
     * @param range {Number} (optional) How many Records to delete.
     * @return {Object} A copy of the data held by the deleted Record.
     */
    deleteRecord : function(index) {
        if(YAHOO.lang.isNumber(index) && (index > -1) && (index < this.getLength())) {
            // Copy data from the Record for the event that gets fired later
            var oRecordData = this.getRecord(index).getData();
            var oData = {};
            for(var key in oRecordData) {
                oData[key] = oRecordData[key];
            }
            
            this._deleteRecord(index);
            this.fireEvent("recordDeleteEvent",{data:oData,index:index});
            YAHOO.log("Record deleted at index " + index +
                    " and containing data " + YAHOO.lang.dump(oData), "info", this.toString());
            return oData;
        }
        else {
            YAHOO.log("Could not delete Record at index " + index, "error", this.toString());
            return null;
        }
    },

    /**
     * Removes the Record at the given position index from the RecordSet. If a range
     * is also provided, removes that many Records, starting from the index. Length
     * of RecordSet is correspondingly shortened.
     *
     * @method deleteRecords
     * @param index {Number} Record's RecordSet position index.
     * @param range {Number} (optional) How many Records to delete.
     */
    deleteRecords : function(index, range) {
        if(!YAHOO.lang.isNumber(range)) {
            range = 1;
        }
        if(YAHOO.lang.isNumber(index) && (index > -1) && (index < this.getLength())) {
            var recordsToDelete = this.getRecords(index, range);
            // Copy data from each Record for the event that gets fired later
            var deletedData = [];
            for(var i=0; i<recordsToDelete.length; i++) {
                var oData = {};
                for(var key in recordsToDelete[i]) {
                    oData[key] = recordsToDelete[i][key];
                }
                deletedData.push(oData);
            }
            this._deleteRecord(index, range);

            this.fireEvent("recordsDeleteEvent",{data:deletedData,index:index});
            YAHOO.log(range + "Record(s) deleted at index " + index +
                    " and containing data " + YAHOO.lang.dump(deletedData), "info", this.toString());

        }
        else {
            YAHOO.log("Could not delete Records at index " + index, "error", this.toString());
        }
    },

    /**
     * Deletes all Records from the RecordSet.
     *
     * @method reset
     */
    reset : function() {
        this._records = [];
        //this._length = 0;
        this.fireEvent("resetEvent");
        YAHOO.log("All Records deleted from RecordSet", "info", this.toString());
    }
};

YAHOO.augment(YAHOO.widget.RecordSet, YAHOO.util.EventProvider);

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The Record class defines a DataTable record.
 *
 * @namespace YAHOO.widget
 * @class Record
 * @constructor
 * @param oConfigs {Object} (optional) Object literal of key/value pairs.
 */
YAHOO.widget.Record = function(oLiteral) {
    this._sId = "yui-rec" + YAHOO.widget.Record._nCount;
    YAHOO.widget.Record._nCount++;
    this._oData = {};
    if(oLiteral && (oLiteral.constructor == Object)) {
        for(var sKey in oLiteral) {
            this._oData[sKey] = oLiteral[sKey];
        }
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Internal class variable to give unique IDs to Record instances.
 *
 * @property Record._nCount
 * @type Number
 * @private
 */
YAHOO.widget.Record._nCount = 0;

YAHOO.widget.Record.prototype = {
    /**
     * Immutable unique ID assigned at instantiation. Remains constant while a
     * Record's position index can change from sorting.
     *
     * @property _sId
     * @type String
     * @private
     */
    _sId : null,

    /**
     * Holds data for the Record in an object literal.
     *
     * @property _oData
     * @type Object
     * @private
     */
    _oData : null,

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public member variables
    //
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    //
    // Public methods
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Returns unique ID assigned at instantiation.
     *
     * @method getId
     * @return String
     */
    getId : function() {
        return this._sId;
    },

    /**
     * Returns data for the Record for a key if given, or the entire object
     * literal otherwise.
     *
     * @method getData
     * @param sKey {String} (Optional) The key to retrieve a single data value.
     * @return Object
     */
    getData : function(sKey) {
        if(YAHOO.lang.isString(sKey)) {
            return this._oData[sKey];
        }
        else {
            return this._oData;
        }
    },

    /**
     * Sets given data at the given key. Use the RecordSet method setValue to trigger
     * events. 
     *
     * @method setData
     * @param sKey {String} The key of the new value.
     * @param oData {MIXED} The new value.
     */
    setData : function(sKey, oData) {
        this._oData[sKey] = oData;
    }
};

/**
 * The Paginator widget provides a set of controls to navigate through paged
 * data.
 *
 * @namespace YAHOO.widget
 * @class Paginator
 * @uses YAHOO.util.EventProvider
 * @uses YAHOO.util.AttributeProvider
 *
 * @constructor
 * @param config {Object} Object literal to set instance and ui component
 * configuration.
 */
YAHOO.widget.Paginator = function (config) {
    var UNLIMITED = YAHOO.widget.Paginator.VALUE_UNLIMITED,
        lang      = YAHOO.lang,
        attrib, initialPage, records, perPage;

    config = lang.isObject(config) ? config : {};

    this.initConfig();

    this.initEvents();

    // Set the basic config keys first
    this.set('rowsPerPage',config.rowsPerPage,true);
    if (lang.isNumber(config.totalRecords)) {
        this.set('totalRecords',config.totalRecords,true);
    }
    
    this.initUIComponents();

    // Update the other config values
    for (attrib in config) {
        if (lang.hasOwnProperty(config,attrib)) {
            this.set(attrib,config[attrib],true);
        }
    }

    // Calculate the initial record offset
    initialPage = this.get('initialPage');
    records     = this.get('totalRecords');
    perPage     = this.get('rowsPerPage');
    if (initialPage > 1 && perPage !== UNLIMITED) {
        var startIndex = (initialPage - 1) * perPage;
        if (records === UNLIMITED || startIndex < records) {
            this.set('recordOffset',startIndex,true);
        }
    }
};


// Static members
YAHOO.lang.augmentObject(YAHOO.widget.Paginator, {
    /**
     * Incrementing index used to give instances unique ids.
     * @static
     * @property id
     * @type number
     * @private
     */
    id : 0,

    /**
     * Base of id strings used for ui components.
     * @static
     * @property ID_BASE
     * @type string
     * @private
     */
    ID_BASE : 'yui-pg',

    /**
     * Used to identify unset, optional configurations, or used explicitly in
     * the case of totalRecords to indicate unlimited pagination.
     * @static
     * @property VALUE_UNLIMITED
     * @type number
     * @final
     */
    VALUE_UNLIMITED : -1,

    /**
     * Default template used by Paginator instances.  Update this if you want
     * all new Paginators to use a different default template.
     * @static
     * @property TEMPLATE_DEFAULT
     * @type string
     */
    TEMPLATE_DEFAULT : "{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",

    /**
     * Common alternate pagination format, including page links, links for
     * previous, next, first and last pages as well as a rows-per-page
     * dropdown.  Offered as a convenience.
     * @static
     * @property TEMPLATE_ROWS_PER_PAGE
     * @type string
     */
    TEMPLATE_ROWS_PER_PAGE : "{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {RowsPerPageDropdown}"

},true);


// Instance members and methods
YAHOO.widget.Paginator.prototype = {

    // Instance members

    /**
     * Array of nodes in which to render pagination controls.  This is set via
     * the &quot;containers&quot; attribute.
     * @property _containers
     * @type Array(HTMLElement)
     * @private
     */
    _containers : [],




    // Instance methods

    /**
     * Initialize the Paginator's attributes (see YAHOO.util.Element class
     * AttributeProvider).
     * @method initConfig
     * @private
     */
    initConfig : function () {

        var UNLIMITED = YAHOO.widget.Paginator.VALUE_UNLIMITED,
            l         = YAHOO.lang;

        /**
         * REQUIRED. Number of records constituting a &quot;page&quot;
         * @attribute rowsPerPage
         * @type integer
         */
        this.setAttributeConfig('rowsPerPage', {
            value     : 0,
            validator : l.isNumber
        });

        /**
         * REQUIRED. Node references or ids of nodes in which to render the
         * pagination controls.
         * @attribute containers
         * @type {string|HTMLElement|Array(string|HTMLElement)}
         */
        this.setAttributeConfig('containers', {
            value     : null,
            writeOnce : true,
            validator : function (val) {
                if (!l.isArray(val)) {
                    val = [val];
                }
                for (var i = 0, len = val.length; i < len; ++i) {
                    if (l.isString(val[i]) || 
                        (l.isObject(val[i]) && val[i].nodeType === 1)) {
                        continue;
                    }
                    return false;
                }
                return true;
            },
            method : function (val) {
                val = YAHOO.util.Dom.get(val);
                if (!l.isArray(val)) {
                    val = [val];
                }
                this._containers = val;
            }
        });

        /**
         * Total number of records to paginate through
         * @attribute totalRecords
         * @type integer
         * @default Paginator.VALUE_UNLIMITED
         */
        this.setAttributeConfig('totalRecords', {
            value     : UNLIMITED,
            validator : l.isNumber
        });

        /**
         * Zero based index of the record considered first on the current page.
         * For page based interactions, don't modify this attribute directly;
         * use setPage(n).
         * @attribute recordOffset
         * @type integer
         * @default 0
         */
        this.setAttributeConfig('recordOffset', {
            value     : 0,
            validator : function (val) {
                var total = this.get('totalRecords');
                if (l.isNumber(val)) {
                    return total === UNLIMITED || total > val;
                }

                return false;
            }
        });

        /**
         * Page to display on initial paint
         * @attribute initialPage
         * @type integer
         * @default 1
         */
        this.setAttributeConfig('initialPage', {
            value     : 1,
            validator : l.isNumber
        });

        /**
         * Template used to render controls.  The string will be used as
         * innerHTML on all specified container nodes.  Bracketed keys
         * (e.g. {pageLinks}) in the string will be replaced with an instance
         * of the so named ui component.
         * @see Paginator.TEMPLATE_DEFAULT
         * @see Paginator.TEMPLATE_ROWS_PER_PAGE
         * @attribute template
         * @type string
         */
        this.setAttributeConfig('template', {
            value : YAHOO.widget.Paginator.TEMPLATE_DEFAULT,
            validator : l.isString
        });

        /**
         * Class assigned to the element(s) containing pagination controls.
         * @attribute containerClass
         * @type string
         * @default 'yui-pg-container'
         */
        this.setAttributeConfig('containerClass', {
            value : 'yui-pg-container',
            validator : l.isString
        });

        /**
         * Display pagination controls even when there is only one page.  Set
         * to false to forgo rendering and/or hide the containers when there
         * is only one page of data.  Note if you are using the rowsPerPage
         * dropdown ui component, visibility will be maintained as long as the
         * number of records exceeds the smallest page size.
         * @attribute alwaysVisible
         * @type boolean
         * @default true
         */
        this.setAttributeConfig('alwaysVisible', {
            value : true,
            validator : l.isBoolean
        });

        /**
         * Update the UI immediately upon interaction.  If false, changeRequest
         * subscribers or other external code will need to explicitly set the
         * new values in the paginator to trigger repaint.
         * @attribute updateOnChange
         * @type boolean
         * @default false
         */
        this.setAttributeConfig('updateOnChange', {
            value     : false,
            validator : l.isBoolean
        });



        // Read only attributes

        /**
         * Unique id assigned to this instance
         * @attribute id
         * @type integer
         * @final
         */
        this.setAttributeConfig('id', {
            value    : YAHOO.widget.Paginator.id++,
            readOnly : true
        });

        /**
         * Indicator of whether the DOM nodes have been initially created
         * @attribute rendered
         * @type boolean
         * @final
         */
        this.setAttributeConfig('rendered', {
            value    : false,
            readOnly : true
        });

    },

    /**
     * Initialize registered ui components onto this instance.
     * @method initUIComponents
     * @private
     */
    initUIComponents : function () {
        var ui = YAHOO.widget.Paginator.ui;
        for (var name in ui) {
            var UIComp = ui[name];
            if (YAHOO.lang.isObject(UIComp) &&
                YAHOO.lang.isFunction(UIComp.init)) {
                UIComp.init(this);
            }
        }
    },

    /**
     * Initialize this instance's CustomEvents.
     * @method initEvents
     * @private
     */
    initEvents : function () {
        this.createEvent('recordOffsetChange');
        this.createEvent('totalRecordsChange');
        this.createEvent('rowsPerPageChange');
        this.createEvent('alwaysVisibleChange');

        this.createEvent('rendered');
        this.createEvent('changeRequest');
        this.createEvent('beforeDestroy');

        // Listen for changes to totalRecords and alwaysVisible 
        this.subscribe('totalRecordsChange',this.updateVisibility,this,true);
        this.subscribe('alwaysVisibleChange',this.updateVisibility,this,true);
    },

    /**
     * Render the pagination controls per the format attribute into the
     * specified container nodes.
     * @method render
     */
    render : function () {
        if (this.get('rendered')) {
            return;
        }

        // Forgo rendering if only one page and alwaysVisible is off
        var totalRecords = this.get('totalRecords');
        if (totalRecords !== YAHOO.widget.Paginator.VALUE_UNLIMITED &&
            totalRecords < this.get('rowsPerPage') &&
            !this.get('alwaysVisible')) {
            return;
        }

        var Dom            = YAHOO.util.Dom,
            template       = this.get('template'),
            containerClass = this.get('containerClass');

        // add marker spans to the template html to indicate drop zones
        // for ui components
        template = template.replace(/\{([a-z0-9_ \-]+)\}/gi,
            '<span class="yui-pg-ui $1"></span>');
        for (var i = 0, len = this._containers.length; i < len; ++i) {
            var c       = this._containers[i],
                // ex. yui-pg0-1 (first paginator, second container)
                id_base = YAHOO.widget.Paginator.ID_BASE + this.get('id') +
                          '-' + i;

            if (!c) {
                continue;
            }
            // Hide the container while its contents are rendered
            c.style.display = 'none';

            Dom.addClass(c,containerClass);

            // Place the template innerHTML
            c.innerHTML = template;

            // Replace each marker with the ui component's render() output
            var markers = Dom.getElementsByClassName('yui-pg-ui','span',c);

            for (var j = 0, jlen = markers.length; j < jlen; ++j) {
                var m      = markers[j],
                    mp     = m.parentNode,
                    name   = m.className.replace(/\s*yui-pg-ui\s+/g,''),
                    UIComp = YAHOO.widget.Paginator.ui[name];

                if (YAHOO.lang.isFunction(UIComp)) {
                    var comp = new UIComp(this);
                    if (YAHOO.lang.isFunction(comp.render)) {
                        mp.replaceChild(comp.render(id_base),m);
                    }
                }
            }

            // Show the container allowing page reflow
            c.style.display = '';
        }

        // Set render attribute manually to support its readOnly contract
        if (this._containers.length) {
            this.setAttributeConfig('rendered',{value:true});

            this.fireEvent('rendered',this.getState());
        }
    },

    /**
     * Removes controls from the page and unhooks events.
     * @method destroy
     */
    destroy : function () {
        this.fireEvent('beforeDestroy');
        for (var i = 0, len = this._containers.length; i < len; ++i) {
            this._containers[i].innerHTML = '';
        }
        this.setAttributeConfig('rendered',{value:false});
    },

    /**
     * Hides the containers if there is only one page of data and attribute
     * alwaysVisible is false.  Conversely, it displays the containers if either
     * there is more than one page worth of data or alwaysVisible is turned on.
     * @method updateVisibility
     */
    updateVisibility : function (e) {
        var alwaysVisible = this.get('alwaysVisible');
        if (e.type === 'alwaysVisibleChange' || !alwaysVisible) {
            var totalRecords = this.get('totalRecords'),
                visible = true,
                rpp = this.get('rowsPerPage'),
                rppOptions = this.get('rowsPerPageOptions'),
                i,len;

            if (YAHOO.lang.isArray(rppOptions)) {
                for (i = 0, len = rppOptions.length; i < len; ++i) {
                    rpp = Math.min(rpp,rppOptions[i]);
                }
            }

            if (totalRecords !== YAHOO.widget.Paginator.VALUE_UNLIMITED &&
                totalRecords <= rpp) {
                visible = false;
            }

            visible = visible || alwaysVisible;

            for (i = 0, len = this._containers.length; i < len; ++i) {
                YAHOO.util.Dom.setStyle(this._containers[i],'display',
                    visible ? '' : 'none');
            }
        }
    },




    /**
     * Get the configured container nodes
     * @method getContainerNodes
     * @return {Array} array of HTMLElement nodes
     */
    getContainerNodes : function () {
        return this._containers;
    },

    /**
     * Get the total number of pages in the data set according to the current
     * rowsPerPage and totalRecords values.  If totalRecords is not set, or
     * set to YAHOO.widget.Paginator.VALUE_UNLIMITED, returns
     * YAHOO.widget.Paginator.VALUE_UNLIMITED.
     * @method getTotalPages
     * @return {number}
     */
    getTotalPages : function () {
        var records = this.get('totalRecords');
        var perPage = this.get('rowsPerPage');

        // rowsPerPage not set.  Can't calculate
        if (!perPage) {
            return null;
        }

        if (records === YAHOO.widget.Paginator.VALUE_UNLIMITED) {
            return YAHOO.widget.Paginator.VALUE_UNLIMITED;
        }

        return Math.ceil(records/perPage);
    },

    /**
     * Does the requested page have any records?
     * @method hasPage
     * @param page {number} the page in question
     * @return {boolean}
     */
    hasPage : function (page) {
        if (!YAHOO.lang.isNumber(page) || page < 1) {
            return false;
        }

        var totalPages = this.getTotalPages();

        return (totalPages === YAHOO.widget.Paginator.VALUE_UNLIMITED || totalPages >= page);
    },

    /**
     * Get the page number corresponding to the current record offset.
     * @method getCurrentPage
     * @return {number}
     */
    getCurrentPage : function () {
        var perPage = this.get('rowsPerPage');
        if (!perPage) {
            return null;
        }
        return Math.floor(this.get('recordOffset') / perPage) + 1;
    },

    /**
     * Are there records on the next page?
     * @method hasNextPage
     * @return {boolean}
     */
    hasNextPage : function () {
        var currentPage = this.getCurrentPage(),
            totalPages  = this.getTotalPages();

        if (currentPage === null) {
            return false;
        }

        return (totalPages === YAHOO.widget.Paginator.VALUE_UNLIMITED ? true : currentPage < totalPages);
    },

    /**
     * Get the page number of the next page, or null if the current page is the
     * last page.
     * @method getNextPage
     * @return {number}
     */
    getNextPage : function () {
        return this.hasNextPage() ? this.getCurrentPage() + 1 : null;
    },

    /**
     * Is there a page before the current page?
     * @method hasPreviousPage
     * @return {boolean}
     */
    hasPreviousPage : function () {
        return (this.getCurrentPage() > 1);
    },

    /**
     * Get the page number of the previous page, or null if the current page
     * is the first page.
     * @method getPreviousPage
     * @return {number}
     */
    getPreviousPage : function () {
        return (this.hasPreviousPage() ? this.getCurrentPage() - 1 : 1);
    },

    /**
     * Get the start and end record indexes of the specified page.
     * @method getPageRecords
     * @param page {number} (optional) The page (current page if not specified)
     * @return {Array} [start_index, end_index]
     */
    getPageRecords : function (page) {
        if (!YAHOO.lang.isNumber(page)) {
            page = this.getCurrentPage();
        }

        var perPage = this.get('rowsPerPage'),
            records = this.get('totalRecords'),
            start, end;

        if (!perPage) {
            return null;
        }

        start = (page - 1) * perPage;
        if (records !== YAHOO.widget.Paginator.VALUE_UNLIMITED) {
            if (start >= records) {
                return null;
            }
            end = Math.min(start + perPage, records) - 1;
        } else {
            end = start + perPage - 1;
        }

        return [start,end];
    },

    /**
     * Set the current page to the provided page number if possible.
     * @method setPage
     * @param newPage {number} the new page number
     * @param silent {boolean} whether to forcibly avoid firing the
     * changeRequest event
     */
    setPage : function (page,silent) {
        if (this.hasPage(page) && page !== this.getCurrentPage()) {
            if (this.get('updateOnChange') || silent) {
                this.set('recordOffset', (page - 1) * this.get('rowsPerPage'));
            } else {
                this.fireEvent('changeRequest',this.getState({'page':page}));
            }
        }
    },

    /**
     * Get the number of rows per page.
     * @method getRowsPerPage
     * @return {number} the current setting of the rowsPerPage attribute
     */
    getRowsPerPage : function () {
        return this.get('rowsPerPage');
    },

    /**
     * Set the number of rows per page.
     * @method setRowsPerPage
     * @param rpp {number} the new number of rows per page
     * @param silent {boolean} whether to forcibly avoid firing the
     * changeRequest event
     */
    setRowsPerPage : function (rpp,silent) {
        if (YAHOO.lang.isNumber(rpp) && rpp > 0 &&
            rpp !== this.get('rowsPerPage')) {
            if (this.get('updateOnChange') || silent) {
                this.set('rowsPerPage',rpp);
            } else {
                this.fireEvent('changeRequest',
                    this.getState({'rowsPerPage':rpp}));
            }
        }
    },

    /**
     * Get the total number of records.
     * @method getTotalRecords
     * @return {number} the current setting of totalRecords attribute
     */
    getTotalRecords : function () {
        return this.get('totalRecords');
    },

    /**
     * Set the total number of records.
     * @method setTotalRecords
     * @param total {number} the new total number of records
     * @param silent {boolean} whether to forcibly avoid firing the changeRequest event
     */
    setTotalRecords : function (total,silent) {
        if (YAHOO.lang.isNumber(total) && total >= 0 &&
            total !== this.get('totalRecords')) {
            if (this.get('updateOnChange') || silent) {
                this.set('totalRecords',total);
            } else {
                this.fireEvent('changeRequest',
                    this.getState({'totalRecords':total}));
            }
        }
    },

    /**
     * Get the index of the first record on the current page
     * @method getStartIndex
     * @return {number} the index of the first record on the current page
     */
    getStartIndex : function () {
        return this.get('recordOffset');
    },

    /**
     * Move the record offset to a new starting index.  This will likely cause
     * the calculated current page to change.  You should probably use setPage.
     * @method setStartIndex
     * @param offset {number} the new record offset
     * @param silent {boolean} whether to forcibly avoid firing the changeRequest event
     */
    setStartIndex : function (offset,silent) {
        if (YAHOO.lang.isNumber(offset) && offset >= 0 &&
            offset !== this.get('recordOffset')) {
            if (this.get('updateOnChange') || silent) {
                this.set('recordOffset',offset);
            } else {
                this.fireEvent('changeRequest',
                    this.getState({'recordOffset':offset}));
            }
        }
    },

    /**
     * Get an object literal describing the current state of the paginator.  If
     * an object literal of proposed values is passed, the proposed state will
     * be returned as an object literal with the following keys:
     * <ul>
     * <li>paginator - instance of the Paginator</li>
     * <li>page - number</li>
     * <li>totalRecords - number</li>
     * <li>recordOffset - number</li>
     * <li>rowsPerPage - number</li>
     * <li>records - [ start_index, end_index ]</li>
     * <li>before - (OPTIONAL) { state object literal for current state }</li>
     * </ul>
     * @method getState
     * @return {object}
     * @param changes {object} OPTIONAL object literal with proposed values
     * Supported change keys include:
     * <ul>
     * <li>rowsPerPage</li>
     * <li>totalRecords</li>
     * <li>recordOffset OR</li>
     * <li>page</li>
     * </ul>
     */
    getState : function (changes) {
        var UNLIMITED = YAHOO.widget.Paginator.VALUE_UNLIMITED,
            L         = YAHOO.lang;

        var currentState = {
            paginator    : this,
            page         : this.getCurrentPage(),
            totalRecords : this.get('totalRecords'),
            recordOffset : this.get('recordOffset'),
            rowsPerPage  : this.get('rowsPerPage'),
            records      : this.getPageRecords()
        };

        if (!changes) {
            return currentState;
        }

        var newOffset = currentState.recordOffset;
        var state = {
            paginator    : this,
            before       : currentState,

            rowsPerPage  : changes.rowsPerPage || currentState.rowsPerPage,
            totalRecords : (L.isNumber(changes.totalRecords) ?
                                Math.max(changes.totalRecords,UNLIMITED) :
                                currentState.totalRecords)
        };

        if (state.totalRecords === 0) {
            newOffset  = 0;
            state.page = 0;
        } else {
            if (!L.isNumber(changes.recordOffset) &&
                 L.isNumber(changes.page)) {
                newOffset = (changes.page - 1) * state.rowsPerPage;
                if (state.totalRecords === UNLIMITED) {
                    state.page = changes.page;
                } else {
                    // Limit values by totalRecords and rowsPerPage
                    state.page = Math.min(
                                    changes.page,
                                    Math.ceil(state.totalRecords / state.rowsPerPage));
                    newOffset  = Math.min(newOffset, state.totalRecords - 1);
                }
            } else {
                newOffset  = Math.min(newOffset,state.totalRecords - 1);
                state.page = Math.floor(newOffset/state.rowsPerPage) + 1;
            }
        }

        // Jump offset to top of page
        state.recordOffset = state.recordOffset ||
                             newOffset - (newOffset % state.rowsPerPage);

        state.records = [ state.recordOffset,
                          state.recordOffset + state.rowsPerPage - 1 ];

        if (state.totalRecords !== UNLIMITED &&
            state.recordOffset < state.totalRecords &&
            state.records[1] > state.totalRecords - 1) {
            // limit upper index to totalRecords - 1
            state.records[1] = state.totalRecords - 1;
        }

        return state;
    }
};

YAHOO.lang.augmentProto(YAHOO.widget.Paginator, YAHOO.util.AttributeProvider);






// UI Components

(function () {

// UI Component namespace
YAHOO.widget.Paginator.ui = {};

var Paginator = YAHOO.widget.Paginator,
    ui        = Paginator.ui,
    l         = YAHOO.lang;

/**
 * ui Component to generate the link to jump to the first page.
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class FirstPageLink
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.FirstPageLink = function (p) {
    this.paginator = p;

    p.createEvent('firstPageLinkLabelChange');
    p.createEvent('firstPageLinkClassChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    // TODO: make this work
    p.subscribe('firstPageLinkLabelChange',this.update,this,true);
    p.subscribe('firstPageLinkClassChange',this.update,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.FirstPageLink.init = function (p) {

    /**
     * Used as innerHTML for the first page link/span.
     * @attribute firstPageLinkLabel
     * @default '&lt;&lt;&nbsp;first'
     */
    p.setAttributeConfig('firstPageLinkLabel', {
        value : '&lt;&lt;&nbsp;first',
        validator : l.isString
    });

    /**
     * CSS class assigned to the link/span
     * @attribute firstPageLinkClass
     * @default 'yui-pg-first'
     */
    p.setAttributeConfig('firstPageLinkClass', {
        value : 'yui-pg-first',
        validator : l.isString
    });
};

// Instance members and methods
ui.FirstPageLink.prototype = {

    /**
     * The currently placed HTMLElement node
     * @property current
     * @type HTMLElement
     * @private
     */
    current   : null,

    /**
     * Link node
     * @property link
     * @type HTMLElement
     * @private
     */
    link      : null,

    /**
     * Span node (inactive link)
     * @property span
     * @type HTMLElement
     * @private
     */
    span      : null,

    /**
     * Generate the nodes and return the appropriate node given the current
     * pagination state.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        var p     = this.paginator,
            c     = p.get('firstPageLinkClass'),
            label = p.get('firstPageLinkLabel');

        this.link     = document.createElement('a');
        this.span     = document.createElement('span');

        this.link.id        = id_base + '-first-link';
        this.link.href      = '#';
        this.link.className = c;
        this.link.innerHTML = label;
        YAHOO.util.Event.on(this.link,'click',this.onClick,this,true);

        this.span.id        = id_base + '-first-span';
        this.span.className = c;
        this.span.innerHTML = label;

        this.current = p.get('recordOffset') < 1 ? this.span : this.link;
        return this.current;
    },

    /**
     * Swap the link and span nodes if appropriate.
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var par = this.current ? this.current.parentNode : null;
        if (this.paginator.get('recordOffset') < 1) {
            if (par && this.current === this.link) {
                par.replaceChild(this.span,this.current);
                this.current = this.span;
            }
        } else {
            if (par && this.current === this.span) {
                par.replaceChild(this.link,this.current);
                this.current = this.link;
            }
        }
    },

    /**
     * Removes the onClick listener from the link in preparation for content
     * removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.link);
    },

    /**
     * Listener for the link's onclick event.  Pass new value to setPage method.
     * @method onClick
     * @param e {DOMEvent} The click event
     */
    onClick : function (e) {
        YAHOO.util.Event.stopEvent(e);
        this.paginator.setPage(1);
    }
};



/**
 * ui Component to generate the link to jump to the last page.
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class LastPageLink
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.LastPageLink = function (p) {
    this.paginator = p;

    p.createEvent('lastPageLinkLabelChange');
    p.createEvent('lastPageLinkClassChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('totalRecordsChange',this.update,this,true);
    p.subscribe('rowsPerPageChange', this.update,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    // TODO: make this work
    p.subscribe('lastPageLinkLabelChange',this.update,this,true);
    p.subscribe('lastPageLinkClassChange', this.update,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param paginator {Paginator} Paginator instance to decorate
 * @static
 */
ui.LastPageLink.init = function (p) {

    /**
     * Used as innerHTML for the last page link/span.
     * @attribute lastPageLinkLabel
     * @default 'last&nbsp;&gt;&gt;'
     */
    p.setAttributeConfig('lastPageLinkLabel', {
        value : 'last&nbsp;&gt;&gt;',
        validator : l.isString
    });

    /**
     * CSS class assigned to the link/span
     * @attribute lastPageLinkClass
     * @default 'yui-pg-last'
     */
    p.setAttributeConfig('lastPageLinkClass', {
        value : 'yui-pg-last',
        validator : l.isString
    });
};

ui.LastPageLink.prototype = {

    /**
     * Currently placed HTMLElement node
     * @property current
     * @type HTMLElement
     * @private
     */
    current   : null,

    /**
     * Link HTMLElement node
     * @property link
     * @type HTMLElement
     * @private
     */
    link      : null,

    /**
     * Span node (inactive link)
     * @property span
     * @type HTMLElement
     * @private
     */
    span      : null,

    /**
     * Empty place holder node for when the last page link is inappropriate to
     * display in any form (unlimited paging).
     * @property na
     * @type HTMLElement
     * @private
     */
    na        : null,


    /**
     * Generate the nodes and return the appropriate node given the current
     * pagination state.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        var p     = this.paginator,
            c     = p.get('lastPageLinkClass'),
            label = p.get('lastPageLinkLabel'),
            last  = p.getTotalPages();

        this.link = document.createElement('a');
        this.span = document.createElement('span');
        this.na   = this.span.cloneNode(false);

        this.link.id        = id_base + '-last-link';
        this.link.href      = '#';
        this.link.className = c;
        this.link.innerHTML = label;
        YAHOO.util.Event.on(this.link,'click',this.onClick,this,true);

        this.span.id        = id_base + '-last-span';
        this.span.className = c;
        this.span.innerHTML = label;

        this.na.id = id_base + '-last-na';

        switch (last) {
            case Paginator.VALUE_UNLIMITED :
                    this.current = this.na; break;
            case p.getCurrentPage() :
                    this.current = this.span; break;
            default :
                    this.current = this.link;
        }

        return this.current;
    },

    /**
     * Swap the link, span, and na nodes if appropriate.
     * @method update
     * @param e {CustomEvent} The calling change event (ignored)
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var par   = this.current ? this.current.parentNode : null,
            after = this.link;

        if (par) {
            switch (this.paginator.getTotalPages()) {
                case Paginator.VALUE_UNLIMITED :
                        after = this.na; break;
                case this.paginator.getCurrentPage() :
                        after = this.span; break;
            }

            if (this.current !== after) {
                par.replaceChild(after,this.current);
                this.current = after;
            }
        }
    },

    /**
     * Removes the onClick listener from the link in preparation for content
     * removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.link);
    },

    /**
     * Listener for the link's onclick event.  Passes to setPage method.
     * @method onClick
     * @param e {DOMEvent} The click event
     */
    onClick : function (e) {
        YAHOO.util.Event.stopEvent(e);
        this.paginator.setPage(this.paginator.getTotalPages());
    }
};


/**
 * ui Component to generate the link to jump to the previous page.
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class PreviousPageLink
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.PreviousPageLink = function (p) {
    this.paginator = p;

    p.createEvent('previousPageLinkLabelChange');
    p.createEvent('previousPageLinkClassChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    // TODO: make this work
    p.subscribe('previousPageLinkLabelChange',this.update,this,true);
    p.subscribe('previousPageLinkClassChange',this.update,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.PreviousPageLink.init = function (p) {

    /**
     * Used as innerHTML for the previous page link/span.
     * @attribute previousPageLinkLabel
     * @default '&lt;&nbsp;prev'
     */
    p.setAttributeConfig('previousPageLinkLabel', {
        value : '&lt;&nbsp;prev',
        validator : l.isString
    });

    /**
     * CSS class assigned to the link/span
     * @attribute previousPageLinkClass
     * @default 'yui-pg-previous'
     */
    p.setAttributeConfig('previousPageLinkClass', {
        value : 'yui-pg-previous',
        validator : l.isString
    });
};

ui.PreviousPageLink.prototype = {

    /**
     * Currently placed HTMLElement node
     * @property current
     * @type HTMLElement
     * @private
     */
    current   : null,

    /**
     * Link node
     * @property link
     * @type HTMLElement
     * @private
     */
    link      : null,

    /**
     * Span node (inactive link)
     * @property span
     * @type HTMLElement
     * @private
     */
    span      : null,


    /**
     * Generate the nodes and return the appropriate node given the current
     * pagination state.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        var p     = this.paginator,
            c     = p.get('previousPageLinkClass'),
            label = p.get('previousPageLinkLabel');

        this.link     = document.createElement('a');
        this.span     = document.createElement('span');

        this.link.id        = id_base + '-prev-link';
        this.link.href      = '#';
        this.link.className = c;
        this.link.innerHTML = label;
        YAHOO.util.Event.on(this.link,'click',this.onClick,this,true);

        this.span.id        = id_base + '-prev-span';
        this.span.className = c;
        this.span.innerHTML = label;

        this.current = p.get('recordOffset') < 1 ? this.span : this.link;
        return this.current;
    },

    /**
     * Swap the link and span nodes if appropriate.
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var par = this.current ? this.current.parentNode : null;
        if (this.paginator.get('recordOffset') < 1) {
            if (par && this.current === this.link) {
                par.replaceChild(this.span,this.current);
                this.current = this.span;
            }
        } else {
            if (par && this.current === this.span) {
                par.replaceChild(this.link,this.current);
                this.current = this.link;
            }
        }
    },

    /**
     * Removes the onClick listener from the link in preparation for content
     * removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.link);
    },

    /**
     * Listener for the link's onclick event.  Passes to setPage method.
     * @method onClick
     * @param e {DOMEvent} The click event
     */
    onClick : function (e) {
        YAHOO.util.Event.stopEvent(e);
        this.paginator.setPage(this.paginator.getPreviousPage());
    }
};



/**
 * ui Component to generate the link to jump to the next page.
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class NextPageLink
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.NextPageLink = function (p) {
    this.paginator = p;

    p.createEvent('nextPageLinkLabelChange');
    p.createEvent('nextPageLinkClassChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('totalRecordsChange',this.update,this,true);
    p.subscribe('rowsPerPageChange', this.update,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    // TODO: make this work
    p.subscribe('nextPageLinkLabelChange', this.update,this,true);
    p.subscribe('nextPageLinkClassChange', this.update,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.NextPageLink.init = function (p) {

    /**
     * Used as innerHTML for the next page link/span.
     * @attribute nextPageLinkLabel
     * @default 'next&nbsp;&gt;'
     */
    p.setAttributeConfig('nextPageLinkLabel', {
        value : 'next&nbsp;&gt;',
        validator : l.isString
    });

    /**
     * CSS class assigned to the link/span
     * @attribute nextPageLinkClass
     * @default 'yui-pg-next'
     */
    p.setAttributeConfig('nextPageLinkClass', {
        value : 'yui-pg-next',
        validator : l.isString
    });
};

ui.NextPageLink.prototype = {

    /**
     * Currently placed HTMLElement node
     * @property current
     * @type HTMLElement
     * @private
     */
    current   : null,

    /**
     * Link node
     * @property link
     * @type HTMLElement
     * @private
     */
    link      : null,

    /**
     * Span node (inactive link)
     * @property span
     * @type HTMLElement
     * @private
     */
    span      : null,


    /**
     * Generate the nodes and return the appropriate node given the current
     * pagination state.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        var p     = this.paginator,
            c     = p.get('nextPageLinkClass'),
            label = p.get('nextPageLinkLabel'),
            last  = p.getTotalPages();

        this.link     = document.createElement('a');
        this.span     = document.createElement('span');

        this.link.id        = id_base + '-next-link';
        this.link.href      = '#';
        this.link.className = c;
        this.link.innerHTML = label;
        YAHOO.util.Event.on(this.link,'click',this.onClick,this,true);

        this.span.id        = id_base + '-next-span';
        this.span.className = c;
        this.span.innerHTML = label;

        this.current = p.getCurrentPage() === last ? this.span : this.link;

        return this.current;
    },

    /**
     * Swap the link and span nodes if appropriate.
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var last = this.paginator.getTotalPages(),
            par  = this.current ? this.current.parentNode : null;

        if (this.paginator.getCurrentPage() !== last) {
            if (par && this.current === this.span) {
                par.replaceChild(this.link,this.current);
                this.current = this.link;
            }
        } else if (this.current === this.link) {
            if (par) {
                par.replaceChild(this.span,this.current);
                this.current = this.span;
            }
        }
    },

    /**
     * Removes the onClick listener from the link in preparation for content
     * removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.link);
    },

    /**
     * Listener for the link's onclick event.  Passes to setPage method.
     * @method onClick
     * @param e {DOMEvent} The click event
     */
    onClick : function (e) {
        YAHOO.util.Event.stopEvent(e);
        this.paginator.setPage(this.paginator.getNextPage());
    }
};


/**
 * ui Component to generate the page links
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class PageLinks
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.PageLinks = function (p) {
    this.paginator = p;

    p.createEvent('pageLinkClassChange');
    p.createEvent('currentPageClassChange');
    p.createEvent('pageLinksContainerClassChange');
    p.createEvent('pageLinksChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('pageLinksChange',   this.rebuild,this,true);
    p.subscribe('totalRecordsChange',this.rebuild,this,true);
    p.subscribe('rowsPerPageChange', this.rebuild,this,true);
    p.subscribe('pageLinkClassChange', this.rebuild,this,true);
    p.subscribe('currentPageClassChange', this.rebuild,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    //TODO: Make this work
    p.subscribe('pageLinksContainerClassChange', this.rebuild,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.PageLinks.init = function (p) {

    /**
     * CSS class assigned to each page link/span.
     * @attribute pageLinkClass
     * @default 'yui-pg-page'
     */
    p.setAttributeConfig('pageLinkClass', {
        value : 'yui-pg-page',
        validator : l.isString
    });

    /**
     * CSS class assigned to the current page span.
     * @attribute currentPageClass
     * @default 'yui-pg-current-page'
     */
    p.setAttributeConfig('currentPageClass', {
        value : 'yui-pg-current-page',
        validator : l.isString
    });

    /**
     * CSS class assigned to the span containing the page links.
     * @attribute pageLinksContainerClass
     * @default 'yui-pg-pages'
     */
    p.setAttributeConfig('pageLinksContainerClass', {
        value : 'yui-pg-pages',
        validator : l.isString
    });

    /**
     * Maximum number of page links to display at one time.
     * @attribute pageLinks
     * @default 10
     */
    p.setAttributeConfig('pageLinks', {
        value : 10,
        validator : l.isNumber
    });

    /**
     * Function used generate the innerHTML for each page link/span.  The
     * function receives as parameters the page number and a reference to the
     * paginator object.
     * @attribute pageLabelBuilder
     * @default function (page, paginator) { return page; }
     */
    p.setAttributeConfig('pageLabelBuilder', {
        value : function (page, paginator) { return page; },
        validator : l.isFunction
    });
};

/**
 * Calculates start and end page numbers given a current page, attempting
 * to keep the current page in the middle
 * @static
 * @method calculateRange
 * @param {int} currentPage  The current page
 * @param {int} totalPages   (optional) Maximum number of pages
 * @param {int} numPages     (optional) Preferred number of pages in range
 * @return {Array} [start_page_number, end_page_number]
 */
ui.PageLinks.calculateRange = function (currentPage,totalPages,numPages) {
    var UNLIMITED = Paginator.VALUE_UNLIMITED,
        start, end, delta;

    if (!currentPage) {
        return null;
    }

    // Either has no pages, or unlimited pages.  Show none.
    if (numPages === 0 || totalPages === 0 ||
        (totalPages === UNLIMITED && numPages === UNLIMITED)) {
        return [0,-1];
    }

    // Limit requested pageLinks if there are fewer totalPages
    if (totalPages !== UNLIMITED) {
        numPages = numPages === UNLIMITED ?
                    totalPages :
                    Math.min(numPages,totalPages);
    }

    // Determine start and end, trying to keep current in the middle
    start = Math.max(1,Math.ceil(currentPage - (numPages/2)));
    if (totalPages === UNLIMITED) {
        end = start + numPages - 1;
    } else {
        end = Math.min(totalPages, start + numPages - 1);
    }

    // Adjust the start index when approaching the last page
    delta = numPages - (end - start + 1);
    start = Math.max(1, start - delta);

    return [start,end];
};


ui.PageLinks.prototype = {

    /**
     * Current page
     * @property current
     * @type number
     * @private
     */
    current     : null,

    /**
     * Span node containing the page links
     * @property container
     * @type HTMLElement
     * @private
     */
    container   : null,


    /**
     * Generate the nodes and return the container node containing page links
     * appropriate to the current pagination state.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        var p = this.paginator;

        // Set up container
        this.container = document.createElement('span');
        this.container.id        = id_base + '-pages';
        this.container.className = p.get('pageLinksContainerClass');
        YAHOO.util.Event.on(this.container,'click',this.onClick,this,true);

        // Call update, flagging a need to rebuild
        this.update({newValue : null, rebuild : true});

        return this.container;
    },

    /**
     * Update the links if appropriate
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var p           = this.paginator,
            currentPage = p.getCurrentPage();

        // Replace content if there's been a change
        if (this.current !== currentPage || e.rebuild) {
            var labelBuilder = p.get('pageLabelBuilder'),
                range        = ui.PageLinks.calculateRange(
                                currentPage,
                                p.getTotalPages(),
                                p.get('pageLinks')),
                start        = range[0],
                end          = range[1],
                content      = '',
                linkTemplate,i;

            linkTemplate = '<a href="#" class="' + p.get('pageLinkClass') +
                           '" page="';
            for (i = start; i <= end; ++i) {
                if (i === currentPage) {
                    content +=
                        '<span class="' + p.get('currentPageClass') + ' ' +
                                          p.get('pageLinkClass') + '">' +
                        labelBuilder(i,p) + '</span>';
                } else {
                    content +=
                        linkTemplate + i + '">' + labelBuilder(i,p) + '</a>';
                }
            }

            this.container.innerHTML = content;
        }
    },

    /**
     * Force a rebuild of the page links.
     * @method rebuild
     * @param e {CustomEvent} The calling change event
     */
    rebuild     : function (e) {
        e.rebuild = true;
        this.update(e);
    },

    /**
     * Removes the onClick listener from the container in preparation for
     * content removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.container,true);
    },

    /**
     * Listener for the container's onclick event.  Looks for qualifying link
     * clicks, and pulls the page number from the link's page attribute.
     * Sends link's page attribute to the Paginator's setPage method.
     * @method onClick
     * @param e {DOMEvent} The click event
     */
    onClick : function (e) {
        var t = YAHOO.util.Event.getTarget(e);
        if (t && YAHOO.util.Dom.hasClass(t,
                        this.paginator.get('pageLinkClass'))) {

            YAHOO.util.Event.stopEvent(e);

            this.paginator.setPage(parseInt(t.getAttribute('page'),10));
        }
    }

};


/**
 * ui Component to generate the rows-per-page dropdown
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class RowsPerPageDropdown
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.RowsPerPageDropdown = function (p) {
    this.paginator = p;

    p.createEvent('rowsPerPageOptionsChange');
    p.createEvent('rowsPerPageDropdownClassChange');

    p.subscribe('rowsPerPageChange',this.update,this,true);
    p.subscribe('rowsPerPageOptionsChange',this.rebuild,this,true);
    p.subscribe('beforeDestroy',this.destroy,this,true);

    // TODO: make this work
    p.subscribe('rowsPerPageDropdownClassChange',this.rebuild,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.RowsPerPageDropdown.init = function (p) {

    /**
     * Array of available rows-per-page sizes.  Converted into select options.
     * Array values may be positive integers or object literals in the form<br>
     * { value : NUMBER, text : STRING }
     * @attribute rowsPerPageOptions
     * @default []
     */
    p.setAttributeConfig('rowsPerPageOptions', {
        value : [],
        validator : l.isArray
    });

    /**
     * CSS class assigned to the select node
     * @attribute rowsPerPageDropdownClass
     * @default 'yui-pg-rpp-options'
     */
    p.setAttributeConfig('rowsPerPageDropdownClass', {
        value : 'yui-pg-rpp-options',
        validator : l.isString
    });
};

ui.RowsPerPageDropdown.prototype = {

    /**
     * select node
     * @property select
     * @type HTMLElement
     * @private
     */
    select  : null,


    /**
     * Generate the select and option nodes and returns the select node.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        this.select = document.createElement('select');
        this.select.id        = id_base + '-rpp';
        this.select.className = this.paginator.get('rowsPerPageDropdownClass');
        this.select.title = 'Rows per page';

        YAHOO.util.Event.on(this.select,'change',this.onChange,this,true);

        this.rebuild();

        return this.select;
    },

    /**
     * Select the appropriate option if changed.
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }

        var rpp     = this.paginator.get('rowsPerPage'),
            options = this.select.options,
            i,len;

        for (i = 0, len = options.length; i < len; ++i) {
            if (parseInt(options[i].value,10) === rpp) {
                options[i].selected = true;
            }
        }
    },


    /**
     * (Re)generate the select options.
     * @method rebuild
     */
    rebuild : function (e) {
        var p       = this.paginator,
            sel     = this.select,
            options = p.get('rowsPerPageOptions'),
            opt_tem = document.createElement('option'),
            i,len;

        while (sel.firstChild) {
            sel.removeChild(sel.firstChild);
        }

        for (i = 0, len = options.length; i < len; ++i) {
            var node = opt_tem.cloneNode(false),
                opt  = options[i];
            node.value = l.isValue(opt.value) ? opt.value : opt;
            node.innerHTML = l.isValue(opt.text) ? opt.text : opt;
            sel.appendChild(node);
        }

        this.update();
    },

    /**
     * Removes the onChange listener from the select in preparation for content
     * removal.
     * @method destroy
     * @private
     */
    destroy : function () {
        YAHOO.util.Event.purgeElement(this.select);
    },

    /**
     * Listener for the select's onchange event.  Sent to setRowsPerPage method.
     * @method onChange
     * @param e {DOMEvent} The change event
     */
    onChange : function (e) {
        this.paginator.setRowsPerPage(
                parseInt(this.select.options[this.select.selectedIndex].value,10));
    }
};



/**
 * ui Component to generate the textual report of current pagination status.
 * E.g. "Now viewing page 1 of 13".
 *
 * @namespace YAHOO.widget.Paginator.ui
 * @class CurrentPageReport
 * @for YAHOO.widget.Paginator
 *
 * @constructor
 * @param p {Pagintor} Paginator instance to attach to
 */
ui.CurrentPageReport = function (p) {
    this.paginator = p;

    p.createEvent('pageReportClassChange');
    p.createEvent('pageReportTemplateChange');

    p.subscribe('recordOffsetChange',this.update,this,true);
    p.subscribe('totalRecordsChange',this.update,this,true);
    p.subscribe('rowsPerPageChange', this.update,this,true);
    p.subscribe('pageReportTemplateChange', this.update,this,true);

    //TODO: make this work
    p.subscribe('pageReportClassChange', this.update,this,true);
};

/**
 * Decorates Paginator instances with new attributes. Called during
 * Paginator instantiation.
 * @method init
 * @param p {Paginator} Paginator instance to decorate
 * @static
 */
ui.CurrentPageReport.init = function (p) {

    /**
     * CSS class assigned to the span containing the info.
     * @attribute pageReportClass
     * @default 'yui-pg-current'
     */
    p.setAttributeConfig('pageReportClass', {
        value : 'yui-pg-current',
        validator : l.isString
    });

    /**
     * Used as innerHTML for the span.  Place holders in the form of {name}
     * will be replaced with the so named value from the key:value map
     * generated by the function held in the pageReportValueGenerator attribute.
     * @attribute pageReportTemplate
     * @default '({currentPage} of {totalPages})'
     * @see pageReportValueGenerator attribute
     */
    p.setAttributeConfig('pageReportTemplate', {
        value : '({currentPage} of {totalPages})',
        validator : l.isString
    });

    /**
     * Function to generate the value map used to populate the
     * pageReportTemplate.  The function is passed the Paginator instance as a
     * parameter.  The default function returns a map with the following keys:
     * <ul>
     * <li>currentPage</li>
     * <li>totalPages</li>
     * <li>startIndex</li>
     * <li>endIndex</li>
     * <li>startRecord</li>
     * <li>endRecord</li>
     * <li>totalRecords</li>
     * </ul>
     * @attribute pageReportValueGenarator
     */
    p.setAttributeConfig('pageReportValueGenerator', {
        value : function (paginator) {
            var curPage = paginator.getCurrentPage(),
                records = paginator.getPageRecords(curPage);

            return {
                'currentPage' : curPage,
                'totalPages'  : paginator.getTotalPages(),
                'startIndex'  : records[0],
                'endIndex'    : records[1],
                'startRecord' : records[0] + 1,
                'endRecord'   : records[1] + 1,
                'totalRecords': paginator.get('totalRecords')
            };
        },
        validator : l.isFunction
    });
};

/**
 * Replace place holders in a string with the named values found in an
 * object literal.
 * @static
 * @method sprintf
 * @param template {string} The content string containing place holders
 * @param values {object} The key:value pairs used to replace the place holders
 * @return {string}
 */
ui.CurrentPageReport.sprintf = function (template, values) {
    return template.replace(/{([\w\s\-]+)}/g, function (x,key) {
            return (key in values) ? values[key] : '';
        });
};

ui.CurrentPageReport.prototype = {

    /**
     * Span node containing the formatted info
     * @property span
     * @type HTMLElement
     * @private
     */
    span : null,


    /**
     * Generate the span containing info formatted per the pageReportTemplate
     * attribute.
     * @method render
     * @param id_base {string} used to create unique ids for generated nodes
     * @return {HTMLElement}
     */
    render : function (id_base) {
        this.span = document.createElement('span');
        this.span.id        = id_base + '-page-report';
        this.span.className = this.paginator.get('pageReportClass');
        this.update();
        
        return this.span;
    },
    
    /**
     * Regenerate the content of the span if appropriate. Calls
     * CurrentPageReport.sprintf with the value of the pageReportTemplate
     * attribute and the value map returned from pageReportValueGenerator
     * function.
     * @method update
     * @param e {CustomEvent} The calling change event
     */
    update : function (e) {
        if (e && e.prevValue === e.newValue) {
            return;
        }


        this.span.innerHTML = ui.CurrentPageReport.sprintf(
            this.paginator.get('pageReportTemplate'),
            this.paginator.get('pageReportValueGenerator')(this.paginator));
    }
};

})();

/**
 * The DataTable widget provides a progressively enhanced DHTML control for
 * displaying tabular data across A-grade browsers.
 *
 * @module datatable
 * @requires yahoo, dom, event, element, datasource
 * @optional connection, dragdrop
 * @title DataTable Widget
 * @beta
 */

/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * DataTable class for the YUI DataTable widget.
 *
 * @namespace YAHOO.widget
 * @class DataTable
 * @uses YAHOO.util.EventProvider
 * @constructor
 * @param elContainer {HTMLElement} Container element for the TABLE.
 * @param aColumnDefs {Object[]} Array of object literal Column definitions.
 * @param oDataSource {YAHOO.util.DataSource} DataSource instance.
 * @param oConfigs {object} (optional) Object literal of configuration values.
 */
YAHOO.widget.DataTable = function(elContainer,aColumnDefs,oDataSource,oConfigs) {
    var DT = YAHOO.widget.DataTable,
        DS = YAHOO.util.DataSource;

    // Internal vars
    this._nIndex = DT._nCount;
    this._sId = "yui-dt"+this._nIndex;
    this._oChain = new YAHOO.util.Chain();

    // Initialize configs
    this._initConfigs(oConfigs);

    // Initialize DataSource
    this._initDataSource(oDataSource);
    if(!this._oDataSource) {
        YAHOO.log("Could not instantiate DataTable due to an invalid DataSource", "error", this.toString());
        return;
    }

    // Initialize ColumnSet
    this._initColumnSet(aColumnDefs);
    if(!this._oColumnSet) {
        YAHOO.log("Could not instantiate DataTable due to an invalid ColumnSet", "error", this.toString());
        return;
    }

    // Initialize RecordSet
    this._initRecordSet();
    if(!this._oRecordSet) {
        YAHOO.log("Could not instantiate DataTable due to an invalid RecordSet", "error", this.toString());
        return;
    }

    // Initialize node templates
    this._initNodeTemplates();

    // Initialize container element
    this._initContainerEl(elContainer);
    if(!this._elContainer) {
        YAHOO.log("Could not instantiate DataTable due to an invalid container element", "error", this.toString());
        return;
    }

    // Initialize the rest of the DOM elements
    this._initTableEl();
    if(!this._elContainer || !this._elThead || !this._elTbody) {
        YAHOO.log("Could not instantiate DataTable due to an invalid DOM elements", "error", this.toString());
        return;
    }

    // Call Element's constructor after DOM elements are created
    // but *before* table is populated with data
    DT.superclass.constructor.call(this, this._elContainer, this._oConfigs);

    // HACK: Set sortedBy values for backward compatibility
    var oSortedBy = this.get("sortedBy");
    if(oSortedBy) {
        if(oSortedBy.dir == "desc") {
            this._configs.sortedBy.value.dir = DT.CLASS_DESC;
        }
        else if(oSortedBy.dir == "asc") {
            this._configs.sortedBy.value.dir = DT.CLASS_ASC;
        }
    }

    //HACK: Set the paginator values.  Attribute doesn't afford for merging
    // obj value's keys.  It's all or nothing.  Merge in provided keys.
    if(this._oConfigs.paginator && !(this._oConfigs.paginator instanceof YAHOO.widget.Paginator)) {
        // Backward compatibility
        this.updatePaginator(this._oConfigs.paginator);
    }

    // Initialize inline Cell editing
    this._initCellEditorEl();
    
    // Initialize Column sort
    this._initColumnSort();

    // Once per instance
    YAHOO.util.Event.addListener(document, "click", this._onDocumentClick, this);

    DT._nCount++;
    DT._nCurrentCount++;
    
    // Send a simple initial request
    var oCallback = {
        success : this.onDataReturnSetRecords,
        failure : this.onDataReturnSetRecords,
        scope   : this,
        argument: {}
    };
    if(this.get("initialLoad") === true) {
        this._oDataSource.sendRequest(this.get("initialRequest"), oCallback);
    }
    // Do not send an initial request at all
    else if(this.get("initialLoad") === false) {
        this.showTableMessage(DT.MSG_EMPTY, DT.CLASS_EMPTY);
        this._oChain.add({
            method: function() {
                if((this instanceof DT) && this._sId && this._bInit) {
                    this._bInit = false;
                    this.fireEvent("initEvent");
                    YAHOO.log("DataTable initialized with no rows", "info", this.toString());
                }
            },
            scope: this
        });
        this._oChain.run();
    }
    // Send an initial request with a custom payload
    else {
        var oCustom = this.get("initialLoad");
        oCallback.argument = oCustom.argument;
        this._oDataSource.sendRequest(oCustom.request, oCallback);
    }
};

/////////////////////////////////////////////////////////////////////////////
//
// Public constants
//
/////////////////////////////////////////////////////////////////////////////
(function () {

var lang   = YAHOO.lang,
    util   = YAHOO.util,
    widget = YAHOO.widget,
    ua     = YAHOO.env.ua,
    
    Dom    = util.Dom,
    Ev     = util.Event,
    DS     = util.DataSource,
    DT     = widget.DataTable,
    Pag    = widget.Paginator;
    

    

lang.augmentObject(DT, {

    /**
     * Class name assigned to liner DIV elements.
     *
     * @property DataTable.CLASS_LINER
     * @type String
     * @static
     * @final
     * @default "yui-dt-liner"
     */
    CLASS_LINER : "yui-dt-liner",

    /**
     * Class name assigned to display label elements.
     *
     * @property DataTable.CLASS_LABEL
     * @type String
     * @static
     * @final
     * @default "yui-dt-label"
     */
    CLASS_LABEL : "yui-dt-label",

    /**
     * Class name assigned to Column drag target.
     *
     * @property DataTable.CLASS_COLTARGET
     * @type String
     * @static
     * @final
     * @default "yui-dt-coltarget"
     */
    CLASS_COLTARGET : "yui-dt-coltarget",

    /**
     * Class name assigned to resizer handle elements.
     *
     * @property DataTable.CLASS_RESIZER
     * @type String
     * @static
     * @final
     * @default "yui-dt-resizer"
     */
    CLASS_RESIZER : "yui-dt-resizer",

    /**
     * Class name assigned to resizer proxy elements.
     *
     * @property DataTable.CLASS_RESIZERPROXY
     * @type String
     * @static
     * @final
     * @default "yui-dt-resizerproxy"
     */
    CLASS_RESIZERPROXY : "yui-dt-resizerproxy",

    /**
     * Class name assigned to Editor container elements.
     *
     * @property DataTable.CLASS_EDITOR
     * @type String
     * @static
     * @final
     * @default "yui-dt-editor"
     */
    CLASS_EDITOR : "yui-dt-editor",

    /**
     * Class name assigned to paginator container elements.
     *
     * @property DataTable.CLASS_PAGINATOR
     * @type String
     * @static
     * @final
     * @default "yui-dt-paginator"
     */
    CLASS_PAGINATOR : "yui-dt-paginator",

    /**
     * Class name assigned to page number indicators.
     *
     * @property DataTable.CLASS_PAGE
     * @type String
     * @static
     * @final
     * @default "yui-dt-page"
     */
    CLASS_PAGE : "yui-dt-page",

    /**
     * Class name assigned to default indicators.
     *
     * @property DataTable.CLASS_DEFAULT
     * @type String
     * @static
     * @final
     * @default "yui-dt-default"
     */
    CLASS_DEFAULT : "yui-dt-default",

    /**
     * Class name assigned to previous indicators.
     *
     * @property DataTable.CLASS_PREVIOUS
     * @type String
     * @static
     * @final
     * @default "yui-dt-previous"
     */
    CLASS_PREVIOUS : "yui-dt-previous",

    /**
     * Class name assigned next indicators.
     *
     * @property DataTable.CLASS_NEXT
     * @type String
     * @static
     * @final
     * @default "yui-dt-next"
     */
    CLASS_NEXT : "yui-dt-next",

    /**
     * Class name assigned to first elements.
     *
     * @property DataTable.CLASS_FIRST
     * @type String
     * @static
     * @final
     * @default "yui-dt-first"
     */
    CLASS_FIRST : "yui-dt-first",

    /**
     * Class name assigned to last elements.
     *
     * @property DataTable.CLASS_LAST
     * @type String
     * @static
     * @final
     * @default "yui-dt-last"
     */
    CLASS_LAST : "yui-dt-last",

    /**
     * Class name assigned to even elements.
     *
     * @property DataTable.CLASS_EVEN
     * @type String
     * @static
     * @final
     * @default "yui-dt-even"
     */
    CLASS_EVEN : "yui-dt-even",

    /**
     * Class name assigned to odd elements.
     *
     * @property DataTable.CLASS_ODD
     * @type String
     * @static
     * @final
     * @default "yui-dt-odd"
     */
    CLASS_ODD : "yui-dt-odd",

    /**
     * Class name assigned to selected elements.
     *
     * @property DataTable.CLASS_SELECTED
     * @type String
     * @static
     * @final
     * @default "yui-dt-selected"
     */
    CLASS_SELECTED : "yui-dt-selected",

    /**
     * Class name assigned to highlighted elements.
     *
     * @property DataTable.CLASS_HIGHLIGHTED
     * @type String
     * @static
     * @final
     * @default "yui-dt-highlighted"
     */
    CLASS_HIGHLIGHTED : "yui-dt-highlighted",

    /**
     * Class name assigned to hidden elements.
     *
     * @property DataTable.CLASS_HIDDEN
     * @type String
     * @static
     * @final
     * @default "yui-dt-hidden"
     */
    CLASS_HIDDEN : "yui-dt-hidden",

    /**
     * Class name assigned to disabled elements.
     *
     * @property DataTable.CLASS_DISABLED
     * @type String
     * @static
     * @final
     * @default "yui-dt-disabled"
     */
    CLASS_DISABLED : "yui-dt-disabled",

    /**
     * Class name assigned to empty indicators.
     *
     * @property DataTable.CLASS_EMPTY
     * @type String
     * @static
     * @final
     * @default "yui-dt-empty"
     */
    CLASS_EMPTY : "yui-dt-empty",

    /**
     * Class name assigned to loading indicatorx.
     *
     * @property DataTable.CLASS_LOADING
     * @type String
     * @static
     * @final
     * @default "yui-dt-loading"
     */
    CLASS_LOADING : "yui-dt-loading",

    /**
     * Class name assigned to error indicators.
     *
     * @property DataTable.CLASS_ERROR
     * @type String
     * @static
     * @final
     * @default "yui-dt-error"
     */
    CLASS_ERROR : "yui-dt-error",

    /**
     * Class name assigned to editable elements.
     *
     * @property DataTable.CLASS_EDITABLE
     * @type String
     * @static
     * @final
     * @default "yui-dt-editable"
     */
    CLASS_EDITABLE : "yui-dt-editable",

    /**
     * Class name assigned to draggable elements.
     *
     * @property DataTable.CLASS_DRAGGABLE
     * @type String
     * @static
     * @final
     * @default "yui-dt-draggable"
     */
    CLASS_DRAGGABLE : "yui-dt-draggable",

    /**
     * Class name assigned to resizeable elements.
     *
     * @property DataTable.CLASS_RESIZEABLE
     * @type String
     * @static
     * @final
     * @default "yui-dt-resizeable"
     */
    CLASS_RESIZEABLE : "yui-dt-resizeable",

    /**
     * Class name assigned to scrollable elements.
     *
     * @property DataTable.CLASS_SCROLLABLE
     * @type String
     * @static
     * @final
     * @default "yui-dt-scrollable"
     */
    CLASS_SCROLLABLE : "yui-dt-scrollable",

    /**
     * Class name assigned to sortable elements.
     *
     * @property DataTable.CLASS_SORTABLE
     * @type String
     * @static
     * @final
     * @default "yui-dt-sortable"
     */
    CLASS_SORTABLE : "yui-dt-sortable",

    /**
     * Class name assigned to ascending elements.
     *
     * @property DataTable.CLASS_ASC
     * @type String
     * @static
     * @final
     * @default "yui-dt-asc"
     */
    CLASS_ASC : "yui-dt-asc",

    /**
     * Class name assigned to descending elements.
     *
     * @property DataTable.CLASS_DESC
     * @type String
     * @static
     * @final
     * @default "yui-dt-desc"
     */
    CLASS_DESC : "yui-dt-desc",

    /**
     * Class name assigned to BUTTON elements and/or container elements.
     *
     * @property DataTable.CLASS_BUTTON
     * @type String
     * @static
     * @final
     * @default "yui-dt-button"
     */
    CLASS_BUTTON : "yui-dt-button",

    /**
     * Class name assigned to INPUT TYPE=CHECKBOX elements and/or container elements.
     *
     * @property DataTable.CLASS_CHECKBOX
     * @type String
     * @static
     * @final
     * @default "yui-dt-checkbox"
     */
    CLASS_CHECKBOX : "yui-dt-checkbox",

    /**
     * Class name assigned to SELECT elements and/or container elements.
     *
     * @property DataTable.CLASS_DROPDOWN
     * @type String
     * @static
     * @final
     * @default "yui-dt-dropdown"
     */
    CLASS_DROPDOWN : "yui-dt-dropdown",

    /**
     * Class name assigned to INPUT TYPE=RADIO elements and/or container elements.
     *
     * @property DataTable.CLASS_RADIO
     * @type String
     * @static
     * @final
     * @default "yui-dt-radio"
     */
    CLASS_RADIO : "yui-dt-radio",

    /**
     * Message to display if DataTable has no data.
     *
     * @property DataTable.MSG_EMPTY
     * @type String
     * @static
     * @final
     * @default "No records found."
     */
    MSG_EMPTY : "No records found.",

    /**
     * Message to display while DataTable is loading data.
     *
     * @property DataTable.MSG_LOADING
     * @type String
     * @static
     * @final
     * @default "Loading data..."
     */
    MSG_LOADING : "Loading data...",

    /**
     * Message to display while DataTable has data error.
     *
     * @property DataTable.MSG_ERROR
     * @type String
     * @static
     * @final
     * @default "Data error."
     */
    MSG_ERROR : "Data error.",

    /////////////////////////////////////////////////////////////////////////
    //
    // Private static variables
    //
    /////////////////////////////////////////////////////////////////////////

    /**
     * Internal class variable for indexing multiple DataTable instances.
     *
     * @property DataTable._nCount
     * @type Number
     * @private
     * @static
     */
    _nCount : 0,

    /**
     * Internal class variable tracking current number of DataTable instances,
     * so that certain class values can be reset when all instances are destroyed.          
     *
     * @property DataTable._nCurrentCount
     * @type Number
     * @private
     * @static
     */
    _nCurrentCount : 0,

    /**
     * Reference to STYLE node that is dynamically created and written to
     * in order to manage Column widths.
     *
     * @property DataTable._elStylesheet
     * @type HTMLElement
     * @private
     * @static     
     */
    _elStylesheet : null,

    /**
     * Set to true if _elStylesheet cannot be populated due to browser incompatibility.
     *
     * @property DataTable._bStylesheetFallback
     * @type boolean
     * @private
     * @static     
     */
    _bStylesheetFallback : false,

    /**
     * Object literal hash of Columns and their dynamically create style rules.
     *
     * @property DataTable._oStylesheetRules
     * @type Object
     * @private
     * @static     
     */
    _oStylesheetRules : {},

    /**
     * Element reference to shared Column drag target.
     *
     * @property DataTable._elColumnDragTarget
     * @type HTMLElement
     * @private
     * @static 
     */
    _elColumnDragTarget : null,

    /**
     * Element reference to shared Column resizer proxy.
     *
     * @property DataTable._elColumnResizerProxy
     * @type HTMLElement
     * @private
     * @static 
     */
    _elColumnResizerProxy : null,

    /**
     * Clones object literal or array of object literals.
     *
     * @method DataTable._cloneObject
     * @param o {Object} Object.
     * @private
     * @static     
     */
    _cloneObject : function(o) {
        if(lang.isUndefined(o)) {
            return o;
        }
        
        var copy = {};
        
        if(lang.isArray(o)) {
            var array = [];
            for(var i=0,len=o.length;i<len;i++) {
                array[i] = DT._cloneObject(o[i]);
            }
            copy = array;
        }
        else if(o.constructor == Object) { 
            for (var x in o){
                if(lang.hasOwnProperty(o, x)) {
                    if(lang.isValue(o[x]) && (o[x].constructor == Object) || lang.isArray(o[x])) {
                        copy[x] = DT._cloneObject(o[x]);
                    }
                    else {
                        copy[x] = o[x];
                    }
                }
            }
        }
        else {
            copy = o;
        }
    
        return copy;
    },

    /**
     * Creates HTML markup for shared Column drag target.
     *
     * @method DataTable._initColumnDragTargetEl
     * @return {HTMLElement} Reference to Column drag target. 
     * @private
     * @static 
     */
    _initColumnDragTargetEl : function() {
        if(!DT._elColumnDragTarget) {
            // Attach Column drag target element as first child of body
            var elColumnDragTarget = document.createElement('div');
            elColumnDragTarget.id = "yui-dt-coltarget";
            elColumnDragTarget.className = DT.CLASS_COLTARGET;
            elColumnDragTarget.style.display = "none";
            document.body.insertBefore(elColumnDragTarget, document.body.firstChild);

            // Internal tracker of Column drag target
            DT._elColumnDragTarget = elColumnDragTarget;
            
        }
        return DT._elColumnDragTarget;
    },

    /**
     * Creates HTML markup for shared Column resizer proxy.
     *
     * @method DataTable._initColumnResizerProxyEl
     * @return {HTMLElement} Reference to Column resizer proxy.
     * @private 
     * @static 
     */
    _initColumnResizerProxyEl : function() {
        if(!DT._elColumnResizerProxy) {

            // Attach Column resizer element as first child of body
            var elColumnResizerProxy = document.createElement("div");
            elColumnResizerProxy.id = "yui-dt-colresizerproxy";
            Dom.addClass(elColumnResizerProxy, DT.CLASS_RESIZERPROXY);
            document.body.insertBefore(elColumnResizerProxy, document.body.firstChild);

            // Internal tracker of Column resizer proxy
            DT._elColumnResizerProxy = elColumnResizerProxy;
        }
        return DT._elColumnResizerProxy;
    },

    /**
     * Outputs markup into the given TH based on given Column.
     *
     * @method DataTable.formatTheadCell
     * @param elCellLabel {HTMLElement} The label DIV element within the TH liner.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oSelf {DT} DataTable instance.
     * @static
     */
    formatTheadCell : function(elCellLabel, oColumn, oSelf) {
        var sKey = oColumn.getKey();
        var sLabel = lang.isValue(oColumn.label) ? oColumn.label : sKey;

        // Add accessibility link for sortable Columns
        if(oColumn.sortable) {
            // Calculate the direction
            var sSortClass = oSelf.getColumnSortDir(oColumn);
            var sSortDir = (sSortClass === DT.CLASS_DESC) ? "descending" : "ascending";

            // Generate a unique HREF for visited status
            var sHref = oSelf.getId() + "-sort" + oColumn.getId() + "-" + sSortDir;
            
            // Generate a dynamic TITLE for sort status
            var sTitle = "Click to sort " + sSortDir;
            
            // Format the element
            elCellLabel.innerHTML = "<a href=\"" + sHref + "\" title=\"" + sTitle + "\" class=\"" + DT.CLASS_SORTABLE + "\">" + sLabel + "</a>";
        }
        // Just display the label for non-sortable Columns
        else {
            elCellLabel.innerHTML = sLabel;
        }
    },

    /**
     * Formats a BUTTON element.
     *
     * @method DataTable.formatButton
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object | Boolean} Data value for the cell. By default, the value
     * is what gets written to the BUTTON.
     * @static
     */
    formatButton : function(el, oRecord, oColumn, oData) {
        var sValue = lang.isValue(oData) ? oData : "Click";
        //TODO: support YAHOO.widget.Button
        //if(YAHOO.widget.Button) {

        //}
        //else {
            el.innerHTML = "<button type=\"button\" class=\""+
                    DT.CLASS_BUTTON + "\">" + sValue + "</button>";
        //}
    },

    /**
     * Formats a CHECKBOX element.
     *
     * @method DataTable.formatCheckbox
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object | Boolean} Data value for the cell. Can be a simple
     * Boolean to indicate whether checkbox is checked or not. Can be object literal
     * {checked:bBoolean, label:sLabel}. Other forms of oData require a custom
     * formatter.
     * @static
     */
    formatCheckbox : function(el, oRecord, oColumn, oData) {
        var bChecked = oData;
        bChecked = (bChecked) ? " checked" : "";
        el.innerHTML = "<input type=\"checkbox\"" + bChecked +
                " class=\"" + DT.CLASS_CHECKBOX + "\">";
    },

    /**
     * Formats currency. Default unit is USD.
     *
     * @method DataTable.formatCurrency
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Number} Data value for the cell.
     * @static
     */
    formatCurrency : function(el, oRecord, oColumn, oData) {
        el.innerHTML = util.Number.format(oData, {
                prefix:"$",
                decimalPlaces:2,
                decimalSeparator:".",
                thousandsSeparator:","
            });
    },

    /**
     * Formats JavaScript Dates.
     *
     * @method DataTable.formatDate
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} Data value for the cell, or null.
     * @static
     */
    formatDate : function(el, oRecord, oColumn, oData) {
        el.innerHTML = util.Date.format(oData, {format:"MM/DD/YYYY"});
    },

    /**
     * Formats SELECT elements.
     *
     * @method DataTable.formatDropdown
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} Data value for the cell, or null.
     * @static
     */
    formatDropdown : function(el, oRecord, oColumn, oData) {
        var selectedValue = (lang.isValue(oData)) ? oData : oRecord.getData(oColumn.key);
        var options = (lang.isArray(oColumn.dropdownOptions)) ?
                oColumn.dropdownOptions : null;

        var selectEl;
        var collection = el.getElementsByTagName("select");

        // Create the form element only once, so we can attach the onChange listener
        if(collection.length === 0) {
            // Create SELECT element
            selectEl = document.createElement("select");
            Dom.addClass(selectEl, DT.CLASS_DROPDOWN);
            selectEl = el.appendChild(selectEl);

            // Add event listener
            Ev.addListener(selectEl,"change",this._onDropdownChange,this);
        }

        selectEl = collection[0];

        // Update the form element
        if(selectEl) {
            // Clear out previous options
            selectEl.innerHTML = "";

            // We have options to populate
            if(options) {
                // Create OPTION elements
                for(var i=0; i<options.length; i++) {
                    var option = options[i];
                    var optionEl = document.createElement("option");
                    optionEl.value = (lang.isValue(option.value)) ?
                            option.value : option;
                    optionEl.innerHTML = (lang.isValue(option.text)) ?
                            option.text : option;
                    optionEl = selectEl.appendChild(optionEl);
                }
            }
            // Selected value is our only option
            else {
                selectEl.innerHTML = "<option value=\"" + selectedValue + "\">" + selectedValue + "</option>";
            }
        }
        else {
            el.innerHTML = lang.isValue(oData) ? oData : "";
        }
    },

    /**
     * Formats emails.
     *
     * @method DataTable.formatEmail
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} Data value for the cell, or null.
     * @static
     */
    formatEmail : function(el, oRecord, oColumn, oData) {
        if(lang.isString(oData)) {
            el.innerHTML = "<a href=\"mailto:" + oData + "\">" + oData + "</a>";
        }
        else {
            el.innerHTML = lang.isValue(oData) ? oData : "";
        }
    },

    /**
     * Formats links.
     *
     * @method DataTable.formatLink
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} Data value for the cell, or null.
     * @static
     */
    formatLink : function(el, oRecord, oColumn, oData) {
        if(lang.isString(oData)) {
            el.innerHTML = "<a href=\"" + oData + "\">" + oData + "</a>";
        }
        else {
            el.innerHTML = lang.isValue(oData) ? oData : "";
        }
    },

    /**
     * Formats numbers.
     *
     * @method DataTable.formatNumber
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} Data value for the cell, or null.
     * @static
     */
    formatNumber : function(el, oRecord, oColumn, oData) {
        if(lang.isNumber(oData)) {
            el.innerHTML = oData;
        }
        else {
            el.innerHTML = lang.isValue(oData) ? oData : "";
        }
    },

    /**
     * Formats INPUT TYPE=RADIO elements.
     *
     * @method DataTable.formatRadio
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} (Optional) Data value for the cell.
     * @static
     */
    formatRadio : function(el, oRecord, oColumn, oData) {
        var bChecked = oData;
        bChecked = (bChecked) ? " checked" : "";
        el.innerHTML = "<input type=\"radio\"" + bChecked +
                " name=\"col" + oColumn.getId() + "-radio\"" +
                " class=\"" + DT.CLASS_RADIO+ "\">";
    },

    /**
     * Formats text strings.
     *
     * @method DataTable.formatText
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} (Optional) Data value for the cell.
     * @static
     */
    formatText : function(el, oRecord, oColumn, oData) {
        var value = (lang.isValue(oRecord.getData(oColumn.key))) ?
                oRecord.getData(oColumn.key) : "";
        //TODO: move to util function
        el.innerHTML = value.toString().replace(/&/g, "&#38;").replace(/</g, "&#60;").replace(/>/g, "&#62;");
    },

    /**
     * Formats TEXTAREA elements.
     *
     * @method DataTable.formatTextarea
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} (Optional) Data value for the cell.
     * @static
     */
    formatTextarea : function(el, oRecord, oColumn, oData) {
        var value = (lang.isValue(oRecord.getData(oColumn.key))) ?
                oRecord.getData(oColumn.key) : "";
        var markup = "<textarea>" + value + "</textarea>";
        el.innerHTML = markup;
    },

    /**
     * Formats INPUT TYPE=TEXT elements.
     *
     * @method DataTable.formatTextbox
     * @param el {HTMLElement} The element to format with markup.
     * @param oRecord {YAHOO.widget.Record} Record instance.
     * @param oColumn {YAHOO.widget.Column} Column instance.
     * @param oData {Object} (Optional) Data value for the cell.
     * @static
     */
    formatTextbox : function(el, oRecord, oColumn, oData) {
        var value = (lang.isValue(oRecord.getData(oColumn.key))) ?
                oRecord.getData(oColumn.key) : "";
        var markup = "<input type=\"text\" value=\"" + value + "\">";
        el.innerHTML = markup;
    },

    /**
     * Handles Pag changeRequest events for static DataSources
     * (i.e. DataSources that return all data immediately)
     * @method DataTable.handleSimplePagination
     * @param {object} the requested state of the pagination
     * @param {DataTable} the DataTable instance
     * @static     
     */
    handleSimplePagination : function (oState,self) {
        // Set the core pagination values silently (the second param)
        // to avoid looping back through the changeRequest mechanism
        oState.paginator.setTotalRecords(oState.totalRecords,true);
        oState.paginator.setStartIndex(oState.recordOffset,true);
        oState.paginator.setRowsPerPage(oState.rowsPerPage,true);

        self.render();
    },

    /**
     * Handles Pag changeRequest events for dynamic DataSources
     * such as DataSource.TYPE_XHR or DataSource.TYPE_JSFUNCTION.
     * @method DataTable.handleDataSourcePagination
     * @param {object} the requested state of the pagination
     * @param {DataTable} the DataTable instance
     * @static     
     */
    handleDataSourcePagination : function (oState,self) {
        var requestedRecords = oState.records[1] - oState.recordOffset;

        if (self._oRecordSet.hasRecords(oState.recordOffset, requestedRecords)) {
            DT.handleSimplePagination(oState,self);
        } else {
            // Translate the proposed page state into a DataSource request param
            var generateRequest = self.get('generateRequest');
            var request = generateRequest({ pagination : oState }, self);

            var callback = {
                success : self.onDataReturnSetRecords,
                failure : self.onDataReturnSetRecords,
                argument : {
                    startIndex : oState.recordOffset,
                    pagination : oState
                },
                scope : self
            };

            self._oDataSource.sendRequest(request, callback);
        }
    },

    /**
     * Enables CHECKBOX Editor.
     *
     * @method DataTable.editCheckbox
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    //DT.editCheckbox = function(elContainer, oRecord, oColumn, oEditor, oSelf) 
    editCheckbox : function(oEditor, oSelf) {
        var elCell = oEditor.cell;
        var oRecord = oEditor.record;
        var oColumn = oEditor.column;
        var elContainer = oEditor.container;
        var aCheckedValues = oEditor.value;
        if(!lang.isArray(aCheckedValues)) {
            aCheckedValues = [aCheckedValues];
        }

        // Checkboxes
        if(oColumn.editorOptions && lang.isArray(oColumn.editorOptions.checkboxOptions)) {
            var checkboxOptions = oColumn.editorOptions.checkboxOptions;
            var checkboxValue, checkboxId, elLabel, j, k;
            // First create the checkbox buttons in an IE-friendly way
            for(j=0; j<checkboxOptions.length; j++) {
                checkboxValue = lang.isValue(checkboxOptions[j].label) ?
                        checkboxOptions[j].label : checkboxOptions[j];
                checkboxId =  oSelf.getId() + "-editor-checkbox" + j;
                elContainer.innerHTML += "<input type=\"checkbox\"" +
                        " name=\"" + oSelf.getId() + "-editor-checkbox\"" +
                        " value=\"" + checkboxValue + "\"" +
                        " id=\"" +  checkboxId + "\">";
                // Then create the labels in an IE-friendly way
                elLabel = elContainer.appendChild(document.createElement("label"));
                elLabel.htmlFor = checkboxId;
                elLabel.innerHTML = checkboxValue;
            }
            var aCheckboxEls = [];
            var checkboxEl;
            // Loop through checkboxes to check them
            for(j=0; j<checkboxOptions.length; j++) {
                checkboxEl = Dom.get(oSelf.getId() + "-editor-checkbox" + j);
                aCheckboxEls.push(checkboxEl);
                for(k=0; k<aCheckedValues.length; k++) {
                    if(checkboxEl.value === aCheckedValues[k]) {
                        checkboxEl.checked = true;
                    }
                }
                // Focus the first checkbox
                if(j===0) {
                    oSelf._focusEl(checkboxEl);
                }
            }
            // Loop through checkboxes to assign click handlers
            for(j=0; j<checkboxOptions.length; j++) {
                checkboxEl = Dom.get(oSelf.getId() + "-editor-checkbox" + j);
                Ev.addListener(checkboxEl, "click", function(){
                    var aNewValues = [];
                    for(var m=0; m<aCheckboxEls.length; m++) {
                        if(aCheckboxEls[m].checked) {
                            aNewValues.push(aCheckboxEls[m].value);
                        }
                    }
                    oSelf._oCellEditor.value = aNewValues;
                    oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
                });
            }
        }
    },

    /**
     * Enables Date Editor.
     *
     * @method DataTable.editDate
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    editDate : function(oEditor, oSelf) {
        var elCell = oEditor.cell;
        var oRecord = oEditor.record;
        var oColumn = oEditor.column;
        var elContainer = oEditor.container;
        var value = oEditor.value;
        
        // Set a default
        if(!(value instanceof Date)) {
            value = oEditor.defaultValue || new Date();
        }

        // Calendar widget
        if(YAHOO.widget.Calendar) {
            var selectedValue = (value.getMonth()+1)+"/"+value.getDate()+"/"+value.getFullYear();
            var calContainer = elContainer.appendChild(document.createElement("div"));
            var calPrefix = oColumn.getColEl();
            calContainer.id = calPrefix + "-dateContainer";
            var calendar =
                    new YAHOO.widget.Calendar(calPrefix + "-date",
                    calContainer.id,
                    {selected:selectedValue, pagedate:value});
            calendar.render();
            calContainer.style.cssFloat = "none";

            if(ua.ie == 6) {
                var calFloatClearer = elContainer.appendChild(document.createElement("br"));
                calFloatClearer.style.clear = "both";
            }

            calendar.selectEvent.subscribe(function(type, args, obj) {
                oSelf._oCellEditor.value = new Date(args[0][0][0], args[0][0][1]-1, args[0][0][2]);
                oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
            });
        }
        else {
            //TODO;
        }
    },

    /**
     * Enables SELECT Editor.
     *
     * @method DataTable.editDropdown
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    editDropdown : function(oEditor, oSelf) {
        var elCell = oEditor.cell;
        var oRecord = oEditor.record;
        var oColumn = oEditor.column;
        var elContainer = oEditor.container;
        var value = oEditor.value;
        
        // Set a default
        if(!lang.isValue(value)) {
            value = oEditor.defaultValue;
        }


        // Textbox
        var elDropdown = elContainer.appendChild(document.createElement("select"));
        var dropdownOptions = (oColumn.editorOptions && lang.isArray(oColumn.editorOptions.dropdownOptions)) ?
                oColumn.editorOptions.dropdownOptions : [];
        for(var j=0; j<dropdownOptions.length; j++) {
            var dropdownOption = dropdownOptions[j];
            var elOption = document.createElement("option");
            elOption.value = (lang.isValue(dropdownOption.value)) ?
                    dropdownOption.value : dropdownOption;
            elOption.innerHTML = (lang.isValue(dropdownOption.text)) ?
                    dropdownOption.text : dropdownOption;
            elOption = elDropdown.appendChild(elOption);
            if(value === elDropdown.options[j].value) {
                elDropdown.options[j].selected = true;
            }
        }

        // Set up a listener on each check box to track the input value
        Ev.addListener(elDropdown, "change",
            function(){
                oSelf._oCellEditor.value = elDropdown[elDropdown.selectedIndex].value;
                oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
        });

        // Focus the dropdown
        oSelf._focusEl(elDropdown);
    },

    /**
     * Enables INPUT TYPE=RADIO Editor.
     *
     * @method DataTable.editRadio
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    editRadio : function(oEditor, oSelf) {
        var elCell = oEditor.cell;
        var oRecord = oEditor.record;
        var oColumn = oEditor.column;
        var elContainer = oEditor.container;
        var value = oEditor.value;

        // Set a default
        if(!lang.isValue(value)) {
            value = oEditor.defaultValue;
        }

        // Radios
        if(oColumn.editorOptions && lang.isArray(oColumn.editorOptions.radioOptions)) {
            var radioOptions = oColumn.editorOptions.radioOptions;
            var radioValue, radioId, elLabel, j;
            // First create the radio buttons in an IE-friendly way
            for(j=0; j<radioOptions.length; j++) {
                radioValue = lang.isValue(radioOptions[j].label) ?
                        radioOptions[j].label : radioOptions[j];
                radioId =  oSelf.getId() + "-col" + oColumn.getId() + "-radioeditor" + j;
                elContainer.innerHTML += "<input type=\"radio\"" +
                        " name=\"" + oSelf.getId() + "-editor-radio\"" +
                        " value=\"" + radioValue + "\"" +
                        " id=\"" +  radioId + "\">";
                // Then create the labels in an IE-friendly way
                elLabel = elContainer.appendChild(document.createElement("label"));
                elLabel.htmlFor = radioId;
                elLabel.innerHTML = radioValue;
            }
            // Then check one, and assign click handlers
            for(j=0; j<radioOptions.length; j++) {
                var radioEl = Dom.get(oSelf.getId() + "-col" + oColumn.getId() + "-radioeditor" + j);
                if(value === radioEl.value) {
                    radioEl.checked = true;
                    oSelf._focusEl(radioEl);
                }
                Ev.addListener(radioEl, "click",
                    function(){
                        oSelf._oCellEditor.value = this.value;
                        oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
                });
            }
        }
    },

    /**
     * Enables TEXTAREA Editor.
     *
     * @method DataTable.editTextarea
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    editTextarea : function(oEditor, oSelf) {
       var elCell = oEditor.cell;
       var oRecord = oEditor.record;
       var oColumn = oEditor.column;
       var elContainer = oEditor.container;
       var value = oEditor.value;

        // Set a default
        if(!lang.isValue(value)) {
            value = oEditor.defaultValue || "";
        }

        // Textarea
        var elTextarea = elContainer.appendChild(document.createElement("textarea"));
        elTextarea.style.width = elCell.offsetWidth + "px"; //(parseInt(elCell.offsetWidth,10)) + "px";
        elTextarea.style.height = "3em"; //(parseInt(elCell.offsetHeight,10)) + "px";
        elTextarea.value = value;

        // Set up a listener on each check box to track the input value
        Ev.addListener(elTextarea, "keyup", function(){
            //TODO: set on a timeout
            oSelf._oCellEditor.value = elTextarea.value;
            oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
        });

        // Select the text
        elTextarea.focus();
        elTextarea.select();
    },

    /**
     * Enables INPUT TYPE=TEXT Editor.
     *
     * @method DataTable.editTextbox
     * @param oEditor {Object} Object literal representation of Editor values.
     * @param oSelf {DT} Reference back to DataTable instance.
     * @static
     */
    editTextbox : function(oEditor, oSelf) {
       var elCell = oEditor.cell;
       var oRecord = oEditor.record;
       var oColumn = oEditor.column;
       var elContainer = oEditor.container;
       var value = oEditor.value;

        // Set a default
        if(!lang.isValue(value)) {
            value = oEditor.defaultValue || "";
        }

        // Textbox
        var elTextbox = elContainer.appendChild(document.createElement("input"));
        elTextbox.type = "text";
        elTextbox.style.width = elCell.offsetWidth + "px"; //(parseInt(elCell.offsetWidth,10)) + "px";
        //elTextbox.style.height = "1em"; //(parseInt(elCell.offsetHeight,10)) + "px";
        elTextbox.value = value;

        // Set up a listener on each textbox to track the input value
        Ev.addListener(elTextbox, "keyup", function(){
            //TODO: set on a timeout
            oSelf._oCellEditor.value = elTextbox.value;
            oSelf.fireEvent("editorUpdateEvent",{editor:oSelf._oCellEditor});
        });

        // Select the text
        elTextbox.focus();
        elTextbox.select();
    },

    /**
     * Validates Editor input value to type Number, doing type conversion as
     * necessary. A valid Number value is return, else the previous value is returned
     * if input value does not validate.
     *
     *
     * @method DataTable.validateNumber
     * @param oData {Object} Data to validate.
     * @static
    */
    validateNumber : function(oData) {
        //Convert to number
        var number = oData * 1;

        // Validate
        if(lang.isNumber(number)) {
            return number;
        }
        else {
            YAHOO.log("Could not validate data " + lang.dump(oData) + " to type Number", "warn", this.toString());
            return null;
        }
    },

    /**
     * Translates (proposed) DataTable state data into a form consumable by
     * DataSource sendRequest as the request parameter.  Use
     * set('generateParameter', yourFunc) to use a custom function rather than this
     * one.
     * @method DataTable._generateRequest
     * @param oData {Object} Object literal defining the current or proposed state
     * @param oDataTable {DataTable} Reference to the DataTable instance
     * @returns {MIXED} Returns appropriate value based on DataSource type
     * @private
     * @static     
     */
    _generateRequest : function (oData, oDataTable) {
        var request = oData;

        if (oData.pagination) {
            if (oDataTable._oDataSource.dataType === DS.TYPE_XHR) {
                request = '?page=' +         oData.pagination.page +
                          '&recordOffset=' + oData.pagination.recordOffset +
                          '&rowsPerPage=' +  oData.pagination.rowsPerPage;
            }
        }
        
        return request;
    }
});

// Do in separate step so referenced properties are available
// TODO: editor shortcuts
/**
 * Cell formatting functions.
 * @property DataTable.Formatter
 * @type Object
 * @static
 */
DT.Formatter = {
    button   : DT.formatButton,
    checkbox : DT.formatCheckbox,
    currency : DT.formatCurrency,
    "date"   : DT.formatDate,
    dropdown : DT.formatDropdown,
    email    : DT.formatEmail,
    link     : DT.formatLink,
    "number" : DT.formatNumber,
    radio    : DT.formatRadio,
    text     : DT.formatText,
    textarea : DT.formatTextarea,
    textbox  : DT.formatTextbox
};

lang.extend(DT, util.Element, {

/////////////////////////////////////////////////////////////////////////////
//
// Superclass methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Implementation of Element's abstract method. Sets up config values.
 *
 * @method initAttributes
 * @param oConfigs {Object} (Optional) Object literal definition of configuration values.
 * @private
 */

initAttributes : function(oConfigs) {
    oConfigs = oConfigs || {};
    DT.superclass.initAttributes.call(this, oConfigs);

    /**
    * @attribute summary
    * @description Value for the SUMMARY attribute.
    * @type String
    */
    this.setAttributeConfig("summary", {
        value: null,
        validator: lang.isString,
        method: function(sSummary) {
            this._elThead.parentNode.summary = sSummary;
        }
    });

    /**
    * @attribute selectionMode
    * @description Specifies row or cell selection mode. Accepts the following strings:
    *    <dl>
    *      <dt>"standard"</dt>
    *      <dd>Standard row selection with support for modifier keys to enable
    *      multiple selections.</dd>
    *
    *      <dt>"single"</dt>
    *      <dd>Row selection with modifier keys disabled to not allow
    *      multiple selections.</dd>
    *
    *      <dt>"singlecell"</dt>
    *      <dd>Cell selection with modifier keys disabled to not allow
    *      multiple selections.</dd>
    *
    *      <dt>"cellblock"</dt>
    *      <dd>Cell selection with support for modifier keys to enable multiple
    *      selections in a block-fashion, like a spreadsheet.</dd>
    *
    *      <dt>"cellrange"</dt>
    *      <dd>Cell selection with support for modifier keys to enable multiple
    *      selections in a range-fashion, like a calendar.</dd>
    *    </dl>
    *
    * @default "standard"
    * @type String
    */
    this.setAttributeConfig("selectionMode", {
        value: "standard",
        validator: lang.isString
    });

    /**
    * @attribute initialRequest
    * @description Defines the initial request that gets sent to the DataSource
    * during initialization. Value is ignored if initialLoad is set to any value
    * other than true.    
    * @type MIXED
    * @default null
    */
    this.setAttributeConfig("initialRequest", {
        value: null
    });

    /**
    * @attribute initialLoad
    * @description Determines whether or not to load data at instantiation. By
    * default, will trigger a sendRequest() to the DataSource and pass in the
    * request defined by initialRequest. If set to false, data will not load
    * at instantiation. Alternatively, implementers who wish to work with a 
    * custom payload may pass in an object literal with the following values:
    *     
    *    <dl>
    *      <dt>request (MIXED)</dt>
    *      <dd>Request value.</dd>
    *
    *      <dt>argument (MIXED)</dt>
    *      <dd>Custom data that will be passed through to the callback function.</dd>
    *    </dl>
    *
    *                    
    * @type Boolean | Object
    * @default true
    */
    this.setAttributeConfig("initialLoad", {
        value: true
    });

    /**
     * @attribute generateRequest
     * @description A function used to translate proposed DataTable state info
     * into a value which is then passed to the DataSource's sendRequest method.
     * This function is called to get the DataTable's initial data as well as
     * any data changes or requests such as pagination or sorting.  The method
     * is passed two params, an object literal with the state data and a
     * reference to the DataTable.
     * @type function
     * @default DT._generateRequest
     */
    this.setAttributeConfig("generateRequest", {
        value: DT._generateRequest,
        validator: lang.isFunction
    });

    /**
    * @attribute sortedBy
    * @description Object literal provides metadata for initial sort values if
    * data will arrive pre-sorted:
    * <dl>
    *     <dt>sortedBy.key</dt>
    *     <dd>{String} Key of sorted Column</dd>
    *     <dt>sortedBy.dir</dt>
    *     <dd>{String} Initial sort direction, either DT.CLASS_ASC or DT.CLASS_DESC</dd>
    * </dl>
    * @type Object
    */
    this.setAttributeConfig("sortedBy", {
        value: null,
        // TODO: accepted array for nested sorts
        validator: function(oNewSortedBy) {
            return (oNewSortedBy && (oNewSortedBy.constructor == Object) && oNewSortedBy.key);
        },
        method: function(oNewSortedBy) {
            // Remove ASC/DESC from TH
            var oOldSortedBy = this.get("sortedBy");
            if(oOldSortedBy && (oOldSortedBy.constructor == Object) && oOldSortedBy.key) {
                var oldColumn = this._oColumnSet.getColumn(oOldSortedBy.key);
                var oldThEl = this.getThEl(oldColumn);
                Dom.removeClass(oldThEl, DT.CLASS_ASC);
                Dom.removeClass(oldThEl, DT.CLASS_DESC);
            }

            // Set ASC/DESC on TH
            var column = (oNewSortedBy.column) ? oNewSortedBy.column : this._oColumnSet.getColumn(oNewSortedBy.key);
            if(column) {
                // Backward compatibility
                if(oNewSortedBy.dir && ((oNewSortedBy.dir == "asc") ||  (oNewSortedBy.dir == "desc"))) {
                    var newClass = (oNewSortedBy.dir == "desc") ?
                            DT.CLASS_DESC :
                            DT.CLASS_ASC;
                    Dom.addClass(column.getThEl(), newClass);
                }
                else {
                     var sortClass = oNewSortedBy.dir || DT.CLASS_ASC;
                     Dom.addClass(column.getThEl(), sortClass);
                }
            }
        }
    });
    
    /**
    * @attribute paginator
    * @description Stores an instance of Pag, or (for
    * backward compatibility), an object literal of pagination values in the
    * following form:<br>
    *   { containers:[], // UI container elements <br>
    *   rowsPerPage:500, // 500 rows <br>
    *   currentPage:1,  // page one <br>
    *   pageLinks:0,    // show all links <br>
    *   pageLinksStart:1, // first link is page 1 <br>
    *   dropdownOptions:null, // no dropdown <br>
    *   links: [], // links elements <br>
    *   dropdowns: [] } //dropdown elements
    *
    * @default null
    * @type {Object|YAHOO.widget.Paginator}
    */
    this.setAttributeConfig("paginator", {
        value : { // Backward compatibility
            rowsPerPage:500, // 500 rows per page
            currentPage:1,  // show page one
            startRecordIndex:0, // start with first Record
            totalRecords:0, // how many Records total
            totalPages:0, // how many pages total
            rowsThisPage:0, // how many rows this page
            pageLinks:0,    // show all links
            pageLinksStart:1, // first link is page 1
            dropdownOptions: null, //no dropdown
            containers:[], // Paginator container element references
            dropdowns: [], //dropdown element references,
            links: [] // links elements
        },
        validator : function (oNewPaginator) {
            if (typeof oNewPaginator === 'object' && oNewPaginator) {
                if (oNewPaginator instanceof Pag) {
                    return true;
                }
                else {
                    // Backward compatibility
                    if(oNewPaginator && (oNewPaginator.constructor == Object)) {
                        // Check for incomplete set of values
                        if((oNewPaginator.rowsPerPage !== undefined) &&
                                (oNewPaginator.currentPage !== undefined) &&
                                (oNewPaginator.startRecordIndex !== undefined) &&
                                (oNewPaginator.totalRecords !== undefined) &&
                                (oNewPaginator.totalPages !== undefined) &&
                                (oNewPaginator.rowsThisPage !== undefined) &&
                                (oNewPaginator.pageLinks !== undefined) &&
                                (oNewPaginator.pageLinksStart !== undefined) &&
                                (oNewPaginator.dropdownOptions !== undefined) &&
                                (oNewPaginator.containers !== undefined) &&
                                (oNewPaginator.dropdowns !== undefined) &&
                                (oNewPaginator.links !== undefined)) {

                            // Validate each value
                            if(lang.isNumber(oNewPaginator.rowsPerPage) &&
                                    lang.isNumber(oNewPaginator.currentPage) &&
                                    lang.isNumber(oNewPaginator.startRecordIndex) &&
                                    lang.isNumber(oNewPaginator.totalRecords) &&
                                    lang.isNumber(oNewPaginator.totalPages) &&
                                    lang.isNumber(oNewPaginator.rowsThisPage) &&
                                    lang.isNumber(oNewPaginator.pageLinks) &&
                                    lang.isNumber(oNewPaginator.pageLinksStart) &&
                                    lang.isArray(oNewPaginator.dropdownOptions) &&
                                    lang.isArray(oNewPaginator.containers) &&
                                    lang.isArray(oNewPaginator.dropdowns) &&
                                    lang.isArray(oNewPaginator.links)) {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        },
        method : function (oNewPaginator) {
            // Hook into the pagintor's change event
            if (oNewPaginator instanceof Pag) {
                oNewPaginator.subscribe('changeRequest', this.onPaginatorChange, this, true);

                // If the paginator has no configured containers, add some
                var containers = oNewPaginator.getContainerNodes();
                if (!containers.length) {
                    // Build the container nodes
                    var c_above = document.createElement('div');
                    c_above.id = this._sId + "-paginator0";
                    this._elContainer.insertBefore(c_above,this._elContainer.firstChild);

                    // ...and one below the table
                    var c_below = document.createElement('div');
                    c_below.id = this._sId + "-paginator1";
                    this._elContainer.appendChild(c_below);

                    containers = [c_above, c_below];
                    Dom.addClass(containers,
                                DT.CLASS_PAGINATOR);

                    oNewPaginator.set('containers',containers);
                }
            }
        }
    });

    /**
    * @attribute paginated
    * @deprecated No longer used, as long as "paginator" value is an instance of
    * Paginator class.  
    */
    this.setAttributeConfig("paginated", {
        value: false,
        validator: lang.isBoolean,
        method : function (on) {
            var curVal = this.get('paginated');
            var i,len;
            if (on == curVal) {
                return;
            }

            var oPaginator  = this.get('paginator');
            if (!(oPaginator instanceof Pag)) {
                // Backward compatibility--pagination generated here
                oPaginator = oPaginator || {
                    rowsPerPage     : 500,  // 500 rows per page
                    currentPage     : 1,    // show page one
                    startRecordIndex: 0,    // start with first Record
                    totalRecords    : 0,    // how many Records total
                    totalPages      : 0,    // how many pages total
                    rowsThisPage    : 0,    // how many rows this page
                    pageLinks       : 0,    // show all links
                    pageLinksStart  : 1,    // first link is page 1
                    dropdownOptions : null, // no dropdown
                    containers      : [],   // Paginator container element references
                    dropdowns       : [],   // dropdown element references,
                    links           : []    // links elements
                };
                var aContainerEls = oPaginator.containers;

                // Paginator is enabled
                if(on) {
                    // No containers found, create two from scratch
                    if(aContainerEls.length === 0) {
                        // One before TABLE
                        var pag0 = document.createElement("span");
                        pag0.id = this._sId + "-paginator0";
                        Dom.addClass(pag0, DT.CLASS_PAGINATOR);
                        pag0 = this._elContainer.insertBefore(pag0, this._elContainer.firstChild);
                        aContainerEls.push(pag0);

                        // One after TABLE
                        var pag1 = document.createElement("span");
                        pag1.id = this._sId + "-paginator1";
                        Dom.addClass(pag1, DT.CLASS_PAGINATOR);
                        pag1 = this._elContainer.appendChild(pag1);
                        aContainerEls.push(pag1);

                        // (re)set the paginator value directly
                        oPaginator.containers = aContainerEls;
                        this._configs.paginator.value= oPaginator;
                    }
                    else {
                        // Show each container
                        for(i=0; i<aContainerEls.length; i++) {
                            aContainerEls[i].style.display = "";
                        }
                    }

                    // Links are enabled
                    if(oPaginator.pageLinks > -1) {
                        var aLinkEls = oPaginator.links;
                        // No links containers found, create from scratch
                        if(aLinkEls.length === 0) {
                            for(i=0; i<aContainerEls.length; i++) {
                                // Create one links container per Paginator container
                                var linkEl = document.createElement("span");
                                linkEl.id = "yui-dt-pagselect"+i;
                                linkEl = aContainerEls[i].appendChild(linkEl);

                                // Add event listener
                                //TODO: anon fnc
                                Ev.addListener(linkEl,"click",this._onPaginatorLinkClick,this);

                                 // Add directly to tracker
                                this._configs.paginator.value.links.push(linkEl);
                           }
                       }
                    }

                    for(i=0; i<aContainerEls.length; i++) {
                        // Create one SELECT element per Paginator container
                        var selectEl = document.createElement("select");
                        Dom.addClass(selectEl, DT.CLASS_DROPDOWN);
                        selectEl = aContainerEls[i].appendChild(selectEl);
                        selectEl.id = "yui-dt-pagselect"+i;

                        // Add event listener
                        //TODO: anon fnc
                        Ev.addListener(selectEl,"change",this._onPaginatorDropdownChange,this);

                        // Add DOM reference directly to tracker
                       this._configs.paginator.value.dropdowns.push(selectEl);

                        // Hide dropdown
                        if(!oPaginator.dropdownOptions) {
                            selectEl.style.display = "none";
                        }
                    }

                    //TODO: fire paginatorDisabledEvent & add to api doc
                    YAHOO.log("Paginator enabled", "info", this.toString());
                }
                // Pagination is disabled
                else {
                    // Containers found
                    if(aContainerEls.length > 0) {
                        // Destroy or just hide?

                        // Hide each container
                        for(i=0; i<aContainerEls.length; i++) {
                            aContainerEls[i].style.display = "none";
                        }

                        /*TODO?
                        // Destroy each container
                        for(i=0; i<aContainerEls.length; i++) {
                            Ev.purgeElement(aContainerEls[i], true);
                            aContainerEls.innerHTML = null;
                            //TODO: remove container?
                            // aContainerEls[i].parentNode.removeChild(aContainerEls[i]);
                        }
                        */
                    }
                    //TODO: fire paginatorDisabledEvent & add to api doc
                    YAHOO.log("Paginator disabled", "info", this.toString());
                }
            }
        }
    });

    /**
     * @attribute paginationEventHandler
     * @description For use with Pag pagination.  A
     * handler function that receives the requestChange event from the
     * configured paginator.  The handler method will be passed these
     * parameters:
     * <ol>
     * <li>oState {Object} - an object literal describing the requested
     * pagination state</li>
     * <li>oSelf {DataTable} - The DataTable instance.</li>
     * </ol>
     * 
     * For pagination through dynamic or server side data, assign
     * DT.handleDataSourcePagination or your own custom
     * handler.
     * @type {function|Object}
     * @default DT.handleSimplePagination
     */
    this.setAttributeConfig("paginationEventHandler", {
        value     : DT.handleSimplePagination,
        validator : lang.isObject
    });

    /**
    * @attribute caption
    * @description Value for the CAPTION element.
    * @type String
    */
    this.setAttributeConfig("caption", {
        value: null,
        validator: lang.isString,
        method: function(sCaption) {
            // Create CAPTION element
            if(!this._elCaption) {
                this._elCaption = this._elThead.parentNode.insertBefore(document.createElement("caption"), this._elThead.parentNode.firstChild);
            }
            // Set CAPTION value
            this._elCaption.innerHTML = sCaption;
        }
    });

    /**
    * @attribute scrollable
    * @description True if primary TBODY should scroll.
    * @default false
    * @type Boolean
    */
    this.setAttributeConfig("scrollable", {
        value: false,
        validator: function(oParam) {
            return (lang.isBoolean(oParam));
        },
        method: function(oParam) {
            if(oParam) {
                Dom.addClass(this._elContainer,DT.CLASS_SCROLLABLE);
                // Bug 1743176 - Safari 2 shifts the _elTbodyContainer up
                // when placed in overflow:auto container.  Should only shift
                // the table inside.  Apply topMargin to _elTbodyContainer
                // to account for the bug.
                if (ua.webkit && ua.webkit < 420) {
                    this._elTbodyContainer.style.marginTop =
                        this._elTbody.parentNode.style.marginTop.replace('-','');
                }
                this._syncScrollPadding();
            }
            else {
                Dom.removeClass(this._elContainer,DT.CLASS_SCROLLABLE);
                if (ua.webkit && ua.webkit < 420) {
                    this._elTbodyContainer.style.marginTop = "";
                }
                this._syncScrollPadding();
            }
        }
    });

    /**
    * @attribute width
    * @description Table width for scrollable tables
    * @type String
    */
    this.setAttributeConfig("width", {
        value: null,
        validator: lang.isString,
        method: function(oParam) {
            if(this.get("scrollable")) {
                this._elTheadContainer.style.width = oParam;
                this._elTbodyContainer.style.width = oParam;            
            }
        }
    });

    /**
    * @attribute height
    * @description Table height for scrollable tables
    * @type String
    */
    this.setAttributeConfig("height", {
        value: null,
        validator: lang.isString,
        method: function(oParam) {
            if(this.get("scrollable")) {
                this._elTbodyContainer.style.height = oParam;  
            }          
        }
    });

    /**
    * @attribute draggableColumns
    * @description True if Columns are draggable to reorder, false otherwise.
    * The Drag & Drop Utility is required to enable this feature. Only top-level
    * and non-nested Columns are draggable. Write once.
    * @default false
    * @type Boolean
    */
    this.setAttributeConfig("draggableColumns", {
        value: false,
        validator: lang.isBoolean,
        writeOnce: true
    });

    /**
     * @attribute renderLoopSize 	 
     * @description A value greater than 0 enables DOM rendering of rows to be
     * executed from a non-blocking timeout queue and sets how many rows to be
     * rendered per timeout. Recommended for very large data sets.     
     * @type Number 	 
     * @default 0 	 
     */ 	 
     this.setAttributeConfig("renderLoopSize", { 	 
         value: 0, 	 
         validator: lang.isNumber 	 
     }); 	 
},

/////////////////////////////////////////////////////////////////////////////
//
// Private member variables
//
/////////////////////////////////////////////////////////////////////////////

/**
 * True if instance is initialized, so as to fire the initEvent rather than
 * renderEvent.
 *
 * @property _bInit
 * @type Boolean
 * @default true
 * @private
 */
_bInit : true,

/**
 * Index assigned to instance.
 *
 * @property _nIndex
 * @type Number
 * @private
 */
_nIndex : null,

/**
 * Counter for IDs assigned to TR elements.
 *
 * @property _nTrCount
 * @type Number
 * @private
 */
_nTrCount : 0,

/**
 * Counter for IDs assigned to TD elements.
 *
 * @property _nTdCount
 * @type Number
 * @private
 */
_nTdCount : 0,

/**
 * Unique id assigned to instance "yui-dtN", useful prefix for generating unique
 * DOM ID strings and log messages.
 *
 * @property _sId
 * @type String
 * @private
 */
_sId : null,

/**
 * Render chain.
 *
 * @property _oChain
 * @type YAHOO.util.Chain
 * @private
 */
_oChain : null,

/**
 * Sparse array of custom functions to set column widths for browsers that don't
 * support dynamic CSS rules.  Functions are added at the index representing
 * the number of rows they update.
 *
 * @property _aFallbackColResizer
 * @type Array
 * @private
 */
_aFallbackColResizer : [],

/**
 * DOM reference to the container element for the DataTable instance into which
 * all other elements get created.
 *
 * @property _elContainer
 * @type HTMLElement
 * @private
 */
_elContainer : null,

/**
 * DOM reference to the container element for the DataTable's primary THEAD.
 *
 * @property _elTheadContainer
 * @type HTMLElement
 * @private
 */
_elTheadContainer : null,

/**
 * DOM reference to the container element for the DataTable's primary TBODY.
 *
 * @property _elTbodyContainer
 * @type HTMLElement
 * @private
 */
_elTbodyContainer : null,

/**
 * DOM reference to the CAPTION element for the DataTable instance.
 *
 * @property _elCaption
 * @type HTMLElement
 * @private
 */
_elCaption : null,

/**
 * DOM reference to the primary THEAD element for the DataTable instance.
 *
 * @property _elThead
 * @type HTMLElement
 * @private
 */
_elThead : null,

/**
 * DOM reference to the primary TBODY element for the DataTable instance.
 *
 * @property _elTbody
 * @type HTMLElement
 * @private
 */
_elTbody : null,

/**
 * DOM reference to the secondary TBODY element used to display DataTable messages.
 *
 * @property _elMsgTbody
 * @type HTMLElement
 * @private
 */
_elMsgTbody : null,

/**
 * DOM reference to the secondary TBODY element's single TR element used to display DataTable messages.
 *
 * @property _elMsgTbodyRow
 * @type HTMLElement
 * @private
 */
_elMsgTbodyRow : null,

/**
 * DOM reference to the secondary TBODY element's single TD element used to display DataTable messages.
 *
 * @property _elMsgTbodyCell
 * @type HTMLElement
 * @private
 */
_elMsgTbodyCell : null,

/**
 * DataSource instance for the DataTable instance.
 *
 * @property _oDataSource
 * @type YAHOO.util.DataSource
 * @private
 */
_oDataSource : null,

/**
 * ColumnSet instance for the DataTable instance.
 *
 * @property _oColumnSet
 * @type YAHOO.widget.ColumnSet
 * @private
 */
_oColumnSet : null,

/**
 * RecordSet instance for the DataTable instance.
 *
 * @property _oRecordSet
 * @type YAHOO.widget.RecordSet
 * @private
 */
_oRecordSet : null,

/**
 * ID string of first TR element of the current DataTable page.
 *
 * @property _sFirstTrId
 * @type String
 * @private
 */
_sFirstTrId : null,

/**
 * ID string of the last TR element of the current DataTable page.
 *
 * @property _sLastTrId
 * @type String
 * @private
 */
_sLastTrId : null,

/**
 * Template cell to create all new cells from.
 * @property _tdElTemplate
 * @type {HTMLElement}
 * @private 
 */
_tdElTemplate : null,

/**
 * Template row to create all new rows from.
 * @property _trElTemplate
 * @type {HTMLElement}
 * @private 
 */
_trElTemplate : null,

/**
 * True if x-scrollbar is currently visible.
 * @property _bScrollbarX
 * @type {Boolean}
 * @private 
 */
_bScrollbarX : null,



























/////////////////////////////////////////////////////////////////////////////
//
// Private methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Clears browser text selection. Useful to call on rowSelectEvent or
 * cellSelectEvent to prevent clicks or dblclicks from selecting text in the
 * browser.
 *
 * @method clearTextSelection
 */
clearTextSelection : function() {
    var sel;
    if(window.getSelection) {
    	sel = window.getSelection();
    }
    else if(document.getSelection) {
    	sel = document.getSelection();
    }
    else if(document.selection) {
    	sel = document.selection;
    }
    if(sel) {
        if(sel.empty) {
            sel.empty();
        }
        else if (sel.removeAllRanges) {
            sel.removeAllRanges();
        }
        else if(sel.collapse) {
            sel.collapse();
        }
    }
},

/**
 * Sets focus on the given element.
 *
 * @method _focusEl
 * @param el {HTMLElement} Element.
 * @private
 */
_focusEl : function(el) {
    el = el || this._elTbody;
    // http://developer.mozilla.org/en/docs/index.php?title=Key-navigable_custom_DHTML_widgets
    // The timeout is necessary in both IE and Firefox 1.5, to prevent scripts from doing
    // strange unexpected things as the user clicks on buttons and other controls.
    setTimeout(function() {
        try {
            el.focus();
        }
        catch(e) {
        }
    },0);
},

/**
 * Syncs up widths of THs and TDs across all those Columns without width values.
 * Actual adjustment is to the liner DIVs so window resizing will not affect cells. 
 *
 * @method _syncColWidths
 * @private
 */
_syncColWidths : function() {
    // Validate there is at least one row with cells and at least one Column
    var allKeys = this._oColumnSet.keys,
        elRow = this.getFirstTrEl();

    if(allKeys && elRow && (elRow.cells.length === allKeys.length)) {
        // Temporarily unsnap container since it causes inaccurate calculations
        var bUnsnap = false;
        if((YAHOO.env.ua.gecko || YAHOO.env.ua.opera) && this.get("scrollable") && this.get("width")) {
            bUnsnap = true;
            this._elTheadContainer.style.width = "";
            this._elTbodyContainer.style.width = "";
        }

        var i,
            oColumn,
            cellsLen = elRow.cells.length;
        // First time through, reset the widths to get an accurate measure of the TD
        for(i=0; i<cellsLen; i++) {
            oColumn = allKeys[i];
            // Only for Columns without widths
            if(!oColumn.width) {
                this._setColumnWidth(oColumn, "auto");
            }
        }

        // Calculate width for every Column
        for(i=0; i<cellsLen; i++) {
            oColumn = allKeys[i];
            var newWidth;
            
            // Columns without widths
            if(!oColumn.width) {
                var elTh = oColumn.getThEl();
                var elTd = elRow.cells[i];
                
                if(elTh.offsetWidth !== elTd.offsetWidth) {
                    var elWider = (elTh.offsetWidth > elTd.offsetWidth) ? elTh.firstChild : elTd.firstChild;               
                    // Calculate the final width by comparing liner widths
                    newWidth = elWider.offsetWidth -
                            (parseInt(Dom.getStyle(elWider,"paddingLeft"),10)|0) -
                            (parseInt(Dom.getStyle(elWider,"paddingRight"),10)|0);
                    
                    // Validate against minWidth        
                    newWidth = (oColumn.minWidth && (oColumn.minWidth > newWidth)) ?
                            oColumn.minWidth : newWidth;
            
                }
            }
            // Columns with widths
            else {
                newWidth = oColumn.width;
            }
            
            // Hidden Columns
            if(oColumn.hidden) {
                oColumn._nLastWidth = newWidth;
                newWidth = 1;
            }
            
            // Update to the new width
            this._setColumnWidth(oColumn, newWidth+"px"); 
        }
        
        // Resnap unsnapped containers
        if(bUnsnap) {
            var sWidth = this.get("width");
            this._elTheadContainer.style.width = sWidth;
            this._elTbodyContainer.style.width = sWidth;     
        } 
    }
    
    this._syncScrollPadding();
},

/**
 * Syncs padding around scrollable tables, including Column header right-padding
 * and container width and height.
 *
 * @method _syncScrollPadding
 * @private
 */
_syncScrollPadding : function() {
    // Proceed only if scrollable is enabled
    if(this.get("scrollable")) {
        var elTbody = this._elTbody,
            elTbodyContainer = this._elTbodyContainer,
            aLastHeaders, len, prefix, i, elLiner;
        
        // IE 6 and 7 only when y-scrolling not enabled
        if(!this.get("height") && (ua.ie)) {
            // Snap outer container height to content
            // but account for x-scrollbar if it is visible
            elTbodyContainer.style.height = 
                    (elTbodyContainer.scrollWidth > elTbodyContainer.offsetWidth) ?
                    (elTbody.offsetHeight + 19) + "px" : 
                    elTbody.offsetHeight + "px";
        }
        
        // X-scrolling not enabled
        if(!this.get("width")) {
            // Snap outer container width to content
            // but account for y-scrollbar if it is visible
            this._elContainer.style.width = 
                    (elTbodyContainer.scrollHeight > elTbodyContainer.offsetHeight) ?
                    (elTbody.parentNode.offsetWidth + 19) + "px" :
                    (elTbody.parentNode.offsetWidth) + "px";
        }
        // X-scrolling is enabled and x-scrollbar is visible
        else if(elTbodyContainer.scrollWidth > elTbodyContainer.offsetWidth) {
            // Perform sync routine
            if(!this._bScrollbarX) {
                // Add Column header right-padding
                aLastHeaders = this._oColumnSet.headers[this._oColumnSet.headers.length-1];
                len = aLastHeaders.length;
                prefix = this._sId+"-th";
                for(i=0; i<len; i++) {
                    //TODO: A better way to get th cell
                    elLiner = Dom.get(prefix+aLastHeaders[i]).firstChild;
                    elLiner.style.marginRight = 
                            (parseInt(Dom.getStyle(elLiner,"marginRight"),10) + 
                            27) + "px";
                }
                
                // Save state   
                this._bScrollbarX = true;
            }
        }
        // X-scrollbar enabled but x-scrollbar is not visible
        else {
            // Perform sync routine
            if(this._bScrollbarX) {                 
                // Remove Column header right-padding                   
                aLastHeaders = this._oColumnSet.headers[this._oColumnSet.headers.length-1];
                len = aLastHeaders.length;
                prefix = this._sId+"-th";
                for(i=0; i<len; i++) {
                    //TODO: A better way to get th cell
                    elLiner = Dom.get(prefix+aLastHeaders[i]).firstChild;
                    Dom.setStyle(elLiner,"marginRight","");
                }
                                        
                // Save state
                this._bScrollbarX = false;
            }
        }
    }
},











// INIT FUNCTIONS

/**
 * Initializes the HTMLElement templates used to create various table child
 * nodes.
 * @method _initNodeTemplates
 * @private
 */
_initNodeTemplates : function () {
    var d   = document,
        tr  = d.createElement('tr'),
        td  = d.createElement('td'),
        div = d.createElement('div');

    // Append the liner element
    td.appendChild(div);

    this._tdElTemplate = td;
    this._trElTemplate = tr;
},

/**
 * Initializes the DataTable container element.
 *
 * @method _initContainerEl
 * @param elContainer {HTMLElement | String} HTML DIV element by reference or ID.
 * @private
 */
_initContainerEl : function(elContainer) {
    // Clear any previous container
    if(this._elContainer) {
        Ev.purgeElement(this._elContainer, true);
        this._elContainer.innerHTML = "";
    }

    elContainer = Dom.get(elContainer);
    if(elContainer && elContainer.nodeName && (elContainer.nodeName.toLowerCase() == "div")) {
        // Esp for progressive enhancement
        Ev.purgeElement(elContainer, true);
        elContainer.innerHTML = "";

        Dom.addClass(elContainer,"yui-dt yui-dt-noop");
        
        // Container for header TABLE
        this._elTheadContainer = elContainer.appendChild(document.createElement("div"));
        Dom.addClass(this._elTheadContainer, "yui-dt-hd");

        // Container for body TABLE
        this._elTbodyContainer = elContainer.appendChild(document.createElement("div"));
        Dom.addClass(this._elTbodyContainer, "yui-dt-bd");

        this._elContainer = elContainer;
    }
},

/**
 * Initializes object literal of config values.
 *
 * @method _initConfigs
 * @param oConfig {Object} Object literal of config values.
 * @private
 */
_initConfigs : function(oConfigs) {
    if(oConfigs) {
        if(oConfigs.constructor != Object) {
            oConfigs = null;
            YAHOO.log("Invalid configs", "warn", this.toString());
        }
        // Backward compatibility
        else if(lang.isBoolean(oConfigs.paginator)) {
            YAHOO.log("DataTable's paginator model has been revised" +
            " -- please refer to the documentation for implementation" +
            " details", "warn", this.toString());
        }
        this._oConfigs = oConfigs;
    }
    else {
        this._oConfigs = {};
    }
},

/**
 * Initializes ColumnSet.
 *
 * @method _initColumnSet
 * @param aColumnDefs {Object[]} Array of object literal Column definitions.
 * @private
 */
_initColumnSet : function(aColumnDefs) {
    if(this._oColumnSet) {
        // First clear _oStylesheetRules for existing ColumnSet
        for(var i=0, l=this._oColumnSet.keys.length; i<l; i++) {
            DT._oStylesheetRules[".yui-dt-col-"+this._oColumnSet.keys[i].getId()] = undefined;
        }
        
        this._oColumnSet = null;
    }
    
    if(lang.isArray(aColumnDefs)) {
        this._oColumnSet =  new YAHOO.widget.ColumnSet(aColumnDefs);
    }
    // Backward compatibility
    else if(aColumnDefs instanceof YAHOO.widget.ColumnSet) {
        this._oColumnSet =  aColumnDefs;
        YAHOO.log("DataTable's constructor now requires an array" +
        " of object literal Column definitions instead of a ColumnSet instance",
        "warn", this.toString());
    }
},

/**
 * Initializes DataSource.
 *
 * @method _initDataSource
 * @param oDataSource {YAHOO.util.DataSource} DataSource instance.
 * @private
 */
_initDataSource : function(oDataSource) {
    this._oDataSource = null;
    if(oDataSource && (oDataSource instanceof DS)) {
        this._oDataSource = oDataSource;
    }
    // Backward compatibility
    else {
        var tmpTable = null;
        var tmpContainer = this._elContainer;
        var i;
        // Peek in container child nodes to see if TABLE already exists
        if(tmpContainer.hasChildNodes()) {
            var tmpChildren = tmpContainer.childNodes;
            for(i=0; i<tmpChildren.length; i++) {
                if(tmpChildren[i].nodeName && tmpChildren[i].nodeName.toLowerCase() == "table") {
                    tmpTable = tmpChildren[i];
                    break;
                }
            }
            if(tmpTable) {
                var tmpFieldsArray = [];
                for(i=0; i<this._oColumnSet.keys.length; i++) {
                    tmpFieldsArray.push({key:this._oColumnSet.keys[i].key});
                }

                this._oDataSource = new DS(tmpTable);
                this._oDataSource.responseType = DS.TYPE_HTMLTABLE;
                this._oDataSource.responseSchema = {fields: tmpFieldsArray};
                YAHOO.log("Null DataSource for progressive enhancement from" +
                " markup has been deprecated", "warn", this.toString());
            }
        }
    }
},

/**
 * Initializes RecordSet.
 *
 * @method _initRecordSet
 * @private
 */
_initRecordSet : function() {
    if(this._oRecordSet) {
        this._oRecordSet.reset();
    }
    else {
        this._oRecordSet = new YAHOO.widget.RecordSet();
    }
},

/**
 * Creates HTML markup for TABLE, THEAD and TBODY elements.
 *
 * @method _initTableEl
 * @private
 */
_initTableEl : function() {
    var elTable;

    // Destroy existing
    if(this._elThead) {
        var i;
        // Destroy ColumnDDs
        var aTree = this._oColumnSet.tree[0];
        for(i=0; i<aTree.length; i++) {
            if(aTree[i]._dd) {
                aTree[i]._dd = aTree[i]._dd.unreg();
            }
        }
    
        // Destroy ColumnResizers
        var aKeys = this._oColumnSet.keys;
        for(i=0; i<aKeys.length; i++) {
            if(aKeys[i]._ddResizer) {
                aKeys[i]._ddResizer = aKeys[i]._ddResizer.unreg();
            }
        }
        elTable = this._elThead.parentNode;
        Ev.purgeElement(elTable, true);
        elTable.parentNode.removeChild(elTable);
        this._elThead = null;
    }
    if(this._elTbody) {
        elTable = this._elTbody.parentNode;
        Ev.purgeElement(elTable, true);
        elTable.parentNode.removeChild(elTable);
        this._elTbody = null;
    }

    // Create elements for header
    // Create TABLE
    var elHeadTable = document.createElement("table");
    elHeadTable.id = this._sId + "-headtable";
    elHeadTable = this._elTheadContainer.appendChild(elHeadTable);

    // Create elements for body
    // Create TABLE
    var elBodyTable = document.createElement("table");
    elBodyTable.id = this._sId + "-bodytable";
    this._elTbodyContainer.appendChild(elBodyTable);

    // Create THEAD for display and for a11y
    this._initTheadEls();

    // Create TBODY for data
    this._elTbody = elBodyTable.appendChild(document.createElement("tbody"));
    this._elTbody.tabIndex = 0;
    Dom.addClass(this._elTbody,DT.CLASS_BODY);
    // Bug 1716354 - fix gap in Safari 2 and 3 (Also saw small gap in Opera.
    // this fixes all)
    this._elTbody.parentNode.style.marginTop = "-"+this._elTbody.offsetTop+"px";

    // Create TBODY for messages
    var elMsgTbody = document.createElement("tbody");
    var elMsgRow = elMsgTbody.appendChild(document.createElement("tr"));
    Dom.addClass(elMsgRow,DT.CLASS_FIRST);
    Dom.addClass(elMsgRow,DT.CLASS_LAST);
    this._elMsgRow = elMsgRow;
    var elMsgCell = elMsgRow.appendChild(document.createElement("td"));
    elMsgCell.colSpan = this._oColumnSet.keys.length;
    Dom.addClass(elMsgCell,DT.CLASS_FIRST);
    Dom.addClass(elMsgCell,DT.CLASS_LAST);
    this._elMsgTd = elMsgCell;
    this._elMsgTbody = elBodyTable.appendChild(elMsgTbody);
    var elMsgCellLiner = elMsgCell.appendChild(document.createElement("div"));
    Dom.addClass(elMsgCellLiner,DT.CLASS_LINER);
    this.showTableMessage(DT.MSG_LOADING, DT.CLASS_LOADING);

    var elContainer = this._elContainer;
    var elThead = this._elThead;
    var elTbody = this._elTbody;
    
    // IE puts focus outline in the wrong place
    if(ua.ie) {
        elTbody.hideFocus=true;
    }
    var elTbodyContainer = this._elTbodyContainer;

    // Set up DOM events
    Ev.addListener(elContainer, "focus", this._onTableFocus, this);
    Ev.addListener(elTbody, "focus", this._onTbodyFocus, this);

    Ev.addListener(elTbody, "mouseover", this._onTableMouseover, this);
    Ev.addListener(elTbody, "mouseout", this._onTableMouseout, this);
    Ev.addListener(elTbody, "mousedown", this._onTableMousedown, this);

    Ev.addListener(elTbody, "keydown", this._onTbodyKeydown, this);

    Ev.addListener(elTbody, "keypress", this._onTableKeypress, this);

    // Since we can't listen for click and dblclick on the same element...
    Ev.addListener(elTbody.parentNode, "dblclick", this._onTableDblclick, this);
    Ev.addListener(elTbody, "click", this._onTbodyClick, this);

    Ev.addListener(elTbodyContainer, "scroll", this._onScroll, this); // to sync horiz scroll headers
},

/**
 * Initializes THEAD elements for display and for screen readers.
 *
 * @method _initTheadEls
 * @private
 */
_initTheadEls : function() {
    var i,j, l, elThead, elA11yThead, aTheads;
    
    // First time through
    if(!this._elThead) {
        // Create THEADs
        elThead = this._elTheadContainer.firstChild.appendChild(document.createElement("thead"));
        this._elThead = elThead;
    
        elA11yThead = this._elTbodyContainer.firstChild.appendChild(document.createElement("thead"));
        this._elA11yThead = elA11yThead;
        
        aTheads = [elThead, elA11yThead];

        Ev.addListener(elThead, "focus", this._onTheadFocus, this);
        Ev.addListener(elThead, "keydown", this._onTheadKeydown, this);
        Ev.addListener(elThead, "mouseover", this._onTableMouseover, this);
        Ev.addListener(elThead, "mouseout", this._onTableMouseout, this);
        Ev.addListener(elThead, "mousedown", this._onTableMousedown, this);
        Ev.addListener(elThead, "mouseup", this._onTableMouseup, this);
        Ev.addListener(elThead, "click", this._onTheadClick, this);
        Ev.addListener(elThead.parentNode, "dblclick", this._onTableDblclick, this);
    }
    // Reinitialization
    else {
        // Clear rows from THEADs
        elThead = this._elThead;
        elA11yThead = this._elA11yThead;
        aTheads = [elThead, elA11yThead];
            
        for(i=0; i<aTheads.length; i++) {
            for(j=aTheads[i].rows.length-1; j>-1; j--) {
                Ev.purgeElement(aTheads[i].rows[j], true);
                aTheads[i].removeChild(aTheads[i].rows[j]);
            }     
        }
    }

    
    var oColumn,
        oColumnSet = this._oColumnSet;

    // Add TRs to the THEADs
    var colTree = oColumnSet.tree;
    var elTheadCell;
    for(l=0; l<aTheads.length; l++) {
        for(i=0; i<colTree.length; i++) {
            var elTheadRow = aTheads[l].appendChild(document.createElement("tr"));
            elTheadRow.id = this._sId+"-hdrow"+i;
    
            // ...and create TH cells
            for(j=0; j<colTree[i].length; j++) {
                oColumn = colTree[i][j];
                elTheadCell = elTheadRow.appendChild(document.createElement("th"));
                if(l===0) {
                    oColumn._elTh = elTheadCell;
                }
                var id = (l===1) ? this._sId+"-th" + oColumn.getId() + "-a11y": this._sId+"-th" + oColumn.getId();
                elTheadCell.id = id;
                elTheadCell.yuiCellIndex = j;
                this._initThEl(elTheadCell,oColumn,i,j, (l===1));
            }
    
            if(l===0) {
                // Set FIRST/LAST on THEAD rows
                if(i === 0) {
                    Dom.addClass(elTheadRow, DT.CLASS_FIRST);
                }
                if(i === (colTree.length-1)) {
                    Dom.addClass(elTheadRow, DT.CLASS_LAST);
                }
            }
        }

        if(l===0) {
            // Set FIRST/LAST on TH elements using the values in ColumnSet headers array
            var aFirstHeaders = oColumnSet.headers[0];
            var aLastHeaders = oColumnSet.headers[oColumnSet.headers.length-1];
            for(i=0; i<aFirstHeaders.length; i++) {
                //TODO: A better way to get th cell
                Dom.addClass(Dom.get(this._sId+"-th"+aFirstHeaders[i]), DT.CLASS_FIRST);
            }
            for(i=0; i<aLastHeaders.length; i++) {
                //TODO: A better way to get th cell
                Dom.addClass(Dom.get(this._sId+"-th"+aLastHeaders[i]), DT.CLASS_LAST);
            }
        
            // Add DD features only after DOM has been updated
            var foundDD = (util.DD) ? true : false;
            var needDD = false;
            // draggable
            // HACK: Not able to use attribute since this code is run before
            // attributes are initialized
            if(this._oConfigs.draggableColumns) {
                for(i=0; i<this._oColumnSet.tree[0].length; i++) {
                    oColumn = this._oColumnSet.tree[0][i];
                    if(foundDD) {
                        elTheadCell = oColumn.getThEl();
                        Dom.addClass(elTheadCell, DT.CLASS_DRAGGABLE);
                        var elDragTarget = DT._initColumnDragTargetEl();
                        oColumn._dd = new YAHOO.widget.ColumnDD(this, oColumn, elTheadCell, elDragTarget);
                    }
                    else {
                        needDD = true;
                    }    
                }
            }
            // resizeable
            for(i=0; i<this._oColumnSet.keys.length; i++) {
                oColumn = this._oColumnSet.keys[i];
                if(oColumn.resizeable) {
                    if(foundDD) {
                        elTheadCell = oColumn.getThEl();
                        Dom.addClass(elTheadCell, DT.CLASS_RESIZEABLE);
                        var elThLiner = elTheadCell.firstChild;
                        var elThResizer = elThLiner.appendChild(document.createElement("div"));
                        elThResizer.id = this._sId + "-colresizer" + oColumn.getId();
                        oColumn._elResizer = elThResizer;
                        Dom.addClass(elThResizer,DT.CLASS_RESIZER);
                        var elResizerProxy = DT._initColumnResizerProxyEl();
                        oColumn._ddResizer = new YAHOO.util.ColumnResizer(
                                this, oColumn, elTheadCell, elThResizer.id, elResizerProxy);
                        var cancelClick = function(e) {
                            Ev.stopPropagation(e);
                        };
                        Ev.addListener(elThResizer,"click",cancelClick);
                    }
                    else {
                        needDD = true;
                    }
                }
            }
            if(needDD) {
                YAHOO.log("Could not find DragDrop dependancy", "warn", this.toString());
            }
            
            YAHOO.log("TH cells for " + this._oColumnSet.keys.length + " keys created","info",this.toString());
        }
        else {
            YAHOO.log("Accessibility TH cells for " + this._oColumnSet.keys.length + " keys created","info",this.toString());
        }
    }
},

/**
 * Populates TH cell as defined by Column.
 *
 * @method _initThEl
 * @param elTheadCell {HTMLElement} TH cell element reference.
 * @param oColumn {YAHOO.widget.Column} Column object.
 * @param row {Number} Row index.
 * @param col {Number} Column index.
 * @param bA11y {Boolean} True if TH is for accessibility, so as not to
 * initialize presentation elements.
 * @private
 */
_initThEl : function(elTheadCell,oColumn,row,col, bA11y) {
    // Clear out the cell of prior content
    // TODO: purgeListeners and other validation-related things
    var colKey = oColumn.getKey();
    var colId = oColumn.getId();
    elTheadCell.yuiColumnKey = colKey;
    elTheadCell.yuiColumnId = colId;
    elTheadCell.innerHTML = "";
    elTheadCell.rowSpan = oColumn.getRowspan();
    elTheadCell.colSpan = oColumn.getColspan();

    var elTheadCellLiner = elTheadCell.appendChild(document.createElement("div"));
    var elTheadCellLabel = elTheadCellLiner.appendChild(document.createElement("span"));

    // Keep it basic for screen readers
    if(bA11y) {
        //TODO: remove IDs and form elements from label
        if(oColumn.abbr) {
            elTheadCell.abbr = oColumn.abbr;
        }
        elTheadCellLabel.innerHTML = lang.isValue(oColumn.label) ? oColumn.label : colKey;
    }
    // Visually format the elements
    else {
        // Needed for resizer
        elTheadCellLiner.id = elTheadCell.id + "-liner";
        
        // Add classes on the liner
        var aClasses;
        if(lang.isString(oColumn.className)) {
            aClasses = [oColumn.className];
        }
        else if(lang.isArray(oColumn.className)) {
            aClasses = oColumn.className;
        }
        else {
            aClasses = [];
        }
        
        //TODO: document special keys will get stripped here
        aClasses[aClasses.length] = "yui-dt-col-"+colKey.replace(/[^\w\-.:]/g,"");
        
        aClasses[aClasses.length] = "yui-dt-col-"+oColumn.getId();
        
        aClasses[aClasses.length] = DT.CLASS_LINER;

        Dom.addClass(elTheadCellLiner,aClasses.join(" "));

        // Add classes on the label
        Dom.addClass(elTheadCellLabel,DT.CLASS_LABEL);
        
        // Add classes on the cell
        aClasses = [];
        if(oColumn.resizeable) {
            aClasses[aClasses.length] = DT.CLASS_RESIZEABLE;
        }
        if(oColumn.sortable) {
            aClasses[aClasses.length] = DT.CLASS_SORTABLE;
        }

        //Set Column hidden
        if(oColumn.hidden) {
            aClasses[aClasses.length] = DT.CLASS_HIDDEN;
        }
        
        // Set Column selection on TD
        if(oColumn.selected) {
            aClasses[aClasses.length] = DT.CLASS_SELECTED;
        }

        Dom.addClass(elTheadCell,aClasses.join(" "));

        DT.formatTheadCell(elTheadCellLabel, oColumn, this);
    }
},

/**
 * Creates HTML markup for Cell Editor.
 *
 * @method _initCellEditorEl
 * @private
 */
_initCellEditorEl : function() {
    // TODO: destroy previous instances

    // Attach Cell Editor container element as first child of body
    var elCellEditor = document.createElement("div");
    elCellEditor.id = this._sId + "-celleditor";
    elCellEditor.style.display = "none";
    elCellEditor.tabIndex = 0;
    Dom.addClass(elCellEditor, DT.CLASS_EDITOR);
    var elFirstChild = Dom.getFirstChild(document.body);
    if(elFirstChild) {
        elCellEditor = Dom.insertBefore(elCellEditor, elFirstChild);
    }
    else {
        elCellEditor = document.body.appendChild(elCellEditor);
    }
    
    // Internal tracker of Cell Editor values
    var oCellEditor = {};
    oCellEditor.container = elCellEditor;
    oCellEditor.value = null;
    oCellEditor.isActive = false;
    this._oCellEditor = oCellEditor;
},

/** 	 
  * Initializes Column sorting. 	 
  * 	 
  * @method _initColumnSort 	 
  * @private 	 
  */ 	 
_initColumnSort : function() {
    this.subscribe("theadCellClickEvent", this.onEventSortColumn); 	 
}, 	 
 

































// DOM MUTATION FUNCTIONS



/**
 * Create a TR element for a given Record.
 * @method _createTrEl
 * @param oRecord {YAHOO.widget.Record} Record instance
 * @return {HTMLElement} The new TR element.  This must be added to the DOM.
 * @private 
 */
_createTrEl : function (oRecord) {
    // Clone the empty tr template.  We can't clone an existing row
    // because of the expandos and td ids that must be set in _addTdEl
    var elRow     = this._trElTemplate.cloneNode(true);

    elRow.id = this._sId+"-bdrow"+this._nTrCount;
    this._nTrCount++;

    // Call _updateTrEl to populate and align the row contents
    return this._updateTrEl(elRow,oRecord);
},

/**
 * Formats all TD elements of given TR element with data from the given Record.
 *
 * @method _updateTrEl
 * @param elRow {HTMLElement} The TR element to update.
 * @param oRecord {YAHOO.widget.Record} The associated Record instance.
 * @return {HTMLElement} DOM reference to the new TR element.
 * @private
 */
_updateTrEl : function(elRow, oRecord) {
    var oColumnSet = this._oColumnSet,
        sortKey,
        sortClass,
        isSortedBy = this.get("sortedBy"),
        i,j,len,jlen;

    if(isSortedBy) {
        sortKey = isSortedBy.key;
        sortClass = isSortedBy.dir;
    }

    // Hide the row to prevent constant reflows
    elRow.style.display = 'none';

    // Remove extra TD elements
    while(elRow.childNodes.length > oColumnSet.keys.length) {
        elRow.removeChild(elRow.firstChild);
    }
    // Add more TD elements as needed
    for (i=elRow.childNodes.length||0, len=oColumnSet.keys.length; i < len; ++i) {
        this._addTdEl(elRow,oColumnSet.keys[i],i);
    }

    // Update TD elements with new data
    for(i=0,len=oColumnSet.keys.length; i<len; ++i) {
        var oColumn     = oColumnSet.keys[i],
            elCell      = elRow.childNodes[i],
            elCellLiner = elCell.firstChild,
            cellHeaders = '';

        // Set the cell content
        this.formatCell(elCellLiner, oRecord, oColumn);

        // Set the cell's accessibility headers
        for(j=0,jlen=oColumnSet.headers[i].length; j < jlen; ++j) {
            cellHeaders += this._sId + "-th" + oColumnSet.headers[i][j] + "-a11y ";
        }
        elCell.headers = cellHeaders;

        // Set ASC/DESC on TD
        if(oColumn.key === sortKey) {
            Dom.replaceClass(elCell, sortClass === DT.CLASS_ASC ?
                                     DT.CLASS_DESC : DT.CLASS_ASC, sortClass);
        } else {
            Dom.removeClass(elCell, DT.CLASS_ASC);
            Dom.removeClass(elCell, DT.CLASS_DESC);
        }
        
        // Set Column hidden if appropriate
        if(oColumn.hidden) {
            Dom.addClass(elCell, DT.CLASS_HIDDEN);
        }
        else {
            Dom.removeClass(elCell, DT.CLASS_HIDDEN);
        }

        // Set Column selection on TH
        if(oColumn.selected) {
            Dom.addClass(elCell, DT.CLASS_SELECTED);
        }
        else {
            Dom.removeClass(elCell, DT.CLASS_SELECTED);
        }
    }

    // Update Record ID
    elRow.yuiRecordId = oRecord.getId();
    
    // Redisplay the row for reflow
    elRow.style.display = '';

    return elRow;
},


/**
 * Creates a cell within the specified row and column.
 * @method _addTdEl
 * @param elRow {HTMLElement} The row to add the cell to
 * @param oColumn {Column} The column definition to use for the cell
 * @param index {number} (optional) the index to add the cell at (default null)
 * @return {HTMLElement} the new cell
 * @private
 */
_addTdEl : function (elRow,oColumn,index) {
    var elCell      = this._tdElTemplate.cloneNode(true),
        elCellLiner = elCell.firstChild;

    index = index || elRow.cells.length;

    elCell.id           = elRow.id+"-cell"+this._nTdCount;
    this._nTdCount++;
    elCell.yuiColumnKey = oColumn.getKey();
    elCell.yuiColumnId  = oColumn.getId();

    // For SF2 cellIndex bug: http://www.webreference.com/programming/javascript/ppk2/3.html
    elCell.yuiCellIndex = index;

    // Set FIRST/LAST on TD
    if (!(index % this._oColumnSet.keys.length - 1)) {
        elCell.className = index ? DT.CLASS_LAST : DT.CLASS_FIRST;
    }

    var insertBeforeCell = elRow.cells[index] || null;
    return elRow.insertBefore(elCell,insertBeforeCell);
},

/**
 * Deletes TR element by DOM reference or by DataTable page row index.
 *
 * @method _deleteTrEl
 * @param row {HTMLElement | Number} TR element reference or Datatable page row index.
 * @return {Boolean} Returns true if successful, else returns false.
 * @private
 */
_deleteTrEl : function(row) {
    var rowIndex;

    // Get page row index for the element
    if(!lang.isNumber(row)) {
        rowIndex = Dom.get(row).sectionRowIndex;
    }
    else {
        rowIndex = row;
    }
    if(lang.isNumber(rowIndex) && (rowIndex > -2) && (rowIndex < this._elTbody.rows.length)) {
        this._elTbody.deleteRow(rowIndex);
        return true;
    }
    else {
        return false;
    }
},



























// CSS/STATE FUNCTIONS




/**
 * Assigns the class DT.CLASS_FIRST to the first TR element
 * of the DataTable page and updates internal tracker.
 *
 * @method _setFirstRow
 * @private
 */
_setFirstRow : function() {
    var rowEl = this.getFirstTrEl();
    if(rowEl) {
        // Remove FIRST
        if(this._sFirstTrId) {
            Dom.removeClass(this._sFirstTrId, DT.CLASS_FIRST);
        }
        // Set FIRST
        Dom.addClass(rowEl, DT.CLASS_FIRST);
        this._sFirstTrId = rowEl.id;
    }
    else {
        this._sFirstTrId = null;
    }
},

/**
 * Assigns the class DT.CLASS_LAST to the last TR element
 * of the DataTable page and updates internal tracker.
 *
 * @method _setLastRow
 * @private
 */
_setLastRow : function() {
    var rowEl = this.getLastTrEl();
    if(rowEl) {
        // Unassign previous class
        if(this._sLastTrId) {
            Dom.removeClass(this._sLastTrId, DT.CLASS_LAST);
        }
        // Assign class
        Dom.addClass(rowEl, DT.CLASS_LAST);
        this._sLastTrId = rowEl.id;
    }
    else {
        this._sLastTrId = null;
    }
},

/**
 * Assigns the classes DT.CLASS_EVEN and
 * DT.CLASS_ODD to alternating TR elements of the DataTable
 * page. For performance, a subset of rows may be specified.
 *
 * @method _setRowStripes
 * @param row {HTMLElement | String | Number} (optional) HTML TR element reference
 * or string ID, or page row index of where to start striping.
 * @param range {Number} (optional) If given, how many rows to stripe, otherwise
 * stripe all the rows until the end.
 * @private
 */
_setRowStripes : function(row, range) {
    // Default values stripe all rows
    var allRows = this._elTbody.rows,
        nStartIndex = 0,
        nEndIndex = allRows.length,
        aOdds = [], nOddIdx = 0,
        aEvens = [], nEvenIdx = 0;

    // Stripe a subset
    if((row !== null) && (row !== undefined)) {
        // Validate given start row
        var elStartRow = this.getTrEl(row);
        if(elStartRow) {
            nStartIndex = elStartRow.sectionRowIndex;

            // Validate given range
            if(lang.isNumber(range) && (range > 1)) {
                nEndIndex = nStartIndex + range;
            }
        }
    }

    for(var i=nStartIndex; i<nEndIndex; i++) {
        if(i%2) {
            aOdds[nOddIdx++] = allRows[i];
        } else {
            aEvens[nEvenIdx++] = allRows[i];
        }
    }

    if (aOdds.length) {
        Dom.replaceClass(aOdds, DT.CLASS_EVEN, DT.CLASS_ODD);
    }

    if (aEvens.length) {
        Dom.replaceClass(aEvens, DT.CLASS_ODD, DT.CLASS_EVEN);
    }
},













































/////////////////////////////////////////////////////////////////////////////
//
// Private DOM Event Handlers
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Syncs scrolltop and scrollleft of all TABLEs.
 *
 * @method _onScroll
 * @param e {HTMLEvent} The scroll event.
 * @param oSelf {DT} DataTable instance
 * @private
 */
_onScroll : function(e, oSelf) {
    oSelf._elTheadContainer.scrollLeft = oSelf._elTbodyContainer.scrollLeft;

    if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
        oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
        oSelf.cancelCellEditor();
    }

    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    oSelf.fireEvent("tableScrollEvent", {event:e, target:elTarget});
},

/**
 * Handles click events on the DOCUMENT.
 *
 * @method _onDocumentClick
 * @param e {HTMLEvent} The click event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onDocumentClick : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();

    if(!Dom.isAncestor(oSelf._elContainer, elTarget)) {
        oSelf.fireEvent("tableBlurEvent");

        // Fires editorBlurEvent when click is not within the TABLE.
        // For cases when click is within the TABLE, due to timing issues,
        // the editorBlurEvent needs to get fired by the lower-level DOM click
        // handlers below rather than by the TABLE click handler directly.
        if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
            // Only if the click was not within the Cell Editor container
            if(!Dom.isAncestor(oSelf._oCellEditor.container, elTarget) &&
                    (oSelf._oCellEditor.container.id !== elTarget.id)) {
                oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
            }
        }
    }
},

/**
 * Handles focus events on the DataTable instance.
 *
 * @method _onTableFocus
 * @param e {HTMLEvent} The focus event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableFocus : function(e, oSelf) {
    oSelf.fireEvent("tableFocusEvent");
},

/**
 * Handles focus events on the THEAD element.
 *
 * @method _onTheadFocus
 * @param e {HTMLEvent} The focus event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTheadFocus : function(e, oSelf) {
    oSelf.fireEvent("theadFocusEvent");
    oSelf.fireEvent("tableFocusEvent");
},

/**
 * Handles focus events on the TBODY element.
 *
 * @method _onTbodyFocus
 * @param e {HTMLEvent} The focus event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTbodyFocus : function(e, oSelf) {
    oSelf.fireEvent("tbodyFocusEvent");
    oSelf.fireEvent("tableFocusEvent");
},

/**
 * Handles mouseover events on the DataTable instance.
 *
 * @method _onTableMouseover
 * @param e {HTMLEvent} The mouseover event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableMouseover : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
        var elTag = elTarget.nodeName.toLowerCase();
        var bKeepBubbling = true;
        while(elTarget && (elTag != "table")) {
            switch(elTag) {
                case "body":
                     return;
                case "a":
                    break;
                case "td":
                    bKeepBubbling = oSelf.fireEvent("cellMouseoverEvent",{target:elTarget,event:e});
                    break;
                case "span":
                    if(Dom.hasClass(elTarget, DT.CLASS_LABEL)) {
                        bKeepBubbling = oSelf.fireEvent("theadLabelMouseoverEvent",{target:elTarget,event:e});
                        // Backward compatibility
                        bKeepBubbling = oSelf.fireEvent("headerLabelMouseoverEvent",{target:elTarget,event:e});
                    }
                    break;
                case "th":
                    bKeepBubbling = oSelf.fireEvent("theadCellMouseoverEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerCellMouseoverEvent",{target:elTarget,event:e});
                    break;
                case "tr":
                    if(elTarget.parentNode.nodeName.toLowerCase() == "thead") {
                        bKeepBubbling = oSelf.fireEvent("theadRowMouseoverEvent",{target:elTarget,event:e});
                        // Backward compatibility
                        bKeepBubbling = oSelf.fireEvent("headerRowMouseoverEvent",{target:elTarget,event:e});
                    }
                    else {
                        bKeepBubbling = oSelf.fireEvent("rowMouseoverEvent",{target:elTarget,event:e});
                    }
                    break;
                default:
                    break;
            }
            if(bKeepBubbling === false) {
                return;
            }
            else {
                elTarget = elTarget.parentNode;
                if(elTarget) {
                    elTag = elTarget.nodeName.toLowerCase();
                }
            }
        }
        oSelf.fireEvent("tableMouseoverEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles mouseout events on the DataTable instance.
 *
 * @method _onTableMouseout
 * @param e {HTMLEvent} The mouseout event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableMouseout : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "a":
                break;
            case "td":
                bKeepBubbling = oSelf.fireEvent("cellMouseoutEvent",{target:elTarget,event:e});
                break;
            case "span":
                if(Dom.hasClass(elTarget, DT.CLASS_LABEL)) {
                    bKeepBubbling = oSelf.fireEvent("theadLabelMouseoutEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerLabelMouseoutEvent",{target:elTarget,event:e});
                }
                break;
            case "th":
                bKeepBubbling = oSelf.fireEvent("theadCellMouseoutEvent",{target:elTarget,event:e});
                // Backward compatibility
                bKeepBubbling = oSelf.fireEvent("headerCellMouseoutEvent",{target:elTarget,event:e});
                break;
            case "tr":
                if(elTarget.parentNode.nodeName.toLowerCase() == "thead") {
                    bKeepBubbling = oSelf.fireEvent("theadRowMouseoutEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerRowMouseoutEvent",{target:elTarget,event:e});
                }
                else {
                    bKeepBubbling = oSelf.fireEvent("rowMouseoutEvent",{target:elTarget,event:e});
                }
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableMouseoutEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles mousedown events on the DataTable instance.
 *
 * @method _onTableMousedown
 * @param e {HTMLEvent} The mousedown event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableMousedown : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "a":
                break;
            case "td":
                bKeepBubbling = oSelf.fireEvent("cellMousedownEvent",{target:elTarget,event:e});
                break;
            case "span":
                if(Dom.hasClass(elTarget, DT.CLASS_LABEL)) {
                    bKeepBubbling = oSelf.fireEvent("theadLabelMousedownEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerLabelMousedownEvent",{target:elTarget,event:e});
                }
                break;
            case "th":
                bKeepBubbling = oSelf.fireEvent("theadCellMousedownEvent",{target:elTarget,event:e});
                // Backward compatibility
                bKeepBubbling = oSelf.fireEvent("headerCellMousedownEvent",{target:elTarget,event:e});
                break;
            case "tr":
                if(elTarget.parentNode.nodeName.toLowerCase() == "thead") {
                    bKeepBubbling = oSelf.fireEvent("theadRowMousedownEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerRowMousedownEvent",{target:elTarget,event:e});
                }
                else {
                    bKeepBubbling = oSelf.fireEvent("rowMousedownEvent",{target:elTarget,event:e});
                }
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableMousedownEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles dblclick events on the DataTable instance.
 *
 * @method _onTableDblclick
 * @param e {HTMLEvent} The dblclick event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableDblclick : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "td":
                bKeepBubbling = oSelf.fireEvent("cellDblclickEvent",{target:elTarget,event:e});
                break;
            case "span":
                if(Dom.hasClass(elTarget, DT.CLASS_LABEL)) {
                    bKeepBubbling = oSelf.fireEvent("theadLabelDblclickEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerLabelDblclickEvent",{target:elTarget,event:e});
                }
                break;
            case "th":
                bKeepBubbling = oSelf.fireEvent("theadCellDblclickEvent",{target:elTarget,event:e});
                // Backward compatibility
                bKeepBubbling = oSelf.fireEvent("headerCellDblclickEvent",{target:elTarget,event:e});
                break;
            case "tr":
                if(elTarget.parentNode.nodeName.toLowerCase() == "thead") {
                    bKeepBubbling = oSelf.fireEvent("theadRowDblclickEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerRowDblclickEvent",{target:elTarget,event:e});
                }
                else {
                    bKeepBubbling = oSelf.fireEvent("rowDblclickEvent",{target:elTarget,event:e});
                }
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableDblclickEvent",{target:(elTarget || oSelf._elContainer),event:e});
},
/**
 * Handles keydown events on the THEAD element.
 *
 * @method _onTheadKeydown
 * @param e {HTMLEvent} The key event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTheadKeydown : function(e, oSelf) {
    // If tabbing to next TH label link causes THEAD to scroll,
    // need to sync scrollLeft with TBODY
    if(Ev.getCharCode(e) === 9) {
        setTimeout(function() {
            if((oSelf instanceof DT) && oSelf._sId) {
                oSelf._elTbodyContainer.scrollLeft = oSelf._elTheadContainer.scrollLeft;
            }
        },0);
    }
    
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "input":
            case "textarea":
                // TODO
                break;
            case "thead":
                bKeepBubbling = oSelf.fireEvent("theadKeyEvent",{target:elTarget,event:e});
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableKeyEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles keydown events on the TBODY element. Handles selection behavior,
 * provides hooks for ENTER to edit functionality.
 *
 * @method _onTbodyKeydown
 * @param e {HTMLEvent} The key event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTbodyKeydown : function(e, oSelf) {
    var sMode = oSelf.get("selectionMode");

    if(sMode == "standard") {
        oSelf._handleStandardSelectionByKey(e);
    }
    else if(sMode == "single") {
        oSelf._handleSingleSelectionByKey(e);
    }
    else if(sMode == "cellblock") {
        oSelf._handleCellBlockSelectionByKey(e);
    }
    else if(sMode == "cellrange") {
        oSelf._handleCellRangeSelectionByKey(e);
    }
    else if(sMode == "singlecell") {
        oSelf._handleSingleCellSelectionByKey(e);
    }
    
    if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
        oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
    }

    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "tbody":
                bKeepBubbling = oSelf.fireEvent("tbodyKeyEvent",{target:elTarget,event:e});
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableKeyEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles keypress events on the TABLE. Mainly to support stopEvent on Mac.
 *
 * @method _onTableKeypress
 * @param e {HTMLEvent} The key event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTableKeypress : function(e, oSelf) {
    if(ua.webkit) {
        var nKey = Ev.getCharCode(e);
        // arrow down
        if(nKey == 40) {
            Ev.stopEvent(e);
        }
        // arrow up
        else if(nKey == 38) {
            Ev.stopEvent(e);
        }
    }
},

/**
 * Handles click events on the THEAD element.
 *
 * @method _onTheadClick
 * @param e {HTMLEvent} The click event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTheadClick : function(e, oSelf) {
    // Always blur the cell editor
    if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
        oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
    }

    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "input":
                if(elTarget.type.toLowerCase() == "checkbox") {
                    bKeepBubbling = oSelf.fireEvent("theadCheckboxClickEvent",{target:elTarget,event:e});
                }
                else if(elTarget.type.toLowerCase() == "radio") {
                    bKeepBubbling = oSelf.fireEvent("theadRadioClickEvent",{target:elTarget,event:e});
                }
                break;
            case "a":
                bKeepBubbling = oSelf.fireEvent("theadLinkClickEvent",{target:elTarget,event:e});
                break;
            case "button":
                bKeepBubbling = oSelf.fireEvent("theadButtonClickEvent",{target:elTarget,event:e});
                break;
            case "span":
                if(Dom.hasClass(elTarget, DT.CLASS_LABEL)) {
                    bKeepBubbling = oSelf.fireEvent("theadLabelClickEvent",{target:elTarget,event:e});
                    // Backward compatibility
                    bKeepBubbling = oSelf.fireEvent("headerLabelClickEvent",{target:elTarget,event:e});
                }
                break;
            case "th":
                bKeepBubbling = oSelf.fireEvent("theadCellClickEvent",{target:elTarget,event:e});
                // Backward compatibility
                bKeepBubbling = oSelf.fireEvent("headerCellClickEvent",{target:elTarget,event:e});
                break;
            case "tr":
                bKeepBubbling = oSelf.fireEvent("theadRowClickEvent",{target:elTarget,event:e});
                // Backward compatibility
                bKeepBubbling = oSelf.fireEvent("headerRowClickEvent",{target:elTarget,event:e});
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableClickEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/**
 * Handles click events on the primary TBODY element.
 *
 * @method _onTbodyClick
 * @param e {HTMLEvent} The click event.
 * @param oSelf {DT} DataTable instance.
 * @private
 */
_onTbodyClick : function(e, oSelf) {
    // Always blur the cell editor
    if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
        oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
    }

    // Fire Custom Events
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();
    var bKeepBubbling = true;
    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "input":
                if(elTarget.type.toLowerCase() == "checkbox") {
                    bKeepBubbling = oSelf.fireEvent("checkboxClickEvent",{target:elTarget,event:e});
                }
                else if(elTarget.type.toLowerCase() == "radio") {
                    bKeepBubbling = oSelf.fireEvent("radioClickEvent",{target:elTarget,event:e});
                }
                break;
            case "a":
                bKeepBubbling = oSelf.fireEvent("linkClickEvent",{target:elTarget,event:e});
                break;
            case "button":
                bKeepBubbling = oSelf.fireEvent("buttonClickEvent",{target:elTarget,event:e});
                break;
            case "td":
                bKeepBubbling = oSelf.fireEvent("cellClickEvent",{target:elTarget,event:e});
                break;
            case "tr":
                bKeepBubbling = oSelf.fireEvent("rowClickEvent",{target:elTarget,event:e});
                break;
            default:
                break;
        }
        if(bKeepBubbling === false) {
            return;
        }
        else {
            elTarget = elTarget.parentNode;
            if(elTarget) {
                elTag = elTarget.nodeName.toLowerCase();
            }
        }
    }
    oSelf.fireEvent("tableClickEvent",{target:(elTarget || oSelf._elContainer),event:e});
},

/*TODO undeprecate?
 * Handles change events on SELECT elements within DataTable.
 *
 * @method _onDropdownChange
 * @param e {HTMLEvent} The change event.
 * @param oSelf {DT} DataTable instance.
 * @private
 * @deprecated
 */
_onDropdownChange : function(e, oSelf) {
    var elTarget = Ev.getTarget(e);
    //TODO: pass what args?
    //var value = elTarget[elTarget.selectedIndex].value;
    oSelf.fireEvent("dropdownChangeEvent", {event:e, target:elTarget});
},
































/////////////////////////////////////////////////////////////////////////////
//
// Public member variables
//
/////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////
//
// Public methods
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Returns unique id assigned to instance, which is a useful prefix for
 * generating unique DOM ID strings.
 *
 * @method getId
 * @return {String} Unique ID of the DataSource instance.
 */
getId : function() {
    return this._sId;
},

/**
 * DataSource instance name, for logging.
 *
 * @method toString
 * @return {String} Unique name of the DataSource instance.
 */

toString : function() {
    return "DataTable instance " + this._sId;
},

/**
 * Returns the DataTable instance's DataSource instance.
 *
 * @method getDataSource
 * @return {YAHOO.util.DataSource} DataSource instance.
 */
getDataSource : function() {
    return this._oDataSource;
},

/**
 * Returns the DataTable instance's ColumnSet instance.
 *
 * @method getColumnSet
 * @return {YAHOO.widget.ColumnSet} ColumnSet instance.
 */
getColumnSet : function() {
    return this._oColumnSet;
},

/**
 * Returns the DataTable instance's RecordSet instance.
 *
 * @method getRecordSet
 * @return {YAHOO.widget.RecordSet} RecordSet instance.
 */
getRecordSet : function() {
    return this._oRecordSet;
},

/**
 * Returns the DataTable instance's Cell Editor as an object literal with the
 * following properties:
 * <dl>
 * <dt>cell</dt>
 * <dd>{HTMLElement} Cell element being edited.</dd>
 *
 * <dt>column</dt>
 * <dd>{YAHOO.widget.Column} Associated Column instance.</dd>
 *
 * <dt>container</dt>
 * <dd>{HTMLElement} Reference to editor's container DIV element.</dd>
 *
 * <dt>isActive</dt>
 * <dd>{Boolean} True if cell is currently being edited.</dd>
 *
 * <dt>record</dt>
 * <dd>{YAHOO.widget.Record} Associated Record instance.</dd>
 *
 * <dt>validator</dt>
 * <dd>{HTMLFunction} Associated validator function called before new data is stored. Called
 * within the scope of the DataTable instance, the function receieves the
 * following arguments:
 *
 * <dl>
 *  <dt>oNewData</dt>
 *  <dd>{Object} New data to validate.</dd>
 *
 *  <dt>oOldData</dt>
 *  <dd>{Object} Original data in case of reversion.</dd>
 *
 *  <dt>oCellEditor</dt>
 *  <dd>{Object} Object literal representation of Editor values.</dd>
 * </dl>
 *
 *  </dd>
 *
 * <dt>defaultValue</dt>
 * <dd>Dynamically settable default value</dd>
 * </dl>
 *
 * <dt>value</dt>
 * <dd>Current input value</dd>
 * </dl>
 *
 *
 *
 *
 *
 * @method getCellEditor
 * @return {Object} Cell Editor object literal values.
 */
getCellEditor : function() {
    return this._oCellEditor;
},











































// DOM ACCESSORS

/**
 * Returns DOM reference to the DataTable's container element.
 *
 * @method getContainerEl
 * @return {HTMLElement} Reference to DIV element.
 */
getContainerEl : function() {
    return this._elContainer;
},

/**
 * Returns DOM reference to the DataTable's THEAD element.
 *
 * @method getTheadEl
 * @return {HTMLElement} Reference to THEAD element.
 */
getTheadEl : function() {
    return this._elThead;
},

/**
 * Returns DOM reference to the DataTable's primary TBODY element.
 *
 * @method getTbodyEl
 * @return {HTMLElement} Reference to TBODY element.
 */
getTbodyEl : function() {
    return this._elTbody;
},

/**
 * Returns DOM reference to the DataTable's secondary TBODY element that is
 * used to display messages.
 *
 * @method getMsgTbodyEl
 * @return {HTMLElement} Reference to TBODY element.
 */
getMsgTbodyEl : function() {
    return this._elMsgTbody;
},

/**
 * Returns DOM reference to the TD element within the secondary TBODY that is
 * used to display messages.
 *
 * @method getMsgTdEl
 * @return {HTMLElement} Reference to TD element.
 */
getMsgTdEl : function() {
    return this._elMsgTd;
},

/**
 * Returns the corresponding TR reference for a given DOM element, ID string or
 * directly page row index. If the given identifier is a child of a TR element,
 * then DOM tree is traversed until a parent TR element is returned, otherwise
 * null.
 *
 * @method getTrEl
 * @param row {HTMLElement | String | Number | YAHOO.widget.Record} Which row to
 * get: by element reference, ID string, page row index, or Record.
 * @return {HTMLElement} Reference to TR element, or null.
 */
getTrEl : function(row) {
    var allRows = this._elTbody.rows;

    // By Record
    if(row instanceof YAHOO.widget.Record) {
        var nTrIndex = this.getTrIndex(row);
            if(nTrIndex !== null) {
                return allRows[nTrIndex];
            }
            // Not a valid Record
            else {
                return null;
            }
    }
    // By page row index
    else if(lang.isNumber(row) && (row > -1) && (row < allRows.length)) {
        return allRows[row];
    }
    // By ID string or element reference
    else {
        var elRow;
        var el = Dom.get(row);

        // Validate HTML element
        if(el && (el.ownerDocument == document)) {
            // Validate TR element
            if(el.nodeName.toLowerCase() != "tr") {
                // Traverse up the DOM to find the corresponding TR element
                elRow = Dom.getAncestorByTagName(el,"tr");
            }
            else {
                elRow = el;
            }

            // Make sure the TR is in this TBODY
            if(elRow && (elRow.parentNode == this._elTbody)) {
                // Now we can return the TR element
                return elRow;
            }
        }
    }

    return null;
},

/**
 * Returns DOM reference to the first TR element in the DataTable page, or null.
 *
 * @method getFirstTrEl
 * @return {HTMLElement} Reference to TR element.
 */
getFirstTrEl : function() {
    return this._elTbody.rows[0] || null;
},

/**
 * Returns DOM reference to the last TR element in the DataTable page, or null.
 *
 * @method getLastTrEl
 * @return {HTMLElement} Reference to last TR element.
 */
getLastTrEl : function() {
    var allRows = this._elTbody.rows;
        if(allRows.length > 0) {
            return allRows[allRows.length-1] || null;
        }
},

/**
 * Returns DOM reference to the next TR element from the given TR element, or null.
 *
 * @method getNextTrEl
 * @param row {HTMLElement | String | Number | YAHOO.widget.Record} Element
 * reference, ID string, page row index, or Record from which to get next TR element.
 * @return {HTMLElement} Reference to next TR element.
 */
getNextTrEl : function(row) {
    var nThisTrIndex = this.getTrIndex(row);
    if(nThisTrIndex !== null) {
        var allRows = this._elTbody.rows;
        if(nThisTrIndex < allRows.length-1) {
            return allRows[nThisTrIndex+1];
        }
    }

    YAHOO.log("Could not get next TR element for row " + row, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the previous TR element from the given TR element, or null.
 *
 * @method getPreviousTrEl
 * @param row {HTMLElement | String | Number | YAHOO.widget.Record} Element
 * reference, ID string, page row index, or Record from which to get previous TR element.
 * @return {HTMLElement} Reference to previous TR element.
 */
getPreviousTrEl : function(row) {
    var nThisTrIndex = this.getTrIndex(row);
    if(nThisTrIndex !== null) {
        var allRows = this._elTbody.rows;
        if(nThisTrIndex > 0) {
            return allRows[nThisTrIndex-1];
        }
    }

    YAHOO.log("Could not get previous TR element for row " + row, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to a TD liner element.
 *
 * @method getTdLinerEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn}.
 * @return {HTMLElement} Reference to TD liner element.
 */
getTdLinerEl : function(cell) {
    var elCell = this.getTdEl(cell);
    return elCell.firstChild || null;
},

/**
 * Returns DOM reference to a TD element.
 *
 * @method getTdEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn}.
 * @return {HTMLElement} Reference to TD element.
 */
getTdEl : function(cell) {
    var elCell;
    var el = Dom.get(cell);

    // Validate HTML element
    if(el && (el.ownerDocument == document)) {
        // Validate TD element
        if(el.nodeName.toLowerCase() != "td") {
            // Traverse up the DOM to find the corresponding TR element
            elCell = Dom.getAncestorByTagName(el, "td");
        }
        else {
            elCell = el;
        }

        // Make sure the TD is in this TBODY
        if(elCell && (elCell.parentNode.parentNode == this._elTbody)) {
            // Now we can return the TD element
            return elCell;
        }
    }
    else if(cell) {
        var oRecord, nColKeyIndex;

        if(lang.isString(cell.columnId) && lang.isString(cell.recordId)) {
            oRecord = this.getRecord(cell.recordId);
            var oColumn = this.getColumnById(cell.columnId);
            if(oColumn) {
                nColKeyIndex = oColumn.getKeyIndex();
            }

        }
        if(cell.record && cell.column && cell.column.getKeyIndex) {
            oRecord = cell.record;
            nColKeyIndex = cell.column.getKeyIndex();
        }
        var elRow = this.getTrEl(oRecord);
        if((nColKeyIndex !== null) && elRow && elRow.cells && elRow.cells.length > 0) {
            return elRow.cells[nColKeyIndex] || null;
        }
    }

    return null;
},

/**
 * Returns DOM reference to the first TD element in the DataTable page (by default),
 * the first TD element of the optionally given row, or null.
 *
 * @method getFirstTdEl
 * @param row {HTMLElement} (optional) row from which to get first TD
 * @return {HTMLElement} Reference to TD element.
 */
getFirstTdEl : function(row) {
    var elRow = this.getTrEl(row) || this.getFirstTrEl();
    if(elRow && (elRow.cells.length > 0)) {
        return elRow.cells[0];
    }
    YAHOO.log("Could not get first TD element for row " + elRow, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the last TD element in the DataTable page (by default),
 * the first TD element of the optionally given row, or null.
 *
 * @method getLastTdEl
 * @return {HTMLElement} Reference to last TD element.
 */
getLastTdEl : function(row) {
    var elRow = this.getTrEl(row) || this.getLastTrEl();
    if(elRow && (elRow.cells.length > 0)) {
        return elRow.cells[elRow.cells.length-1];
    }
    YAHOO.log("Could not get last TD element for row " + elRow, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the next TD element from the given cell, or null.
 *
 * @method getNextTdEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn} from which to get next TD element.
 * @return {HTMLElement} Reference to next TD element, or null.
 */
getNextTdEl : function(cell) {
    var elCell = this.getTdEl(cell);
    if(elCell) {
        var nThisTdIndex = elCell.yuiCellIndex;
        var elRow = this.getTrEl(elCell);
        if(nThisTdIndex < elRow.cells.length-1) {
            return elRow.cells[nThisTdIndex+1];
        }
        else {
            var elNextRow = this.getNextTrEl(elRow);
            if(elNextRow) {
                return elNextRow.cells[0];
            }
        }
    }
    YAHOO.log("Could not get next TD element for cell " + cell, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the previous TD element from the given cell, or null.
 *
 * @method getPreviousTdEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn} from which to get previous TD element.
 * @return {HTMLElement} Reference to previous TD element, or null.
 */
getPreviousTdEl : function(cell) {
    var elCell = this.getTdEl(cell);
    if(elCell) {
        var nThisTdIndex = elCell.yuiCellIndex;
        var elRow = this.getTrEl(elCell);
        if(nThisTdIndex > 0) {
            return elRow.cells[nThisTdIndex-1];
        }
        else {
            var elPreviousRow = this.getPreviousTrEl(elRow);
            if(elPreviousRow) {
                return this.getLastTdEl(elPreviousRow);
            }
        }
    }
    YAHOO.log("Could not get next TD element for cell " + cell, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the above TD element from the given cell, or null.
 *
 * @method getAboveTdEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn} from which to get next TD element.
 * @return {HTMLElement} Reference to next TD element, or null.
 */
getAboveTdEl : function(cell) {
    var elCell = this.getTdEl(cell);
    if(elCell) {
        var elPreviousRow = this.getPreviousTrEl(elCell);
        if(elPreviousRow) {
            return elPreviousRow.cells[elCell.yuiCellIndex];
        }
    }
    YAHOO.log("Could not get above TD element for cell " + cell, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to the below TD element from the given cell, or null.
 *
 * @method getBelowTdEl
 * @param cell {HTMLElement | String | Object} DOM element reference or string ID, or
 * object literal of syntax {record:oRecord, column:oColumn} from which to get previous TD element.
 * @return {HTMLElement} Reference to previous TD element, or null.
 */
getBelowTdEl : function(cell) {
    var elCell = this.getTdEl(cell);
    if(elCell) {
        var elNextRow = this.getNextTrEl(elCell);
        if(elNextRow) {
            return elNextRow.cells[elCell.yuiCellIndex];
        }
    }
    YAHOO.log("Could not get below TD element for cell " + cell, "info", this.toString());
    return null;
},

/**
 * Returns DOM reference to a TH liner element.
 *
 * @method getThLinerEl
 * @param theadCell {YAHOO.widget.Column | HTMLElement | String} Column instance,
 * DOM element reference, or string ID.
 * @return {HTMLElement} Reference to TH liner element.
 */
getThLinerEl : function(theadCell) {
    var elCell = this.getThEl(theadCell);
    return elCell.firstChild || null;
},

/**
 * Returns DOM reference to a TH element.
 *
 * @method getThEl
 * @param theadCell {YAHOO.widget.Column | HTMLElement | String} Column instance,
 * DOM element reference, or string ID.
 * @return {HTMLElement} Reference to TH element.
 */
getThEl : function(theadCell) {
    var elTheadCell;

    // Validate Column instance
    if(theadCell instanceof YAHOO.widget.Column) {
        var oColumn = theadCell;
        elTheadCell = oColumn.getThEl();
        if(elTheadCell) {
            return elTheadCell;
        }
    }
    // Validate HTML element
    else {
        var el = Dom.get(theadCell);

        if(el && (el.ownerDocument == document)) {
            // Validate TH element
            if(el.nodeName.toLowerCase() != "th") {
                // Traverse up the DOM to find the corresponding TR element
                elTheadCell = Dom.getAncestorByTagName(el,"th");
            }
            else {
                elTheadCell = el;
            }

            // Make sure the TH is in this THEAD
            if(elTheadCell && (elTheadCell.parentNode.parentNode == this._elThead)) {
                // Now we can return the TD element
                return elTheadCell;
            }
        }
    }

    return null;
},

/**
 * Returns the page row index of given row. Returns null if the row is not on the
 * current DataTable page.
 *
 * @method getTrIndex
 * @param row {HTMLElement | String | YAHOO.widget.Record | Number} DOM or ID
 * string reference to an element within the DataTable page, a Record instance,
 * or a Record's RecordSet index.
 * @return {Number} Page row index, or null if row does not exist or is not on current page.
 */
getTrIndex : function(row) {
    var nRecordIndex;

    // By Record
    if(row instanceof YAHOO.widget.Record) {
        nRecordIndex = this._oRecordSet.getRecordIndex(row);
        if(nRecordIndex === null) {
            // Not a valid Record
            return null;
        }
    }
    // Calculate page row index from Record index
    else if(lang.isNumber(row)) {
        nRecordIndex = row;
    }
    if(lang.isNumber(nRecordIndex)) {
        // Validate the number
        if((nRecordIndex > -1) && (nRecordIndex < this._oRecordSet.getLength())) {
            // DataTable is paginated
            var oPaginator = this.get('paginator');
            if(oPaginator instanceof Pag || this.get('paginated')) {
                // Get the first and last Record on current page
                var startRecordIndex = 0,
                    endRecordIndex   = 0;

                if (oPaginator instanceof Pag) {
                    var rng = oPaginator.getPageRecords();
                    startRecordIndex = rng[0];
                    endRecordIndex   = rng[1];
                } else {
                    startRecordIndex = oPaginator.startRecordIndex;
                    endRecordIndex = startRecordIndex + oPaginator.rowsPerPage - 1;
                }

                // This Record is on current page
                if((nRecordIndex >= startRecordIndex) && (nRecordIndex <= endRecordIndex)) {
                    return nRecordIndex - startRecordIndex;
                }
                // This Record is not on current page
                else {
                    return null;
                }
            }
            // Not paginated, just return the Record index
            else {
                return nRecordIndex;
            }
        }
        // RecordSet index is out of range
        else {
            return null;
        }
    }
    // By element reference or ID string
    else {
        // Validate TR element
        var elRow = this.getTrEl(row);
        if(elRow && (elRow.ownerDocument == document) &&
                (elRow.parentNode == this._elTbody)) {
            return elRow.sectionRowIndex;
        }
    }

    YAHOO.log("Could not get page row index for row " + row, "info", this.toString());
    return null;
},














































// TABLE FUNCTIONS

/**
 * Resets a RecordSet with the given data and populates the page view
 * with the new data. Any previous data and selection states are cleared.
 * However, sort states are not cleared, so if the given data is in a particular
 * sort order, implementers should take care to reset the sortedBy property. If
 * pagination is enabled, the currentPage is shown and Paginator UI updated,
 * otherwise all rows are displayed as a single page. For performance, existing
 * DOM elements are reused when possible.
 *
 * @method initializeTable
 */
initializeTable : function() {
    // Reset init flag
    this._bInit = true;
    
    // Clear the RecordSet
    this._oRecordSet.reset();

    // Clear selections
    this._unselectAllTrEls();
    this._unselectAllTdEls();
    this._aSelections = null;
    this._oAnchorRecord = null;
    this._oAnchorCell = null;
},

/**
 * Renders the view with existing Records from the RecordSet while
 * maintaining sort, pagination, and selection states. For performance, reuses
 * existing DOM elements when possible while deleting extraneous elements.
 *
 * @method render
 */
render : function() {
    this._oChain.stop();
    this.showTableMessage(DT.MSG_LOADING, DT.CLASS_LOADING);
    YAHOO.log("DataTable rendering...", "info", this.toString());

    var i, j, k, l, len, allRecords;

    // Paginator is enabled, show a subset of Records and update Paginator UI
    var oPaginator = this.get('paginator');
    var bPaginated = oPaginator instanceof Pag || this.get('paginated');
    if(bPaginated) {
        if (oPaginator instanceof Pag) {
            allRecords = this._oRecordSet.getRecords(
                            oPaginator.getStartIndex(),
                            oPaginator.getRowsPerPage());
            oPaginator.render();
        }
        else {
            // Backward compatibility
            this.updatePaginator();
            var rowsPerPage = oPaginator.rowsPerPage;
            var startRecordIndex = (oPaginator.currentPage - 1) * rowsPerPage;
            allRecords = this._oRecordSet.getRecords(startRecordIndex, rowsPerPage);
            this.formatPaginators();
        }
    }
    // Show all records
    else {
        allRecords = this._oRecordSet.getRecords();
    }

    var elTbody = this._elTbody;
    var allRows = elTbody.rows;

    // Should have rows
    if(lang.isArray(allRecords) && (allRecords.length > 0)) {
        // Keep track of selected rows
        var allSelectedRows = this.getSelectedRows();
        // Keep track of selected cells
        var allSelectedCells = this.getSelectedCells();
        // Anything to reinstate?
        var bReselect = (allSelectedRows.length>0) || (allSelectedCells.length > 0);

        // Remove extra rows from the bottom so as to preserve ID order
        while(elTbody.hasChildNodes() && (allRows.length > allRecords.length)) {
            elTbody.deleteRow(-1);
        }

        // Unselect all TR and TD elements in the UI
        if(bReselect) {
            this._unselectAllTrEls();
            this._unselectAllTdEls();
        }

        this.hideTableMessage();

        // How many rows to work with each loop
        var loopN = this.get("renderLoopSize");
        var loopStart,
            loopEnd;

        // From the top, update in-place existing rows, so as to reuse DOM elements
        if(allRows.length > 0) {
            loopEnd = allRows.length; // End at last row
            this._oChain.add({
                method: function(oArg) {
                    if((this instanceof DT) && this._sId) {
                        var i = oArg.nCurrentRow,
                            // Must reference allRows.length instead of loopEnd
                            // here because loopEnd is reused below outside
                            // this closure.
                            len = loopN > 0 ? Math.min(i + loopN,allRows.length) : allRows.length;
                        for(; i < len; ++i) {
                            this._updateTrEl(allRows[i], allRecords[i]);
                        }
                        if (loopN > 0) {
                            this._syncColWidths();
                        }
                        oArg.nCurrentRow = i;
                    }
                },
                iterations: (loopN > 0) ? Math.ceil(loopEnd/loopN) : 1,
                argument: {nCurrentRow:0}, // Start at first row
                scope: this,
                timeout: (loopN > 0) ? 0 : -1
            });
        }

        // Add more TR elements as necessary
        loopStart = allRows.length; // where to start
        loopEnd = allRecords.length; // where to end
        var nRowsNeeded = (loopEnd - loopStart); // how many needed
        if(nRowsNeeded > 0) {
            this._oChain.add({
                method: function(oArg) {
                    if((this instanceof DT) && this._sId) {
                        var i = oArg.nCurrentRow,
                            len = loopN > 0 ? Math.min(i + loopN,loopEnd) : loopEnd,
                            df = document.createDocumentFragment(),
                            tr;
                        for(; i < len; ++i) {
                            tr = this._createTrEl(allRecords[i]);
                            tr.className = (i%2) ? DT.CLASS_ODD : DT.CLASS_EVEN;
                            df.appendChild(tr);
                        }
                        this._elTbody.appendChild(df);
                        if (loopN > 0) {
                            this._syncColWidths();
                        }
                        oArg.nCurrentRow = i;
                    }
                },
                iterations: (loopN > 0) ? Math.ceil(nRowsNeeded/loopN) : 1,
                argument: {nCurrentRow:loopStart}, // start at last row
                scope: this,
                timeout: (loopN > 0) ? 0 : -1
            });
        }

        this._oChain.add({
            method: function(oArg) {
                if((this instanceof DT) && this._sId) {
                    this._setFirstRow();
                    this._setLastRow();

                    // Reinstate selected and sorted classes
                    if(bReselect) {
                        // Loop over each row
                        for(j=0; j<allRows.length; j++) {
                            var thisRow = allRows[j];
                            var sMode = this.get("selectionMode");
                            if ((sMode == "standard") || (sMode == "single")) {
                                // Set SELECTED
                                for(k=0; k<allSelectedRows.length; k++) {
                                    if(allSelectedRows[k] === thisRow.yuiRecordId) {
                                        Dom.addClass(thisRow, DT.CLASS_SELECTED);
                                        if(j === allRows.length-1) {
                                            this._oAnchorRecord = this.getRecord(thisRow.yuiRecordId);
                                        }
                                    }
                                }
                            }
                            else {
                                // Loop over each cell
                                for(k=0; k<thisRow.cells.length; k++) {
                                    var thisCell = thisRow.cells[k];
                                    // Set SELECTED
                                    for(l=0; l<allSelectedCells.length; l++) {
                                        if((allSelectedCells[l].recordId === thisRow.yuiRecordId) &&
                                                (allSelectedCells[l].columnId === thisCell.yuiColumnId)) {
                                            Dom.addClass(thisCell, DT.CLASS_SELECTED);
                                            if(k === thisRow.cells.length-1) {
                                                this._oAnchorCell = {record:this.getRecord(thisRow.yuiRecordId), column:this.getColumnById(thisCell.yuiColumnId)};
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(this._bInit) {
                    this._oChain.add({
                        method: function() {
                            if((this instanceof DT) && this._sId && this._bInit) {
                                this._bInit = false;
                                this.fireEvent("initEvent");
                                YAHOO.log("DataTable initialized with " + allRecords.length + " of " + this._oRecordSet.getLength() + " rows", "info", this.toString());
                            }
                        },
                        scope: this
                    });
                    this._oChain.run();
                }
                else {
                    this.fireEvent("renderEvent");
                    // Backward compatibility
                    this.fireEvent("refreshEvent");
                    YAHOO.log("DataTable rendered " + allRecords.length + " of " + this._oRecordSet.getLength() + " rows", "info", this.toString());
                }
            
            
            },
            scope: this,
            timeout: (loopN > 0) ? 0 : -1
        }); 
        
        this._oChain.add({
            method: function() {
                if((this instanceof DT) && this._sId) {
                    this._syncColWidths();
                }
            },
            scope: this
        });
        
        // Bug 1741322: Force FF to redraw to fix squishy headers on wide tables
        if(ua.gecko) {
            this._oChain.add({
                method: function(oArg) {
                    if((this instanceof DT) && this._sId) {
                        Dom.removeClass(this.getContainerEl(),"yui-dt-noop");
                    }
                },
                scope: this
            });
            this._oChain.add({
                method: function() {
                    if((this instanceof DT) && this._sId) {
                        Dom.addClass(this.getContainerEl(),"yui-dt-noop");
                    }
                },
                scope:this
            });
        }
            
        this._oChain.run();  
    }
    // Empty
    else {
        // Remove all rows
        while(elTbody.hasChildNodes()) {
            elTbody.deleteRow(-1);
        }

        this.showTableMessage(DT.MSG_EMPTY, DT.CLASS_EMPTY);
    }
},

/**
 * Nulls out the entire DataTable instance and related objects, removes attached
 * event listeners, and clears out DOM elements inside the container. After
 * calling this method, the instance reference should be expliclitly nulled by
 * implementer, as in myDataTable = null. Use with caution!
 *
 * @method destroy
 */
destroy : function() {
    this._oChain.stop();
    
    //TODO: destroy static resizer proxy and column proxy?
    
    var i;
    // Destroy ColumnDDs
    var aTree = this._oColumnSet.tree[0];
    for(i=0; i<aTree.length; i++) {
        if(aTree[i]._dd) {
            aTree[i]._dd = aTree[i]._dd.unreg();
        }
    }

    // Destroy ColumnResizers
    var aKeys = this._oColumnSet.keys;
    for(i=0; i<aKeys.length; i++) {
        if(aKeys[i]._ddResizer) {
            aKeys[i]._ddResizer = aKeys[i]._ddResizer.unreg();
        }
    }
    
    // Destroy Cell Editor
    Ev.purgeElement(this._oCellEditor.container, true);
    document.body.removeChild(this._oCellEditor.container);

    var instanceName = this.toString();
    var elContainer = this._elContainer;

    // Unhook custom events
    this._oRecordSet.unsubscribeAll();
    this.unsubscribeAll();

    // Unhook DOM events
    Ev.purgeElement(elContainer, true);
    Ev.removeListener(document, "click", this._onDocumentClick);

    // Remove DOM elements
    elContainer.innerHTML = "";

    // Null out objects
    for(var param in this) {
        if(lang.hasOwnProperty(this, param)) {
            this[param] = null;
        }
    }
    
    // Clean up static values
    DT._nCurrentCount--;
    
    if(DT._nCurrentCount < 1) {
        if(DT._elStylesheet) {
            document.getElementsByTagName('head')[0].removeChild(DT._elStylesheet);
            DT._elStylesheet = null;
        }
    }

    YAHOO.log("DataTable instance destroyed: " + instanceName);
},

/**
 * Displays message within secondary TBODY.
 *
 * @method showTableMessage
 * @param sHTML {String} (optional) Value for innerHTMlang.
 * @param sClassName {String} (optional) Classname.
 */
showTableMessage : function(sHTML, sClassName) {
    var elCell = this._elMsgTd;
    if(lang.isString(sHTML)) {
        elCell.firstChild.innerHTML = sHTML;
    }
    if(lang.isString(sClassName)) {
        Dom.addClass(elCell.firstChild, sClassName);
    }

    var elCellLiner = elCell.firstChild;
    elCellLiner.style.width = ((this.getTheadEl().parentNode.offsetWidth) -
        (parseInt(Dom.getStyle(elCellLiner,"paddingLeft"),10)) -
        (parseInt(Dom.getStyle(elCellLiner,"paddingRight"),10))) + "px";

    this._elMsgTbody.style.display = "";
    this.fireEvent("tableMsgShowEvent", {html:sHTML, className:sClassName});
    YAHOO.log("DataTable showing message: " + sHTML, "info", this.toString());
},

/**
 * Hides secondary TBODY.
 *
 * @method hideTableMessage
 */
hideTableMessage : function() {
    if(this._elMsgTbody.style.display != "none") {
        this._elMsgTbody.style.display = "none";
        this.fireEvent("tableMsgHideEvent");
        YAHOO.log("DataTable message hidden", "info", this.toString());
    }
},

/**
 * Brings focus to the TBODY element. Alias to focusTbodyEl.
 *
 * @method focus
 */
focus : function() {
    this.focusTbodyEl();
},

/**
 * Brings focus to the THEAD element.
 *
 * @method focusTheadEl
 */
focusTheadEl : function() {
    this._focusEl(this._elThead);
},

/**
 * Brings focus to the TBODY element.
 *
 * @method focusTbodyEl
 */
focusTbodyEl : function() {
    this._focusEl(this._elTbody);
},

































































// RECORDSET FUNCTIONS

/**
 * Returns Record index for given TR element or page row index.
 *
 * @method getRecordIndex
 * @param row {YAHOO.widget.Record | HTMLElement | Number} Record instance, TR
 * element reference or page row index.
 * @return {Number} Record's RecordSet index, or null.
 */
getRecordIndex : function(row) {
    var nTrIndex;

    if(!lang.isNumber(row)) {
        // By Record
        if(row instanceof YAHOO.widget.Record) {
            return this._oRecordSet.getRecordIndex(row);
        }
        // By element reference
        else {
            // Find the TR element
            var el = this.getTrEl(row);
            if(el) {
                nTrIndex = el.sectionRowIndex;
            }
        }
    }
    // By page row index
    else {
        nTrIndex = row;
    }

    if(lang.isNumber(nTrIndex)) {
        var oPaginator = this.get("paginator");
        if(oPaginator instanceof Pag) {
            return oPaginator.get('recordOffset') + nTrIndex;
        }
        else if (this.get('paginated')) {
            return oPaginator.startRecordIndex + nTrIndex;
        }
        else {
            return nTrIndex;
        }
    }

    YAHOO.log("Could not get Record index for row " + row, "info", this.toString());
    return null;
},

/**
 * For the given identifier, returns the associated Record instance.
 *
 * @method getRecord
 * @param row {HTMLElement | Number | String} DOM reference to a TR element (or
 * child of a TR element), RecordSet position index, or Record ID.
 * @return {YAHOO.widget.Record} Record instance.
 */
getRecord : function(row) {
    var oRecord = this._oRecordSet.getRecord(row);

    if(!oRecord) {
        // Validate TR element
        var elRow = this.getTrEl(row);
        if(elRow) {
            oRecord = this._oRecordSet.getRecord(elRow.yuiRecordId);
        }
    }

    if(oRecord instanceof YAHOO.widget.Record) {
        return this._oRecordSet.getRecord(oRecord);
    }
    else {
        YAHOO.log("Could not get Record for row at " + row, "info", this.toString());
        return null;
    }
},














































// COLUMN FUNCTIONS

/**
 * For the given identifier, returns the associated Column instance. Note: For
 * getting Columns by Column ID string, please use the method getColumnById().
 *
 * @method getColumn
 * @param column {HTMLElement | String | Number} DOM reference or ID string to a
 * TH/TD element (or child of a TH/TD element), a Column key, or a ColumnSet key index.
 * @return {YAHOO.widget.Column} Column instance.
 */
getColumn : function(column) {
    var oColumn = this._oColumnSet.getColumn(column);

    if(!oColumn) {
        // Validate TD element
        var elCell = this.getTdEl(column);
        if(elCell) {
            oColumn = this._oColumnSet.getColumnById(elCell.yuiColumnId);
        }
        // Validate TH element
        else {
            elCell = this.getThEl(column);
            if(elCell) {
                oColumn = this._oColumnSet.getColumnById(elCell.yuiColumnId);
            }
        }
    }
    if(!oColumn) {
        YAHOO.log("Could not get Column for column at " + column, "info", this.toString());
    }
    return oColumn;
},

/**
 * For the given Column ID, returns the associated Column instance. Note: For
 * getting Columns by key, please use the method getColumn().
 *
 * @method getColumnById
 * @param column {String} Column ID string.
 * @return {YAHOO.widget.Column} Column instance.
 */
getColumnById : function(column) {
    return this._oColumnSet.getColumnById(column);
},

/**
 * For the given Column instance, returns next direction to sort.
 *
 * @method getColumnSortDir
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @return {String} DataTable.widget.CLASS_ASC or DataTable.widget.CLASS_DESC.
 */
getColumnSortDir : function(oColumn) {
    // Backward compatibility
    if(oColumn.sortOptions && oColumn.sortOptions.defaultOrder) {
        if(oColumn.sortOptions.defaultOrder == "asc") {
            oColumn.sortOptions.defaultDir = DT.CLASS_ASC;
        }
        else if (oColumn.sortOptions.defaultOrder == "desc") {
            oColumn.sortOptions.defaultDir = DT.CLASS_DESC;
        }
    }
    
    // What is the Column's default sort direction?
    var sortDir = (oColumn.sortOptions && oColumn.sortOptions.defaultDir) ? oColumn.sortOptions.defaultDir : DT.CLASS_ASC;

    // Already sorted?
    var bSorted = false;
    var oSortedBy = this.get("sortedBy");
    if(oSortedBy && (oSortedBy.key === oColumn.key)) {
        bSorted = true;
        if(oSortedBy.dir) {
            sortDir = (oSortedBy.dir == DT.CLASS_ASC) ? DT.CLASS_DESC : DT.CLASS_ASC;
        }
        else {
            sortDir = (sortDir == DT.CLASS_ASC) ? DT.CLASS_DESC : DT.CLASS_ASC;
        }
    }
    return sortDir;
},

/**
 * Sorts given Column.
 *
 * @method sortColumn
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param sDir {String} (Optional) DT.CLASS_ASC or
 * DT.CLASS_DESC
 */
sortColumn : function(oColumn, sDir) {
    if(oColumn && (oColumn instanceof YAHOO.widget.Column)) {
        if(!oColumn.sortable) {
            Dom.addClass(this.getThEl(oColumn), DT.CLASS_SORTABLE);
        }
        
        // Validate given direction
        if(sDir && (sDir !== DT.CLASS_ASC) && (sDir !== DT.CLASS_DESC)) {
            sDir = null;
        }
        
        // Get the sort dir
        var sortDir = sDir || this.getColumnSortDir(oColumn);

        // Do the actual sort
        var oSortedBy = this.get("sortedBy") || {};
        var bSorted = (oSortedBy.key === oColumn.key) ? true : false;
        if(!bSorted || sDir) {
            // Is there a custom sort handler function defined?
            var sortFnc = (oColumn.sortOptions && lang.isFunction(oColumn.sortOptions.sortFunction)) ?
                    // Custom sort function
                    oColumn.sortOptions.sortFunction :

                    // Default sort function
                    function(a, b, desc) {
                        var sorted = YAHOO.util.Sort.compare(a.getData(oColumn.key),b.getData(oColumn.key), desc);
                        if(sorted === 0) {
                            return YAHOO.util.Sort.compare(a.getId(),b.getId(), desc);
                        }
                        else {
                            return sorted;
                        }
                    };

            this._oRecordSet.sortRecords(sortFnc, ((sortDir == DT.CLASS_DESC) ? true : false));
        }
        else {
            this._oRecordSet.reverseRecords();
        }

        // Update sortedBy tracker
        this.set("sortedBy", {key:oColumn.key, dir:sortDir, column:oColumn});

        // Reset to first page
        //TODO: Keep selection in view
        var oPaginator = this.get('paginator');
        if (oPaginator instanceof Pag) {
            // TODO : is this server-side op safe?  Will fire changeRequest
            // event mechanism
            oPaginator.setPage(1,true);
        }
        else if (this.get('paginated')) {
            // Backward compatibility
            this.updatePaginator({currentPage:1});
        }

        // Update the UI
        DT.formatTheadCell(oColumn.getThEl().firstChild.firstChild, oColumn, this);
        this.render();

        this.fireEvent("columnSortEvent",{column:oColumn,dir:sortDir});
        YAHOO.log("Column \"" + oColumn.key + "\" sorted \"" + sortDir + "\"", "info", this.toString());
    }
    else {
        YAHOO.log("Could not sort Column \"" + oColumn.key + "\"", "warn", this.toString());
    }
},

/**
 * Sets DOM elements of given Column to given pixel width. No validations
 * against minimum width and no updating Column.width value.
 *
 * @method _setColumnWidth
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param sWidth {String} New width value.
 * @private
 */
_setColumnWidth : function(oColumn, sWidth) {
    oColumn = this.getColumn(oColumn);
    if(oColumn) {
        // Create STYLE node
        if(!DT._bStylesheetFallback) {
            var s;
            if(!DT._elStylesheet) {
                    s = document.createElement('style');
                    s.type = 'text/css';
                    DT._elStylesheet = document.getElementsByTagName('head').item(0).appendChild(s);
            }
                
            if(DT._elStylesheet) {
                s = DT._elStylesheet;
                
                var sClassname = ".yui-dt-col-" + oColumn.getId();
                
                // Create rule for the Column
                var rule = DT._oStylesheetRules[sClassname];
                if (!rule) {
                    if (s.styleSheet && s.styleSheet.addRule) {
                        s.styleSheet.addRule(sClassname,"width:"+sWidth);
                        rule = s.styleSheet.rules[s.styleSheet.rules.length-1];
                    } else if (s.sheet && s.sheet.insertRule) {
                        s.sheet.insertRule(sClassname+" {width:"+sWidth+";}",s.sheet.cssRules.length);
                        rule = s.sheet.cssRules[s.sheet.cssRules.length];
                    } else {
                        DT._bStylesheetFallback = true;
                    }
                    DT._oStylesheetRules[sClassname] = rule;
                }
                
                // Update existing rule for the Column
                else {
                    rule.style.width = sWidth;
                }
                
                return;
            }
            
            DT._bStylesheetFallback = true;
        }
        // Do it the old fashioned way
        if(DT._bStylesheetFallback) {
            if(sWidth == "auto") {
                sWidth = ""; 
            }

            if (!this._aFallbackColResizer[this._elTbody.rows.length]) {
                /*
                Compile a custom function to do all the cell width
                assignments at the same time.  A new resizer function is created
                for each new unique number of rows in _elTbody.  This will
                result in a function declaration like:
                function (oColumn,sWidth) {
                    var colIdx = oColumn.getKeyIndex();
                    oColumn.getThEl().firstChild.style.width =
                    this._elTbody.rows[0].cells[colIdx].firstChild.style.width =
                    this._elTbody.rows[0].cells[colIdx].style.width =
                    this._elTbody.rows[1].cells[colIdx].firstChild.style.width =
                    this._elTbody.rows[1].cells[colIdx].style.width =
                    ... (for all row indices in this._elTbody.rows.length - 1)
                    this._elTbody.rows[99].cells[colIdx].firstChild.style.width =
                    this._elTbody.rows[99].cells[colIdx].style.width = sWidth;
                    ending with the sWidth as the initial assignment   ^
                }
                */

                var resizerDef = [
                    'var colIdx=oColumn.getKeyIndex();',
                    'oColumn.getThEl().firstChild.style.width='
                ];
                for (var i=this._elTbody.rows.length-1, j=2; i >= 0; --i) {
                    resizerDef[j++] = 'this._elTbody.rows[';
                    resizerDef[j++] = i;
                    resizerDef[j++] = '].cells[colIdx].firstChild.style.width=';
                    resizerDef[j++] = 'this._elTbody.rows[';
                    resizerDef[j++] = i;
                    resizerDef[j++] = '].cells[colIdx].style.width=';
                }
                resizerDef[j] = 'sWidth;';
                this._aFallbackColResizer[this._elTbody.rows.length] =
                    new Function('oColumn','sWidth',resizerDef.join(''));
            }
            var resizerFn = this._aFallbackColResizer[this._elTbody.rows.length];

            if (resizerFn) {
                resizerFn.call(this,oColumn,sWidth);
            }
        }
    }
    else {
        YAHOO.log("Could not set width of Column " + oColumn + " to " + sWidth, "warn", this.toString());
    }
},

/**
 * Sets given Column to given pixel width. If new width is less than minimum
 * width, sets to minimum width. Updates oColumn.width value.
 *
 * @method setColumnWidth
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param nWidth {Number} New width in pixels.
 */
setColumnWidth : function(oColumn, nWidth) {
    oColumn = this.getColumn(oColumn);
    if(oColumn) {
        // Validate new width against minimum width
        var sWidth = "";
        if(lang.isNumber(nWidth)) {
            sWidth = (nWidth > oColumn.minWidth) ? nWidth + "px" : oColumn.minWidth + "px";
        }

        // Save state
        oColumn.width = parseInt(sWidth,10);
        
        // Resize the DOM elements
        this._setColumnWidth(oColumn, sWidth);
        
        this._syncScrollPadding();
        
        this.fireEvent("columnSetWidthEvent",{column:oColumn,width:nWidth});
        YAHOO.log("Set width of Column " + oColumn + " to " + nWidth + "px", "info", this.toString());
    }
    else {
        YAHOO.log("Could not set width of Column " + oColumn + " to " + nWidth + "px", "warn", this.toString());
    }
},


/**
 * Hides given Column. NOTE: You cannot hide/show nested Columns. You can only
 * hide/show non-nested Columns, and top-level parent Columns (which will
 * hide/show all children Columns).
 *
 * @method hideColumn
 * @param oColumn {YAHOO.widget.Column} Column instance.
 */
hideColumn : function(oColumn) {
    oColumn = this.getColumn(oColumn);
    if(oColumn && !oColumn.hidden) {
        // Only top-level Columns can get hidden
        if(oColumn.getTreeIndex() !== null) {
            var allrows = this.getTbodyEl().rows;
            var l = allrows.length;
            var allDescendants = this._oColumnSet.getDescendants(oColumn);
            for(var i=0; i<allDescendants.length; i++) {
                var thisColumn = allDescendants[i];
                thisColumn.hidden = true;

                var elTheadCell = thisColumn.getThEl();
                var elTheadCellLiner = elTheadCell.firstChild;
                // Store to reinstate later
                thisColumn._nLastWidth = elTheadCellLiner.offsetWidth - 
                        (parseInt(Dom.getStyle(elTheadCellLiner,"paddingLeft"),10)|0) -
                        (parseInt(Dom.getStyle(elTheadCellLiner,"paddingRight"),10)|0);
                Dom.addClass(elTheadCell,DT.CLASS_HIDDEN);

                // Adjust body cells (if key Column)
                var thisKeyIndex = thisColumn.getKeyIndex();
                if(thisKeyIndex !== null) {
                    for(var j=0;j<l;j++) {
                        Dom.addClass(allrows[j].cells[thisKeyIndex],DT.CLASS_HIDDEN);
                    }

                    this._setColumnWidth(thisColumn, "1px");
                    
                    // Disable interactive features
                    if(thisColumn.resizeable) {
                        Dom.removeClass(thisColumn.getResizerEl(),DT.CLASS_RESIZER);
                    }
                    if(thisColumn.sortable) {
                        Dom.removeClass(thisColumn.getThEl(),DT.CLASS_SORTABLE);
                        thisColumn.getThEl().firstChild.firstChild.firstChild.style.display = "none";
                    }
                }
                // Just set thead cell width directly for parent Column
                else {
                    elTheadCell.firstChild.style.width = "1px";
                }
                
                this.fireEvent("columnHideEvent",{column:thisColumn});
                YAHOO.log("Column \"" + oColumn.key + "\" hidden", "info", this.toString());
            }
        }
        else {
            YAHOO.log("Could not hide Column \"" + oColumn.key + "\". Only non-nested Columns can be hidden", "warn", this.toString());
        }
    }
},

/**
 * Shows given Column. NOTE: You cannot hide/show nested Columns. You can only
 * hide/show non-nested Columns, and top-level parent Columns (which will
 * hide/show all children Columns).
 *
 * @method showColumn
 * @param oColumn {YAHOO.widget.Column} Column instance.
 */
showColumn : function(oColumn) {
    oColumn = this.getColumn(oColumn);
    if(oColumn && oColumn.hidden) {
        // Only top-level Columns can get hidden
        if(oColumn.getTreeIndex() !== null) {
            var allrows = this.getTbodyEl().rows;
            var l = allrows.length;
            var allDescendants = this._oColumnSet.getDescendants(oColumn);
            for(var i=0; i<allDescendants.length; i++) {
                var thisColumn = allDescendants[i];
                thisColumn.hidden = false;
                
                var elTheadCell = thisColumn.getThEl();
                Dom.removeClass(elTheadCell,DT.CLASS_HIDDEN);

                // Adjust body cells (if key Column)
                var thisKeyIndex = thisColumn.getKeyIndex();
                if(thisKeyIndex !== null) {
                    for(var j=0;j<l;j++) {
                        Dom.removeClass(allrows[j].cells[thisKeyIndex],DT.CLASS_HIDDEN);
                    }
                    
                    this.setColumnWidth(thisColumn, (thisColumn._nLastWidth || thisColumn.minWidth), true);

                    // Enable interactive features
                    if(thisColumn.sortable) {
                        thisColumn.getThEl().firstChild.firstChild.firstChild.style.display = "";
                        Dom.removeClass(thisColumn.getThEl(),DT.CLASS_SORTABLE);
                    }
                    if(thisColumn.resizeable) {
                        thisColumn._ddResizer.resetResizerEl();
                        Dom.addClass(thisColumn.getResizerEl(),DT.CLASS_RESIZER);
                    }
                }
                else {
                    elTheadCell.firstChild.style.width = "";
                }

                thisColumn._nLastWidth = null;
                this.fireEvent("columnShowEvent",{column:thisColumn});
                YAHOO.log("Column \"" + oColumn.key + "\" shown", "info", this.toString());
            }
        }
        else {
            YAHOO.log("Could not show Column \"" + oColumn.key + "\". Only non-nested Columns can be shown", "warn", this.toString());
        }
    }
},

/**
 * Removes given Column. NOTE: You cannot remove nested Columns. You can only remove
 * non-nested Columns, and top-level parent Columns (which will remove all
 * children Columns).
 *
 * @method removeColumn
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @return oColumn {YAHOO.widget.Column} Removed Column instance.
 */
removeColumn : function(oColumn) {
    var nColTreeIndex = oColumn.getTreeIndex();
    if(nColTreeIndex !== null) {
        this._oChain.stop();
        var aOrigColumnDefs = this._oColumnSet.getDefinitions();

        oColumn = aOrigColumnDefs.splice(nColTreeIndex,1)[0];
        this._initColumnSet(aOrigColumnDefs);
        this._initTheadEls();

        this.render();
        this.fireEvent("columnRemoveEvent",{column:oColumn});
        YAHOO.log("Column \"" + oColumn.key + "\" removed", "info", this.toString());
        return oColumn;
    }
    YAHOO.log("Could not remove Column \"" + oColumn.key + "\". Only non-nested Columns can be removed", "warn", this.toString());
},

/**
 * Inserts given Column at the index if given, otherwise at the end. NOTE: You
 * can only add non-nested Columns and top-level parent Columns. You cannot add
 * a nested Column to an existing parent.
 *
 * @method insertColumn
 * @param oColumn {Object | YAHOO.widget.Column} Object literal Column
 * definition or a Column instance.
 * @param index {Number} (optional) Column key index.
 */
insertColumn : function(oColumn, index) {
    // Validate Column
    if(oColumn instanceof YAHOO.widget.Column) {
        oColumn = oColumn.getDefinition();
    }
    else if(oColumn.constructor !== Object) {
        YAHOO.log("Could not insert Column \"" + oColumn + "\" due to invalid argument", "warn", this.toString());
        return;
    }
    
    var oColumnSet = this._oColumnSet;

    // Validate index
    if(!lang.isValue(index) || !lang.isNumber(index)) {
        index = oColumnSet.tree[0].length;
    }
    
    this._oChain.stop();
    var aNewColumnDefs = this._oColumnSet.getDefinitions();
    aNewColumnDefs.splice(index, 0, oColumn);
    this._initColumnSet(aNewColumnDefs);
    this._initTheadEls();
    this.render();
    this.fireEvent("columnInsertEvent",{column:oColumn,index:index});
    YAHOO.log("Column \"" + oColumn.key + "\" inserted into index " + index, "info", this.toString());
},

/**
 * Selects given Column. NOTE: You cannot select/unselect nested Columns. You can only
 * select/unselect non-nested Columns, and bottom-level key Columns.
 *
 * @method selectColumn
 * @param column {HTMLElement | String | Number} DOM reference or ID string to a
 * TH/TD element (or child of a TH/TD element), a Column key, or a ColumnSet key index.
 */
selectColumn : function(oColumn) {
    oColumn = this.getColumn(oColumn);
    if(oColumn && !oColumn.selected) {
        // Only bottom-level Columns can get hidden
        if(oColumn.getKeyIndex() !== null) {
            oColumn.selected = true;
            
            // Update head cell
            var elTh = oColumn.getThEl();
            Dom.addClass(elTh,DT.CLASS_SELECTED);

            // Update body cells
            var allRows = this.getTbodyEl().rows;
            var oChain = this._oChain;
            oChain.add({
                method: function(oArg) {
                    if((this instanceof DT) && this._sId && allRows[oArg.rowIndex] && allRows[oArg.rowIndex].cells[oArg.cellIndex]) {
                        Dom.addClass(allRows[oArg.rowIndex].cells[oArg.cellIndex],DT.CLASS_SELECTED);                    
                    }
                    oArg.rowIndex++;
                },
                scope: this,
                iterations: allRows.length,
                argument: {rowIndex:0,cellIndex:oColumn.getKeyIndex()}
            });
            oChain.run();       
            
            this.fireEvent("columnSelectEvent",{column:oColumn});
            YAHOO.log("Column \"" + oColumn.key + "\" selected", "info", this.toString());
        }
        else {
            YAHOO.log("Could not select Column \"" + oColumn.key + "\". Only non-nested Columns can be selected", "warn", this.toString());
        }
    }
},

/**
 * Unselects given Column. NOTE: You cannot select/unselect nested Columns. You can only
 * select/unselect non-nested Columns, and bottom-level key Columns.
 *
 * @method unSelectColumn
 * @param column {HTMLElement | String | Number} DOM reference or ID string to a
 * TH/TD element (or child of a TH/TD element), a Column key, or a ColumnSet key index.
 */
unselectColumn : function(oColumn) {
    oColumn = this.getColumn(oColumn);
    if(oColumn && oColumn.selected) {
        // Only bottom-level Columns can get hidden
        if(oColumn.getKeyIndex() !== null) {
            oColumn.selected = false;
            
            // Update head cell
            var elTh = oColumn.getThEl();
            Dom.removeClass(elTh,DT.CLASS_SELECTED);

            // Update body cells
            var allRows = this.getTbodyEl().rows;
            var oChain = this._oChain;
            oChain.add({
                method: function(oArg) {
                    if((this instanceof DT) && this._sId && allRows[oArg.rowIndex] && allRows[oArg.rowIndex].cells[oArg.cellIndex]) {
                        Dom.removeClass(allRows[oArg.rowIndex].cells[oArg.cellIndex],DT.CLASS_SELECTED); 
                    }                   
                    oArg.rowIndex++;
                },
                scope: this,
                iterations:allRows.length,
                argument: {rowIndex:0,cellIndex:oColumn.getKeyIndex()}
            });
            oChain.run();       
            
            this.fireEvent("columnUnselectEvent",{column:oColumn});
            YAHOO.log("Column \"" + oColumn.key + "\" unselected", "info", this.toString());
        }
        else {
            YAHOO.log("Could not unselect Column \"" + oColumn.key + "\". Only non-nested Columns can be unselected", "warn", this.toString());
        }
    }
},

/**
 * Returns an array selected Column instances.
 *
 * @method getSelectedColumns
 * @return {YAHOO.widget.Column[]} Array of Column instances.
 */
getSelectedColumns : function(oColumn) {
    var selectedColumns = [];
    var aKeys = this._oColumnSet.keys;
    for(var i=0,len=aKeys.length; i<len; i++) {
        if(aKeys[i].selected) {
            selectedColumns[selectedColumns.length] = aKeys[i];
        }
    }
    return selectedColumns;
},

/**
 * Assigns the class DT.CLASS_HIGHLIGHTED to cells of the given Column.
 * NOTE: You cannot highlight/unhighlight nested Columns. You can only
 * highlight/unhighlight non-nested Columns, and bottom-level key Columns.
 *
 * @method highlightColumn
 * @param column {HTMLElement | String | Number} DOM reference or ID string to a
 * TH/TD element (or child of a TH/TD element), a Column key, or a ColumnSet key index.
 */
highlightColumn : function(column) {
    var oColumn = this.getColumn(column);
    // Only bottom-level Columns can get highlighted
    if(oColumn && (oColumn.getKeyIndex() !== null)) {
        /*// Make sure previous row is unhighlighted
        var sId = oColumn.getId();
        var sLastId = this._sLastHighlightedColumnId;
        if(sLastId && (sLastId !== sId)) {
            this.unhighlightColumn(this.getColumn(sLastId));
        }*/

        //this._sLastHighlightedColumnId = sId;
            
        // Update head cell
        var elTh = oColumn.getThEl();
        Dom.addClass(elTh,DT.CLASS_HIGHLIGHTED);

        // Update body cells
        var allRows = this.getTbodyEl().rows;
        var oChain = this._oChain;
        oChain.add({
            method: function(oArg) {
                if((this instanceof DT) && this._sId && allRows[oArg.rowIndex] && allRows[oArg.rowIndex].cells[oArg.cellIndex]) {
                    Dom.addClass(allRows[oArg.rowIndex].cells[oArg.cellIndex],DT.CLASS_HIGHLIGHTED);   
                }                 
                oArg.rowIndex++;
            },
            scope: this,
            iterations:allRows.length,
            argument: {rowIndex:0,cellIndex:oColumn.getKeyIndex()}
        });
        oChain.run();       
            
        this.fireEvent("columnHighlightEvent",{column:oColumn});
        YAHOO.log("Column \"" + oColumn.key + "\" highlighed", "info", this.toString());
    }
    else {
        YAHOO.log("Could not highlight Column \"" + oColumn.key + "\". Only non-nested Columns can be highlighted", "warn", this.toString());
    }
},

/**
 * Removes the class DT.CLASS_HIGHLIGHTED to cells of the given Column.
 * NOTE: You cannot highlight/unhighlight nested Columns. You can only
 * highlight/unhighlight non-nested Columns, and bottom-level key Columns.
 *
 * @method unhighlightColumn
 * @param column {HTMLElement | String | Number} DOM reference or ID string to a
 * TH/TD element (or child of a TH/TD element), a Column key, or a ColumnSet key index.
 */
unhighlightColumn : function(column) {
    var oColumn = this.getColumn(column);
    // Only bottom-level Columns can get highlighted
    if(oColumn && (oColumn.getKeyIndex() !== null)) {
        // Update head cell
        var elTh = oColumn.getThEl();
        Dom.removeClass(elTh,DT.CLASS_HIGHLIGHTED);

        // Update body cells
        var allRows = this.getTbodyEl().rows;
        var oChain = this._oChain;
        oChain.add({
            method: function(oArg) {
                if((this instanceof DT) && this._sId && allRows[oArg.rowIndex] && allRows[oArg.rowIndex].cells[oArg.cellIndex]) {
                    Dom.removeClass(allRows[oArg.rowIndex].cells[oArg.cellIndex],DT.CLASS_HIGHLIGHTED);
                }                 
                oArg.rowIndex++;
            },
            scope: this,
            iterations:allRows.length,
            argument: {rowIndex:0,cellIndex:oColumn.getKeyIndex()}
        });
        oChain.run();       
            
        this.fireEvent("columnUnhighlightEvent",{column:oColumn});
        YAHOO.log("Column \"" + oColumn.key + "\" unhighlighted", "info", this.toString());
    }
    else {
        YAHOO.log("Could not unhighlight Column \"" + oColumn.key + "\". Only non-nested Columns can be unhighlighted", "warn", this.toString());
    }
},












































// ROW FUNCTIONS


/**
 * Adds one new Record of data into the RecordSet at the index if given,
 * otherwise at the end. If the new Record is in page view, the
 * corresponding DOM elements are also updated.
 *
 * @method addRow
 * @param oData {Object} Object literal of data for the row.
 * @param index {Number} (optional) RecordSet position index at which to add data.
 */
addRow : function(oData, index) {
    if(oData && (oData.constructor == Object)) {
        var oRecord = this._oRecordSet.addRecord(oData, index);
        if(oRecord) {
            var recIndex;
            var oPaginator = this.get('paginator');

            // Paginated
            if (oPaginator instanceof Pag ||
                this.get('paginated')) {
                recIndex = this.getRecordIndex(oRecord);
                var endRecIndex;
                if (oPaginator instanceof Pag) {
                    // Update the paginator's totalRecords
                    var totalRecords = oPaginator.get('totalRecords');
                    if (totalRecords !== Pag.VALUE_UNLIMITED) {
                        oPaginator.set('totalRecords',totalRecords + 1);
                    }

                    endRecIndex = (oPaginator.getPageRecords())[1];
                }
                // Backward compatibility
                else {
                    endRecIndex = oPaginator.startRecordIndex +
                                  oPaginator.rowsPerPage - 1;
                    this.updatePaginator();
                }

                // New record affects the view
                if (recIndex <= endRecIndex) {
                    this.render();
                }
                
                // TODO: what args to pass?
                this.fireEvent("rowAddEvent", {record:oRecord});
        
                // For log message
                recIndex = (lang.isValue(recIndex))? recIndex : "n/a";
        
                YAHOO.log("Added row: Record ID = " + oRecord.getId() +
                        ", Record index = " + this.getRecordIndex(oRecord) +
                        ", page row index = " + recIndex, "info", this.toString());
                
                return;
            }
            // Not paginated
            else {
                recIndex = this.getTrIndex(oRecord);
                if(lang.isNumber(recIndex)) {
                    if((this instanceof DT) && this._sId) {
                        // Add the TR element
                        var elNewTr = this._createTrEl(oRecord);
                        if(elNewTr) {
                            if (recIndex >= 0 && recIndex < this._elTbody.rows.length) {
                                this._elTbody.insertBefore(elNewTr,
                                    this._elTbody.rows[recIndex]);
                                if (!recIndex) {
                                    this._setFirstRow();
                                }
                            } else {
                                this._elTbody.appendChild(elNewTr);
                                this._setLastRow();
                                recIndex = this._elTbody.rows.length - 1;
                            }
                            this._setRowStripes(recIndex);

                            this._syncColWidths();
                        }
                        this.hideTableMessage();

                        // TODO: what args to pass?
                        this.fireEvent("rowAddEvent", {record:oRecord});

                        // For log message
                        recIndex = (lang.isValue(recIndex))? recIndex : "n/a";

                        YAHOO.log("Added row: Record ID = " + oRecord.getId() +
                                ", Record index = " + this.getRecordIndex(oRecord) +
                                ", page row index = " + recIndex, "info", this.toString());
                    }
                    return;
                }
            }            
        }
    }
    YAHOO.log("Could not add row with " + lang.dump(oData), "error", this.toString());
},

/**
 * Convenience method to add multiple rows.
 *
 * @method addRows
 * @param aData {Object[]} Array of object literal data for the rows.
 * @param index {Number} (optional) RecordSet position index at which to add data.
 */
addRows : function(aData, index) {
    if(lang.isArray(aData)) {
        var i;
        if(lang.isNumber(index)) {
            for(i=aData.length-1; i>-1; i--) {
                this.addRow(aData[i], index);
            }
        }
        else {
            for(i=0; i<aData.length; i++) {
                this.addRow(aData[i]);
            }
        }
    }
    else {
        YAHOO.log("Could not add rows " + lang.dump(aData));
    }
},

/**
 * For the given row, updates the associated Record with the given data. If the
 * row is on current page, the corresponding DOM elements are also updated.
 *
 * @method updateRow
 * @param row {YAHOO.widget.Record | Number | HTMLElement | String}
 * Which row to update: By Record instance, by Record's RecordSet
 * position index, by HTMLElement reference to the TR element, or by ID string
 * of the TR element.
 * @param oData {Object} Object literal of data for the row.
 */
updateRow : function(row, oData) {
    var oldRecord, oldData, updatedRecord, elRow;

    // Get the Record directly
    if((row instanceof YAHOO.widget.Record) || (lang.isNumber(row))) {
            // Get the Record directly
            oldRecord = this._oRecordSet.getRecord(row);

            // Is this row on current page?
            elRow = this.getTrEl(oldRecord);
    }
    // Get the Record by TR element
    else {
        elRow = this.getTrEl(row);
        if(elRow) {
            oldRecord = this.getRecord(elRow);
        }
    }

    // Update the Record
    if(oldRecord) {
        // Copy data from the Record for the event that gets fired later
        var oRecordData = oldRecord.getData();
        oldData = YAHOO.widget.DataTable._cloneObject(oRecordData);

        updatedRecord = this._oRecordSet.updateRecord(oldRecord, oData);
    }
    else {
        YAHOO.log("Could not update row " + row + " with the data : " +
                lang.dump(oData), "error", this.toString());
        return;

    }

    // Update the TR only if row is on current page
    if(elRow) {
        this._oChain.add({
            method: function() {
                if((this instanceof DT) && this._sId) {
                    this._updateTrEl(elRow, updatedRecord);
                    this._syncColWidths();
                    this.fireEvent("rowUpdateEvent", {record:updatedRecord, oldData:oldData});
                    YAHOO.log("DataTable row updated: Record ID = " + updatedRecord.getId() +
                            ", Record index = " + this.getRecordIndex(updatedRecord) +
                            ", page row index = " + this.getTrIndex(updatedRecord), "info", this.toString());
                }
            },
            scope: this,
            timeout: (this.get("renderLoopSize") > 0) ? 0 : -1
        });
        this._oChain.run();
    }
    else {
        this.fireEvent("rowUpdateEvent", {record:updatedRecord, oldData:oldData});
        YAHOO.log("DataTable row updated: Record ID = " + updatedRecord.getId() +
                ", Record index = " + this.getRecordIndex(updatedRecord) +
                ", page row index = " + this.getTrIndex(updatedRecord), "info", this.toString());   
    }

},

/**
 * Deletes the given row's Record from the RecordSet. If the row is on current page,
 * the corresponding DOM elements are also deleted.
 *
 * @method deleteRow
 * @param row {HTMLElement | String | Number} DOM element reference or ID string
 * to DataTable page element or RecordSet index.
 */
deleteRow : function(row) {
    // Get the Record index...
    var oRecord = null;
    // ...by Record index
    if(lang.isNumber(row)) {
        oRecord = this._oRecordSet.getRecord(row);
    }
    // ...by element reference
    else {
        var elRow = Dom.get(row);
        elRow = this.getTrEl(elRow);
        if(elRow) {
            oRecord = this.getRecord(elRow);
        }
    }
    if(oRecord) {
        var oPaginator = this.get('paginator');
        var sRecordId = oRecord.getId();

        // Remove from selection tracker if there
        var tracker = this._aSelections || [];
        for(var j=tracker.length-1; j>-1; j--) {
            if((lang.isNumber(tracker[j]) && (tracker[j] === sRecordId)) ||
                    ((tracker[j].constructor == Object) && (tracker[j].recordId === sRecordId))) {
                tracker.splice(j,1);
            }
        }

        // Copy data from the Record for the event that gets fired later
        var nTrIndex = this.getTrIndex(oRecord);
        var nRecordIndex = this.getRecordIndex(oRecord);
        var oRecordData = oRecord.getData();
        var oData = YAHOO.widget.DataTable._cloneObject(oRecordData);

        // Delete Record from RecordSet
        this._oRecordSet.deleteRecord(nRecordIndex);

        // If paginated and the deleted row was on this or a prior page, just
        // re-render
        if (oPaginator instanceof Pag ||
            this.get('paginated')) {

            var endRecIndex;
            if (oPaginator instanceof Pag) {
                // Update the paginator's totalRecords
                var totalRecords = oPaginator.get('totalRecords');
                if (totalRecords !== Pag.VALUE_UNLIMITED) {
                    oPaginator.set('totalRecords',totalRecords - 1);
                }

                endRecIndex = (oPaginator.getPageRecords())[1];
            } else {
                // Backward compatibility
                endRecIndex = oPaginator.startRecordIndex +
                              oPaginator.rowsPerPage - 1;

                this.updatePaginator();
            }

            // If the deleted record was on this or a prior page, re-render
            if (nRecordIndex <= endRecIndex) {
                this.render();
            }
        }
        else {
            if(lang.isNumber(nTrIndex)) {
                this._oChain.add({
                    method: function() {
                        if((this instanceof DT) && this._sId) {
                            var isLast = (nTrIndex == this.getLastTrEl().sectionRowIndex);
                            this._deleteTrEl(nTrIndex);
            
                            // Empty body
                            if(this._elTbody.rows.length === 0) {
                                this.showTableMessage(DT.MSG_EMPTY, DT.CLASS_EMPTY);
                            }
                            // Update UI
                            else {
                                // Set FIRST/LAST
                                if(nTrIndex === 0) {
                                    this._setFirstRow();
                                }
                                if(isLast) {
                                    this._setLastRow();
                                }
                                // Set EVEN/ODD
                                if(nTrIndex != this._elTbody.rows.length) {
                                    this._setRowStripes(nTrIndex);
                                }                                
                            }
            
                            this._syncColWidths();
                            
                            this.fireEvent("rowDeleteEvent", {recordIndex:nRecordIndex,
                                    oldData:oData, trElIndex:nTrIndex});
                            YAHOO.log("DataTable row deleted: Record ID = " + sRecordId +
                                    ", Record index = " + nRecordIndex +
                                    ", page row index = " + nTrIndex, "info", this.toString());
                        }
                    },
                    scope: this,
                    timeout: (this.get("renderLoopSize") > 0) ? 0 : -1
                });
                this._oChain.run();
                return;
            }
        }

        this.fireEvent("rowDeleteEvent", {recordIndex:nRecordIndex,
                oldData:oData, trElIndex:nTrIndex});
        YAHOO.log("DataTable row deleted: Record ID = " + sRecordId +
                ", Record index = " + nRecordIndex +
                ", page row index = " + nTrIndex, "info", this.toString());
    }
    else {
        YAHOO.log("Could not delete row: " + row, "warn", this.toString());
    }
},

/**
 * Convenience method to delete multiple rows.
 *
 * @method deleteRows
 * @param row {HTMLElement | String | Number} DOM element reference or ID string
 * to DataTable page element or RecordSet index.
 * @param count {Number} (optional) How many rows to delete. A negative value
 * will delete towards the beginning.
 */
deleteRows : function(row, count) {
    // Get the Record index...
    var nRecordIndex = null;
    // ...by Record index
    if(lang.isNumber(row)) {
        nRecordIndex = row;
    }
    // ...by element reference
    else {
        var elRow = Dom.get(row);
        elRow = this.getTrEl(elRow);
        if(elRow) {
            nRecordIndex = this.getRecordIndex(elRow);
        }
    }
    if(nRecordIndex !== null) {
        if(count && lang.isNumber(count)) {
            // Start with highest index and work down
            var startIndex = (count > 0) ? nRecordIndex + count -1 : nRecordIndex;
            var endIndex = (count > 0) ? nRecordIndex : nRecordIndex + count + 1;
            for(var i=startIndex; i>endIndex-1; i--) {
                this.deleteRow(i);
            }
        }
        else {
            this.deleteRow(nRecordIndex);
        }
    }
    else {
        YAHOO.log("Could not delete row " + row, "info", this.toString());
    }
},














































// CELL FUNCTIONS

/**
 * Outputs markup into the given TD based on given Record.
 *
 * @method formatCell
 * @param elCell {HTMLElement} The liner DIV element within the TD.
 * @param oRecord {YAHOO.widget.Record} (Optional) Record instance.
 * @param oColumn {YAHOO.widget.Column} (Optional) Column instance.
 */
formatCell : function(elCell, oRecord, oColumn) {
    if(!(oRecord instanceof YAHOO.widget.Record)) {
        oRecord = this.getRecord(elCell);
    }
    if(!(oColumn instanceof YAHOO.widget.Column)) {
        oColumn = this._oColumnSet.getColumn(elCell.parentNode.yuiColumnKey);
    }

    if(oRecord && oColumn) {
        var sKey = oColumn.key;
        var oData = oRecord.getData(sKey);

        // Add classNames
        var aClasses;
        if(lang.isString(oColumn.className)) {
            aClasses = [oColumn.className];
        }
        else if(lang.isArray(oColumn.className)) {
            aClasses = oColumn.className;
        }
        else {
            aClasses = [];
        }

        //TODO: document special keys will get stripped here
        aClasses[aClasses.length] = "yui-dt-col-"+sKey.replace(/[^\w\-.:]/g,"");
        
        aClasses[aClasses.length] = "yui-dt-col-"+oColumn.getId();
        
        aClasses[aClasses.length] = DT.CLASS_LINER;

        if(oColumn.sortable) {
            aClasses[aClasses.length] = DT.CLASS_SORTABLE;
        }
        if(oColumn.resizeable) {
            aClasses[aClasses.length] = DT.CLASS_RESIZEABLE;
        }
        if(oColumn.editor) {
            aClasses[aClasses.length] = DT.CLASS_EDITABLE;
        }

        elCell.className = "";
        Dom.addClass(elCell, aClasses.join(" "));


        var fnFormatter = typeof oColumn.formatter === 'function' ?
                          oColumn.formatter :
                          DT.Formatter[oColumn.formatter+''];

        // Apply special formatter
        if(fnFormatter) {
            fnFormatter.call(this, elCell, oRecord, oColumn, oData);
        }
        else {
            elCell.innerHTML = oData === undefined ||
                               oData === null ||
                               (typeof oData === 'number' && isNaN(oData)) ?
                                "" : oData.toString();
        }

        this.fireEvent("cellFormatEvent", {record:oRecord, column:oColumn, key:sKey, el:elCell});
    }
    else {
        YAHOO.log("Could not format cell " + elCell, "error", this.toString());
    }
},


















































// PAGINATION

/**
 * Delegates the Pag changeRequest events to the configured
 * handler.
 * @method onPaginatorChange
 * @param {Object} an object literal describing the proposed pagination state
 */
onPaginatorChange : function (oState) {
    var handler = this.get('paginationEventHandler');

    handler(oState,this);
},


















































// SELECTION/HIGHLIGHTING

/**
 * ID string of last highlighted cell element
 *
 * @property _sLastHighlightedTdElId
 * @type String
 * @private
 */
//_sLastHighlightedTdElId : null,

/**
 * ID string of last highlighted row element
 *
 * @property _sLastHighlightedTrElId
 * @type String
 * @private
 */
//_sLastHighlightedTrElId : null,

/**
 * Array to track row selections (by sRecordId) and/or cell selections
 * (by {recordId:sRecordId, columnId:sColumnId})
 *
 * @property _aSelections
 * @type Object[]
 * @private
 */
_aSelections : null,

/**
 * Record instance of the row selection anchor.
 *
 * @property _oAnchorRecord
 * @type YAHOO.widget.Record
 * @private
 */
_oAnchorRecord : null,

/**
 * Object literal representing cell selection anchor:
 * {recordId:sRecordId, columnId:sColumnId}.
 *
 * @property _oAnchorCell
 * @type Object
 * @private
 */
_oAnchorCell : null,

/**
 * Convenience method to remove the class DT.CLASS_SELECTED
 * from all TR elements on the page.
 *
 * @method _unselectAllTrEls
 * @private
 */
_unselectAllTrEls : function() {
    var selectedRows = Dom.getElementsByClassName(DT.CLASS_SELECTED,"tr",this._elTbody);
    Dom.removeClass(selectedRows, DT.CLASS_SELECTED);
},

/**
 * Returns object literal of values that represent the selection trigger. Used
 * to determine selection behavior resulting from a key event.
 *
 * @method _getSelectionTrigger
 * @private
 */
_getSelectionTrigger : function() {
    var sMode = this.get("selectionMode");
    var oTrigger = {};
    var oTriggerCell, oTriggerRecord, nTriggerRecordIndex, elTriggerRow, nTriggerTrIndex;

    // Cell mode
    if((sMode == "cellblock") || (sMode == "cellrange") || (sMode == "singlecell")) {
        oTriggerCell = this.getLastSelectedCell();
        // No selected cells found
        if(!oTriggerCell) {
            return null;
        }
        else {
            oTriggerRecord = this.getRecord(oTriggerCell.recordId);
            nTriggerRecordIndex = this.getRecordIndex(oTriggerRecord);
            elTriggerRow = this.getTrEl(oTriggerRecord);
            nTriggerTrIndex = this.getTrIndex(elTriggerRow);

            // Selected cell not found on this page
            if(nTriggerTrIndex === null) {
                return null;
            }
            else {
                oTrigger.record = oTriggerRecord;
                oTrigger.recordIndex = nTriggerRecordIndex;
                oTrigger.el = this.getTdEl(oTriggerCell);
                oTrigger.trIndex = nTriggerTrIndex;
                oTrigger.column = this.getColumnById(oTriggerCell.columnId);
                oTrigger.colKeyIndex = oTrigger.column.getKeyIndex();
                oTrigger.cell = oTriggerCell;
                return oTrigger;
            }
        }
    }
    // Row mode
    else {
        oTriggerRecord = this.getLastSelectedRecord();
        // No selected rows found
        if(!oTriggerRecord) {
                return null;
        }
        else {
            // Selected row found, but is it on current page?
            oTriggerRecord = this.getRecord(oTriggerRecord);
            nTriggerRecordIndex = this.getRecordIndex(oTriggerRecord);
            elTriggerRow = this.getTrEl(oTriggerRecord);
            nTriggerTrIndex = this.getTrIndex(elTriggerRow);

            // Selected row not found on this page
            if(nTriggerTrIndex === null) {
                return null;
            }
            else {
                oTrigger.record = oTriggerRecord;
                oTrigger.recordIndex = nTriggerRecordIndex;
                oTrigger.el = elTriggerRow;
                oTrigger.trIndex = nTriggerTrIndex;
                return oTrigger;
            }
        }
    }
},

/**
 * Returns object literal of values that represent the selection anchor. Used
 * to determine selection behavior resulting from a user event.
 *
 * @method _getSelectionAnchor
 * @param oTrigger {Object} (Optional) Object literal of selection trigger values
 * (for key events).
 * @private
 */
_getSelectionAnchor : function(oTrigger) {
    var sMode = this.get("selectionMode");
    var oAnchor = {};
    var oAnchorRecord, nAnchorRecordIndex, nAnchorTrIndex;

    // Cell mode
    if((sMode == "cellblock") || (sMode == "cellrange") || (sMode == "singlecell")) {
        // Validate anchor cell
        var oAnchorCell = this._oAnchorCell;
        if(!oAnchorCell) {
            if(oTrigger) {
                oAnchorCell = this._oAnchorCell = oTrigger.cell;
            }
            else {
                return null;
            }
        }
        oAnchorRecord = this._oAnchorCell.record;
        nAnchorRecordIndex = this._oRecordSet.getRecordIndex(oAnchorRecord);
        nAnchorTrIndex = this.getTrIndex(oAnchorRecord);
        // If anchor cell is not on this page...
        if(nAnchorTrIndex === null) {
            // ...set TR index equal to top TR
            if(nAnchorRecordIndex < this.getRecordIndex(this.getFirstTrEl())) {
                nAnchorTrIndex = 0;
            }
            // ...set TR index equal to bottom TR
            else {
                nAnchorTrIndex = this.getRecordIndex(this.getLastTrEl());
            }
        }

        oAnchor.record = oAnchorRecord;
        oAnchor.recordIndex = nAnchorRecordIndex;
        oAnchor.trIndex = nAnchorTrIndex;
        oAnchor.column = this._oAnchorCell.column;
        oAnchor.colKeyIndex = oAnchor.column.getKeyIndex();
        oAnchor.cell = oAnchorCell;
        return oAnchor;
    }
    // Row mode
    else {
        oAnchorRecord = this._oAnchorRecord;
        if(!oAnchorRecord) {
            if(oTrigger) {
                oAnchorRecord = this._oAnchorRecord = oTrigger.record;
            }
            else {
                return null;
            }
        }

        nAnchorRecordIndex = this.getRecordIndex(oAnchorRecord);
        nAnchorTrIndex = this.getTrIndex(oAnchorRecord);
        // If anchor row is not on this page...
        if(nAnchorTrIndex === null) {
            // ...set TR index equal to top TR
            if(nAnchorRecordIndex < this.getRecordIndex(this.getFirstTrEl())) {
                nAnchorTrIndex = 0;
            }
            // ...set TR index equal to bottom TR
            else {
                nAnchorTrIndex = this.getRecordIndex(this.getLastTrEl());
            }
        }

        oAnchor.record = oAnchorRecord;
        oAnchor.recordIndex = nAnchorRecordIndex;
        oAnchor.trIndex = nAnchorTrIndex;
        return oAnchor;
    }
},

/**
 * Determines selection behavior resulting from a mouse event when selection mode
 * is set to "standard".
 *
 * @method _handleStandardSelectionByMouse
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 * @private
 */
_handleStandardSelectionByMouse : function(oArgs) {
    var elTarget = oArgs.target;

    // Validate target row
    var elTargetRow = this.getTrEl(elTarget);
    if(elTargetRow) {
        var e = oArgs.event;
        var bSHIFT = e.shiftKey;
        var bCTRL = e.ctrlKey || ((navigator.userAgent.toLowerCase().indexOf("mac") != -1) && e.metaKey);

        var oTargetRecord = this.getRecord(elTargetRow);
        var nTargetRecordIndex = this._oRecordSet.getRecordIndex(oTargetRecord);

        var oAnchor = this._getSelectionAnchor();

        var i;

        // Both SHIFT and CTRL
        if(bSHIFT && bCTRL) {
            // Validate anchor
            if(oAnchor) {
                if(this.isSelected(oAnchor.record)) {
                    // Select all rows between anchor row and target row, including target row
                    if(oAnchor.recordIndex < nTargetRecordIndex) {
                        for(i=oAnchor.recordIndex+1; i<=nTargetRecordIndex; i++) {
                            if(!this.isSelected(i)) {
                                this.selectRow(i);
                            }
                        }
                    }
                    // Select all rows between target row and anchor row, including target row
                    else {
                        for(i=oAnchor.recordIndex-1; i>=nTargetRecordIndex; i--) {
                            if(!this.isSelected(i)) {
                                this.selectRow(i);
                            }
                        }
                    }
                }
                else {
                    // Unselect all rows between anchor row and target row
                    if(oAnchor.recordIndex < nTargetRecordIndex) {
                        for(i=oAnchor.recordIndex+1; i<=nTargetRecordIndex-1; i++) {
                            if(this.isSelected(i)) {
                                this.unselectRow(i);
                            }
                        }
                    }
                    // Unselect all rows between target row and anchor row
                    else {
                        for(i=nTargetRecordIndex+1; i<=oAnchor.recordIndex-1; i++) {
                            if(this.isSelected(i)) {
                                this.unselectRow(i);
                            }
                        }
                    }
                    // Select the target row
                    this.selectRow(oTargetRecord);
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorRecord = oTargetRecord;

                // Toggle selection of target
                if(this.isSelected(oTargetRecord)) {
                    this.unselectRow(oTargetRecord);
                }
                else {
                    this.selectRow(oTargetRecord);
                }
            }
        }
         // Only SHIFT
        else if(bSHIFT) {
            this.unselectAllRows();

            // Validate anchor
            if(oAnchor) {
                // Select all rows between anchor row and target row,
                // including the anchor row and target row
                if(oAnchor.recordIndex < nTargetRecordIndex) {
                    for(i=oAnchor.recordIndex; i<=nTargetRecordIndex; i++) {
                        this.selectRow(i);
                    }
                }
                // Select all rows between target row and anchor row,
                // including the target row and anchor row
                else {
                    for(i=oAnchor.recordIndex; i>=nTargetRecordIndex; i--) {
                        this.selectRow(i);
                    }
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorRecord = oTargetRecord;

                // Select target row only
                this.selectRow(oTargetRecord);
            }
        }
        // Only CTRL
        else if(bCTRL) {
            // Set anchor
            this._oAnchorRecord = oTargetRecord;

            // Toggle selection of target
            if(this.isSelected(oTargetRecord)) {
                this.unselectRow(oTargetRecord);
            }
            else {
                this.selectRow(oTargetRecord);
            }
        }
        // Neither SHIFT nor CTRL
        else {
            this._handleSingleSelectionByMouse(oArgs);
            return;
        }
    }
},

/**
 * Determines selection behavior resulting from a key event when selection mode
 * is set to "standard".
 *
 * @method _handleStandardSelectionByKey
 * @param e {HTMLEvent} Event object.
 * @private
 */
_handleStandardSelectionByKey : function(e) {
    var nKey = Ev.getCharCode(e);

    if((nKey == 38) || (nKey == 40)) {
        var bSHIFT = e.shiftKey;

        // Validate trigger
        var oTrigger = this._getSelectionTrigger();
        // Arrow selection only works if last selected row is on current page
        if(!oTrigger) {
            return null;
        }

        Ev.stopEvent(e);

        // Validate anchor
        var oAnchor = this._getSelectionAnchor(oTrigger);

        // Determine which direction we're going to
        if(bSHIFT) {
            // Selecting down away from anchor row
            if((nKey == 40) && (oAnchor.recordIndex <= oTrigger.trIndex)) {
                this.selectRow(this.getNextTrEl(oTrigger.el));
            }
            // Selecting up away from anchor row
            else if((nKey == 38) && (oAnchor.recordIndex >= oTrigger.trIndex)) {
                this.selectRow(this.getPreviousTrEl(oTrigger.el));
            }
            // Unselect trigger
            else {
                this.unselectRow(oTrigger.el);
            }
        }
        else {
            this._handleSingleSelectionByKey(e);
        }
    }
},

/**
 * Determines selection behavior resulting from a mouse event when selection mode
 * is set to "single".
 *
 * @method _handleSingleSelectionByMouse
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 * @private
 */
_handleSingleSelectionByMouse : function(oArgs) {
    var elTarget = oArgs.target;

    // Validate target row
    var elTargetRow = this.getTrEl(elTarget);
    if(elTargetRow) {
        var oTargetRecord = this.getRecord(elTargetRow);

        // Set anchor
        this._oAnchorRecord = oTargetRecord;

        // Select only target
        this.unselectAllRows();
        this.selectRow(oTargetRecord);
    }
},

/**
 * Determines selection behavior resulting from a key event when selection mode
 * is set to "single".
 *
 * @method _handleSingleSelectionByKey
 * @param e {HTMLEvent} Event object.
 * @private
 */
_handleSingleSelectionByKey : function(e) {
    var nKey = Ev.getCharCode(e);

    if((nKey == 38) || (nKey == 40)) {
        // Validate trigger
        var oTrigger = this._getSelectionTrigger();
        // Arrow selection only works if last selected row is on current page
        if(!oTrigger) {
            return null;
        }

        Ev.stopEvent(e);

        // Determine the new row to select
        var elNew;
        if(nKey == 38) { // arrow up
            elNew = this.getPreviousTrEl(oTrigger.el);

            // Validate new row
            if(elNew === null) {
                //TODO: wrap around to last tr on current page
                //elNew = this.getLastTrEl();

                //TODO: wrap back to last tr of previous page

                // Top row selection is sticky
                elNew = this.getFirstTrEl();
            }
        }
        else if(nKey == 40) { // arrow down
            elNew = this.getNextTrEl(oTrigger.el);

            // Validate new row
            if(elNew === null) {
                //TODO: wrap around to first tr on current page
                //elNew = this.getFirstTrEl();

                //TODO: wrap forward to first tr of previous page

                // Bottom row selection is sticky
                elNew = this.getLastTrEl();
            }
        }

        // Unselect all rows
        this.unselectAllRows();

        // Select the new row
        this.selectRow(elNew);

        // Set new anchor
        this._oAnchorRecord = this.getRecord(elNew);
    }
},

/**
 * Determines selection behavior resulting from a mouse event when selection mode
 * is set to "cellblock".
 *
 * @method _handleCellBlockSelectionByMouse
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 * @private
 */
_handleCellBlockSelectionByMouse : function(oArgs) {
    var elTarget = oArgs.target;

    // Validate target cell
    var elTargetCell = this.getTdEl(elTarget);
    if(elTargetCell) {
        var e = oArgs.event;
        var bSHIFT = e.shiftKey;
        var bCTRL = e.ctrlKey || ((navigator.userAgent.toLowerCase().indexOf("mac") != -1) && e.metaKey);

        var elTargetRow = this.getTrEl(elTargetCell);
        var nTargetTrIndex = this.getTrIndex(elTargetRow);
        var oTargetColumn = this.getColumn(elTargetCell);
        var nTargetColKeyIndex = oTargetColumn.getKeyIndex();
        var oTargetRecord = this.getRecord(elTargetRow);
        var nTargetRecordIndex = this._oRecordSet.getRecordIndex(oTargetRecord);
        var oTargetCell = {record:oTargetRecord, column:oTargetColumn};

        var oAnchor = this._getSelectionAnchor();

        var allRows = this.getTbodyEl().rows;
        var startIndex, endIndex, currentRow, i, j;

        // Both SHIFT and CTRL
        if(bSHIFT && bCTRL) {

            // Validate anchor
            if(oAnchor) {
                // Anchor is selected
                if(this.isSelected(oAnchor.cell)) {
                    // All cells are on the same row
                    if(oAnchor.recordIndex === nTargetRecordIndex) {
                        // Select all cells between anchor cell and target cell, including target cell
                        if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                            for(i=oAnchor.colKeyIndex+1; i<=nTargetColKeyIndex; i++) {
                                this.selectCell(elTargetRow.cells[i]);
                            }
                        }
                        // Select all cells between target cell and anchor cell, including target cell
                        else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                            for(i=nTargetColKeyIndex; i<oAnchor.colKeyIndex; i++) {
                                this.selectCell(elTargetRow.cells[i]);
                            }
                        }
                    }
                    // Anchor row is above target row
                    else if(oAnchor.recordIndex < nTargetRecordIndex) {
                        startIndex = Math.min(oAnchor.colKeyIndex, nTargetColKeyIndex);
                        endIndex = Math.max(oAnchor.colKeyIndex, nTargetColKeyIndex);

                        // Select all cells from startIndex to endIndex on rows between anchor row and target row
                        for(i=oAnchor.trIndex; i<=nTargetTrIndex; i++) {
                            for(j=startIndex; j<=endIndex; j++) {
                                this.selectCell(allRows[i].cells[j]);
                            }
                        }
                    }
                    // Anchor row is below target row
                    else {
                        startIndex = Math.min(oAnchor.trIndex, nTargetColKeyIndex);
                        endIndex = Math.max(oAnchor.trIndex, nTargetColKeyIndex);

                        // Select all cells from startIndex to endIndex on rows between target row and anchor row
                        for(i=oAnchor.trIndex; i>=nTargetTrIndex; i--) {
                            for(j=endIndex; j>=startIndex; j--) {
                                this.selectCell(allRows[i].cells[j]);
                            }
                        }
                    }
                }
                // Anchor cell is unselected
                else {
                    // All cells are on the same row
                    if(oAnchor.recordIndex === nTargetRecordIndex) {
                        // Unselect all cells between anchor cell and target cell
                        if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                            for(i=oAnchor.colKeyIndex+1; i<nTargetColKeyIndex; i++) {
                                this.unselectCell(elTargetRow.cells[i]);
                            }
                        }
                        // Select all cells between target cell and anchor cell
                        else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                            for(i=nTargetColKeyIndex+1; i<oAnchor.colKeyIndex; i++) {
                                this.unselectCell(elTargetRow.cells[i]);
                            }
                        }
                    }
                    // Anchor row is above target row
                    if(oAnchor.recordIndex < nTargetRecordIndex) {
                        // Unselect all cells from anchor cell to target cell
                        for(i=oAnchor.trIndex; i<=nTargetTrIndex; i++) {
                            currentRow = allRows[i];
                            for(j=0; j<currentRow.cells.length; j++) {
                                // This is the anchor row, only unselect cells after the anchor cell
                                if(currentRow.sectionRowIndex === oAnchor.trIndex) {
                                    if(j>oAnchor.colKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // This is the target row, only unelect cells before the target cell
                                else if(currentRow.sectionRowIndex === nTargetTrIndex) {
                                    if(j<nTargetColKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // Unselect all cells on this row
                                else {
                                    this.unselectCell(currentRow.cells[j]);
                                }
                            }
                        }
                    }
                    // Anchor row is below target row
                    else {
                        // Unselect all cells from target cell to anchor cell
                        for(i=nTargetTrIndex; i<=oAnchor.trIndex; i++) {
                            currentRow = allRows[i];
                            for(j=0; j<currentRow.cells.length; j++) {
                                // This is the target row, only unselect cells after the target cell
                                if(currentRow.sectionRowIndex == nTargetTrIndex) {
                                    if(j>nTargetColKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // This is the anchor row, only unselect cells before the anchor cell
                                else if(currentRow.sectionRowIndex == oAnchor.trIndex) {
                                    if(j<oAnchor.colKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // Unselect all cells on this row
                                else {
                                    this.unselectCell(currentRow.cells[j]);
                                }
                            }
                        }
                    }

                    // Select the target cell
                    this.selectCell(elTargetCell);
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorCell = oTargetCell;

                // Toggle selection of target
                if(this.isSelected(oTargetCell)) {
                    this.unselectCell(oTargetCell);
                }
                else {
                    this.selectCell(oTargetCell);
                }
            }

        }
         // Only SHIFT
        else if(bSHIFT) {
            this.unselectAllCells();

            // Validate anchor
            if(oAnchor) {
                // All cells are on the same row
                if(oAnchor.recordIndex === nTargetRecordIndex) {
                    // Select all cells between anchor cell and target cell,
                    // including the anchor cell and target cell
                    if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                        for(i=oAnchor.colKeyIndex; i<=nTargetColKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                    // Select all cells between target cell and anchor cell
                    // including the target cell and anchor cell
                    else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                        for(i=nTargetColKeyIndex; i<=oAnchor.colKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                }
                // Anchor row is above target row
                else if(oAnchor.recordIndex < nTargetRecordIndex) {
                    // Select the cellblock from anchor cell to target cell
                    // including the anchor cell and the target cell
                    startIndex = Math.min(oAnchor.colKeyIndex, nTargetColKeyIndex);
                    endIndex = Math.max(oAnchor.colKeyIndex, nTargetColKeyIndex);

                    for(i=oAnchor.trIndex; i<=nTargetTrIndex; i++) {
                        for(j=startIndex; j<=endIndex; j++) {
                            this.selectCell(allRows[i].cells[j]);
                        }
                    }
                }
                // Anchor row is below target row
                else {
                    // Select the cellblock from target cell to anchor cell
                    // including the target cell and the anchor cell
                    startIndex = Math.min(oAnchor.colKeyIndex, nTargetColKeyIndex);
                    endIndex = Math.max(oAnchor.colKeyIndex, nTargetColKeyIndex);

                    for(i=nTargetTrIndex; i<=oAnchor.trIndex; i++) {
                        for(j=startIndex; j<=endIndex; j++) {
                            this.selectCell(allRows[i].cells[j]);
                        }
                    }
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorCell = oTargetCell;

                // Select target only
                this.selectCell(oTargetCell);
            }
        }
        // Only CTRL
        else if(bCTRL) {

            // Set anchor
            this._oAnchorCell = oTargetCell;

            // Toggle selection of target
            if(this.isSelected(oTargetCell)) {
                this.unselectCell(oTargetCell);
            }
            else {
                this.selectCell(oTargetCell);
            }

        }
        // Neither SHIFT nor CTRL
        else {
            this._handleSingleCellSelectionByMouse(oArgs);
        }
    }
},

/**
 * Determines selection behavior resulting from a key event when selection mode
 * is set to "cellblock".
 *
 * @method _handleCellBlockSelectionByKey
 * @param e {HTMLEvent} Event object.
 * @private
 */
_handleCellBlockSelectionByKey : function(e) {
    var nKey = Ev.getCharCode(e);
    var bSHIFT = e.shiftKey;
    if((nKey == 9) || !bSHIFT) {
        this._handleSingleCellSelectionByKey(e);
        return;
    }

    if((nKey > 36) && (nKey < 41)) {
        // Validate trigger
        var oTrigger = this._getSelectionTrigger();
        // Arrow selection only works if last selected row is on current page
        if(!oTrigger) {
            return null;
        }

        Ev.stopEvent(e);

        // Validate anchor
        var oAnchor = this._getSelectionAnchor(oTrigger);

        var i, startIndex, endIndex, elNew, elNewRow;
        var allRows = this.getTbodyEl().rows;
        var elThisRow = oTrigger.el.parentNode;

        // Determine which direction we're going to

        if(nKey == 40) { // arrow down
            // Selecting away from anchor cell
            if(oAnchor.recordIndex <= oTrigger.recordIndex) {
                // Select the horiz block on the next row...
                // ...making sure there is room below the trigger row
                elNewRow = this.getNextTrEl(oTrigger.el);
                if(elNewRow) {
                    startIndex = oAnchor.colKeyIndex;
                    endIndex = oTrigger.colKeyIndex;
                    // ...going left
                    if(startIndex > endIndex) {
                        for(i=startIndex; i>=endIndex; i--) {
                            elNew = elNewRow.cells[i];
                            this.selectCell(elNew);
                        }
                    }
                    // ... going right
                    else {
                        for(i=startIndex; i<=endIndex; i++) {
                            elNew = elNewRow.cells[i];
                            this.selectCell(elNew);
                        }
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                startIndex = Math.min(oAnchor.colKeyIndex, oTrigger.colKeyIndex);
                endIndex = Math.max(oAnchor.colKeyIndex, oTrigger.colKeyIndex);
                // Unselect the horiz block on this row towards the next row
                for(i=startIndex; i<=endIndex; i++) {
                    this.unselectCell(elThisRow.cells[i]);
                }
            }
        }
        // Arrow up
        else if(nKey == 38) {
            // Selecting away from anchor cell
            if(oAnchor.recordIndex >= oTrigger.recordIndex) {
                // Select the horiz block on the previous row...
                // ...making sure there is room
                elNewRow = this.getPreviousTrEl(oTrigger.el);
                if(elNewRow) {
                    // Select in order from anchor to trigger...
                    startIndex = oAnchor.colKeyIndex;
                    endIndex = oTrigger.colKeyIndex;
                    // ...going left
                    if(startIndex > endIndex) {
                        for(i=startIndex; i>=endIndex; i--) {
                            elNew = elNewRow.cells[i];
                            this.selectCell(elNew);
                        }
                    }
                    // ... going right
                    else {
                        for(i=startIndex; i<=endIndex; i++) {
                            elNew = elNewRow.cells[i];
                            this.selectCell(elNew);
                        }
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                startIndex = Math.min(oAnchor.colKeyIndex, oTrigger.colKeyIndex);
                endIndex = Math.max(oAnchor.colKeyIndex, oTrigger.colKeyIndex);
                // Unselect the horiz block on this row towards the previous row
                for(i=startIndex; i<=endIndex; i++) {
                    this.unselectCell(elThisRow.cells[i]);
                }
            }
        }
        // Arrow right
        else if(nKey == 39) {
            // Selecting away from anchor cell
            if(oAnchor.colKeyIndex <= oTrigger.colKeyIndex) {
                // Select the next vert block to the right...
                // ...making sure there is room
                if(oTrigger.colKeyIndex < elThisRow.cells.length-1) {
                    // Select in order from anchor to trigger...
                    startIndex = oAnchor.trIndex;
                    endIndex = oTrigger.trIndex;
                    // ...going up
                    if(startIndex > endIndex) {
                        for(i=startIndex; i>=endIndex; i--) {
                            elNew = allRows[i].cells[oTrigger.colKeyIndex+1];
                            this.selectCell(elNew);
                        }
                    }
                    // ... going down
                    else {
                        for(i=startIndex; i<=endIndex; i++) {
                            elNew = allRows[i].cells[oTrigger.colKeyIndex+1];
                            this.selectCell(elNew);
                        }
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                // Unselect the vert block on this column towards the right
                startIndex = Math.min(oAnchor.trIndex, oTrigger.trIndex);
                endIndex = Math.max(oAnchor.trIndex, oTrigger.trIndex);
                for(i=startIndex; i<=endIndex; i++) {
                    this.unselectCell(allRows[i].cells[oTrigger.colKeyIndex]);
                }
            }
        }
        // Arrow left
        else if(nKey == 37) {
            // Selecting away from anchor cell
            if(oAnchor.colKeyIndex >= oTrigger.colKeyIndex) {
                //Select the previous vert block to the left
                if(oTrigger.colKeyIndex > 0) {
                    // Select in order from anchor to trigger...
                    startIndex = oAnchor.trIndex;
                    endIndex = oTrigger.trIndex;
                    // ...going up
                    if(startIndex > endIndex) {
                        for(i=startIndex; i>=endIndex; i--) {
                            elNew = allRows[i].cells[oTrigger.colKeyIndex-1];
                            this.selectCell(elNew);
                        }
                    }
                    // ... going down
                    else {
                        for(i=startIndex; i<=endIndex; i++) {
                            elNew = allRows[i].cells[oTrigger.colKeyIndex-1];
                            this.selectCell(elNew);
                        }
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                // Unselect the vert block on this column towards the left
                startIndex = Math.min(oAnchor.trIndex, oTrigger.trIndex);
                endIndex = Math.max(oAnchor.trIndex, oTrigger.trIndex);
                for(i=startIndex; i<=endIndex; i++) {
                    this.unselectCell(allRows[i].cells[oTrigger.colKeyIndex]);
                }
            }
        }
    }
},

/**
 * Determines selection behavior resulting from a mouse event when selection mode
 * is set to "cellrange".
 *
 * @method _handleCellRangeSelectionByMouse
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 * @private
 */
_handleCellRangeSelectionByMouse : function(oArgs) {
    var elTarget = oArgs.target;

    // Validate target cell
    var elTargetCell = this.getTdEl(elTarget);
    if(elTargetCell) {
        var e = oArgs.event;
        var bSHIFT = e.shiftKey;
        var bCTRL = e.ctrlKey || ((navigator.userAgent.toLowerCase().indexOf("mac") != -1) && e.metaKey);

        var elTargetRow = this.getTrEl(elTargetCell);
        var nTargetTrIndex = this.getTrIndex(elTargetRow);
        var oTargetColumn = this.getColumn(elTargetCell);
        var nTargetColKeyIndex = oTargetColumn.getKeyIndex();
        var oTargetRecord = this.getRecord(elTargetRow);
        var nTargetRecordIndex = this._oRecordSet.getRecordIndex(oTargetRecord);
        var oTargetCell = {record:oTargetRecord, column:oTargetColumn};

        var oAnchor = this._getSelectionAnchor();

        var allRows = this.getTbodyEl().rows;
        var currentRow, i, j;

        // Both SHIFT and CTRL
        if(bSHIFT && bCTRL) {

            // Validate anchor
            if(oAnchor) {
                // Anchor is selected
                if(this.isSelected(oAnchor.cell)) {
                    // All cells are on the same row
                    if(oAnchor.recordIndex === nTargetRecordIndex) {
                        // Select all cells between anchor cell and target cell, including target cell
                        if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                            for(i=oAnchor.colKeyIndex+1; i<=nTargetColKeyIndex; i++) {
                                this.selectCell(elTargetRow.cells[i]);
                            }
                        }
                        // Select all cells between target cell and anchor cell, including target cell
                        else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                            for(i=nTargetColKeyIndex; i<oAnchor.colKeyIndex; i++) {
                                this.selectCell(elTargetRow.cells[i]);
                            }
                        }
                    }
                    // Anchor row is above target row
                    else if(oAnchor.recordIndex < nTargetRecordIndex) {
                        // Select all cells on anchor row from anchor cell to the end of the row
                        for(i=oAnchor.colKeyIndex+1; i<elTargetRow.cells.length; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }

                        // Select all cells on all rows between anchor row and target row
                        for(i=oAnchor.trIndex+1; i<nTargetTrIndex; i++) {
                            for(j=0; j<allRows[i].cells.length; j++){
                                this.selectCell(allRows[i].cells[j]);
                            }
                        }

                        // Select all cells on target row from first cell to the target cell
                        for(i=0; i<=nTargetColKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                    // Anchor row is below target row
                    else {
                        // Select all cells on target row from target cell to the end of the row
                        for(i=nTargetColKeyIndex; i<elTargetRow.cells.length; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }

                        // Select all cells on all rows between target row and anchor row
                        for(i=nTargetTrIndex+1; i<oAnchor.trIndex; i++) {
                            for(j=0; j<allRows[i].cells.length; j++){
                                this.selectCell(allRows[i].cells[j]);
                            }
                        }

                        // Select all cells on anchor row from first cell to the anchor cell
                        for(i=0; i<oAnchor.colKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                }
                // Anchor cell is unselected
                else {
                    // All cells are on the same row
                    if(oAnchor.recordIndex === nTargetRecordIndex) {
                        // Unselect all cells between anchor cell and target cell
                        if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                            for(i=oAnchor.colKeyIndex+1; i<nTargetColKeyIndex; i++) {
                                this.unselectCell(elTargetRow.cells[i]);
                            }
                        }
                        // Select all cells between target cell and anchor cell
                        else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                            for(i=nTargetColKeyIndex+1; i<oAnchor.colKeyIndex; i++) {
                                this.unselectCell(elTargetRow.cells[i]);
                            }
                        }
                    }
                    // Anchor row is above target row
                    if(oAnchor.recordIndex < nTargetRecordIndex) {
                        // Unselect all cells from anchor cell to target cell
                        for(i=oAnchor.trIndex; i<=nTargetTrIndex; i++) {
                            currentRow = allRows[i];
                            for(j=0; j<currentRow.cells.length; j++) {
                                // This is the anchor row, only unselect cells after the anchor cell
                                if(currentRow.sectionRowIndex === oAnchor.trIndex) {
                                    if(j>oAnchor.colKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // This is the target row, only unelect cells before the target cell
                                else if(currentRow.sectionRowIndex === nTargetTrIndex) {
                                    if(j<nTargetColKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // Unselect all cells on this row
                                else {
                                    this.unselectCell(currentRow.cells[j]);
                                }
                            }
                        }
                    }
                    // Anchor row is below target row
                    else {
                        // Unselect all cells from target cell to anchor cell
                        for(i=nTargetTrIndex; i<=oAnchor.trIndex; i++) {
                            currentRow = allRows[i];
                            for(j=0; j<currentRow.cells.length; j++) {
                                // This is the target row, only unselect cells after the target cell
                                if(currentRow.sectionRowIndex == nTargetTrIndex) {
                                    if(j>nTargetColKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // This is the anchor row, only unselect cells before the anchor cell
                                else if(currentRow.sectionRowIndex == oAnchor.trIndex) {
                                    if(j<oAnchor.colKeyIndex) {
                                        this.unselectCell(currentRow.cells[j]);
                                    }
                                }
                                // Unselect all cells on this row
                                else {
                                    this.unselectCell(currentRow.cells[j]);
                                }
                            }
                        }
                    }

                    // Select the target cell
                    this.selectCell(elTargetCell);
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorCell = oTargetCell;

                // Toggle selection of target
                if(this.isSelected(oTargetCell)) {
                    this.unselectCell(oTargetCell);
                }
                else {
                    this.selectCell(oTargetCell);
                }
            }
        }
         // Only SHIFT
        else if(bSHIFT) {

            this.unselectAllCells();

            // Validate anchor
            if(oAnchor) {
                // All cells are on the same row
                if(oAnchor.recordIndex === nTargetRecordIndex) {
                    // Select all cells between anchor cell and target cell,
                    // including the anchor cell and target cell
                    if(oAnchor.colKeyIndex < nTargetColKeyIndex) {
                        for(i=oAnchor.colKeyIndex; i<=nTargetColKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                    // Select all cells between target cell and anchor cell
                    // including the target cell and anchor cell
                    else if(nTargetColKeyIndex < oAnchor.colKeyIndex) {
                        for(i=nTargetColKeyIndex; i<=oAnchor.colKeyIndex; i++) {
                            this.selectCell(elTargetRow.cells[i]);
                        }
                    }
                }
                // Anchor row is above target row
                else if(oAnchor.recordIndex < nTargetRecordIndex) {
                    // Select all cells from anchor cell to target cell
                    // including the anchor cell and target cell
                    for(i=oAnchor.trIndex; i<=nTargetTrIndex; i++) {
                        currentRow = allRows[i];
                        for(j=0; j<currentRow.cells.length; j++) {
                            // This is the anchor row, only select the anchor cell and after
                            if(currentRow.sectionRowIndex == oAnchor.trIndex) {
                                if(j>=oAnchor.colKeyIndex) {
                                    this.selectCell(currentRow.cells[j]);
                                }
                            }
                            // This is the target row, only select the target cell and before
                            else if(currentRow.sectionRowIndex == nTargetTrIndex) {
                                if(j<=nTargetColKeyIndex) {
                                    this.selectCell(currentRow.cells[j]);
                                }
                            }
                            // Select all cells on this row
                            else {
                                this.selectCell(currentRow.cells[j]);
                            }
                        }
                    }
                }
                // Anchor row is below target row
                else {
                    // Select all cells from target cell to anchor cell,
                    // including the target cell and anchor cell
                    for(i=nTargetTrIndex; i<=oAnchor.trIndex; i++) {
                        currentRow = allRows[i];
                        for(j=0; j<currentRow.cells.length; j++) {
                            // This is the target row, only select the target cell and after
                            if(currentRow.sectionRowIndex == nTargetTrIndex) {
                                if(j>=nTargetColKeyIndex) {
                                    this.selectCell(currentRow.cells[j]);
                                }
                            }
                            // This is the anchor row, only select the anchor cell and before
                            else if(currentRow.sectionRowIndex == oAnchor.trIndex) {
                                if(j<=oAnchor.colKeyIndex) {
                                    this.selectCell(currentRow.cells[j]);
                                }
                            }
                            // Select all cells on this row
                            else {
                                this.selectCell(currentRow.cells[j]);
                            }
                        }
                    }
                }
            }
            // Invalid anchor
            else {
                // Set anchor
                this._oAnchorCell = oTargetCell;

                // Select target only
                this.selectCell(oTargetCell);
            }


        }
        // Only CTRL
        else if(bCTRL) {

            // Set anchor
            this._oAnchorCell = oTargetCell;

            // Toggle selection of target
            if(this.isSelected(oTargetCell)) {
                this.unselectCell(oTargetCell);
            }
            else {
                this.selectCell(oTargetCell);
            }

        }
        // Neither SHIFT nor CTRL
        else {
            this._handleSingleCellSelectionByMouse(oArgs);
        }
    }
},

/**
 * Determines selection behavior resulting from a key event when selection mode
 * is set to "cellrange".
 *
 * @method _handleCellRangeSelectionByKey
 * @param e {HTMLEvent} Event object.
 * @private
 */
_handleCellRangeSelectionByKey : function(e) {
    var nKey = Ev.getCharCode(e);
    var bSHIFT = e.shiftKey;
    if((nKey == 9) || !bSHIFT) {
        this._handleSingleCellSelectionByKey(e);
        return;
    }

    if((nKey > 36) && (nKey < 41)) {
        // Validate trigger
        var oTrigger = this._getSelectionTrigger();
        // Arrow selection only works if last selected row is on current page
        if(!oTrigger) {
            return null;
        }

        Ev.stopEvent(e);

        // Validate anchor
        var oAnchor = this._getSelectionAnchor(oTrigger);

        var i, elNewRow, elNew;
        var allRows = this.getTbodyEl().rows;
        var elThisRow = oTrigger.el.parentNode;

        // Arrow down
        if(nKey == 40) {
            elNewRow = this.getNextTrEl(oTrigger.el);

            // Selecting away from anchor cell
            if(oAnchor.recordIndex <= oTrigger.recordIndex) {
                // Select all cells to the end of this row
                for(i=oTrigger.colKeyIndex+1; i<elThisRow.cells.length; i++){
                    elNew = elThisRow.cells[i];
                    this.selectCell(elNew);
                }

                // Select some of the cells on the next row down
                if(elNewRow) {
                    for(i=0; i<=oTrigger.colKeyIndex; i++){
                        elNew = elNewRow.cells[i];
                        this.selectCell(elNew);
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                // Unselect all cells to the end of this row
                for(i=oTrigger.colKeyIndex; i<elThisRow.cells.length; i++){
                    this.unselectCell(elThisRow.cells[i]);
                }

                // Unselect some of the cells on the next row down
                if(elNewRow) {
                    for(i=0; i<oTrigger.colKeyIndex; i++){
                        this.unselectCell(elNewRow.cells[i]);
                    }
                }
            }
        }
        // Arrow up
        else if(nKey == 38) {
            elNewRow = this.getPreviousTrEl(oTrigger.el);

            // Selecting away from anchor cell
            if(oAnchor.recordIndex >= oTrigger.recordIndex) {
                // Select all the cells to the beginning of this row
                for(i=oTrigger.colKeyIndex-1; i>-1; i--){
                    elNew = elThisRow.cells[i];
                    this.selectCell(elNew);
                }

                // Select some of the cells from the end of the previous row
                if(elNewRow) {
                    for(i=elThisRow.cells.length-1; i>=oTrigger.colKeyIndex; i--){
                        elNew = elNewRow.cells[i];
                        this.selectCell(elNew);
                    }
                }
            }
            // Unselecting towards anchor cell
            else {
                // Unselect all the cells to the beginning of this row
                for(i=oTrigger.colKeyIndex; i>-1; i--){
                    this.unselectCell(elThisRow.cells[i]);
                }

                // Unselect some of the cells from the end of the previous row
                if(elNewRow) {
                    for(i=elThisRow.cells.length-1; i>oTrigger.colKeyIndex; i--){
                        this.unselectCell(elNewRow.cells[i]);
                    }
                }
            }
        }
        // Arrow right
        else if(nKey == 39) {
            elNewRow = this.getNextTrEl(oTrigger.el);

            // Selecting away from anchor cell
            if(oAnchor.recordIndex < oTrigger.recordIndex) {
                // Select the next cell to the right
                if(oTrigger.colKeyIndex < elThisRow.cells.length-1) {
                    elNew = elThisRow.cells[oTrigger.colKeyIndex+1];
                    this.selectCell(elNew);
                }
                // Select the first cell of the next row
                else if(elNewRow) {
                    elNew = elNewRow.cells[0];
                    this.selectCell(elNew);
                }
            }
            // Unselecting towards anchor cell
            else if(oAnchor.recordIndex > oTrigger.recordIndex) {
                this.unselectCell(elThisRow.cells[oTrigger.colKeyIndex]);

                // Unselect this cell towards the right
                if(oTrigger.colKeyIndex < elThisRow.cells.length-1) {
                }
                // Unselect this cells towards the first cell of the next row
                else {
                }
            }
            // Anchor is on this row
            else {
                // Selecting away from anchor
                if(oAnchor.colKeyIndex <= oTrigger.colKeyIndex) {
                    // Select the next cell to the right
                    if(oTrigger.colKeyIndex < elThisRow.cells.length-1) {
                        elNew = elThisRow.cells[oTrigger.colKeyIndex+1];
                        this.selectCell(elNew);
                    }
                    // Select the first cell on the next row
                    else if(oTrigger.trIndex < allRows.length-1){
                        elNew = elNewRow.cells[0];
                        this.selectCell(elNew);
                    }
                }
                // Unselecting towards anchor
                else {
                    // Unselect this cell towards the right
                    this.unselectCell(elThisRow.cells[oTrigger.colKeyIndex]);
                }
            }
        }
        // Arrow left
        else if(nKey == 37) {
            elNewRow = this.getPreviousTrEl(oTrigger.el);

            // Unselecting towards the anchor
            if(oAnchor.recordIndex < oTrigger.recordIndex) {
                this.unselectCell(elThisRow.cells[oTrigger.colKeyIndex]);

                // Unselect this cell towards the left
                if(oTrigger.colKeyIndex > 0) {
                }
                // Unselect this cell towards the last cell of the previous row
                else {
                }
            }
            // Selecting towards the anchor
            else if(oAnchor.recordIndex > oTrigger.recordIndex) {
                // Select the next cell to the left
                if(oTrigger.colKeyIndex > 0) {
                    elNew = elThisRow.cells[oTrigger.colKeyIndex-1];
                    this.selectCell(elNew);
                }
                // Select the last cell of the previous row
                else if(oTrigger.trIndex > 0){
                    elNew = elNewRow.cells[elNewRow.cells.length-1];
                    this.selectCell(elNew);
                }
            }
            // Anchor is on this row
            else {
                // Selecting away from anchor cell
                if(oAnchor.colKeyIndex >= oTrigger.colKeyIndex) {
                    // Select the next cell to the left
                    if(oTrigger.colKeyIndex > 0) {
                        elNew = elThisRow.cells[oTrigger.colKeyIndex-1];
                        this.selectCell(elNew);
                    }
                    // Select the last cell of the previous row
                    else if(oTrigger.trIndex > 0){
                        elNew = elNewRow.cells[elNewRow.cells.length-1];
                        this.selectCell(elNew);
                    }
                }
                // Unselecting towards anchor cell
                else {
                    this.unselectCell(elThisRow.cells[oTrigger.colKeyIndex]);

                    // Unselect this cell towards the left
                    if(oTrigger.colKeyIndex > 0) {
                    }
                    // Unselect this cell towards the last cell of the previous row
                    else {
                    }
                }
            }
        }
    }
},

/**
 * Determines selection behavior resulting from a mouse event when selection mode
 * is set to "singlecell".
 *
 * @method _handleSingleCellSelectionByMouse
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 * @private
 */
_handleSingleCellSelectionByMouse : function(oArgs) {
    var elTarget = oArgs.target;

    // Validate target cell
    var elTargetCell = this.getTdEl(elTarget);
    if(elTargetCell) {
        var elTargetRow = this.getTrEl(elTargetCell);
        var oTargetRecord = this.getRecord(elTargetRow);
        var oTargetColumn = this.getColumn(elTargetCell);
        var oTargetCell = {record:oTargetRecord, column:oTargetColumn};

        // Set anchor
        this._oAnchorCell = oTargetCell;

        // Select only target
        this.unselectAllCells();
        this.selectCell(oTargetCell);
    }
},

/**
 * Determines selection behavior resulting from a key event when selection mode
 * is set to "singlecell".
 *
 * @method _handleSingleCellSelectionByKey
 * @param e {HTMLEvent} Event object.
 * @private
 */
_handleSingleCellSelectionByKey : function(e) {
    var nKey = Ev.getCharCode(e);
    if((nKey == 9) || ((nKey > 36) && (nKey < 41))) {
        var bSHIFT = e.shiftKey;

        // Validate trigger
        var oTrigger = this._getSelectionTrigger();
        // Arrow selection only works if last selected row is on current page
        if(!oTrigger) {
            return null;
        }

        // Determine the new cell to select
        var elNew;
        if(nKey == 40) { // Arrow down
            elNew = this.getBelowTdEl(oTrigger.el);

            // Validate new cell
            if(elNew === null) {
                //TODO: wrap around to first tr on current page

                //TODO: wrap forward to first tr of next page

                // Bottom selection is sticky
                elNew = oTrigger.el;
            }
        }
        else if(nKey == 38) { // Arrow up
            elNew = this.getAboveTdEl(oTrigger.el);

            // Validate new cell
            if(elNew === null) {
                //TODO: wrap around to last tr on current page

                //TODO: wrap back to last tr of previous page

                // Top selection is sticky
                elNew = oTrigger.el;
            }
        }
        else if((nKey == 39) || (!bSHIFT && (nKey == 9))) { // Arrow right or tab
            elNew = this.getNextTdEl(oTrigger.el);

            // Validate new cell
            if(elNew === null) {
                //TODO: wrap around to first td on current page

                //TODO: wrap forward to first td of next page

                // Top-left selection is sticky, and release TAB focus
                //elNew = oTrigger.el;
                return;
            }
        }
        else if((nKey == 37) || (bSHIFT && (nKey == 9))) { // Arrow left or shift-tab
            elNew = this.getPreviousTdEl(oTrigger.el);

            // Validate new cell
            if(elNew === null) {
                //TODO: wrap around to last td on current page

                //TODO: wrap back to last td of previous page

                // Bottom-right selection is sticky, and release TAB focus
                //elNew = oTrigger.el;
                return;
            }
        }

        Ev.stopEvent(e);
        
        // Unselect all cells
        this.unselectAllCells();

        // Select the new cell
        this.selectCell(elNew);

        // Set new anchor
        this._oAnchorCell = {record:this.getRecord(elNew), column:this.getColumn(elNew)};
    }
},

/**
 * Returns array of selected TR elements on the page.
 *
 * @method getSelectedTrEls
 * @return {HTMLElement[]} Array of selected TR elements.
 */
getSelectedTrEls : function() {
    return Dom.getElementsByClassName(DT.CLASS_SELECTED,"tr",this._elTbody);
},

/**
 * Sets given row to the selected state.
 *
 * @method selectRow
 * @param row {HTMLElement | String | YAHOO.widget.Record | Number} HTML element
 * reference or ID string, Record instance, or RecordSet position index.
 */
selectRow : function(row) {
    var oRecord, elRow;

    if(row instanceof YAHOO.widget.Record) {
        oRecord = this._oRecordSet.getRecord(row);
        elRow = this.getTrEl(oRecord);
    }
    else if(lang.isNumber(row)) {
        oRecord = this.getRecord(row);
        elRow = this.getTrEl(oRecord);
    }
    else {
        elRow = this.getTrEl(row);
        oRecord = this.getRecord(elRow);
    }

    if(oRecord) {
        // Update selection trackers
        var tracker = this._aSelections || [];
        var sRecordId = oRecord.getId();
        var index = -1;

        // Remove if already there:
        // Use Array.indexOf if available...
        /*if(tracker.indexOf && (tracker.indexOf(sRecordId) >  -1)) {
            tracker.splice(tracker.indexOf(sRecordId),1);
        }*/
        if(tracker.indexOf) {
            index = tracker.indexOf(sRecordId);
            
        }
        // ...or do it the old-fashioned way
        else {
            for(var j=tracker.length-1; j>-1; j--) {
                if(tracker[j] === sRecordId){
                    index = j;
                    break;
                }
            }
        }
        if(index > -1) {
            tracker.splice(index,1);
        }
        
        // Add to the end
        tracker.push(sRecordId);
        this._aSelections = tracker;

        // Update trackers
        if(!this._oAnchorRecord) {
            this._oAnchorRecord = oRecord;
        }

        // Update UI
        if(elRow) {
            Dom.addClass(elRow, DT.CLASS_SELECTED);
        }

        this.fireEvent("rowSelectEvent", {record:oRecord, el:elRow});
        YAHOO.log("Selected " + elRow, "info", this.toString());
    }
    else {
        YAHOO.log("Could not select row " + row, "warn", this.toString());
    }
},

/**
 * Sets given row to the selected state.
 *
 * @method unselectRow
 * @param row {HTMLElement | String | YAHOO.widget.Record | Number} HTML element
 * reference or ID string, Record instance, or RecordSet position index.
 */
unselectRow : function(row) {
    var elRow = this.getTrEl(row);

    var oRecord;
    if(row instanceof YAHOO.widget.Record) {
        oRecord = this._oRecordSet.getRecord(row);
    }
    else if(lang.isNumber(row)) {
        oRecord = this.getRecord(row);
    }
    else {
        oRecord = this.getRecord(elRow);
    }

    if(oRecord) {
        // Update selection trackers
        var tracker = this._aSelections || [];
        var sRecordId = oRecord.getId();
        var index = -1;

        // Remove if found
        var bFound = false;

        // Use Array.indexOf if available...
        if(tracker.indexOf) {
            index = tracker.indexOf(sRecordId);
        }
        // ...or do it the old-fashioned way
        else {
            for(var j=tracker.length-1; j>-1; j--) {
                if(tracker[j] === sRecordId){
                    index = j;
                    break;
                }
            }
        }
        if(index > -1) {
            tracker.splice(index,1);
        }

        if(bFound) {
            // Update tracker
            this._aSelections = tracker;

            // Update the UI
            Dom.removeClass(elRow, DT.CLASS_SELECTED);

            this.fireEvent("rowUnselectEvent", {record:oRecord, el:elRow});
            YAHOO.log("Unselected " + elRow, "info", this.toString());

            return;
        }

        // Update the UI
        Dom.removeClass(elRow, DT.CLASS_SELECTED);

        this.fireEvent("rowUnselectEvent", {record:oRecord, el:elRow});
        YAHOO.log("Unselected " + elRow, "info", this.toString());
    }
    YAHOO.log("Could not unselect row " + row, "warn", this.toString());
},

/**
 * Clears out all row selections.
 *
 * @method unselectAllRows
 */
unselectAllRows : function() {
    // Remove all rows from tracker
    var tracker = this._aSelections || [];
    for(var j=tracker.length-1; j>-1; j--) {
       if(lang.isString(tracker[j])){
            tracker.splice(j,1);
        }
    }

    // Update tracker
    this._aSelections = tracker;

    // Update UI
    this._unselectAllTrEls();

    //TODO: send an array of [{el:el,record:record}]
    //TODO: or convert this to an unselectRows method
    //TODO: that takes an array of rows or unselects all if none given
    this.fireEvent("unselectAllRowsEvent");
    YAHOO.log("Unselected all rows", "info", this.toString());
},

/**
 * Convenience method to remove the class DT.CLASS_SELECTED
 * from all TD elements in the internal tracker.
 *
 * @method _unselectAllTdEls
 * @private
 */
_unselectAllTdEls : function() {
    var selectedCells = Dom.getElementsByClassName(DT.CLASS_SELECTED,"td",this._elTbody);
    Dom.removeClass(selectedCells, DT.CLASS_SELECTED);
},

/**
 * Returns array of selected TD elements on the page.
 *
 * @method getSelectedTdEls
 * @return {HTMLElement[]} Array of selected TD elements.
 */
getSelectedTdEls : function() {
    return Dom.getElementsByClassName(DT.CLASS_SELECTED,"td",this._elTbody);
},

/**
 * Sets given cell to the selected state.
 *
 * @method selectCell
 * @param cell {HTMLElement | String} DOM element reference or ID string
 * to DataTable page element or RecordSet index.
 */
selectCell : function(cell) {
/*TODO:
accept {record}
*/
    var elCell = this.getTdEl(cell);

    if(elCell) {
        var oRecord = this.getRecord(elCell);
        var sColumnId = elCell.yuiColumnId;

        if(oRecord && sColumnId) {
            // Get Record ID
            var tracker = this._aSelections || [];
            var sRecordId = oRecord.getId();

            // Remove if there
            for(var j=tracker.length-1; j>-1; j--) {
               if((tracker[j].recordId === sRecordId) && (tracker[j].columnId === sColumnId)){
                    tracker.splice(j,1);
                    break;
                }
            }

            // Add to the end
            tracker.push({recordId:sRecordId, columnId:sColumnId});

            // Update trackers
            this._aSelections = tracker;
            if(!this._oAnchorCell) {
                this._oAnchorCell = {record:oRecord, column:this.getColumnById(sColumnId)};
            }

            // Update the UI
            Dom.addClass(elCell, DT.CLASS_SELECTED);

            this.fireEvent("cellSelectEvent", {record:oRecord, column:this.getColumnById(sColumnId), key: elCell.yuiColumnKey, el:elCell});
            YAHOO.log("Selected " + elCell, "info", this.toString());
            return;
        }
    }
    YAHOO.log("Could not select cell " + cell, "warn", this.toString());
},

/**
 * Sets given cell to the unselected state.
 *
 * @method unselectCell
 * @param cell {HTMLElement | String} DOM element reference or ID string
 * to DataTable page element or RecordSet index.
 */
unselectCell : function(cell) {
    var elCell = this.getTdEl(cell);

    if(elCell) {
        var oRecord = this.getRecord(elCell);
        var sColumnId = elCell.yuiColumnId;

        if(oRecord && sColumnId) {
            // Get Record ID
            var tracker = this._aSelections || [];
            var id = oRecord.getId();

            // Is it selected?
            for(var j=tracker.length-1; j>-1; j--) {
                if((tracker[j].recordId === id) && (tracker[j].columnId === sColumnId)){
                    // Remove from tracker
                    tracker.splice(j,1);

                    // Update tracker
                    this._aSelections = tracker;

                    // Update the UI
                    Dom.removeClass(elCell, DT.CLASS_SELECTED);

                    this.fireEvent("cellUnselectEvent", {record:oRecord, column: this.getColumnById(sColumnId), key:elCell.yuiColumnKey, el:elCell});
                    YAHOO.log("Unselected " + elCell, "info", this.toString());
                    return;
                }
            }
        }
    }
    YAHOO.log("Could not unselect cell " + cell, "warn", this.toString());
},

/**
 * Clears out all cell selections.
 *
 * @method unselectAllCells
 */
unselectAllCells: function() {
    // Remove all cells from tracker
    var tracker = this._aSelections || [];
    for(var j=tracker.length-1; j>-1; j--) {
       if(tracker[j].constructor == Object){
            tracker.splice(j,1);
        }
    }

    // Update tracker
    this._aSelections = tracker;

    // Update UI
    this._unselectAllTdEls();

    //TODO: send data
    //TODO: or fire individual cellUnselectEvent
    this.fireEvent("unselectAllCellsEvent");
    YAHOO.log("Unselected all cells", "info", this.toString());
},

/**
 * Returns true if given item is selected, false otherwise.
 *
 * @method isSelected
 * @param o {String | HTMLElement | YAHOO.widget.Record | Number
 * {record:YAHOO.widget.Record, column:YAHOO.widget.Column} } TR or TD element by
 * reference or ID string, a Record instance, a RecordSet position index,
 * or an object literal representation
 * of a cell.
 * @return {Boolean} True if item is selected.
 */
isSelected : function(o) {
    var oRecord, sRecordId, j;

    var el = this.getTrEl(o) || this.getTdEl(o);
    if(el) {
        return Dom.hasClass(el,DT.CLASS_SELECTED);
    }
    else {
        var tracker = this._aSelections;
        if(tracker && tracker.length > 0) {
            // Looking for a Record?
            if(o instanceof YAHOO.widget.Record) {
                oRecord = o;
            }
            else if(lang.isNumber(o)) {
                oRecord = this.getRecord(o);
            }
            if(oRecord) {
                sRecordId = oRecord.getId();

                // Is it there?
                // Use Array.indexOf if available...
                if(tracker.indexOf) {
                    if(tracker.indexOf(sRecordId) >  -1) {
                        return true;
                    }
                }
                // ...or do it the old-fashioned way
                else {
                    for(j=tracker.length-1; j>-1; j--) {
                       if(tracker[j] === sRecordId){
                        return true;
                       }
                    }
                }
            }
            // Looking for a cell
            else if(o.record && o.column){
                sRecordId = o.record.getId();
                var sColumnId = o.column.getId();

                for(j=tracker.length-1; j>-1; j--) {
                    if((tracker[j].recordId === sRecordId) && (tracker[j].columnId === sColumnId)){
                        return true;
                    }
                }
            }
        }
    }
    return false;
},

/**
 * Returns selected rows as an array of Record IDs.
 *
 * @method getSelectedRows
 * @return {String[]} Array of selected rows by Record ID.
 */
getSelectedRows : function() {
    var aSelectedRows = [];
    var tracker = this._aSelections || [];
    for(var j=0; j<tracker.length; j++) {
       if(lang.isString(tracker[j])){
            aSelectedRows.push(tracker[j]);
        }
    }
    return aSelectedRows;
},

/**
 * Returns selected cells as an array of object literals:
 *     {recordId:sRecordId, columnId:sColumnId}.
 *
 * @method getSelectedCells
 * @return {Object[]} Array of selected cells by Record ID and Column ID.
 */
getSelectedCells : function() {
    var aSelectedCells = [];
    var tracker = this._aSelections || [];
    for(var j=0; j<tracker.length; j++) {
       if(tracker[j] && (tracker[j].constructor == Object)){
            aSelectedCells.push(tracker[j]);
        }
    }
    return aSelectedCells;
},

/**
 * Returns last selected Record ID.
 *
 * @method getLastSelectedRecord
 * @return {String} Record ID of last selected row.
 */
getLastSelectedRecord : function() {
    var tracker = this._aSelections;
    if(tracker && tracker.length > 0) {
        for(var i=tracker.length-1; i>-1; i--) {
           if(lang.isString(tracker[i])){
                return tracker[i];
            }
        }
    }
},

/**
 * Returns last selected cell as an object literal:
 *     {recordId:sRecordId, columnId:sColumnId}.
 *
 * @method getLastSelectedCell
 * @return {Object} Object literal representation of a cell.
 */
getLastSelectedCell : function() {
    var tracker = this._aSelections;
    if(tracker && tracker.length > 0) {
        for(var i=tracker.length-1; i>-1; i--) {
           if(tracker[i].recordId && tracker[i].columnId){
                return tracker[i];
            }
        }
    }
},

/**
 * Assigns the class DT.CLASS_HIGHLIGHTED to the given row.
 *
 * @method highlightRow
 * @param row {HTMLElement | String} DOM element reference or ID string.
 */
highlightRow : function(row) {
    var elRow = this.getTrEl(row);

    if(elRow) {
        // Make sure previous row is unhighlighted
/*        if(this._sLastHighlightedTrElId) {
            Dom.removeClass(this._sLastHighlightedTrElId,DT.CLASS_HIGHLIGHTED);
        }*/
        var oRecord = this.getRecord(elRow);
        Dom.addClass(elRow,DT.CLASS_HIGHLIGHTED);
        //this._sLastHighlightedTrElId = elRow.id;
        this.fireEvent("rowHighlightEvent", {record:oRecord, el:elRow});
        YAHOO.log("Highlighted " + elRow, "info", this.toString());
        return;
    }
    YAHOO.log("Could not highlight row " + row, "warn", this.toString());
},

/**
 * Removes the class DT.CLASS_HIGHLIGHTED from the given row.
 *
 * @method unhighlightRow
 * @param row {HTMLElement | String} DOM element reference or ID string.
 */
unhighlightRow : function(row) {
    var elRow = this.getTrEl(row);

    if(elRow) {
        var oRecord = this.getRecord(elRow);
        Dom.removeClass(elRow,DT.CLASS_HIGHLIGHTED);
        this.fireEvent("rowUnhighlightEvent", {record:oRecord, el:elRow});
        YAHOO.log("Unhighlighted " + elRow, "info", this.toString());
        return;
    }
    YAHOO.log("Could not unhighlight row " + row, "warn", this.toString());
},

/**
 * Assigns the class DT.CLASS_HIGHLIGHTED to the given cell.
 *
 * @method highlightCell
 * @param cell {HTMLElement | String} DOM element reference or ID string.
 */
highlightCell : function(cell) {
    var elCell = this.getTdEl(cell);

    if(elCell) {
        // Make sure previous cell is unhighlighted
        /*if(this._sLastHighlightedTdElId) {
            Dom.removeClass(this._sLastHighlightedTdElId,DT.CLASS_HIGHLIGHTED);
        }*/

        var oRecord = this.getRecord(elCell);
        var sColumnId = elCell.yuiColumnId;
        Dom.addClass(elCell,DT.CLASS_HIGHLIGHTED);
        //this._sLastHighlightedTdElId = elCell.id;
        this.fireEvent("cellHighlightEvent", {record:oRecord, column:this.getColumnById(sColumnId), key:elCell.yuiColumnKey, el:elCell});
        YAHOO.log("Highlighted " + elCell, "info", this.toString());
        return;
    }
    YAHOO.log("Could not highlight cell " + cell, "warn", this.toString());
},

/**
 * Removes the class DT.CLASS_HIGHLIGHTED from the given cell.
 *
 * @method unhighlightCell
 * @param cell {HTMLElement | String} DOM element reference or ID string.
 */
unhighlightCell : function(cell) {
    var elCell = this.getTdEl(cell);

    if(elCell) {
        var oRecord = this.getRecord(elCell);
        Dom.removeClass(elCell,DT.CLASS_HIGHLIGHTED);
        this.fireEvent("cellUnhighlightEvent", {record:oRecord, column:this.getColumnById(elCell.yuiColumnId), key:elCell.yuiColumnKey, el:elCell});
        YAHOO.log("Unhighlighted " + elCell, "info", this.toString());
        return;
    }
    YAHOO.log("Could not unhighlight cell " + cell, "warn", this.toString());
},













































// INLINE EDITING

/**
 * Shows Cell Editor for given cell.
 *
 * @method showCellEditor
 * @param elCell {HTMLElement | String} Cell to edit.
 * @param oRecord {YAHOO.widget.Record} (Optional) Record instance.
 * @param oColumn {YAHOO.widget.Column} (Optional) Column instance.
 */
showCellEditor : function(elCell, oRecord, oColumn) {
    elCell = Dom.get(elCell);

    if(elCell && (elCell.ownerDocument === document)) {
        if(!oRecord || !(oRecord instanceof YAHOO.widget.Record)) {
            oRecord = this.getRecord(elCell);
        }
        if(!oColumn || !(oColumn instanceof YAHOO.widget.Column)) {
            oColumn = this.getColumn(elCell);
        }
        if(oRecord && oColumn) {
            var oCellEditor = this._oCellEditor;

            // Clear previous Editor
            if(oCellEditor.isActive) {
                this.cancelCellEditor();
            }

            // Editor not defined
            if(!oColumn.editor) {
                return;
            }

            // Update Editor values
            oCellEditor.cell = elCell;
            oCellEditor.record = oRecord;
            oCellEditor.column = oColumn;
            oCellEditor.validator = (oColumn.editorOptions &&
                    lang.isFunction(oColumn.editorOptions.validator)) ?
                    oColumn.editorOptions.validator : null;
            oCellEditor.value = oRecord.getData(oColumn.key);
            oCellEditor.defaultValue = null;

            // Move Editor
            var elContainer = oCellEditor.container;
            var x = Dom.getX(elCell);
            var y = Dom.getY(elCell);

            // SF doesn't get xy for cells in scrolling table
            // when tbody display is set to block
            if(isNaN(x) || isNaN(y)) {
                x = elCell.offsetLeft + // cell pos relative to table
                        Dom.getX(this._elTbody.parentNode) - // plus table pos relative to document
                        this._elTbody.scrollLeft; // minus tbody scroll
                y = elCell.offsetTop + // cell pos relative to table
                        Dom.getY(this._elTbody.parentNode) - // plus table pos relative to document
                        this._elTbody.scrollTop + // minus tbody scroll
                        this._elThead.offsetHeight; // account for fixed THEAD cells
            }

            elContainer.style.left = x + "px";
            elContainer.style.top = y + "px";

            // Hook to customize the UI
            this.doBeforeShowCellEditor(this._oCellEditor);

            //TODO: This is temporarily up here due so elements can be focused
            // Show Editor
            elContainer.style.display = "";

            // Handle ESC key
            Ev.addListener(elContainer, "keydown", function(e, oSelf) {
                // ESC hides Cell Editor
                if((e.keyCode == 27)) {
                    oSelf.cancelCellEditor();
                    oSelf.focusTbodyEl();
                }
                else {
                    oSelf.fireEvent("editorKeydownEvent", {editor:oSelf._oCellEditor, event:e});
                }
            }, this);

            // Render Editor markup
            var fnEditor;
            if(lang.isString(oColumn.editor)) {
                switch(oColumn.editor) {
                    case "checkbox":
                        fnEditor = DT.editCheckbox;
                        break;
                    case "date":
                        fnEditor = DT.editDate;
                        break;
                    case "dropdown":
                        fnEditor = DT.editDropdown;
                        break;
                    case "radio":
                        fnEditor = DT.editRadio;
                        break;
                    case "textarea":
                        fnEditor = DT.editTextarea;
                        break;
                    case "textbox":
                        fnEditor = DT.editTextbox;
                        break;
                    default:
                        fnEditor = null;
                }
            }
            else if(lang.isFunction(oColumn.editor)) {
                fnEditor = oColumn.editor;
            }

            if(fnEditor) {
                // Create DOM input elements
                fnEditor(this._oCellEditor, this);

                // Show Save/Cancel buttons
                if(!oColumn.editorOptions || !oColumn.editorOptions.disableBtns) {
                    this.showCellEditorBtns(elContainer);
                }

                oCellEditor.isActive = true;

                //TODO: verify which args to pass
                this.fireEvent("editorShowEvent", {editor:oCellEditor});
                YAHOO.log("Cell Editor shown for " + elCell, "info", this.toString());
                return;
            }
        }
    }
    YAHOO.log("Could not show Cell Editor for " + elCell, "warn", this.toString());
},

/**
 * Overridable abstract method to customize Cell Editor UI.
 *
 * @method doBeforeShowCellEditor
 * @param oCellEditor {Object} Cell Editor object literal.
 */
doBeforeShowCellEditor : function(oCellEditor) {
},

/**
 * Adds Save/Cancel buttons to Cell Editor.
 *
 * @method showCellEditorBtns
 * @param elContainer {HTMLElement} Cell Editor container.
 */
showCellEditorBtns : function(elContainer) {
    // Buttons
    var elBtnsDiv = elContainer.appendChild(document.createElement("div"));
    Dom.addClass(elBtnsDiv, DT.CLASS_BUTTON);

    // Save button
    var elSaveBtn = elBtnsDiv.appendChild(document.createElement("button"));
    Dom.addClass(elSaveBtn, DT.CLASS_DEFAULT);
    elSaveBtn.innerHTML = "OK";
    Ev.addListener(elSaveBtn, "click", function(oArgs, oSelf) {
        oSelf.onEventSaveCellEditor(oArgs, oSelf);
        oSelf.focusTbodyEl();
    }, this, true);

    // Cancel button
    var elCancelBtn = elBtnsDiv.appendChild(document.createElement("button"));
    elCancelBtn.innerHTML = "Cancel";
    Ev.addListener(elCancelBtn, "click", function(oArgs, oSelf) {
        oSelf.onEventCancelCellEditor(oArgs, oSelf);
        oSelf.focusTbodyEl();
    }, this, true);
},

/**
 * Clears Cell Editor of all state and UI.
 *
 * @method resetCellEditor
 */

resetCellEditor : function() {
    var elContainer = this._oCellEditor.container;
    elContainer.style.display = "none";
    Ev.purgeElement(elContainer, true);
    elContainer.innerHTML = "";
    this._oCellEditor.value = null;
    this._oCellEditor.isActive = false;
},

/**
 * Saves Cell Editor input to Record.
 *
 * @method saveCellEditor
 */
saveCellEditor : function() {
    if(this._oCellEditor.isActive) {
        var newData = this._oCellEditor.value;
        // Copy the data to pass to the event
        var oldData = YAHOO.widget.DataTable._cloneObject(this._oCellEditor.record.getData(this._oCellEditor.column.key));

        // Validate input data
        if(this._oCellEditor.validator) {
            newData = this._oCellEditor.value = this._oCellEditor.validator.call(this, newData, oldData, this._oCellEditor);
            if(newData === null ) {
                this.resetCellEditor();
                this.fireEvent("editorRevertEvent",
                        {editor:this._oCellEditor, oldData:oldData, newData:newData});
                YAHOO.log("Could not save Cell Editor input due to invalid data " +
                        lang.dump(newData), "warn", this.toString());
                return;
            }
        }

        // Update the Record
        this._oRecordSet.updateRecordValue(this._oCellEditor.record, this._oCellEditor.column.key, this._oCellEditor.value);

        // Update the UI
        this.formatCell(this._oCellEditor.cell.firstChild);
        this._syncColWidths();

        // Clear out the Cell Editor
        this.resetCellEditor();

        this.fireEvent("editorSaveEvent",
                {editor:this._oCellEditor, oldData:oldData, newData:newData});
        YAHOO.log("Cell Editor input saved", "info", this.toString());
    }
    else {
        YAHOO.log("Cell Editor not active to save input", "warn", this.toString());
    }
},

/**
 * Cancels Cell Editor.
 *
 * @method cancelCellEditor
 */
cancelCellEditor : function() {
    if(this._oCellEditor.isActive) {
        this.resetCellEditor();
        //TODO: preserve values for the event?
        this.fireEvent("editorCancelEvent", {editor:this._oCellEditor});
        YAHOO.log("Cell Editor input canceled", "info", this.toString());
    }
    else {
        YAHOO.log("Cell Editor not active to cancel input", "warn", this.toString());
    }
},







































// ABSTRACT METHODS

/**
 * Overridable method gives implementers a hook to access data before
 * it gets added to RecordSet and rendered to the TBODY.
 *
 * @method doBeforeLoadData
 * @param sRequest {String} Original request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} additional arguments
 * @return {Boolean} Return true to continue loading data into RecordSet and
 * updating DataTable with new Records, false to cancel.
 */
doBeforeLoadData : function(sRequest, oResponse, oPayload) {
    return true;
},































































/////////////////////////////////////////////////////////////////////////////
//
// Public Custom Event Handlers
//
/////////////////////////////////////////////////////////////////////////////

/**
 * Overridable custom event handler to sort Column.
 *
 * @method onEventSortColumn
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventSortColumn : function(oArgs) {
//TODO: support form elements in sortable columns
    var evt = oArgs.event;
    var target = oArgs.target;

    var el = this.getThEl(target) || this.getTdEl(target);
    if(el && el.yuiColumnKey) {
        var oColumn = this.getColumn(el.yuiColumnKey);
        if(oColumn.sortable) {
            Ev.stopEvent(evt);
            this.sortColumn(oColumn);
        }
    }
    else {
        YAHOO.log("Could not find Column for " + target, "warn", this.toString());
    }
},

/**
 * Overridable custom event handler to select Column.
 *
 * @method onEventSelectColumn
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventSelectColumn : function(oArgs) {
    this.selectColumn(oArgs.target);
},

/**
 * Overridable custom event handler to highlight Column. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventHighlightColumn
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventHighlightColumn : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.highlightColumn(oArgs.target);
    }
},

/**
 * Overridable custom event handler to unhighlight Column. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventUnhighlightColumn
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventUnhighlightColumn : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.unhighlightColumn(oArgs.target);
    }
},

/**
 * Overridable custom event handler to manage selection according to desktop paradigm.
 *
 * @method onEventSelectRow
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventSelectRow : function(oArgs) {
    var sMode = this.get("selectionMode");
    if(sMode == "single") {
        this._handleSingleSelectionByMouse(oArgs);
    }
    else {
        this._handleStandardSelectionByMouse(oArgs);
    }
},

/**
 * Overridable custom event handler to select cell.
 *
 * @method onEventSelectCell
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventSelectCell : function(oArgs) {
    var sMode = this.get("selectionMode");
    if(sMode == "cellblock") {
        this._handleCellBlockSelectionByMouse(oArgs);
    }
    else if(sMode == "cellrange") {
        this._handleCellRangeSelectionByMouse(oArgs);
    }
    else {
        this._handleSingleCellSelectionByMouse(oArgs);
    }
},

/**
 * Overridable custom event handler to highlight row. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventHighlightRow
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventHighlightRow : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.highlightRow(oArgs.target);
    }
},

/**
 * Overridable custom event handler to unhighlight row. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventUnhighlightRow
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventUnhighlightRow : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.unhighlightRow(oArgs.target);
    }
},

/**
 * Overridable custom event handler to highlight cell. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventHighlightCell
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventHighlightCell : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.highlightCell(oArgs.target);
    }
},

/**
 * Overridable custom event handler to unhighlight cell. Accounts for spurious
 * caused-by-child events. 
 *
 * @method onEventUnhighlightCell
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventUnhighlightCell : function(oArgs) {
    //TODO: filter for all spurious events at a lower level
    if(!Dom.isAncestor(oArgs.target,Ev.getRelatedTarget(oArgs.event))) {
        this.unhighlightCell(oArgs.target);
    }
},

/**
 * Overridable custom event handler to format cell.
 *
 * @method onEventFormatCell
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventFormatCell : function(oArgs) {
    var target = oArgs.target;

    var elCell = this.getTdEl(target);
    if(elCell && elCell.yuiColumnKey) {
        var oColumn = this.getColumn(elCell.yuiColumnKey);
        this.formatCell(elCell.firstChild, this.getRecord(elCell), oColumn);
    }
    else {
        YAHOO.log("Could not format cell " + target, "warn", this.toString());
    }
},

/**
 * Overridable custom event handler to edit cell.
 *
 * @method onEventShowCellEditor
 * @param oArgs.event {HTMLEvent} Event object.
 * @param oArgs.target {HTMLElement} Target element.
 */
onEventShowCellEditor : function(oArgs) {
    var target = oArgs.target;

    var elCell = this.getTdEl(target);
    if(elCell) {
        this.showCellEditor(elCell);
    }
    else {
        YAHOO.log("Could not edit cell " + target, "warn", this.toString());
    }
},

/**
 * Overridable custom event handler to save Cell Editor input.
 *
 * @method onEventSaveCellEditor
 * @param oArgs.editor {Object} Cell Editor object literal.
 */
onEventSaveCellEditor : function(oArgs) {
    this.saveCellEditor();
},

/**
 * Overridable custom event handler to cancel Cell Editor.
 *
 * @method onEventCancelCellEditor
 * @param oArgs.editor {Object} Cell Editor object literal.
 */
onEventCancelCellEditor : function(oArgs) {
    this.cancelCellEditor();
},

/**
 * Callback function receives data from DataSource and populates an entire
 * DataTable with Records and TR elements, clearing previous Records, if any.
 *
 * @method onDataReturnInitializeTable
 * @param sRequest {String} Original request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} (optional) Additional argument(s)
 */
onDataReturnInitializeTable : function(sRequest, oResponse, oPayload) {
    this.initializeTable();

    this.onDataReturnSetRecords(sRequest,oResponse,oPayload);
},

/**
 * Callback function receives data from DataSource and appends to an existing
 * DataTable new Records and, if applicable, creates or updates
 * corresponding TR elements.
 *
 * @method onDataReturnAppendRows
 * @param sRequest {String} Original request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} (optional) Additional argument(s)
 */
onDataReturnAppendRows : function(sRequest, oResponse, oPayload) {
    this.fireEvent("dataReturnEvent", {request:sRequest,response:oResponse,payload:oPayload});

    // Pass data through abstract method for any transformations
    var ok = this.doBeforeLoadData(sRequest, oResponse, oPayload);

    // Data ok to append
    if(ok && oResponse && !oResponse.error && lang.isArray(oResponse.results)) {
        this.addRows(oResponse.results);

        // Update the instance with any payload data
        this._handleDataReturnPayload(sRequest,oResponse,oPayload);
    }
    // Error
    else if(ok && oResponse.error) {
        this.showTableMessage(DT.MSG_ERROR, DT.CLASS_ERROR);
    }
},

/**
 * Callback function receives data from DataSource and inserts new records
 * starting at the index specified in oPayload.insertIndex.  If applicable,
 * creates or updates corresponding TR elements.
 *
 * @method onDataReturnInsertRows
 * @param sRequest {String} Original request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} (optional) Additional argument(s)
 */
onDataReturnInsertRows : function(sRequest, oResponse, oPayload) {
    this.fireEvent("dataReturnEvent", {request:sRequest,response:oResponse,payload:oPayload});

    oPayload = oPayload || { insertIndex : 0 };

    // Pass data through abstract method for any transformations
    var ok = this.doBeforeLoadData(sRequest, oResponse, oPayload);

    // Data ok to append
    if(ok && oResponse && !oResponse.error && lang.isArray(oResponse.results)) {
        this.addRows(oResponse.results, oPayload.insertIndex || 0);

        // Update the instance with any payload data
        this._handleDataReturnPayload(sRequest,oResponse,oPayload);
    }
    // Error
    else if(ok && oResponse.error) {
        this.showTableMessage(DT.MSG_ERROR, DT.CLASS_ERROR);
    }
},

/**
 * Receives reponse from DataSource and populates the RecordSet with the
 * results.
 * @method onDataReturnSetRecords
 * @param oRequest {MIXED} Original generated request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} (optional) Additional argument(s)
 */
onDataReturnSetRecords : function(oRequest, oResponse, oPayload) {
    this.fireEvent("dataReturnEvent", {request:oRequest,response:oResponse,payload:oPayload});

    // Pass data through abstract method for any transformations
    var ok = this.doBeforeLoadData(oRequest, oResponse, oPayload);

    // Data ok to set
    if(ok && oResponse && !oResponse.error && lang.isArray(oResponse.results)) {
        var oPaginator = this.get('paginator');
        var startIndex = oPayload && lang.isNumber(oPayload.startIndex) ?
                            oPayload.startIndex : 0;

        // If paginating, set the number of total records if provided
        if (oPaginator instanceof Pag) {
            if (lang.isNumber(oResponse.totalRecords)) {
                oPaginator.setTotalRecords(oResponse.totalRecords,true);
            } else {
                oPaginator.setTotalRecords(oResponse.results.length,true);
            }
        }

        this._oRecordSet.setRecords(oResponse.results, startIndex);

        // Update the instance with any payload data
        this._handleDataReturnPayload(oRequest,oResponse,oPayload);

        this.render();
    }
    // Error
    else if(ok && oResponse.error) {
        this.showTableMessage(DT.MSG_ERROR, DT.CLASS_ERROR);
    }
},

/**
 * Updates the DataTable with data sent in an onDataReturn* payload
 * @method _handleDataReturnPayload
 * @param oRequest {MIXED} Original generated request.
 * @param oResponse {Object} Response object.
 * @param oPayload {MIXED} Additional argument(s)
 * @private
 */
_handleDataReturnPayload : function (oRequest, oResponse, oPayload) {
    if (oPayload) {
        // Update with any pagination information
        var oState = oPayload.pagination;

        if (oState) {
            // Set the paginator values in preparation for refresh
            var oPaginator = this.get('paginator');
            if (oPaginator && oPaginator instanceof Pag) {
                oPaginator.setStartIndex(oState.recordOffset,true);
                oPaginator.setRowsPerPage(oState.rowsPerPage,true);
            }

        }

        // Update with any sorting information
        oState = oPayload.sorting;

        if (oState) {
            // Set the sorting values in preparation for refresh
            this.set('sortedBy', oState);
        }
    }
},

































    /////////////////////////////////////////////////////////////////////////////
    //
    // Custom Events
    //
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Fired when the DataTable instance's initialization is complete.
     *
     * @event initEvent
     */

    /**
     * Fired when the DataTable's view is rendered.
     *
     * @event renderEvent
     */

    /**
     * Fired when data is returned from DataSource but before it is consumed by
     * DataTable.
     *
     * @event dataReturnEvent
     * @param oArgs.request {String} Original request.
     * @param oArgs.response {Object} Response object.
     */

    /**
     * Fired when the DataTable has a focus event.
     *
     * @event tableFocusEvent
     */

    /**
     * Fired when the DataTable THEAD element has a focus event.
     *
     * @event theadFocusEvent
     */

    /**
     * Fired when the DataTable TBODY element has a focus event.
     *
     * @event tbodyFocusEvent
     */

    /**
     * Fired when the DataTable has a blur event.
     *
     * @event tableBlurEvent
     */

    /*TODO
     * Fired when the DataTable THEAD element has a blur event.
     *
     * @event theadBlurEvent
     */

    /*TODO
     * Fired when the DataTable TBODY element has a blur event.
     *
     * @event tbodyBlurEvent
     */

    /**
     * Fired when the DataTable has a key event.
     *
     * @event tableKeyEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     */

    /**
     * Fired when the DataTable THEAD element has a key event.
     *
     * @event theadKeyEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     */

    /**
     * Fired when the DataTable TBODY element has a key event.
     *
     * @event tbodyKeyEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     */

    /**
     * Fired when the DataTable has a mouseover.
     *
     * @event tableMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     *
     */

    /**
     * Fired when the DataTable has a mouseout.
     *
     * @event tableMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     *
     */

    /**
     * Fired when the DataTable has a mousedown.
     *
     * @event tableMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     *
     */

    /**
     * Fired when the DataTable has a click.
     *
     * @event tableClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     *
     */

    /**
     * Fired when the DataTable has a dblclick.
     *
     * @event tableDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's TABLE element.
     *
     */

    /**
     * Fired when a fixed scrolling DataTable has a scroll.
     *
     * @event tableScrollEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The DataTable's CONTAINER element (in IE)
     * or the DataTable's TBODY element (everyone else).
     *
     */

    /**
     * Fired when a message is shown in the DataTable's message element.
     *
     * @event tableMsgShowEvent
     * @param oArgs.html {String} The HTML displayed.
     * @param oArgs.className {String} The className assigned.
     *
     */

    /**
     * Fired when the DataTable's message element is hidden.
     *
     * @event tableMsgHideEvent
     */

    /**
     * Fired when a THEAD row has a mouseover.
     *
     * @event theadRowMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a THEAD row has a mouseout.
     *
     * @event theadRowMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a THEAD row has a mousedown.
     *
     * @event theadRowMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a THEAD row has a click.
     *
     * @event theadRowClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a THEAD row has a dblclick.
     *
     * @event theadRowDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a THEAD cell has a mouseover.
     *
     * @event theadCellMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TH element.
     *
     */

    /**
     * Fired when a THEAD cell has a mouseout.
     *
     * @event theadCellMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TH element.
     *
     */

    /**
     * Fired when a THEAD cell has a mousedown.
     *
     * @event theadCellMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TH element.
     */

    /**
     * Fired when a THEAD cell has a click.
     *
     * @event theadCellClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TH element.
     */

    /**
     * Fired when a THEAD cell has a dblclick.
     *
     * @event theadCellDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TH element.
     */

    /**
     * Fired when a THEAD label has a mouseover.
     *
     * @event theadLabelMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SPAN element.
     *
     */

    /**
     * Fired when a THEAD label has a mouseout.
     *
     * @event theadLabelMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SPAN element.
     *
     */

    /**
     * Fired when a THEAD label has a mousedown.
     *
     * @event theadLabelMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SPAN element.
     */

    /**
     * Fired when a THEAD label has a click.
     *
     * @event theadLabelClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SPAN element.
     */

    /**
     * Fired when a THEAD label has a dblclick.
     *
     * @event theadLabelDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SPAN element.
     */

    /**
     * Fired when a column is sorted.
     *
     * @event columnSortEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     * @param oArgs.dir {String} Sort direction: YAHOO.widget.DataTable.CLASS_ASC
     * or YAHOO.widget.DataTable.CLASS_DESC.
     */

    /**
     * Fired when a column width is set.
     *
     * @event columnSetWidthEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     * @param oArgs.width {Number} The width in pixels.
     */

    /**
     * Fired when a column is drag-resized.
     *
     * @event columnResizeEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     * @param oArgs.target {HTMLElement} The TH element.
     */

    /**
     * Fired when a column is hidden.
     *
     * @event columnHideEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     */

    /**
     * Fired when a column is shown.
     *
     * @event columnShowEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     */

    /**
     * Fired when a column is selected.
     *
     * @event columnSelectEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     */

    /**
     * Fired when a column is unselected.
     *
     * @event columnUnselectEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     */
    /**
     * Fired when a column is removed.
     *
     * @event columnRemoveEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     */

    /**
     * Fired when a column is inserted.
     *
     * @event columnInsertEvent
     * @param oArgs.column {YAHOO.widget.Column} The Column instance.
     * @param oArgs.index {Number} The index position.
     */

    /**
     * Fired when a column is highlighted.
     *
     * @event columnHighlightEvent
     * @param oArgs.column {YAHOO.widget.Column} The highlighted Column.
     */

    /**
     * Fired when a column is unhighlighted.
     *
     * @event columnUnhighlightEvent
     * @param oArgs.column {YAHOO.widget.Column} The unhighlighted Column.
     */


    /**
     * Fired when a row has a mouseover.
     *
     * @event rowMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a row has a mouseout.
     *
     * @event rowMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a row has a mousedown.
     *
     * @event rowMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a row has a click.
     *
     * @event rowClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a row has a dblclick.
     *
     * @event rowDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TR element.
     */

    /**
     * Fired when a row is added.
     *
     * @event rowAddEvent
     * @param oArgs.record {YAHOO.widget.Record} The added Record.
     */

    /**
     * Fired when a row is updated.
     *
     * @event rowUpdateEvent
     * @param oArgs.record {YAHOO.widget.Record} The updated Record.
     * @param oArgs.oldData {Object} Object literal of the old data.
     */

    /**
     * Fired when a row is deleted.
     *
     * @event rowDeleteEvent
     * @param oArgs.oldData {Object} Object literal of the deleted data.
     * @param oArgs.recordIndex {Number} Index of the deleted Record.
     * @param oArgs.trElIndex {Number} Index of the deleted TR element, if on current page.
     */

    /**
     * Fired when a row is selected.
     *
     * @event rowSelectEvent
     * @param oArgs.el {HTMLElement} The selected TR element, if applicable.
     * @param oArgs.record {YAHOO.widget.Record} The selected Record.
     */

    /**
     * Fired when a row is unselected.
     *
     * @event rowUnselectEvent
     * @param oArgs.el {HTMLElement} The unselected TR element, if applicable.
     * @param oArgs.record {YAHOO.widget.Record} The unselected Record.
     */

    /**
     * Fired when all row selections are cleared.
     *
     * @event unselectAllRowsEvent
     */

    /**
     * Fired when a row is highlighted.
     *
     * @event rowHighlightEvent
     * @param oArgs.el {HTMLElement} The highlighted TR element.
     * @param oArgs.record {YAHOO.widget.Record} The highlighted Record.
     */

    /**
     * Fired when a row is unhighlighted.
     *
     * @event rowUnhighlightEvent
     * @param oArgs.el {HTMLElement} The highlighted TR element.
     * @param oArgs.record {YAHOO.widget.Record} The highlighted Record.
     */

    /**
     * Fired when a cell has a mouseover.
     *
     * @event cellMouseoverEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TD element.
     */

    /**
     * Fired when a cell has a mouseout.
     *
     * @event cellMouseoutEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TD element.
     */

    /**
     * Fired when a cell has a mousedown.
     *
     * @event cellMousedownEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TD element.
     */

    /**
     * Fired when a cell has a click.
     *
     * @event cellClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TD element.
     */

    /**
     * Fired when a cell has a dblclick.
     *
     * @event cellDblclickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The TD element.
     */

    /**
     * Fired when a cell is formatted.
     *
     * @event cellFormatEvent
     * @param oArgs.el {HTMLElement} The formatted TD element.
     * @param oArgs.record {YAHOO.widget.Record} The associated Record instance.
     * @param oArgs.column {YAHOO.widget.Column} The associated Column instance.
     * @param oArgs.key {String} (deprecated) The key of the formatted cell.
     */

    /**
     * Fired when a cell is selected.
     *
     * @event cellSelectEvent
     * @param oArgs.el {HTMLElement} The selected TD element.
     * @param oArgs.record {YAHOO.widget.Record} The associated Record instance.
     * @param oArgs.column {YAHOO.widget.Column} The associated Column instance.
     * @param oArgs.key {String} (deprecated) The key of the selected cell.
     */

    /**
     * Fired when a cell is unselected.
     *
     * @event cellUnselectEvent
     * @param oArgs.el {HTMLElement} The unselected TD element.
     * @param oArgs.record {YAHOO.widget.Record} The associated Record.
     * @param oArgs.column {YAHOO.widget.Column} The associated Column instance.
     * @param oArgs.key {String} (deprecated) The key of the unselected cell.

     */

    /**
     * Fired when a cell is highlighted.
     *
     * @event cellHighlightEvent
     * @param oArgs.el {HTMLElement} The highlighted TD element.
     * @param oArgs.record {YAHOO.widget.Record} The associated Record instance.
     * @param oArgs.column {YAHOO.widget.Column} The associated Column instance.
     * @param oArgs.key {String} (deprecated) The key of the highlighted cell.

     */

    /**
     * Fired when a cell is unhighlighted.
     *
     * @event cellUnhighlightEvent
     * @param oArgs.el {HTMLElement} The unhighlighted TD element.
     * @param oArgs.record {YAHOO.widget.Record} The associated Record instance.
     * @param oArgs.column {YAHOO.widget.Column} The associated Column instance.
     * @param oArgs.key {String} (deprecated) The key of the unhighlighted cell.

     */

    /**
     * Fired when all cell selections are cleared.
     *
     * @event unselectAllCellsEvent
     */

    /**
     * Fired when an Editor is activated.
     *
     * @event editorShowEvent
     * @param oArgs.editor {Object} The Editor object literal.
     */

    /**
     * Fired when an active Editor has a keydown.
     *
     * @event editorKeydownEvent
     * @param oArgs.editor {Object} The Editor object literal.
     * @param oArgs.event {HTMLEvent} The event object.
     */

    /**
     * Fired when Editor input is reverted.
     *
     * @event editorRevertEvent
     * @param oArgs.editor {Object} The Editor object literal.
     * @param oArgs.newData {Object} New data value from form input field.
     * @param oArgs.oldData {Object} Old data value.
     */

    /**
     * Fired when Editor input is saved.
     *
     * @event editorSaveEvent
     * @param oArgs.editor {Object} The Editor object literal.
     * @param oArgs.newData {Object} New data value from form input field.
     * @param oArgs.oldData {Object} Old data value.
     */

    /**
     * Fired when Editor input is canceled.
     *
     * @event editorCancelEvent
     * @param oArgs.editor {Object} The Editor object literal.
     */

    /**
     * Fired when an active Editor has a blur.
     *
     * @event editorBlurEvent
     * @param oArgs.editor {Object} The Editor object literal.
     */







    /**
     * Fired when a link is clicked.
     *
     * @event linkClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The A element.
     */

    /**
     * Fired when a BUTTON element is clicked.
     *
     * @event buttonClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The BUTTON element.
     */

    /**
     * Fired when a CHECKBOX element is clicked.
     *
     * @event checkboxClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The CHECKBOX element.
     */

    /*TODO
     * Fired when a SELECT element is changed.
     *
     * @event dropdownChangeEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The SELECT element.
     */

    /**
     * Fired when a RADIO element is clicked.
     *
     * @event radioClickEvent
     * @param oArgs.event {HTMLEvent} The event object.
     * @param oArgs.target {HTMLElement} The RADIO element.
     */


























/////////////////////////////////////////////////////////////////////////////
//
// Deprecated APIs
//
/////////////////////////////////////////////////////////////////////////////

/**
 * @method getBody
 * @deprecated Use getTbodyEl().
 */
getBody : function() {
    // Backward compatibility
    YAHOO.log("The method getBody() has been deprecated" +
            " in favor of getTbodyEl()", "warn", this.toString());
    return this.getTbodyEl();
},

/**
 * @method getCell
 * @deprecated Use getTdEl().
 */
getCell : function(index) {
    // Backward compatibility
    YAHOO.log("The method getCell() has been deprecated" +
            " in favor of getTdEl()", "warn", this.toString());
    return this.getTdEl(index);
},

/**
 * @method getRow
 * @deprecated Use getTrEl().
 */
getRow : function(index) {
    // Backward compatibility
    YAHOO.log("The method getRow() has been deprecated" +
            " in favor of getTrEl()", "warn", this.toString());
    return this.getTrEl(index);
},

/**
 * @method refreshView
 * @deprecated Use render.
 */
refreshView : function() {
    // Backward compatibility
    YAHOO.log("The method refreshView() has been deprecated" +
            " in favor of render()", "warn", this.toString());
    this.render();
},

/**
 * @method select
 * @deprecated Use selectRow.
 */
select : function(els) {
    // Backward compatibility
    YAHOO.log("The method select() has been deprecated" +
            " in favor of selectRow()", "warn", this.toString());
    if(!lang.isArray(els)) {
        els = [els];
    }
    for(var i=0; i<els.length; i++) {
        this.selectRow(els[i]);
    }
},

/**
 * @method updatePaginator
 * @deprecated Use Paginator class APIs.
 */
updatePaginator : function(oNewValues) {
    // Complete the set (default if not present)
    var oValidPaginator = this.get("paginator");

    var nOrigCurrentPage = oValidPaginator.currentPage;
    for(var param in oNewValues) {
        if(lang.hasOwnProperty(oValidPaginator, param)) {
            oValidPaginator[param] = oNewValues[param];
        }
    }

    oValidPaginator.totalRecords = this._oRecordSet.getLength();
    oValidPaginator.rowsThisPage = Math.min(oValidPaginator.rowsPerPage, oValidPaginator.totalRecords);
    oValidPaginator.totalPages = Math.ceil(oValidPaginator.totalRecords / oValidPaginator.rowsThisPage);
    if(isNaN(oValidPaginator.totalPages)) {
        oValidPaginator.totalPages = 0;
    }
    if(oValidPaginator.currentPage > oValidPaginator.totalPages) {
        if(oValidPaginator.totalPages < 1) {
            oValidPaginator.currentPage = 1;
        }
        else {
            oValidPaginator.currentPage = oValidPaginator.totalPages;
        }
    }

    if(oValidPaginator.currentPage !== nOrigCurrentPage) {
        oValidPaginator.startRecordIndex = (oValidPaginator.currentPage-1)*oValidPaginator.rowsPerPage;
    }


    this.set("paginator", oValidPaginator);
    return this.get("paginator");
},

/**
 * @method showPage
 * @deprecated Use Paginator class APIs.
 */
showPage : function(nPage) {
    var oPaginator = this.get('paginator');
    // Validate input
    if(!lang.isNumber(nPage) || (nPage < 1)) {
        if (oPaginator instanceof Pag) {
            if (!oPaginator.hasPage(nPage)) {
                nPage = 1;
            }
        } else if (nPage > oPaginator.totalPages) {
            nPage = 1;
        }
    }

    if (oPaginator instanceof Pag) {
        oPaginator.setPage(nPage);
    } else {
        this.updatePaginator({currentPage:nPage});
        this.render();
    }
},

/**
 * @method formatPaginators
 * @deprecated Use Paginator class APIs.
 */
formatPaginators : function() {
    var pag = this.get("paginator");
    if (pag instanceof Pag) {
        pag.update();
        return;
    }

    var i;

    // For Opera workaround
    var dropdownEnabled = false;

    // Links are enabled
    if(pag.pageLinks > -1) {
        for(i=0; i<pag.links.length; i++) {
            this.formatPaginatorLinks(pag.links[i], pag.currentPage, pag.pageLinksStart, pag.pageLinks, pag.totalPages);
        }
    }

    // Dropdown is enabled
    for(i=0; i<pag.dropdowns.length; i++) {
         if(pag.dropdownOptions) {
            dropdownEnabled = true;
            this.formatPaginatorDropdown(pag.dropdowns[i], pag.dropdownOptions);
        }
        else {
            pag.dropdowns[i].style.display = "none";
        }
    }

    // For Opera artifacting in dropdowns
    if(dropdownEnabled && ua.opera) {
        document.body.style += '';
    }
    YAHOO.log("Paginators formatted", "info", this.toString());
},

/**
 * @method formatPaginatorDropdown
 * @deprecated Use Paginator class APIs.
 */
formatPaginatorDropdown : function(elDropdown, dropdownOptions) {
    if(elDropdown && (elDropdown.ownerDocument == document)) {
        // Clear OPTION elements
        while (elDropdown.firstChild) {
            elDropdown.removeChild(elDropdown.firstChild);
        }

        // Create OPTION elements
        for(var j=0; j<dropdownOptions.length; j++) {
            var dropdownOption = dropdownOptions[j];
            var optionEl = document.createElement("option");
            optionEl.value = (lang.isValue(dropdownOption.value)) ?
                    dropdownOption.value : dropdownOption;
            optionEl.innerHTML = (lang.isValue(dropdownOption.text)) ?
                    dropdownOption.text : dropdownOption;
            optionEl = elDropdown.appendChild(optionEl);
        }

        var options = elDropdown.options;
        // Update dropdown's "selected" value
        if(options.length) {
            for(var i=options.length-1; i>-1; i--) {
                if((this.get("paginator").rowsPerPage + "") === options[i].value) {
                    options[i].selected = true;
                }
            }
        }

        // Show the dropdown
        elDropdown.style.display = "";
        return;
    }
    YAHOO.log("Could not update Paginator dropdown " + elDropdown, "error", this.toString());
},

/**
 * @method formatPaginatorLinks
 * @deprecated Use Paginator class APIs.
 */
formatPaginatorLinks : function(elContainer, nCurrentPage, nPageLinksStart, nPageLinksLength, nTotalPages) {
    if(elContainer && (elContainer.ownerDocument == document) &&
            lang.isNumber(nCurrentPage) && lang.isNumber(nPageLinksStart) &&
            lang.isNumber(nTotalPages)) {
        // Set up markup for first/last/previous/next
        var bIsFirstPage = (nCurrentPage == 1) ? true : false;
        var bIsLastPage = (nCurrentPage == nTotalPages) ? true : false;
        var sFirstLinkMarkup = (bIsFirstPage) ?
                " <span class=\"" + DT.CLASS_DISABLED +
                " " + DT.CLASS_FIRST + "\">&lt;&lt;</span> " :
                " <a href=\"#\" class=\"" + DT.CLASS_FIRST + "\">&lt;&lt;</a> ";
        var sPrevLinkMarkup = (bIsFirstPage) ?
                " <span class=\"" + DT.CLASS_DISABLED +
                " " + DT.CLASS_PREVIOUS + "\">&lt;</span> " :
                " <a href=\"#\" class=\"" + DT.CLASS_PREVIOUS + "\">&lt;</a> " ;
        var sNextLinkMarkup = (bIsLastPage) ?
                " <span class=\"" + DT.CLASS_DISABLED +
                " " + DT.CLASS_NEXT + "\">&gt;</span> " :
                " <a href=\"#\" class=\"" + DT.CLASS_NEXT + "\">&gt;</a> " ;
        var sLastLinkMarkup = (bIsLastPage) ?
                " <span class=\"" + DT.CLASS_DISABLED +
                " " + DT.CLASS_LAST +  "\">&gt;&gt;</span> " :
                " <a href=\"#\" class=\"" + DT.CLASS_LAST + "\">&gt;&gt;</a> ";

        // Start with first and previous
        var sMarkup = sFirstLinkMarkup + sPrevLinkMarkup;

        // Ok to show all links
        var nMaxLinks = nTotalPages;
        var nFirstLink = 1;
        var nLastLink = nTotalPages;

        if(nPageLinksLength > 0) {
        // Calculate how many links to show
            nMaxLinks = (nPageLinksStart+nPageLinksLength < nTotalPages) ?
                    nPageLinksStart+nPageLinksLength-1 : nTotalPages;

            // Try to keep the current page in the middle
            nFirstLink = (nCurrentPage - Math.floor(nMaxLinks/2) > 0) ? nCurrentPage - Math.floor(nMaxLinks/2) : 1;
            nLastLink = (nCurrentPage + Math.floor(nMaxLinks/2) <= nTotalPages) ? nCurrentPage + Math.floor(nMaxLinks/2) : nTotalPages;

            // Keep the last link in range
            if(nFirstLink === 1) {
                nLastLink = nMaxLinks;
            }
            // Keep the first link in range
            else if(nLastLink === nTotalPages) {
                nFirstLink = nTotalPages - nMaxLinks + 1;
            }

            // An even number of links can get funky
            if(nLastLink - nFirstLink === nMaxLinks) {
                nLastLink--;
            }
      }

        // Generate markup for each page
        for(var i=nFirstLink; i<=nLastLink; i++) {
            if(i != nCurrentPage) {
                sMarkup += " <a href=\"#\" class=\"" + DT.CLASS_PAGE + "\">" + i + "</a> ";
            }
            else {
                sMarkup += " <span class=\"" + DT.CLASS_SELECTED + "\">" + i + "</span>";
            }
        }
        sMarkup += sNextLinkMarkup + sLastLinkMarkup;
        elContainer.innerHTML = sMarkup;
        return;
    }
    YAHOO.log("Could not format Paginator links", "error", this.toString());
},

/**
 * @method _onPaginatorLinkClick
 * @private
 * @deprecated Use Paginator class APIs.
 */
_onPaginatorLinkClick : function(e, oSelf) {
    // Backward compatibility
    var elTarget = Ev.getTarget(e);
    var elTag = elTarget.nodeName.toLowerCase();

    if(oSelf._oCellEditor && oSelf._oCellEditor.isActive) {
        oSelf.fireEvent("editorBlurEvent", {editor:oSelf._oCellEditor});
    }

    while(elTarget && (elTag != "table")) {
        switch(elTag) {
            case "body":
                return;
            case "a":
                Ev.stopEvent(e);
                //TODO: after the showPage call, figure out which link
                //TODO: was clicked and reset focus to the new version of it
                //TODO: support multiple custom classnames
                switch(elTarget.className) {
                    case DT.CLASS_PAGE:
                        oSelf.showPage(parseInt(elTarget.innerHTML,10));
                        return;
                    case DT.CLASS_FIRST:
                        oSelf.showPage(1);
                        return;
                    case DT.CLASS_LAST:
                        oSelf.showPage(oSelf.get("paginator").totalPages);
                        return;
                    case DT.CLASS_PREVIOUS:
                        oSelf.showPage(oSelf.get("paginator").currentPage - 1);
                        return;
                    case DT.CLASS_NEXT:
                        oSelf.showPage(oSelf.get("paginator").currentPage + 1);
                        return;
                }
                break;
            default:
                return;
        }
        elTarget = elTarget.parentNode;
        if(elTarget) {
            elTag = elTarget.nodeName.toLowerCase();
        }
        else {
            return;
        }
    }
},

/**
 * @method _onPaginatorDropdownChange
 * @private
 * @deprecated Use Paginator class APIs.
 */
_onPaginatorDropdownChange : function(e, oSelf) {
    // Backward compatibility
    var elTarget = Ev.getTarget(e);
    var newValue = elTarget[elTarget.selectedIndex].value;

    var newRowsPerPage = lang.isValue(parseInt(newValue,10)) ? parseInt(newValue,10) : null;
    if(newRowsPerPage !== null) {
        var newStartRecordIndex = (oSelf.get("paginator").currentPage-1) * newRowsPerPage;
        oSelf.updatePaginator({rowsPerPage:newRowsPerPage, startRecordIndex:newStartRecordIndex});
        oSelf.render();
    }
    else {
        YAHOO.log("Could not paginate with " + newValue + " rows per page", "error", oSelf.toString());
    }
},

/**
 * @method onEventEditCell
 * @deprecated Use onEventShowCellEditor.
 */
onEventEditCell : function(oArgs) {
    // Backward compatibility
    YAHOO.log("The method onEventEditCell() has been deprecated" +
        " in favor of onEventShowCellEditor()", "warn", this.toString());
    this.onEventShowCellEditor(oArgs);
},

/**
 * @method onDataReturnReplaceRows
 * @deprecated Use onDataReturnInitializeTable.
 */
onDataReturnReplaceRows : function(sRequest, oResponse) {
    // Backward compatibility
    YAHOO.log("The method onDataReturnReplaceRows() has been deprecated" +
            " in favor of onDataReturnInitializeTable()", "warn", this.toString());
    this.onDataReturnInitializeTable(sRequest, oResponse);
}

/**
 * @event headerRowMouseoverEvent
 * @deprecated Use theadRowMouseoverEvent.
 */

/**
 * @event headerRowMouseoutEvent
 * @deprecated Use theadRowMouseoutEvent.
 */

/**
 * @event headerRowMousedownEvent
 * @deprecated Use theadRowMousedownEvent.
 */

/**
 * @event headerRowClickEvent
 * @deprecated Use theadRowClickEvent.
 */

/**
 * @event headerRowDblclickEvent
 * @deprecated Use theadRowDblclickEvent.
 */

/**
 * @event headerCellMouseoverEvent
 * @deprecated Use theadCellMouseoverEvent.
 */

/**
 * @event headerCellMouseoutEvent
 * @deprecated Use theadCellMouseoutEvent.
 */

/**
 * @event headerCellMousedownEvent
 * @deprecated Use theadCellMousedownEvent.
 */

/**
 * @event headerCellClickEvent
 * @deprecated Use theadCellClickEvent.
 */

/**
 * @event headerCellDblclickEvent
 * @deprecated Use theadCellDblclickEvent.
 */

/**
 * @event headerLabelMouseoverEvent
 * @deprecated Use theadLabelMouseoverEvent.
 */

/**
 * @event headerLabelMouseoutEvent
 * @deprecated Use theadLabelMouseoutEvent.
 */

/**
 * @event headerLabelMousedownEvent
 * @deprecated Use theadLabelMousedownEvent.
 */

/**
 * @event headerLabelClickEvent
 * @deprecated Use theadLabelClickEvent.
 */

/**
 * @event headerLabelDbllickEvent
 * @deprecated Use theadLabelDblclickEvent.
 */

});
})();

YAHOO.register("datatable", YAHOO.widget.DataTable, {version: "2.5.0", build: "895"});
