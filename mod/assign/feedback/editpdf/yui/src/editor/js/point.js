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
POINT = function(x, y) {

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
