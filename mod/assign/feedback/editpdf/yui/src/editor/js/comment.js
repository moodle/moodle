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
COMMENT = function(editor, gradeid, pageno, x, y, width, colour, rawtext) {

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
            gradeid : this.gradeid,
            x : parseInt(this.x, 10),
            y : parseInt(this.y, 10),
            width : parseInt(this.width, 10),
            rawtext : this.rawtext,
            pageno : this.currentpage,
            colour : this.colour
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
            drawingregion = Y.one(SELECTOR.DRAWINGREGION),
            container,
            menu,
            position,
            scrollheight;

        // Lets add a contenteditable div.
        node = Y.Node.create('<textarea/>');
        container = Y.Node.create('<div class="commentdrawable"/>');
        menu = Y.Node.create('<a href="#"><img src="' + M.util.image_url('t/contextmenu', 'core') + '"/></a>');

        this.menulink = menu;
        container.append(node);

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
        drawable.nodes.push(container);
        node.set('value', this.rawtext);
        scrollheight = node.get('scrollHeight'),
        node.setStyles({
            'height' : scrollheight + 'px',
            'overflow': 'hidden'
        });
        if (!this.editor.get('readonly')) {
            this.attach_events(node, menu);
        }
        if (focus) {
            node.focus();
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
            var scrollheight = node.get('scrollHeight'),
                height = parseInt(node.getStyle('height'), 10);

            // Webkit scrollheight fix.
            if (scrollheight === height + 8) {
                scrollheight -= 8;
            }
            node.setStyle('height', scrollheight + 'px');

        });

        node.on('gesturemovestart', function(e) {
            node.setData('dragging', true);
            node.setData('offsetx', e.clientX - node.getX());
            node.setData('offsety', e.clientY - node.getY());
        });
        node.on('gesturemoveend', function() {
            node.setData('dragging', false);
            this.editor.save_current_page();
        }, null, this);
        node.on('gesturemove', function(e) {
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
            node.ancestor().setX(windowlocation.x);
            node.ancestor().setY(windowlocation.y);
        }, null, this);

        this.menu = new M.assignfeedback_editpdf.commentmenu({
            buttonNode: this.menulink,
            comment: this
        });
    };

    /**
     * Delete a comment.
     * @method remove
     */
    this.remove = function() {
        var i = 0, comments;

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

        this.rawtext = quickcomment.rawtext;
        this.width = quickcomment.width;
        this.colour = quickcomment.colour;

        this.editor.save_current_page();

        this.editor.redraw();
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
