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

import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import BaseHandler from 'tiny_ai/datahandler/base';

/**
 * Tiny AI data handler for optimize prompt page.
 *
 * @module      tiny_ai/datahandler/optimize
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    getTemplateContext = () => {
        const context = {
            modalHeadline: BasedataHandler.getTinyAiString('reworkprompt'),
            showIcon: true,
            textareatype: 'prompt'
        };
        Object.assign(context, BasedataHandler.getBackAndGenerateButtonContext());
        return context;
    };
}


