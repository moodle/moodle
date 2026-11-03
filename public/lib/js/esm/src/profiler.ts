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
 * Shared React Profiler helpers.
 *
 * @module     core/profiler
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    createElement,
    Profiler,
    type ComponentType,
    type ProfilerOnRenderCallback,
} from 'react';

declare global {
    // Moodle injects M on every page; it is absent under Jest, hence the optional chain below.
    var M: {cfg?: {jsrev?: number}} | undefined;
}

/**
 * Resolve the name a component is profiled under.
 *
 * @param component The component being named.
 * @param preferred An explicitly supplied name, if there is one.
 * @param fallback The name to use when the component is anonymous.
 * @returns The first name that has text, or the fallback.
 */
export const getComponentId = <P>(
    component: ComponentType<P>,
    preferred: string | undefined,
    fallback: string,
): string => {
    const candidates = [preferred, component.displayName, component.name];
    return candidates.find(candidate => candidate !== undefined && candidate !== '') ?? fallback;
};

/**
 * Returns whether the React Profiler should be active.
 *
 * Profiling is enabled when Moodle is running in developer mode
 * (`M.cfg.jsrev === -1`), which causes the profiling build of `react-dom`
 * to be loaded instead of the standard production bundle.
 *
 * @returns `true` when developer mode is active and profiling is enabled.
 */
export const isProfilerEnabled = (): boolean => globalThis.M?.cfg?.jsrev === -1;

/**
 * React Profiler `onRender` callback that logs render timings to the console.
 *
 * Outputs a collapsed console group with a timing table for every render. Emits
 * a `console.warn` for renders that exceed 16 ms (60 fps budget) and a
 * `console.error` for renders that exceed 50 ms. Silently exits when profiling
 * is disabled so it is safe to register unconditionally.
 *
 * @param id The `id` prop of the `<Profiler>` tree that just committed.
 * @param phase `"mount"` on first render, `"update"` on subsequent renders.
 * @param actualDuration Time spent rendering the profiled subtree (ms).
 * @param baseDuration Estimated time to render without memoisation (ms).
 * @param startTime When React began rendering this update (ms).
 * @param commitTime When React committed this update (ms).
 */
/* eslint-disable max-params -- React fixes the onRender signature at six arguments. */
export const onRenderCallback: ProfilerOnRenderCallback = (
    id,
    phase,
    actualDuration,
    baseDuration,
    startTime,
    commitTime,
) => {
    if (!isProfilerEnabled()) {
        return;
    }

    console.groupCollapsed(`[${phase}] ${id} - ${actualDuration.toFixed(2)}ms`);

    console.table({
        Component: id,
        Phase: phase,
        'Duration (ms)': actualDuration.toFixed(2),
        'Base Duration (ms)': baseDuration.toFixed(2),
        'Start Time': startTime.toFixed(2),
        'Commit Time': commitTime.toFixed(2),
    });

    if (actualDuration > 16) {
        console.warn(`Slow render: ${actualDuration.toFixed(2)}ms (target: <16ms for 60fps)`);
    }

    if (actualDuration > 50) {
        console.error(`Very slow render: ${actualDuration.toFixed(2)}ms - Consider optimization!`);
    }

    console.groupEnd();
};
/* eslint-enable max-params */

/**
 * Returns the profiler `onRender` callback when profiling is enabled.
 *
 * Convenience helper for code paths that pass the callback directly to a
 * `<Profiler>` prop — returns `undefined` in production so the prop can be
 * spread without needing a separate conditional.
 *
 * @returns when profiling is active, `undefined` otherwise.
 */
export const getProfilerCallback = (): ProfilerOnRenderCallback | undefined => isProfilerEnabled() ? onRenderCallback : undefined;

/**
 * Wraps a component with a React `<Profiler>` in developer mode.
 *
 * Returns the original component unchanged in production so there is no
 * runtime overhead. The wrapped component's `displayName` is set to
 * `withProfiler(<id>)` to make it identifiable in React DevTools.
 *
 * @example
 * ```tsx
 * import { withProfiler } from '@moodle/core/profiler';
 *
 * function MyComponent(props) {
 *   return <div>...</div>;
 * }
 *
 * export default withProfiler(MyComponent, 'MyComponent');
 * ```
 *
 * @param Component The React component to wrap.
 * @param id Optional profiler ID. Falls back to `Component.displayName`,
 * `Component.name`, or `"Component"` in that order.
 * @returns The profiler-wrapped component in dev mode, or the original component in production.
 */
export function withProfiler<P extends Record<string, unknown>>(
    Component: ComponentType<P>,
    id?: string,
): ComponentType<P> {
    if (!isProfilerEnabled()) {
        return Component;
    }

    const componentId = getComponentId(Component, id, 'Component');

    const ProfiledComponent = (props: P) =>
        createElement(
            Profiler,
            {id: componentId, onRender: onRenderCallback},
            createElement(Component, props),
        );

    ProfiledComponent.displayName = `withProfiler(${componentId})`;

    return ProfiledComponent;
}
