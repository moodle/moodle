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
 * Competency rule points module.
 *
 * @module     tool_lp/competency_rule_all
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/str',
        'core/templates',
        'tool_lp/competency_rule',
        ],
        function($, Str, Templates, RuleBase) {

    /**
     * Competency rule points class.
     */
    var Rule = function() {
        RuleBase.apply(this, arguments);
    };
    Rule.prototype = Object.create(RuleBase.prototype);

    /** @property {Node} Reference to the container in which the template was included. */
    Rule.prototype._container = null;
    /** @property {Boolean} Whether or not the template was included. */
    Rule.prototype._templateLoaded = false;

    /**
     * The config established by this rule.
     *
     * @return {String}
     * @method getConfig
     */
    Rule.prototype.getConfig = function() {
        return JSON.stringify({
            base: {
                points: this._getRequiredPoints(),
            },
            competencies: this._getCompetenciesConfig()
        });
    };

    /**
     * Gathers the input provided by the user for competencies.
     *
     * @return {Array} Containing id, points and required.
     * @method _getCompetenciesConfig
     * @protected
     */
    Rule.prototype._getCompetenciesConfig = function() {
        var competencies = [];

        this._container.find('[data-competency]').each(function() {
            var node = $(this),
                id = node.data('competency'),
                points = parseInt(node.find('[name="points"]').val(), 10),
                required = node.find('[name="required"]').prop('checked');

            competencies.push({
                id: id,
                points: points,
                required: required ? 1 : 0
            });
        });

        return competencies;
    };

    /**
     * Fetches the required points set by the user.
     *
     * @return {Number}
     * @method _getRequiredPoints
     * @protected
     */
    Rule.prototype._getRequiredPoints = function() {
        return parseInt(this._container.find('[name="requiredpoints"]').val() || 1, 10);
    };

    /**
     * Return the type of the module.
     *
     * @return {String}
     * @method getType
     */
    Rule.prototype.getType = function() {
        return 'core_competency\\competency_rule_points';
    };

    /**
     * Callback to inject the template.
     *
     * @param  {Node} container Node to inject in.
     * @return {Promise} Resolved when done.
     * @method injectTemplate
     */
    Rule.prototype.injectTemplate = function(container) {
        var self = this,
            children = this._tree.getChildren(this._competency.id),
            context,
            config = {
                base: {points: 2},
                competencies: []
            };

        this._templateLoaded = false;

        // Only pre-load the configuration when the competency is using this rule.
        if (self._competency.ruletype == self.getType()) {
            try {
                config = JSON.parse(self._competency.ruleconfig);
            } catch (e) {
                // eslint-disable-line no-empty
            }
        }

        context = {
            requiredpoints: (config && config.base) ? config.base.points : 2,
            competency: self._competency,
            children: []
        };

        $.each(children, function(index, child) {
            var competency = {
                id: child.id,
                shortname: child.shortname,
                required: false,
                points: 0
            };

            if (config) {
                $.each(config.competencies, function(index, comp) {
                    if (comp.id == competency.id) {
                        competency.required = comp.required ? true : false;
                        competency.points = comp.points;
                    }
                });
            }

            context.children.push(competency);
        });

        return Templates.render('tool_lp/competency_rule_points', context).then(function(html) {
            self._container = container;
            container.html(html);
            container.find('input').change(function() {
                self._triggerChange();
            });

            // We're done, let's trigger a change.
            self._templateLoaded = true;
            self._triggerChange();
            return;
        });
    };

    /**
     * Whether or not the current config is valid.
     *
     * @return {Boolean}
     * @method isValid
     */
    Rule.prototype.isValid = function() {
        if (!this._templateLoaded) {
            return false;
        }

        var required = this._getRequiredPoints(),
            max = 0,
            valid = true;

        $.each(this._getCompetenciesConfig(), function(index, competency) {
            if (competency.points < 0) {
                valid = false;
            }
            max += competency.points;
        });

        valid = valid && max >= required;
        return valid;
    };

    return Rule;
});
