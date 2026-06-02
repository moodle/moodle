var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Shared React mount helper with optional profiling support.
 *
 * Use this for mounting React roots so profiling behavior is consistent
 * across autoinit and manually-initialised entrypoints.
 *
 * @module     core/mount
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { createElement, Profiler } from "react";
import { createRoot } from "react-dom/client";
import { isProfilerEnabled, onRenderCallback } from "@moodle/lms/core/profiler";
const rootUnmountMap = /* @__PURE__ */ new WeakMap();
function mountReactApp(container, Component, props, options = {}) {
  const componentId = options.id || Component.displayName || Component.name || "ReactApp";
  let node = createElement(Component, props);
  if (isProfilerEnabled()) {
    node = createElement(
      Profiler,
      { id: componentId, onRender: onRenderCallback },
      node
    );
  }
  const root = createRoot(container);
  root.render(node);
  const unmount = /* @__PURE__ */ __name(() => {
    root.unmount();
  }, "unmount");
  rootUnmountMap.set(container, unmount);
  return unmount;
}
__name(mountReactApp, "mountReactApp");
function unmountReactApp(container) {
  const unmount = rootUnmountMap.get(container);
  if (unmount) {
    unmount();
    rootUnmountMap.delete(container);
  }
}
__name(unmountReactApp, "unmountReactApp");
export {
  mountReactApp,
  unmountReactApp
};
//# sourceMappingURL=mount.dev.js.map
