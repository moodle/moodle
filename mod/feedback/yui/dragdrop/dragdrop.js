YUI.add('moodle-mod_feedback-dragdrop', function(Y) {
    var DRAGDROPNAME = 'mod_feedback_dragdrop';
    var CSS = {
        OLDMOVE : 'span.feedback_item_command_move',
        OLDMOVEUP : 'span.feedback_item_command_moveup',
        OLDMOVEDOWN : 'span.feedback_item_command_movedown',
        DRAGAREA : '#feedback_dragarea',
        DRAGITEM : 'li.feedback_itemlist',
        DRAGLIST : '#feedback_dragarea ul#feedback_draglist',
        POSITIONLABEL : '.feedback_item_commands.position',
        ITEMBOX : '#feedback_item_box_',
        DRAGHANDLE : 'itemhandle'
    };

    var DRAGDROP = function() {
        DRAGDROP.superclass.constructor.apply(this, arguments);
    };

    Y.extend(DRAGDROP, M.core.dragdrop, {

        initializer : function(params) {
            //Static Vars
            this.cmid = params.cmid;
            this.goingUp = false, lastY = 0;

            var groups = ['feedbackitem'];

            handletitle = M.util.get_string('move_item', 'feedback');
            this.mydraghandle = this.get_drag_handle(handletitle, CSS.DRAGHANDLE, 'icon');

            //Get the list of li's in the lists and add the drag handle.
            basenode = Y.Node.one(CSS.DRAGLIST);
            listitems = basenode.all(CSS.DRAGITEM).each(function(v) {
                item_id = this.get_node_id(v.get('id')); //Get the id of the feedback item.
                item_box = Y.Node.one(CSS.ITEMBOX + item_id); //Get the current item box so we can add the drag handle.
                v.insert(this.mydraghandle.cloneNode(true), item_box); //Insert the new handle into the item box.
            }, this);

            //We use a delegate to make all items draggable
            var del = new Y.DD.Delegate({
                container: CSS.DRAGLIST,
                nodes: CSS.DRAGITEM,
                target: {
                    padding: '0 0 0 20'
                },
                handles: ['.' + CSS.DRAGHANDLE],
                dragConfig: {groups: groups}
            });

            //Add plugins to the delegate
            del.dd.plug(Y.Plugin.DDProxy, {
                // Don't move the node at the end of the drag
                moveOnEnd: false,
                cloneNode: true
            });
            del.dd.plug(Y.Plugin.DDConstrained, {
                // Keep it inside the .course-content
                constrain: CSS.DRAGAREA
            });
            del.dd.plug(Y.Plugin.DDWinScroll);

            //Listen for all drop:over events
            del.on('drop:over', this.drop_over_handler, this);
            //Listen for all drag:drag events
            del.on('drag:drag',  this.drag_drag_handler, this);
            //Listen for all drag:start events
            del.on('drag:start',  this.drag_start_handler, this);
            //Listen for a drag:end events
            del.on('drag:end',  this.drag_end_handler, this);
            //Listen for all drag:drophit events
            del.on('drag:drophit',  this.drag_drophit_handler, this);
            //Listen for all drag:dropmiss events
            del.on('drag:dropmiss',  this.drag_dropmiss_handler, this);

            // Remove all legacy move icons.
            Y.all(CSS.OLDMOVEUP).remove();
            Y.all(CSS.OLDMOVEDOWN).remove();
            Y.all(CSS.OLDMOVE).remove();

            //Create targets for drop.
            var droparea = Y.Node.one(CSS.DRAGLIST);
            var tar = new Y.DD.Drop({
                groups: groups,
                node: droparea
            });

        },

        /**
         * Handles the drop:over event.
         *
         * @param e the event
         * @return void
         */
        drop_over_handler : function(e) {
            //Get a reference to our drag and drop nodes
            var drag = e.drag.get('node'),
                drop = e.drop.get('node');

            //Are we dropping on an li node?
            if (drop.get('tagName').toLowerCase() === 'li') {
                //Are we not going up?
                if (!this.goingUp) {
                    drop = drop.get('nextSibling');
                }
                //Add the node to this list
                e.drop.get('node').get('parentNode').insertBefore(drag, drop);
                //Resize this nodes shim, so we can drop on it later.
                e.drop.sizeShim();
            }
        },

        /**
         * Handles the drag:drag event.
         *
         * @param e the event
         * @return void
         */
        drag_drag_handler : function(e) {
            //Get the last y point
            var y = e.target.lastXY[1];
            //Is it greater than the lastY var?
            if (y < this.lastY) {
                //We are going up
                this.goingUp = true;
            } else {
                //We are going down.
                this.goingUp = false;
            }
            //Cache for next check
            this.lastY = y;
        },

        /**
         * Handles the drag:start event.
         *
         * @param e the event
         * @return void
         */
        drag_start_handler : function(e) {
            //Get our drag object
            var drag = e.target;

            //Set some styles here
            drag.get('node').addClass('drag_target_active');
            drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
            drag.get('dragNode').addClass('drag_item_active');
            drag.get('dragNode').setStyles({
                borderColor: drag.get('node').getStyle('borderColor'),
                backgroundColor: drag.get('node').getStyle('backgroundColor')
            });
        },

        /**
         * Handles the drag:end event.
         *
         * @param e the event
         * @return void
         */
        drag_end_handler : function(e) {
            var drag = e.target;
            //Put our styles back
            drag.get('node').removeClass('drag_target_active');
        },

        /**
         * Handles the drag:drophit event.
         *
         * @param e the event
         * @return void
         */
        drag_drophit_handler : function(e) {
            var drop = e.drop.get('node'),
                drag = e.drag.get('node');
            dragnode = Y.one(drag);
            //If we are not on an li, we must have been dropped on a ul.
            if (drop.get('tagName').toLowerCase() !== 'li') {
                if (!drop.contains(drag)) {
                    drop.appendChild(drag);
                }
                myElements = '';
                counter = 1;
                drop.get('children').each(function(v) {
                    poslabeltext = '(' + M.util.get_string('position', 'feedback') + ':' + counter + ')';
                    poslabel = v.one(CSS.POSITIONLABEL);
                    poslabel.setHTML(poslabeltext);
                    myElements = myElements + ',' + this.get_node_id(v.get('id'));
                    counter++;
                }, this);
                var spinner = M.util.add_spinner(Y, dragnode);
                this.save_item_order(this.cmid, myElements, spinner);
           }
        },

        /**
         * Save the new item order.
         *
         * @param cmid the coursemodule id
         * @param itemorder A comma separated list with item ids
         * @param spinner The spinner icon shown while saving
         * @return void
         */
        save_item_order : function(cmid, itemorder, spinner) {

            Y.io(M.cfg.wwwroot + '/mod/feedback/ajax.php', {
                //The needed paramaters
                data: {action: 'saveitemorder',
                       id: cmid,
                       itemorder: itemorder,
                       sesskey: M.cfg.sesskey
                },

                timeout: 5000, //5 seconds for timeout I think it is enough.

                //Define the events.
                on: {
                    start : function(transactionid) {
                        spinner.show();
                    },
                    success : function(transactionid, xhr) {
                        var response = xhr.responseText;
                        var ergebnis = Y.JSON.parse(response);
                        window.setTimeout(function(e) {
                            spinner.hide();
                        }, 250);
                    },
                    failure : function(transactionid, xhr) {
                        var msg = {
                            name : xhr.status+' '+xhr.statusText,
                            message : xhr.responseText
                        };
                        return new M.core.exception(msg);
                        //~ this.ajax_failure(xhr);
                        spinner.hide();
                    }
                },
                context:this
            });
        },

        /**
         * Returns the numeric id from the dom id of an item.
         *
         * @param id The dom id, f.g.: feedback_item_22
         * @return int
         */
        get_node_id : function(id) {
            return Number(id.replace(/feedback_item_/i, ''));
        }

    }, {
        NAME : DRAGDROPNAME,
        ATTRS : {
            cmid : {
                value : 0
            }
        }

    });

    M.mod_feedback = M.mod_feedback || {};
    M.mod_feedback.init_dragdrop = function(params) {
        return new DRAGDROP(params);
    }

}, '@VERSION@', {
    requires:['io', 'json-parse', 'dd-constrain', 'dd-proxy', 'dd-drop', 'dd-scroll', 'moodle-core-dragdrop', 'moodle-core-notification']
});
