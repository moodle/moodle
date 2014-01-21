YUI.add('gallery-sm-treeview', function (Y, NAME) {

var Micro = Y.Template.Micro;

Y.namespace('TreeView').Templates = {
    children: Micro.compile(
        '<ul class="<%= data.classNames.children %>" ' +

            '<% if (data.node.isRoot()) { %>' +
                'role="tree" tabindex="0"' +
            '<% } else { %>' +
                'role="group"' +
            '<% } %>' +

        '></ul>'
    ),

    node: Micro.compile(
        '<li id="<%= data.node.id %>" class="<%= data.nodeClassNames.join(" ") %>" role="treeitem" aria-labelled-by="<%= data.node.id %>-label">' +
            '<div class="<%= data.classNames.row %>" data-node-id="<%= data.node.id %>">' +
                '<span class="<%= data.classNames.indicator %>"><s></s></span>' +
                '<span class="<%= data.classNames.icon %>"></span>' +
                '<span id="<%= data.node.id %>-label" class="<%= data.classNames.label %>"><%== data.node.label %></span>' +
            '</div>' +
        '</li>'
    )
};
/*jshint expr:true, onevar:false */

/**
Provides the `Y.TreeView` widget.

@module gallery-sm-treeview
@main gallery-sm-treeview
**/

/**
TreeView widget.

@class TreeView
@constructor
@extends View
@uses Tree
@uses Tree.Labelable
@uses Tree.Openable
@uses Tree.Selectable
**/

var getClassName = Y.ClassNameManager.getClassName,

TreeView = Y.Base.create('treeView', Y.View, [
    Y.Tree,
    Y.Tree.Labelable,
    Y.Tree.Openable,
    Y.Tree.Selectable
], {
    // -- Public Properties ----------------------------------------------------

    /**
    CSS class names used by this treeview.

    @property {Object} classNames
    @param {String} canHaveChildren Class name indicating that a tree node can
        contain child nodes (whether or not it actually does).
    @param {String} children Class name for a list of child nodes.
    @param {String} hasChildren Class name indicating that a tree node has one
        or more child nodes.
    @param {String} icon Class name for a tree node's icon.
    @param {String} indicator Class name for an open/closed indicator.
    @param {String} label Class name for a tree node's user-visible label.
    @param {String} node Class name for a tree node item.
    @param {String} noTouch Class name added to the TreeView container when not
        using a touchscreen device.
    @param {String} open Class name indicating that a tree node is open.
    @param {String} row Class name for a row container encompassing the
        indicator and label within a tree node.
    @param {String} selected Class name for a tree node that's selected.
    @param {String} touch Class name added to the TreeView container when using
        a touchscreen device.
    @param {String} treeview Class name for the TreeView container.
    **/
    classNames: {
        canHaveChildren: getClassName('treeview-can-have-children'),
        children       : getClassName('treeview-children'),
        hasChildren    : getClassName('treeview-has-children'),
        icon           : getClassName('treeview-icon'),
        indicator      : getClassName('treeview-indicator'),
        label          : getClassName('treeview-label'),
        node           : getClassName('treeview-node'),
        noTouch        : getClassName('treeview-notouch'),
        open           : getClassName('treeview-open'),
        row            : getClassName('treeview-row'),
        selected       : getClassName('treeview-selected'),
        touch          : getClassName('treeview-touch'),
        treeview       : getClassName('treeview')
    },

    /**
    Whether or not this TreeView has been rendered.

    @property {Boolean} rendered
    @default false
    **/
    rendered: false,

    /**
    Default templates used to render this TreeView.

    @property {Object} templates
    **/
    templates: Y.TreeView.Templates,

    // -- Protected Properties -------------------------------------------------

    /**
    Simple way to type-check that this is a TreeView instance.

    @property {Boolean} _isYUITreeView
    @default true
    @protected
    **/
    _isYUITreeView: true,

    /**
    Cached value of the `lazyRender` attribute.

    @property {Boolean} _lazyRender
    @protected
    **/

    // -- Lifecycle Methods ----------------------------------------------------

    initializer: function (config) {
        if (config && config.templates) {
            this.templates = Y.merge(this.templates, config.templates);
        }

        this._renderQueue = {};
        this._attachTreeViewEvents();
    },

    destructor: function () {
        clearTimeout(this._renderTimeout);
        this._detachTreeViewEvents();

        this._renderQueue = null;
    },

    // -- Public Methods -------------------------------------------------------

    destroyNode: function (node, options) {
        node._htmlNode = null;
        return Y.Tree.prototype.destroyNode.call(this, node, options);
    },

    /**
    Returns the HTML node (as a `Y.Node` instance) associated with the specified
    `Tree.Node` instance, if any.

    @method getHTMLNode
    @param {Tree.Node} treeNode Tree node.
    @return {Node} `Y.Node` instance associated with the given tree node, or
        `undefined` if one was not found.
    **/
    getHTMLNode: function (treeNode) {
        if (!treeNode._htmlNode) {
            treeNode._htmlNode = this.get('container').one('#' + treeNode.id);
        }

        return treeNode._htmlNode;
    },

    /**
    Renders this TreeView into its container.

    If the container hasn't already been added to the current document, it will
    be appended to the `<body>` element.

    @method render
    @chainable
    **/
    render: function () {
        var container     = this.get('container'),
            isTouchDevice = 'ontouchstart' in Y.config.win;

        container.addClass(this.classNames.treeview);
        container.addClass(this.classNames[isTouchDevice ? 'touch' : 'noTouch']);

        this._childrenNode = this.renderChildren(this.rootNode, {
            container: container
        });

        if (!container.inDoc()) {
            Y.one('body').append(container);
        }

        this.rendered = true;

        return this;
    },

    /**
    Renders the children of the specified tree node.

    If a container is specified, it will be assumed to be an existing rendered
    tree node, and the children will be rendered (or re-rendered) inside it.

    @method renderChildren
    @param {Tree.Node} treeNode Tree node whose children should be rendered.
    @param {Object} [options] Options.
        @param {Node} [options.container] `Y.Node` instance of a container into
            which the children should be rendered. If the container already
            contains rendered children, they will be re-rendered in place.
    @return {Node} `Y.Node` instance containing the rendered children.
    **/
    renderChildren: function (treeNode, options) {
        options || (options = {});

        var container    = options.container,
            childrenNode = container && container.one('>.' + this.classNames.children),
            lazyRender   = this._lazyRender;

        if (!childrenNode) {
            childrenNode = Y.Node.create(this.templates.children({
                classNames: this.classNames,
                node      : treeNode,
                treeview  : this // not currently used, but may be useful for custom templates
            }));
        }

        if (treeNode.hasChildren()) {
            childrenNode.set('aria-expanded', treeNode.isOpen());

            for (var i = 0, len = treeNode.children.length; i < len; i++) {
                var child = treeNode.children[i];

                this.renderNode(child, {
                    container     : childrenNode,
                    renderChildren: !lazyRender || child.isOpen()
                });
            }
        }

        // Keep track of whether or not this node's children have been rendered
        // so we'll know whether we need to render them later if the node is
        // opened.
        treeNode.state.renderedChildren = true;

        if (container) {
            container.append(childrenNode);
        }

        return childrenNode;
    },

    /**
    Renders the specified tree node and its children (if any).

    If a container is specified, the rendered node will be appended to it.

    @method renderNode
    @param {Tree.Node} treeNode Tree node to render.
    @param {Object} [options] Options.
        @param {Node} [options.container] `Y.Node` instance of a container to
            which the rendered tree node should be appended.
        @param {Boolean} [options.renderChildren=false] Whether or not to render
            this node's children.
    @return {Node} `Y.Node` instance of the rendered tree node.
    **/
    renderNode: function (treeNode, options) {
        options || (options = {});

        var classNames     = this.classNames,
            hasChildren    = treeNode.hasChildren(),
            htmlNode       = treeNode._htmlNode,
            nodeClassNames = {},
            className;

        // Build the hash of CSS classes for this node.
        nodeClassNames[classNames.node]            = true;
        nodeClassNames[classNames.canHaveChildren] = !!treeNode.canHaveChildren;
        nodeClassNames[classNames.hasChildren]     = hasChildren;

        if (htmlNode) {
            // This node has already been rendered, so we just need to update
            // the DOM instead of re-rendering it from scratch.
            htmlNode.one('.' + classNames.label).setHTML(treeNode.label);

            for (className in nodeClassNames) {
                if (nodeClassNames.hasOwnProperty(className)) {
                    htmlNode.toggleClass(className, nodeClassNames[className]);
                }
            }
        } else {
            // This node hasn't been rendered yet, so render it from scratch.
            var enabledClassNames = [];

            for (className in nodeClassNames) {
                if (nodeClassNames.hasOwnProperty(className) && nodeClassNames[className]) {
                    enabledClassNames.push(className);
                }
            }

            htmlNode = treeNode._htmlNode = Y.Node.create(this.templates.node({
                classNames    : classNames,
                nodeClassNames: enabledClassNames,
                node          : treeNode,
                treeview      : this // not currently used, but may be useful for custom templates
            }));
        }

        this._syncNodeOpenState(treeNode, htmlNode);
        this._syncNodeSelectedState(treeNode, htmlNode);

        if (hasChildren) {
            if (options.renderChildren) {
                this.renderChildren(treeNode, {
                    container: htmlNode
                });
            }
        } else {
            // If children were previously rendered but this node no longer has
            // children, remove the empty child list.
            var childrenNode = htmlNode.one('>.' + classNames.children);

            if (childrenNode) {
                childrenNode.remove(true);
            }
        }

        treeNode.state.rendered = true;

        if (options.container) {
            options.container.append(htmlNode);
        }

        return htmlNode;
    },

    // -- Protected Methods ----------------------------------------------------

    _attachTreeViewEvents: function () {
        this._treeViewEvents || (this._treeViewEvents = []);

        var classNames = this.classNames,
            container  = this.get('container');

        this._treeViewEvents.push(
            // Custom events.
            this.after({
                add              : this._afterAdd,
                clear            : this._afterClear,
                close            : this._afterClose,
                multiSelectChange: this._afterTreeViewMultiSelectChange, // sheesh
                open             : this._afterOpen,
                remove           : this._afterRemove,
                select           : this._afterSelect,
                unselect         : this._afterUnselect
            }),

            // DOM events.
            container.on('mousedown', this._onMouseDown, this),

            container.delegate('click', this._onIndicatorClick,
                '.' + classNames.indicator, this),

            container.delegate('click', this._onRowClick,
                '.' + classNames.row, this),

            container.delegate('dblclick', this._onRowDoubleClick,
                '.' + classNames.canHaveChildren + ' > .' + classNames.row, this)
        );
    },

    _detachTreeViewEvents: function () {
        (new Y.EventHandle(this._treeViewEvents)).detach();
    },

    _processRenderQueue: function () {
        if (!this.rendered) {
            return;
        }

        var queue = this._renderQueue,
            node;

        for (var id in queue) {
            if (queue.hasOwnProperty(id)) {
                node = this.getNodeById(id);

                if (node) {
                    this.renderNode(node, queue[id]);
                }
            }
        }

        this._renderQueue = {};
    },

    _queueRender: function (node, options) {
        if (!this.rendered) {
            return;
        }

        var queue = this._renderQueue,
            self  = this;

        clearTimeout(this._renderTimeout);

        queue[node.id] = Y.merge(queue[node.id], options);

        this._renderTimeout = setTimeout(function () {
            self._processRenderQueue();
        }, 15);

        return this;
    },

    /**
    Setter for the `lazyRender` attribute.

    Just caches the value in a property for faster lookups.

    @method _setLazyRender
    @return {Boolean} Value.
    @protected
    **/
    _setLazyRender: function (value) {
        /*jshint boss:true */
        return this._lazyRender = value;
    },

    _syncNodeOpenState: function (node, htmlNode) {
        htmlNode || (htmlNode = this.getHTMLNode(node));

        if (!htmlNode) {
            return;
        }

        if (node.isOpen()) {
            htmlNode
                .addClass(this.classNames.open)
                .set('aria-expanded', true);
        } else {
            htmlNode
                .removeClass(this.classNames.open)
                .set('aria-expanded', false);
        }
    },

    _syncNodeSelectedState: function (node, htmlNode) {
        htmlNode || (htmlNode = this.getHTMLNode(node));

        if (!htmlNode) {
            return;
        }

        var multiSelect = this.get('multiSelect');

        if (node.isSelected()) {
            htmlNode.addClass(this.classNames.selected);

            if (multiSelect) {
                // It's only necessary to set aria-selected when multi-select is
                // enabled and focus can't be used to track the selection state.
                htmlNode.set('aria-selected', true);
            } else {
                htmlNode.set('tabIndex', 0);
            }
        } else {
            htmlNode
                .removeClass(this.classNames.selected)
                .removeAttribute('tabIndex');

            if (multiSelect) {
                htmlNode.set('aria-selected', false);
            }
        }
    },

    // -- Protected Event Handlers ---------------------------------------------

    _afterAdd: function (e) {
        // Nothing to do if the treeview hasn't been rendered yet.
        if (!this.rendered) {
            return;
        }

        var parent       = e.parent,
            parentIsRoot = parent.isRoot(),
            treeNode     = e.node,

            htmlChildren,
            htmlParent;

        if (parentIsRoot) {
            htmlChildren = this._childrenNode;
        } else {
            htmlParent   = this.getHTMLNode(parent),
            htmlChildren = htmlParent && htmlParent.one('>.' + this.classNames.children);
        }

        if (htmlChildren) {
            // Parent's children have already been rendered. Instead of
            // re-rendering all of them, just render the new node and insert it
            // at the correct position.
            htmlChildren.insert(this.renderNode(treeNode, {
                renderChildren: !this._lazyRender || treeNode.isOpen()
            }), e.index);

            // Schedule the parent node to be re-rendered in order to update its
            // state. This is done asynchronously and throttled in order to
            // avoid re-rendering the parent many times if multiple children are
            // added in quick succession.
            if (!parentIsRoot) {
                this._queueRender(parent);
            }
        } else if (!parentIsRoot) {
            // Either the parent hasn't been rendered yet, or its children
            // haven't been rendered yet. Schedule it to be rendered. This is
            // done asynchronously and throttled in order to avoid re-rendering
            // the parent many times if multiple children are added in quick
            // succession.
            this._queueRender(parent, {renderChildren: true});
        }
    },

    _afterClear: function () {
        // Nothing to do if the treeview hasn't been rendered yet.
        if (!this.rendered) {
            return;
        }

        clearTimeout(this._renderTimeout);
        this._renderQueue = {};

        delete this._childrenNode;
        this.rendered = false;

        this.get('container').empty();
        this.render();
    },

    _afterClose: function (e) {
        if (this.rendered) {
            this._syncNodeOpenState(e.node);
        }
    },

    _afterOpen: function (e) {
        if (!this.rendered) {
            return;
        }

        var treeNode = e.node,
            htmlNode = this.getHTMLNode(treeNode);

        // If this node's children haven't been rendered yet, render them.
        if (!treeNode.state.renderedChildren) {
            this.renderChildren(treeNode, {
                container: htmlNode
            });
        }

        this._syncNodeOpenState(treeNode, htmlNode);
    },

    _afterRemove: function (e) {
        if (!this.rendered) {
            return;
        }

        var treeNode = e.node,
            parent   = e.parent;

        // If this node is in the render queue, remove it from the queue.
        if (this._renderQueue[treeNode.id]) {
            delete this._renderQueue[treeNode.id];
        }

        // Remove DOM nodes associated with this node and any of its
        // descendants, and mark all nodes as unrendered so that they'll be
        // re-rendered if they're reinserted in the tree.
        var htmlNode = this.getHTMLNode(treeNode);

        if (htmlNode) {
            htmlNode
                .empty()
                .remove(true);

            treeNode._htmlNode = null;
        }

        if (!treeNode.state.destroyed) {
            treeNode.traverse(function (node) {
                node._htmlNode              = null;
                node.state.rendered         = false;
                node.state.renderedChildren = false;
            });
        }

        // Re-render the parent to update its state if this was its last child.
        if (parent && !parent.hasChildren()) {
            this.renderNode(parent);
        }
    },

    _afterSelect: function (e) {
        if (this.rendered) {
            this._syncNodeSelectedState(e.node);
        }
    },

    _afterTreeViewMultiSelectChange: function (e) {
        if (!this.rendered) {
            return;
        }

        var container = this.get('container'),
            rootList  = container.one('> .' + this.classNames.children),
            htmlNodes = container.all('.' + this.classNames.node);

        if (e.newVal) {
            rootList.set('aria-multiselectable', true);
            htmlNodes.set('aria-selected', false);
        } else {
            // When multiselect is disabled, aria-selected must not be set on
            // any nodes, since focus is used to indicate selection.
            rootList.removeAttribute('aria-multiselectable');
            htmlNodes.removeAttribute('aria-selected');
        }
    },

    _afterUnselect: function (e) {
        if (this.rendered) {
            this._syncNodeSelectedState(e.node);
        }
    },

    _onIndicatorClick: function (e) {
        var rowNode = e.currentTarget.ancestor('.' + this.classNames.row);

        // Indicator clicks shouldn't toggle selection state, so don't allow
        // this event to propagate to the _onRowClick() handler.
        e.stopImmediatePropagation();

        this.getNodeById(rowNode.getData('node-id')).toggleOpen();
    },

    _onMouseDown: function (e) {
        // This prevents the tree from momentarily grabbing focus before focus
        // is set on a node.
        e.preventDefault();
    },

    _onRowClick: function (e) {
        // Ignore buttons other than the left button.
        if (e.button > 1) {
            return;
        }

        var node = this.getNodeById(e.currentTarget.getData('node-id'));

        if (this.get('multiSelect')) {
            node[node.isSelected() ? 'unselect' : 'select']();
        } else {
            node.select();
        }
    },

    _onRowDoubleClick: function (e) {
        // Ignore buttons other than the left button.
        if (e.button > 1) {
            return;
        }

        this.getNodeById(e.currentTarget.getData('node-id')).toggleOpen();
    }
}, {
    ATTRS: {
        /**
        When `true`, a node's children won't be rendered until the first time
        that node is opened.

        This can significantly speed up the time it takes to render a large
        tree, but might not make sense if you're using CSS that doesn't hide the
        contents of closed nodes.

        @attribute {Boolean} lazyRender
        @default true
        **/
        lazyRender: {
            lazyAdd: false, // to ensure that the setter runs on init
            setter : '_setLazyRender',
            value  : true
        }
    }
});

Y.TreeView = Y.mix(TreeView, Y.TreeView);


}, 'gallery-2013.06.20-02-07', {
    "requires": [
        "base-build",
        "classnamemanager",
        "template-micro",
        "tree",
        "tree-labelable",
        "tree-openable",
        "tree-selectable",
        "view"
    ],
    "skinnable": true
});
