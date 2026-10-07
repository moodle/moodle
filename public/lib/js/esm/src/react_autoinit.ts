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

import {type ComponentType} from 'react';
import {isProfilerEnabled} from '@moodle/lms/core/profiler';
import {mountReactApp, unmountReactApp} from '@moodle/lms/core/mount';
import Pending from '@moodle/lms/core/pending';

/** Props decoded from the data-react-props attribute. */
type ReactProps = Record<string, unknown>;

/** The shape a component module must expose to be mountable. */
type ComponentModule = {
    default?: ComponentType<ReactProps>;
};

const SELECTOR = '[data-react-component]';
const reactUnmountMap = new WeakMap<Element, () => void>();
const isProfilingEnabled = isProfilerEnabled();

/**
 * DOM ready promise.
 *
 * @returns Resolves when the DOM is ready.
 */
const domReady = async (): Promise<void> => {
    if (document.readyState !== 'loading') {
        return;
    }

    return new Promise(resolve => {
        document.addEventListener('DOMContentLoaded', () => {
            resolve();
        }, {once: true});
    });
};

/**
 * Safe JSON parsing from data-react-props.
 *
 * @param element The element with the data-react-props attribute.
 * @returns Parsed props object, or empty object on failure.
 */
const parseProps = (element: HTMLElement): ReactProps => {
    const raw = element.dataset.reactProps;
    if (raw === undefined || raw === '') {
        return {};
    }

    try {
        // JSON.parse is typed as any; anything that is not an object is discarded below.
        const parsed: unknown = JSON.parse(raw);
        if (typeof parsed !== 'object' || parsed === null) {
            return {};
        }

        return parsed as ReactProps;
    } catch (error) {
        console.error('[react_autoinit] invalid JSON', raw, error);
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
 * @returns The imported module, or undefined if resolution failed.
 */
const resolveComponent = async (componentName: string): Promise<ComponentModule | undefined> => {
    if (!componentName.startsWith('@moodle/lms/')) {
        console.error(
            '[react_autoinit] Invalid component format, expected @moodle/lms/<component>/<path>:',
            componentName,
        );
        return undefined;
    }

    try {
        if (isProfilingEnabled) {
            console.log(`[react_autoinit] Loading: ${componentName}`);
        }

        return await (import(componentName) as Promise<ComponentModule>);
    } catch (error) {
        console.error(`[react_autoinit] Failed to import: ${componentName}`, error);
        return undefined;
    }
};

/**
 * Mount a single React component with profiler support.
 *
 * @param element The element to mount the component into.
 * @param component The React component to render.
 * @param props Props to pass to the component.
 */
const mountReactComponent = (
    element: HTMLElement,
    component: ComponentType<ReactProps>,
    props: ReactProps,
): void => {
    const componentName = element.dataset.reactComponent ?? 'Unknown';
    const unmount = mountReactApp(element, component, props, {id: componentName});
    reactUnmountMap.set(element, unmount);
};

/**
 * Mount an element with the `data-react-component` attribute.
 *
 * @param element The element to mount.
 */
const mountOne = async (element: HTMLElement): Promise<void> => {
    if (element.dataset.reactMounted !== undefined || element.dataset.reactMounting !== undefined) {
        return;
    }

    element.dataset.reactMounting = '1';

    const componentName = element.dataset.reactComponent;
    if (componentName === undefined || componentName === '') {
        delete element.dataset.reactMounting;
        return;
    }

    const pendingPromise = new Pending(`reactAutoInit:${componentName}`);

    try {
        const module = await resolveComponent(componentName);

        if (!module) {
            console.warn('[react_autoinit] Component not found:', componentName);
            return;
        }

        const component = module.default;

        if (!component) {
            console.warn('[react_autoinit] Module has no default export:', componentName);
            return;
        }

        mountReactComponent(element, component, parseProps(element));
        element.dataset.reactMounted = '1';

        if (isProfilingEnabled) {
            console.log(`[react_autoinit] Mounted via default: ${componentName}`);
        }
    } catch (error) {
        console.error('[react_autoinit] Mount failed:', componentName, error);
    } finally {
        delete element.dataset.reactMounting;
        pendingPromise.resolve();
    }
};

/**
 * Unmount a single element.
 *
 * @param element The element to unmount.
 */
const unmountOne = (element: HTMLElement): void => {
    const unmount = reactUnmountMap.get(element) ?? (() => {
        unmountReactApp(element);
    });

    try {
        unmount();
        if (isProfilingEnabled) {
            console.log(`[react_autoinit] Unmounted: ${element.dataset.reactComponent}`);
        }
    } catch (error) {
        console.error('[react_autoinit] Error unmounting:', error);
    }

    reactUnmountMap.delete(element);

    delete element.dataset.reactMounted;
    delete element.dataset.reactMounting;
};

/**
 * Scan a root element and mount all matching React components within it.
 *
 * @param root The root to scan.
 */
const scanAndMount = (root: Element | Document): void => {
    const elements = root.querySelectorAll<HTMLElement>(SELECTOR);
    if (isProfilingEnabled && elements.length > 0) {
        console.log(`[react_autoinit] Found ${elements.length} component(s) to mount`);
    }

    for (const element of elements) {
        void mountOne(element);
    }
};

/**
 * Handle an added DOM node, mounting any React components within it.
 *
 * @param node The added node to handle.
 */
const handleAddedNode = (node: Node): void => {
    if (!(node instanceof HTMLElement)) {
        return;
    }

    if (node.matches(SELECTOR)) {
        if (isProfilingEnabled) {
            console.log('[react_autoinit] New component detected');
        }

        void mountOne(node);
    }

    for (const element of node.querySelectorAll<HTMLElement>(SELECTOR)) {
        void mountOne(element);
    }
};

/**
 * Handle a removed DOM node, unmounting any React components within it.
 *
 * @param node The removed node to handle.
 */
const handleRemovedNode = (node: Node): void => {
    if (!(node instanceof HTMLElement)) {
        return;
    }

    if (node.matches(SELECTOR)) {
        unmountOne(node);
    }

    for (const element of node.querySelectorAll<HTMLElement>(SELECTOR)) {
        unmountOne(element);
    }
};

/**
 * Install a MutationObserver to handle dynamically added and removed nodes.
 *
 * @returns The installed observer.
 */
const installObserver = (): MutationObserver => {
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            mutation.addedNodes.forEach(handleAddedNode);
            mutation.removedNodes.forEach(handleRemovedNode);
        }
    });

    observer.observe(document.documentElement, {childList: true, subtree: true});

    return observer;
};

let observer: MutationObserver | null = null;

/**
 * Scan the document for React components and install the MutationObserver.
 */
const init = async (): Promise<void> => {
    await domReady();
    if (isProfilingEnabled) {
        console.log('[react_autoinit] Initializing (profiling enabled)...');
    }

    if (observer === null) {
        observer = installObserver();
    }

    if (isProfilingEnabled) {
        console.log('[react_autoinit] MutationObserver active');
    }

    scanAndMount(document);
};

void init();
