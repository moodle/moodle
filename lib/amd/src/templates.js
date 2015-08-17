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
 * Template renderer for Moodle. Load and render Moodle templates with Mustache.
 *
 * @module     core/templates
 * @package    core
 * @class      templates
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define([ 'core/mustache',
         'jquery',
         'core/ajax',
         'core/str',
         'core/notification',
         'core/url',
         'core/config',
         'core/localstorage'
       ],
       function(mustache, $, ajax, str, notification, coreurl, config, storage) {

    // Private variables and functions.

    /** @var {string[]} templateCache - Cache of already loaded templates */
    var templateCache = {};

    /** @var {string[]} requiredStrings - Collection of strings found during the rendering of one template */
    var requiredStrings = [];

    /** @var {string[]} requiredJS - Collection of js blocks found during the rendering of one template */
    var requiredJS = [];

    /** @var {Number} uniqid Incrementing value that is changed for every call to render */
    var uniqid = 1;

    /** @var {String} themeName for the current render */
    var currentThemeName = '';

    /**
     * Render image icons.
     *
     * @method pixHelper
     * @private
     * @param {string} sectionText The text to parse arguments from.
     * @param {function} helper Used to render the alt attribute of the text.
     * @return {string}
     */
    var pixHelper = function(sectionText, helper) {
        var parts = sectionText.split(',');
        var key = '';
        var component = '';
        var text = '';
        var result;

        if (parts.length > 0) {
            key = parts.shift().trim();
        }
        if (parts.length > 0) {
            component = parts.shift().trim();
        }
        if (parts.length > 0) {
            text = parts.join(',').trim();
        }
        var url = coreurl.imageUrl(key, component);

        var templatecontext = {
            attributes: [
                { name: 'src', value: url},
                { name: 'alt', value: helper(text)},
                { name: 'class', value: 'smallicon'}
            ]
        };
        // We forced loading of this early, so it will be in the cache.
        var template = templateCache[currentThemeName + '/core/pix_icon'];
        result = mustache.render(template, templatecontext, partialHelper);
        return result.trim();
    };

    /**
     * Load a partial from the cache or ajax.
     *
     * @method partialHelper
     * @private
     * @param {string} name The partial name to load.
     * @return {string}
     */
    var partialHelper = function(name) {
        var template = '';

        getTemplate(name, false).done(
            function(source) {
                template = source;
            }
        ).fail(notification.exception);

        return template;
    };

    /**
     * Render blocks of javascript and save them in an array.
     *
     * @method jsHelper
     * @private
     * @param {string} sectionText The text to save as a js block.
     * @param {function} helper Used to render the block.
     * @return {string}
     */
    var jsHelper = function(sectionText, helper) {
        requiredJS.push(helper(sectionText, this));
        return '';
    };

    /**
     * String helper used to render {{#str}}abd component { a : 'fish'}{{/str}}
     * into a get_string call.
     *
     * @method stringHelper
     * @private
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    var stringHelper = function(sectionText, helper) {
        var parts = sectionText.split(',');
        var key = '';
        var component = '';
        var param = '';
        if (parts.length > 0) {
            key = parts.shift().trim();
        }
        if (parts.length > 0) {
            component = parts.shift().trim();
        }
        if (parts.length > 0) {
            param = parts.join(',').trim();
        }

        if (param !== '') {
            // Allow variable expansion in the param part only.
            param = helper(param, this);
        }
        // Allow json formatted $a arguments.
        if ((param.indexOf('{') === 0) && (param.indexOf('{{') !== 0)) {
            param = JSON.parse(param);
        }

        var index = requiredStrings.length;
        requiredStrings.push({key: key, component: component, param: param});
        return '{{_s' + index + '}}';
    };

    /**
     * Add some common helper functions to all context objects passed to templates.
     * These helpers match exactly the helpers available in php.
     *
     * @method addHelpers
     * @private
     * @param {Object} context Simple types used as the context for the template.
     * @param {String} themeName We set this multiple times, because there are async calls.
     */
    var addHelpers = function(context, themeName) {
        currentThemeName = themeName;
        requiredStrings = [];
        requiredJS = [];
        context.uniqid = uniqid++;
        context.str = function() { return stringHelper; };
        context.pix = function() { return pixHelper; };
        context.js = function() { return jsHelper; };
        context.globals = { config : config };
        context.currentTheme = themeName;
    };

    /**
     * Get all the JS blocks from the last rendered template.
     *
     * @method getJS
     * @private
     * @param {string[]} strings Replacement strings.
     * @return {string}
     */
    var getJS = function(strings) {
        var js = '';
        if (requiredJS.length > 0) {
            js = requiredJS.join(";\n");
        }

        var i = 0;

        for (i = 0; i < strings.length; i++) {
            js = js.replace('{{_s' + i + '}}', strings[i]);
        }
        // Re-render to get the final strings.
        return js;
    };

    /**
     * Render a template and then call the callback with the result.
     *
     * @method doRender
     * @private
     * @param {string} templateSource The mustache template to render.
     * @param {Object} context Simple types used as the context for the template.
     * @param {String} themeName Name of the current theme.
     * @return {Promise} object
     */
    var doRender = function(templateSource, context, themeName) {
        var deferred = $.Deferred();

        currentThemeName = themeName;

        // Make sure we fetch this first.
        var loadPixTemplate = getTemplate('core/pix_icon', true);

        loadPixTemplate.done(
            function() {
                addHelpers(context, themeName);
                var result = '';
                try {
                    result = mustache.render(templateSource, context, partialHelper);
                } catch (ex) {
                    deferred.reject(ex);
                }

                if (requiredStrings.length > 0) {
                    str.get_strings(requiredStrings).done(
                        function(strings) {
                            var i;

                            // Why do we not do another call the render here?
                            //
                            // Because that would expose DOS holes. E.g.
                            // I create an assignment called "{{fish" which
                            // would get inserted in the template in the first pass
                            // and cause the template to die on the second pass (unbalanced).
                            for (i = 0; i < strings.length; i++) {
                                result = result.replace('{{_s' + i + '}}', strings[i]);
                            }
                            deferred.resolve(result.trim(), getJS(strings));
                        }
                    ).fail(
                        function(ex) {
                            deferred.reject(ex);
                        }
                    );
                } else {
                    deferred.resolve(result.trim(), getJS([]));
                }
            }
        ).fail(
            function(ex) {
                deferred.reject(ex);
            }
        );
        return deferred.promise();
    };

    /**
     * Load a template from the cache or local storage or ajax request.
     *
     * @method getTemplate
     * @private
     * @param {string} templateName - should consist of the component and the name of the template like this:
     *                              core/menu (lib/templates/menu.mustache) or
     *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
     * @return {Promise} JQuery promise object resolved when the template has been fetched.
     */
    var getTemplate = function(templateName, async) {
        var deferred = $.Deferred();
        var parts = templateName.split('/');
        var component = parts.shift();
        var name = parts.shift();

        var searchKey = currentThemeName + '/' + templateName;

        // First try request variables.
        if (searchKey in templateCache) {
            deferred.resolve(templateCache[searchKey]);
            return deferred.promise();
        }

        // Now try local storage.
        var cached = storage.get('core_template/' + searchKey);

        if (cached) {
            deferred.resolve(cached);
            templateCache[searchKey] = cached;
            return deferred.promise();
        }

        // Oh well - load via ajax.
        var promises = ajax.call([{
            methodname: 'core_output_load_template',
            args:{
                component: component,
                template: name,
                themename: currentThemeName
            }
        }], async);

        promises[0].done(
            function (templateSource) {
                storage.set('core_template/' + searchKey, templateSource);
                templateCache[searchKey] = templateSource;
                deferred.resolve(templateSource);
            }
        ).fail(
            function (ex) {
                deferred.reject(ex);
            }
        );
        return deferred.promise();
    };

    return /** @alias module:core/templates */ {
        // Public variables and functions.
        /**
         * Load a template and call doRender on it.
         *
         * @method render
         * @private
         * @param {string} templateName - should consist of the component and the name of the template like this:
         *                              core/menu (lib/templates/menu.mustache) or
         *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
         * @param {Object} context - Could be array, string or simple value for the context of the template.
         * @param {string} themeName - Name of the current theme.
         * @return {Promise} JQuery promise object resolved when the template has been rendered.
         */
        render: function(templateName, context, themeName) {
            var deferred = $.Deferred();

            if (typeof (themeName) === "undefined") {
                // System context by default.
                themeName = config.theme;
            }

            currentThemeName = themeName;

            var loadTemplate = getTemplate(templateName, true);

            loadTemplate.done(
                function(templateSource) {
                    var renderPromise = doRender(templateSource, context, themeName);

                    renderPromise.done(
                        function(result, js) {
                            deferred.resolve(result, js);
                        }
                    ).fail(
                        function(ex) {
                            deferred.reject(ex);
                        }
                    );
                }
            ).fail(
                function(ex) {
                    deferred.reject(ex);
                }
            );
            return deferred.promise();
        },

        /**
         * Execute a block of JS returned from a template.
         * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
         *
         * @method runTemplateJS
         * @private
         * @param {string} source - A block of javascript.
         */
        runTemplateJS: function(source) {
            var newscript = $('<script>').attr('type','text/javascript').html(source);
            $('head').append(newscript);
        }
    };
});
