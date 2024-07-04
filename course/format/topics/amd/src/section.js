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
 * Format topics section extra logic component.
 *
 * @module     format_topics/section
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import Templates from 'core/templates';

class HighlightSection extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'format_topics_section';
        // Default query selectors.
        this.selectors = {
            SETMARKER: `[data-action="sectionHighlight"]`,
            REMOVEMARKER: `[data-action="sectionUnhighlight"]`,
            ACTIONTEXT: `.menu-action-text`,
            ICON: `.icon`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            HIDE: 'd-none',
        };
        // The topics format section specific actions.
        this.formatActions = {
            HIGHLIGHT: 'sectionHighlight',
            UNHIGHLIGHT: 'sectionUnhighlight',
        };
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `section.current:updated`, handler: this._refreshHighlight},
        ];
    }

    /**
     * Update a content section using the state information.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    async _refreshHighlight({element}) {
        let selector;
        let newAction;
        if (element.current) {
            selector = this.selectors.SETMARKER;
            newAction = this.formatActions.UNHIGHLIGHT;
        } else {
            selector = this.selectors.REMOVEMARKER;
            newAction = this.formatActions.HIGHLIGHT;
        }
        // Find the affected action.
        const affectedAction = this.getElement(`${selector}`, element.id);
        if (!affectedAction) {
            return;
        }
        // Change action, text and icon.
        affectedAction.dataset.action = newAction;
        const actionText = affectedAction.querySelector(this.selectors.ACTIONTEXT);
        if (affectedAction.dataset?.swapname && actionText) {
            const oldText = actionText?.innerText;
            actionText.innerText = affectedAction.dataset.swapname;
            affectedAction.dataset.swapname = oldText;
        }
        const icon = affectedAction.querySelector(this.selectors.ICON);
        if (affectedAction.dataset?.swapicon && icon) {
            const newIcon = affectedAction.dataset.swapicon;
            if (newIcon) {
                const pixHtml = await Templates.renderPix(newIcon, 'core');
                Templates.replaceNode(icon, pixHtml, '');
                affectedAction.dataset.swapicon = affectedAction.dataset.icon;
                affectedAction.dataset.icon = newIcon;
            }
        }
    }
}

export const init = () => {
    // Add component to the section.
    const courseEditor = getCurrentCourseEditor();
    if (courseEditor.supportComponents && courseEditor.isEditing) {
        new HighlightSection({
            element: document.getElementById('page'),
            reactive: courseEditor,
        });
    }
};
