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
 * Shows the footer content in a popover.
 *
 * @module     theme_boost/footer-popover
 * @copyright  2021 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Popover from './popover';

export const init = () => {
    const content = document.querySelector('[data-region="footer-content-popover"]');
    const container = document.querySelector('[data-region="footer-container-popover"]');

    $('[data-action="footer-popover"]').popover({
        content: content.innerHTML,
        container: container,
        html: true,
        placement: 'top',
        customClass: 'footer'
    });
};

export {
    Popover
};
