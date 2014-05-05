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

/*
 * Question Bank Management.
 *
 * @package    question
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Questionbank Management.
 *
 * @module moodle-question-qbankmanager
 */

/**
 * Question Bank Management.
 *
 * @class M.question.qbankmanager
 */

var manager = {
    /**
     * A reference to the header checkbox.
     *
     * @property _header
     * @type Node
     * @private
     */
    _header: null,

    /**
     * The ID of the first checkbox on the page.
     *
     * @property _firstCheckboxId
     * @type String
     * @private
     */
    _firstCheckboxId: null,

    /**
     * Set up the Question Bank Manager.
     *
     * @method init
     * @param {String} firstCheckboxId The ID of the first checkbox on the page.
     */
    init: function(firstCheckboxId) {
        // Find the header checkbox, and set the initial values.
        this._header = Y.one('#qbheadercheckbox');
        this._header.setAttrs({
            disabled: false,
            title: M.util.get_string('selectall', 'moodle')
        });

        this._header.on('click', this._headerClick, this);

        // Store the first checkbox details.
        this._firstCheckboxId = firstCheckboxId;
    },

    /**
     * Handle toggling of the header checkbox.
     *
     * @method _headerClick
     * @private
     */
    _headerClick: function() {
        // Get the list of questions we affect.
        var categoryQuestions = Y.one('#categoryquestions')
                .all('[type=checkbox],[type=radio]');

        // We base the state of all of the questions on the state of the first.
        firstCheckbox = Y.one('#' + this._firstCheckboxId);

        if (firstCheckbox.get('checked')) {
            categoryQuestions.set('checked', false);
            this._header.setAttribute('title', M.util.get_string('selectall', 'moodle'));
        } else {
            categoryQuestions.set('checked', true);
            this._header.setAttribute('title', M.util.get_string('deselectall', 'moodle'));
        }

        this._header.set('checked', false);
    }
};

M.question = M.question || {};
M.question.qbankmanager = M.question.qbankmanager || manager;
