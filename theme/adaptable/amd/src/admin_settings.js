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

//
// Admin settings.  Go back to the current tab after save.
//
// @module     theme_adaptable/admin_settings
// @copyright  2024 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import log from 'core/log';

/**
 * Initialise this module.
 */
export const init = () => {
    const stickyTabs = () => {
        const tabels = document.querySelectorAll('a[data-toggle="tab"]');
        if (tabels.length) {
            const action = document.querySelector('form#adminsettings');
            tabels.forEach(tabel => {
                tabel.addEventListener(
                    "click",
                    (event) => {
                        log.debug("Tab clicky! " + event.target.href);
                        action.setAttribute('action', event.target.href);
                    },
                    false,
                );
            });
        }
    };

    log.debug('Adaptable ES6 Admin settings init');
    if (document.readyState !== 'loading') {
        log.debug("Adaptable ES6 Admin settings JS DOM content already loaded");
        stickyTabs();
    } else {
        log.debug("Adaptable ES6 Admin settings JS DOM content not loaded");
        document.addEventListener('DOMContentLoaded', function() {
            log.debug("Adaptable ES6 Admin settings JS DOM content loaded");
            stickyTabs();
        });
    }
};
