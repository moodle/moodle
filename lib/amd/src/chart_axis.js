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
 * Chart axis.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    /**
     * Chart axis.
     */
    function Axis() {
        // Please eslint no-empty-function.
    }

    Axis.prototype.POS_DEFAULT = null;
    Axis.prototype.POS_BOTTOM = 'bottom';
    Axis.prototype.POS_LEFT = 'left';
    Axis.prototype.POS_RIGHT = 'right';
    Axis.prototype.POS_TOP = 'top';

    Axis.prototype._label = null;
    Axis.prototype._position = null;
    Axis.prototype._stepSize = null;

    Axis.prototype.create = function(obj) {
        var s = new Axis();
        s.setPosition(obj.position);
        s.setLabel(obj.label);
        s.setStepSize(obj.stepSize);
        return s;
    };

    Axis.prototype.getLabel = function() {
        return this._label;
    };

    Axis.prototype.getPosition = function() {
        return this._position;
    };

    Axis.prototype.getStepSize = function() {
        return this._stepSize;
    };

    Axis.prototype.setLabel = function(label) {
        this._label = label || null;
    };

    Axis.prototype.setPosition = function(position) {
        if (position != this.POS_DEFAULT
                && position != this.POS_BOTTOM
                && position != this.POS_LEFT
                && position != this.POS_RIGHT
                && position != this.POS_TOP) {
            throw new Error('Invalid axis position.');
        }
        this._position = position;
    };

    Axis.prototype.setStepSize = function(stepSize) {
        if (typeof stepSize === 'undefined' || stepSize === null) {
            stepSize = null;
        } else if (isNaN(Number(stepSize))) {
            throw new Error('Value for stepSize is not a number.');
        } else {
            stepSize = Number(stepSize);
        }

        this._stepSize = stepSize;
    };

    return Axis;

});
