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

/*
 * JavaScript to allow dragging options to slots (using mouse down or touch) or tab through slots using keyboard.
 *
 * @module     qtype_ddimageortext/form
 * @package    qtype_ddimageortext
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/dragdrop'], function($, dragDrop) {

    "use strict";

    /**
     * Singleton object to handle progressive enhancement of the
     * drag-drop onto image question editing form.
     * @type {Object}
     */
    var dragDropToImageForm = {
        /**
         * @var {Object} with properties width and height.
         */
        maxBgImageSize: null,

        /**
         * @var {Object} with properties width and height.
         */
        maxDragImageSize: null,

        /**
         * @var {object} for interacting with the file pickers.
         */
        fp: null, // Object containing functions associated with the file picker.

        /**
         * Initialise the form javascript features.
         */
        init: function() {
            dragDropToImageForm.fp = dragDropToImageForm.filePickers();

            $('#id_previewareaheader').append(
                '<div class="ddarea que ddimageortext">' +
                '  <div class="droparea">' +
                '    <img class="dropbackground" />' +
                '    <div class="dropzones"></div>' +
                '  </div>' +
                '  <div class="dragitems"></div>' +
                '</div>');

            dragDropToImageForm.updateVisibilityOfFilePickers();
            dragDropToImageForm.setOptionsForDragItemSelectors();
            dragDropToImageForm.setupEventHandlers();
            dragDropToImageForm.waitForFilePickerToInitialise();
        },

        /**
         * Waits for the file-pickers to be sufficiently ready before initialising the preview.
         */
        waitForFilePickerToInitialise: function() {
            if (dragDropToImageForm.fp.file('bgimage').href === null) {
                // It would be better to use an onload or onchange event rather than this timeout.
                // Unfortunately attempts to do this early are overwritten by filepicker during its loading.
                setTimeout(dragDropToImageForm.waitForFilePickerToInitialise, 1000);
                return;
            }
            M.util.js_pending('dragDropToImageForm');

            // From now on, when a new file gets loaded into the filepicker, update the preview.
            // This is not in the setupEventHandlers section as it needs to be delayed until
            // after filepicker's javascript has finished.
            $('form.mform[data-qtype="ddimageortext"]').on('change', '.filepickerhidden', function() {
                M.util.js_pending('dragDropToImageForm');
                dragDropToImageForm.loadPreviewImage();
            });

            dragDropToImageForm.loadPreviewImage();
        },

        /**
         * Loads the preview background image.
         */
        loadPreviewImage: function() {
            $('fieldset#id_previewareaheader .dropbackground')
                .one('load', dragDropToImageForm.afterPreviewImageLoaded)
                .attr('src', dragDropToImageForm.fp.file('bgimage').href);
        },

        /**
         * After the background image is loaded, continue setting up the preview.
         */
        afterPreviewImageLoaded: function() {
            dragDropToImageForm.createDropZones();
            M.util.js_complete('dragDropToImageForm');
        },

        /**
         * Create, or recreate all the drop zones.
         */
        createDropZones: function() {
            var dropZoneHolder = $('.dropzones');
            dropZoneHolder.empty();

            var bgimageurl = dragDropToImageForm.fp.file('bgimage').href;
            if (bgimageurl === null) {
                return; // There is not currently a valid preview to update.
            }

            var numDrops = dragDropToImageForm.form.getFormValue('nodropzone', []);
            for (var dropNo = 0; dropNo < numDrops; dropNo++) {
                var dragNo = dragDropToImageForm.form.getFormValue('drops', [dropNo, 'choice']);
                if (dragNo === '0') {
                    continue;
                }
                dragNo = dragNo - 1;
                var group = dragDropToImageForm.form.getFormValue('drags', [dragNo, 'draggroup']),
                    label = dragDropToImageForm.form.getFormValue('draglabel', [dragNo]);
                if ('image' === dragDropToImageForm.form.getFormValue('drags', [dragNo, 'dragitemtype'])) {
                    var imgUrl = dragDropToImageForm.fp.file('dragitem[' + dragNo + ']').href;
                    if (imgUrl === null) {
                        continue;
                    }
                    // Althoug these are previews of drops, we also add the class name 'drag',
                    dropZoneHolder.append('<img class="droppreview group' + group + ' drop' + dropNo +
                            '" src="' + imgUrl + '" alt="' + label + '" data-drop-no="' + dropNo + '">');

                } else if (label !== '') {
                    dropZoneHolder.append('<div class="droppreview group' + group + ' drop' + dropNo +
                        '"  data-drop-no="' + dropNo + '">' + label + '</div>');
                }
            }

            dragDropToImageForm.waitForAllDropImagesToBeLoaded();
        },

        /**
         * This polls until all the drop-zone images have loaded, and then calls updateDropZones().
         */
        waitForAllDropImagesToBeLoaded: function() {
            var notYetLoadedImages = $('.dropzones img').not(function(i, imgNode) {
                return dragDropToImageForm.imageIsLoaded(imgNode);
            });

            if (notYetLoadedImages.length > 0) {
                setTimeout(function() {
                    dragDropToImageForm.waitForAllDropImagesToBeLoaded();
                }, 100);
                return;
            }

            dragDropToImageForm.updateDropZones();
        },

        /**
         * Check if an image has loaded without errors.
         *
         * @param {HTMLImageElement} imgElement an image.
         * @returns {boolean} true if this image has loaded without errors.
         */
        imageIsLoaded: function(imgElement) {
            return imgElement.complete && imgElement.naturalHeight !== 0;
        },

        /**
         * Set the size and position of all the drop zones.
         */
        updateDropZones: function() {
            var bgimageurl = dragDropToImageForm.fp.file('bgimage').href;
            if (bgimageurl === null) {
                return; // There is not currently a valid preview to update.
            }

            var dropBackgroundPosition = $('fieldset#id_previewareaheader .dropbackground').offset(),
                numDrops = dragDropToImageForm.form.getFormValue('nodropzone', []);

            // Move each drop to the right position and update the text.
            for (var dropNo = 0; dropNo < numDrops; dropNo++) {
                var drop = $('.dropzones .drop' + dropNo);
                if (drop.length === 0) {
                    continue;
                }
                var dragNo = dragDropToImageForm.form.getFormValue('drops', [dropNo, 'choice']) - 1;

                drop.offset({
                    left: dropBackgroundPosition.left +
                            parseInt(dragDropToImageForm.form.getFormValue('drops', [dropNo, 'xleft'])),
                    top: dropBackgroundPosition.top +
                            parseInt(dragDropToImageForm.form.getFormValue('drops', [dropNo, 'ytop']))
                });

                var label = dragDropToImageForm.form.getFormValue('draglabel', [dragNo]);
                if (drop.is('img')) {
                    drop.attr('alt', label);
                } else {
                    drop.html(label);
                }
            }

            // Resize them to the same size.
            $('.dropzones .droppreview').css('padding', '0');
            var numGroups = $('select.draggroup').first().find('option').length;
            for (var group = 1; group <= numGroups; group++) {
                dragDropToImageForm.resizeAllDragsAndDropsInGroup(group);
            }
        },

        /**
         * In a given group, set all the drags and drops to be the same size.
         *
         * @param {int} group the group number.
         */
        resizeAllDragsAndDropsInGroup: function(group) {
            var drops = $('.dropzones .droppreview.group' + group),
                maxWidth = 0,
                maxHeight = 0;

            // Find the maximum size of any drag in this groups.
            drops.each(function(i, drop) {
                maxWidth = Math.max(maxWidth, Math.ceil(drop.offsetWidth));
                maxHeight = Math.max(maxHeight, Math.ceil(drop.offsetHeight));
            });

            // The size we will want to set is a bit bigger than this.
            maxWidth += 10;
            maxHeight += 10;

            // Set each drag home to that size.
            drops.each(function(i, drop) {
                var left = Math.round((maxWidth - drop.offsetWidth) / 2),
                    top = Math.floor((maxHeight - drop.offsetHeight) / 2);
                // Set top and left padding so the item is centred.
                $(drop).css({
                    'padding-left': left + 'px',
                    'padding-right': (maxWidth - drop.offsetWidth - left) + 'px',
                    'padding-top': top + 'px',
                    'padding-bottom': (maxHeight - drop.offsetHeight - top) + 'px'
                });
            });
        },

        /**
         * Events linked to form actions.
         */
        setupEventHandlers: function() {
            // Changes to settings in the draggable items section.
            $('fieldset#id_draggableitemheader')
                .on('change input', 'input, select', function(e) {
                    var input = $(e.target).closest('select, input');
                    if (input.hasClass('dragitemtype')) {
                        dragDropToImageForm.updateVisibilityOfFilePickers();
                    }

                    dragDropToImageForm.setOptionsForDragItemSelectors();

                    if (input.is('.dragitemtype, .draggroup')) {
                        dragDropToImageForm.createDropZones();
                    } else if (input.is('.draglabel')) {
                        dragDropToImageForm.updateDropZones();
                    }
                });

            // Changes to Drop zones section: left, top and drag item.
            $('fieldset#id_dropzoneheader').on('change input', 'input, select', function(e) {
                var input = $(e.target).closest('select, input');
                if (input.is('select')) {
                    dragDropToImageForm.createDropZones();
                } else {
                    dragDropToImageForm.updateDropZones();
                }
            });

            // Moving drop zones in the preview.
            $('fieldset#id_previewareaheader').on('mousedown touchstart', '.droppreview', function(e) {
                dragDropToImageForm.dragStart(e);
            });

            $(window).on('resize', function() {
                dragDropToImageForm.updateDropZones();
            });
        },

        /**
         * Update all the drag item filepickers, so they are only shown for
         */
        updateVisibilityOfFilePickers: function() {
            var numDrags = dragDropToImageForm.form.getFormValue('noitems', []);
            for (var dragNo = 0; dragNo < numDrags; dragNo++) {
                var picker = $('input#id_dragitem_' + dragNo).closest('.fitem_ffilepicker');
                if ('image' === dragDropToImageForm.form.getFormValue('drags', [dragNo, 'dragitemtype'])) {
                    picker.show();
                } else {
                    picker.hide();
                }
            }
        },


        setOptionsForDragItemSelectors: function() {
            var dragItemOptions = {'0': ''},
                numDrags = dragDropToImageForm.form.getFormValue('noitems', []),
                numDrops = dragDropToImageForm.form.getFormValue('nodropzone', []);

            // Work out the list of options.
            for (var dragNo = 0; dragNo < numDrags; dragNo++) {
                var label = dragDropToImageForm.form.getFormValue('draglabel', [dragNo]);
                var file = dragDropToImageForm.fp.file(dragDropToImageForm.form.toNameWithIndex('dragitem', [dragNo]));
                if ('image' === dragDropToImageForm.form.getFormValue('drags', [dragNo, 'dragitemtype']) && file.name !== null) {
                    dragItemOptions[dragNo + 1] = (dragNo + 1) + '. ' + label + ' (' + file.name + ')';
                } else if (label !== '') {
                    dragItemOptions[dragNo + 1] = (dragNo + 1) + '. ' + label;
                }
            }

            // Initialise each select.
            for (var dropNo = 0; dropNo < numDrops; dropNo++) {
                var selector = $('#id_drops_' + dropNo + '_choice');

                var selectedvalue = selector.val();
                selector.find('option').remove();
                for (var value in dragItemOptions) {
                    if (!dragItemOptions.hasOwnProperty(value)) {
                        continue;
                    }
                    selector.append('<option value="' + value + '">' + dragItemOptions[value] + '</option>');
                    var optionnode = selector.find('option[value="' + value + '"]');
                    if (parseInt(value) === parseInt(selectedvalue)) {
                        optionnode.attr('selected', true);
                    } else if (dragDropToImageForm.isItemUsed(parseInt(value))) {
                        optionnode.attr('disabled', true);
                    }
                }
            }
        },

        /**
         * Checks if the specified drag option is already used somewhere.
         *
         * @param {Number} value of the drag item to check
         * @return {Boolean} true if item is allocated to dropzone
         */
        isItemUsed: function(value) {
            if (value === 0) {
                return false; // None option can always be selected.
            }

            if (dragDropToImageForm.form.getFormValue('drags', [value - 1, 'infinite'])) {
                return false; // Infinite, so can't be used up.
            }

            return $('fieldset#id_dropzoneheader select').filter(function(i, selectNode) {
                return parseInt($(selectNode).val()) === value;
            }).length !== 0;
        },

        /**
         * Handles when a dropzone in dragged in the preview.
         * @param {Object} e Event object
         */
        dragStart: function(e) {
            var drop = $(e.target).closest('.droppreview');

            var info = dragDrop.prepare(e);
            if (!info.start) {
                return;
            }

            dragDrop.start(e, drop, function(x, y, drop) {
                dragDropToImageForm.dragMove(drop);
            }, function() {
                dragDropToImageForm.dragEnd();
            });
        },

        /**
         * Handles update while a drop is being dragged.
         *
         * @param {jQuery} drop the drop preview being moved.
         */
        dragMove: function(drop) {
            var backgroundImage = $('fieldset#id_previewareaheader .dropbackground'),
                backgroundPosition = backgroundImage.offset(),
                dropNo = drop.data('dropNo'),
                dropPosition = drop.offset(),
                left = Math.round(dropPosition.left - backgroundPosition.left),
                top = Math.round(dropPosition.top - backgroundPosition.top);

            // Constrain coordinates to be inside the background.
            // The -10 here matches the +10 in resizeAllDragsAndDropsInGroup().
            left = Math.max(0, Math.min(left, backgroundImage.width() - drop.width() - 10));
            top = Math.max(0, Math.min(top, backgroundImage.height() - drop.height() - 10));

            // Update the form.
            dragDropToImageForm.form.setFormValue('drops', [dropNo, 'xleft'], left);
            dragDropToImageForm.form.setFormValue('drops', [dropNo, 'ytop'], top);
        },

        /**
         * Handles when the drag ends.
         */
        dragEnd: function() {
            // Redraw, in case the position was constrained.
            dragDropToImageForm.updateDropZones();
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
                var form = $('form.mform[data-qtype="ddimageortext"]')[0];
                return form.elements[this.toNameWithIndex(name, indexes)];
            },

            /**
             * Helper to get the value of a form elements with name like "drops[0][xleft]".
             *
             * @param {String} name the base name, e.g. 'drops'.
             * @param {String[]} indexes the indexes, e.g. ['0', 'xleft'].
             * @return {String} the value of that field.
             */
            getFormValue: function(name, indexes) {
                var el = this.getEl(name, indexes);
                if (!el.type) {
                    el = el[el.length - 1];
                }
                if (el.type === 'checkbox') {
                    return el.checked;
                } else {
                    return el.value;
                }
            },

            /**
             * Helper to get the value of a form elements with name like "drops[0][xleft]".
             *
             * @param {String} name the base name, e.g. 'drops'.
             * @param {String[]} indexes the indexes, e.g. ['0', 'xleft'].
             * @param {String|Number} value the value to set.
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
                var fp = $('form.mform[data-qtype="ddimageortext"] input.filepickerhidden');
                fp.each(function(index, filepicker) {
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

    /**
     * @alias module:qtype_ddimageortext/form
     */
    return {
        /**
         * Initialise the form JavaScript features.
         */
        init: dragDropToImageForm.init
    };
});
