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
 * @module mod_assignfeedback_editpdfplus/annotationstampplus
 * @param {Jquery} $
 * @param {Annotation} Annotation super-class
 * @returns {AnnotationStampplus} annotation stamp plus
 */
define(['jquery', './annotation'],
        function ($, Annotation) {

            /********************************
             * CONSTRUCTOR and EXTEND-CLASS *
             ********************************/

            // I return an initialized object.
            function AnnotationStampplus() {
                // Call the super constructor.
                Annotation.call(this);
                // Return this object reference.
                return(this);
            }
            // The Friend class extends the base Model class.
            AnnotationStampplus.prototype = Object.create(Annotation.prototype);

            /*************
             * FUNCTIONS *
             *************/

            /**
             * Init the annotation with demo parameters
             * @param {Tool} currentTool
             */
            AnnotationStampplus.prototype.initAdminDemo = function (currentTool) {
                Annotation.prototype.initAdminDemo.call(this, currentTool);
                this.x = 60;
                this.y = 100;
            };
            /**
             * Draw the annotation
             * @param {JQuery Entity} canevas
             * @returns {Annotation} this annotation
             */
            AnnotationStampplus.prototype.draw = function (canevas) {
                if (canevas) {
                    var divStamp = "<div id='" + this.id + "'></div>";
                    canevas.append(divStamp);
                    $("#" + this.id).css('position', 'relative');
                    $("#" + this.id).css('top', this.y);
                    $("#" + this.id).css('left', this.x);
                    $("#" + this.id).css('color', this.colour);
                    $("#" + this.id).css('border', '2px solid ' + this.colour);
                    $("#" + this.id).css('padding', '0 2px');
                    $("#" + this.id).css('display', 'inline-block');
                    $("#" + this.id).append(this.tooltype.label);
                }
                return this;
            };

            return AnnotationStampplus;
        });