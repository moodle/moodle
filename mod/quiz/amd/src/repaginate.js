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
 * Initialise the repaginate dialogue on quiz editing page.
 *
 * @module    mod_quiz/repaginate
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory'], function($, ModalFactory) {

    var SELECTORS = {
        REPAGINATECOMMAND: '#repaginatecommand',
        HEADER: 'header',
        BODY: 'form'
    };

    /**
     * Initialise the repaginate button and add the event listener.
     */
    var init = function() {
        ModalFactory.create(
            {
                title: $(SELECTORS.REPAGINATECOMMAND).data(SELECTORS.HEADER),
                body: $(SELECTORS.REPAGINATECOMMAND).data(SELECTORS.BODY),
                large: false,
            },
            $(SELECTORS.REPAGINATECOMMAND)
        );
    };
    return {
        init: init
    };
});
