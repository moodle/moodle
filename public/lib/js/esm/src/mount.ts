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
 * Shared React mount helper with optional profiling support.
 *
 * Use this for mounting React roots so profiling behavior is consistent
 * across autoinit and manually-initialised entrypoints.
 *
 * @module     core/mount
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    createElement,
    Profiler,
    type ComponentType,
    type ReactNode,
} from 'react';
import {createRoot} from 'react-dom/client';
import {getComponentId, isProfilerEnabled, onRenderCallback} from '@moodle/lms/core/profiler';

/**
 * Options for mounting a React component.
 */
type MountOptions = {
    /** Identifier used as the React Profiler `id` when profiling is enabled. */
    id?: string;
};

/** A function that unmounts a previously mounted React root. */
type UnmountFunction = () => void;

/** Tracks the unmount function for each mounted container so callers can tear down roots cleanly. */
const rootUnmountMap = new WeakMap<Element, UnmountFunction>();

/**
 * Mounts a React component to a container with optional profiling support.
 *
 * When the Moodle dev-mode profiler is active (`M.cfg.jsrev === -1`), the
 * component is automatically wrapped in a React `<Profiler>` so render timings
 * appear in the browser console.
 *
 * @param container The DOM element that will become the React root.
 * @param Component The React component to render.
 * @param props Props to pass to the component.
 * @param options Optional mount configuration.
 * @returns A function that, when called, unmounts the React root from the container.
 */
export function mountReactApp<P extends Record<string, unknown>>(
    container: Element,
    Component: ComponentType<P>,
    props: P,
    options: MountOptions = {},
): () => void {
    const componentId = getComponentId(Component, options.id, 'ReactApp');

    let node: ReactNode = createElement(Component, props);
    if (isProfilerEnabled()) {
        node = createElement(
            Profiler,
            {id: componentId, onRender: onRenderCallback},
            node,
        );
    }

    const root = createRoot(container);
    root.render(node);

    const unmount = () => {
        root.unmount();
    };

    rootUnmountMap.set(container, unmount);

    return unmount;
}

/**
 * Unmounts a previously mounted React app from a container.
 *
 * If the container was never mounted via {@link mountReactApp}, this is a no-op.
 *
 * @param container The DOM element whose React root should be unmounted.
 */
export function unmountReactApp(container: Element): void {
    const unmount = rootUnmountMap.get(container);
    if (unmount) {
        unmount();
        rootUnmountMap.delete(container);
    }
}
