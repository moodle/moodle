YUI.add('moodle-core-dragdrop', function(Y) {
    var MOVEICON = {
        pix: "i/move_2d",
        largepix: "i/dragdrop",
        component: 'moodle'
    };

   /*
    * General DRAGDROP class, this should not be used directly,
    * it is supposed to be extended by your class
    */
    var DRAGDROP = function() {
        DRAGDROP.superclass.constructor.apply(this, arguments);
    };

    Y.extend(DRAGDROP, Y.Base, {
        goingup : null,
        absgoingup : null,
        samenodeclass : null,
        parentnodeclass : null,
        groups : [],
        lastdroptarget : null,
        initializer : function(params) {
            // Listen for all drag:start events
            Y.DD.DDM.on('drag:start', this.global_drag_start, this);
            // Listen for all drag:end events
            Y.DD.DDM.on('drag:end', this.global_drag_end, this);
            // Listen for all drag:drag events
            Y.DD.DDM.on('drag:drag', this.global_drag_drag, this);
            // Listen for all drop:over events
            Y.DD.DDM.on('drop:over', this.global_drop_over, this);
            // Listen for all drop:hit events
            Y.DD.DDM.on('drop:hit', this.global_drop_hit, this);
            // Listen for all drop:miss events
            Y.DD.DDM.on('drag:dropmiss', this.global_drag_dropmiss, this);
        },

        get_drag_handle: function(title, classname, iconclass, large) {
            var iconname = MOVEICON.pix;
            if (large) {
                iconname = MOVEICON.largepix;
            }
            var dragicon = Y.Node.create('<img />')
                .setStyle('cursor', 'move')
                .setAttrs({
                    'src' : M.util.image_url(iconname, MOVEICON.component),
                    'alt' : title
                });
            if (iconclass) {
                dragicon.addClass(iconclass);
            }

            var dragelement = Y.Node.create('<span></span>')
                .addClass(classname)
                .setAttribute('title', title)
            dragelement.appendChild(dragicon);
            return dragelement;
        },

        lock_drag_handle: function(drag, classname) {
            // Disable dragging
            drag.removeHandle('.'+classname);
        },

        unlock_drag_handle: function(drag, classname) {
            // Enable dragging
            drag.addHandle('.'+classname);
        },

        ajax_failure: function(response) {
            var e = {
                name : response.status+' '+response.statusText,
                message : response.responseText
            };
            return new M.core.exception(e);
        },

        in_group: function(target) {
            var ret = false;
            Y.each(this.groups, function(v, k) {
                if (target._groups[v]) {
                    ret = true;
                }
            }, this);
            return ret;
        },
        /*
         * Drag-dropping related functions
         */
        global_drag_start : function(e) {
            // Get our drag object
            var drag = e.target;
            // Check that drag object belongs to correct group
            if (!this.in_group(drag)) {
                return;
            }
            // Set some general styles here
            drag.get('node').setStyle('opacity', '.25');
            drag.get('dragNode').setStyles({
                opacity: '.75',
                borderColor: drag.get('node').getStyle('borderColor'),
                backgroundColor: drag.get('node').getStyle('backgroundColor')
            });
            drag.get('dragNode').empty();
            this.drag_start(e);
        },

        global_drag_end : function(e) {
            var drag = e.target;
            // Check that drag object belongs to correct group
            if (!this.in_group(drag)) {
                return;
            }
            //Put our general styles back
            drag.get('node').setStyles({
                visibility: '',
                opacity: '1'
            });
            this.drag_end(e);
        },

        global_drag_drag : function(e) {
            var drag = e.target,
                info = e.info;

            // Check that drag object belongs to correct group
            if (!this.in_group(drag)) {
                return;
            }

            // Note, we test both < and > situations here. We don't want to
            // effect a change in direction if the user is only moving side
            // to side with no Y position change.

            // Detect changes in the position relative to the start point.
            if (info.start[1] < info.xy[1]) {
                // We are going up if our final position is higher than our start position.
                this.absgoingup = true;

            } else if (info.start[1] > info.xy[1]) {
                // Otherwise we're going down.
                this.absgoingup = false;
            }

            // Detect changes in the position relative to the last movement.
            if (info.delta[1] < 0) {
                // We are going up if our final position is higher than our start position.
                this.goingup = true;

            } else if (info.delta[1] > 0) {
                // Otherwise we're going down.
                this.goingup = false;
            }

            this.drag_drag(e);
        },

        global_drop_over : function(e) {
            // Check that drop object belong to correct group
            if (!e.drop || !e.drop.inGroup(this.groups)) {
                return;
            }
            //Get a reference to our drag and drop nodes
            var drag = e.drag.get('node');
            var drop = e.drop.get('node');
            // Save last drop target for the case of missed target processing
            this.lastdroptarget = e.drop;
            //Are we dropping on the same node?
            if (drop.hasClass(this.samenodeclass)) {
                var where;

                if (this.goingup) {
                    where = "before";
                } else {
                    where = "after";
                }

                // Add the node contents so that it's moved, otherwise only the drag handle is moved.
                drop.insert(drag, where);
            } else if ((drop.hasClass(this.parentnodeclass) || drop.test('[data-droptarget="1"]')) && !drop.contains(drag)) {
                // We are dropping on parent node and it is empty
                if (this.goingup) {
                    drop.append(drag);
                } else {
                    drop.prepend(drag);
                }
            }
            this.drop_over(e);
        },

        global_drag_dropmiss : function(e) {
            // drag:dropmiss does not have e.drag and e.drop properties
            // we substitute them for the ease of use. For e.drop we use,
            // this.lastdroptarget (ghost node we use for indicating where to drop)
            e.drag = e.target;
            e.drop = this.lastdroptarget;
            // Check that drag object belongs to correct group
            if (!this.in_group(e.drag)) {
                return;
            }
            // Check that drop object belong to correct group
            if (!e.drop || !e.drop.inGroup(this.groups)) {
                return;
            }
            this.drag_dropmiss(e);
        },

        global_drop_hit : function(e) {
            // Check that drop object belong to correct group
            if (!e.drop || !e.drop.inGroup(this.groups)) {
                return;
            }
            this.drop_hit(e);
        },

        /*
         * Abstract functions definitions
         */
        drag_start : function(e) {},
        drag_end : function(e) {},
        drag_drag : function(e) {},
        drag_dropmiss : function(e) {},
        drop_over : function(e) {},
        drop_hit : function(e) {}
    }, {
        NAME : 'dragdrop',
        ATTRS : {}
    });

M.core = M.core || {};
M.core.dragdrop = DRAGDROP;

}, '@VERSION@', {requires:['base', 'node', 'io', 'dom', 'dd', 'moodle-core-notification']});
