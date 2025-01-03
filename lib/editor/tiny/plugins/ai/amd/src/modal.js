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
 * Equation Modal for Tiny.
 *
 * @module      tiny_ai/modal
 * @copyright   2024, ISB Bayern
 * @author      Dr. Peter Mayer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';

export default class AiModal extends Modal {
    static TYPE = 'ai-modal';
    static TEMPLATE = 'tiny_ai/components/moodle-modal';

    registerEventListeners() {
        // Call the parent registration.
        super.registerEventListeners();
    }

    configure(modalConfig) {
        modalConfig.large = true;
        modalConfig.removeOnClose = true;
        super.configure(modalConfig);
    }
}
