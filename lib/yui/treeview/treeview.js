/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.6.0
*/
(function () {
	var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event,
		Lang = YAHOO.lang,
		Widget = YAHOO.widget;

/**
 * The treeview widget is a generic tree building tool.
 * @module treeview
 * @title TreeView Widget
 * @requires yahoo, event
 * @optional animation
 * @namespace YAHOO.widget
 */

/**
 * Contains the tree view state data and the root node.
 *
 * @class TreeView
 * @uses YAHOO.util.EventProvider
 * @constructor
 * @param {string|HTMLElement} id The id of the element, or the element itself that the tree will be inserted into.  Existing markup in this element, if valid, will be used to build the tree
 * @param {Array|object|string}  oConfig (optional)  An array containing the definition of the tree.  Objects will be converted to arrays of one element.  A string will produce a single TextNode
 * 
 */
YAHOO.widget.TreeView = function(id, oConfig) {
    if (id) { this.init(id); }
	if (oConfig) {
		if (!Lang.isArray(oConfig)) {
			oConfig = [oConfig];
		}
		this.buildTreeFromObject(oConfig);
	} else if (Lang.trim(this._el.innerHTML)) {
		this.buildTreeFromMarkup(id);
	}
};

var TV = Widget.TreeView;

TV.prototype = {

    /**
     * The id of tree container element
     * @property id
     * @type String
     */
    id: null,

    /**
     * The host element for this tree
     * @property _el
     * @private
     * @type HTMLelement
     */
    _el: null,

     /**
     * Flat collection of all nodes in this tree.  This is a sparse
     * array, so the length property can't be relied upon for a
     * node count for the tree.
     * @property _nodes
     * @type Node[]
     * @private
     */
    _nodes: null,

    /**
     * We lock the tree control while waiting for the dynamic loader to return
     * @property locked
     * @type boolean
     */
    locked: false,

    /**
     * The animation to use for expanding children, if any
     * @property _expandAnim
     * @type string
     * @private
     */
    _expandAnim: null,

    /**
     * The animation to use for collapsing children, if any
     * @property _collapseAnim
     * @type string
     * @private
     */
    _collapseAnim: null,

    /**
     * The current number of animations that are executing
     * @property _animCount
     * @type int
     * @private
     */
    _animCount: 0,

    /**
     * The maximum number of animations to run at one time.
     * @property maxAnim
     * @type int
     */
    maxAnim: 2,

    /**
     * Whether there is any subscriber to dblClickEvent
     * @property _hasDblClickSubscriber
     * @type boolean
     * @private
     */
    _hasDblClickSubscriber: false,
	
    /**
     * Stores the timer used to check for double clicks
     * @property _dblClickTimer
     * @type window.timer object
     * @private
     */
    _dblClickTimer: null,


    /**
     * Sets up the animation for expanding children
     * @method setExpandAnim
     * @param {string} type the type of animation (acceptable values defined 
     * in YAHOO.widget.TVAnim)
     */
    setExpandAnim: function(type) {
        this._expandAnim = (Widget.TVAnim.isValid(type)) ? type : null;
    },

    /**
     * Sets up the animation for collapsing children
     * @method setCollapseAnim
     * @param {string} the type of animation (acceptable values defined in 
     * YAHOO.widget.TVAnim)
     */
    setCollapseAnim: function(type) {
        this._collapseAnim = (Widget.TVAnim.isValid(type)) ? type : null;
    },

    /**
     * Perform the expand animation if configured, or just show the
     * element if not configured or too many animations are in progress
     * @method animateExpand
     * @param el {HTMLElement} the element to animate
     * @param node {YAHOO.util.Node} the node that was expanded
     * @return {boolean} true if animation could be invoked, false otherwise
     */
    animateExpand: function(el, node) {

        if (this._expandAnim && this._animCount < this.maxAnim) {
            // this.locked = true;
            var tree = this;
            var a = Widget.TVAnim.getAnim(this._expandAnim, el, 
                            function() { tree.expandComplete(node); });
            if (a) { 
                ++this._animCount;
                this.fireEvent("animStart", {
                        "node": node, 
                        "type": "expand"
                    });
                a.animate();
            }

            return true;
        }

        return false;
    },

    /**
     * Perform the collapse animation if configured, or just show the
     * element if not configured or too many animations are in progress
     * @method animateCollapse
     * @param el {HTMLElement} the element to animate
     * @param node {YAHOO.util.Node} the node that was expanded
     * @return {boolean} true if animation could be invoked, false otherwise
     */
    animateCollapse: function(el, node) {

        if (this._collapseAnim && this._animCount < this.maxAnim) {
            // this.locked = true;
            var tree = this;
            var a = Widget.TVAnim.getAnim(this._collapseAnim, el, 
                            function() { tree.collapseComplete(node); });
            if (a) { 
                ++this._animCount;
                this.fireEvent("animStart", {
                        "node": node, 
                        "type": "collapse"
                    });
                a.animate();
            }

            return true;
        }

        return false;
    },

    /**
     * Function executed when the expand animation completes
     * @method expandComplete
     */
    expandComplete: function(node) {
        --this._animCount;
        this.fireEvent("animComplete", {
                "node": node, 
                "type": "expand"
            });
        // this.locked = false;
    },

    /**
     * Function executed when the collapse animation completes
     * @method collapseComplete
     */
    collapseComplete: function(node) {
        --this._animCount;
        this.fireEvent("animComplete", {
                "node": node, 
                "type": "collapse"
            });
        // this.locked = false;
    },

    /**
     * Initializes the tree
     * @method init
     * @parm {string|HTMLElement} id the id of the element that will hold the tree
     * @private
     */
    init: function(id) {
		this._el = Dom.get(id);
		this.id = Dom.generateId(this._el,"yui-tv-auto-id-");

    /**
         * When animation is enabled, this event fires when the animation
         * starts
         * @event animStart
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that is expanding/collapsing
         * @parm {String} type the type of animation ("expand" or "collapse")
         */
        this.createEvent("animStart", this);

        /**
         * When animation is enabled, this event fires when the animation
         * completes
         * @event animComplete
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that is expanding/collapsing
         * @parm {String} type the type of animation ("expand" or "collapse")
         */
        this.createEvent("animComplete", this);

        /**
         * Fires when a node is going to be collapsed.  Return false to stop
         * the collapse.
         * @event collapse
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that is collapsing
         */
        this.createEvent("collapse", this);

        /**
         * Fires after a node is successfully collapsed.  This event will not fire
         * if the "collapse" event was cancelled.
         * @event collapseComplete
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that was collapsed
         */
        this.createEvent("collapseComplete", this);

        /**
         * Fires when a node is going to be expanded.  Return false to stop
         * the collapse.
         * @event expand
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that is expanding
         */
        this.createEvent("expand", this);

        /**
         * Fires after a node is successfully expanded.  This event will not fire
         * if the "expand" event was cancelled.
         * @event expandComplete
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that was expanded
         */
        this.createEvent("expandComplete", this);

    /**
         * Fires when the Enter key is pressed on a node that has the focus
         * @event enterKeyPressed
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node that has the focus
         */
        this.createEvent("enterKeyPressed", this);
		
    /**
         * Fires when the label in a TextNode or MenuNode or content in an HTMLNode receives a Click.
	* The listener may return false to cancel toggling and focusing on the node.
         * @event clickEvent
         * @type CustomEvent
         * @param oArgs.event  {HTMLEvent} The event object
         * @param oArgs.node {YAHOO.widget.Node} node the node that was clicked
         */
        this.createEvent("clickEvent", this);

	/**
         * Fires when the label in a TextNode or MenuNode or content in an HTMLNode receives a double Click
         * @event dblClickEvent
         * @type CustomEvent
         * @param oArgs.event  {HTMLEvent} The event object
         * @param oArgs.node {YAHOO.widget.Node} node the node that was clicked
         */
		var self = this;
        this.createEvent("dblClickEvent", {
			scope:this,
			onSubscribeCallback: function() {
				self._hasDblClickSubscriber = true;
			}
		});
		
	/**
         * Custom event that is fired when the text node label is clicked. 
         *  The node clicked is  provided as an argument
         *
         * @event labelClick
         * @type CustomEvent
         * @param {YAHOO.widget.Node} node the node clicked
	* @deprecated use clickEvent or dblClickEvent
         */
		this.createEvent("labelClick", this);


        this._nodes = [];

        // store a global reference
        TV.trees[this.id] = this;

        // Set up the root node
        this.root = new Widget.RootNode(this);

        var LW = Widget.LogWriter;


		
        // YAHOO.util.Event.onContentReady(this.id, this.handleAvailable, this, true);
        // YAHOO.util.Event.on(this.id, "click", this.handleClick, this, true);
    },

    //handleAvailable: function() {
        //var Event = YAHOO.util.Event;
        //Event.on(this.id, 
    //},
 /**
     * Builds the TreeView from an object.  This is the method called by the constructor to build the tree when it has a second argument.
     * @method buildTreeFromObject
     * @param  oConfig {Array}  array containing a full description of the tree
     * 
     */
	buildTreeFromObject: function (oConfig) {
		var build = function (parent, oConfig) {
			var i, item, node, children, type, NodeType, ThisType;
			for (i = 0; i < oConfig.length; i++) {
				item = oConfig[i];
				if (Lang.isString(item)) {
					node = new Widget.TextNode(item, parent);
				} else if (Lang.isObject(item)) {
					children = item.children;
					delete item.children;
					type = item.type || 'text';
					delete item.type;
					switch (type.toLowerCase()) {
						case 'text':
							node = new Widget.TextNode(item, parent);
							break;
						case 'menu':
							node = new Widget.MenuNode(item, parent);
							break;
						case 'html':
							node = new Widget.HTMLNode(item, parent);
							break;
						default:
							NodeType = Widget[type];
							if (Lang.isObject(NodeType)) {
								for (ThisType = NodeType; ThisType && ThisType !== Widget.Node; ThisType = ThisType.superclass.constructor) {}
								if (ThisType) {
									node = new NodeType(item, parent);
								} else {
								}
							} else {
							}
					}
					if (children) {
						build(node,children);
					}
				} else {
				}
			}
		};
							
					
		build(this.root,oConfig);
	},
/**
     * Builds the TreeView from existing markup.   Markup should consist of &lt;UL&gt; or &lt;OL&gt; elements, possibly nested.  
     * Depending what the &lt;LI&gt; elements contain the following will be created: <ul>
     * 	         <li>plain text:  a regular TextNode</li>
     * 	         <li>an (un-)ordered list: a nested branch</li>
     * 	         <li>anything else: an HTMLNode</li></ul>
     * Only the first  outermost (un-)ordered list in the markup and its children will be parsed.
     * Tree will be fully collapsed.
     *  HTMLNodes have hasIcon set to true if the markup for that node has a className called hasIcon.
     * @method buildTreeFromMarkup
     * @param {string|HTMLElement} id the id of the element that contains the markup or a reference to it.
     */
	buildTreeFromMarkup: function (id) {
		var build = function (parent,markup) {
			var el, node, child, text;
			for (el = Dom.getFirstChild(markup); el; el = Dom.getNextSibling(el)) {
				if (el.nodeType == 1) {
					switch (el.tagName.toUpperCase()) {
						case 'LI':
							for (child = el.firstChild; child; child = child.nextSibling) {
								if (child.nodeType == 3) {
									text = Lang.trim(child.nodeValue);
									if (text.length) {
										node = new Widget.TextNode(text, parent, false);
									}
								} else {
									switch (child.tagName.toUpperCase()) {
										case 'UL':
										case 'OL':
											build(node,child);
											break;
										case 'A':
											node = new Widget.TextNode({
												label:child.innerHTML,
												href: child.href,
												target:child.target,
												title:child.title ||child.alt
											},parent,false);
											break;
										default:
											node = new Widget.HTMLNode(child.parentNode.innerHTML, parent, false, true);
											break;
									}
								}
							}
							break;
						case 'UL':
						case 'OL':
							build(node, el);
							break;
					}
				}
			}
		
		};
		var markup = Dom.getChildrenBy(Dom.get(id),function (el) { 
			var tag = el.tagName.toUpperCase();
			return  tag == 'UL' || tag == 'OL';
		});
		if (markup.length) {
			build(this.root, markup[0]);
		} else {
		}
	},
    /**
     * Renders the tree boilerplate and visible nodes
     * @method render
     */
    render: function() {
        var html = this.root.getHtml();
        this.getEl().innerHTML = html;
		var getTarget = function (ev) {
			var target = Event.getTarget(ev); 
			if (target.tagName.toUpperCase() != 'TD') { target = Dom.getAncestorByTagName(target,'td'); }
			if (Lang.isNull(target)) { return null; }
			if (target.className.length === 0) {
				target = target.previousSibling;
				if (Lang.isNull(target)) { return null; }
			}
			return target;
		};
		if (!this._hasEvents) {
			Event.on(
				this.getEl(),
				'click',
				function (ev) {
					var self = this,
						el = Event.getTarget(ev),
						node = this.getNodeByElement(el);
					if (!node) { return; }
						
					var toggle = function () {
						if (node.expanded) {
							node.collapse();
						} else {
							node.expand();
						}
						node.focus();
					};
					
					if (Dom.hasClass(el, node.labelStyle) || Dom.getAncestorByClassName(el,node.labelStyle)) {
						this.fireEvent('labelClick',node);
					}
					while (el && !Dom.hasClass(el.parentNode,'ygtvrow') && !/ygtv[tl][mp]h?h?/.test(el.className)) {
						el = Dom.getAncestorByTagName(el,'td');
					}
					if (el) {
						// If it is a spacer cell, do nothing
						if (/ygtv(blank)?depthcell/.test(el.className)) { return;}
						//  If it is a toggle cell, toggle
						if (/ygtv[tl][mp]h?h?/.test(el.className)) {
							toggle();
						} else {
							if (this._dblClickTimer) {
								window.clearTimeout(this._dblClickTimer);
								this._dblClickTimer = null;
							} else {
								if (this._hasDblClickSubscriber) {
									this._dblClickTimer = window.setTimeout(function () {
										self._dblClickTimer = null;
										if (self.fireEvent('clickEvent', {event:ev,node:node}) !== false) { 
											toggle();
										}
									}, 200);
								} else {
									if (self.fireEvent('clickEvent', {event:ev,node:node}) !== false) { 
										toggle();
									}
								}
							}
						}
					}
				},
				this,
				true
			);
			
			Event.on(
				this.getEl(),
				'dblclick',
				function (ev) {
					if (!this._hasDblClickSubscriber) { return; }
					var el = Event.getTarget(ev);
					while (!Dom.hasClass(el.parentNode,'ygtvrow')) {
						el = Dom.getAncestorByTagName(el,'td');
					}
					if (/ygtv(blank)?depthcell/.test(el.className)) { return;}
					if (!(/ygtv[tl][mp]h?h?/.test(el.className))) {
						this.fireEvent('dblClickEvent', {event:ev, node:this.getNodeByElement(el)}); 
						if (this._dblClickTimer) {
							window.clearTimeout(this._dblClickTimer);
							this._dblClickTimer = null;
						}
					}
				},
				this,
				true
			);
			Event.on(
				this.getEl(),
				'mouseover',
				function (ev) {
					var target = getTarget(ev);
					if (target) {
target.className = target.className.replace(/ygtv([lt])([mp])/gi, 'ygtv$1$2h').replace(/h+/, 'h');
					}
				}
			);
			Event.on(
				this.getEl(),
				'mouseout',
				function (ev) {
					var target = getTarget(ev);
					if (target) {
						target.className = target.className.replace(/ygtv([lt])([mp])h/gi,'ygtv$1$2');
					}
				}
			);
			Event.on(
				this.getEl(),
				'keydown',
				function (ev) {
					var target = Event.getTarget(ev),
						node = this.getNodeByElement(target),
						newNode = node,
						KEY = YAHOO.util.KeyListener.KEY;

					switch(ev.keyCode) {
						case KEY.UP:
							do {
								if (newNode.previousSibling) {
									newNode = newNode.previousSibling;
								} else {
									newNode = newNode.parent;
								}
							} while (newNode && !newNode.focus());
							if (!newNode) { node.focus(); }
							Event.preventDefault(ev);
							break;
						case KEY.DOWN:
							do {
								if (newNode.nextSibling) {
									newNode = newNode.nextSibling;
								} else {
									newNode.expand();
									newNode = (newNode.children.length || null) && newNode.children[0];
								}
							} while (newNode && !newNode.focus());
							if (!newNode) { node.focus(); }
							Event.preventDefault(ev);
							break;
						case KEY.LEFT:
							do {
								if (newNode.parent) {
									newNode = newNode.parent;
								} else {
									newNode = newNode.previousSibling;
								}
							} while (newNode && !newNode.focus());
							if (!newNode) { node.focus(); }
							Event.preventDefault(ev);
							break;
						case KEY.RIGHT:
							do {
								newNode.expand();
								if (newNode.children.length) {
									newNode = newNode.children[0];
								} else {
									newNode = newNode.nextSibling;
								}
							} while (newNode && !newNode.focus());
							if (!newNode) { node.focus(); }
							Event.preventDefault(ev);
							break;
						case KEY.ENTER:
							if (node.href) {
								if (node.target) {
									window.open(node.href,node.target);
								} else {
									window.location(node.href);
								}
							} else {
								node.toggle();
							}
							this.fireEvent('enterKeyPressed',node);
							Event.preventDefault(ev);
							break;
						case KEY.HOME:
							newNode = this.getRoot();
							if (newNode.children.length) {newNode = newNode.children[0];}
							if (!newNode.focus()) { node.focus(); }
							Event.preventDefault(ev);
							break;
						case KEY.END:
							newNode = newNode.parent.children;
							newNode = newNode[newNode.length -1];
							if (!newNode.focus()) { node.focus(); }
							Event.preventDefault(ev);
							break;
						// case KEY.PAGE_UP:
							// break;
						// case KEY.PAGE_DOWN:
							// break;
						case 107:  // plus key
							if (ev.shiftKey) {
								node.parent.expandAll();
							} else {
								node.expand();
							}
							break;
						case 109: // minus key
							if (ev.shiftKey) {
								node.parent.collapseAll();
							} else {
								node.collapse();
							}
							break;
						default:
							break;
					}
				},
				this,
				true
			);
		}
		this._hasEvents = true;
    },
	
  /**
     * Returns the tree's host element
     * @method getEl
     * @return {HTMLElement} the host element
     */
    getEl: function() {
        if (! this._el) {
            this._el = Dom.get(this.id);
        }
        return this._el;
    },

    /**
     * Nodes register themselves with the tree instance when they are created.
     * @method regNode
     * @param node {Node} the node to register
     * @private
     */
    regNode: function(node) {
        this._nodes[node.index] = node;
    },

    /**
     * Returns the root node of this tree
     * @method getRoot
     * @return {Node} the root node
     */
    getRoot: function() {
        return this.root;
    },

    /**
     * Configures this tree to dynamically load all child data
     * @method setDynamicLoad
     * @param {function} fnDataLoader the function that will be called to get the data
     * @param iconMode {int} configures the icon that is displayed when a dynamic
     * load node is expanded the first time without children.  By default, the 
     * "collapse" icon will be used.  If set to 1, the leaf node icon will be
     * displayed.
     */
    setDynamicLoad: function(fnDataLoader, iconMode) { 
        this.root.setDynamicLoad(fnDataLoader, iconMode);
    },

    /**
     * Expands all child nodes.  Note: this conflicts with the "multiExpand"
     * node property.  If expand all is called in a tree with nodes that
     * do not allow multiple siblings to be displayed, only the last sibling
     * will be expanded.
     * @method expandAll
     */
    expandAll: function() { 
        if (!this.locked) {
            this.root.expandAll(); 
        }
    },

    /**
     * Collapses all expanded child nodes in the entire tree.
     * @method collapseAll
     */
    collapseAll: function() { 
        if (!this.locked) {
            this.root.collapseAll(); 
        }
    },

    /**
     * Returns a node in the tree that has the specified index (this index
     * is created internally, so this function probably will only be used
     * in html generated for a given node.)
     * @method getNodeByIndex
     * @param {int} nodeIndex the index of the node wanted
     * @return {Node} the node with index=nodeIndex, null if no match
     */
    getNodeByIndex: function(nodeIndex) {
        var n = this._nodes[nodeIndex];
        return (n) ? n : null;
    },

    /**
     * Returns a node that has a matching property and value in the data
     * object that was passed into its constructor.
     * @method getNodeByProperty
     * @param {object} property the property to search (usually a string)
     * @param {object} value the value we want to find (usuall an int or string)
     * @return {Node} the matching node, null if no match
     */
    getNodeByProperty: function(property, value) {
        for (var i in this._nodes) {
			if (this._nodes.hasOwnProperty(i)) {
	            var n = this._nodes[i];
	            if (n.data && value == n.data[property]) {
	                return n;
	            }
			}
        }

        return null;
    },

    /**
     * Returns a collection of nodes that have a matching property 
     * and value in the data object that was passed into its constructor.  
     * @method getNodesByProperty
     * @param {object} property the property to search (usually a string)
     * @param {object} value the value we want to find (usuall an int or string)
     * @return {Array} the matching collection of nodes, null if no match
     */
    getNodesByProperty: function(property, value) {
        var values = [];
        for (var i in this._nodes) {
			if (this._nodes.hasOwnProperty(i)) {
	            var n = this._nodes[i];
	            if (n.data && value == n.data[property]) {
	                values.push(n);
	            }
			}
        }

        return (values.length) ? values : null;
    },

    /**
     * Returns the treeview node reference for an anscestor element
     * of the node, or null if it is not contained within any node
     * in this tree.
     * @method getNodeByElement
     * @param {HTMLElement} the element to test
     * @return {YAHOO.widget.Node} a node reference or null
     */
    getNodeByElement: function(el) {

        var p=el, m, re=/ygtv([^\d]*)(.*)/;

        do {

            if (p && p.id) {
                m = p.id.match(re);
                if (m && m[2]) {
                    return this.getNodeByIndex(m[2]);
                }
            }

            p = p.parentNode;

            if (!p || !p.tagName) {
                break;
            }

        } 
        while (p.id !== this.id && p.tagName.toLowerCase() !== "body");

        return null;
    },

    /**
     * Removes the node and its children, and optionally refreshes the 
     * branch of the tree that was affected.
     * @method removeNode
     * @param {Node} The node to remove
     * @param {boolean} autoRefresh automatically refreshes branch if true
     * @return {boolean} False is there was a problem, true otherwise.
     */
    removeNode: function(node, autoRefresh) { 

        // Don't delete the root node
        if (node.isRoot()) {
            return false;
        }

        // Get the branch that we may need to refresh
        var p = node.parent;
        if (p.parent) {
            p = p.parent;
        }

        // Delete the node and its children
        this._deleteNode(node);

        // Refresh the parent of the parent
        if (autoRefresh && p && p.childrenRendered) {
            p.refresh();
        }

        return true;
    },

    /**
     * wait until the animation is complete before deleting 
     * to avoid javascript errors
     * @method _removeChildren_animComplete
     * @param o the custom event payload
     * @private
     */
    _removeChildren_animComplete: function(o) {
        this.unsubscribe(this._removeChildren_animComplete);
        this.removeChildren(o.node);
    },

    /**
     * Deletes this nodes child collection, recursively.  Also collapses
     * the node, and resets the dynamic load flag.  The primary use for
     * this method is to purge a node and allow it to fetch its data
     * dynamically again.
     * @method removeChildren
     * @param {Node} node the node to purge
     */
    removeChildren: function(node) { 

        if (node.expanded) {
            // wait until the animation is complete before deleting to
            // avoid javascript errors
            if (this._collapseAnim) {
                this.subscribe("animComplete", 
                        this._removeChildren_animComplete, this, true);
                Widget.Node.prototype.collapse.call(node);
                return;
            }

            node.collapse();
        }

        while (node.children.length) {
            this._deleteNode(node.children[0]);
        }

        if (node.isRoot()) {
            Widget.Node.prototype.expand.call(node);
        }

        node.childrenRendered = false;
        node.dynamicLoadComplete = false;

        node.updateIcon();
    },

    /**
     * Deletes the node and recurses children
     * @method _deleteNode
     * @private
     */
    _deleteNode: function(node) { 
        // Remove all the child nodes first
        this.removeChildren(node);

        // Remove the node from the tree
        this.popNode(node);
    },

    /**
     * Removes the node from the tree, preserving the child collection 
     * to make it possible to insert the branch into another part of the 
     * tree, or another tree.
     * @method popNode
     * @param {Node} the node to remove
     */
    popNode: function(node) { 
        var p = node.parent;

        // Update the parent's collection of children
        var a = [];

        for (var i=0, len=p.children.length;i<len;++i) {
            if (p.children[i] != node) {
                a[a.length] = p.children[i];
            }
        }

        p.children = a;

        // reset the childrenRendered flag for the parent
        p.childrenRendered = false;

         // Update the sibling relationship
        if (node.previousSibling) {
            node.previousSibling.nextSibling = node.nextSibling;
        }

        if (node.nextSibling) {
            node.nextSibling.previousSibling = node.previousSibling;
        }

        node.parent = null;
        node.previousSibling = null;
        node.nextSibling = null;
        node.tree = null;

        // Update the tree's node collection 
        delete this._nodes[node.index];
    },

	/**
	* Nulls out the entire TreeView instance and related objects, removes attached
	* event listeners, and clears out DOM elements inside the container. After
	* calling this method, the instance reference should be expliclitly nulled by
	* implementer, as in myDataTable = null. Use with caution!
	*
	* @method destroy
	*/
	destroy : function() {
		// Since the label editor can be separated from the main TreeView control
		// the destroy method for it might not be there.
		if (this._destroyEditor) { this._destroyEditor(); }
		var el = this.getEl();
		Event.removeListener(el,'click');
		Event.removeListener(el,'dblclick');
		Event.removeListener(el,'mouseover');
		Event.removeListener(el,'mouseout');
		Event.removeListener(el,'keydown');
		for (var i = 0 ; i < this._nodes.length; i++) {
			var node = this._nodes[i];
			if (node && node.destroy) {node.destroy(); }
		}
		el.parentNode.removeChild(el);
		this._hasEvents = false;
	},
		
			


    /**
     * TreeView instance toString
     * @method toString
     * @return {string} string representation of the tree
     */
    toString: function() {
        return "TreeView " + this.id;
    },

    /**
     * Count of nodes in tree
     * @method getNodeCount
     * @return {int} number of nodes in the tree
     */
    getNodeCount: function() {
        return this.getRoot().getNodeCount();
    },

    /**
     * Returns an object which could be used to rebuild the tree.
     * It can be passed to the tree constructor to reproduce the same tree.
     * It will return false if any node loads dynamically, regardless of whether it is loaded or not.
     * @method getTreeDefinition
     * @return {Object | false}  definition of the tree or false if any node is defined as dynamic
     */
    getTreeDefinition: function() {
        return this.getRoot().getNodeDefinition();
    },

    /**
     * Abstract method that is executed when a node is expanded
     * @method onExpand
     * @param node {Node} the node that was expanded
     * @deprecated use treeobj.subscribe("expand") instead
     */
    onExpand: function(node) { },

    /**
     * Abstract method that is executed when a node is collapsed.
     * @method onCollapse
     * @param node {Node} the node that was collapsed.
     * @deprecated use treeobj.subscribe("collapse") instead
     */
    onCollapse: function(node) { }

};

/* Backwards compatibility aliases */
var PROT = TV.prototype;
 /**
     * Renders the tree boilerplate and visible nodes.
     *  Alias for render
     * @method draw
     * @deprecated Use render instead
     */
PROT.draw = PROT.render;

/* end backwards compatibility aliases */

YAHOO.augment(TV, YAHOO.util.EventProvider);

/**
 * Running count of all nodes created in all trees.  This is 
 * used to provide unique identifies for all nodes.  Deleting
 * nodes does not change the nodeCount.
 * @property YAHOO.widget.TreeView.nodeCount
 * @type int
 * @static
 */
TV.nodeCount = 0;

/**
 * Global cache of tree instances
 * @property YAHOO.widget.TreeView.trees
 * @type Array
 * @static
 * @private
 */
TV.trees = [];

/**
 * Global method for getting a tree by its id.  Used in the generated
 * tree html.
 * @method YAHOO.widget.TreeView.getTree
 * @param treeId {String} the id of the tree instance
 * @return {TreeView} the tree instance requested, null if not found.
 * @static
 */
TV.getTree = function(treeId) {
    var t = TV.trees[treeId];
    return (t) ? t : null;
};


/**
 * Global method for getting a node by its id.  Used in the generated
 * tree html.
 * @method YAHOO.widget.TreeView.getNode
 * @param treeId {String} the id of the tree instance
 * @param nodeIndex {String} the index of the node to return
 * @return {Node} the node instance requested, null if not found
 * @static
 */
TV.getNode = function(treeId, nodeIndex) {
    var t = TV.getTree(treeId);
    return (t) ? t.getNodeByIndex(nodeIndex) : null;
};


/**
     * Class name assigned to elements that have the focus
     *
     * @property TreeView.FOCUS_CLASS_NAME
     * @type String
     * @static
     * @final
     * @default "ygtvfocus"

	*/ 
TV.FOCUS_CLASS_NAME = 'ygtvfocus';

/**
 * Attempts to preload the images defined in the styles used to draw the tree by
 * rendering off-screen elements that use the styles.
 * @method YAHOO.widget.TreeView.preload
 * @param {string} prefix the prefix to use to generate the names of the
 * images to preload, default is ygtv
 * @static
 */
TV.preload = function(e, prefix) {
    prefix = prefix || "ygtv";


    var styles = ["tn","tm","tmh","tp","tph","ln","lm","lmh","lp","lph","loading"];
    // var styles = ["tp"];

    var sb = [];
    
    // save the first one for the outer container
    for (var i=1; i < styles.length; i=i+1) { 
        sb[sb.length] = '<span class="' + prefix + styles[i] + '">&#160;</span>';
    }

    var f = document.createElement("div");
    var s = f.style;
    s.className = prefix + styles[0];
    s.position = "absolute";
    s.height = "1px";
    s.width = "1px";
    s.top = "-1000px";
    s.left = "-1000px";
    f.innerHTML = sb.join("");

    document.body.appendChild(f);

    Event.removeListener(window, "load", TV.preload);

};

Event.addListener(window,"load", TV.preload);
})();
(function () {
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event;
/**
 * The base class for all tree nodes.  The node's presentation and behavior in
 * response to mouse events is handled in Node subclasses.
 * @namespace YAHOO.widget
 * @class Node
 * @uses YAHOO.util.EventProvider
 * @param oData {object} a string or object containing the data that will
 * be used to render this node, and any custom attributes that should be
 * stored with the node (which is available in noderef.data).
 * All values in oData will be used to set equally named properties in the node
 * as long as the node does have such properties, they are not undefined, private or functions.
 * @param oParent {Node} this node's parent node
 * @param expanded {boolean} the initial expanded/collapsed state (deprecated, use oData.expanded)
 * @constructor
 */
YAHOO.widget.Node = function(oData, oParent, expanded) {
    if (oData) { this.init(oData, oParent, expanded); }
};

YAHOO.widget.Node.prototype = {

    /**
     * The index for this instance obtained from global counter in YAHOO.widget.TreeView.
     * @property index
     * @type int
     */
    index: 0,

    /**
     * This node's child node collection.
     * @property children
     * @type Node[] 
     */
    children: null,

    /**
     * Tree instance this node is part of
     * @property tree
     * @type TreeView
     */
    tree: null,

    /**
     * The data linked to this node.  This can be any object or primitive
     * value, and the data can be used in getNodeHtml().
     * @property data
     * @type object
     */
    data: null,

    /**
     * Parent node
     * @property parent
     * @type Node
     */
    parent: null,

    /**
     * The depth of this node.  We start at -1 for the root node.
     * @property depth
     * @type int
     */
    depth: -1,

    /**
     * The href for the node's label.  If one is not specified, the href will
     * be set so that it toggles the node.
     * @property href
     * @type string
     */
    href: null,

    /**
     * The label href target, defaults to current window
     * @property target
     * @type string
     */
    target: "_self",

    /**
     * The node's expanded/collapsed state
     * @property expanded
     * @type boolean
     */
    expanded: false,

    /**
     * Can multiple children be expanded at once?
     * @property multiExpand
     * @type boolean
     */
    multiExpand: true,

    /**
     * Should we render children for a collapsed node?  It is possible that the
     * implementer will want to render the hidden data...  @todo verify that we 
     * need this, and implement it if we do.
     * @property renderHidden
     * @type boolean
     */
    renderHidden: false,

    /**
     * This flag is set to true when the html is generated for this node's
     * children, and set to false when new children are added.
     * @property childrenRendered
     * @type boolean
     */
    childrenRendered: false,

    /**
     * Dynamically loaded nodes only fetch the data the first time they are
     * expanded.  This flag is set to true once the data has been fetched.
     * @property dynamicLoadComplete
     * @type boolean
     */
    dynamicLoadComplete: false,

    /**
     * This node's previous sibling
     * @property previousSibling
     * @type Node
     */
    previousSibling: null,

    /**
     * This node's next sibling
     * @property nextSibling
     * @type Node
     */
    nextSibling: null,

    /**
     * We can set the node up to call an external method to get the child
     * data dynamically.
     * @property _dynLoad
     * @type boolean
     * @private
     */
    _dynLoad: false,

    /**
     * Function to execute when we need to get this node's child data.
     * @property dataLoader
     * @type function
     */
    dataLoader: null,

    /**
     * This is true for dynamically loading nodes while waiting for the
     * callback to return.
     * @property isLoading
     * @type boolean
     */
    isLoading: false,

    /**
     * The toggle/branch icon will not show if this is set to false.  This
     * could be useful if the implementer wants to have the child contain
     * extra info about the parent, rather than an actual node.
     * @property hasIcon
     * @type boolean
     */
    hasIcon: true,

    /**
     * Used to configure what happens when a dynamic load node is expanded
     * and we discover that it does not have children.  By default, it is
     * treated as if it still could have children (plus/minus icon).  Set
     * iconMode to have it display like a leaf node instead.
     * @property iconMode
     * @type int
     */
    iconMode: 0,

    /**
     * Specifies whether or not the content area of the node should be allowed
     * to wrap.
     * @property nowrap
     * @type boolean
     * @default false
     */
    nowrap: false,

 /**
     * If true, the node will alway be rendered as a leaf node.  This can be
     * used to override the presentation when dynamically loading the entire
     * tree.  Setting this to true also disables the dynamic load call for the
     * node.
     * @property isLeaf
     * @type boolean
     * @default false
     */
    isLeaf: false,

/**
     * The CSS class for the html content container.  Defaults to ygtvhtml, but 
     * can be overridden to provide a custom presentation for a specific node.
     * @property contentStyle
     * @type string
     */
    contentStyle: "",

    /**
     * The generated id that will contain the data passed in by the implementer.
     * @property contentElId
     * @type string
     */
    contentElId: null,
 /**
     * The node type
     * @property _type
     * @private
     * @type string
     * @default "Node"
*/
    _type: "Node",

    /*
    spacerPath: "http://us.i1.yimg.com/us.yimg.com/i/space.gif",
    expandedText: "Expanded",
    collapsedText: "Collapsed",
    loadingText: "Loading",
    */

    /**
     * Initializes this node, gets some of the properties from the parent
     * @method init
     * @param oData {object} a string or object containing the data that will
     * be used to render this node
     * @param oParent {Node} this node's parent node
     * @param expanded {boolean} the initial expanded/collapsed state
     */
    init: function(oData, oParent, expanded) {

        this.data       = oData;
        this.children   = [];
        this.index      = YAHOO.widget.TreeView.nodeCount;
        ++YAHOO.widget.TreeView.nodeCount;
		this.contentElId = "ygtvcontentel" + this.index;
		
		if (Lang.isObject(oData)) {
			for (var property in oData) {
				if (property.charAt(0) != '_'  && oData.hasOwnProperty(property) && !Lang.isUndefined(this[property]) && !Lang.isFunction(this[property]) ) {
					this[property] = oData[property];
				}
			}
		}
		if (!Lang.isUndefined(expanded) ) {	this.expanded  = expanded;	}
		

        /**
         * The parentChange event is fired when a parent element is applied
         * to the node.  This is useful if you need to apply tree-level
         * properties to a tree that need to happen if a node is moved from
         * one tree to another.
         *
         * @event parentChange
         * @type CustomEvent
         */
        this.createEvent("parentChange", this);

        // oParent should never be null except when we create the root node.
        if (oParent) {
            oParent.appendChild(this);
        }
    },

    /**
     * Certain properties for the node cannot be set until the parent
     * is known. This is called after the node is inserted into a tree.
     * the parent is also applied to this node's children in order to
     * make it possible to move a branch from one tree to another.
     * @method applyParent
     * @param {Node} parentNode this node's parent node
     * @return {boolean} true if the application was successful
     */
    applyParent: function(parentNode) {
        if (!parentNode) {
            return false;
        }

        this.tree   = parentNode.tree;
        this.parent = parentNode;
        this.depth  = parentNode.depth + 1;

        // @todo why was this put here.  This causes new nodes added at the
        // root level to lose the menu behavior.
        // if (! this.multiExpand) {
            // this.multiExpand = parentNode.multiExpand;
        // }

        this.tree.regNode(this);
        parentNode.childrenRendered = false;

        // cascade update existing children
        for (var i=0, len=this.children.length;i<len;++i) {
            this.children[i].applyParent(this);
        }

        this.fireEvent("parentChange");

        return true;
    },

    /**
     * Appends a node to the child collection.
     * @method appendChild
     * @param childNode {Node} the new node
     * @return {Node} the child node
     * @private
     */
    appendChild: function(childNode) {
        if (this.hasChildren()) {
            var sib = this.children[this.children.length - 1];
            sib.nextSibling = childNode;
            childNode.previousSibling = sib;
        }
        this.children[this.children.length] = childNode;
        childNode.applyParent(this);

        // part of the IE display issue workaround. If child nodes
        // are added after the initial render, and the node was
        // instantiated with expanded = true, we need to show the
        // children div now that the node has a child.
        if (this.childrenRendered && this.expanded) {
            this.getChildrenEl().style.display = "";
        }

        return childNode;
    },

    /**
     * Appends this node to the supplied node's child collection
     * @method appendTo
     * @param parentNode {Node} the node to append to.
     * @return {Node} The appended node
     */
    appendTo: function(parentNode) {
        return parentNode.appendChild(this);
    },

    /**
    * Inserts this node before this supplied node
    * @method insertBefore
    * @param node {Node} the node to insert this node before
    * @return {Node} the inserted node
    */
    insertBefore: function(node) {
        var p = node.parent;
        if (p) {

            if (this.tree) {
                this.tree.popNode(this);
            }

            var refIndex = node.isChildOf(p);
            p.children.splice(refIndex, 0, this);
            if (node.previousSibling) {
                node.previousSibling.nextSibling = this;
            }
            this.previousSibling = node.previousSibling;
            this.nextSibling = node;
            node.previousSibling = this;

            this.applyParent(p);
        }

        return this;
    },
 
    /**
    * Inserts this node after the supplied node
    * @method insertAfter
    * @param node {Node} the node to insert after
    * @return {Node} the inserted node
    */
    insertAfter: function(node) {
        var p = node.parent;
        if (p) {

            if (this.tree) {
                this.tree.popNode(this);
            }

            var refIndex = node.isChildOf(p);

            if (!node.nextSibling) {
                this.nextSibling = null;
                return this.appendTo(p);
            }

            p.children.splice(refIndex + 1, 0, this);

            node.nextSibling.previousSibling = this;
            this.previousSibling = node;
            this.nextSibling = node.nextSibling;
            node.nextSibling = this;

            this.applyParent(p);
        }

        return this;
    },

    /**
    * Returns true if the Node is a child of supplied Node
    * @method isChildOf
    * @param parentNode {Node} the Node to check
    * @return {boolean} The node index if this Node is a child of 
    *                   supplied Node, else -1.
    * @private
    */
    isChildOf: function(parentNode) {
        if (parentNode && parentNode.children) {
            for (var i=0, len=parentNode.children.length; i<len ; ++i) {
                if (parentNode.children[i] === this) {
                    return i;
                }
            }
        }

        return -1;
    },

    /**
     * Returns a node array of this node's siblings, null if none.
     * @method getSiblings
     * @return Node[]
     */
    getSiblings: function() {
		var sib =  this.parent.children.slice(0);
		for (var i=0;i < sib.length && sib[i] != this;i++) {}
		sib.splice(i,1);
		if (sib.length) { return sib; }
		return null;
    },

    /**
     * Shows this node's children
     * @method showChildren
     */
    showChildren: function() {
        if (!this.tree.animateExpand(this.getChildrenEl(), this)) {
            if (this.hasChildren()) {
                this.getChildrenEl().style.display = "";
            }
        }
    },

    /**
     * Hides this node's children
     * @method hideChildren
     */
    hideChildren: function() {

        if (!this.tree.animateCollapse(this.getChildrenEl(), this)) {
            this.getChildrenEl().style.display = "none";
        }
    },

    /**
     * Returns the id for this node's container div
     * @method getElId
     * @return {string} the element id
     */
    getElId: function() {
        return "ygtv" + this.index;
    },

    /**
     * Returns the id for this node's children div
     * @method getChildrenElId
     * @return {string} the element id for this node's children div
     */
    getChildrenElId: function() {
        return "ygtvc" + this.index;
    },

    /**
     * Returns the id for this node's toggle element
     * @method getToggleElId
     * @return {string} the toggel element id
     */
    getToggleElId: function() {
        return "ygtvt" + this.index;
    },


    /*
     * Returns the id for this node's spacer image.  The spacer is positioned
     * over the toggle and provides feedback for screen readers.
     * @method getSpacerId
     * @return {string} the id for the spacer image
     */
    /*
    getSpacerId: function() {
        return "ygtvspacer" + this.index;
    }, 
    */

    /**
     * Returns this node's container html element
     * @method getEl
     * @return {HTMLElement} the container html element
     */
    getEl: function() {
        return Dom.get(this.getElId());
    },

    /**
     * Returns the div that was generated for this node's children
     * @method getChildrenEl
     * @return {HTMLElement} this node's children div
     */
    getChildrenEl: function() {
        return Dom.get(this.getChildrenElId());
    },

    /**
     * Returns the element that is being used for this node's toggle.
     * @method getToggleEl
     * @return {HTMLElement} this node's toggle html element
     */
    getToggleEl: function() {
        return Dom.get(this.getToggleElId());
    },
    /**
	* Returns the outer html element for this node's content
	* @method getContentEl
	* @return {HTMLElement} the element
	*/
    getContentEl: function() { 
        return Dom.get(this.contentElId);
    },


    /*
     * Returns the element that is being used for this node's spacer.
     * @method getSpacer
     * @return {HTMLElement} this node's spacer html element
     */
    /*
    getSpacer: function() {
        return document.getElementById( this.getSpacerId() ) || {};
    },
    */

    /*
    getStateText: function() {
        if (this.isLoading) {
            return this.loadingText;
        } else if (this.hasChildren(true)) {
            if (this.expanded) {
                return this.expandedText;
            } else {
                return this.collapsedText;
            }
        } else {
            return "";
        }
    },
    */

  /**
     * Hides this nodes children (creating them if necessary), changes the toggle style.
     * @method collapse
     */
    collapse: function() {
        // Only collapse if currently expanded
        if (!this.expanded) { return; }

        // fire the collapse event handler
        var ret = this.tree.onCollapse(this);

        if (false === ret) {
            return;
        }

        ret = this.tree.fireEvent("collapse", this);

        if (false === ret) {
            return;
        }


        if (!this.getEl()) {
            this.expanded = false;
        } else {
            // hide the child div
            this.hideChildren();
            this.expanded = false;

            this.updateIcon();
        }

        // this.getSpacer().title = this.getStateText();

        ret = this.tree.fireEvent("collapseComplete", this);

    },

    /**
     * Shows this nodes children (creating them if necessary), changes the
     * toggle style, and collapses its siblings if multiExpand is not set.
     * @method expand
     */
    expand: function(lazySource) {
        // Only expand if currently collapsed.
        if (this.expanded && !lazySource) { 
            return; 
        }

        var ret = true;

        // When returning from the lazy load handler, expand is called again
        // in order to render the new children.  The "expand" event already
        // fired before fething the new data, so we need to skip it now.
        if (!lazySource) {
            // fire the expand event handler
            ret = this.tree.onExpand(this);

            if (false === ret) {
                return;
            }
            
            ret = this.tree.fireEvent("expand", this);
        }

        if (false === ret) {
            return;
        }

        if (!this.getEl()) {
            this.expanded = true;
            return;
        }

        if (!this.childrenRendered) {
            this.getChildrenEl().innerHTML = this.renderChildren();
        } else {
        }

        this.expanded = true;

        this.updateIcon();

        // this.getSpacer().title = this.getStateText();

        // We do an extra check for children here because the lazy
        // load feature can expose nodes that have no children.

        // if (!this.hasChildren()) {
        if (this.isLoading) {
            this.expanded = false;
            return;
        }

        if (! this.multiExpand) {
            var sibs = this.getSiblings();
            for (var i=0; sibs && i<sibs.length; ++i) {
                if (sibs[i] != this && sibs[i].expanded) { 
                    sibs[i].collapse(); 
                }
            }
        }

        this.showChildren();

        ret = this.tree.fireEvent("expandComplete", this);
    },

    updateIcon: function() {
        if (this.hasIcon) {
            var el = this.getToggleEl();
            if (el) {
                el.className = el.className.replace(/ygtv(([tl][pmn]h?)|(loading))/,this.getStyle());
            }
        }
    },

    /**
     * Returns the css style name for the toggle
     * @method getStyle
     * @return {string} the css class for this node's toggle
     */
    getStyle: function() {
        if (this.isLoading) {
            return "ygtvloading";
        } else {
            // location top or bottom, middle nodes also get the top style
            var loc = (this.nextSibling) ? "t" : "l";

            // type p=plus(expand), m=minus(collapase), n=none(no children)
            var type = "n";
            if (this.hasChildren(true) || (this.isDynamic() && !this.getIconMode())) {
            // if (this.hasChildren(true)) {
                type = (this.expanded) ? "m" : "p";
            }

            return "ygtv" + loc + type;
        }
    },

    /**
     * Returns the hover style for the icon
     * @return {string} the css class hover state
     * @method getHoverStyle
     */
    getHoverStyle: function() { 
        var s = this.getStyle();
        if (this.hasChildren(true) && !this.isLoading) { 
            s += "h"; 
        }
        return s;
    },

    /**
     * Recursively expands all of this node's children.
     * @method expandAll
     */
    expandAll: function() { 
        for (var i=0;i<this.children.length;++i) {
            var c = this.children[i];
            if (c.isDynamic()) {
                break;
            } else if (! c.multiExpand) {
                break;
            } else {
                c.expand();
                c.expandAll();
            }
        }
    },

    /**
     * Recursively collapses all of this node's children.
     * @method collapseAll
     */
    collapseAll: function() { 
        for (var i=0;i<this.children.length;++i) {
            this.children[i].collapse();
            this.children[i].collapseAll();
        }
    },

    /**
     * Configures this node for dynamically obtaining the child data
     * when the node is first expanded.  Calling it without the callback
     * will turn off dynamic load for the node.
     * @method setDynamicLoad
     * @param fmDataLoader {function} the function that will be used to get the data.
     * @param iconMode {int} configures the icon that is displayed when a dynamic
     * load node is expanded the first time without children.  By default, the 
     * "collapse" icon will be used.  If set to 1, the leaf node icon will be
     * displayed.
     */
    setDynamicLoad: function(fnDataLoader, iconMode) { 
        if (fnDataLoader) {
            this.dataLoader = fnDataLoader;
            this._dynLoad = true;
        } else {
            this.dataLoader = null;
            this._dynLoad = false;
        }

        if (iconMode) {
            this.iconMode = iconMode;
        }
    },

    /**
     * Evaluates if this node is the root node of the tree
     * @method isRoot
     * @return {boolean} true if this is the root node
     */
    isRoot: function() { 
        return (this == this.tree.root);
    },

    /**
     * Evaluates if this node's children should be loaded dynamically.  Looks for
     * the property both in this instance and the root node.  If the tree is
     * defined to load all children dynamically, the data callback function is
     * defined in the root node
     * @method isDynamic
     * @return {boolean} true if this node's children are to be loaded dynamically
     */
    isDynamic: function() { 
        if (this.isLeaf) {
            return false;
        } else {
            return (!this.isRoot() && (this._dynLoad || this.tree.root._dynLoad));
            // return lazy;
        }
    },

    /**
     * Returns the current icon mode.  This refers to the way childless dynamic
     * load nodes appear (this comes into play only after the initial dynamic
     * load request produced no children).
     * @method getIconMode
     * @return {int} 0 for collapse style, 1 for leaf node style
     */
    getIconMode: function() {
        return (this.iconMode || this.tree.root.iconMode);
    },

    /**
     * Checks if this node has children.  If this node is lazy-loading and the
     * children have not been rendered, we do not know whether or not there
     * are actual children.  In most cases, we need to assume that there are
     * children (for instance, the toggle needs to show the expandable 
     * presentation state).  In other times we want to know if there are rendered
     * children.  For the latter, "checkForLazyLoad" should be false.
     * @method hasChildren
     * @param checkForLazyLoad {boolean} should we check for unloaded children?
     * @return {boolean} true if this has children or if it might and we are
     * checking for this condition.
     */
    hasChildren: function(checkForLazyLoad) { 
        if (this.isLeaf) {
            return false;
        } else {
            return ( this.children.length > 0 || 
(checkForLazyLoad && this.isDynamic() && !this.dynamicLoadComplete) );
        }
    },

    /**
     * Expands if node is collapsed, collapses otherwise.
     * @method toggle
     */
    toggle: function() {
        if (!this.tree.locked && ( this.hasChildren(true) || this.isDynamic()) ) {
            if (this.expanded) { this.collapse(); } else { this.expand(); }
        }
    },

    /**
     * Returns the markup for this node and its children.
     * @method getHtml
     * @return {string} the markup for this node and its expanded children.
     */
    getHtml: function() {

        this.childrenRendered = false;

        var sb = [];
        sb[sb.length] = '<div class="ygtvitem" id="' + this.getElId() + '">';
        sb[sb.length] = this.getNodeHtml();
        sb[sb.length] = this.getChildrenHtml();
        sb[sb.length] = '</div>';
        return sb.join("");
    },

    /**
     * Called when first rendering the tree.  We always build the div that will
     * contain this nodes children, but we don't render the children themselves
     * unless this node is expanded.
     * @method getChildrenHtml
     * @return {string} the children container div html and any expanded children
     * @private
     */
    getChildrenHtml: function() {


        var sb = [];
        sb[sb.length] = '<div class="ygtvchildren"';
        sb[sb.length] = ' id="' + this.getChildrenElId() + '"';

        // This is a workaround for an IE rendering issue, the child div has layout
        // in IE, creating extra space if a leaf node is created with the expanded
        // property set to true.
        if (!this.expanded || !this.hasChildren()) {
            sb[sb.length] = ' style="display:none;"';
        }
        sb[sb.length] = '>';


        // Don't render the actual child node HTML unless this node is expanded.
        if ( (this.hasChildren(true) && this.expanded) ||
                (this.renderHidden && !this.isDynamic()) ) {
            sb[sb.length] = this.renderChildren();
        }

        sb[sb.length] = '</div>';

        return sb.join("");
    },

    /**
     * Generates the markup for the child nodes.  This is not done until the node
     * is expanded.
     * @method renderChildren
     * @return {string} the html for this node's children
     * @private
     */
    renderChildren: function() {


        var node = this;

        if (this.isDynamic() && !this.dynamicLoadComplete) {
            this.isLoading = true;
            this.tree.locked = true;

            if (this.dataLoader) {

                setTimeout( 
                    function() {
                        node.dataLoader(node, 
                            function() { 
                                node.loadComplete(); 
                            });
                    }, 10);
                
            } else if (this.tree.root.dataLoader) {

                setTimeout( 
                    function() {
                        node.tree.root.dataLoader(node, 
                            function() { 
                                node.loadComplete(); 
                            });
                    }, 10);

            } else {
                return "Error: data loader not found or not specified.";
            }

            return "";

        } else {
            return this.completeRender();
        }
    },

    /**
     * Called when we know we have all the child data.
     * @method completeRender
     * @return {string} children html
     */
    completeRender: function() {
        var sb = [];

        for (var i=0; i < this.children.length; ++i) {
            // this.children[i].childrenRendered = false;
            sb[sb.length] = this.children[i].getHtml();
        }
        
        this.childrenRendered = true;

        return sb.join("");
    },

    /**
     * Load complete is the callback function we pass to the data provider
     * in dynamic load situations.
     * @method loadComplete
     */
    loadComplete: function() {
        this.getChildrenEl().innerHTML = this.completeRender();
        this.dynamicLoadComplete = true;
        this.isLoading = false;
        this.expand(true);
        this.tree.locked = false;
    },

    /**
     * Returns this node's ancestor at the specified depth.
     * @method getAncestor
     * @param {int} depth the depth of the ancestor.
     * @return {Node} the ancestor
     */
    getAncestor: function(depth) {
        if (depth >= this.depth || depth < 0)  {
            return null;
        }

        var p = this.parent;
        
        while (p.depth > depth) {
            p = p.parent;
        }

        return p;
    },

    /**
     * Returns the css class for the spacer at the specified depth for
     * this node.  If this node's ancestor at the specified depth
     * has a next sibling the presentation is different than if it
     * does not have a next sibling
     * @method getDepthStyle
     * @param {int} depth the depth of the ancestor.
     * @return {string} the css class for the spacer
     */
    getDepthStyle: function(depth) {
        return (this.getAncestor(depth).nextSibling) ? 
            "ygtvdepthcell" : "ygtvblankdepthcell";
    },

    /**
     * Get the markup for the node.  This may be overrided so that we can
     * support different types of nodes.
     * @method getNodeHtml
     * @return {string} The HTML that will render this node.
     */
    getNodeHtml: function() { 
        var sb = [];

        sb[sb.length] = '<table border="0" cellpadding="0" cellspacing="0" class="ygtvdepth' + this.depth + '">';
        sb[sb.length] = '<tr class="ygtvrow">';
        
        for (var i=0;i<this.depth;++i) {
            sb[sb.length] = '<td class="' + this.getDepthStyle(i) + '"><div class="ygtvspacer"></div></td>';
        }

        if (this.hasIcon) {
            sb[sb.length] = '<td'; 
            sb[sb.length] = ' id="' + this.getToggleElId() + '"';
            sb[sb.length] = ' class="' + this.getStyle() + '"';
            sb[sb.length] = '><a href="#" class="ygtvspacer">&nbsp;</a></td>';
        }

        sb[sb.length] = '<td';
        sb[sb.length] = ' id="' + this.contentElId + '"'; 
        sb[sb.length] = ' class="' + this.contentStyle  + ' ygtvcontent" ';
        sb[sb.length] = (this.nowrap) ? ' nowrap="nowrap" ' : '';
        sb[sb.length] = ' >';
		sb[sb.length] = this.getContentHtml();
        sb[sb.length] = '</td>';
        sb[sb.length] = '</tr>';
        sb[sb.length] = '</table>';

        return sb.join("");

    },
	/**
     * Get the markup for the contents of the node.  This is designed to be overrided so that we can
     * support different types of nodes.
     * @method getContentHtml
     * @return {string} The HTML that will render the content of this node.
     */
	getContentHtml: function () {
		return "";
	},

    /**
     * Regenerates the html for this node and its children.  To be used when the
     * node is expanded and new children have been added.
     * @method refresh
     */
    refresh: function() {
        // this.loadComplete();
        this.getChildrenEl().innerHTML = this.completeRender();

        if (this.hasIcon) {
            var el = this.getToggleEl();
            if (el) {
                el.className = this.getStyle();
            }
        }
    },

    /**
     * Node toString
     * @method toString
     * @return {string} string representation of the node
     */
    toString: function() {
        return this._type + " (" + this.index + ")";
    },
	/**
	* array of items that had the focus set on them
	* so that they can be cleaned when focus is lost
	* @property _focusHighlightedItems
	* @type Array of DOM elements
	* @private
	*/
	_focusHighlightedItems: [],
	_focusedItem: null,
	/**
	* Sets the focus on the node element.
	* It will only be able to set the focus on nodes that have anchor elements in it.  
	* Toggle or branch icons have anchors and can be focused on.  
	* If will fail in nodes that have no anchor
	* @method focus
	* @return {boolean} success
	*/
	focus: function () {
		var focused = false, self = this;

		var removeListeners = function () {
			var el;
			if (self._focusedItem) {
				Event.removeListener(self._focusedItem,'blur');
				self._focusedItem = null;
			}
			
			while ((el = self._focusHighlightedItems.shift())) {  // yes, it is meant as an assignment, really
				Dom.removeClass(el,YAHOO.widget.TreeView.FOCUS_CLASS_NAME );
			}
		};
		removeListeners();

		Dom.getElementsBy  ( 
			function (el) {
				return /ygtv(([tl][pmn]h?)|(content))/.test(el.className);
			} ,
			'td' , 
			this.getEl().firstChild , 
			function (el) {
				Dom.addClass(el, YAHOO.widget.TreeView.FOCUS_CLASS_NAME );
				if (!focused) { 
					var aEl = el.getElementsByTagName('a');
					if (aEl.length) {
						aEl = aEl[0];
						aEl.focus();
						self._focusedItem = aEl;
						Event.on(aEl,'blur',removeListeners);
						focused = true;
					}
				}
				self._focusHighlightedItems.push(el);
			}
		);
		if (!focused) { removeListeners(); }
		return focused;
	},

  /**
     * Count of nodes in tree
     * @method getNodeCount
     * @return {int} number of nodes in the tree
     */
    getNodeCount: function() {
		for (var i = 0, count = 0;i< this.children.length;i++) {
			count += this.children[i].getNodeCount();
		}
        return count + 1;
    },
	
	  /**
     * Returns an object which could be used to build a tree out of this node and its children.
     * It can be passed to the tree constructor to reproduce this node as a tree.
     * It will return false if the node or any children loads dynamically, regardless of whether it is loaded or not.
     * @method getNodeDefinition
     * @return {Object | false}  definition of the tree or false if the node or any children is defined as dynamic
     */
    getNodeDefinition: function() {
	
		if (this.isDynamic()) { return false; }
		
		var def, defs = this.data, children = []; 
		
		
		if (this.href) { defs.href = this.href; }
		if (this.target != '_self') { defs.target = this.target; }
		if (this.expanded) {defs.expanded = this.expanded; }
		if (!this.multiExpand) { defs.multiExpand = this.multiExpand; }
		if (!this.hasIcon) { defs.hasIcon = this.hasIcon; }
		if (this.nowrap) { defs.nowrap = this.nowrap; }
		defs.type = this._type;
		
		
		
		for (var i = 0; i < this.children.length;i++) {
			def = this.children[i].getNodeDefinition();
			if (def === false) { return false;}
			children.push(def);
		}
		if (children.length) { defs.children = children; }
		return defs;
    },


    /**
     * Generates the link that will invoke this node's toggle method
     * @method getToggleLink
     * @return {string} the javascript url for toggling this node
     */
    getToggleLink: function() {
        return 'return false;';
    }

};

YAHOO.augment(YAHOO.widget.Node, YAHOO.util.EventProvider);
})();
(function () {
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event;
/**
 * The default node presentation.  The first parameter should be
 * either a string that will be used as the node's label, or an object
 * that has at least a string property called label.  By default,  clicking the
 * label will toggle the expanded/collapsed state of the node.  By
 * setting the href property of the instance, this behavior can be
 * changed so that the label will go to the specified href.
 * @namespace YAHOO.widget
 * @class TextNode
 * @extends YAHOO.widget.Node
 * @constructor
 * @param oData {object} a string or object containing the data that will
 * be used to render this node.
 * Providing a string is the same as providing an object with a single property named label.
 * All values in the oData will be used to set equally named properties in the node
 * as long as the node does have such properties, they are not undefined, private or functions.
 * All attributes are made available in noderef.data, which
 * can be used to store custom attributes.  TreeView.getNode(s)ByProperty
 * can be used to retrieve a node by one of the attributes.
 * @param oParent {YAHOO.widget.Node} this node's parent node
 * @param expanded {boolean} the initial expanded/collapsed state (deprecated; use oData.expanded) 
 */
YAHOO.widget.TextNode = function(oData, oParent, expanded) {

    if (oData) { 
        if (Lang.isString(oData)) {
            oData = { label: oData };
        }
        this.init(oData, oParent, expanded);
        this.setUpLabel(oData);
    }

};

YAHOO.extend(YAHOO.widget.TextNode, YAHOO.widget.Node, {
    
    /**
     * The CSS class for the label href.  Defaults to ygtvlabel, but can be
     * overridden to provide a custom presentation for a specific node.
     * @property labelStyle
     * @type string
     */
    labelStyle: "ygtvlabel",

    /**
     * The derived element id of the label for this node
     * @property labelElId
     * @type string
     */
    labelElId: null,

    /**
     * The text for the label.  It is assumed that the oData parameter will
     * either be a string that will be used as the label, or an object that
     * has a property called "label" that we will use.
     * @property label
     * @type string
     */
    label: null,

    /**
     * The text for the title (tooltip) for the label element
     * @property title
     * @type string
     */
    title: null,
	
/**
     * The node type
     * @property _type
     * @private
     * @type string
     * @default "TextNode"
     */
    _type: "TextNode",


    /**
     * Sets up the node label
     * @method setUpLabel
     * @param oData string containing the label, or an object with a label property
     */
    setUpLabel: function(oData) { 
        
        if (Lang.isString(oData)) {
            oData = { 
                label: oData 
            };
        } else {
        if (oData.style) {
            this.labelStyle = oData.style;
        }
        }

        this.label = oData.label;

        this.labelElId = "ygtvlabelel" + this.index;
		
    },

    /**
     * Returns the label element
     * @for YAHOO.widget.TextNode
     * @method getLabelEl
     * @return {object} the element
     */
    getLabelEl: function() { 
        return Dom.get(this.labelElId);
    },

    // overrides YAHOO.widget.Node
	getContentHtml: function() { 
        var sb = [];
        sb[sb.length] = this.href?'<a':'<span';
        sb[sb.length] = ' id="' + this.labelElId + '"';
        if (this.title) {
            sb[sb.length] = ' title="' + this.title + '"';
        }
        sb[sb.length] = ' class="' + this.labelStyle  + '"';
		if (this.href) {
			sb[sb.length] = ' href="' + this.href + '"';
			sb[sb.length] = ' target="' + this.target + '"';
		} 
        sb[sb.length] = ' >';
        sb[sb.length] = this.label;
        sb[sb.length] = this.href?'</a>':'</span>';
        return sb.join("");
    },



  /**
     * Returns an object which could be used to build a tree out of this node and its children.
     * It can be passed to the tree constructor to reproduce this node as a tree.
     * It will return false if the node or any descendant loads dynamically, regardless of whether it is loaded or not.
     * @method getNodeDefinition
     * @return {Object | false}  definition of the tree or false if this node or any descendant is defined as dynamic
     */
    getNodeDefinition: function() {
		var def = YAHOO.widget.TextNode.superclass.getNodeDefinition.call(this);
		if (def === false) { return false; }

		// Node specific properties
		def.label = this.label;
		if (this.labelStyle != 'ygtvlabel') { def.style = this.labelStyle; }
		if (this.title) { def.title = this.title ; }

		return def;
	
	},

    toString: function() { 
        return YAHOO.widget.TextNode.superclass.toString.call(this) + ": " + this.label;
    },

    // deprecated
    onLabelClick: function() {
		return false;
    }
});
})();
/**
 * A custom YAHOO.widget.Node that handles the unique nature of 
 * the virtual, presentationless root node.
 * @namespace YAHOO.widget
 * @class RootNode
 * @extends YAHOO.widget.Node
 * @param oTree {YAHOO.widget.TreeView} The tree instance this node belongs to
 * @constructor
 */
YAHOO.widget.RootNode = function(oTree) {
	// Initialize the node with null params.  The root node is a
	// special case where the node has no presentation.  So we have
	// to alter the standard properties a bit.
	this.init(null, null, true);
	
	/*
	 * For the root node, we get the tree reference from as a param
	 * to the constructor instead of from the parent element.
	 */
	this.tree = oTree;
};

YAHOO.extend(YAHOO.widget.RootNode, YAHOO.widget.Node, {
    
   /**
     * The node type
     * @property _type
      * @type string
     * @private
     * @default "RootNode"
     */
    _type: "RootNode",
	
    // overrides YAHOO.widget.Node
    getNodeHtml: function() { 
        return ""; 
    },

    toString: function() { 
        return this._type;
    },

    loadComplete: function() { 
        this.tree.draw();
    },
	
   /**
     * Count of nodes in tree.  
    * It overrides Nodes.getNodeCount because the root node should not be counted.
     * @method getNodeCount
     * @return {int} number of nodes in the tree
     */
    getNodeCount: function() {
		for (var i = 0, count = 0;i< this.children.length;i++) {
			count += this.children[i].getNodeCount();
		}
        return count;
    },

  /**
     * Returns an object which could be used to build a tree out of this node and its children.
     * It can be passed to the tree constructor to reproduce this node as a tree.
     * Since the RootNode is automatically created by treeView, 
     * its own definition is excluded from the returned node definition
     * which only contains its children.
     * @method getNodeDefinition
     * @return {Object | false}  definition of the tree or false if any child node is defined as dynamic
     */
    getNodeDefinition: function() {
		
		for (var def, defs = [], i = 0; i < this.children.length;i++) {
			def = this.children[i].getNodeDefinition();
			if (def === false) { return false;}
			defs.push(def);
		}
		return defs;
    },

    collapse: function() {},
    expand: function() {},
	getSiblings: function() { return null; },
	focus: function () {}

});
(function () {
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event;

/**
 * This implementation takes either a string or object for the
 * oData argument.  If is it a string, it will use it for the display
 * of this node (and it can contain any html code).  If the parameter
 * is an object,it looks for a parameter called "html" that will be
 * used for this node's display.
 * @namespace YAHOO.widget
 * @class HTMLNode
 * @extends YAHOO.widget.Node
 * @constructor
 * @param oData {object} a string or object containing the data that will
 * be used to render this node.  
 * Providing a string is the same as providing an object with a single property named html.
 * All values in the oData will be used to set equally named properties in the node
 * as long as the node does have such properties, they are not undefined, private or functions.
 * All other attributes are made available in noderef.data, which
 * can be used to store custom attributes.  TreeView.getNode(s)ByProperty
 * can be used to retrieve a node by one of the attributes.
 * @param oParent {YAHOO.widget.Node} this node's parent node
 * @param expanded {boolean} the initial expanded/collapsed state (deprecated; use oData.expanded) 
 * @param hasIcon {boolean} specifies whether or not leaf nodes should
 * be rendered with or without a horizontal line line and/or toggle icon. If the icon
 * is not displayed, the content fills the space it would have occupied.
 * This option operates independently of the leaf node presentation logic
 * for dynamic nodes.
 * (deprecated; use oData.hasIcon) 
 */
YAHOO.widget.HTMLNode = function(oData, oParent, expanded, hasIcon) {
    if (oData) { 
        this.init(oData, oParent, expanded);
        this.initContent(oData, hasIcon);
    }
};

YAHOO.extend(YAHOO.widget.HTMLNode, YAHOO.widget.Node, {

    /**
     * The CSS class for the html content container.  Defaults to ygtvhtml, but 
     * can be overridden to provide a custom presentation for a specific node.
     * @property contentStyle
     * @type string
     */
    contentStyle: "ygtvhtml",


    /**
     * The HTML content to use for this node's display
     * @property html
     * @type string
     */
    html: null,
	
/**
     * The node type
     * @property _type
     * @private
     * @type string
     * @default "HTMLNode"
     */
    _type: "HTMLNode",

    /**
     * Sets up the node label
     * @property initContent
     * @param oData {object} An html string or object containing an html property
     * @param hasIcon {boolean} determines if the node will be rendered with an
     * icon or not
     */
    initContent: function(oData, hasIcon) { 
        this.setHtml(oData);
        this.contentElId = "ygtvcontentel" + this.index;
		if (!Lang.isUndefined(hasIcon)) { this.hasIcon  = hasIcon; }
		
    },

    /**
     * Synchronizes the node.data, node.html, and the node's content
     * @property setHtml
     * @param o {object} An html string or object containing an html property
     */
    setHtml: function(o) {

        this.data = o;
        this.html = (typeof o === "string") ? o : o.html;

        var el = this.getContentEl();
        if (el) {
            el.innerHTML = this.html;
        }

    },

    // overrides YAHOO.widget.Node
    getContentHtml: function() { 
        return this.html;
    },
	
	  /**
     * Returns an object which could be used to build a tree out of this node and its children.
     * It can be passed to the tree constructor to reproduce this node as a tree.
     * It will return false if any node loads dynamically, regardless of whether it is loaded or not.
     * @method getNodeDefinition
     * @return {Object | false}  definition of the tree or false if any node is defined as dynamic
     */
    getNodeDefinition: function() {
		var def = YAHOO.widget.HTMLNode.superclass.getNodeDefinition.call(this);
		if (def === false) { return false; }
		def.html = this.html;
		return def;
	
	}
});
})();
/**
 * A menu-specific implementation that differs from TextNode in that only 
 * one sibling can be expanded at a time.
 * @namespace YAHOO.widget
 * @class MenuNode
 * @extends YAHOO.widget.TextNode
 * @param oData {object} a string or object containing the data that will
 * be used to render this node.
 * Providing a string is the same as providing an object with a single property named label.
 * All values in the oData will be used to set equally named properties in the node
 * as long as the node does have such properties, they are not undefined, private or functions.
 * All attributes are made available in noderef.data, which
 * can be used to store custom attributes.  TreeView.getNode(s)ByProperty
 * can be used to retrieve a node by one of the attributes.
 * @param oParent {YAHOO.widget.Node} this node's parent node
 * @param expanded {boolean} the initial expanded/collapsed state (deprecated; use oData.expanded) 
 * @constructor
 */
YAHOO.widget.MenuNode = function(oData, oParent, expanded) {
	YAHOO.widget.MenuNode.superclass.constructor.call(this,oData,oParent,expanded);

   /*
     * Menus usually allow only one branch to be open at a time.
     */
	this.multiExpand = false;

};

YAHOO.extend(YAHOO.widget.MenuNode, YAHOO.widget.TextNode, {

    /**
     * The node type
     * @property _type
     * @private
    * @default "MenuNode"
     */
    _type: "MenuNode"

});
(function () {
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event,
		Calendar = YAHOO.widget.Calendar;
		
/**
 * A Date-specific implementation that differs from TextNode in that it uses 
 * YAHOO.widget.Calendar as an in-line editor, if available
 * If Calendar is not available, it behaves as a plain TextNode.
 * @namespace YAHOO.widget
 * @class DateNode
 * @extends YAHOO.widget.TextNode
 * @param oData {object} a string or object containing the data that will
 * be used to render this node.
 * Providing a string is the same as providing an object with a single property named label.
 * All values in the oData will be used to set equally named properties in the node
 * as long as the node does have such properties, they are not undefined, private nor functions.
 * All attributes are made available in noderef.data, which
 * can be used to store custom attributes.  TreeView.getNode(s)ByProperty
 * can be used to retrieve a node by one of the attributes.
 * @param oParent {YAHOO.widget.Node} this node's parent node
 * @param expanded {boolean} the initial expanded/collapsed state (deprecated; use oData.expanded) 
 * @constructor
 */
YAHOO.widget.DateNode = function(oData, oParent, expanded) {
	YAHOO.widget.DateNode.superclass.constructor.call(this,oData, oParent, expanded);
};

YAHOO.extend(YAHOO.widget.DateNode, YAHOO.widget.TextNode, {

    /**
     * The node type
     * @property _type
     * @type string
     * @private
     * @default  "DateNode"
     */
    _type: "DateNode",
	
	/**
	* Configuration object for the Calendar editor, if used.
	* See <a href="http://developer.yahoo.com/yui/calendar/#internationalization">http://developer.yahoo.com/yui/calendar/#internationalization</a>
	* @property calendarConfig
	*/
	calendarConfig: null,
	
	
	
	/** 
	 *  If YAHOO.widget.Calendar is available, it will pop up a Calendar to enter a new date.  Otherwise, it falls back to a plain &lt;input&gt;  textbox
	 * @method fillEditorContainer
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @return void
	 */
	fillEditorContainer: function (editorData) {
	
		var cal, container = editorData.inputContainer;
		
		if (Lang.isUndefined(Calendar)) {
			Dom.replaceClass(editorData.editorPanel,'ygtv-edit-DateNode','ygtv-edit-TextNode');
			YAHOO.widget.DateNode.superclass.fillEditorContainer.call(this, editorData);
			return;
		}
			
		if (editorData.nodeType != this._type) {
			editorData.nodeType = this._type;
			editorData.saveOnEnter = false;
			
			editorData.node.destroyEditorContents(editorData);

			editorData.inputObject = cal = new Calendar(container.appendChild(document.createElement('div')));
			if (this.calendarConfig) { 
				cal.cfg.applyConfig(this.calendarConfig,true); 
				cal.cfg.fireQueue();
			}
			cal.selectEvent.subscribe(function () {
				this.tree._closeEditor(true);
			},this,true);
		} else {
			cal = editorData.inputObject;
		}

		cal.cfg.setProperty("selected",this.label, false); 

		var delim = cal.cfg.getProperty('DATE_FIELD_DELIMITER');
		var pageDate = this.label.split(delim);
		cal.cfg.setProperty('pagedate',pageDate[cal.cfg.getProperty('MDY_MONTH_POSITION') -1] + delim + pageDate[cal.cfg.getProperty('MDY_YEAR_POSITION') -1]);
		cal.cfg.fireQueue();

		cal.render();
		cal.oDomContainer.focus();
	},
	/**
	* Saves the date entered in the editor into the DateNode label property and displays it.
	* Overrides Node.saveEditorValue
	* @method saveEditorValue
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 */
	saveEditorValue: function (editorData) {
		var node = editorData.node, value;
		if (Lang.isUndefined(Calendar)) {
			value = editorData.inputElement.value;
		} else {
			var cal = editorData.inputObject,
				date = cal.getSelectedDates()[0],
				dd = [];
				
			dd[cal.cfg.getProperty('MDY_DAY_POSITION') -1] = date.getDate();
			dd[cal.cfg.getProperty('MDY_MONTH_POSITION') -1] = date.getMonth() + 1;
			dd[cal.cfg.getProperty('MDY_YEAR_POSITION') -1] = date.getFullYear();
			value = dd.join(cal.cfg.getProperty('DATE_FIELD_DELIMITER'));
		}

		node.label = value;
		node.data.label = value;
		node.getLabelEl().innerHTML = value;
	}

});
})();
(function () {
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang, 
		Event = YAHOO.util.Event,
		TV = YAHOO.widget.TreeView,
		TVproto = TV.prototype;

	/**
	 * An object to store information used for in-line editing
	 * for all Nodes of all TreeViews. It contains:
	 * <ul>
	* <li>active {boolean}, whether there is an active cell editor </li>
	* <li>whoHasIt {YAHOO.widget.TreeView} TreeView instance that is currently using the editor</li>
	* <li>nodeType {string} value of static Node._type property, allows reuse of input element if node is of the same type.</li>
	* <li>editorPanel {HTMLelement (&lt;div&gt;)} element holding the in-line editor</li>
	* <li>inputContainer {HTMLelement (&lt;div&gt;)} element which will hold the type-specific input element(s) to be filled by the fillEditorContainer method</li>
	* <li>buttonsContainer {HTMLelement (&lt;div&gt;)} element which holds the &lt;button&gt; elements for Ok/Cancel.  If you don't want any of the buttons, hide it via CSS styles, don't destroy it</li>
	* <li>node {YAHOO.widget.Node} reference to the Node being edited</li>
	* <li>saveOnEnter {boolean}, whether the Enter key should be accepted as a Save command (Esc. is always taken as Cancel), disable for multi-line input elements </li>
	* </ul>
	*  Editors are free to use this object to store additional data.
	 * @property editorData
	 * @static
	 * @for YAHOO.widget.TreeView
	 */
	TV.editorData = {
		active:false,
		whoHasIt:null, // which TreeView has it
		nodeType:null,
		editorPanel:null,
		inputContainer:null,
		buttonsContainer:null,
		node:null, // which Node is being edited
		saveOnEnter:true
		// Each node type is free to add its own properties to this as it sees fit.
	};
	
	/**
	* Entry point of the editing plug-in.  
	* TreeView will call this method if it exists when a node label is clicked
	* @method _nodeEditing
	* @param node {YAHOO.widget.Node} the node to be edited
	* @return {Boolean} true to indicate that the node is editable and prevent any further bubbling of the click.
	 * @for YAHOO.widget.TreeView
	*/
	
	
	TVproto._nodeEditing = function (node) {
		if (node.fillEditorContainer && node.editable) {
			var ed, topLeft, buttons, button, editorData = TV.editorData;
			editorData.active = true;
			editorData.whoHasIt = this;
			if (!editorData.nodeType) {
				editorData.editorPanel = ed = document.body.appendChild(document.createElement('div'));
				Dom.addClass(ed,'ygtv-label-editor');

				buttons = editorData.buttonsContainer = ed.appendChild(document.createElement('div'));
				Dom.addClass(buttons,'ygtv-button-container');
				button = buttons.appendChild(document.createElement('button'));
				Dom.addClass(button,'ygtvok');
				button.innerHTML = ' ';
				button = buttons.appendChild(document.createElement('button'));
				Dom.addClass(button,'ygtvcancel');
				button.innerHTML = ' ';
				Event.on(buttons, 'click', function (ev) {
					var target = Event.getTarget(ev);
					var node = TV.editorData.node;
					if (Dom.hasClass(target,'ygtvok')) {
						Event.stopEvent(ev);
						this._closeEditor(true);
					}
					if (Dom.hasClass(target,'ygtvcancel')) {
						Event.stopEvent(ev);
						this._closeEditor(false);
					}
				}, this, true);

				editorData.inputContainer = ed.appendChild(document.createElement('div'));
				Dom.addClass(editorData.inputContainer,'ygtv-input');
				
				Event.on(ed,'keydown',function (ev) {
					var editorData = TV.editorData,
						KEY = YAHOO.util.KeyListener.KEY;
					switch (ev.keyCode) {
						case KEY.ENTER:
							Event.stopEvent(ev);
							if (editorData.saveOnEnter) { 
								this._closeEditor(true);
							}
							break;
						case KEY.ESCAPE:
							Event.stopEvent(ev);
							this._closeEditor(false);
							break;
					}
				},this,true);


				
			} else {
				ed = editorData.editorPanel;
			}
			editorData.node = node;
			if (editorData.nodeType) {
				Dom.removeClass(ed,'ygtv-edit-' + editorData.nodeType);
			}
			Dom.addClass(ed,' ygtv-edit-' + node._type);
			topLeft = Dom.getXY(node.getContentEl());
			Dom.setStyle(ed,'left',topLeft[0] + 'px');
			Dom.setStyle(ed,'top',topLeft[1] + 'px');
			Dom.setStyle(ed,'display','block');
			ed.focus();
			node.fillEditorContainer(editorData);

			return true;  // If inline editor available, don't do anything else.
		}
	};
	
	/**
	* Method to be associated with an event (clickEvent, dblClickEvent or enterKeyPressed) to pop up the contents editor
	*  It calls the corresponding node editNode method.
	* @method onEventEditNode
	* @param oArgs {object} Object passed as arguments to TreeView event listeners
	 * @for YAHOO.widget.TreeView
	*/

	TVproto.onEventEditNode = function (oArgs) {
		if (oArgs instanceof YAHOO.widget.Node) {
			oArgs.editNode();
		} else if (oArgs.node instanceof YAHOO.widget.Node) {
			oArgs.node.editNode();
		}
	};
	
	/**
	* Method to be called when the inline editing is finished and the editor is to be closed
	* @method _closeEditor
	* @param save {Boolean} true if the edited value is to be saved, false if discarded
	* @private
	 * @for YAHOO.widget.TreeView
	*/
	
	TVproto._closeEditor = function (save) {
		var ed = TV.editorData, 
			node = ed.node;
		if (save) { 
			ed.node.saveEditorValue(ed); 
		}
		Dom.setStyle(ed.editorPanel,'display','none');	
		ed.active = false;
		node.focus();
	};
	
	/**
	*  Entry point for TreeView's destroy method to destroy whatever the editing plug-in has created
	* @method _destroyEditor
	* @private
	 * @for YAHOO.widget.TreeView
	*/
	TVproto._destroyEditor = function() {
		var ed = TV.editorData;
		if (ed && ed.nodeType && (!ed.active || ed.whoHasIt === this)) {
			Event.removeListener(ed.editorPanel,'keydown');
			Event.removeListener(ed.buttonContainer,'click');
			ed.node.destroyEditorContents(ed);
			document.body.removeChild(ed.editorPanel);
			ed.nodeType = ed.editorPanel = ed.inputContainer = ed.buttonsContainer = ed.whoHasIt = ed.node = null;
			ed.active = false;
		}
	};
	
	var Nproto = YAHOO.widget.Node.prototype;
	
	/**
	* Signals if the label is editable.  (Ignored on TextNodes with href set.)
	* @property editable
	* @type boolean
         * @for YAHOO.widget.Node
	*/
	Nproto.editable = false;
	
	/**
	* pops up the contents editor, if there is one and the node is declared editable
	* @method editNode
	 * @for YAHOO.widget.Node
	*/
	
	Nproto.editNode = function () {
		this.tree._nodeEditing(this);
	};
	
	


	/** Placeholder for a function that should provide the inline node label editor.
	 *   Leaving it set to null will indicate that this node type is not editable.
	 * It should be overridden by nodes that provide inline editing.
	 *  The Node-specific editing element (input box, textarea or whatever) should be inserted into editorData.inputContainer.
	 * @method fillEditorContainer
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @return void
	 * @for YAHOO.widget.Node
	 */
	Nproto.fillEditorContainer = null;

	
	/**
	* Node-specific destroy function to empty the contents of the inline editor panel
	* This function is the worst case alternative that will purge all possible events and remove the editor contents
	* Method Event.purgeElement is somewhat costly so if it can be replaced by specifc Event.removeListeners, it is better to do so.
	* @method destroyEditorContents
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @for YAHOO.widget.Node
	 */
	Nproto.destroyEditorContents = function (editorData) {
		// In the worst case, if the input editor (such as the Calendar) has no destroy method
		// we can only try to remove all possible events on it.
		Event.purgeElement(editorData.inputContainer,true);
		editorData.inputContainer.innerHTML = '';
	};

	/**
	* Saves the value entered into the editor.
	* Should be overridden by each node type
	* @method saveEditorValue
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @for YAHOO.widget.Node
	 */
	Nproto.saveEditorValue = function (editorData) {
	};
	
	var TNproto = YAHOO.widget.TextNode.prototype;
	


	/** 
	 *  Places an &lt;input&gt;  textbox in the input container and loads the label text into it
	 * @method fillEditorContainer
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @return void
	 * @for YAHOO.widget.TextNode
	 */
	TNproto.fillEditorContainer = function (editorData) {
	
		var input;
		// If last node edited is not of the same type as this one, delete it and fill it with our editor
		if (editorData.nodeType != this._type) {
			editorData.nodeType = this._type;
			editorData.saveOnEnter = true;
			editorData.node.destroyEditorContents(editorData);

			editorData.inputElement = input = editorData.inputContainer.appendChild(document.createElement('input'));
			
		} else {
			// if the last node edited was of the same time, reuse the input element.
			input = editorData.inputElement;
		}

		input.value = this.label;
		input.focus();
		input.select();
	};
	
	/**
	* Saves the value entered in the editor into the TextNode label property and displays it
	* Overrides Node.saveEditorValue
	* @method saveEditorValue
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @for YAHOO.widget.TextNode
	 */
	TNproto.saveEditorValue = function (editorData) {
		var node = editorData.node, value = editorData.inputElement.value;
		node.label = value;
		node.data.label = value;
		node.getLabelEl().innerHTML = value;
	};

	/**
	* Destroys the contents of the inline editor panel
	* Overrides Node.destroyEditorContent
	* Since we didn't set any event listeners on this inline editor, it is more efficient to avoid the generic method in Node
	* @method destroyEditorContents
	 * @param editorData {YAHOO.widget.TreeView.editorData}  a shortcut to the static object holding editing information
	 * @for YAHOO.widget.TextNode
	 */
	TNproto.destroyEditorContents = function (editorData) {
		editorData.inputContainer.innerHTML = '';
	};
})();
/**
 * A static factory class for tree view expand/collapse animations
 * @class TVAnim
 * @static
 */
YAHOO.widget.TVAnim = function() {
    return {
        /**
         * Constant for the fade in animation
         * @property FADE_IN
         * @type string
         * @static
         */
        FADE_IN: "TVFadeIn",

        /**
         * Constant for the fade out animation
         * @property FADE_OUT
         * @type string
         * @static
         */
        FADE_OUT: "TVFadeOut",

        /**
         * Returns a ygAnim instance of the given type
         * @method getAnim
         * @param type {string} the type of animation
         * @param el {HTMLElement} the element to element (probably the children div)
         * @param callback {function} function to invoke when the animation is done.
         * @return {YAHOO.util.Animation} the animation instance
         * @static
         */
        getAnim: function(type, el, callback) {
            if (YAHOO.widget[type]) {
                return new YAHOO.widget[type](el, callback);
            } else {
                return null;
            }
        },

        /**
         * Returns true if the specified animation class is available
         * @method isValid
         * @param type {string} the type of animation
         * @return {boolean} true if valid, false if not
         * @static
         */
        isValid: function(type) {
            return (YAHOO.widget[type]);
        }
    };
} ();
/**
 * A 1/2 second fade-in animation.
 * @class TVFadeIn
 * @constructor
 * @param el {HTMLElement} the element to animate
 * @param callback {function} function to invoke when the animation is finished
 */
YAHOO.widget.TVFadeIn = function(el, callback) {
    /**
     * The element to animate
     * @property el
     * @type HTMLElement
     */
    this.el = el;

    /**
     * the callback to invoke when the animation is complete
     * @property callback
     * @type function
     */
    this.callback = callback;

};

YAHOO.widget.TVFadeIn.prototype = {
    /**
     * Performs the animation
     * @method animate
     */
    animate: function() {
        var tvanim = this;

        var s = this.el.style;
        s.opacity = 0.1;
        s.filter = "alpha(opacity=10)";
        s.display = "";

        var dur = 0.4; 
        var a = new YAHOO.util.Anim(this.el, {opacity: {from: 0.1, to: 1, unit:""}}, dur);
        a.onComplete.subscribe( function() { tvanim.onComplete(); } );
        a.animate();
    },

    /**
     * Clean up and invoke callback
     * @method onComplete
     */
    onComplete: function() {
        this.callback();
    },

    /**
     * toString
     * @method toString
     * @return {string} the string representation of the instance
     */
    toString: function() {
        return "TVFadeIn";
    }
};
/**
 * A 1/2 second fade out animation.
 * @class TVFadeOut
 * @constructor
 * @param el {HTMLElement} the element to animate
 * @param callback {Function} function to invoke when the animation is finished
 */
YAHOO.widget.TVFadeOut = function(el, callback) {
    /**
     * The element to animate
     * @property el
     * @type HTMLElement
     */
    this.el = el;

    /**
     * the callback to invoke when the animation is complete
     * @property callback
     * @type function
     */
    this.callback = callback;

};

YAHOO.widget.TVFadeOut.prototype = {
    /**
     * Performs the animation
     * @method animate
     */
    animate: function() {
        var tvanim = this;
        var dur = 0.4;
        var a = new YAHOO.util.Anim(this.el, {opacity: {from: 1, to: 0.1, unit:""}}, dur);
        a.onComplete.subscribe( function() { tvanim.onComplete(); } );
        a.animate();
    },

    /**
     * Clean up and invoke callback
     * @method onComplete
     */
    onComplete: function() {
        var s = this.el.style;
        s.display = "none";
        // s.opacity = 1;
        s.filter = "alpha(opacity=100)";
        this.callback();
    },

    /**
     * toString
     * @method toString
     * @return {string} the string representation of the instance
     */
    toString: function() {
        return "TVFadeOut";
    }
};
YAHOO.register("treeview", YAHOO.widget.TreeView, {version: "2.6.0", build: "1321"});
