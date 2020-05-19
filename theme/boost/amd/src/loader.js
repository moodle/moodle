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

import $ from 'jquery';
import Aria from './aria';
import Scroll from './scroll';
import Bootstrap from './bootstrap/index';
import CustomEvents from 'core/custom_interaction_events';

/**
 * Set up the search.
 *
 * @method init
 */
export {
    init,
    Bootstrap
};

/**
 * Bootstrap init function
 */
const init = () => {
    rememberTabs();

    enablePopovers();

    enableTooltips();

    const scroll = new Scroll();
    scroll.init();

    // Disables flipping the dropdowns up and getting hidden behind the navbar.
    $.fn.dropdown.Constructor.Default.flip = false;

    Aria.init();
};

/**
 * Rember the last visited tabs.
 */
const rememberTabs = () => {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var hash = $(e.target).attr('href');
        if (history.replaceState) {
            history.replaceState(null, null, hash);
        } else {
            location.hash = hash;
        }
    });
    var hash = window.location.hash;
    if (hash) {
       $('.nav-link[href="' + hash + '"]').tab('show');
    }
};

/**
 * Enable all popovers
 *
 */
const enablePopovers = () => {
    $('body').popover({
        selector: '[data-toggle="popover"]',
        trigger: 'focus hover',
        placement: 'auto'
    });

    CustomEvents.define($('body'), [
        CustomEvents.events.escape,
    ]);
    $('body').on(CustomEvents.events.escape, '[data-toggle=popover]', function() {

        $(this).trigger('blur');
    });
};

/**
 * Enable tooltips
 *
 */
const enableTooltips = () => {
    $('body').tooltip({
        container: 'body',
        selector: '[data-toggle="tooltip"]'
    });
};