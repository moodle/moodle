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
 * Shared aria-label fetching for the day-filter and view-selector dropdowns:
 * a button label plus a per-option label, each composed from two language strings.
 *
 * @module     block_timeline/common/useAriaLabels
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect} from 'react';
import {getString} from '@moodle/lms/core/stringUtils';

interface AriaLabelOption<T extends string> {
    name: T;
    labelKey: string;
    labelComponent?: string;
}

/**
 * Fetch a dropdown's button aria-label and a per-option aria-label map.
 *
 * @param buttonKey string identifier (block_timeline) for the button's aria-label.
 * @param itemAriaKey string identifier (block_timeline) wrapping each option's label into its aria-label.
 * @param options dropdown options; each option's own label is read via labelKey/labelComponent.
 */
export function useAriaLabels<T extends string>(
    buttonKey: string,
    itemAriaKey: string,
    options: AriaLabelOption<T>[]
) {
    const [buttonLabel, setButtonLabel] = useState('');
    const [itemLabels, setItemLabels] = useState<Partial<Record<T, string>>>({});

    useEffect(() => {
        getString(buttonKey, 'block_timeline').then(setButtonLabel);

        options.forEach(opt => {
            getString(opt.labelKey, opt.labelComponent ?? 'block_timeline')
                .then(label => getString(itemAriaKey, 'block_timeline', label))
                .then(ariaLabel => setItemLabels(prev => ({...prev, [opt.name]: ariaLabel})));
        });
    }, []);

    return {buttonLabel, itemLabels};
}
