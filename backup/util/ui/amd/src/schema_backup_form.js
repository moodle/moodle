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
 * Schema selector javascript controls.
 *
 * This module controls:
 * - The select all feature.
 * - Disabling activities checkboxes when the section is not selected.
 *
 * @module     core_backup/schema_backup_form
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import * as Templates from 'core/templates';

const Selectors = {
    action: '[data-mdl-action]',
    checkboxes: '#id_coursesettings input[type="checkbox"]',
    firstSection: 'fieldset#id_coursesettings .fcontainer .grouped_settings.section_level',
    modCheckboxes: (modName) => `setting_activity_${modName}_`,
};

const Suffixes = {
    userData: '_userdata',
    userInfo: '_userinfo',
    included: '_included',
};

/**
 * Adds select all/none links to the top of the backup/restore/import schema page.
 */
export default class BackupFormController {

    /**
     * Static module init method.
     * @param {Array<string>} modNames - The names of the modules.
     * @returns {BackupFormController}
     */
    static init(modNames) {
        return new BackupFormController(modNames);
    }

    /**
     * Creates a new instance of the SchemaBackupForm class.
     * @param {Array<string>} modNames - The names of the modules.
     */
    constructor(modNames) {
        this.modNames = modNames;
        this.scanFormUserData();
        this.addSelectorsToPage();
    }

    /**
     * Detect the user data attribute from the form.
     *
     * @private
     */
    scanFormUserData() {
        this.withuserdata = false;
        this.userDataSuffix = Suffixes.userData;

        const checkboxes = document.querySelectorAll(Selectors.checkboxes);
        if (!checkboxes) {
            return;
        }
        // Depending on the form, user data inclusion is called userinfo or userdata.
        for (const checkbox of checkboxes) {
            const name = checkbox.name;
            if (name.endsWith(Suffixes.userData)) {
                this.withuserdata = true;
                break;
            } else if (name.endsWith(Suffixes.userInfo)) {
                this.withuserdata = true;
                this.userDataSuffix = Suffixes.userInfo;
                break;
            }
        }
    }

    /**
     * Initializes all related events.
     *
     * @private
     * @param {HTMLElement} element - The element to attach the events to.
     */
    initEvents(element) {
        element.addEventListener('click', (event) => {
            const action = event.target.closest(Selectors.action);
            if (!action) {
                return;
            }
            event.preventDefault();

            const suffix = (action.dataset?.mdlType == 'userdata') ? this.userDataSuffix : Suffixes.included;

            this.changeSelection(
                action.dataset.mdlAction == 'selectall',
                suffix,
                action.dataset?.mdlMod ?? null
            );
        });
    }

    /**
     * Changes the selection according to the params.
     *
     * @private
     * @param {boolean} checked - The checked state for the checkboxes.
     * @param {string} suffix - The checkboxes suffix
     * @param {string} [modName] - The module name.
     */
    changeSelection(checked, suffix, modName) {
        const prefix = modName ? Selectors.modCheckboxes(modName) : null;

        let formId;

        const checkboxes = document.querySelectorAll(Selectors.checkboxes);
        for (const checkbox of checkboxes) {
            formId = formId ?? checkbox.closest('form').getAttribute('id');

            if (prefix && !checkbox.name.startsWith(prefix)) {
                continue;
            }
            if (checkbox.name.endsWith(suffix)) {
                checkbox.checked = checked;
            }
        }

        // At this point, we really need to persuade the form we are part of to
        // update all of its disabledIf rules. However, as far as I can see,
        // given the way that lib/form/form.js is written, that is impossible.
        if (formId && M.form) {
            M.form.updateFormState(formId);
        }
    }

    /**
     * Generates the full selectors element to add to the page.
     *
     * @private
     * @returns {HTMLElement} The selectors element.
     */
    generateSelectorsElement() {
        const links = document.createElement('div');
        links.id = 'backup_selectors';
        this.initEvents(links);
        this.renderSelectorsTemplate(links);
        return links;
    }

    /**
     * Load the select all template.
     *
     * @private
     * @param {HTMLElement} element the container
     */
    renderSelectorsTemplate(element) {
        const data = {
            modules: this.getModulesTemplateData(),
            withuserdata: (this.withuserdata) ? true : undefined,
        };
        Templates.renderForPromise(
            'core_backup/formselectall',
            data
        ).then(({html, js}) => {
            return Templates.replaceNodeContents(element, html, js);
        }).catch(Notification.exception);
    }

    /**
     * Generate the modules template data.
     *
     * @private
     * @returns {Array} of modules data.
     */
    getModulesTemplateData() {
        const modules = [];
        for (const modName in this.modNames) {
            if (!this.modNames.hasOwnProperty(modName)) {
                continue;
            }
            modules.push({
                modname: modName,
                heading: this.modNames[modName],
            });
        }
        return modules;
    }

    /**
     * Adds select all/none functionality to the backup form.
     *
     * @private
     */
    addSelectorsToPage() {
        const firstSection = document.querySelector(Selectors.firstSection);
        if (!firstSection) {
            // This is not a relevant page.
            return;
        }
        if (!firstSection.querySelector(Selectors.checkboxes)) {
            // No checkboxes.
            return;
        }

        // Add global select all/none options.
        const selector = this.generateSelectorsElement();
        firstSection.parentNode.insertBefore(selector, firstSection);
    }
}
