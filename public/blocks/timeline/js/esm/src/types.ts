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
 * TypeScript types for the Timeline block React frontend.
 *
 * @module     block_timeline/types
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Icon data as returned by core_calendar event_icon_exporter. */
export interface CalendarEventIcon {
    key: string;
    component: string;
    alttext: string;
    iconurl: string;
    iconclass: string;
    purpose: string;
}

/** A calendar action event as returned by the timeline web services. */
export interface CalendarEvent {
    id: number;
    name: string;
    timesort: number;
    timeusermidnight: number;
    formattedday: string;
    overdue: boolean;
    eventtype: string;
    url: string;
    modulename: string;
    activityname?: string;
    activitystr?: string;
    purpose?: string;
    icon?: CalendarEventIcon;
    course: {
        id: number;
        fullname: string;
        fullnamedisplay: string;
        viewurl: string;
    };
    action?: {
        name: string;
        url: string;
        itemcount: number;
        actionable: boolean;
        showitemcount: boolean;
    };
}

/** A course as returned by get_courses_with_events, including its events. */
export interface CourseWithEvents {
    id: number;
    fullname: string;
    shortname: string;
    viewurl: string;
    courseimage: string;
    events: CalendarEvent[];
}

/** Day filter option names matching the PHP constants in lib.php. */
export type FilterName =
    | 'all'
    | 'overdue'
    | 'next7days'
    | 'next30days'
    | 'next3months'
    | 'next6months';

/** Sort order option names matching the PHP constants in lib.php. */
export type OrderName = 'sortbydates' | 'sortbycourses';

/** Props seeded from PHP into data-react-props. */
export interface TimelineProps {
    midnight: number;
    filter: FilterName;
    order: OrderName;
    limit: number;
    nocoursesurl: string;
    noeventsurl: string;
    hasenrolledcourses: boolean;
}

/** Offset/limit values derived from the active filter. */
export interface FilterOffsets {
    daysoffset: number;
    dayslimit: number | null;
    filteroverdue: boolean;
}
