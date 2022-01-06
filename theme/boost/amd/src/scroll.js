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
 * Manage user scroll in Moodle for future floating elements.
 *
 * @module theme_boost/scroll
 * @copyright  2020 Ferran Recio <ferran@moodle.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Moodle scroll handling. For now it just handle a "scrolled" class
 * on the body tag but in the near future could handle more floating
 * elements like option bars, docked elements or other active elements.
 *
 * @class theme_boost/scroll
 */
export default class MoodleScroll {

    /**
     * Initialise the scroll monitoring.
     *
     * @method  init
     * @chainable
     * @return {Object} this.
     */
    init() {
        this.scrollY = 0;
        window.addEventListener("scroll", this.scrollHandler.bind(this));
        return this;
    }

    /**
     * Add special classes to body depending on scroll position.
     *
     * @method  update
     * @chainable
     * @return {Integer} current scroll position.
     */
    getScrollPosition() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }

    /**
     * Add special classes to body depending on scroll position.
     *
     * @method  update
     * @chainable
     */
    scrollHandler() {
        const body = document.querySelector('body');
        const scrollY = this.getScrollPosition();
        if (scrollY >= window.innerHeight) {
            body.classList.add('scrolled');
        } else {
            body.classList.remove('scrolled');
        }
    }
}
