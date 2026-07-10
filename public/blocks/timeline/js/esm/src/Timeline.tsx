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
 * Root Timeline block React component.
 *
 * Mounted automatically via data-react-component="@moodle/lms/block_timeline/Timeline"
 * by core/react_autoinit.
 *
 * @module     block_timeline/Timeline
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useCallback} from 'react';
import {fetchOne} from '@moodle/lms/core/ajax';
import DayFilter from './nav/DayFilter';
import ViewSelector from './nav/ViewSelector';
import Search from './nav/Search';
import DatesView from './views/DatesView';
import CoursesView from './views/CoursesView';
import type {TimelineProps, FilterName, OrderName, FilterOffsets} from './types';

const PREF_FILTER = 'block_timeline_user_filter_preference';
const PREF_ORDER = 'block_timeline_user_sort_preference';

/**
 * Persist a single user preference via the core_user_update_user_preferences WS.
 * Failures are intentionally swallowed — the preference will revert on next page load.
 */
const setUserPreference = (name: string, value: string): void => {
    fetchOne({
        methodname: 'core_user_update_user_preferences',
        args: {preferences: [{type: name, value}]},
    }).catch(() => undefined);
};

// Overdue filter sets dayslimit to null so endtime is null — the WS returns events from daysoffset
// to infinity and the client filters by event.overdue=true. Using dayslimit:0 would set
// endtime=midnight, excluding events whose timesort falls between midnight and now.
const FILTER_OFFSETS: Record<FilterName, FilterOffsets> = {
    all: {daysoffset: -14, dayslimit: null, filteroverdue: false},
    overdue: {daysoffset: -14, dayslimit: null, filteroverdue: true},
    next7days: {daysoffset: 0, dayslimit: 7, filteroverdue: false},
    next30days: {daysoffset: 0, dayslimit: 30, filteroverdue: false},
    next3months: {daysoffset: 0, dayslimit: 90, filteroverdue: false},
    next6months: {daysoffset: 0, dayslimit: 180, filteroverdue: false},
};

/**
 * Root component for the Timeline block.
 *
 * Manages filter/sort/search state, persists user preferences, and renders
 * either the DatesView or CoursesView depending on the active sort order.
 * Both view containers are always present in the DOM so Behat selectors always
 * find [data-region='view-dates'] and [data-region='view-courses'].
 */
export default function Timeline({midnight, filter, order, limit, nocoursesurl, noeventsurl, hasenrolledcourses}: TimelineProps) {
    const [activeFilter, setActiveFilter] = useState<FilterName>(filter);
    const [activeOrder, setActiveOrder] = useState<OrderName>(order);
    const [searchvalue, setSearchvalue] = useState('');
    const [searchPending, setSearchPending] = useState(false);

    /**
     * Applies the newly selected day filter and persists it as a user preference.
     *
     * @param next filter option chosen from the DayFilter dropdown
     */
    const handleFilterChange = useCallback((next: FilterName) => {
        setActiveFilter(next);
        setUserPreference(PREF_FILTER, next);
    }, []);

    /**
     * Applies the newly selected sort order and persists it as a user preference.
     *
     * @param next sort order chosen from the ViewSelector dropdown
     */
    const handleOrderChange = useCallback((next: OrderName) => {
        setActiveOrder(next);
        setUserPreference(PREF_ORDER, next);
    }, []);

    /**
     * Updates the active search term used to filter events client-side.
     *
     * @param value current text entered into the Search field
     */
    const handleSearch = useCallback((value: string) => {
        setSearchvalue(value);
    }, []);

    const offsets = FILTER_OFFSETS[activeFilter];
    const showCoursesView = activeOrder === 'sortbycourses';

    return (
        <div data-region="timeline" className="block-timeline">
            <div className="p-0 px-2">
                <div className="d-flex flex-wrap gap-1 g-0">
                    <div>
                        <DayFilter activeFilter={activeFilter} onChange={handleFilterChange} />
                    </div>
                    <div>
                        <ViewSelector activeOrder={activeOrder} onChange={handleOrderChange} />
                    </div>
                    <div className="flex-grow-1 d-flex justify-content-end nav-search">
                        <Search onSearch={handleSearch} onSearching={setSearchPending} />
                    </div>
                </div>
                <div className="pb-3 px-2 border-bottom"></div>
            </div>

            <div className="p-0">
                {/* Both containers are always in the DOM so Behat can find [data-region='view-dates']
                    and [data-region='view-courses'] regardless of which view is active. */}
                <div data-region="view-dates" role="tabpanel" className={showCoursesView ? 'd-none' : ''}>
                    {!showCoursesView && (
                        <DatesView
                            midnight={midnight}
                            offsets={offsets}
                            searchvalue={searchvalue}
                            nocoursesurl={nocoursesurl}
                            noeventsurl={noeventsurl}
                            hasenrolledcourses={hasenrolledcourses}
                            limit={limit}
                            searchPending={searchPending}
                        />
                    )}
                </div>
                <div data-region="view-courses" role="tabpanel" className={showCoursesView ? '' : 'd-none'}>
                    {showCoursesView && (
                        <CoursesView
                            midnight={midnight}
                            offsets={offsets}
                            searchvalue={searchvalue}
                            nocoursesurl={nocoursesurl}
                            noeventsurl={noeventsurl}
                            hasenrolledcourses={hasenrolledcourses}
                            searchPending={searchPending}
                        />
                    )}
                </div>
            </div>
        </div>
    );
}
