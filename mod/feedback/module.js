M.mod_feedback = {};
M.mod_feedback.init = function(Y, id, sesskey, yuibase, ajaxscript, moodlebase) {
    //Listen for all drop:over events
    Y.DD.DDM.on('drop:over', function(e) {
        //Get a reference to our drag and drop nodes
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');

        //Are we dropping on a li node?
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
        //is it greater than the lastY var?
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
        drag.get('node').setStyle('opacity', '.25');
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
    });
    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        var drag = e.target;
        //Put our styles back
        drag.get('node').setStyles({
            visibility: '',
            opacity: '1'
        });

    });
    //Listen for all drag:drophit events
    Y.DD.DDM.on('drag:drophit', function(e) {
        var drop = e.drop.get('node'),
            drag = e.drag.get('node');
        dragnode = Y.one(drag);
        //if we are not on an li, we must have been dropped on a ul
        if (drop.get('tagName').toLowerCase() !== 'li') {
            if (!drop.contains(drag)) {
                drop.appendChild(drag);
            }
            myElements = '';
            drop.get('children').each(function(v, k) {
                myElements = myElements + ',' + get_node_id(v.get('id'));
            });
            var spinner = M.util.add_spinner(Y, dragnode);
            saveposition(Y, this, id, myElements, sesskey, spinner);
       }
    });

    //Static Vars
    var goingUp = false, lastY = 0;

    //Get the list of li's in the lists and make them draggable
    var listitems = Y.Node.all('#feedback_dragarea ul li.feedback_itemlist');
    listitems.each(function(v, k) { //make each item draggable
        var dd = new Y.DD.Drag({
            node: v,
            target: {
                padding: '0 0 0 20'
            }
        }).plug(Y.Plugin.DDProxy, {
            moveOnEnd: false
        }).plug(Y.Plugin.DDConstrained, {
            constrain2node: '#feedback_dragarea' //prevent dragging outside the dragarea
        });

        item_id = get_node_id(v.get('id')); //get the id of the feedback item
        item_box = Y.Node.one('#feedback_item_box_' + item_id); //get the current item box so we can add the drag handle
        handletitle = M.util.get_string('move_item', 'feedback');
        mydraghandle = get_drag_handle(handletitle, 'itemhandle');
        v.insert(mydraghandle, item_box); //insert the new handle into the item box
        dd.addHandle(mydraghandle); //now we add the handle to the drag object, so the box only can be moved with this handle
    });
    // remove all legacy move icons
    Y.Node.all('span.feedback_item_command_moveup').each(function(v, k) {
        v.remove();
    });;
    Y.Node.all('span.feedback_item_command_movedown').each(function(v, k) {
        v.remove();
    });;
    Y.Node.all('span.feedback_item_command_move').each(function(v, k) {
        v.remove();
    });;

    //Create targets for drop.
    var droparea = Y.Node.one('#feedback_dragarea ul#feedback_draglist');
    var tar = new Y.DD.Drop({
        node: droparea
    });

    // here we save the new itemorder
    function saveposition(Y, objekt, id, itemorder, sesskey, spinner){

        Y.io(ajaxscript, {
            //the needed paramaters
            data: {action: 'saveitemorder',
                   id: id,
                   itemorder: itemorder,
                   sesskey: sesskey
            },

            timeout: 5000, //5 seconds for timeout I think it is enough

            //define the events
            on: {
                start : function(transactionid) {
                    spinner.show();
                },
                success: function(transactionid, xhr) {
                    var response = xhr.response;
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
    };

    //this returns the numeric id from the dom id
    function get_node_id(id) {
        return Number(id.replace(/feedback_item_/i, ''));
    };

    //this creates a new drag handle and return it as a new node
    function get_drag_handle(title, handleclass) {
        var MOVEICON = {
            pix: "i/move_2d",
            largepix: "i/dragdrop",
            component: 'moodle'
        };


        var iconname = MOVEICON.pix;
        var dragicon = Y.Node.create('<img />')
            .setStyle('cursor', 'move')
            .setAttrs({
                'src' : M.util.image_url(iconname, MOVEICON.component),
                'alt' : title,
                'class' : handleclass
            });

        var dragelement = Y.Node.create('<span></span>')
            .setAttribute('title', title)
        dragelement.appendChild(dragicon);
        return dragelement;
    };

};
