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
