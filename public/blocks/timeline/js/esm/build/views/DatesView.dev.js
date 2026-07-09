var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Dates view for the Timeline block — events grouped by day with lazy loading.
 *
 * @module     block_timeline/views/DatesView
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect, useCallback } from "react";
import String from "@moodle/lms/core/String";
import { getString } from "@moodle/lms/core/stringUtils";
import { Button } from "@moodlehq/design-system";
import { getTimelineEvents } from "../repository";
import { DatesViewSkeleton } from "../Skeleton";
import EventListItem from "../EventListItem";
import { computeTimeRange, groupByDay, filterEvents } from "../utils";
const FIRST_LOAD_LIMIT = 5;
const MORE_LOAD_LIMIT = 10;
function DatesView({ midnight, offsets, searchvalue, nocoursesurl, noeventsurl, hasenrolledcourses, searchPending }) {
  const [days, setDays] = useState([]);
  const [loading, setLoading] = useState(true);
  const [hasMore, setHasMore] = useState(false);
  const [lastId, setLastId] = useState(0);
  const [loadingMore, setLoadingMore] = useState(false);
  const [moreActivitiesLabel, setMoreActivitiesLabel] = useState("");
  useEffect(() => {
    getString("moreactivities", "block_timeline").then(setMoreActivitiesLabel);
  }, []);
  const { starttime, endtime } = computeTimeRange(midnight, offsets);
  const load = useCallback(async (aftereventid, append) => {
    const result = await getTimelineEvents({
      timesortfrom: starttime,
      timesortto: endtime,
      aftereventid,
      limitnum: (append ? MORE_LOAD_LIMIT : FIRST_LOAD_LIMIT) + 1,
      searchvalue: searchvalue || null
    });
    let filtered = filterEvents(result.events, midnight, offsets.filteroverdue);
    const loadedAll = filtered.length <= (append ? MORE_LOAD_LIMIT : FIRST_LOAD_LIMIT);
    if (!loadedAll) {
      filtered.pop();
    }
    const newDays = groupByDay(filtered);
    if (append) {
      setDays((prev) => {
        if (newDays.length === 0) {
          return prev;
        }
        const merged = [...prev];
        if (merged.length > 0 && newDays.length > 0 && merged[merged.length - 1].dayTimestamp === newDays[0].dayTimestamp) {
          merged[merged.length - 1] = {
            ...merged[merged.length - 1],
            events: [...merged[merged.length - 1].events, ...newDays[0].events]
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
  }, [starttime, endtime, searchvalue, offsets.filteroverdue, midnight]);
  useEffect(() => {
    setLoading(true);
    setDays([]);
    setLastId(0);
    load(0, false).finally(() => setLoading(false));
  }, [load]);
  const handleShowMore = /* @__PURE__ */ __name(async () => {
    setLoadingMore(true);
    await load(lastId, true);
    setLoadingMore(false);
  }, "handleShowMore");
  if (loading || searchPending) {
    return /* @__PURE__ */ jsxDEV(DatesViewSkeleton, {}, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
      lineNumber: 127,
      columnNumber: 16
    }, this);
  }
  if (days.length === 0) {
    if (!hasenrolledcourses) {
      return /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center mt-3", "data-region": "no-courses-empty-message", children: [
        /* @__PURE__ */ jsxDEV("img", { src: nocoursesurl, className: "timeline-empty-icon", alt: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
          lineNumber: 134,
          columnNumber: 21
        }, this),
        /* @__PURE__ */ jsxDEV("p", { className: "text-muted mt-1", children: /* @__PURE__ */ jsxDEV(String, { identifier: "nocoursesinprogress", component: "block_timeline", children: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
          lineNumber: 136,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
          lineNumber: 135,
          columnNumber: 21
        }, this)
      ] }, void 0, true, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 133,
        columnNumber: 17
      }, this);
    }
    return /* @__PURE__ */ jsxDEV("div", { className: "text-xs-center text-center mt-3", "data-region": "no-events-empty-message", children: [
      /* @__PURE__ */ jsxDEV("img", { src: noeventsurl, className: "timeline-empty-icon", alt: "" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 143,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("p", { className: "text-muted mt-1", children: /* @__PURE__ */ jsxDEV(String, { identifier: "noevents", component: "block_timeline", children: "" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 145,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 144,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
      lineNumber: 142,
      columnNumber: 13
    }, this);
  }
  return /* @__PURE__ */ jsxDEV("div", { "data-region": "timeline-view-dates", children: [
    /* @__PURE__ */ jsxDEV("div", { className: "pb-2", "data-region": "event-list-wrapper", children: days.map((day) => /* @__PURE__ */ jsxDEV("div", { children: [
      /* @__PURE__ */ jsxDEV("div", { className: "mt-3", "data-region": "event-list-content-date", "data-timestamp": day.dayTimestamp, children: /* @__PURE__ */ jsxDEV("h4", { className: "h6 d-inline fw-bold px-2", children: day.events[0].formattedday }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 157,
        columnNumber: 29
      }, this) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 156,
        columnNumber: 25
      }, this),
      /* @__PURE__ */ jsxDEV("div", { className: "list-group list-group-flush", children: day.events.map((event) => /* @__PURE__ */ jsxDEV(EventListItem, { event }, event.id, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 163,
        columnNumber: 33
      }, this)) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 161,
        columnNumber: 25
      }, this)
    ] }, day.dayTimestamp, true, {
      fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
      lineNumber: 155,
      columnNumber: 21
    }, this)) }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
      lineNumber: 153,
      columnNumber: 13
    }, this),
    hasMore && /* @__PURE__ */ jsxDEV("div", { className: "pt-1 pb-2 ps-2", "data-region": "more-events-button-container", children: /* @__PURE__ */ jsxDEV(
      Button,
      {
        variant: "secondary",
        size: "lg",
        onClick: handleShowMore,
        disabled: loadingMore,
        "data-action": "more-events",
        label: moreActivitiesLabel,
        endIcon: loadingMore ? /* @__PURE__ */ jsxDEV("i", { className: "spinner-border spinner-border-sm ms-1", role: "status", "aria-hidden": "true" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
          lineNumber: 180,
          columnNumber: 31
        }, this) : void 0
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
        lineNumber: 172,
        columnNumber: 21
      },
      this
    ) }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
      lineNumber: 171,
      columnNumber: 17
    }, this)
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/views/DatesView.tsx",
    lineNumber: 152,
    columnNumber: 9
  }, this);
}
__name(DatesView, "DatesView");
export {
  DatesView as default
};
//# sourceMappingURL=DatesView.dev.js.map
