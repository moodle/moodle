var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * The site-wide Edit mode switch, rendered with the design system Switch component.
 *
 * @module     core/EditModeSwitch
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useEffect } from "react";
import { Switch } from "@moodlehq/design-system";
import { requireAsync } from "@moodle/lms/core/amd";
import Pending from "@moodle/lms/core/pending";
function EditModeSwitch({ id, context, pageurl, checked, label }) {
  useEffect(() => {
    let cancelled = false;
    const pending = new Pending("core/EditModeSwitch:init");
    requireAsync("core/edit_switch").then((editSwitch) => {
      if (!cancelled) {
        editSwitch.init(id);
      }
      return void 0;
    }).finally(() => {
      pending.resolve();
    });
    return () => {
      cancelled = true;
    };
  }, [id]);
  return /* @__PURE__ */ jsxDEV(
    Switch,
    {
      id,
      name: "setmode",
      variant: "enable",
      labelSide: "start",
      label,
      defaultChecked: checked,
      "data-context": context,
      "data-pageurl": pageurl
    },
    void 0,
    false,
    {
      fileName: "public/lib/js/esm/src/EditModeSwitch.tsx",
      lineNumber: 98,
      columnNumber: 9
    },
    this
  );
}
__name(EditModeSwitch, "EditModeSwitch");
export {
  EditModeSwitch as default
};
//# sourceMappingURL=EditModeSwitch.dev.js.map
