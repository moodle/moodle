/**
 * This is the question editing form code.
 */
YUI.add('moodle-qtype_ddimagetoimage-form', function(Y) {
    var DDIMAGETOIMAGEFORMNAME = 'ddimagetoimage_form';
    var DDIMAGETOIMAGE_FORM = function() {
        DDIMAGETOIMAGE_FORM.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DDIMAGETOIMAGE_FORM, M.qtype_ddimagetoimage.dd_base_class, {
        fp : null,

        initializer : function(params) {
            this.fp = this.file_pickers();
            Y.one(this.get('topnode')).append('<div class="ddarea"><div class="droparea"></div>'+
                    '<div class="dragitems"></div>'+
                    '<div class="dropzones"></div></div>');
            this.doc = this.doc_structure(this);
            this.draw_dd_area();
        },

        draw_dd_area : function() {
            var bgimageurl = this.fp.file('bgimage').href;
            this.stop_selector_events();
            this.set_options_for_drag_image_selectors();
            if (bgimageurl !== null) {
                this.doc.load_bg_img(bgimageurl);
                this.load_drag_homes();

                var drop = new Y.DD.Drop({
                    node: this.doc.bg_img()
                });
                //Listen for a drop:hit on the background image
                drop.on('drop:hit', function(e) {
                    e.drag.get('node').setData('gooddrop', true);
                });

                this.doc.bg_img().on('load', this.constrain_image_size, this, 'bgimage');
                this.doc.drag_image_homes()
                                        .on('load', this.constrain_image_size, this, 'dragimage');
                this.doc.bg_img().after('load', this.poll_for_image_load, this,
                                                        true, 0, this.after_all_images_loaded);
                this.doc.drag_image_homes() .after('load', this.poll_for_image_load, this,
                                                        true, 0, this.after_all_images_loaded);
            } else {
                this.setup_form_events();
            }
        },

        after_all_images_loaded : function () {
            this.update_padding_sizes_all();
            this.update_drag_instances();
            this.reposition_drags_for_form();
            this.set_options_for_drag_image_selectors();
            this.setup_form_events();
            Y.later(500, this, this.reposition_drags_for_form, [], true);
        },

        constrain_image_size : function (e, imagetype) {
            var maxsize = this.get('maxsizes')[imagetype];
            var reduceby = Math.max(e.target.get('width') / maxsize.width,
                                    e.target.get('height') / maxsize.height);
            if (reduceby > 1) {
                e.target.set('width', Math.floor(e.target.get('width') / reduceby));
            }
            e.target.addClass('constrained');
            e.target.detach('load', this.constrain_image_size);
        },

        load_drag_homes : function () {
            //set up drag items homes
            for (var i=0; i < this.form.get_form_value('noimages', []); i++) {
                this.load_drag_home(i);
            }
        },

        load_drag_home : function (dragimageno) {
            var url = this.fp.file(this.form.to_name_with_index('dragitem', [dragimageno])).href;
            this.doc.add_or_update_drag_image_home(dragimageno, url,
                    this.form.get_form_value('drags', [dragimageno, 'draglabel']),
                    this.form.get_form_value('drags', [dragimageno, 'draggroup']));
        },

        update_drag_instances : function () {
            //set up drop zones
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var dragimageno = this.form.get_form_value('drops', [i, 'choice']);
                if (dragimageno !== '0' && (this.doc.drag_image(i) === null)) {
                    var drag = this.doc.clone_new_drag_image(i, dragimageno - 1);
                    if (drag !== null) {
                        this.doc.draggable_for_form(drag);
                    }
                }
            }
        },
        set_options_for_drag_image_selectors : function () {
            var dragimagesoptions = {0: ''};
            for (var i=0; i < this.form.get_form_value('noimages', []); i++) {
                var file = this.fp.file(this.form.to_name_with_index('dragitem', [i]));
                var label = this.form.get_form_value('drags', [i, 'draglabel']);
                if (file.name !== null) {
                    dragimagesoptions[i+1] = (i+1)+'. '+label+' ('+file.name+')';
                } else if (label != '') {
                    dragimagesoptions[i+1] = (i+1)+'. '+label;
                }
            }
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var selector = Y.one('#id_drops_'+i+'_choice');
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
                            var cbel = Y.one(cbselector);
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

        stop_selector_events : function () {
            Y.all('fieldset#dropzoneheader select').detachAll();
        },

        setup_form_events : function () {
            //events triggered by changes to form data

            //x and y coordinates
            Y.all('fieldset#dropzoneheader input').on('blur', function (e){
                var name = e.target.getAttribute('name');
                var draginstanceno = this.form.from_name_with_index(name).indexes[0];
            }, this);

            //change in selected image
            Y.all('fieldset#dropzoneheader select').on('change', function (e){
                var name = e.target.getAttribute('name');
                var draginstanceno = this.form.from_name_with_index(name).indexes[0];
                var old = this.doc.drag_image(draginstanceno);
                if (old !== null) {
                    old.remove(true);
                }
                this.draw_dd_area();
            }, this);

            for (var i=0; i < this.form.get_form_value('noimages', []); i++) {
                //change to group selector
                Y.all('fieldset#draggableimageheader_'+i+' select').on('change', function (e){
                    this.doc.drag_images().remove(true);
                    this.draw_dd_area();
                }, this);
                Y.all('fieldset#draggableimageheader_'+i+' input[type="text"]')
                                                                    .on('blur', function (e){
                    this.doc.drag_images().remove(true);
                    this.draw_dd_area();
                }, this);
                //change to infinite checkbox
                Y.all('fieldset#draggableimageheader_'+i+' input[type="checkbox"]')
                                    .on('change', this.set_options_for_drag_image_selectors, this);
            }
            //event on file picker new file selection
            Y.after(function (e){
                var name = this.fp.name(e.id);
                if (name !== 'bgimage') {
                    this.doc.drag_images().remove(true);
                }
                this.draw_dd_area();
            }, M.form_filepicker, 'callback', this);
        },

        reposition_drags_for_form : function() {
            this.doc.drag_images().each(function (drag) {
                var draginstanceno = drag.getData('draginstanceno');
                this.reposition_drag_for_form(draginstanceno);
            }, this);
        },

        reposition_drag_for_form : function (draginstanceno) {
            var drag = this.doc.drag_image(draginstanceno);
            if (null !== drag && !drag.hasClass('yui3-dd-dragging')) {
                var fromform = [this.form.get_form_value('drops', [draginstanceno, 'xleft']),
                                this.form.get_form_value('drops', [draginstanceno, 'ytop'])];
                if (fromform[0] == '' && fromform[1] == '') {
                    var dragimageno = drag.getData('dragimageno');
                    drag.setXY(this.doc.drag_image_home(dragimageno).getXY());
                } else {
                    var constrainedxy = this.constrain_xy(draginstanceno, fromform);
                    drag.setXY(this.convert_to_window_xy(constrainedxy));
                }
            }
        },
        set_drag_xy : function (draginstanceno, xy) {
            xy = this.constrain_xy(draginstanceno, this.convert_to_bg_img_xy(xy));
            this.form.set_form_value('drops', [draginstanceno, 'xleft'], Math.floor(xy[0]));
            this.form.set_form_value('drops', [draginstanceno, 'ytop'], Math.floor(xy[1]));
        },
        reset_drag_xy : function (draginstanceno) {
            this.form.set_form_value('drops', [draginstanceno, 'xleft'], '');
            this.form.set_form_value('drops', [draginstanceno, 'ytop'], '');
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
                var indexstring = name;
                for (var i=0; i < indexes.length; i++) {
                    indexstring = indexstring + '[' + indexes[i] + ']';
                }
                return indexstring;
            },
            get_el : function (name, indexes) {
                var form = document.getElementById('mform1');
                return form.elements[this.to_name_with_index(name, indexes)]
            },
            get_form_value : function(name, indexes) {
                var el = this.get_el(name, indexes);
                if (el.type === 'checkbox') {
                    return el.checked;
                } else {
                    return el.value;
                }
            },
            set_form_value : function(name, indexes, value) {
                var el = this.get_el(name, indexes);
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
                var filepickers = Y.all('form.mform input.filepickerhidden');
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
    }, {NAME : DDIMAGETOIMAGEFORMNAME, ATTRS : {maxsizes:{value:null}}});
    M.qtype_ddimagetoimage = M.qtype_ddimagetoimage || {};
    M.qtype_ddimagetoimage.init_form = function(config) {
        return new DDIMAGETOIMAGE_FORM(config);
    }
}, '@VERSION@', {
    requires:['moodle-qtype_ddimagetoimage-dd', 'form_filepicker']
});