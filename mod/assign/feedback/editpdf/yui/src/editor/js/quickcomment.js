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
