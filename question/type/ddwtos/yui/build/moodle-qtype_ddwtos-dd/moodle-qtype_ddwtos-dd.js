YUI.add('moodle-qtype_ddwtos-dd', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript code for the ddwtos question type.
 *
 * @package    qtype
 * @subpackage ddwtos
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var DDWTOSDDNAME = 'ddwtos_dd';
var DDWTOS_DD = function() {
    DDWTOS_DD.superclass.constructor.apply(this, arguments);
};
/**
 * This is the class for ddwtos question rendering.
 * A DDWTOS_DD class is created for each question.
 */
Y.extend(DDWTOS_DD, Y.Base, {
    selectors : null,
    touchscrolldisable: null,
    initializer : function() {
        var pendingid = 'qtype_ddwtos-' + Math.random().toString(36).slice(2); // Random string.
        M.util.js_pending(pendingid);
        this.selectors = this.css_selectors(this.get('topnode'));
        this.set_padding_sizes_all();
        this.clone_drag_items();
        this.initial_place_of_drag_items();
        this.make_drop_zones();
        if (!this.get('readonly')) {
            Y.later(500, this, this.position_drag_items, [pendingid, true]);
        } else {
            Y.later(500, this, this.position_drag_items, [pendingid, 3]);
            Y.one('window').on('resize', function() {
                this.position_drag_items(pendingid);
            }, this);
        }
    },
    /**
     * put all our selectors in the same place so we can quickly find and change them later
     * if the structure of the document changes.
     */
    css_selectors : function(topnode) {
        return {
            top_node : function() {
                return topnode;
            },
            drag_container : function() {
                return topnode + ' div.drags';
            },
            drags : function() {
                return this.drag_container() + ' span.drag';
            },
            drag : function(no) {
                return this.drags() + '.no' + no;
            },
            drags_in_group : function(groupno) {
                return this.drags() + '.group' + groupno;
            },
            unplaced_drags_in_group : function(groupno) {
                return this.drags_in_group(groupno) + '.unplaced';
            },
            drags_for_choice_in_group : function(choiceno, groupno) {
                return this.drags_in_group(groupno) + '.choice' + choiceno;
            },
            unplaced_drags_for_choice_in_group : function(choiceno, groupno) {
                return this.unplaced_drags_in_group(groupno) + '.choice' + choiceno;
            },
            drops : function() {
                return topnode + ' span.drop';
            },
            drop_for_place : function(placeno) {
                return this.drops() + '.place' + placeno;
            },
            drops_in_group : function(groupno) {
                return this.drops() + '.group' + groupno;
            },
            drag_homes : function() {
                return topnode + ' span.draghome';
            },
            drag_homes_group : function(groupno) {
                return topnode + ' .draggrouphomes' + groupno + ' span.draghome';
            },
            drag_home : function(groupno, choiceno) {
                return topnode + ' .draggrouphomes' + groupno + ' span.draghome.choice' + choiceno;
            },
            drops_group : function(groupno) {
                return topnode + ' span.drop.group' + groupno;
            }
        };
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
                maxwidth = Math.max(maxwidth, Math.ceil(item.get('offsetWidth')));
                maxheight = Math.max(maxheight, Math.ceil(item.get('offsetHeight')));
            }, this);
            maxwidth += 8;
            maxheight += 2;
            groupitems.each(function(item) {
                this.pad_to_width_height(item, maxwidth, maxheight);
            }, this);
            Y.all(this.selectors.drops_group(groupno)).each(function(item) {
                this.pad_to_width_height(item, maxwidth + 2, maxheight + 2);
            }, this);
        }
    },
    pad_to_width_height : function (node, width, height) {
        node.setStyle('width', width + 'px').setStyle('height', height + 'px')
                .setStyle('lineHeight', height + 'px');
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
            for (var i = 0; i < noofdrags; i++) {
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
        drag.addClass('no' + this.nextdragitemno);
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
            for (var index = 0; index < classesarr.length; index++) {
                var patt1 = new RegExp('^' + prefix + '([0-9])+$');
                if (patt1.test(classesarr[index])) {
                    var patt2 = new RegExp('([0-9])+$');
                    var match = patt2.exec(classesarr[index]);
                    return Number(match[0]);
                }
            }
        }
        throw 'Prefix "' + prefix + '" not found in class names.';
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
    initial_place_of_drag_items : function() {
        Y.all(this.selectors.drags()).addClass('unplaced');
        this.placed = [];
        for (var placeno in this.get('inputids')) {
            var inputid = this.get('inputids')[placeno];
            var inputnode = Y.one('input#' + inputid);
            var choiceno = Number(inputnode.get('value'));
            if (choiceno !== 0) {
                var drop = Y.one(this.selectors.drop_for_place(placeno));
                var groupno = this.get_group(drop);
                var drag =
                    Y.one(this.selectors.unplaced_drags_for_choice_in_group(choiceno, groupno));
                this.place_drag_in_drop(drag, drop);
                this.position_drag_item(drag);
            }
        }
    },
    make_draggable : function (drag) {
        new Y.DD.Drag({
            node: drag,
            groups: [this.get_group(drag)],
            dragMode: 'point'
        }).plug(Y.Plugin.DDConstrained, {constrain2node: this.selectors.top_node()});

        // Prevent scrolling whilst dragging on Adroid devices.
        this.prevent_touchmove_from_scrolling(drag);
    },

    /**
     * prevent_touchmove_from_scrolling allows users of touch screen devices to
     * use drag and drop and normal scrolling at the same time. I.e. when
     * touching and dragging a draggable item, the screen does not scroll, but
     * you can scroll by touching other area of the screen apart from the
     * draggable items.
     */
    prevent_touchmove_from_scrolling : function(drag) {
        var touchstart = (Y.UA.ie) ? 'MSPointerStart' : 'touchstart';
        var touchend = (Y.UA.ie) ? 'MSPointerEnd' : 'touchend';
        var touchmove = (Y.UA.ie) ? 'MSPointerMove' : 'touchmove';

        // Disable scrolling when touching the draggable items.
        drag.on(touchstart, function() {
            if (this.touchscrolldisable) {
                return; // Already disabled.
            }
            this.touchscrolldisable = Y.one('body').on(touchmove, function(e) {
                e = e || window.event;
                e.preventDefault();
            });
        }, this);

        // Allow scrolling after releasing the draggable items.
        drag.on(touchend, function() {
            if (this.touchscrolldisable) {
                this.touchscrolldisable.detach();
                this.touchscrolldisable = null;
            }
        }, this);
    },

    make_drop_zones : function () {
        Y.all(this.selectors.drops()).each(this.make_drop_zone, this);
    },
    make_drop_zone : function (drop) {
        var dropdd = new Y.DD.Drop({
            node: drop,
            groups: [this.get_group(drop)] });
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
        var inputnode = Y.one('input#' + inputid);
        if (drag !== null) {
            inputnode.set('value', this.get_choice(drag));
        } else {
            inputnode.set('value', '0');
        }
        for (var alreadytheredragno in this.placed) {
            if (this.placed[alreadytheredragno] === placeno) {
                delete this.placed[alreadytheredragno];
                var alreadytheredrag = Y.one(this.selectors.drag(alreadytheredragno));
                if (alreadytheredrag && alreadytheredrag.dd) {
                    alreadytheredrag.dd.detach('drag:start');
                }
            }
        }
        if (drag !== null) {
            this.placed[this.get_no(drag)] = placeno;
            if (drag.dd) {
                drag.dd.once('drag:start', function (e, inputnode, drag) {
                    inputnode.set('value', 0);
                    delete this.placed[this.get_no(drag)];
                    drag.addClass('unplaced');
                },this, inputnode, drag);
            }
        }
    },
    remove_drag_from_drop : function (drop) {
        this.place_drag_in_drop(null, drop);
    },

    /**
     * Postition, or reposition, all the drag items.
     * @param pendingid (optional) if given, then mark the js task complete after the
     * items are all positioned.
     * @param dotimeout (optional) if true, continually re-position the items so
     * they stay in place. Else, if an integer, reposition this many times before stopping.
     */
    position_drag_items : function (pendingid, dotimeout) {
       Y.all(this.selectors.drags()).each(this.position_drag_item, this);
       M.util.js_complete(pendingid);
       if (dotimeout === true || dotimeout > 0) {
           if (dotimeout !== true) {
               dotimeout -= 1;
           }
           Y.later(500, this, this.position_drag_items, [pendingid, dotimeout]);
       }
    },
    position_drag_item : function (drag) {
        if (!drag.hasClass('yui3-dd-dragging')) {
            if (!this.placed[this.get_no(drag)]) {
                var groupno = this.get_group(drag);
                var choiceno = this.get_choice(drag);
                var home = Y.one(this.selectors.drag_home(groupno, choiceno));
                drag.setXY(home.getXY());
                drag.addClass('unplaced');
            } else {
                var placeno = this.placed[this.get_no(drag)];
                var drop = Y.one(this.selectors.drop_for_place(placeno));
                drag.setXY([drop.getX() + 2, drop.getY() + 2]);
                drag.removeClass('unplaced');
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
        var inputnode = Y.one('input#' + inputid);
        return Number(inputnode.get('value'));
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
        '32': 'next',     // Space
        '37': 'previous', // Left arrow
        '38': 'previous', // Up arrow
        '39': 'next',     // Right arrow
        '40': 'next',     // Down arrow
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

M.qtype_ddwtos = M.qtype_ddwtos || {};
M.qtype_ddwtos.init_question = function(config) {
    return new DDWTOS_DD(config);
};

}, '@VERSION@', {"requires": ["node", "dd", "dd-drop", "dd-constrain"]});
