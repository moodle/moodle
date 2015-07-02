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
 * javascript for a searchable select type element
 *
 * @package   formlib
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

selector = {
    select: null,
    input: null,
    button: null,
    goodbrowser: false,
    alloptions: [],

    filter_init: function(strsearch, selectinputid) {
        selector.goodbrowser = !(' ' + document.body.className + ' ').match(/ ie | safari /);

        // selector.id = selectinputid
        selector.select = document.getElementById(selectinputid);
        selector.button = document.getElementById('settingssubmit');

        // Copy all selector options into a plain array. selector.select.options
        // is linked live to the document, which causes problems in IE and Safari.
        for (var i = 0; i < selector.select.options.length; i++) {
            selector.alloptions[i] = selector.select.options[i];
        }

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.id = 'searchui';

        // Find the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = selectinputid+'_search';
        selector.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        selector.select.parentNode.insertBefore(div, selector.select);
        input.addEventListener('keyup', selector.filter_change);
    },

    filter_change: function() {
        var searchtext = selector.input.value.toLowerCase();
        var found = false;
        for (var i = 0; i < selector.alloptions.length; i++) {
            var option = selector.alloptions[i];
            if (option.text.toLowerCase().indexOf(searchtext) >= 0) {
                // The option is matching the search text.
                selector.set_visible(option, true);
                found = true;
            } else {
                selector.set_visible(option, false);
            }
        }

        if (found) {
            // No error.
            selector.input.className = "";
        } else {
            // The search didn't find any matching, color the search text in red.
            selector.input.className = "error";
        }
    },

    set_visible: function(element, visible) {
        if (selector.goodbrowser) {
            if (visible) {
                element.style.display = 'block';
                element.disabled = false;
            } else {
                element.style.display = 'none';
                element.selected = false;
                element.disabled = true;
            }
        } else {
            // This is a deeply evil hack to make the filtering work in IE.
            // IE ignores display: none; on select options, but wrapping the
            // option in a span does seem to hide the option.
            // Thanks http://work.arounds.org/issue/96/option-elements-do-not-hide-in-IE/
            if (visible) {
                if (element.parentNode.tagName.toLowerCase() === 'span') {
                    element.parentNode.parentNode.replaceChild(element, element.parentNode); // New, old.
                }
                element.disabled = false;
            } else {
                if (element.parentNode.tagName.toLowerCase() !== 'span') {
                    var span = document.createElement('span');
                    element.parentNode.replaceChild(span, element); // New, old.
                    span.appendChild(element);
                    span.style.display = 'none';
                }
                element.disabled = true;
                element.selected = false;
            }
        }
    }
};
