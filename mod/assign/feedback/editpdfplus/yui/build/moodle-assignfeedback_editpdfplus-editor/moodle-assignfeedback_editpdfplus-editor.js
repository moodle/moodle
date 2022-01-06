YUI.add('moodle-assignfeedback_editpdfplus-editor', function (Y, NAME) {

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
 * A list of globals used by this module.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var AJAXBASE = M.cfg.wwwroot + '/mod/assign/feedback/editpdfplus/ajax.php',
        AJAXBASEPROGRESS = M.cfg.wwwroot + '/mod/assign/feedback/editpdfplus/ajax_progress.php',
        CSS = {
            DIALOGUE: 'assignfeedback_editpdfplus_widget'
        },
        SELECTOR = {
            PREVIOUSBUTTON: '.navigate-previous-button',
            NEXTBUTTON: ' .navigate-next-button',
            PAGESELECT: '.navigate-page-select',
            LOADINGICON: '.loading',
            PROGRESSBARCONTAINER: '.progress-info.progress-striped',
            DRAWINGREGION: '.drawingregion',
            DRAWINGREGIONCLASS: 'drawingregion',
            DRAWINGCANVAS: '.drawingcanvas',
            DRAWINGTOOLBAR: 'drawingtoolbar',
            SAVE: '.savebutton',
            ANNOTATIONCOLOURBUTTON: '.annotationcolourbutton',
            DELETEANNOTATIONBUTTON: '.deleteannotationbutton',
            WARNINGMESSAGECONTAINER: '.warningmessages',
            ICONMESSAGECONTAINER: '.assignfeedback_editpdfplus_infoicon',
            UNSAVEDCHANGESDIV: '.assignfeedback_editpdf_warningmessages',
            UNSAVEDCHANGESINPUT: 'input[name="assignfeedback_editpdfplus_haschanges"]',
            UNSAVEDCHANGESDIVEDIT: '.assignfeedback_editpdfplus_unsavedchanges_edit',
            HELPMESSAGETITLE: '#afppHelpmessageTitle',
            HELPMESSAGE: '#afppHelpmessageBody',
            USERINFOREGION: '[data-region="user-info"]',
            ROTATELEFTBUTTON: '.rotateleftbutton',
            ROTATERIGHTBUTTON: '.rotaterightbutton',
            DIALOGUE: '.' + CSS.DIALOGUE,
            CUSTOMTOOLBARID: '#toolbaraxis',
            CUSTOMTOOLBARS: '.customtoolbar',
            AXISCUSTOMTOOLBAR: '.menuaxisselection',
            CUSTOMTOOLBARBUTTONS: '.costumtoolbarbutton',
            GENERICTOOLBARBUTTONS: '.generictoolbarbutton',
            HELPBTNCLASS: '.helpmessage',
            STATUTSELECTOR: '#menustatutselection',
            QUESTIONSELECTOR: '#menuquestionselection',
            STUDENTVALIDATION: '#student_valide_button'
        },
        SELECTEDBORDERCOLOUR = 'rgba(200, 200, 255, 0.9)',
        SELECTEDFILLCOLOUR = 'rgba(200, 200, 255, 0.5)',
        ANNOTATIONCOLOUR = {
            'white': 'rgb(255,255,255)',
            'yellowlemon': 'rgb(255,255,0)',
            'yellow': 'rgb(255,207,53)',
            'red': 'rgb(239,69,64)',
            'green': 'rgb(152,202,62)',
            //'blue': 'rgb(125,159,211)',
            'blue': 'rgb(0,0,255)',
            'black': 'rgb(51,51,51)'
        },
        CLICKTIMEOUT = 300,
        TOOLSELECTOR = {
            'select': '.selectbutton',
            'drag': '.dragbutton',
            'resize': '.resizebutton'
        },
        TOOLTYPE = {
            'HIGHLIGHTPLUS': 1,
            'LINEPLUS': 2,
            'STAMPPLUS': 3,
            'FRAME': 4,
            'VERTICALLINE': 5,
            'STAMPCOMMENT': 6,
            'COMMENTPLUS': 7,
            'PEN': 8,
            'LINE': 9,
            'RECTANGLE': 10,
            'OVAL': 11,
            'HIGHLIGHT': 12
        },
        TOOLTYPELIB = {
            'HIGHLIGHTPLUS': 'highlightplus',
            'LINEPLUS': 'lineplus',
            'STAMPPLUS': 'stampplus',
            'FRAME': 'frame',
            'VERTICALLINE': 'verticalline',
            'STAMPCOMMENT': 'stampcomment',
            'COMMENTPLUS': 'commentplus',
            'PEN': 'pen',
            'LINE': 'line',
            'RECTANGLE': 'rectangle',
            'OVAL': 'oval',
            'HIGHLIGHT': 'highlight'
        },
        STROKEWEIGHT = 2;// This file is part of Moodle - http://moodle.org/
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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a 2d point.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @param Number x
 * @param Number y
 * @class point
 */
var POINT = function(x, y) {

    /**
     * X coordinate.
     * @property x
     * @type int
     * @public
     */
    this.x = parseInt(x, 10);

    /**
     * Y coordinate.
     * @property y
     * @type int
     * @public
     */
    this.y = parseInt(y, 10);

    /**
     * Clip this point to the rect
     * @method clip
     * @param M.assignfeedback_editpdfplus.point
     * @public
     */
    this.clip = function(bounds) {
        if (this.x < bounds.x) {
            this.x = bounds.x;
        }
        if (this.x > (bounds.x + bounds.width)) {
            this.x = bounds.x + bounds.width;
        }
        if (this.y < bounds.y) {
            this.y = bounds.y;
        }
        if (this.y > (bounds.y + bounds.height)) {
            this.y = bounds.y + bounds.height;
        }
        // For chaining.
        return this;
    };
};

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.point = POINT;
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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a 2d rect.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @param int x
 * @param int y
 * @param int width
 * @param int height
 * @class rect
 */
var RECT = function(x, y, width, height) {

    /**
     * X coordinate.
     * @property x
     * @type int
     * @public
     */
    this.x = x;

    /**
     * Y coordinate.
     * @property y
     * @type int
     * @public
     */
    this.y = y;

    /**
     * Width
     * @property width
     * @type int
     * @public
     */
    this.width = width;

    /**
     * Height
     * @property height
     * @type int
     * @public
     */
    this.height = height;

    /**
     * Set this rect to represent the smallest possible rectangle containing this list of points.
     * @method bounds
     * @param M.assignfeedback_editpdfplus.point[]
     * @public
     */
    this.bound = function(points) {
        var minx = 0,
            maxx = 0,
            miny = 0,
            maxy = 0,
            i = 0,
            point;

        for (i = 0; i < points.length; i++) {
            point = points[i];
            if (point.x < minx || i === 0) {
                minx = point.x;
            }
            if (point.x > maxx || i === 0) {
                maxx = point.x;
            }
            if (point.y < miny || i === 0) {
                miny = point.y;
            }
            if (point.y > maxy || i === 0) {
                maxy = point.y;
            }
        }
        this.x = minx;
        this.y = miny;
        this.width = maxx - minx;
        this.height = maxy - miny;
        // Allow chaining.
        return this;
    };

    /**
     * Checks if rect has min width.
     * @method has_min_width
     * @return bool true if width is more than 5px.
     * @public
     */
    this.has_min_width = function() {
        return (this.width >= 5);
    };

    /**
     * Checks if rect has min height.
     * @method has_min_height
     * @return bool true if height is more than 5px.
     * @public
     */
    this.has_min_height = function() {
        return (this.height >= 5);
    };

    /**
     * Set min. width of annotation bound.
     * @method set_min_width
     * @public
     */
    this.set_min_width = function() {
        this.width = 5;
    };

    /**
     * Set min. height of annotation bound.
     * @method set_min_height
     * @public
     */
    this.set_min_height = function() {
        this.height = 5;
    };
};

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.rect = RECT;
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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * EDIT
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class edit
 */
var EDIT = function() {

    /**
     * Starting point for the edit.
     * @property start
     * @type M.assignfeedback_editpdfplus.point|false
     * @public
     */
    this.start = false;

    /**
     * Finishing point for the edit.
     * @property end
     * @type M.assignfeedback_editpdfplus.point|false
     * @public
     */
    this.end = false;

    /**
     * Starting time for the edit.
     * @property starttime
     * @type int
     * @public
     */
    this.starttime = 0;

    /**
     * Starting point for the currently selected annotation.
     * @property annotationstart
     * @type M.assignfeedback_editpdfplus.point|false
     * @public
     */
    this.annotationstart = false;

    /**
     * The currently selected tool
     * @property tool
     * @type String
     * @public
     */
    this.tool = "drag";

    /**
     * The currently comment colour
     * @property commentcolour
     * @type String
     * @public
     */
    this.commentcolour = 'yellow';

    /**
     * The currently annotation colour
     * @property annotationcolour
     * @type String
     * @public
     */
    this.annotationcolour = 'red';

    /**
     * List of points the the current drawing path.
     * @property path
     * @type M.assignfeedback_editpdfplus.point[]
     * @public
     */
    this.path = [];
};

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.edit = EDIT;
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
/* global Y, M, SELECTOR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a drawable thing which contains both Y.Nodes, and Y.Shapes.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @param M.assignfeedback_editpdfplus.editor editor
 * @class drawable
 */
var DRAWABLE = function(editor) {

    /**
     * Reference to M.assignfeedback_editpdfplus.editor.
     * @property editor
     * @type M.assignfeedback_editpdfplus.editor
     * @public
     */
    this.editor = editor;

    /**
     * Array of Y.Shape
     * @property shapes
     * @type Y.Shape[]
     * @public
     */
    this.shapes = [];

    /**
     * Array of Y.Node
     * @property nodes
     * @type Y.Node[]
     * @public
     */
    this.nodes = [];

    /**
     * Delete the shapes from the drawable.
     * @protected
     * @method erase_drawable
     */
    this.erase = function() {
        if (this.shapes) {
            while (this.shapes.length > 0) {
                this.editor.graphic.removeShape(this.shapes.pop());
            }
        }
        if (this.nodes) {
            while (this.nodes.length > 0) {
                this.nodes.pop().remove();
            }
        }
    };

    /**
     * Update the positions of all absolutely positioned nodes, when the drawing canvas is scrolled
     * @public
     * @method scroll_update
     * @param scrollx int
     * @param scrolly int
     */
    this.scroll_update = function(scrollx, scrolly) {
        var i, x, y;
        for (i = 0; i < this.nodes.length; i++) {
            x = this.nodes[i].getData('x');
            y = this.nodes[i].getData('y');
            if (x !== undefined && y !== undefined) {
                this.nodes[i].setX(parseInt(x, 10) - scrollx);
                this.nodes[i].setY(parseInt(y, 10) - scrolly);
            }
        }
    };

    /**
     * Store the initial position of the node, so it can be updated when the drawing canvas is scrolled
     * @public
     * @method store_position
     * @param container
     * @param x
     * @param y
     */
    this.store_position = function(container, x, y) {
        var drawingregion, scrollx, scrolly;

        drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION);
        scrollx = parseInt(drawingregion.get('scrollLeft'), 10);
        scrolly = parseInt(drawingregion.get('scrollTop'), 10);
        container.setData('x', x + scrollx);
        container.setData('y', y + scrolly);
    };
};

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.drawable = DRAWABLE;
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
/* global Y, M, SELECTOR, TOOLTYPE, STROKEWEIGHT, SELECTEDBORDERCOLOUR, SELECTEDFILLCOLOUR, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing an annotation.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotation
 * @constructor
 */
var ANNOTATION = function (config) {
    ANNOTATION.superclass.constructor.apply(this, [config]);
};
ANNOTATION.NAME = "annotation";
ANNOTATION.ATTRS = {};
Y.extend(ANNOTATION, Y.Base, {
    /**
     * Reference to M.assignfeedback_editpdfplus.editor.
     * @property editor
     * @type M.assignfeedback_editpdfplus.editor
     * @public
     */
    editor: null,
    /**
     * Grade id
     * @property gradeid
     * @type Int
     * @public
     */
    gradeid: 0,
    /**
     * Comment page number
     * @property pageno
     * @type Int
     * @public
     */
    pageno: 0,
    /**
     * X position
     * @property x
     * @type Int
     * @public
     */
    x: 0,
    /**
     * Y position
     * @property y
     * @type Int
     * @public
     */
    y: 0,
    /**
     * Ending x position
     * @property endx
     * @type Int
     * @public
     */
    endx: 0,
    /**
     * Ending y position
     * @property endy
     * @type Int
     * @public
     */
    endy: 0,
    /**
     * Path
     * @property path
     * @type String - list of points like x1,y1:x2,y2
     * @public
     */
    path: '',
    /**
     * Tool.
     * @property toolid
     * @type Int
     * @public
     */
    toolid: 0,
    /**
     * Annotation colour.
     * @property colour
     * @type String
     * @public
     */
    colour: 'red',
    /**
     * Reference to M.assignfeedback_editpdfplus.drawable
     * @property drawable
     * @type M.assignfeedback_editpdfplus.drawable
     * @public
     */
    drawable: false,
    /**
     * List of all resize areas (div id) for this annotation
     * @type array
     * @public
     */
    resizeAreas: [],
    /**
     * Reference to M.assignfeedback_editpdfplus.tool
     * @property tooltype
     * @type M.assignfeedback_editpdfplus.tool
     * @public
     */
    tooltype: null,
    /**
     * Reference to M.assignfeedback_editpdfplus.type_tool
     * @property tooltypefamille
     * @type M.assignfeedback_editpdfplus.type_tool
     * @public
     */
    tooltypefamille: null,
    /**
     * HTML id for the cartridge.
     * @property divcartridge
     * @type String
     * @public
     */
    divcartridge: '',
    /**
     * Text annotation
     * @property textannot
     * @type String
     * @public
     */
    textannot: '',
    /**
     * Get the information of display (3 positions).
     * @property displaylock
     * @type Int
     * @public
     */
    displaylock: 1,
    /**
     * Display the orientation of the stamp
     * @property displayrotation
     * @type Int
     * @public
     */
    displayrotation: 0,
    /**
     * Border style for cartridge and other element (frame...)
     * @property borderstyle
     * @type String
     * @public
     */
    borderstyle: '',
    /**
     * Parent annotation ID.
     * @property parent_annot
     * @type Int
     * @public
     */
    parent_annot: 0,
    /**
     * Reference to M.assignfeedback_editpdfplus.annotation
     * @property parent_annot_element
     * @type M.assignfeedback_editpdfplus.annotation
     * @public
     */
    parent_annot_element: null,
    /**
     * id of the annotation in BDD.
     * @property id
     * @type Int
     * @public
     */
    id: 0,
    /**
     * Shape HTML id
     * @property shape_id
     * @type String
     * @public
     */
    shape_id: '',
    /**
     * position x of the cartridge.
     * @property cartridgex
     * @type Int
     * @public
     */
    cartridgex: 0,
    /**
     * position y of the cartridge.
     * @property cartridgey
     * @type Int
     * @public
     */
    cartridgey: 0,
    /**
     * If the annotation is an question or not.
     * @property answerrequested
     * @type Int
     * @public
     */
    answerrequested: 0,
    /**
     * Student status of the annotation.
     * @property studentstatus
     * @type Int
     * @public
     */
    studentstatus: 0,
    /**
     * Student answer for the comment in this annotation
     * @property studentanswer
     * @type String
     * @public
     */
    studentanswer: "",
    /**
     * pdf display for this annotation
     * @property pdfdisplay
     * @type String
     * @public
     */
    pdfdisplay: "footnote",
    /**
     * minimum size for resize area
     * @type Int
     * @public
     */
    minresizewidth: 20,
    /**
     * Initialise the annotation.
     *
     * @method initializer
     * @return void
     */
    initializer: function (config) {
        if (config.parent_annot_element) {
            this.editor = config.parent_annot_element.editor || null;
            this.gradeid = parseInt(config.parent_annot_element.gradeid, 10) || 0;
            this.pageno = parseInt(config.parent_annot_element.pageno, 10) || 0;
            this.cartridgex = parseInt(config.parent_annot_element.cartridgex, 10) || 0;
            this.cartridgey = parseInt(config.parent_annot_element.cartridgey, 10) || 0;
            this.colour = config.parent_annot_element.colour || 'red';
            this.tooltype = config.tooltype;
            this.textannot = config.parent_annot_element.textannot;
            this.displaylock = parseInt(config.parent_annot_element.displaylock, 10);
            this.displayrotation = config.parent_annot_element.displayrotation;
            this.borderstyle = config.parent_annot_element.borderstyle || 'solid';
            this.parent_annot = parseInt(config.parent_annot_element.id, 10);
            this.answerrequested = parseInt(config.parent_annot_element.answerrequested, 10) || 0;
            this.studentstatus = parseInt(config.parent_annot_element.studentstatus, 10) || 0;
            this.parent_annot_element = config.parent_annot_element;
        } else {
            this.editor = config.editor || null;
            this.gradeid = parseInt(config.gradeid, 10) || 0;
            this.pageno = parseInt(config.pageno, 10) || 0;
            this.cartridgex = parseInt(config.cartridgex, 10) || 0;
            this.cartridgey = parseInt(config.cartridgey, 10) || 0;
            this.colour = config.colour || 'red';
            this.tooltype = config.tooltype;
            this.textannot = config.textannot;
            this.displaylock = parseInt(config.displaylock, 10);
            this.displayrotation = config.displayrotation;
            this.borderstyle = config.borderstyle || 'solid';
            this.parent_annot = parseInt(config.parent_annot, 10);
            this.answerrequested = parseInt(config.answerrequested, 10) || 0;
            this.studentstatus = parseInt(config.studentstatus, 10) || 0;
            this.studentanswer = config.studentanswer;
        }
        this.id = config.id;
        this.x = parseInt(config.x, 10) || 0;
        this.y = parseInt(config.y, 10) || 0;
        this.endx = parseInt(config.endx, 10) || 0;
        this.endy = parseInt(config.endy, 10) || 0;
        this.path = config.path || '';
        this.toolid = config.toolid || this.editor.get_dialogue_element(TOOLTYPE.RECTANGLE);
        this.drawable = false;
        this.resizeAreas = [];
        this.pdfdisplay = config.pdfdisplay;
        this.tooltypefamille = this.editor.typetools[this.tooltype.type];
    },
    /**
     * Clean a comment record, returning an oject with only fields that are valid.
     * @public
     * @method clean
     * @return {}
     */
    clean: function () {
        if (this.parent_annot_element) {
            return {
                id: this.id,
                gradeid: this.gradeid,
                x: parseInt(this.x, 10),
                y: parseInt(this.y, 10),
                endx: parseInt(this.endx, 10),
                endy: parseInt(this.endy, 10),
                cartridgex: parseInt(this.cartridgex, 10),
                cartridgey: parseInt(this.cartridgey, 10),
                toolid: this.toolid,
                path: this.path,
                pageno: this.pageno,
                colour: this.colour,
                textannot: this.textannot,
                displaylock: parseInt(this.displaylock, 10),
                displayrotation: parseInt(this.displayrotation, 10),
                borderstyle: this.borderstyle,
                parent_annot: parseInt(this.parent_annot, 10),
                divcartridge: this.divcartridge,
                parent_annot_div: this.parent_annot_element.divcartridge,
                answerrequested: parseInt(this.answerrequested, 10),
                pdfdisplay: this.pdfdisplay
            };
        }
        return {
            id: this.id,
            gradeid: this.gradeid,
            x: parseInt(this.x, 10),
            y: parseInt(this.y, 10),
            endx: parseInt(this.endx, 10),
            endy: parseInt(this.endy, 10),
            cartridgex: parseInt(this.cartridgex, 10),
            cartridgey: parseInt(this.cartridgey, 10),
            toolid: this.toolid,
            path: this.path,
            pageno: this.pageno,
            colour: this.colour,
            textannot: this.textannot,
            displaylock: parseInt(this.displaylock, 10),
            displayrotation: parseInt(this.displayrotation, 10),
            borderstyle: this.borderstyle,
            parent_annot: parseInt(this.parent_annot, 10),
            divcartridge: this.divcartridge,
            parent_annot_div: '',
            answerrequested: parseInt(this.answerrequested, 10),
            studentstatus: parseInt(this.studentstatus, 10),
            pdfdisplay: this.pdfdisplay
        };
    },
    /**
     * Clean a comment record, returning an oject with only fields that are valid.
     * @public
     * @method clean
     * @return {}
     */
    light_clean: function () {
        return {
            id: this.id,
            studentstatus: parseInt(this.studentstatus, 10),
            studentanswer: this.studentanswer
        };
    },
    /**
     * Draw a selection around this annotation if it is selected.
     * @public
     * @method draw_highlight
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw_highlight: function () {
        var bounds,
                drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
                offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY(),
                shape;
        if (this.editor.currentannotation === this) {
            // Draw a highlight around the annotation.
            bounds = new M.assignfeedback_editpdfplus.rect();
            bounds.bound([new M.assignfeedback_editpdfplus.point(this.x - 10, this.y - 10),
                new M.assignfeedback_editpdfplus.point(this.endx + 10, this.endy + 10)]);
            shape = this.editor.graphic.addShape({
                type: Y.Rect,
                width: bounds.width,
                height: bounds.height,
                stroke: {
                    weight: STROKEWEIGHT,
                    color: SELECTEDBORDERCOLOUR
                },
                fill: {
                    color: SELECTEDFILLCOLOUR
                },
                x: bounds.x,
                y: bounds.y
            });
            this.drawable.shapes.push(shape);
            shape.editor = this.editor;
            shape.on('clickoutside', Y.rbind(this.editor.redraw_annotation, this.editor));
            // Add a delete X to the annotation.
            var deleteicon = Y.Node.create('<i class="fa fa-trash" aria-hidden="true"></i>'),
                    deletelink = Y.Node.create('<a href="#" role="button"></a>');
            deleteicon.setAttrs({
                'alt': M.util.get_string('deleteannotation', 'assignfeedback_editpdfplus')
            });
            deleteicon.setStyles({
                'backgroundColor': 'white'
            });
            deletelink.addClass('deleteannotationbutton');
            deletelink.append(deleteicon);
            drawingregion.append(deletelink);
            deletelink.setData('annotation', this);
            deletelink.setStyle('zIndex', '200');
            deletelink.on('click', this.remove, this);
            deletelink.on('key', this.remove, 'space,enter', this);
            deletelink.setX(offsetcanvas[0] + bounds.x + bounds.width - 18);
            deletelink.setY(offsetcanvas[1] + bounds.y + bounds.height - 18);
            this.drawable.nodes.push(deletelink);
        }
        return this.drawable;
    },
    /**
     * Draw an annotation
     * @public
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable|false
     */
    draw: function () {
        // Should be overridden by the subclass.
        this.draw_highlight();
        return this.drawable;
    },
    /**
     * Get the final color for the annotation
     * @return string
     * @protected
     */
    get_color: function () {
        var color = ANNOTATIONCOLOUR[this.colour];
        if (!color) {
            color = this.colour;
        } else {
            // Add an alpha channel to the rgb colour.
            color = color.replace('rgb', 'rgba');
            color = color.replace(')', ',0.5)');
        }
        return color;
    },
    /**
     * Get the final color for the cartridge
     * @return string
     * @protected
     */
    get_color_cartridge: function () {
        var color = ANNOTATIONCOLOUR[this.tooltype.cartridge_color];
        if (!color) {
            color = this.tooltype.cartridge_color;
        } else {
            // Add an alpha channel to the rgb colour.
            color = color.replace('rgb', 'rgba');
            color = color.replace(')', ',0.5)');
        }
        if (color === '') {
            return this.tooltypefamille.cartridge_color;
        }
        return color;
    },
    /**
     * Init the HTML id for the annotation
     * @protected
     */
    init_div_cartridge_id: function () {
        var date = (new Date().toJSON()).replace(/:/g, '').replace(/\./g, '');
        this.divcartridge = 'ct_' + this.tooltype.id + '_' + date;
    },
    /**
     * Init the HTML id for the shape
     * @protected
     * @param {String} toolname
     * @returns {String} the shape id
     */
    init_shape_id: function (toolname) {
        if (!this.shape_id) {
            //create only one time the shape_id
            var d = new Date();
            var n = d.getTime();
            this.shape_id = "ct_" + toolname + "_" + n;
        }
        return this.shape_id;
    },
    /**
     * get the html node for the cartridge
     * @param {string} colorcartridge
     * @return node
     */
    get_div_cartridge: function (colorcartridge) {
        var div = "<div ";
        div += "id='" + this.divcartridge + "' ";
        div += "class='assignfeedback_editpdfplus_cartridge' ";
        div += "style='border-color: " + colorcartridge + ";'> ";
        div += "</div>";
        var divdisplay = Y.Node.create(div);
        if (this.editor.get('readonly')) {
            divdisplay.on('click', this.view_annot, this);
        }
        return divdisplay;
    },
    /**
     * get the html node for the label cartridge
     * @param {string} colorcartridge
     * @param {boolean} draggable
     * @return node
     */
    get_div_cartridge_label: function (colorcartridge, draggable) {
        var divcartridge = "<div ";
        divcartridge += "id='" + this.divcartridge + "_cartridge' ";
        divcartridge += "class='assignfeedback_editpdfplus_" + this.tooltypefamille.label + "_cartridge' ";
        if (this.editor.get('readonly') && this.get_valref() === '') {
            divcartridge += "style='border-right:none;padding-right:0px;color:" + colorcartridge + ";' ";
        } else {
            divcartridge += "style='border-right-color: " + colorcartridge + ";color:" + colorcartridge + ";' ";
        }
        divcartridge += "> ";
        divcartridge += this.tooltype.cartridge;
        divcartridge += "</div>";
        var divcartridgedisplay = Y.Node.create(divcartridge);
        if (draggable && !this.editor.get('readonly')) {
            divcartridgedisplay.on('mousedown', this.move_cartridge_begin, this);
            return divcartridgedisplay;
        }
        return divcartridgedisplay;
    },
    /**
     * get the html node for the textannot associated to the annotation
     * @param {string} colorcartridge
     * @return node
     */
    get_div_input: function (colorcartridge) {
        var divinput = "<div ";
        divinput += "id='" + this.divcartridge + "_display' ";
        divinput += "style='color:" + colorcartridge + "; ";
        if (this.editor.get('readonly') && this.get_valref() === '') {
            divinput += "padding:0px;";
        }
        divinput += "'></div>";
        var divinputdisplay = Y.Node.create(divinput);
        if (!this.editor.get('readonly')) {
            divinputdisplay.on('click', this.edit_annot, this);
        }
        return divinputdisplay;
    },
    /**
     * get the html node for the edition of comment and parameters
     * @return node
     */
    get_div_edition: function () {
        var divedition = "<div ";
        divedition += "id='" + this.divcartridge + "_edit' ";
        divedition += "class='assignfeedback_editpdfplus_" + this.tooltypefamille.label + "_edition' ";
        divedition += "style='display:none;'> ";
        divedition += "<textarea id='"
                + this.divcartridge
                + "_editinput' type='text' value=\""
                + this.get_valref() + "\" class='form-control' style='margin-bottom:5px;' >"
                + this.get_valref() + "</textarea>";
        divedition += "</div>";
        var diveditiondisplay = Y.Node.create(divedition);
        var propositions = this.tooltype.texts;
        if (propositions && propositions.length > 0) {
            var divproposition = "<div class='btn-group-vertical aepp-toolbar-vertical'></div>";
            var divpropositiondisplay = Y.Node.create(divproposition);
            var propositionarray = propositions.split('","');
            for (var i = 0; i < propositionarray.length; i++) {
                var buttontmp = "<button class='btn btn-outline-dark' type='button'>"
                        + propositionarray[i].replace('"', '')
                        + "</button>";
                var buttontmpdisplay = Y.Node.create(buttontmp);
                buttontmpdisplay.on('click', this.fill_input_edition, this, propositionarray[i].replace('"', ''));
                divpropositiondisplay.append(buttontmpdisplay);
                divpropositiondisplay.append("<br/>");
            }
            diveditiondisplay.append(divpropositiondisplay);
        }
        return diveditiondisplay;
    },
    /**
     * get the html node for the visualisation of comment and question
     * @param {string} colorcartridge
     * @return node
     */
    get_div_visu: function (colorcartridge) {
        var divvisu = "<div ";
        divvisu += "id='" + this.divcartridge + "_visu' ";
        divvisu += "class='assignfeedback_editpdfplus_" + this.tooltypefamille.label + "_visu' ";
        divvisu += "style='display:none;color:" + colorcartridge + ";'> ";
        divvisu += this.get_valref().replace(/\n/g, "<br/>");
        divvisu += "</div>";
        var divvisudisplay = Y.Node.create(divvisu);
        if (this.answerrequested === 1) {
            var divinput = Y.Node.create("<div></div>");
            var hr = Y.Node.create("<hr style='margin-bottom:0px;'/>");
            var label = Y.Node.create("<label style='display:inline;'>"
                    + M.util.get_string('student_answer_lib', 'assignfeedback_editpdfplus')
                    + "</label>");
            var rep = "";
            if (this.studentanswer && this.studentanswer !== "0" && this.studentanswer !== "1") {
                rep = this.studentanswer;
            }
            var textarea = Y.Node.create("<br/><textarea id='"
                    + this.divcartridge
                    + "_studentanswer' type='text' value=\""
                    + rep + "\" class='form-control'>"
                    + rep
                    + "</textarea>");
            rep = this.studentanswer;
            var buttonsave = "<button id='"
                    + this.divcartridge
                    + "_buttonsavestudentanswer' style='margin-left:110px;' class='btn' type='button'>"
                    //+ M.util.image_url('e/save', 'core')
                    + "<i class='fa fa-floppy-o' aria-hidden='true'></i>"
                    + "</button>";
            var buttonsavedisplay = Y.Node.create(buttonsave);
            buttonsavedisplay.on('click', this.save_studentanswer, this, null);
            divinput.append(hr);
            divinput.append(label);
            divinput.append(buttonsavedisplay);
            divinput.append(textarea);
            divvisudisplay.append(divinput);
        }
        return divvisudisplay;
    },
    /**
     * get the html node for the text annotation, tools and options
     * @param {string} colorcartridge
     * @return node
     */
    get_div_container: function (colorcartridge) {
        var divconteneur = "<div ";
        divconteneur += "class='assignfeedback_editpdfplus_" + this.tooltypefamille.label + "_conteneur' >";
        divconteneur += "</div>";
        var divconteneurdisplay = Y.Node.create(divconteneur);
        var divinputdisplay = this.get_div_input(colorcartridge);
        divinputdisplay.addClass('assignfeedback_editpdfplus_' + this.tooltypefamille.label + '_input');
        //var inputvalref = this.get_input_valref();
        var onof = 1;
        if (this.displaylock || this.displaylock >= 0) {
            onof = this.displaylock;
        }
        var inputonof = Y.Node.create("<input type='hidden' id='" + this.divcartridge + "_onof' value=" + onof + " />");
        var readonly = this.editor.get('readonly');
        if (!readonly) {
            divinputdisplay.on('click', this.edit_annot, this);
        }
        divconteneurdisplay.append(divinputdisplay);
        divconteneurdisplay.append(inputonof);
        divconteneurdisplay.append(this.get_input_question());
        divconteneurdisplay.append(this.get_input_pdfdisplay());

        return divconteneurdisplay;
    },

    /**
     * get the html node for toolbar on annotation
     * @return node
     */
    get_toolbar: function () {
        var divtoolbar = "<div id='" + this.divcartridge + "_toolbar' class='btn-group btn-group-sm aepp-toolbar'></div>";
        var divtoolbardisplay = Y.Node.create(divtoolbar);
        var readonly = this.editor.get('readonly');
        if (!readonly) {
            divtoolbardisplay.append(this.get_button_visibility_left());
            divtoolbardisplay.append(this.get_button_visibility_right());
            divtoolbardisplay.append(this.get_button_save());
            divtoolbardisplay.append(this.get_button_cancel());
            if (this.tooltype.reply === 1) {
                divtoolbardisplay.append(this.get_button_question());
            }
            divtoolbardisplay.append(this.get_button_pdfdisplay());
            divtoolbardisplay.append(this.get_button_remove());
        } else {
            divtoolbardisplay.append(this.get_button_student_status());
        }
        return divtoolbardisplay;
    },

    /**
     * get the html node for student button to set status
     * @return node
     */
    get_button_student_status: function () {
        var buttonstatus1 = '<label style="padding-left:20px;" class="radio-inline"><input type="radio" name="'
                + this.divcartridge
                + '_status" value=0 >'
                + M.util.get_string('student_statut_nc', 'assignfeedback_editpdfplus')
                + '</label>';
        var buttonstatus2 = '<label class="radio-inline"><input type="radio" name="'
                + this.divcartridge
                + '_status" value=1 >'
                + '<i style="color:green;" class="fa fa-check" aria-hidden="true"></i>'
                + '</label>';
        var buttonstatus3 = '<label class="radio-inline"><input type="radio" name="'
                + this.divcartridge
                + '_status" value=2 >'
                + '<i style="color:red;" class="fa fa-times" aria-hidden="true"></i>'
                + '</label> ';
        var buttonstatus1display = Y.Node.create(buttonstatus1);
        var buttonstatus2display = Y.Node.create(buttonstatus2);
        var buttonstatus3display = Y.Node.create(buttonstatus3);
        buttonstatus1display.on('click', this.change_status, this, 0);
        buttonstatus2display.on('click', this.change_status, this, 1);
        buttonstatus3display.on('click', this.change_status, this, 2);
        var buttonstatusdisplay = Y.Node.create("<div id='"
                + this.divcartridge
                + "_radioContainer' style='display:inline;'></div>");
        buttonstatusdisplay.append(buttonstatus1display);
        buttonstatusdisplay.append(buttonstatus2display);
        buttonstatusdisplay.append(buttonstatus3display);
        return buttonstatusdisplay;
    },
    /**
     * get the html node for the button to set visibility on right
     * @return node
     */
    get_button_visibility_right: function () {
        var buttonvisibility = "<button id='" + this.divcartridge
                + "_buttonedit_right' class='btn btn-sm btn-outline-dark' type='button'>";
        buttonvisibility += "<i class='fa fa-arrow-right' aria-hidden='true'></i>";
        buttonvisibility += "</button>";
        var buttonvisibilitydisplay = Y.Node.create(buttonvisibility);
        buttonvisibilitydisplay.on('click', this.change_visibility_annot, this, 'r');
        return buttonvisibilitydisplay;
    },
    /**
     * get the html node for the button to set visibility on left
     * @return node
     */
    get_button_visibility_left: function () {
        var buttonvisibility = "<button id='" + this.divcartridge
                + "_buttonedit_left' class='btn btn-sm btn-outline-dark' type='button'>";
        buttonvisibility += "<i class='fa fa-arrow-left' aria-hidden='true'></i>";
        buttonvisibility += "</button>";
        var buttonvisibilitydisplay = Y.Node.create(buttonvisibility);
        buttonvisibilitydisplay.on('click', this.change_visibility_annot, this, 'l');
        return buttonvisibilitydisplay;
    },
    /**
     * get the html node for the button to save the text in the annotation
     * @return node
     */
    get_button_save: function () {
        var buttonsave = "<button id='"
                + this.divcartridge
                + "_buttonsave' style='display:none;margin-left:110px;' class='btn btn-sm btn-outline-dark' type='button'>"
                + "<i class='fa fa-check' aria-hidden='true'></i>"
                + "</button>";
        var buttonsavedisplay = Y.Node.create(buttonsave);
        buttonsavedisplay.on('click', this.save_annot, this, null);
        return buttonsavedisplay;
    },
    /**
     * get the html node for the button to cancel the text edition of the annotation
     * @return node
     */
    get_button_cancel: function () {
        var buttoncancel = "<button id='"
                + this.divcartridge
                + "_buttoncancel' style='display:none;' class='btn btn-sm btn-outline-dark' type='button'>"
                + "<i class='fa fa-undo' aria-hidden='true'></i>"
                + "</button>";
        var buttoncanceldisplay = Y.Node.create(buttoncancel);
        buttoncanceldisplay.on('click', this.cancel_edit, this);
        return buttoncanceldisplay;
    },
    /**
     * get the html node for the button to set a question
     * @return node
     */
    get_button_question: function () {
        var buttonquestion = "<button id='"
                + this.divcartridge
                + "_buttonquestion' style='display:none;margin-left:10px;' class='btn btn-sm btn-outline-dark' type='button'>"
                + '<span class="fa-stack fa-lg" style="line-height: 1em;width: 1em;">'
                + '<i class="fa fa-question-circle-o fa-stack-1x"></i>'
                + '<i class="fa fa-ban fa-stack-1x text-danger"></i>'
                + '</span>'
                + "</button>";
        var buttonquestiondisplay = Y.Node.create(buttonquestion);
        buttonquestiondisplay.on('click', this.change_question_status, this);
        return buttonquestiondisplay;
    },
    /**
     * get the html node for the button to remove the annotation
     * @return node
     */
    get_button_remove: function () {
        var buttontrash = "<button id='"
                + this.divcartridge
                + "_buttonremove' style='display:none;margin-left:10px;' class='btn btn-sm btn-outline-dark' type='button'>"
                + "<i class='fa fa-trash' aria-hidden='true'></i>"
                + "</button>";
        var buttontrashdisplay = Y.Node.create(buttontrash);
        buttontrashdisplay.on('click', this.remove_by_trash, this);
        return buttontrashdisplay;
    },
    /**
     * get the html node for the button to change display on pdf for the annotation
     * @return node
     */
    get_button_pdfdisplay: function () {
        var buttontrash = "<button id='"
                + this.divcartridge
                + "_buttonpdfdisplay' style='display:none;margin-left:10px;' class='btn btn-sm btn-outline-dark' type='button'>"
                + "<i class='fa fa-file-pdf-o' aria-hidden='true'></i>&nbsp;"
                + "<i class='fa fa-arrow-circle-o-down' aria-hidden='true'></i>"
                + "</button>";
        var buttontrashdisplay = Y.Node.create(buttontrash);
        buttontrashdisplay.on('click', this.change_pdf_display, this);
        return buttontrashdisplay;
    },
    /**
     * get the html node for the hidden input to keep information about question state
     * @return node
     */
    get_input_question: function () {
        var qst = 0;
        if (this.answerrequested && this.answerrequested === 1) {
            qst = 1;
        }
        return Y.Node.create("<input type='hidden' id='" + this.divcartridge + "_question' value='" + qst + "'/>");
    },
    /**
     * get the html node for the hidden input to keep information about question state
     * @return node
     */
    get_input_pdfdisplay: function () {
        return Y.Node.create("<input type='hidden' id='" + this.divcartridge + "_pdfdisplay' value='" + this.pdfdisplay + "'/>");
    },
    /**
     * get the final reference text value
     * @return node
     */
    get_valref: function () {
        if (this.textannot && this.textannot.length > 0 && typeof this.textannot === 'string') {
            return this.textannot;
        }
        return '';
    },
    /**
     * get the html node for the hidden input to keep real reference text value
     * @return node
     * @deprecated since 11/16
     */
    get_input_valref: function () {
        return Y.Node.create("<input type='hidden' id='" + this.divcartridge + "_valref' value=\"" + this.get_valref() + "\"/>");
    },
    /**
     * display the annotation according to parameters and profile
     * @return node
     */
    apply_visibility_annot: function () {
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        var interrupt = this.editor.get_dialogue_element('#' + this.divcartridge + "_onof");
        var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
        var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
        var buttonstatus = this.editor.get_dialogue_element('#' + this.divcartridge + "_radioContainer");
        if (interrupt) {
            if (interrupt.get('value') === '1') {
                if (buttonplusr) {
                    buttonplusr.show();
                }
                if (buttonplusl) {
                    buttonplusl.show();
                }
            } else if (interrupt.get('value') === '0') {
                if (buttonplusr) {
                    buttonplusr.show();
                }
                if (buttonplusl) {
                    buttonplusl.hide();
                }
            } else {
                if (buttonplusr) {
                    buttonplusr.hide();
                }
                if (buttonplusl) {
                    buttonplusl.show();
                }
            }
        }
        if (divdisplay) {
            divdisplay.setContent(this.get_text_to_diplay_in_cartridge());
        }
        if (this.tooltypefamille.label === 'frame' && buttonplusr) {
            buttonplusr.hide();
            buttonplusl.hide();
        }
        if (buttonstatus) {
            buttonstatus.hide();
        }
        this.apply_question_status();
        this.apply_pdfdisplay();
    },
    /**
     * get the html node for the text to display for the annotation, according to parameters
     * @return node
     */
    get_text_to_diplay_in_cartridge: function () {
        var valref = this.get_valref();
        var interrupt = this.editor.get_dialogue_element('#' + this.divcartridge + "_onof");
        var finalcontent = "";
        if (valref === '' && !this.editor.get('readonly')) {
            finalcontent = '&nbsp;&nbsp;&nbsp;&nbsp';
        }
        if (interrupt.get('value') === '1' && valref !== '') {
            finalcontent = valref.substr(0, 20);
        } else if (interrupt.get('value') === '0' && valref !== '') {
            finalcontent = '...';
        } else if (valref !== '') {
            finalcontent = valref;
        }
        if (this.answerrequested === 1) {
            finalcontent += '&nbsp;<span style="color:red;">[?]</span>';
        }
        return finalcontent;
    },
    /**
     * change the visibility of the annotation according to parameters and variable sens
     * @param {type} e
     * @param {char} sens
     */
    change_visibility_annot: function (e, sens) {
        var interrupt = this.editor.get_dialogue_element('#' + this.divcartridge + "_onof");
        var finalvalue = parseInt(interrupt.get('value'), 10);
        if (sens === 'r') {
            finalvalue += 1;
        } else {
            finalvalue -= 1;
        }
        interrupt.set('value', finalvalue);
        this.displaylock = finalvalue;
        this.apply_visibility_annot();
        this.editor.save_current_page();
    },
    /**
     * change question status of the annotation (with or not)
     */
    change_pdf_display: function () {
        var pdfdisplayvalue = this.editor.get_dialogue_element('#' + this.divcartridge + "_pdfdisplay");
        var value = pdfdisplayvalue.get('value');
        if (value === "footnote") {
            pdfdisplayvalue.set('value', "inline");
            this.pdfdisplay = "inline";
        } else {
            pdfdisplayvalue.set('value', "footnote");
            this.pdfdisplay = "footnote";
        }
        this.apply_pdfdisplay();
        this.editor.save_current_page();
    },
    /**
     * change question status of the annotation (with or not)
     */
    change_question_status: function () {
        var questionvalue = this.editor.get_dialogue_element('#' + this.divcartridge + "_question");
        var value = parseInt(questionvalue.get('value'), 10);
        var finalvalue = 0;
        if (value === 0) {
            finalvalue = 1;
        }
        questionvalue.set('value', finalvalue);
        this.answerrequested = finalvalue;
        this.apply_question_status();
        this.editor.save_current_page();
    },
    /**
     * change student status of the annotation
     * @param {type} e
     * @param {int} idclick value of new status
     */
    change_status: function (e, idclick) {
        this.studentstatus = idclick;
        var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_studentanswer");
        if (input) {
            this.studentanswer = input.get('value');
        }

        var shapesChildren = this.editor.annotationsparent[this.id];
        if (shapesChildren) {
            for (var i = 0; i < shapesChildren.length; i++) {
                shapesChildren[i].studentstatus = idclick;
            }
        }

        this.editor.save_current_page_edited();
        this.hide_edit();
    },
    /**
     * change question set of the annotation
     * @return null
     */
    apply_question_status: function () {
        var buttonquestion = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonquestion");
        var questionvalue = this.editor.get_dialogue_element('#' + this.divcartridge + "_question");
        var value = 0;
        if (questionvalue) {
            value = parseInt(questionvalue.get('value'), 10);
        }
        if (buttonquestion) {
            if (value === 1) {
                buttonquestion.setHTML('<i class="fa fa-question-circle-o"></i>');
            } else {
                buttonquestion.setHTML('<span class="fa-stack fa-lg" style="line-height: 1em;width: 1em;">'
                        + '<i class="fa fa-question-circle-o fa-stack-1x"></i>'
                        + '<i class="fa fa-ban fa-stack-1x text-danger"></i>'
                        + '</span>');
            }
        }
        return;
    },
    /**
     * change pdf display mode set of the annotation
     * @return null
     */
    apply_pdfdisplay: function () {
        var buttonpdf = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpdfdisplay");
        var pdfdisplayvalue = this.editor.get_dialogue_element('#' + this.divcartridge + "_pdfdisplay");
        var value = pdfdisplayvalue.get('value');
        if (buttonpdf) {
            if (value === 'footnote') {
                buttonpdf.setHTML("<i class='fa fa-file-pdf-o' aria-hidden='true'></i>&nbsp;"
                        + "<i class='fa fa-arrow-circle-o-down' aria-hidden='true'></i>");
            } else {
                buttonpdf.setHTML("<i class='fa fa-file-pdf-o' aria-hidden='true'></i>&nbsp;"
                        + "<i class='fa fa-arrow-circle-o-right' aria-hidden='true'></i>");
            }
        }
        return;
    },
    /**
     * drag-and-drop start
     * @param {type} e
     */
    move_cartridge_begin: function (e) {
        e.preventDefault();
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                        e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        this.oldx = point.x;
        this.oldy = point.y;
        canvas.on('mousemove', this.move_cartridge_continue, this);
        canvas.on('mouseup', this.move_cartridge_stop, this);
    },
    /**
     * drag-and-drop process
     * @param {type} e
     */
    move_cartridge_continue: function (e) {
        e.preventDefault();
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                        e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.x + this.cartridgex + diffx);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey + diffy);
    },
    /**
     * drag-and-drop stop
     * @param {type} e
     */
    move_cartridge_stop: function (e) {
        e.preventDefault();
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        canvas.detach('mousemove', this.move_cartridge_continue, this);
        canvas.detach('mouseup', this.move_cartridge_stop, this);
        var clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;
        this.cartridgex += diffx;
        this.cartridgey += diffy;
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.x + this.cartridgex);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey);
        //window.console.log('move_cartridge_stop');
        this.editor.save_current_page();
    },
    /**
     * global method, draw empty cartridge
     */
    draw_catridge: function () {
        return true;
    },
    /**
     * global method, replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        return true;
    },
    /**
     * global method, draw empty resize area
     */
    draw_resizeAreas: function () {
        return true;
    },
    /**
     * get the html node for the cartridge
     * @param {string} colorcartridge
     * @return node
     */
    get_div_resizearea: function (direction, minwidth, minheight) {
        var plane = "horizontal";
        if (direction === "up" || direction === "down") {
            plane = "vertical";
        }
        var div = "<div "
                + "id='" + this.divcartridge + "_resize_" + direction + "' "
                + "class='assignfeedback_editpdfplus_resize assignfeedback_editpdfplus_resize_" + plane + "' ";
        if (plane === "horizontal") {
            var intery = Math.max(this.endy - this.y, 7);
            if (minheight) {
                intery = minheight;
            }
            div += "style='min-width:7px;min-height:" + intery + "px;' ";
        } else {
            var interx = Math.max(this.endx - this.x, 7);
            if (minwidth) {
                interx = minwidth;
            }
            div += "style='min-height:7px;min-width:" + interx + "px;' ";
        }
        div += "data-direction='" + direction + "' ";
        div += "data-page='" + this.pageno + "' "
                + "> "
                + "</div>";
        return Y.Node.create(div);
    },
    /**
     * Remove all resize areas
     */
    remove_resizearea: function () {
        var divAreaResize = Y.all('.assignfeedback_editpdfplus_resize');
        divAreaResize.remove();
    },
    /**
     * Insert new resize area in the DOM
     * @param {String} direction direction for the resizing {left, up down, right}
     * @param {int} x left position of the resize area
     * @param {int} y top position of the resize area
     */
    push_div_resizearea: function (direction, x, y, minwidth, minheight) {
        var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        var div = this.editor.get_dialogue_element('#' + this.divcartridge + "_resize_" + direction);
        if (div) {
            return;
        }
        var divresize = this.get_div_resizearea(direction, minwidth, minheight);
        if (!divresize) {
            return;
        }
        divresize.setX(x);
        divresize.setY(y);
        drawingregion.append(divresize);
        this.resizeAreas.push(divresize);
    },
    /**
     * global method, actions when resizing a shape
     */
    mousemoveResize: function () {
        return true;
    },
    /**
     * Actions after resizing a shape
     * - save new positions
     * - redraw cartridge
     * @param {Event} e click event
     * @param {Div node} divresize resize area div
     */
    mouseupResize: function (e, divresize) {
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        var offset = canvas.getXY();
        var direction = divresize.getData('direction');
        if (direction === 'right') {
            this.endx = e.clientX + canvas.get('docScrollX') - offset[0];
        } else if (direction === 'left') {
            this.x = e.clientX + canvas.get('docScrollX') - offset[0];
        } else if (direction === 'up') {
            this.y = e.clientY + canvas.get('docScrollY') - offset[1];
        } else if (direction === 'down') {
            this.endy = e.clientY + canvas.get('docScrollY') - offset[1];
        }
        this.replacement_cartridge();
    },
    /**
     * display annotation view
     * @param {type} e
     * @param {string} clickType
     */
    view_annot: function (e, clickType) {
        if (!clickType || !(clickType === 'click' && this.editor.currentannotationreview === this)) {
            this.editor.currentannotationreview = this;
            if (this.tooltype.type <= TOOLTYPE.COMMENTPLUS && !this.parent_annot_element) {
                var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
                var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
                var divvisu = this.editor.get_dialogue_element('#' + this.divcartridge + "_visu");
                var buttonstatus = this.editor.get_dialogue_element('#' + this.divcartridge + "_radioContainer");
                var studentstatusinput = Y.all("[name=" + this.divcartridge + "_status]");
                divdisplay.hide();
                divvisu.show();
                if (this.answerrequested === 1) {
                    var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_studentanswer");
                    if (input) {
                        input.set(this.studentanswer);
                    }
                }
                for (var i = 0; i < studentstatusinput.size(); i++) {
                    var tmp = studentstatusinput.item(i);
                    if (parseInt(tmp.get('value'), 10) === this.studentstatus) {
                        tmp.set('checked', true);
                    } else {
                        tmp.set('checked', false);
                    }
                }
                buttonstatus.show();
                buttonstatus.set('style', 'display:inline;color:' + this.get_color_cartridge() + ';');
                divprincipale.setStyle('z-index', 1000);
                this.disabled_canvas_event();
                divprincipale.detach();
                divprincipale.on('clickoutside', this.hide_edit, this, 'clickoutside');
            }
        } else {
            this.editor.currentannotationreview = null;
        }
    },
    /**
     * display annotation edditing view
     */
    edit_annot: function () {
        if (this.tooltype.type <= TOOLTYPE.COMMENTPLUS && !this.parent_annot_element) {
            var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
            var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
            if (!divdisplay) {
                //for basic tools (pen, rectangle,...)
                return;
            }
            var divedit = this.editor.get_dialogue_element('#' + this.divcartridge + "_edit");
            var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
            var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
            var buttonsave = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonsave");
            var buttoncancel = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttoncancel");
            var buttonquestion = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonquestion");
            var buttonrotation = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonrotation");
            var buttonpdfdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpdfdisplay");
            var buttonremove = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonremove");
            var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_editinput");
            divdisplay.hide();
            if (buttonplusr) {
                buttonplusr.hide();
            }
            if (buttonplusl) {
                buttonplusl.hide();
            }
            if (buttonrotation) {
                buttonrotation.hide();
            }
            divedit.show();
            buttonsave.show();
            buttoncancel.show();
            if (buttonquestion) {
                buttonquestion.show();
            }
            buttonpdfdisplay.show();
            buttonremove.show();
            divprincipale.setStyle('z-index', 1000);
            if (input) {
                input.set('focus', 'on');
            }
            this.disabled_canvas_event();
            divprincipale.on('clickoutside', this.save_annot_clickout, this, 'clickoutside');
        }
    },
    /**
     * fill input edition with new text
     * @param {type} e
     * @param {string} unputtext
     */
    fill_input_edition: function (e, unputtext) {
        var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_editinput");
        if (input) {
            input.set('value', unputtext);
        }
        this.save_annot(unputtext);
    },
    save_annot_clickout: function (e, clickType) {
        if (!(clickType === 'clickoutside' && this.editor.currentannotation === this)) {
            this.save_annot(null);
        }
        return;
    },
    /**
     * save text annotation
     * @param {string} result
     */
    save_annot: function (result) {
        if (typeof result !== 'string') {
            var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_editinput");
            if (input) {
                result = input.get('value');
            }
        }
        this.textannot = result;
        this.editor.save_current_page();
        if (result.length === 0) {
            result = "&nbsp;&nbsp;";
        }
        this.hide_edit();
        this.apply_visibility_annot();
    },
    /**
     * save student answer
     */
    save_studentanswer: function () {
        var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_studentanswer");
        if (input) {
            this.studentanswer = input.get('value');
            this.editor.save_current_page_edited();
        }
        this.hide_edit();
        this.apply_visibility_annot();
    },
    /**
     * cancel annotation detail view
     * @param {type} e
     * @param {string} clickType
     */
    cancel_edit: function (e, clickType) {
        if (!(clickType === 'clickoutside' && this.editor.currentannotation === this)) {
            var valref = this.get_valref();
            var input = this.editor.get_dialogue_element('#' + this.divcartridge + "_editinput");
            if (valref && input) {
                input.set('value', valref);
            }
            this.hide_edit();
            this.apply_visibility_annot();
            var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
            if (divprincipale) {
                divprincipale.detach();
            }
        }
        return;
    },
    /**
     * remove annotation detail view
     * @param {type} e
     * @param {string} clickType
     */
    hide_edit: function (e, clickType) {
        if (!clickType || !(clickType === 'clickoutside' && this.editor.currentannotation === this)) {
            var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
            var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
            var divedit = this.editor.get_dialogue_element('#' + this.divcartridge + "_edit");
            var divvisu = this.editor.get_dialogue_element('#' + this.divcartridge + "_visu");
            var buttonsave = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonsave");
            var buttoncancel = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttoncancel");
            var buttonquestion = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonquestion");
            var buttonrotation = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonrotation");
            var buttonpdfdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpdfdisplay");
            var buttonremove = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonremove");
            var buttonstatus = this.editor.get_dialogue_element('#' + this.divcartridge + "_radioContainer");
            if (divdisplay) {
                divdisplay.show();
                divdisplay.set('style', 'display:inline;color:' + this.get_color_cartridge() + ';');
            }
            if (buttonrotation) {
                buttonrotation.show();
            }
            if (divedit) {
                divedit.hide();
                buttonsave.hide();
                buttoncancel.hide();
            }
            if (divvisu) {
                divvisu.hide();
            }
            if (buttonquestion) {
                buttonquestion.hide();
            }
            if (buttonpdfdisplay) {
                buttonpdfdisplay.hide();
            }
            if (buttonremove) {
                buttonremove.hide();
            }
            if (divprincipale) {
                divprincipale.setStyle('z-index', 1);
                divprincipale.detach();
                if (this.editor.get('readonly')) {
                    divprincipale.on('click', this.view_annot, this, 'click');
                }
            }
            if (divedit) {
                this.enabled_canvas_event();
            }
            if (buttonstatus) {
                buttonstatus.hide();
            }
        }
    },
    /**
     * remove annotation by clicking on a button
     * @param {type} e
     */
    remove_by_trash: function (e) {
        this.cancel_edit();
        this.remove(e);
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations,
                i;
        e.preventDefault();
        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y,
                newpath, oldpath, xy,
                x, y;
        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;
        if (this.path) {
            newpath = [];
            oldpath = this.path.split(':');
            Y.each(oldpath, function (position) {
                xy = position.split(',');
                x = parseInt(xy[0], 10);
                y = parseInt(xy[1], 10);
                newpath.push((x + diffx) + ',' + (y + diffy));
            });
            this.path = newpath.join(':');
        }
        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());

        //init resize area
        this.remove_resizearea();
        this.draw_resizeAreas();
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var noop = edit && false;
        // Override me please.
        return noop;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = bounds.y;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;
        this.colour = edit.annotationcolour;
        this.path = '';
        return (bounds.has_min_width() && bounds.has_min_height());
    },
    /**
     * Disable canvas event (click on other tool or annotation)
     */
    disabled_canvas_event: function () {
        var drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        drawingcanvas.detach();
    },
    /**
     * Enable canvas event (click on other tool or annotation)
     */
    enabled_canvas_event: function () {
        var drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        drawingcanvas.on('gesturemovestart', this.editor.edit_start, null, this.editor);
        drawingcanvas.on('gesturemove', this.editor.edit_move, null, this.editor);
        drawingcanvas.on('gesturemoveend', this.editor.edit_end, null, this.editor);
    }

});
M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotation = ANNOTATION;
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
/* global Y, M, STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a line.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationline
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONLINE = function(config) {
    ANNOTATIONLINE.superclass.constructor.apply(this, [config]);
};

ANNOTATIONLINE.NAME = "annotationline";
ANNOTATIONLINE.ATTRS = {};

Y.extend(ANNOTATIONLINE, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a line annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw : function() {
        var drawable,
            shape;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);

        shape = this.editor.graphic.addShape({
        type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: ANNOTATIONCOLOUR[this.colour]
            }
        });

        shape.moveTo(this.x, this.y);
        shape.lineTo(this.endx, this.endy);
        shape.end();
        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONLINE.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit : function(edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
            shape;

        shape = this.editor.graphic.addShape({
           type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: ANNOTATIONCOLOUR[edit.annotationcolour]
            }
        });

        shape.moveTo(edit.start.x, edit.start.y);
        shape.lineTo(edit.end.x, edit.end.y);
        shape.end();

        drawable.shapes.push(shape);

        return drawable;
    },

    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if line bound is more than min width/height, else false.
     */
    init_from_edit : function(edit) {
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = edit.start.x;
        this.y = edit.start.y;
        this.endx = edit.end.x;
        this.endy = edit.end.y;
        this.colour = edit.annotationcolour;
        this.path = '';

        return !(((this.endx - this.x) === 0) && ((this.endy - this.y) === 0));
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationline = ANNOTATIONLINE;
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
/* global Y, M, STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a rectangle.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationrectangle
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONRECTANGLE = function(config) {
    ANNOTATIONRECTANGLE.superclass.constructor.apply(this, [config]);
};

ANNOTATIONRECTANGLE.NAME = "annotationrectangle";
ANNOTATIONRECTANGLE.ATTRS = {};

Y.extend(ANNOTATIONRECTANGLE, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a rectangle annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw : function() {
        var drawable,
            bounds,
            shape;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
                      new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: {
               weight: STROKEWEIGHT,
               color: ANNOTATIONCOLOUR[this.colour]
            },
            x: bounds.x,
            y: bounds.y
        });
        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONRECTANGLE.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit : function(edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
            shape,
            bounds;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
                      new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width and height of rectangle.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }
        if (!bounds.has_min_height()) {
            bounds.set_min_height();
        }

        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: {
               weight: STROKEWEIGHT,
               color: ANNOTATIONCOLOUR[edit.annotationcolour]
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);

        return drawable;
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationrectangle = ANNOTATIONRECTANGLE;
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
/* global Y, M, STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a oval.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationoval
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONOVAL = function(config) {
    ANNOTATIONOVAL.superclass.constructor.apply(this, [config]);
};

ANNOTATIONOVAL.NAME = "annotationoval";
ANNOTATIONOVAL.ATTRS = {};

Y.extend(ANNOTATIONOVAL, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a oval annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw : function() {
        var drawable,
            bounds,
            shape;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
                      new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        shape = this.editor.graphic.addShape({
            type: Y.Ellipse,
            width: bounds.width,
            height: bounds.height,
            stroke: {
               weight: STROKEWEIGHT,
               color: ANNOTATIONCOLOUR[this.colour]
            },
            x: bounds.x,
            y: bounds.y
        });
        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONOVAL.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit : function(edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
            shape,
            bounds;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
                      new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width and height of oval.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }
        if (!bounds.has_min_height()) {
            bounds.set_min_height();
        }

        shape = this.editor.graphic.addShape({
            type: Y.Ellipse,
            width: bounds.width,
            height: bounds.height,
            stroke: {
               weight: STROKEWEIGHT,
               color: ANNOTATIONCOLOUR[edit.annotationcolour]
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);

        return drawable;
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationoval = ANNOTATIONOVAL;
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
/* global Y, M, STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a pen.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationpen
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONPEN = function(config) {
    ANNOTATIONPEN.superclass.constructor.apply(this, [config]);
};

ANNOTATIONPEN.NAME = "annotationpen";
ANNOTATIONPEN.ATTRS = {};

Y.extend(ANNOTATIONPEN, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a pen annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw : function() {
        var drawable,
            shape,
            first,
            positions,
            xy;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);

        shape = this.editor.graphic.addShape({
           type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: ANNOTATIONCOLOUR[this.colour]
            }
        });

        first = true;
        // Recreate the pen path array.
        positions = this.path.split(':');
        // Redraw all the lines.
        Y.each(positions, function(position) {
            xy = position.split(',');
            if (first) {
                shape.moveTo(xy[0], xy[1]);
                first = false;
            } else {
                shape.lineTo(xy[0], xy[1]);
            }
        }, this);

        shape.end();

        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONPEN.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit : function(edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
            shape,
            first;

        shape = this.editor.graphic.addShape({
           type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: ANNOTATIONCOLOUR[edit.annotationcolour]
            }
        });

        first = true;
        // Recreate the pen path array.
        // Redraw all the lines.
        Y.each(edit.path, function(position) {
            if (first) {
                shape.moveTo(position.x, position.y);
                first = false;
            } else {
                shape.lineTo(position.x, position.y);
            }
        }, this);

        shape.end();

        drawable.shapes.push(shape);

        return drawable;
    },


    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if pen bound is more than min width/height, else false.
     */
    init_from_edit : function(edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect(),
            pathlist = [],
            i = 0;

        // This will get the boundaries of all points in the path.
        bounds.bound(edit.path);

        for (i = 0; i < edit.path.length; i++) {
            pathlist.push(parseInt(edit.path[i].x, 10) + ',' + parseInt(edit.path[i].y, 10));
        }

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = bounds.y;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;
        this.colour = edit.annotationcolour;
        this.path = pathlist.join(':');

        return (bounds.has_min_width() || bounds.has_min_height());
    }


});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationpen = ANNOTATIONPEN;
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
/* global Y, M, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationhighlight
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONHIGHLIGHT = function (config) {
    ANNOTATIONHIGHLIGHT.superclass.constructor.apply(this, [config]);
};

ANNOTATIONHIGHLIGHT.NAME = "annotationhighlight";
ANNOTATIONHIGHLIGHT.ATTRS = {};

Y.extend(ANNOTATIONHIGHLIGHT, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                bounds,
                highlightcolour;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);
        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
            new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        highlightcolour = ANNOTATIONCOLOUR[this.colour];

        // Add an alpha channel to the rgb colour.

        highlightcolour = highlightcolour.replace('rgb', 'rgba');
        highlightcolour = highlightcolour.replace(')', ',0.5)');

        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: false,
            fill: {
                color: highlightcolour
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONHIGHLIGHT.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                highlightcolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = ANNOTATIONCOLOUR[edit.annotationcolour];
        // Add an alpha channel to the rgb colour.

        highlightcolour = highlightcolour.replace('rgb', 'rgba');
        highlightcolour = highlightcolour.replace(')', ',0.5)');

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 20,
            stroke: false,
            fill: {
                color: highlightcolour
            },
            x: bounds.x,
            y: edit.start.y - 10
        });

        drawable.shapes.push(shape);

        return drawable;
    },

    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 16;
        this.colour = edit.annotationcolour;
        this.page = '';

        return (bounds.has_min_width());
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationhighlight = ANNOTATIONHIGHLIGHT;
/* global M, Y, SELECTOR */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationhighlightplus
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONHIGHLIGHTPLUS = function (config) {
    ANNOTATIONHIGHLIGHTPLUS.superclass.constructor.apply(this, [config]);
};

ANNOTATIONHIGHLIGHTPLUS.NAME = "annotationhighlightplus";
ANNOTATIONHIGHLIGHTPLUS.ATTRS = {};

Y.extend(ANNOTATIONHIGHLIGHTPLUS, M.assignfeedback_editpdfplus.annotation, {

    /**
     * Margin to let for resize area
     * @type Number
     * @protected
     */
    marginDivResize: 4,
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                bounds,
                highlightcolour;

        highlightcolour = this.get_color();
        this.init_shape_id('hightlightplus');

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);
        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
            new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        shape = this.editor.graphic.addShape({
            id: this.shape_id,
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: false,
            fill: {
                color: highlightcolour,
                opacity: 0.5
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);
        this.drawable = drawable;

        this.draw_catridge();

        this.draw_resizeAreas();

        return ANNOTATIONHIGHLIGHTPLUS.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                highlightcolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = this.get_color();

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 16,
            stroke: false,
            fill: {
                color: highlightcolour,
                opacity: 0.5
            },
            x: bounds.x,
            y: edit.start.y - 8
        });

        drawable.shapes.push(shape);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y - 8;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 16 - 8;
        this.page = '';

        return (bounds.has_min_width());
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        var divdisplay;
        if (this.divcartridge === '') {
            this.init_div_cartridge_id();
            var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

            //init cartridge
            var colorcartridge = this.get_color_cartridge();
            divdisplay = this.get_div_cartridge(colorcartridge);
            divdisplay.addClass('assignfeedback_editpdfplus_hightlightplus');

            // inscription entete
            var divcartridge = this.get_div_cartridge_label(colorcartridge);
            divdisplay.append(divcartridge);

            //creation input
            var divconteneurdisplay = this.get_div_container(colorcartridge);
            var toolbar = this.get_toolbar();
            divconteneurdisplay.append(toolbar);
            divdisplay.append(divconteneurdisplay);

            //creation de la div d'edition
            if (!this.editor.get('readonly')) {
                var diveditiondisplay = this.get_div_edition();
                divconteneurdisplay.append(diveditiondisplay);
            } else {
                var divvisudisplay = this.get_div_visu(colorcartridge);
                divconteneurdisplay.append(divvisudisplay);
            }

            //positionnement de la div par rapport a l'annotation
            if (!this.cartridgex || this.cartridgex === 0) {
                this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
            }
            if (!this.cartridgey || this.cartridgey === 0) {
                this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
            }
            divdisplay.setX(this.x + this.cartridgex);
            divdisplay.setY(this.y + this.cartridgey);
            drawingregion.append(divdisplay);

            this.apply_visibility_annot();
        } else {
            this.replacement_cartridge();
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.x + this.cartridgex);
            divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
        }
    },
    /**
     * Draw empty resize area on left and right
     */
    draw_resizeAreas: function () {
        this.push_div_resizearea('left', this.x - this.marginDivResize, this.y);
        this.push_div_resizearea('right', this.endx - this.marginDivResize, this.y);
    },
    /**
     * Actions when resizing a shape:
     * - on left, new x and width
     * - on right, new width
     * New placement of resize area (div)
     * @param {Event} e
     * @param {Point} point current position
     * @param {div} divresize id of resize area
     */
    mousemoveResize: function (e, point, divresize) {
        if (this.drawable.shapes.length === 0) {
            return;
        }
        var shape = this.drawable.shapes[0];
        if (!shape) {
            return;
        }
        var direction = divresize.getData('direction');
        var width = this.minresizewidth;
        var canvasDim = this.editor.get_canvas_bounds();
        var newpointx = point.x;
        //sortie de cadre
        if (newpointx < 0) {
            newpointx = 0;
        } else if (canvasDim.width < newpointx) {
            newpointx = canvasDim.width;
        }
        var decalage = canvasDim.x;
        if (direction === 'right') {
            width = Math.max(newpointx - this.x, this.minresizewidth);
            shape.set('width', width);
            divresize.setX(this.x + width + decalage - this.marginDivResize);
        } else if (direction === 'left') {
            width = Math.max(this.endx - newpointx, this.minresizewidth);
            shape.set('x', Math.min(newpointx, this.endx - this.minresizewidth));
            shape.set('width', width);
            divresize.setX(this.endx - width + decalage - this.marginDivResize);
        }
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations,
                i;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                if (this.divcartridge !== '') {
                    var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
                    divdisplay.remove();
                }
                this.remove_resizearea();
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationhighlightplus = ANNOTATIONHIGHLIGHTPLUS;
/* global M, Y, SELECTOR */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a stamp.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationstampplus
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONSTAMPPLUS = function (config) {
    ANNOTATIONSTAMPPLUS.superclass.constructor.apply(this, [config]);
};

ANNOTATIONSTAMPPLUS.NAME = "annotationstampplus";
ANNOTATIONSTAMPPLUS.ATTRS = {};

Y.extend(ANNOTATIONSTAMPPLUS, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a stamp annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                node,
                position;

        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(this.x, this.y));
        node = Y.Node.create('<div>' + this.tooltype.label + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'color': this.colour,
            'border': '2px solid ' + this.colour,
            'padding': '0 2px'
        });

        drawingcanvas.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);
        drawable.nodes.push(node);

        this.drawable = drawable;
        return ANNOTATIONSTAMPPLUS.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect(),
                drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
                node,
                position;

        bounds.bound([edit.start, edit.end]);
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(bounds.x, bounds.y));

        node = Y.Node.create('<div>' + this.tooltype.label + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'color': this.colour,
            'border': '2px solid ' + this.colour,
            'padding': '0 2px'
        });

        drawingregion.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);

        drawable.nodes.push(node);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        if (bounds.width < 40) {
            bounds.width = 40;
        }
        if (bounds.height < 40) {
            bounds.height = 40;
        }
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x - 5;
        this.y = bounds.y - 10;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;

        // Min width and height is always more than 40px.
        return true;
    },
    /**
     * display annotation edditing view
     * No edit annot for this annotation
     */
    edit_annot: function () {
        return true;
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationstampplus = ANNOTATIONSTAMPPLUS;
/* global Y, M, SELECTOR */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a stampcomment.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationstampcomment
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONSTAMPCOMMENT = function (config) {
    ANNOTATIONSTAMPCOMMENT.superclass.constructor.apply(this, [config]);
};

ANNOTATIONSTAMPCOMMENT.NAME = "annotationstampcomment";
ANNOTATIONSTAMPCOMMENT.ATTRS = {};

Y.extend(ANNOTATIONSTAMPCOMMENT, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a stampcomment annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                node,
                position;

        this.shape_id = 'ct_stampcomment_' + (new Date().toJSON()).replace(/:/g, '').replace(/\./g, '');
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(this.x, this.y));
        var colorcartridge = this.get_color_cartridge();
        var fleche = '<i id="'
                + this.shape_id
                + '_img" '
                + 'class="fa fa-arrows-h fa-2x" aria-hidden="true" style="color:'
                + colorcartridge
                + ';"></i>';
        if (this.displayrotation > 0) {
            fleche = '<i id="' + this.shape_id + '_img" '
                    + 'class="fa fa-arrows-v fa-2x" aria-hidden="true" style="color:'
                    + colorcartridge
                    + ';"></i>';
        }
        node = Y.Node.create('<div id="' + this.shape_id + '">' + fleche + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block'
        });

        drawingcanvas.append(node);
        node.setY(position.y);
        node.setX(position.x);
        drawable.store_position(node, position.x, position.y);
        drawable.nodes.push(node);

        this.drawable = drawable;

        this.draw_catridge();
        return ANNOTATIONSTAMPCOMMENT.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect(),
                drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
                node,
                position;

        bounds.bound([edit.start, edit.end]);
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(bounds.x, bounds.y));

        var colorcartridge = this.get_color_cartridge();
        var nodeContent = '<div><i class="fa fa-arrows-v fa-2x" aria-hidden="true"  style="color:'
                + colorcartridge
                + '"></i></div>';
        node = Y.Node.create(nodeContent);
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block'
        });

        drawingregion.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);

        drawable.nodes.push(node);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        if (bounds.width < 30) {
            bounds.width = 30;
        }
        if (bounds.height < 30) {
            bounds.height = 30;
        }
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x - 20;
        this.y = bounds.y - 25;
        this.endx = bounds.x + bounds.width - 20;
        this.endy = bounds.y + bounds.height - 25;

        // Min width and height is always more than 40px.
        return true;
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        var divdisplay;
        if (this.divcartridge === '') {
            this.init_div_cartridge_id();
            var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

            //init cartridge
            var colorcartridge = this.get_color_cartridge();
            divdisplay = this.get_div_cartridge(colorcartridge);
            divdisplay.addClass('assignfeedback_editpdfplus_stampcomment');

            // inscription entete
            var divcartridge = this.get_div_cartridge_label(colorcartridge, true);
            divdisplay.append(divcartridge);

            //creation input
            var divconteneurdisplay = this.get_div_container(colorcartridge);
            var toolbar = this.get_toolbar();
            if (!this.editor.get('readonly')) {
                var rotationvalue = 0;
                if (this.displayrotation > 0) {
                    rotationvalue = 1;
                }
                var inputrotationdisplay = Y.Node.create("<input type='hidden' id='"
                        + this.divcartridge
                        + "_rotation' value="
                        + rotationvalue
                        + " />");
                toolbar.append(inputrotationdisplay);
                var buttonrotation = "<button id='"
                        + this.divcartridge
                        + "_buttonrotation' class='btn btn-sm btn-outline-dark' type='button'>"
                        + '<i class="fa fa-refresh" aria-hidden="true"></i>'
                        + "</button>";
                var buttonrotationdisplay = Y.Node.create(buttonrotation);
                buttonrotationdisplay.on('click', this.change_stamp, this);
                toolbar.append(buttonrotationdisplay);
            }
            divconteneurdisplay.append(toolbar);
            divdisplay.append(divconteneurdisplay);

            //creation de la div d'edition
            if (!this.editor.get('readonly')) {
                var diveditiondisplay = this.get_div_edition();
                divconteneurdisplay.append(diveditiondisplay);
            } else {
                var divvisudisplay = this.get_div_visu(colorcartridge);
                divconteneurdisplay.append(divvisudisplay);
            }

            //positionnement de la div par rapport a l'annotation
            if (!this.cartridgex || this.cartridgex === 0) {
                this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
            }
            if (!this.cartridgey || this.cartridgey === 0) {
                this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
            }
            divdisplay.setX(this.x + this.cartridgex);
            divdisplay.setY(this.y + this.cartridgey);
            drawingregion.append(divdisplay);

            this.apply_visibility_annot();
        } else {
            this.replacement_cartridge();
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.x + this.cartridgex);
            divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
        }
    },
    change_stamp: function () {
        var rotationstate = this.editor.get_dialogue_element('#' + this.divcartridge + "_rotation");
        var img = this.editor.get_dialogue_element('#' + this.shape_id + "_img");
        if (rotationstate.get('value') === '0') {
            this.displayrotation = 1;
            rotationstate.set('value', 1);
            img.removeClass("fa-arrows-h");
            img.addClass("fa-arrows-v");
        } else {
            rotationstate.set('value', 0);
            img.removeClass("fa-arrows-v");
            img.addClass("fa-arrows-h");
            this.displayrotation = 0;
        }
        this.editor.save_current_page();
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param {event} e
     */
    remove: function (e) {
        var annotations,
                i;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                if (this.divcartridge !== '') {
                    var divid = '#' + this.divcartridge;
                    var divdisplay = this.editor.get_dialogue_element(divid);
                    divdisplay.remove();
                }
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationstampcomment = ANNOTATIONSTAMPCOMMENT;
/* global M, Y */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationframe
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONFRAME = function (config) {
    ANNOTATIONFRAME.superclass.constructor.apply(this, [config]);
};

ANNOTATIONFRAME.NAME = "annotationframe";
ANNOTATIONFRAME.ATTRS = {};

Y.extend(ANNOTATIONFRAME, M.assignfeedback_editpdfplus.annotation, {
    children: [],
    oldx: 0,
    oldy: 0,
    /**
     * Margin to let for resize area
     * @type Number
     * @protected
     */
    marginDivResize: 4,
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                bounds,
                highlightcolour;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);
        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
            new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        highlightcolour = this.get_color();
        this.init_shape_id('frame');

        shape = this.editor.graphic.addShape({
            id: this.shape_id,
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: {
                weight: 2,
                color: this.get_color()
            },
            x: bounds.x,
            y: bounds.y
        });
        if (this.parent_annot_element) {
            shape.addClass('class_' + this.parent_annot_element.divcartridge);
        }
        if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else if (this.borderstyle === 'dotted') {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }

        drawable.shapes.push(shape);
        this.drawable = drawable;

        this.draw_catridge();

        this.draw_resizeAreas();

        return ANNOTATIONFRAME.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                highlightcolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = this.get_color();

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 16,
            stroke: {
                weight: 2,
                color: this.get_color()
            },
            x: bounds.x,
            y: edit.start.y - 8
        });
        if (this.parent_annot_element) {
            shape.addClass('class_' + this.parent_annot_element.divcartridge);
        }
        if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else if (this.borderstyle === 'dotted') {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }

        drawable.shapes.push(shape);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y - 8;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 16 - 8;
        this.page = '';

        return (bounds.has_min_width());
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());

        //init resize area
        this.remove_resizearea();
        this.draw_resizeAreas();
    },
    /**
     * Get the color of the element, depend of data on DB
     * @return {string} color
     */
    get_color: function () {
        return this.colour;
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        if (this.parent_annot_element === null && this.parent_annot === 0) {
            var divdisplay;
            if (this.divcartridge === '') {
                this.init_div_cartridge_id();
                var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

                //rattachement de la shape
                var shapechd = this.editor.graphic.getShapeById(this.shape_id);
                if (shapechd) {
                    shapechd.addClass('class_' + this.divcartridge);
                }

                //init cartridge
                var colorcartridge = this.get_color();
                divdisplay = this.get_div_cartridge(colorcartridge);
                divdisplay.addClass('assignfeedback_editpdfplus_frame');
                divdisplay.setStyles({'border-style': this.borderstyle});
                //divdisplay.set('draggable', 'true');

                // inscription entete
                var divcartridge = this.get_div_cartridge_label(colorcartridge, true);
                divdisplay.append(divcartridge);

                //creation input
                var divconteneurdisplay = this.get_div_container(colorcartridge);
                var toolbar = this.get_toolbar();
                if (!this.editor.get('readonly')) {
                    var buttonrender = "<button id='"
                            + this.divcartridge
                            + "_buttonpencil' class='btn btn-sm btn-outline-dark' type='button'>";
                    buttonrender += '<i class="fa fa-eyedropper" aria-hidden="true"></i>';
                    buttonrender += "</button>";
                    var buttonrenderdisplay = Y.Node.create(buttonrender);
                    buttonrenderdisplay.on('click', this.display_picker, this);
                    var buttonadd = "<button id='"
                            + this.divcartridge
                            + "_buttonadd' class='btn btn-sm btn-outline-dark' type='button'>";
                    buttonadd += '<i class="fa fa-plus" aria-hidden="true"></i>';
                    buttonadd += "</button>";
                    var buttonadddisplay = Y.Node.create(buttonadd);
                    buttonadddisplay.on('click', this.add_annot, this);
                    toolbar.append(buttonrenderdisplay);
                    toolbar.append(buttonadddisplay);
                }
                divconteneurdisplay.append(toolbar);
                divdisplay.append(divconteneurdisplay);

                //creation de la div d'edition
                if (!this.editor.get('readonly')) {
                    var diveditiondisplay = this.get_div_edition();
                    divconteneurdisplay.append(diveditiondisplay);
                } else {
                    var divvisudisplay = this.get_div_visu(colorcartridge);
                    divconteneurdisplay.append(divvisudisplay);
                }

                //creation de la div palette
                if (!this.editor.get('readonly')) {
                    var styleEditionHtml = "margin:5px;border:2px #ccc ";
                    var styleEditionMinHtml = "min-width:20px;min-height:20px;";

                    var diveditionrender = "<div ";
                    diveditionrender += "id='" + this.divcartridge + "_picker' ";
                    diveditionrender += "class='assignfeedback_editpdfplus_frame_picker' ";
                    diveditionrender += "style='display:none;text-align:right;'> ";
                    diveditionrender += "</div>";
                    var diveditionrenderdisplay = Y.Node.create(diveditionrender);
                    divconteneurdisplay.append(diveditionrenderdisplay);
                    var diveditioncolordisplay = Y.Node.create("<div style='display:inline-block;vertical-align:top;'></div>");
                    var diveditionframedisplay = Y.Node.create("<div style='display:inline-block;vertical-align:top;'></div>");
                    diveditionrenderdisplay.append(diveditioncolordisplay);
                    diveditionrenderdisplay.append(diveditionframedisplay);
                    var diveditionwhitedisplay = Y.Node.create("<div style='background-color:white;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionwhitedisplay.on('click', this.change_color, this, 'white');
                    var diveditionyellowdisplay = Y.Node.create("<div style='background-color:#E69F00;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionyellowdisplay.on('click', this.change_color, this, '#E69F00');//orange
                    var diveditionreddisplay = Y.Node.create("<div style='background-color:#D55E00;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionreddisplay.on('click', this.change_color, this, '#D55E00');//red
                    var diveditiongreendisplay = Y.Node.create("<div style='background-color:#009E73;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditiongreendisplay.on('click', this.change_color, this, '#009E73');//green
                    var diveditionbluedisplay = Y.Node.create("<div style='background-color:#0072B2;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionbluedisplay.on('click', this.change_color, this, '#0072B2');//blue
                    var diveditionblackdisplay = Y.Node.create("<div style='background-color:black;"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditionblackdisplay.on('click', this.change_color, this, 'black');
                    diveditioncolordisplay.append(diveditionwhitedisplay);
                    diveditioncolordisplay.append(diveditionyellowdisplay);
                    diveditioncolordisplay.append(diveditionreddisplay);
                    diveditioncolordisplay.append(diveditiongreendisplay);
                    diveditioncolordisplay.append(diveditionbluedisplay);
                    diveditioncolordisplay.append(diveditionblackdisplay);
                    var diveditsoliddisplay = Y.Node.create("<div style='"
                            + styleEditionHtml
                            + "solid;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditsoliddisplay.on('click', this.change_border, this, 'solid');
                    var diveditdotteddisplay = Y.Node.create("<div style='"
                            + styleEditionHtml
                            + "dotted;"
                            + styleEditionMinHtml
                            + "'></div>");
                    diveditdotteddisplay.on('click', this.change_border, this, 'dotted');
                    var diveditdasheddisplayhtml = "<div style='"
                            + styleEditionHtml
                            + "dashed;"
                            + styleEditionMinHtml + "'>"
                            + "</div>";
                    var diveditiondasheddisplay = Y.Node.create(diveditdasheddisplayhtml);
                    diveditiondasheddisplay.on('click', this.change_border, this, 'dashed');
                    diveditionframedisplay.append(diveditsoliddisplay);
                    diveditionframedisplay.append(diveditdotteddisplay);
                    diveditionframedisplay.append(diveditiondasheddisplay);
                }

                //positionnement de la div par rapport a l'annotation
                if (!this.cartridgex || this.cartridgex === 0) {
                    this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
                }
                if (!this.cartridgey || this.cartridgey === 0) {
                    this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
                }
                divdisplay.setX(this.cartridgex);
                divdisplay.setY(this.y + this.cartridgey);
                drawingregion.append(divdisplay);

                this.apply_visibility_annot();
                if (!this.editor.get('readonly')) {
                    var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
                    var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
                    buttonplusr.hide();
                    buttonplusl.hide();
                }

            } else {
                this.replacement_cartridge();
            }
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divid = '#' + this.divcartridge;
        var divdisplay = this.editor.get_dialogue_element(divid);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.cartridgex);
            divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
        }
    },
    /**
     * Draw empty resize area on left and right
     */
    draw_resizeAreas: function () {
        this.push_div_resizearea('left', this.x - this.marginDivResize, this.y);
        this.push_div_resizearea('right', this.endx - this.marginDivResize, this.y);
    },
    /**
     * Actions when resizing a shape:
     * - on left, new x and width
     * - on right, new width
     * New placement of resize area (div)
     * @param {Event} e
     * @param {Point} point current position
     * @param {div} divresize id of resize area
     */
    mousemoveResize: function (e, point, divresize) {
        if (this.drawable.shapes.length === 0) {
            return;
        }
        var shape = this.drawable.shapes[0];
        if (!shape) {
            return;
        }
        var direction = divresize.getData('direction');
        var width = this.minresizewidth;
        var canvasDim = this.editor.get_canvas_bounds();
        var newpointx = point.x;
        //sortie de cadre
        if (newpointx < 0) {
            newpointx = 0;
        } else if (canvasDim.width < newpointx) {
            newpointx = canvasDim.width;
        }
        var decalage = canvasDim.x;
        if (direction === 'right') {
            width = Math.max(newpointx - this.x, this.minresizewidth);
            shape.set('width', width);
            divresize.setX(this.x + width + decalage - this.marginDivResize);
        } else if (direction === 'left') {
            width = Math.max(this.endx - point.x, this.minresizewidth);
            shape.set('x', Math.min(newpointx, this.endx - this.minresizewidth));
            shape.set('width', width);
            divresize.setX(this.endx - width + decalage - this.marginDivResize);
        }
    },
    /**
     * drag-and-drop process
     * @param {type} e
     */
    move_cartridge_continue: function (e) {
        e.preventDefault();

        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                        e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();

        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;

        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.cartridgex + diffx);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey + diffy);
    },
    /**
     * drag-and-drop stop
     * @param {type} e
     */
    move_cartridge_stop: function (e) {
        e.preventDefault();
        var canvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
        canvas.detach('mousemove', this.move_cartridge_continue, this);
        canvas.detach('mouseup', this.move_cartridge_stop, this);

        var clientpoint = new M.assignfeedback_editpdfplus.point(e.clientX + canvas.get('docScrollX'),
                e.clientY + canvas.get('docScrollY')),
                point = this.editor.get_canvas_coordinates(clientpoint);
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();

        var diffx = point.x - this.oldx;
        var diffy = point.y - this.oldy;

        this.cartridgex += diffx;
        this.cartridgey += diffy;

        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divcartridge.setX(offsetcanvas[0] + this.cartridgex);
        divcartridge.setY(offsetcanvas[1] + this.y + this.cartridgey);

        this.editor.save_current_page();
    },
    /**
     * Add child annotation (new associed frame)
     * @param {type} e
     */
    add_annot: function (e) {
        this.editor.currentedit.parent_annot_element = this;
        this.editor.handle_tool_button(e, TOOLTYPELIB.FRAME, 'ctbutton' + this.toolid, 1);
    },
    /**
     * Display color/border picker toolbar
     */
    display_picker: function () {
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        var buttonrenderdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        divcartridge.setStyle('z-index', 1000);
        divpalette.show();
        buttonrenderdisplay.on('click', this.hide_picker, this);
    },
    /**
     * Hide color/border picker toolbar
     */
    hide_picker: function () {
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        var buttonrenderdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge);
        divpalette.hide();
        divcartridge.setStyle('z-index', 0);
        buttonrenderdisplay.on('click', this.display_picker, this);
    },
    /**
     * Apply "change color" on element and children
     * @param {type} e
     * @param {string} colour
     */
    change_color: function (e, colour) {
        this.colour = colour;
        var shape = this.editor.graphic.getShapeById(this.shape_id);
        shape.set("stroke", {
            color: this.colour
        });
        var shapesChildren = null;
        if (this.id) {
            shapesChildren = this.editor.annotationsparent[this.id];
        } else {
            shapesChildren = this.editor.annotationsparent[this.divcartridge];
        }
        if (shapesChildren) {
            for (var i = 0; i < shapesChildren.length; i++) {
                shapesChildren[i].colour = colour;
                var shapechd = this.editor.graphic.getShapeById(shapesChildren[i].shape_id);
                if (shapechd) {
                    shapechd.set("stroke", {
                        color: this.colour
                    });
                }
            }
        }
        var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
        divprincipale.setStyles({
            'border-color': this.colour,
            'color': this.colour
        });
        var divcartridge = this.editor.get_dialogue_element('#' + this.divcartridge + "_cartridge");
        divcartridge.setStyles({
            'border-color': this.colour,
            'color': this.colour
        });
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        divdisplay.setStyles({
            'color': this.colour
        });
        this.hide_picker();
        this.editor.save_current_page();
    },
    /**
     * Apply "change border" on element and children
     * @param {type} e
     * @param {string} colour
     */
    change_border: function (e, border) {
        this.borderstyle = border;
        var shape = this.editor.graphic.getShapeById(this.shape_id);
        if (this.borderstyle === 'solid') {
            shape.set("stroke", {
                dashstyle: 'none'
            });
        } else if (this.borderstyle === 'dashed') {
            shape.set("stroke", {
                dashstyle: [5, 3]
            });
        } else {
            shape.set("stroke", {
                dashstyle: [2, 2]
            });
        }
        var shapesChildren = [];
        if (this.id) {
            shapesChildren = this.editor.annotationsparent[this.id];
        } else {
            shapesChildren = this.editor.annotationsparent[this.divcartridge];
        }
        if (shapesChildren) {
            for (var i = 0; i < shapesChildren.length; i++) {
                shapesChildren[i].borderstyle = border;
                var shapechd = this.editor.graphic.getShapeById(shapesChildren[i].shape_id);
                if (shapechd) {
                    if (this.borderstyle === 'solid') {
                        shapechd.set("stroke", {
                            dashstyle: 'none'
                        });
                    } else if (this.borderstyle === 'dashed') {
                        shapechd.set("stroke", {
                            dashstyle: [5, 3]
                        });
                    } else {
                        shapechd.set("stroke", {
                            dashstyle: [2, 2]
                        });
                    }
                }
            }
        }
        var divprincipale = this.editor.get_dialogue_element('#' + this.divcartridge);
        divprincipale.setStyles({
            'border-style': this.borderstyle
        });
        var divpalette = this.editor.get_dialogue_element('#' + this.divcartridge + "_picker");
        divpalette.hide();
        this.editor.save_current_page();
    },
    /**
     * display annotation edditing view
     */
    edit_annot: function () {
        if (!this.parent_annot_element) {
            var buttonrender = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
            var buttonadd = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonadd");
            this.hide_picker();
            if (buttonrender) {
                buttonrender.hide();
                buttonadd.hide();
            }
            ANNOTATIONFRAME.superclass.edit_annot.call(this);
        }
    },
    /**
     * remove annotation detail view
     * @param {type} e
     * @param {string} clickType
     */
    hide_edit: function () {
        ANNOTATIONFRAME.superclass.hide_edit.call(this);
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        var buttonrender = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonpencil");
        var buttonadd = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonadd");
        var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
        var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
        if (divdisplay) {
            divdisplay.set('style', 'display:inline;color:' + this.get_color() + ';');
            if (buttonrender) {
                buttonrender.show();
                buttonadd.show();
            }
            if (buttonplusr) {
                buttonplusr.hide();
            }
            if (buttonplusl) {
                buttonplusl.hide();
            }
        }
    },
    /**
     * Delete an annotation and its children
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (var k = 0; k < annotations.length; k++) {
            if (annotations[k] === this) {
                if (this.divcartridge !== '') {
                    var divid = '#' + this.divcartridge;
                    var divdisplay = this.editor.get_dialogue_element(divid);
                    divdisplay.remove();
                }
                this.remove_resizearea();
                annotations.splice(k, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }

                var shapesChildren = [];
                if (this.id) {
                    shapesChildren = this.editor.annotationsparent[this.id];
                } else {
                    shapesChildren = this.editor.annotationsparent[this.divcartridge];
                }
                if (shapesChildren) {
                    for (var i = 0; i < shapesChildren.length; i++) {
                        for (var j = 0; j < annotations.length; j++) {
                            if (annotations[j] === shapesChildren[i]) {
                                annotations.splice(j, 1);
                                if (shapesChildren[i].drawable) {
                                    shapesChildren[i].drawable.erase();
                                }
                            }
                        }
                    }
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});
M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationframe = ANNOTATIONFRAME;
/* global M, Y, STROKEWEIGHT, SELECTOR */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a verticalline.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationverticalline
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONVERTICALLINE = function (config) {
    ANNOTATIONVERTICALLINE.superclass.constructor.apply(this, [config]);
};

ANNOTATIONVERTICALLINE.NAME = "annotationverticalline";
ANNOTATIONVERTICALLINE.ATTRS = {};

Y.extend(ANNOTATIONVERTICALLINE, M.assignfeedback_editpdfplus.annotation, {

    /**
     * Margin to let for resize area on top and down
     * @type Number
     * @protected
     */
    marginyDivResize: 2,
    /**
     * Margin to let for resize area on left and right
     * @type Number
     * @protected
     */
    marginxDivResize: 7,
    /**
     * Min width for resize area
     * @type Number
     * @protected
     */
    minWidthDivResize: 15,
    /**
     * Draw a verticalline annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                verticallinecolour;

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);

        verticallinecolour = this.get_color();
        this.init_shape_id('verticalline');

        shape = this.editor.graphic.addShape({
            id: this.shape_id,
            type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: verticallinecolour
            }
        });

        shape.moveTo(this.x, this.y);
        if (this.endy - this.y <= 30) {
            this.endy = this.y + 30;
        }
        shape.lineTo(this.x, this.endy);
        shape.end();

        drawable.shapes.push(shape);
        this.drawable = drawable;

        this.draw_catridge();

        this.draw_resizeAreas();

        return ANNOTATIONVERTICALLINE.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                verticallinecolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of verticalline.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }
        if (!bounds.has_min_height()) {
            bounds.set_min_height();
        }

        verticallinecolour = this.get_color();

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Path,
            fill: false,
            stroke: {
                weight: STROKEWEIGHT,
                color: verticallinecolour
            }
        });

        shape.moveTo(edit.start.x, edit.start.y);
        if (edit.end.y - edit.start.y <= 30) {
            shape.lineTo(edit.start.x, edit.start.y + 30);
        } else {
            shape.lineTo(edit.start.x, edit.end.y);
        }
        shape.end();

        drawable.shapes.push(shape);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if verticalline bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = edit.start.x;
        this.y = edit.start.y;
        this.endx = edit.end.x + 4;
        if (edit.end.y - this.y <= 30) {
            this.endy = this.y + 30;
        } else {
            this.endy = edit.end.y;
        }
        this.page = '';
        return !(((this.endx - this.x) === 0) && ((this.endy - this.y) === 0));
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        var divdisplay;
        if (this.divcartridge === '') {
            this.init_div_cartridge_id();
            var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

            //init cartridge
            var colorcartridge = this.get_color_cartridge();
            divdisplay = this.get_div_cartridge(colorcartridge);
            divdisplay.addClass('assignfeedback_editpdfplus_verticalline');

            // inscription entete
            var divcartridge = this.get_div_cartridge_label(colorcartridge, true);
            divdisplay.append(divcartridge);

            //creation input
            var divconteneurdisplay = this.get_div_container(colorcartridge);
            var toolbar = this.get_toolbar();
            divconteneurdisplay.append(toolbar);
            divdisplay.append(divconteneurdisplay);

            //creation de la div d'edition
            if (!this.editor.get('readonly')) {
                var diveditiondisplay = this.get_div_edition();
                divconteneurdisplay.append(diveditiondisplay);
            } else {
                var divvisudisplay = this.get_div_visu(colorcartridge);
                divconteneurdisplay.append(divvisudisplay);
            }

            //positionnement de la div par rapport a l'annotation
            if (!this.cartridgex || this.cartridgex === 0) {
                this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
            }
            if (!this.cartridgey || this.cartridgey === 0) {
                this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
            }
            divdisplay.setX(this.x + this.cartridgex);
            divdisplay.setY(this.y + this.cartridgey);
            drawingregion.append(divdisplay);

            this.apply_visibility_annot();
        } else {
            this.replacement_cartridge();
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.x + this.cartridgex);
            divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
        }
    },
    /**
     * Draw empty resize area on top and down
     */
    draw_resizeAreas: function () {
        this.push_div_resizearea('up', this.x - this.marginxDivResize, this.y - this.marginyDivResize, this.minWidthDivResize);
        this.push_div_resizearea('down', this.x - this.marginxDivResize, this.endy - this.marginyDivResize, this.minWidthDivResize);
    },
    /**
     * Actions when resizing a shape:
     * - on top, new height
     * - on down, new y and nw height
     * New placement of resize area (div)
     * @param {Event} e
     * @param {Point} point current position
     * @param {div} divresize id of resize area
     */
    mousemoveResize: function (e, point, divresize) {
        if (this.drawable.shapes.length === 0) {
            return;
        }
        var shape = this.drawable.shapes[0];
        if (!shape) {
            return;
        }
        var height = this.minresizewidth;
        var direction = divresize.getData('direction');
        var canvasDim = this.editor.get_canvas_bounds();
        var newpointy = point.y;
        //sortie de cadre
        if (newpointy < 0) {
            newpointy = 0;
        } else if (canvasDim.height < newpointy) {
            newpointy = canvasDim.height;
        }
        var decalage = canvasDim.y;
        if (direction === 'up') {
            height = Math.max(this.endy - newpointy, this.minresizewidth);
            shape.clear();
            shape.moveTo(this.x, Math.min(newpointy, this.endy - this.minresizewidth));
            shape.lineTo(this.x, this.endy);
            shape.end();
            divresize.setY(this.endy - height + decalage - this.marginyDivResize);
        } else if (direction === 'down') {
            height = Math.max(newpointy - this.y, this.minresizewidth);
            shape.clear();
            shape.moveTo(this.x, this.y);
            shape.lineTo(this.x, this.y + height);
            shape.end();
            divresize.setY(this.y + height + decalage - this.marginyDivResize);
        }
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations,
                i;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                if (this.divcartridge !== '') {
                    var divid = '#' + this.divcartridge;
                    var divdisplay = this.editor.get_dialogue_element(divid);
                    divdisplay.remove();
                }
                this.remove_resizearea();
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationverticalline = ANNOTATIONVERTICALLINE;
/* global M, Y, SELECTOR */

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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a comment.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationcommentplus
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONCOMMENTPLUS = function (config) {
    ANNOTATIONCOMMENTPLUS.superclass.constructor.apply(this, [config]);
};

ANNOTATIONCOMMENTPLUS.NAME = "annotationcommentplus";
ANNOTATIONCOMMENTPLUS.ATTRS = {};

Y.extend(ANNOTATIONCOMMENTPLUS, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a comment annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                node,
                position;

        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(this.x, this.y));
        var colorcartridge = this.get_color_cartridge();
        node = Y.Node.create('<div><i class="fa fa-commenting" aria-hidden="true" style="color:'
                + colorcartridge
                + ';"></i></div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'zIndex': 50,
            'color': this.colour,
            'padding': '0 2px'
        });

        drawingcanvas.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);
        drawable.nodes.push(node);

        this.drawable = drawable;

        this.draw_catridge();

        return ANNOTATIONCOMMENTPLUS.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect(),
                drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
                node,
                position;

        bounds.bound([edit.start, edit.end]);
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(bounds.x, bounds.y));

        node = Y.Node.create('<div>' + this.tooltype.label + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'zIndex': 50,
            'color': this.colour,
            'padding': '0 2px'
        });

        drawingregion.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);

        drawable.nodes.push(node);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        if (bounds.width < 20) {
            bounds.width = 20;
        }
        if (bounds.height < 20) {
            bounds.height = 20;
        }
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x - 20;
        this.y = bounds.y - 10;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;

        // Min width and height is always more than 40px.
        return true;
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        var divdisplay;
        if (this.divcartridge === '') {
            this.init_div_cartridge_id();
            var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

            //init cartridge
            var colorcartridge = this.get_color_cartridge();
            divdisplay = this.get_div_cartridge(colorcartridge);
            divdisplay.addClass('assignfeedback_editpdfplus_commentplus');

            // inscription entete
            var divcartridge = this.get_div_cartridge_label(colorcartridge);
            divdisplay.append(divcartridge);

            //creation input
            var divconteneurdisplay = this.get_div_container(colorcartridge);
            var toolbar = this.get_toolbar();
            divconteneurdisplay.append(toolbar);
            divdisplay.append(divconteneurdisplay);

            //creation de la div d'edition
            if (!this.editor.get('readonly')) {
                var diveditiondisplay = this.get_div_edition();
                divconteneurdisplay.append(diveditiondisplay);
            } else {
                var divvisudisplay = this.get_div_visu(colorcartridge);
                divconteneurdisplay.append(divvisudisplay);
            }

            divdisplay.setX(this.x + 20);
            divdisplay.setY(this.y);
            drawingregion.append(divdisplay);

            this.apply_visibility_annot();
        } else {
            this.replacement_cartridge();
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.x + 20);
            divdisplay.setY(offsetcanvas[1] + this.y);
        }
    },
    /**
     * Display the annotation according to current parameters
     */
    apply_visibility_annot: function () {
        ANNOTATIONCOMMENTPLUS.superclass.apply_visibility_annot.apply(this);

        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge + "_display");
        var interrupt = this.editor.get_dialogue_element('#' + this.divcartridge + "_onof");
        var buttonplusr = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_right");
        var buttonplusl = this.editor.get_dialogue_element('#' + this.divcartridge + "_buttonedit_left");
        if (buttonplusr) {
            buttonplusr.setHTML('<i class="fa fa-arrow-down" aria-hidden="true"></i>');
            buttonplusl.setHTML('<i class="fa fa-arrow-up" aria-hidden="true"></i>');
            if (interrupt.get('value') === '2') {
                divdisplay.setContent('<table><tr><td>'
                        + this.get_text_to_diplay_in_cartridge().replace(/\n/g, "<br/>")
                        + '</td></tr></table><br/>');
            } else if (interrupt.get('value') === '1') {
                buttonplusl.setHTML('<i class="fa fa-arrow-left" aria-hidden="true"></i>');
            } else if (interrupt.get('value') === '0') {
                buttonplusr.setHTML('<i class="fa fa-arrow-right" aria-hidden="true"></i>');
            }
        }
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations,
                i;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                if (this.divcartridge !== '') {
                    var divid = '#' + this.divcartridge;
                    var divdisplay = this.editor.get_dialogue_element(divid);
                    divdisplay.remove();
                }
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationcommentplus = ANNOTATIONCOMMENTPLUS;
var DROPDOWN_NAME = "Dropdown menu",
        DROPDOWN;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * This is a drop down list of buttons triggered (and aligned to) a button.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class dropdown
 * @constructor
 * @extends M.core.dialogue
 */
DROPDOWN = function (config) {
    config.draggable = false;
    config.centered = false;
    config.width = 'auto';
    config.visible = false;
    config.footerContent = '';
    DROPDOWN.superclass.constructor.apply(this, [config]);
};

Y.extend(DROPDOWN, M.core.dialogue, {
    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function (config) {
        var button, body, headertext, bb;
        DROPDOWN.superclass.initializer.call(this, config);

        bb = this.get('boundingBox');
        bb.addClass('assignfeedback_editpdfplus_dropdown');

        // Align the menu to the button that opens it.
        button = this.get('buttonNode');

        // Close the menu when clicked outside (excluding the button that opened the menu).
        body = this.bodyNode;

        headertext = Y.Node.create('<h3/>');
        headertext.addClass('accesshide');
        headertext.setHTML(this.get('headerText'));
        body.prepend(headertext);

        body.on('clickoutside', function (e) {
            if (this.get('visible')) {
                // Note: we need to compare ids because for some reason - sometimes button is an Object, not a Y.Node.
                if (e.target.get('id') !== button.get('id') && e.target.ancestor().get('id') !== button.get('id')) {
                    e.preventDefault();
                    this.hide();
                }
            }
        }, this);

        button.on('click', function (e) {
            e.preventDefault();
            this.show();
        }, this);
        button.on('key', this.show, 'enter,space', this);
    },

    /**
     * Override the show method to align to the button.
     *
     * @method show
     * @return void
     */
    show: function () {
        var button = this.get('buttonNode'),
                result = DROPDOWN.superclass.show.call(this);
        this.align(button, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);

        return result;
    }
}, {
    NAME: DROPDOWN_NAME,
    ATTRS: {
        /**
         * The header for the drop down (only accessible to screen readers).
         *
         * @attribute headerText
         * @type String
         * @default ''
         */
        headerText: {
            value: ''
        },

        /**
         * The button used to show/hide this drop down menu.
         *
         * @attribute buttonNode
         * @type Y.Node
         * @default null
         */
        buttonNode: {
            value: null
        }
    }
});

Y.Base.modifyAttrs(DROPDOWN, {
    /**
     * Whether the widget should be modal or not.
     *
     * Moodle override: We override this for commentsearch to force it always false.
     *
     * @attribute Modal
     * @type Boolean
     * @default false
     */
    modal: {
        getter: function () {
            return false;
        }
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.dropdown = DROPDOWN;
/* global Y, M */

var COLOURPICKER_NAME = "Colourpicker",
        COLOURPICKER;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * COLOURPICKER
 * This is a drop down list of colours.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class colourpicker
 * @constructor
 * @extends M.assignfeedback_editpdfplus.dropdown
 */
COLOURPICKER = function (config) {
    COLOURPICKER.superclass.constructor.apply(this, [config]);
};

Y.extend(COLOURPICKER, M.assignfeedback_editpdfplus.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function (config) {
        var colourlist = Y.Node.create('<ul role="menu" class="assignfeedback_editpdfplus_menu"/>'),
                body;
        var iconGoutte;

        // Build a list of coloured buttons.
        Y.each(this.get('colours'), function (rgb, colour) {
            var button, listitem, title;

            title = M.util.get_string(colour, 'assignfeedback_editpdfplus');
            if (colour === "white" || colour === "yellowlemon") {
                iconGoutte = Y.Node.create('<span class="fa-stack fa-lg">'
                        + '<i class="fa fa-square fa-stack-2x" style="color:#E3E3E3;"></i>'
                        + '<i class="fa fa-tint fa-stack-1x fa-inverse" aria-hidden="true" '
                        + 'style="color:' + rgb + ';">'
                        + '</i>'
                        + '</span>');
            } else {
                iconGoutte = Y.Node.create('<span class="fa-stack fa-lg">'
                        + '<i class="fa fa-square-o fa-stack-2x" style="color:#E3E3E3;"></i>'
                        + '<i class="fa fa-tint fa-stack-1x" aria-hidden="true" '
                        + 'style="color:' + rgb + ';">'
                        + '</i>'
                        + '</span>');
            }
            iconGoutte.setAttribute('data-colour', colour);
            button = Y.Node.create('<button class="btn btn-sm" type="button"></button>');
            button.append(iconGoutte);
            button.setAttribute('data-colour', colour);
            button.setAttribute('data-rgb', rgb);
            button.setStyle('backgroundImage', 'none');
            listitem = Y.Node.create('<li/>');
            listitem.append(button);
            colourlist.append(listitem);
        }, this);

        body = Y.Node.create('<div style="max-width:50px;"></div>');

        // Set the call back.
        colourlist.delegate('click', this.callback_handler, 'button', this);
        colourlist.delegate('key', this.callback_handler, 'down:13', 'button', this);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('colourpicker', 'assignfeedback_editpdfplus'));

        // Set the body content.
        body.append(colourlist);
        this.set('bodyContent', body);

        COLOURPICKER.superclass.initializer.call(this, config);
    },
    callback_handler: function (e) {
        e.preventDefault();

        var callback = this.get('callback'),
                callbackcontext = this.get('context'),
                bind;

        this.hide();

        // Call the callback with the specified context.
        bind = Y.bind(callback, callbackcontext, e);

        bind();
    }
}, {
    NAME: COLOURPICKER_NAME,
    ATTRS: {
        /**
         * The list of colours this colour picker supports.
         *
         * @attribute colours
         * @type {String: String} (The keys of the array are the colour names and the values are localized strings)
         * @default {}
         */
        colours: {
            value: {}
        },

        /**
         * The function called when a new colour is chosen.
         *
         * @attribute callback
         * @type function
         * @default null
         */
        callback: {
            value: null
        },

        /**
         * The context passed to the callback when a colour is chosen.
         *
         * @attribute context
         * @type Y.Node
         * @default null
         */
        context: {
            value: null
        },

        /**
         * The prefix for the icon image names.
         *
         * @attribute iconprefix
         * @type String
         * @default 'colour_'
         */
        iconprefix: {
            value: 'colour_'
        }
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.colourpicker = COLOURPICKER;
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


}, '@VERSION@', {
    "requires": [
        "base",
        "event",
        "node",
        "io",
        "graphics",
        "json",
        "event-move",
        "event-resize",
        "transition",
        "querystring-stringify-simple",
        "moodle-core-notification-dialog",
        "moodle-core-notification-alert",
        "moodle-core-notification-warning",
        "moodle-core-notification-exception",
        "moodle-core-notification-ajaxexception"
    ]
});
