YUI.add('moodle-mod_feedback-dragdrop', function(Y) {
    var DRAGDROPNAME = 'mod_feedback_dragdrop';
    var CSS = {
        OLDMOVESELECTOR : 'span.feedback_item_command_move',
        OLDMOVEUPSELECTOR : 'span.feedback_item_command_moveup',
        OLDMOVEDOWNSELECTOR : 'span.feedback_item_command_movedown',
        DRAGAREASELECTOR : '#feedback_dragarea',
        DRAGITEMSELECTOR : '#feedback_dragarea ul li.feedback_itemlist',
        DRAGTARGETSELECTOR : '#feedback_dragarea ul#feedback_draglist',
        POSITIONLABEL : '.feedback_item_commands.position',
        ITEMBOXSELECTOR : '#feedback_item_box_'
    };

    var DRAGDROP = function() {
        DRAGDROP.superclass.constructor.apply(this, arguments);
    };

    Y.extend(DRAGDROP, Y.Base, {

        event:null,

        initializer : function(params) {
            var cmid = params.cmid;

            //Listen for all drop:over events
            Y.DD.DDM.on('drop:over', function(e) {
                //Get a reference to our drag and drop nodes
                var drag = e.drag.get('node'),
                    drop = e.drop.get('node');

                //Are we dropping on an li node?
                if (drop.get('tagName').toLowerCase() === 'li') {
                    //Are we not going up?
                    if (!goingUp) {
                        drop = drop.get('nextSibling');
                    }
                    //Add the node to this list
                    e.drop.get('node').get('parentNode').insertBefore(drag, drop);
                    //Resize this nodes shim, so we can drop on it later.
                    e.drop.sizeShim();
                }
            });
            //Listen for all drag:drag events
            Y.DD.DDM.on('drag:drag', function(e) {
                //Get the last y point
                var y = e.target.lastXY[1];
                //Is it greater than the lastY var?
                if (y < lastY) {
                    //We are going up
                    goingUp = true;
                } else {
                    //We are going down.
                    goingUp = false;
                }
                //Cache for next check
                lastY = y;
            });
            //Listen for all drag:start events
            Y.DD.DDM.on('drag:start', function(e) {
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
            });
            //Listen for a drag:end events
            Y.DD.DDM.on('drag:end', function(e) {
                var drag = e.target;
                //Put our styles back
                drag.get('node').removeClass('drag_target_active');

            });
            //Listen for all drag:drophit events
            Y.DD.DDM.on('drag:drophit', function(e) {
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
                    this.saveposition(cmid, myElements, spinner);
               }
            }, this);

            //Static Vars
            var goingUp = false, lastY = 0;

            //Get the list of li's in the lists and make them draggable.
            listitems = Y.Node.all(CSS.DRAGITEMSELECTOR);

            listitems.each(function(v) { //Make each item draggable.
                var dd = new Y.DD.Drag({
                    node: v,
                    target: {
                        padding: '0 0 0 20'
                    }
                }).plug(Y.Plugin.DDProxy, {
                    moveOnEnd: false
                }).plug(Y.Plugin.DDConstrained, {
                    constrain2node: CSS.DRAGAREASELECTOR //Prevent dragging outside the dragarea.
                });

                item_id = this.get_node_id(v.get('id')); //Get the id of the feedback item.
                item_box = Y.Node.one(CSS.ITEMBOXSELECTOR + item_id); //Get the current item box so we can add the drag handle.
                handletitle = M.util.get_string('move_item', 'feedback');
                mydraghandle = this.get_drag_handle(handletitle, 'itemhandle');
                v.insert(mydraghandle, item_box); //Insert the new handle into the item box.
                dd.addHandle(mydraghandle); //Now we add the handle to the drag object, so the box only can be moved with this handle.
            }, this);

            // Remove all legacy move icons.
            Y.Node.all(CSS.OLDMOVEUPSELECTOR).each(function(v, k) {
                v.remove();
            });;
            Y.Node.all(CSS.OLDMOVEDOWNSELECTOR).each(function(v, k) {
                v.remove();
            });;
            Y.Node.all(CSS.OLDMOVESELECTOR).each(function(v, k) {
                v.remove();
            });;

            //Create targets for drop.
            var droparea = Y.Node.one(CSS.DRAGTARGETSELECTOR);
            var tar = new Y.DD.Drop({
                node: droparea
            });

        },

        /**
         * Creates a new drag handle and return it as a new node.
         *
         * @param title The title of the drag icon
         * @param handleclass The css class for the drag handle
         * @return void
         */
        get_drag_handle : function(title, handleclass) {
            var moveicon = {
                pix: "i/move_2d",
                largepix: "i/dragdrop",
                component: 'moodle'
            };

            var iconname = moveicon.largepix;
            var dragicon = Y.Node.create('<img />')
                .setStyle('cursor', 'move')
                .setAttrs({
                    'src' : M.util.image_url(iconname, moveicon.component),
                    'alt' : title,
                    'class' : handleclass
                });

            var dragelement = Y.Node.create('<span></span>')
                .setAttribute('title', title);
            dragelement.appendChild(dragicon);
            return dragelement;
        },

        /**
         * Save the new item order.
         *
         * @param cmid the coursemodule id
         * @param itemorder A comma separated list with item ids
         * @param spinner The spinner icon shown while saving
         * @return void
         */
        saveposition : function(cmid, itemorder, spinner) {

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
                    success: function(transactionid, xhr) {
                        var response = xhr.responseText;
                        var ergebnis = Y.JSON.parse(response);
                        window.setTimeout(function(e) {
                            spinner.hide();
                        }, 250);
                    },
                    failure: function() {
                        window.setTimeout(function(e) {
                            spinner.hide();
                        }, 250);
                    }
                }
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
    requires:['io', 'json-parse', 'dd-constrain', 'dd-proxy', 'dd-drop']
});
