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
 * Day-filter dropdown for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-day-filter.mustache template.
 *
 * @module     block_timeline/nav/DayFilter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import type {FilterName} from '../common/types';
import {useAriaLabels} from '../common/useAriaLabels';
import {useComposedLabel} from '../common/useComposedLabel';
import String from '@moodle/lms/core/String';

const MENU_ID = 'menudayfilter';
const GROUP_ID = 'duedatefiltergrouplabel';

type FilterOption = {
    name: FilterName;
    labelKey: string;
    labelComponent: string;
    dataFrom: string;
    dataTo?: string;
};

/** Top-level items (above the divider). */
const TOP_OPTIONS: FilterOption[] = [
    {
        name: 'all', labelKey: 'all', labelComponent: 'core', dataFrom: '-14',
    },
    {
        name: 'overdue', labelKey: 'overdue', labelComponent: 'block_timeline', dataFrom: '-14', dataTo: '1',
    },
];

/** Grouped date-range items (below the divider). */
const GROUP_OPTIONS: FilterOption[] = [
    {
        name: 'next7days', labelKey: 'next7days', labelComponent: 'block_timeline', dataFrom: '0', dataTo: '7',
    },
    {
        name: 'next30days', labelKey: 'next30days', labelComponent: 'block_timeline', dataFrom: '0', dataTo: '30',
    },
    {
        name: 'next3months', labelKey: 'next3months', labelComponent: 'block_timeline', dataFrom: '0', dataTo: '90',
    },
    {
        name: 'next6months', labelKey: 'next6months', labelComponent: 'block_timeline', dataFrom: '0', dataTo: '180',
    },
];

const ALL_OPTIONS = [...TOP_OPTIONS, ...GROUP_OPTIONS];

type DayFilterProps = {
    activeFilter: FilterName;
    onChange: (filter: FilterName) => void;
};

/**
 * Renders the day-filter dropdown button and menu, matching the legacy Mustache template.
 *
 * Delegates open/close and Popper.js positioning to Bootstrap JS via data-bs-toggle="dropdown"
 * so the gap and outside-click behaviour match the original exactly.
 */
export default function DayFilter({activeFilter, onChange}: DayFilterProps) {
    const {buttonLabel: menuLabel, itemLabels} = useAriaLabels('ariadayfilter', 'ariadayfilteroption', ALL_OPTIONS);

    const activeOption = ALL_OPTIONS.find(o => o.name === activeFilter) ?? ALL_OPTIONS[0];

    const toggleLabel = useComposedLabel('ariadayfilterbutton', activeOption.labelKey, activeOption.labelComponent);

    const renderItem = (option: FilterOption) => (
        <a
            key={option.name}
            className={`dropdown-item${activeFilter === option.name ? ' active dropdown-item-active' : ''}`}
            href='#'
            data-from={option.dataFrom}
            {...(option.dataTo === undefined ? {} : {'data-to': option.dataTo})}
            data-filtername={option.name}
            aria-current={activeFilter === option.name ? 'true' : undefined}
            aria-label={itemLabels[option.name]}
            role='menuitem'
            onClick={event => {
                event.preventDefault();
                onChange(option.name);
            }}
        >
            <String identifier={option.labelKey} component={option.labelComponent}>{''}</String>
        </a>
    );

    return (
        <div data-region='day-filter' className='dropdown mb-1'>
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
                aria-controls={MENU_ID}
                title={menuLabel}
            >
                <span data-active-item-text=''>
                    <String
                        identifier={activeOption.labelKey}
                        component={activeOption.labelComponent}
                    >{''}</String>
                </span>
            </button>

            <div
                id={MENU_ID}
                role='menu'
                aria-label={menuLabel}
                className='dropdown-menu'
                data-show-active-item=''
                data-skip-active-class='true'
            >
                {TOP_OPTIONS.map(option => renderItem(option))}

                <div className='dropdown-divider' role='separator' />

                <div role='group' aria-labelledby={GROUP_ID}>
                    <div className='h6 dropdown-header' role='presentation' id={GROUP_ID}>
                        <String identifier='duedate' component='block_timeline'>{''}</String>
                    </div>
                    {GROUP_OPTIONS.map(option => renderItem(option))}
                </div>
            </div>
        </div>
    );
}
