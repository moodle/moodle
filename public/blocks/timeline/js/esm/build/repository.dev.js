var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Data-access layer for the Timeline block — wraps block_timeline and core_calendar web services.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { fetchOne } from "@moodle/lms/core/ajax";
const getTimelineEvents = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "block_timeline_get_timeline_events",
    args: {
      timesortfrom: args.timesortfrom ?? 0,
      timesortto: args.timesortto ?? null,
      aftereventid: args.aftereventid ?? 0,
      limitnum: args.limitnum ?? 20,
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getTimelineEvents");
const getCoursesWithEvents = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "block_timeline_get_courses_with_events",
    args: {
      starttime: args.starttime ?? null,
      endtime: args.endtime ?? null,
      limit: args.limit ?? 2,
      offset: args.offset ?? 0,
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getCoursesWithEvents");
const getEventsByCourse = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "core_calendar_get_action_events_by_course",
    args: {
      courseid: args.courseid,
      timesortfrom: args.timesortfrom,
      timesortto: args.timesortto ?? null,
      aftereventid: args.aftereventid ?? 0,
      limitnum: args.limitnum ?? 20,
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getEventsByCourse");
export {
  getCoursesWithEvents,
  getEventsByCourse,
  getTimelineEvents
};
//# sourceMappingURL=repository.dev.js.map
