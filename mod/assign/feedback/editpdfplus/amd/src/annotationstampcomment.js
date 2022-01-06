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
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module mod_assignfeedback_editpdfplus/annotationstampcomment
 * @param {Jquery} $
 * @param {Annotation} Annotation super-class
 * @returns {AnnotationStampcomment} annotation stamp comment
 */
define(['jquery', './annotation'],
        function ($, Annotation) {

            /********************************
             * CONSTRUCTOR and EXTEND-CLASS *
             ********************************/

            // I return an initialized object.
            function AnnotationStampcomment() {
                // Call the super constructor.
                Annotation.call(this);
                // Return this object reference.
                return(this);
            }
            // The Friend class extends the base Model class.
            AnnotationStampcomment.prototype = Object.create(Annotation.prototype);

            /*************
             * FUNCTIONS *
             *************/

            /**
             * Init the annotation with demo parameters
             * @param {Tool} currentTool
             */
            AnnotationStampcomment.prototype.initAdminDemo = function (currentTool) {
                Annotation.prototype.initAdminDemo.call(this, currentTool);
                this.x = 188;
                this.y = 118;
                this.displayrotation = 1;
            };
            /**
             * Draw the annotation
             * @param {JQuery Entity} canevas
             * @returns {Annotation} this annotation
             */
            AnnotationStampcomment.prototype.draw = function (canevas) {
                if (canevas) {
                    var color = this.get_color_cartridge();
                    var flecheIco = "fa-arrows-h";
                    if (this.displayrotation > 0) {
                        flecheIco = "fa-arrows-v";
                    }
                    this.shape_id = 'ct_stampcomment_' + (new Date().toJSON()).replace(/:/g, '').replace(/\./g, '');
                    var fleche = '<i id="'
                            + this.shape_id
                            + '_img" '
                            + 'class="fa ' + flecheIco + ' fa-2x" aria-hidden="true" style="color:'
                            + color
                            + ';"></i>';
                    var divStampComment = "<div id='" + this.id + "'>" + fleche + "</div>";
                    canevas.append(divStampComment);
                    $("#" + this.id).css('position', 'relative');
                    $("#" + this.id).css('left', this.x);
                    $("#" + this.id).css('top', this.y);
                }

                this.draw_catridge(canevas);

                return;
            };
            /**
             * Display cartridge and toolbox for the annotation
             * @param {JQuery Entity} canevas
             * @returns {Boolean} res
             */
            AnnotationStampcomment.prototype.draw_catridge = function (canevas) {
                var divdisplay;
                if (!this.divcartridge || this.divcartridge === '') {
                    this.init_div_cartridge_id();

                    //init cartridge
                    var colorcartridge = this.get_color_cartridge();
                    divdisplay = this.get_div_cartridge(colorcartridge, canevas);
                    divdisplay.addClass('assignfeedback_editpdfplus_stampcomment');

                    // inscription entete
                    this.get_div_cartridge_label(colorcartridge, divdisplay);

                    //creation input
                    var divconteneurdisplay = this.get_div_container(colorcartridge, divdisplay);
                    var toolbar = this.get_toolbar();
                    //if (!this.editor.get('readonly')) {
                    var rotationvalue = 0;
                    if (this.displayrotation > 0) {
                        rotationvalue = 1;
                    }
                    var inputrotationdisplay = "<input type='hidden' id='"
                            + this.divcartridge
                            + "_rotation' value="
                            + rotationvalue
                            + " />";
                    divconteneurdisplay.append(inputrotationdisplay);
                    var buttonrotation = "<button id='"
                            + this.divcartridge
                            + "_buttonrotation' class='btn btn-sm btn-outline-dark'";
                    if (this.adminDemo) {
                        buttonrotation += " disabled";
                    }
                    buttonrotation += " type='button'>"
                            + '<i class="fa fa-refresh" aria-hidden="true"></i>'
                            + "</button>";
                    var buttonrotationdisplay = $(buttonrotation);
                    //buttonrotationdisplay.on('click', this.change_stamp, this);
                    toolbar.append(buttonrotationdisplay);
                    divconteneurdisplay.append(toolbar);
                    //}

                    //creation de la div d'edition
                    //if (!this.editor.get('readonly')) {
                    this.get_div_edition(divconteneurdisplay);
                    //} else {
                    //var divvisudisplay = this.get_div_visu(colorcartridge);
                    //divconteneurdisplay.append(divvisudisplay);
                    //}

                    //positionnement de la div par rapport a l'annotation
                    if (!this.cartridgex || this.cartridgex === 0) {
                        this.cartridgex = parseInt(this.tooltype.getToolTypeCartX(), 10);
                    }
                    if (!this.cartridgey || this.cartridgey === 0) {
                        this.cartridgey = parseInt(this.tooltype.getToolTypeCartY(), 10);
                    }
                    divdisplay.css('left', this.x + this.cartridgex);
                    divdisplay.css('top', this.y + this.cartridgey - 30);

                    this.apply_visibility_annot();
                } else {
                    //divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
                    //divdisplay.setX(offsetcanvas[0] + this.x + this.cartridgex);
                    //divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
                }
                return true;
            };

            return AnnotationStampcomment;
        });