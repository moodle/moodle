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
 * Manage the quiz views.
 *
 * @module     quizaccess_seb/view
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from "core/notification";
import * as Templates from "core/templates";
import * as Str from "core/str";
import ModalAlert from "core/local/modal/alert";

/** @var SELECTOR List of CSS selectors. */
const SELECTOR = {
    MAIN: '#region-main',
    LOADING: '.seb-loading',
};

/** @var Template List of mustache templates. */
const TEMPLATE = {
    LOADING: 'quizaccess_seb/loading',
};

/**
 * Manages view when access has been granted.
 */
export const allowAccess = () => {
    window.location.reload();
};

/**
 * Add an alert to page to inform that Safe Exam Browser access is being checked.
 *
 * @return {Promise}
 */
export const addLoadingAlert = () => {
    return Templates.render(TEMPLATE.LOADING, {}).then((html, js) => {
        const alertRegion = window.document.querySelector(SELECTOR.MAIN);
        return Templates.prependNodeContents(alertRegion, html, js);
    }).catch(Notification.exception);
};

/**
 * Remove the Safe Exam Browser access check alert from the page.
 */
export const clearLoadingAlert = () => {
    const alert = window.document.querySelector(SELECTOR.LOADING);
    if (alert) {
        Templates.replaceNode(alert, '', '');
    }
};

/**
 * Display validation failed modal.
 */
export const showValidationFailedModal = () => {
    ModalAlert.create({
        title: Str.get_string('sebkeysvalidationfailed', 'quizaccess_seb'),
        body: Str.get_string('invalidkeys', 'quizaccess_seb'),
        large: false,
        show: true,
    }).catch(Notification.exception);
};
