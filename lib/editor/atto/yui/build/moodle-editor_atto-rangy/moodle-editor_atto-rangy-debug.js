/**
 * Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */

(function(factory, root) {
    // No AMD or CommonJS support so we place Rangy in (probably) the global variable
    root.rangy = factory();
})(function() {

    var OBJECT = "object", FUNCTION = "function", UNDEFINED = "undefined";

    // Minimal set of properties required for DOM Level 2 Range compliance. Comparison constants such as START_TO_START
    // are omitted because ranges in KHTML do not have them but otherwise work perfectly well. See issue 113.
    var domRangeProperties = ["startContainer", "startOffset", "endContainer", "endOffset", "collapsed",
        "commonAncestorContainer"];

    // Minimal set of methods required for DOM Level 2 Range compliance
    var domRangeMethods = ["setStart", "setStartBefore", "setStartAfter", "setEnd", "setEndBefore",
        "setEndAfter", "collapse", "selectNode", "selectNodeContents", "compareBoundaryPoints", "deleteContents",
        "extractContents", "cloneContents", "insertNode", "surroundContents", "cloneRange", "toString", "detach"];

    var textRangeProperties = ["boundingHeight", "boundingLeft", "boundingTop", "boundingWidth", "htmlText", "text"];

    // Subset of TextRange's full set of methods that we're interested in
    var textRangeMethods = ["collapse", "compareEndPoints", "duplicate", "moveToElementText", "parentElement", "select",
        "setEndPoint", "getBoundingClientRect"];

    /*----------------------------------------------------------------------------------------------------------------*/

    // Trio of functions taken from Peter Michaux's article:
    // http://peter.michaux.ca/articles/feature-detection-state-of-the-art-browser-scripting
    function isHostMethod(o, p) {
        var t = typeof o[p];
        return t == FUNCTION || (!!(t == OBJECT && o[p])) || t == "unknown";
    }

    function isHostObject(o, p) {
        return !!(typeof o[p] == OBJECT && o[p]);
    }

    function isHostProperty(o, p) {
        return typeof o[p] != UNDEFINED;
    }

    // Creates a convenience function to save verbose repeated calls to tests functions
    function createMultiplePropertyTest(testFunc) {
        return function(o, props) {
            var i = props.length;
            while (i--) {
                if (!testFunc(o, props[i])) {
                    return false;
                }
            }
            return true;
        };
    }

    // Next trio of functions are a convenience to save verbose repeated calls to previous two functions
    var areHostMethods = createMultiplePropertyTest(isHostMethod);
    var areHostObjects = createMultiplePropertyTest(isHostObject);
    var areHostProperties = createMultiplePropertyTest(isHostProperty);

    function isTextRange(range) {
        return range && areHostMethods(range, textRangeMethods) && areHostProperties(range, textRangeProperties);
    }

    function getBody(doc) {
        return isHostObject(doc, "body") ? doc.body : doc.getElementsByTagName("body")[0];
    }

    var forEach = [].forEach ?
        function(arr, func) {
            arr.forEach(func);
        } :
        function(arr, func) {
            for (var i = 0, len = arr.length; i < len; ++i) {
                func(arr[i], i);
            }
        };

    var modules = {};

    var isBrowser = (typeof window != UNDEFINED && typeof document != UNDEFINED);

    var util = {
        isHostMethod: isHostMethod,
        isHostObject: isHostObject,
        isHostProperty: isHostProperty,
        areHostMethods: areHostMethods,
        areHostObjects: areHostObjects,
        areHostProperties: areHostProperties,
        isTextRange: isTextRange,
        getBody: getBody,
        forEach: forEach
    };

    var api = {
        version: "1.3.0",
        initialized: false,
        isBrowser: isBrowser,
        supported: true,
        util: util,
        features: {},
        modules: modules,
        config: {
            alertOnFail: false,
            alertOnWarn: false,
            preferTextRange: false,
            autoInitialize: (typeof rangyAutoInitialize == UNDEFINED) ? true : rangyAutoInitialize
        }
    };

    function consoleLog(msg) {
        if (typeof console != UNDEFINED && isHostMethod(console, "log")) {
            console.log(msg);
        }
    }

    function alertOrLog(msg, shouldAlert) {
        if (isBrowser && shouldAlert) {
            alert(msg);
        } else  {
            consoleLog(msg);
        }
    }

    function fail(reason) {
        api.initialized = true;
        api.supported = false;
        alertOrLog("Rangy is not supported in this environment. Reason: " + reason, api.config.alertOnFail);
    }

    api.fail = fail;

    function warn(msg) {
        alertOrLog("Rangy warning: " + msg, api.config.alertOnWarn);
    }

    api.warn = warn;

    // Add utility extend() method
    var extend;
    if ({}.hasOwnProperty) {
        util.extend = extend = function(obj, props, deep) {
            var o, p;
            for (var i in props) {
                if (props.hasOwnProperty(i)) {
                    o = obj[i];
                    p = props[i];
                    if (deep && o !== null && typeof o == "object" && p !== null && typeof p == "object") {
                        extend(o, p, true);
                    }
                    obj[i] = p;
                }
            }
            // Special case for toString, which does not show up in for...in loops in IE <= 8
            if (props.hasOwnProperty("toString")) {
                obj.toString = props.toString;
            }
            return obj;
        };

        util.createOptions = function(optionsParam, defaults) {
            var options = {};
            extend(options, defaults);
            if (optionsParam) {
                extend(options, optionsParam);
            }
            return options;
        };
    } else {
        fail("hasOwnProperty not supported");
    }

    // Test whether we're in a browser and bail out if not
    if (!isBrowser) {
        fail("Rangy can only run in a browser");
    }

    // Test whether Array.prototype.slice can be relied on for NodeLists and use an alternative toArray() if not
    (function() {
        var toArray;

        if (isBrowser) {
            var el = document.createElement("div");
            el.appendChild(document.createElement("span"));
            var slice = [].slice;
            try {
                if (slice.call(el.childNodes, 0)[0].nodeType == 1) {
                    toArray = function(arrayLike) {
                        return slice.call(arrayLike, 0);
                    };
                }
            } catch (e) {}
        }

        if (!toArray) {
            toArray = function(arrayLike) {
                var arr = [];
                for (var i = 0, len = arrayLike.length; i < len; ++i) {
                    arr[i] = arrayLike[i];
                }
                return arr;
            };
        }

        util.toArray = toArray;
    })();

    // Very simple event handler wrapper function that doesn't attempt to solve issues such as "this" handling or
    // normalization of event properties
    var addListener;
    if (isBrowser) {
        if (isHostMethod(document, "addEventListener")) {
            addListener = function(obj, eventType, listener) {
                obj.addEventListener(eventType, listener, false);
            };
        } else if (isHostMethod(document, "attachEvent")) {
            addListener = function(obj, eventType, listener) {
                obj.attachEvent("on" + eventType, listener);
            };
        } else {
            fail("Document does not have required addEventListener or attachEvent method");
        }

        util.addListener = addListener;
    }

    var initListeners = [];

    function getErrorDesc(ex) {
        return ex.message || ex.description || String(ex);
    }

    // Initialization
    function init() {
        if (!isBrowser || api.initialized) {
            return;
        }
        var testRange;
        var implementsDomRange = false, implementsTextRange = false;

        // First, perform basic feature tests

        if (isHostMethod(document, "createRange")) {
            testRange = document.createRange();
            if (areHostMethods(testRange, domRangeMethods) && areHostProperties(testRange, domRangeProperties)) {
                implementsDomRange = true;
            }
        }

        var body = getBody(document);
        if (!body || body.nodeName.toLowerCase() != "body") {
            fail("No body element found");
            return;
        }

        if (body && isHostMethod(body, "createTextRange")) {
            testRange = body.createTextRange();
            if (isTextRange(testRange)) {
                implementsTextRange = true;
            }
        }

        if (!implementsDomRange && !implementsTextRange) {
            fail("Neither Range nor TextRange are available");
            return;
        }

        api.initialized = true;
        api.features = {
            implementsDomRange: implementsDomRange,
            implementsTextRange: implementsTextRange
        };

        // Initialize modules
        var module, errorMessage;
        for (var moduleName in modules) {
            if ( (module = modules[moduleName]) instanceof Module ) {
                module.init(module, api);
            }
        }

        // Call init listeners
        for (var i = 0, len = initListeners.length; i < len; ++i) {
            try {
                initListeners[i](api);
            } catch (ex) {
                errorMessage = "Rangy init listener threw an exception. Continuing. Detail: " + getErrorDesc(ex);
                consoleLog(errorMessage);
            }
        }
    }

    function deprecationNotice(deprecated, replacement, module) {
        if (module) {
            deprecated += " in module " + module.name;
        }
        api.warn("DEPRECATED: " + deprecated + " is deprecated. Please use " +
        replacement + " instead.");
    }

    function createAliasForDeprecatedMethod(owner, deprecated, replacement, module) {
        owner[deprecated] = function() {
            deprecationNotice(deprecated, replacement, module);
            return owner[replacement].apply(owner, util.toArray(arguments));
        };
    }

    util.deprecationNotice = deprecationNotice;
    util.createAliasForDeprecatedMethod = createAliasForDeprecatedMethod;

    // Allow external scripts to initialize this library in case it's loaded after the document has loaded
    api.init = init;

    // Execute listener immediately if already initialized
    api.addInitListener = function(listener) {
        if (api.initialized) {
            listener(api);
        } else {
            initListeners.push(listener);
        }
    };

    var shimListeners = [];

    api.addShimListener = function(listener) {
        shimListeners.push(listener);
    };

    function shim(win) {
        win = win || window;
        init();

        // Notify listeners
        for (var i = 0, len = shimListeners.length; i < len; ++i) {
            shimListeners[i](win);
        }
    }

    if (isBrowser) {
        api.shim = api.createMissingNativeApi = shim;
        createAliasForDeprecatedMethod(api, "createMissingNativeApi", "shim");
    }

    function Module(name, dependencies, initializer) {
        this.name = name;
        this.dependencies = dependencies;
        this.initialized = false;
        this.supported = false;
        this.initializer = initializer;
    }

    Module.prototype = {
        init: function() {
            var requiredModuleNames = this.dependencies || [];
            for (var i = 0, len = requiredModuleNames.length, requiredModule, moduleName; i < len; ++i) {
                moduleName = requiredModuleNames[i];

                requiredModule = modules[moduleName];
                if (!requiredModule || !(requiredModule instanceof Module)) {
                    throw new Error("required module '" + moduleName + "' not found");
                }

                requiredModule.init();

                if (!requiredModule.supported) {
                    throw new Error("required module '" + moduleName + "' not supported");
                }
            }

            // Now run initializer
            this.initializer(this);
        },

        fail: function(reason) {
            this.initialized = true;
            this.supported = false;
            throw new Error(reason);
        },

        warn: function(msg) {
            api.warn("Module " + this.name + ": " + msg);
        },

        deprecationNotice: function(deprecated, replacement) {
            api.warn("DEPRECATED: " + deprecated + " in module " + this.name + " is deprecated. Please use " +
                replacement + " instead");
        },

        createError: function(msg) {
            return new Error("Error in Rangy " + this.name + " module: " + msg);
        }
    };

    function createModule(name, dependencies, initFunc) {
        var newModule = new Module(name, dependencies, function(module) {
            if (!module.initialized) {
                module.initialized = true;
                try {
                    initFunc(api, module);
                    module.supported = true;
                } catch (ex) {
                    var errorMessage = "Module '" + name + "' failed to load: " + getErrorDesc(ex);
                    consoleLog(errorMessage);
                    if (ex.stack) {
                        consoleLog(ex.stack);
                    }
                }
            }
        });
        modules[name] = newModule;
        return newModule;
    }

    api.createModule = function(name) {
        // Allow 2 or 3 arguments (second argument is an optional array of dependencies)
        var initFunc, dependencies;
        if (arguments.length == 2) {
            initFunc = arguments[1];
            dependencies = [];
        } else {
            initFunc = arguments[2];
            dependencies = arguments[1];
        }

        var module = createModule(name, dependencies, initFunc);

        // Initialize the module immediately if the core is already initialized
        if (api.initialized && api.supported) {
            module.init();
        }
    };

    api.createCoreModule = function(name, dependencies, initFunc) {
        createModule(name, dependencies, initFunc);
    };

    /*----------------------------------------------------------------------------------------------------------------*/

    // Ensure rangy.rangePrototype and rangy.selectionPrototype are available immediately

    function RangePrototype() {}
    api.RangePrototype = RangePrototype;
    api.rangePrototype = new RangePrototype();

    function SelectionPrototype() {}
    api.selectionPrototype = new SelectionPrototype();

    /*----------------------------------------------------------------------------------------------------------------*/

    // DOM utility methods used by Rangy
    api.createCoreModule("DomUtil", [], function(api, module) {
        var UNDEF = "undefined";
        var util = api.util;
        var getBody = util.getBody;

        // Perform feature tests
        if (!util.areHostMethods(document, ["createDocumentFragment", "createElement", "createTextNode"])) {
            module.fail("document missing a Node creation method");
        }

        if (!util.isHostMethod(document, "getElementsByTagName")) {
            module.fail("document missing getElementsByTagName method");
        }

        var el = document.createElement("div");
        if (!util.areHostMethods(el, ["insertBefore", "appendChild", "cloneNode"] ||
                !util.areHostObjects(el, ["previousSibling", "nextSibling", "childNodes", "parentNode"]))) {
            module.fail("Incomplete Element implementation");
        }

        // innerHTML is required for Range's createContextualFragment method
        if (!util.isHostProperty(el, "innerHTML")) {
            module.fail("Element is missing innerHTML property");
        }

        var textNode = document.createTextNode("test");
        if (!util.areHostMethods(textNode, ["splitText", "deleteData", "insertData", "appendData", "cloneNode"] ||
                !util.areHostObjects(el, ["previousSibling", "nextSibling", "childNodes", "parentNode"]) ||
                !util.areHostProperties(textNode, ["data"]))) {
            module.fail("Incomplete Text Node implementation");
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Removed use of indexOf because of a bizarre bug in Opera that is thrown in one of the Acid3 tests. I haven't been
        // able to replicate it outside of the test. The bug is that indexOf returns -1 when called on an Array that
        // contains just the document as a single element and the value searched for is the document.
        var arrayContains = /*Array.prototype.indexOf ?
            function(arr, val) {
                return arr.indexOf(val) > -1;
            }:*/

            function(arr, val) {
                var i = arr.length;
                while (i--) {
                    if (arr[i] === val) {
                        return true;
                    }
                }
                return false;
            };

        // Opera 11 puts HTML elements in the null namespace, it seems, and IE 7 has undefined namespaceURI
        function isHtmlNamespace(node) {
            var ns;
            return typeof node.namespaceURI == UNDEF || ((ns = node.namespaceURI) === null || ns == "http://www.w3.org/1999/xhtml");
        }

        function parentElement(node) {
            var parent = node.parentNode;
            return (parent.nodeType == 1) ? parent : null;
        }

        function getNodeIndex(node) {
            var i = 0;
            while( (node = node.previousSibling) ) {
                ++i;
            }
            return i;
        }

        function getNodeLength(node) {
            switch (node.nodeType) {
                case 7:
                case 10:
                    return 0;
                case 3:
                case 8:
                    return node.length;
                default:
                    return node.childNodes.length;
            }
        }

        function getCommonAncestor(node1, node2) {
            var ancestors = [], n;
            for (n = node1; n; n = n.parentNode) {
                ancestors.push(n);
            }

            for (n = node2; n; n = n.parentNode) {
                if (arrayContains(ancestors, n)) {
                    return n;
                }
            }

            return null;
        }

        function isAncestorOf(ancestor, descendant, selfIsAncestor) {
            var n = selfIsAncestor ? descendant : descendant.parentNode;
            while (n) {
                if (n === ancestor) {
                    return true;
                } else {
                    n = n.parentNode;
                }
            }
            return false;
        }

        function isOrIsAncestorOf(ancestor, descendant) {
            return isAncestorOf(ancestor, descendant, true);
        }

        function getClosestAncestorIn(node, ancestor, selfIsAncestor) {
            var p, n = selfIsAncestor ? node : node.parentNode;
            while (n) {
                p = n.parentNode;
                if (p === ancestor) {
                    return n;
                }
                n = p;
            }
            return null;
        }

        function isCharacterDataNode(node) {
            var t = node.nodeType;
            return t == 3 || t == 4 || t == 8 ; // Text, CDataSection or Comment
        }

        function isTextOrCommentNode(node) {
            if (!node) {
                return false;
            }
            var t = node.nodeType;
            return t == 3 || t == 8 ; // Text or Comment
        }

        function insertAfter(node, precedingNode) {
            var nextNode = precedingNode.nextSibling, parent = precedingNode.parentNode;
            if (nextNode) {
                parent.insertBefore(node, nextNode);
            } else {
                parent.appendChild(node);
            }
            return node;
        }

        // Note that we cannot use splitText() because it is bugridden in IE 9.
        function splitDataNode(node, index, positionsToPreserve) {
            var newNode = node.cloneNode(false);
            newNode.deleteData(0, index);
            node.deleteData(index, node.length - index);
            insertAfter(newNode, node);

            // Preserve positions
            if (positionsToPreserve) {
                for (var i = 0, position; position = positionsToPreserve[i++]; ) {
                    // Handle case where position was inside the portion of node after the split point
                    if (position.node == node && position.offset > index) {
                        position.node = newNode;
                        position.offset -= index;
                    }
                    // Handle the case where the position is a node offset within node's parent
                    else if (position.node == node.parentNode && position.offset > getNodeIndex(node)) {
                        ++position.offset;
                    }
                }
            }
            return newNode;
        }

        function getDocument(node) {
            if (node.nodeType == 9) {
                return node;
            } else if (typeof node.ownerDocument != UNDEF) {
                return node.ownerDocument;
            } else if (typeof node.document != UNDEF) {
                return node.document;
            } else if (node.parentNode) {
                return getDocument(node.parentNode);
            } else {
                throw module.createError("getDocument: no document found for node");
            }
        }

        function getWindow(node) {
            var doc = getDocument(node);
            if (typeof doc.defaultView != UNDEF) {
                return doc.defaultView;
            } else if (typeof doc.parentWindow != UNDEF) {
                return doc.parentWindow;
            } else {
                throw module.createError("Cannot get a window object for node");
            }
        }

        function getIframeDocument(iframeEl) {
            if (typeof iframeEl.contentDocument != UNDEF) {
                return iframeEl.contentDocument;
            } else if (typeof iframeEl.contentWindow != UNDEF) {
                return iframeEl.contentWindow.document;
            } else {
                throw module.createError("getIframeDocument: No Document object found for iframe element");
            }
        }

        function getIframeWindow(iframeEl) {
            if (typeof iframeEl.contentWindow != UNDEF) {
                return iframeEl.contentWindow;
            } else if (typeof iframeEl.contentDocument != UNDEF) {
                return iframeEl.contentDocument.defaultView;
            } else {
                throw module.createError("getIframeWindow: No Window object found for iframe element");
            }
        }

        // This looks bad. Is it worth it?
        function isWindow(obj) {
            return obj && util.isHostMethod(obj, "setTimeout") && util.isHostObject(obj, "document");
        }

        function getContentDocument(obj, module, methodName) {
            var doc;

            if (!obj) {
                doc = document;
            }

            // Test if a DOM node has been passed and obtain a document object for it if so
            else if (util.isHostProperty(obj, "nodeType")) {
                doc = (obj.nodeType == 1 && obj.tagName.toLowerCase() == "iframe") ?
                    getIframeDocument(obj) : getDocument(obj);
            }

            // Test if the doc parameter appears to be a Window object
            else if (isWindow(obj)) {
                doc = obj.document;
            }

            if (!doc) {
                throw module.createError(methodName + "(): Parameter must be a Window object or DOM node");
            }

            return doc;
        }

        function getRootContainer(node) {
            var parent;
            while ( (parent = node.parentNode) ) {
                node = parent;
            }
            return node;
        }

        function comparePoints(nodeA, offsetA, nodeB, offsetB) {
            // See http://www.w3.org/TR/DOM-Level-2-Traversal-Range/ranges.html#Level-2-Range-Comparing
            var nodeC, root, childA, childB, n;
            if (nodeA == nodeB) {
                // Case 1: nodes are the same
                return offsetA === offsetB ? 0 : (offsetA < offsetB) ? -1 : 1;
            } else if ( (nodeC = getClosestAncestorIn(nodeB, nodeA, true)) ) {
                // Case 2: node C (container B or an ancestor) is a child node of A
                return offsetA <= getNodeIndex(nodeC) ? -1 : 1;
            } else if ( (nodeC = getClosestAncestorIn(nodeA, nodeB, true)) ) {
                // Case 3: node C (container A or an ancestor) is a child node of B
                return getNodeIndex(nodeC) < offsetB  ? -1 : 1;
            } else {
                root = getCommonAncestor(nodeA, nodeB);
                if (!root) {
                    throw new Error("comparePoints error: nodes have no common ancestor");
                }

                // Case 4: containers are siblings or descendants of siblings
                childA = (nodeA === root) ? root : getClosestAncestorIn(nodeA, root, true);
                childB = (nodeB === root) ? root : getClosestAncestorIn(nodeB, root, true);

                if (childA === childB) {
                    // This shouldn't be possible
                    throw module.createError("comparePoints got to case 4 and childA and childB are the same!");
                } else {
                    n = root.firstChild;
                    while (n) {
                        if (n === childA) {
                            return -1;
                        } else if (n === childB) {
                            return 1;
                        }
                        n = n.nextSibling;
                    }
                }
            }
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Test for IE's crash (IE 6/7) or exception (IE >= 8) when a reference to garbage-collected text node is queried
        var crashyTextNodes = false;

        function isBrokenNode(node) {
            var n;
            try {
                n = node.parentNode;
                return false;
            } catch (e) {
                return true;
            }
        }

        (function() {
            var el = document.createElement("b");
            el.innerHTML = "1";
            var textNode = el.firstChild;
            el.innerHTML = "<br />";
            crashyTextNodes = isBrokenNode(textNode);

            api.features.crashyTextNodes = crashyTextNodes;
        })();

        /*----------------------------------------------------------------------------------------------------------------*/

        function inspectNode(node) {
            if (!node) {
                return "[No node]";
            }
            if (crashyTextNodes && isBrokenNode(node)) {
                return "[Broken node]";
            }
            if (isCharacterDataNode(node)) {
                return '"' + node.data + '"';
            }
            if (node.nodeType == 1) {
                var idAttr = node.id ? ' id="' + node.id + '"' : "";
                return "<" + node.nodeName + idAttr + ">[index:" + getNodeIndex(node) + ",length:" + node.childNodes.length + "][" + (node.innerHTML || "[innerHTML not supported]").slice(0, 25) + "]";
            }
            return node.nodeName;
        }

        function fragmentFromNodeChildren(node) {
            var fragment = getDocument(node).createDocumentFragment(), child;
            while ( (child = node.firstChild) ) {
                fragment.appendChild(child);
            }
            return fragment;
        }

        var getComputedStyleProperty;
        if (typeof window.getComputedStyle != UNDEF) {
            getComputedStyleProperty = function(el, propName) {
                return getWindow(el).getComputedStyle(el, null)[propName];
            };
        } else if (typeof document.documentElement.currentStyle != UNDEF) {
            getComputedStyleProperty = function(el, propName) {
                return el.currentStyle ? el.currentStyle[propName] : "";
            };
        } else {
            module.fail("No means of obtaining computed style properties found");
        }

        function createTestElement(doc, html, contentEditable) {
            var body = getBody(doc);
            var el = doc.createElement("div");
            el.contentEditable = "" + !!contentEditable;
            if (html) {
                el.innerHTML = html;
            }

            // Insert the test element at the start of the body to prevent scrolling to the bottom in iOS (issue #292)
            var bodyFirstChild = body.firstChild;
            if (bodyFirstChild) {
                body.insertBefore(el, bodyFirstChild);
            } else {
                body.appendChild(el);
            }

            return el;
        }

        function removeNode(node) {
            return node.parentNode.removeChild(node);
        }

        function NodeIterator(root) {
            this.root = root;
            this._next = root;
        }

        NodeIterator.prototype = {
            _current: null,

            hasNext: function() {
                return !!this._next;
            },

            next: function() {
                var n = this._current = this._next;
                var child, next;
                if (this._current) {
                    child = n.firstChild;
                    if (child) {
                        this._next = child;
                    } else {
                        next = null;
                        while ((n !== this.root) && !(next = n.nextSibling)) {
                            n = n.parentNode;
                        }
                        this._next = next;
                    }
                }
                return this._current;
            },

            detach: function() {
                this._current = this._next = this.root = null;
            }
        };

        function createIterator(root) {
            return new NodeIterator(root);
        }

        function DomPosition(node, offset) {
            this.node = node;
            this.offset = offset;
        }

        DomPosition.prototype = {
            equals: function(pos) {
                return !!pos && this.node === pos.node && this.offset == pos.offset;
            },

            inspect: function() {
                return "[DomPosition(" + inspectNode(this.node) + ":" + this.offset + ")]";
            },

            toString: function() {
                return this.inspect();
            }
        };

        function DOMException(codeName) {
            this.code = this[codeName];
            this.codeName = codeName;
            this.message = "DOMException: " + this.codeName;
        }

        DOMException.prototype = {
            INDEX_SIZE_ERR: 1,
            HIERARCHY_REQUEST_ERR: 3,
            WRONG_DOCUMENT_ERR: 4,
            NO_MODIFICATION_ALLOWED_ERR: 7,
            NOT_FOUND_ERR: 8,
            NOT_SUPPORTED_ERR: 9,
            INVALID_STATE_ERR: 11,
            INVALID_NODE_TYPE_ERR: 24
        };

        DOMException.prototype.toString = function() {
            return this.message;
        };

        api.dom = {
            arrayContains: arrayContains,
            isHtmlNamespace: isHtmlNamespace,
            parentElement: parentElement,
            getNodeIndex: getNodeIndex,
            getNodeLength: getNodeLength,
            getCommonAncestor: getCommonAncestor,
            isAncestorOf: isAncestorOf,
            isOrIsAncestorOf: isOrIsAncestorOf,
            getClosestAncestorIn: getClosestAncestorIn,
            isCharacterDataNode: isCharacterDataNode,
            isTextOrCommentNode: isTextOrCommentNode,
            insertAfter: insertAfter,
            splitDataNode: splitDataNode,
            getDocument: getDocument,
            getWindow: getWindow,
            getIframeWindow: getIframeWindow,
            getIframeDocument: getIframeDocument,
            getBody: getBody,
            isWindow: isWindow,
            getContentDocument: getContentDocument,
            getRootContainer: getRootContainer,
            comparePoints: comparePoints,
            isBrokenNode: isBrokenNode,
            inspectNode: inspectNode,
            getComputedStyleProperty: getComputedStyleProperty,
            createTestElement: createTestElement,
            removeNode: removeNode,
            fragmentFromNodeChildren: fragmentFromNodeChildren,
            createIterator: createIterator,
            DomPosition: DomPosition
        };

        api.DOMException = DOMException;
    });

    /*----------------------------------------------------------------------------------------------------------------*/

    // Pure JavaScript implementation of DOM Range
    api.createCoreModule("DomRange", ["DomUtil"], function(api, module) {
        var dom = api.dom;
        var util = api.util;
        var DomPosition = dom.DomPosition;
        var DOMException = api.DOMException;

        var isCharacterDataNode = dom.isCharacterDataNode;
        var getNodeIndex = dom.getNodeIndex;
        var isOrIsAncestorOf = dom.isOrIsAncestorOf;
        var getDocument = dom.getDocument;
        var comparePoints = dom.comparePoints;
        var splitDataNode = dom.splitDataNode;
        var getClosestAncestorIn = dom.getClosestAncestorIn;
        var getNodeLength = dom.getNodeLength;
        var arrayContains = dom.arrayContains;
        var getRootContainer = dom.getRootContainer;
        var crashyTextNodes = api.features.crashyTextNodes;

        var removeNode = dom.removeNode;

        /*----------------------------------------------------------------------------------------------------------------*/

        // Utility functions

        function isNonTextPartiallySelected(node, range) {
            return (node.nodeType != 3) &&
                   (isOrIsAncestorOf(node, range.startContainer) || isOrIsAncestorOf(node, range.endContainer));
        }

        function getRangeDocument(range) {
            return range.document || getDocument(range.startContainer);
        }

        function getRangeRoot(range) {
            return getRootContainer(range.startContainer);
        }

        function getBoundaryBeforeNode(node) {
            return new DomPosition(node.parentNode, getNodeIndex(node));
        }

        function getBoundaryAfterNode(node) {
            return new DomPosition(node.parentNode, getNodeIndex(node) + 1);
        }

        function insertNodeAtPosition(node, n, o) {
            var firstNodeInserted = node.nodeType == 11 ? node.firstChild : node;
            if (isCharacterDataNode(n)) {
                if (o == n.length) {
                    dom.insertAfter(node, n);
                } else {
                    n.parentNode.insertBefore(node, o == 0 ? n : splitDataNode(n, o));
                }
            } else if (o >= n.childNodes.length) {
                n.appendChild(node);
            } else {
                n.insertBefore(node, n.childNodes[o]);
            }
            return firstNodeInserted;
        }

        function rangesIntersect(rangeA, rangeB, touchingIsIntersecting) {
            assertRangeValid(rangeA);
            assertRangeValid(rangeB);

            if (getRangeDocument(rangeB) != getRangeDocument(rangeA)) {
                throw new DOMException("WRONG_DOCUMENT_ERR");
            }

            var startComparison = comparePoints(rangeA.startContainer, rangeA.startOffset, rangeB.endContainer, rangeB.endOffset),
                endComparison = comparePoints(rangeA.endContainer, rangeA.endOffset, rangeB.startContainer, rangeB.startOffset);

            return touchingIsIntersecting ? startComparison <= 0 && endComparison >= 0 : startComparison < 0 && endComparison > 0;
        }

        function cloneSubtree(iterator) {
            var partiallySelected;
            for (var node, frag = getRangeDocument(iterator.range).createDocumentFragment(), subIterator; node = iterator.next(); ) {
                partiallySelected = iterator.isPartiallySelectedSubtree();
                node = node.cloneNode(!partiallySelected);
                if (partiallySelected) {
                    subIterator = iterator.getSubtreeIterator();
                    node.appendChild(cloneSubtree(subIterator));
                    subIterator.detach();
                }

                if (node.nodeType == 10) { // DocumentType
                    throw new DOMException("HIERARCHY_REQUEST_ERR");
                }
                frag.appendChild(node);
            }
            return frag;
        }

        function iterateSubtree(rangeIterator, func, iteratorState) {
            var it, n;
            iteratorState = iteratorState || { stop: false };
            for (var node, subRangeIterator; node = rangeIterator.next(); ) {
                if (rangeIterator.isPartiallySelectedSubtree()) {
                    if (func(node) === false) {
                        iteratorState.stop = true;
                        return;
                    } else {
                        // The node is partially selected by the Range, so we can use a new RangeIterator on the portion of
                        // the node selected by the Range.
                        subRangeIterator = rangeIterator.getSubtreeIterator();
                        iterateSubtree(subRangeIterator, func, iteratorState);
                        subRangeIterator.detach();
                        if (iteratorState.stop) {
                            return;
                        }
                    }
                } else {
                    // The whole node is selected, so we can use efficient DOM iteration to iterate over the node and its
                    // descendants
                    it = dom.createIterator(node);
                    while ( (n = it.next()) ) {
                        if (func(n) === false) {
                            iteratorState.stop = true;
                            return;
                        }
                    }
                }
            }
        }

        function deleteSubtree(iterator) {
            var subIterator;
            while (iterator.next()) {
                if (iterator.isPartiallySelectedSubtree()) {
                    subIterator = iterator.getSubtreeIterator();
                    deleteSubtree(subIterator);
                    subIterator.detach();
                } else {
                    iterator.remove();
                }
            }
        }

        function extractSubtree(iterator) {
            for (var node, frag = getRangeDocument(iterator.range).createDocumentFragment(), subIterator; node = iterator.next(); ) {

                if (iterator.isPartiallySelectedSubtree()) {
                    node = node.cloneNode(false);
                    subIterator = iterator.getSubtreeIterator();
                    node.appendChild(extractSubtree(subIterator));
                    subIterator.detach();
                } else {
                    iterator.remove();
                }
                if (node.nodeType == 10) { // DocumentType
                    throw new DOMException("HIERARCHY_REQUEST_ERR");
                }
                frag.appendChild(node);
            }
            return frag;
        }

        function getNodesInRange(range, nodeTypes, filter) {
            var filterNodeTypes = !!(nodeTypes && nodeTypes.length), regex;
            var filterExists = !!filter;
            if (filterNodeTypes) {
                regex = new RegExp("^(" + nodeTypes.join("|") + ")$");
            }

            var nodes = [];
            iterateSubtree(new RangeIterator(range, false), function(node) {
                if (filterNodeTypes && !regex.test(node.nodeType)) {
                    return;
                }
                if (filterExists && !filter(node)) {
                    return;
                }
                // Don't include a boundary container if it is a character data node and the range does not contain any
                // of its character data. See issue 190.
                var sc = range.startContainer;
                if (node == sc && isCharacterDataNode(sc) && range.startOffset == sc.length) {
                    return;
                }

                var ec = range.endContainer;
                if (node == ec && isCharacterDataNode(ec) && range.endOffset == 0) {
                    return;
                }

                nodes.push(node);
            });
            return nodes;
        }

        function inspect(range) {
            var name = (typeof range.getName == "undefined") ? "Range" : range.getName();
            return "[" + name + "(" + dom.inspectNode(range.startContainer) + ":" + range.startOffset + ", " +
                    dom.inspectNode(range.endContainer) + ":" + range.endOffset + ")]";
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // RangeIterator code partially borrows from IERange by Tim Ryan (http://github.com/timcameronryan/IERange)

        function RangeIterator(range, clonePartiallySelectedTextNodes) {
            this.range = range;
            this.clonePartiallySelectedTextNodes = clonePartiallySelectedTextNodes;


            if (!range.collapsed) {
                this.sc = range.startContainer;
                this.so = range.startOffset;
                this.ec = range.endContainer;
                this.eo = range.endOffset;
                var root = range.commonAncestorContainer;

                if (this.sc === this.ec && isCharacterDataNode(this.sc)) {
                    this.isSingleCharacterDataNode = true;
                    this._first = this._last = this._next = this.sc;
                } else {
                    this._first = this._next = (this.sc === root && !isCharacterDataNode(this.sc)) ?
                        this.sc.childNodes[this.so] : getClosestAncestorIn(this.sc, root, true);
                    this._last = (this.ec === root && !isCharacterDataNode(this.ec)) ?
                        this.ec.childNodes[this.eo - 1] : getClosestAncestorIn(this.ec, root, true);
                }
            }
        }

        RangeIterator.prototype = {
            _current: null,
            _next: null,
            _first: null,
            _last: null,
            isSingleCharacterDataNode: false,

            reset: function() {
                this._current = null;
                this._next = this._first;
            },

            hasNext: function() {
                return !!this._next;
            },

            next: function() {
                // Move to next node
                var current = this._current = this._next;
                if (current) {
                    this._next = (current !== this._last) ? current.nextSibling : null;

                    // Check for partially selected text nodes
                    if (isCharacterDataNode(current) && this.clonePartiallySelectedTextNodes) {
                        if (current === this.ec) {
                            (current = current.cloneNode(true)).deleteData(this.eo, current.length - this.eo);
                        }
                        if (this._current === this.sc) {
                            (current = current.cloneNode(true)).deleteData(0, this.so);
                        }
                    }
                }

                return current;
            },

            remove: function() {
                var current = this._current, start, end;

                if (isCharacterDataNode(current) && (current === this.sc || current === this.ec)) {
                    start = (current === this.sc) ? this.so : 0;
                    end = (current === this.ec) ? this.eo : current.length;
                    if (start != end) {
                        current.deleteData(start, end - start);
                    }
                } else {
                    if (current.parentNode) {
                        removeNode(current);
                    } else {
                    }
                }
            },

            // Checks if the current node is partially selected
            isPartiallySelectedSubtree: function() {
                var current = this._current;
                return isNonTextPartiallySelected(current, this.range);
            },

            getSubtreeIterator: function() {
                var subRange;
                if (this.isSingleCharacterDataNode) {
                    subRange = this.range.cloneRange();
                    subRange.collapse(false);
                } else {
                    subRange = new Range(getRangeDocument(this.range));
                    var current = this._current;
                    var startContainer = current, startOffset = 0, endContainer = current, endOffset = getNodeLength(current);

                    if (isOrIsAncestorOf(current, this.sc)) {
                        startContainer = this.sc;
                        startOffset = this.so;
                    }
                    if (isOrIsAncestorOf(current, this.ec)) {
                        endContainer = this.ec;
                        endOffset = this.eo;
                    }

                    updateBoundaries(subRange, startContainer, startOffset, endContainer, endOffset);
                }
                return new RangeIterator(subRange, this.clonePartiallySelectedTextNodes);
            },

            detach: function() {
                this.range = this._current = this._next = this._first = this._last = this.sc = this.so = this.ec = this.eo = null;
            }
        };

        /*----------------------------------------------------------------------------------------------------------------*/

        var beforeAfterNodeTypes = [1, 3, 4, 5, 7, 8, 10];
        var rootContainerNodeTypes = [2, 9, 11];
        var readonlyNodeTypes = [5, 6, 10, 12];
        var insertableNodeTypes = [1, 3, 4, 5, 7, 8, 10, 11];
        var surroundNodeTypes = [1, 3, 4, 5, 7, 8];

        function createAncestorFinder(nodeTypes) {
            return function(node, selfIsAncestor) {
                var t, n = selfIsAncestor ? node : node.parentNode;
                while (n) {
                    t = n.nodeType;
                    if (arrayContains(nodeTypes, t)) {
                        return n;
                    }
                    n = n.parentNode;
                }
                return null;
            };
        }

        var getDocumentOrFragmentContainer = createAncestorFinder( [9, 11] );
        var getReadonlyAncestor = createAncestorFinder(readonlyNodeTypes);
        var getDocTypeNotationEntityAncestor = createAncestorFinder( [6, 10, 12] );

        function assertNoDocTypeNotationEntityAncestor(node, allowSelf) {
            if (getDocTypeNotationEntityAncestor(node, allowSelf)) {
                throw new DOMException("INVALID_NODE_TYPE_ERR");
            }
        }

        function assertValidNodeType(node, invalidTypes) {
            if (!arrayContains(invalidTypes, node.nodeType)) {
                throw new DOMException("INVALID_NODE_TYPE_ERR");
            }
        }

        function assertValidOffset(node, offset) {
            if (offset < 0 || offset > (isCharacterDataNode(node) ? node.length : node.childNodes.length)) {
                throw new DOMException("INDEX_SIZE_ERR");
            }
        }

        function assertSameDocumentOrFragment(node1, node2) {
            if (getDocumentOrFragmentContainer(node1, true) !== getDocumentOrFragmentContainer(node2, true)) {
                throw new DOMException("WRONG_DOCUMENT_ERR");
            }
        }

        function assertNodeNotReadOnly(node) {
            if (getReadonlyAncestor(node, true)) {
                throw new DOMException("NO_MODIFICATION_ALLOWED_ERR");
            }
        }

        function assertNode(node, codeName) {
            if (!node) {
                throw new DOMException(codeName);
            }
        }

        function isValidOffset(node, offset) {
            return offset <= (isCharacterDataNode(node) ? node.length : node.childNodes.length);
        }

        function isRangeValid(range) {
            return (!!range.startContainer && !!range.endContainer &&
                    !(crashyTextNodes && (dom.isBrokenNode(range.startContainer) || dom.isBrokenNode(range.endContainer))) &&
                    getRootContainer(range.startContainer) == getRootContainer(range.endContainer) &&
                    isValidOffset(range.startContainer, range.startOffset) &&
                    isValidOffset(range.endContainer, range.endOffset));
        }

        function assertRangeValid(range) {
            if (!isRangeValid(range)) {
                throw new Error("Range error: Range is not valid. This usually happens after DOM mutation. Range: (" + range.inspect() + ")");
            }
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Test the browser's innerHTML support to decide how to implement createContextualFragment
        var styleEl = document.createElement("style");
        var htmlParsingConforms = false;
        try {
            styleEl.innerHTML = "<b>x</b>";
            htmlParsingConforms = (styleEl.firstChild.nodeType == 3); // Opera incorrectly creates an element node
        } catch (e) {
            // IE 6 and 7 throw
        }

        api.features.htmlParsingConforms = htmlParsingConforms;

        var createContextualFragment = htmlParsingConforms ?

            // Implementation as per HTML parsing spec, trusting in the browser's implementation of innerHTML. See
            // discussion and base code for this implementation at issue 67.
            // Spec: http://html5.org/specs/dom-parsing.html#extensions-to-the-range-interface
            // Thanks to Aleks Williams.
            function(fragmentStr) {
                // "Let node the context object's start's node."
                var node = this.startContainer;
                var doc = getDocument(node);

                // "If the context object's start's node is null, raise an INVALID_STATE_ERR
                // exception and abort these steps."
                if (!node) {
                    throw new DOMException("INVALID_STATE_ERR");
                }

                // "Let element be as follows, depending on node's interface:"
                // Document, Document Fragment: null
                var el = null;

                // "Element: node"
                if (node.nodeType == 1) {
                    el = node;

                // "Text, Comment: node's parentElement"
                } else if (isCharacterDataNode(node)) {
                    el = dom.parentElement(node);
                }

                // "If either element is null or element's ownerDocument is an HTML document
                // and element's local name is "html" and element's namespace is the HTML
                // namespace"
                if (el === null || (
                    el.nodeName == "HTML" &&
                    dom.isHtmlNamespace(getDocument(el).documentElement) &&
                    dom.isHtmlNamespace(el)
                )) {

                // "let element be a new Element with "body" as its local name and the HTML
                // namespace as its namespace.""
                    el = doc.createElement("body");
                } else {
                    el = el.cloneNode(false);
                }

                // "If the node's document is an HTML document: Invoke the HTML fragment parsing algorithm."
                // "If the node's document is an XML document: Invoke the XML fragment parsing algorithm."
                // "In either case, the algorithm must be invoked with fragment as the input
                // and element as the context element."
                el.innerHTML = fragmentStr;

                // "If this raises an exception, then abort these steps. Otherwise, let new
                // children be the nodes returned."

                // "Let fragment be a new DocumentFragment."
                // "Append all new children to fragment."
                // "Return fragment."
                return dom.fragmentFromNodeChildren(el);
            } :

            // In this case, innerHTML cannot be trusted, so fall back to a simpler, non-conformant implementation that
            // previous versions of Rangy used (with the exception of using a body element rather than a div)
            function(fragmentStr) {
                var doc = getRangeDocument(this);
                var el = doc.createElement("body");
                el.innerHTML = fragmentStr;

                return dom.fragmentFromNodeChildren(el);
            };

        function splitRangeBoundaries(range, positionsToPreserve) {
            assertRangeValid(range);

            var sc = range.startContainer, so = range.startOffset, ec = range.endContainer, eo = range.endOffset;
            var startEndSame = (sc === ec);

            if (isCharacterDataNode(ec) && eo > 0 && eo < ec.length) {
                splitDataNode(ec, eo, positionsToPreserve);
            }

            if (isCharacterDataNode(sc) && so > 0 && so < sc.length) {
                sc = splitDataNode(sc, so, positionsToPreserve);
                if (startEndSame) {
                    eo -= so;
                    ec = sc;
                } else if (ec == sc.parentNode && eo >= getNodeIndex(sc)) {
                    eo++;
                }
                so = 0;
            }
            range.setStartAndEnd(sc, so, ec, eo);
        }

        function rangeToHtml(range) {
            assertRangeValid(range);
            var container = range.commonAncestorContainer.parentNode.cloneNode(false);
            container.appendChild( range.cloneContents() );
            return container.innerHTML;
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        var rangeProperties = ["startContainer", "startOffset", "endContainer", "endOffset", "collapsed",
            "commonAncestorContainer"];

        var s2s = 0, s2e = 1, e2e = 2, e2s = 3;
        var n_b = 0, n_a = 1, n_b_a = 2, n_i = 3;

        util.extend(api.rangePrototype, {
            compareBoundaryPoints: function(how, range) {
                assertRangeValid(this);
                assertSameDocumentOrFragment(this.startContainer, range.startContainer);

                var nodeA, offsetA, nodeB, offsetB;
                var prefixA = (how == e2s || how == s2s) ? "start" : "end";
                var prefixB = (how == s2e || how == s2s) ? "start" : "end";
                nodeA = this[prefixA + "Container"];
                offsetA = this[prefixA + "Offset"];
                nodeB = range[prefixB + "Container"];
                offsetB = range[prefixB + "Offset"];
                return comparePoints(nodeA, offsetA, nodeB, offsetB);
            },

            insertNode: function(node) {
                assertRangeValid(this);
                assertValidNodeType(node, insertableNodeTypes);
                assertNodeNotReadOnly(this.startContainer);

                if (isOrIsAncestorOf(node, this.startContainer)) {
                    throw new DOMException("HIERARCHY_REQUEST_ERR");
                }

                // No check for whether the container of the start of the Range is of a type that does not allow
                // children of the type of node: the browser's DOM implementation should do this for us when we attempt
                // to add the node

                var firstNodeInserted = insertNodeAtPosition(node, this.startContainer, this.startOffset);
                this.setStartBefore(firstNodeInserted);
            },

            cloneContents: function() {
                assertRangeValid(this);

                var clone, frag;
                if (this.collapsed) {
                    return getRangeDocument(this).createDocumentFragment();
                } else {
                    if (this.startContainer === this.endContainer && isCharacterDataNode(this.startContainer)) {
                        clone = this.startContainer.cloneNode(true);
                        clone.data = clone.data.slice(this.startOffset, this.endOffset);
                        frag = getRangeDocument(this).createDocumentFragment();
                        frag.appendChild(clone);
                        return frag;
                    } else {
                        var iterator = new RangeIterator(this, true);
                        clone = cloneSubtree(iterator);
                        iterator.detach();
                    }
                    return clone;
                }
            },

            canSurroundContents: function() {
                assertRangeValid(this);
                assertNodeNotReadOnly(this.startContainer);
                assertNodeNotReadOnly(this.endContainer);

                // Check if the contents can be surrounded. Specifically, this means whether the range partially selects
                // no non-text nodes.
                var iterator = new RangeIterator(this, true);
                var boundariesInvalid = (iterator._first && (isNonTextPartiallySelected(iterator._first, this)) ||
                        (iterator._last && isNonTextPartiallySelected(iterator._last, this)));
                iterator.detach();
                return !boundariesInvalid;
            },

            surroundContents: function(node) {
                assertValidNodeType(node, surroundNodeTypes);

                if (!this.canSurroundContents()) {
                    throw new DOMException("INVALID_STATE_ERR");
                }

                // Extract the contents
                var content = this.extractContents();

                // Clear the children of the node
                if (node.hasChildNodes()) {
                    while (node.lastChild) {
                        node.removeChild(node.lastChild);
                    }
                }

                // Insert the new node and add the extracted contents
                insertNodeAtPosition(node, this.startContainer, this.startOffset);
                node.appendChild(content);

                this.selectNode(node);
            },

            cloneRange: function() {
                assertRangeValid(this);
                var range = new Range(getRangeDocument(this));
                var i = rangeProperties.length, prop;
                while (i--) {
                    prop = rangeProperties[i];
                    range[prop] = this[prop];
                }
                return range;
            },

            toString: function() {
                assertRangeValid(this);
                var sc = this.startContainer;
                if (sc === this.endContainer && isCharacterDataNode(sc)) {
                    return (sc.nodeType == 3 || sc.nodeType == 4) ? sc.data.slice(this.startOffset, this.endOffset) : "";
                } else {
                    var textParts = [], iterator = new RangeIterator(this, true);
                    iterateSubtree(iterator, function(node) {
                        // Accept only text or CDATA nodes, not comments
                        if (node.nodeType == 3 || node.nodeType == 4) {
                            textParts.push(node.data);
                        }
                    });
                    iterator.detach();
                    return textParts.join("");
                }
            },

            // The methods below are all non-standard. The following batch were introduced by Mozilla but have since
            // been removed from Mozilla.

            compareNode: function(node) {
                assertRangeValid(this);

                var parent = node.parentNode;
                var nodeIndex = getNodeIndex(node);

                if (!parent) {
                    throw new DOMException("NOT_FOUND_ERR");
                }

                var startComparison = this.comparePoint(parent, nodeIndex),
                    endComparison = this.comparePoint(parent, nodeIndex + 1);

                if (startComparison < 0) { // Node starts before
                    return (endComparison > 0) ? n_b_a : n_b;
                } else {
                    return (endComparison > 0) ? n_a : n_i;
                }
            },

            comparePoint: function(node, offset) {
                assertRangeValid(this);
                assertNode(node, "HIERARCHY_REQUEST_ERR");
                assertSameDocumentOrFragment(node, this.startContainer);

                if (comparePoints(node, offset, this.startContainer, this.startOffset) < 0) {
                    return -1;
                } else if (comparePoints(node, offset, this.endContainer, this.endOffset) > 0) {
                    return 1;
                }
                return 0;
            },

            createContextualFragment: createContextualFragment,

            toHtml: function() {
                return rangeToHtml(this);
            },

            // touchingIsIntersecting determines whether this method considers a node that borders a range intersects
            // with it (as in WebKit) or not (as in Gecko pre-1.9, and the default)
            intersectsNode: function(node, touchingIsIntersecting) {
                assertRangeValid(this);
                if (getRootContainer(node) != getRangeRoot(this)) {
                    return false;
                }

                var parent = node.parentNode, offset = getNodeIndex(node);
                if (!parent) {
                    return true;
                }

                var startComparison = comparePoints(parent, offset, this.endContainer, this.endOffset),
                    endComparison = comparePoints(parent, offset + 1, this.startContainer, this.startOffset);

                return touchingIsIntersecting ? startComparison <= 0 && endComparison >= 0 : startComparison < 0 && endComparison > 0;
            },

            isPointInRange: function(node, offset) {
                assertRangeValid(this);
                assertNode(node, "HIERARCHY_REQUEST_ERR");
                assertSameDocumentOrFragment(node, this.startContainer);

                return (comparePoints(node, offset, this.startContainer, this.startOffset) >= 0) &&
                       (comparePoints(node, offset, this.endContainer, this.endOffset) <= 0);
            },

            // The methods below are non-standard and invented by me.

            // Sharing a boundary start-to-end or end-to-start does not count as intersection.
            intersectsRange: function(range) {
                return rangesIntersect(this, range, false);
            },

            // Sharing a boundary start-to-end or end-to-start does count as intersection.
            intersectsOrTouchesRange: function(range) {
                return rangesIntersect(this, range, true);
            },

            intersection: function(range) {
                if (this.intersectsRange(range)) {
                    var startComparison = comparePoints(this.startContainer, this.startOffset, range.startContainer, range.startOffset),
                        endComparison = comparePoints(this.endContainer, this.endOffset, range.endContainer, range.endOffset);

                    var intersectionRange = this.cloneRange();
                    if (startComparison == -1) {
                        intersectionRange.setStart(range.startContainer, range.startOffset);
                    }
                    if (endComparison == 1) {
                        intersectionRange.setEnd(range.endContainer, range.endOffset);
                    }
                    return intersectionRange;
                }
                return null;
            },

            union: function(range) {
                if (this.intersectsOrTouchesRange(range)) {
                    var unionRange = this.cloneRange();
                    if (comparePoints(range.startContainer, range.startOffset, this.startContainer, this.startOffset) == -1) {
                        unionRange.setStart(range.startContainer, range.startOffset);
                    }
                    if (comparePoints(range.endContainer, range.endOffset, this.endContainer, this.endOffset) == 1) {
                        unionRange.setEnd(range.endContainer, range.endOffset);
                    }
                    return unionRange;
                } else {
                    throw new DOMException("Ranges do not intersect");
                }
            },

            containsNode: function(node, allowPartial) {
                if (allowPartial) {
                    return this.intersectsNode(node, false);
                } else {
                    return this.compareNode(node) == n_i;
                }
            },

            containsNodeContents: function(node) {
                return this.comparePoint(node, 0) >= 0 && this.comparePoint(node, getNodeLength(node)) <= 0;
            },

            containsRange: function(range) {
                var intersection = this.intersection(range);
                return intersection !== null && range.equals(intersection);
            },

            containsNodeText: function(node) {
                var nodeRange = this.cloneRange();
                nodeRange.selectNode(node);
                var textNodes = nodeRange.getNodes([3]);
                if (textNodes.length > 0) {
                    nodeRange.setStart(textNodes[0], 0);
                    var lastTextNode = textNodes.pop();
                    nodeRange.setEnd(lastTextNode, lastTextNode.length);
                    return this.containsRange(nodeRange);
                } else {
                    return this.containsNodeContents(node);
                }
            },

            getNodes: function(nodeTypes, filter) {
                assertRangeValid(this);
                return getNodesInRange(this, nodeTypes, filter);
            },

            getDocument: function() {
                return getRangeDocument(this);
            },

            collapseBefore: function(node) {
                this.setEndBefore(node);
                this.collapse(false);
            },

            collapseAfter: function(node) {
                this.setStartAfter(node);
                this.collapse(true);
            },

            getBookmark: function(containerNode) {
                var doc = getRangeDocument(this);
                var preSelectionRange = api.createRange(doc);
                containerNode = containerNode || dom.getBody(doc);
                preSelectionRange.selectNodeContents(containerNode);
                var range = this.intersection(preSelectionRange);
                var start = 0, end = 0;
                if (range) {
                    preSelectionRange.setEnd(range.startContainer, range.startOffset);
                    start = preSelectionRange.toString().length;
                    end = start + range.toString().length;
                }

                return {
                    start: start,
                    end: end,
                    containerNode: containerNode
                };
            },

            moveToBookmark: function(bookmark) {
                var containerNode = bookmark.containerNode;
                var charIndex = 0;
                this.setStart(containerNode, 0);
                this.collapse(true);
                var nodeStack = [containerNode], node, foundStart = false, stop = false;
                var nextCharIndex, i, childNodes;

                while (!stop && (node = nodeStack.pop())) {
                    if (node.nodeType == 3) {
                        nextCharIndex = charIndex + node.length;
                        if (!foundStart && bookmark.start >= charIndex && bookmark.start <= nextCharIndex) {
                            this.setStart(node, bookmark.start - charIndex);
                            foundStart = true;
                        }
                        if (foundStart && bookmark.end >= charIndex && bookmark.end <= nextCharIndex) {
                            this.setEnd(node, bookmark.end - charIndex);
                            stop = true;
                        }
                        charIndex = nextCharIndex;
                    } else {
                        childNodes = node.childNodes;
                        i = childNodes.length;
                        while (i--) {
                            nodeStack.push(childNodes[i]);
                        }
                    }
                }
            },

            getName: function() {
                return "DomRange";
            },

            equals: function(range) {
                return Range.rangesEqual(this, range);
            },

            isValid: function() {
                return isRangeValid(this);
            },

            inspect: function() {
                return inspect(this);
            },

            detach: function() {
                // In DOM4, detach() is now a no-op.
            }
        });

        function copyComparisonConstantsToObject(obj) {
            obj.START_TO_START = s2s;
            obj.START_TO_END = s2e;
            obj.END_TO_END = e2e;
            obj.END_TO_START = e2s;

            obj.NODE_BEFORE = n_b;
            obj.NODE_AFTER = n_a;
            obj.NODE_BEFORE_AND_AFTER = n_b_a;
            obj.NODE_INSIDE = n_i;
        }

        function copyComparisonConstants(constructor) {
            copyComparisonConstantsToObject(constructor);
            copyComparisonConstantsToObject(constructor.prototype);
        }

        function createRangeContentRemover(remover, boundaryUpdater) {
            return function() {
                assertRangeValid(this);

                var sc = this.startContainer, so = this.startOffset, root = this.commonAncestorContainer;

                var iterator = new RangeIterator(this, true);

                // Work out where to position the range after content removal
                var node, boundary;
                if (sc !== root) {
                    node = getClosestAncestorIn(sc, root, true);
                    boundary = getBoundaryAfterNode(node);
                    sc = boundary.node;
                    so = boundary.offset;
                }

                // Check none of the range is read-only
                iterateSubtree(iterator, assertNodeNotReadOnly);

                iterator.reset();

                // Remove the content
                var returnValue = remover(iterator);
                iterator.detach();

                // Move to the new position
                boundaryUpdater(this, sc, so, sc, so);

                return returnValue;
            };
        }

        function createPrototypeRange(constructor, boundaryUpdater) {
            function createBeforeAfterNodeSetter(isBefore, isStart) {
                return function(node) {
                    assertValidNodeType(node, beforeAfterNodeTypes);
                    assertValidNodeType(getRootContainer(node), rootContainerNodeTypes);

                    var boundary = (isBefore ? getBoundaryBeforeNode : getBoundaryAfterNode)(node);
                    (isStart ? setRangeStart : setRangeEnd)(this, boundary.node, boundary.offset);
                };
            }

            function setRangeStart(range, node, offset) {
                var ec = range.endContainer, eo = range.endOffset;
                if (node !== range.startContainer || offset !== range.startOffset) {
                    // Check the root containers of the range and the new boundary, and also check whether the new boundary
                    // is after the current end. In either case, collapse the range to the new position
                    if (getRootContainer(node) != getRootContainer(ec) || comparePoints(node, offset, ec, eo) == 1) {
                        ec = node;
                        eo = offset;
                    }
                    boundaryUpdater(range, node, offset, ec, eo);
                }
            }

            function setRangeEnd(range, node, offset) {
                var sc = range.startContainer, so = range.startOffset;
                if (node !== range.endContainer || offset !== range.endOffset) {
                    // Check the root containers of the range and the new boundary, and also check whether the new boundary
                    // is after the current end. In either case, collapse the range to the new position
                    if (getRootContainer(node) != getRootContainer(sc) || comparePoints(node, offset, sc, so) == -1) {
                        sc = node;
                        so = offset;
                    }
                    boundaryUpdater(range, sc, so, node, offset);
                }
            }

            // Set up inheritance
            var F = function() {};
            F.prototype = api.rangePrototype;
            constructor.prototype = new F();

            util.extend(constructor.prototype, {
                setStart: function(node, offset) {
                    assertNoDocTypeNotationEntityAncestor(node, true);
                    assertValidOffset(node, offset);

                    setRangeStart(this, node, offset);
                },

                setEnd: function(node, offset) {
                    assertNoDocTypeNotationEntityAncestor(node, true);
                    assertValidOffset(node, offset);

                    setRangeEnd(this, node, offset);
                },

                /**
                 * Convenience method to set a range's start and end boundaries. Overloaded as follows:
                 * - Two parameters (node, offset) creates a collapsed range at that position
                 * - Three parameters (node, startOffset, endOffset) creates a range contained with node starting at
                 *   startOffset and ending at endOffset
                 * - Four parameters (startNode, startOffset, endNode, endOffset) creates a range starting at startOffset in
                 *   startNode and ending at endOffset in endNode
                 */
                setStartAndEnd: function() {
                    var args = arguments;
                    var sc = args[0], so = args[1], ec = sc, eo = so;

                    switch (args.length) {
                        case 3:
                            eo = args[2];
                            break;
                        case 4:
                            ec = args[2];
                            eo = args[3];
                            break;
                    }

                    boundaryUpdater(this, sc, so, ec, eo);
                },

                setBoundary: function(node, offset, isStart) {
                    this["set" + (isStart ? "Start" : "End")](node, offset);
                },

                setStartBefore: createBeforeAfterNodeSetter(true, true),
                setStartAfter: createBeforeAfterNodeSetter(false, true),
                setEndBefore: createBeforeAfterNodeSetter(true, false),
                setEndAfter: createBeforeAfterNodeSetter(false, false),

                collapse: function(isStart) {
                    assertRangeValid(this);
                    if (isStart) {
                        boundaryUpdater(this, this.startContainer, this.startOffset, this.startContainer, this.startOffset);
                    } else {
                        boundaryUpdater(this, this.endContainer, this.endOffset, this.endContainer, this.endOffset);
                    }
                },

                selectNodeContents: function(node) {
                    assertNoDocTypeNotationEntityAncestor(node, true);

                    boundaryUpdater(this, node, 0, node, getNodeLength(node));
                },

                selectNode: function(node) {
                    assertNoDocTypeNotationEntityAncestor(node, false);
                    assertValidNodeType(node, beforeAfterNodeTypes);

                    var start = getBoundaryBeforeNode(node), end = getBoundaryAfterNode(node);
                    boundaryUpdater(this, start.node, start.offset, end.node, end.offset);
                },

                extractContents: createRangeContentRemover(extractSubtree, boundaryUpdater),

                deleteContents: createRangeContentRemover(deleteSubtree, boundaryUpdater),

                canSurroundContents: function() {
                    assertRangeValid(this);
                    assertNodeNotReadOnly(this.startContainer);
                    assertNodeNotReadOnly(this.endContainer);

                    // Check if the contents can be surrounded. Specifically, this means whether the range partially selects
                    // no non-text nodes.
                    var iterator = new RangeIterator(this, true);
                    var boundariesInvalid = (iterator._first && isNonTextPartiallySelected(iterator._first, this) ||
                            (iterator._last && isNonTextPartiallySelected(iterator._last, this)));
                    iterator.detach();
                    return !boundariesInvalid;
                },

                splitBoundaries: function() {
                    splitRangeBoundaries(this);
                },

                splitBoundariesPreservingPositions: function(positionsToPreserve) {
                    splitRangeBoundaries(this, positionsToPreserve);
                },

                normalizeBoundaries: function() {
                    assertRangeValid(this);

                    var sc = this.startContainer, so = this.startOffset, ec = this.endContainer, eo = this.endOffset;

                    var mergeForward = function(node) {
                        var sibling = node.nextSibling;
                        if (sibling && sibling.nodeType == node.nodeType) {
                            ec = node;
                            eo = node.length;
                            node.appendData(sibling.data);
                            removeNode(sibling);
                        }
                    };

                    var mergeBackward = function(node) {
                        var sibling = node.previousSibling;
                        if (sibling && sibling.nodeType == node.nodeType) {
                            sc = node;
                            var nodeLength = node.length;
                            so = sibling.length;
                            node.insertData(0, sibling.data);
                            removeNode(sibling);
                            if (sc == ec) {
                                eo += so;
                                ec = sc;
                            } else if (ec == node.parentNode) {
                                var nodeIndex = getNodeIndex(node);
                                if (eo == nodeIndex) {
                                    ec = node;
                                    eo = nodeLength;
                                } else if (eo > nodeIndex) {
                                    eo--;
                                }
                            }
                        }
                    };

                    var normalizeStart = true;
                    var sibling;

                    if (isCharacterDataNode(ec)) {
                        if (eo == ec.length) {
                            mergeForward(ec);
                        } else if (eo == 0) {
                            sibling = ec.previousSibling;
                            if (sibling && sibling.nodeType == ec.nodeType) {
                                eo = sibling.length;
                                if (sc == ec) {
                                    normalizeStart = false;
                                }
                                sibling.appendData(ec.data);
                                removeNode(ec);
                                ec = sibling;
                            }
                        }
                    } else {
                        if (eo > 0) {
                            var endNode = ec.childNodes[eo - 1];
                            if (endNode && isCharacterDataNode(endNode)) {
                                mergeForward(endNode);
                            }
                        }
                        normalizeStart = !this.collapsed;
                    }

                    if (normalizeStart) {
                        if (isCharacterDataNode(sc)) {
                            if (so == 0) {
                                mergeBackward(sc);
                            } else if (so == sc.length) {
                                sibling = sc.nextSibling;
                                if (sibling && sibling.nodeType == sc.nodeType) {
                                    if (ec == sibling) {
                                        ec = sc;
                                        eo += sc.length;
                                    }
                                    sc.appendData(sibling.data);
                                    removeNode(sibling);
                                }
                            }
                        } else {
                            if (so < sc.childNodes.length) {
                                var startNode = sc.childNodes[so];
                                if (startNode && isCharacterDataNode(startNode)) {
                                    mergeBackward(startNode);
                                }
                            }
                        }
                    } else {
                        sc = ec;
                        so = eo;
                    }

                    boundaryUpdater(this, sc, so, ec, eo);
                },

                collapseToPoint: function(node, offset) {
                    assertNoDocTypeNotationEntityAncestor(node, true);
                    assertValidOffset(node, offset);
                    this.setStartAndEnd(node, offset);
                }
            });

            copyComparisonConstants(constructor);
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Updates commonAncestorContainer and collapsed after boundary change
        function updateCollapsedAndCommonAncestor(range) {
            range.collapsed = (range.startContainer === range.endContainer && range.startOffset === range.endOffset);
            range.commonAncestorContainer = range.collapsed ?
                range.startContainer : dom.getCommonAncestor(range.startContainer, range.endContainer);
        }

        function updateBoundaries(range, startContainer, startOffset, endContainer, endOffset) {
            range.startContainer = startContainer;
            range.startOffset = startOffset;
            range.endContainer = endContainer;
            range.endOffset = endOffset;
            range.document = dom.getDocument(startContainer);

            updateCollapsedAndCommonAncestor(range);
        }

        function Range(doc) {
            this.startContainer = doc;
            this.startOffset = 0;
            this.endContainer = doc;
            this.endOffset = 0;
            this.document = doc;
            updateCollapsedAndCommonAncestor(this);
        }

        createPrototypeRange(Range, updateBoundaries);

        util.extend(Range, {
            rangeProperties: rangeProperties,
            RangeIterator: RangeIterator,
            copyComparisonConstants: copyComparisonConstants,
            createPrototypeRange: createPrototypeRange,
            inspect: inspect,
            toHtml: rangeToHtml,
            getRangeDocument: getRangeDocument,
            rangesEqual: function(r1, r2) {
                return r1.startContainer === r2.startContainer &&
                    r1.startOffset === r2.startOffset &&
                    r1.endContainer === r2.endContainer &&
                    r1.endOffset === r2.endOffset;
            }
        });

        api.DomRange = Range;
    });

    /*----------------------------------------------------------------------------------------------------------------*/

    // Wrappers for the browser's native DOM Range and/or TextRange implementation
    api.createCoreModule("WrappedRange", ["DomRange"], function(api, module) {
        var WrappedRange, WrappedTextRange;
        var dom = api.dom;
        var util = api.util;
        var DomPosition = dom.DomPosition;
        var DomRange = api.DomRange;
        var getBody = dom.getBody;
        var getContentDocument = dom.getContentDocument;
        var isCharacterDataNode = dom.isCharacterDataNode;


        /*----------------------------------------------------------------------------------------------------------------*/

        if (api.features.implementsDomRange) {
            // This is a wrapper around the browser's native DOM Range. It has two aims:
            // - Provide workarounds for specific browser bugs
            // - provide convenient extensions, which are inherited from Rangy's DomRange

            (function() {
                var rangeProto;
                var rangeProperties = DomRange.rangeProperties;

                function updateRangeProperties(range) {
                    var i = rangeProperties.length, prop;
                    while (i--) {
                        prop = rangeProperties[i];
                        range[prop] = range.nativeRange[prop];
                    }
                    // Fix for broken collapsed property in IE 9.
                    range.collapsed = (range.startContainer === range.endContainer && range.startOffset === range.endOffset);
                }

                function updateNativeRange(range, startContainer, startOffset, endContainer, endOffset) {
                    var startMoved = (range.startContainer !== startContainer || range.startOffset != startOffset);
                    var endMoved = (range.endContainer !== endContainer || range.endOffset != endOffset);
                    var nativeRangeDifferent = !range.equals(range.nativeRange);

                    // Always set both boundaries for the benefit of IE9 (see issue 35)
                    if (startMoved || endMoved || nativeRangeDifferent) {
                        range.setEnd(endContainer, endOffset);
                        range.setStart(startContainer, startOffset);
                    }
                }

                var createBeforeAfterNodeSetter;

                WrappedRange = function(range) {
                    if (!range) {
                        throw module.createError("WrappedRange: Range must be specified");
                    }
                    this.nativeRange = range;
                    updateRangeProperties(this);
                };

                DomRange.createPrototypeRange(WrappedRange, updateNativeRange);

                rangeProto = WrappedRange.prototype;

                rangeProto.selectNode = function(node) {
                    this.nativeRange.selectNode(node);
                    updateRangeProperties(this);
                };

                rangeProto.cloneContents = function() {
                    return this.nativeRange.cloneContents();
                };

                // Due to a long-standing Firefox bug that I have not been able to find a reliable way to detect,
                // insertNode() is never delegated to the native range.

                rangeProto.surroundContents = function(node) {
                    this.nativeRange.surroundContents(node);
                    updateRangeProperties(this);
                };

                rangeProto.collapse = function(isStart) {
                    this.nativeRange.collapse(isStart);
                    updateRangeProperties(this);
                };

                rangeProto.cloneRange = function() {
                    return new WrappedRange(this.nativeRange.cloneRange());
                };

                rangeProto.refresh = function() {
                    updateRangeProperties(this);
                };

                rangeProto.toString = function() {
                    return this.nativeRange.toString();
                };

                // Create test range and node for feature detection

                var testTextNode = document.createTextNode("test");
                getBody(document).appendChild(testTextNode);
                var range = document.createRange();

                /*--------------------------------------------------------------------------------------------------------*/

                // Test for Firefox 2 bug that prevents moving the start of a Range to a point after its current end and
                // correct for it

                range.setStart(testTextNode, 0);
                range.setEnd(testTextNode, 0);

                try {
                    range.setStart(testTextNode, 1);

                    rangeProto.setStart = function(node, offset) {
                        this.nativeRange.setStart(node, offset);
                        updateRangeProperties(this);
                    };

                    rangeProto.setEnd = function(node, offset) {
                        this.nativeRange.setEnd(node, offset);
                        updateRangeProperties(this);
                    };

                    createBeforeAfterNodeSetter = function(name) {
                        return function(node) {
                            this.nativeRange[name](node);
                            updateRangeProperties(this);
                        };
                    };

                } catch(ex) {

                    rangeProto.setStart = function(node, offset) {
                        try {
                            this.nativeRange.setStart(node, offset);
                        } catch (ex) {
                            this.nativeRange.setEnd(node, offset);
                            this.nativeRange.setStart(node, offset);
                        }
                        updateRangeProperties(this);
                    };

                    rangeProto.setEnd = function(node, offset) {
                        try {
                            this.nativeRange.setEnd(node, offset);
                        } catch (ex) {
                            this.nativeRange.setStart(node, offset);
                            this.nativeRange.setEnd(node, offset);
                        }
                        updateRangeProperties(this);
                    };

                    createBeforeAfterNodeSetter = function(name, oppositeName) {
                        return function(node) {
                            try {
                                this.nativeRange[name](node);
                            } catch (ex) {
                                this.nativeRange[oppositeName](node);
                                this.nativeRange[name](node);
                            }
                            updateRangeProperties(this);
                        };
                    };
                }

                rangeProto.setStartBefore = createBeforeAfterNodeSetter("setStartBefore", "setEndBefore");
                rangeProto.setStartAfter = createBeforeAfterNodeSetter("setStartAfter", "setEndAfter");
                rangeProto.setEndBefore = createBeforeAfterNodeSetter("setEndBefore", "setStartBefore");
                rangeProto.setEndAfter = createBeforeAfterNodeSetter("setEndAfter", "setStartAfter");

                /*--------------------------------------------------------------------------------------------------------*/

                // Always use DOM4-compliant selectNodeContents implementation: it's simpler and less code than testing
                // whether the native implementation can be trusted
                rangeProto.selectNodeContents = function(node) {
                    this.setStartAndEnd(node, 0, dom.getNodeLength(node));
                };

                /*--------------------------------------------------------------------------------------------------------*/

                // Test for and correct WebKit bug that has the behaviour of compareBoundaryPoints round the wrong way for
                // constants START_TO_END and END_TO_START: https://bugs.webkit.org/show_bug.cgi?id=20738

                range.selectNodeContents(testTextNode);
                range.setEnd(testTextNode, 3);

                var range2 = document.createRange();
                range2.selectNodeContents(testTextNode);
                range2.setEnd(testTextNode, 4);
                range2.setStart(testTextNode, 2);

                if (range.compareBoundaryPoints(range.START_TO_END, range2) == -1 &&
                        range.compareBoundaryPoints(range.END_TO_START, range2) == 1) {
                    // This is the wrong way round, so correct for it

                    rangeProto.compareBoundaryPoints = function(type, range) {
                        range = range.nativeRange || range;
                        if (type == range.START_TO_END) {
                            type = range.END_TO_START;
                        } else if (type == range.END_TO_START) {
                            type = range.START_TO_END;
                        }
                        return this.nativeRange.compareBoundaryPoints(type, range);
                    };
                } else {
                    rangeProto.compareBoundaryPoints = function(type, range) {
                        return this.nativeRange.compareBoundaryPoints(type, range.nativeRange || range);
                    };
                }

                /*--------------------------------------------------------------------------------------------------------*/

                // Test for IE deleteContents() and extractContents() bug and correct it. See issue 107.

                var el = document.createElement("div");
                el.innerHTML = "123";
                var textNode = el.firstChild;
                var body = getBody(document);
                body.appendChild(el);

                range.setStart(textNode, 1);
                range.setEnd(textNode, 2);
                range.deleteContents();

                if (textNode.data == "13") {
                    // Behaviour is correct per DOM4 Range so wrap the browser's implementation of deleteContents() and
                    // extractContents()
                    rangeProto.deleteContents = function() {
                        this.nativeRange.deleteContents();
                        updateRangeProperties(this);
                    };

                    rangeProto.extractContents = function() {
                        var frag = this.nativeRange.extractContents();
                        updateRangeProperties(this);
                        return frag;
                    };
                } else {
                }

                body.removeChild(el);
                body = null;

                /*--------------------------------------------------------------------------------------------------------*/

                // Test for existence of createContextualFragment and delegate to it if it exists
                if (util.isHostMethod(range, "createContextualFragment")) {
                    rangeProto.createContextualFragment = function(fragmentStr) {
                        return this.nativeRange.createContextualFragment(fragmentStr);
                    };
                }

                /*--------------------------------------------------------------------------------------------------------*/

                // Clean up
                getBody(document).removeChild(testTextNode);

                rangeProto.getName = function() {
                    return "WrappedRange";
                };

                api.WrappedRange = WrappedRange;

                api.createNativeRange = function(doc) {
                    doc = getContentDocument(doc, module, "createNativeRange");
                    return doc.createRange();
                };
            })();
        }

        if (api.features.implementsTextRange) {
            /*
            This is a workaround for a bug where IE returns the wrong container element from the TextRange's parentElement()
            method. For example, in the following (where pipes denote the selection boundaries):

            <ul id="ul"><li id="a">| a </li><li id="b"> b |</li></ul>

            var range = document.selection.createRange();
            alert(range.parentElement().id); // Should alert "ul" but alerts "b"

            This method returns the common ancestor node of the following:
            - the parentElement() of the textRange
            - the parentElement() of the textRange after calling collapse(true)
            - the parentElement() of the textRange after calling collapse(false)
            */
            var getTextRangeContainerElement = function(textRange) {
                var parentEl = textRange.parentElement();
                var range = textRange.duplicate();
                range.collapse(true);
                var startEl = range.parentElement();
                range = textRange.duplicate();
                range.collapse(false);
                var endEl = range.parentElement();
                var startEndContainer = (startEl == endEl) ? startEl : dom.getCommonAncestor(startEl, endEl);

                return startEndContainer == parentEl ? startEndContainer : dom.getCommonAncestor(parentEl, startEndContainer);
            };

            var textRangeIsCollapsed = function(textRange) {
                return textRange.compareEndPoints("StartToEnd", textRange) == 0;
            };

            // Gets the boundary of a TextRange expressed as a node and an offset within that node. This function started
            // out as an improved version of code found in Tim Cameron Ryan's IERange (http://code.google.com/p/ierange/)
            // but has grown, fixing problems with line breaks in preformatted text, adding workaround for IE TextRange
            // bugs, handling for inputs and images, plus optimizations.
            var getTextRangeBoundaryPosition = function(textRange, wholeRangeContainerElement, isStart, isCollapsed, startInfo) {
                var workingRange = textRange.duplicate();
                workingRange.collapse(isStart);
                var containerElement = workingRange.parentElement();

                // Sometimes collapsing a TextRange that's at the start of a text node can move it into the previous node, so
                // check for that
                if (!dom.isOrIsAncestorOf(wholeRangeContainerElement, containerElement)) {
                    containerElement = wholeRangeContainerElement;
                }


                // Deal with nodes that cannot "contain rich HTML markup". In practice, this means form inputs, images and
                // similar. See http://msdn.microsoft.com/en-us/library/aa703950%28VS.85%29.aspx
                if (!containerElement.canHaveHTML) {
                    var pos = new DomPosition(containerElement.parentNode, dom.getNodeIndex(containerElement));
                    return {
                        boundaryPosition: pos,
                        nodeInfo: {
                            nodeIndex: pos.offset,
                            containerElement: pos.node
                        }
                    };
                }

                var workingNode = dom.getDocument(containerElement).createElement("span");

                // Workaround for HTML5 Shiv's insane violation of document.createElement(). See Rangy issue 104 and HTML5
                // Shiv issue 64: https://github.com/aFarkas/html5shiv/issues/64
                if (workingNode.parentNode) {
                    dom.removeNode(workingNode);
                }

                var comparison, workingComparisonType = isStart ? "StartToStart" : "StartToEnd";
                var previousNode, nextNode, boundaryPosition, boundaryNode;
                var start = (startInfo && startInfo.containerElement == containerElement) ? startInfo.nodeIndex : 0;
                var childNodeCount = containerElement.childNodes.length;
                var end = childNodeCount;

                // Check end first. Code within the loop assumes that the endth child node of the container is definitely
                // after the range boundary.
                var nodeIndex = end;

                while (true) {
                    if (nodeIndex == childNodeCount) {
                        containerElement.appendChild(workingNode);
                    } else {
                        containerElement.insertBefore(workingNode, containerElement.childNodes[nodeIndex]);
                    }
                    workingRange.moveToElementText(workingNode);
                    comparison = workingRange.compareEndPoints(workingComparisonType, textRange);
                    if (comparison == 0 || start == end) {
                        break;
                    } else if (comparison == -1) {
                        if (end == start + 1) {
                            // We know the endth child node is after the range boundary, so we must be done.
                            break;
                        } else {
                            start = nodeIndex;
                        }
                    } else {
                        end = (end == start + 1) ? start : nodeIndex;
                    }
                    nodeIndex = Math.floor((start + end) / 2);
                    containerElement.removeChild(workingNode);
                }


                // We've now reached or gone past the boundary of the text range we're interested in
                // so have identified the node we want
                boundaryNode = workingNode.nextSibling;

                if (comparison == -1 && boundaryNode && isCharacterDataNode(boundaryNode)) {
                    // This is a character data node (text, comment, cdata). The working range is collapsed at the start of
                    // the node containing the text range's boundary, so we move the end of the working range to the
                    // boundary point and measure the length of its text to get the boundary's offset within the node.
                    workingRange.setEndPoint(isStart ? "EndToStart" : "EndToEnd", textRange);

                    var offset;

                    if (/[\r\n]/.test(boundaryNode.data)) {
                        /*
                        For the particular case of a boundary within a text node containing rendered line breaks (within a
                        <pre> element, for example), we need a slightly complicated approach to get the boundary's offset in
                        IE. The facts:

                        - Each line break is represented as \r in the text node's data/nodeValue properties
                        - Each line break is represented as \r\n in the TextRange's 'text' property
                        - The 'text' property of the TextRange does not contain trailing line breaks

                        To get round the problem presented by the final fact above, we can use the fact that TextRange's
                        moveStart() and moveEnd() methods return the actual number of characters moved, which is not
                        necessarily the same as the number of characters it was instructed to move. The simplest approach is
                        to use this to store the characters moved when moving both the start and end of the range to the
                        start of the document body and subtracting the start offset from the end offset (the
                        "move-negative-gazillion" method). However, this is extremely slow when the document is large and
                        the range is near the end of it. Clearly doing the mirror image (i.e. moving the range boundaries to
                        the end of the document) has the same problem.

                        Another approach that works is to use moveStart() to move the start boundary of the range up to the
                        end boundary one character at a time and incrementing a counter with the value returned by the
                        moveStart() call. However, the check for whether the start boundary has reached the end boundary is
                        expensive, so this method is slow (although unlike "move-negative-gazillion" is largely unaffected
                        by the location of the range within the document).

                        The approach used below is a hybrid of the two methods above. It uses the fact that a string
                        containing the TextRange's 'text' property with each \r\n converted to a single \r character cannot
                        be longer than the text of the TextRange, so the start of the range is moved that length initially
                        and then a character at a time to make up for any trailing line breaks not contained in the 'text'
                        property. This has good performance in most situations compared to the previous two methods.
                        */
                        var tempRange = workingRange.duplicate();
                        var rangeLength = tempRange.text.replace(/\r\n/g, "\r").length;

                        offset = tempRange.moveStart("character", rangeLength);
                        while ( (comparison = tempRange.compareEndPoints("StartToEnd", tempRange)) == -1) {
                            offset++;
                            tempRange.moveStart("character", 1);
                        }
                    } else {
                        offset = workingRange.text.length;
                    }
                    boundaryPosition = new DomPosition(boundaryNode, offset);
                } else {

                    // If the boundary immediately follows a character data node and this is the end boundary, we should favour
                    // a position within that, and likewise for a start boundary preceding a character data node
                    previousNode = (isCollapsed || !isStart) && workingNode.previousSibling;
                    nextNode = (isCollapsed || isStart) && workingNode.nextSibling;
                    if (nextNode && isCharacterDataNode(nextNode)) {
                        boundaryPosition = new DomPosition(nextNode, 0);
                    } else if (previousNode && isCharacterDataNode(previousNode)) {
                        boundaryPosition = new DomPosition(previousNode, previousNode.data.length);
                    } else {
                        boundaryPosition = new DomPosition(containerElement, dom.getNodeIndex(workingNode));
                    }
                }

                // Clean up
                dom.removeNode(workingNode);

                return {
                    boundaryPosition: boundaryPosition,
                    nodeInfo: {
                        nodeIndex: nodeIndex,
                        containerElement: containerElement
                    }
                };
            };

            // Returns a TextRange representing the boundary of a TextRange expressed as a node and an offset within that
            // node. This function started out as an optimized version of code found in Tim Cameron Ryan's IERange
            // (http://code.google.com/p/ierange/)
            var createBoundaryTextRange = function(boundaryPosition, isStart) {
                var boundaryNode, boundaryParent, boundaryOffset = boundaryPosition.offset;
                var doc = dom.getDocument(boundaryPosition.node);
                var workingNode, childNodes, workingRange = getBody(doc).createTextRange();
                var nodeIsDataNode = isCharacterDataNode(boundaryPosition.node);

                if (nodeIsDataNode) {
                    boundaryNode = boundaryPosition.node;
                    boundaryParent = boundaryNode.parentNode;
                } else {
                    childNodes = boundaryPosition.node.childNodes;
                    boundaryNode = (boundaryOffset < childNodes.length) ? childNodes[boundaryOffset] : null;
                    boundaryParent = boundaryPosition.node;
                }

                // Position the range immediately before the node containing the boundary
                workingNode = doc.createElement("span");

                // Making the working element non-empty element persuades IE to consider the TextRange boundary to be within
                // the element rather than immediately before or after it
                workingNode.innerHTML = "&#feff;";

                // insertBefore is supposed to work like appendChild if the second parameter is null. However, a bug report
                // for IERange suggests that it can crash the browser: http://code.google.com/p/ierange/issues/detail?id=12
                if (boundaryNode) {
                    boundaryParent.insertBefore(workingNode, boundaryNode);
                } else {
                    boundaryParent.appendChild(workingNode);
                }

                workingRange.moveToElementText(workingNode);
                workingRange.collapse(!isStart);

                // Clean up
                boundaryParent.removeChild(workingNode);

                // Move the working range to the text offset, if required
                if (nodeIsDataNode) {
                    workingRange[isStart ? "moveStart" : "moveEnd"]("character", boundaryOffset);
                }

                return workingRange;
            };

            /*------------------------------------------------------------------------------------------------------------*/

            // This is a wrapper around a TextRange, providing full DOM Range functionality using rangy's DomRange as a
            // prototype

            WrappedTextRange = function(textRange) {
                this.textRange = textRange;
                this.refresh();
            };

            WrappedTextRange.prototype = new DomRange(document);

            WrappedTextRange.prototype.refresh = function() {
                var start, end, startBoundary;

                // TextRange's parentElement() method cannot be trusted. getTextRangeContainerElement() works around that.
                var rangeContainerElement = getTextRangeContainerElement(this.textRange);

                if (textRangeIsCollapsed(this.textRange)) {
                    end = start = getTextRangeBoundaryPosition(this.textRange, rangeContainerElement, true,
                        true).boundaryPosition;
                } else {
                    startBoundary = getTextRangeBoundaryPosition(this.textRange, rangeContainerElement, true, false);
                    start = startBoundary.boundaryPosition;

                    // An optimization used here is that if the start and end boundaries have the same parent element, the
                    // search scope for the end boundary can be limited to exclude the portion of the element that precedes
                    // the start boundary
                    end = getTextRangeBoundaryPosition(this.textRange, rangeContainerElement, false, false,
                        startBoundary.nodeInfo).boundaryPosition;
                }

                this.setStart(start.node, start.offset);
                this.setEnd(end.node, end.offset);
            };

            WrappedTextRange.prototype.getName = function() {
                return "WrappedTextRange";
            };

            DomRange.copyComparisonConstants(WrappedTextRange);

            var rangeToTextRange = function(range) {
                if (range.collapsed) {
                    return createBoundaryTextRange(new DomPosition(range.startContainer, range.startOffset), true);
                } else {
                    var startRange = createBoundaryTextRange(new DomPosition(range.startContainer, range.startOffset), true);
                    var endRange = createBoundaryTextRange(new DomPosition(range.endContainer, range.endOffset), false);
                    var textRange = getBody( DomRange.getRangeDocument(range) ).createTextRange();
                    textRange.setEndPoint("StartToStart", startRange);
                    textRange.setEndPoint("EndToEnd", endRange);
                    return textRange;
                }
            };

            WrappedTextRange.rangeToTextRange = rangeToTextRange;

            WrappedTextRange.prototype.toTextRange = function() {
                return rangeToTextRange(this);
            };

            api.WrappedTextRange = WrappedTextRange;

            // IE 9 and above have both implementations and Rangy makes both available. The next few lines sets which
            // implementation to use by default.
            if (!api.features.implementsDomRange || api.config.preferTextRange) {
                // Add WrappedTextRange as the Range property of the global object to allow expression like Range.END_TO_END to work
                var globalObj = (function(f) { return f("return this;")(); })(Function);
                if (typeof globalObj.Range == "undefined") {
                    globalObj.Range = WrappedTextRange;
                }

                api.createNativeRange = function(doc) {
                    doc = getContentDocument(doc, module, "createNativeRange");
                    return getBody(doc).createTextRange();
                };

                api.WrappedRange = WrappedTextRange;
            }
        }

        api.createRange = function(doc) {
            doc = getContentDocument(doc, module, "createRange");
            return new api.WrappedRange(api.createNativeRange(doc));
        };

        api.createRangyRange = function(doc) {
            doc = getContentDocument(doc, module, "createRangyRange");
            return new DomRange(doc);
        };

        util.createAliasForDeprecatedMethod(api, "createIframeRange", "createRange");
        util.createAliasForDeprecatedMethod(api, "createIframeRangyRange", "createRangyRange");

        api.addShimListener(function(win) {
            var doc = win.document;
            if (typeof doc.createRange == "undefined") {
                doc.createRange = function() {
                    return api.createRange(doc);
                };
            }
            doc = win = null;
        });
    });

    /*----------------------------------------------------------------------------------------------------------------*/

    // This module creates a selection object wrapper that conforms as closely as possible to the Selection specification
    // in the HTML Editing spec (http://dvcs.w3.org/hg/editing/raw-file/tip/editing.html#selections)
    api.createCoreModule("WrappedSelection", ["DomRange", "WrappedRange"], function(api, module) {
        api.config.checkSelectionRanges = true;

        var BOOLEAN = "boolean";
        var NUMBER = "number";
        var dom = api.dom;
        var util = api.util;
        var isHostMethod = util.isHostMethod;
        var DomRange = api.DomRange;
        var WrappedRange = api.WrappedRange;
        var DOMException = api.DOMException;
        var DomPosition = dom.DomPosition;
        var getNativeSelection;
        var selectionIsCollapsed;
        var features = api.features;
        var CONTROL = "Control";
        var getDocument = dom.getDocument;
        var getBody = dom.getBody;
        var rangesEqual = DomRange.rangesEqual;


        // Utility function to support direction parameters in the API that may be a string ("backward", "backwards",
        // "forward" or "forwards") or a Boolean (true for backwards).
        function isDirectionBackward(dir) {
            return (typeof dir == "string") ? /^backward(s)?$/i.test(dir) : !!dir;
        }

        function getWindow(win, methodName) {
            if (!win) {
                return window;
            } else if (dom.isWindow(win)) {
                return win;
            } else if (win instanceof WrappedSelection) {
                return win.win;
            } else {
                var doc = dom.getContentDocument(win, module, methodName);
                return dom.getWindow(doc);
            }
        }

        function getWinSelection(winParam) {
            return getWindow(winParam, "getWinSelection").getSelection();
        }

        function getDocSelection(winParam) {
            return getWindow(winParam, "getDocSelection").document.selection;
        }

        function winSelectionIsBackward(sel) {
            var backward = false;
            if (sel.anchorNode) {
                backward = (dom.comparePoints(sel.anchorNode, sel.anchorOffset, sel.focusNode, sel.focusOffset) == 1);
            }
            return backward;
        }

        // Test for the Range/TextRange and Selection features required
        // Test for ability to retrieve selection
        var implementsWinGetSelection = isHostMethod(window, "getSelection"),
            implementsDocSelection = util.isHostObject(document, "selection");

        features.implementsWinGetSelection = implementsWinGetSelection;
        features.implementsDocSelection = implementsDocSelection;

        var useDocumentSelection = implementsDocSelection && (!implementsWinGetSelection || api.config.preferTextRange);

        if (useDocumentSelection) {
            getNativeSelection = getDocSelection;
            api.isSelectionValid = function(winParam) {
                var doc = getWindow(winParam, "isSelectionValid").document, nativeSel = doc.selection;

                // Check whether the selection TextRange is actually contained within the correct document
                return (nativeSel.type != "None" || getDocument(nativeSel.createRange().parentElement()) == doc);
            };
        } else if (implementsWinGetSelection) {
            getNativeSelection = getWinSelection;
            api.isSelectionValid = function() {
                return true;
            };
        } else {
            module.fail("Neither document.selection or window.getSelection() detected.");
            return false;
        }

        api.getNativeSelection = getNativeSelection;

        var testSelection = getNativeSelection();

        // In Firefox, the selection is null in an iframe with display: none. See issue #138.
        if (!testSelection) {
            module.fail("Native selection was null (possibly issue 138?)");
            return false;
        }

        var testRange = api.createNativeRange(document);
        var body = getBody(document);

        // Obtaining a range from a selection
        var selectionHasAnchorAndFocus = util.areHostProperties(testSelection,
            ["anchorNode", "focusNode", "anchorOffset", "focusOffset"]);

        features.selectionHasAnchorAndFocus = selectionHasAnchorAndFocus;

        // Test for existence of native selection extend() method
        var selectionHasExtend = isHostMethod(testSelection, "extend");
        features.selectionHasExtend = selectionHasExtend;

        // Test if rangeCount exists
        var selectionHasRangeCount = (typeof testSelection.rangeCount == NUMBER);
        features.selectionHasRangeCount = selectionHasRangeCount;

        var selectionSupportsMultipleRanges = false;
        var collapsedNonEditableSelectionsSupported = true;

        var addRangeBackwardToNative = selectionHasExtend ?
            function(nativeSelection, range) {
                var doc = DomRange.getRangeDocument(range);
                var endRange = api.createRange(doc);
                endRange.collapseToPoint(range.endContainer, range.endOffset);
                nativeSelection.addRange(getNativeRange(endRange));
                nativeSelection.extend(range.startContainer, range.startOffset);
            } : null;

        if (util.areHostMethods(testSelection, ["addRange", "getRangeAt", "removeAllRanges"]) &&
                typeof testSelection.rangeCount == NUMBER && features.implementsDomRange) {

            (function() {
                // Previously an iframe was used but this caused problems in some circumstances in IE, so tests are
                // performed on the current document's selection. See issue 109.

                // Note also that if a selection previously existed, it is wiped and later restored by these tests. This
                // will result in the selection direction begin reversed if the original selection was backwards and the
                // browser does not support setting backwards selections (Internet Explorer, I'm looking at you).
                var sel = window.getSelection();
                if (sel) {
                    // Store the current selection
                    var originalSelectionRangeCount = sel.rangeCount;
                    var selectionHasMultipleRanges = (originalSelectionRangeCount > 1);
                    var originalSelectionRanges = [];
                    var originalSelectionBackward = winSelectionIsBackward(sel);
                    for (var i = 0; i < originalSelectionRangeCount; ++i) {
                        originalSelectionRanges[i] = sel.getRangeAt(i);
                    }

                    // Create some test elements
                    var testEl = dom.createTestElement(document, "", false);
                    var textNode = testEl.appendChild( document.createTextNode("\u00a0\u00a0\u00a0") );

                    // Test whether the native selection will allow a collapsed selection within a non-editable element
                    var r1 = document.createRange();

                    r1.setStart(textNode, 1);
                    r1.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(r1);
                    collapsedNonEditableSelectionsSupported = (sel.rangeCount == 1);
                    sel.removeAllRanges();

                    // Test whether the native selection is capable of supporting multiple ranges.
                    if (!selectionHasMultipleRanges) {
                        // Doing the original feature test here in Chrome 36 (and presumably later versions) prints a
                        // console error of "Discontiguous selection is not supported." that cannot be suppressed. There's
                        // nothing we can do about this while retaining the feature test so we have to resort to a browser
                        // sniff. I'm not happy about it. See
                        // https://code.google.com/p/chromium/issues/detail?id=399791
                        var chromeMatch = window.navigator.appVersion.match(/Chrome\/(.*?) /);
                        if (chromeMatch && parseInt(chromeMatch[1]) >= 36) {
                            selectionSupportsMultipleRanges = false;
                        } else {
                            var r2 = r1.cloneRange();
                            r1.setStart(textNode, 0);
                            r2.setEnd(textNode, 3);
                            r2.setStart(textNode, 2);
                            sel.addRange(r1);
                            sel.addRange(r2);
                            selectionSupportsMultipleRanges = (sel.rangeCount == 2);
                        }
                    }

                    // Clean up
                    dom.removeNode(testEl);
                    sel.removeAllRanges();

                    for (i = 0; i < originalSelectionRangeCount; ++i) {
                        if (i == 0 && originalSelectionBackward) {
                            if (addRangeBackwardToNative) {
                                addRangeBackwardToNative(sel, originalSelectionRanges[i]);
                            } else {
                                api.warn("Rangy initialization: original selection was backwards but selection has been restored forwards because the browser does not support Selection.extend");
                                sel.addRange(originalSelectionRanges[i]);
                            }
                        } else {
                            sel.addRange(originalSelectionRanges[i]);
                        }
                    }
                }
            })();
        }

        features.selectionSupportsMultipleRanges = selectionSupportsMultipleRanges;
        features.collapsedNonEditableSelectionsSupported = collapsedNonEditableSelectionsSupported;

        // ControlRanges
        var implementsControlRange = false, testControlRange;

        if (body && isHostMethod(body, "createControlRange")) {
            testControlRange = body.createControlRange();
            if (util.areHostProperties(testControlRange, ["item", "add"])) {
                implementsControlRange = true;
            }
        }
        features.implementsControlRange = implementsControlRange;

        // Selection collapsedness
        if (selectionHasAnchorAndFocus) {
            selectionIsCollapsed = function(sel) {
                return sel.anchorNode === sel.focusNode && sel.anchorOffset === sel.focusOffset;
            };
        } else {
            selectionIsCollapsed = function(sel) {
                return sel.rangeCount ? sel.getRangeAt(sel.rangeCount - 1).collapsed : false;
            };
        }

        function updateAnchorAndFocusFromRange(sel, range, backward) {
            var anchorPrefix = backward ? "end" : "start", focusPrefix = backward ? "start" : "end";
            sel.anchorNode = range[anchorPrefix + "Container"];
            sel.anchorOffset = range[anchorPrefix + "Offset"];
            sel.focusNode = range[focusPrefix + "Container"];
            sel.focusOffset = range[focusPrefix + "Offset"];
        }

        function updateAnchorAndFocusFromNativeSelection(sel) {
            var nativeSel = sel.nativeSelection;
            sel.anchorNode = nativeSel.anchorNode;
            sel.anchorOffset = nativeSel.anchorOffset;
            sel.focusNode = nativeSel.focusNode;
            sel.focusOffset = nativeSel.focusOffset;
        }

        function updateEmptySelection(sel) {
            sel.anchorNode = sel.focusNode = null;
            sel.anchorOffset = sel.focusOffset = 0;
            sel.rangeCount = 0;
            sel.isCollapsed = true;
            sel._ranges.length = 0;
        }

        function getNativeRange(range) {
            var nativeRange;
            if (range instanceof DomRange) {
                nativeRange = api.createNativeRange(range.getDocument());
                nativeRange.setEnd(range.endContainer, range.endOffset);
                nativeRange.setStart(range.startContainer, range.startOffset);
            } else if (range instanceof WrappedRange) {
                nativeRange = range.nativeRange;
            } else if (features.implementsDomRange && (range instanceof dom.getWindow(range.startContainer).Range)) {
                nativeRange = range;
            }
            return nativeRange;
        }

        function rangeContainsSingleElement(rangeNodes) {
            if (!rangeNodes.length || rangeNodes[0].nodeType != 1) {
                return false;
            }
            for (var i = 1, len = rangeNodes.length; i < len; ++i) {
                if (!dom.isAncestorOf(rangeNodes[0], rangeNodes[i])) {
                    return false;
                }
            }
            return true;
        }

        function getSingleElementFromRange(range) {
            var nodes = range.getNodes();
            if (!rangeContainsSingleElement(nodes)) {
                throw module.createError("getSingleElementFromRange: range " + range.inspect() + " did not consist of a single element");
            }
            return nodes[0];
        }

        // Simple, quick test which only needs to distinguish between a TextRange and a ControlRange
        function isTextRange(range) {
            return !!range && typeof range.text != "undefined";
        }

        function updateFromTextRange(sel, range) {
            // Create a Range from the selected TextRange
            var wrappedRange = new WrappedRange(range);
            sel._ranges = [wrappedRange];

            updateAnchorAndFocusFromRange(sel, wrappedRange, false);
            sel.rangeCount = 1;
            sel.isCollapsed = wrappedRange.collapsed;
        }

        function updateControlSelection(sel) {
            // Update the wrapped selection based on what's now in the native selection
            sel._ranges.length = 0;
            if (sel.docSelection.type == "None") {
                updateEmptySelection(sel);
            } else {
                var controlRange = sel.docSelection.createRange();
                if (isTextRange(controlRange)) {
                    // This case (where the selection type is "Control" and calling createRange() on the selection returns
                    // a TextRange) can happen in IE 9. It happens, for example, when all elements in the selected
                    // ControlRange have been removed from the ControlRange and removed from the document.
                    updateFromTextRange(sel, controlRange);
                } else {
                    sel.rangeCount = controlRange.length;
                    var range, doc = getDocument(controlRange.item(0));
                    for (var i = 0; i < sel.rangeCount; ++i) {
                        range = api.createRange(doc);
                        range.selectNode(controlRange.item(i));
                        sel._ranges.push(range);
                    }
                    sel.isCollapsed = sel.rangeCount == 1 && sel._ranges[0].collapsed;
                    updateAnchorAndFocusFromRange(sel, sel._ranges[sel.rangeCount - 1], false);
                }
            }
        }

        function addRangeToControlSelection(sel, range) {
            var controlRange = sel.docSelection.createRange();
            var rangeElement = getSingleElementFromRange(range);

            // Create a new ControlRange containing all the elements in the selected ControlRange plus the element
            // contained by the supplied range
            var doc = getDocument(controlRange.item(0));
            var newControlRange = getBody(doc).createControlRange();
            for (var i = 0, len = controlRange.length; i < len; ++i) {
                newControlRange.add(controlRange.item(i));
            }
            try {
                newControlRange.add(rangeElement);
            } catch (ex) {
                throw module.createError("addRange(): Element within the specified Range could not be added to control selection (does it have layout?)");
            }
            newControlRange.select();

            // Update the wrapped selection based on what's now in the native selection
            updateControlSelection(sel);
        }

        var getSelectionRangeAt;

        if (isHostMethod(testSelection, "getRangeAt")) {
            // try/catch is present because getRangeAt() must have thrown an error in some browser and some situation.
            // Unfortunately, I didn't write a comment about the specifics and am now scared to take it out. Let that be a
            // lesson to us all, especially me.
            getSelectionRangeAt = function(sel, index) {
                try {
                    return sel.getRangeAt(index);
                } catch (ex) {
                    return null;
                }
            };
        } else if (selectionHasAnchorAndFocus) {
            getSelectionRangeAt = function(sel) {
                var doc = getDocument(sel.anchorNode);
                var range = api.createRange(doc);
                range.setStartAndEnd(sel.anchorNode, sel.anchorOffset, sel.focusNode, sel.focusOffset);

                // Handle the case when the selection was selected backwards (from the end to the start in the
                // document)
                if (range.collapsed !== this.isCollapsed) {
                    range.setStartAndEnd(sel.focusNode, sel.focusOffset, sel.anchorNode, sel.anchorOffset);
                }

                return range;
            };
        }

        function WrappedSelection(selection, docSelection, win) {
            this.nativeSelection = selection;
            this.docSelection = docSelection;
            this._ranges = [];
            this.win = win;
            this.refresh();
        }

        WrappedSelection.prototype = api.selectionPrototype;

        function deleteProperties(sel) {
            sel.win = sel.anchorNode = sel.focusNode = sel._ranges = null;
            sel.rangeCount = sel.anchorOffset = sel.focusOffset = 0;
            sel.detached = true;
        }

        var cachedRangySelections = [];

        function actOnCachedSelection(win, action) {
            var i = cachedRangySelections.length, cached, sel;
            while (i--) {
                cached = cachedRangySelections[i];
                sel = cached.selection;
                if (action == "deleteAll") {
                    deleteProperties(sel);
                } else if (cached.win == win) {
                    if (action == "delete") {
                        cachedRangySelections.splice(i, 1);
                        return true;
                    } else {
                        return sel;
                    }
                }
            }
            if (action == "deleteAll") {
                cachedRangySelections.length = 0;
            }
            return null;
        }

        var getSelection = function(win) {
            // Check if the parameter is a Rangy Selection object
            if (win && win instanceof WrappedSelection) {
                win.refresh();
                return win;
            }

            win = getWindow(win, "getNativeSelection");

            var sel = actOnCachedSelection(win);
            var nativeSel = getNativeSelection(win), docSel = implementsDocSelection ? getDocSelection(win) : null;
            if (sel) {
                sel.nativeSelection = nativeSel;
                sel.docSelection = docSel;
                sel.refresh();
            } else {
                sel = new WrappedSelection(nativeSel, docSel, win);
                cachedRangySelections.push( { win: win, selection: sel } );
            }
            return sel;
        };

        api.getSelection = getSelection;

        util.createAliasForDeprecatedMethod(api, "getIframeSelection", "getSelection");

        var selProto = WrappedSelection.prototype;

        function createControlSelection(sel, ranges) {
            // Ensure that the selection becomes of type "Control"
            var doc = getDocument(ranges[0].startContainer);
            var controlRange = getBody(doc).createControlRange();
            for (var i = 0, el, len = ranges.length; i < len; ++i) {
                el = getSingleElementFromRange(ranges[i]);
                try {
                    controlRange.add(el);
                } catch (ex) {
                    throw module.createError("setRanges(): Element within one of the specified Ranges could not be added to control selection (does it have layout?)");
                }
            }
            controlRange.select();

            // Update the wrapped selection based on what's now in the native selection
            updateControlSelection(sel);
        }

        // Selecting a range
        if (!useDocumentSelection && selectionHasAnchorAndFocus && util.areHostMethods(testSelection, ["removeAllRanges", "addRange"])) {
            selProto.removeAllRanges = function() {
                this.nativeSelection.removeAllRanges();
                updateEmptySelection(this);
            };

            var addRangeBackward = function(sel, range) {
                addRangeBackwardToNative(sel.nativeSelection, range);
                sel.refresh();
            };

            if (selectionHasRangeCount) {
                selProto.addRange = function(range, direction) {
                    if (implementsControlRange && implementsDocSelection && this.docSelection.type == CONTROL) {
                        addRangeToControlSelection(this, range);
                    } else {
                        if (isDirectionBackward(direction) && selectionHasExtend) {
                            addRangeBackward(this, range);
                        } else {
                            var previousRangeCount;
                            if (selectionSupportsMultipleRanges) {
                                previousRangeCount = this.rangeCount;
                            } else {
                                this.removeAllRanges();
                                previousRangeCount = 0;
                            }
                            // Clone the native range so that changing the selected range does not affect the selection.
                            // This is contrary to the spec but is the only way to achieve consistency between browsers. See
                            // issue 80.
                            var clonedNativeRange = getNativeRange(range).cloneRange();
                            try {
                                this.nativeSelection.addRange(clonedNativeRange);
                            } catch (ex) {
                            }

                            // Check whether adding the range was successful
                            this.rangeCount = this.nativeSelection.rangeCount;

                            if (this.rangeCount == previousRangeCount + 1) {
                                // The range was added successfully

                                // Check whether the range that we added to the selection is reflected in the last range extracted from
                                // the selection
                                if (api.config.checkSelectionRanges) {
                                    var nativeRange = getSelectionRangeAt(this.nativeSelection, this.rangeCount - 1);
                                    if (nativeRange && !rangesEqual(nativeRange, range)) {
                                        // Happens in WebKit with, for example, a selection placed at the start of a text node
                                        range = new WrappedRange(nativeRange);
                                    }
                                }
                                this._ranges[this.rangeCount - 1] = range;
                                updateAnchorAndFocusFromRange(this, range, selectionIsBackward(this.nativeSelection));
                                this.isCollapsed = selectionIsCollapsed(this);
                            } else {
                                // The range was not added successfully. The simplest thing is to refresh
                                this.refresh();
                            }
                        }
                    }
                };
            } else {
                selProto.addRange = function(range, direction) {
                    if (isDirectionBackward(direction) && selectionHasExtend) {
                        addRangeBackward(this, range);
                    } else {
                        this.nativeSelection.addRange(getNativeRange(range));
                        this.refresh();
                    }
                };
            }

            selProto.setRanges = function(ranges) {
                if (implementsControlRange && implementsDocSelection && ranges.length > 1) {
                    createControlSelection(this, ranges);
                } else {
                    this.removeAllRanges();
                    for (var i = 0, len = ranges.length; i < len; ++i) {
                        this.addRange(ranges[i]);
                    }
                }
            };
        } else if (isHostMethod(testSelection, "empty") && isHostMethod(testRange, "select") &&
                   implementsControlRange && useDocumentSelection) {

            selProto.removeAllRanges = function() {
                // Added try/catch as fix for issue #21
                try {
                    this.docSelection.empty();

                    // Check for empty() not working (issue #24)
                    if (this.docSelection.type != "None") {
                        // Work around failure to empty a control selection by instead selecting a TextRange and then
                        // calling empty()
                        var doc;
                        if (this.anchorNode) {
                            doc = getDocument(this.anchorNode);
                        } else if (this.docSelection.type == CONTROL) {
                            var controlRange = this.docSelection.createRange();
                            if (controlRange.length) {
                                doc = getDocument( controlRange.item(0) );
                            }
                        }
                        if (doc) {
                            var textRange = getBody(doc).createTextRange();
                            textRange.select();
                            this.docSelection.empty();
                        }
                    }
                } catch(ex) {}
                updateEmptySelection(this);
            };

            selProto.addRange = function(range) {
                if (this.docSelection.type == CONTROL) {
                    addRangeToControlSelection(this, range);
                } else {
                    api.WrappedTextRange.rangeToTextRange(range).select();
                    this._ranges[0] = range;
                    this.rangeCount = 1;
                    this.isCollapsed = this._ranges[0].collapsed;
                    updateAnchorAndFocusFromRange(this, range, false);
                }
            };

            selProto.setRanges = function(ranges) {
                this.removeAllRanges();
                var rangeCount = ranges.length;
                if (rangeCount > 1) {
                    createControlSelection(this, ranges);
                } else if (rangeCount) {
                    this.addRange(ranges[0]);
                }
            };
        } else {
            module.fail("No means of selecting a Range or TextRange was found");
            return false;
        }

        selProto.getRangeAt = function(index) {
            if (index < 0 || index >= this.rangeCount) {
                throw new DOMException("INDEX_SIZE_ERR");
            } else {
                // Clone the range to preserve selection-range independence. See issue 80.
                return this._ranges[index].cloneRange();
            }
        };

        var refreshSelection;

        if (useDocumentSelection) {
            refreshSelection = function(sel) {
                var range;
                if (api.isSelectionValid(sel.win)) {
                    range = sel.docSelection.createRange();
                } else {
                    range = getBody(sel.win.document).createTextRange();
                    range.collapse(true);
                }

                if (sel.docSelection.type == CONTROL) {
                    updateControlSelection(sel);
                } else if (isTextRange(range)) {
                    updateFromTextRange(sel, range);
                } else {
                    updateEmptySelection(sel);
                }
            };
        } else if (isHostMethod(testSelection, "getRangeAt") && typeof testSelection.rangeCount == NUMBER) {
            refreshSelection = function(sel) {
                if (implementsControlRange && implementsDocSelection && sel.docSelection.type == CONTROL) {
                    updateControlSelection(sel);
                } else {
                    sel._ranges.length = sel.rangeCount = sel.nativeSelection.rangeCount;
                    if (sel.rangeCount) {
                        for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                            sel._ranges[i] = new api.WrappedRange(sel.nativeSelection.getRangeAt(i));
                        }
                        updateAnchorAndFocusFromRange(sel, sel._ranges[sel.rangeCount - 1], selectionIsBackward(sel.nativeSelection));
                        sel.isCollapsed = selectionIsCollapsed(sel);
                    } else {
                        updateEmptySelection(sel);
                    }
                }
            };
        } else if (selectionHasAnchorAndFocus && typeof testSelection.isCollapsed == BOOLEAN && typeof testRange.collapsed == BOOLEAN && features.implementsDomRange) {
            refreshSelection = function(sel) {
                var range, nativeSel = sel.nativeSelection;
                if (nativeSel.anchorNode) {
                    range = getSelectionRangeAt(nativeSel, 0);
                    sel._ranges = [range];
                    sel.rangeCount = 1;
                    updateAnchorAndFocusFromNativeSelection(sel);
                    sel.isCollapsed = selectionIsCollapsed(sel);
                } else {
                    updateEmptySelection(sel);
                }
            };
        } else {
            module.fail("No means of obtaining a Range or TextRange from the user's selection was found");
            return false;
        }

        selProto.refresh = function(checkForChanges) {
            var oldRanges = checkForChanges ? this._ranges.slice(0) : null;
            var oldAnchorNode = this.anchorNode, oldAnchorOffset = this.anchorOffset;

            refreshSelection(this);
            if (checkForChanges) {
                // Check the range count first
                var i = oldRanges.length;
                if (i != this._ranges.length) {
                    return true;
                }

                // Now check the direction. Checking the anchor position is the same is enough since we're checking all the
                // ranges after this
                if (this.anchorNode != oldAnchorNode || this.anchorOffset != oldAnchorOffset) {
                    return true;
                }

                // Finally, compare each range in turn
                while (i--) {
                    if (!rangesEqual(oldRanges[i], this._ranges[i])) {
                        return true;
                    }
                }
                return false;
            }
        };

        // Removal of a single range
        var removeRangeManually = function(sel, range) {
            var ranges = sel.getAllRanges();
            sel.removeAllRanges();
            for (var i = 0, len = ranges.length; i < len; ++i) {
                if (!rangesEqual(range, ranges[i])) {
                    sel.addRange(ranges[i]);
                }
            }
            if (!sel.rangeCount) {
                updateEmptySelection(sel);
            }
        };

        if (implementsControlRange && implementsDocSelection) {
            selProto.removeRange = function(range) {
                if (this.docSelection.type == CONTROL) {
                    var controlRange = this.docSelection.createRange();
                    var rangeElement = getSingleElementFromRange(range);

                    // Create a new ControlRange containing all the elements in the selected ControlRange minus the
                    // element contained by the supplied range
                    var doc = getDocument(controlRange.item(0));
                    var newControlRange = getBody(doc).createControlRange();
                    var el, removed = false;
                    for (var i = 0, len = controlRange.length; i < len; ++i) {
                        el = controlRange.item(i);
                        if (el !== rangeElement || removed) {
                            newControlRange.add(controlRange.item(i));
                        } else {
                            removed = true;
                        }
                    }
                    newControlRange.select();

                    // Update the wrapped selection based on what's now in the native selection
                    updateControlSelection(this);
                } else {
                    removeRangeManually(this, range);
                }
            };
        } else {
            selProto.removeRange = function(range) {
                removeRangeManually(this, range);
            };
        }

        // Detecting if a selection is backward
        var selectionIsBackward;
        if (!useDocumentSelection && selectionHasAnchorAndFocus && features.implementsDomRange) {
            selectionIsBackward = winSelectionIsBackward;

            selProto.isBackward = function() {
                return selectionIsBackward(this);
            };
        } else {
            selectionIsBackward = selProto.isBackward = function() {
                return false;
            };
        }

        // Create an alias for backwards compatibility. From 1.3, everything is "backward" rather than "backwards"
        selProto.isBackwards = selProto.isBackward;

        // Selection stringifier
        // This is conformant to the old HTML5 selections draft spec but differs from WebKit and Mozilla's implementation.
        // The current spec does not yet define this method.
        selProto.toString = function() {
            var rangeTexts = [];
            for (var i = 0, len = this.rangeCount; i < len; ++i) {
                rangeTexts[i] = "" + this._ranges[i];
            }
            return rangeTexts.join("");
        };

        function assertNodeInSameDocument(sel, node) {
            if (sel.win.document != getDocument(node)) {
                throw new DOMException("WRONG_DOCUMENT_ERR");
            }
        }

        // No current browser conforms fully to the spec for this method, so Rangy's own method is always used
        selProto.collapse = function(node, offset) {
            assertNodeInSameDocument(this, node);
            var range = api.createRange(node);
            range.collapseToPoint(node, offset);
            this.setSingleRange(range);
            this.isCollapsed = true;
        };

        selProto.collapseToStart = function() {
            if (this.rangeCount) {
                var range = this._ranges[0];
                this.collapse(range.startContainer, range.startOffset);
            } else {
                throw new DOMException("INVALID_STATE_ERR");
            }
        };

        selProto.collapseToEnd = function() {
            if (this.rangeCount) {
                var range = this._ranges[this.rangeCount - 1];
                this.collapse(range.endContainer, range.endOffset);
            } else {
                throw new DOMException("INVALID_STATE_ERR");
            }
        };

        // The spec is very specific on how selectAllChildren should be implemented and not all browsers implement it as
        // specified so the native implementation is never used by Rangy.
        selProto.selectAllChildren = function(node) {
            assertNodeInSameDocument(this, node);
            var range = api.createRange(node);
            range.selectNodeContents(node);
            this.setSingleRange(range);
        };

        selProto.deleteFromDocument = function() {
            // Sepcial behaviour required for IE's control selections
            if (implementsControlRange && implementsDocSelection && this.docSelection.type == CONTROL) {
                var controlRange = this.docSelection.createRange();
                var element;
                while (controlRange.length) {
                    element = controlRange.item(0);
                    controlRange.remove(element);
                    dom.removeNode(element);
                }
                this.refresh();
            } else if (this.rangeCount) {
                var ranges = this.getAllRanges();
                if (ranges.length) {
                    this.removeAllRanges();
                    for (var i = 0, len = ranges.length; i < len; ++i) {
                        ranges[i].deleteContents();
                    }
                    // The spec says nothing about what the selection should contain after calling deleteContents on each
                    // range. Firefox moves the selection to where the final selected range was, so we emulate that
                    this.addRange(ranges[len - 1]);
                }
            }
        };

        // The following are non-standard extensions
        selProto.eachRange = function(func, returnValue) {
            for (var i = 0, len = this._ranges.length; i < len; ++i) {
                if ( func( this.getRangeAt(i) ) ) {
                    return returnValue;
                }
            }
        };

        selProto.getAllRanges = function() {
            var ranges = [];
            this.eachRange(function(range) {
                ranges.push(range);
            });
            return ranges;
        };

        selProto.setSingleRange = function(range, direction) {
            this.removeAllRanges();
            this.addRange(range, direction);
        };

        selProto.callMethodOnEachRange = function(methodName, params) {
            var results = [];
            this.eachRange( function(range) {
                results.push( range[methodName].apply(range, params || []) );
            } );
            return results;
        };

        function createStartOrEndSetter(isStart) {
            return function(node, offset) {
                var range;
                if (this.rangeCount) {
                    range = this.getRangeAt(0);
                    range["set" + (isStart ? "Start" : "End")](node, offset);
                } else {
                    range = api.createRange(this.win.document);
                    range.setStartAndEnd(node, offset);
                }
                this.setSingleRange(range, this.isBackward());
            };
        }

        selProto.setStart = createStartOrEndSetter(true);
        selProto.setEnd = createStartOrEndSetter(false);

        // Add select() method to Range prototype. Any existing selection will be removed.
        api.rangePrototype.select = function(direction) {
            getSelection( this.getDocument() ).setSingleRange(this, direction);
        };

        selProto.changeEachRange = function(func) {
            var ranges = [];
            var backward = this.isBackward();

            this.eachRange(function(range) {
                func(range);
                ranges.push(range);
            });

            this.removeAllRanges();
            if (backward && ranges.length == 1) {
                this.addRange(ranges[0], "backward");
            } else {
                this.setRanges(ranges);
            }
        };

        selProto.containsNode = function(node, allowPartial) {
            return this.eachRange( function(range) {
                return range.containsNode(node, allowPartial);
            }, true ) || false;
        };

        selProto.getBookmark = function(containerNode) {
            return {
                backward: this.isBackward(),
                rangeBookmarks: this.callMethodOnEachRange("getBookmark", [containerNode])
            };
        };

        selProto.moveToBookmark = function(bookmark) {
            var selRanges = [];
            for (var i = 0, rangeBookmark, range; rangeBookmark = bookmark.rangeBookmarks[i++]; ) {
                range = api.createRange(this.win);
                range.moveToBookmark(rangeBookmark);
                selRanges.push(range);
            }
            if (bookmark.backward) {
                this.setSingleRange(selRanges[0], "backward");
            } else {
                this.setRanges(selRanges);
            }
        };

        selProto.saveRanges = function() {
            return {
                backward: this.isBackward(),
                ranges: this.callMethodOnEachRange("cloneRange")
            };
        };

        selProto.restoreRanges = function(selRanges) {
            this.removeAllRanges();
            for (var i = 0, range; range = selRanges.ranges[i]; ++i) {
                this.addRange(range, (selRanges.backward && i == 0));
            }
        };

        selProto.toHtml = function() {
            var rangeHtmls = [];
            this.eachRange(function(range) {
                rangeHtmls.push( DomRange.toHtml(range) );
            });
            return rangeHtmls.join("");
        };

        if (features.implementsTextRange) {
            selProto.getNativeTextRange = function() {
                var sel, textRange;
                if ( (sel = this.docSelection) ) {
                    var range = sel.createRange();
                    if (isTextRange(range)) {
                        return range;
                    } else {
                        throw module.createError("getNativeTextRange: selection is a control selection");
                    }
                } else if (this.rangeCount > 0) {
                    return api.WrappedTextRange.rangeToTextRange( this.getRangeAt(0) );
                } else {
                    throw module.createError("getNativeTextRange: selection contains no range");
                }
            };
        }

        function inspect(sel) {
            var rangeInspects = [];
            var anchor = new DomPosition(sel.anchorNode, sel.anchorOffset);
            var focus = new DomPosition(sel.focusNode, sel.focusOffset);
            var name = (typeof sel.getName == "function") ? sel.getName() : "Selection";

            if (typeof sel.rangeCount != "undefined") {
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    rangeInspects[i] = DomRange.inspect(sel.getRangeAt(i));
                }
            }
            return "[" + name + "(Ranges: " + rangeInspects.join(", ") +
                    ")(anchor: " + anchor.inspect() + ", focus: " + focus.inspect() + "]";
        }

        selProto.getName = function() {
            return "WrappedSelection";
        };

        selProto.inspect = function() {
            return inspect(this);
        };

        selProto.detach = function() {
            actOnCachedSelection(this.win, "delete");
            deleteProperties(this);
        };

        WrappedSelection.detachAll = function() {
            actOnCachedSelection(null, "deleteAll");
        };

        WrappedSelection.inspect = inspect;
        WrappedSelection.isDirectionBackward = isDirectionBackward;

        api.Selection = WrappedSelection;

        api.selectionPrototype = selProto;

        api.addShimListener(function(win) {
            if (typeof win.getSelection == "undefined") {
                win.getSelection = function() {
                    return getSelection(win);
                };
            }
            win = null;
        });
    });
    

    /*----------------------------------------------------------------------------------------------------------------*/

    // Wait for document to load before initializing
    var docReady = false;

    var loadHandler = function(e) {
        if (!docReady) {
            docReady = true;
            if (!api.initialized && api.config.autoInitialize) {
                init();
            }
        }
    };

    if (isBrowser) {
        // Test whether the document has already been loaded and initialize immediately if so
        if (document.readyState == "complete") {
            loadHandler();
        } else {
            if (isHostMethod(document, "addEventListener")) {
                document.addEventListener("DOMContentLoaded", loadHandler, false);
            }

            // Add a fallback in case the DOMContentLoaded event isn't supported
            addListener(window, "load", loadHandler);
        }
    }

    return api;
}, this);

/**
 * Selection save and restore module for Rangy.
 * Saves and restores user selections using marker invisible elements in the DOM.
 *
 * Part of Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Depends on Rangy core.
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */
(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {
    rangy.createModule("SaveRestore", ["WrappedRange"], function(api, module) {
        var dom = api.dom;
        var removeNode = dom.removeNode;
        var isDirectionBackward = api.Selection.isDirectionBackward;
        var markerTextChar = "\ufeff";

        function gEBI(id, doc) {
            return (doc || document).getElementById(id);
        }

        function insertRangeBoundaryMarker(range, atStart) {
            var markerId = "selectionBoundary_" + (+new Date()) + "_" + ("" + Math.random()).slice(2);
            var markerEl;
            var doc = dom.getDocument(range.startContainer);

            // Clone the Range and collapse to the appropriate boundary point
            var boundaryRange = range.cloneRange();
            boundaryRange.collapse(atStart);

            // Create the marker element containing a single invisible character using DOM methods and insert it
            markerEl = doc.createElement("span");
            markerEl.id = markerId;
            markerEl.style.lineHeight = "0";
            markerEl.style.display = "none";
            markerEl.className = "rangySelectionBoundary";
            markerEl.appendChild(doc.createTextNode(markerTextChar));

            boundaryRange.insertNode(markerEl);
            return markerEl;
        }

        function setRangeBoundary(doc, range, markerId, atStart) {
            var markerEl = gEBI(markerId, doc);
            if (markerEl) {
                range[atStart ? "setStartBefore" : "setEndBefore"](markerEl);
                removeNode(markerEl);
            } else {
                module.warn("Marker element has been removed. Cannot restore selection.");
            }
        }

        function compareRanges(r1, r2) {
            return r2.compareBoundaryPoints(r1.START_TO_START, r1);
        }

        function saveRange(range, direction) {
            var startEl, endEl, doc = api.DomRange.getRangeDocument(range), text = range.toString();
            var backward = isDirectionBackward(direction);

            if (range.collapsed) {
                endEl = insertRangeBoundaryMarker(range, false);
                return {
                    document: doc,
                    markerId: endEl.id,
                    collapsed: true
                };
            } else {
                endEl = insertRangeBoundaryMarker(range, false);
                startEl = insertRangeBoundaryMarker(range, true);

                return {
                    document: doc,
                    startMarkerId: startEl.id,
                    endMarkerId: endEl.id,
                    collapsed: false,
                    backward: backward,
                    toString: function() {
                        return "original text: '" + text + "', new text: '" + range.toString() + "'";
                    }
                };
            }
        }

        function restoreRange(rangeInfo, normalize) {
            var doc = rangeInfo.document;
            if (typeof normalize == "undefined") {
                normalize = true;
            }
            var range = api.createRange(doc);
            if (rangeInfo.collapsed) {
                var markerEl = gEBI(rangeInfo.markerId, doc);
                if (markerEl) {
                    markerEl.style.display = "inline";
                    var previousNode = markerEl.previousSibling;

                    // Workaround for issue 17
                    if (previousNode && previousNode.nodeType == 3) {
                        removeNode(markerEl);
                        range.collapseToPoint(previousNode, previousNode.length);
                    } else {
                        range.collapseBefore(markerEl);
                        removeNode(markerEl);
                    }
                } else {
                    module.warn("Marker element has been removed. Cannot restore selection.");
                }
            } else {
                setRangeBoundary(doc, range, rangeInfo.startMarkerId, true);
                setRangeBoundary(doc, range, rangeInfo.endMarkerId, false);
            }

            if (normalize) {
                range.normalizeBoundaries();
            }

            return range;
        }

        function saveRanges(ranges, direction) {
            var rangeInfos = [], range, doc;
            var backward = isDirectionBackward(direction);

            // Order the ranges by position within the DOM, latest first, cloning the array to leave the original untouched
            ranges = ranges.slice(0);
            ranges.sort(compareRanges);

            for (var i = 0, len = ranges.length; i < len; ++i) {
                rangeInfos[i] = saveRange(ranges[i], backward);
            }

            // Now that all the markers are in place and DOM manipulation over, adjust each range's boundaries to lie
            // between its markers
            for (i = len - 1; i >= 0; --i) {
                range = ranges[i];
                doc = api.DomRange.getRangeDocument(range);
                if (range.collapsed) {
                    range.collapseAfter(gEBI(rangeInfos[i].markerId, doc));
                } else {
                    range.setEndBefore(gEBI(rangeInfos[i].endMarkerId, doc));
                    range.setStartAfter(gEBI(rangeInfos[i].startMarkerId, doc));
                }
            }

            return rangeInfos;
        }

        function saveSelection(win) {
            if (!api.isSelectionValid(win)) {
                module.warn("Cannot save selection. This usually happens when the selection is collapsed and the selection document has lost focus.");
                return null;
            }
            var sel = api.getSelection(win);
            var ranges = sel.getAllRanges();
            var backward = (ranges.length == 1 && sel.isBackward());

            var rangeInfos = saveRanges(ranges, backward);

            // Ensure current selection is unaffected
            if (backward) {
                sel.setSingleRange(ranges[0], backward);
            } else {
                sel.setRanges(ranges);
            }

            return {
                win: win,
                rangeInfos: rangeInfos,
                restored: false
            };
        }

        function restoreRanges(rangeInfos) {
            var ranges = [];

            // Ranges are in reverse order of appearance in the DOM. We want to restore earliest first to avoid
            // normalization affecting previously restored ranges.
            var rangeCount = rangeInfos.length;

            for (var i = rangeCount - 1; i >= 0; i--) {
                ranges[i] = restoreRange(rangeInfos[i], true);
            }

            return ranges;
        }

        function restoreSelection(savedSelection, preserveDirection) {
            if (!savedSelection.restored) {
                var rangeInfos = savedSelection.rangeInfos;
                var sel = api.getSelection(savedSelection.win);
                var ranges = restoreRanges(rangeInfos), rangeCount = rangeInfos.length;

                if (rangeCount == 1 && preserveDirection && api.features.selectionHasExtend && rangeInfos[0].backward) {
                    sel.removeAllRanges();
                    sel.addRange(ranges[0], true);
                } else {
                    sel.setRanges(ranges);
                }

                savedSelection.restored = true;
            }
        }

        function removeMarkerElement(doc, markerId) {
            var markerEl = gEBI(markerId, doc);
            if (markerEl) {
                removeNode(markerEl);
            }
        }

        function removeMarkers(savedSelection) {
            var rangeInfos = savedSelection.rangeInfos;
            for (var i = 0, len = rangeInfos.length, rangeInfo; i < len; ++i) {
                rangeInfo = rangeInfos[i];
                if (rangeInfo.collapsed) {
                    removeMarkerElement(savedSelection.doc, rangeInfo.markerId);
                } else {
                    removeMarkerElement(savedSelection.doc, rangeInfo.startMarkerId);
                    removeMarkerElement(savedSelection.doc, rangeInfo.endMarkerId);
                }
            }
        }

        api.util.extend(api, {
            saveRange: saveRange,
            restoreRange: restoreRange,
            saveRanges: saveRanges,
            restoreRanges: restoreRanges,
            saveSelection: saveSelection,
            restoreSelection: restoreSelection,
            removeMarkerElement: removeMarkerElement,
            removeMarkers: removeMarkers
        });
    });
    
    return rangy;
}, this);

/**
 * Serializer module for Rangy.
 * Serializes Ranges and Selections. An example use would be to store a user's selection on a particular page in a
 * cookie or local storage and restore it on the user's next visit to the same page.
 *
 * Part of Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Depends on Rangy core.
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */
(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {
    rangy.createModule("Serializer", ["WrappedSelection"], function(api, module) {
        var UNDEF = "undefined";
        var util = api.util;

        // encodeURIComponent and decodeURIComponent are required for cookie handling
        if (typeof encodeURIComponent == UNDEF || typeof decodeURIComponent == UNDEF) {
            module.fail("encodeURIComponent and/or decodeURIComponent method is missing");
        }

        // Checksum for checking whether range can be serialized
        var crc32 = (function() {
            function utf8encode(str) {
                var utf8CharCodes = [];

                for (var i = 0, len = str.length, c; i < len; ++i) {
                    c = str.charCodeAt(i);
                    if (c < 128) {
                        utf8CharCodes.push(c);
                    } else if (c < 2048) {
                        utf8CharCodes.push((c >> 6) | 192, (c & 63) | 128);
                    } else {
                        utf8CharCodes.push((c >> 12) | 224, ((c >> 6) & 63) | 128, (c & 63) | 128);
                    }
                }
                return utf8CharCodes;
            }

            var cachedCrcTable = null;

            function buildCRCTable() {
                var table = [];
                for (var i = 0, j, crc; i < 256; ++i) {
                    crc = i;
                    j = 8;
                    while (j--) {
                        if ((crc & 1) == 1) {
                            crc = (crc >>> 1) ^ 0xEDB88320;
                        } else {
                            crc >>>= 1;
                        }
                    }
                    table[i] = crc >>> 0;
                }
                return table;
            }

            function getCrcTable() {
                if (!cachedCrcTable) {
                    cachedCrcTable = buildCRCTable();
                }
                return cachedCrcTable;
            }

            return function(str) {
                var utf8CharCodes = utf8encode(str), crc = -1, crcTable = getCrcTable();
                for (var i = 0, len = utf8CharCodes.length, y; i < len; ++i) {
                    y = (crc ^ utf8CharCodes[i]) & 0xFF;
                    crc = (crc >>> 8) ^ crcTable[y];
                }
                return (crc ^ -1) >>> 0;
            };
        })();

        var dom = api.dom;

        function escapeTextForHtml(str) {
            return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        function nodeToInfoString(node, infoParts) {
            infoParts = infoParts || [];
            var nodeType = node.nodeType, children = node.childNodes, childCount = children.length;
            var nodeInfo = [nodeType, node.nodeName, childCount].join(":");
            var start = "", end = "";
            switch (nodeType) {
                case 3: // Text node
                    start = escapeTextForHtml(node.nodeValue);
                    break;
                case 8: // Comment
                    start = "<!--" + escapeTextForHtml(node.nodeValue) + "-->";
                    break;
                default:
                    start = "<" + nodeInfo + ">";
                    end = "</>";
                    break;
            }
            if (start) {
                infoParts.push(start);
            }
            for (var i = 0; i < childCount; ++i) {
                nodeToInfoString(children[i], infoParts);
            }
            if (end) {
                infoParts.push(end);
            }
            return infoParts;
        }

        // Creates a string representation of the specified element's contents that is similar to innerHTML but omits all
        // attributes and comments and includes child node counts. This is done instead of using innerHTML to work around
        // IE <= 8's policy of including element properties in attributes, which ruins things by changing an element's
        // innerHTML whenever the user changes an input within the element.
        function getElementChecksum(el) {
            var info = nodeToInfoString(el).join("");
            return crc32(info).toString(16);
        }

        function serializePosition(node, offset, rootNode) {
            var pathParts = [], n = node;
            rootNode = rootNode || dom.getDocument(node).documentElement;
            while (n && n != rootNode) {
                pathParts.push(dom.getNodeIndex(n, true));
                n = n.parentNode;
            }
            return pathParts.join("/") + ":" + offset;
        }

        function deserializePosition(serialized, rootNode, doc) {
            if (!rootNode) {
                rootNode = (doc || document).documentElement;
            }
            var parts = serialized.split(":");
            var node = rootNode;
            var nodeIndices = parts[0] ? parts[0].split("/") : [], i = nodeIndices.length, nodeIndex;

            while (i--) {
                nodeIndex = parseInt(nodeIndices[i], 10);
                if (nodeIndex < node.childNodes.length) {
                    node = node.childNodes[nodeIndex];
                } else {
                    throw module.createError("deserializePosition() failed: node " + dom.inspectNode(node) +
                            " has no child with index " + nodeIndex + ", " + i);
                }
            }

            return new dom.DomPosition(node, parseInt(parts[1], 10));
        }

        function serializeRange(range, omitChecksum, rootNode) {
            rootNode = rootNode || api.DomRange.getRangeDocument(range).documentElement;
            if (!dom.isOrIsAncestorOf(rootNode, range.commonAncestorContainer)) {
                throw module.createError("serializeRange(): range " + range.inspect() +
                    " is not wholly contained within specified root node " + dom.inspectNode(rootNode));
            }
            var serialized = serializePosition(range.startContainer, range.startOffset, rootNode) + "," +
                serializePosition(range.endContainer, range.endOffset, rootNode);
            if (!omitChecksum) {
                serialized += "{" + getElementChecksum(rootNode) + "}";
            }
            return serialized;
        }

        var deserializeRegex = /^([^,]+),([^,\{]+)(\{([^}]+)\})?$/;

        function deserializeRange(serialized, rootNode, doc) {
            if (rootNode) {
                doc = doc || dom.getDocument(rootNode);
            } else {
                doc = doc || document;
                rootNode = doc.documentElement;
            }
            var result = deserializeRegex.exec(serialized);
            var checksum = result[4];
            if (checksum) {
                var rootNodeChecksum = getElementChecksum(rootNode);
                if (checksum !== rootNodeChecksum) {
                    throw module.createError("deserializeRange(): checksums of serialized range root node (" + checksum +
                        ") and target root node (" + rootNodeChecksum + ") do not match");
                }
            }
            var start = deserializePosition(result[1], rootNode, doc), end = deserializePosition(result[2], rootNode, doc);
            var range = api.createRange(doc);
            range.setStartAndEnd(start.node, start.offset, end.node, end.offset);
            return range;
        }

        function canDeserializeRange(serialized, rootNode, doc) {
            if (!rootNode) {
                rootNode = (doc || document).documentElement;
            }
            var result = deserializeRegex.exec(serialized);
            var checksum = result[3];
            return !checksum || checksum === getElementChecksum(rootNode);
        }

        function serializeSelection(selection, omitChecksum, rootNode) {
            selection = api.getSelection(selection);
            var ranges = selection.getAllRanges(), serializedRanges = [];
            for (var i = 0, len = ranges.length; i < len; ++i) {
                serializedRanges[i] = serializeRange(ranges[i], omitChecksum, rootNode);
            }
            return serializedRanges.join("|");
        }

        function deserializeSelection(serialized, rootNode, win) {
            if (rootNode) {
                win = win || dom.getWindow(rootNode);
            } else {
                win = win || window;
                rootNode = win.document.documentElement;
            }
            var serializedRanges = serialized.split("|");
            var sel = api.getSelection(win);
            var ranges = [];

            for (var i = 0, len = serializedRanges.length; i < len; ++i) {
                ranges[i] = deserializeRange(serializedRanges[i], rootNode, win.document);
            }
            sel.setRanges(ranges);

            return sel;
        }

        function canDeserializeSelection(serialized, rootNode, win) {
            var doc;
            if (rootNode) {
                doc = win ? win.document : dom.getDocument(rootNode);
            } else {
                win = win || window;
                rootNode = win.document.documentElement;
            }
            var serializedRanges = serialized.split("|");

            for (var i = 0, len = serializedRanges.length; i < len; ++i) {
                if (!canDeserializeRange(serializedRanges[i], rootNode, doc)) {
                    return false;
                }
            }

            return true;
        }

        var cookieName = "rangySerializedSelection";

        function getSerializedSelectionFromCookie(cookie) {
            var parts = cookie.split(/[;,]/);
            for (var i = 0, len = parts.length, nameVal, val; i < len; ++i) {
                nameVal = parts[i].split("=");
                if (nameVal[0].replace(/^\s+/, "") == cookieName) {
                    val = nameVal[1];
                    if (val) {
                        return decodeURIComponent(val.replace(/\s+$/, ""));
                    }
                }
            }
            return null;
        }

        function restoreSelectionFromCookie(win) {
            win = win || window;
            var serialized = getSerializedSelectionFromCookie(win.document.cookie);
            if (serialized) {
                deserializeSelection(serialized, win.doc);
            }
        }

        function saveSelectionCookie(win, props) {
            win = win || window;
            props = (typeof props == "object") ? props : {};
            var expires = props.expires ? ";expires=" + props.expires.toUTCString() : "";
            var path = props.path ? ";path=" + props.path : "";
            var domain = props.domain ? ";domain=" + props.domain : "";
            var secure = props.secure ? ";secure" : "";
            var serialized = serializeSelection(api.getSelection(win));
            win.document.cookie = encodeURIComponent(cookieName) + "=" + encodeURIComponent(serialized) + expires + path + domain + secure;
        }

        util.extend(api, {
            serializePosition: serializePosition,
            deserializePosition: deserializePosition,
            serializeRange: serializeRange,
            deserializeRange: deserializeRange,
            canDeserializeRange: canDeserializeRange,
            serializeSelection: serializeSelection,
            deserializeSelection: deserializeSelection,
            canDeserializeSelection: canDeserializeSelection,
            restoreSelectionFromCookie: restoreSelectionFromCookie,
            saveSelectionCookie: saveSelectionCookie,
            getElementChecksum: getElementChecksum,
            nodeToInfoString: nodeToInfoString
        });

        util.crc32 = crc32;
    });
    
    return rangy;
}, this);

/**
 * Class Applier module for Rangy.
 * Adds, removes and toggles classes on Ranges and Selections
 *
 * Part of Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Depends on Rangy core.
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */
(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {
    rangy.createModule("ClassApplier", ["WrappedSelection"], function(api, module) {
        var dom = api.dom;
        var DomPosition = dom.DomPosition;
        var contains = dom.arrayContains;
        var util = api.util;
        var forEach = util.forEach;


        var defaultTagName = "span";
        var createElementNSSupported = util.isHostMethod(document, "createElementNS");

        function each(obj, func) {
            for (var i in obj) {
                if (obj.hasOwnProperty(i)) {
                    if (func(i, obj[i]) === false) {
                        return false;
                    }
                }
            }
            return true;
        }

        function trim(str) {
            return str.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
        }

        function classNameContainsClass(fullClassName, className) {
            return !!fullClassName && new RegExp("(?:^|\\s)" + className + "(?:\\s|$)").test(fullClassName);
        }

        // Inefficient, inelegant nonsense for IE's svg element, which has no classList and non-HTML className implementation
        function hasClass(el, className) {
            if (typeof el.classList == "object") {
                return el.classList.contains(className);
            } else {
                var classNameSupported = (typeof el.className == "string");
                var elClass = classNameSupported ? el.className : el.getAttribute("class");
                return classNameContainsClass(elClass, className);
            }
        }

        function addClass(el, className) {
            if (typeof el.classList == "object") {
                el.classList.add(className);
            } else {
                var classNameSupported = (typeof el.className == "string");
                var elClass = classNameSupported ? el.className : el.getAttribute("class");
                if (elClass) {
                    if (!classNameContainsClass(elClass, className)) {
                        elClass += " " + className;
                    }
                } else {
                    elClass = className;
                }
                if (classNameSupported) {
                    el.className = elClass;
                } else {
                    el.setAttribute("class", elClass);
                }
            }
        }

        var removeClass = (function() {
            function replacer(matched, whiteSpaceBefore, whiteSpaceAfter) {
                return (whiteSpaceBefore && whiteSpaceAfter) ? " " : "";
            }

            return function(el, className) {
                if (typeof el.classList == "object") {
                    el.classList.remove(className);
                } else {
                    var classNameSupported = (typeof el.className == "string");
                    var elClass = classNameSupported ? el.className : el.getAttribute("class");
                    elClass = elClass.replace(new RegExp("(^|\\s)" + className + "(\\s|$)"), replacer);
                    if (classNameSupported) {
                        el.className = elClass;
                    } else {
                        el.setAttribute("class", elClass);
                    }
                }
            };
        })();

        function getClass(el) {
            var classNameSupported = (typeof el.className == "string");
            return classNameSupported ? el.className : el.getAttribute("class");
        }

        function sortClassName(className) {
            return className && className.split(/\s+/).sort().join(" ");
        }

        function getSortedClassName(el) {
            return sortClassName( getClass(el) );
        }

        function haveSameClasses(el1, el2) {
            return getSortedClassName(el1) == getSortedClassName(el2);
        }

        function hasAllClasses(el, className) {
            var classes = className.split(/\s+/);
            for (var i = 0, len = classes.length; i < len; ++i) {
                if (!hasClass(el, trim(classes[i]))) {
                    return false;
                }
            }
            return true;
        }

        function canTextBeStyled(textNode) {
            var parent = textNode.parentNode;
            return (parent && parent.nodeType == 1 && !/^(textarea|style|script|select|iframe)$/i.test(parent.nodeName));
        }

        function movePosition(position, oldParent, oldIndex, newParent, newIndex) {
            var posNode = position.node, posOffset = position.offset;
            var newNode = posNode, newOffset = posOffset;

            if (posNode == newParent && posOffset > newIndex) {
                ++newOffset;
            }

            if (posNode == oldParent && (posOffset == oldIndex  || posOffset == oldIndex + 1)) {
                newNode = newParent;
                newOffset += newIndex - oldIndex;
            }

            if (posNode == oldParent && posOffset > oldIndex + 1) {
                --newOffset;
            }

            position.node = newNode;
            position.offset = newOffset;
        }

        function movePositionWhenRemovingNode(position, parentNode, index) {
            if (position.node == parentNode && position.offset > index) {
                --position.offset;
            }
        }

        function movePreservingPositions(node, newParent, newIndex, positionsToPreserve) {
            // For convenience, allow newIndex to be -1 to mean "insert at the end".
            if (newIndex == -1) {
                newIndex = newParent.childNodes.length;
            }

            var oldParent = node.parentNode;
            var oldIndex = dom.getNodeIndex(node);

            forEach(positionsToPreserve, function(position) {
                movePosition(position, oldParent, oldIndex, newParent, newIndex);
            });

            // Now actually move the node.
            if (newParent.childNodes.length == newIndex) {
                newParent.appendChild(node);
            } else {
                newParent.insertBefore(node, newParent.childNodes[newIndex]);
            }
        }

        function removePreservingPositions(node, positionsToPreserve) {

            var oldParent = node.parentNode;
            var oldIndex = dom.getNodeIndex(node);

            forEach(positionsToPreserve, function(position) {
                movePositionWhenRemovingNode(position, oldParent, oldIndex);
            });

            dom.removeNode(node);
        }

        function moveChildrenPreservingPositions(node, newParent, newIndex, removeNode, positionsToPreserve) {
            var child, children = [];
            while ( (child = node.firstChild) ) {
                movePreservingPositions(child, newParent, newIndex++, positionsToPreserve);
                children.push(child);
            }
            if (removeNode) {
                removePreservingPositions(node, positionsToPreserve);
            }
            return children;
        }

        function replaceWithOwnChildrenPreservingPositions(element, positionsToPreserve) {
            return moveChildrenPreservingPositions(element, element.parentNode, dom.getNodeIndex(element), true, positionsToPreserve);
        }

        function rangeSelectsAnyText(range, textNode) {
            var textNodeRange = range.cloneRange();
            textNodeRange.selectNodeContents(textNode);

            var intersectionRange = textNodeRange.intersection(range);
            var text = intersectionRange ? intersectionRange.toString() : "";

            return text != "";
        }

        function getEffectiveTextNodes(range) {
            var nodes = range.getNodes([3]);

            // Optimization as per issue 145

            // Remove non-intersecting text nodes from the start of the range
            var start = 0, node;
            while ( (node = nodes[start]) && !rangeSelectsAnyText(range, node) ) {
                ++start;
            }

            // Remove non-intersecting text nodes from the start of the range
            var end = nodes.length - 1;
            while ( (node = nodes[end]) && !rangeSelectsAnyText(range, node) ) {
                --end;
            }

            return nodes.slice(start, end + 1);
        }

        function elementsHaveSameNonClassAttributes(el1, el2) {
            if (el1.attributes.length != el2.attributes.length) return false;
            for (var i = 0, len = el1.attributes.length, attr1, attr2, name; i < len; ++i) {
                attr1 = el1.attributes[i];
                name = attr1.name;
                if (name != "class") {
                    attr2 = el2.attributes.getNamedItem(name);
                    if ( (attr1 === null) != (attr2 === null) ) return false;
                    if (attr1.specified != attr2.specified) return false;
                    if (attr1.specified && attr1.nodeValue !== attr2.nodeValue) return false;
                }
            }
            return true;
        }

        function elementHasNonClassAttributes(el, exceptions) {
            for (var i = 0, len = el.attributes.length, attrName; i < len; ++i) {
                attrName = el.attributes[i].name;
                if ( !(exceptions && contains(exceptions, attrName)) && el.attributes[i].specified && attrName != "class") {
                    return true;
                }
            }
            return false;
        }

        var getComputedStyleProperty = dom.getComputedStyleProperty;
        var isEditableElement = (function() {
            var testEl = document.createElement("div");
            return typeof testEl.isContentEditable == "boolean" ?
                function (node) {
                    return node && node.nodeType == 1 && node.isContentEditable;
                } :
                function (node) {
                    if (!node || node.nodeType != 1 || node.contentEditable == "false") {
                        return false;
                    }
                    return node.contentEditable == "true" || isEditableElement(node.parentNode);
                };
        })();

        function isEditingHost(node) {
            var parent;
            return node && node.nodeType == 1 &&
                (( (parent = node.parentNode) && parent.nodeType == 9 && parent.designMode == "on") ||
                (isEditableElement(node) && !isEditableElement(node.parentNode)));
        }

        function isEditable(node) {
            return (isEditableElement(node) || (node.nodeType != 1 && isEditableElement(node.parentNode))) && !isEditingHost(node);
        }

        var inlineDisplayRegex = /^inline(-block|-table)?$/i;

        function isNonInlineElement(node) {
            return node && node.nodeType == 1 && !inlineDisplayRegex.test(getComputedStyleProperty(node, "display"));
        }

        // White space characters as defined by HTML 4 (http://www.w3.org/TR/html401/struct/text.html)
        var htmlNonWhiteSpaceRegex = /[^\r\n\t\f \u200B]/;

        function isUnrenderedWhiteSpaceNode(node) {
            if (node.data.length == 0) {
                return true;
            }
            if (htmlNonWhiteSpaceRegex.test(node.data)) {
                return false;
            }
            var cssWhiteSpace = getComputedStyleProperty(node.parentNode, "whiteSpace");
            switch (cssWhiteSpace) {
                case "pre":
                case "pre-wrap":
                case "-moz-pre-wrap":
                    return false;
                case "pre-line":
                    if (/[\r\n]/.test(node.data)) {
                        return false;
                    }
            }

            // We now have a whitespace-only text node that may be rendered depending on its context. If it is adjacent to a
            // non-inline element, it will not be rendered. This seems to be a good enough definition.
            return isNonInlineElement(node.previousSibling) || isNonInlineElement(node.nextSibling);
        }

        function getRangeBoundaries(ranges) {
            var positions = [], i, range;
            for (i = 0; range = ranges[i++]; ) {
                positions.push(
                    new DomPosition(range.startContainer, range.startOffset),
                    new DomPosition(range.endContainer, range.endOffset)
                );
            }
            return positions;
        }

        function updateRangesFromBoundaries(ranges, positions) {
            for (var i = 0, range, start, end, len = ranges.length; i < len; ++i) {
                range = ranges[i];
                start = positions[i * 2];
                end = positions[i * 2 + 1];
                range.setStartAndEnd(start.node, start.offset, end.node, end.offset);
            }
        }

        function isSplitPoint(node, offset) {
            if (dom.isCharacterDataNode(node)) {
                if (offset == 0) {
                    return !!node.previousSibling;
                } else if (offset == node.length) {
                    return !!node.nextSibling;
                } else {
                    return true;
                }
            }

            return offset > 0 && offset < node.childNodes.length;
        }

        function splitNodeAt(node, descendantNode, descendantOffset, positionsToPreserve) {
            var newNode, parentNode;
            var splitAtStart = (descendantOffset == 0);

            if (dom.isAncestorOf(descendantNode, node)) {
                return node;
            }

            if (dom.isCharacterDataNode(descendantNode)) {
                var descendantIndex = dom.getNodeIndex(descendantNode);
                if (descendantOffset == 0) {
                    descendantOffset = descendantIndex;
                } else if (descendantOffset == descendantNode.length) {
                    descendantOffset = descendantIndex + 1;
                } else {
                    throw module.createError("splitNodeAt() should not be called with offset in the middle of a data node (" +
                        descendantOffset + " in " + descendantNode.data);
                }
                descendantNode = descendantNode.parentNode;
            }

            if (isSplitPoint(descendantNode, descendantOffset)) {
                // descendantNode is now guaranteed not to be a text or other character node
                newNode = descendantNode.cloneNode(false);
                parentNode = descendantNode.parentNode;
                if (newNode.id) {
                    newNode.removeAttribute("id");
                }
                var child, newChildIndex = 0;

                while ( (child = descendantNode.childNodes[descendantOffset]) ) {
                    movePreservingPositions(child, newNode, newChildIndex++, positionsToPreserve);
                }
                movePreservingPositions(newNode, parentNode, dom.getNodeIndex(descendantNode) + 1, positionsToPreserve);
                return (descendantNode == node) ? newNode : splitNodeAt(node, parentNode, dom.getNodeIndex(newNode), positionsToPreserve);
            } else if (node != descendantNode) {
                newNode = descendantNode.parentNode;

                // Work out a new split point in the parent node
                var newNodeIndex = dom.getNodeIndex(descendantNode);

                if (!splitAtStart) {
                    newNodeIndex++;
                }
                return splitNodeAt(node, newNode, newNodeIndex, positionsToPreserve);
            }
            return node;
        }

        function areElementsMergeable(el1, el2) {
            return el1.namespaceURI == el2.namespaceURI &&
                el1.tagName.toLowerCase() == el2.tagName.toLowerCase() &&
                haveSameClasses(el1, el2) &&
                elementsHaveSameNonClassAttributes(el1, el2) &&
                getComputedStyleProperty(el1, "display") == "inline" &&
                getComputedStyleProperty(el2, "display") == "inline";
        }

        function createAdjacentMergeableTextNodeGetter(forward) {
            var siblingPropName = forward ? "nextSibling" : "previousSibling";

            return function(textNode, checkParentElement) {
                var el = textNode.parentNode;
                var adjacentNode = textNode[siblingPropName];
                if (adjacentNode) {
                    // Can merge if the node's previous/next sibling is a text node
                    if (adjacentNode && adjacentNode.nodeType == 3) {
                        return adjacentNode;
                    }
                } else if (checkParentElement) {
                    // Compare text node parent element with its sibling
                    adjacentNode = el[siblingPropName];
                    if (adjacentNode && adjacentNode.nodeType == 1 && areElementsMergeable(el, adjacentNode)) {
                        var adjacentNodeChild = adjacentNode[forward ? "firstChild" : "lastChild"];
                        if (adjacentNodeChild && adjacentNodeChild.nodeType == 3) {
                            return adjacentNodeChild;
                        }
                    }
                }
                return null;
            };
        }

        var getPreviousMergeableTextNode = createAdjacentMergeableTextNodeGetter(false),
            getNextMergeableTextNode = createAdjacentMergeableTextNodeGetter(true);

    
        function Merge(firstNode) {
            this.isElementMerge = (firstNode.nodeType == 1);
            this.textNodes = [];
            var firstTextNode = this.isElementMerge ? firstNode.lastChild : firstNode;
            if (firstTextNode) {
                this.textNodes[0] = firstTextNode;
            }
        }

        Merge.prototype = {
            doMerge: function(positionsToPreserve) {
                var textNodes = this.textNodes;
                var firstTextNode = textNodes[0];
                if (textNodes.length > 1) {
                    var firstTextNodeIndex = dom.getNodeIndex(firstTextNode);
                    var textParts = [], combinedTextLength = 0, textNode, parent;
                    forEach(textNodes, function(textNode, i) {
                        parent = textNode.parentNode;
                        if (i > 0) {
                            parent.removeChild(textNode);
                            if (!parent.hasChildNodes()) {
                                dom.removeNode(parent);
                            }
                            if (positionsToPreserve) {
                                forEach(positionsToPreserve, function(position) {
                                    // Handle case where position is inside the text node being merged into a preceding node
                                    if (position.node == textNode) {
                                        position.node = firstTextNode;
                                        position.offset += combinedTextLength;
                                    }
                                    // Handle case where both text nodes precede the position within the same parent node
                                    if (position.node == parent && position.offset > firstTextNodeIndex) {
                                        --position.offset;
                                        if (position.offset == firstTextNodeIndex + 1 && i < len - 1) {
                                            position.node = firstTextNode;
                                            position.offset = combinedTextLength;
                                        }
                                    }
                                });
                            }
                        }
                        textParts[i] = textNode.data;
                        combinedTextLength += textNode.data.length;
                    });
                    firstTextNode.data = textParts.join("");
                }
                return firstTextNode.data;
            },

            getLength: function() {
                var i = this.textNodes.length, len = 0;
                while (i--) {
                    len += this.textNodes[i].length;
                }
                return len;
            },

            toString: function() {
                var textParts = [];
                forEach(this.textNodes, function(textNode, i) {
                    textParts[i] = "'" + textNode.data + "'";
                });
                return "[Merge(" + textParts.join(",") + ")]";
            }
        };

        var optionProperties = ["elementTagName", "ignoreWhiteSpace", "applyToEditableOnly", "useExistingElements",
            "removeEmptyElements", "onElementCreate"];

        // TODO: Populate this with every attribute name that corresponds to a property with a different name. Really??
        var attrNamesForProperties = {};

        function ClassApplier(className, options, tagNames) {
            var normalize, i, len, propName, applier = this;
            applier.cssClass = applier.className = className; // cssClass property is for backward compatibility

            var elementPropertiesFromOptions = null, elementAttributes = {};

            // Initialize from options object
            if (typeof options == "object" && options !== null) {
                if (typeof options.elementTagName !== "undefined") {
                    options.elementTagName = options.elementTagName.toLowerCase();
                }
                tagNames = options.tagNames;
                elementPropertiesFromOptions = options.elementProperties;
                elementAttributes = options.elementAttributes;

                for (i = 0; propName = optionProperties[i++]; ) {
                    if (options.hasOwnProperty(propName)) {
                        applier[propName] = options[propName];
                    }
                }
                normalize = options.normalize;
            } else {
                normalize = options;
            }

            // Backward compatibility: the second parameter can also be a Boolean indicating to normalize after unapplying
            applier.normalize = (typeof normalize == "undefined") ? true : normalize;

            // Initialize element properties and attribute exceptions
            applier.attrExceptions = [];
            var el = document.createElement(applier.elementTagName);
            applier.elementProperties = applier.copyPropertiesToElement(elementPropertiesFromOptions, el, true);
            each(elementAttributes, function(attrName, attrValue) {
                applier.attrExceptions.push(attrName);
                // Ensure each attribute value is a string
                elementAttributes[attrName] = "" + attrValue;
            });
            applier.elementAttributes = elementAttributes;

            applier.elementSortedClassName = applier.elementProperties.hasOwnProperty("className") ?
                sortClassName(applier.elementProperties.className + " " + className) : className;

            // Initialize tag names
            applier.applyToAnyTagName = false;
            var type = typeof tagNames;
            if (type == "string") {
                if (tagNames == "*") {
                    applier.applyToAnyTagName = true;
                } else {
                    applier.tagNames = trim(tagNames.toLowerCase()).split(/\s*,\s*/);
                }
            } else if (type == "object" && typeof tagNames.length == "number") {
                applier.tagNames = [];
                for (i = 0, len = tagNames.length; i < len; ++i) {
                    if (tagNames[i] == "*") {
                        applier.applyToAnyTagName = true;
                    } else {
                        applier.tagNames.push(tagNames[i].toLowerCase());
                    }
                }
            } else {
                applier.tagNames = [applier.elementTagName];
            }
        }

        ClassApplier.prototype = {
            elementTagName: defaultTagName,
            elementProperties: {},
            elementAttributes: {},
            ignoreWhiteSpace: true,
            applyToEditableOnly: false,
            useExistingElements: true,
            removeEmptyElements: true,
            onElementCreate: null,

            copyPropertiesToElement: function(props, el, createCopy) {
                var s, elStyle, elProps = {}, elPropsStyle, propValue, elPropValue, attrName;

                for (var p in props) {
                    if (props.hasOwnProperty(p)) {
                        propValue = props[p];
                        elPropValue = el[p];

                        // Special case for class. The copied properties object has the applier's class as well as its own
                        // to simplify checks when removing styling elements
                        if (p == "className") {
                            addClass(el, propValue);
                            addClass(el, this.className);
                            el[p] = sortClassName(el[p]);
                            if (createCopy) {
                                elProps[p] = propValue;
                            }
                        }

                        // Special case for style
                        else if (p == "style") {
                            elStyle = elPropValue;
                            if (createCopy) {
                                elProps[p] = elPropsStyle = {};
                            }
                            for (s in props[p]) {
                                if (props[p].hasOwnProperty(s)) {
                                    elStyle[s] = propValue[s];
                                    if (createCopy) {
                                        elPropsStyle[s] = elStyle[s];
                                    }
                                }
                            }
                            this.attrExceptions.push(p);
                        } else {
                            el[p] = propValue;
                            // Copy the property back from the dummy element so that later comparisons to check whether
                            // elements may be removed are checking against the right value. For example, the href property
                            // of an element returns a fully qualified URL even if it was previously assigned a relative
                            // URL.
                            if (createCopy) {
                                elProps[p] = el[p];

                                // Not all properties map to identically-named attributes
                                attrName = attrNamesForProperties.hasOwnProperty(p) ? attrNamesForProperties[p] : p;
                                this.attrExceptions.push(attrName);
                            }
                        }
                    }
                }

                return createCopy ? elProps : "";
            },

            copyAttributesToElement: function(attrs, el) {
                for (var attrName in attrs) {
                    if (attrs.hasOwnProperty(attrName) && !/^class(?:Name)?$/i.test(attrName)) {
                        el.setAttribute(attrName, attrs[attrName]);
                    }
                }
            },

            appliesToElement: function(el) {
                return contains(this.tagNames, el.tagName.toLowerCase());
            },

            getEmptyElements: function(range) {
                var applier = this;
                return range.getNodes([1], function(el) {
                    return applier.appliesToElement(el) && !el.hasChildNodes();
                });
            },

            hasClass: function(node) {
                return node.nodeType == 1 &&
                    (this.applyToAnyTagName || this.appliesToElement(node)) &&
                    hasClass(node, this.className);
            },

            getSelfOrAncestorWithClass: function(node) {
                while (node) {
                    if (this.hasClass(node)) {
                        return node;
                    }
                    node = node.parentNode;
                }
                return null;
            },

            isModifiable: function(node) {
                return !this.applyToEditableOnly || isEditable(node);
            },

            // White space adjacent to an unwrappable node can be ignored for wrapping
            isIgnorableWhiteSpaceNode: function(node) {
                return this.ignoreWhiteSpace && node && node.nodeType == 3 && isUnrenderedWhiteSpaceNode(node);
            },

            // Normalizes nodes after applying a class to a Range.
            postApply: function(textNodes, range, positionsToPreserve, isUndo) {
                var firstNode = textNodes[0], lastNode = textNodes[textNodes.length - 1];

                var merges = [], currentMerge;

                var rangeStartNode = firstNode, rangeEndNode = lastNode;
                var rangeStartOffset = 0, rangeEndOffset = lastNode.length;

                var textNode, precedingTextNode;

                // Check for every required merge and create a Merge object for each
                forEach(textNodes, function(textNode) {
                    precedingTextNode = getPreviousMergeableTextNode(textNode, !isUndo);
                    if (precedingTextNode) {
                        if (!currentMerge) {
                            currentMerge = new Merge(precedingTextNode);
                            merges.push(currentMerge);
                        }
                        currentMerge.textNodes.push(textNode);
                        if (textNode === firstNode) {
                            rangeStartNode = currentMerge.textNodes[0];
                            rangeStartOffset = rangeStartNode.length;
                        }
                        if (textNode === lastNode) {
                            rangeEndNode = currentMerge.textNodes[0];
                            rangeEndOffset = currentMerge.getLength();
                        }
                    } else {
                        currentMerge = null;
                    }
                });

                // Test whether the first node after the range needs merging
                var nextTextNode = getNextMergeableTextNode(lastNode, !isUndo);

                if (nextTextNode) {
                    if (!currentMerge) {
                        currentMerge = new Merge(lastNode);
                        merges.push(currentMerge);
                    }
                    currentMerge.textNodes.push(nextTextNode);
                }

                // Apply the merges
                if (merges.length) {
                    for (i = 0, len = merges.length; i < len; ++i) {
                        merges[i].doMerge(positionsToPreserve);
                    }

                    // Set the range boundaries
                    range.setStartAndEnd(rangeStartNode, rangeStartOffset, rangeEndNode, rangeEndOffset);
                }
            },

            createContainer: function(parentNode) {
                var doc = dom.getDocument(parentNode);
                var namespace;
                var el = createElementNSSupported && !dom.isHtmlNamespace(parentNode) && (namespace = parentNode.namespaceURI) ?
                    doc.createElementNS(parentNode.namespaceURI, this.elementTagName) :
                    doc.createElement(this.elementTagName);

                this.copyPropertiesToElement(this.elementProperties, el, false);
                this.copyAttributesToElement(this.elementAttributes, el);
                addClass(el, this.className);
                if (this.onElementCreate) {
                    this.onElementCreate(el, this);
                }
                return el;
            },

            elementHasProperties: function(el, props) {
                var applier = this;
                return each(props, function(p, propValue) {
                    if (p == "className") {
                        // For checking whether we should reuse an existing element, we just want to check that the element
                        // has all the classes specified in the className property. When deciding whether the element is
                        // removable when unapplying a class, there is separate special handling to check whether the
                        // element has extra classes so the same simple check will do.
                        return hasAllClasses(el, propValue);
                    } else if (typeof propValue == "object") {
                        if (!applier.elementHasProperties(el[p], propValue)) {
                            return false;
                        }
                    } else if (el[p] !== propValue) {
                        return false;
                    }
                });
            },

            elementHasAttributes: function(el, attrs) {
                return each(attrs, function(name, value) {
                    if (el.getAttribute(name) !== value) {
                        return false;
                    }
                });
            },

            applyToTextNode: function(textNode, positionsToPreserve) {

                // Check whether the text node can be styled. Text within a <style> or <script> element, for example,
                // should not be styled. See issue 283.
                if (canTextBeStyled(textNode)) {
                    var parent = textNode.parentNode;
                    if (parent.childNodes.length == 1 &&
                        this.useExistingElements &&
                        this.appliesToElement(parent) &&
                        this.elementHasProperties(parent, this.elementProperties) &&
                        this.elementHasAttributes(parent, this.elementAttributes)) {

                        addClass(parent, this.className);
                    } else {
                        var textNodeParent = textNode.parentNode;
                        var el = this.createContainer(textNodeParent);
                        textNodeParent.insertBefore(el, textNode);
                        el.appendChild(textNode);
                    }
                }

            },

            isRemovable: function(el) {
                return el.tagName.toLowerCase() == this.elementTagName &&
                    getSortedClassName(el) == this.elementSortedClassName &&
                    this.elementHasProperties(el, this.elementProperties) &&
                    !elementHasNonClassAttributes(el, this.attrExceptions) &&
                    this.elementHasAttributes(el, this.elementAttributes) &&
                    this.isModifiable(el);
            },

            isEmptyContainer: function(el) {
                var childNodeCount = el.childNodes.length;
                return el.nodeType == 1 &&
                    this.isRemovable(el) &&
                    (childNodeCount == 0 || (childNodeCount == 1 && this.isEmptyContainer(el.firstChild)));
            },

            removeEmptyContainers: function(range) {
                var applier = this;
                var nodesToRemove = range.getNodes([1], function(el) {
                    return applier.isEmptyContainer(el);
                });

                var rangesToPreserve = [range];
                var positionsToPreserve = getRangeBoundaries(rangesToPreserve);

                forEach(nodesToRemove, function(node) {
                    removePreservingPositions(node, positionsToPreserve);
                });

                // Update the range from the preserved boundary positions
                updateRangesFromBoundaries(rangesToPreserve, positionsToPreserve);
            },

            undoToTextNode: function(textNode, range, ancestorWithClass, positionsToPreserve) {
                if (!range.containsNode(ancestorWithClass)) {
                    // Split out the portion of the ancestor from which we can remove the class
                    //var parent = ancestorWithClass.parentNode, index = dom.getNodeIndex(ancestorWithClass);
                    var ancestorRange = range.cloneRange();
                    ancestorRange.selectNode(ancestorWithClass);
                    if (ancestorRange.isPointInRange(range.endContainer, range.endOffset)) {
                        splitNodeAt(ancestorWithClass, range.endContainer, range.endOffset, positionsToPreserve);
                        range.setEndAfter(ancestorWithClass);
                    }
                    if (ancestorRange.isPointInRange(range.startContainer, range.startOffset)) {
                        ancestorWithClass = splitNodeAt(ancestorWithClass, range.startContainer, range.startOffset, positionsToPreserve);
                    }
                }

                if (this.isRemovable(ancestorWithClass)) {
                    replaceWithOwnChildrenPreservingPositions(ancestorWithClass, positionsToPreserve);
                } else {
                    removeClass(ancestorWithClass, this.className);
                }
            },

            splitAncestorWithClass: function(container, offset, positionsToPreserve) {
                var ancestorWithClass = this.getSelfOrAncestorWithClass(container);
                if (ancestorWithClass) {
                    splitNodeAt(ancestorWithClass, container, offset, positionsToPreserve);
                }
            },

            undoToAncestor: function(ancestorWithClass, positionsToPreserve) {
                if (this.isRemovable(ancestorWithClass)) {
                    replaceWithOwnChildrenPreservingPositions(ancestorWithClass, positionsToPreserve);
                } else {
                    removeClass(ancestorWithClass, this.className);
                }
            },

            applyToRange: function(range, rangesToPreserve) {
                var applier = this;
                rangesToPreserve = rangesToPreserve || [];

                // Create an array of range boundaries to preserve
                var positionsToPreserve = getRangeBoundaries(rangesToPreserve || []);

                range.splitBoundariesPreservingPositions(positionsToPreserve);

                // Tidy up the DOM by removing empty containers
                if (applier.removeEmptyElements) {
                    applier.removeEmptyContainers(range);
                }

                var textNodes = getEffectiveTextNodes(range);

                if (textNodes.length) {
                    forEach(textNodes, function(textNode) {
                        if (!applier.isIgnorableWhiteSpaceNode(textNode) && !applier.getSelfOrAncestorWithClass(textNode) &&
                                applier.isModifiable(textNode)) {
                            applier.applyToTextNode(textNode, positionsToPreserve);
                        }
                    });
                    var lastTextNode = textNodes[textNodes.length - 1];
                    range.setStartAndEnd(textNodes[0], 0, lastTextNode, lastTextNode.length);
                    if (applier.normalize) {
                        applier.postApply(textNodes, range, positionsToPreserve, false);
                    }

                    // Update the ranges from the preserved boundary positions
                    updateRangesFromBoundaries(rangesToPreserve, positionsToPreserve);
                }

                // Apply classes to any appropriate empty elements
                var emptyElements = applier.getEmptyElements(range);

                forEach(emptyElements, function(el) {
                    addClass(el, applier.className);
                });
            },

            applyToRanges: function(ranges) {

                var i = ranges.length;
                while (i--) {
                    this.applyToRange(ranges[i], ranges);
                }


                return ranges;
            },

            applyToSelection: function(win) {
                var sel = api.getSelection(win);
                sel.setRanges( this.applyToRanges(sel.getAllRanges()) );
            },

            undoToRange: function(range, rangesToPreserve) {
                var applier = this;
                // Create an array of range boundaries to preserve
                rangesToPreserve = rangesToPreserve || [];
                var positionsToPreserve = getRangeBoundaries(rangesToPreserve);


                range.splitBoundariesPreservingPositions(positionsToPreserve);

                // Tidy up the DOM by removing empty containers
                if (applier.removeEmptyElements) {
                    applier.removeEmptyContainers(range, positionsToPreserve);
                }

                var textNodes = getEffectiveTextNodes(range);
                var textNode, ancestorWithClass;
                var lastTextNode = textNodes[textNodes.length - 1];

                if (textNodes.length) {
                    applier.splitAncestorWithClass(range.endContainer, range.endOffset, positionsToPreserve);
                    applier.splitAncestorWithClass(range.startContainer, range.startOffset, positionsToPreserve);
                    for (var i = 0, len = textNodes.length; i < len; ++i) {
                        textNode = textNodes[i];
                        ancestorWithClass = applier.getSelfOrAncestorWithClass(textNode);
                        if (ancestorWithClass && applier.isModifiable(textNode)) {
                            applier.undoToAncestor(ancestorWithClass, positionsToPreserve);
                        }
                    }
                    // Ensure the range is still valid
                    range.setStartAndEnd(textNodes[0], 0, lastTextNode, lastTextNode.length);


                    if (applier.normalize) {
                        applier.postApply(textNodes, range, positionsToPreserve, true);
                    }

                    // Update the ranges from the preserved boundary positions
                    updateRangesFromBoundaries(rangesToPreserve, positionsToPreserve);
                }

                // Remove class from any appropriate empty elements
                var emptyElements = applier.getEmptyElements(range);

                forEach(emptyElements, function(el) {
                    removeClass(el, applier.className);
                });
            },

            undoToRanges: function(ranges) {
                // Get ranges returned in document order
                var i = ranges.length;

                while (i--) {
                    this.undoToRange(ranges[i], ranges);
                }

                return ranges;
            },

            undoToSelection: function(win) {
                var sel = api.getSelection(win);
                var ranges = api.getSelection(win).getAllRanges();
                this.undoToRanges(ranges);
                sel.setRanges(ranges);
            },

            isAppliedToRange: function(range) {
                if (range.collapsed || range.toString() == "") {
                    return !!this.getSelfOrAncestorWithClass(range.commonAncestorContainer);
                } else {
                    var textNodes = range.getNodes( [3] );
                    if (textNodes.length)
                    for (var i = 0, textNode; textNode = textNodes[i++]; ) {
                        if (!this.isIgnorableWhiteSpaceNode(textNode) && rangeSelectsAnyText(range, textNode) &&
                                this.isModifiable(textNode) && !this.getSelfOrAncestorWithClass(textNode)) {
                            return false;
                        }
                    }
                    return true;
                }
            },

            isAppliedToRanges: function(ranges) {
                var i = ranges.length;
                if (i == 0) {
                    return false;
                }
                while (i--) {
                    if (!this.isAppliedToRange(ranges[i])) {
                        return false;
                    }
                }
                return true;
            },

            isAppliedToSelection: function(win) {
                var sel = api.getSelection(win);
                return this.isAppliedToRanges(sel.getAllRanges());
            },

            toggleRange: function(range) {
                if (this.isAppliedToRange(range)) {
                    this.undoToRange(range);
                } else {
                    this.applyToRange(range);
                }
            },

            toggleSelection: function(win) {
                if (this.isAppliedToSelection(win)) {
                    this.undoToSelection(win);
                } else {
                    this.applyToSelection(win);
                }
            },

            getElementsWithClassIntersectingRange: function(range) {
                var elements = [];
                var applier = this;
                range.getNodes([3], function(textNode) {
                    var el = applier.getSelfOrAncestorWithClass(textNode);
                    if (el && !contains(elements, el)) {
                        elements.push(el);
                    }
                });
                return elements;
            },

            detach: function() {}
        };

        function createClassApplier(className, options, tagNames) {
            return new ClassApplier(className, options, tagNames);
        }

        ClassApplier.util = {
            hasClass: hasClass,
            addClass: addClass,
            removeClass: removeClass,
            getClass: getClass,
            hasSameClasses: haveSameClasses,
            hasAllClasses: hasAllClasses,
            replaceWithOwnChildren: replaceWithOwnChildrenPreservingPositions,
            elementsHaveSameNonClassAttributes: elementsHaveSameNonClassAttributes,
            elementHasNonClassAttributes: elementHasNonClassAttributes,
            splitNodeAt: splitNodeAt,
            isEditableElement: isEditableElement,
            isEditingHost: isEditingHost,
            isEditable: isEditable
        };

        api.CssClassApplier = api.ClassApplier = ClassApplier;
        api.createClassApplier = createClassApplier;
        util.createAliasForDeprecatedMethod(api, "createCssClassApplier", "createClassApplier", module);
    });
    
    return rangy;
}, this);

/**
 * Highlighter module for Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Depends on Rangy core, ClassApplier and optionally TextRange modules.
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */
(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {
    rangy.createModule("Highlighter", ["ClassApplier"], function(api, module) {
        var dom = api.dom;
        var contains = dom.arrayContains;
        var getBody = dom.getBody;
        var createOptions = api.util.createOptions;
        var forEach = api.util.forEach;
        var nextHighlightId = 1;

        // Puts highlights in order, last in document first.
        function compareHighlights(h1, h2) {
            return h1.characterRange.start - h2.characterRange.start;
        }

        function getContainerElement(doc, id) {
            return id ? doc.getElementById(id) : getBody(doc);
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        var highlighterTypes = {};

        function HighlighterType(type, converterCreator) {
            this.type = type;
            this.converterCreator = converterCreator;
        }

        HighlighterType.prototype.create = function() {
            var converter = this.converterCreator();
            converter.type = this.type;
            return converter;
        };

        function registerHighlighterType(type, converterCreator) {
            highlighterTypes[type] = new HighlighterType(type, converterCreator);
        }

        function getConverter(type) {
            var highlighterType = highlighterTypes[type];
            if (highlighterType instanceof HighlighterType) {
                return highlighterType.create();
            } else {
                throw new Error("Highlighter type '" + type + "' is not valid");
            }
        }

        api.registerHighlighterType = registerHighlighterType;

        /*----------------------------------------------------------------------------------------------------------------*/

        function CharacterRange(start, end) {
            this.start = start;
            this.end = end;
        }

        CharacterRange.prototype = {
            intersects: function(charRange) {
                return this.start < charRange.end && this.end > charRange.start;
            },

            isContiguousWith: function(charRange) {
                return this.start == charRange.end || this.end == charRange.start;
            },

            union: function(charRange) {
                return new CharacterRange(Math.min(this.start, charRange.start), Math.max(this.end, charRange.end));
            },

            intersection: function(charRange) {
                return new CharacterRange(Math.max(this.start, charRange.start), Math.min(this.end, charRange.end));
            },

            getComplements: function(charRange) {
                var ranges = [];
                if (this.start >= charRange.start) {
                    if (this.end <= charRange.end) {
                        return [];
                    }
                    ranges.push(new CharacterRange(charRange.end, this.end));
                } else {
                    ranges.push(new CharacterRange(this.start, Math.min(this.end, charRange.start)));
                    if (this.end > charRange.end) {
                        ranges.push(new CharacterRange(charRange.end, this.end));
                    }
                }
                return ranges;
            },

            toString: function() {
                return "[CharacterRange(" + this.start + ", " + this.end + ")]";
            }
        };

        CharacterRange.fromCharacterRange = function(charRange) {
            return new CharacterRange(charRange.start, charRange.end);
        };

        /*----------------------------------------------------------------------------------------------------------------*/

        var textContentConverter = {
            rangeToCharacterRange: function(range, containerNode) {
                var bookmark = range.getBookmark(containerNode);
                return new CharacterRange(bookmark.start, bookmark.end);
            },

            characterRangeToRange: function(doc, characterRange, containerNode) {
                var range = api.createRange(doc);
                range.moveToBookmark({
                    start: characterRange.start,
                    end: characterRange.end,
                    containerNode: containerNode
                });

                return range;
            },

            serializeSelection: function(selection, containerNode) {
                var ranges = selection.getAllRanges(), rangeCount = ranges.length;
                var rangeInfos = [];

                var backward = rangeCount == 1 && selection.isBackward();

                for (var i = 0, len = ranges.length; i < len; ++i) {
                    rangeInfos[i] = {
                        characterRange: this.rangeToCharacterRange(ranges[i], containerNode),
                        backward: backward
                    };
                }

                return rangeInfos;
            },

            restoreSelection: function(selection, savedSelection, containerNode) {
                selection.removeAllRanges();
                var doc = selection.win.document;
                for (var i = 0, len = savedSelection.length, range, rangeInfo, characterRange; i < len; ++i) {
                    rangeInfo = savedSelection[i];
                    characterRange = rangeInfo.characterRange;
                    range = this.characterRangeToRange(doc, rangeInfo.characterRange, containerNode);
                    selection.addRange(range, rangeInfo.backward);
                }
            }
        };

        registerHighlighterType("textContent", function() {
            return textContentConverter;
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        // Lazily load the TextRange-based converter so that the dependency is only checked when required.
        registerHighlighterType("TextRange", (function() {
            var converter;

            return function() {
                if (!converter) {
                    // Test that textRangeModule exists and is supported
                    var textRangeModule = api.modules.TextRange;
                    if (!textRangeModule) {
                        throw new Error("TextRange module is missing.");
                    } else if (!textRangeModule.supported) {
                        throw new Error("TextRange module is present but not supported.");
                    }

                    converter = {
                        rangeToCharacterRange: function(range, containerNode) {
                            return CharacterRange.fromCharacterRange( range.toCharacterRange(containerNode) );
                        },

                        characterRangeToRange: function(doc, characterRange, containerNode) {
                            var range = api.createRange(doc);
                            range.selectCharacters(containerNode, characterRange.start, characterRange.end);
                            return range;
                        },

                        serializeSelection: function(selection, containerNode) {
                            return selection.saveCharacterRanges(containerNode);
                        },

                        restoreSelection: function(selection, savedSelection, containerNode) {
                            selection.restoreCharacterRanges(containerNode, savedSelection);
                        }
                    };
                }

                return converter;
            };
        })());

        /*----------------------------------------------------------------------------------------------------------------*/

        function Highlight(doc, characterRange, classApplier, converter, id, containerElementId) {
            if (id) {
                this.id = id;
                nextHighlightId = Math.max(nextHighlightId, id + 1);
            } else {
                this.id = nextHighlightId++;
            }
            this.characterRange = characterRange;
            this.doc = doc;
            this.classApplier = classApplier;
            this.converter = converter;
            this.containerElementId = containerElementId || null;
            this.applied = false;
        }

        Highlight.prototype = {
            getContainerElement: function() {
                return getContainerElement(this.doc, this.containerElementId);
            },

            getRange: function() {
                return this.converter.characterRangeToRange(this.doc, this.characterRange, this.getContainerElement());
            },

            fromRange: function(range) {
                this.characterRange = this.converter.rangeToCharacterRange(range, this.getContainerElement());
            },

            getText: function() {
                return this.getRange().toString();
            },

            containsElement: function(el) {
                return this.getRange().containsNodeContents(el.firstChild);
            },

            unapply: function() {
                this.classApplier.undoToRange(this.getRange());
                this.applied = false;
            },

            apply: function() {
                this.classApplier.applyToRange(this.getRange());
                this.applied = true;
            },

            getHighlightElements: function() {
                return this.classApplier.getElementsWithClassIntersectingRange(this.getRange());
            },

            toString: function() {
                return "[Highlight(ID: " + this.id + ", class: " + this.classApplier.className + ", character range: " +
                    this.characterRange.start + " - " + this.characterRange.end + ")]";
            }
        };

        /*----------------------------------------------------------------------------------------------------------------*/

        function Highlighter(doc, type) {
            type = type || "textContent";
            this.doc = doc || document;
            this.classAppliers = {};
            this.highlights = [];
            this.converter = getConverter(type);
        }

        Highlighter.prototype = {
            addClassApplier: function(classApplier) {
                this.classAppliers[classApplier.className] = classApplier;
            },

            getHighlightForElement: function(el) {
                var highlights = this.highlights;
                for (var i = 0, len = highlights.length; i < len; ++i) {
                    if (highlights[i].containsElement(el)) {
                        return highlights[i];
                    }
                }
                return null;
            },

            removeHighlights: function(highlights) {
                for (var i = 0, len = this.highlights.length, highlight; i < len; ++i) {
                    highlight = this.highlights[i];
                    if (contains(highlights, highlight)) {
                        highlight.unapply();
                        this.highlights.splice(i--, 1);
                    }
                }
            },

            removeAllHighlights: function() {
                this.removeHighlights(this.highlights);
            },

            getIntersectingHighlights: function(ranges) {
                // Test each range against each of the highlighted ranges to see whether they overlap
                var intersectingHighlights = [], highlights = this.highlights;
                forEach(ranges, function(range) {
                    //var selCharRange = converter.rangeToCharacterRange(range);
                    forEach(highlights, function(highlight) {
                        if (range.intersectsRange( highlight.getRange() ) && !contains(intersectingHighlights, highlight)) {
                            intersectingHighlights.push(highlight);
                        }
                    });
                });

                return intersectingHighlights;
            },

            highlightCharacterRanges: function(className, charRanges, options) {
                var i, len, j;
                var highlights = this.highlights;
                var converter = this.converter;
                var doc = this.doc;
                var highlightsToRemove = [];
                var classApplier = className ? this.classAppliers[className] : null;

                options = createOptions(options, {
                    containerElementId: null,
                    exclusive: true
                });

                var containerElementId = options.containerElementId;
                var exclusive = options.exclusive;

                var containerElement, containerElementRange, containerElementCharRange;
                if (containerElementId) {
                    containerElement = this.doc.getElementById(containerElementId);
                    if (containerElement) {
                        containerElementRange = api.createRange(this.doc);
                        containerElementRange.selectNodeContents(containerElement);
                        containerElementCharRange = new CharacterRange(0, containerElementRange.toString().length);
                    }
                }

                var charRange, highlightCharRange, removeHighlight, isSameClassApplier, highlightsToKeep, splitHighlight;

                for (i = 0, len = charRanges.length; i < len; ++i) {
                    charRange = charRanges[i];
                    highlightsToKeep = [];

                    // Restrict character range to container element, if it exists
                    if (containerElementCharRange) {
                        charRange = charRange.intersection(containerElementCharRange);
                    }

                    // Ignore empty ranges
                    if (charRange.start == charRange.end) {
                        continue;
                    }

                    // Check for intersection with existing highlights. For each intersection, create a new highlight
                    // which is the union of the highlight range and the selected range
                    for (j = 0; j < highlights.length; ++j) {
                        removeHighlight = false;

                        if (containerElementId == highlights[j].containerElementId) {
                            highlightCharRange = highlights[j].characterRange;
                            isSameClassApplier = (classApplier == highlights[j].classApplier);
                            splitHighlight = !isSameClassApplier && exclusive;

                            // Replace the existing highlight if it needs to be:
                            //  1. merged (isSameClassApplier)
                            //  2. partially or entirely erased (className === null)
                            //  3. partially or entirely replaced (isSameClassApplier == false && exclusive == true)
                            if (    (highlightCharRange.intersects(charRange) || highlightCharRange.isContiguousWith(charRange)) &&
                                    (isSameClassApplier || splitHighlight) ) {

                                // Remove existing highlights, keeping the unselected parts
                                if (splitHighlight) {
                                    forEach(highlightCharRange.getComplements(charRange), function(rangeToAdd) {
                                        highlightsToKeep.push( new Highlight(doc, rangeToAdd, highlights[j].classApplier, converter, null, containerElementId) );
                                    });
                                }

                                removeHighlight = true;
                                if (isSameClassApplier) {
                                    charRange = highlightCharRange.union(charRange);
                                }
                            }
                        }

                        if (removeHighlight) {
                            highlightsToRemove.push(highlights[j]);
                            highlights[j] = new Highlight(doc, highlightCharRange.union(charRange), classApplier, converter, null, containerElementId);
                        } else {
                            highlightsToKeep.push(highlights[j]);
                        }
                    }

                    // Add new range
                    if (classApplier) {
                        highlightsToKeep.push(new Highlight(doc, charRange, classApplier, converter, null, containerElementId));
                    }
                    this.highlights = highlights = highlightsToKeep;
                }

                // Remove the old highlights
                forEach(highlightsToRemove, function(highlightToRemove) {
                    highlightToRemove.unapply();
                });

                // Apply new highlights
                var newHighlights = [];
                forEach(highlights, function(highlight) {
                    if (!highlight.applied) {
                        highlight.apply();
                        newHighlights.push(highlight);
                    }
                });

                return newHighlights;
            },

            highlightRanges: function(className, ranges, options) {
                var selCharRanges = [];
                var converter = this.converter;

                options = createOptions(options, {
                    containerElement: null,
                    exclusive: true
                });

                var containerElement = options.containerElement;
                var containerElementId = containerElement ? containerElement.id : null;
                var containerElementRange;
                if (containerElement) {
                    containerElementRange = api.createRange(containerElement);
                    containerElementRange.selectNodeContents(containerElement);
                }

                forEach(ranges, function(range) {
                    var scopedRange = containerElement ? containerElementRange.intersection(range) : range;
                    selCharRanges.push( converter.rangeToCharacterRange(scopedRange, containerElement || getBody(range.getDocument())) );
                });

                return this.highlightCharacterRanges(className, selCharRanges, {
                    containerElementId: containerElementId,
                    exclusive: options.exclusive
                });
            },

            highlightSelection: function(className, options) {
                var converter = this.converter;
                var classApplier = className ? this.classAppliers[className] : false;

                options = createOptions(options, {
                    containerElementId: null,
                    selection: api.getSelection(this.doc),
                    exclusive: true
                });

                var containerElementId = options.containerElementId;
                var exclusive = options.exclusive;
                var selection = options.selection;
                var doc = selection.win.document;
                var containerElement = getContainerElement(doc, containerElementId);

                if (!classApplier && className !== false) {
                    throw new Error("No class applier found for class '" + className + "'");
                }

                // Store the existing selection as character ranges
                var serializedSelection = converter.serializeSelection(selection, containerElement);

                // Create an array of selected character ranges
                var selCharRanges = [];
                forEach(serializedSelection, function(rangeInfo) {
                    selCharRanges.push( CharacterRange.fromCharacterRange(rangeInfo.characterRange) );
                });

                var newHighlights = this.highlightCharacterRanges(className, selCharRanges, {
                    containerElementId: containerElementId,
                    exclusive: exclusive
                });

                // Restore selection
                converter.restoreSelection(selection, serializedSelection, containerElement);

                return newHighlights;
            },

            unhighlightSelection: function(selection) {
                selection = selection || api.getSelection(this.doc);
                var intersectingHighlights = this.getIntersectingHighlights( selection.getAllRanges() );
                this.removeHighlights(intersectingHighlights);
                selection.removeAllRanges();
                return intersectingHighlights;
            },

            getHighlightsInSelection: function(selection) {
                selection = selection || api.getSelection(this.doc);
                return this.getIntersectingHighlights(selection.getAllRanges());
            },

            selectionOverlapsHighlight: function(selection) {
                return this.getHighlightsInSelection(selection).length > 0;
            },

            serialize: function(options) {
                var highlighter = this;
                var highlights = highlighter.highlights;
                var serializedType, serializedHighlights, convertType, serializationConverter;

                highlights.sort(compareHighlights);
                options = createOptions(options, {
                    serializeHighlightText: false,
                    type: highlighter.converter.type
                });

                serializedType = options.type;
                convertType = (serializedType != highlighter.converter.type);

                if (convertType) {
                    serializationConverter = getConverter(serializedType);
                }

                serializedHighlights = ["type:" + serializedType];

                forEach(highlights, function(highlight) {
                    var characterRange = highlight.characterRange;
                    var containerElement;

                    // Convert to the current Highlighter's type, if different from the serialization type
                    if (convertType) {
                        containerElement = highlight.getContainerElement();
                        characterRange = serializationConverter.rangeToCharacterRange(
                            highlighter.converter.characterRangeToRange(highlighter.doc, characterRange, containerElement),
                            containerElement
                        );
                    }

                    var parts = [
                        characterRange.start,
                        characterRange.end,
                        highlight.id,
                        highlight.classApplier.className,
                        highlight.containerElementId
                    ];

                    if (options.serializeHighlightText) {
                        parts.push(highlight.getText());
                    }
                    serializedHighlights.push( parts.join("$") );
                });

                return serializedHighlights.join("|");
            },

            deserialize: function(serialized) {
                var serializedHighlights = serialized.split("|");
                var highlights = [];

                var firstHighlight = serializedHighlights[0];
                var regexResult;
                var serializationType, serializationConverter, convertType = false;
                if ( firstHighlight && (regexResult = /^type:(\w+)$/.exec(firstHighlight)) ) {
                    serializationType = regexResult[1];
                    if (serializationType != this.converter.type) {
                        serializationConverter = getConverter(serializationType);
                        convertType = true;
                    }
                    serializedHighlights.shift();
                } else {
                    throw new Error("Serialized highlights are invalid.");
                }

                var classApplier, highlight, characterRange, containerElementId, containerElement;

                for (var i = serializedHighlights.length, parts; i-- > 0; ) {
                    parts = serializedHighlights[i].split("$");
                    characterRange = new CharacterRange(+parts[0], +parts[1]);
                    containerElementId = parts[4] || null;

                    // Convert to the current Highlighter's type, if different from the serialization type
                    if (convertType) {
                        containerElement = getContainerElement(this.doc, containerElementId);
                        characterRange = this.converter.rangeToCharacterRange(
                            serializationConverter.characterRangeToRange(this.doc, characterRange, containerElement),
                            containerElement
                        );
                    }

                    classApplier = this.classAppliers[ parts[3] ];

                    if (!classApplier) {
                        throw new Error("No class applier found for class '" + parts[3] + "'");
                    }

                    highlight = new Highlight(this.doc, characterRange, classApplier, this.converter, parseInt(parts[2]), containerElementId);
                    highlight.apply();
                    highlights.push(highlight);
                }
                this.highlights = highlights;
            }
        };

        api.Highlighter = Highlighter;

        api.createHighlighter = function(doc, rangeCharacterOffsetConverterType) {
            return new Highlighter(doc, rangeCharacterOffsetConverterType);
        };
    });
    
    return rangy;
}, this);

/**
 * Text range module for Rangy.
 * Text-based manipulation and searching of ranges and selections.
 *
 * Features
 *
 * - Ability to move range boundaries by character or word offsets
 * - Customizable word tokenizer
 * - Ignores text nodes inside <script> or <style> elements or those hidden by CSS display and visibility properties
 * - Range findText method to search for text or regex within the page or within a range. Flags for whole words and case
 *   sensitivity
 * - Selection and range save/restore as text offsets within a node
 * - Methods to return visible text within a range or selection
 * - innerText method for elements
 *
 * References
 *
 * https://www.w3.org/Bugs/Public/show_bug.cgi?id=13145
 * http://aryeh.name/spec/innertext/innertext.html
 * http://dvcs.w3.org/hg/editing/raw-file/tip/editing.html
 *
 * Part of Rangy, a cross-browser JavaScript range and selection library
 * https://github.com/timdown/rangy
 *
 * Depends on Rangy core.
 *
 * Copyright 2015, Tim Down
 * Licensed under the MIT license.
 * Version: 1.3.0
 * Build date: 10 May 2015
 */

/**
 * Problem: handling of trailing spaces before line breaks is handled inconsistently between browsers.
 *
 * First, a <br>: this is relatively simple. For the following HTML:
 *
 * 1 <br>2
 *
 * - IE and WebKit render the space, include it in the selection (i.e. when the content is selected and pasted into a
 *   textarea, the space is present) and allow the caret to be placed after it.
 * - Firefox does not acknowledge the space in the selection but it is possible to place the caret after it.
 * - Opera does not render the space but has two separate caret positions on either side of the space (left and right
 *   arrow keys show this) and includes the space in the selection.
 *
 * The other case is the line break or breaks implied by block elements. For the following HTML:
 *
 * <p>1 </p><p>2<p>
 *
 * - WebKit does not acknowledge the space in any way
 * - Firefox, IE and Opera as per <br>
 *
 * One more case is trailing spaces before line breaks in elements with white-space: pre-line. For the following HTML:
 *
 * <p style="white-space: pre-line">1
 * 2</p>
 *
 * - Firefox and WebKit include the space in caret positions
 * - IE does not support pre-line up to and including version 9
 * - Opera ignores the space
 * - Trailing space only renders if there is a non-collapsed character in the line
 *
 * Problem is whether Rangy should ever acknowledge the space and if so, when. Another problem is whether this can be
 * feature-tested
 */
(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {
    rangy.createModule("TextRange", ["WrappedSelection"], function(api, module) {
        var UNDEF = "undefined";
        var CHARACTER = "character", WORD = "word";
        var dom = api.dom, util = api.util;
        var extend = util.extend;
        var createOptions = util.createOptions;
        var getBody = dom.getBody;


        var spacesRegex = /^[ \t\f\r\n]+$/;
        var spacesMinusLineBreaksRegex = /^[ \t\f\r]+$/;
        var allWhiteSpaceRegex = /^[\t-\r \u0085\u00A0\u1680\u180E\u2000-\u200B\u2028\u2029\u202F\u205F\u3000]+$/;
        var nonLineBreakWhiteSpaceRegex = /^[\t \u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000]+$/;
        var lineBreakRegex = /^[\n-\r\u0085\u2028\u2029]$/;

        var defaultLanguage = "en";

        var isDirectionBackward = api.Selection.isDirectionBackward;

        // Properties representing whether trailing spaces inside blocks are completely collapsed (as they are in WebKit,
        // but not other browsers). Also test whether trailing spaces before <br> elements are collapsed.
        var trailingSpaceInBlockCollapses = false;
        var trailingSpaceBeforeBrCollapses = false;
        var trailingSpaceBeforeBlockCollapses = false;
        var trailingSpaceBeforeLineBreakInPreLineCollapses = true;

        (function() {
            var el = dom.createTestElement(document, "<p>1 </p><p></p>", true);
            var p = el.firstChild;
            var sel = api.getSelection();
            sel.collapse(p.lastChild, 2);
            sel.setStart(p.firstChild, 0);
            trailingSpaceInBlockCollapses = ("" + sel).length == 1;

            el.innerHTML = "1 <br />";
            sel.collapse(el, 2);
            sel.setStart(el.firstChild, 0);
            trailingSpaceBeforeBrCollapses = ("" + sel).length == 1;

            el.innerHTML = "1 <p>1</p>";
            sel.collapse(el, 2);
            sel.setStart(el.firstChild, 0);
            trailingSpaceBeforeBlockCollapses = ("" + sel).length == 1;

            dom.removeNode(el);
            sel.removeAllRanges();
        })();

        /*----------------------------------------------------------------------------------------------------------------*/

        // This function must create word and non-word tokens for the whole of the text supplied to it
        function defaultTokenizer(chars, wordOptions) {
            var word = chars.join(""), result, tokenRanges = [];

            function createTokenRange(start, end, isWord) {
                tokenRanges.push( { start: start, end: end, isWord: isWord } );
            }

            // Match words and mark characters
            var lastWordEnd = 0, wordStart, wordEnd;
            while ( (result = wordOptions.wordRegex.exec(word)) ) {
                wordStart = result.index;
                wordEnd = wordStart + result[0].length;

                // Create token for non-word characters preceding this word
                if (wordStart > lastWordEnd) {
                    createTokenRange(lastWordEnd, wordStart, false);
                }

                // Get trailing space characters for word
                if (wordOptions.includeTrailingSpace) {
                    while ( nonLineBreakWhiteSpaceRegex.test(chars[wordEnd]) ) {
                        ++wordEnd;
                    }
                }
                createTokenRange(wordStart, wordEnd, true);
                lastWordEnd = wordEnd;
            }

            // Create token for trailing non-word characters, if any exist
            if (lastWordEnd < chars.length) {
                createTokenRange(lastWordEnd, chars.length, false);
            }

            return tokenRanges;
        }

        function convertCharRangeToToken(chars, tokenRange) {
            var tokenChars = chars.slice(tokenRange.start, tokenRange.end);
            var token = {
                isWord: tokenRange.isWord,
                chars: tokenChars,
                toString: function() {
                    return tokenChars.join("");
                }
            };
            for (var i = 0, len = tokenChars.length; i < len; ++i) {
                tokenChars[i].token = token;
            }
            return token;
        }

        function tokenize(chars, wordOptions, tokenizer) {
            var tokenRanges = tokenizer(chars, wordOptions);
            var tokens = [];
            for (var i = 0, tokenRange; tokenRange = tokenRanges[i++]; ) {
                tokens.push( convertCharRangeToToken(chars, tokenRange) );
            }
            return tokens;
        }

        var defaultCharacterOptions = {
            includeBlockContentTrailingSpace: true,
            includeSpaceBeforeBr: true,
            includeSpaceBeforeBlock: true,
            includePreLineTrailingSpace: true,
            ignoreCharacters: ""
        };

        function normalizeIgnoredCharacters(ignoredCharacters) {
            // Check if character is ignored
            var ignoredChars = ignoredCharacters || "";

            // Normalize ignored characters into a string consisting of characters in ascending order of character code
            var ignoredCharsArray = (typeof ignoredChars == "string") ? ignoredChars.split("") : ignoredChars;
            ignoredCharsArray.sort(function(char1, char2) {
                return char1.charCodeAt(0) - char2.charCodeAt(0);
            });

            /// Convert back to a string and remove duplicates
            return ignoredCharsArray.join("").replace(/(.)\1+/g, "$1");
        }

        var defaultCaretCharacterOptions = {
            includeBlockContentTrailingSpace: !trailingSpaceBeforeLineBreakInPreLineCollapses,
            includeSpaceBeforeBr: !trailingSpaceBeforeBrCollapses,
            includeSpaceBeforeBlock: !trailingSpaceBeforeBlockCollapses,
            includePreLineTrailingSpace: true
        };

        var defaultWordOptions = {
            "en": {
                wordRegex: /[a-z0-9]+('[a-z0-9]+)*/gi,
                includeTrailingSpace: false,
                tokenizer: defaultTokenizer
            }
        };

        var defaultFindOptions = {
            caseSensitive: false,
            withinRange: null,
            wholeWordsOnly: false,
            wrap: false,
            direction: "forward",
            wordOptions: null,
            characterOptions: null
        };

        var defaultMoveOptions = {
            wordOptions: null,
            characterOptions: null
        };

        var defaultExpandOptions = {
            wordOptions: null,
            characterOptions: null,
            trim: false,
            trimStart: true,
            trimEnd: true
        };

        var defaultWordIteratorOptions = {
            wordOptions: null,
            characterOptions: null,
            direction: "forward"
        };

        function createWordOptions(options) {
            var lang, defaults;
            if (!options) {
                return defaultWordOptions[defaultLanguage];
            } else {
                lang = options.language || defaultLanguage;
                defaults = {};
                extend(defaults, defaultWordOptions[lang] || defaultWordOptions[defaultLanguage]);
                extend(defaults, options);
                return defaults;
            }
        }

        function createNestedOptions(optionsParam, defaults) {
            var options = createOptions(optionsParam, defaults);
            if (defaults.hasOwnProperty("wordOptions")) {
                options.wordOptions = createWordOptions(options.wordOptions);
            }
            if (defaults.hasOwnProperty("characterOptions")) {
                options.characterOptions = createOptions(options.characterOptions, defaultCharacterOptions);
            }
            return options;
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        /* DOM utility functions */
        var getComputedStyleProperty = dom.getComputedStyleProperty;

        // Create cachable versions of DOM functions

        // Test for old IE's incorrect display properties
        var tableCssDisplayBlock;
        (function() {
            var table = document.createElement("table");
            var body = getBody(document);
            body.appendChild(table);
            tableCssDisplayBlock = (getComputedStyleProperty(table, "display") == "block");
            body.removeChild(table);
        })();

        var defaultDisplayValueForTag = {
            table: "table",
            caption: "table-caption",
            colgroup: "table-column-group",
            col: "table-column",
            thead: "table-header-group",
            tbody: "table-row-group",
            tfoot: "table-footer-group",
            tr: "table-row",
            td: "table-cell",
            th: "table-cell"
        };

        // Corrects IE's "block" value for table-related elements
        function getComputedDisplay(el, win) {
            var display = getComputedStyleProperty(el, "display", win);
            var tagName = el.tagName.toLowerCase();
            return (display == "block" &&
                    tableCssDisplayBlock &&
                    defaultDisplayValueForTag.hasOwnProperty(tagName)) ?
                defaultDisplayValueForTag[tagName] : display;
        }

        function isHidden(node) {
            var ancestors = getAncestorsAndSelf(node);
            for (var i = 0, len = ancestors.length; i < len; ++i) {
                if (ancestors[i].nodeType == 1 && getComputedDisplay(ancestors[i]) == "none") {
                    return true;
                }
            }

            return false;
        }

        function isVisibilityHiddenTextNode(textNode) {
            var el;
            return textNode.nodeType == 3 &&
                (el = textNode.parentNode) &&
                getComputedStyleProperty(el, "visibility") == "hidden";
        }

        /*----------------------------------------------------------------------------------------------------------------*/

    
        // "A block node is either an Element whose "display" property does not have
        // resolved value "inline" or "inline-block" or "inline-table" or "none", or a
        // Document, or a DocumentFragment."
        function isBlockNode(node) {
            return node &&
                ((node.nodeType == 1 && !/^(inline(-block|-table)?|none)$/.test(getComputedDisplay(node))) ||
                node.nodeType == 9 || node.nodeType == 11);
        }

        function getLastDescendantOrSelf(node) {
            var lastChild = node.lastChild;
            return lastChild ? getLastDescendantOrSelf(lastChild) : node;
        }

        function containsPositions(node) {
            return dom.isCharacterDataNode(node) ||
                !/^(area|base|basefont|br|col|frame|hr|img|input|isindex|link|meta|param)$/i.test(node.nodeName);
        }

        function getAncestors(node) {
            var ancestors = [];
            while (node.parentNode) {
                ancestors.unshift(node.parentNode);
                node = node.parentNode;
            }
            return ancestors;
        }

        function getAncestorsAndSelf(node) {
            return getAncestors(node).concat([node]);
        }

        function nextNodeDescendants(node) {
            while (node && !node.nextSibling) {
                node = node.parentNode;
            }
            if (!node) {
                return null;
            }
            return node.nextSibling;
        }

        function nextNode(node, excludeChildren) {
            if (!excludeChildren && node.hasChildNodes()) {
                return node.firstChild;
            }
            return nextNodeDescendants(node);
        }

        function previousNode(node) {
            var previous = node.previousSibling;
            if (previous) {
                node = previous;
                while (node.hasChildNodes()) {
                    node = node.lastChild;
                }
                return node;
            }
            var parent = node.parentNode;
            if (parent && parent.nodeType == 1) {
                return parent;
            }
            return null;
        }

        // Adpated from Aryeh's code.
        // "A whitespace node is either a Text node whose data is the empty string; or
        // a Text node whose data consists only of one or more tabs (0x0009), line
        // feeds (0x000A), carriage returns (0x000D), and/or spaces (0x0020), and whose
        // parent is an Element whose resolved value for "white-space" is "normal" or
        // "nowrap"; or a Text node whose data consists only of one or more tabs
        // (0x0009), carriage returns (0x000D), and/or spaces (0x0020), and whose
        // parent is an Element whose resolved value for "white-space" is "pre-line"."
        function isWhitespaceNode(node) {
            if (!node || node.nodeType != 3) {
                return false;
            }
            var text = node.data;
            if (text === "") {
                return true;
            }
            var parent = node.parentNode;
            if (!parent || parent.nodeType != 1) {
                return false;
            }
            var computedWhiteSpace = getComputedStyleProperty(node.parentNode, "whiteSpace");

            return (/^[\t\n\r ]+$/.test(text) && /^(normal|nowrap)$/.test(computedWhiteSpace)) ||
                (/^[\t\r ]+$/.test(text) && computedWhiteSpace == "pre-line");
        }

        // Adpated from Aryeh's code.
        // "node is a collapsed whitespace node if the following algorithm returns
        // true:"
        function isCollapsedWhitespaceNode(node) {
            // "If node's data is the empty string, return true."
            if (node.data === "") {
                return true;
            }

            // "If node is not a whitespace node, return false."
            if (!isWhitespaceNode(node)) {
                return false;
            }

            // "Let ancestor be node's parent."
            var ancestor = node.parentNode;

            // "If ancestor is null, return true."
            if (!ancestor) {
                return true;
            }

            // "If the "display" property of some ancestor of node has resolved value "none", return true."
            if (isHidden(node)) {
                return true;
            }

            return false;
        }

        function isCollapsedNode(node) {
            var type = node.nodeType;
            return type == 7 /* PROCESSING_INSTRUCTION */ ||
                type == 8 /* COMMENT */ ||
                isHidden(node) ||
                /^(script|style)$/i.test(node.nodeName) ||
                isVisibilityHiddenTextNode(node) ||
                isCollapsedWhitespaceNode(node);
        }

        function isIgnoredNode(node, win) {
            var type = node.nodeType;
            return type == 7 /* PROCESSING_INSTRUCTION */ ||
                type == 8 /* COMMENT */ ||
                (type == 1 && getComputedDisplay(node, win) == "none");
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Possibly overengineered caching system to prevent repeated DOM calls slowing everything down

        function Cache() {
            this.store = {};
        }

        Cache.prototype = {
            get: function(key) {
                return this.store.hasOwnProperty(key) ? this.store[key] : null;
            },

            set: function(key, value) {
                return this.store[key] = value;
            }
        };

        var cachedCount = 0, uncachedCount = 0;

        function createCachingGetter(methodName, func, objProperty) {
            return function(args) {
                var cache = this.cache;
                if (cache.hasOwnProperty(methodName)) {
                    cachedCount++;
                    return cache[methodName];
                } else {
                    uncachedCount++;
                    var value = func.call(this, objProperty ? this[objProperty] : this, args);
                    cache[methodName] = value;
                    return value;
                }
            };
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        function NodeWrapper(node, session) {
            this.node = node;
            this.session = session;
            this.cache = new Cache();
            this.positions = new Cache();
        }

        var nodeProto = {
            getPosition: function(offset) {
                var positions = this.positions;
                return positions.get(offset) || positions.set(offset, new Position(this, offset));
            },

            toString: function() {
                return "[NodeWrapper(" + dom.inspectNode(this.node) + ")]";
            }
        };

        NodeWrapper.prototype = nodeProto;

        var EMPTY = "EMPTY",
            NON_SPACE = "NON_SPACE",
            UNCOLLAPSIBLE_SPACE = "UNCOLLAPSIBLE_SPACE",
            COLLAPSIBLE_SPACE = "COLLAPSIBLE_SPACE",
            TRAILING_SPACE_BEFORE_BLOCK = "TRAILING_SPACE_BEFORE_BLOCK",
            TRAILING_SPACE_IN_BLOCK = "TRAILING_SPACE_IN_BLOCK",
            TRAILING_SPACE_BEFORE_BR = "TRAILING_SPACE_BEFORE_BR",
            PRE_LINE_TRAILING_SPACE_BEFORE_LINE_BREAK = "PRE_LINE_TRAILING_SPACE_BEFORE_LINE_BREAK",
            TRAILING_LINE_BREAK_AFTER_BR = "TRAILING_LINE_BREAK_AFTER_BR",
            INCLUDED_TRAILING_LINE_BREAK_AFTER_BR = "INCLUDED_TRAILING_LINE_BREAK_AFTER_BR";

        extend(nodeProto, {
            isCharacterDataNode: createCachingGetter("isCharacterDataNode", dom.isCharacterDataNode, "node"),
            getNodeIndex: createCachingGetter("nodeIndex", dom.getNodeIndex, "node"),
            getLength: createCachingGetter("nodeLength", dom.getNodeLength, "node"),
            containsPositions: createCachingGetter("containsPositions", containsPositions, "node"),
            isWhitespace: createCachingGetter("isWhitespace", isWhitespaceNode, "node"),
            isCollapsedWhitespace: createCachingGetter("isCollapsedWhitespace", isCollapsedWhitespaceNode, "node"),
            getComputedDisplay: createCachingGetter("computedDisplay", getComputedDisplay, "node"),
            isCollapsed: createCachingGetter("collapsed", isCollapsedNode, "node"),
            isIgnored: createCachingGetter("ignored", isIgnoredNode, "node"),
            next: createCachingGetter("nextPos", nextNode, "node"),
            previous: createCachingGetter("previous", previousNode, "node"),

            getTextNodeInfo: createCachingGetter("textNodeInfo", function(textNode) {
                var spaceRegex = null, collapseSpaces = false;
                var cssWhitespace = getComputedStyleProperty(textNode.parentNode, "whiteSpace");
                var preLine = (cssWhitespace == "pre-line");
                if (preLine) {
                    spaceRegex = spacesMinusLineBreaksRegex;
                    collapseSpaces = true;
                } else if (cssWhitespace == "normal" || cssWhitespace == "nowrap") {
                    spaceRegex = spacesRegex;
                    collapseSpaces = true;
                }

                return {
                    node: textNode,
                    text: textNode.data,
                    spaceRegex: spaceRegex,
                    collapseSpaces: collapseSpaces,
                    preLine: preLine
                };
            }, "node"),

            hasInnerText: createCachingGetter("hasInnerText", function(el, backward) {
                var session = this.session;
                var posAfterEl = session.getPosition(el.parentNode, this.getNodeIndex() + 1);
                var firstPosInEl = session.getPosition(el, 0);

                var pos = backward ? posAfterEl : firstPosInEl;
                var endPos = backward ? firstPosInEl : posAfterEl;

                /*
                 <body><p>X  </p><p>Y</p></body>

                 Positions:

                 body:0:""
                 p:0:""
                 text:0:""
                 text:1:"X"
                 text:2:TRAILING_SPACE_IN_BLOCK
                 text:3:COLLAPSED_SPACE
                 p:1:""
                 body:1:"\n"
                 p:0:""
                 text:0:""
                 text:1:"Y"

                 A character is a TRAILING_SPACE_IN_BLOCK iff:

                 - There is no uncollapsed character after it within the visible containing block element

                 A character is a TRAILING_SPACE_BEFORE_BR iff:

                 - There is no uncollapsed character after it preceding a <br> element

                 An element has inner text iff

                 - It is not hidden
                 - It contains an uncollapsed character

                 All trailing spaces (pre-line, before <br>, end of block) require definite non-empty characters to render.
                 */

                while (pos !== endPos) {
                    pos.prepopulateChar();
                    if (pos.isDefinitelyNonEmpty()) {
                        return true;
                    }
                    pos = backward ? pos.previousVisible() : pos.nextVisible();
                }

                return false;
            }, "node"),

            isRenderedBlock: createCachingGetter("isRenderedBlock", function(el) {
                // Ensure that a block element containing a <br> is considered to have inner text
                var brs = el.getElementsByTagName("br");
                for (var i = 0, len = brs.length; i < len; ++i) {
                    if (!isCollapsedNode(brs[i])) {
                        return true;
                    }
                }
                return this.hasInnerText();
            }, "node"),

            getTrailingSpace: createCachingGetter("trailingSpace", function(el) {
                if (el.tagName.toLowerCase() == "br") {
                    return "";
                } else {
                    switch (this.getComputedDisplay()) {
                        case "inline":
                            var child = el.lastChild;
                            while (child) {
                                if (!isIgnoredNode(child)) {
                                    return (child.nodeType == 1) ? this.session.getNodeWrapper(child).getTrailingSpace() : "";
                                }
                                child = child.previousSibling;
                            }
                            break;
                        case "inline-block":
                        case "inline-table":
                        case "none":
                        case "table-column":
                        case "table-column-group":
                            break;
                        case "table-cell":
                            return "\t";
                        default:
                            return this.isRenderedBlock(true) ? "\n" : "";
                    }
                }
                return "";
            }, "node"),

            getLeadingSpace: createCachingGetter("leadingSpace", function(el) {
                switch (this.getComputedDisplay()) {
                    case "inline":
                    case "inline-block":
                    case "inline-table":
                    case "none":
                    case "table-column":
                    case "table-column-group":
                    case "table-cell":
                        break;
                    default:
                        return this.isRenderedBlock(false) ? "\n" : "";
                }
                return "";
            }, "node")
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        function Position(nodeWrapper, offset) {
            this.offset = offset;
            this.nodeWrapper = nodeWrapper;
            this.node = nodeWrapper.node;
            this.session = nodeWrapper.session;
            this.cache = new Cache();
        }

        function inspectPosition() {
            return "[Position(" + dom.inspectNode(this.node) + ":" + this.offset + ")]";
        }

        var positionProto = {
            character: "",
            characterType: EMPTY,
            isBr: false,

            /*
            This method:
            - Fully populates positions that have characters that can be determined independently of any other characters.
            - Populates most types of space positions with a provisional character. The character is finalized later.
             */
            prepopulateChar: function() {
                var pos = this;
                if (!pos.prepopulatedChar) {
                    var node = pos.node, offset = pos.offset;
                    var visibleChar = "", charType = EMPTY;
                    var finalizedChar = false;
                    if (offset > 0) {
                        if (node.nodeType == 3) {
                            var text = node.data;
                            var textChar = text.charAt(offset - 1);

                            var nodeInfo = pos.nodeWrapper.getTextNodeInfo();
                            var spaceRegex = nodeInfo.spaceRegex;
                            if (nodeInfo.collapseSpaces) {
                                if (spaceRegex.test(textChar)) {
                                    // "If the character at position is from set, append a single space (U+0020) to newdata and advance
                                    // position until the character at position is not from set."

                                    // We also need to check for the case where we're in a pre-line and we have a space preceding a
                                    // line break, because such spaces are collapsed in some browsers
                                    if (offset > 1 && spaceRegex.test(text.charAt(offset - 2))) {
                                    } else if (nodeInfo.preLine && text.charAt(offset) === "\n") {
                                        visibleChar = " ";
                                        charType = PRE_LINE_TRAILING_SPACE_BEFORE_LINE_BREAK;
                                    } else {
                                        visibleChar = " ";
                                        //pos.checkForFollowingLineBreak = true;
                                        charType = COLLAPSIBLE_SPACE;
                                    }
                                } else {
                                    visibleChar = textChar;
                                    charType = NON_SPACE;
                                    finalizedChar = true;
                                }
                            } else {
                                visibleChar = textChar;
                                charType = UNCOLLAPSIBLE_SPACE;
                                finalizedChar = true;
                            }
                        } else {
                            var nodePassed = node.childNodes[offset - 1];
                            if (nodePassed && nodePassed.nodeType == 1 && !isCollapsedNode(nodePassed)) {
                                if (nodePassed.tagName.toLowerCase() == "br") {
                                    visibleChar = "\n";
                                    pos.isBr = true;
                                    charType = COLLAPSIBLE_SPACE;
                                    finalizedChar = false;
                                } else {
                                    pos.checkForTrailingSpace = true;
                                }
                            }

                            // Check the leading space of the next node for the case when a block element follows an inline
                            // element or text node. In that case, there is an implied line break between the two nodes.
                            if (!visibleChar) {
                                var nextNode = node.childNodes[offset];
                                if (nextNode && nextNode.nodeType == 1 && !isCollapsedNode(nextNode)) {
                                    pos.checkForLeadingSpace = true;
                                }
                            }
                        }
                    }

                    pos.prepopulatedChar = true;
                    pos.character = visibleChar;
                    pos.characterType = charType;
                    pos.isCharInvariant = finalizedChar;
                }
            },

            isDefinitelyNonEmpty: function() {
                var charType = this.characterType;
                return charType == NON_SPACE || charType == UNCOLLAPSIBLE_SPACE;
            },

            // Resolve leading and trailing spaces, which may involve prepopulating other positions
            resolveLeadingAndTrailingSpaces: function() {
                if (!this.prepopulatedChar) {
                    this.prepopulateChar();
                }
                if (this.checkForTrailingSpace) {
                    var trailingSpace = this.session.getNodeWrapper(this.node.childNodes[this.offset - 1]).getTrailingSpace();
                    if (trailingSpace) {
                        this.isTrailingSpace = true;
                        this.character = trailingSpace;
                        this.characterType = COLLAPSIBLE_SPACE;
                    }
                    this.checkForTrailingSpace = false;
                }
                if (this.checkForLeadingSpace) {
                    var leadingSpace = this.session.getNodeWrapper(this.node.childNodes[this.offset]).getLeadingSpace();
                    if (leadingSpace) {
                        this.isLeadingSpace = true;
                        this.character = leadingSpace;
                        this.characterType = COLLAPSIBLE_SPACE;
                    }
                    this.checkForLeadingSpace = false;
                }
            },

            getPrecedingUncollapsedPosition: function(characterOptions) {
                var pos = this, character;
                while ( (pos = pos.previousVisible()) ) {
                    character = pos.getCharacter(characterOptions);
                    if (character !== "") {
                        return pos;
                    }
                }

                return null;
            },

            getCharacter: function(characterOptions) {
                this.resolveLeadingAndTrailingSpaces();

                var thisChar = this.character, returnChar;

                // Check if character is ignored
                var ignoredChars = normalizeIgnoredCharacters(characterOptions.ignoreCharacters);
                var isIgnoredCharacter = (thisChar !== "" && ignoredChars.indexOf(thisChar) > -1);

                // Check if this position's  character is invariant (i.e. not dependent on character options) and return it
                // if so
                if (this.isCharInvariant) {
                    returnChar = isIgnoredCharacter ? "" : thisChar;
                    return returnChar;
                }

                var cacheKey = ["character", characterOptions.includeSpaceBeforeBr, characterOptions.includeBlockContentTrailingSpace, characterOptions.includePreLineTrailingSpace, ignoredChars].join("_");
                var cachedChar = this.cache.get(cacheKey);
                if (cachedChar !== null) {
                    return cachedChar;
                }

                // We need to actually get the character now
                var character = "";
                var collapsible = (this.characterType == COLLAPSIBLE_SPACE);

                var nextPos, previousPos;
                var gotPreviousPos = false;
                var pos = this;

                function getPreviousPos() {
                    if (!gotPreviousPos) {
                        previousPos = pos.getPrecedingUncollapsedPosition(characterOptions);
                        gotPreviousPos = true;
                    }
                    return previousPos;
                }

                // Disallow a collapsible space that is followed by a line break or is the last character
                if (collapsible) {
                    // Allow a trailing space that we've previously determined should be included
                    if (this.type == INCLUDED_TRAILING_LINE_BREAK_AFTER_BR) {
                        character = "\n";
                    }
                    // Disallow a collapsible space that follows a trailing space or line break, or is the first character,
                    // or follows a collapsible included space
                    else if (thisChar == " " &&
                            (!getPreviousPos() || previousPos.isTrailingSpace || previousPos.character == "\n" || (previousPos.character == " " && previousPos.characterType == COLLAPSIBLE_SPACE))) {
                    }
                    // Allow a leading line break unless it follows a line break
                    else if (thisChar == "\n" && this.isLeadingSpace) {
                        if (getPreviousPos() && previousPos.character != "\n") {
                            character = "\n";
                        } else {
                        }
                    } else {
                        nextPos = this.nextUncollapsed();
                        if (nextPos) {
                            if (nextPos.isBr) {
                                this.type = TRAILING_SPACE_BEFORE_BR;
                            } else if (nextPos.isTrailingSpace && nextPos.character == "\n") {
                                this.type = TRAILING_SPACE_IN_BLOCK;
                            } else if (nextPos.isLeadingSpace && nextPos.character == "\n") {
                                this.type = TRAILING_SPACE_BEFORE_BLOCK;
                            }

                            if (nextPos.character == "\n") {
                                if (this.type == TRAILING_SPACE_BEFORE_BR && !characterOptions.includeSpaceBeforeBr) {
                                } else if (this.type == TRAILING_SPACE_BEFORE_BLOCK && !characterOptions.includeSpaceBeforeBlock) {
                                } else if (this.type == TRAILING_SPACE_IN_BLOCK && nextPos.isTrailingSpace && !characterOptions.includeBlockContentTrailingSpace) {
                                } else if (this.type == PRE_LINE_TRAILING_SPACE_BEFORE_LINE_BREAK && nextPos.type == NON_SPACE && !characterOptions.includePreLineTrailingSpace) {
                                } else if (thisChar == "\n") {
                                    if (nextPos.isTrailingSpace) {
                                        if (this.isTrailingSpace) {
                                        } else if (this.isBr) {
                                            nextPos.type = TRAILING_LINE_BREAK_AFTER_BR;

                                            if (getPreviousPos() && previousPos.isLeadingSpace && !previousPos.isTrailingSpace && previousPos.character == "\n") {
                                                nextPos.character = "";
                                            } else {
                                                nextPos.type = INCLUDED_TRAILING_LINE_BREAK_AFTER_BR;
                                            }
                                        }
                                    } else {
                                        character = "\n";
                                    }
                                } else if (thisChar == " ") {
                                    character = " ";
                                } else {
                                }
                            } else {
                                character = thisChar;
                            }
                        } else {
                        }
                    }
                }

                if (ignoredChars.indexOf(character) > -1) {
                    character = "";
                }


                this.cache.set(cacheKey, character);

                return character;
            },

            equals: function(pos) {
                return !!pos && this.node === pos.node && this.offset === pos.offset;
            },

            inspect: inspectPosition,

            toString: function() {
                return this.character;
            }
        };

        Position.prototype = positionProto;

        extend(positionProto, {
            next: createCachingGetter("nextPos", function(pos) {
                var nodeWrapper = pos.nodeWrapper, node = pos.node, offset = pos.offset, session = nodeWrapper.session;
                if (!node) {
                    return null;
                }
                var nextNode, nextOffset, child;
                if (offset == nodeWrapper.getLength()) {
                    // Move onto the next node
                    nextNode = node.parentNode;
                    nextOffset = nextNode ? nodeWrapper.getNodeIndex() + 1 : 0;
                } else {
                    if (nodeWrapper.isCharacterDataNode()) {
                        nextNode = node;
                        nextOffset = offset + 1;
                    } else {
                        child = node.childNodes[offset];
                        // Go into the children next, if children there are
                        if (session.getNodeWrapper(child).containsPositions()) {
                            nextNode = child;
                            nextOffset = 0;
                        } else {
                            nextNode = node;
                            nextOffset = offset + 1;
                        }
                    }
                }

                return nextNode ? session.getPosition(nextNode, nextOffset) : null;
            }),

            previous: createCachingGetter("previous", function(pos) {
                var nodeWrapper = pos.nodeWrapper, node = pos.node, offset = pos.offset, session = nodeWrapper.session;
                var previousNode, previousOffset, child;
                if (offset == 0) {
                    previousNode = node.parentNode;
                    previousOffset = previousNode ? nodeWrapper.getNodeIndex() : 0;
                } else {
                    if (nodeWrapper.isCharacterDataNode()) {
                        previousNode = node;
                        previousOffset = offset - 1;
                    } else {
                        child = node.childNodes[offset - 1];
                        // Go into the children next, if children there are
                        if (session.getNodeWrapper(child).containsPositions()) {
                            previousNode = child;
                            previousOffset = dom.getNodeLength(child);
                        } else {
                            previousNode = node;
                            previousOffset = offset - 1;
                        }
                    }
                }
                return previousNode ? session.getPosition(previousNode, previousOffset) : null;
            }),

            /*
             Next and previous position moving functions that filter out

             - Hidden (CSS visibility/display) elements
             - Script and style elements
             */
            nextVisible: createCachingGetter("nextVisible", function(pos) {
                var next = pos.next();
                if (!next) {
                    return null;
                }
                var nodeWrapper = next.nodeWrapper, node = next.node;
                var newPos = next;
                if (nodeWrapper.isCollapsed()) {
                    // We're skipping this node and all its descendants
                    newPos = nodeWrapper.session.getPosition(node.parentNode, nodeWrapper.getNodeIndex() + 1);
                }
                return newPos;
            }),

            nextUncollapsed: createCachingGetter("nextUncollapsed", function(pos) {
                var nextPos = pos;
                while ( (nextPos = nextPos.nextVisible()) ) {
                    nextPos.resolveLeadingAndTrailingSpaces();
                    if (nextPos.character !== "") {
                        return nextPos;
                    }
                }
                return null;
            }),

            previousVisible: createCachingGetter("previousVisible", function(pos) {
                var previous = pos.previous();
                if (!previous) {
                    return null;
                }
                var nodeWrapper = previous.nodeWrapper, node = previous.node;
                var newPos = previous;
                if (nodeWrapper.isCollapsed()) {
                    // We're skipping this node and all its descendants
                    newPos = nodeWrapper.session.getPosition(node.parentNode, nodeWrapper.getNodeIndex());
                }
                return newPos;
            })
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        var currentSession = null;

        var Session = (function() {
            function createWrapperCache(nodeProperty) {
                var cache = new Cache();

                return {
                    get: function(node) {
                        var wrappersByProperty = cache.get(node[nodeProperty]);
                        if (wrappersByProperty) {
                            for (var i = 0, wrapper; wrapper = wrappersByProperty[i++]; ) {
                                if (wrapper.node === node) {
                                    return wrapper;
                                }
                            }
                        }
                        return null;
                    },

                    set: function(nodeWrapper) {
                        var property = nodeWrapper.node[nodeProperty];
                        var wrappersByProperty = cache.get(property) || cache.set(property, []);
                        wrappersByProperty.push(nodeWrapper);
                    }
                };
            }

            var uniqueIDSupported = util.isHostProperty(document.documentElement, "uniqueID");

            function Session() {
                this.initCaches();
            }

            Session.prototype = {
                initCaches: function() {
                    this.elementCache = uniqueIDSupported ? (function() {
                        var elementsCache = new Cache();

                        return {
                            get: function(el) {
                                return elementsCache.get(el.uniqueID);
                            },

                            set: function(elWrapper) {
                                elementsCache.set(elWrapper.node.uniqueID, elWrapper);
                            }
                        };
                    })() : createWrapperCache("tagName");

                    // Store text nodes keyed by data, although we may need to truncate this
                    this.textNodeCache = createWrapperCache("data");
                    this.otherNodeCache = createWrapperCache("nodeName");
                },

                getNodeWrapper: function(node) {
                    var wrapperCache;
                    switch (node.nodeType) {
                        case 1:
                            wrapperCache = this.elementCache;
                            break;
                        case 3:
                            wrapperCache = this.textNodeCache;
                            break;
                        default:
                            wrapperCache = this.otherNodeCache;
                            break;
                    }

                    var wrapper = wrapperCache.get(node);
                    if (!wrapper) {
                        wrapper = new NodeWrapper(node, this);
                        wrapperCache.set(wrapper);
                    }
                    return wrapper;
                },

                getPosition: function(node, offset) {
                    return this.getNodeWrapper(node).getPosition(offset);
                },

                getRangeBoundaryPosition: function(range, isStart) {
                    var prefix = isStart ? "start" : "end";
                    return this.getPosition(range[prefix + "Container"], range[prefix + "Offset"]);
                },

                detach: function() {
                    this.elementCache = this.textNodeCache = this.otherNodeCache = null;
                }
            };

            return Session;
        })();

        /*----------------------------------------------------------------------------------------------------------------*/

        function startSession() {
            endSession();
            return (currentSession = new Session());
        }

        function getSession() {
            return currentSession || startSession();
        }

        function endSession() {
            if (currentSession) {
                currentSession.detach();
            }
            currentSession = null;
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Extensions to the rangy.dom utility object

        extend(dom, {
            nextNode: nextNode,
            previousNode: previousNode
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        function createCharacterIterator(startPos, backward, endPos, characterOptions) {

            // Adjust the end position to ensure that it is actually reached
            if (endPos) {
                if (backward) {
                    if (isCollapsedNode(endPos.node)) {
                        endPos = startPos.previousVisible();
                    }
                } else {
                    if (isCollapsedNode(endPos.node)) {
                        endPos = endPos.nextVisible();
                    }
                }
            }

            var pos = startPos, finished = false;

            function next() {
                var charPos = null;
                if (backward) {
                    charPos = pos;
                    if (!finished) {
                        pos = pos.previousVisible();
                        finished = !pos || (endPos && pos.equals(endPos));
                    }
                } else {
                    if (!finished) {
                        charPos = pos = pos.nextVisible();
                        finished = !pos || (endPos && pos.equals(endPos));
                    }
                }
                if (finished) {
                    pos = null;
                }
                return charPos;
            }

            var previousTextPos, returnPreviousTextPos = false;

            return {
                next: function() {
                    if (returnPreviousTextPos) {
                        returnPreviousTextPos = false;
                        return previousTextPos;
                    } else {
                        var pos, character;
                        while ( (pos = next()) ) {
                            character = pos.getCharacter(characterOptions);
                            if (character) {
                                previousTextPos = pos;
                                return pos;
                            }
                        }
                        return null;
                    }
                },

                rewind: function() {
                    if (previousTextPos) {
                        returnPreviousTextPos = true;
                    } else {
                        throw module.createError("createCharacterIterator: cannot rewind. Only one position can be rewound.");
                    }
                },

                dispose: function() {
                    startPos = endPos = null;
                }
            };
        }

        var arrayIndexOf = Array.prototype.indexOf ?
            function(arr, val) {
                return arr.indexOf(val);
            } :
            function(arr, val) {
                for (var i = 0, len = arr.length; i < len; ++i) {
                    if (arr[i] === val) {
                        return i;
                    }
                }
                return -1;
            };

        // Provides a pair of iterators over text positions, tokenized. Transparently requests more text when next()
        // is called and there is no more tokenized text
        function createTokenizedTextProvider(pos, characterOptions, wordOptions) {
            var forwardIterator = createCharacterIterator(pos, false, null, characterOptions);
            var backwardIterator = createCharacterIterator(pos, true, null, characterOptions);
            var tokenizer = wordOptions.tokenizer;

            // Consumes a word and the whitespace beyond it
            function consumeWord(forward) {
                var pos, textChar;
                var newChars = [], it = forward ? forwardIterator : backwardIterator;

                var passedWordBoundary = false, insideWord = false;

                while ( (pos = it.next()) ) {
                    textChar = pos.character;


                    if (allWhiteSpaceRegex.test(textChar)) {
                        if (insideWord) {
                            insideWord = false;
                            passedWordBoundary = true;
                        }
                    } else {
                        if (passedWordBoundary) {
                            it.rewind();
                            break;
                        } else {
                            insideWord = true;
                        }
                    }
                    newChars.push(pos);
                }


                return newChars;
            }

            // Get initial word surrounding initial position and tokenize it
            var forwardChars = consumeWord(true);
            var backwardChars = consumeWord(false).reverse();
            var tokens = tokenize(backwardChars.concat(forwardChars), wordOptions, tokenizer);

            // Create initial token buffers
            var forwardTokensBuffer = forwardChars.length ?
                tokens.slice(arrayIndexOf(tokens, forwardChars[0].token)) : [];

            var backwardTokensBuffer = backwardChars.length ?
                tokens.slice(0, arrayIndexOf(tokens, backwardChars.pop().token) + 1) : [];

            function inspectBuffer(buffer) {
                var textPositions = ["[" + buffer.length + "]"];
                for (var i = 0; i < buffer.length; ++i) {
                    textPositions.push("(word: " + buffer[i] + ", is word: " + buffer[i].isWord + ")");
                }
                return textPositions;
            }


            return {
                nextEndToken: function() {
                    var lastToken, forwardChars;

                    // If we're down to the last token, consume character chunks until we have a word or run out of
                    // characters to consume
                    while ( forwardTokensBuffer.length == 1 &&
                        !(lastToken = forwardTokensBuffer[0]).isWord &&
                        (forwardChars = consumeWord(true)).length > 0) {

                        // Merge trailing non-word into next word and tokenize
                        forwardTokensBuffer = tokenize(lastToken.chars.concat(forwardChars), wordOptions, tokenizer);
                    }

                    return forwardTokensBuffer.shift();
                },

                previousStartToken: function() {
                    var lastToken, backwardChars;

                    // If we're down to the last token, consume character chunks until we have a word or run out of
                    // characters to consume
                    while ( backwardTokensBuffer.length == 1 &&
                        !(lastToken = backwardTokensBuffer[0]).isWord &&
                        (backwardChars = consumeWord(false)).length > 0) {

                        // Merge leading non-word into next word and tokenize
                        backwardTokensBuffer = tokenize(backwardChars.reverse().concat(lastToken.chars), wordOptions, tokenizer);
                    }

                    return backwardTokensBuffer.pop();
                },

                dispose: function() {
                    forwardIterator.dispose();
                    backwardIterator.dispose();
                    forwardTokensBuffer = backwardTokensBuffer = null;
                }
            };
        }

        function movePositionBy(pos, unit, count, characterOptions, wordOptions) {
            var unitsMoved = 0, currentPos, newPos = pos, charIterator, nextPos, absCount = Math.abs(count), token;
            if (count !== 0) {
                var backward = (count < 0);

                switch (unit) {
                    case CHARACTER:
                        charIterator = createCharacterIterator(pos, backward, null, characterOptions);
                        while ( (currentPos = charIterator.next()) && unitsMoved < absCount ) {
                            ++unitsMoved;
                            newPos = currentPos;
                        }
                        nextPos = currentPos;
                        charIterator.dispose();
                        break;
                    case WORD:
                        var tokenizedTextProvider = createTokenizedTextProvider(pos, characterOptions, wordOptions);
                        var next = backward ? tokenizedTextProvider.previousStartToken : tokenizedTextProvider.nextEndToken;

                        while ( (token = next()) && unitsMoved < absCount ) {
                            if (token.isWord) {
                                ++unitsMoved;
                                newPos = backward ? token.chars[0] : token.chars[token.chars.length - 1];
                            }
                        }
                        break;
                    default:
                        throw new Error("movePositionBy: unit '" + unit + "' not implemented");
                }

                // Perform any necessary position tweaks
                if (backward) {
                    newPos = newPos.previousVisible();
                    unitsMoved = -unitsMoved;
                } else if (newPos && newPos.isLeadingSpace && !newPos.isTrailingSpace) {
                    // Tweak the position for the case of a leading space. The problem is that an uncollapsed leading space
                    // before a block element (for example, the line break between "1" and "2" in the following HTML:
                    // "1<p>2</p>") is considered to be attached to the position immediately before the block element, which
                    // corresponds with a different selection position in most browsers from the one we want (i.e. at the
                    // start of the contents of the block element). We get round this by advancing the position returned to
                    // the last possible equivalent visible position.
                    if (unit == WORD) {
                        charIterator = createCharacterIterator(pos, false, null, characterOptions);
                        nextPos = charIterator.next();
                        charIterator.dispose();
                    }
                    if (nextPos) {
                        newPos = nextPos.previousVisible();
                    }
                }
            }


            return {
                position: newPos,
                unitsMoved: unitsMoved
            };
        }

        function createRangeCharacterIterator(session, range, characterOptions, backward) {
            var rangeStart = session.getRangeBoundaryPosition(range, true);
            var rangeEnd = session.getRangeBoundaryPosition(range, false);
            var itStart = backward ? rangeEnd : rangeStart;
            var itEnd = backward ? rangeStart : rangeEnd;

            return createCharacterIterator(itStart, !!backward, itEnd, characterOptions);
        }

        function getRangeCharacters(session, range, characterOptions) {

            var chars = [], it = createRangeCharacterIterator(session, range, characterOptions), pos;
            while ( (pos = it.next()) ) {
                chars.push(pos);
            }

            it.dispose();
            return chars;
        }

        function isWholeWord(startPos, endPos, wordOptions) {
            var range = api.createRange(startPos.node);
            range.setStartAndEnd(startPos.node, startPos.offset, endPos.node, endPos.offset);
            return !range.expand("word", { wordOptions: wordOptions });
        }

        function findTextFromPosition(initialPos, searchTerm, isRegex, searchScopeRange, findOptions) {
            var backward = isDirectionBackward(findOptions.direction);
            var it = createCharacterIterator(
                initialPos,
                backward,
                initialPos.session.getRangeBoundaryPosition(searchScopeRange, backward),
                findOptions.characterOptions
            );
            var text = "", chars = [], pos, currentChar, matchStartIndex, matchEndIndex;
            var result, insideRegexMatch;
            var returnValue = null;

            function handleMatch(startIndex, endIndex) {
                var startPos = chars[startIndex].previousVisible();
                var endPos = chars[endIndex - 1];
                var valid = (!findOptions.wholeWordsOnly || isWholeWord(startPos, endPos, findOptions.wordOptions));

                return {
                    startPos: startPos,
                    endPos: endPos,
                    valid: valid
                };
            }

            while ( (pos = it.next()) ) {
                currentChar = pos.character;
                if (!isRegex && !findOptions.caseSensitive) {
                    currentChar = currentChar.toLowerCase();
                }

                if (backward) {
                    chars.unshift(pos);
                    text = currentChar + text;
                } else {
                    chars.push(pos);
                    text += currentChar;
                }

                if (isRegex) {
                    result = searchTerm.exec(text);
                    if (result) {
                        matchStartIndex = result.index;
                        matchEndIndex = matchStartIndex + result[0].length;
                        if (insideRegexMatch) {
                            // Check whether the match is now over
                            if ((!backward && matchEndIndex < text.length) || (backward && matchStartIndex > 0)) {
                                returnValue = handleMatch(matchStartIndex, matchEndIndex);
                                break;
                            }
                        } else {
                            insideRegexMatch = true;
                        }
                    }
                } else if ( (matchStartIndex = text.indexOf(searchTerm)) != -1 ) {
                    returnValue = handleMatch(matchStartIndex, matchStartIndex + searchTerm.length);
                    break;
                }
            }

            // Check whether regex match extends to the end of the range
            if (insideRegexMatch) {
                returnValue = handleMatch(matchStartIndex, matchEndIndex);
            }
            it.dispose();

            return returnValue;
        }

        function createEntryPointFunction(func) {
            return function() {
                var sessionRunning = !!currentSession;
                var session = getSession();
                var args = [session].concat( util.toArray(arguments) );
                var returnValue = func.apply(this, args);
                if (!sessionRunning) {
                    endSession();
                }
                return returnValue;
            };
        }

        /*----------------------------------------------------------------------------------------------------------------*/

        // Extensions to the Rangy Range object

        function createRangeBoundaryMover(isStart, collapse) {
            /*
             Unit can be "character" or "word"
             Options:

             - includeTrailingSpace
             - wordRegex
             - tokenizer
             - collapseSpaceBeforeLineBreak
             */
            return createEntryPointFunction(
                function(session, unit, count, moveOptions) {
                    if (typeof count == UNDEF) {
                        count = unit;
                        unit = CHARACTER;
                    }
                    moveOptions = createNestedOptions(moveOptions, defaultMoveOptions);

                    var boundaryIsStart = isStart;
                    if (collapse) {
                        boundaryIsStart = (count >= 0);
                        this.collapse(!boundaryIsStart);
                    }
                    var moveResult = movePositionBy(session.getRangeBoundaryPosition(this, boundaryIsStart), unit, count, moveOptions.characterOptions, moveOptions.wordOptions);
                    var newPos = moveResult.position;
                    this[boundaryIsStart ? "setStart" : "setEnd"](newPos.node, newPos.offset);
                    return moveResult.unitsMoved;
                }
            );
        }

        function createRangeTrimmer(isStart) {
            return createEntryPointFunction(
                function(session, characterOptions) {
                    characterOptions = createOptions(characterOptions, defaultCharacterOptions);
                    var pos;
                    var it = createRangeCharacterIterator(session, this, characterOptions, !isStart);
                    var trimCharCount = 0;
                    while ( (pos = it.next()) && allWhiteSpaceRegex.test(pos.character) ) {
                        ++trimCharCount;
                    }
                    it.dispose();
                    var trimmed = (trimCharCount > 0);
                    if (trimmed) {
                        this[isStart ? "moveStart" : "moveEnd"](
                            "character",
                            isStart ? trimCharCount : -trimCharCount,
                            { characterOptions: characterOptions }
                        );
                    }
                    return trimmed;
                }
            );
        }

        extend(api.rangePrototype, {
            moveStart: createRangeBoundaryMover(true, false),

            moveEnd: createRangeBoundaryMover(false, false),

            move: createRangeBoundaryMover(true, true),

            trimStart: createRangeTrimmer(true),

            trimEnd: createRangeTrimmer(false),

            trim: createEntryPointFunction(
                function(session, characterOptions) {
                    var startTrimmed = this.trimStart(characterOptions), endTrimmed = this.trimEnd(characterOptions);
                    return startTrimmed || endTrimmed;
                }
            ),

            expand: createEntryPointFunction(
                function(session, unit, expandOptions) {
                    var moved = false;
                    expandOptions = createNestedOptions(expandOptions, defaultExpandOptions);
                    var characterOptions = expandOptions.characterOptions;
                    if (!unit) {
                        unit = CHARACTER;
                    }
                    if (unit == WORD) {
                        var wordOptions = expandOptions.wordOptions;
                        var startPos = session.getRangeBoundaryPosition(this, true);
                        var endPos = session.getRangeBoundaryPosition(this, false);

                        var startTokenizedTextProvider = createTokenizedTextProvider(startPos, characterOptions, wordOptions);
                        var startToken = startTokenizedTextProvider.nextEndToken();
                        var newStartPos = startToken.chars[0].previousVisible();
                        var endToken, newEndPos;

                        if (this.collapsed) {
                            endToken = startToken;
                        } else {
                            var endTokenizedTextProvider = createTokenizedTextProvider(endPos, characterOptions, wordOptions);
                            endToken = endTokenizedTextProvider.previousStartToken();
                        }
                        newEndPos = endToken.chars[endToken.chars.length - 1];

                        if (!newStartPos.equals(startPos)) {
                            this.setStart(newStartPos.node, newStartPos.offset);
                            moved = true;
                        }
                        if (newEndPos && !newEndPos.equals(endPos)) {
                            this.setEnd(newEndPos.node, newEndPos.offset);
                            moved = true;
                        }

                        if (expandOptions.trim) {
                            if (expandOptions.trimStart) {
                                moved = this.trimStart(characterOptions) || moved;
                            }
                            if (expandOptions.trimEnd) {
                                moved = this.trimEnd(characterOptions) || moved;
                            }
                        }

                        return moved;
                    } else {
                        return this.moveEnd(CHARACTER, 1, expandOptions);
                    }
                }
            ),

            text: createEntryPointFunction(
                function(session, characterOptions) {
                    return this.collapsed ?
                        "" : getRangeCharacters(session, this, createOptions(characterOptions, defaultCharacterOptions)).join("");
                }
            ),

            selectCharacters: createEntryPointFunction(
                function(session, containerNode, startIndex, endIndex, characterOptions) {
                    var moveOptions = { characterOptions: characterOptions };
                    if (!containerNode) {
                        containerNode = getBody( this.getDocument() );
                    }
                    this.selectNodeContents(containerNode);
                    this.collapse(true);
                    this.moveStart("character", startIndex, moveOptions);
                    this.collapse(true);
                    this.moveEnd("character", endIndex - startIndex, moveOptions);
                }
            ),

            // Character indexes are relative to the start of node
            toCharacterRange: createEntryPointFunction(
                function(session, containerNode, characterOptions) {
                    if (!containerNode) {
                        containerNode = getBody( this.getDocument() );
                    }
                    var parent = containerNode.parentNode, nodeIndex = dom.getNodeIndex(containerNode);
                    var rangeStartsBeforeNode = (dom.comparePoints(this.startContainer, this.endContainer, parent, nodeIndex) == -1);
                    var rangeBetween = this.cloneRange();
                    var startIndex, endIndex;
                    if (rangeStartsBeforeNode) {
                        rangeBetween.setStartAndEnd(this.startContainer, this.startOffset, parent, nodeIndex);
                        startIndex = -rangeBetween.text(characterOptions).length;
                    } else {
                        rangeBetween.setStartAndEnd(parent, nodeIndex, this.startContainer, this.startOffset);
                        startIndex = rangeBetween.text(characterOptions).length;
                    }
                    endIndex = startIndex + this.text(characterOptions).length;

                    return {
                        start: startIndex,
                        end: endIndex
                    };
                }
            ),

            findText: createEntryPointFunction(
                function(session, searchTermParam, findOptions) {
                    // Set up options
                    findOptions = createNestedOptions(findOptions, defaultFindOptions);

                    // Create word options if we're matching whole words only
                    if (findOptions.wholeWordsOnly) {
                        // We don't ever want trailing spaces for search results
                        findOptions.wordOptions.includeTrailingSpace = false;
                    }

                    var backward = isDirectionBackward(findOptions.direction);

                    // Create a range representing the search scope if none was provided
                    var searchScopeRange = findOptions.withinRange;
                    if (!searchScopeRange) {
                        searchScopeRange = api.createRange();
                        searchScopeRange.selectNodeContents(this.getDocument());
                    }

                    // Examine and prepare the search term
                    var searchTerm = searchTermParam, isRegex = false;
                    if (typeof searchTerm == "string") {
                        if (!findOptions.caseSensitive) {
                            searchTerm = searchTerm.toLowerCase();
                        }
                    } else {
                        isRegex = true;
                    }

                    var initialPos = session.getRangeBoundaryPosition(this, !backward);

                    // Adjust initial position if it lies outside the search scope
                    var comparison = searchScopeRange.comparePoint(initialPos.node, initialPos.offset);

                    if (comparison === -1) {
                        initialPos = session.getRangeBoundaryPosition(searchScopeRange, true);
                    } else if (comparison === 1) {
                        initialPos = session.getRangeBoundaryPosition(searchScopeRange, false);
                    }

                    var pos = initialPos;
                    var wrappedAround = false;

                    // Try to find a match and ignore invalid ones
                    var findResult;
                    while (true) {
                        findResult = findTextFromPosition(pos, searchTerm, isRegex, searchScopeRange, findOptions);

                        if (findResult) {
                            if (findResult.valid) {
                                this.setStartAndEnd(findResult.startPos.node, findResult.startPos.offset, findResult.endPos.node, findResult.endPos.offset);
                                return true;
                            } else {
                                // We've found a match that is not a whole word, so we carry on searching from the point immediately
                                // after the match
                                pos = backward ? findResult.startPos : findResult.endPos;
                            }
                        } else if (findOptions.wrap && !wrappedAround) {
                            // No result found but we're wrapping around and limiting the scope to the unsearched part of the range
                            searchScopeRange = searchScopeRange.cloneRange();
                            pos = session.getRangeBoundaryPosition(searchScopeRange, !backward);
                            searchScopeRange.setBoundary(initialPos.node, initialPos.offset, backward);
                            wrappedAround = true;
                        } else {
                            // Nothing found and we can't wrap around, so we're done
                            return false;
                        }
                    }
                }
            ),

            pasteHtml: function(html) {
                this.deleteContents();
                if (html) {
                    var frag = this.createContextualFragment(html);
                    var lastChild = frag.lastChild;
                    this.insertNode(frag);
                    this.collapseAfter(lastChild);
                }
            }
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        // Extensions to the Rangy Selection object

        function createSelectionTrimmer(methodName) {
            return createEntryPointFunction(
                function(session, characterOptions) {
                    var trimmed = false;
                    this.changeEachRange(function(range) {
                        trimmed = range[methodName](characterOptions) || trimmed;
                    });
                    return trimmed;
                }
            );
        }

        extend(api.selectionPrototype, {
            expand: createEntryPointFunction(
                function(session, unit, expandOptions) {
                    this.changeEachRange(function(range) {
                        range.expand(unit, expandOptions);
                    });
                }
            ),

            move: createEntryPointFunction(
                function(session, unit, count, options) {
                    var unitsMoved = 0;
                    if (this.focusNode) {
                        this.collapse(this.focusNode, this.focusOffset);
                        var range = this.getRangeAt(0);
                        if (!options) {
                            options = {};
                        }
                        options.characterOptions = createOptions(options.characterOptions, defaultCaretCharacterOptions);
                        unitsMoved = range.move(unit, count, options);
                        this.setSingleRange(range);
                    }
                    return unitsMoved;
                }
            ),

            trimStart: createSelectionTrimmer("trimStart"),
            trimEnd: createSelectionTrimmer("trimEnd"),
            trim: createSelectionTrimmer("trim"),

            selectCharacters: createEntryPointFunction(
                function(session, containerNode, startIndex, endIndex, direction, characterOptions) {
                    var range = api.createRange(containerNode);
                    range.selectCharacters(containerNode, startIndex, endIndex, characterOptions);
                    this.setSingleRange(range, direction);
                }
            ),

            saveCharacterRanges: createEntryPointFunction(
                function(session, containerNode, characterOptions) {
                    var ranges = this.getAllRanges(), rangeCount = ranges.length;
                    var rangeInfos = [];

                    var backward = rangeCount == 1 && this.isBackward();

                    for (var i = 0, len = ranges.length; i < len; ++i) {
                        rangeInfos[i] = {
                            characterRange: ranges[i].toCharacterRange(containerNode, characterOptions),
                            backward: backward,
                            characterOptions: characterOptions
                        };
                    }

                    return rangeInfos;
                }
            ),

            restoreCharacterRanges: createEntryPointFunction(
                function(session, containerNode, saved) {
                    this.removeAllRanges();
                    for (var i = 0, len = saved.length, range, rangeInfo, characterRange; i < len; ++i) {
                        rangeInfo = saved[i];
                        characterRange = rangeInfo.characterRange;
                        range = api.createRange(containerNode);
                        range.selectCharacters(containerNode, characterRange.start, characterRange.end, rangeInfo.characterOptions);
                        this.addRange(range, rangeInfo.backward);
                    }
                }
            ),

            text: createEntryPointFunction(
                function(session, characterOptions) {
                    var rangeTexts = [];
                    for (var i = 0, len = this.rangeCount; i < len; ++i) {
                        rangeTexts[i] = this.getRangeAt(i).text(characterOptions);
                    }
                    return rangeTexts.join("");
                }
            )
        });

        /*----------------------------------------------------------------------------------------------------------------*/

        // Extensions to the core rangy object

        api.innerText = function(el, characterOptions) {
            var range = api.createRange(el);
            range.selectNodeContents(el);
            var text = range.text(characterOptions);
            return text;
        };

        api.createWordIterator = function(startNode, startOffset, iteratorOptions) {
            var session = getSession();
            iteratorOptions = createNestedOptions(iteratorOptions, defaultWordIteratorOptions);
            var startPos = session.getPosition(startNode, startOffset);
            var tokenizedTextProvider = createTokenizedTextProvider(startPos, iteratorOptions.characterOptions, iteratorOptions.wordOptions);
            var backward = isDirectionBackward(iteratorOptions.direction);

            return {
                next: function() {
                    return backward ? tokenizedTextProvider.previousStartToken() : tokenizedTextProvider.nextEndToken();
                },

                dispose: function() {
                    tokenizedTextProvider.dispose();
                    this.next = function() {};
                }
            };
        };

        /*----------------------------------------------------------------------------------------------------------------*/

        api.noMutation = function(func) {
            var session = getSession();
            func(session);
            endSession();
        };

        api.noMutation.createEntryPointFunction = createEntryPointFunction;

        api.textRange = {
            isBlockNode: isBlockNode,
            isCollapsedWhitespaceNode: isCollapsedWhitespaceNode,

            createPosition: createEntryPointFunction(
                function(session, node, offset) {
                    return session.getPosition(node, offset);
                }
            )
        };
    });
    
    return rangy;
}, this);
YUI.add('moodle-editor_atto-rangy', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

if (!rangy.initialized) {
    rangy.init();
}


}, '@VERSION@', {"requires": []});
