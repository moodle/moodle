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
 * AI image modal for Tiny.
 *
 * @module      tiny_aiplacement/imagemodal
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import TextModal from './textmodal';
import ModalRegistry from 'core/modal_registry';

const ImageModal = class extends TextModal {
    static TYPE = 'tiny_aiplacement/imagemodal';
    static TEMPLATE = 'tiny_aiplacement/imagemodal';

    /**
     * Configure the modal.
     *
     * @param {object} modalConfig The modal configuration.
     */
    configure(modalConfig) {
        super.configure(modalConfig);

        // Add modal extra class.
        this.getModal().addClass('tiny_aiplacement_modal');
    }
};

ModalRegistry.register(ImageModal.TYPE, ImageModal, ImageModal.TEMPLATE);

export default ImageModal;
