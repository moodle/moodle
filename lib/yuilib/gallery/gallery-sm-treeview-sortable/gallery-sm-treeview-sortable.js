YUI.add('gallery-sm-treeview-sortable', function (Y, NAME) {

/**
Provides `Y.TreeView.Sortable`, a `Y.TreeView` extension that mixes in
`Y.Tree.Sortable` and provides related TreeView-specific functionality.

@module gallery-sm-treeview
@submodule gallery-sm-treeview-sortable
**/

/**
Extension for `Y.TreeView` that mixes in `Y.Tree.Sortable` and provides related
TreeView-specific functionality (such as re-rendering a node after it's sorted).

@class TreeView.Sortable
@constructor
@extensionfor TreeView
@extends Tree.Sortable
**/

var Sortable = Y.TreeView.Sortable = function () {};

Y.mix(Sortable.prototype, Y.Tree.Sortable.prototype);

// -- Protected Methods ----------------------------------------------------

// Overrides Y.TreeView#_attachTreeViewEvents().
Sortable.prototype._attachTreeViewEvents = function () {
    Y.TreeView.prototype._attachTreeViewEvents.call(this);

    this._treeViewEvents.push(
        this.after('sort', this._afterSort)
    );
};

// -- Event Handlers -------------------------------------------------------

/**
Re-renders a node if necessary after a `sort` event.

@method _afterSort
@param {EventFacade} e
@protected
**/
Sortable.prototype._afterSort = function (e) {
    var node = e.node;

    // If this tree hasn't been rendered yet or the sorted node's children
    // haven't been rendered yet, there's nothing to do.
    if (!this.rendered || !node.state.renderedChildren) {
        return;
    }

    // Re-render the sorted node and its children.
    if (node.isRoot()) {
        this.render();
    } else {
        this.renderNode(node, {renderChildren: true});
    }
};


}, 'gallery-2013.06.20-02-07', {"requires": ["gallery-sm-treeview", "tree-sortable"]});
