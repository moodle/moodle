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
 * This module adds ajax display functions to the template library page.
 *
 * @module     tool_templatelibrary/display
 * @package    tool_templatelibrary
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/log', 'core/notification', 'core/templates', 'core/config', 'core/str'],
       function($, ajax, log, notification, templates, config, str) {

    /**
     * Handle a template loaded response.
     *
     * @param {String} templateName The template name
     * @param {String} source The template source
     */
    var templateLoaded = function(templateName, source) {
        str.get_string('templateselected', 'tool_templatelibrary', templateName).done(function(s) {
            $('[data-region="displaytemplateheader"]').text(s);
        }).fail(notification.exception);

        // Find the comment section marked with @template component/template.
        var marker = "@template " + templateName;

        var sections = source.match(/{{!([\s\S]*?)}}/g);
        var i = 0;

        // If no sections match - show the entire file.
        if (sections !== null) {
            for (i = 0; i < sections.length; i++) {
                var section = sections[i];
                var start = section.indexOf(marker);
                if (start !== -1) {
                    // Remove {{! and }} from start and end.
                    var offset = start + marker.length + 1;
                    section = section.substr(offset, section.length - 2 - offset);
                    source = section;
                    break;
                }
            }
        }

        $('[data-region="displaytemplatesource"]').text(source);

        // Now search the text for a json example.

        var example = source.match(/Example context \(json\):([\s\S]*)/);
        var context = false;
        if (example) {
            var rawJSON = example[1].trim();
            try {
                context = $.parseJSON(rawJSON);
            } catch (e) {
                log.debug('Could not parse json example context for template.');
                log.debug(e);
            }
        }
        if (context) {
            templates.render(templateName, context).done(function(html, js) {
                $('[data-region="displaytemplateexample"]').empty();
                $('[data-region="displaytemplateexample"]').append(html);
                templates.runTemplateJS(js);
            }).fail(notification.exception);
        } else {
            str.get_string('templatehasnoexample', 'tool_templatelibrary').done(function(s) {
                $('[data-region="displaytemplateexample"]').text(s);
            }).fail(notification.exception);
        }
    };

    /**
     * Load the a template source from Moodle.
     * @param {String} templateName
     */
    var loadTemplate = function(templateName) {
        var parts = templateName.split('/');
        var component = parts.shift();
        var name = parts.shift();

        ajax.call([{
            methodname: 'core_output_load_template',
            args:{
                    component: component,
                    template: name,
                    themename: config.theme
            },
            done: function(source) { templateLoaded(templateName, source); },
            fail: notification.exception
        }]);
    };

    // Add the event listeners.
    $('[data-region="list-templates"]').on('click', '[data-templatename]', function() {
        var templatename = $(this).data('templatename');
        loadTemplate(templatename);
    });

    // This module does not expose anything.
    return {};
});
