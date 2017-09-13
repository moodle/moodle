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
/* global SELECTOR, TOOLSELECTOR, AJAXBASE, COMMENTCOLOUR, ANNOTATIONCOLOUR, AJAXBASEPROGRESS, CLICKTIMEOUT */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * EDITOR
 * This is an in browser PDF editor.
 *
 * @namespace M.assignfeedback_editpdf
 * @class editor
 * @constructor
 * @extends Y.Base
 */
var EDITOR = function() {
    EDITOR.superclass.constructor.apply(this, arguments);
};
EDITOR.prototype = {

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
     * @type M.assignfeedback_editpdf.edit
     * @protected
     */
    currentedit: new M.assignfeedback_editpdf.edit(),

    /**
     * Current drawable.
     *
     * @property currentdrawable
     * @type M.assignfeedback_editpdf.drawable|false
     * @protected
     */
    currentdrawable: false,

    /**
     * Current drawables.
     *
     * @property drawables
     * @type array(M.assignfeedback_editpdf.drawable)
     * @protected
     */
    drawables: [],

    /**
     * Current comment when the comment menu is open.
     * @property currentcomment
     * @type M.assignfeedback_editpdf.comment
     * @protected
     */
    currentcomment: null,

    /**
     * Current annotation when the select tool is used.
     * @property currentannotation
     * @type M.assignfeedback_editpdf.annotation
     * @protected
     */
    currentannotation: null,

    /**
     * Track the previous annotation so we can remove selection highlights.
     * @property lastannotation
     * @type M.assignfeedback_editpdf.annotation
     * @protected
     */
    lastannotation: null,

    /**
     * Last selected annotation tool
     * @property lastannotationtool
     * @type String
     * @protected
     */
    lastannotationtool: "pen",

    /**
     * The users comments quick list
     * @property quicklist
     * @type M.assignfeedback_editpdf.quickcommentlist
     * @protected
     */
    quicklist: null,

    /**
     * The search comments window.
     * @property searchcommentswindow
     * @type M.core.dialogue
     * @protected
     */
    searchcommentswindow: null,


    /**
     * The selected stamp picture.
     * @property currentstamp
     * @type String
     * @protected
     */
    currentstamp: null,

    /**
     * The stamps.
     * @property stamps
     * @type Array
     * @protected
     */
    stamps: [],

    /**
     * Prevent new comments from appearing
     * immediately after clicking off a current
     * comment
     * @property editingcomment
     * @type Boolean
     * @public
     */
    editingcomment: false,

    /**
     * Should inactive comments be collapsed?
     *
     * @property collapsecomments
     * @type Boolean
     * @public
     */
    collapsecomments: true,

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer: function() {
        var link;

        link = Y.one('#' + this.get('linkid'));

        if (link) {
            link.on('click', this.link_handler, this);
            link.on('key', this.link_handler, 'down:13', this);

            // We call the amd module to see if we can take control of the review panel.
            require(['mod_assign/grading_review_panel'], function(ReviewPanelManager) {
                var panelManager = new ReviewPanelManager();

                var panel = panelManager.getReviewPanel('assignfeedback_editpdf');
                if (panel) {
                    panel = Y.one(panel);
                    panel.empty();
                    link.ancestor('.fitem').hide();
                    this.open_in_panel(panel);
                }
                this.currentedit.start = false;
                this.currentedit.end = false;
                if (!this.get('readonly')) {
                    this.quicklist = new M.assignfeedback_editpdf.quickcommentlist(this);
                }
            }.bind(this));

        }
    },

    /**
     * Called to show/hide buttons and set the current colours/stamps.
     * @method refresh_button_state
     */
    refresh_button_state: function() {
        var button, currenttoolnode, imgurl, drawingregion;

        // Initalise the colour buttons.
        button = this.get_dialogue_element(SELECTOR.COMMENTCOLOURBUTTON);

        imgurl = M.util.image_url('background_colour_' + this.currentedit.commentcolour, 'assignfeedback_editpdf');
        button.one('img').setAttribute('src', imgurl);

        if (this.currentedit.commentcolour === 'clear') {
            button.one('img').setStyle('borderStyle', 'dashed');
        } else {
            button.one('img').setStyle('borderStyle', 'solid');
        }

        button = this.get_dialogue_element(SELECTOR.ANNOTATIONCOLOURBUTTON);
        imgurl = M.util.image_url('colour_' + this.currentedit.annotationcolour, 'assignfeedback_editpdf');
        button.one('img').setAttribute('src', imgurl);

        currenttoolnode = this.get_dialogue_element(TOOLSELECTOR[this.currentedit.tool]);
        currenttoolnode.addClass('assignfeedback_editpdf_selectedbutton');
        currenttoolnode.setAttribute('aria-pressed', 'true');
        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        drawingregion.setAttribute('data-currenttool', this.currentedit.tool);

        button = this.get_dialogue_element(SELECTOR.STAMPSBUTTON);
        button.one('img').setAttrs({'src': this.get_stamp_image_url(this.currentedit.stamp),
                                    'height': '16',
                                    'width': '16'});
    },

    /**
     * Called to get the bounds of the drawing region.
     * @method get_canvas_bounds
     */
    get_canvas_bounds: function() {
        var canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
            offsetcanvas = canvas.getXY(),
            offsetleft = offsetcanvas[0],
            offsettop = offsetcanvas[1],
            width = parseInt(canvas.getStyle('width'), 10),
            height = parseInt(canvas.getStyle('height'), 10);

        return new M.assignfeedback_editpdf.rect(offsetleft, offsettop, width, height);
    },

    /**
     * Called to translate from window coordinates to canvas coordinates.
     * @method get_canvas_coordinates
     * @param M.assignfeedback_editpdf.point point in window coordinats.
     */
    get_canvas_coordinates: function(point) {
        var bounds = this.get_canvas_bounds(),
            newpoint = new M.assignfeedback_editpdf.point(point.x - bounds.x, point.y - bounds.y);

        bounds.x = bounds.y = 0;

        newpoint.clip(bounds);
        return newpoint;
    },

    /**
     * Called to translate from canvas coordinates to window coordinates.
     * @method get_window_coordinates
     * @param M.assignfeedback_editpdf.point point in window coordinats.
     */
    get_window_coordinates: function(point) {
        var bounds = this.get_canvas_bounds(),
            newpoint = new M.assignfeedback_editpdf.point(point.x + bounds.x, point.y + bounds.y);

        return newpoint;
    },

    /**
     * Open the edit-pdf editor in the panel in the page instead of a popup.
     * @method open_in_panel
     */
    open_in_panel: function(panel) {
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
        }

        this.start_generation();
    },

    /**
     * Called to open the pdf editing dialogue.
     * @method link_handler
     */
    link_handler: function(e) {
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
    start_generation: function() {
        this.poll_document_conversion_status();
    },

    /**
     * Poll the current document conversion status and start the next step
     * in the process.
     *
     * @method poll_document_conversion_status
     */
    poll_document_conversion_status: function() {
        if (this.get('destroyed')) {
            return;
        }

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
                success: function(tid, response) {
                    var data = this.handle_response_data(response),
                        poll = false;
                    if (data) {
                        this.documentstatus = data.status;
                        if (data.status === 0) {
                            // The combined document is still waiting for input to be ready.
                            poll = true;

                        } else if (data.status === 1) {
                            // The combine document is ready for conversion into a single PDF.
                            poll = true;

                        } else if (data.status === 2 || data.status === -1) {
                            // The combined PDF is ready.
                            // We now know the page count and can convert it to a set of images.
                            this.pagecount = data.pagecount;

                            if (data.pageready == data.pagecount) {
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
                failure: function(tid, response) {
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
    start_document_to_image_conversion: function() {
        if (this.get('destroyed')) {
            return;
        }
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
                success: function(tid, response) {
                    var data = this.handle_response_data(response);
                    if (data) {
                        this.documentstatus = data.status;
                        if (data.status === 2) {
                            // The pages are ready. Add all of the annotations to them.
                            this.prepare_pages_for_display(data);
                        }
                    }
                },
                failure: function(tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        });
    },

    /**
     * The info about all pages in the pdf has been returned.
     *
     * @param string The ajax response as text.
     * @protected
     * @method prepare_pages_for_display
     */
    prepare_pages_for_display: function(data) {
        var i, j, comment, error;
        if (!data.pagecount) {
            if (this.dialogue) {
                this.dialogue.hide();
            }
            // Display alert dialogue.
            error = new M.core.alert({message: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdf')});
            error.show();
            return;
        }

        this.pages = data.pages;

        for (i = 0; i < this.pages.length; i++) {
            for (j = 0; j < this.pages[i].comments.length; j++) {
                comment = this.pages[i].comments[j];
                this.pages[i].comments[j] = new M.assignfeedback_editpdf.comment(this,
                                                                                 comment.gradeid,
                                                                                 comment.pageno,
                                                                                 comment.x,
                                                                                 comment.y,
                                                                                 comment.width,
                                                                                 comment.colour,
                                                                                 comment.rawtext);
            }
            for (j = 0; j < this.pages[i].annotations.length; j++) {
                data = this.pages[i].annotations[j];
                this.pages[i].annotations[j] = this.create_annotation(data.type, data);
            }
        }

        // Update the ui.
        if (this.quicklist) {
            this.quicklist.load();
        }
        this.setup_navigation();
        this.setup_toolbar();
        this.change_page();
    },

    /**
     * Fetch the page images.
     *
     * @method update_page_load_progress
     */
    update_page_load_progress: function() {
        if (this.get('destroyed')) {
            return;
        }
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
                success: function(tid, response) {
                    if (this.get('destroyed')) {
                        return;
                    }
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
                            Y.later(1000, this, function() {
                                M.util.js_complete('checkconversionstatus');
                                Y.io(AJAXBASEPROGRESS, checkconversionstatus);
                            });
                        }
                    }
                },
                failure: function(tid, response) {
                    if (this.get('destroyed')) {
                        return;
                    }
                    ajax_error_total = ajax_error_total + 1;
                    // We only continue on error if the all pages were not generated,
                    // and if the ajax call did not produce 5 errors in the row.
                    if (this.pagecount === 0 && ajax_error_total < 5) {
                        M.util.js_pending('checkconversionstatus');
                        Y.later(1000, this, function() {
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
        Y.later(1000, this, function() {
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
    handle_response_data: function(response) {
        if (this.get('destroyed')) {
            return;
        }
        var data;
        try {
            data = Y.JSON.parse(response.responseText);
            if (data.error) {
                if (this.dialogue) {
                    this.dialogue.hide();
                }

                new M.core.alert({
                    message: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdf'),
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
                title: M.util.get_string('cannotopenpdf', 'assignfeedback_editpdf'),
                visible: true
            });
        }

        return;
    },

    /**
     * Get the full pluginfile url for an image file - just given the filename.
     *
     * @public
     * @method get_stamp_image_url
     * @param string filename
     */
    get_stamp_image_url: function(filename) {
        var urls = this.get('stampfiles'),
            fullurl = '';

        Y.Array.each(urls, function(url) {
            if (url.indexOf(filename) > 0) {
                fullurl = url;
            }
        }, this);

        return fullurl;
    },

    /**
     * Attach listeners and enable the color picker buttons.
     * @protected
     * @method setup_toolbar
     */
    setup_toolbar: function() {
        var toolnode,
            commentcolourbutton,
            annotationcolourbutton,
            searchcommentsbutton,
            expcolcommentsbutton,
            currentstampbutton,
            stampfiles,
            picker,
            filename;

        searchcommentsbutton = this.get_dialogue_element(SELECTOR.SEARCHCOMMENTSBUTTON);
        searchcommentsbutton.on('click', this.open_search_comments, this);
        searchcommentsbutton.on('key', this.open_search_comments, 'down:13', this);

        expcolcommentsbutton = this.get_dialogue_element(SELECTOR.EXPCOLCOMMENTSBUTTON);
        expcolcommentsbutton.on('click', this.expandCollapseComments, this);
        expcolcommentsbutton.on('key', this.expandCollapseComments, 'down:13', this);

        if (this.get('readonly')) {
            return;
        }
        // Setup the tool buttons.
        Y.each(TOOLSELECTOR, function(selector, tool) {
            toolnode = this.get_dialogue_element(selector);
            toolnode.on('click', this.handle_tool_button, this, tool);
            toolnode.on('key', this.handle_tool_button, 'down:13', this, tool);
            toolnode.setAttribute('aria-pressed', 'false');
        }, this);

        // Set the default tool.

        commentcolourbutton = this.get_dialogue_element(SELECTOR.COMMENTCOLOURBUTTON);
        picker = new M.assignfeedback_editpdf.colourpicker({
            buttonNode: commentcolourbutton,
            colours: COMMENTCOLOUR,
            iconprefix: 'background_colour_',
            callback: function(e) {
                var colour = e.target.getAttribute('data-colour');
                if (!colour) {
                    colour = e.target.ancestor().getAttribute('data-colour');
                }
                this.currentedit.commentcolour = colour;
                this.handle_tool_button(e, "comment");
            },
            context: this
        });

        annotationcolourbutton = this.get_dialogue_element(SELECTOR.ANNOTATIONCOLOURBUTTON);
        picker = new M.assignfeedback_editpdf.colourpicker({
            buttonNode: annotationcolourbutton,
            iconprefix: 'colour_',
            colours: ANNOTATIONCOLOUR,
            callback: function(e) {
                var colour = e.target.getAttribute('data-colour');
                if (!colour) {
                    colour = e.target.ancestor().getAttribute('data-colour');
                }
                this.currentedit.annotationcolour = colour;
                if (this.lastannotationtool) {
                    this.handle_tool_button(e, this.lastannotationtool);
                } else {
                    this.handle_tool_button(e, "pen");
                }
            },
            context: this
        });

        stampfiles = this.get('stampfiles');
        if (stampfiles.length <= 0) {
            this.get_dialogue_element(TOOLSELECTOR.stamp).ancestor().hide();
        } else {
            filename = stampfiles[0].substr(stampfiles[0].lastIndexOf('/') + 1);
            this.currentedit.stamp = filename;
            currentstampbutton = this.get_dialogue_element(SELECTOR.STAMPSBUTTON);

            picker = new M.assignfeedback_editpdf.stamppicker({
                buttonNode: currentstampbutton,
                stamps: stampfiles,
                callback: function(e) {
                    var stamp = e.target.getAttribute('data-stamp'),
                        filename;

                    if (!stamp) {
                        stamp = e.target.ancestor().getAttribute('data-stamp');
                    }
                    filename = stamp.substr(stamp.lastIndexOf('/'));
                    this.currentedit.stamp = filename;
                    this.handle_tool_button(e, "stamp");
                },
                context: this
            });
            this.refresh_button_state();
        }
    },

    /**
     * Change the current tool.
     * @protected
     * @method handle_tool_button
     */
    handle_tool_button: function(e, tool) {
        var currenttoolnode;

        e.preventDefault();

        // Change style of the pressed button.
        currenttoolnode = this.get_dialogue_element(TOOLSELECTOR[this.currentedit.tool]);
        currenttoolnode.removeClass('assignfeedback_editpdf_selectedbutton');
        currenttoolnode.setAttribute('aria-pressed', 'false');
        this.currentedit.tool = tool;

        if (tool !== "comment" && tool !== "select" && tool !== "drag" && tool !== "stamp") {
            this.lastannotationtool = tool;
        }
        this.refresh_button_state();
    },

    /**
     * JSON encode the current page data - stripping out drawable references which cannot be encoded.
     * @protected
     * @method stringify_current_page
     * @return string
     */
    stringify_current_page: function() {
        var comments = [],
            annotations = [],
            page,
            i = 0;

        for (i = 0; i < this.pages[this.currentpage].comments.length; i++) {
            comments[i] = this.pages[this.currentpage].comments[i].clean();
        }
        for (i = 0; i < this.pages[this.currentpage].annotations.length; i++) {
            annotations[i] = this.pages[this.currentpage].annotations[i].clean();
        }

        page = {comments: comments, annotations: annotations};

        return Y.JSON.stringify(page);
    },

    /**
     * Generate a drawable from the current in progress edit.
     * @protected
     * @method get_current_drawable
     */
    get_current_drawable: function() {
        var comment,
            annotation,
            drawable = false;

        if (!this.currentedit.start || !this.currentedit.end) {
            return false;
        }

        if (this.currentedit.tool === 'comment') {
            comment = new M.assignfeedback_editpdf.comment(this);
            drawable = comment.draw_current_edit(this.currentedit);
        } else {
            annotation = this.create_annotation(this.currentedit.tool, {});
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
    get_dialogue_element: function(selector) {
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
    redraw_current_edit: function() {
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
    edit_start: function(e) {
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

        if (this.editingcomment) {
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
            Y.each(annotations, function(annotation) {
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
                    }
                }
                // Redraw the newly selected annotation to show the highlight.
                if (this.currentannotation.drawable) {
                    this.currentannotation.drawable.erase();
                }
                this.drawables.push(this.currentannotation.draw());
            } else {
                this.lastannotation = this.currentannotation;
                this.currentannotation = null;

                // Redraw the last selected annotation to remove the highlight.
                if (this.lastannotation && this.lastannotation.drawable) {
                    this.lastannotation.drawable.erase();
                    this.drawables.push(this.lastannotation.draw());
                }
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
    edit_move: function(e) {
        e.preventDefault();
        var bounds = this.get_canvas_bounds(),
            canvas = this.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
            drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION),
            clientpoint = new M.assignfeedback_editpdf.point(e.clientX + canvas.get('docScrollX'),
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
    edit_end: function() {
        var duration,
            comment,
            annotation;

        duration = new Date().getTime() - this.currentedit.start;

        if (duration < CLICKTIMEOUT || this.currentedit.start === false) {
            return;
        }

        if (this.currentedit.tool === 'comment') {
            if (this.currentdrawable) {
                this.currentdrawable.erase();
            }
            this.currentdrawable = false;
            comment = new M.assignfeedback_editpdf.comment(this);
            if (comment.init_from_edit(this.currentedit)) {
                this.pages[this.currentpage].comments.push(comment);
                this.drawables.push(comment.draw(true));
                this.editingcomment = true;
            }
        } else {
            annotation = this.create_annotation(this.currentedit.tool, {});
            if (annotation) {
                if (this.currentdrawable) {
                    this.currentdrawable.erase();
                }
                this.currentdrawable = false;
                if (annotation.init_from_edit(this.currentedit)) {
                    this.pages[this.currentpage].annotations.push(annotation);
                    this.drawables.push(annotation.draw());
                }
            }
        }

        // Save the changes.
        this.save_current_page();

        // Reset the current edit.
        this.currentedit.starttime = 0;
        this.currentedit.start = false;
        this.currentedit.end = false;
        this.currentedit.path = [];
    },

    /**
     * Resize the dialogue window when the browser is resized.
     * @public
     * @method resize
     */
    resize: function() {
        var drawingregion, drawregionheight;

        if (this.dialogue) {
            if (!this.dialogue.get('visible')) {
                return;
            }
            this.dialogue.centerDialogue();
        }

        // Make sure the dialogue box is not bigger than the max height of the viewport.
        drawregionheight = Y.one('body').get('winHeight') - 120; // Space for toolbar + titlebar.
        if (drawregionheight < 100) {
            drawregionheight = 100;
        }
        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        if (this.dialogue) {
            drawingregion.setStyle('maxHeight', drawregionheight + 'px');
        }
        this.redraw();
        return true;
    },

    /**
     * Factory method for creating annotations of the correct subclass.
     * @public
     * @method create_annotation
     */
    create_annotation: function(type, data) {
        data.type = type;
        data.editor = this;
        if (type === "line") {
            return new M.assignfeedback_editpdf.annotationline(data);
        } else if (type === "rectangle") {
            return new M.assignfeedback_editpdf.annotationrectangle(data);
        } else if (type === "oval") {
            return new M.assignfeedback_editpdf.annotationoval(data);
        } else if (type === "pen") {
            return new M.assignfeedback_editpdf.annotationpen(data);
        } else if (type === "highlight") {
            return new M.assignfeedback_editpdf.annotationhighlight(data);
        } else if (type === "stamp") {
            return new M.assignfeedback_editpdf.annotationstamp(data);
        }
        return false;
    },

    /**
     * Save all the annotations and comments for the current page.
     * @protected
     * @method save_current_page
     */
    save_current_page: function() {
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
                'action': 'savepage',
                'index': this.currentpage,
                'userid': this.get('userid'),
                'attemptnumber': this.get('attemptnumber'),
                'assignmentid': this.get('assignmentid'),
                'page': this.stringify_current_page()
            },
            on: {
                success: function(tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        }
                        Y.one(SELECTOR.UNSAVEDCHANGESINPUT).set('value', 'true');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIV).setStyle('opacity', 1);
                        Y.one(SELECTOR.UNSAVEDCHANGESDIV).setStyle('display', 'inline-block');
                        Y.one(SELECTOR.UNSAVEDCHANGESDIV).transition({
                            duration: 1,
                            delay: 2,
                            opacity: 0
                        }, function() {
                            Y.one(SELECTOR.UNSAVEDCHANGESDIV).setStyle('display', 'none');
                        });
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function(tid, response) {
                    return new M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);
    },

    /**
     * Event handler to open the comment search interface.
     *
     * @param Event e
     * @protected
     * @method open_search_comments
     */
    open_search_comments: function(e) {
        if (!this.searchcommentswindow) {
            this.searchcommentswindow = new M.assignfeedback_editpdf.commentsearch({
                editor: this
            });
        }

        this.searchcommentswindow.show();
        e.preventDefault();
    },

    /**
     * Toggle function to expand/collapse all comments on page.
     *
     * @protected
     * @method expandCollapseComments
     */
    expandCollapseComments: function() {
        if (this.collapsecomments) {
            this.collapsecomments = false;
        } else {
            this.collapsecomments = true;
        }

        this.redraw();
    },

    /**
     * Redraw all the comments and annotations.
     * @protected
     * @method redraw
     */
    redraw: function() {
        var i,
            page;

        page = this.pages[this.currentpage];
        if (page === undefined) {
            return; // Can happen if a redraw is triggered by an event, before the page has been selected.
        }
        while (this.drawables.length > 0) {
            this.drawables.pop().erase();
        }

        for (i = 0; i < page.annotations.length; i++) {
            this.drawables.push(page.annotations[i].draw());
        }
        for (i = 0; i < page.comments.length; i++) {
            this.drawables.push(page.comments[i].draw(false));
        }
    },

    /**
     * Load the image for this pdf page and remove the loading icon (if there).
     * @protected
     * @method change_page
     */
    change_page: function() {
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
        this.loadingicon.hide();
        drawingcanvas.setStyle('backgroundImage', 'url("' + page.url + '")');
        drawingcanvas.setStyle('width', page.width + 'px');
        drawingcanvas.setStyle('height', page.height + 'px');

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
    setup_navigation: function() {
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
                option.setHTML(M.util.get_string('pagexofy', 'assignfeedback_editpdf', strinfo));
                pageselect.append(option);
            }
        }
        pageselect.removeAttribute('disabled');
        pageselect.on('change', function() {
            this.currentpage = pageselect.get('value');
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
    previous_page: function(e) {
        e.preventDefault();
        this.currentpage--;
        if (this.currentpage < 0) {
            this.currentpage = 0;
        }
        this.change_page();
    },

    /**
     * Navigate to the next page.
     * @protected
     * @method next_page
     */
    next_page: function(e) {
        e.preventDefault();
        this.currentpage++;
        if (this.currentpage >= this.pages.length) {
            this.currentpage = this.pages.length - 1;
        }
        this.change_page();
    },

    /**
     * Update any absolutely positioned nodes, within each drawable, when the drawing canvas is scrolled
     * @protected
     * @method move_canvas
     */
    move_canvas: function() {
        var drawingregion, x, y, i;

        drawingregion = this.get_dialogue_element(SELECTOR.DRAWINGREGION);
        x = parseInt(drawingregion.get('scrollLeft'), 10);
        y = parseInt(drawingregion.get('scrollTop'), 10);

        for (i = 0; i < this.drawables.length; i++) {
            this.drawables[i].scroll_update(x, y);
        }
    }

};

Y.extend(EDITOR, Y.Base, EDITOR.prototype, {
    NAME: 'moodle-assignfeedback_editpdf-editor',
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
        },
        stampfiles: {
            validator: Y.Lang.isArray,
            value: ''
        }
    }
});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.editor = M.assignfeedback_editpdf.editor || {};

/**
 * Init function - will create a new instance every time.
 * @method editor.init
 * @static
 * @param {Object} params
 */
M.assignfeedback_editpdf.editor.init = M.assignfeedback_editpdf.editor.init || function(params) {
    if (typeof M.assignfeedback_editpdf.instance !== 'undefined') {
        M.assignfeedback_editpdf.instance.destroy();
    }

    M.assignfeedback_editpdf.instance = new EDITOR(params);
    return M.assignfeedback_editpdf.instance;
};
