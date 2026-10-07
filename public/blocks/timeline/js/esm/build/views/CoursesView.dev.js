var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * Courses view for the Timeline block — events grouped by course with lazy loading.
 *
 * @module     block_timeline/views/CoursesView
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
  useState,
  useEffect,
  useCallback,
  useRef
} from "react";
import { Button } from "@moodlehq/design-system";
import {
  getEnrolledCourses,
  getEventsByCourses,
  getEventsByCourse,
  getFormattedDays,
  getFormattedEventDateTimes
} from "../repository";
import { computeTimeRange, groupByDay, filterEvents } from "../common/utils";
import EventListItem from "@moodle/lms/block_timeline/views/EventListItem";
import { getString } from "@moodle/lms/core/stringUtils";
import String from "@moodle/lms/core/String";
const COURSES_PER_PAGE = 2;
const EVENTS_PER_PAGE = 6;
const MORE_EVENTS_LIMIT = 10;
function processInitialEvents(raw, midnight, filteroverdue) {
  const filtered = filterEvents(raw, midnight, filteroverdue);
  const hasMore = filtered.length > EVENTS_PER_PAGE;
  const shown = hasMore ? filtered.slice(0, EVENTS_PER_PAGE) : filtered;
  const lastId = shown.length > 0 ? shown[shown.length - 1].id : 0;
  return { shown, hasMore, lastId };
}
__name(processInitialEvents, "processInitialEvents");
function CoursesView({
  midnight,
  offsets,
  searchvalue,
  nocoursesurl,
  noeventsurl,
  hasenrolledcourses,
  searchPending
}) {
  const [displayedCourses, setDisplayedCourses] = useState([]);
  const [perCourse, setPerCourse] = useState(/* @__PURE__ */ new Map());
  const [nextBatch, setNextBatch] = useState(null);
  const [hasMoreCourses, setHasMoreCourses] = useState(false);
  const [preparingNextBatch, setPreparingNextBatch] = useState(false);
  const [loading, setLoading] = useState(true);
  const [moreActivitiesLabel, setMoreActivitiesLabel] = useState("");
  const [moreCoursesLabel, setMoreCoursesLabel] = useState("");
  useEffect(() => {
    void getString("moreactivities", "block_timeline").then(setMoreActivitiesLabel);
    void getString("morecourses", "block_timeline").then(setMoreCoursesLabel);
  }, []);
  const { starttime, endtime } = computeTimeRange(midnight, offsets);
  const { filteroverdue } = offsets;
  const loadUntilVisible = useCallback(async (startOffset) => {
    const visibleCourses = [];
    const visiblePerCourse = /* @__PURE__ */ new Map();
    let offset = startOffset;
    let hasMorePhp = false;
    do {
      const coursesResult = await getEnrolledCourses({
        limit: COURSES_PER_PAGE + 1,
        offset,
        searchvalue: searchvalue === "" ? null : searchvalue
      });
      hasMorePhp = coursesResult.courses.length > COURSES_PER_PAGE;
      const pageCourses = hasMorePhp ? coursesResult.courses.slice(0, COURSES_PER_PAGE) : coursesResult.courses;
      offset += pageCourses.length;
      if (pageCourses.length > 0) {
        const eventsResult = await getEventsByCourses({
          courseids: pageCourses.map((c) => c.id),
          timesortfrom: starttime,
          timesortto: endtime,
          limitnum: EVENTS_PER_PAGE + 1,
          searchvalue: searchvalue === "" ? null : searchvalue
        });
        const eventsByCourseId = new Map(eventsResult.groupedbycourse.map((g) => [g.courseid, g.events]));
        const allEvents = eventsResult.groupedbycourse.flatMap((g) => g.events);
        const dayMap = await getFormattedDays(allEvents.map((event) => event.timeusermidnight));
        const dateTimeMap = await getFormattedEventDateTimes(allEvents.map((event) => event.timesort));
        for (const course of pageCourses) {
          const events = (eventsByCourseId.get(course.id) ?? []).map((event) => ({
            ...event,
            formattedday: dayMap.get(event.timeusermidnight) ?? "",
            formatteddatetime: dateTimeMap.get(event.timesort) ?? ""
          }));
          const { shown, hasMore, lastId } = processInitialEvents(events, midnight, filteroverdue);
          if (shown.length > 0) {
            visibleCourses.push({ ...course, events });
            visiblePerCourse.set(course.id, {
              events: shown,
              hasMore,
              lastEventId: lastId,
              loading: false
            });
          }
        }
      }
    } while (visibleCourses.length < COURSES_PER_PAGE && hasMorePhp);
    return {
      courses: visibleCourses,
      perCourse: visiblePerCourse,
      nextOffset: offset,
      hasMorePhp
    };
  }, [starttime, endtime, searchvalue, midnight, filteroverdue]);
  const loadUntilVisibleReference = useRef(loadUntilVisible);
  loadUntilVisibleReference.current = loadUntilVisible;
  useEffect(() => {
    let cancelled = false;
    const init = /* @__PURE__ */ __name(async () => {
      setLoading(true);
      setDisplayedCourses([]);
      setPerCourse(/* @__PURE__ */ new Map());
      setNextBatch(null);
      setHasMoreCourses(false);
      const batch1 = await loadUntilVisibleReference.current(0);
      if (cancelled) {
        return;
      }
      setDisplayedCourses(batch1.courses);
      setPerCourse(new Map(batch1.perCourse));
      if (batch1.hasMorePhp) {
        const batch2 = await loadUntilVisibleReference.current(batch1.nextOffset);
        if (!cancelled) {
          setNextBatch(batch2);
          setHasMoreCourses(batch2.courses.length > 0);
        }
      } else {
        setNextBatch({
          courses: [],
          perCourse: /* @__PURE__ */ new Map(),
          nextOffset: 0,
          hasMorePhp: false
        });
      }
      if (!cancelled) {
        setLoading(false);
      }
    }, "init");
    void init();
    return () => {
      cancelled = true;
    };
  }, [loadUntilVisible]);
  if (loading || searchPending) {
    return null;
  }
  if (displayedCourses.length === 0) {
    if (hasenrolledcourses) {
      return /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center mt-3", "data-region": "no-events-empty-message", children: [
        /* @__PURE__ */ jsxDEV("img", { src: noeventsurl, className: "timeline-empty-icon", alt: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
          lineNumber: 232,
          columnNumber: 21
        }, this),
        /* @__PURE__ */ jsxDEV("p", { className: "text-muted mt-1", children: /* @__PURE__ */ jsxDEV(String, { identifier: "noevents", component: "block_timeline", children: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
          lineNumber: 234,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
          lineNumber: 233,
          columnNumber: 21
        }, this)
      ] }, void 0, true, {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 231,
        columnNumber: 17
      }, this);
    }
    return /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center mt-3", "data-region": "no-courses-empty-message", children: [
      /* @__PURE__ */ jsxDEV("img", { src: nocoursesurl, className: "timeline-empty-icon", alt: "" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 242,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("p", { className: "text-muted mt-1", children: /* @__PURE__ */ jsxDEV(String, { identifier: "nocoursesinprogress", component: "block_timeline", children: "" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 244,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 243,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
      lineNumber: 241,
      columnNumber: 13
    }, this);
  }
  const handleShowMoreCourses = /* @__PURE__ */ __name(async () => {
    if (preparingNextBatch || !nextBatch || nextBatch.courses.length === 0) {
      return;
    }
    const consumed = nextBatch;
    setDisplayedCourses((previous) => [...previous, ...consumed.courses]);
    setPerCourse((previous) => new Map([...previous, ...consumed.perCourse]));
    setPreparingNextBatch(true);
    if (consumed.hasMorePhp) {
      const peek = await loadUntilVisibleReference.current(consumed.nextOffset);
      setNextBatch(peek);
      setHasMoreCourses(peek.courses.length > 0);
    } else {
      setNextBatch({
        courses: [],
        perCourse: /* @__PURE__ */ new Map(),
        nextOffset: 0,
        hasMorePhp: false
      });
      setHasMoreCourses(false);
    }
    setPreparingNextBatch(false);
  }, "handleShowMoreCourses");
  const handleShowMoreActivities = /* @__PURE__ */ __name(async (courseId) => {
    const state = perCourse.get(courseId);
    if (!state) {
      return;
    }
    setPerCourse((previous) => {
      const next = new Map(previous);
      next.set(courseId, { ...state, loading: true });
      return next;
    });
    try {
      const result = await getEventsByCourse({
        courseid: courseId,
        timesortfrom: starttime,
        timesortto: endtime,
        aftereventid: state.lastEventId,
        limitnum: MORE_EVENTS_LIMIT + 1,
        searchvalue: searchvalue === "" ? null : searchvalue
      });
      const dayMap = await getFormattedDays(result.events.map((event) => event.timeusermidnight));
      const dateTimeMap = await getFormattedEventDateTimes(result.events.map((event) => event.timesort));
      const enriched = result.events.map((event) => ({
        ...event,
        formattedday: dayMap.get(event.timeusermidnight) ?? "",
        formatteddatetime: dateTimeMap.get(event.timesort) ?? ""
      }));
      const filtered = filterEvents(enriched, midnight, filteroverdue);
      const moreExist = filtered.length > MORE_EVENTS_LIMIT;
      const newEvents = moreExist ? filtered.slice(0, MORE_EVENTS_LIMIT) : filtered;
      const updatedEvents = [...state.events, ...newEvents];
      const newLastId = updatedEvents.length > 0 ? updatedEvents[updatedEvents.length - 1].id : state.lastEventId;
      setPerCourse((previous) => {
        const next = new Map(previous);
        next.set(courseId, {
          events: updatedEvents,
          hasMore: moreExist,
          lastEventId: newLastId,
          loading: false
        });
        return next;
      });
    } catch {
      setPerCourse((previous) => {
        const next = new Map(previous);
        next.set(courseId, { ...state, loading: false });
        return next;
      });
    }
  }, "handleShowMoreActivities");
  return /* @__PURE__ */ jsxDEV(Fragment, { children: [
    /* @__PURE__ */ jsxDEV("ul", { className: "list-group unstyled", "data-region": "courses-list", children: displayedCourses.map((course) => {
      const state = perCourse.get(course.id);
      if (!state) {
        return null;
      }
      return /* @__PURE__ */ jsxDEV("li", { className: "list-group-item mt-3 p-0 border-0", children: /* @__PURE__ */ jsxDEV(
        "div",
        {
          "data-region": "course-events-container",
          id: `course-events-container-${course.id}`,
          "data-course-id": course.id,
          children: [
            /* @__PURE__ */ jsxDEV("h4", { className: "h5 fw-bold", children: course.fullname }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 359,
              columnNumber: 33
            }, this),
            /* @__PURE__ */ jsxDEV("div", { className: "pb-2", "data-region": "event-list-wrapper", children: state.events.length === 0 ? /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center mt-3", "data-region": "no-events-empty-message", children: /* @__PURE__ */ jsxDEV("p", { className: "text-muted mt-1", children: /* @__PURE__ */ jsxDEV(
              String,
              {
                identifier: "noevents",
                component: "block_timeline",
                children: ""
              },
              void 0,
              false,
              {
                fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                lineNumber: 365,
                columnNumber: 53
              },
              this
            ) }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 364,
              columnNumber: 49
            }, this) }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 363,
              columnNumber: 45
            }, this) : groupByDay(state.events).map(({ dayTimestamp, events }) => /* @__PURE__ */ jsxDEV("div", { children: [
              /* @__PURE__ */ jsxDEV(
                "div",
                {
                  className: "mt-3",
                  "data-region": "event-list-content-date",
                  "data-timestamp": dayTimestamp,
                  children: /* @__PURE__ */ jsxDEV("h4", { className: "h6 d-inline", children: events[0].formattedday }, void 0, false, {
                    fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                    lineNumber: 380,
                    columnNumber: 57
                  }, this)
                },
                void 0,
                false,
                {
                  fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                  lineNumber: 375,
                  columnNumber: 53
                },
                this
              ),
              /* @__PURE__ */ jsxDEV("div", { className: "list-group list-group-flush", children: events.map((event) => /* @__PURE__ */ jsxDEV(EventListItem, { event, courseview: true }, event.id, false, {
                fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                lineNumber: 384,
                columnNumber: 61
              }, this)) }, void 0, false, {
                fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                lineNumber: 382,
                columnNumber: 53
              }, this)
            ] }, dayTimestamp, true, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 374,
              columnNumber: 49
            }, this)) }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 360,
              columnNumber: 33
            }, this),
            state.hasMore && /* @__PURE__ */ jsxDEV("div", { className: "pt-1 pb-2 ps-2", "data-region": "more-events-button-container", children: /* @__PURE__ */ jsxDEV(
              Button,
              {
                variant: "secondary",
                size: "sm",
                onClick: async () => handleShowMoreActivities(course.id),
                disabled: state.loading,
                "data-action": "more-events",
                label: moreActivitiesLabel,
                endIcon: state.loading ? /* @__PURE__ */ jsxDEV(
                  "i",
                  {
                    className: "spinner-border spinner-border-sm ms-1",
                    role: "status",
                    "aria-hidden": "true"
                  },
                  void 0,
                  false,
                  {
                    fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                    lineNumber: 403,
                    columnNumber: 53
                  },
                  this
                ) : void 0
              },
              void 0,
              false,
              {
                fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
                lineNumber: 394,
                columnNumber: 41
              },
              this
            ) }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
              lineNumber: 393,
              columnNumber: 37
            }, this)
          ]
        },
        void 0,
        true,
        {
          fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
          lineNumber: 354,
          columnNumber: 29
        },
        this
      ) }, course.id, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 353,
        columnNumber: 25
      }, this);
    }) }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
      lineNumber: 345,
      columnNumber: 13
    }, this),
    hasMoreCourses && /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center pt-3", "data-region": "more-courses-button-container", children: /* @__PURE__ */ jsxDEV(
      Button,
      {
        variant: "primary",
        size: "lg",
        onClick: handleShowMoreCourses,
        disabled: preparingNextBatch,
        "data-action": "more-courses",
        label: moreCoursesLabel
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
        lineNumber: 421,
        columnNumber: 21
      },
      this
    ) }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
      lineNumber: 420,
      columnNumber: 17
    }, this)
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/views/CoursesView.tsx",
    lineNumber: 344,
    columnNumber: 9
  }, this);
}
__name(CoursesView, "CoursesView");
export {
  CoursesView as default
};
//# sourceMappingURL=CoursesView.dev.js.map
