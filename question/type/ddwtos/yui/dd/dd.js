YUI.add('moodle-qtype_ddwtos-dd', function(Y) {
    var DDWTOSDDNAME = 'ddwtos_dd';
    var DDWTOS_DD = function() {
        DDWTOS_DD.superclass.constructor.apply(this, arguments);
    }
    /**
     * This is the class for ddwtos question rendering.
     * A DDWTOS_DD class is created for each question.
     */
    Y.extend(DDWTOS_DD, Y.Base, {
        selectors : null,
        initializer : function(params) {
            this.selectors = this.css_selectors(this.get('topnode'));
            this.set_padding_sizes_all();
            this.clone_drag_items();
            this.place_drag_items();
            this.make_drop_zones();
            Y.later(500, this, this.position_drag_items, [], true);
        },
        /**
         * put all our selectors in the same place so we can quickly find and change them later
         * if the structure of the document changes.
         */
        css_selectors : function(topnode){
            return {
                top_node : function() {
                    return topnode;
                },
                drag_container : function() {
                    return topnode+' div.drags';
                },
                drags : function() {
                    return this.drag_container()+' span.drag';
                },
                drag : function(no) {
                    return this.drags()+'.no'+no;
                },
                drags_in_group : function(groupno) {
                    return this.drags()+'.group'+groupno;
                },
                unplaced_drags_in_group : function(groupno) {
                    return this.drags_in_group(groupno)+'.unplaced';
                },
                drags_for_choice_in_group : function(choiceno, groupno) {
                    return this.drags_in_group(groupno)+'.choice'+choiceno;
                },
                unplaced_drags_for_choice_in_group : function(choiceno, groupno) {
                    return this.unplaced_drags_in_group(groupno)+'.choice'+choiceno;
                },
                drops : function() {
                    return topnode+' span.drop';
                },
                drop_for_place : function(placeno) {
                    return this.drops()+'.place'+placeno;
                },
                drops_in_group : function(groupno) {
                    return this.drops()+'.group'+groupno;
                },
                drag_homes : function() {
                    return topnode+' span.draghome';
                },
                drag_homes_group : function(groupno) {
                    return topnode+' .draggrouphomes'+groupno+' span.draghome';
                },
                drag_home : function(groupno, choiceno) {
                    return topnode+' .draggrouphomes'+groupno+' span.draghome.choice'+choiceno;
                },
                drops_group : function(groupno) {
                    return topnode+' span.drop.group'+groupno;
                }
            }
        },
        set_padding_sizes_all : function () {
            for (var groupno = 1; groupno <= 8; groupno++) {
                this.set_padding_size_for_group(groupno);
            }
        },
        set_padding_size_for_group : function (groupno) {
            var groupitems = Y.all(this.selectors.drag_homes_group(groupno));
            if (groupitems.size() !== 0) {
                var maxwidth = 0;
                var maxheight = 0;
                //find max height and width
                groupitems.each(function(item){
                    maxwidth = Math.max(maxwidth, item.get('offsetWidth'));
                    maxheight = Math.max(maxheight, item.get('offsetHeight'));
                }, this);
                groupitems.each(function(item) {
                    var margintop = Math.round((maxheight - item.get('offsetHeight'))/2);
                    var marginleft = Math.round((maxwidth - item.get('offsetWidth'))/2);
                    var marginbottom = (maxheight - item.get('offsetHeight')) - margintop;
                    var marginright = (maxwidth - item.get('offsetWidth')) - marginleft;
                    item.setStyle('padding', margintop+'px '+marginleft+'px '
                                            +marginbottom+'px '+marginright+'px');
                }, this);
                Y.all(this.selectors.drops_group(groupno)).setStyles({'width': maxwidth - 2,
                                                                'height': maxheight - 2});
            }
        },
        /**
         * Invisible 'drag homes' are output by the renderer. These have the same properties 
         * as the drag items but are invisible. We clone these invisible elements to make the 
         * actual drag items.
         */
        clone_drag_items : function () {
            Y.all(this.selectors.drag_homes()).each(this.clone_drag_items_for_one_choice, this);
        },
        clone_drag_items_for_one_choice : function(draghome) {
            if (draghome.hasClass('infinite')) {
                var groupno = this.get_group(draghome);
                var noofdrags = Y.all(this.selectors.drops_in_group(groupno)).size();
                for (var i=0; i < noofdrags; i++) {
                    this.clone_drag_item(draghome);
                }
            } else {
                this.clone_drag_item(draghome);
            }
        },
        nextdragitemno : 1,
        clone_drag_item : function (draghome) {
            var drag = draghome.cloneNode(true);
            drag.removeClass('draghome');
            drag.addClass('drag');
            drag.addClass('no'+this.nextdragitemno);
            this.nextdragitemno++;
            drag.setStyles({'visibility': 'visible', 'position' : 'absolute'});
            Y.one(this.selectors.drag_container()).appendChild(drag);
            if (!this.get('readonly')) {
                this.make_draggable(drag);
            }
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
            throw 'Prefix "'+prefix+'" not found in class names.';
        },
        get_choice : function(node) {
            return this.get_classname_numeric_suffix(node, 'choice');
        },
        get_group : function(node) {
            return this.get_classname_numeric_suffix(node, 'group');
        },
        get_place : function(node) {
            return this.get_classname_numeric_suffix(node, 'place');
        },
        get_no : function(node) {
            return this.get_classname_numeric_suffix(node, 'no');
        },
        placed : null,
        place_drag_items : function() {
            Y.all(this.selectors.drags()).addClass('unplaced');
            this.placed = [];
            for (var placeno in this.get('inputids')) {
                var inputid = this.get('inputids')[placeno];
                var inputnode = Y.one('input#'+inputid);
                var choiceno = +inputnode.get('value');
                if (choiceno !== 0) {
                    var drop = Y.one(this.selectors.drop_for_place(placeno));
                    var groupno = this.get_group(drop);
                    var drag =
                        Y.one(this.selectors.unplaced_drags_for_choice_in_group(choiceno, groupno));
                    this.place_drag_in_drop(drag, drop);
                }
            }
        },
        make_draggable : function (drag) {
            var choice = this.get_choice(drag);
            var group = this.get_group(drag);
            var dd = new Y.DD.Drag({
                node: drag,
                dragMode: 'intersect'
            }).plug(Y.Plugin.DDConstrained, {constrain2node: this.selectors.top_node()});
        },
        make_drop_zones : function () {
            Y.all(this.selectors.drops()).each(this.make_drop_zone, this);
        },
        make_drop_zone : function (drop) {
            var dropdd = new Y.DD.Drop({
                node: drop});
            dropdd.on('drop:hit', function(e) {
                var drag = e.drag.get('node');
                var drop = e.drop.get('node');
                if (this.get_group(drop) === this.get_group(drag)){
                    this.place_drag_in_drop(drag, drop);
                }
            }, this);
            if (!this.get('readonly')) {
                drop.on('dragchange', this.drop_zone_key_press, this);
            }
        },
        place_drag_in_drop : function (drag, drop) {
            var placeno = this.get_place(drop);
            var inputid = this.get('inputids')[placeno];
            var inputnode = Y.one('input#'+inputid);
            if (drag !== null) {
                inputnode.set('value', this.get_choice(drag));
            } else {
                inputnode.set('value', '0');
            }
            for (var alreadytheredragno in this.placed) {
                if (this.placed[alreadytheredragno] === placeno) {
                    delete this.placed[alreadytheredragno];
                    var alreadytheredrag = Y.one(this.selectors.drag(alreadytheredragno));
                    alreadytheredrag.addClass('unplaced');
                }
            }
            if (drag !== null) {
                this.placed[this.get_no(drag)] = placeno;
                if (drag.dd) {
                    drag.dd.once('drag:start', function (e, inputnode, drag) {
                        inputnode.set('value', 0);
                        drag.addClass('unplaced');
                        delete this.placed[this.get_no(drag)];
                    },this, inputnode, drag);
                }
                drag.removeClass('unplaced');
            }
        },
        remove_drag_from_drop : function (drop) {
            this.place_drag_in_drop(null, drop);
        },
        position_drag_items : function () {
            Y.all(this.selectors.drags()).each(this.position_drag_item, this)
        },
        position_drag_item : function (drag) {
            if (!drag.hasClass('yui3-dd-dragging')) {
                if (!this.placed[this.get_no(drag)]) {
                    var groupno = this.get_group(drag);
                    var choiceno = this.get_choice(drag)
                    var home = Y.one(this.selectors.drag_home(groupno, choiceno));
                    drag.setXY(home.getXY());
                } else {
                    var placeno = this.placed[this.get_no(drag)];
                    var drop = Y.one(this.selectors.drop_for_place(placeno));
                    drag.setXY(drop.getXY());
                }
            }
        },
        
        drop_zone_key_press : function (e) {
            switch (e.direction) {
                case 'next' :
                    this.place_next_drag_in(e.target);
                    break;
                case 'previous' :
                    this.place_previous_drag_in(e.target);
                    break;
                case 'remove' :
                    this.remove_drag_from_drop(e.target);
                    break;
            }
            e.preventDefault();
        },
        place_next_drag_in : function (drop) {
            this.choose_next_choice_for_drop(drop, 1);
        },
        place_previous_drag_in : function (drop) {
            this.choose_next_choice_for_drop(drop, -1);
        },
        choose_next_choice_for_drop : function (drop, direction) {
            var next;
            var groupno = this.get_group(drop);
            var current = this.current_choice_in_drop(drop);
            var unplaceddragsingroup = Y.all(this.selectors.unplaced_drags_in_group(groupno));
            if (0 === current) {
                if (direction === 1) {
                    next = 1;
                } else {
                    var lastdrag = unplaceddragsingroup.pop();
                    next = this.get_choice(lastdrag);
                }
            } else {
                next = current + direction;
            }
            var drag;
            do {
                drag = Y.one(this.selectors.unplaced_drags_for_choice_in_group(next, groupno));
                if (Y.one(this.selectors.drags_for_choice_in_group(next, groupno)) === null) {
                    this.remove_drag_from_drop(drop);
                    return;
                }
                next = next + direction;
            } while (drag === null);
            this.place_drag_in_drop(drag, drop);
        },
        current_choice_in_drop : function(drop) {
            var inputid = this.get('inputids')[this.get_place(drop)];
            var inputnode = Y.one('input#'+inputid);
            return +inputnode.get('value');
        }
    }, {
        NAME : DDWTOSDDNAME,
        ATTRS : {
            readonly : {value : false},
            topnode : {value : null},
            inputids : {value : null}
        }
    });
    Y.Event.define('dragchange', {
        // Webkit and IE repeat keydown when you hold down arrow keys.
        // Opera links keypress to page scroll; others keydown.
        // Firefox prevents page scroll via preventDefault() on either
        // keydown or keypress.
        _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

        _keys: {
            '32': 'next',
            '37': 'previous',
            '38': 'previous',
            '39': 'next',
            '40': 'next',
            '27': 'remove'
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
    M.qtype_ddwtos = M.qtype_ddwtos || {};
    M.qtype_ddwtos.init_question = function(config) {
        return new DDWTOS_DD(config);
    }
}, '@VERSION@', {
      requires:['node', 'dd', 'dd-drop', 'dd-constrain']
});
