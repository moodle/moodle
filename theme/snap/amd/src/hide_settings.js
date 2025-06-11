/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @copyright Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A javascript module to hide/show settings
 */
import $ from 'jquery';

export const hideDependingOnChecked = (name, dependencies, dependants = []) => {

    const formItemSelector = $(`#admin-${name}`);
    const checkboxSelector = $(`#admin-${name} input[type="checkbox"]`);
    const inputs = $('#adminsettings input[type="checkbox"]').filter((i, input) => {
        return dependencies.some(suffix => input.id.endsWith(suffix));
    });
    const depends = $('#adminsettings .form-item').filter((i, item) => {
        return dependants.some(suffix => item.id.endsWith(suffix));
    });

    const hide = () => {
        formItemSelector.hide();
        hideDependants();
    };

    const show = () => {
        formItemSelector.show();
        showDependants();
    };

    const hideDependants = () => {
        if (dependants.length > 0) {
            depends.hide();
        }
    };

    const showDependants = () => {
        if (dependants.length > 0 && checkboxSelector.is(':checked')) {
            depends.show();
        }
    };

    const update = () => {
        const anyChecked = inputs.is(':checked');
        if (anyChecked) {
            show();
        } else {
            hide();
        }
    };

    hideDependants();
    update();

    inputs.on('change', () => {
        update();
    });

    checkboxSelector.on('change', () => {
        if (checkboxSelector.is(':checked')) {
            showDependants();
        } else {
            hideDependants();
        }
    });
};