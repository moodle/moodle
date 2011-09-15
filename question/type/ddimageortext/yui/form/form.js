/**
 * This is the question editing form code.
 */
YUI.add('moodle-qtype_ddimageortext-form', function(Y) {
    var DDIMAGEORTEXTFORMNAME = 'ddimageortext_form';
    var DDIMAGEORTEXT_FORM = function() {
        DDIMAGEORTEXT_FORM.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DDIMAGEORTEXT_FORM, M.qtype_ddimageortext.dd_base_class, {
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
            this.set_options_for_drag_item_selectors();
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

                this.afterimageloaddone = false;
                this.doc.bg_img().on('load', this.constrain_image_size, this, 'bgimage');
                this.doc.drag_item_homes()
                                        .on('load', this.constrain_image_size, this, 'dragimage');
                this.doc.bg_img().after('load', this.poll_for_image_load, this,
                                                        true, 0, this.after_all_images_loaded);
                this.doc.drag_item_homes() .after('load', this.poll_for_image_load, this,
                                                        true, 0, this.after_all_images_loaded);
            } else {
                this.setup_form_events();
            }
            this.update_visibility_of_file_pickers();
        },

        after_all_images_loaded : function () {
            this.update_padding_sizes_all();
            this.update_drag_instances();
            this.reposition_drags_for_form();
            this.set_options_for_drag_item_selectors();
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
            for (var i=0; i < this.form.get_form_value('noitems', []); i++) {
                this.load_drag_home(i);
            }
        },

        load_drag_home : function (dragitemno) {
            if ('image' === this.form.get_form_value('dragitemtype', [dragitemno])) {
                var url =
                    this.fp.file(this.form.to_name_with_index('dragitem', [dragitemno])).href;
            } else {
                var url = null;
            }
            this.doc.add_or_update_drag_item_home(dragitemno, url,
                    this.form.get_form_value('drags', [dragitemno, 'draglabel']),
                    this.form.get_form_value('drags', [dragitemno, 'draggroup']));
        },

        update_drag_instances : function () {
            //set up drop zones
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var dragitemno = this.form.get_form_value('drops', [i, 'choice']);
                if (dragitemno !== '0' && (this.doc.drag_item(i) === null)) {
                    var drag = this.doc.clone_new_drag_item(i, dragitemno - 1);
                    if (drag !== null) {
                        this.doc.draggable_for_form(drag);
                    }
                }
            }
        },
        set_options_for_drag_item_selectors : function () {
            var dragitemsoptions = {0: ''};
            for (var i=0; i < this.form.get_form_value('noitems', []); i++) {
                var label = this.form.get_form_value('drags', [i, 'draglabel']);
                var file = this.fp.file(this.form.to_name_with_index('dragitem', [i]));
                if ('image' === this.form.get_form_value('dragitemtype', [i])
                                                                        && file.name !== null) {
                    dragitemsoptions[i+1] = (i+1)+'. '+label+' ('+file.name+')';
                } else if (label != '') {
                    dragitemsoptions[i+1] = (i+1)+'. '+label;
                }
            }
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var selector = Y.one('#id_drops_'+i+'_choice');
                var selectedvalue = selector.get('value');
                selector.all('option').remove(true);
                for (var value in dragitemsoptions) {
                    value = +value;
                    var option = '<option value="'+ value +'">'
                                    + dragitemsoptions[value] +
                                    '</option>';
                    selector.append(option);
                    var optionnode = selector.one('option[value="' + value + '"]')
                    if (value === +selectedvalue) {
                        optionnode.set('selected', true);
                    } else {
                        if (value !== 0) { // no item option is always selectable
                            var cbselector = 'fieldset#draggableitemheader_'+(value-1)
                                                                        +' input[type="checkbox"]';
                            var cbel = Y.one(cbselector);
                            var infinite = cbel.get('checked');
                            if ((!infinite) &&
                                    (this.doc.drag_items_cloned_from(value - 1).size() !== 0)) {
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
                var fromform = [this.form.get_form_value('drops', [draginstanceno, 'xleft']),
                                this.form.get_form_value('drops', [draginstanceno, 'ytop'])];
                var constrainedxy = this.constrain_xy(draginstanceno, fromform);
                this.form.set_form_value('drops', [draginstanceno, 'xleft'], constrainedxy[0]);
                this.form.set_form_value('drops', [draginstanceno, 'ytop'], constrainedxy[1]);
            }, this);

            //change in selected item
            Y.all('fieldset#dropzoneheader select').on('change', function (e){
                var name = e.target.getAttribute('name');
                var draginstanceno = this.form.from_name_with_index(name).indexes[0];
                var old = this.doc.drag_item(draginstanceno);
                if (old !== null) {
                    old.remove(true);
                }
                this.draw_dd_area();
            }, this);

            for (var i=0; i < this.form.get_form_value('noitems', []); i++) {
                //change to group selector
                Y.all('fieldset#draggableitemheader_'+i+' select.draggroup')
                                                                    .on('change', function (e){
                    this.doc.drag_items().remove(true);
                    this.draw_dd_area();
                }, this);
                Y.all('fieldset#draggableitemheader_'+i+' select.dragitemtype')
                                                                    .on('change', function (e){
                    this.doc.drag_items().remove(true);
                    this.draw_dd_area();
                }, this);
                Y.all('fieldset#draggableitemheader_'+i+' input[type="text"]')
                                                        .on('blur', function (e, draginstanceno){
                    this.doc.drag_item(draginstanceno).remove(true);
                    this.draw_dd_area();
                }, this, i);
                //change to infinite checkbox
                Y.all('fieldset#draggableitemheader_'+i+' input[type="checkbox"]')
                                    .on('change', this.set_options_for_drag_item_selectors, this);
            }
            //event on file picker new file selection
            Y.after(function (e){
                var name = this.fp.name(e.id);
                if (name !== 'bgimage') {
                    this.doc.drag_items().remove(true);
                }
                this.draw_dd_area();
            }, M.form_filepicker, 'callback', this);
        },

        update_visibility_of_file_pickers : function() {
            for (var i=0; i < this.form.get_form_value('noitems', []); i++) {
                if ('image' === this.form.get_form_value('dragitemtype', [i])) {
                    Y.one('input#id_dragitem_'+i).get('parentNode').get('parentNode')
                                .setStyle('display', 'block');
                } else {
                    Y.one('input#id_dragitem_'+i).get('parentNode').get('parentNode')
                                .setStyle('display', 'none');
                }
            }
        },

        reposition_drags_for_form : function() {
            this.doc.drag_items().each(function (drag) {
                var draginstanceno = drag.getData('draginstanceno');
                this.reposition_drag_for_form(draginstanceno);
            }, this);
        },

        reposition_drag_for_form : function (draginstanceno) {
            var drag = this.doc.drag_item(draginstanceno);
            if (null !== drag && !drag.hasClass('yui3-dd-dragging')) {
                var fromform = [this.form.get_form_value('drops', [draginstanceno, 'xleft']),
                                this.form.get_form_value('drops', [draginstanceno, 'ytop'])];
                if (fromform[0] == '' && fromform[1] == '') {
                    var dragitemno = drag.getData('dragitemno');
                    drag.setXY(this.doc.drag_item_home(dragitemno).getXY());
                } else {
                    drag.setXY(this.convert_to_window_xy(fromform));
                }
            }
        },
        set_drag_xy : function (draginstanceno, xy) {
            xy = this.constrain_xy(draginstanceno, this.convert_to_bg_img_xy(xy));
            this.form.set_form_value('drops', [draginstanceno, 'xleft'], Math.round(xy[0]));
            this.form.set_form_value('drops', [draginstanceno, 'ytop'], Math.round(xy[1]));
        },
        reset_drag_xy : function (draginstanceno) {
            this.form.set_form_value('drops', [draginstanceno, 'xleft'], '');
            this.form.set_form_value('drops', [draginstanceno, 'ytop'], '');
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
            return [+windowxy[0] - this.doc.bg_img().getX()-1,
                    +windowxy[1] - this.doc.bg_img().getY()-1];
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
    }, {NAME : DDIMAGEORTEXTFORMNAME, ATTRS : {maxsizes:{value:null}}});
    M.qtype_ddimageortext = M.qtype_ddimageortext || {};
    M.qtype_ddimageortext.init_form = function(config) {
        return new DDIMAGEORTEXT_FORM(config);
    }
}, '@VERSION@', {
    requires:['moodle-qtype_ddimageortext-dd', 'form_filepicker']
});