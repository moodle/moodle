YUI.add('moodle-qtype_ddmarker-form', function (Y, NAME) {

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
 * This is the question editing form code.
 */
var DDMARKERFORMNAME = 'moodle-qtype_ddmarker-form';
var DDMARKER_FORM = function() {
    DDMARKER_FORM.superclass.constructor.apply(this, arguments);
};
Y.extend(DDMARKER_FORM, M.qtype_ddmarker.dd_base_class, {
    fp : null,

    initializer : function() {
        var pendingid = 'qtype_ddmarker-form-' + Math.random().toString(36).slice(2); // Random string.
        M.util.js_pending(pendingid);
        this.fp = this.file_pickers();
        var tn = Y.one(this.get('topnode'));
        tn.one('div.fcontainer').append(
                '<div class="ddarea">' +
                    '<div class="markertexts"></div>' +
                    '<div class="droparea"></div>' +
                    '<div class="dropzones"></div>' +
                    '<div class="grid"></div>' +
                '</div>');
        this.doc = this.doc_structure(this);
        this.stop_selector_events();
        this.set_options_for_drag_item_selectors();
        this.setup_form_events();
        Y.later(500, this, this.update_drop_zones, [pendingid], true);
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

    update_drop_zones : function (pendingid) {

        // Set up drop zones.
        if (this.graphics !== null) {
            this.graphics.destroy();
        }
        this.restart_colours();
        this.graphics = new Y.Graphic({render:"div.ddarea div.dropzones"});
        var noofdropzones = this.form.get_form_value('nodropzone', []);
        for (var dropzoneno = 0; dropzoneno < noofdropzones; dropzoneno++) {
            var dragitemno = this.form.get_form_value('drops', [dropzoneno, 'choice']);
            var markertext = this.get_marker_text(dragitemno);
            var shape = this.form.get_form_value('drops', [dropzoneno, 'shape']);
            var coords = this.get_coords(dropzoneno);
            var colourfordropzone = this.get_next_colour();
            Y.one('input#id_drops_' + dropzoneno + '_coords')
                    .setStyle('background-color', colourfordropzone);
            this.draw_drop_zone(dropzoneno, markertext,
                    shape, coords, colourfordropzone, false);
        }
        if (this.doc.bg_img()) {
            Y.one('div.ddarea .grid')
                    .setXY(this.convert_to_window_xy([0, 0]))
                    .setStyle('width', this.doc.bg_img().get('width'))
                    .setStyle('height', this.doc.bg_img().get('height'));
        }
        M.util.js_complete(pendingid);
    },

    get_coords : function (dropzoneno) {
        var coords = this.form.get_form_value('drops', [dropzoneno, 'coords']);
        return coords.replace(new RegExp("\\s*", 'g'), '');
    },
    get_marker_text : function (markerno) {
        if (Number(markerno) !== 0) {
            var label = this.form.get_form_value('drags', [markerno - 1, 'label']);
            return label.replace(new RegExp("^\\s*(.*)\\s*$"), "$1");
        } else {
            return '';
        }
    },
    set_options_for_drag_item_selectors : function () {
        var dragitemsoptions = {0: ''};
        for (var i = 1; i <= this.form.get_form_value('noitems', []); i++) {
            var label = this.get_marker_text(i);
            if (label !== "") {
                dragitemsoptions[i] = Y.Escape.html(label);
            }
        }
        // Get all the currently selected drags for each drop.
        var selectedvalues = [];
        var selector;
        for (i = 0; i < this.form.get_form_value('nodropzone', []); i++) {
            selector = Y.one('#id_drops_' + i + '_choice');
            selectedvalues[i] = Number(selector.get('value'));
        }
        for (i = 0; i < this.form.get_form_value('nodropzone', []); i++) {
            selector = Y.one('#id_drops_' + i + '_choice');
            // Remove all options for drag choice.
            selector.all('option').remove(true);
            // And recreate the options.
            for (var value in dragitemsoptions) {
                value = Number(value);
                var option = '<option value="' + value + '">' + dragitemsoptions[value] + '</option>';
                selector.append(option);
                var optionnode = selector.one('option[value="' + value + '"]');
                // Is this the currently selected value?
                if (value === selectedvalues[i]) {
                    optionnode.set('selected', true);
                } else {
                    // It is not the currently selected value, is it selectable?
                    if (value !== 0) { // The 'no item' option is always selectable.
                        // Variables to hold form values about this drag item.
                        var noofdrags = this.form.get_form_value('drags', [value - 1, 'noofdrags']);
                        if (Number(noofdrags) !== 0) { // 'noofdrags == 0' means infinite.
                            // Go through all selected values in drop downs.
                            for (var k in selectedvalues) {
                                // Count down 'noofdrags' and if reach zero then set disabled option for this drag item.
                                if (Number(selectedvalues[k]) === value) {
                                    if (Number(noofdrags) === 1) {
                                        optionnode.set('disabled', true);
                                        break;
                                    } else {
                                        noofdrags--;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    },

    stop_selector_events : function () {
        Y.all('fieldset#id_dropzoneheader select').detachAll();
    },

    setup_form_events : function () {
        //events triggered by changes to form data

        // Changes to labels.
        Y.all('fieldset#id_draggableitemheader input').on('change', function () {
            this.set_options_for_drag_item_selectors();
        }, this);

        // Changes to selected drag item.
        Y.all('fieldset#id_draggableitemheader select').on('change', function () {
            this.set_options_for_drag_item_selectors();
        }, this);

        // Change in selected item.
        Y.all('fieldset#id_dropzoneheader select').on('change', function () {
            this.set_options_for_drag_item_selectors();
        }, this);
    },

    /**
     * Low level operations on form.
     */
    form : {
        to_name_with_index : function(name, indexes) {
            var indexstring = name;
            for (var i = 0; i < indexes.length; i++) {
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
},{NAME : DDMARKERFORMNAME, ATTRS : {maxsizes:{value:null}}});

M.qtype_ddmarker = M.qtype_ddmarker || {};
M.qtype_ddmarker.init_form = function(config) {
    return new DDMARKER_FORM(config);
};


}, '@VERSION@', {"requires": ["moodle-qtype_ddmarker-dd", "form_filepicker", "graphics", "escape"]});
