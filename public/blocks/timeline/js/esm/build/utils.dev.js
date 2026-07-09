var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Shared calendar utilities for the Timeline block.
 *
 * @module     block_timeline/utils
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { fetchMany } from "@moodle/lms/core/ajax";
import { getString } from "@moodle/lms/core/stringUtils";
import config from "@moodle/lms/core/config";
const SECONDS_IN_DAY = 86400;
function computeTimeRange(midnight, offsets) {
  return {
    starttime: midnight + offsets.daysoffset * SECONDS_IN_DAY,
    endtime: offsets.dayslimit !== null ? midnight + offsets.dayslimit * SECONDS_IN_DAY : null
  };
}
__name(computeTimeRange, "computeTimeRange");
function groupByDay(events) {
  const map = /* @__PURE__ */ new Map();
  for (const event of events) {
    const day = event.timeusermidnight;
    if (!map.has(day)) {
      map.set(day, []);
    }
    map.get(day).push(event);
  }
  return Array.from(map.entries()).sort(([a], [b]) => a - b).map(([dayTimestamp, evts]) => ({ dayTimestamp, events: evts }));
}
__name(groupByDay, "groupByDay");
function filterEvents(events, midnight, filteroverdue) {
  return events.filter((event) => {
    if (event.eventtype === "open" || event.eventtype === "opensubmission") {
      return event.timeusermidnight > midnight;
    }
    return !filteroverdue || event.overdue;
  });
}
__name(filterEvents, "filterEvents");
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
  SECONDS_IN_DAY,
  computeTimeRange,
  filterEvents,
  getFormattedDays,
  groupByDay
};
//# sourceMappingURL=utils.dev.js.map
