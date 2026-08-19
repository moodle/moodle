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
 * Courses view for the Timeline block — events grouped by course with lazy loading.
 *
 * @module     block_timeline/views/CoursesView
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect, useCallback, useRef} from 'react';
import String from '@moodle/lms/core/String';
import {getString} from '@moodle/lms/core/stringUtils';
import {Button} from '@moodlehq/design-system';
import {
    getEnrolledCourses, getEventsByCourses, getEventsByCourse, getFormattedDays, getFormattedEventDateTimes,
} from '../repository';
import EventListItem from '@moodle/lms/block_timeline/views/EventListItem';
import {computeTimeRange, groupByDay, filterEvents} from '../common/utils';
import type {CalendarEvent, CourseWithEvents, FilterOffsets} from '../common/types';

const COURSES_PER_PAGE = 2;
const EVENTS_PER_PAGE = 6;
const MORE_EVENTS_LIMIT = 10;

interface PerCourseState {
    events: CalendarEvent[];
    hasMore: boolean;
    lastEventId: number;
    loading: boolean;
}

interface Batch {
    courses: CourseWithEvents[];
    perCourse: Map<number, PerCourseState>;
    nextOffset: number;
    hasMorePhp: boolean;
}

/**
 * Filters a course's raw events and slices them down to the first-load page size.
 *
 * @param raw unfiltered events fetched for the course.
 * @param midnight start-of-day timestamp used by the overdue/open-event filter.
 * @param filteroverdue whether only overdue events should be kept.
 */
function processInitialEvents(
    raw: CalendarEvent[],
    midnight: number,
    filteroverdue: boolean,
): {shown: CalendarEvent[]; hasMore: boolean; lastId: number} {
    const filtered = filterEvents(raw, midnight, filteroverdue);
    const hasMore = filtered.length > EVENTS_PER_PAGE;
    const shown = hasMore ? filtered.slice(0, EVENTS_PER_PAGE) : filtered;
    const lastId = shown.length > 0 ? shown[shown.length - 1].id : 0;
    return {shown, hasMore, lastId};
}

interface CoursesViewProps {
    midnight: number;
    offsets: FilterOffsets;
    searchvalue: string;
    nocoursesurl: string;
    noeventsurl: string;
    hasenrolledcourses: boolean;
    /** True while the search debounce is pending — suppresses stale results until it resolves. */
    searchPending?: boolean;
}

/** Renders timeline events grouped by in-progress courses with lazy-load pagination. */
export default function CoursesView({
    midnight,
    offsets,
    searchvalue,
    nocoursesurl,
    noeventsurl,
    hasenrolledcourses,
    searchPending,
}: CoursesViewProps) {
    const [displayedCourses, setDisplayedCourses] = useState<CourseWithEvents[]>([]);
    const [perCourse, setPerCourse] = useState<Map<number, PerCourseState>>(new Map());
    const [nextBatch, setNextBatch] = useState<Batch | null>(null);
    const [hasMoreCourses, setHasMoreCourses] = useState(false);
    const [preparingNextBatch, setPreparingNextBatch] = useState(false);
    const [loading, setLoading] = useState(true);
    const [moreActivitiesLabel, setMoreActivitiesLabel] = useState('');
    const [moreCoursesLabel, setMoreCoursesLabel] = useState('');

    useEffect(() => {
        getString('moreactivities', 'block_timeline').then(setMoreActivitiesLabel);
        getString('morecourses', 'block_timeline').then(setMoreCoursesLabel);
    }, []);

    const {starttime, endtime} = computeTimeRange(midnight, offsets);
    const {filteroverdue} = offsets;

    /**
     * Loads course pages until COURSES_PER_PAGE visible courses are found or pages run out.
     *
     * @param startOffset WS course offset to resume loading from.
     */
    const loadUntilVisible = useCallback(async(startOffset: number): Promise<Batch> => {
        const visibleCourses: CourseWithEvents[] = [];
        const visiblePerCourse = new Map<number, PerCourseState>();
        let offset = startOffset;
        let hasMorePhp = false;

        do {
            // Request one extra course as a sentinel — the WS has no "more available" flag.
            const coursesResult = await getEnrolledCourses({
                limit:       COURSES_PER_PAGE + 1,
                offset,
                searchvalue: searchvalue || null,
            });
            hasMorePhp = coursesResult.courses.length > COURSES_PER_PAGE;
            const pageCourses = hasMorePhp
                ? coursesResult.courses.slice(0, COURSES_PER_PAGE)
                : coursesResult.courses;
            // Advance by courses kept, not coursesResult.nextoffset (that counts the sentinel too).
            offset += pageCourses.length;

            if (pageCourses.length > 0) {
                const eventsResult = await getEventsByCourses({
                    courseids:    pageCourses.map(c => c.id),
                    timesortfrom: starttime,
                    timesortto:   endtime,
                    limitnum:     EVENTS_PER_PAGE + 1,
                    searchvalue:  searchvalue || null,
                });
                const eventsByCourseId = new Map(eventsResult.groupedbycourse.map(g => [g.courseid, g.events]));
                const allEvents = eventsResult.groupedbycourse.flatMap(g => g.events);
                const dayMap = await getFormattedDays(allEvents.map(e => e.timeusermidnight));
                const dateTimeMap = await getFormattedEventDateTimes(allEvents.map(e => e.timesort));

                for (const course of pageCourses) {
                    const events = (eventsByCourseId.get(course.id) ?? []).map(e => ({
                        ...e,
                        formattedday:      dayMap.get(e.timeusermidnight) ?? '',
                        formatteddatetime: dateTimeMap.get(e.timesort) ?? '',
                    }));
                    const {shown, hasMore, lastId} = processInitialEvents(events, midnight, filteroverdue);
                    if (shown.length > 0) {
                        visibleCourses.push({...course, events});
                        visiblePerCourse.set(course.id, {events: shown, hasMore, lastEventId: lastId, loading: false});
                    }
                }
            }
        } while (visibleCourses.length < COURSES_PER_PAGE && hasMorePhp);

        return {courses: visibleCourses, perCourse: visiblePerCourse, nextOffset: offset, hasMorePhp};
    }, [starttime, endtime, searchvalue, midnight, filteroverdue]);

    const loadUntilVisibleRef = useRef(loadUntilVisible);
    loadUntilVisibleRef.current = loadUntilVisible;

    useEffect(() => {
        let cancelled = false;

        const init = async() => {
            setLoading(true);
            setDisplayedCourses([]);
            setPerCourse(new Map());
            setNextBatch(null);
            setHasMoreCourses(false);

            const batch1 = await loadUntilVisibleRef.current(0);
            if (cancelled) {
                return;
            }

            setDisplayedCourses(batch1.courses);
            setPerCourse(new Map(batch1.perCourse));

            if (batch1.hasMorePhp) {
                const batch2 = await loadUntilVisibleRef.current(batch1.nextOffset);
                if (!cancelled) {
                    setNextBatch(batch2);
                    setHasMoreCourses(batch2.courses.length > 0);
                }
            } else {
                setNextBatch({courses: [], perCourse: new Map(), nextOffset: 0, hasMorePhp: false});
            }

            if (!cancelled) {
                setLoading(false);
            }
        };

        init();
        return () => {
            cancelled = true;
        };
    }, [loadUntilVisible]);

    /**
     * Reveals the pre-loaded next batch and kicks off pre-loading the one after it.
     *
     * The reveal itself is always instant (the batch is already in state), so there is
     * nothing to show a spinner for — but the button stays visible and disabled while
     * the following batch pre-loads, matching legacy's "disable, don't hide, while a
     * fetch is in flight" pattern. Disabling (rather than hiding) is also what prevents
     * a double-click from re-appending the same batch.
     */
    const handleShowMoreCourses = async() => {
        if (preparingNextBatch || !nextBatch || nextBatch.courses.length === 0) {
            return;
        }

        const consumed = nextBatch;
        setDisplayedCourses(prev => [...prev, ...consumed.courses]);
        setPerCourse(prev => new Map([...prev, ...consumed.perCourse]));
        setPreparingNextBatch(true);

        if (consumed.hasMorePhp) {
            const peek = await loadUntilVisibleRef.current(consumed.nextOffset);
            setNextBatch(peek);
            setHasMoreCourses(peek.courses.length > 0);
        } else {
            setNextBatch({courses: [], perCourse: new Map(), nextOffset: 0, hasMorePhp: false});
            setHasMoreCourses(false);
        }

        setPreparingNextBatch(false);
    };

    /**
     * Loads and appends the next page of events for a single course's "show more" button.
     *
     * @param courseId id of the course whose event list is being expanded.
     */
    const handleShowMoreActivities = async(courseId: number) => {
        const state = perCourse.get(courseId);
        if (!state) {
            return;
        }

        setPerCourse(prev => {
            const next = new Map(prev);
            next.set(courseId, {...state, loading: true});
            return next;
        });

        try {
            const result = await getEventsByCourse({
                courseid:     courseId,
                timesortfrom: starttime,
                timesortto:   endtime,
                aftereventid: state.lastEventId,
                limitnum:     MORE_EVENTS_LIMIT + 1,
                searchvalue:  searchvalue || null,
            });

            const dayMap = await getFormattedDays(result.events.map(e => e.timeusermidnight));
            const dateTimeMap = await getFormattedEventDateTimes(result.events.map(e => e.timesort));
            const enriched = result.events.map(e => ({
                ...e,
                formattedday:      dayMap.get(e.timeusermidnight) ?? '',
                formatteddatetime: dateTimeMap.get(e.timesort) ?? '',
            }));

            const filtered = filterEvents(enriched, midnight, filteroverdue);
            const moreExist = filtered.length > MORE_EVENTS_LIMIT;
            const newEvents = moreExist ? filtered.slice(0, MORE_EVENTS_LIMIT) : filtered;
            const updatedEvents = [...state.events, ...newEvents];
            const newLastId = updatedEvents.length > 0 ? updatedEvents[updatedEvents.length - 1].id : state.lastEventId;

            setPerCourse(prev => {
                const next = new Map(prev);
                next.set(courseId, {
                    events:      updatedEvents,
                    hasMore:     moreExist,
                    lastEventId: newLastId,
                    loading:     false,
                });
                return next;
            });
        } catch {
            setPerCourse(prev => {
                const next = new Map(prev);
                next.set(courseId, {...state, loading: false});
                return next;
            });
        }
    };

    if (loading || searchPending) {
        return null;
    }

    if (displayedCourses.length === 0) {
        if (hasenrolledcourses) {
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
            <div className="text-xs-center text-center mt-3" data-region="no-courses-empty-message">
                <img src={nocoursesurl} className="timeline-empty-icon" alt="" />
                <p className="text-muted mt-1">
                    <String identifier="nocoursesinprogress" component="block_timeline">{''}</String>
                </p>
            </div>
        );
    }

    return (
        <>
            <ul className="list-group unstyled" data-region="courses-list">
                {displayedCourses.map(course => {
                    const state = perCourse.get(course.id);
                    if (!state) {
                        return null;
                    }
                    return (
                        <li key={course.id} className="list-group-item mt-3 p-0 border-0">
                            <div
                                data-region="course-events-container"
                                id={`course-events-container-${course.id}`}
                                data-course-id={course.id}
                                className="px-2"
                            >
                                <h4 className="h5 fw-bold">{course.fullname}</h4>
                                <div className="pb-2" data-region="event-list-wrapper">
                                    {state.events.length === 0 ? (
                                        <div className="text-xs-center text-center mt-3" data-region="no-events-empty-message">
                                            <p className="text-muted mt-1">
                                                <String
                                                    identifier="noevents"
                                                    component="block_timeline"
                                                >{''}</String>
                                            </p>
                                        </div>
                                    ) : (
                                        groupByDay(state.events).map(({dayTimestamp, events}) => (
                                            <div key={dayTimestamp}>
                                                <div
                                                    className="mt-3"
                                                    data-region="event-list-content-date"
                                                    data-timestamp={dayTimestamp}
                                                >
                                                    <h4 className="h6 d-inline">{events[0].formattedday}</h4>
                                                </div>
                                                <div className="list-group list-group-flush">
                                                    {events.map(event => (
                                                        <EventListItem key={event.id} event={event} courseview />
                                                    ))}
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>

                                {state.hasMore && (
                                    <div className="pt-1 pb-2 ps-2" data-region="more-events-button-container">
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            onClick={() => handleShowMoreActivities(course.id)}
                                            disabled={state.loading}
                                            data-action="more-events"
                                            label={moreActivitiesLabel}
                                            endIcon={state.loading ? (
                                                <i
                                                    className="spinner-border spinner-border-sm ms-1"
                                                    role="status"
                                                    aria-hidden="true"
                                                />
                                            ) : undefined}
                                        />
                                    </div>
                                )}
                            </div>
                        </li>
                    );
                })}
            </ul>

            {hasMoreCourses && (
                <div className="text-xs-center text-center pt-3" data-region="more-courses-button-container">
                    <Button
                        variant="primary"
                        size="lg"
                        onClick={handleShowMoreCourses}
                        disabled={preparingNextBatch}
                        data-action="more-courses"
                        label={moreCoursesLabel}
                    />
                </div>
            )}
        </>
    );
}
