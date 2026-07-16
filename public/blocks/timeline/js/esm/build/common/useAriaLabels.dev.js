var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Shared aria-label fetching for the day-filter and view-selector dropdowns:
 * a button label plus a per-option label, each composed from two language strings.
 *
 * @module     block_timeline/common/useAriaLabels
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect } from "react";
import { getString } from "@moodle/lms/core/stringUtils";
function useAriaLabels(buttonKey, itemAriaKey, options) {
  const [buttonLabel, setButtonLabel] = useState("");
  const [itemLabels, setItemLabels] = useState({});
  useEffect(() => {
    getString(buttonKey, "block_timeline").then(setButtonLabel);
    options.forEach((opt) => {
      getString(opt.labelKey, opt.labelComponent ?? "block_timeline").then((label) => getString(itemAriaKey, "block_timeline", label)).then((ariaLabel) => setItemLabels((prev) => ({ ...prev, [opt.name]: ariaLabel })));
    });
  }, []);
  return { buttonLabel, itemLabels };
}
__name(useAriaLabels, "useAriaLabels");
export {
  useAriaLabels
};
//# sourceMappingURL=useAriaLabels.dev.js.map
