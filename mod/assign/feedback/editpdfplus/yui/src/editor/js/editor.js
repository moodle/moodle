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
/* eslint-disable no-unused-vars */
/* global SELECTOR, TOOLSELECTOR, TOOLTYPE, TOOLTYPELIB, AJAXBASE, ANNOTATIONCOLOUR, AJAXBASEPROGRESS, CLICKTIMEOUT, Y, M */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * EDITOR
 * This is an in browser PDF editor.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class editor
 * @constructor
 * @extends Y.Base
 */
var EDITOR = function () {
    EDITOR.superclass.constructor.apply(this, arguments);
};
EDITOR.prototype = {

    /**
     * Store old coordinates of the annotations before rotation happens.
     */
    oldannotationcoordinates: null,

    /**
     * The dialogue used for all action menu displays.
     *
     * @property type
     * @type M.core.dialogue
     * @protected
     */
    dialogue: null,

    /**
     * The panel used for all action menu displays.
     *
     * @property type
     * @type Y.Node
     * @protected
     */
    panel: null,

    /**
     * The number of pages in the pdf.
     *
     * @property pagecount
     * @type Number
     * @protected
     */
    pagecount: 0,

    /**
     * The active page in the editor.
     *
     * @property currentpage
     * @type Number
     * @protected
     */
    currentpage: 0,

    /**
     * A list of page objects. Each page has a list of comments and annotations.
     *
     * @property pages
     * @type array
     * @protected
     */
    pages: [],

    /**
     * The reported status of the document.
     *
     * @property documentstatus
     * @type int
     * @protected
     */
    documentstatus: 0,

    /**
     * The yui node for the loading icon.
     *
     * @property loadingicon
     * @type Node
     * @protected
     */
    loadingicon: null,

    /**
     * Image object of the current page image.
     *
     * @property pageimage
     * @type Image
     * @protected
     */
    pageimage: null,

    /**
     * YUI Graphic class for drawing shapes.
     *
     * @property graphic
     * @type Graphic
     * @protected
     */
    graphic: null,

    /**
     * Info about the current edit operation.
     *
     * @property currentedit
     * @type M.assignfeedback_editpdfplus.edit
     * @protected
     */
    currentedit: new M.assignfeedback_editpdfplus.edit(),

    /**
     * Current drawable.
     *
     * @property currentdrawable
     * @type M.assignfeedback_editpdfplus.drawable|false
     * @protected
     */
    currentdrawable: false,

    /**
     * Current drawables.
     *
     * @property drawables
     * @type array(M.assignfeedback_editpdfplus.drawable)
     * @protected
     */
    drawables: [],

    /**
     * Current annotations.
     *
     * @property drawables
     * @type array(M.assignfeedback_editpdfplus.drawable)
     * @protected
     */
    drawablesannotations: [],

    /**
     * Current annotation when the select tool is used.
     * @property currentannotation
     * @type M.assignfeedback_editpdfplus.annotation
     * @protected
     */
    currentannotation: null,

    /**
     * Track the previous annotation so we can remove selection highlights.
     * @property lastannotation
     * @type M.assignfeedback_editpdfplus.annotation
     * @protected
     */
    lastannotation: null,

    /**
     * Last selected annotation tool
     * @property lastannotationtool
     * @type String
     * @protected
     */
    lastannotationtool: null,

    /**
     * The parents annotations
     * @type Array
     * @protected
     */
    annotationsparent: [],
    /**
     * The student statut to display
     * @type Number
     * @protected
     */
    studentstatut: -1,
    /**
     * The type of annotation (question or not) to display
     * @type Number
     * @protected
     */
    questionstatut: -1,
    /**
     * current annotation which is reviewed
     * @type annotation
     * @protected
     */
    currentannotationreview: null,
    /**
     * id of the current selected resize area
     * @type String
     */
    resizeareaselected: null,

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer: function () {
        var link;

        link = Y.one('#' + this.get('linkid'));

        if (link) {
            link.on('click', this.link_handler, this);
            link.on('key', this.link_handler, 'down:13', this);

            // We call the amd module to see if we can take control of the review panel.
            require(['mod_assign/grading_review_panel'], function (ReviewPanelManager) {
                var panelManager = new ReviewPanelManager();

                var panel = panelManager.getReviewPanel('assignfeedback_editpdfplus');
                if (panel) {
                    panel = Y.one(panel);
                    panel.empty();
                    link.ancestor('.fitem').hide();
                    this.open_in_panel(panel);
                }
                this.currentedit.start = false;
                this.currentedit.end = false;
            }.bind(this));

        }
    },

    /**
     * Called to show/hide buttons and set the current colours.
     * @method refresh_button_state
     */
    refresh_button_state: function () {
        var currenttoolnode, drawingregion, drawingcanvas;

        drawingcanvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

        this.refresh_button_color_state();

        //remove active class for resize areas
        var resizezones = Y.all('.assignfeedback_editpdfplus_resize');
        if (resizezones) {
            resizezones.removeClass('assignfeedback_editpdfplus_resize_active');
        }

        if (this.currentedit.id) {
            currenttoolnode = this.get_dialogue_element('#' + this.currentedit.id);
        } else {
            currenttoolnode = this.get_dialogue_element(TOOLSELECTOR[this.currentedit.tool]);
        }
        if (currenttoolnode) {
            currenttoolnode.addClass('active');
            currenttoolnode.setAttribute('aria-pressed', 'true');
        }
        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        drawingregion.setAttribute('data-currenttool', this.currentedit.tool);

        switch (this.currentedit.tool) {
            case 'drag':
                drawingcanvas.setStyle('cursor', 'move');
                break;
            case 'highlight':
                drawingcanvas.setStyle('cursor', 'text');
                break;
            case 'select':
                drawingcanvas.setStyle('cursor', 'default');
                break;
            case 'resize':
                drawingcanvas.setStyle('cursor', 'default');
                var resizezonespage = Y.all('.assignfeedback_editpdfplus_resize[data-page=' + this.currentpage + ']');
                resizezonespage.addClass('assignfeedback_editpdfplus_resize_active');
                break;
            default:
                drawingcanvas.setStyle('cursor', 'crosshair');
        }
    },

    /**
     * Called to set the current colours
     * @method refresh_button_color_state
     */
    refresh_button_color_state: function () {
        var button;
        button = this.get_dialogue_element(SELECTOR.ANNOTATIONCOLOURBUTTON);
        if (this.currentedit.annotationcolour === "white") {
            button.one('i').setStyle('color', this.currentedit.annotationcolour);
            button.setStyle('background-color', '#EEEEEE');
        } else {
            switch (this.currentedit.annotationcolour) {
                case "yellowlemon":
                    button.one('i').setStyle('color', "#fff44f");
                    break;
                case "yellow":
                    button.one('i').setStyle('color', "rgb(255,207,53)");
                    break;
                default:
                    button.one('i').setStyle('color', this.currentedit.annotationcolour);
                    break;
            }
            button.setStyle('background-color', '');
        }
    },

    /**
     * Called to get the bounds of the drawing region.
     * @method get_canvas_bounds
     */
    get_canvas_bounds: function () {
        var canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                offsetcanvas = canvas.getXY(),
                offsetleft = offsetcanvas[0],
                offsettop = offsetcanvas[1],
                width = parseInt(canvas.getStyle('width'), 10),
                height = parseInt(canvas.getStyle('height'), 10);

        return new M.assignfeedback_editpdfplus.rect(offsetleft, offsettop, width, height);
    },

    /**
     * Called to translate from window coordinates to canvas coordinates.
     * @method get_canvas_coordinates
     * @param M.assignfeedback_editpdfplus.point point in window coordinats.
     */
    get_canvas_coordinates: function (point) {
        var bounds = this.get_canvas_bounds(),
                newpoint = new M.assignfeedback_editpdfplus.point(point.x - bounds.x, point.y - bounds.y);

        bounds.x = bounds.y = 0;

        newpoint.clip(bounds);
        return newpoint;
    },

    /**
     * Called to translate from canvas coordinates to window coordinates.
     * @method get_window_coordinates
     * @param M.assignfeedback_editpdfplus.point point in window coordinats.
     */
    get_window_coordinates: function (point) {
        var bounds = this.get_canvas_bounds(),
                newpoint = new M.assignfeedback_editpdfplus.point(point.x + bounds.x, point.y + bounds.y);

        return newpoint;
    },

    /**
     * Open the edit-pdf editor in the panel in the page instead of a popup.
     * @method open_in_panel
     */
    open_in_panel: function (panel) {
        var drawingcanvas, drawingregion;

        this.panel = panel;
        panel.append(this.get('body'));
        panel.addClass(CSS.DIALOGUE);

        this.loadingicon = this.get_dialogue_element(SELECTOR.LOADINGICON);

        drawingcanvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        this.graphic = new Y.Graphic({render: drawingcanvas});

        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        drawingregion.on('scroll', this.move_canvas, this);

        if (!this.get('readonly')) {
            drawingcanvas.on('gesturemovestart', this.edit_start, null, this);
            drawingcanvas.on('gesturemove', this.edit_move, null, this);
            drawingcanvas.on('gesturemoveend', this.edit_end, null, this);

            this.refresh_button_state();

            //trigger when window is resized
            drawingcanvas.on('windowresize', this.resize, this);
            var buttonChooseView = Y.one('.collapse-buttons');
            buttonChooseView.on('click', this.temporise, this, this.resize, 500);
        }

        this.start_generation();
    },

    /**
     * Called to open the pdf editing dialogue.
     * @method link_handler
     */
    link_handler: function (e) {
        var drawingcanvas, drawingregion;
        var resize = true;
        e.preventDefault();

        if (!this.dialogue) {
            this.dialogue = new M.core.dialogue({
                headerContent: this.get('header'),
                bodyContent: this.get('body'),
                footerContent: this.get('footer'),
                modal: true,
                width: '840px',
                visible: false,
                draggable: true
            });

            // Add custom class for styling.
            this.dialogue.get('boundingBox').addClass(CSS.DIALOGUE);

            this.loadingicon = this.get_dialogue_element(SELECTOR.LOADINGICON);

            drawingcanvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
            this.graphic = new Y.Graphic({render: drawingcanvas});

            drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
            drawingregion.on('scroll', this.move_canvas, this);

            if (!this.get('readonly')) {
                drawingcanvas.on('gesturemovestart', this.edit_start, null, this);
                drawingcanvas.on('gesturemove', this.edit_move, null, this);
                drawingcanvas.on('gesturemoveend', this.edit_end, null, this);

                this.refresh_button_state();
            }

            this.start_generation();
            drawingcanvas.on('windowresize', this.resize, this);

            resize = false;
        }
        this.dialogue.centerDialogue();
        this.dialogue.show();

        // Redraw when the dialogue is moved, to ensure the absolute elements are all positioned correctly.
        this.dialogue.dd.on('drag:end', this.redraw, this);
        if (resize) {
            this.resize(); // When re-opening the dialog call redraw, to make sure the size + layout is correct.
        }
    },

    /**
     * Called to load the information and annotations for all pages.
     *
     * @method start_generation
     */
    start_generation: function () {
        this.poll_document_conversion_status();
    },

    /**
     * Poll the current document conversion status and start the next step
     * in the process.
     *
     * @method poll_document_conversion_status
     */
    poll_document_conversion_status: function () {
        var requestUserId = this.get('userid');

        Y.io(AJAXBASE, {
            method: 'get',
            context: this,
            sync: false,
            data: {
                sesskey: M.cfg.sesskey,
                action: 'pollconversions',
                userid: this.get('userid'),
                attemptnumber: this.get('attemptnumber'),
                assignmentid: this.get('assignmentid'),
                readonly: this.get('readonly') ? 1 : 0
            },
            on: {
                success: function (tid, response) {
                    var currentUserRegion = Y.one(SELECTOR.USERINFOREGION);
                    if (currentUserRegion) {
                        var currentUserId = currentUserRegion.getAttribute('data-userid');
                        if (currentUserId && (currentUserId != requestUserId)) {
                            // Polling conversion status needs to abort because
                            // the current user changed.
                            return;
                        }
                    }
                    var data = this.handle_response_data(response),
                            poll = false;
                    if (data) {
                        this.documentstatus = data.status;
                        if (data.status === 0) {
                            // The combined document is still waiting for input to be ready.
                            poll = true;

                        } else if (data.status === 1 || data.status === 3) {
                            // The combine document is ready for conversion into a single PDF.
                            poll = true;

                        } else if (data.status === 2 || data.status === -1) {
                            // The combined PDF is ready.
                            // We now know the page count and can convert it to a set of images.
                            this.pagecount = data.pagecount;

                            if (data.pageready === data.pagecount) {
                                this.prepare_pages_for_display(data);
                            } else {
                                // Some pages are not ready yet.
                                // Note: We use a different polling process here which does not block.
                                this.update_page_load_progress();

                                // Fetch the images for the combined document.
                                this.start_document_to_image_conversion();
                            }
                        }

                        if (poll) {
                            // Check again in 1 second.
                            Y.later(1000, this, this.poll_document_conversion_status);
                        }
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        });
    },

    /**
     * Spwan the PDF to Image conversion on the server.
     *
     * @method get_images_for_documents
     */
    start_document_to_image_conversion: function () {
        Y.io(AJAXBASE, {
            method: 'get',
            context: this,
            sync: false,
            data: {
                sesskey: M.cfg.sesskey,
                action: 'pollconversions',
                userid: this.get('userid'),
                attemptnumber: this.get('attemptnumber'),
                assignmentid: this.get('assignmentid'),
                readonly: this.get('readonly') ? 1 : 0
            },
            on: {
                success: function (tid, response) {
                    var data = this.handle_response_data(response);
                    if (data) {
                        this.documentstatus = data.status;
                        if (data.status === 2) {
                            // The pages are ready. Add all of the annotations to them.
                            this.prepare_pages_for_display(data);
                        }
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        });
    },

    /**
     * Display an error in a small part of the page (don't block everything).
     *
     * @param string The error text.
     * @param boolean dismissable Not critical messages can be removed after a short display.
     * @protected
     * @method warning
     */
    warning: function (message, dismissable) {
        var warningmessageorigine = this.get_dialogue_element('div.assignfeedback_editpdfplus_warningmessages');
        if (warningmessageorigine) {
            warningmessageorigine.remove();
        }

        var icontemplate = this.get_dialogue_element(SELECTOR.ICONMESSAGECONTAINER);
        var warningregion = this.get_dialogue_element(SELECTOR.WARNINGMESSAGECONTAINER);
        var delay = 15, duration = 1;
        var messageclasses = 'assignfeedback_editpdfplus_warningmessages label label-warning';
        if (dismissable) {
            delay = 4;
            messageclasses = 'assignfeedback_editpdfplus_warningmessages label label-info';
        }
        var warningelement = Y.Node.create('<div class="' + messageclasses + '"></div>');

        // Copy info icon template.
        warningelement.append(icontemplate.one('*').cloneNode());

        // Append the message.
        warningelement.append(message);

        // Add the entire warning to the container.
        warningregion.prepend(warningelement);

        // Remove the message after a short delay.
        warningelement.transition(
                {
                    duration: duration,
                    delay: delay,
                    opacity: 0
                },
                function () {
                    warningelement.remove();
                }
        );
    },

    /**
     * The info about all pages in the pdf has been returned.
     *
     * @param string The ajax response as text.
     * @protected
     * @method prepare_pages_for_display
     */
    prepare_pages_for_display: function (data) {
        var i, j, error, annotation, readonly;
        if (!data.pagecount) {
            if (this.dialogue) {
                this.dialogue.hide();
            }
            // Display alert dialogue.
            error = new M.core.alert({message: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdfplus')});
            error.show();
            return;
        }

        this.pagecount = data.pagecount;
        this.pages = data.pages;

        this.tools = [];
        for (i = 0; i < data.tools.length; i++) {
            var tooltmp = data.tools[i];
            this.tools[tooltmp.id] = tooltmp;
        }

        this.typetools = [];
        for (i = 0; i < data.typetools.length; i++) {
            var typetooltmp = data.typetools[i];
            this.typetools[typetooltmp.id] = typetooltmp;
        }

        this.axis = [];
        for (i = 0; i < data.axis.length; i++) {
            var axistmp = data.axis[i];
            axistmp.visibility = true;
            this.axis[axistmp.id] = axistmp;
        }

        //memorisation des annotations et des annotations parentes (pour annotation frame)
        for (i = 0; i < this.pages.length; i++) {
            var parentannot = [];
            for (j = 0; j < this.pages[i].annotations.length; j++) {
                annotation = this.pages[i].annotations[j];
                if (annotation.parent_annot && parseInt(annotation.parent_annot, 10) !== 0) {
                    annotation.parent_annot_element = parentannot[annotation.parent_annot];
                }
                var dTId = annotation.toolid;
                var newannot = this.create_annotation(
                        this.typetools[this.tools[dTId].type].label,
                        dTId,
                        annotation,
                        this.tools[dTId]
                        );
                if (newannot.parent_annot_element) {
                    var parentAnnotElemId = newannot.parent_annot_element.id;
                    if (this.annotationsparent[parentAnnotElemId]) {
                        this.annotationsparent[parentAnnotElemId][this.annotationsparent[parentAnnotElemId].length] = newannot;
                    } else {
                        this.annotationsparent[parentAnnotElemId] = [newannot];
                    }
                }
                parentannot[annotation.id] = newannot;
                this.pages[i].annotations[j] = newannot;
            }
        }

        readonly = this.get('readonly');
        if (!readonly && data.partial) {
            // Warn about non converted files, but only for teachers.
            this.warning(M.util.get_string('partialwarning', 'assignfeedback_editpdfplus', false));
        }

        // Update the ui.
        this.setup_navigation();
        this.setup_toolbar_advanced();
        this.change_page();
    },

    /**
     * Fetch the page images.
     *
     * @method update_page_load_progress
     */
    update_page_load_progress: function () {
        var checkconversionstatus,
                ajax_error_total = 0,
                progressbar = this.get_dialogue_element(SELECTOR.PROGRESSBARCONTAINER + ' .bar');

        if (!progressbar) {
            return;
        }

        // If pages are not loaded, check PDF conversion status for the progress bar.
        checkconversionstatus = {
            method: 'get',
            context: this,
            sync: false,
            data: {
                sesskey: M.cfg.sesskey,
                action: 'conversionstatus',
                userid: this.get('userid'),
                attemptnumber: this.get('attemptnumber'),
                assignmentid: this.get('assignmentid')
            },
            on: {
                success: function (tid, response) {
                    ajax_error_total = 0;

                    var progress = 0;
                    var progressbar = this.get_dialogue_element(SELECTOR.PROGRESSBARCONTAINER + ' .bar');
                    if (progressbar) {
                        // Calculate progress.
                        progress = (response.response / this.pagecount) * 100;
                        progressbar.setStyle('width', progress + '%');
                        progressbar.ancestor(SELECTOR.PROGRESSBARCONTAINER).setAttribute('aria-valuenow', progress);

                        if (progress < 100) {
                            // Keep polling until all pages are generated.
                            M.util.js_pending('checkconversionstatus');
                            Y.later(1000, this, function () {
                                M.util.js_complete('checkconversionstatus');
                                Y.io(AJAXBASEPROGRESS, checkconversionstatus);
                            });
                        }
                    }
                },
                failure: function (tid, response) {
                    ajax_error_total = ajax_error_total + 1;
                    // We only continue on error if the all pages were not generated,
                    // and if the ajax call did not produce 5 errors in the row.
                    if (this.pagecount === 0 && ajax_error_total < 5) {
                        M.util.js_pending('checkconversionstatus');
                        Y.later(1000, this, function () {
                            M.util.js_complete('checkconversionstatus');
                            Y.io(AJAXBASEPROGRESS, checkconversionstatus);
                        });
                    }
                    return new M.core.exception(response.responseText);
                }
            }
        };
        // We start the AJAX "generated page total number" call a second later to give a chance to
        // the AJAX "combined pdf generation" call to clean the previous submission images.
        M.util.js_pending('checkconversionstatus');
        Y.later(1000, this, function () {
            ajax_error_total = 0;
            M.util.js_complete('checkconversionstatus');
            Y.io(AJAXBASEPROGRESS, checkconversionstatus);
        });
    },

    /**
     * Handle response data.
     *
     * @method  handle_response_data
     * @param   {object} response
     * @return  {object}
     */
    handle_response_data: function (response) {
        var data;
        try {
            data = Y.JSON.parse(response.responseText);
            if (data.error) {
                if (this.dialogue) {
                    this.dialogue.hide();
                }

                new M.core.alert({
                    message: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdfplus'),
                    visible: true
                });
            } else {
                return data;
            }
        } catch (e) {
            if (this.dialogue) {
                this.dialogue.hide();
            }

            new M.core.alert({
                title: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdfplus'),
                visible: true
            });
        }

        return;
    },

    /**
     * Show only annotations from selected axis
     * @public
     * @param {type} edit
     * @param array axis
     * @param html_element axe
     */
    handle_axis_button: function (edit, axis, axe) {
        axis.visibility = axe.get('checked');
        this.redraw();
    },

    /**
     * Attach listeners and enable the color picker buttons.
     * @protected
     * @method setup_toolbar_advanced
     */
    setup_toolbar_advanced: function () {
        var annotationcolourbutton,
                picker;

        if (this.get('readonly')) {
            // Setup the tool buttons.
            for (var axisIndex in this.axis) {
                var axisTmp = this.axis[axisIndex];
                var axe = this.get_dialogue_element('#ctaxis' + axisTmp.id);
                if (axe) {
                    axe.set('checked', 'true');
                    axe.on('click', this.handle_axis_button, this, axisTmp, axe);
                }
            }

            var questionselector = this.get_dialogue_element(SELECTOR.QUESTIONSELECTOR);
            if (questionselector) {
                questionselector.on('change', this.update_visu_annotation_q, this);
            }

            var statutselector = this.get_dialogue_element(SELECTOR.STATUTSELECTOR);
            if (statutselector) {
                statutselector.on('change', this.update_visu_annotation, this);
            }

            var studentvalidation = this.get_dialogue_element(SELECTOR.STUDENTVALIDATION);
            if (studentvalidation) {
                studentvalidation.on('click', this.update_student_feedback, this);
            }

            return;
        }

        // Rotate Left.
        var rotateleftbutton = this.get_dialogue_element(SELECTOR.ROTATELEFTBUTTON);
        rotateleftbutton.on('click', this.rotatePDF, this, true);
        rotateleftbutton.on('key', this.rotatePDF, 'down:13', this, true);
        // Rotate Right.
        var rotaterightbutton = this.get_dialogue_element(SELECTOR.ROTATERIGHTBUTTON);
        rotaterightbutton.on('click', this.rotatePDF, this, false);
        rotaterightbutton.on('key', this.rotatePDF, 'down:13', this, false);

        this.disable_touch_scroll();

        var customtoolbar = this.get_dialogue_element(SELECTOR.CUSTOMTOOLBARID + '1');
        if (customtoolbar) {
            customtoolbar.show();
        }
        var axisselector = this.get_dialogue_element(SELECTOR.AXISCUSTOMTOOLBAR);
        if (axisselector) {
            axisselector.on('change', this.update_custom_toolbars, this);
        }
        this.update_custom_toolbars();
        Y.all(SELECTOR.CUSTOMTOOLBARBUTTONS).each(function (toolnode) {
            var toolid = toolnode.get('id');
            var toollib = toolnode.getAttribute('data-tool');
            toolnode.on('click', this.handle_tool_button, this, toollib, toolid);
            toolnode.on('key', this.handle_tool_button, 'down:13', this, toollib, toolid);
            toolnode.setAttribute('aria-pressed', 'false');
        }, this);

        // Setup the tool buttons.
        Y.all(SELECTOR.GENERICTOOLBARBUTTONS).each(function (toolnode) {
            var toolid = toolnode.get('id');
            var toollib = toolnode.getAttribute('data-tool');
            toolnode.on('click', this.handle_tool_button, this, toollib, toolid);
            toolnode.on('key', this.handle_tool_button, 'down:13', this, toollib, toolid);
            toolnode.setAttribute('aria-pressed', 'false');
        }, this);

        annotationcolourbutton = this.get_dialogue_element(SELECTOR.ANNOTATIONCOLOURBUTTON);
        picker = new M.assignfeedback_editpdfplus.colourpicker({
            buttonNode: annotationcolourbutton,
            iconprefix: 'colour_',
            colours: ANNOTATIONCOLOUR,
            callback: function (e) {
                var colour = e.target.getAttribute('data-colour');
                if (!colour) {
                    colour = e.target.ancestor().getAttribute('data-colour');
                }
                this.currentedit.annotationcolour = colour;
                this.refresh_button_color_state();
            },
            context: this
        });

        //help part
        var helpbutton = this.get_dialogue_element(SELECTOR.HELPBTNCLASS);
        if (helpbutton) {
            helpbutton.on('click', this.display_help_message, this);
        }
    },
    /**
     * Re-create new PDF from all fresh data
     * @protected
     */
    update_student_feedback: function () {
        this.refresh_pdf();
    },

    /**
     * Refresh view with option on question shown or not
     * @protected
     */
    update_visu_annotation_q: function () {
        var questionselector = this.get_dialogue_element(SELECTOR.QUESTIONSELECTOR + ' option:checked');
        var questionid = parseInt(questionselector.get('value'), 10) - 1;
        this.questionstatut = questionid;
        this.redraw();
    },
    /**
     * Refresh view with option on student status
     * @protected
     */
    update_visu_annotation: function () {
        var statusselector = this.get_dialogue_element(SELECTOR.STATUTSELECTOR + ' option:checked');
        var statusid = parseInt(statusselector.get('value'), 10) - 1;
        this.studentstatut = statusid;
        this.redraw();
    },
    /**
     * Refresh toolbar from axis selected
     * @protected
     */
    update_custom_toolbars: function () {
        Y.all(SELECTOR.CUSTOMTOOLBARS).each(function (toolbar) {
            toolbar.hide();
        }, this);
        var axisselector = this.get_dialogue_element(SELECTOR.AXISCUSTOMTOOLBAR + ' option:checked');
        var axisid = parseInt(axisselector.get('value'), 10);
        var customtoolbar = this.get_dialogue_element(SELECTOR.CUSTOMTOOLBARID + '' + axisid);
        customtoolbar.show();
    },

    /**
     * Change the current tool from a button's call.
     * @protected
     * @method handle_tool_button
     */
    handle_tool_button: function (e, tool, toolid, has_parent) {
        e.preventDefault();
        this.handle_tool_button_action(tool, toolid, has_parent);
    },
    /**
     * Change the current tool
     * @param tool tool
     * @param int toolid
     * @param boolean has_parent
     * @protected
     */
    handle_tool_button_action: function (tool, toolid, has_parent) {
        var drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

        var currenttoolnode;
        // Change style of the pressed button.
        if (this.currentedit.id) {
            currenttoolnode = this.get_dialogue_element("#" + this.currentedit.id);
        } else {
            currenttoolnode = this.get_dialogue_element(TOOLSELECTOR[this.currentedit.tool]);
        }
        if (currenttoolnode) {
            currenttoolnode.removeClass('active');
            currenttoolnode.setAttribute('aria-pressed', 'false');
            drawingregion.setStyle('cursor', 'auto');
        }
        //update the currentedit object with the new tool
        this.currentedit.tool = tool;
        this.currentedit.id = toolid;

        if (tool !== "select" && tool !== "drag" && tool !== "resize") {
            this.lastannotationtool = tool;
        }

        if (tool !== "select") {
            this.redraw_annotation();
        }
        if (!has_parent) {
            this.currentedit.parent_annot_element = null;
        }

        this.refresh_button_state();
    },

    /**
     * Refresh the display of each annotation
     * @protected
     */
    redraw_annotation: function () {
        this.currentannotation = null;
        var annotations = this.pages[this.currentpage].annotations;
        Y.each(annotations, function (annotation) {
            if (annotation && annotation.drawable) {
                // Redraw the annotation to remove the highlight.
                annotation.drawable.erase();
                annotation.draw();
            }
        });
    },
    /**
     * JSON encode the current page data - stripping out drawable references which cannot be encoded.
     * @protected
     * @method stringify_current_page
     * @return string
     */
    stringify_current_page: function () {
        var annotations = [],
                page,
                i = 0;

        for (i = 0; i < this.pages[this.currentpage].annotations.length; i++) {
            annotations[i] = this.pages[this.currentpage].annotations[i].clean();
        }

        page = {annotations: annotations};

        return Y.JSON.stringify(page);
    },

    /**
     * JSON encode the current page data - stripping out drawable references
     * which cannot be encoded (light, only for student information).
     * @protected
     * @method stringify_current_page
     * @return string
     */
    stringify_current_page_edited: function () {
        var annotations = [],
                page,
                i = 0;
        for (i = 0; i < this.pages[this.currentpage].annotations.length; i++) {
            annotations[i] = this.pages[this.currentpage].annotations[i].light_clean();
        }
        page = {annotations: annotations};
        return Y.JSON.stringify(page);
    },

    /**
     * Generate a drawable from the current in progress edit.
     * @protected
     * @method get_current_drawable
     */
    get_current_drawable: function () {
        var annotation,
                drawable = false;

        if (!this.currentedit.start || !this.currentedit.end) {
            return false;
        }

        if (this.currentedit.tool !== 'comment') {
            var toolid = this.currentedit.id;
            if (this.currentedit.id && this.currentedit.id[0] === 'c') {
                toolid = this.currentedit.id.substr(8);
            }
            annotation = this.create_annotation(this.currentedit.tool, this.currentedit.id, {}, this.tools[toolid]);
            if (annotation) {
                drawable = annotation.draw_current_edit(this.currentedit);
            }
        }

        return drawable;
    },

    /**
     * Find an element within the dialogue.
     * @protected
     * @method get_dialogue_element
     */
    get_dialogue_element: function (selector) {
        if (this.panel) {
            return this.panel.one(selector);
        } else {
            return this.dialogue.get('boundingBox').one(selector);
        }
    },

    /**
     * Redraw the active edit.
     * @protected
     * @method redraw_active_edit
     */
    redraw_current_edit: function () {
        if (this.currentdrawable) {
            this.currentdrawable.erase();
        }
        this.currentdrawable = this.get_current_drawable();
    },

    /**
     * Event handler for mousedown or touchstart.
     * @protected
     * @param Event
     * @method edit_start
     */
    edit_start: function (e) {
        var canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                offset = canvas.getXY(),
                scrolltop = canvas.get('docScrollY'),
                scrollleft = canvas.get('docScrollX'),
                point = {x: e.clientX - offset[0] + scrollleft,
                    y: e.clientY - offset[1] + scrolltop},
                selected = false;

        // Ignore right mouse click.
        if (e.button === 3) {
            return;
        }

        if (this.currentedit.starttime) {
            return;
        }

        this.currentedit.starttime = new Date().getTime();
        this.currentedit.start = point;
        this.currentedit.end = {x: point.x, y: point.y};

        if (this.currentedit.tool === 'select') {
            var x = this.currentedit.end.x,
                    y = this.currentedit.end.y,
                    annotations = this.pages[this.currentpage].annotations;
            // Find the first annotation whose bounds encompass the click.
            Y.each(annotations, function (annotation) {
                if (((x - annotation.x) * (x - annotation.endx)) <= 0 &&
                        ((y - annotation.y) * (y - annotation.endy)) <= 0) {
                    selected = annotation;
                }
            });

            if (selected) {
                this.lastannotation = this.currentannotation;
                this.currentannotation = selected;
                if (this.lastannotation && this.lastannotation !== selected) {
                    // Redraw the last selected annotation to remove the highlight.
                    if (this.lastannotation.drawable) {
                        this.lastannotation.drawable.erase();
                        this.drawables.push(this.lastannotation.draw());
                        this.drawablesannotations.push(this.lastannotation);
                    }
                }
                // Redraw the newly selected annotation to show the highlight.
                if (this.currentannotation.drawable) {
                    this.currentannotation.drawable.erase();
                }
                this.drawables.push(this.currentannotation.draw());
                this.drawablesannotations.push(this.currentannotation);
            } else {
                this.lastannotation = this.currentannotation;
                this.currentannotation = null;

                // Redraw the last selected annotation to remove the highlight.
                if (this.lastannotation && this.lastannotation.drawable) {
                    this.lastannotation.drawable.erase();
                    this.drawables.push(this.lastannotation.draw());
                    this.drawablesannotations.push(this.lastannotation);
                }
            }
        }

        if (this.currentedit.tool === 'resize') {
            var annotations2 = this.pages[this.currentpage].annotations;
            var selectedAnnot = null;
            // Find the first annotation whose bounds encompass the click.
            Y.each(annotations2, function (annotation) {
                Y.each(annotation.resizeAreas, function (area) {
                    if (e.target == area) {
                        selectedAnnot = annotation;
                    }
                });
            });
            if (selectedAnnot) {
                this.resizeareaselected = e.target.get('id');
                if (e.target.getData('direction') === 'left' || e.target.getData('direction') === 'right') {
                    canvas.setStyle('cursor', 'col-resize');
                } else {
                    canvas.setStyle('cursor', 'row-resize');
                }
                this.lastannotation = this.currentannotation;
                this.currentannotation = selectedAnnot;
            }
        }

        if (this.currentannotation) {
            // Used to calculate drag offset.
            this.currentedit.annotationstart = {x: this.currentannotation.x,
                y: this.currentannotation.y};
        }
    },

    /**
     * Event handler for mousemove.
     * @protected
     * @param Event
     * @method edit_move
     */
    edit_move: function (e) {
        e.preventDefault();
        var bounds = this.get_canvas_bounds(),
                canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION),
                clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                        e.clientY + canvas.get('docScrollY')),
                point = this.get_canvas_coordinates(clientpoint),
                diffX,
                diffY;

        // Ignore events out of the canvas area.
        if (point.x < 0 || point.x > bounds.width || point.y < 0 || point.y > bounds.height) {
            return;
        }

        if (this.currentedit.tool === 'pen') {
            this.currentedit.path.push(point);
        }

        if (this.currentedit.tool === 'select') {
            if (this.currentannotation && this.currentedit) {
                this.currentannotation.move(this.currentedit.annotationstart.x + point.x - this.currentedit.start.x,
                        this.currentedit.annotationstart.y + point.y - this.currentedit.start.y);
            }
        } else if (this.currentedit.tool === 'drag') {
            diffX = point.x - this.currentedit.start.x;
            diffY = point.y - this.currentedit.start.y;

            drawingregion.getDOMNode().scrollLeft -= diffX;
            drawingregion.getDOMNode().scrollTop -= diffY;

        } else if (this.currentedit.tool === 'resize' && this.resizeareaselected) {
            var resizearea = this.get_dialogue_element("#" + this.resizeareaselected);
            this.currentannotation.mousemoveResize(e, point, resizearea);

        } else {
            if (this.currentedit.start) {
                this.currentedit.end = point;
                this.redraw_current_edit();
            }
        }
    },

    /**
     * Event handler for mouseup or touchend.
     * @protected
     * @param Event
     * @method edit_end
     */
    edit_end: function (e) {
        var duration,
                annotation;

        duration = new Date().getTime() - this.currentedit.start;

        if (duration < CLICKTIMEOUT || this.currentedit.start === false) {
            return;
        }

        var toolid = this.currentedit.id;
        if (this.currentedit.id && this.currentedit.id[0] === 'c') {
            toolid = this.currentedit.id.substr(8);
        }
        if (this.currentedit.tool !== 'select' && this.currentedit.tool !== 'drag' && this.currentedit.tool !== 'resize') {
            annotation = this.create_annotation(this.currentedit.tool, this.currentedit.id, {}, this.tools[toolid]);
            if (annotation) {
                if (this.currentdrawable) {
                    this.currentdrawable.erase();
                }
                this.currentdrawable = false;
                if (annotation.init_from_edit(this.currentedit)) {
                    this.currentannotation = annotation;
                    annotation.draw_catridge(this.currentedit);
                    annotation.edit_annot();
                    if (annotation.parent_annot_element) {
                        var index = 0;
                        if (annotation.parent_annot_element.id) {
                            index = annotation.parent_annot_element.id;
                        } else {
                            index = annotation.parent_annot_element.divcartridge;
                        }
                        if (this.annotationsparent[index]) {
                            this.annotationsparent[index][this.annotationsparent[index].length] = annotation;
                        } else {
                            this.annotationsparent[index] = [annotation];
                        }
                    }
                    this.pages[this.currentpage].annotations.push(annotation);
                    this.drawables.push(annotation.draw());
                    this.drawablesannotations.push(annotation);
                }
            }
        } else if (this.currentedit.tool === 'resize' && this.resizeareaselected) {
            var resizearea = this.get_dialogue_element("#" + this.resizeareaselected);
            this.currentannotation.mouseupResize(e, resizearea);
            var canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
            canvas.setStyle('cursor', 'default');
            this.resizeareaselected = null;
        }

        // Save the changes.
        this.save_current_page();

        // Reset the current edit.
        this.currentedit.starttime = 0;
        this.currentedit.start = false;
        this.currentedit.end = false;
        this.currentedit.path = [];
        if (this.currentedit.tool !== 'drag' && this.currentedit.tool !== 'resize') {
            this.handle_tool_button_action("select");
        }
    },

    /**
     * Temporise a function.
     * @public
     * @method temporise
     */
    temporise: function (e, fct, timeout) {
        e.preventDefault();
        setTimeout(fct, timeout);
    },

    /**
     * Resize the dialogue window when the browser is resized.
     * @public
     * @method resize
     */
    resize: function () {
        var drawingregion, drawregionheight, drawregiontop, drawheaderheight, drawfooterheight;
        if (this.dialogue) {
            if (!this.dialogue.get('visible')) {
                return;
            }
            this.dialogue.centerDialogue();
        }

        //calculate top div
        var drawingregionheaderSelector = document.getElementsByClassName(SELECTOR.DRAWINGTOOLBAR);
        if (drawingregionheaderSelector.length > 0) {
            var drawingregionheader = drawingregionheaderSelector[0];
            drawregiontop = drawingregionheader.getBoundingClientRect().height;
            drawheaderheight = drawingregionheader.getBoundingClientRect().bottom;
        } else {
            drawregiontop = 52;
            drawheaderheight = 170;
        }
        //get footer's height
        var footer = document.querySelector("div[data-region='grade-actions-panel']");
        if (footer) {
            drawfooterheight = footer.getBoundingClientRect().height;
        } else {
            drawfooterheight = 60;
        }
        // Make sure the dialogue box is not bigger than the max height of the viewport.
        // be careful to remove space for toolbar + titlebar.
        drawregionheight = Y.one('body').get('winHeight') - (drawfooterheight + drawheaderheight);
        if (drawregionheight < 100) {
            drawregionheight = 100;
        }
        var drawingregionSelector = document.getElementsByClassName(SELECTOR.DRAWINGREGIONCLASS);
        if (drawingregionSelector.length > 0) {
            drawingregion = drawingregionSelector[0];
            drawingregion.style.top = drawregiontop + 'px';
            drawingregion.style.maxHeight = drawregionheight + 'px';
        } else {
            drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
            if (this.dialogue) {
                drawingregion.setStyle('maxHeight', drawregionheight + 'px');
            }
        }
        try {
            this.redraw();
        } catch (exception) {
        }

        return true;
    },

    /**
     * Factory method for creating annotations of the correct subclass.
     * @public
     * @method create_annotation
     * @param string type label du type de tool
     * @param int toolid id du tool en cours
     * @param annotation data annotation complete si elle existe
     * @param tool toolobjet le tool
     * @returns {M.assignfeedback_editpdfplus.annotationrectangle|M.assignfeedback_editpdfplus.annotationhighlight
     * |M.assignfeedback_editpdfplus.annotationoval|Boolean|M.assignfeedback_editpdfplus.annotationstampplus
     * |M.assignfeedback_editpdfplus.annotationframe|M.assignfeedback_editpdfplus.annotationline
     * |M.assignfeedback_editpdfplus.annotationstampcomment|M.assignfeedback_editpdfplus.annotationhighlightplus
     * |M.assignfeedback_editpdfplus.annotationverticalline|M.assignfeedback_editpdfplus.annotationpen}
     */
    create_annotation: function (type, toolid, data, toolobjet) {

        if (toolid !== null && toolid[0] === 'c') {
            data.toolid = toolid.substr(8);
        }
        if (!data.tooltype || data.tooltype === '') {
            data.tooltype = toolobjet;
        }

        data.tool = type;
        data.editor = this;
        if (data.tool === TOOLTYPE.LINE + '' || data.tool === TOOLTYPELIB.LINE) {
            return new M.assignfeedback_editpdfplus.annotationline(data);
        } else if (data.tool === TOOLTYPE.RECTANGLE + '' || data.tool === TOOLTYPELIB.RECTANGLE) {
            return new M.assignfeedback_editpdfplus.annotationrectangle(data);
        } else if (data.tool === TOOLTYPE.OVAL + '' || data.tool === TOOLTYPELIB.OVAL) {
            return new M.assignfeedback_editpdfplus.annotationoval(data);
        } else if (data.tool === TOOLTYPE.PEN + '' || data.tool === TOOLTYPELIB.PEN) {
            return new M.assignfeedback_editpdfplus.annotationpen(data);
        } else if (data.tool === TOOLTYPE.HIGHLIGHT + '' || data.tool === TOOLTYPELIB.HIGHLIGHT) {
            return new M.assignfeedback_editpdfplus.annotationhighlight(data);
        } else {
            if (data.tool === TOOLTYPE.FRAME + '' || data.tool === TOOLTYPELIB.FRAME) {
                if (toolobjet) {
                    if (data.colour === "") {
                        data.colour = this.typetools[toolobjet.type].color;
                    }
                }
                if (!data.parent_annot && !data.parent_annot_element) {
                    if (this.currentedit.parent_annot_element) {
                        data.parent_annot_element = this.currentedit.parent_annot_element;
                    } else {
                        data.parent_annot_element = null;
                        data.parent_annot = 0;
                    }
                }
                return new M.assignfeedback_editpdfplus.annotationframe(data);
            } else {
                if (toolobjet) {
                    if (toolobjet.colors && toolobjet.colors.indexOf(',') !== -1) {
                        data.colour = toolobjet.colors.substr(0, toolobjet.colors.indexOf(','));
                    } else {
                        data.colour = toolobjet.colors;
                    }
                    if (data.colour === "") {
                        data.colour = this.typetools[toolobjet.type].color;
                    }
                }
                if (data.tool === TOOLTYPE.HIGHLIGHTPLUS + '' || data.tool === TOOLTYPELIB.HIGHLIGHTPLUS) {
                    return new M.assignfeedback_editpdfplus.annotationhighlightplus(data);
                } else if (data.tool === TOOLTYPE.STAMPPLUS + '' || data.tool === TOOLTYPELIB.STAMPPLUS) {
                    return new M.assignfeedback_editpdfplus.annotationstampplus(data);
                } else if (data.tool === TOOLTYPE.VERTICALLINE + '' || data.tool === TOOLTYPELIB.VERTICALLINE) {
                    return new M.assignfeedback_editpdfplus.annotationverticalline(data);
                } else if (data.tool === TOOLTYPE.STAMPCOMMENT + '' || data.tool === TOOLTYPELIB.STAMPCOMMENT) {
                    return new M.assignfeedback_editpdfplus.annotationstampcomment(data);
                } else if (data.tool === TOOLTYPE.COMMENTPLUS + '' || data.tool === TOOLTYPELIB.COMMENTPLUS) {
                    return new M.assignfeedback_editpdfplus.annotationcommentplus(data);
                }
            }
        }
        return false;
    },

    /**
     * AJAX call for refresh PDF with last annotations and comments/status
     * @returns {undefined}
     */
    refresh_pdf: function () {
        var ajaxurl = AJAXBASE,
                config;

        config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'generatepdf',
                'userid': this.get('userid'),
                'attemptnumber': this.get('attemptnumber'),
                'assignmentid': this.get('assignmentid'),
                'refresh': true
            },
            on: {
                success: function (tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        }
                        Y.one(SELECTOR.UNSAVEDCHANGESINPUT).set('value', 'true');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('opacity', 1);
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('display', 'inline-block');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).transition({
                            duration: 1,
                            delay: 2,
                            opacity: 0
                        }, function () {
                            Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('display', 'none');
                        });
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);

    },

    /**
     * Save all the annotations and comments for the current page.
     * @protected
     * @method save_current_page
     */
    save_current_page: function () {
        this.clear_warnings(false);
        var ajaxurl = AJAXBASE,
                config;

        config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'savepage',
                'index': this.currentpage,
                'userid': this.get('userid'),
                'attemptnumber': this.get('attemptnumber'),
                'assignmentid': this.get('assignmentid'),
                'page': this.stringify_current_page()
            },
            on: {
                success: function (tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        }
                        // Show warning that we have not saved the feedback.
                        Y.one(SELECTOR.UNSAVEDCHANGESINPUT).set('value', 'true');
                        this.warning(M.util.get_string('draftchangessaved', 'assignfeedback_editpdfplus'), true);
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);

    },

    /**
     * Save all the annotations and comments for the current page fot student view.
     * @protected
     * @method save_current_page_edited
     */
    save_current_page_edited: function () {
        if (this.get('destroyed')) {
            return;
        }
        var ajaxurl = AJAXBASE,
                config;
        config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'updatestudentview',
                'index': this.currentpage,
                'userid': this.get('userid'),
                'attemptnumber': this.get('attemptnumber'),
                'assignmentid': this.get('assignmentid'),
                'page': this.stringify_current_page_edited()
            },
            on: {
                success: function (tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        }
                        Y.one(SELECTOR.UNSAVEDCHANGESINPUT).set('value', 'true');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('opacity', 1);
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('display', 'inline-block');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).transition({
                            duration: 1,
                            delay: 2,
                            opacity: 0
                        }, function () {
                            Y.one(SELECTOR.UNSAVEDCHANGESDIVEDIT).setStyle('display', 'none');
                        });
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        };
        Y.io(ajaxurl, config);
    },

    /**
     * Redraw all the comments and annotations.
     * @protected
     * @method redraw
     */
    redraw: function () {
        var i, annot,
                page;

        page = this.pages[this.currentpage];
        if (page === undefined) {
            return; // Can happen if a redraw is triggered by an event, before the page has been selected.
        }
        while (this.drawables.length > 0) {
            this.drawables.pop().erase();
        }
        while (this.drawablesannotations.length > 0) {
            annot = this.drawablesannotations.pop();
            if (annot.divcartridge) {
                var divannot = Y.one('#' + annot.divcartridge);
                if (divannot) {
                    divannot.remove();
                }
                annot.divcartridge = "";
            }
            if (annot.drawable) {
                annot.drawable.erase();
            }
        }

        //remove active class for resize areas
        var resizezones = Y.all('.assignfeedback_editpdfplus_resize');
        if (resizezones) {
            resizezones.removeClass('assignfeedback_editpdfplus_resize_active');
        }

        //refresh selected tool
        if (!this.get('readonly')) {
            this.refresh_button_state();
        }

        for (i = 0; i < page.annotations.length; i++) {
            annot = page.annotations[i];
            var tool = annot.tooltype;
            if (this.get('readonly')
                    && tool.axis
                    && (this.axis[tool.axis] && this.axis[tool.axis].visibility
                            || tool.axis === "0")
                    && (this.studentstatut < 0 || this.studentstatut === annot.studentstatus)
                    && (this.questionstatut < 0 || this.questionstatut === annot.answerrequested)
                    || !this.get('readonly')) {
                this.drawables.push(annot.draw());
                this.drawablesannotations.push(annot);
            }
        }
    },

    /**
     * Clear all current warning messages from display.
     * @protected
     * @method clear_warnings
     * @param {Boolean} allwarnings If true, all previous warnings are removed.
     */
    clear_warnings: function (allwarnings) {
        // Remove all warning messages, they may not relate to the current document or page anymore.
        var warningregion = this.get_dialogue_element(SELECTOR.WARNINGMESSAGECONTAINER);
        if (allwarnings) {
            warningregion.empty();
        } else {
            warningregion.all('.alert-info').remove(true);
        }
    },

    /**
     * Load the image for this pdf page and remove the loading icon (if there).
     * @protected
     * @method change_page
     */
    change_page: function () {
        var drawingcanvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                page,
                previousbutton,
                nextbutton;

        previousbutton = this.get_dialogue_element(SELECTOR.PREVIOUSBUTTON);
        nextbutton = this.get_dialogue_element(SELECTOR.NEXTBUTTON);

        if (this.currentpage > 0) {
            previousbutton.removeAttribute('disabled');
        } else {
            previousbutton.setAttribute('disabled', 'true');
        }
        if (this.currentpage < (this.pagecount - 1)) {
            nextbutton.removeAttribute('disabled');
        } else {
            nextbutton.setAttribute('disabled', 'true');
        }

        page = this.pages[this.currentpage];
        if (this.loadingicon) {
            this.loadingicon.hide();
        }
        drawingcanvas.setStyle('backgroundImage', 'url("' + page.url + '")');
        drawingcanvas.setStyle('width', page.width + 'px');
        drawingcanvas.setStyle('height', page.height + 'px');
        drawingcanvas.scrollIntoView();

        // Update page select.
        this.get_dialogue_element(SELECTOR.PAGESELECT).set('selectedIndex', this.currentpage);

        this.resize(); // Internally will call 'redraw', after checking the dialogue size.
    },

    /**
     * Now we know how many pages there are,
     * we can enable the navigation controls.
     * @protected
     * @method setup_navigation
     */
    setup_navigation: function () {
        var pageselect,
                i,
                strinfo,
                option,
                previousbutton,
                nextbutton;

        pageselect = this.get_dialogue_element(SELECTOR.PAGESELECT);

        var options = pageselect.all('option');
        if (options.size() <= 1) {
            for (i = 0; i < this.pages.length; i++) {
                option = Y.Node.create('<option/>');
                option.setAttribute('value', i);
                strinfo = {page: i + 1, total: this.pages.length};
                option.setHTML(M.util.get_string('pagexofy', 'assignfeedback_editpdfplus', strinfo));
                pageselect.append(option);
            }
        }
        pageselect.removeAttribute('disabled');
        pageselect.on('change', function () {
            this.currentpage = pageselect.get('value');
            this.clear_warnings(false);
            this.change_page();
        }, this);

        previousbutton = this.get_dialogue_element(SELECTOR.PREVIOUSBUTTON);
        nextbutton = this.get_dialogue_element(SELECTOR.NEXTBUTTON);

        previousbutton.on('click', this.previous_page, this);
        previousbutton.on('key', this.previous_page, 'down:13', this);
        nextbutton.on('click', this.next_page, this);
        nextbutton.on('key', this.next_page, 'down:13', this);
    },

    /**
     * Navigate to the previous page.
     * @protected
     * @method previous_page
     */
    previous_page: function (e) {
        e.preventDefault();
        this.currentpage--;
        if (this.currentpage < 0) {
            this.currentpage = 0;
        }
        this.clear_warnings(false);
        this.change_page();
    },

    /**
     * Navigate to the next page.
     * @protected
     * @method next_page
     */
    next_page: function (e) {
        e.preventDefault();
        this.currentpage++;
        if (this.currentpage >= this.pages.length) {
            this.currentpage = this.pages.length - 1;
        }
        this.clear_warnings(false);
        this.change_page();
    },

    /**
     * Update any absolutely positioned nodes, within each drawable, when the drawing canvas is scrolled
     * @protected
     * @method move_canvas
     */
    move_canvas: function () {
        var drawingregion, x, y, i;

        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        x = parseInt(drawingregion.get('scrollLeft'), 10);
        y = parseInt(drawingregion.get('scrollTop'), 10);

        for (i = 0; i < this.drawables.length; i++) {
            this.drawables[i].scroll_update(x, y);
        }
    },

    /**
     * Calculate degree to rotate.
     * @protected
     * @param {Object} e javascript event
     * @param {boolean} left  true if rotating left, false if rotating right
     * @method rotatepdf
     */
    rotatePDF: function (e, left) {
        e.preventDefault();

        if (this.get('destroyed')) {
            return;
        }
        var self = this;
        // Save old coordinates.
        var i;
        this.oldannotationcoordinates = [];
        var annotations = this.pages[this.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            var oldannotation = annotations[i];
            this.oldannotationcoordinates.push([oldannotation.x, oldannotation.y]);
        }

        var ajaxurl = AJAXBASE;
        var config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'rotatepage',
                'index': this.currentpage,
                'userid': this.get('userid'),
                'attemptnumber': this.get('attemptnumber'),
                'assignmentid': this.get('assignmentid'),
                'rotateleft': left
            },
            on: {
                success: function (tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        var page = self.pages[self.currentpage];
                        page.url = jsondata.page.url;
                        page.width = jsondata.page.width;
                        page.height = jsondata.page.height;
                        self.loadingicon.hide();

                        // Change canvas size to fix the new page.
                        var drawingcanvas = self.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
                        drawingcanvas.setStyle('backgroundImage', 'url("' + page.url + '")');
                        drawingcanvas.setStyle('width', page.width + 'px');
                        drawingcanvas.setStyle('height', page.height + 'px');

                        /**
                         * Move annotation to old position.
                         * Reason: When canvas size change
                         * > Shape annotations move with relation to canvas coordinates
                         * > Nodes of stamp annotations move with relation to canvas coordinates
                         * > Presentation (picture) of stamp annotations  stay to document coordinates (stick to its own position)
                         * > Without relocating the node and presentation of a stamp annotation to the same x,y position,
                         * the stamp annotation cannot be chosen when using "drag" tool.
                         * The following code brings all annotations to their old positions with relation to the canvas coordinates.
                         */
                        var i;
                        // Annotations.
                        var annotations = page.annotations;
                        for (i = 0; i < annotations.length; i++) {
                            if (self.oldannotationcoordinates && self.oldannotationcoordinates[i]) {
                                var oldX = self.oldannotationcoordinates[i][0];
                                var oldY = self.oldannotationcoordinates[i][1];
                                var annotation = annotations[i];
                                annotation.move(oldX, oldY);
                            }
                        }
                        // Save Annotations.
                        return self.save_current_page();
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function (tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        };
        Y.io(ajaxurl, config);
    },

    /**
     * Test the browser support for options objects on event listeners.
     * @return Boolean
     */
    event_listener_options_supported: function () {
        var passivesupported = false,
                options,
                testeventname = "testpassiveeventoptions";

        // Options support testing example from:
        // https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener

        try {
            options = Object.defineProperty({}, "passive", {
                get: function () {
                    passivesupported = true;
                }
            });

            // We use an event name that is not likely to conflict with any real event.
            document.addEventListener(testeventname, options, options);
            // We remove the event listener as we have tested the options already.
            document.removeEventListener(testeventname, options, options);
        } catch (err) {
            // It's already false.
            passivesupported = false;
        }
        return passivesupported;
    },

    /**
     * Disable Touch Move scrolling
     */
    disable_touch_scroll: function () {
        if (this.event_listener_options_supported()) {
            document.addEventListener('touchmove', this.stop_touch_scroll.bind(this), {passive: false});
        }
    },

    /**
     * Stop Touch Scrolling
     * @param {Object} e
     */
    stop_touch_scroll: function (e) {
        var drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);

        if (drawingregion.contains(e.target)) {
            e.stopPropagation();
            e.preventDefault();
        }
    },

    /**
     * Display a help popup in order to explain tools usability
     * @protected
     * @method display_help_message
     */
    display_help_message: function (event) {
        event.preventDefault();
        var helptitle = this.get_dialogue_element(SELECTOR.HELPMESSAGETITLE);
        var helpbody = this.get_dialogue_element(SELECTOR.HELPMESSAGE);
        var helpopup = new M.core.dialogue({
            headerContent: helptitle.get('innerHTML'),
            bodyContent: helpbody.get('innerHTML'),
            modal: true,
            width: '840px',
            visible: false,
            draggable: true});
        helpopup.centerDialogue();
        helpopup.show();
    }

};

Y.extend(EDITOR, Y.Base, EDITOR.prototype, {
    NAME: 'moodle-assignfeedback_editpdfplus-editor',
    ATTRS: {
        userid: {
            validator: Y.Lang.isInteger,
            value: 0
        },
        assignmentid: {
            validator: Y.Lang.isInteger,
            value: 0
        },
        attemptnumber: {
            validator: Y.Lang.isInteger,
            value: 0
        },
        header: {
            validator: Y.Lang.isString,
            value: ''
        },
        body: {
            validator: Y.Lang.isString,
            value: ''
        },
        footer: {
            validator: Y.Lang.isString,
            value: ''
        },
        linkid: {
            validator: Y.Lang.isString,
            value: ''
        },
        deletelinkid: {
            validator: Y.Lang.isString,
            value: ''
        },
        readonly: {
            validator: Y.Lang.isBoolean,
            value: true
        }
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.editor = M.assignfeedback_editpdfplus.editor || {};

/**
 * Init function - will create a new instance every time.
 * @method editor.init
 * @static
 * @param {Object} params
 */
M.assignfeedback_editpdfplus.editor.init = M.assignfeedback_editpdfplus.editor.init || function (params) {
    M.assignfeedback_editpdfplus.instance = new EDITOR(params);
    return M.assignfeedback_editpdfplus.instance;
};
