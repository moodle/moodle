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
 * Theme settings js logic
 *
 * @package
 * @copyright  2022 Willian Mano - https://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import AccessibilitySettingsModal from 'theme_moove/accessibilitysettings_modal';
import $ from 'jquery';

export const init = async() => {
    $('#accessibilitysettings-control').click(function(e) {
        e.preventDefault();

        openModal();
    });
};

const openModal = async() => {
    const modal = await AccessibilitySettingsModal.create({});

    modal.show();
};