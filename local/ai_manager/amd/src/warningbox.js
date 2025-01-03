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
 * Module rendering the warning box to inform the users about misleading AI results.
 *
 * @module     local_ai_manager/warningbox
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getAiConfig} from 'local_ai_manager/config';
import Log from 'core/log';
import Templates from 'core/templates';


/**
 * Renders the warning box.
 *
 * @param {string} selector the selector where the warning box should be rendered into
 */
export const renderWarningBox = async(selector) => {
    let aiConfig = null;
    try {
        aiConfig = await getAiConfig();
    } catch (error) {
        // This typically happens if we do not have the capabilities to retrieve the AI config.
        // So we just eventually log in debug mode and do not render anything.
        Log.debug(error);
        return;
    }
    const showAiWarningLink = aiConfig.aiwarningurl.length > 0;
    const targetElement = document.querySelector(selector);
    const {html, js} = await Templates.renderForPromise('local_ai_manager/ai_info_warning', {
        showaiwarninglink: showAiWarningLink,
        aiwarningurl: aiConfig.aiwarningurl
    });
    Templates.appendNodeContents(targetElement, html, js);
};
