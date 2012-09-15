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

    filter_init: function(strsearch, selectinputid) {
        // selector.id = selectinputid
        selector.select = document.getElementById(selectinputid);
        selector.button = document.getElementById('settingssubmit');

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
        YUI().use('yui2-event', function(Y) {
            Y.YUI2.util.Event.addListener(input, 'keyup', selector.filter_change);
        });
    },

    filter_change: function() {
        var searchtext = selector.input.value.toLowerCase();
        var options = selector.select.options;
        var matchingoption = -1;
        for (var i = 0; i < options.length; i++) {
            var optiontext = options[i].text.toLowerCase();
            if (optiontext.indexOf(searchtext) >= 0) { //the option is matching the search text
                options[i].disabled = false; //the option must be visible
                options[i].style.display = 'block';
                if (matchingoption == -1) { //we found at least one
                    matchingoption = i;
                }
            } else {
                options[i].disabled = true;
                options[i].selected = false;
                options[i].style.display = 'none';
            }
        }

        if (matchingoption == -1) { //the search didn't find any matching, color the search text in red
            selector.input.className = "error";
        } else {
            selector.input.className = "";
        }

    }

};

