// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
 * @module     core/react_autoinit
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {isProfilerEnabled} from "@moodle/lms/core/profiler";
import {mountReactApp, unmountReactApp} from "@moodle/lms/core/mount";
import Pending from "@moodle/lms/core/pending";

const SELECTOR = "[data-react-component]";
const MOUNTED_FLAG = "reactMounted";
const MOUNTING_FLAG = "reactMounting";
const reactUnmountMap: WeakMap<Element, () => void> = new WeakMap();
const profilingEnabled = isProfilerEnabled();

/**
 * DOM ready promise.
 *
 * @returns Resolves when the DOM is ready.
 */
const domReady = () =>
    document.readyState === "loading"
        ? new Promise((resolve) =>
              document.addEventListener("DOMContentLoaded", resolve, {
                  once: true,
              })
          )
        : Promise.resolve();

/**
 * Safe JSON parsing from data-react-props.
 *
 * @param el The element with the data-react-props attribute.
 * @returns Parsed props object, or empty object on failure.
 */
const parseProps = (el: Element): Record<string, any> => {
    const raw = el.getAttribute("data-react-props");
    if (!raw) {
        return {};
    }
    try {
        return JSON.parse(raw);
    } catch (e) {
        window.console.error("[react_autoinit] invalid JSON", raw, e);
        return {};
    }
};

/**
 * Dynamically import a component module using ESM.
 *
 * Expects the specifier in `@moodle/lms/<component>/<path>` format, which is
 * resolved by the browser through the Moodle import map.
 * The module must have a default-exported React function component.
 *
 * @param componentName The component specifier in `@moodle/lms/<component>/<path>` format.
 * @returns The imported module, or null if resolution failed.
 */
const resolveComponent = async(componentName: string): Promise<any> => {
    if (!componentName) {
        return null;
    }

    if (!componentName.startsWith("@moodle/lms/")) {
        window.console.error(
            "[react_autoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",
            componentName
        );
        return null;
    }

    try {
        if (profilingEnabled) {
            window.console.log(
                `[react_autoinit] Loading: ${componentName}`
            );
        }
        const module = await import(componentName);
        return module;
    } catch (e) {
        window.console.error(`[react_autoinit] Failed to import: ${componentName}`, e);
        return null;
    }
};

/**
 * Mount a single React component with profiler support.
 */
const mountReactComponent = (
    el: Element,
    Component: any,
    props: Record<string, any>
) => {
    const componentName = el.getAttribute("data-react-component") || "Unknown";
    const unmount = mountReactApp(el, Component, props, {
        id: componentName,
    });
    reactUnmountMap.set(el, unmount);
};

/**
 * Mount an element with the `data-react-component` attribute.
 *
 * @param el The element to mount.
 */
const mountOne = async(el: Element) => {
    if ((el as HTMLElement).dataset[MOUNTED_FLAG]) {
        return;
    }

    if ((el as HTMLElement).dataset[MOUNTING_FLAG]) {
        return;
    }

    (el as HTMLElement).dataset[MOUNTING_FLAG] = "1";

    const componentName = el.getAttribute("data-react-component");
    if (!componentName) {
        delete (el as HTMLElement).dataset[MOUNTING_FLAG];
        return;
    }

    const pendingPromise = new Pending(`reactAutoInit:${componentName}`);

    try {
        const mod = await resolveComponent(componentName);

        if (!mod) {
            window.console.warn("[react_autoinit] Component not found:", componentName);
            return;
        }

        const Component = mod.default;

        if (!Component) {
            window.console.warn("[react_autoinit] Module has no default export:", componentName);
            return;
        }

        const props = parseProps(el);
        mountReactComponent(el, Component, props);
        (el as HTMLElement).dataset[MOUNTED_FLAG] = "1";

        if (profilingEnabled) {
            window.console.log(
                `[react_autoinit] Mounted via default: ${componentName}`
            );
        }
    } catch (e) {
        window.console.error("[react_autoinit] Mount failed:", componentName, e);
    } finally {
        delete (el as HTMLElement).dataset[MOUNTING_FLAG];
        pendingPromise.resolve();
    }
};

/**
 * Unmount a single element.
 *
 * @param el The element to unmount.
 */
const unmountOne = (el: Element) => {
    const unmount = reactUnmountMap.get(el) ?? (() => unmountReactApp(el));
    if (unmount) {
        try {
            unmount();
            if (profilingEnabled) {
                const componentName = el.getAttribute("data-react-component");
                window.console.log(`[react_autoinit] Unmounted: ${componentName}`);
            }
        } catch (e) {
            window.console.error("[react_autoinit] Error unmounting:", e);
        }
        reactUnmountMap.delete(el);
    }
    delete (el as HTMLElement).dataset[MOUNTED_FLAG];
    delete (el as HTMLElement).dataset[MOUNTING_FLAG];
};

/**
 * Scan a root element and mount all matching React components within it.
 *
 * @param root The root to scan.
 */
const scanAndMount = (root: Element | Document) => {
    const elements = root.querySelectorAll(SELECTOR);
    if (profilingEnabled && elements.length > 0) {
        window.console.log(
            `[react_autoinit] Found ${elements.length} component(s) to mount`
        );
    }

    for (const el of elements) {
        mountOne(el);
    }
};

/**
 * Handle an added DOM node, mounting any React components within it.
 *
 * @param node The added node to handle.
 */
const handleAddedNode = (node: Node) => {
    if (!(node instanceof Element)) {
        return;
    }

    if (node.matches?.(SELECTOR)) {
        if (profilingEnabled) {
            window.console.log("[react_autoinit] New component detected");
        }
        mountOne(node);
    }
    node.querySelectorAll?.(SELECTOR).forEach(mountOne);
};

/**
 * Handle a removed DOM node, unmounting any React components within it.
 *
 * @param node The removed node to handle.
 */
const handleRemovedNode = (node: Node) => {
    if (!(node instanceof Element)) {
        return;
    }

    if (node.matches?.(SELECTOR)) {
        unmountOne(node);
    }

    node.querySelectorAll?.(SELECTOR).forEach(unmountOne);
};

/**
 * Install a MutationObserver to handle dynamically added and removed nodes.
 *
 * @returns The installed observer.
 */
const installObserver = () => {
    const obs = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes?.forEach(handleAddedNode);
            mutation.removedNodes?.forEach(handleRemovedNode);
        });
    });

    obs.observe(document.documentElement, {
        childList: true,
        subtree: true,
    });

    return obs;
};

let observer: MutationObserver | null = null;

/**
 * Scan the document for React components and install the MutationObserver.
 */
const init = async() => {
    await domReady();
    if (profilingEnabled) {
        window.console.log("[react_autoinit] Initializing (profiling enabled)...");
    }
    if (!observer) {
        observer = installObserver();
        if (profilingEnabled) {
            window.console.log("[react_autoinit] MutationObserver active");
        }
    }
    scanAndMount(document);
};

init();
