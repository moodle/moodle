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

        initializer : function(params) {
            this.fp = this.file_pickers();
            Y.one(this.get('topnode')).append(
                    '<div class="ddarea">'+
                        '<div class="markertexts"></div>'+
                        '<div class="droparea"></div>'+
                        '<div class="dropzones"></div>'+
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
                //Listen for a drop:hit on the background image
                drop.on('drop:hit', function(e) {
                    e.drag.get('node').setData('gooddrop', true);
                });

                this.afterimageloaddone = false;
                this.doc.bg_img().on('load', this.constrain_image_size, this);
            }
        },

        constrain_image_size : function (e) {
            var maxsize = this.get('maxsizes')['bgimage'];
            var reduceby = Math.max(e.target.get('width') / maxsize.width,
                                    e.target.get('height') / maxsize.height);
            if (reduceby > 1) {
                e.target.set('width', Math.floor(e.target.get('width') / reduceby));
            }
            e.target.addClass('constrained');
            e.target.detach('load', this.constrain_image_size);
        },
        
        graphics : null,


        update_drop_zones : function () {
            
            //set up drop zones
            if (this.graphics !== null) {
                this.graphics.destroy();
            }
            this.restart_colours();
            this.graphics = new Y.Graphic({render:"div.ddarea div.dropzones"});
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                this.update_drop_zone(i);
            }
        },
        update_drop_zone : function (dropzoneno) {
            var dragitemno = this.form.get_form_value('drops', [dropzoneno, 'choice']);
            var markertext = this.get_marker_text(dragitemno);
            var existingmarkertext = Y.one('div.ddarea div.markertexts span.markertext'+dropzoneno);
            if (existingmarkertext) {
                if (markertext !== '') {
                    existingmarkertext.setContent(markertext);
                } else {
                    existingmarkertext.remove(true);
                }
            } else if (markertext !== '') {
                var classnames = 'markertext markertext' + dropzoneno;
                Y.one('div.ddarea div.markertexts').append('<span class="'+classnames+'">' +
                                                                    markertext+'</span>');
            }
            var shape = this.form.get_form_value('drops', [dropzoneno, 'shape']);
            var drawfunc = 'draw_shape_'+shape;
            var colourfordropzone = this.get_next_colour();
            Y.one('input#id_drops_'+dropzoneno+'_coords')
                                                .setStyle('background-color', colourfordropzone);
            if (this[drawfunc] instanceof Function){
               var xyfortext = this[drawfunc](dropzoneno, colourfordropzone);
               if (xyfortext !== null) {
                   var markerspan = Y.one('div.ddarea div.markertexts span.markertext'+dropzoneno);
                   if (markerspan !== null) {
                       markerspan.setStyle('opacity', '0.4');
                       xyfortext[0] -= Math.round(markerspan.get('offsetWidth') / 2);
                       xyfortext[1] -= Math.round(markerspan.get('offsetHeight') / 2);
                       markerspan.setXY(this.convert_to_window_xy(xyfortext));
                   }
               }
            }
        },
        draw_shape_circle : function (dropzoneno, colourfordropzone) {
            var coords = this.get_coords(dropzoneno);
            var coordsparts = coords.match(/(\d+),(\d+);(\d+)/);
            if (coordsparts && coordsparts.length === 4) {
                var xy = [+coordsparts[1] - coordsparts[3], +coordsparts[2] - coordsparts[3]];
                if (this.coords_in_img(xy)) {
                    var widthheight = [+coordsparts[3]*2, +coordsparts[3]*2];
                    var shape = this.graphics.addShape({
                            type: 'circle',
                            width: widthheight[0],
                            height: widthheight[1],
                            fill: {
                                color: colourfordropzone,
                                opacity : "0.5"
                            },
                            stroke: {
                                weight:1,
                                color: "black"
                            }
                    });
                    shape.setXY(this.convert_to_window_xy(xy));
                    return [+coordsparts[1], +coordsparts[2]];
                }
            }
            return null;
        },
        draw_shape_rectangle : function (dropzoneno, colourfordropzone) {
            var coords = this.get_coords(dropzoneno);
            var coordsparts = coords.match(/(\d+),(\d+);(\d+),(\d+)/);
            if (coordsparts && coordsparts.length === 5) {
                var xy = [+coordsparts[1], +coordsparts[2]];
                var widthheight = [+coordsparts[3], +coordsparts[4]];
                if (this.coords_in_img([xy[0]+widthheight[0], xy[1]+widthheight[1]])) {
                    var shape = this.graphics.addShape({
                            type: 'rect',
                            width: widthheight[0],
                            height: widthheight[1],
                            fill: {
                                color: colourfordropzone,
                                opacity : "0.5"
                            },
                            stroke: {
                                weight:1,
                                color: "black"
                            }
                    });
                    shape.setXY(this.convert_to_window_xy(xy));
                    return [+xy[0]+widthheight[0]/2, +xy[1]+widthheight[1]/2];
                }
            }
            return null;

        },
        draw_shape_polygon : function (dropzoneno, colourfordropzone) {
            var coords = this.form.get_form_value('drops', [dropzoneno, 'coords']);
            var coordsparts = coords.split(';');
            var xy = [];
            for (var i in coordsparts) {
                var parts = coordsparts[i].match(/^(\d+),(\d+)$/);
                if (parts !== null && this.coords_in_img([parts[1], parts[2]])) {
                    xy[xy.length] = [parts[1], parts[2]];
                }
            }
            if (xy.length > 2) {
                var polygon = this.graphics.addShape({
                    type: "path",
                    stroke: {
                        weight: 1,
                        color: "black"
                    },
                    fill: {
                        color: colourfordropzone,
                        opacity : "0.5"
                    }
                });
                var maxxy = [0,0];
                var minxy = [this.doc.bg_img().get('width'), this.doc.bg_img().get('height')];
                for (var i in xy) {
                    //calculate min and max points to find center to show marker on
                    minxy[0] = Math.min(xy[i][0], minxy[0]);
                    minxy[1] = Math.min(xy[i][1], minxy[1]);
                    maxxy[0] = Math.max(xy[i][0], maxxy[0]);
                    maxxy[1] = Math.max(xy[i][1], maxxy[1]);
                    if (i == 0) {
                        polygon.moveTo(xy[i][0], xy[i][1]);
                    } else {
                        polygon.lineTo(xy[i][0], xy[i][1]);
                    }
                }
                if (+xy[0][0] !== +xy[xy.length-1][0] || +xy[0][1] !== +xy[xy.length-1][1]) {
                    var windowxy = this.convert_to_window_xy(xy[0]);
                    polygon.lineTo(xy[0][0], xy[0][1]); //close polygon if not already closed
                }
                polygon.end();
                polygon.setXY(this.doc.bg_img().getXY());
                return [Math.round((minxy[0] + maxxy[0])/2), Math.round((minxy[1] + maxxy[1])/2)];
            }
            return null;
        },
        coords_in_img : function (coords) {
            return (coords[0] <= this.doc.bg_img().get('width') && 
                            coords[1] <= this.doc.bg_img().get('height'));
        },
        get_coords : function (dropzoneno) {
            var coords = this.form.get_form_value('drops', [dropzoneno, 'coords']);
            return coords.replace(new RegExp("\\s*", 'g'), '');
        },
        get_marker_text : function (markerno) {
            if (+markerno !== 0){
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
                    dragitemsoptions[i] = label;
                }
            }
            var selectedvalues = [];
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var selector = Y.one('#id_drops_'+i+'_choice');
                selectedvalues[i] = +selector.get('value');
            }
            for (var i=0; i < this.form.get_form_value('nodropzone', []); i++) {
                var selector = Y.one('#id_drops_'+i+'_choice');
                selector.all('option').remove(true);
                for (var value in dragitemsoptions) {
                    value = +value;
                    var option = '<option value="'+ value +'">'
                                    + dragitemsoptions[value] +
                                    '</option>';
                    selector.append(option);
                    var optionnode = selector.one('option[value="' + value + '"]')
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
            Y.all('fieldset#draggableitemheader input').on('change', function (e){
                this.set_options_for_drag_item_selectors();
            }, this);

            //change in selected item
            Y.all('fieldset#dropzoneheader select').on('change', function (e){
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
    }, {NAME : DDMARKERFORMNAME, ATTRS : {maxsizes:{value:null}}});
    M.qtype_ddmarker = M.qtype_ddmarker || {};
    M.qtype_ddmarker.init_form = function(config) {
        return new DDMARKER_FORM(config);
    }
}, '@VERSION@', {
    requires:['moodle-qtype_ddmarker-dd', 'form_filepicker', 'graphics']
});