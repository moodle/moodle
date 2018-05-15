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

/* eslint max-depth: ["error", 8] */

/**
 * Form class extends base drag and drop marker question type.
 *
 * @package    qtype_ddmarker
 * @subpackage form
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/svgjs'], function($, svgjs) {

    "use strict";

    /**
     * @alias qtype_ddmarker/form
     */
    var t = {

        topnode: null, // The jQuery page container for this functionality.
        maxSizes: null, // Object containing maximum sizes for the background image.
        fp: null, // Object containing functions associated with the file picker.
        pollTimer: null, // Timer reference.
        imageReady: false, // The background image is loaded.
        bgImg: null, // Reference to the jQuery background image object.
        svg: {}, // Reference to the main svgjs object.
        shapes: {}, // References to the dropzone shape objects.

        /**
         * Initialise the form javascript features.
         * @param {Object} config Array of topnode and maxsizes
         */
        init: function(config) {
            t.topnode = $(config.topnode);
            t.maxSizes = config.maxsizes.bgimage;
            t.fp = t.filePickers();
            t.setupPreviewArea();
            t.setOptionsForDragItemSelectors();
            t.setupFormEvents();
            t.whenImageReady();
         },

        /**
         * Prevents adding drop zones until the preview background image is ready to load.
         */
        whenImageReady: function() {
            if (t.imageReady) {
                return;
            }
            var bgimageurl = t.fp.file('bgimage').href;
            if (bgimageurl !== null) {
                if (t.pollTimer !== null) {
                    clearTimeout(t.pollTimer);
                    t.pollTimer = null;
                }
                t.imageReady = true;
                t.filepickerOnChange();
                t.loadPreviewImage();
            } else {
                // It would be better to use an onload or onchange event rather than this timeout.
                // Unfortunately attempts to do this early are overwritten by filepicker during its loading.
                t.pollTimer = setTimeout(t.whenImageReady, 1000);
            }
        },

        /**
         * When a new file gets loaded into the filepicker then display the image.
         * This form event is not in the setupFormEvents section as it needs to be delayed till after
         * filepicker's javascript has finished.
         */
        filepickerOnChange: function() {
            $('form.mform').on('change', '#id_bgimage', t.loadPreviewImage);
        },

        /**
         * Loads the preview background image.
         */
        loadPreviewImage: function() {
            var bgimageurl = t.fp.file('bgimage').href;
            t.bgImg = t.topnode.find('.dropbackground');
            t.bgImg.one('load', t.afterImageLoaded);
            t.bgImg.attr('src', bgimageurl);
            t.bgImg.css({'border': '1px solid #000', 'max-width': 'none'});
        },

        /**
         * Functions to run after background image loaded.
         */
        afterImageLoaded: function() {
            t.constrainImageSize();
            // Place the dropzone area over the background image (adding one to account for the border).
            $('#ddm-dropzone').css('position', 'relative').css('top', (t.bgImg.height() + 1) * -1);
            $('#ddm-droparea').css('height', t.bgImg.height() + 20);
            t.addDropzones();
        },

        /**
         * Limits the background image display size.
         */
        constrainImageSize: function() {
            var reduceby = Math.max(t.bgImg.width() / t.maxSizes.width,
                t.bgImg.height() / t.maxSizes.height);
            if (reduceby > 1) {
                t.bgImg.css('width', Math.floor(t.bgImg.width() / reduceby));
            }
            t.bgImg.addClass('constrained');
        },

        /**
         * Draws or re-draws all dropzones in the preview area based on form data.
         * Call this function when there is a change in the form data.
         */
        addDropzones: function() {
            var noOfDropzones, dropzoneNo, dragItemNo, markerText, shape, coords;
            // Instantiate the svg drawing feature within the dropzone area.
            if (!$.isEmptyObject(t.svg)) {
                $('svg').remove();
            }
            t.svg = svgjs('ddm-dropzone').size(t.bgImg.outerWidth(), t.bgImg.outerHeight());
            // Add dropzone shapes based on data from the form fields.
            noOfDropzones = t.form.getFormValue('nodropzone', []);
            for (dropzoneNo = 0; dropzoneNo < noOfDropzones; dropzoneNo++) {
                dragItemNo = t.form.getFormValue('drops', [dropzoneNo, 'choice']);
                markerText = t.getMarkerText(dragItemNo);
                shape = t.form.getFormValue('drops', [dropzoneNo, 'shape']);
                coords = t.getCoords(dropzoneNo);
                if (coords !== '') {
                    t.addShape(dropzoneNo, shape, coords, markerText);
                }
            }
            t.addEditingFeatures();
        },

        /**
         * Adds a dropzone shape in the preview.
         * @param {string} dropzoneNo
         * @param {string} shape
         * @param {string} coords
         * @param {string} markerText
         */
        addShape: function(dropzoneNo, shape, coords, markerText) {
            var coordsParts, x, y, radius, w, h, xy, parts, max, min, path, i, cx, cy;
            switch (shape) {
                case 'circle':
                    coordsParts = coords.match(/(\d+),(\d+);(\d+)/);
                    if (coordsParts && coordsParts.length === 4) {
                        cx = Number(coordsParts[1]);
                        cy = Number(coordsParts[2]);
                        radius = Number(coordsParts[3]);
                        t.shapes[dropzoneNo] = {'shape': shape, 'center': [cx, cy], 'radius': radius};
                        t.shapes[dropzoneNo].dropzone = t.svg.circle(radius * 2)
                            .cx(cx)
                            .cy(cy)
                            .attr({fill: '#FFF', 'fill-opacity': 0.2, stroke: '#000', 'stroke-width': 1})
                            .addClass('dzno' + dropzoneNo);
                        t.shapes[dropzoneNo].markerText = t.svg.text(markerText)
                            .cx(cx)
                            .cy(cy + 15);
                    }
                    break;
                case 'rectangle':
                    coordsParts = coords.match(/(\d+),(\d+);(\d+),(\d+)/);
                    if (coordsParts && coordsParts.length === 5) {
                        x = Number(coordsParts[1]);
                        y = Number(coordsParts[2]);
                        w = Number(coordsParts[3]);
                        h = Number(coordsParts[4]);
                        cx = x + w / 2;
                        cy = y + h / 2;
                        t.shapes[dropzoneNo] = {'shape': shape, 'center': [cx, cy], 'top': [x, y], 'width': w, 'height': h};
                        t.shapes[dropzoneNo].dropzone = t.svg.rect(w, h)
                            .x(x)
                            .y(y)
                            .attr({fill: '#FFF', 'fill-opacity': 0.2, stroke: '#000', 'stroke-width': 1})
                            .addClass('dzno' + dropzoneNo);
                        t.shapes[dropzoneNo].markerText = t.svg.text(markerText)
                            .cx(cx)
                            .cy(cy + 15);
                    }
                    break;
                case 'polygon':
                    coordsParts = coords.split(';');
                    xy = [];
                    for (i in coordsParts) {
                        parts = coordsParts[i].match(/^(\d+),(\d+)$/);
                        if (parts !== null) {
                            xy[xy.length] = [Number(parts[1]), Number(parts[2])];
                        }
                    }
                    if (xy.length > 2) {
                        max = [0, 0];
                        min = [t.maxSizes.width, t.maxSizes.height];
                        for (i = 0; i < xy.length; i++) {
                            // Calculate min and max points to find center.
                            min[0] = Math.min(xy[i][0], min[0]);
                            min[1] = Math.min(xy[i][1], min[1]);
                            max[0] = Math.max(xy[i][0], max[0]);
                            max[1] = Math.max(xy[i][1], max[1]);
                        }
                        cx = (min[0] + max[0]) / 2;
                        cy = (min[1] + max[1]) / 2;
                        path = coords.replace(/[;]/g, ' ');
                        t.shapes[dropzoneNo] = {'shape': shape, 'center': [cx, cy], 'xy': xy};
                        t.shapes[dropzoneNo].dropzone = t.svg.polygon(path)
                            .attr({fill: '#FFF', 'fill-opacity': 0.2, stroke: '#000', 'stroke-width': 1})
                            .addClass('dzno' + dropzoneNo);
                        t.shapes[dropzoneNo].markerText = t.svg.text(markerText)
                            .cx(cx)
                            .cy(cy + 15);
                    }
                    break;
            }
         },

        /**
         * Adds the on click editing/moving features.
         */
        addEditingFeatures: function() {
            if (!$.isEmptyObject(t.shapes)) {
                for (var dropzoneNo in t.shapes) {
                    t.shapes[dropzoneNo].dropzone.on('click', t.addHandles);
                }
            }
        },

        /**
         * Adds/removes the visual vertex handles to a dropzone shape and the moving/editing events.
         * @param {Object} e Event object
         */
        addHandles: function(e) {
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            var cxy = t.shapes[dropzoneNo].center;
            var xy, i;
            if (t.shapes[dropzoneNo].hasOwnProperty('editing') && t.shapes[dropzoneNo].editing) {
                // Turn off editing.
                t.shapes[dropzoneNo].movehandle.remove();
                t.shapes[dropzoneNo].dropzone.attr('stroke-width', 1);
                if (t.shapes[dropzoneNo].shape === 'polygon') {
                    xy = t.shapes[dropzoneNo].xy;
                    for (i = 0; i < xy.length; i++) {
                        t.shapes[dropzoneNo].resizehandle[i].remove();
                    }
                } else {
                    t.shapes[dropzoneNo].resizehandle.remove();
                }
                t.shapes[dropzoneNo].editing = false;
                t.shapes[dropzoneNo].moving = false;
                t.shapes[dropzoneNo].resizeing = false;
            } else {
                // Turn editing on.
                t.shapes[dropzoneNo].editing = true;
                t.shapes[dropzoneNo].dropzone.attr('stroke-width', 2);
                // Add a move handle in the center.
                t.shapes[dropzoneNo].movehandle = t.svg.circle(10)
                    .cx(cxy[0])
                    .cy(cxy[1])
                    .attr({fill: 'white', 'fill-opacity': 0.1, stroke: 'red', 'stroke-width': 1})
                    .style('cursor', 'pointer')
                    .addClass('dzno' + dropzoneNo);
                t.shapes[dropzoneNo].movehandle.on('mousedown', t.moveStart);
                t.shapes[dropzoneNo].movehandle.on('mousemove', t.move);
                t.shapes[dropzoneNo].movehandle.on('mouseup', t.moveEnd);
                // Add edit handle(s).
                switch (t.shapes[dropzoneNo].shape) {
                    case 'circle':
                        t.shapes[dropzoneNo].resizehandle = t.svg.rect(7, 7)
                            .cx(cxy[0] + t.shapes[dropzoneNo].radius)
                            .cy(cxy[1])
                            .attr({fill: 'white', 'fill-opacity': 0.1, stroke: 'blue', 'stroke-width': 1})
                            .addClass('dzno' + dropzoneNo);
                        t.shapes[dropzoneNo].resizehandle.on('mousedown', t.resizeStart);
                        t.shapes[dropzoneNo].resizehandle.on('mousemove', t.resize);
                        t.shapes[dropzoneNo].resizehandle.on('mouseup', t.resizeEnd);
                        break;
                    case 'rectangle':
                        t.shapes[dropzoneNo].resizehandle = t.svg.rect(7, 7)
                            .cx(t.shapes[dropzoneNo].top[0] + t.shapes[dropzoneNo].width)
                            .cy(t.shapes[dropzoneNo].top[1] + t.shapes[dropzoneNo].height)
                            .attr({fill: 'white', 'fill-opacity': 0.1, stroke: 'blue', 'stroke-width': 1})
                            .addClass('dzno' + dropzoneNo);
                        t.shapes[dropzoneNo].resizehandle.on('mousedown', t.resizeStart);
                        t.shapes[dropzoneNo].resizehandle.on('mousemove', t.resize);
                        t.shapes[dropzoneNo].resizehandle.on('mouseup', t.resizeEnd);
                        break;
                    case 'polygon':
                        xy = t.shapes[dropzoneNo].xy;
                        t.shapes[dropzoneNo].resizehandle = [];
                        for (i = 0; i < xy.length; i++) {
                            t.shapes[dropzoneNo].resizehandle[i] = t.svg.rect(7, 7)
                                .cx(xy[i][0])
                                .cy(xy[i][1])
                                .attr({fill: 'white', 'fill-opacity': 0.1, stroke: 'blue', 'stroke-width': 1})
                                .addClass('dzno' + dropzoneNo)
                                .data('resizeno', i);
                            t.shapes[dropzoneNo].resizehandle[i].on('mousedown', t.resizeStart);
                            t.shapes[dropzoneNo].resizehandle[i].on('mousemove', t.resize);
                            t.shapes[dropzoneNo].resizehandle[i].on('mouseup', t.resizeEnd);
                        }
                        break;
                }
            }
        },

        /**
         * Move events handlers.
         * @param {Object} e Event object
         */
        moveStart: function(e) {
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            // It would be ideal to use the dragdrop library here, so editing this qtype could
            // be done on a mobile device, but the dragProxy passed here does not work with
            // an svgjs element.
            // e.g. dd.start(event, t.shapes[dropzoneNo].movehandle, t.move, t.moveEnd);
            t.shapes[dropzoneNo].moving = true;
            t.shapes[dropzoneNo].resizeing = false;
        },

        move: function(e) {
            var cxy, xy, newxy, changex, changey;
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            if (t.shapes[dropzoneNo].hasOwnProperty('moving') && t.shapes[dropzoneNo].moving) {
                cxy = t.shapes[dropzoneNo].center;
                // Note for some reason FF has difficulty with e.offsetX and Y.
                changex = e.movementX;
                changey = e.movementY;
                cxy = [cxy[0] + changex, cxy[1] + changey];
                switch (t.shapes[dropzoneNo].shape) {
                    case 'circle':
                        t.shapes[dropzoneNo].resizehandle
                            .cx(cxy[0] + t.shapes[dropzoneNo].radius)
                            .cy(cxy[1]);
                        t.shapes[dropzoneNo].dropzone
                            .cx(cxy[0])
                            .cy(cxy[1]);
                        break;
                    case 'rectangle':
                        t.shapes[dropzoneNo].resizehandle
                            .cx(cxy[0] + (t.shapes[dropzoneNo].width / 2))
                            .cy(cxy[1] + (t.shapes[dropzoneNo].height / 2));
                        t.shapes[dropzoneNo].top = [cxy[0] - (t.shapes[dropzoneNo].width / 2),
                            cxy[1] - (t.shapes[dropzoneNo].height / 2)];
                        t.shapes[dropzoneNo].dropzone
                            .cx(cxy[0])
                            .cy(cxy[1]);
                        break;
                    case 'polygon':
                        xy = t.shapes[dropzoneNo].xy;
                        newxy = [];
                        for (var i = 0; i < xy.length; i++) {
                            t.shapes[dropzoneNo].resizehandle[i]
                                .cx(xy[i][0] + changex)
                                .cy(xy[i][1] + changey);
                            newxy[i] = [xy[i][0] + changex, xy[i][1] + changey];
                        }
                        t.shapes[dropzoneNo].xy = newxy;
                        t.shapes[dropzoneNo].dropzone.plot(newxy);
                        break;
                }
                t.shapes[dropzoneNo].movehandle
                    .cx(cxy[0])
                    .cy(cxy[1]);
                t.shapes[dropzoneNo].center = cxy;
                t.shapes[dropzoneNo].markerText
                    .cx(cxy[0])
                    .cy(cxy[1] + 15);
            }
        },

        moveEnd: function(e) {
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            var value, i;
            t.shapes[dropzoneNo].moving = false;
            // Save the coords to the form.
            switch (t.shapes[dropzoneNo].shape) {
                case 'circle':
                    value = t.shapes[dropzoneNo].center[0] + ',' + t.shapes[dropzoneNo].center[1] + ';' +
                        t.shapes[dropzoneNo].radius;
                    t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    break;
                case 'rectangle':
                    value = Math.round(t.shapes[dropzoneNo].top[0]) + ',' + Math.round(t.shapes[dropzoneNo].top[1]) + ';' +
                        Math.round(t.shapes[dropzoneNo].width) + ',' + Math.round(t.shapes[dropzoneNo].height);
                    t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    break;
                case 'polygon':
                    value = '';
                    for (i = 0; i < t.shapes[dropzoneNo].xy.length; i++) {
                        value = value + Math.round(t.shapes[dropzoneNo].xy[i][0]) + ',' +
                            Math.round(t.shapes[dropzoneNo].xy[i][1]) + ';';
                    }
                    value = value.slice(0, value.length - 1);
                    t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    break;
            }
        },

        /**
         * Resize events handlers.
         * @param {Object} e Event object
         */
        resizeStart: function(e) {
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            var resizeNo;
            t.shapes[dropzoneNo].moving = false;
            t.shapes[dropzoneNo].resizeing = true;
            // Use CTRL + click on a vertex to add a new vertex between the current and next vertex.
            if (t.shapes[dropzoneNo].shape === 'polygon' && e.ctrlKey) {
                resizeNo = e.currentTarget.dataset.resizeno;
                t.addNewHandle(dropzoneNo, resizeNo);
            }
        },

        resize: function(e) {
            var w, h, resizeno, xy, plot, i, cx, cy, min, max, cxy;
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            if (t.shapes[dropzoneNo].hasOwnProperty('resizeing') && t.shapes[dropzoneNo].resizeing) {
                cxy = t.shapes[dropzoneNo].center;
                switch (t.shapes[dropzoneNo].shape) {
                    case 'circle':
                        var radius = t.shapes[dropzoneNo].radius + e.movementX;
                        if (radius > 1) {
                            t.shapes[dropzoneNo].resizehandle.cx(cxy[0] + radius);
                            t.shapes[dropzoneNo].radius = radius;
                            t.shapes[dropzoneNo].dropzone.radius(radius);
                        }
                        break;
                    case 'rectangle':
                        w = t.shapes[dropzoneNo].width + e.movementX;
                        h = t.shapes[dropzoneNo].height + e.movementY;
                        if (w > 1 && h > 1) {
                            t.shapes[dropzoneNo].width = w;
                            t.shapes[dropzoneNo].height = h;
                            t.shapes[dropzoneNo].dropzone.size(w, h);
                            cx = t.shapes[dropzoneNo].resizehandle.cx() + e.movementX;
                            cy = t.shapes[dropzoneNo].resizehandle.cy() + e.movementY;
                            t.shapes[dropzoneNo].resizehandle.cx(cx).cy(cy);
                            cx = t.shapes[dropzoneNo].top[0] + (w / 2);
                            cy = t.shapes[dropzoneNo].top[1] + (h / 2);
                            t.shapes[dropzoneNo].movehandle.cx(cx).cy(cy);
                            t.shapes[dropzoneNo].markerText.cx(cx).cy(cy + 15);
                            t.shapes[dropzoneNo].center = [cx, cy];
                        }
                        break;
                    case 'polygon':
                        resizeno = e.currentTarget.dataset.resizeno;
                        if (resizeno !== undefined) {
                            resizeno = Number(resizeno);
                            xy = t.shapes[dropzoneNo].xy;
                            cx = t.shapes[dropzoneNo].resizehandle[resizeno].cx() + e.movementX;
                            cy = t.shapes[dropzoneNo].resizehandle[resizeno].cy() + e.movementY;
                            t.shapes[dropzoneNo].resizehandle[resizeno].cx(cx).cy(cy);
                            xy[resizeno] = [cx, cy];
                            plot = [];
                            for (i = 0; i < xy.length; i++) {
                                plot.push(xy[i]);
                            }
                            t.shapes[dropzoneNo].dropzone.plot(plot);
                            // Recalculate and reset the center.
                            min = [t.maxSizes.width, t.maxSizes.height];
                            max = [0, 0];
                            for (i = 0; i < xy.length; i++) {
                                min[0] = Math.min(xy[i][0], min[0]);
                                min[1] = Math.min(xy[i][1], min[1]);
                                max[0] = Math.max(xy[i][0], max[0]);
                                max[1] = Math.max(xy[i][1], max[1]);
                            }
                            cx = (min[0] + max[0]) / 2;
                            cy = (min[1] + max[1]) / 2;
                            t.shapes[dropzoneNo].center = [cx, cy];
                            t.shapes[dropzoneNo].movehandle.cx(cx).cy(cy);
                            t.shapes[dropzoneNo].markerText.cx(cx).cy(cy + 15);
                        }
                        break;
                }
            }
        },

        resizeEnd: function(e) {
            var dropzoneNo = e.currentTarget.className.baseVal.slice(4);
            var value, xy, i;
            t.shapes[dropzoneNo].resizeing = false;
            // Save the coords to the form.
            switch (t.shapes[dropzoneNo].shape) {
                case 'circle':
                    value = t.shapes[dropzoneNo].center[0] + ',' + t.shapes[dropzoneNo].center[1] + ';' +
                        t.shapes[dropzoneNo].radius;
                    t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    break;
                case 'rectangle':
                    value = t.shapes[dropzoneNo].top[0] + ',' + t.shapes[dropzoneNo].top[1] + ';' +
                        t.shapes[dropzoneNo].width + ',' + t.shapes[dropzoneNo].height;
                    t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    break;
                case 'polygon':
                    xy = t.shapes[dropzoneNo].xy;
                    value = '';
                    for (i = 0; i < xy.length; i++) {
                        value = value + Math.round(xy[i][0]) + ',' + Math.round(xy[i][1]) + ';';
                    }
                    if (value !== '') {
                        value = value.slice(0, value.length - 1);
                        t.form.setFormValue('drops', [dropzoneNo, 'coords'], value);
                    }
                    break;
            }
        },

        /**
         * For polygon shaped dropzone only, adds a new vertex (handle).
         * @param {string} dropzoneNo
         * @param {string} resizeno The current vertex or handle number.
         */
        addNewHandle: function(dropzoneNo, resizeno) {
            // Work out the 'next' vertex, add a new vertex between this vertex and the next vertex.
            var coords, xy, i, next;
            coords = '';
            xy = t.shapes[dropzoneNo].xy;
            for (i = 0; i < xy.length; i++) {
                coords = coords + Math.round(xy[i][0]) + ',' + Math.round(xy[i][1]) + ';';
                if (i === Number(resizeno)) {
                    if ((i + 1) === xy.length) {
                        next = 0;
                    } else {
                        next = i + 1;
                    }
                    // Make sure we only put integer numbers into the form.
                    coords = coords + Math.round((xy[i][0] + xy[next][0]) / 2) + ',' +
                        Math.round((xy[i][1] + xy[next][1]) / 2) + ';';
                }
            }
            // Add new coords to form, then redraw the dropzones.
            coords = coords.slice(0, coords.length - 1);
            t.form.setFormValue('drops', [dropzoneNo, 'coords'], coords);
            t.addDropzones();
        },

        /**
         * Returns the coordinates for a drop zone.
         * @param {string} dropzoneNo
         * @returns {string} coords
         */
        getCoords: function(dropzoneNo) {
            var coords = t.form.getFormValue('drops', [dropzoneNo, 'coords']);
            return coords.replace(new RegExp("\\s*", 'g'), '');
        },

        /**
         * Returns the label text for a marker.
         * @param {string} markerno
         * @returns {string} Marker text
         */
        getMarkerText: function(markerno) {
            if (Number(markerno) !== 0) {
                var label = t.form.getFormValue('drags', [markerno - 1, 'label']);
                return label.replace(new RegExp("^\\s*(.*)\\s*$"), "$1");
            } else {
                return '';
            }
        },

        /**
         * Add html for the preview area.
         */
        setupPreviewArea: function() {
            t.topnode.find('div.fcontainer').append(
                '<div class="ddarea">' +
                '   <div class="markertexts"></div>' +
                '   <div id="ddm-droparea" class="droparea">' +
                '       <img class="dropbackground" />' +
                '       <div id="ddm-dropzone" class="dropzones"></div>' +
                '   </div>' +
                '</div>');
        },

        /**
         * When a new marker is added this function updates the Marker dropdown controls in Drop zones.
         */
        setOptionsForDragItemSelectors: function() {
            var dragItemsOptions = {'0': ''};
            var noItems = t.form.getFormValue('noitems', []);
            var selectedValues = [];
            var selector;
            var i, label;
            for (i = 1; i <= noItems; i++) {
                label = t.getMarkerText(i);
                if (label !== "") {
                    // HTML escape the label.
                    dragItemsOptions[i] = $('<div/>').text(label).html();
                }
            }
            // Get all the currently selected drags for each drop.
            for (i = 0; i < t.form.getFormValue('nodropzone', []); i++) {
                selector = $('#id_drops_' + i + '_choice');
                selectedValues[i] = Number(selector.val());
            }
            for (i = 0; i < t.form.getFormValue('nodropzone', []); i++) {
                selector = $('#id_drops_' + i + '_choice');
                // Remove all options for drag choice.
                selector.find('option').remove();
                // And recreate the options.
                for (var value in dragItemsOptions) {
                    value = Number(value);
                    var option = '<option value="' + value + '">' + dragItemsOptions[value] + '</option>';
                    selector.append(option);
                    var optionnode = selector.find('option[value="' + value + '"]');
                    // Is this the currently selected value?
                    if (value === selectedValues[i]) {
                        optionnode.attr('selected', true);
                    } else {
                        // It is not the currently selected value, is it selectable?
                        if (value !== 0) { // The 'no item' option is always selectable.
                            // Variables to hold form values about this drag item.
                            var noofdrags = t.form.getFormValue('drags', [value - 1, 'noofdrags']);
                            if (Number(noofdrags) !== 0) { // 'noofdrags === 0' means infinite.
                                // Go through all selected values in drop downs.
                                for (var k in selectedValues) {
                                    // Count down 'noofdrags' and if reach zero then set disabled option for this drag item.
                                    if (Number(selectedValues[k]) === value) {
                                        if (Number(noofdrags) === 1) {
                                            optionnode.attr('disabled', true);
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

        /**
         * Convert one dropzone shape to another shape.
         * @param {string} dropzoneNo
         * @param {string} newshape
         */
        updateShape: function(dropzoneNo, newshape) {
            var coords, newCoords, coordParts, parts, xy, i, cx, cy, w, h, x, y, min, max;
            newCoords = '';
            coords = t.getCoords(dropzoneNo);
            if (coords === '') {
                switch (newshape) {
                    case 'circle':
                        newCoords = '30,30;20';
                        break;
                    case 'rectangle':
                        newCoords = '10,10;30,30';
                        break;
                    case 'polygon':
                        newCoords = '10,10;40,20;20,40';
                        break;
                }
            } else {
                // Use existing coords to influence the newcoords for the newshape.
                coordParts = coords.split(';');
                xy = [];
                for (i in coordParts) {
                    parts = coordParts[i].match(/^(\d+),(\d+)$/);
                    if (parts !== null) {
                        xy[xy.length] = [Number(parts[1]), Number(parts[2])];
                    }
                }
                switch (newshape) {
                    case 'circle':
                        if (xy.length === 2) {
                            // Rectangle to circle.
                            w = xy[1][0];
                            h = xy[1][1];
                            cx = Math.round(xy[0][0] + (w / 2));
                            cy = Math.round(xy[0][1] + (h / 2));
                            newCoords = cx + ',' + cy + ';' + Math.round((w + h) / 4);
                        }
                        if (xy.length > 2) {
                            // Polygon to circle.
                            cx = Math.round(t.shapes[dropzoneNo].center[0]);
                            cy = Math.round(t.shapes[dropzoneNo].center[1]);
                            // Guess at the radius.
                            w = Math.abs(Math.round(xy[0][0] - cx));
                            if (w < 10) {
                                w = 10;
                            }
                            newCoords = cx + ',' + cy + ';' + w;
                        }
                        break;
                    case 'rectangle':
                        if (xy.length === 1) {
                            // Circle to rectangle.
                            w = t.shapes[dropzoneNo].radius * 2;
                            x = Math.abs(Math.round(xy[0][0] - (w / 2)));
                            y = Math.abs(Math.round(xy[0][1] - (w / 2)));
                            newCoords = x + ',' + y + ';' + w + ',' + w;
                        }
                        if (xy.length > 2) {
                            min = [t.maxSizes.width, t.maxSizes.height];
                            max = [0, 0];
                            for (i = 0; i < xy.length; i++) {
                                min[0] = Math.min(xy[i][0], min[0]);
                                min[1] = Math.min(xy[i][1], min[1]);
                                max[0] = Math.max(xy[i][0], max[0]);
                                max[1] = Math.max(xy[i][1], max[1]);
                            }
                            w = Math.abs(Math.round(max[0] - min[0]));
                            if (w < 10) {
                                w = 10;
                            }
                            h = Math.abs(Math.round(max[1] - min[1]));
                            if (h < 10) {
                                h = 10;
                            }
                            x = min[0];
                            y = min[1];
                            newCoords = x + ',' + y + ';' + w + ',' + h;
                        }
                        break;
                    case 'polygon':
                        if (xy.length === 1) {
                            // Circle to polygon.
                            w = t.shapes[dropzoneNo].radius * 2;
                            x = Math.abs(Math.round(xy[0][0] - (w / 2)));
                            y = Math.abs(Math.round(xy[0][1] - (w / 2)));
                            newCoords = x + ',' + y + ';' + (x + w) + ',' + y + ';' +
                                (x + w) + ',' + (y + w) + ';' + x + ',' + (y + w);
                        }
                        if (xy.length === 2) {
                            x = Math.round(xy[0][0]);
                            y = Math.round(xy[0][1]);
                            w = Math.round(xy[1][0]);
                            h = Math.round(xy[1][1]);
                            newCoords = x + ',' + y + ';' + (x + w) + ',' + y + ';' +
                                (x + w) + ',' + (y + h) + ';' + x + ',' + (y + h);
                        }
                        break;
                }
            }
            t.form.setFormValue('drops', [dropzoneNo, 'coords'], newCoords);
            t.addDropzones();
        },

        /**
         * Events linked to form actions.
         */
        setupFormEvents: function() {
            // Changes to labels in the Markers section.
            $('fieldset#id_draggableitemheader').on('change', 'input', function() {
                t.setOptionsForDragItemSelectors();
            });
            $('fieldset#id_draggableitemheader').on('change', 'select', function() {
                t.setOptionsForDragItemSelectors();
            });
            // Change in Drop zones section - shape and marker.
            $('fieldset#id_dropzoneheader').on('change', 'select', function(e) {
                var res = e.currentTarget.id.match(/^id_drops_(\d+)_([a-z]*)$/);
                if (!res) {
                    return;
                }
                if (res[2] === 'shape') {
                    t.updateShape(res[1], e.currentTarget.value);
                } else {
                    t.addDropzones();
                }
            });
            // Change in Drop zones section - manual changes to coordinates.
            $('fieldset#id_dropzoneheader').on('change', 'input', function() {
                t.addDropzones();
            });
        },

        /**
         * Low level operations on form.
         */
        form: {
            toNameWithIndex: function(name, indexes) {
                var indexString = name;
                for (var i = 0; i < indexes.length; i++) {
                    indexString = indexString + '[' + indexes[i] + ']';
                }
                return indexString;
            },
            getEl: function(name, indexes) {
                var form = document.getElementById('mform1');
                return form.elements[this.toNameWithIndex(name, indexes)];
            },
            getFormValue: function(name, indexes) {
                var el = this.getEl(name, indexes);
                if (el.type === 'checkbox') {
                    return el.checked;
                } else {
                    return el.value;
                }
            },
            setFormValue: function(name, indexes, value) {
                var el = this.getEl(name, indexes);
                if (el.type === 'checkbox') {
                    el.checked = value;
                } else {
                    el.value = value;
                }
            }
        },

        /**
         * Utility to get the file name and url from the filepicker.
         * @returns {Object} object containing functions {file, name}
         */
        filePickers: function() {
            var draftItemIdsToName;
            var nameToParentNode;
            var fp;
            if (draftItemIdsToName === undefined) {
                draftItemIdsToName = {};
                nameToParentNode = {};
                fp = $('form.mform input.filepickerhidden');
                fp.each(function(key, filepicker) {
                    draftItemIdsToName[filepicker.value] = filepicker.name;
                    nameToParentNode[filepicker.name] = filepicker.parentNode;
                });
            }
            return {
                file: function(name) {
                    var parentNode = $(nameToParentNode[name]);
                    var fileAnchor = parentNode.find('div.filepicker-filelist a');
                    if (fileAnchor.length) {
                        return {href: fileAnchor.get(0).href, name: fileAnchor.get(0).innerHTML};
                    } else {
                        return {href: null, name: null};
                    }
                },
                name: function(draftitemid) {
                    return draftItemIdsToName[draftitemid];
                }
            };
        }
    };

    return t;
});
