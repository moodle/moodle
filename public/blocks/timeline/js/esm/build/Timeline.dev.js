var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Root Timeline block React component.
 *
 * Mounted automatically via data-react-component="@moodle/lms/block_timeline/Timeline"
 * by core/react_autoinit.
 *
 * @module     block_timeline/Timeline
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useCallback } from "react";
import DayFilter from "@moodle/lms/block_timeline/nav/DayFilter";
import ViewSelector from "@moodle/lms/block_timeline/nav/ViewSelector";
import Search from "@moodle/lms/block_timeline/nav/Search";
import DatesView from "@moodle/lms/block_timeline/views/DatesView";
import CoursesView from "@moodle/lms/block_timeline/views/CoursesView";
import { setUserPreference } from "./repository";
const PREF_FILTER = "block_timeline_user_filter_preference";
const PREF_ORDER = "block_timeline_user_sort_preference";
const FILTER_OFFSETS = {
  all: { daysoffset: -14, dayslimit: null, filteroverdue: false },
  overdue: { daysoffset: -14, dayslimit: null, filteroverdue: true },
  next7days: { daysoffset: 0, dayslimit: 7, filteroverdue: false },
  next30days: { daysoffset: 0, dayslimit: 30, filteroverdue: false },
  next3months: { daysoffset: 0, dayslimit: 90, filteroverdue: false },
  next6months: { daysoffset: 0, dayslimit: 180, filteroverdue: false }
};
function Timeline({ midnight, filter, order, limit, nocoursesurl, noeventsurl, hasenrolledcourses }) {
  const [activeFilter, setActiveFilter] = useState(filter);
  const [activeOrder, setActiveOrder] = useState(order);
  const [searchvalue, setSearchvalue] = useState("");
  const [searchPending, setSearchPending] = useState(false);
  const handleFilterChange = useCallback((next) => {
    setActiveFilter(next);
    setUserPreference(PREF_FILTER, next);
  }, []);
  const handleOrderChange = useCallback((next) => {
    setActiveOrder(next);
    setUserPreference(PREF_ORDER, next);
  }, []);
  const handleSearch = useCallback((value) => {
    setSearchvalue(value);
  }, []);
  const offsets = FILTER_OFFSETS[activeFilter];
  const showCoursesView = activeOrder === "sortbycourses";
  return /* @__PURE__ */ jsxDEV("div", { "data-region": "timeline", className: "block-timeline", children: [
    /* @__PURE__ */ jsxDEV("div", { className: "p-0 px-2", children: [
      /* @__PURE__ */ jsxDEV("div", { className: "d-flex flex-wrap gap-1 g-0", children: [
        /* @__PURE__ */ jsxDEV("div", { children: /* @__PURE__ */ jsxDEV(DayFilter, { activeFilter, onChange: handleFilterChange }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 101,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 100,
          columnNumber: 21
        }, this),
        /* @__PURE__ */ jsxDEV("div", { children: /* @__PURE__ */ jsxDEV(ViewSelector, { activeOrder, onChange: handleOrderChange }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 104,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 103,
          columnNumber: 21
        }, this),
        /* @__PURE__ */ jsxDEV("div", { className: "flex-grow-1 d-flex justify-content-end nav-search", children: /* @__PURE__ */ jsxDEV(Search, { onSearch: handleSearch, onSearching: setSearchPending }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 107,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 106,
          columnNumber: 21
        }, this)
      ] }, void 0, true, {
        fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
        lineNumber: 99,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("div", { className: "pb-3 px-2 border-bottom" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
        lineNumber: 110,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
      lineNumber: 98,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { className: "p-0", children: [
      /* @__PURE__ */ jsxDEV("div", { "data-region": "view-dates", role: "tabpanel", className: showCoursesView ? "d-none" : "", children: !showCoursesView && /* @__PURE__ */ jsxDEV(
        DatesView,
        {
          midnight,
          offsets,
          searchvalue,
          nocoursesurl,
          noeventsurl,
          hasenrolledcourses,
          limit,
          searchPending
        },
        void 0,
        false,
        {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 116,
          columnNumber: 25
        },
        this
      ) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
        lineNumber: 114,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("div", { "data-region": "view-courses", role: "tabpanel", className: showCoursesView ? "" : "d-none", children: showCoursesView && /* @__PURE__ */ jsxDEV(
        CoursesView,
        {
          midnight,
          offsets,
          searchvalue,
          nocoursesurl,
          noeventsurl,
          hasenrolledcourses,
          searchPending
        },
        void 0,
        false,
        {
          fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
          lineNumber: 130,
          columnNumber: 25
        },
        this
      ) }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
        lineNumber: 128,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
      lineNumber: 113,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/Timeline.tsx",
    lineNumber: 97,
    columnNumber: 9
  }, this);
}
__name(Timeline, "Timeline");
export {
  Timeline as default
};
//# sourceMappingURL=Timeline.dev.js.map
