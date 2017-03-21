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
         'core/log',
         'core/config',
         'core/localstorage',
         'core/event',
         'core/yui',
         'core/log'
       ],
       function(mustache, $, ajax, str, notification, coreurl, log, config, storage, event, Y, Log) {

    // Module variables.
    /** @var {Number} uniqInstances Count of times this constructor has been called. */
    var uniqInstances = 0;

    /** @var {String[]} templateCache - Cache of already loaded template strings */
    var templateCache = {};

    /** @var {Promise[]} templatePromises - Cache of already loaded template promises */
    var templatePromises = {};

    /**
     * Constructor
     *
     * Each call to templates.render gets it's own instance of this class.
     */
    var Renderer = function() {
        this.requiredStrings = [];
        this.requiredJS = [];
        this.currentThemeName = '';
    };
    // Class variables and functions.

    /** @var {string[]} requiredStrings - Collection of strings found during the rendering of one template */
    Renderer.prototype.requiredStrings = null;

    /** @var {string[]} requiredJS - Collection of js blocks found during the rendering of one template */
    Renderer.prototype.requiredJS = null;

    /** @var {String} themeName for the current render */
    Renderer.prototype.currentThemeName = '';

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
    Renderer.prototype.getTemplate = function(templateName) {
        var parts = templateName.split('/');
        var component = parts.shift();
        var name = parts.shift();

        var searchKey = this.currentThemeName + '/' + templateName;

        // First try request variables.
        if (searchKey in templatePromises) {
            return templatePromises[searchKey];
        }

        // Now try local storage.
        var cached = storage.get('core_template/' + searchKey);

        if (cached) {
            templateCache[searchKey] = cached;
            templatePromises[searchKey] = $.Deferred().resolve(cached).promise();
            return templatePromises[searchKey];
        }

        // Oh well - load via ajax.
        var promises = ajax.call([{
            methodname: 'core_output_load_template',
            args:{
                component: component,
                template: name,
                themename: this.currentThemeName
            }
        }], true, false);

        templatePromises[searchKey] = promises[0].then(
            function(templateSource) {
                templateCache[searchKey] = templateSource;
                storage.set('core_template/' + searchKey, templateSource);
                return templateSource;
            }
        );
        return templatePromises[searchKey];
    };

    /**
     * Load a partial from the cache or ajax.
     *
     * @method partialHelper
     * @private
     * @param {string} name The partial name to load.
     * @return {string}
     */
    Renderer.prototype.partialHelper = function(name) {

        var searchKey = this.currentThemeName + '/' + name;

        if (!(searchKey in templateCache)) {
            notification.exception(new Error('Failed to pre-fetch the template: ' + name));
        }

        return templateCache[searchKey];
    };

    /**
     * Render image icons.
     *
     * @method pixHelper
     * @private
     * @param {object} context The mustache context
     * @param {string} sectionText The text to parse arguments from.
     * @param {function} helper Used to render the alt attribute of the text.
     * @return {string}
     */
    Renderer.prototype.pixHelper = function(context, sectionText, helper) {
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
                { name: 'title', value: helper(text)},
                { name: 'class', value: 'smallicon'}
            ]
        };
        // We forced loading of this early, so it will be in the cache.
        var searchKey = this.currentThemeName + '/core/pix_icon';
        var template = templateCache[searchKey];
        result = mustache.render(template, templatecontext, this.partialHelper.bind(this));
        return result.trim();
    };

    /**
     * Render blocks of javascript and save them in an array.
     *
     * @method jsHelper
     * @private
     * @param {object} context The current mustache context.
     * @param {string} sectionText The text to save as a js block.
     * @param {function} helper Used to render the block.
     * @return {string}
     */
    Renderer.prototype.jsHelper = function(context, sectionText, helper) {
        this.requiredJS.push(helper(sectionText, context));
        return '';
    };

    /**
     * String helper used to render {{#str}}abd component { a : 'fish'}{{/str}}
     * into a get_string call.
     *
     * @method stringHelper
     * @private
     * @param {object} context The current mustache context.
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    Renderer.prototype.stringHelper = function(context, sectionText, helper) {
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
            param = helper(param, context);
        }
        // Allow json formatted $a arguments.
        if ((param.indexOf('{') === 0) && (param.indexOf('{{') !== 0)) {
            param = JSON.parse(param);
        }

        var index = this.requiredStrings.length;
        this.requiredStrings.push({key: key, component: component, param: param});

        // The placeholder must not use {{}} as those can be misinterpreted by the engine.
        return '[[_s' + index + ']]';
    };

    /**
     * Quote helper used to wrap content in quotes, and escape all quotes present in the content.
     *
     * @method quoteHelper
     * @private
     * @param {object} context The current mustache context.
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    Renderer.prototype.quoteHelper = function(context, sectionText, helper) {
        var content = helper(sectionText.trim(), context);

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
    Renderer.prototype.addHelpers = function(context, themeName) {
        this.currentThemeName = themeName;
        this.requiredStrings = [];
        this.requiredJS = [];
        context.uniqid = (uniqInstances++);
        context.str = function() {
          return this.stringHelper.bind(this, context);
        }.bind(this);
        context.pix = function() {
          return this.pixHelper.bind(this, context);
        }.bind(this);
        context.js = function() {
          return this.jsHelper.bind(this, context);
        }.bind(this);
        context.quote = function() {
          return this.quoteHelper.bind(this, context);
        }.bind(this);
        context.globals = {config: config};
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
    Renderer.prototype.getJS = function(strings) {
        var js = '';
        if (this.requiredJS.length > 0) {
            js = this.requiredJS.join(";\n");
        }

        // Re-render to get the final strings.
        return this.treatStringsInContent(js, strings);
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
    Renderer.prototype.treatStringsInContent = function(content, strings) {
        var pattern = /\[\[_s\d+\]\]/,
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
                walker = 4;  // 4 is the length of '[[_s'.

                // Walk the characters to manually extract the index of the string from the placeholder.
                char = content.substr(walker, 1);
                do {
                    strIndex += char;
                    walker++;
                    char = content.substr(walker, 1);
                } while (char != ']');

                // Get the string, add it to the treated result, and remove the placeholder from the content to treat.
                strFinal = strings[parseInt(strIndex, 10)];
                if (typeof strFinal === 'undefined') {
                    Log.debug('Could not find string for pattern [[_s' + strIndex + ']].');
                    strFinal = '';
                }
                treated += strFinal;
                content = content.substr(6 + strIndex.length);  // 6 is the length of the placeholder without the index: '[[_s]]'.

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
    Renderer.prototype.doRender = function(templateSource, context, themeName) {
        this.currentThemeName = themeName;

        return this.getTemplate('core/pix_icon').then(function() {
            this.addHelpers(context, themeName);
            var result = mustache.render(templateSource, context, this.partialHelper.bind(this));

            if (this.requiredStrings.length > 0) {
                return str.get_strings(this.requiredStrings).then(function(strings) {

                    // Why do we not do another call the render here?
                    //
                    // Because that would expose DOS holes. E.g.
                    // I create an assignment called "{{fish" which
                    // would get inserted in the template in the first pass
                    // and cause the template to die on the second pass (unbalanced).

                    result = this.treatStringsInContent(result, strings);
                    return $.Deferred().resolve(result, this.getJS(strings)).promise();
                }.bind(this));
            } else {
                return $.Deferred().resolve(result.trim(), this.getJS([])).promise();
            }
        }.bind(this));
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

    /**
     * Scan a template source for partial tags and return a list of the found partials.
     *
     * @method scanForPartials
     * @private
     * @param {string} templateSource - source template to scan.
     * @return {Array} List of partials.
     */
    Renderer.prototype.scanForPartials = function(templateSource) {
        var tokens = mustache.parse(templateSource),
            partials = [];

        var findPartial = function(tokens, partials) {
            var i, token;
            for (i = 0; i < tokens.length; i++) {
                token = tokens[i];
                if (token[0] == '>' || token[0] == '<') {
                    partials.push(token[1]);
                }
                if (token.length > 4) {
                    findPartial(token[4], partials);
                }
            }
        };

        findPartial(tokens, partials);

        return partials;
    };

    /**
     * Load a template and scan it for partials. Recursively fetch the partials.
     *
     * @method cachePartials
     * @private
     * @param {string} templateName - should consist of the component and the name of the template like this:
     *                              core/menu (lib/templates/menu.mustache) or
     *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
     * @return {Promise} JQuery promise object resolved when all partials are in the cache.
     */
    Renderer.prototype.cachePartials = function(templateName) {
        return this.getTemplate(templateName).then(function(templateSource) {
            var i;
            var partials = this.scanForPartials(templateSource);
            var fetchThemAll = [];

            for (i = 0; i < partials.length; i++) {
                var searchKey = this.currentThemeName + '/' + partials[i];
                if (searchKey in templatePromises) {
                    fetchThemAll.push(templatePromises[searchKey]);
                } else {
                    fetchThemAll.push(this.cachePartials(partials[i]));
                }
            }

            return $.when.apply($, fetchThemAll).then(function() {
                return templateSource;
            });
        }.bind(this));
    };

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
    Renderer.prototype.render = function(templateName, context, themeName) {
        if (typeof (themeName) === "undefined") {
            // System context by default.
            themeName = config.theme;
        }

        this.currentThemeName = themeName;

        return this.cachePartials(templateName).then(function(templateSource) {
            return this.doRender(templateSource, context, themeName);
        }.bind(this));
    };


    return /** @alias module:core/templates */ {
        // Public variables and functions.
        /**
         * Every call to render creates a new instance of the class and calls render on it. This
         * means each render call has it's own class variables.
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
            var renderer = new Renderer();
            return renderer.render(templateName, context, themeName);
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
