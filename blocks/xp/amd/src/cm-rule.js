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
 * Course module rule.
 *
 * @author     Frédéric Massart <fred@branchup.tech>
 * @copyright  2018 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/str', 'block_xp/dialogue-base'], function($, Templates, Str, DialogueBase) {
    var SELECTOR_WRAPPER = '.block-xp-filters';
    var SELECTOR_WIDGET = '.block_xp-cm-rule-widget';
    var SELECTOR_WIDGET_TRIGGER = '.block_xp-cm-rule-widget button';
    var SELECTOR_RESOURCE_SELECTOR_WRAPPER = '.block_xp-cm-selector-widget';

    /**
     * The dialogue.
     *
     * @param {Object} [initWithCourse] The course to initialise with.
     */
    function Dialogue(initWithCourse) {
        this.initWithCourse = initWithCourse || null;
        DialogueBase.prototype.constructor.apply(this, []);
    }
    Dialogue.prototype = Object.create(DialogueBase.prototype);
    Dialogue.prototype.constructor = Dialogue;

    /**
     * Render.
     *
     * @return {Promise} The promise.
     */
    Dialogue.prototype._render = function() {
        var initWithCourseJson = JSON.stringify(this.initWithCourse);
        return Str.get_string('cmselector', 'block_xp').then(
            function(title) {
                return Templates.render('block_xp/cm-selector', {
                    initwithcoursejson: initWithCourseJson
                }).then(
                    function(html, js) {
                        this.setTitle(title);
                        this._setDialogueContent(html);
                        Templates.runTemplateJS(js);
                        this.center();
                        this.find(SELECTOR_RESOURCE_SELECTOR_WRAPPER).on(
                            'cm-selected',
                            function(e, resource) {
                                this.trigger('cm-selected', resource);
                                this.close();
                            }.bind(this)
                        );
                    }.bind(this)
                );
            }.bind(this)
        );
    };

    /**
     * Initialise the widgets.
     *
     * @param {Object} [initWithCourse] The course to initialise with.
     */
    function init(initWithCourse) {
        $(SELECTOR_WRAPPER).on('click', SELECTOR_WIDGET_TRIGGER, function(e) {
            e.preventDefault();
            var node = $(e.target).closest(SELECTOR_WIDGET);
            if (!node) {
                return;
            }

            var d = new Dialogue(initWithCourse);
            d.on('cm-selected', function(e, resource) {
                var cm = resource.cm;
                var course = resource.course;
                node.find('.cm-rule-contextid').val(cm.contextid);
                node.find('.cm-selected').text(
                    M.util.get_string('rulecmdescwithcourse', 'block_xp', {
                        contextname: cm.name,
                        coursename: course.shortname
                    })
                );
                node.addClass('has-cm');
            });
            d.show();
        });
    }

    return {
        init: init
    };
});
