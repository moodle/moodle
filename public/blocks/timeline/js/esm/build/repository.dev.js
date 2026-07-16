var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Data-access layer for the Timeline block — wraps existing core_calendar and
 * core_course web services; block_timeline defines none of its own.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { fetchOne } from "@moodle/lms/core/ajax";
const getTimelineEvents = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "core_calendar_get_action_events_by_timesort",
    args: {
      timesortfrom: args.timesortfrom ?? 0,
      timesortto: args.timesortto ?? null,
      aftereventid: args.aftereventid ?? 0,
      limitnum: args.limitnum ?? 20,
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getTimelineEvents");
const getEnrolledCourses = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "core_course_get_enrolled_courses_by_timeline_classification",
    args: {
      classification: "all",
      limit: args.limit ?? 2,
      offset: args.offset ?? 0,
      sort: "fullname ASC",
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getEnrolledCourses");
const getEventsByCourses = /* @__PURE__ */ __name((args) => {
  return fetchOne({
    methodname: "core_calendar_get_action_events_by_courses",
    args: {
      courseids: args.courseids,
      timesortfrom: args.timesortfrom ?? null,
      timesortto: args.timesortto ?? null,
      limitnum: args.limitnum ?? 10,
      searchvalue: args.searchvalue ?? null
    }
  });
}, "getEventsByCourses");
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
  getEnrolledCourses,
  getEventsByCourse,
  getEventsByCourses,
  getTimelineEvents
};
//# sourceMappingURL=repository.dev.js.map
