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
 * Matches the DOM structure of the legacy nav-view-selector.mustache template.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect, useId} from 'react';
import String from '@moodle/lms/core/String';
import {getString} from '@moodle/lms/core/stringUtils';
import type {OrderName} from '../types';

const SPAN_ID = 'timeline-view-selector-current-selection';

interface ViewOption {
    name: OrderName;
    labelKey: string;
}

const VIEW_OPTIONS: ViewOption[] = [
    {name: 'sortbydates', labelKey: 'sortbydates'},
    {name: 'sortbycourses', labelKey: 'sortbycourses'},
];

interface ViewSelectorProps {
    activeOrder: OrderName;
    onChange: (order: OrderName) => void;
}

/**
 * Renders the sort-by-dates / sort-by-courses toggle dropdown, matching the legacy template.
 *
 * Delegates open/close and Popper.js positioning to Bootstrap JS via data-bs-toggle="dropdown"
 * so the gap and outside-click behaviour match the original exactly.
 */
export default function ViewSelector({activeOrder, onChange}: ViewSelectorProps) {
    const uid = useId().replace(/:/g, '');
    const menuId = 'menusortby';
    const datesId = `view_dates_${uid}`;
    const coursesId = `view_courses_${uid}`;

    const panelId: Record<OrderName, string> = {
        sortbydates:   datesId,
        sortbycourses: coursesId,
    };

    const [buttonLabel, setButtonLabel] = useState('');
    const [itemLabels, setItemLabels] = useState<Partial<Record<OrderName, string>>>({});

    useEffect(() => {
        getString('ariaviewselector', 'block_timeline').then(setButtonLabel);

        VIEW_OPTIONS.forEach(opt => {
            getString(opt.labelKey, 'block_timeline')
                .then(label => getString('ariaviewselectoroption', 'block_timeline', label))
                .then(ariaLabel => setItemLabels(prev => ({...prev, [opt.name]: ariaLabel})));
        });
    }, []);

    const activeOption = VIEW_OPTIONS.find(o => o.name === activeOrder) ?? VIEW_OPTIONS[0];

    return (
        <div data-region="view-selector" className="dropdown mb-1">
            <button
                type="button"
                className="btn btn-outline-secondary dropdown-toggle icon-no-margin"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-label={buttonLabel}
                aria-controls={menuId}
                title={buttonLabel}
                aria-describedby={SPAN_ID}
            >
                <span id={SPAN_ID} data-active-item-text="">
                    <String identifier={activeOption.labelKey} component="block_timeline">{''}</String>
                </span>
            </button>

            <div
                id={menuId}
                role="tablist"
                className="dropdown-menu dropdown-menu-end"
                data-show-active-item=""
            >
                {VIEW_OPTIONS.map(option => (
                    <a
                        key={option.name}
                        className={`dropdown-item${activeOrder === option.name ? ' active dropdown-item-active' : ''}`}
                        href={`#${panelId[option.name]}`}
                        data-filtername={option.name}
                        aria-current={activeOrder === option.name ? 'true' : undefined}
                        aria-label={itemLabels[option.name]}
                        aria-controls={panelId[option.name]}
                        role="tab"
                        onClick={(e) => {
                            e.preventDefault();
                            onChange(option.name);
                        }}
                    >
                        <String identifier={option.labelKey} component="block_timeline">{''}</String>
                    </a>
                ))}
            </div>
        </div>
    );
}
