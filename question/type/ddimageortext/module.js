M.qtype_ddimagetoimage={
    Y : null,
    doc : null,
    fp : null,
    /**
     * Entry point when called from question editing form
     */
    init_form : function(Y, maxsizes) {
        this.Y = Y;
        this.fp = this.file_pickers();
        topnode = Y.one('fieldset#previewareaheader');
        topnode.append('<div class="ddarea"><div class="droparea"></div>'+
                '<div class="dragitems"></div>'+
                '<div class="dropzones"></div></div>');
        this.doc = this.doc_structure(Y, topnode, this, maxsizes);
        this.init_dd_area();
    },
    /**
     * Entry point when called from question renderer
     */
    init_question : function(Y, drops, topnodestr, readonly) {
        this.Y = Y;
        this.doc = this.doc_structure(Y, Y.one(topnodestr), this, null);
        this.init_drops(drops);
        var i = 0;
        this.doc.drag_item_homes().each(function(dragitemhome){
            var dragimageno = 
                +this.doc.get_classname_numeric_suffix(dragitemhome, 'dragimagehomes');
            var choice = +this.doc.get_classname_numeric_suffix(dragitemhome, 'choice');
            var group = +this.doc.get_classname_numeric_suffix(dragitemhome, 'group')
            var groupsize = this.doc.drop_zone_group(group).size();
            var dragnode = this.doc.clone_new_drag_item(i, dragimageno);
            i++;
            if (!readonly) {
                this.doc.draggable_for_question(dragnode, group, choice);
            }
            if (dragnode.hasClass('infinite')) {
                var dragstocreate = groupsize - 1;
                while (dragstocreate > 0) {
                    dragnode = this.doc.clone_new_drag_item(i, dragimageno);
                    i++;
                    if (!readonly) {
                        this.doc.draggable_for_question(dragnode, group, choice);
                    }
                    dragstocreate--;
                }
            }
            //put these here for easy refernece later
        }, this);
        this.doc.bg_img().on('load', this.delayed_redraw, this);
        this.doc.drag_item_homes().on('load', this.delayed_redraw, this);
        Y.on('windowresize', this.redraw, this);
    },
    delayed_redraw : function (e) {
        this.redraw();
        this.Y.later(2000, this, this.redraw);
    },
    redraw : function(e) {
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
                var group = dropzone.getData('group');
                var dragitem = this.doc.top_node()
                        .one('div.dragitemgroup'+group+' img.choice'+choice+'.drag:not(.placed)');
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
            if (!dragitem.hasClass('placed')) {
                var dragitemhome = this.doc.get_drag_image_home(dragitem.getData('dragimageno'));
                dragitem.setXY(dragitemhome.getXY());
            }
        }, this);
        this.update_padding_sizes_all();
    },
    init_drops : function (drops) {
        var dropareas = this.doc.top_node().one('div.dropzones');
        var groupnodes = {};
        for (var groupno =1; groupno <= 8; groupno++) {
            var groupnode = this.Y.Node.create('<div class = "dropzonegroup'+groupno+'"></div>');
            dropareas.append(groupnode);
            groupnodes[groupno] = groupnode;
        }
        for (var dropno in drops){
            var nodeclass = 'dropzone group'+drops[dropno].group+' place'+dropno;
            var title = drops[dropno].text.replace('"', '\"');
            var dropnodehtml = '<div title="'+ title +'" class="'+nodeclass+'">&nbsp;</div>';
            var dropnode = this.Y.Node.create(dropnodehtml);
            groupnodes[drops[dropno].group].append(dropnode);
            dropnode.setStyles({'opacity': 0.5});
            dropnode.setData('xy', drops[dropno].xy);
            dropnode.setData('place', dropno);
            dropnode.setData('inputid', drops[dropno].fieldname.replace(':', '_'));
            dropnode.setData('group', drops[dropno].group);
            var dropdd = new this.Y.DD.Drop({
                  node: dropnode});
            dropdd.on('drop:hit', function(e) {
                var drag = e.drag.get('node');
                var drop = e.drop.get('node');
                if (+drop.getData('group') === drag.getData('group')){
                    var inputid = drop.getData('inputid');
                    var inputnode = this.Y.one('input#'+inputid);
                    inputnode.set('value', drag.getData('choice'));
                }
            }, this);
        };
    },
    /**
     * Object to encapsulate operations on dd area.
     */
    doc_structure : function (Y, topnode, mainobj, maxsizes) {
        var dragitemsarea = topnode.one('div.dragitems');
        var dropbgarea = topnode.one('div.droparea');
        return {
            top_node : function() {
                return topnode;
            },
            drag_items : function() {
                return dragitemsarea.all('img.drag');
            },
            drop_zones : function() {
                return topnode.all('div.dropzones div.dropzone');
            },
            drop_zone_group : function(groupno) {
                return topnode.all('div.dropzones div.group' + groupno);
            },
            drag_items_cloned_from : function(dragimageno) {
                return dragitemsarea.all('img.dragimages'+dragimageno);
            },
            drag_item : function(draginstanceno) {
                return dragitemsarea.one('img.draginstance' + draginstanceno);
            },
            drag_item_homes : function() {
                return dragitemsarea.all('img.draghome');
            },
            bg_img : function() {
                return topnode.one('img.dropbackground');
            },
            load_bg_img : function (url) {
                dropbgarea.setContent('<img class="dropbackground" src="'+ url +'"/>');
                this.bg_img().on('load', this.on_image_load, this, 'bg_image');
            },
            add_or_update_drag_item_home : function (dragimageno, url, alt, group) {
                var oldhome = dragitemsarea.one('img.dragimagehomes'+dragimageno);
                var classes = 'draghome dragimagehomes'+dragimageno+' group'+group;
                var imghtml = '<img class="'+classes+'" src="'+url+'" alt="'+alt+'" />';
                if (oldhome === null) {
                    if (url) {
                        dragitemsarea.append(imghtml);
                    }
                } else {
                    if (url) {
                        dragitemsarea.insert(imghtml, oldhome);
                    }
                    oldhome.remove(true);
                }
                var newlycreated = dragitemsarea.one('img.dragimagehomes'+dragimageno);
                if (newlycreated !== null) {
                    newlycreated.setData('groupno', group);
                    newlycreated.setData('dragimageno', dragimageno);
                    newlycreated.on('load', this.on_image_load, this, 'drag_image');
                }
            },
            on_image_load : function (e, imagetype) {
                if (maxsizes !== null) {
                    var maxsize = maxsizes[imagetype];
                    var reduceby = Math.max(e.target.get('width') / maxsize.width,
                                            e.target.get('height') / maxsize.height);
                    if (reduceby > 1) {
                        e.target.set('width', Math.floor(e.target.get('width') / reduceby));
                    }
                }
            },
            get_drag_image_home : function (dragimageno) {
                return dragitemsarea.one('img.dragimagehomes' + dragimageno);
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
            clone_new_drag_item : function (draginstanceno, dragimageno) {
                var draghome = this.get_drag_image_home(dragimageno);
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
                
//                dd.on('drag:start', function(e) {
//                    var drag = e.target.get('node');
//                    var inputid = drag.getData('placedonid');
//                    if (inputid !== undefined || inputid !== null) {
//                        var inputnode = this.Y.one('input#'+inputid);
//                        if (inputnode !== null) {
//                            inputnode.set('value', '');
//                        }
//                    }
//                    drag.setData('placedonid', '');
//                }, this);
                dd.on('drag:end', function(e) {
                    mainobj.redraw();
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
    
    file_pickers : function () {
        var draftitemidstoname;
        var nametoparentnode;
        if (draftitemidstoname === undefined) {
            draftitemidstoname = {};
            nametoparentnode = {};
            var filepickers = this.Y.all('form.mform input.filepickerhidden');
            filepickers.each(function(filepicker, k, items) {
                draftitemidstoname[filepicker.get('value')] = filepicker.get('name');
                nametoparentnode[filepicker.get('name')] = filepicker.get('parentNode');
            }, this);
        }
        var toreturn = {
            file : function (name) {
                var parentnode = nametoparentnode[name];
                var fileanchor = parentnode.one('div.filepicker-filelist a');
                if (fileanchor) {
                    return fileanchor.get('href');
                } else {
                    return null;
                }
            },
            name : function (draftitemid) {
                return draftitemidstoname[draftitemid];
            }
        }
        return toreturn;
    },
    /**
     * Low level operations on form.
     */
    form : {
        to_name_with_index : function(name, indexes) {
            indexes = indexes || [];
            var indexstring = name;
            for (var i=0; i < indexes.length; i++) {
                indexstring = indexstring + '[' + indexes[i] + ']';
            }
            return indexstring;
        },
        get_form_value : function(name, indexes) {
            var el = document.forms[0].elements[this.to_name_with_index(name, indexes)];
            if (el.type === 'checkbox') {
                return el.checked;
            } else {
                return el.value;
            }
        },
        set_form_value : function(name, indexes, value) {
            var el = document.forms[0].elements[this.to_name_with_index(name, indexes)];
            if (el.type === 'checkbox') {
                el.checked = value;
            } else {
                el.value = value;
            }
        },
        from_name_with_index : function(name) {
            var toreturn = {};
            toreturn.indexes = [];
            var bracket = name.indexOf('[');
            toreturn.name = name.substring(0, bracket);
            while (bracket !== -1) {
                var end = name.indexOf(']', bracket + 1);
                toreturn.indexes.push(name.substring(bracket + 1, end));
                bracket = name.indexOf('[', end + 1);
            }
            return toreturn;
        }
    },

    init_dd_area : function() {
        //set up drag items homes
        for (var i=0; i < this.form.get_form_value('noimages'); i++) {
            var url = this.fp.file(this.form.to_name_with_index('dragitem', [i]));
            this.doc.add_or_update_drag_item_home(i, url,
                                this.form.get_form_value('drags', [i, 'draglabel']),
                                this.form.get_form_value('drags', [i, 'draggroup']));
        }

        //set up drop zones
        for (var i=0; i < this.form.get_form_value('nodropzone'); i++) {
            var dragimageno = this.form.get_form_value('drops', [i, 'choice']);
            if (dragimageno !== '0') {
                var drag = this.doc.clone_new_drag_item(i, dragimageno - 1);
                if (drag !== null) {
                    this.doc.draggable_for_form(drag);
                }
            }
        }
        
        this.setup_form_events();

        this.doc.load_bg_img(this.fp.file(this.form.to_name_with_index('bgimage')));
        
        var drop = new this.Y.DD.Drop({
            node: this.doc.bg_img()
        });
        //Listen for a drop:hit on this target
        drop.on('drop:hit', function(e) {
            e.drag.get('node').setData('gooddrop', true);
        });

        this.Y.on('windowresize', this.reposition_drags, this);
        
        this.doc.bg_img().after('load', function(e) {
            this.Y.later(3000, this, function (e) {
                this.reposition_drags();
                this.update_padding_sizes_all();
            }, this);
        }, this);

    },
    setup_form_events : function () {
        //events triggered by changes to form data
        this.Y.all('fieldset#dropzoneheader input').on('blur', function (e){
            var name = e.target.getAttribute('name');
            var nameparts = this.form.from_name_with_index(name);
            this.reposition_drag(nameparts.indexes[0]);
        }, this);

        this.Y.all('fieldset#dropzoneheader select').on('change', function (e){
            var name = e.target.getAttribute('name');
            var nameparts = this.form.from_name_with_index(name);
            var draginstanceno = nameparts.indexes[0];
            this.update_drag_instance(draginstanceno);
        }, this);
        
        this.Y.all('fieldset#dropzoneheader select').on('change', function (e){
            var name = e.target.getAttribute('name');
            var nameparts = this.form.from_name_with_index(name);
            var draginstanceno = nameparts.indexes[0];
            this.update_drag_instance(draginstanceno);
        }, this);
        
        for (var i=0; i < this.form.get_form_value('noimages'); i++) {
            this.Y.all('fieldset#draggableimageheader_'+i+' select')
                                                .on('change', this.on_drag_group_change, this);
        }
        
        this.Y.after(this.after_image_upload, M.form_filepicker, 'callback', this);

    },
    on_drag_group_change : function (e){
        var name = e.target.getAttribute('name');
        var nameparts = this.form.from_name_with_index(name);
        var dragimageno = nameparts.indexes[0];
        this.update_drag_image_home(dragimageno);
    },
    after_image_upload : function (params) {
        var url = params['url'];
        var itemname = this.fp.name(params.id);
        var itemnameparts = this.form.from_name_with_index(itemname);
        if (itemnameparts.name === 'dragitem') {
            var dragimageno = itemnameparts.indexes[0];
            this.update_drag_image_home(dragimageno);
        } else {
            this.doc.load_bg_img(url);
            this.doc.bg_img().after('load', this.reposition_drags, this);
        }
    },
    update_padding_sizes_all : function () {
        for (var groupno = 1; groupno <= 8; groupno++) {
            this.update_padding_size_for_group(groupno);
        }
    },
    update_padding_size_for_group : function (groupno) {
        var groupimages = this.doc.top_node().all('img.group'+groupno);
        var maxwidth = 0;
        var maxheight = 0;
        groupimages.each(function(image){
            maxwidth = Math.max(maxwidth, image.get('width'));
            maxheight = Math.max(maxheight, image.get('height'));
        }, this);
        groupimages.each(function(image) {
            var margintopbottom = Math.round((10 + maxheight - image.get('height')) / 2);
            var marginleftright = Math.round((10 + maxwidth - image.get('width')) / 2);
            image.setStyle('padding', margintopbottom+'px '+marginleftright+'px');
        }, this);
        this.doc.drop_zone_group(groupno).setStyles({'width': maxwidth + 10,
                                                        'height': maxheight + 10});
    },
    update_drag_image_home : function (dragimageno) {
        dragimageno = +dragimageno;
        var url = this.fp.file(this.form.to_name_with_index('dragitem', [dragimageno]));
        this.doc.add_or_update_drag_item_home(dragimageno, url, 
                this.form.get_form_value('drags', [dragimageno, 'draglabel']),
                this.form.get_form_value('drags', [dragimageno, 'draggroup']));
        for (var i=0; i < this.form.get_form_value('nodropzone'); i++) {
            var dropdragimageno = this.form.get_form_value('drops', [i, 'choice']) - 1;
            if (dropdragimageno === dragimageno) {
                this.update_drag_instance(i);
            }
        }
    },
    update_drag_instance : function(draginstanceno) {
        var drag = this.doc.drag_item(draginstanceno);
        var dragimageno = this.form.get_form_value('drops', [draginstanceno, 'choice']) - 1;
        if (drag !== null) {
            drag.remove(true);
        }
        drag = this.doc.clone_new_drag_item(draginstanceno, dragimageno);
        this.doc.draggable_for_form(drag);
        if (drag !== null) {
            drag.on('load', function(e){
                this.reposition_drag(draginstanceno);
            }, this);
        }
    },
    reposition_drags : function() {
        this.doc.drag_items().each(function (drag) {
            var draginstanceno = drag.getData('draginstanceno');
            this.reposition_drag(draginstanceno);
        }, this);
    },
    reposition_drag : function (draginstanceno) {
        this.update_padding_sizes_all();
        var drag = this.doc.drag_item(draginstanceno);
        if (null !== drag) {
            var fromform = [this.form.get_form_value('drops', [draginstanceno, 'xleft']),
                            this.form.get_form_value('drops', [draginstanceno, 'ytop'])];
            if (fromform[0] == '' && fromform[1] == '') {
                var dragimageno = drag.getData('dragimageno'); 
                drag.setXY(this.doc.get_drag_image_home(dragimageno).getXY());
            } else {
                drag.setXY(this.convert_to_window_xy(this.constrain_xy(draginstanceno, fromform)));
            }
        }
    },
    set_drag_xy : function (draginstanceno, xy) {
        xy = this.constrain_xy(draginstanceno, this.convert_to_bg_img_xy(xy));
        this.form.set_form_value('drops', [draginstanceno, 'xleft'], Math.round(xy[0]));
        this.form.set_form_value('drops', [draginstanceno, 'ytop'], Math.round(xy[1]));
        this.reposition_drag(draginstanceno);
    },
    reset_drag_xy : function (draginstanceno) {
        this.form.set_form_value('drops', [draginstanceno, 'xleft'], '');
        this.form.set_form_value('drops', [draginstanceno, 'ytop'], '');
        this.reposition_drag(draginstanceno);
    },

    //make sure xy value is not out of bounds of bg image
    constrain_xy : function (draginstanceno, bgimgxy) {
        var drag = this.doc.drag_item(draginstanceno);
        var xleftconstrained =
            Math.min(bgimgxy[0], this.doc.bg_img().get('width') - drag.get('offsetWidth'));
        var ytopconstrained =
            Math.min(bgimgxy[1], this.doc.bg_img().get('height') - drag.get('offsetHeight'));
        xleftconstrained = Math.max(xleftconstrained, 0);
        ytopconstrained = Math.max(ytopconstrained, 0);
        return [xleftconstrained, ytopconstrained];
    },
    convert_to_bg_img_xy : function (windowxy) {
        return [windowxy[0] - this.doc.bg_img().getX(),
                windowxy[1] - this.doc.bg_img().getY()];
    },
    convert_to_window_xy : function (bgimgxy) {
        return [+bgimgxy[0] + this.doc.bg_img().getX() + 1,
                +bgimgxy[1] + this.doc.bg_img().getY() + 1];
    }

}