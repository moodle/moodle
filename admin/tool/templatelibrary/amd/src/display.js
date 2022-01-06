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
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/log', 'core/notification', 'core/templates', 'core/config', 'core/str'],
       function($, ajax, log, notification, templates, config, str) {

    /**
     * Search through a template for a template docs comment.
     *
     * @param {String} templateSource The raw template
     * @param {String} templateName The name of the template used to search for docs tag
     * @return {String|boolean} the correct comment or false
     */
    var findDocsSection = function(templateSource, templateName) {

        if (!templateSource) {
            return false;
        }
        // Find the comment section marked with @template component/template.
        var marker = "@template " + templateName,
            i = 0,
            sections = [];

        sections = templateSource.match(/{{!([\s\S]*?)}}/g);

        // If no sections match - show the entire file.
        if (sections !== null) {
            for (i = 0; i < sections.length; i++) {
                var section = sections[i];
                var start = section.indexOf(marker);
                if (start !== -1) {
                    // Remove {{! and }} from start and end.
                    var offset = start + marker.length + 1;
                    section = section.substr(offset, section.length - 2 - offset);
                    return section;
                }
            }
        }
        // No matching comment.
        return false;
    };

    /**
     * Handle a template loaded response.
     *
     * @param {String} templateName The template name
     * @param {String} source The template source
     * @param {String} originalSource The original template source (not theme overridden)
     */
    var templateLoaded = function(templateName, source, originalSource) {
        str.get_string('templateselected', 'tool_templatelibrary', templateName).done(function(s) {
            $('[data-region="displaytemplateheader"]').text(s);
        }).fail(notification.exception);

        // Find the comment section marked with @template component/template.
        var docs = findDocsSection(source, templateName);

        if (docs === false) {
            // Docs was not in theme template, try original.
            docs = findDocsSection(originalSource, templateName);
        }

        // If we found a docs section, limit the template library to showing this section.
        if (docs) {
            source = docs;
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
                templates.replaceNodeContents($('[data-region="displaytemplateexample"]'), html, js);
            }).fail(notification.exception);
        } else {
            str.get_string('templatehasnoexample', 'tool_templatelibrary').done(function(s) {
                $('[data-region="displaytemplateexample"]').text(s);
            }).fail(notification.exception);
        }
    };

    /**
     * Load the a template source from Moodle.
     *
     * @param {String} templateName
     */
    var loadTemplate = function(templateName) {
        var parts = templateName.split('/');
        var component = parts.shift();
        var name = parts.join('/');

        var promises = ajax.call([{
            methodname: 'core_output_load_template',
            args: {
                    component: component,
                    template: name,
                    themename: config.theme,
                    includecomments: true
            }
        }, {
            methodname: 'tool_templatelibrary_load_canonical_template',
            args: {
                    component: component,
                    template: name
            }
        }], true, false);

        // When returns a new promise that is resolved when all the passed in promises are resolved.
        // The arguments to the done become the values of each resolved promise.
        $.when.apply($, promises)
            .done(function(source, originalSource) {
              templateLoaded(templateName, source, originalSource);
            })
            .fail(notification.exception);
    };

    // Add the event listeners.
    $('[data-region="list-templates"]').on('click', '[data-templatename]', function(e) {
        var templatename = $(this).data('templatename');
        e.preventDefault();
        loadTemplate(templatename);
    });

    // This module does not expose anything.
    return {};
});
