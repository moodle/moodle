YUI.add('moodle-qtype_ddimageortext-dd', function(Y) {
    var DDIMAGEORTEXTDDNAME = 'ddimageortext_dd';
    var DDIMAGEORTEXT_DD = function() {
        DDIMAGEORTEXT_DD.superclass.constructor.apply(this, arguments);
    }
    /**
     * This is the base class for the question rendering and question editing form code.
     */
    Y.extend(DDIMAGEORTEXT_DD, Y.Base, {
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
            var alldragsloaded = !this.doc.drag_item_homes().some(function(dragitemhome){
                //in 'some' loop returning true breaks the loop and is passed as return value from
                //'some' else returns false. Can be though of as equivalent to ||.
                if (dragitemhome.get('tagName') !== 'IMG'){
                    return false;
                }
                var done = (dragitemhome.get('complete'));
                if (waitforimageconstrain) {
                    done = done && dragitemhome.hasClass('constrained');
                }
                return !done;
            });
            if (bgdone && alldragsloaded) {
                if (this.polltimer !== null) {
                    this.polltimer.cancel();
                    this.polltimer = null;
                }
                this.doc.drag_item_homes().detach('load', this.poll_for_image_load);
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
                drag_items : function() {
                    return dragitemsarea.all('.drag');
                },
                drop_zones : function() {
                    return topnode.all('div.dropzones div.dropzone');
                },
                drop_zone_group : function(groupno) {
                    return topnode.all('div.dropzones div.group' + groupno);
                },
                drag_items_cloned_from : function(dragitemno) {
                    return dragitemsarea.all('.dragitems'+dragitemno);
                },
                drag_item : function(draginstanceno) {
                    return dragitemsarea.one('.draginstance' + draginstanceno);
                },
                drag_items_in_group : function(groupno) {
                    return dragitemsarea.all('.drag.group' + groupno);
                },
                drag_item_homes : function() {
                    return dragitemsarea.all('.draghome');
                },
                bg_img : function() {
                    return topnode.one('.dropbackground');
                },
                load_bg_img : function (url) {
                    dropbgarea.setContent('<img class="dropbackground" src="'+ url +'"/>');
                    this.bg_img().on('load', this.on_image_load, this, 'bg_image');
                },
                add_or_update_drag_item_home : function (dragitemno, url, alt, group) {
                    var oldhome = this.drag_item_home(dragitemno);
                    var classes = 'draghome dragitemhomes'+dragitemno+' group'+group;
                    var imghtml = '<img class="'+classes+'" src="'+url+'" alt="'+alt+'" />';
                    var divhtml = '<div class="yui3-cssfonts '+classes+'">'+alt+'</div>';
                    if (oldhome === null) {
                        if (url) {
                            dragitemsarea.append(imghtml);
                        } else if (alt !== '') {
                            dragitemsarea.append(divhtml);
                        }
                    } else {
                        if (url) {
                            dragitemsarea.insert(imghtml, oldhome);
                        } else if (alt !== '') {
                            dragitemsarea.insert(divhtml, oldhome);
                        }
                        oldhome.remove(true);
                    }
                    var newlycreated = dragitemsarea.one('.dragitemhomes'+dragitemno);
                    if (newlycreated !== null) {
                        newlycreated.setData('groupno', group);
                        newlycreated.setData('dragitemno', dragitemno);
                    }
                },
                drag_item_home : function (dragitemno) {
                    return dragitemsarea.one('.dragitemhomes' + dragitemno);
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
                clone_new_drag_item : function (draginstanceno, dragitemno) {
                    var draghome = this.drag_item_home(dragitemno);
                    if (draghome === null) {
                        return null;
                    }
                    var drag = draghome.cloneNode(true);
                    drag.removeClass('dragitemhomes' + dragitemno);
                    drag.addClass('dragitems' + dragitemno);
                    drag.addClass('draginstance' + draginstanceno);
                    drag.removeClass('draghome');
                    drag.addClass('drag');
                    drag.setStyles({'visibility': 'visible', 'position' : 'absolute'});
                    drag.setData('draginstanceno', draginstanceno);
                    drag.setData('dragitemno', dragitemno);
                    draghome.get('parentNode').appendChild(drag);
                    return drag;
                },
                draggable_for_question : function (drag, group, choice) {
                    var dd = new Y.DD.Drag({
                        node: drag,
                        dragMode: 'intersect'
                    }).plug(Y.Plugin.DDConstrained, {constrain2node: topnode});

                    drag.setData('group', group);
                    drag.setData('choice', choice);

                },
                draggable_for_form : function (drag) {
                    var dd = new Y.DD.Drag({
                        node: drag,
                        dragMode: 'intersect'
                    }).plug(Y.Plugin.DDConstrained, {constrain2node: topnode});
                    dd.on('drag:end', function(e) {
                        var dragnode = e.target.get('node');
                        var draginstanceno = dragnode.getData('draginstanceno');
                        var gooddrop = dragnode.getData('gooddrop');
                        var endxy;

                        if (!gooddrop) {
                            mainobj.reset_drag_xy(draginstanceno);
                        } else {
                            endxy = [Math.round(e.pageX), Math.round(e.pageY)];
                            mainobj.set_drag_xy(draginstanceno, endxy);
                        }
                    }, this);
                    dd.on('drag:start', function(e) {
                        var drag = e.target;
                        drag.get('node').setData('gooddrop', false);
                    }, this);

                }

            }
        },

        update_padding_sizes_all : function () {
            for (var groupno = 1; groupno <= 8; groupno++) {
                this.update_padding_size_for_group(groupno);
            }
        },
        update_padding_size_for_group : function (groupno) {
            var groupitems = this.doc.top_node().all('.draghome.group'+groupno);
            if (groupitems.size() !== 0) {
                var maxwidth = 0;
                var maxheight = 0;
                groupitems.each(function(item){
                    maxwidth = Math.max(maxwidth, item.get('clientWidth'));
                    maxheight = Math.max(maxheight, item.get('clientHeight'));
                }, this);
                groupitems.each(function(item) {
                    var margintopbottom = Math.round((10 + maxheight - item.get('clientHeight')) / 2);
                    var marginleftright = Math.round((10 + maxwidth - item.get('clientWidth')) / 2);
                    item.setStyle('padding', margintopbottom+'px '+marginleftright+'px '
                                            +margintopbottom+'px '+marginleftright+'px');
                }, this);
                this.doc.drop_zone_group(groupno).setStyles({'width': maxwidth + 10,
                                                                'height': maxheight + 10});
            }
        },
        convert_to_window_xy : function (bgimgxy) {
            return [+bgimgxy[0] + this.doc.bg_img().getX(),
                    +bgimgxy[1] + this.doc.bg_img().getY()];
        }
    }, {
        NAME : DDIMAGEORTEXTDDNAME,
        ATTRS : {
            drops : {value : null},
            readonly : {value : false},
            topnode : {value : null}
        }
    });
    M.qtype_ddimageortext = M.qtype_ddimageortext || {};
    M.qtype_ddimageortext.dd_base_class = DDIMAGEORTEXT_DD;

    var DDIMAGEORTEXTQUESTIONNAME = 'ddimageortext_question';
    var DDIMAGEORTEXT_QUESTION = function() {
        DDIMAGEORTEXT_QUESTION.superclass.constructor.apply(this, arguments);
    };
    /**
     * This is the code for question rendering.
     */
    Y.extend(DDIMAGEORTEXT_QUESTION, M.qtype_ddimageortext.dd_base_class, {
        initializer : function(params) {
            this.doc = this.doc_structure(this);
            this.poll_for_image_load(null, false, 0, this.create_all_drag_and_drops);
            this.doc.bg_img().after('load', this.poll_for_image_load, this,
                                                    false, 0, this.create_all_drag_and_drops);
            this.doc.drag_item_homes().after('load', this.poll_for_image_load, this,
                                                    false, 0, this.create_all_drag_and_drops);
            Y.later(500, this, this.reposition_drags_for_question, [], true);
        },
        create_all_drag_and_drops : function () {
            this.init_drops();
            this.update_padding_sizes_all();
            var i = 0;
            this.doc.drag_item_homes().each(function(dragitemhome){
                var dragitemno =
                    +this.doc.get_classname_numeric_suffix(dragitemhome, 'dragitemhomes');
                var choice = +this.doc.get_classname_numeric_suffix(dragitemhome, 'choice');
                var group = +this.doc.get_classname_numeric_suffix(dragitemhome, 'group')
                var groupsize = this.doc.drop_zone_group(group).size();
                var dragnode = this.doc.clone_new_drag_item(i, dragitemno);
                i++;
                if (!this.get('readonly')) {
                    this.doc.draggable_for_question(dragnode, group, choice);
                }
                if (dragnode.hasClass('infinite')) {
                    var dragstocreate = groupsize - 1;
                    while (dragstocreate > 0) {
                        dragnode = this.doc.clone_new_drag_item(i, dragitemno);
                        i++;
                        if (!this.get('readonly')) {
                            this.doc.draggable_for_question(dragnode, group, choice);
                        }
                        dragstocreate--;
                    }
                }
            }, this);
            this.reposition_drags_for_question();
            if (!this.get('readonly')) {
                this.doc.drop_zones().set('tabIndex', 0);
                this.doc.drop_zones().each(
                    function(v){
                        v.on('dragchange', this.drop_zone_key_press, this);
                    }
                , this);
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
            this.reposition_drags_for_question();
        },
        place_next_drag_in : function (drop) {
            this.search_for_unplaced_drop_choice(drop, 1);
        },
        place_previous_drag_in : function (drop) {
            this.search_for_unplaced_drop_choice(drop, -1);
        },
        search_for_unplaced_drop_choice : function (drop, direction) {
            var next;
            var current = this.current_drag_in_drop(drop);
            if ('' === current) {
                if (direction == 1) {
                    next = 1;
                } else {
                    next = 1;
                    var groupno = drop.getData('group');
                    this.doc.drag_items_in_group(groupno).each(function(drag) {
                        next = Math.max(next, drag.getData('choice'));
                    }, this);
                }
            } else {
                next = + current + direction;
            }
            var drag;
            do {
                if (this.get_choices_for_drop(next, drop).size() === 0){
                    this.remove_drag_from_drop(drop);
                    return;
                } else {
                    drag = this.get_unplaced_choice_for_drop(next, drop);
                }
                next = next + direction;
            } while (drag === null);
            this.place_drag_in_drop(drag, drop);
        },
        current_drag_in_drop : function (drop) {
            var inputid = drop.getData('inputid');
            var inputnode = Y.one('input#'+inputid);
            return inputnode.get('value');
        },
        remove_drag_from_drop : function (drop) {
            this.place_drag_in_drop(null, drop);
        },
        place_drag_in_drop : function (drag, drop) {
            var inputid = drop.getData('inputid');
            var inputnode = Y.one('input#'+inputid);
            if (drag !== null) {
                inputnode.set('value', drag.getData('choice'));
            } else {
                inputnode.set('value', '');
            }
        },
        reposition_drags_for_question : function() {
            this.doc.drag_items().removeClass('placed');
            this.doc.drag_items().each (function (dragitem) {
                if (dragitem.dd !== undefined) {
                    dragitem.dd.detachAll('drag:start');
                }
            }, this);
            this.doc.drop_zones().each(function(dropzone) {
                var relativexy = dropzone.getData('xy');
                dropzone.setXY(this.convert_to_window_xy(relativexy));
                var inputcss = 'input#' + dropzone.getData('inputid');
                var input = this.doc.top_node().one(inputcss);
                var choice = input.get('value');
                if (choice !== "") {
                    var dragitem = this.get_unplaced_choice_for_drop(choice, dropzone);
                    if (dragitem !== null) {
                        dragitem.setXY(dropzone.getXY());
                        dragitem.addClass('placed');
                        if (dragitem.dd !== undefined) {
                            dragitem.dd.once('drag:start', function (e, input) {
                                input.set('value', '');
                                e.target.get('node').removeClass('placed');
                            },this, input);
                        }
                    }
                }
            }, this);
            this.doc.drag_items().each(function(dragitem) {
                if (!dragitem.hasClass('placed') && !dragitem.hasClass('yui3-dd-dragging')) {
                    var dragitemhome = this.doc.drag_item_home(dragitem.getData('dragitemno'));
                    dragitem.setXY(dragitemhome.getXY());
                }
            }, this);
        },
        get_choices_for_drop : function(choice, drop) {
            var group = drop.getData('group');
            var dragitem = null;
            var dragitems = this.doc.top_node()
                                .all('div.dragitemgroup'+group+' .choice'+choice+'.drag');
            return dragitems;
        },
        get_unplaced_choice_for_drop : function(choice, drop) {
            var dragitems = this.get_choices_for_drop(choice, drop);
            var dragitem = null;
            if (dragitems.some(function (d) {
                if (!d.hasClass('placed') && !d.hasClass('yui3-dd-dragging')) {
                    dragitem = d;
                    return true;
                } else {
                    return false;
                }
            }));
            return dragitem;
        },
        init_drops : function () {
            var dropareas = this.doc.top_node().one('div.dropzones');
            var groupnodes = {};
            for (var groupno =1; groupno <= 8; groupno++) {
                var groupnode = Y.Node.create('<div class = "dropzonegroup'+groupno+'"></div>');
                dropareas.append(groupnode);
                groupnodes[groupno] = groupnode;
            }
            for (var dropno in this.get('drops')){
                var drop = this.get('drops')[dropno];
                var nodeclass = 'dropzone group'+drop.group+' place'+dropno;
                var title = drop.text.replace('"', '\"');
                var dropnodehtml = '<div title="'+ title +'" class="'+nodeclass+'">&nbsp;</div>';
                var dropnode = Y.Node.create(dropnodehtml);
                groupnodes[drop.group].append(dropnode);
                dropnode.setStyles({'opacity': 0.5});
                dropnode.setData('xy', drop.xy);
                dropnode.setData('place', dropno);
                dropnode.setData('inputid', drop.fieldname.replace(':', '_'));
                dropnode.setData('group', drop.group);
                var dropdd = new Y.DD.Drop({
                      node: dropnode});
                dropdd.on('drop:hit', function(e) {
                    var drag = e.drag.get('node');
                    var drop = e.drop.get('node');
                    if (+drop.getData('group') === drag.getData('group')){
                        this.place_drag_in_drop(drag, drop);
                    }
                }, this);
            };
        }
    }, {NAME : DDIMAGEORTEXTQUESTIONNAME, ATTRS : {}});

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
    M.qtype_ddimageortext.init_question = function(config) {
        return new DDIMAGEORTEXT_QUESTION(config);
    }
}, '@VERSION@', {
      requires:['node', 'dd', 'dd-drop', 'dd-constrain']
});
