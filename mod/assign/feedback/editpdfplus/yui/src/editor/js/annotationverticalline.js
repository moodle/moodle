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
