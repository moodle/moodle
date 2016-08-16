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
