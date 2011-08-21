YUI.add('moodle-qtype_ddimagetoimage-dd', function(Y) {
    var DDIMAGETOIMAGEDDNAME = 'ddimagetoimage_dd';
    var DDIMAGETOIMAGE_DD = function() {
        DDIMAGETOIMAGE_DD.superclass.constructor.apply(this, arguments);
    }
    Y.extend(DDIMAGETOIMAGE_DD, Y.Base, {
        doc : null,
        polltimer : null,
        poll_for_image_load : function (e, waitforimageconstrain, pause, doafterwords) {
            var bgdone = this.doc.bg_img().get('complete');
            if (waitforimageconstrain) {
                bgdone = bgdone && this.doc.bg_img().hasClass('constrained');
            }
            var alldragsloaded = !this.doc.drag_image_homes().some(function(dragimagehome){
                //in 'some' loop returning true breaks the loop and is passed as return value from 
                //'some' else returns false. Can be though of as equivalent to ||.
                var done = (dragimagehome.get('complete'));
                if (waitforimageconstrain) {
                    done = done && dragimagehome.hasClass('constrained');
                }
                return !done;
            });
            if (alldragsloaded && alldragsloaded) {
                if (this.polltimer !== null) {
                    this.polltimer.cancel();
                    this.polltimer = null;
                }
                this.doc.drag_image_homes().detach('load', this.poll_for_image_load);
                this.doc.bg_img().detach('load', this.poll_for_image_load);
                if (pause !== 0) {
                    Y.later(pause, this, doafterwords);
                } else {
                    doafterwords.call(this);
                }
            } else if (this.polltimer === null) {
                var pollarguments = [null, waitforimageconstrain, pause, doafterwords];
                this.polltimer =
                            Y.later(500, this, this.poll_for_image_load, pollarguments, true);
            }
        },
        /**
         * Object to encapsulate operations on dd area.
         */
        doc_structure : function (mainobj) {
            var topnode = Y.one(this.get('topnode'));
            var dragimagesarea = topnode.one('div.dragitems');
            var dropbgarea = topnode.one('div.droparea');
            return {
                top_node : function() {
                    return topnode;
                },
                drag_images : function() {
                    return dragimagesarea.all('img.drag');
                },
                drop_zones : function() {
                    return topnode.all('div.dropzones div.dropzone');
                },
                drop_zone_group : function(groupno) {
                    return topnode.all('div.dropzones div.group' + groupno);
                },
                drag_images_cloned_from : function(dragimageno) {
                    return dragimagesarea.all('img.dragimages'+dragimageno);
                },
                drag_image : function(draginstanceno) {
                    return dragimagesarea.one('img.draginstance' + draginstanceno);
                },
                drag_image_homes : function() {
                    return dragimagesarea.all('img.draghome');
                },
                bg_img : function() {
                    return topnode.one('img.dropbackground');
                },
                load_bg_img : function (url) {
                    dropbgarea.setContent('<img class="dropbackground" src="'+ url +'"/>');
                    this.bg_img().on('load', this.on_image_load, this, 'bg_image');
                },
                add_or_update_drag_image_home : function (dragimageno, url, alt, group) {
                    var oldhome = this.drag_image_home(dragimageno);
                    var classes = 'draghome dragimagehomes'+dragimageno+' group'+group;
                    var imghtml = '<img class="'+classes+'" src="'+url+'" alt="'+alt+'" />';
                    if (oldhome === null) {
                        if (url) {
                            dragimagesarea.append(imghtml);
                        }
                    } else {
                        if (url) {
                            dragimagesarea.insert(imghtml, oldhome);
                        }
                        oldhome.remove(true);
                    }
                    var newlycreated = dragimagesarea.one('img.dragimagehomes'+dragimageno);
                    if (newlycreated !== null) {
                        newlycreated.setData('groupno', group);
                        newlycreated.setData('dragimageno', dragimageno);
                    }
                },
                drag_image_home : function (dragimageno) {
                    return dragimagesarea.one('img.dragimagehomes' + dragimageno);
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
                clone_new_drag_image : function (draginstanceno, dragimageno) {
                    var draghome = this.drag_image_home(dragimageno);
                    if (draghome === null) {
                        return null;
                    }
                    var drag = draghome.cloneNode(true);
                    drag.removeClass('dragimagehomes' + dragimageno);
                    drag.addClass('dragimages' + dragimageno);
                    drag.addClass('draginstance' + draginstanceno);
                    drag.removeClass('draghome');
                    drag.addClass('drag');
                    drag.setStyles({'visibility': 'visible', 'position' : 'absolute'});
                    drag.setData('draginstanceno', draginstanceno);
                    drag.setData('dragimageno', dragimageno);
                    draghome.get('parentNode').appendChild(drag);
                    return drag;
                },
                draggable_for_question : function (drag, group, choice) {
                    var dd = new Y.DD.Drag({
                        node: drag,
                        dragMode: 'intersect'
                    }).plug(Y.Plugin.DDConstrained, {constrain2node: topnode});
                    
                    dd.on('drag:end', function(e) {
                        mainobj.reposition_drags_for_question();
                    }, this);
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
            var groupimages = this.doc.top_node().all('img.draghome.group'+groupno);
            if (groupimages.size() !== 0) {
                var maxwidth = 0;
                var maxheight = 0;
                groupimages.each(function(image){
                    maxwidth = Math.max(maxwidth, image.get('width'));
                    maxheight = Math.max(maxheight, image.get('height'));
                }, this);
                groupimages.each(function(image) {
                    var margintopbottom = Math.round((10 + maxheight - image.get('height')) / 2);
                    var marginleftright = Math.round((10 + maxwidth - image.get('width')) / 2);
                    image.setStyle('padding', margintopbottom+'px '+marginleftright+'px '
                                            +margintopbottom+'px '+marginleftright+'px');
                }, this);
                this.doc.drop_zone_group(groupno).setStyles({'width': maxwidth + 10,
                                                                'height': maxheight + 10});
            }
        },
        convert_to_window_xy : function (bgimgxy) {
            return [+bgimgxy[0] + this.doc.bg_img().getX() + 1,
                    +bgimgxy[1] + this.doc.bg_img().getY() + 1];
        }
    }, {
        NAME : DDIMAGETOIMAGEDDNAME,
        ATTRS : {
            drops : {value : null},
            readonly : {value : false},
            topnode : {value : null}
        }
    });
    M.qtype_ddimagetoimage = M.qtype_ddimagetoimage || {};
    M.qtype_ddimagetoimage.dd_base_class = DDIMAGETOIMAGE_DD;
  }, '@VERSION@', {
      requires:['node', 'dd', 'dd-drop', 'dd-constrain']
  });
