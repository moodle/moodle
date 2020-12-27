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
 * Competency rule config.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/templates',
        'tool_lp/dialogue',
        'tool_lp/competency_outcomes',
        'core/str'],
        function($, Notification, Templates, Dialogue, Outcomes, Str) {

    /**
     * Competency rule class.
     *
     * When implementing this you should attach a listener to the event 'save'
     * on the instance. E.g.
     *
     * var config = new RuleConfig(tree, modules);
     * config.on('save', function(e, config) { ... });
     *
     * @param {competencytree} tree The competency tree.
     * @param {Array} rulesModules The modules containing the rules: [{ typeName: { amd: amdModule, name: ruleName }}].
     */
    var RuleConfig = function(tree, rulesModules) {
        this._eventNode = $('<div></div>');
        this._tree = tree;
        this._rulesModules = rulesModules;
        this._setUp();
    };

    /** @type {Object} The current competency. */
    RuleConfig.prototype._competency = null;
    /** @type {Node} The node we attach the events to. */
    RuleConfig.prototype._eventNode = null;
    /** @type {Array} Outcomes options. */
    RuleConfig.prototype._outcomesOption = null;
    /** @type {Dialogue} The dialogue. */
    RuleConfig.prototype._popup = null;
    /** @type {Promise} Resolved when the module is ready. */
    RuleConfig.prototype._ready = null;
    /** @type {Array} The rules. */
    RuleConfig.prototype._rules = null;
    /** @type {Array} The rules modules. */
    RuleConfig.prototype._rulesModules = null;
    /** @type {competencytree} The competency tree. */
    RuleConfig.prototype._tree = null;

    /**
     * After change.
     *
     * Triggered when a change occured.
     *
     * @method _afterChange
     * @protected
     */
    RuleConfig.prototype._afterChange = function() {
        if (!this._isValid()) {
            this._find('[data-action="save"]').prop('disabled', true);
        } else {
            this._find('[data-action="save"]').prop('disabled', false);
        }
    };

    /**
     * After change in rule's config.
     *
     * Triggered when a change occured in a specific rule config.
     *
     * @method _afterRuleConfigChange
     * @protected
     * @param {Event} e
     * @param {Rule} rule
     */
    RuleConfig.prototype._afterRuleConfigChange = function(e, rule) {
        if (rule != this._getRule()) {
            // This rule is not the current one any more, we can ignore.
            return;
        }
        this._afterChange();
    };

    /**
     * After render hook.
     *
     * @method _afterRender
     * @protected
     */
    RuleConfig.prototype._afterRender = function() {
        var self = this;

        self._find('[name="outcome"]').on('change', function() {
            self._switchedOutcome();
        }).trigger('change');

        self._find('[name="rule"]').on('change', function() {
            self._switchedRule();
        }).trigger('change');

        self._find('[data-action="save"]').on('click', function() {
            self._trigger('save', self._getConfig());
            self.close();
        });

        self._find('[data-action="cancel"]').on('click', function() {
            self.close();
        });
    };

    /**
     * Whether the current competency can be configured.
     *
     * @return {Boolean}
     * @method canBeConfigured
     */
    RuleConfig.prototype.canBeConfigured = function() {
        var can = false;
        $.each(this._rules, function(index, rule) {
            if (rule.canConfig()) {
                can = true;
                return;
            }
        });
        return can;
    };

    /**
     * Close the dialogue.
     *
     * @method close
     */
    RuleConfig.prototype.close = function() {
        this._popup.close();
        this._popup = null;
    };

    /**
     * Opens the picker.
     *
     * @param {Number} competencyId The competency ID of the competency to work on.
     * @method display
     * @return {Promise}
     */
    RuleConfig.prototype.display = function() {
        var self = this;
        if (!self._competency) {
            return false;
        }
        return $.when(Str.get_string('competencyrule', 'tool_lp'), self._render())
        .then(function(title, render) {
            self._popup = new Dialogue(
                title,
                render[0],
                self._afterRender.bind(self)
            );
            return;
        }).fail(Notification.exception);
    };

    /**
     * Find a node in the dialogue.
     *
     * @param {String} selector
     * @return {JQuery}
     * @method _find
     * @protected
     */
    RuleConfig.prototype._find = function(selector) {
        return $(this._popup.getContent()).find(selector);
    };

    /**
     * Get the applicable outcome options.
     *
     * @return {Array}
     * @method _getApplicableOutcomesOptions
     * @protected
     */
    RuleConfig.prototype._getApplicableOutcomesOptions = function() {
        var self = this,
            options = [];

        $.each(self._outcomesOption, function(index, outcome) {
            options.push({
                code: outcome.code,
                name: outcome.name,
                selected: (outcome.code == self._competency.ruleoutcome) ? true : false,
            });
        });

        return options;
    };

    /**
     * Get the applicable rules options.
     *
     * @return {Array}
     * @method _getApplicableRulesOptions
     * @protected
     */
    RuleConfig.prototype._getApplicableRulesOptions = function() {
        var self = this,
            options = [];

        $.each(self._rules, function(index, rule) {
            if (!rule.canConfig()) {
                return;
            }
            options.push({
                name: self._getRuleName(rule.getType()),
                type: rule.getType(),
                selected: (rule.getType() == self._competency.ruletype) ? true : false,
            });
        });

        return options;
    };

    /**
     * Get the full config for the competency.
     *
     * @return {Object} Contains rule, ruleoutcome and ruleconfig.
     * @method _getConfig
     * @protected
     */
    RuleConfig.prototype._getConfig = function() {
        var rule = this._getRule();
        return {
            ruletype: rule ? rule.getType() : null,
            ruleconfig: rule ? rule.getConfig() : null,
            ruleoutcome: this._getOutcome()
        };
    };

    /**
     * Get the selected outcome code.
     *
     * @return {String}
     * @method _getOutcome
     * @protected
     */
    RuleConfig.prototype._getOutcome = function() {
        return this._find('[name="outcome"]').val();
    };

    /**
     * Get the selected rule.
     *
     * @return {null|Rule}
     * @method _getRule
     * @protected
     */
    RuleConfig.prototype._getRule = function() {
        var result,
            type = this._find('[name="rule"]').val();

        $.each(this._rules, function(index, rule) {
            if (rule.getType() == type) {
                result = rule;
                return;
            }
        });

        return result;
    };

    /**
     * Return the name of a rule.
     *
     * @param  {String} type The type of a rule.
     * @return {String}
     * @method _getRuleName
     * @protected
     */
    RuleConfig.prototype._getRuleName = function(type) {
        var self = this,
            name;
        $.each(self._rulesModules, function(index, modInfo) {
            if (modInfo.type == type) {
                name = modInfo.name;
                return;
            }
        });
        return name;
    };

    /**
     * Initialise the outcomes.
     *
     * @return {Promise}
     * @method _initOutcomes
     * @protected
     */
    RuleConfig.prototype._initOutcomes = function() {
        var self = this;
        return Outcomes.getAll().then(function(outcomes) {
            self._outcomesOption = outcomes;
            return;
        });
    };

    /**
     * Initialise the rules.
     *
     * @return {Promise}
     * @method _initRules
     * @protected
     */
    RuleConfig.prototype._initRules = function() {
        var self = this,
            promises = [];
        $.each(self._rules, function(index, rule) {
            var promise = rule.init().then(function() {
                rule.setTargetCompetency(self._competency);
                rule.on('change', self._afterRuleConfigChange.bind(self));
                return;
            }, function() {
                // Upon failure remove the rule, and resolve the promise.
                self._rules.splice(index, 1);
                return $.when();
            });
            promises.push(promise);
        });

        return $.when.apply($.when, promises);
    };

    /**
     * Whether or not the current config is valid.
     *
     * @return {Boolean}
     * @method _isValid
     * @protected
     */
    RuleConfig.prototype._isValid = function() {
        var outcome = this._getOutcome(),
            rule = this._getRule();

        if (outcome == Outcomes.NONE) {
            return true;
        } else if (!rule) {
            return false;
        }

        return rule.isValid();
    };

    /**
     * Register an event listener.
     *
     * @param {String} type The event type.
     * @param {Function} handler The event listener.
     * @method on
     */
    RuleConfig.prototype.on = function(type, handler) {
        this._eventNode.on(type, handler);
    };

    /**
     * Hook to executed before render.
     *
     * @method _preRender
     * @protected
     * @return {Promise}
     */
    RuleConfig.prototype._preRender = function() {
        // We need to have all the information about the rule plugins first.
        return this.ready();
    };

    /**
     * Returns a promise that is resolved when the module is ready.
     *
     * @return {Promise}
     * @method ready
     * @protected
     */
    RuleConfig.prototype.ready = function() {
        return this._ready.promise();
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @protected
     * @return {Promise}
     */
    RuleConfig.prototype._render = function() {
        var self = this;
        return this._preRender().then(function() {
            var config;

            if (!self.canBeConfigured()) {
                config = false;
            } else {
                config = {};
                config.outcomes = self._getApplicableOutcomesOptions();
                config.rules = self._getApplicableRulesOptions();
            }

            var context = {
                competencyshortname: self._competency.shortname,
                config: config
            };

            return Templates.render('tool_lp/competency_rule_config', context);
        });
    };

    /**
     * Set the target competency.
     *
     * @param {Number} competencyId The target competency Id.
     * @method setTargetCompetencyId
     */
    RuleConfig.prototype.setTargetCompetencyId = function(competencyId) {
        var self = this;
        self._competency = self._tree.getCompetency(competencyId);
        $.each(self._rules, function(index, rule) {
            rule.setTargetCompetency(self._competency);
        });
    };

    /**
     * Set up the instance.
     *
     * @method _setUp
     * @protected
     */
    RuleConfig.prototype._setUp = function() {
        var self = this,
            promises = [],
            modules = [];

        self._ready = $.Deferred();
        self._rules = [];

        $.each(self._rulesModules, function(index, rule) {
            modules.push(rule.amd);
        });

        // Load all the modules.
        require(modules, function() {
            $.each(arguments, function(index, Module) {
                // Instantiate the rule and listen to it.
                var rule = new Module(self._tree);
                self._rules.push(rule);
            });

            // Load all the option values.
            promises.push(self._initRules());
            promises.push(self._initOutcomes());

            // Ready when everything is done.
            $.when.apply($.when, promises).always(function() {
                self._ready.resolve();
            });
        });
    };

    /**
     * Called when the user switches outcome.
     *
     * @method _switchedOutcome
     * @protected
     */
    RuleConfig.prototype._switchedOutcome = function() {
        var self = this,
            type = self._getOutcome();

        if (type == Outcomes.NONE) {
            // Reset to defaults.
            self._find('[data-region="rule-type"]').hide()
                .find('[name="rule"]').val(-1);
            self._find('[data-region="rule-config"]').empty().hide();
            self._afterChange();
            return;
        }

        self._find('[data-region="rule-type"]').show();
        self._find('[data-region="rule-config"]').show();
        self._afterChange();
    };

    /**
     * Called when the user switches rule.
     *
     * @method _switchedRule
     * @protected
     */
    RuleConfig.prototype._switchedRule = function() {
        var self = this,
            container = self._find('[data-region="rule-config"]'),
            rule = self._getRule();

        if (!rule) {
            container.empty().hide();
            self._afterChange();
            return;
        }
        rule.injectTemplate(container).then(function() {
            container.show();
            return;
        }).always(function() {
            self._afterChange();
        }).catch(function() {
            container.empty().hide();
        });
    };

    /**
     * Trigger an event.
     *
     * @param {String} type The type of event.
     * @param {Object} data The data to pass to the listeners.
     * @method _trigger
     * @protected
     */
    RuleConfig.prototype._trigger = function(type, data) {
        this._eventNode.trigger(type, [data]);
    };

    return /** @alias module:tool_lp/competencyruleconfig */ RuleConfig;

});
