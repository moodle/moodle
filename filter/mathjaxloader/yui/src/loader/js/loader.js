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
 * Mathjax JS Loader.
 *
 * @package    filter_mathjaxloader
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.filter_mathjaxloader = M.filter_mathjaxloader || {

    /**
     * The users current language - this can't be set until MathJax is loaded - so we need to store it.
     * @property _lang
     * @type String
     * @default ''
     * @private
     */
    _lang: '',

    /**
     * Boolean used to prevent configuring MathJax twice.
     * @property _configured
     * @type Boolean
     * @default ''
     * @private
     */
    _configured: false,

    /**
     * Called by the filter when it is active on any page.
     * This does not load MathJAX yet - it addes the configuration to the head incase it gets loaded later.
     * It also subscribes to the filter-content-updated event so MathJax can respond to content loaded by Ajax.
     *
     * @method typeset
     * @param {Object} params List of configuration params containing mathjaxconfig (text) and lang
     */
    configure: function(params) {

        // Add a js configuration object to the head.
        // See "http://docs.mathjax.org/en/latest/dynamic.html#ajax-mathjax"
        var script = document.createElement("script");
        script.type = "text/x-mathjax-config";
        script[(window.opera ? "innerHTML" : "text")] = params.mathjaxconfig;
        document.getElementsByTagName("head")[0].appendChild(script);

        // Save the lang config until MathJax is actually loaded.
        this._lang = params.lang;

        // Listen for events triggered when new text is added to a page that needs
        // processing by a filter.
        Y.on(M.core.event.FILTER_CONTENT_UPDATED, this.contentUpdated, this);
    },

    /**
     * Set the correct language for the MathJax menus. Only do this once.
     *
     * @method setLocale
     * @private
     */
    _setLocale: function() {
        if (!this._configured) {
            var lang = this._lang;
            if (typeof window.MathJax !== "undefined") {
                window.MathJax.Hub.Queue(function() {
                    window.MathJax.Localization.setLocale(lang);
                });
                window.MathJax.Hub.Configured();
                this._configured = true;
            }
        }
    },

    /**
     * Called by the filter when an equation is found while rendering the page.
     *
     * @method typeset
     */
    typeset: function() {
        if (!this._configured) {
            var self = this;
            Y.use('mathjax', function() {
                self._setLocale();
                Y.all('.filter_mathjaxloader_equation').each(function(node) {
                    if (typeof window.MathJax !== "undefined") {
                        window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, node.getDOMNode()]);
                    }
                });
            });
        }
    },

    /**
     * Handle content updated events - typeset the new content.
     * @method contentUpdated
     * @param Y.Event - Custom event with "nodes" indicating the root of the updated nodes.
     */
    contentUpdated: function(event) {
        var self = this;
        Y.use('mathjax', function() {
            if (typeof window.MathJax === "undefined") {
                return;
            }
            var processdelay = window.MathJax.Hub.processSectionDelay;
            // Set the process section delay to 0 when updating the formula.
            window.MathJax.Hub.processSectionDelay = 0;
            self._setLocale();
            event.nodes.each(function(node) {
                node.all('.filter_mathjaxloader_equation').each(function(node) {
                    window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, node.getDOMNode()]);
                });
            });
            // Set the delay back to normal after processing.
            window.MathJax.Hub.processSectionDelay = processdelay;
        });
    }
};
