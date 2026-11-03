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
 * Accessible name for a dropdown toggle that shows its current selection.
 *
 * The name has to contain the visible selection so that it can be spoken by voice control
 * (WCAG 2.5.3), but building it by placing the selection next to a second string leaves the
 * assembled phrase untranslatable: a translator sees each half on its own, with no context and no
 * way to change the order the two appear in. So the whole phrase is one string taking the
 * selection as {$a}, in the manner of core's monthprevwithname.
 *
 * @module     block_timeline/common/useComposedLabel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect} from 'react';
import {getString} from '@moodle/lms/core/stringUtils';

/**
 * Fetch a control's accessible name, with the active option's label substituted into it.
 *
 * @param labelKey string identifier (block_timeline) for the name, taking the option label as {$a}.
 * @param activeLabelKey string identifier for the currently selected option's own label.
 * @param activeLabelComponent component owning activeLabelKey.
 * @returns the composed name, empty until the strings resolve.
 */
export function useComposedLabel(
    labelKey: string,
    activeLabelKey: string,
    activeLabelComponent = 'block_timeline',
) {
    const [label, setLabel] = useState('');

    useEffect(() => {
        // The selection can change again before these resolve, so drop a response that is no
        // longer for the current option rather than letting it overwrite a newer one.
        let current = true;

        void getString(activeLabelKey, activeLabelComponent)
            .then(async activeLabel => getString(labelKey, 'block_timeline', activeLabel))
            .then(composed => {
                if (current) {
                    setLabel(composed);
                }

                return composed;
            });

        return () => {
            current = false;
        };
    }, [labelKey, activeLabelKey, activeLabelComponent]);

    return label;
}
