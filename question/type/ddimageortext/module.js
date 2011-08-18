M.qtype_ddimagetoimage={
    Y : null,
    doc : null,
    drops : null,
    readonly : null,
    /**
     * Entry point when called from question renderer
     */
    init_question : function(Y, drops, topnodestr, readonly) {
        this.Y = Y;
        this.drops = drops;
        this.readonly = readonly;
        this.doc = this.doc_structure(Y, Y.one(topnodestr), this, null);
        this.poll_for_image_load(null, 1000, this.create_all_drag_and_drops);
        this.doc.bg_img()
            .after('load', this.poll_for_image_load, this, 1000, this.create_all_drag_and_drops);
        this.doc.drag_image_homes()
            .after('load', this.poll_for_image_load, this, 1000, this.create_all_drag_and_drops);
        this.Y.on('windowresize', this.redraw, this);
    },
    polltimer : null,
    poll_for_image_load : function (e, pause, doafterwords) {
        if (this.doc.bg_img().get('complete')) {
            var notalldragsloaded = this.doc.drag_image_homes().some(function(dragimagehome){
                !dragimagehome.get('complete');
            });
            if (!notalldragsloaded) {
                if (this.polltimer !== null) {
                    this.polltimer.cancel();
                    this.polltimer = null;
                }
                this.doc.drag_image_homes().detach('load', this.poll_for_image_load);
                this.doc.bg_img().detach('load', this.poll_for_image_load);
                if (pause !== 0) {
                    this.Y.later(pause, this, doafterwords);
                } else {
                    doafterwords.call(this);
                }
            } else if (this.polltimer === null) {
                this.polltimer = this.Y.later(500, this.poll_for_image_load, this, true);
            }
        }
    },
    create_all_drag_and_drops : function () {
        this.init_drops();
        var i = 0;
        this.doc.drag_image_homes().each(function(dragimagehome){
            var dragimageno = 
                +this.doc.get_classname_numeric_suffix(dragimagehome, 'dragimagehomes');
            var choice = +this.doc.get_classname_numeric_suffix(dragimagehome, 'choice');
            var group = +this.doc.get_classname_numeric_suffix(dragimagehome, 'group')
            var groupsize = this.doc.drop_zone_group(group).size();
            var dragnode = this.doc.clone_new_drag_image(i, dragimageno);
            i++;
            if (!this.readonly) {
                this.doc.draggable_for_question(dragnode, group, choice);
            }
            if (dragnode.hasClass('infinite')) {
                var dragstocreate = groupsize - 1;
                while (dragstocreate > 0) {
                    dragnode = this.doc.clone_new_drag_image(i, dragimageno);
                    i++;
                    if (!this.readonly) {
                        this.doc.draggable_for_question(dragnode, group, choice);
                    }
                    dragstocreate--;
                }
            }
        }, this);
        this.update_padding_sizes_all();
        this.redraw();
    },
    redraw : function() {
        this.doc.drag_images().removeClass('placed');
        this.doc.drag_images().each (function (dragimage) {
            if (dragimage.dd !== undefined) {
                dragimage.dd.detachAll('drag:start');
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
                var dragimage = null;
                var dragimages = this.doc.top_node()
                                    .all('div.dragitemgroup'+group+' img.choice'+choice+'.drag');
                dragimages.some(function (d) {
                    if (!d.hasClass('placed')) {
                        dragimage = d;
                        return true;
                    } else {
                        return false;
                    }
                });
                if (dragimage !== null) {
                    dragimage.setXY(dropzone.getXY());
                    dragimage.addClass('placed');
                    if (dragimage.dd !== undefined) {
                        dragimage.dd.once('drag:start', function (e, input) {
                            input.set('value', '');
                            e.target.get('node').removeClass('placed');
                        },this, input);
                    }
                }
            }
        }, this);
        this.doc.drag_images().each(function(dragimage) {
            if (!dragimage.hasClass('placed')) {
                var dragimagehome = this.doc.drag_image_home(dragimage.getData('dragimageno'));
                dragimage.setXY(dragimagehome.getXY());
            }
        }, this);
    },
    init_drops : function () {
        var dropareas = this.doc.top_node().one('div.dropzones');
        var groupnodes = {};
        for (var groupno =1; groupno <= 8; groupno++) {
            var groupnode = this.Y.Node.create('<div class = "dropzonegroup'+groupno+'"></div>');
            dropareas.append(groupnode);
            groupnodes[groupno] = groupnode;
        }
        for (var dropno in this.drops){
            var nodeclass = 'dropzone group'+this.drops[dropno].group+' place'+dropno;
            var title = this.drops[dropno].text.replace('"', '\"');
            var dropnodehtml = '<div title="'+ title +'" class="'+nodeclass+'">&nbsp;</div>';
            var dropnode = this.Y.Node.create(dropnodehtml);
            groupnodes[this.drops[dropno].group].append(dropnode);
            dropnode.setStyles({'opacity': 0.5});
            dropnode.setData('xy', this.drops[dropno].xy);
            dropnode.setData('place', dropno);
            dropnode.setData('inputid', this.drops[dropno].fieldname.replace(':', '_'));
            dropnode.setData('group', this.drops[dropno].group);
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

    update_padding_sizes_all : function () {
        for (var groupno = 1; groupno <= 8; groupno++) {
            this.update_padding_size_for_group(groupno);
        }
    },
    update_padding_size_for_group : function (groupno) {
        var groupimages = this.doc.top_node().all('img.group'+groupno);
        if (groupimages.size() !== 0) {
            var maxwidth = 0;
            var maxheight = 0;
            groupimages.each(function(image){
                maxwidth = Math.max(maxwidth, image.get('width'));
                maxheight = Math.max(maxheight, image.get('height'));
            }, this);
            console.log('groupno : '+groupno);
            console.log('maxwidth : '+maxwidth);
            console.log('maxheight : '+maxheight);
            groupimages.each(function(image) {
                var margintopbottom = Math.round((10 + maxheight - image.get('height')) / 2);
                var marginleftright = Math.round((10 + maxwidth - image.get('width')) / 2);
                image.setStyle('padding', margintopbottom+'px '+marginleftright+'px '
                                        +margintopbottom+'px '+marginleftright+'px');
                console.log(margintopbottom+'px '+marginleftright+'px '
                        +margintopbottom+'px '+marginleftright+'px');
            }, this);
            this.doc.drop_zone_group(groupno).setStyles({'width': maxwidth + 10,
                                                            'height': maxheight + 10});
        }
    },
    convert_to_window_xy : function (bgimgxy) {
        return [+bgimgxy[0] + this.doc.bg_img().getX() + 1,
                +bgimgxy[1] + this.doc.bg_img().getY() + 1];
    },
    
    //---------- stuff below this line only used in question editing form ---------------
    
    fp : null,
    maxsizes : null,
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
        this.doc = this.doc_structure(Y, topnode, this);
        this.maxsizes = maxsizes;
        this.draw_dd_area();
    },

    draw_dd_area : function() {
        var bgimageurl = this.fp.file(this.form.to_name_with_index('bgimage')).href;
        if (bgimageurl !== null) {
            this.doc.load_bg_img(bgimageurl);
            this.load_drag_homes();
            this.setup_form_events();
            
            var drop = new this.Y.DD.Drop({
                node: this.doc.bg_img()
            });
            //Listen for a drop:hit on the background image
            drop.on('drop:hit', function(e) {
                e.drag.get('node').setData('gooddrop', true);
            });
    
            this.Y.on('windowresize', this.reposition_drags, this);
            
            this.doc.bg_img().on('load', this.constrain_image_size, this, 'bgimage')
            this.doc.drag_image_homes().on('load', this.constrain_image_size, this, 'dragimage');
            this.poll_for_image_load(null, 1000, this.after_all_images_loaded);
            this.doc.bg_img()
                .after('load', this.poll_for_image_load, this, 1000, this.after_all_images_loaded);
            this.doc.drag_image_homes()
                .after('load', this.poll_for_image_load, this, 1000, this.after_all_images_loaded);
        }
    },
    
    after_all_images_loaded : function () {
        this.doc.drag_images().remove(true);
        this.copy_drag_instances();
        this.update_padding_sizes_all();
        this.reposition_drags();
        this.set_options_for_drag_image_selectors();
    },
    
    constrain_image_size : function (e, imagetype) {
        var maxsize = this.maxsizes[imagetype];
        var reduceby = Math.max(e.target.get('width') / maxsize.width,
                                e.target.get('height') / maxsize.height);
        if (reduceby > 1) {
            e.target.set('width', Math.floor(e.target.get('width') / reduceby));
        }
    },

    load_drag_homes : function () {
        //set up drag items homes
        var dragimagesoptions = {0: ''}; 
        for (var i=0; i < this.form.get_form_value('noimages'); i++) {
            this.load_drag_home(i);
        }
    },

    load_drag_home : function (dragimageno) {
        var url = this.fp.file(this.form.to_name_with_index('dragitem', [dragimageno])).href;
        this.doc.add_or_update_drag_image_home(dragimageno, url, 
                this.form.get_form_value('drags', [dragimageno, 'draglabel']),
                this.form.get_form_value('drags', [dragimageno, 'draggroup']));
    },

    copy_drag_instances : function () {
        //set up drop zones
        for (var i=0; i < this.form.get_form_value('nodropzone'); i++) {
            var dragimageno = this.form.get_form_value('drops', [i, 'choice']);
            if (dragimageno !== '0') {
                var drag = this.doc.clone_new_drag_image(i, dragimageno - 1);
                if (drag !== null) {
                    this.doc.draggable_for_form(drag);
                }
            }
        }
    },
    set_options_for_drag_image_selectors : function () {
        var dragimagesoptions = {0: ''}; 
        for (var i=0; i < this.form.get_form_value('noimages'); i++) {
            var file = this.fp.file(this.form.to_name_with_index('dragitem', [i]));
            if (file.name !== null) {
                dragimagesoptions[i+1] = (i+1)+'. '+file.name;
            }
        }
        for (var i=0; i < this.form.get_form_value('nodropzone'); i++) {
            var selector = this.Y.one('#id_drops_'+i+'_choice');
            var selectedvalue = selector.get('value');
            selector.all('option').remove(true);
            for (var value in dragimagesoptions) {
                value = +value;
                var option = '<option value="'+ value +'">'
                                + dragimagesoptions[value] +
                                '</option>';
                selector.append(option);
                var optionnode = selector.one('option[value="' + value + '"]')
                if (value === +selectedvalue) {
                    optionnode.set('selected', true);
                } else {
                    if (value !== 0) { // no image option is always selectable
                        var cbselector = 'fieldset#draggableimageheader_'+(value-1)
                                                                        +' input[type="checkbox"]';
                        var cbel = this.Y.one(cbselector);
                        var infinite = cbel.get('checked');
                        if ((!infinite) &&
                                    (this.doc.drag_images_cloned_from(value - 1).size() !== 0)) {
                            optionnode.set('disabled', true);
                        }
                    }
                }
            }
        }
    },

    setup_form_events : function () {
        //events triggered by changes to form data
        
        //x and y coordinates
        this.Y.all('fieldset#dropzoneheader input').on('blur', function (e){
            var name = e.target.getAttribute('name');
            var nameparts = this.form.from_name_with_index(name);
            this.reposition_drag(nameparts.indexes[0]);
        }, this);

        //change in selected image
        this.Y.all('fieldset#dropzoneheader select').on('change', this.draw_dd_area, this);
        
        for (var i=0; i < this.form.get_form_value('noimages'); i++) {
            //change to group selector
            this.Y.all('fieldset#draggableimageheader_'+i+' select')
                                                .on('change', this.draw_dd_area, this);
            //change to infinite checkbox
            this.Y.all('fieldset#draggableimageheader_'+i+' input[type="checkbox"]')
                                    .on('change', this.set_options_for_drag_image_selectors, this);
        }
        //event on file picker new file selection
        this.Y.after(this.draw_dd_area, M.form_filepicker, 'callback', this);
    },


    reposition_drags : function() {
        this.doc.drag_images().each(function (drag) {
            var draginstanceno = drag.getData('draginstanceno');
            this.reposition_drag(draginstanceno);
        }, this);
    },
    
    reposition_drag : function (draginstanceno) {
        var drag = this.doc.drag_image(draginstanceno);
        if (null !== drag) {
            var fromform = [this.form.get_form_value('drops', [draginstanceno, 'xleft']),
                            this.form.get_form_value('drops', [draginstanceno, 'ytop'])];
            if (fromform[0] == '' && fromform[1] == '') {
                var dragimageno = drag.getData('dragimageno'); 
                drag.setXY(this.doc.drag_image_home(dragimageno).getXY());
            } else {
                var constrainedxy = this.constrain_xy(draginstanceno, fromform);
                drag.setXY(this.convert_to_window_xy(constrainedxy));
                if (constrainedxy[0] !== +fromform[0] || constrainedxy[1] !== +fromform[1]){
                    this.form.set_form_value('drops', [draginstanceno, 'xleft'], constrainedxy[0]);
                    this.form.set_form_value('drops', [draginstanceno, 'ytop'], constrainedxy[1]);
                }
            }
        }
    },
    set_drag_xy : function (draginstanceno, xy) {
        xy = this.constrain_xy(draginstanceno, this.convert_to_bg_img_xy(xy));
        this.form.set_form_value('drops', [draginstanceno, 'xleft'], Math.floor(xy[0]));
        this.form.set_form_value('drops', [draginstanceno, 'ytop'], Math.floor(xy[1]));
        this.reposition_drag(draginstanceno);
    },
    reset_drag_xy : function (draginstanceno) {
        this.form.set_form_value('drops', [draginstanceno, 'xleft'], '');
        this.form.set_form_value('drops', [draginstanceno, 'ytop'], '');
        this.reposition_drag(draginstanceno);
    },

    //make sure xy value is not out of bounds of bg image
    constrain_xy : function (draginstanceno, bgimgxy) {
        var drag = this.doc.drag_image(draginstanceno);
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
                    return {href : fileanchor.get('href'), name : fileanchor.get('innerHTML')};
                } else {
                    return {href : null, name : null};
                }
            },
            name : function (draftitemid) {
                return draftitemidstoname[draftitemid];
            }
        }
        return toreturn;
    }

}