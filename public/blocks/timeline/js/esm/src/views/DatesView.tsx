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
 * Dates view for the Timeline block — events grouped by day with lazy loading.
 *
 * @module     block_timeline/views/DatesView
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect, useCallback} from 'react';
import String from '@moodle/lms/core/String';
import {getString} from '@moodle/lms/core/stringUtils';
import {Button} from '@moodlehq/design-system';
import {getTimelineEvents} from '../repository';
import {DatesViewSkeleton} from '../Skeleton';
import EventListItem from '../EventListItem';
import {computeTimeRange, groupByDay, filterEvents, getFormattedDays} from '../utils';
import type {DayGroup} from '../utils';
import type {FilterOffsets} from '../types';

const MORE_LOAD_LIMIT = 10;

interface DatesViewProps {
    midnight: number;
    offsets: FilterOffsets;
    searchvalue: string;
    nocoursesurl: string;
    noeventsurl: string;
    hasenrolledcourses: boolean;
    /** Number of events to show on first load, from block_timeline_user_limit_preference. */
    limit: number;
    /** True while the search debounce is pending — show skeleton immediately on keystroke. */
    searchPending?: boolean;
}

/**
 * Renders timeline events sorted by date with lazy-load pagination.
 */
export default function DatesView({
    midnight,
    offsets,
    searchvalue,
    nocoursesurl,
    noeventsurl,
    hasenrolledcourses,
    limit,
    searchPending,
}: DatesViewProps) {
    const [days, setDays] = useState<DayGroup[]>([]);
    const [loading, setLoading] = useState(true);
    const [hasMore, setHasMore] = useState(false);
    const [lastId, setLastId] = useState<number>(0);
    const [loadingMore, setLoadingMore] = useState(false);
    const [moreActivitiesLabel, setMoreActivitiesLabel] = useState('');

    useEffect(() => {
        getString('moreactivities', 'block_timeline').then(setMoreActivitiesLabel);
    }, []);

    const {starttime, endtime} = computeTimeRange(midnight, offsets);

    const load = useCallback(async(aftereventid: number, append: boolean) => {
        const result = await getTimelineEvents({
            timesortfrom: starttime,
            timesortto:   endtime,
            aftereventid,
            limitnum:     (append ? MORE_LOAD_LIMIT : limit) + 1,
            searchvalue:  searchvalue || null,
        });

        const dayMap = await getFormattedDays(result.events.map(e => e.timeusermidnight));
        const events = result.events.map(e => ({...e, formattedday: dayMap.get(e.timeusermidnight) ?? ''}));

        let filtered = filterEvents(events, midnight, offsets.filteroverdue);

        const loadedAll = filtered.length <= (append ? MORE_LOAD_LIMIT : limit);
        if (!loadedAll) {
            filtered.pop();
        }

        const newDays = groupByDay(filtered);

        if (append) {
            setDays(prev => {
                if (newDays.length === 0) {
                    return prev;
                }
                // Merge first new day into last existing day if same timestamp.
                const merged = [...prev];
                if (
                    merged.length > 0 &&
                    newDays.length > 0 &&
                    merged[merged.length - 1].dayTimestamp === newDays[0].dayTimestamp
                ) {
                    merged[merged.length - 1] = {
                        ...merged[merged.length - 1],
                        events: [...merged[merged.length - 1].events, ...newDays[0].events],
                    };
                    return [...merged, ...newDays.slice(1)];
                }
                return [...merged, ...newDays];
            });
        } else {
            setDays(newDays);
        }

        setHasMore(!loadedAll);
        if (filtered.length > 0) {
            setLastId(filtered[filtered.length - 1].id);
        }
    }, [starttime, endtime, searchvalue, offsets.filteroverdue, midnight, limit]);

    useEffect(() => {
        setLoading(true);
        setDays([]);
        setLastId(0);
        load(0, false).finally(() => setLoading(false));
    }, [load]);

    const handleShowMore = async() => {
        setLoadingMore(true);
        await load(lastId, true);
        setLoadingMore(false);
    };

    if (loading || searchPending) {
        return <DatesViewSkeleton />;
    }

    if (days.length === 0) {
        if (!hasenrolledcourses) {
            return (
                <div className="text-xs-center text-center mt-3" data-region="no-courses-empty-message">
                    <img src={nocoursesurl} className="timeline-empty-icon" alt="" />
                    <p className="text-muted mt-1">
                        <String identifier="nocoursesinprogress" component="block_timeline">{''}</String>
                    </p>
                </div>
            );
        }
        return (
            <div className="text-xs-center text-center mt-3" data-region="no-events-empty-message">
                <img src={noeventsurl} className="timeline-empty-icon" alt="" />
                <p className="text-muted mt-1">
                    <String identifier="noevents" component="block_timeline">{''}</String>
                </p>
            </div>
        );
    }

    return (
        <div data-region="timeline-view-dates">
            <div className="pb-2" data-region="event-list-wrapper">
                {days.map(day => (
                    <div key={day.dayTimestamp}>
                        <div className="mt-3" data-region="event-list-content-date" data-timestamp={day.dayTimestamp}>
                            <h4 className="h6 d-inline fw-bold px-2">
                                {day.events[0].formattedday}
                            </h4>
                        </div>
                        <div className="list-group list-group-flush">
                            {day.events.map(event => (
                                <EventListItem key={event.id} event={event} />
                            ))}
                        </div>
                    </div>
                ))}
            </div>

            {hasMore && (
                <div className="pt-1 pb-2 ps-2" data-region="more-events-button-container">
                    <Button
                        variant="secondary"
                        size="lg"
                        onClick={handleShowMore}
                        disabled={loadingMore}
                        data-action="more-events"
                        label={moreActivitiesLabel}
                        endIcon={loadingMore
                            ? <i className="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true" />
                            : undefined}
                    />
                </div>
            )}
        </div>
    );
}
