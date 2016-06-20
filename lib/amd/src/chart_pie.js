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
 * Chart pie.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/chart_base'], function(Base) {

    /**
     * Pie chart.
     */
    function Pie() {
        Base.prototype.constructor.apply(this, arguments);
    }
    Pie.prototype = Object.create(Base.prototype);

    Pie.prototype.TYPE = 'pie';

    Pie.prototype._validateSerie = function() {
        if (this._series.length >= 1) {
            throw new Error('Pie charts only support one serie.');
        }
        return Base.prototype._validateSerie.apply(this, arguments);
    };

    return Pie;

});
