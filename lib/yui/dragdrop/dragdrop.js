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
        lasty   : null,
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
            var drag = e.target;
            // Check that drag object belongs to correct group
            if (!this.in_group(drag)) {
                return;
            }
            //Get the last y point
            var y = drag.lastXY[1];
            //is it greater than the lasty var?
            if (y < this.lasty) {
                //We are going up
                this.goingup = true;
            } else {
                //We are going down.
                this.goingup = false;
            }
            //Cache for next check
            this.lasty = y;
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
                //Are we not going up?
                if (!this.goingup) {
                    drop = drop.next('.'+this.samenodeclass);
                }
                //Add the node
                e.drop.get('node').ancestor().insertBefore(drag, drop);
            } else if (drop.hasClass(this.parentnodeclass) && !drop.contains(drag)) {
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
