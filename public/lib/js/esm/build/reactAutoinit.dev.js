var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Auto-init shim for Mustache React helper components.
 *
 * Scans the DOM for elements with the `data-react-component` attribute and
 * mounts the matching React component into each one. A MutationObserver watches
 * for dynamically injected content (AJAX, fragments) so components are mounted
 * and unmounted automatically without any additional initialiser call.
 *
 * The expected DOM contract is:
 * ```html
 *   <div
 *     data-react-component="@mod_book/viewer"
 *     data-react-props='{"title":"My Book"}'
 *   ></div>
 * ```
 *
 * @module     core/reactAutoinit
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { isProfilerEnabled } from "@moodle/lms/core/profiler";
import { mountReactApp, unmountReactApp } from "@moodle/lms/core/mount";
import Pending from "@moodle/lms/core/pending";
const SELECTOR = "[data-react-component]";
const reactUnmountMap = /* @__PURE__ */ new WeakMap();
const isProfilingEnabled = isProfilerEnabled();
const domReady = /* @__PURE__ */ __name(async () => {
  if (document.readyState !== "loading") {
    return;
  }
  return new Promise((resolve) => {
    document.addEventListener("DOMContentLoaded", () => {
      resolve();
    }, { once: true });
  });
}, "domReady");
const parseProps = /* @__PURE__ */ __name((element) => {
  const raw = element.dataset.reactProps;
  if (raw === void 0 || raw === "") {
    return {};
  }
  try {
    const parsed = JSON.parse(raw);
    if (typeof parsed !== "object" || parsed === null) {
      return {};
    }
    return parsed;
  } catch (error) {
    console.error("[reactAutoinit] invalid JSON", raw, error);
    return {};
  }
}, "parseProps");
const resolveComponent = /* @__PURE__ */ __name(async (componentName) => {
  if (!componentName.startsWith("@moodle/lms/")) {
    console.error(
      "[reactAutoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",
      componentName
    );
    return void 0;
  }
  try {
    if (isProfilingEnabled) {
      console.log(`[reactAutoinit] Loading: ${componentName}`);
    }
    return await import(componentName);
  } catch (error) {
    console.error(`[reactAutoinit] Failed to import: ${componentName}`, error);
    return void 0;
  }
}, "resolveComponent");
const mountReactComponent = /* @__PURE__ */ __name((element, component, props) => {
  const componentName = element.dataset.reactComponent ?? "Unknown";
  const unmount = mountReactApp(element, component, props, { id: componentName });
  reactUnmountMap.set(element, unmount);
}, "mountReactComponent");
const mountOne = /* @__PURE__ */ __name(async (element) => {
  if (element.dataset.reactMounted !== void 0 || element.dataset.reactMounting !== void 0) {
    return;
  }
  element.dataset.reactMounting = "1";
  const componentName = element.dataset.reactComponent;
  if (componentName === void 0 || componentName === "") {
    delete element.dataset.reactMounting;
    return;
  }
  const pendingPromise = new Pending(`reactAutoInit:${componentName}`);
  try {
    const module = await resolveComponent(componentName);
    if (!module) {
      console.warn("[reactAutoinit] Component not found:", componentName);
      return;
    }
    const component = module.default;
    if (!component) {
      console.warn("[reactAutoinit] Module has no default export:", componentName);
      return;
    }
    mountReactComponent(element, component, parseProps(element));
    element.dataset.reactMounted = "1";
    if (isProfilingEnabled) {
      console.log(`[reactAutoinit] Mounted via default: ${componentName}`);
    }
  } catch (error) {
    console.error("[reactAutoinit] Mount failed:", componentName, error);
  } finally {
    delete element.dataset.reactMounting;
    pendingPromise.resolve();
  }
}, "mountOne");
const unmountOne = /* @__PURE__ */ __name((element) => {
  const unmount = reactUnmountMap.get(element) ?? (() => {
    unmountReactApp(element);
  });
  try {
    unmount();
    if (isProfilingEnabled) {
      console.log(`[reactAutoinit] Unmounted: ${element.dataset.reactComponent}`);
    }
  } catch (error) {
    console.error("[reactAutoinit] Error unmounting:", error);
  }
  reactUnmountMap.delete(element);
  delete element.dataset.reactMounted;
  delete element.dataset.reactMounting;
}, "unmountOne");
const scanAndMount = /* @__PURE__ */ __name((root) => {
  const elements = root.querySelectorAll(SELECTOR);
  if (isProfilingEnabled && elements.length > 0) {
    console.log(`[reactAutoinit] Found ${elements.length} component(s) to mount`);
  }
  for (const element of elements) {
    void mountOne(element);
  }
}, "scanAndMount");
const handleAddedNode = /* @__PURE__ */ __name((node) => {
  if (!(node instanceof HTMLElement)) {
    return;
  }
  if (node.matches(SELECTOR)) {
    if (isProfilingEnabled) {
      console.log("[reactAutoinit] New component detected");
    }
    void mountOne(node);
  }
  for (const element of node.querySelectorAll(SELECTOR)) {
    void mountOne(element);
  }
}, "handleAddedNode");
const handleRemovedNode = /* @__PURE__ */ __name((node) => {
  if (!(node instanceof HTMLElement)) {
    return;
  }
  if (node.matches(SELECTOR)) {
    unmountOne(node);
  }
  for (const element of node.querySelectorAll(SELECTOR)) {
    unmountOne(element);
  }
}, "handleRemovedNode");
const installObserver = /* @__PURE__ */ __name(() => {
  const observer2 = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      mutation.addedNodes.forEach(handleAddedNode);
      mutation.removedNodes.forEach(handleRemovedNode);
    }
  });
  observer2.observe(document.documentElement, { childList: true, subtree: true });
  return observer2;
}, "installObserver");
let observer = null;
const init = /* @__PURE__ */ __name(async () => {
  await domReady();
  if (isProfilingEnabled) {
    console.log("[reactAutoinit] Initializing (profiling enabled)...");
  }
  if (observer === null) {
    observer = installObserver();
  }
  if (isProfilingEnabled) {
    console.log("[reactAutoinit] MutationObserver active");
  }
  scanAndMount(document);
}, "init");
void init();
//# sourceMappingURL=reactAutoinit.dev.js.map
