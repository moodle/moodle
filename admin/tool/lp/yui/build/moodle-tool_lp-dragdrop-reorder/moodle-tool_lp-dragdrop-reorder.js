YUI.add('moodle-tool_lp-dragdrop-reorder', function (Y, NAME) {

/**
 * Simple drag and drop.
 *
 * Used when we just want a list of things that can be re-ordered by dragging.
 *
 * @class M.tool_lp.dragdrop_reorder
 * @constructor
 * @extends M.core.dragdrop
 */
var DRAGREORDER = function() {
    DRAGREORDER.superclass.constructor.apply(this, arguments);
};

var CSS = {
    EDITINGMOVE: 'editing_move',
    ICONCLASS: 'iconsmall'
};
Y.extend(DRAGREORDER, M.core.dragdrop, {
    initializer: function(args) {
        if (Y.one('.' + args.parentNodeClass).all('.' + args.dragHandleInsertClass).size() <= 1) {
            // We can't re-order when there is only one item.
            return;
        }
        // Set group for parent class
        this.groups = [args.group];
        this.samenodeclass = args.sameNodeClass;
        this.parentnodeclass = args.parentNodeClass;
        this.draghandleinsertclass = args.dragHandleInsertClass;
        this.draghandle = this.get_drag_handle(args.dragHandleText,
                CSS.EDITINGMOVE, CSS.ICONCLASS, true);

        this.samenodelabel = args.sameNodeLabel;
        this.parentnodelabel = args.parentNodeLabel;
        this.callback = args.callback;

        var delegate = new Y.DD.Delegate({
            container: '.' + args.parentNodeClass,
            nodes: '.' + args.sameNodeClass,
            target: true,
            handles: ['.' + CSS.EDITINGMOVE],
            dragConfig: {groups: this.groups}
        });

        delegate.dd.plug(Y.Plugin.DDProxy);

        Y.one('.' + args.parentNodeClass)
         .all('.' + args.dragHandleInsertClass)
         .each(
            function(node) {
                node.insert(this.draghandle.cloneNode(true));
            }, this);
    },

    drop_hit: function(e) {
        this.callback(e);
    }

}, {
    NAME: 'tool_lp-dragdrop-reorder',
    ATTRS: {
    }
});

M.tool_lp = M.tool_lp || {};
M.tool_lp.dragdrop_reorder = function(params) {
    return new DRAGREORDER(params);
};


}, '@VERSION@', {"requires": ["moodle-core-dragdrop"]});
