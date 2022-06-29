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
 * Competency rule all module.
 *
 * @module     tool_lp/competency_rule_all
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/str',
        'tool_lp/competency_rule',
        ],
        function($, Str, RuleBase) {

    /**
     * Competency rule all class.
     *
     * @class tool_lp/competency_rule_all
     */
    var Rule = function() {
        RuleBase.apply(this, arguments);
    };
    Rule.prototype = Object.create(RuleBase.prototype);

    /**
     * Return the type of the module.
     *
     * @return {String}
     * @method getType
     */
    Rule.prototype.getType = function() {
        return 'core_competency\\competency_rule_all';
    };

    /**
     * Whether or not the current config is valid.
     *
     * @return {Boolean}
     * @method isValid
     */
    Rule.prototype.isValid = function() {
        return true;
    };

    return Rule;
});
