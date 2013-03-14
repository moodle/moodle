/**
 * This is the question editing form code.
 */
YUI.add('moodle-qtype_ddmarker-form', function(Y) {
    var DDMARKERFORMNAME = 'ddmarker_form';
    var DDMARKER_FORM = function() {
        DDMARKER_FORM.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DDMARKER_FORM, M.qtype_ddmarker.dd_base_class, {
        fp : null,

        initializer : function() {
            this.fp = this.file_pickers();
            Y.one(this.get('topnode')).append(
                    '<div class="ddarea">'+
                        '<div class="markertexts"></div>'+
                        '<div class="droparea"></div>'+
                        '<div class="dropzones"></div>'+
                        '<div class="grid"></div>'+
                    '</div>');
            this.doc = this.doc_structure(this);
            this.stop_selector_events();
            this.set_options_for_drag_item_selectors();
            this.setup_form_events();
            Y.later(500, this, this.update_drop_zones, [], true);
            Y.after(this.load_bg_image, M.form_filepicker, 'callback', this);
            this.load_bg_image();
        },

        load_bg_image : function() {
            var bgimageurl = this.fp.file('bgimage').href;
            if (bgimageurl !== null) {
                this.doc.load_bg_img(bgimageurl);

                var drop = new Y.DD.Drop({
                    node: this.doc.bg_img()
                });

                // Listen for a drop:hit on the background image.
                drop.on('drop:hit', function(e) {
                    e.drag.get('node').setData('gooddrop', true);
                });

                this.afterimageloaddone = false;
                this.doc.bg_img().on('load', this.constrain_image_size, this);
            }
        },

        constrain_image_size : function (e) {
            var maxsize = this.get('maxsizes').bgimage;
            var reduceby = Math.max(e.target.get('width') / maxsize.width,
                                    e.target.get('height') / maxsize.height);
            if (reduceby > 1) {
                e.target.set('width', Math.floor(e.target.get('width') / reduceby));
            }
            e.target.addClass('constrained');
            e.target.detach('load', this.constrain_image_size);
        },

        update_drop_zones : function () {

            // Set up drop zones.
            if (this.graphics !== null) {
                this.graphics.destroy();
            }
            this.restart_colours();
            this.graphics = new Y.Graphic({render:"div.ddarea div.dropzones"});
            var noofdropzones = this.form.get_form_value('nodropzone', []);
            for (var dropzoneno=0; dropzoneno < noofdropzones; dropzoneno++) {
                var dragitemno = this.form.get_form_value('drops', [dropzoneno, 'choice']);
                var markertext = this.get_marker_text(dragitemno);
                var shape = this.form.get_form_value('drops', [dropzoneno, 'shape']);
                var coords = this.get_coords(dropzoneno);
                var colourfordropzone = this.get_next_colour();
                Y.one('input#id_drops_'+dropzoneno+'_coords')
                                                .setStyle('background-color', colourfordropzone);
                this.draw_drop_zone(dropzoneno, markertext,
                                    shape, coords, colourfordropzone, false);
            }
            Y.one('div.ddarea .grid')
                .setXY(this.doc.bg_img().getXY())
                .setStyle('width', this.doc.bg_img().get('width'))
                .setStyle('height', this.doc.bg_img().get('height'));
        },

        get_coords : function (dropzoneno) {
            var coords = this.form.get_form_value('drops', [dropzoneno, 'coords']);
            return coords.replace(new RegExp("\\s*", 'g'), '');
        },
        get_marker_text : function (markerno) {
            if (+markerno !== 0) {
                var label = this.form.get_form_value('drags', [markerno-1, 'label']);
                return label.replace(new RegExp("^\\s*(.*)\\s*$"), "$1");
            } else {
                return '';
            }
        },
        set_options_for_drag_item_selectors : function () {
            var dragitemsoptions = {0: ''};
            for (var i=0; i < this.form.get_form_value('noitems', []); i++) {
                var label = this.get_marker_text(i);
                if (label !== "") {
                    dragitemsoptions[i] = Y.Escape.html(label);
                }
            }
            var selectedvalues = [];
            var selector;
            for (i = 0; i < this.form.get_form_value('nodropzone', []); i++) {
                selector = Y.one('#id_drops_'+i+'_choice');
                selectedvalues[i] = +selector.get('value');
            }
            for (i = 0; i < this.form.get_form_value('nodropzone', []); i++) {
                selector = Y.one('#id_drops_'+i+'_choice');
                selector.all('option').remove(true);
                for (var value in dragitemsoptions) {
                    value = +value;
                    var option = '<option value="'+ value +'">'
                                    + dragitemsoptions[value] +
                                    '</option>';
                    selector.append(option);
                    var optionnode = selector.one('option[value="' + value + '"]');
                    if (value === selectedvalues[i]) {
                        optionnode.set('selected', true);
                    } else {
                        if (value !== 0) { // no item option is always selectable
                            var infinite = this.form.get_form_value('drags', [value-1, 'infinite']);
                            if (!infinite) {
                                for (var k in selectedvalues) {
                                    if (+selectedvalues[k] === value) {
                                        optionnode.set('disabled', true);
                                    }
                                }
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
            Y.all('fieldset#draggableitemheader input').on('change', function () {
                this.set_options_for_drag_item_selectors();
            }, this);

            //change in selected item
            Y.all('fieldset#dropzoneheader select').on('change', function () {
                this.set_options_for_drag_item_selectors();
            }, this);
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
                return form.elements[this.to_name_with_index(name, indexes)];
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
                filepickers.each(function(filepicker) {
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
            };
            return toreturn;
        }
    }, {NAME : DDMARKERFORMNAME, ATTRS : {maxsizes:{value:null}}});
    M.qtype_ddmarker = M.qtype_ddmarker || {};
    M.qtype_ddmarker.init_form = function(config) {
        return new DDMARKER_FORM(config);
    };
}, '@VERSION@', {
    requires:['moodle-qtype_ddmarker-dd', 'form_filepicker', 'graphics', 'escape']
});