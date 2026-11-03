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
 * Sort-order (dates / courses) selector for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-view-selector.mustache template, except for the
 * ARIA roles: this is a dropdown of two sort options, so it uses the menu pattern that DayFilter
 * and Bootstrap's own dropdown JS already implement, rather than the tablist the legacy template
 * declared but never wired up.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import type {OrderName} from '../common/types';
import {useAriaLabels} from '../common/useAriaLabels';
import {useComposedLabel} from '../common/useComposedLabel';
import String from '@moodle/lms/core/String';

type ViewOption = {
    name: OrderName;
    labelKey: string;
};

const VIEW_OPTIONS: ViewOption[] = [
    {name: 'sortbydates', labelKey: 'sortbydates'},
    {name: 'sortbycourses', labelKey: 'sortbycourses'},
];

type ViewSelectorProps = {
    activeOrder: OrderName;
    onChange: (order: OrderName) => void;
};

/**
 * Renders the sort-by-dates / sort-by-courses toggle dropdown, matching the legacy template.
 *
 * Delegates open/close and Popper.js positioning to Bootstrap JS via data-bs-toggle="dropdown"
 * so the gap and outside-click behaviour match the original exactly.
 */
export default function ViewSelector({activeOrder, onChange}: ViewSelectorProps) {
    const menuId = 'menusortby';

    const {buttonLabel: menuLabel, itemLabels} = useAriaLabels('ariaviewselector', 'ariaviewselectoroption', VIEW_OPTIONS);

    const activeOption = VIEW_OPTIONS.find(o => o.name === activeOrder) ?? VIEW_OPTIONS[0];

    const toggleLabel = useComposedLabel('ariaviewselectorbutton', activeOption.labelKey);

    return (
        <div data-region='view-selector' className='dropdown mb-1'>
            <button
                type='button'
                className='btn btn-outline-secondary dropdown-toggle icon-no-margin'
                data-bs-toggle='dropdown'
                aria-haspopup='true'
                // Bootstrap's dropdown JS flips this to "true" on open and owns it from then
                // on. The literal never changes between renders, so React's reconciler leaves
                // the attribute alone and will not reset it while the menu is open.
                aria-expanded='false'
                aria-label={toggleLabel}
                aria-controls={menuId}
                title={menuLabel}
            >
                <span data-active-item-text=''>
                    <String identifier={activeOption.labelKey} component='block_timeline'>{''}</String>
                </span>
            </button>

            <div
                id={menuId}
                role='menu'
                aria-label={menuLabel}
                className='dropdown-menu dropdown-menu-end'
                data-show-active-item=''
            >
                {VIEW_OPTIONS.map(option => (
                    <a
                        key={option.name}
                        className={`dropdown-item${activeOrder === option.name ? ' active dropdown-item-active' : ''}`}
                        href='#'
                        data-filtername={option.name}
                        aria-current={activeOrder === option.name ? 'true' : undefined}
                        aria-label={itemLabels[option.name]}
                        role='menuitem'
                        onClick={event => {
                            event.preventDefault();
                            onChange(option.name);
                        }}
                    >
                        <String identifier={option.labelKey} component='block_timeline'>{''}</String>
                    </a>
                ))}
            </div>
        </div>
    );
}
