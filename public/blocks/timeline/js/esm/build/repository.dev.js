var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Data-access layer for the Timeline block.
 *
 * All AJAX calls live here — views only ever talk to this module, never to
 * @moodle/lms/core/ajax directly. Every call wraps an existing core_calendar
 * or core_course web service; block_timeline defines none of its own.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { fetchOne, fetchMany } from "@moodle/lms/core/ajax";
import { getString } from "@moodle/lms/core/stringUtils";
import config from "@moodle/lms/core/config";
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
const setUserPreference = /* @__PURE__ */ __name((name, value) => {
  fetchOne({
    methodname: "core_user_update_user_preferences",
    args: { preferences: [{ type: name, value }] }
  }).catch(() => void 0);
}, "setUserPreference");
async function getFormattedDays(timestamps) {
  const unique = [...new Set(timestamps)];
  if (unique.length === 0) {
    return /* @__PURE__ */ new Map();
  }
  const format = await getString("strftimedaydate", "langconfig");
  const [result] = await fetchMany([{
    methodname: "core_get_user_dates",
    args: {
      contextid: config.contextid ?? 1,
      timestamps: unique.map((ts) => ({ timestamp: ts, format }))
    }
  }]);
  return new Map(unique.map((ts, i) => [ts, result.dates[i]]));
}
__name(getFormattedDays, "getFormattedDays");
export {
  getEnrolledCourses,
  getEventsByCourse,
  getEventsByCourses,
  getFormattedDays,
  getTimelineEvents,
  setUserPreference
};
//# sourceMappingURL=repository.dev.js.map
