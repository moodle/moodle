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
 * This class provides the enhancements to the drag-drop marker editing form.
 *
 * @package    qtype_ddmarker
 * @subpackage form
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/dragdrop', 'qtype_ddmarker/shapes'], function($, dragDrop, Shapes) {

    "use strict";

    /**
     * Create the manager object that deals with keeping everything synchronised for one drop zone.
     *
     * @param {int} dropzoneNo the index of this drop zone in the form. 0, 1, ....
     * @constructor
     */
    function DropZoneManager(dropzoneNo) {
        this.dropzoneNo = dropzoneNo;
        this.svgEl = null;

        this.shape = Shapes.make(this.getShapeType(), this.getLabel());
        this.updateCoordinatesFromForm();
    }

    /**
     * Update the coordinates from a particular string.
     *
     * @param {SVGElement} [svg] the SVG element that is the preview.
     */
    DropZoneManager.prototype.updateCoordinatesFromForm = function(svg) {
        var coordinates = this.getCoordinates(),
            currentNumPoints = this.shape.getType() === 'polygon' && this.shape.points.length;
        if (this.shape.getCoordinates() === coordinates) {
            return;
        }
        if (!this.shape.parse(coordinates)) {
            // Invalid coordinates. Don't update the preview.
            return;
        }

        if (this.shape.getType() === 'polygon' && currentNumPoints !== this.shape.points.length) {
            // Polygon, and size has changed.
            var currentyActive = this.isActive();
            this.removeFromSvg();
            if (svg) {
                this.addToSvg(svg);
                if (currentyActive) {
                    this.setActive();
                }
            }
        } else {
            // Simple update.
            this.updateSvgEl();
        }
    };

    /**
     * Update the label.
     */
    DropZoneManager.prototype.updateLabel = function() {
        var label = this.getLabel();
        if (this.shape.label !== label) {
            this.shape.label = label;
            this.updateSvgEl();
        }
    };

    /**
     * Handle if the type of shape has changed.
     *
     * @param {SVGElement} [svg] an SVG element to add this new shape to.
     */
    DropZoneManager.prototype.changeShape = function(svg) {
        var newShapeType = this.getShapeType(),
            currentyActive = this.isActive();

        if (newShapeType === this.shape.getType()) {
            return;
        }

        // It has really changed.
        this.removeFromSvg();
        this.shape = Shapes.getSimilar(newShapeType, this.shape);
        if (svg) {
            this.addToSvg(svg);
            if (currentyActive) {
                this.setActive();
            }
        }
        this.setCoordinatesInForm();
    };

    /**
     * Add this drop zone to an SVG graphic.
     *
     * @param {SVGElement} svg the SVG image to which to add this drop zone.
     */
    DropZoneManager.prototype.addToSvg = function(svg) {
        if (this.svgEl !== null) {
            throw new Error('this.svgEl already set');
        }
        this.svgEl = this.shape.makeSvg(svg);
        if (!this.svgEl) {
            return;
        }
        this.svgEl.setAttribute('class', 'dropzone');
        this.svgEl.setAttribute('data-dropzone-no', this.dropzoneNo);

        // Add handles.
        var handles = this.shape.getHandlePositions();
        if (handles === null) {
            return;
        }

        var moveHandle = Shapes.createSvgElement(this.svgEl, 'circle');
        moveHandle.setAttribute('cx', handles.moveHandle.x);
        moveHandle.setAttribute('cy', handles.moveHandle.y);
        moveHandle.setAttribute('r', 7);
        moveHandle.setAttribute('class', 'handle move');

        for (var i = 0; i < handles.editHandles.length; ++i) {
            this.makeEditHandle(i, handles.editHandles[i]);
        }
    };

    /**
     * Add a new edit handle.
     *
     * @param {int} index the handle index.
     * @param {Point} point the point at which to add the handle.
     */
    DropZoneManager.prototype.makeEditHandle = function(index, point) {
        var editHandle = Shapes.createSvgElement(this.svgEl, 'rect');
        editHandle.setAttribute('x', point.x - 6);
        editHandle.setAttribute('y', point.y - 6);
        editHandle.setAttribute('width', 11);
        editHandle.setAttribute('height', 11);
        editHandle.setAttribute('class', 'handle edit');
        editHandle.setAttribute('data-edit-handle-no', index);
    };

    /**
     * Remove this drop zone from an SVG image.
     */
    DropZoneManager.prototype.removeFromSvg = function() {
        if (this.svgEl !== null) {
            this.svgEl.parentNode.removeChild(this.svgEl);
            this.svgEl = null;
        }
    };

    /**
     * Update the shape of this drop zone (but not type) in an SVG image.
     */
    DropZoneManager.prototype.updateSvgEl = function() {
        if (this.svgEl === null) {
            return;
        }

        this.shape.updateSvg(this.svgEl);

        // Adjust handles.
        var handles = this.shape.getHandlePositions();
        if (handles === null) {
            return;
        }

        // Move handle.
        // The shape + its label are the first two children of svgEl.
        // Then come the move handle followed by the edit handles.
        this.svgEl.childNodes[2].setAttribute('cx', handles.moveHandle.x);
        this.svgEl.childNodes[2].setAttribute('cy', handles.moveHandle.y);

        // Edit handles.
        for (var i = 0; i < handles.editHandles.length; ++i) {
            this.svgEl.childNodes[3 + i].setAttribute('x', handles.editHandles[i].x - 6);
            this.svgEl.childNodes[3 + i].setAttribute('y', handles.editHandles[i].y - 6);
        }
    };

    /**
     * Find out of this drop zone is currently being edited.
     *
     * @return {boolean} true if it is.
     */
    DropZoneManager.prototype.isActive = function() {
        return this.svgEl !== null && this.svgEl.getAttribute('class').match(/\bactive\b/);
    };

    /**
     * Set this drop zone as being edited.
     */
    DropZoneManager.prototype.setActive = function() {
        // Move this one to last, so that it is always on top.
        // (Otherwise the handles may not be able to receive events.)
        var parent = this.svgEl.parentNode;
        parent.removeChild(this.svgEl);
        parent.appendChild(this.svgEl);
        this.svgEl.setAttribute('class', this.svgEl.getAttribute('class') + ' active');
    };

    /**
     * Set the coordinates in the form to match the current shape.
     */
    DropZoneManager.prototype.setCoordinatesInForm = function() {
        dragDropForm.form.setFormValue('drops', [this.dropzoneNo, 'coords'], this.shape.getCoordinates());
    };

    /**
     * Returns the coordinates for a drop zone from the text input in the form.
     * @returns {string} the coordinates.
     */
    DropZoneManager.prototype.getCoordinates = function() {
        return dragDropForm.form.getFormValue('drops', [this.dropzoneNo, 'coords']).replace(/\s*/g, '');
    };

    /**
     * Returns the selected marker number from the dropdown in the form.
     * @returns {int} choice number.
     */
    DropZoneManager.prototype.getChoiceNo = function() {
        return dragDropForm.form.getFormValue('drops', [this.dropzoneNo, 'choice']);
    };

    /**
     * Returns the selected marker number from the dropdown in the form.
     * @returns {String} marker label text.
     */
    DropZoneManager.prototype.getLabel = function() {
        return dragDropForm.form.getMarkerText(this.getChoiceNo());
    };


    /**
     * Returns the selected type of shape in the form.
     * @returns {String} 'circle', 'rectangle' or 'polygon'.
     */
    DropZoneManager.prototype.getShapeType = function() {
        return dragDropForm.form.getFormValue('drops', [this.dropzoneNo, 'shape']);
    };

    /**
     * Start responding to dragging the move handle.
     * @param {Event} e Event object
     */
    DropZoneManager.prototype.handleMove = function(e) {
        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        var movingDropZone = this,
                lastX = info.x,
                lastY = info.y,
                dragProxy = this.makeDragProxy(info.x, info.y),
                bgImg = $('fieldset#id_previewareaheader .dropbackground'),
                maxX = bgImg.width(),
                maxY = bgImg.height();

        dragDrop.start(e, $(dragProxy), function(pageX, pageY) {
            movingDropZone.shape.move(pageX - lastX, pageY - lastY, maxX, maxY);
            lastX = pageX;
            lastY = pageY;
            movingDropZone.updateSvgEl();
            movingDropZone.setCoordinatesInForm();
        }, function() {
            document.body.removeChild(dragProxy);
        });
    };

    /**
     * Start responding to dragging the move handle.
     * @param {Event} e Event object
     * @param {int} handleIndex
     * @param {SVGElement} [svg] an SVG element to add this new shape to.
     */
    DropZoneManager.prototype.handleEdit = function(e, handleIndex, svg) {
        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        // For polygons, CTRL + drag adds a new point.
        if (this.shape.getType() === 'polygon' && (e.ctrlKey || e.metaKey)) {
            this.shape.addNewPointAfter(handleIndex);
            this.removeFromSvg();
            this.addToSvg(svg);
            this.setActive();
        }

        var changingDropZone = this,
            lastX = info.x,
            lastY = info.y,
            dragProxy = this.makeDragProxy(info.x, info.y),
            bgImg = $('fieldset#id_previewareaheader .dropbackground'),
            maxX = bgImg.width(),
            maxY = bgImg.height();

        dragDrop.start(e, $(dragProxy), function(pageX, pageY) {
            changingDropZone.shape.edit(handleIndex, pageX - lastX, pageY - lastY, maxX, maxY);
            lastX = pageX;
            lastY = pageY;
            changingDropZone.updateSvgEl();
            changingDropZone.setCoordinatesInForm();
        }, function() {
            document.body.removeChild(dragProxy);
            changingDropZone.shape.normalizeShape();
            changingDropZone.updateSvgEl();
            changingDropZone.setCoordinatesInForm();
        });
    };

    /**
     * Make an invisible drag proxy.
     *
     * @param {int} x x position .
     * @param {int} y y position.
     * @returns {HTMLElement} the drag proxy.
     */
    DropZoneManager.prototype.makeDragProxy = function(x, y) {
        var dragProxy = document.createElement('div');
        dragProxy.style.position = 'absolute';
        dragProxy.style.top = y + 'px';
        dragProxy.style.left = x + 'px';
        dragProxy.style.width = '1px';
        dragProxy.style.height = '1px';
        document.body.appendChild(dragProxy);
        return dragProxy;
    };

    /**
     * Singleton object for managing all the parts of the form.
     */
    var dragDropForm = {

        /**
         * @var {object} with properties width and height.
         */
        maxSizes: null, // Object containing maximum sizes for the background image.

        /**
         * @var {object} for interacting with the file pickers.
         */
        fp: null, // Object containing functions associated with the file picker.

        /**
         * @var {int} the number of drop-zones on the form.
         */
        noDropZones: null,

        /**
         * @var {DropZoneManager[]} the drop zones in the preview, indexed by drop zone number.
         */
        dropZones: [],

        /**
         * Initialise the form.
         *
         * @param {Object} maxBgimageSize object with two properties width and height.
         */
        init: function(maxBgimageSize) {
            dragDropForm.maxSizes = maxBgimageSize;
            dragDropForm.fp = dragDropForm.filePickers();
            dragDropForm.noDropZones = dragDropForm.form.getFormValue('nodropzone', []);
            dragDropForm.setupPreviewArea();
            dragDropForm.setOptionsForDragItemSelectors();
            dragDropForm.createShapes();
            dragDropForm.setupEventHandlers();
            dragDropForm.waitForFilePickerToInitialise();
        },

        /**
         * Add html for the preview area.
         */
        setupPreviewArea: function() {
            $('fieldset#id_previewareaheader div.fcontainer').append(
                '<div class="ddarea que ddmarker">' +
                '   <div id="ddm-droparea" class="droparea">' +
                '       <img class="dropbackground" />' +
                '       <div id="ddm-dropzone" class="dropzones">' +
                '       </div>' +
                '   </div>' +
                '</div>');
        },

        /**
         * When a new marker is added this function updates the Marker dropdown controls in Drop zones.
         */
        setOptionsForDragItemSelectors: function() {
            var dragItemsOptions = {'0': ''};
            var noItems = dragDropForm.form.getFormValue('noitems', []);
            var selectedValues = [];
            var selector;
            var i, label;
            for (i = 1; i <= noItems; i++) {
                label = dragDropForm.form.getMarkerText(i);
                if (label !== "") {
                    // HTML escape the label.
                    dragItemsOptions[i] = $('<div/>').text(label).html();
                }
            }
            // Get all the currently selected drags for each drop.
            for (i = 0; i < dragDropForm.noDropZones; i++) {
                selector = $('#id_drops_' + i + '_choice');
                selectedValues[i] = Number(selector.val());
            }
            for (i = 0; i < dragDropForm.noDropZones; i++) {
                selector = $('#id_drops_' + i + '_choice');
                // Remove all options for drag choice.
                selector.find('option').remove();
                // And recreate the options.
                for (var value in dragItemsOptions) {
                    value = Number(value);
                    var option = '<option value="' + value + '">' + dragItemsOptions[value] + '</option>';
                    selector.append(option);
                    var optionnode = selector.find('option[value="' + value + '"]');


                    if (value === 0) {
                        continue; // The 'no item' option is always selectable.
                    }

                    // Is this the currently selected value?
                    if (value === selectedValues[i]) {
                        optionnode.attr('selected', true);
                        continue; // If it s selected, we must leave it enabled.
                    }

                    // Count how many times it is used, and if necessary, disable.
                    var noofdrags = dragDropForm.form.getFormValue('drags', [value - 1, 'noofdrags']);
                    if (Number(noofdrags) === 0) { // 'noofdrags === 0' means infinite.
                        continue; // Nothing to check.
                    }

                    // Go through all selected values in drop downs.
                    for (var k in selectedValues) {
                        if (Number(selectedValues[k]) !== value) {
                            continue;
                        }

                        // Count down 'noofdrags' and if reach zero then set disabled option for this drag item.
                        if (Number(noofdrags) === 1) {
                            optionnode.attr('disabled', true);
                            break;
                        } else {
                            noofdrags--;
                        }
                    }
                }

                if (dragDropForm.dropZones.length > 0) {
                    dragDropForm.dropZones[i].updateLabel();
                }
            }
        },

        /**
         * Create the shape representation of each dropZone.
         */
        createShapes: function() {
            for (var dropzoneNo = 0; dropzoneNo < dragDropForm.noDropZones; dropzoneNo++) {
                dragDropForm.dropZones[dropzoneNo] = new DropZoneManager(dropzoneNo);
            }
        },

        /**
         * Events linked to form actions.
         */
        setupEventHandlers: function() {
            // Changes to labels in the Markers section.
            $('fieldset#id_draggableitemheader').on('change input', 'input, select', function() {
                dragDropForm.setOptionsForDragItemSelectors();
            });

            // Changes to Drop zones section: shape, coordinates and marker.
            $('fieldset#id_dropzoneheader').on('change input', 'input, select', function(e) {
                var ids = e.currentTarget.name.match(/^drops\[(\d+)]\[([a-z]*)]$/);
                if (!ids) {
                    return;
                }

                var dropzoneNo = ids[1],
                    inputType = ids[2],
                    dropZone = dragDropForm.dropZones[dropzoneNo];

                switch (inputType) {
                    case 'shape':
                        dropZone.changeShape(dragDropForm.form.getSvg());
                        break;

                    case 'coords':
                        dropZone.updateCoordinatesFromForm(dragDropForm.form.getSvg());
                        break;

                    case 'choice':
                        dropZone.updateLabel();
                        break;
                }
            });

            // Click to toggle graphical editing.
            var previewArea = $('fieldset#id_previewareaheader');
            previewArea.on('click', 'g.dropzone', function(e) {
                var dropzoneNo = $(e.currentTarget).data('dropzone-no'),
                    currentlyActive = dragDropForm.dropZones[dropzoneNo].isActive();

                $(dragDropForm.form.getSvg()).find('.dropzone.active').removeClass('active');

                if (!currentlyActive) {
                    dragDropForm.dropZones[dropzoneNo].setActive();
                }
            });

            // Drag start on a move handle.
            previewArea.on('mousedown touchstart', '.dropzone .handle.move', function(e) {
                var dropzoneNo = $(e.currentTarget).closest('g').data('dropzoneNo');

                dragDropForm.dropZones[dropzoneNo].handleMove(e);
            });

            // Drag start on a move handle.
            previewArea.on('mousedown touchstart', '.dropzone .handle.edit', function(e) {
                var dropzoneNo = $(e.currentTarget).closest('g').data('dropzoneNo'),
                    handleIndex = e.currentTarget.getAttribute('data-edit-handle-no');

                dragDropForm.dropZones[dropzoneNo].handleEdit(e, handleIndex, dragDropForm.form.getSvg());
            });
        },

        /**
         * Prevents adding drop zones until the preview background image is ready to load.
         */
        waitForFilePickerToInitialise: function() {
            if (dragDropForm.fp.file('bgimage').href === null) {
                // It would be better to use an onload or onchange event rather than this timeout.
                // Unfortunately attempts to do this early are overwritten by filepicker during its loading.
                setTimeout(dragDropForm.waitForFilePickerToInitialise, 1000);
                return;
            }

            // From now on, when a new file gets loaded into the filepicker, update the preview.
            // This is not in the setupEventHandlers section as it needs to be delayed until
            // after filepicker's javascript has finished.
            $('form.mform').on('change', '#id_bgimage', dragDropForm.loadPreviewImage);

            dragDropForm.loadPreviewImage();
        },

        /**
         * Loads the preview background image.
         */
        loadPreviewImage: function() {
            $('fieldset#id_previewareaheader .dropbackground')
                    .one('load', dragDropForm.afterPreviewImageLoaded)
                    .attr('src', dragDropForm.fp.file('bgimage').href);
        },

        /**
         * Functions to run after background image loaded.
         */
        afterPreviewImageLoaded: function() {
            var bgImg = $('fieldset#id_previewareaheader .dropbackground');
            dragDropForm.constrainImageSize();
            // Place the dropzone area over the background image (adding one to account for the border).
            $('#ddm-dropzone').css('position', 'relative').css('top', (bgImg.height() + 1) * -1);
            $('#ddm-droparea').css('height', bgImg.height() + 20);
            dragDropForm.updateSvgDisplay();
        },

        /**
         * Limits the background image display size.
         */
        constrainImageSize: function() {
            var bgImg = $('fieldset#id_previewareaheader .dropbackground');
            var reduceby = Math.max(bgImg.width() / dragDropForm.maxSizes.width,
                bgImg.height() / dragDropForm.maxSizes.height);
            if (reduceby > 1) {
                bgImg.css('width', Math.floor(bgImg.width() / reduceby));
            }
            bgImg.addClass('constrained');
        },

        /**
         * Draws or re-draws all dropzones in the preview area based on form data.
         * Call this function when there is a change in the form data.
         */
        updateSvgDisplay: function() {
            var bgImg = $('fieldset#id_previewareaheader .dropbackground'),
                dropzoneNo;

            if (dragDropForm.form.getSvg()) {
                // Already exists, just need to be updated.
                for (dropzoneNo = 0; dropzoneNo < dragDropForm.noDropZones; dropzoneNo++) {
                    dragDropForm.dropZones[dropzoneNo].updateSvgEl();
                }

            } else {
                // Create.
                $('#ddm-dropzone').html('<svg xmlns="http://www.w3.org/2000/svg" class="dropzones" ' +
                    'width="' + bgImg.outerWidth() + '" ' +
                    'height="' + bgImg.outerHeight() + '"></svg>');
                for (dropzoneNo = 0; dropzoneNo < dragDropForm.noDropZones; dropzoneNo++) {
                    dragDropForm.dropZones[dropzoneNo].addToSvg(dragDropForm.form.getSvg());
                }
            }
        },

        /**
         * Helper to make it easy to work with form elements with names like "drops[0][shape]".
         */
        form: {
            /**
             * Returns the label text for a marker.
             * @param {int} markerNo
             * @returns {string} Marker text
             */
            getMarkerText: function(markerNo) {
                if (Number(markerNo) !== 0) {
                    var label = dragDropForm.form.getFormValue('drags', [markerNo - 1, 'label']);
                    return label.replace(new RegExp("^\\s*(.*)\\s*$"), "$1");
                } else {
                    return '';
                }
            },

            /**
             * Get the SVG element, if there is one, otherwise return null.
             *
             * @returns {SVGElement|null} the SVG element or null.
             */
            getSvg: function() {
                var svg = $('fieldset#id_previewareaheader svg');
                if (svg.length === 0) {
                    return null;
                } else {
                    return svg[0];
                }
            },

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

            /**
             * Helper to get the value of a form elements with name like "drops[0][shape]".
             *
             * @param {String} name the base name, e.g. 'drops'.
             * @param {String[]} indexes the indexes, e.g. ['0', 'shape'].
             * @return {String} the value of that field.
             */
            getFormValue: function(name, indexes) {
                var el = this.getEl(name, indexes);
                if (el.type === 'checkbox') {
                    return el.checked;
                } else {
                    return el.value;
                }
            },

            /**
             * Helper to get the value of a form elements with name like "drops[0][shape]".
             *
             * @param {String} name the base name, e.g. 'drops'.
             * @param {String[]} indexes the indexes, e.g. ['0', 'shape'].
             * @param {String} value the value to set.
             */
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
            if (draftItemIdsToName === undefined) {
                draftItemIdsToName = {};
                nameToParentNode = {};
                $('form.mform input.filepickerhidden').each(function(key, filepicker) {
                    draftItemIdsToName[filepicker.value] = filepicker.name;
                    nameToParentNode[filepicker.name] = filepicker.parentNode;
                });
            }
            return {
                file: function(name) {
                    var fileAnchor = $(nameToParentNode[name]).find('div.filepicker-filelist a');
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

    /**
     * @alias module:qtype_ddmarker/form
     */
    return {
        /**
         * Initialise the form javascript features.
         * @param {Object} maxBgimageSize object with two properties: width and height.
         */
        init: dragDropForm.init
    };
});
