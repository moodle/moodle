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
 * Course pagination uses a "drain-until-visible" strategy: pages are loaded
 * greedily until COURSES_PER_PAGE visible courses are found (or pages are
 * exhausted), and the next batch is pre-loaded (lookahead) so "Show more
 * courses" only appears when more visible courses are actually available.
 *
 * Event pagination within a course uses "Show more activities" and calls
 * core_calendar_get_action_events_by_course with aftereventid pagination.
 *
 * @module     block_timeline/views/CoursesView
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useState, useEffect, useCallback, useRef} from 'react';
import String from '@moodle/lms/core/String';
import {getString} from '@moodle/lms/core/stringUtils';
import {Button} from '@moodlehq/design-system';
import {getEnrolledCourses, getEventsByCourses, getEventsByCourse} from '../repository';
import EventListItem from '../EventListItem';
import {computeTimeRange, groupByDay, filterEvents, getFormattedDays} from '../utils';
import type {CalendarEvent, CourseWithEvents, FilterOffsets} from '../types';

/**
 * Visible courses per "page" — how many courses we aim to accumulate before
 * stopping a greedy load pass.  PHP returns COURSES_PER_PAGE courses per call.
 */
const COURSES_PER_PAGE = 2;

/** Events shown per course on first load (PHP EVENTS_PER_COURSE = EVENTS_PER_PAGE + 1 sentinel). */
const EVENTS_PER_PAGE = 6;

/** Events loaded per "Show more activities" click. */
const MORE_EVENTS_LIMIT = 10;

interface PerCourseState {
    events: CalendarEvent[];
    hasMore: boolean;
    lastEventId: number;
    loading: boolean;
}

/**
 * Pre-loaded batch of courses ready to be appended to the display on the
 * next "Show more courses" click.
 */
interface Batch {
    courses: CourseWithEvents[];
    perCourse: Map<number, PerCourseState>;
    /** Offset for the WS call AFTER this batch (for subsequent lookahead). */
    nextOffset: number;
    /** Whether PHP indicated more pages exist after this batch was loaded. */
    hasMorePhp: boolean;
}


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

/**
 * Renders timeline events grouped by in-progress courses.
 *
 * Course-level pagination: "Show more courses" appends the pre-loaded next
 * batch and immediately starts loading the following one.
 *
 * Event-level pagination: per-course "Show more activities" button.
 */
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
    const [loading, setLoading] = useState(true);
    const [loadingMore, setLoadingMore] = useState(false);
    const [moreActivitiesLabel, setMoreActivitiesLabel] = useState('');
    const [moreCoursesLabel, setMoreCoursesLabel] = useState('');

    useEffect(() => {
        getString('moreactivities', 'block_timeline').then(setMoreActivitiesLabel);
        getString('morecourses', 'block_timeline').then(setMoreCoursesLabel);
    }, []);

    const {starttime, endtime} = computeTimeRange(midnight, offsets);
    const {filteroverdue} = offsets;

    /**
     * Load WS pages greedily until we accumulate COURSES_PER_PAGE visible
     * courses (after client-side filtering) or run out of pages.
     *
     * Only visible courses are returned; invisible ones (all events filtered
     * out) are silently discarded since they must not appear in the UI.
     */
    const loadUntilVisible = useCallback(async(startOffset: number): Promise<Batch> => {
        const visibleCourses: CourseWithEvents[] = [];
        const visiblePerCourse = new Map<number, PerCourseState>();
        let offset = startOffset;
        let hasMorePhp = false;

        do {
            // Request one extra course to detect whether more pages exist, mirroring
            // the sentinel trick core_course_get_enrolled_courses_by_timeline_classification's
            // own callers use — the WS itself has no "more available" flag.
            const coursesResult = await getEnrolledCourses({
                limit:       COURSES_PER_PAGE + 1,
                offset,
                searchvalue: searchvalue || null,
            });
            hasMorePhp = coursesResult.courses.length > COURSES_PER_PAGE;
            const pageCourses = hasMorePhp
                ? coursesResult.courses.slice(0, COURSES_PER_PAGE)
                : coursesResult.courses;
            offset = coursesResult.nextoffset;

            if (pageCourses.length > 0) {
                const eventsResult = await getEventsByCourses({
                    courseids:    pageCourses.map(c => c.id),
                    timesortfrom: starttime,
                    timesortto:   endtime,
                    limitnum:     EVENTS_PER_PAGE + 1,
                    searchvalue:  searchvalue || null,
                });
                const eventsByCourseId = new Map(eventsResult.groupedbycourse.map(g => [g.courseid, g.events]));
                const dayMap = await getFormattedDays(
                    eventsResult.groupedbycourse.flatMap(g => g.events.map(e => e.timeusermidnight))
                );

                for (const course of pageCourses) {
                    const events = (eventsByCourseId.get(course.id) ?? []).map(e => ({
                        ...e,
                        formattedday: dayMap.get(e.timeusermidnight) ?? '',
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

    // Keep a ref to loadUntilVisible so the effect closure always gets the latest version.
    const loadUntilVisibleRef = useRef(loadUntilVisible);
    loadUntilVisibleRef.current = loadUntilVisible;

    useEffect(() => {
        let cancelled = false;

        const init = async() => {
            setLoading(true);
            setDisplayedCourses([]);
            setPerCourse(new Map());
            setNextBatch(null);

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

    const handleShowMoreCourses = async() => {
        if (!nextBatch || nextBatch.courses.length === 0) {
            return;
        }

        setLoadingMore(true);

        const consumed = nextBatch;
        setDisplayedCourses(prev => [...prev, ...consumed.courses]);
        setPerCourse(prev => new Map([...prev, ...consumed.perCourse]));

        if (consumed.hasMorePhp) {
            const peek = await loadUntilVisibleRef.current(consumed.nextOffset);
            setNextBatch(peek);
        } else {
            setNextBatch({courses: [], perCourse: new Map(), nextOffset: 0, hasMorePhp: false});
        }

        setLoadingMore(false);
    };

    /**
     * Loads and appends the next page of events for a single course's "show more" button.
     *
     * @param courseId id of the course whose event list is being expanded
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
            const enriched = result.events.map(e => ({
                ...e,
                formattedday: dayMap.get(e.timeusermidnight) ?? '',
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

    const hasMoreCourses = nextBatch !== null && nextBatch.courses.length > 0;

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
                        disabled={loadingMore}
                        data-action="more-courses"
                        label={moreCoursesLabel}
                        endIcon={loadingMore
                            ? <i className="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true" />
                            : undefined}
                    />
                </div>
            )}
        </>
    );
}
