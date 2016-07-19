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
         'core/localstorage',
         'core/event',
         'core/yui',
         'core/log'
       ],
       function(mustache, $, ajax, str, notification, coreurl, config, storage, event, Y, Log) {

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
        }], async, false);

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
     * Quote helper used to wrap content in quotes, and escape all quotes present in the content.
     *
     * @method quoteHelper
     * @private
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    var quoteHelper = function(sectionText, helper) {
        var content = helper(sectionText.trim(), this);

        // Escape the {{ and the ".
        // This involves wrapping {{, and }} in change delimeter tags.
        content = content
            .replace('"', '\\"')
            .replace(/([\{\}]{2,3})/g, '{{=<% %>=}}$1<%={{ }}=%>')
            ;
        return '"' + content + '"';
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
        context.quote = function() { return quoteHelper; };
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

        // Re-render to get the final strings.
        return treatStringsInContent(js, strings);
    };

    /**
     * Treat strings in content.
     *
     * The purpose of this method is to replace the placeholders found in a string
     * with the their respective translated strings.
     *
     * Previously we were relying on String.replace() but the complexity increased with
     * the numbers of strings to replace. Now we manually walk the string and stop at each
     * placeholder we find, only then we replace it. Most of the time we will
     * replace all the placeholders in a single run, at times we will need a few
     * more runs when placeholders are replaced with strings that contain placeholders
     * themselves.
     *
     * @param {String} content The content in which string placeholders are to be found.
     * @param {Array} strings The strings to replace with.
     * @return {String} The treated content.
     */
    var treatStringsInContent = function(content, strings) {
        var pattern = /{{_s\d+}}/,
            treated,
            index,
            strIndex,
            walker,
            char,
            strFinal;

        do {
            treated = '';
            index = content.search(pattern);
            while (index > -1) {

                // Copy the part prior to the placeholder to the treated string.
                treated += content.substring(0, index);
                content = content.substr(index);
                strIndex = '';
                walker = 4;  // 4 is the length of '{{_s'.

                // Walk the characters to manually extract the index of the string from the placeholder.
                char = content.substr(walker, 1);
                do {
                    strIndex += char;
                    walker++;
                    char = content.substr(walker, 1);
                } while (char != '}');

                // Get the string, add it to the treated result, and remove the placeholder from the content to treat.
                strFinal = strings[parseInt(strIndex, 10)];
                if (typeof strFinal === 'undefined') {
                    Log.debug('Could not find string for pattern {{_s' + strIndex + '}}.');
                    strFinal = '';
                }
                treated += strFinal;
                content = content.substr(6 + strIndex.length);  // 6 is the length of the placeholder without the index: '{{_s}}'.

                // Find the next placeholder.
                index = content.search(pattern);
            }

            // The content becomes the treated part with the rest of the content.
            content = treated + content;

            // Check if we need to walk the content again, in case strings contained placeholders.
            index = content.search(pattern);

        } while (index > -1);

        return content;
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
                    str.get_strings(requiredStrings)
                    .then(function(strings) {

                        // Why do we not do another call the render here?
                        //
                        // Because that would expose DOS holes. E.g.
                        // I create an assignment called "{{fish" which
                        // would get inserted in the template in the first pass
                        // and cause the template to die on the second pass (unbalanced).

                        result = treatStringsInContent(result, strings);
                        deferred.resolve(result, getJS(strings));
                    })
                    .fail(deferred.reject);
                } else {
                    deferred.resolve(result.trim(), getJS([]));
                }
            }
        ).fail(deferred.reject);
        return deferred.promise();
    };

    /**
     * Execute a block of JS returned from a template.
     * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
     *
     * @method runTemplateJS
     * @param {string} source - A block of javascript.
     */
    var runTemplateJS = function(source) {
        if (source.trim() !== '') {
            var newscript = $('<script>').attr('type','text/javascript').html(source);
            $('head').append(newscript);
        }
    };

    /**
     * Do some DOM replacement and trigger correct events and fire javascript.
     *
     * @method domReplace
     * @private
     * @param {JQuery} element - Element or selector to replace.
     * @param {String} newHTML - HTML to insert / replace.
     * @param {String} newJS - Javascript to run after the insertion.
     * @param {Boolean} replaceChildNodes - Replace only the childnodes, alternative is to replace the entire node.
     */
    var domReplace = function(element, newHTML, newJS, replaceChildNodes) {
        var replaceNode = $(element);
        if (replaceNode.length) {
            // First create the dom nodes so we have a reference to them.
            var newNodes = $(newHTML);
            var yuiNodes = null;
            // Do the replacement in the page.
            if (replaceChildNodes) {
                // Cleanup any YUI event listeners attached to any of these nodes.
                yuiNodes = new Y.NodeList(replaceNode.children().get());
                yuiNodes.destroy(true);

                // JQuery will cleanup after itself.
                replaceNode.empty();
                replaceNode.append(newNodes);
            } else {
                // Cleanup any YUI event listeners attached to any of these nodes.
                yuiNodes = new Y.NodeList(replaceNode.get());
                yuiNodes.destroy(true);

                // JQuery will cleanup after itself.
                replaceNode.replaceWith(newNodes);
            }
            // Run any javascript associated with the new HTML.
            runTemplateJS(newJS);
            // Notify all filters about the new content.
            event.notifyFilterContentUpdated(newNodes);
        }
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
         * @param {string} source - A block of javascript.
         */
        runTemplateJS: runTemplateJS,

        /**
         * Replace a node in the page with some new HTML and run the JS.
         *
         * @method replaceNodeContents
         * @param {string} source - A block of javascript.
         */
        replaceNodeContents: function(element, newHTML, newJS) {
            return domReplace(element, newHTML, newJS, true);
        },

        /**
         * Insert a node in the page with some new HTML and run the JS.
         *
         * @method replaceNode
         * @param {string} source - A block of javascript.
         */
        replaceNode: function(element, newHTML, newJS) {
            return domReplace(element, newHTML, newJS, false);
        }
    };
});
