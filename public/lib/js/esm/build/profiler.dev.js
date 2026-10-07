var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Shared React Profiler helpers.
 *
 * @module     core/profiler
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
  createElement,
  Profiler
} from "react";
const getComponentId = /* @__PURE__ */ __name((component, preferred, fallback) => {
  const candidates = [preferred, component.displayName, component.name];
  return candidates.find((candidate) => candidate !== void 0 && candidate !== "") ?? fallback;
}, "getComponentId");
const isProfilerEnabled = /* @__PURE__ */ __name(() => globalThis.M?.cfg?.jsrev === -1, "isProfilerEnabled");
const onRenderCallback = /* @__PURE__ */ __name((id, phase, actualDuration, baseDuration, startTime, commitTime) => {
  if (!isProfilerEnabled()) {
    return;
  }
  console.groupCollapsed(`[${phase}] ${id} - ${actualDuration.toFixed(2)}ms`);
  console.table({
    Component: id,
    Phase: phase,
    "Duration (ms)": actualDuration.toFixed(2),
    "Base Duration (ms)": baseDuration.toFixed(2),
    "Start Time": startTime.toFixed(2),
    "Commit Time": commitTime.toFixed(2)
  });
  if (actualDuration > 16) {
    console.warn(`Slow render: ${actualDuration.toFixed(2)}ms (target: <16ms for 60fps)`);
  }
  if (actualDuration > 50) {
    console.error(`Very slow render: ${actualDuration.toFixed(2)}ms - Consider optimization!`);
  }
  console.groupEnd();
}, "onRenderCallback");
const getProfilerCallback = /* @__PURE__ */ __name(() => isProfilerEnabled() ? onRenderCallback : void 0, "getProfilerCallback");
function withProfiler(Component, id) {
  if (!isProfilerEnabled()) {
    return Component;
  }
  const componentId = getComponentId(Component, id, "Component");
  const ProfiledComponent = /* @__PURE__ */ __name((props) => createElement(
    Profiler,
    { id: componentId, onRender: onRenderCallback },
    createElement(Component, props)
  ), "ProfiledComponent");
  ProfiledComponent.displayName = `withProfiler(${componentId})`;
  return ProfiledComponent;
}
__name(withProfiler, "withProfiler");
export {
  getComponentId,
  getProfilerCallback,
  isProfilerEnabled,
  onRenderCallback,
  withProfiler
};
//# sourceMappingURL=profiler.dev.js.map
