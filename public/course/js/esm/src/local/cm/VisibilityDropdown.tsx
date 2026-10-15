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
 * Activity visibility dropdown displayed on every activity card in edit mode.
 *
 * @module     core_course/local/cm/VisibilityDropdown
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useCallback, useEffect, useMemo, useRef, useState, useSyncExternalStore, type KeyboardEvent} from 'react';
import {Dropdown, DropdownItemSelect, DropdownTrigger} from '@moodlehq/design-system';
import {requireAsync} from '@moodle/lms/core/amd';
import Pending from '@moodle/lms/core/pending';

export interface VisibilityOption {
    /** The choice value (show, hide or stealth). */
    value: string;
    label: string;
    description: string;
    /** The rendered option icon HTML. */
    icon: string;
    /** The course editor mutation that applies the option. */
    mutation: string;
    /** The visibility state once the option is applied. */
    state: string;
    /** The trigger label once the option is applied. */
    triggerlabel: string;
    /** The trigger accessible name once the option is applied. It always contains the trigger label. */
    accessiblename: string;
}

export interface VisibilityDropdownProps {
    cmid: number;
    /** The current choice value (show, hide or stealth). */
    value: string;
    options: VisibilityOption[];
}

type CourseEditor = {
    dispatch: (actionName: string, ...params: unknown[]) => Promise<void>;
};

type CourseEditorModule = {
    getCurrentCourseEditor: () => CourseEditor;
};

/**
 * Preserve focus across component remounts when a visibility change reloads the activity card.
 */
const pendingFocus = new Set<number>();

/**
 * Track whether the viewport is below the sm breakpoint.
 *
 * @returns Whether the trigger should render as an icon only button.
 */
const useIconOnly = (): boolean => {
    const mediaQueryList = useMemo(() => window.matchMedia('(max-width: 575.98px)'), []);

    const subscribe = useCallback((onStoreChange: () => void) => {
        mediaQueryList.addEventListener('change', onStoreChange);
        return () => mediaQueryList.removeEventListener('change', onStoreChange);
    }, [mediaQueryList]);

    return useSyncExternalStore(subscribe, () => mediaQueryList.matches);
};

/**
 * Render the activity visibility dropdown.
 *
 * @param props Component props.
 * @returns The rendered dropdown.
 */
export default function VisibilityDropdown({cmid, value, options}: VisibilityDropdownProps) {
    const [open, setOpen] = useState(false);
    const iconOnly = useIconOnly();
    const [current, setCurrent] = useState(value);
    const dropdownRef = useRef<HTMLDivElement>(null);
    const itemRefs = useRef<(HTMLButtonElement | null)[]>([]);

    // A trigger ref would replace the ref Dropdown needs to position the menu.
    const getTrigger = () => dropdownRef.current?.querySelector<HTMLButtonElement>('.activity-visibility-trigger');

    useEffect(() => {
        if (pendingFocus.delete(cmid)) {
            getTrigger()?.focus();
        }
    }, [cmid]);

    // Focus the selected item after the Dropdown focuses its first item in an animation frame.
    useEffect(() => {
        if (!open) {
            return undefined;
        }
        const selectedIndex = options.findIndex((option) => option.value === current);
        if (selectedIndex <= 0) {
            return undefined;
        }
        let frame = requestAnimationFrame(() => {
            frame = requestAnimationFrame(() => itemRefs.current[selectedIndex]?.focus());
        });
        return () => cancelAnimationFrame(frame);
    }, [open, options, current]);

    const closeMenu = () => {
        setOpen(false);
        getTrigger()?.focus();
    };

    const handleKeyDown = (event: KeyboardEvent<HTMLDivElement>) => {
        if (event.key === 'Tab') {
            event.preventDefault();
            closeMenu();
            return;
        }
        const items = itemRefs.current.filter((item): item is HTMLButtonElement => item !== null);
        const current = items.indexOf(document.activeElement as HTMLButtonElement);
        let wrapTo: HTMLButtonElement | null = null;
        if (event.key === 'ArrowDown' && current === items.length - 1) {
            wrapTo = items[0];
        } else if (event.key === 'ArrowUp' && current === 0) {
            wrapTo = items[items.length - 1];
        }
        if (wrapTo) {
            // Stop the Dropdown list navigation, which does not loop.
            event.preventDefault();
            event.stopPropagation();
            wrapTo.focus();
        }
    };

    const selectOption = (option: VisibilityOption) => {
        closeMenu();
        if (!option.mutation) {
            return;
        }
        // Selecting the current value does not reload the card, but it is still dispatched so the
        // course editor announces the value again.
        const previous = current;
        if (option.value !== current) {
            pendingFocus.add(cmid);
            // Show the new value straight away rather than waiting for the card to reload.
            setCurrent(option.value);
        }
        const pending = new Pending(`core_course/VisibilityDropdown:${cmid}`);
        requireAsync<CourseEditorModule>('core_courseformat/courseeditor').then((courseEditor) => {
            return courseEditor.getCurrentCourseEditor().dispatch(option.mutation, [cmid]);
        }).catch(() => {
            pendingFocus.delete(cmid);
            setCurrent(previous);
        }).finally(() => {
            pending.resolve();
        });
    };

    const selected = options.find((option) => option.value === current) ?? options[0];

    // The accessible name states the feature and the value, so it differs from the visible label.
    const trigger = (
        <DropdownTrigger
            className="activity-visibility-trigger"
            label={selected.triggerlabel}
            appearance="default"
            size="md"
            open={open}
            iconOnly={iconOnly}
            aria-label={selected.accessiblename}
            data-visibility={selected.state}
            startIcon={(
                <i
                    className="activity-visibility-icon"
                    aria-hidden="true"
                    dangerouslySetInnerHTML={{__html: selected.icon}}
                />
            )}
        />
    );

    return (
        <Dropdown ref={dropdownRef} trigger={trigger} open={open} onOpenChange={setOpen} placement="bottom-end">
            <div role="none" className="activity-visibility-menu" onKeyDown={handleKeyDown}>
                {options.map((option, index) => (
                    <DropdownItemSelect
                        key={option.value}
                        ref={(element) => {
                            itemRefs.current[index] = element;
                        }}
                        label={option.label}
                        description={option.description}
                        selected={option.value === current}
                        startIcon={
                            <i
                                className="activity-visibility-option-icon"
                                aria-hidden="true"
                                dangerouslySetInnerHTML={{__html: option.icon}}
                            />
                        }
                        data-value={option.value}
                        onClick={() => selectOption(option)}
                    />
                ))}
            </div>
        </Dropdown>
    );
}
