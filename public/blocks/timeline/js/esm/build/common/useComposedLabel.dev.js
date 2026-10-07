var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Accessible name for a dropdown toggle that shows its current selection.
 *
 * The name has to contain the visible selection so that it can be spoken by voice control
 * (WCAG 2.5.3), but building it by placing the selection next to a second string leaves the
 * assembled phrase untranslatable: a translator sees each half on its own, with no context and no
 * way to change the order the two appear in. So the whole phrase is one string taking the
 * selection as {$a}, in the manner of core's monthprevwithname.
 *
 * @module     block_timeline/common/useComposedLabel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect } from "react";
import { getString } from "@moodle/lms/core/stringUtils";
function useComposedLabel(labelKey, activeLabelKey, activeLabelComponent = "block_timeline") {
  const [label, setLabel] = useState("");
  useEffect(() => {
    let current = true;
    void getString(activeLabelKey, activeLabelComponent).then(async (activeLabel) => getString(labelKey, "block_timeline", activeLabel)).then((composed) => {
      if (current) {
        setLabel(composed);
      }
      return composed;
    });
    return () => {
      current = false;
    };
  }, [labelKey, activeLabelKey, activeLabelComponent]);
  return label;
}
__name(useComposedLabel, "useComposedLabel");
export {
  useComposedLabel
};
//# sourceMappingURL=useComposedLabel.dev.js.map
