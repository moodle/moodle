YUI.add('moodle-qtype_ddmarker-dd', function(Y) {
    var DDMARKERDDNAME = 'ddmarker_dd';
    var DDMARKER_DD = function() {
        DDMARKER_DD.superclass.constructor.apply(this, arguments);
    }
    /**
     * This is the base class for the question rendering and question editing form code.
     */
    Y.extend(DDMARKER_DD, Y.Base, {
        doc : null,
        polltimer : null,
        afterimageloaddone : false,
        poll_for_image_load : function (e, waitforimageconstrain, pause, doafterwords) {
            if (this.afterimageloaddone) {
                return;
            }
            var bgdone = this.doc.bg_img().get('complete');
            if (waitforimageconstrain) {
                bgdone = bgdone && this.doc.bg_img().hasClass('constrained');
            }
            if (bgdone) {
                if (this.polltimer !== null) {
                    this.polltimer.cancel();
                    this.polltimer = null;
                }
                this.doc.bg_img().detach('load', this.poll_for_image_load);
                if (pause !== 0) {
                    Y.later(pause, this, doafterwords);
                } else {
                    doafterwords.call(this);
                }
                this.afterimageloaddone = true;
            } else if (this.polltimer === null) {
                var pollarguments = [null, waitforimageconstrain, pause, doafterwords];
                this.polltimer =
                            Y.later(1000, this, this.poll_for_image_load, pollarguments, true);
            }
        },
        /**
         * Object to encapsulate operations on dd area.
         */
        doc_structure : function (mainobj) {
            var topnode = Y.one(this.get('topnode'));
            var dragitemsarea = topnode.one('div.dragitems');
            var dropbgarea = topnode.one('div.droparea');
            return {
                top_node : function() {
                    return topnode;
                },
                bg_img : function() {
                    return topnode.one('.dropbackground');
                },
                load_bg_img : function (url) {
                    dropbgarea.setContent('<img class="dropbackground" src="'+ url +'"/>');
                    this.bg_img().on('load', this.on_image_load, this, 'bg_image');
                },
                drag_items : function() {
                    return dragitemsarea.all('.dragitem');
                },
                drag_items_for_choice : function(choiceno) {
                    return dragitemsarea.all('span.dragitem.choice' + choiceno);
                },
                drag_item_for_choice : function(choiceno, itemno) {
                    return dragitemsarea.one('span.dragitem.choice'+ choiceno + 
                                            '.item' + itemno);
                },
                drag_item_being_dragged : function(choiceno) {
                    return dragitemsarea.one('span.dragitem.yui3-dd-dragging.choice' + choiceno);
                },
                drag_item_home : function (choiceno) {
                    return dragitemsarea.one('span.draghome.choice' + choiceno);
                },
                drag_item_homes : function() {
                    return dragitemsarea.all('span.draghome');
                },
                get_classname_numeric_suffix : function(node, prefix) {
                    var classes = node.getAttribute('class');
                    if (classes !== '') {
                        var classesarr = classes.split(' ');
                        for (index in classesarr) {
                            var patt1 = new RegExp('^'+prefix+'([0-9])+$');
                            if (patt1.test(classesarr[index])) {
                                var patt2 = new RegExp('([0-9])+$');
                                var match = patt2.exec(classesarr[index]);
                                return +match[0];
                            }
                        }
                    }
                    return null;
                },
                inputs_for_choices : function () {
                    return topnode.all('input.choices');
                },
                input_for_choice : function (choiceno) {
                    return topnode.one('input.choice'+choiceno);
                }

            }
        },
        
        colours : ['#FFFFFF', '#B0C4DE', '#DCDCDC', '#D8BFD8',
                   '#87CEFA','#DAA520', '#FFD700', '#F0E68C'],
        nextcolourindex : 0,
        restart_colours : function () {
            this.nextcolourindex = 0;
        },
        get_next_colour : function () {
            var colour = this.colours[this.nextcolourindex];
            this.nextcolourindex++;
            if (this.nextcolourindex === this.colours.length) {
                this.nextcolourindex = 0;
            }
            return colour;
        },
        convert_to_window_xy : function (bgimgxy) {
            return [+bgimgxy[0] + this.doc.bg_img().getX() + 1,
                    +bgimgxy[1] + this.doc.bg_img().getY() + 1];
        }
    }, {
        NAME : DDMARKERDDNAME,
        ATTRS : {
            drops : {value : null},
            readonly : {value : false},
            topnode : {value : null}
        }
    });
    M.qtype_ddmarker = M.qtype_ddmarker || {};
    M.qtype_ddmarker.dd_base_class = DDMARKER_DD;

    var DDMARKERQUESTIONNAME = 'ddmarker_question';
    var DDMARKER_QUESTION = function() {
        DDMARKER_QUESTION.superclass.constructor.apply(this, arguments);
    };
    /**
     * This is the code for question rendering.
     */
    Y.extend(DDMARKER_QUESTION, M.qtype_ddmarker.dd_base_class, {
        initializer : function(params) {
            this.doc = this.doc_structure(this);
            this.poll_for_image_load(null, false, 0, this.after_image_load);
            this.doc.bg_img().after('load', this.poll_for_image_load, this,
                                                    false, 0, this.after_image_load);
        },
        after_image_load : function () {
            this.reposition_drags();
            Y.later(500, this, this.reposition_drags, [], true);
        },
        clone_new_drag_item : function (draghome, itemno) {
            var drag = draghome.cloneNode(true);
            drag.removeClass('draghome');
            drag.addClass('dragitem');
            drag.addClass('item'+ itemno);
            drag.one('span.markertext').setStyle('opacity', 0.6);
            draghome.insert(drag, 'after');
            if (!this.get('readonly')) {
                this.draggable(drag);
            }
            return drag;
        },
        draggable : function (drag) {
            var dd = new Y.DD.Drag({
                node: drag,
                dragMode: 'intersect'
            }).plug(Y.Plugin.DDConstrained, {constrain2node: this.doc.top_node()});
            dd.after('drag:start', function(e){
                var dragnode = e.target.get('node');
                var choiceno = this.get_choiceno_for_node(dragnode);
                var itemno = this.get_itemno_for_node(dragnode);
                if (itemno !== null) {
                    dragnode.removeClass('item'+dragnode);
                }
                this.save_all_xy_for_choice(choiceno, null);
                this.reposition_drags();
            }, this);
            dd.after('drag:end', function(e) {
                var dragnode = e.target.get('node');
                var choiceno = this.get_choiceno_for_node(dragnode);
                this.save_all_xy_for_choice(choiceno, dragnode);
                this.reposition_drags();
            }, this);
            //--- keyboard accessibility
            drag.set('tabIndex', 0);
            drag.on('dragchange', this.drop_zone_key_press, this);
        },
        save_all_xy_for_choice: function (choiceno, dropped) {
            var coords = [];
            for (var i=0; i <= this.doc.drag_items_for_choice(choiceno).size(); i++) {
                var dragitem = this.doc.drag_item_for_choice(choiceno, i);
                if (dragitem) {
                    dragitem.removeClass('item'+i);
                    if (!dragitem.hasClass('yui3-dd-dragging')) {
                        var bgimgxy = this.convert_to_bg_img_xy(dragitem.getXY());
                        if (this.xy_in_bgimg(bgimgxy)) {
                            dragitem.removeClass('item'+i);
                            dragitem.addClass('item'+coords.length);
                            coords[coords.length] = bgimgxy;
                        }
                    }
                }
            }
            if (dropped !== null){
                var bgimgxy = this.convert_to_bg_img_xy(dropped.getXY());
                dropped.addClass('item'+coords.length);
                if (this.xy_in_bgimg(bgimgxy)) {
                    coords[coords.length] = bgimgxy;
                }
            }
            this.set_form_value(choiceno, coords.join(';'));
        },
        reset_drag_xy : function (choiceno) {
            this.set_form_value(choiceno, '');
        },
        set_form_value : function (choiceno, value) {
            this.doc.input_for_choice(choiceno).set('value', value);
        },
        //make sure xy value is not out of bounds of bg image
        xy_in_bgimg : function (bgimgxy) {
            if ((bgimgxy[0] < 0) ||
                    (bgimgxy[1] < 0) ||
                    (bgimgxy[0] > this.doc.bg_img().get('width')) || 
                    (bgimgxy[1] > this.doc.bg_img().get('height'))){
                return false;
            } else {
                return true;
            }
        },
        constrain_to_bgimg : function (windowxy) {
            var bgimgxy = this.convert_to_bg_img_xy(windowxy);
            bgimgxy[0] = Math.max(0, bgimgxy[0]);
            bgimgxy[1] = Math.max(0, bgimgxy[1]);
            bgimgxy[0] = Math.min(this.doc.bg_img().get('width'), bgimgxy[0]);
            bgimgxy[1] = Math.min(this.doc.bg_img().get('height'), bgimgxy[1]);
            return this.convert_to_window_xy(bgimgxy);
        },
        convert_to_bg_img_xy : function (windowxy) {
            return [Math.round(+windowxy[0] - this.doc.bg_img().getX()-1),
                    Math.round(+windowxy[1] - this.doc.bg_img().getY()-1)];
        },
        reposition_drags : function() {
            this.doc.drag_items().each(function(item) {
                //if (!item.hasClass('yui3-dd-dragging')){
                    item.addClass('unneeded');
                //}
            }, this);
            this.doc.inputs_for_choices().each(function (input) {
                var choiceno = this.get_choiceno_for_node(input);
                var coords = this.get_coords(input);
                var dragitemhome = this.doc.drag_item_home(choiceno);
                for (var i=0; i < coords.length; i++) {
                    var dragitem;
                    dragitem = this.doc.drag_item_for_choice(choiceno, i);
                    if (!dragitem || dragitem.hasClass('yui3-dd-dragging')) {
                        dragitem = this.clone_new_drag_item(dragitemhome, i);
                    } else {
                        dragitem.removeClass('unneeded');
                    }
                    dragitem.setXY(coords[i]);
                }
            }, this);
            this.doc.drag_items().each(function(item) {
                if (item.hasClass('unneeded') && !item.hasClass('yui3-dd-dragging')) {
                    item.remove(true);
                }
            }, this);
        },
        /**
         * Return coords of all drag items except any that are currently being dragged
         * based on contents of hidden inputs and whether drags are 'infinite'
         */
        get_coords : function (input) {
            var choiceno = this.get_choiceno_for_node(input);
            var fv = input.get('value');
            var infinite = input.hasClass('infinite');
            var dragging = (null !== this.doc.drag_item_being_dragged(choiceno));
            var coords = [];
            var dragitemhome = this.doc.drag_item_home(choiceno);
            if (fv !== '') {
                var coordsstrings = fv.split(';');
                for (var i=0; i<coordsstrings.length; i++) {
                    coords[coords.length] = this.convert_to_window_xy(coordsstrings[i].split(','));
                }
            }
            if (infinite || (!dragging && fv === '')) {
                coords[coords.length] = dragitemhome.getXY();
            }
            return coords;
        },
        get_choiceno_for_node : function(node) {
            return +this.doc.get_classname_numeric_suffix(node, 'choice');
        },
        get_itemno_for_node : function(node) {
            return +this.doc.get_classname_numeric_suffix(node, 'item');
        },
        
        //----------- keyboard accessibility stuff below line ---------------------
        drop_zone_key_press : function (e) {
            var dragitem = e.target;
            var xy = dragitem.getXY();
            switch (e.direction) {
                case 'left' :
                    xy[0] -= 1;
                    break;
                case 'right' :
                    xy[0] += 1;
                    break;
                case 'down' :
                    xy[1] += 1;
                    break;
                case 'up' :
                    xy[1] -= 1;
                    break;
                case 'remove' :
                    xy = null;
                    break;
            }
            var choiceno = this.get_choiceno_for_node(dragitem);
            if (xy !== null) {
                xy = this.constrain_to_bgimg(xy);
            } else {
                xy = this.doc.drag_item_home(choiceno).getXY();
            }
            console.log(e.direction, xy);
            e.preventDefault();
            dragitem.setXY(xy);
            this.save_all_xy_for_choice(choiceno, null);
        }


    }, {NAME : DDMARKERQUESTIONNAME, ATTRS : {}});

    Y.Event.define('dragchange', {
        // Webkit and IE repeat keydown when you hold down arrow keys.
        // Opera links keypress to page scroll; others keydown.
        // Firefox prevents page scroll via preventDefault() on either
        // keydown or keypress.
        _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

        _keys: {
            '32': 'remove',     // Space
            '37': 'left', // Left arrow
            '38': 'up', // Up arrow
            '39': 'right',     // Right arrow
            '40': 'down',     // Down arrow
            '27': 'remove'    // Escape
        },

        _keyHandler: function (e, notifier) {
            if (this._keys[e.keyCode]) {
                e.direction = this._keys[e.keyCode];
                notifier.fire(e);
            }
        },

        on: function (node, sub, notifier) {
            sub._detacher = node.on(this._event, this._keyHandler,
                                    this, notifier);
        }
    });
    M.qtype_ddmarker.init_question = function(config) {
        return new DDMARKER_QUESTION(config);
    }
}, '@VERSION@', {
      requires:['node', 'event-resize', 'dd', 'dd-drop', 'dd-constrain']
});
