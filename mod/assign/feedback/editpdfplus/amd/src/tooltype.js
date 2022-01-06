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
 * @module mod_assignfeedback_editpdfplus/tooltype
 * @param {Global} global constantes
 * @returns {ToolType} tooltype object
 */
define(['./global'],
        function (global) {

            /*******************************
             * CONSTRUCTOR and SUPER-CLASS *
             *******************************/

            // I am the internal, static counter for the number of models
            // that have been created in the system. This is used to
            // power the unique identifier of each instance.
            var instanceCount = 0;
            // I get the next instance ID.
            var getNewInstanceID = function () {
                // Precrement the instance count in order to generate the
                // next value instance ID.
                return(++instanceCount);
            };
            // I return an initialized object.
            /**
             * ToolType class.
             *
             * @class ToolType
             */
            function ToolType() {
                // Store the private instance id.
                this._instanceID = getNewInstanceID();
                // Return this object reference.
                return(this);
            }
            // I return the current instance count. I am a static method
            // on the Model class.
            ToolType.getInstanceCount = function () {
                return(instanceCount);
            };
            ToolType.prototype.getInstanceID = function () {
                return(this._instanceID);
            };

            /**************
             * Parameters *
             **************/

            /**
             * Base identifiant
             * @property id
             * @type Int
             */
            ToolType.id = -1;
            /**
             * Code name
             * @property label
             * @type String
             */
            ToolType.label = "";
            /**
             * Default HTML color
             * @property color
             * @type String
             */
            ToolType.color = "";
            /**
             * Default HTML cartridge color
             * @property cartridgeColor
             * @type String
             */
            ToolType.cartridgeColor = "";
            /**
             * Default X position for cartridge
             * @property cartridgeX
             * @type Int
             */
            ToolType.cartridgeX = 0;
            /**
             * Default Y position for cartridge
             * @property cartridgeX
             * @type Int
             */
            ToolType.cartridgeY = 0;
            /**
             * Is the content's cartridge allowed to be configurable
             * @property configurableCartridge
             * @type Boolean
             */
            ToolType.configurableCartridge = 1;
            /**
             * Is the color's cartridge allowed to be configurable
             * @property configurableCartridgeColor
             * @type Boolean
             */
            ToolType.configurableCartridgeColor = 1;
            /**
             * Is the tool's color allowed to be configurable
             * @property configurableColor
             * @type Boolean
             */
            ToolType.configurableColor = 1;
            /**
             * Are the tool's texts allowed to be configurable
             * @property configurableTexts
             * @type Boolean
             */
            ToolType.configurableTexts = 1;
            /**
             * Is the tool's question/qnswer allowed to be configurable
             * @property configurableQuestion
             * @type Boolean
             */
            ToolType.configurableQuestion = 1;

            /*************
             * FUNCTIONS *
             *************/

            /**
             * Initialize tooltype object from an object from database with its base's id
             * @param {object} config
             */
            ToolType.prototype.init = function (config) {
                this.id = parseInt(config.id, 10) || 0;
            };
            /**
             * Initialize tooltype object from an object from database
             * @param {object} config
             */
            ToolType.prototype.initAdmin = function (config) {
                this.id = parseInt(config.id, 10) || 0;
                this.label = config.label;
                this.color = config.color;
                this.cartridgeColor = config.cartridge_color;
                this.cartridgeX = config.cartridge_x;
                this.cartridgeY = config.cartridge_y;
                this.configurableCartridge = config.configurable_cartridge;
                this.configurableCartridgeColor = config.configurable_cartridge_color;
                this.configurableColor = config.configurable_color;
                this.configurableTexts = config.configurable_texts;
                this.configurableQuestion = config.configurable_question;
            };
            /**
             * Get the default color of an annotation
             * @return {string} color
             */
            ToolType.prototype.get_color = function () {
                var color = global.ANNOTATIONCOLOUR[this.color];
                if (!color) {
                    color = this.color;
                } else {
                    // Add an alpha channel to the rgb colour.
                    color = color.replace('rgb', 'rgba');
                    color = color.replace(')', ',0.5)');
                }
                return color;
            };
            /**
             * Get the color for the cartridge
             * @return {string} color
             */
            ToolType.prototype.get_color_cartridge = function () {
                var color = global.ANNOTATIONCOLOUR[this.cartridgeColor];
                if (!color) {
                    color = this.cartridgeColor;
                } else {
                    // Add an alpha channel to the rgb colour.
                    color = color.replace('rgb', 'rgba');
                    color = color.replace(')', ',0.5)');
                }
                return color;
            };

            return ToolType;
        });