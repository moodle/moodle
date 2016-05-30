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
 * Change the course competency settings in a popup.
 *
 * @module     tool_lp/configurecoursecompetencysettings
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'tool_lp/dialogue',
        'core/str',
        'core/ajax',
        'core/templates'],
       function($, notification, Dialogue, str, ajax, templates) {

    /**
     * Constructor
     *
     * @param {String} selector - selector for the links to open the dialogue.
     */
    var settingsMod = function(selector) {
        $(selector).on('click', this.configureSettings.bind(this));
    };

    /** @type {Dialogue} Reference to the dialogue that we opened. */
    settingsMod.prototype._dialogue = null;

    /**
     * Open the configure settings dialogue.
     *
     * @param {Event} e
     * @method configureSettings
     */
    settingsMod.prototype.configureSettings = function(e) {
        var courseid = $(e.target).closest('a').data('courseid');
        var currentValue = $(e.target).closest('a').data('pushratingstouserplans');
        var context = {
            courseid: courseid,
            settings: { pushratingstouserplans: currentValue }
        };
        e.preventDefault();

        templates.render('tool_lp/course_competency_settings', context).done(function(html) {
            str.get_string('configurecoursecompetencysettings', 'tool_lp').done(function (title) {
                this._dialogue = new Dialogue(
                    title,
                    html,
                    this.addListeners.bind(this)
                );
            }.bind(this)).fail(notification.exception);
        }.bind(this)).fail(notification.exception);

    };

    /**
     * Add the save listener to the form.
     *
     * @method addSaveListener
     */
    settingsMod.prototype.addListeners = function() {
        var save = this._find('[data-action="save"]');
        save.on('click', this.saveSettings.bind(this));
        var cancel = this._find('[data-action="cancel"]');
        cancel.on('click', this.cancelChanges.bind(this));
    };

    /**
     * Cancel the changes.
     *
     * @param {Event} e
     * @method cancelChanges
     */
    settingsMod.prototype.cancelChanges = function(e) {
        e.preventDefault();
        this._dialogue.close();
    };

    /**
     * Cancel the changes.
     *
     * @param {String} selector
     * @return {JQuery}
     */
    settingsMod.prototype._find = function(selector) {
        return $('[data-region="coursecompetencysettings"]').find(selector);
    };

    /**
     * Save the settings.
     *
     * @param {Event} e
     * @method saveSettings
     */
    settingsMod.prototype.saveSettings = function(e) {
        e.preventDefault();

        var newValue = this._find('input[name="pushratingstouserplans"]:checked').val();
        var courseId = this._find('input[name="courseid"]').val();
        var settings = { pushratingstouserplans: newValue };

        ajax.call([
            { methodname: 'core_competency_update_course_competency_settings',
              args: { courseid: courseId, settings: settings } }
        ])[0].done(function() {
            this.refreshCourseCompetenciesPage();
        }.bind(this)).fail(notification.exception);

    };

    /**
     * Refresh the course competencies page.
     *
     * @param {Event} e
     * @method saveSettings
     */
    settingsMod.prototype.refreshCourseCompetenciesPage = function() {
        var courseId = this._find('input[name="courseid"]').val();

        ajax.call([
            { methodname: 'tool_lp_data_for_course_competencies_page',
              args: { courseid: courseId } }
        ])[0].done(function(context) {
            templates.render('tool_lp/course_competencies_page', context).done(function(html, js) {
                $('[data-region="coursecompetenciespage"]').replaceWith(html);
                templates.runTemplateJS(js);
                this._dialogue.close();
            }.bind(this)).fail(notification.exception);
        }.bind(this)).fail(notification.exception);

    };

    return /** @alias module:tool_lp/configurecoursecompetencysettings */ settingsMod;
});
