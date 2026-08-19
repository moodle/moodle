var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * Event list item for the Timeline block.
 *
 * Matches the DOM structure of the legacy event-list-item.mustache template.
 *
 * @module     block_timeline/views/EventListItem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect } from "react";
import { getString } from "@moodle/lms/core/stringUtils";
import { Badge } from "@moodlehq/design-system";
import { ActivityIcon } from "@moodle/lms/block_timeline/views/ActivityIcon";
function EventListItem({ event, courseview = false }) {
  const pxClass = courseview ? "px-0" : "px-2";
  const [overdueLabel, setOverdueLabel] = useState("");
  const [ariaLabel, setAriaLabel] = useState("");
  useEffect(() => {
    getString("overdue", "block_timeline").then(setOverdueLabel);
  }, []);
  useEffect(() => {
    getString("ariaeventlistitem", "block_timeline", {
      name: event.activityname ?? "",
      course: event.course.fullnamedisplay,
      date: event.formatteddatetime
    }).then(setAriaLabel);
  }, [event.activityname, event.course.fullnamedisplay, event.formatteddatetime]);
  const time = new Date(event.timesort * 1e3).toLocaleTimeString(void 0, {
    hour: "2-digit",
    minute: "2-digit",
    hour12: false
  });
  const purposeClass = event.purpose ? ` ${event.purpose}` : "";
  const iconContainerClass = `small courseicon align-self-center mx-2 mb-1 mb-sm-0 text-nowrap activityiconcontainer${purposeClass}`;
  return /* @__PURE__ */ jsxDEV(
    "div",
    {
      className: `list-group-item timeline-event-list-item flex-column pt-2 pb-0 border-0 ${pxClass}`,
      "data-region": "event-list-item",
      children: [
        /* @__PURE__ */ jsxDEV("div", { className: "d-flex flex-wrap pb-1", children: [
          /* @__PURE__ */ jsxDEV("div", { className: "d-flex me-auto pb-1 mw-100 timeline-name", children: [
            /* @__PURE__ */ jsxDEV("small", { className: "text-end text-nowrap align-self-center ms-1", children: time }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
              lineNumber: 76,
              columnNumber: 21
            }, this),
            event.icon && /* @__PURE__ */ jsxDEV("div", { className: iconContainerClass, children: /* @__PURE__ */ jsxDEV(ActivityIcon, { modulename: event.modulename, iconurl: event.icon.iconurl, alt: event.icon.alttext }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
              lineNumber: 80,
              columnNumber: 29
            }, this) }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
              lineNumber: 79,
              columnNumber: 25
            }, this),
            /* @__PURE__ */ jsxDEV("div", { className: "event-name-container flex-grow-1 line-height-4 nowrap text-truncate", children: [
              /* @__PURE__ */ jsxDEV("div", { className: "d-flex", children: /* @__PURE__ */ jsxDEV("h5", { className: "h6 event-name mb-0 pb-1 text-truncate", children: [
                /* @__PURE__ */ jsxDEV("a", { href: event.url, title: event.name, "aria-label": ariaLabel || void 0, children: event.activityname }, void 0, false, {
                  fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                  lineNumber: 87,
                  columnNumber: 33
                }, this),
                event.overdue && /* @__PURE__ */ jsxDEV(Badge, { variant: "danger", pill: true, label: overdueLabel, className: "ms-1" }, void 0, false, {
                  fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                  lineNumber: 91,
                  columnNumber: 37
                }, this)
              ] }, void 0, true, {
                fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                lineNumber: 86,
                columnNumber: 29
              }, this) }, void 0, false, {
                fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                lineNumber: 85,
                columnNumber: 25
              }, this),
              /* @__PURE__ */ jsxDEV("small", { className: "mb-0", children: [
                event.activitystr,
                !courseview && event.course?.fullnamedisplay && /* @__PURE__ */ jsxDEV(Fragment, { children: [
                  " \xB7 ",
                  event.course.fullnamedisplay
                ] }, void 0, true, {
                  fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                  lineNumber: 98,
                  columnNumber: 33
                }, this)
              ] }, void 0, true, {
                fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                lineNumber: 95,
                columnNumber: 25
              }, this)
            ] }, void 0, true, {
              fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
              lineNumber: 84,
              columnNumber: 21
            }, this)
          ] }, void 0, true, {
            fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
            lineNumber: 75,
            columnNumber: 17
          }, this),
          event.action?.actionable && /* @__PURE__ */ jsxDEV("div", { className: "d-flex timeline-action-button", children: /* @__PURE__ */ jsxDEV("h5", { className: "h6 event-action", children: /* @__PURE__ */ jsxDEV(
            "a",
            {
              className: "list-group-item-action btn btn-outline-secondary btn-sm text-nowrap",
              href: event.action.url,
              "aria-label": event.action.name,
              title: event.action.name,
              children: [
                event.action.name,
                event.action.showitemcount && /* @__PURE__ */ jsxDEV(Badge, { variant: "secondary", label: String(event.action.itemcount) }, void 0, false, {
                  fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
                  lineNumber: 115,
                  columnNumber: 37
                }, this)
              ]
            },
            void 0,
            true,
            {
              fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
              lineNumber: 107,
              columnNumber: 29
            },
            this
          ) }, void 0, false, {
            fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
            lineNumber: 106,
            columnNumber: 25
          }, this) }, void 0, false, {
            fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
            lineNumber: 105,
            columnNumber: 21
          }, this)
        ] }, void 0, true, {
          fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
          lineNumber: 74,
          columnNumber: 13
        }, this),
        /* @__PURE__ */ jsxDEV("div", { className: "pt-2 border-bottom" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
          lineNumber: 122,
          columnNumber: 13
        }, this)
      ]
    },
    void 0,
    true,
    {
      fileName: "public/blocks/timeline/js/esm/src/views/EventListItem.tsx",
      lineNumber: 70,
      columnNumber: 9
    },
    this
  );
}
__name(EventListItem, "EventListItem");
export {
  EventListItem as default
};
//# sourceMappingURL=EventListItem.dev.js.map
