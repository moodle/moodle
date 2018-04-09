YUI.add('moodle-assignfeedback_editpdf-editor', function (Y, NAME) {

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

/**
 * A list of globals used by this module.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */
var AJAXBASE = M.cfg.wwwroot + '/mod/assign/feedback/editpdf/ajax.php',
    AJAXBASEPROGRESS = M.cfg.wwwroot + '/mod/assign/feedback/editpdf/ajax_progress.php',
    CSS = {
        DIALOGUE: 'assignfeedback_editpdf_widget'
    },
    SELECTOR = {
        PREVIOUSBUTTON:  '.navigate-previous-button',
        NEXTBUTTON:  ' .navigate-next-button',
        SEARCHCOMMENTSBUTTON: '.searchcommentsbutton',
        EXPCOLCOMMENTSBUTTON: '.expcolcommentsbutton',
        SEARCHFILTER: '.assignfeedback_editpdf_commentsearch input',
        SEARCHCOMMENTSLIST: '.assignfeedback_editpdf_commentsearch ul',
        PAGESELECT: '.navigate-page-select',
        LOADINGICON: '.loading',
        PROGRESSBARCONTAINER: '.progress-info.progress-striped',
        DRAWINGREGION: '.drawingregion',
        DRAWINGCANVAS: '.drawingcanvas',
        SAVE: '.savebutton',
        COMMENTCOLOURBUTTON: '.commentcolourbutton',
        COMMENTMENU: '.commentdrawable a',
        ANNOTATIONCOLOURBUTTON:  '.annotationcolourbutton',
        DELETEANNOTATIONBUTTON: '.deleteannotationbutton',
        UNSAVEDCHANGESDIV: '.assignfeedback_editpdf_unsavedchanges',
        UNSAVEDCHANGESINPUT: 'input[name="assignfeedback_editpdf_haschanges"]',
        STAMPSBUTTON: '.currentstampbutton',
        DIALOGUE: '.' + CSS.DIALOGUE
    },
    SELECTEDBORDERCOLOUR = 'rgba(200, 200, 255, 0.9)',
    SELECTEDFILLCOLOUR = 'rgba(200, 200, 255, 0.5)',
    COMMENTTEXTCOLOUR = 'rgb(51, 51, 51)',
    COMMENTCOLOUR = {
        'white': 'rgb(255,255,255)',
        'yellow': 'rgb(255,236,174)',
        'red': 'rgb(249,181,179)',
        'green': 'rgb(214,234,178)',
        'blue': 'rgb(203,217,237)',
        'clear': 'rgba(255,255,255, 0)'
    },
    ANNOTATIONCOLOUR = {
        'white': 'rgb(255,255,255)',
        'yellow': 'rgb(255,207,53)',
        'red': 'rgb(239,69,64)',
        'green': 'rgb(152,202,62)',
        'blue': 'rgb(125,159,211)',
        'black': 'rgb(51,51,51)'
    },
    CLICKTIMEOUT = 300,
    TOOLSELECTOR = {
        'comment': '.commentbutton',
        'pen': '.penbutton',
        'line': '.linebutton',
        'rectangle': '.rectanglebutton',
        'oval': '.ovalbutton',
        'stamp': '.stampbutton',
        'select': '.selectbutton',
        'drag': '.dragbutton',
        'highlight': '.highlightbutton'
    },
    STROKEWEIGHT = 4;
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
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a 2d point.
 *
 * @namespace M.assignfeedback_editpdf
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
     * @param M.assignfeedback_editpdf.point
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.point = POINT;
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
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a 2d rect.
 *
 * @namespace M.assignfeedback_editpdf
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
     * @param M.assignfeedback_editpdf.point[]
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.rect = RECT;
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
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * EDIT
 *
 * @namespace M.assignfeedback_editpdf
 * @class edit
 */
var EDIT = function() {

    /**
     * Starting point for the edit.
     * @property start
     * @type M.assignfeedback_editpdf.point|false
     * @public
     */
    this.start = false;

    /**
     * Finishing point for the edit.
     * @property end
     * @type M.assignfeedback_editpdf.point|false
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
     * @type M.assignfeedback_editpdf.point|false
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
     * The current stamp image.
     * @property stamp
     * @type String
     * @public
     */
    this.stamp = '';

    /**
     * List of points the the current drawing path.
     * @property path
     * @type M.assignfeedback_editpdf.point[]
     * @public
     */
    this.path = [];
};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.edit = EDIT;
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
/* global SELECTOR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a drawable thing which contains both Y.Nodes, and Y.Shapes.
 *
 * @namespace M.assignfeedback_editpdf
 * @param M.assignfeedback_editpdf.editor editor
 * @class drawable
 */
var DRAWABLE = function(editor) {

    /**
     * Reference to M.assignfeedback_editpdf.editor.
     * @property editor
     * @type M.assignfeedback_editpdf.editor
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.drawable = DRAWABLE;
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
/* global STROKEWEIGHT, SELECTOR, SELECTEDBORDERCOLOUR, SELECTEDFILLCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotation
 * @constructor
 */
var ANNOTATION = function(config) {
    ANNOTATION.superclass.constructor.apply(this, [config]);
};

ANNOTATION.NAME = "annotation";
ANNOTATION.ATTRS = {};

Y.extend(ANNOTATION, Y.Base, {
    /**
     * Reference to M.assignfeedback_editpdf.editor.
     * @property editor
     * @type M.assignfeedback_editpdf.editor
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
     * @property type
     * @type String
     * @public
     */
    type: 'rect',

    /**
     * Annotation colour.
     * @property colour
     * @type String
     * @public
     */
    colour: 'red',

    /**
     * Reference to M.assignfeedback_editpdf.drawable
     * @property drawable
     * @type M.assignfeedback_editpdf.drawable
     * @public
     */
    drawable: false,

    /**
     * Initialise the annotation.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        this.editor = config.editor || null;
        this.gradeid = parseInt(config.gradeid, 10) || 0;
        this.pageno = parseInt(config.pageno, 10) || 0;
        this.x = parseInt(config.x, 10) || 0;
        this.y = parseInt(config.y, 10) || 0;
        this.endx = parseInt(config.endx, 10) || 0;
        this.endy = parseInt(config.endy, 10) || 0;
        this.path = config.path || '';
        this.type = config.type || 'rect';
        this.colour = config.colour || 'red';
        this.drawable = false;
    },

    /**
     * Clean a comment record, returning an oject with only fields that are valid.
     * @public
     * @method clean
     * @return {}
     */
    clean: function() {
        return {
            gradeid: this.gradeid,
            x: parseInt(this.x, 10),
            y: parseInt(this.y, 10),
            endx: parseInt(this.endx, 10),
            endy: parseInt(this.endy, 10),
            type: this.type,
            path: this.path,
            pageno: this.pageno,
            colour: this.colour
        };
    },

    /**
     * Draw a selection around this annotation if it is selected.
     * @public
     * @method draw_highlight
     * @return M.assignfeedback_editpdf.drawable
     */
    draw_highlight: function() {
        var bounds,
            drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
            offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY(),
            shape;

        if (this.editor.currentannotation === this) {
            // Draw a highlight around the annotation.
            bounds = new M.assignfeedback_editpdf.rect();
            bounds.bound([new M.assignfeedback_editpdf.point(this.x, this.y),
                          new M.assignfeedback_editpdf.point(this.endx, this.endy)]);

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

            // Add a delete X to the annotation.
            var deleteicon = Y.Node.create('<img src="' + M.util.image_url('trash', 'assignfeedback_editpdf') + '"/>'),
                deletelink = Y.Node.create('<a href="#" role="button"></a>');

            deleteicon.setAttrs({
                'alt': M.util.get_string('deleteannotation', 'assignfeedback_editpdf')
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
            deletelink.setY(offsetcanvas[1] + bounds.y + 6);
            this.drawable.nodes.push(deletelink);
        }
        return this.drawable;
    },

    /**
     * Draw an annotation
     * @public
     * @method draw
     * @return M.assignfeedback_editpdf.drawable|false
     */
    draw: function() {
        // Should be overridden by the subclass.
        this.draw_highlight();
        return this.drawable;
    },

    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function(e) {
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
    move: function(newx, newy) {
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
            Y.each(oldpath, function(position) {
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
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var noop = edit && false;
        // Override me please.
        return noop;
    },

    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect();
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
    }

});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotation = ANNOTATION;
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
/* global STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a line.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationline
 * @extends M.assignfeedback_editpdf.annotation
 */
var ANNOTATIONLINE = function(config) {
    ANNOTATIONLINE.superclass.constructor.apply(this, [config]);
};

ANNOTATIONLINE.NAME = "annotationline";
ANNOTATIONLINE.ATTRS = {};

Y.extend(ANNOTATIONLINE, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a line annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable,
            shape;

        drawable = new M.assignfeedback_editpdf.drawable(this.editor);

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
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
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
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool true if line bound is more than min width/height, else false.
     */
    init_from_edit: function(edit) {
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationline = ANNOTATIONLINE;
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
/* global STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a rectangle.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationrectangle
 * @extends M.assignfeedback_editpdf.annotation
 */
var ANNOTATIONRECTANGLE = function(config) {
    ANNOTATIONRECTANGLE.superclass.constructor.apply(this, [config]);
};

ANNOTATIONRECTANGLE.NAME = "annotationrectangle";
ANNOTATIONRECTANGLE.ATTRS = {};

Y.extend(ANNOTATIONRECTANGLE, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a rectangle annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable,
            bounds,
            shape;

        drawable = new M.assignfeedback_editpdf.drawable(this.editor);

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(this.x, this.y),
                      new M.assignfeedback_editpdf.point(this.endx, this.endy)]);

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
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            shape,
            bounds;

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(edit.start.x, edit.start.y),
                      new M.assignfeedback_editpdf.point(edit.end.x, edit.end.y)]);

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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationrectangle = ANNOTATIONRECTANGLE;
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
/* global STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a oval.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationoval
 * @extends M.assignfeedback_editpdf.annotation
 */
var ANNOTATIONOVAL = function(config) {
    ANNOTATIONOVAL.superclass.constructor.apply(this, [config]);
};

ANNOTATIONOVAL.NAME = "annotationoval";
ANNOTATIONOVAL.ATTRS = {};

Y.extend(ANNOTATIONOVAL, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a oval annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable,
            bounds,
            shape;

        drawable = new M.assignfeedback_editpdf.drawable(this.editor);

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(this.x, this.y),
                      new M.assignfeedback_editpdf.point(this.endx, this.endy)]);

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
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            shape,
            bounds;

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(edit.start.x, edit.start.y),
                      new M.assignfeedback_editpdf.point(edit.end.x, edit.end.y)]);

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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationoval = ANNOTATIONOVAL;
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
/* global STROKEWEIGHT, ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a pen.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationpen
 * @extends M.assignfeedback_editpdf.annotation
 */
var ANNOTATIONPEN = function(config) {
    ANNOTATIONPEN.superclass.constructor.apply(this, [config]);
};

ANNOTATIONPEN.NAME = "annotationpen";
ANNOTATIONPEN.ATTRS = {};

Y.extend(ANNOTATIONPEN, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a pen annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable,
            shape,
            first,
            positions,
            xy;

        drawable = new M.assignfeedback_editpdf.drawable(this.editor);

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
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
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
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool true if pen bound is more than min width/height, else false.
     */
    init_from_edit: function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect(),
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationpen = ANNOTATIONPEN;
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
/* global ANNOTATIONCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationhighlight
 * @extends M.assignfeedback_editpdf.annotation
 * @module moodle-assignfeedback_editpdf-editor
 */
var ANNOTATIONHIGHLIGHT = function(config) {
    ANNOTATIONHIGHLIGHT.superclass.constructor.apply(this, [config]);
};

ANNOTATIONHIGHLIGHT.NAME = "annotationhighlight";
ANNOTATIONHIGHLIGHT.ATTRS = {};

Y.extend(ANNOTATIONHIGHLIGHT, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable,
            shape,
            bounds,
            highlightcolour;

        drawable = new M.assignfeedback_editpdf.drawable(this.editor);
        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(this.x, this.y),
                      new M.assignfeedback_editpdf.point(this.endx, this.endy)]);

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
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            shape,
            bounds,
            highlightcolour;

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([new M.assignfeedback_editpdf.point(edit.start.x, edit.start.y),
                      new M.assignfeedback_editpdf.point(edit.end.x, edit.end.y)]);

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
            height: 16,
            stroke: false,
            fill: {
               color: highlightcolour
            },
            x: bounds.x,
            y: edit.start.y
        });

        drawable.shapes.push(shape);

        return drawable;
    },

    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect();
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationhighlight = ANNOTATIONHIGHLIGHT;
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
/* global SELECTOR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a stamp.
 *
 * @namespace M.assignfeedback_editpdf
 * @class annotationstamp
 * @extends M.assignfeedback_editpdf.annotation
 */
var ANNOTATIONSTAMP = function(config) {
    ANNOTATIONSTAMP.superclass.constructor.apply(this, [config]);
};

ANNOTATIONSTAMP.NAME = "annotationstamp";
ANNOTATIONSTAMP.ATTRS = {};

Y.extend(ANNOTATIONSTAMP, M.assignfeedback_editpdf.annotation, {
    /**
     * Draw a stamp annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdf.drawable
     */
    draw: function() {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
            node,
            position;

        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdf.point(this.x, this.y));
        node = Y.Node.create('<div/>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'backgroundImage': 'url(' + this.editor.get_stamp_image_url(this.path) + ')',
            'width': (this.endx - this.x),
            'height': (this.endy - this.y),
            'backgroundSize': '100% 100%',
            'zIndex': 50
        });

        drawingregion.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);

        // Bind events only when editing.
        if (!this.editor.get('readonly')) {
            // Pass through the event handlers on the div.
            node.on('gesturemovestart', this.editor.edit_start, null, this.editor);
            node.on('gesturemove', this.editor.edit_move, null, this.editor);
            node.on('gesturemoveend', this.editor.edit_end, null, this.editor);
        }

        drawable.nodes.push(node);

        this.drawable = drawable;
        return ANNOTATIONSTAMP.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdf.edit edit
     */
    draw_current_edit: function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect(),
            drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
            node,
            position;

        bounds.bound([edit.start, edit.end]);
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdf.point(bounds.x, bounds.y));

        node = Y.Node.create('<div/>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'backgroundImage': 'url(' + this.editor.get_stamp_image_url(edit.stamp) + ')',
            'width': bounds.width,
            'height': bounds.height,
            'backgroundSize': '100% 100%',
            'zIndex': 50
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
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([edit.start, edit.end]);

        if (bounds.width < 40) {
            bounds.width = 40;
        }
        if (bounds.height < 40) {
            bounds.height = 40;
        }
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = bounds.y;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;
        this.colour = edit.annotationcolour;
        this.path = edit.stamp;

        // Min width and height is always more than 40px.
        return true;
    },

    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function(newx, newy) {
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.annotationstamp = ANNOTATIONSTAMP;
var DROPDOWN_NAME = "Dropdown menu",
    DROPDOWN;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * This is a drop down list of buttons triggered (and aligned to) a button.
 *
 * @namespace M.assignfeedback_editpdf
 * @class dropdown
 * @constructor
 * @extends M.core.dialogue
 */
DROPDOWN = function(config) {
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
    initializer: function(config) {
        var button, body, headertext, bb;
        DROPDOWN.superclass.initializer.call(this, config);

        bb = this.get('boundingBox');
        bb.addClass('assignfeedback_editpdf_dropdown');

        // Align the menu to the button that opens it.
        button = this.get('buttonNode');

        // Close the menu when clicked outside (excluding the button that opened the menu).
        body = this.bodyNode;

        headertext = Y.Node.create('<h3/>');
        headertext.addClass('accesshide');
        headertext.setHTML(this.get('headerText'));
        body.prepend(headertext);

        body.on('clickoutside', function(e) {
            if (this.get('visible')) {
                // Note: we need to compare ids because for some reason - sometimes button is an Object, not a Y.Node.
                if (e.target.get('id') !== button.get('id') && e.target.ancestor().get('id') !== button.get('id')) {
                    e.preventDefault();
                    this.hide();
                }
            }
        }, this);

        button.on('click', function(e) {
            e.preventDefault(); this.show();
        }, this);
        button.on('key', this.show, 'enter,space', this);
    },

    /**
     * Override the show method to align to the button.
     *
     * @method show
     * @return void
     */
    show: function() {
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
        getter: function() {
            return false;
        }
    }
});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.dropdown = DROPDOWN;
var COLOURPICKER_NAME = "Colourpicker",
    COLOURPICKER;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * COLOURPICKER
 * This is a drop down list of colours.
 *
 * @namespace M.assignfeedback_editpdf
 * @class colourpicker
 * @constructor
 * @extends M.assignfeedback_editpdf.dropdown
 */
COLOURPICKER = function(config) {
    COLOURPICKER.superclass.constructor.apply(this, [config]);
};

Y.extend(COLOURPICKER, M.assignfeedback_editpdf.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        var colourlist = Y.Node.create('<ul role="menu" class="assignfeedback_editpdf_menu"/>'),
            body;

        // Build a list of coloured buttons.
        Y.each(this.get('colours'), function(rgb, colour) {
            var button, listitem, title, img, iconname;

            title = M.util.get_string(colour, 'assignfeedback_editpdf');
            iconname = this.get('iconprefix') + colour;
            img = M.util.image_url(iconname, 'assignfeedback_editpdf');
            button = Y.Node.create('<button><img alt="' + title + '" src="' + img + '"/></button>');
            button.setAttribute('data-colour', colour);
            button.setAttribute('data-rgb', rgb);
            button.setStyle('backgroundImage', 'none');
            listitem = Y.Node.create('<li/>');
            listitem.append(button);
            colourlist.append(listitem);
        }, this);

        body = Y.Node.create('<div/>');

        // Set the call back.
        colourlist.delegate('click', this.callback_handler, 'button', this);
        colourlist.delegate('key', this.callback_handler, 'down:13', 'button', this);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('colourpicker', 'assignfeedback_editpdf'));

        // Set the body content.
        body.append(colourlist);
        this.set('bodyContent', body);

        COLOURPICKER.superclass.initializer.call(this, config);
    },
    callback_handler: function(e) {
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

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.colourpicker = COLOURPICKER;
var STAMPPICKER_NAME = "Colourpicker",
    STAMPPICKER;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * This is a drop down list of stamps.
 *
 * @namespace M.assignfeedback_editpdf
 * @class stamppicker
 * @constructor
 * @extends M.assignfeedback_editpdf.dropdown
 */
STAMPPICKER = function(config) {
    STAMPPICKER.superclass.constructor.apply(this, [config]);
};

Y.extend(STAMPPICKER, M.assignfeedback_editpdf.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        var stamplist = Y.Node.create('<ul role="menu" class="assignfeedback_editpdf_menu"/>');

        // Build a list of stamped buttons.
        Y.each(this.get('stamps'), function(stamp) {
            var button, listitem, title;

            title = M.util.get_string('stamp', 'assignfeedback_editpdf');
            button = Y.Node.create('<button><img height="16" width="16" alt="' + title + '" src="' + stamp + '"/></button>');
            button.setAttribute('data-stamp', stamp);
            button.setStyle('backgroundImage', 'none');
            listitem = Y.Node.create('<li/>');
            listitem.append(button);
            stamplist.append(listitem);
        }, this);


        // Set the call back.
        stamplist.delegate('click', this.callback_handler, 'button', this);
        stamplist.delegate('key', this.callback_handler, 'down:13', 'button', this);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('stamppicker', 'assignfeedback_editpdf'));

        // Set the body content.
        this.set('bodyContent', stamplist);

        STAMPPICKER.superclass.initializer.call(this, config);
    },
    callback_handler: function(e) {
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
    NAME: STAMPPICKER_NAME,
    ATTRS: {
        /**
         * The list of stamps this stamp picker supports.
         *
         * @attribute stamps
         * @type String[] - the stamp filenames.
         * @default {}
         */
        stamps: {
            value: []
        },

        /**
         * The function called when a new stamp is chosen.
         *
         * @attribute callback
         * @type function
         * @default null
         */
        callback: {
            value: null
        },

        /**
         * The context passed to the callback when a stamp is chosen.
         *
         * @attribute context
         * @type Y.Node
         * @default null
         */
        context: {
            value: null
        }
    }
});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.stamppicker = STAMPPICKER;
var COMMENTMENUNAME = "Commentmenu",
    COMMENTMENU;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * COMMENTMENU
 * This is a drop down list of comment context functions.
 *
 * @namespace M.assignfeedback_editpdf
 * @class commentmenu
 * @constructor
 * @extends M.assignfeedback_editpdf.dropdown
 */
COMMENTMENU = function(config) {
    COMMENTMENU.superclass.constructor.apply(this, [config]);
};

Y.extend(COMMENTMENU, M.assignfeedback_editpdf.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        var commentlinks,
            link,
            body,
            comment;

        comment = this.get('comment');
        // Build the list of menu items.
        commentlinks = Y.Node.create('<ul role="menu" class="assignfeedback_editpdf_menu"/>');

        link = Y.Node.create('<li><a tabindex="-1" href="#">' +
               M.util.get_string('addtoquicklist', 'assignfeedback_editpdf') +
               '</a></li>');
        link.on('click', comment.add_to_quicklist, comment);
        link.on('key', comment.add_to_quicklist, 'enter,space', comment);

        commentlinks.append(link);

        link = Y.Node.create('<li><a tabindex="-1" href="#">' +
               M.util.get_string('deletecomment', 'assignfeedback_editpdf') +
               '</a></li>');
        link.on('click', function(e) {
            e.preventDefault();
            this.menu.hide();
            this.remove();
        }, comment);

        link.on('key', function() {
            comment.menu.hide();
            comment.remove();
        }, 'enter,space', comment);

        commentlinks.append(link);

        link = Y.Node.create('<li><hr/></li>');
        commentlinks.append(link);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('commentcontextmenu', 'assignfeedback_editpdf'));

        body = Y.Node.create('<div/>');

        // Set the body content.
        body.append(commentlinks);
        this.set('bodyContent', body);

        COMMENTMENU.superclass.initializer.call(this, config);
    },

    /**
     * Show the menu.
     *
     * @method show
     * @return void
     */
    show: function() {
        var commentlinks = this.get('boundingBox').one('ul');
            commentlinks.all('.quicklist_comment').remove(true);
        var comment = this.get('comment');

        comment.deleteme = false; // Cancel the deleting of blank comments.

        // Now build the list of quicklist comments.
        Y.each(comment.editor.quicklist.comments, function(quickcomment) {
            var listitem = Y.Node.create('<li class="quicklist_comment"></li>'),
                linkitem = Y.Node.create('<a href="#" tabindex="-1">' + quickcomment.rawtext + '</a>'),
                deletelinkitem = Y.Node.create('<a href="#" tabindex="-1" class="delete_quicklist_comment">' +
                                               '<img src="' + M.util.image_url('t/delete', 'core') + '" ' +
                                               'alt="' + M.util.get_string('deletecomment', 'assignfeedback_editpdf') + '"/>' +
                                               '</a>');
            listitem.append(linkitem);
            listitem.append(deletelinkitem);

            commentlinks.append(listitem);

            listitem.on('click', comment.set_from_quick_comment, comment, quickcomment);
            listitem.on('key', comment.set_from_quick_comment, 'space,enter', comment, quickcomment);

            deletelinkitem.on('click', comment.remove_from_quicklist, comment, quickcomment);
            deletelinkitem.on('key', comment.remove_from_quicklist, 'space,enter', comment, quickcomment);
        }, this);

        COMMENTMENU.superclass.show.call(this);
    }
}, {
    NAME: COMMENTMENUNAME,
    ATTRS: {
        /**
         * The comment this menu is attached to.
         *
         * @attribute comment
         * @type M.assignfeedback_editpdf.comment
         * @default null
         */
        comment: {
            value: null
        }

    }
});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.commentmenu = COMMENTMENU;
/* eslint-disable no-unused-vars */
/* global SELECTOR */
var COMMENTSEARCHNAME = "commentsearch",
    COMMENTSEARCH;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * This is a searchable dialogue of comments.
 *
 * @namespace M.assignfeedback_editpdf
 * @class commentsearch
 * @constructor
 * @extends M.core.dialogue
 */
COMMENTSEARCH = function(config) {
    config.draggable = false;
    config.centered = true;
    config.width = '400px';
    config.visible = false;
    config.headerContent = M.util.get_string('searchcomments', 'assignfeedback_editpdf');
    config.footerContent = '';
    COMMENTSEARCH.superclass.constructor.apply(this, [config]);
};

Y.extend(COMMENTSEARCH, M.core.dialogue, {
    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        var editor,
            container,
            placeholder,
            commentfilter,
            commentlist,
            bb;

        bb = this.get('boundingBox');
        bb.addClass('assignfeedback_editpdf_commentsearch');

        editor = this.get('editor');
        container = Y.Node.create('<div/>');

        placeholder = M.util.get_string('filter', 'assignfeedback_editpdf');
        commentfilter = Y.Node.create('<input type="text" size="20" placeholder="' + placeholder + '"/>');
        container.append(commentfilter);
        commentlist = Y.Node.create('<ul role="menu" class="assignfeedback_editpdf_search"/>');
        container.append(commentlist);

        commentfilter.on('keyup', this.filter_search_comments, this);
        commentlist.delegate('click', this.focus_on_comment, 'a', this);
        commentlist.delegate('key', this.focus_on_comment, 'enter,space', 'a', this);

        // Set the body content.
        this.set('bodyContent', container);

        COMMENTSEARCH.superclass.initializer.call(this, config);
    },

    /**
     * Event handler to filter the list of comments.
     *
     * @protected
     * @method filter_search_comments
     */
    filter_search_comments: function() {
        var filternode,
            commentslist,
            filtertext,
            dialogueid;

        dialogueid = this.get('id');
        filternode = Y.one('#' + dialogueid + SELECTOR.SEARCHFILTER);
        commentslist = Y.one('#' + dialogueid + SELECTOR.SEARCHCOMMENTSLIST);

        filtertext = filternode.get('value');

        commentslist.all('li').each(function(node) {
            if (node.get('text').indexOf(filtertext) !== -1) {
                node.show();
            } else {
                node.hide();
            }
        });
    },

    /**
     * Event handler to focus on a selected comment.
     *
     * @param Event e
     * @protected
     * @method focus_on_comment
     */
    focus_on_comment: function(e) {
        e.preventDefault();
        var target = e.target.ancestor('li'),
            comment = target.getData('comment'),
            editor = this.get('editor');

        this.hide();

        comment.pageno = comment.clean().pageno;
        if (comment.pageno !== editor.currentpage) {
            // Comment is on a different page.
            editor.currentpage = comment.pageno;
            editor.change_page();
        }

        comment.node = comment.drawable.nodes[0].one('textarea');
        comment.node.ancestor('div').removeClass('commentcollapsed');
        comment.node.focus();
    },

    /**
     * Show the menu.
     *
     * @method show
     * @return void
     */
    show: function() {
        var commentlist = this.get('boundingBox').one('ul'),
            editor = this.get('editor');

        commentlist.all('li').remove(true);

        // Rebuild the latest list of comments.
        Y.each(editor.pages, function(page) {
            Y.each(page.comments, function(comment) {
                var commentnode = Y.Node.create('<li><a href="#" tabindex="-1"><pre>' + comment.rawtext + '</pre></a></li>');
                commentlist.append(commentnode);
                commentnode.setData('comment', comment);
            }, this);
        }, this);

        this.centerDialogue();
        COMMENTSEARCH.superclass.show.call(this);
    }
}, {
    NAME: COMMENTSEARCHNAME,
    ATTRS: {
        /**
         * The editor this search window is attached to.
         *
         * @attribute editor
         * @type M.assignfeedback_editpdf.editor
         * @default null
         */
        editor: {
            value: null
        }

    }
});

Y.Base.modifyAttrs(COMMENTSEARCH, {
    /**
     * Whether the widget should be modal or not.
     *
     * Moodle override: We override this for commentsearch to force it always true.
     *
     * @attribute Modal
     * @type Boolean
     * @default true
     */
    modal: {
        getter: function() {
            return true;
        }
    }
});

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.commentsearch = COMMENTSEARCH;
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
/* global SELECTOR, COMMENTCOLOUR, COMMENTTEXTCOLOUR */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a list of comments.
 *
 * @namespace M.assignfeedback_editpdf
 * @class comment
 * @param M.assignfeedback_editpdf.editor editor
 * @param Int gradeid
 * @param Int pageno
 * @param Int x
 * @param Int y
 * @param Int width
 * @param String colour
 * @param String rawtext
 */
var COMMENT = function(editor, gradeid, pageno, x, y, width, colour, rawtext) {

    /**
     * Reference to M.assignfeedback_editpdf.editor.
     * @property editor
     * @type M.assignfeedback_editpdf.editor
     * @public
     */
    this.editor = editor;

    /**
     * Grade id
     * @property gradeid
     * @type Int
     * @public
     */
    this.gradeid = gradeid || 0;

    /**
     * X position
     * @property x
     * @type Int
     * @public
     */
    this.x = parseInt(x, 10) || 0;

    /**
     * Y position
     * @property y
     * @type Int
     * @public
     */
    this.y = parseInt(y, 10) || 0;

    /**
     * Comment width
     * @property width
     * @type Int
     * @public
     */
    this.width = parseInt(width, 10) || 0;

    /**
     * Comment rawtext
     * @property rawtext
     * @type String
     * @public
     */
    this.rawtext = rawtext || '';

    /**
     * Comment page number
     * @property pageno
     * @type Int
     * @public
     */
    this.pageno = pageno || 0;

    /**
     * Comment background colour.
     * @property colour
     * @type String
     * @public
     */
    this.colour = colour || 'yellow';

    /**
     * Reference to M.assignfeedback_editpdf.drawable
     * @property drawable
     * @type M.assignfeedback_editpdf.drawable
     * @public
     */
    this.drawable = false;

    /**
     * Boolean used by a timeout to delete empty comments after a short delay.
     * @property deleteme
     * @type Boolean
     * @public
     */
    this.deleteme = false;

    /**
     * Reference to the link that opens the menu.
     * @property menulink
     * @type Y.Node
     * @public
     */
    this.menulink = null;

    /**
     * Reference to the dialogue that is the context menu.
     * @property menu
     * @type M.assignfeedback_editpdf.dropdown
     * @public
     */
    this.menu = null;

    /**
     * Clean a comment record, returning an oject with only fields that are valid.
     * @public
     * @method clean
     * @return {}
     */
    this.clean = function() {
        return {
            gradeid: this.gradeid,
            x: parseInt(this.x, 10),
            y: parseInt(this.y, 10),
            width: parseInt(this.width, 10),
            rawtext: this.rawtext,
            pageno: parseInt(this.pageno, 10),
            colour: this.colour
        };
    };

    /**
     * Draw a comment.
     * @public
     * @method draw_comment
     * @param boolean focus - Set the keyboard focus to the new comment if true
     * @return M.assignfeedback_editpdf.drawable
     */
    this.draw = function(focus) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            node,
            drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
            container,
            label,
            marker,
            menu,
            position,
            scrollheight;

        // Lets add a contenteditable div.
        node = Y.Node.create('<textarea/>');
        container = Y.Node.create('<div class="commentdrawable"/>');
        label = Y.Node.create('<label/>');
        marker = Y.Node.create('<svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 13 13" ' +
                'preserveAspectRatio="xMinYMin meet">' +
                '<path d="M11 0H1C.4 0 0 .4 0 1v6c0 .6.4 1 1 1h1v4l4-4h5c.6 0 1-.4 1-1V1c0-.6-.4-1-1-1z" ' +
                'fill="currentColor" opacity="0.9" stroke="rgb(153, 153, 153)" stroke-width="0.5"/></svg>');
        menu = Y.Node.create('<a href="#"><img src="' + M.util.image_url('t/contextmenu', 'core') + '"/></a>');

        this.menulink = menu;
        container.append(label);
        label.append(node);
        container.append(marker);
        container.setAttribute('tabindex', '-1');
        label.setAttribute('tabindex', '0');
        node.setAttribute('tabindex', '-1');
        menu.setAttribute('tabindex', '0');

        if (!this.editor.get('readonly')) {
            container.append(menu);
        } else {
            node.setAttribute('readonly', 'readonly');
        }
        if (this.width < 100) {
            this.width = 100;
        }

        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdf.point(this.x, this.y));
        node.setStyles({
            width: this.width + 'px',
            backgroundColor: COMMENTCOLOUR[this.colour],
            color: COMMENTTEXTCOLOUR
        });

        drawingregion.append(container);
        container.setStyle('position', 'absolute');
        container.setX(position.x);
        container.setY(position.y);
        drawable.store_position(container, position.x, position.y);
        drawable.nodes.push(container);
        node.set('value', this.rawtext);
        scrollheight = node.get('scrollHeight');
        node.setStyles({
            'height': scrollheight + 'px',
            'overflow': 'hidden'
        });
        marker.setStyles({
            'position': 'absolute',
            'bottom': 0 - scrollheight + 'px',
            'color': COMMENTCOLOUR[this.colour]
        });
        this.attach_events(node, menu);
        if (focus) {
            node.focus();
        } else if (editor.collapsecomments) {
            container.addClass('commentcollapsed');
        }
        this.drawable = drawable;


        return drawable;
    };

    /**
     * Delete an empty comment if it's menu hasn't been opened in time.
     * @method delete_comment_later
     */
    this.delete_comment_later = function() {
        if (this.deleteme) {
            this.remove();
        }
    };

    /**
     * Comment nodes have a bunch of event handlers attached to them directly.
     * This is all done here for neatness.
     *
     * @protected
     * @method attach_comment_events
     * @param node - The Y.Node representing the comment.
     * @param menu - The Y.Node representing the menu.
     */
    this.attach_events = function(node, menu) {
        var container = node.ancestor('div'),
            label = node.ancestor('label');

        // Function to collapse a comment to a marker icon.
        node.collapse = function(delay) {
            node.collapse.delay = Y.later(delay, node, function() {
                container.addClass('commentcollapsed');
            });
        };

        // Function to expand a comment.
        node.expand = function() {
            container.removeClass('commentcollapsed');
        };

        // Expand comment on mouse over (under certain conditions) or click/tap.
        container.on('mouseenter', function() {
            if (editor.currentedit.tool === 'comment' || editor.currentedit.tool === 'select' || this.editor.get('readonly')) {
                node.expand();
                if (node.collapse.delay) {
                    node.collapse.delay.cancel();
                }
            }
        }, this);
        container.on('click', function() {
            node.expand();
            node.focus();
            if (node.collapse.delay) {
                node.collapse.delay.cancel();
            }
        }, this);

        // Functions to capture reverse tabbing events.
        node.on('keyup', function(e) {
            if (e.keyCode === 9 && e.shiftKey && menu.getAttribute('tabindex') === '0') {
                // User landed here via Shift+Tab (but not from this comment's menu).
                menu.focus();
            }
            menu.setAttribute('tabindex', '0');
        }, this);
        menu.on('keydown', function(e) {
            if (e.keyCode === 9 && e.shiftKey) {
                // User is tabbing back to the comment node from its own menu.
                menu.setAttribute('tabindex', '-1');
            }
        }, this);

        // Comment becomes "active" on label or menu focus.
        label.on('focus', function() {
            node.active = true;
            if (node.collapse.delay) {
                node.collapse.delay.cancel();
            }
            // Give comment a tabindex to prevent focus outline being suppressed.
            node.setAttribute('tabindex', '0');
            // Expand comment and pass focus to it.
            node.expand();
            node.focus();
            // Now remove label tabindex so user can reverse tab past it.
            label.setAttribute('tabindex', '-1');
        }, this);
        menu.on('focus', function() {
            node.active = true;
            if (node.collapse.delay) {
                node.collapse.delay.cancel();
            }
            this.deleteme = false;
            // Restore label tabindex so user can tab back to it from menu.
            label.setAttribute('tabindex', '0');
        }, this);

        // Always restore the default tabindex states when moving away.
        node.on('blur', function() {
            node.setAttribute('tabindex', '-1');
        }, this);
        label.on('blur', function() {
            label.setAttribute('tabindex', '0');
        }, this);

        // Collapse comment on mouse out if not currently active.
        container.on('mouseleave', function() {
            if (editor.collapsecomments && node.active !== true) {
                node.collapse(400);
            }
        }, this);

        // Collapse comment on blur.
        container.on('blur', function() {
            node.active = false;
            if (editor.collapsecomments) {
                node.collapse(800);
            }
        }, this);

        if (!this.editor.get('readonly')) {
            // Save the text on blur.
            node.on('blur', function() {
                // Save the changes back to the comment.
                this.rawtext = node.get('value');
                this.width = parseInt(node.getStyle('width'), 10);

                // Trim.
                if (this.rawtext.replace(/^\s+|\s+$/g, "") === '') {
                    // Delete empty comments.
                    this.deleteme = true;
                    Y.later(400, this, this.delete_comment_later);
                }
                this.editor.save_current_page();
                this.editor.editingcomment = false;
            }, this);

            // For delegated event handler.
            menu.setData('comment', this);

            node.on('keyup', function() {
                node.setStyle('height', 'auto');
                var scrollheight = node.get('scrollHeight'),
                    height = parseInt(node.getStyle('height'), 10);

                // Webkit scrollheight fix.
                if (scrollheight === height + 8) {
                    scrollheight -= 8;
                }
                node.setStyle('height', scrollheight + 'px');
            });

            node.on('gesturemovestart', function(e) {
                if (editor.currentedit.tool === 'select') {
                    e.preventDefault();
                    node.setData('dragging', true);
                    node.setData('offsetx', e.clientX - node.getX());
                    node.setData('offsety', e.clientY - node.getY());
                }
            });
            node.on('gesturemoveend', function() {
                if (editor.currentedit.tool === 'select') {
                    node.setData('dragging', false);
                    this.editor.save_current_page();
                }
            }, null, this);
            node.on('gesturemove', function(e) {
                if (editor.currentedit.tool === 'select') {
                    var x = e.clientX - node.getData('offsetx'),
                        y = e.clientY - node.getData('offsety'),
                        nodewidth,
                        nodeheight,
                        newlocation,
                        windowlocation,
                        bounds;

                    nodewidth = parseInt(node.getStyle('width'), 10);
                    nodeheight = parseInt(node.getStyle('height'), 10);

                    newlocation = this.editor.get_canvas_coordinates(new M.assignfeedback_editpdf.point(x, y));
                    bounds = this.editor.get_canvas_bounds(true);
                    bounds.x = 0;
                    bounds.y = 0;

                    bounds.width -= nodewidth + 42;
                    bounds.height -= nodeheight + 8;
                    // Clip to the window size - the comment size.
                    newlocation.clip(bounds);

                    this.x = newlocation.x;
                    this.y = newlocation.y;

                    windowlocation = this.editor.get_window_coordinates(newlocation);
                    container.setX(windowlocation.x);
                    container.setY(windowlocation.y);
                    this.drawable.store_position(container, windowlocation.x, windowlocation.y);
                }
            }, null, this);

            this.menu = new M.assignfeedback_editpdf.commentmenu({
                buttonNode: this.menulink,
                comment: this
            });
        }
    };

    /**
     * Delete a comment.
     * @method remove
     */
    this.remove = function() {
        var i = 0;
        var comments;

        comments = this.editor.pages[this.editor.currentpage].comments;
        for (i = 0; i < comments.length; i++) {
            if (comments[i] === this) {
                comments.splice(i, 1);
                this.drawable.erase();
                this.editor.save_current_page();
                return;
            }
        }
    };

    /**
     * Event handler to remove a comment from the users quicklist.
     *
     * @protected
     * @method remove_from_quicklist
     */
    this.remove_from_quicklist = function(e, quickcomment) {
        e.preventDefault();
        e.stopPropagation();

        this.menu.hide();

        this.editor.quicklist.remove(quickcomment);
    };

    /**
     * A quick comment was selected in the list, update the active comment and redraw the page.
     *
     * @param Event e
     * @protected
     * @method set_from_quick_comment
     */
    this.set_from_quick_comment = function(e, quickcomment) {
        e.preventDefault();

        this.menu.hide();
        this.deleteme = false;

        this.rawtext = quickcomment.rawtext;
        this.width = quickcomment.width;
        this.colour = quickcomment.colour;

        this.editor.save_current_page();

        this.editor.redraw();

        this.node = this.drawable.nodes[0].one('textarea');
        this.node.ancestor('div').removeClass('commentcollapsed');
        this.node.focus();
    };

    /**
     * Event handler to add a comment to the users quicklist.
     *
     * @protected
     * @method add_to_quicklist
     */
    this.add_to_quicklist = function(e) {
        e.preventDefault();
        this.menu.hide();
        this.editor.quicklist.add(this);
    };

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdf.edit edit
     */
    this.draw_current_edit = function(edit) {
        var drawable = new M.assignfeedback_editpdf.drawable(this.editor),
            shape,
            bounds;

        bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([edit.start, edit.end]);

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            fill: {
               color: COMMENTCOLOUR[edit.commentcolour]
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);

        return drawable;
    };

    /**
     * Promote the current edit to a real comment.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdf.edit edit
     * @return bool true if comment bound is more than min width/height, else false.
     */
    this.init_from_edit = function(edit) {
        var bounds = new M.assignfeedback_editpdf.rect();
        bounds.bound([edit.start, edit.end]);

        // Minimum comment width.
        if (bounds.width < 100) {
            bounds.width = 100;
        }

        // Save the current edit to the server and the current page list.

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = bounds.y;
        this.width = bounds.width;
        this.colour = edit.commentcolour;
        this.rawtext = '';

        return (bounds.has_min_width() && bounds.has_min_height());
    };

};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.comment = COMMENT;
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
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a users quick comment.
 *
 * @namespace M.assignfeedback_editpdf
 * @class quickcomment
 */
var QUICKCOMMENT = function(id, rawtext, width, colour) {

    /**
     * Quick comment text.
     * @property rawtext
     * @type String
     * @public
     */
    this.rawtext = rawtext || '';

    /**
     * ID of the comment
     * @property id
     * @type Int
     * @public
     */
    this.id = id || 0;

    /**
     * Width of the comment
     * @property width
     * @type Int
     * @public
     */
    this.width = width || 100;

    /**
     * Colour of the comment.
     * @property colour
     * @type String
     * @public
     */
    this.colour = colour || "yellow";
};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.quickcomment = QUICKCOMMENT;
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
/* global AJAXBASE */

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a users list of quick comments.
 *
 * @namespace M.assignfeedback_editpdf
 * @class quickcommentlist
 */
var QUICKCOMMENTLIST = function(editor) {

    /**
     * Reference to M.assignfeedback_editpdf.editor.
     * @property editor
     * @type M.assignfeedback_editpdf.editor
     * @public
     */
    this.editor = editor;

    /**
     * Array of Comments
     * @property shapes
     * @type M.assignfeedback_editpdf.quickcomment[]
     * @public
     */
    this.comments = [];

    /**
     * Add a comment to the users quicklist.
     *
     * @protected
     * @method add
     */
    this.add = function(comment) {
        var ajaxurl = AJAXBASE,
            config;

        // Do not save empty comments.
        if (comment.rawtext === '') {
            return;
        }

        config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'addtoquicklist',
                'userid': this.editor.get('userid'),
                'commenttext': comment.rawtext,
                'width': comment.width,
                'colour': comment.colour,
                'attemptnumber': this.editor.get('attemptnumber'),
                'assignmentid': this.editor.get('assignmentid')
            },
            on: {
                success: function(tid, response) {
                    var jsondata, quickcomment;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        } else {
                            quickcomment = new M.assignfeedback_editpdf.quickcomment(jsondata.id,
                                                                                     jsondata.rawtext,
                                                                                     jsondata.width,
                                                                                     jsondata.colour);
                            this.comments.push(quickcomment);
                            this.comments.sort(function(a, b) {
                                return a.rawtext.localeCompare(b.rawtext);
                            });
                        }
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function(tid, response) {
                    return M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);
    };

    /**
     * Remove a comment from the users quicklist.
     *
     * @public
     * @method remove
     */
    this.remove = function(comment) {
        var ajaxurl = AJAXBASE,
            config;

        // Should not happen.
        if (!comment) {
            return;
        }

        config = {
            method: 'post',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'removefromquicklist',
                'userid': this.editor.get('userid'),
                'commentid': comment.id,
                'attemptnumber': this.editor.get('attemptnumber'),
                'assignmentid': this.editor.get('assignmentid')
            },
            on: {
                success: function() {
                    var i;

                    // Find and remove the comment from the quicklist.
                    i = this.comments.indexOf(comment);
                    if (i >= 0) {
                        this.comments.splice(i, 1);
                    }
                },
                failure: function(tid, response) {
                    return M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);
    };

    /**
     * Load the users quick comments list.
     *
     * @protected
     * @method load_quicklist
     */
    this.load = function() {
        var ajaxurl = AJAXBASE,
            config;

        config = {
            method: 'get',
            context: this,
            sync: false,
            data: {
                'sesskey': M.cfg.sesskey,
                'action': 'loadquicklist',
                'userid': this.editor.get('userid'),
                'attemptnumber': this.editor.get('attemptnumber'),
                'assignmentid': this.editor.get('assignmentid')
            },
            on: {
                success: function(tid, response) {
                    var jsondata;
                    try {
                        jsondata = Y.JSON.parse(response.responseText);
                        if (jsondata.error) {
                            return new M.core.ajaxException(jsondata);
                        } else {
                            Y.each(jsondata, function(comment) {
                                var quickcomment = new M.assignfeedback_editpdf.quickcomment(comment.id,
                                                                                             comment.rawtext,
                                                                                             comment.width,
                                                                                             comment.colour);
                                this.comments.push(quickcomment);
                            }, this);

                            this.comments.sort(function(a, b) {
                                return a.rawtext.localeCompare(b.rawtext);
                            });
                        }
                    } catch (e) {
                        return new M.core.exception(e);
                    }
                },
                failure: function(tid, response) {
                    return M.core.exception(response.responseText);
                }
            }
        };

        Y.io(ajaxurl, config);
    };
};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.quickcommentlist = QUICKCOMMENTLIST;
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
        "moodle-core-notification-exception",
        "moodle-core-notification-ajaxexception"
    ]
});
