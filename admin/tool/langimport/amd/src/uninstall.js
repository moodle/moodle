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

import {getStrings} from 'core/str';
import {saveCancelPromise, alert as displayAlert} from "core/notification";
import MoodleConfig from 'core/config';

export const init = (form) => {
    form?.addEventListener('submit', async(e) => {
        e.preventDefault();
        const selectedOptions = form.querySelector('#menuuninstalllang')?.selectedOptions;
        if (!selectedOptions?.length) {
            const alertStrings = await getStrings(
                ['noenglishuninstalltitle', 'selectlangs'].map((key) => ({key, component: 'tool_langimport'})
            ));
            displayAlert(...alertStrings);
            return;
        }

        if ([...selectedOptions].map((node) => node.value).indexOf('en') !== -1) {
            const alertStrings = await getStrings(
                ['noenglishuninstalltitle', 'noenglishuninstall'].map((key) => ({key, component: 'tool_langimport'})
                ));
            displayAlert(...alertStrings);
            return;
        }

        const confirmationStrings = await getStrings([
            {
                key: 'uninstall',
                component: 'tool_langimport',
            },
            {
                key: 'uninstallconfirm',
                component: 'tool_langimport',
                param: [...selectedOptions].map((node) => node.textContent).join(', '),
            },
            {
                key: 'yes',
                component: 'core',
            },
        ]);

        saveCancelPromise(...confirmationStrings).then(() => {
            const url = new URL(form.action);
            url.searchParams.append('sesskey', MoodleConfig.sesskey);
            url.searchParams.append('confirmtouninstall', [...selectedOptions].map((node) => node.value).join('/'));
            form.action = url.toString();
            form.submit();
            return true;
        })
        .catch(() => {
            return false;
        });
    });
};
