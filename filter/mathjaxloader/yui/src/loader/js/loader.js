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
    init: function(params) {

        // Add a js configuration object to the head.
        // See "http://docs.mathjax.org/en/latest/dynamic.html#ajax-mathjax"
        var script = document.createElement("script");
        script.type = "text/x-mathjax-config";
        script[(window.opera ? "innerHTML" : "text")] = params.mathjaxconfig;
        document.getElementsByTagName("head")[0].appendChild(script);

        MathJax.Localization.setLocale(params.lang);

        Y.all('.filter_mathjaxloader_equation').each(function(node) {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, node.getDOMNode()]);
        });
        MathJax.Hub.Configured();

        // Listen for events triggered when new text is added to a page that needs
        // processing by a filter.
        Y.on(M.core.event.FILTER_CONTENT_UPDATED, this.contentUpdated, this);
    },

    /**
     * Handle content updated events - typeset the new content.
     * @method contentUpdated
     * @param Y.Event - Custom event with "nodes" indicating the root of the updated nodes.
     */
    contentUpdated: function(event) {
        event.nodes.each(function (node) {
            node.all('.filter_mathjaxloader_equation').each(function(node) {
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, node.getDOMNode()]);
            });
        });
    }
};
