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
 * Activity group mode dropdown displayed on every activity card in edit mode.
 *
 * @module     core_course/local/cm/GroupModeDropdown
 * @copyright  2026 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
    useSyncExternalStore,
    type KeyboardEvent,
} from 'react';
import {Dropdown, DropdownItemSelect, DropdownTrigger} from '@moodlehq/design-system';
import {requireAsync} from '@moodle/lms/core/amd';
import Pending from '@moodle/lms/core/pending';

export type GroupModeOption = {
    /** The group mode constant (NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS). */
    value: number;
    label: string;
    /** The shorter label the trigger shows. The menu keeps the full label. */
    triggerlabel: string;
    description?: string;
    /** The trigger accessible name while this option is current. It always contains the label. */
    accessiblename: string;
    /** The rendered option icon HTML. */
    icon: string;
    /** The course editor mutation that applies the option. */
    mutation: string;
};

export type GroupModeDropdownProps = {
    cmid: number;
    /** The current group mode. */
    value: number;
    options: GroupModeOption[];
};

type CourseEditor = {
    dispatch: (actionName: string, ...params: unknown[]) => Promise<void>;
};

type CourseEditorModule = {
    getCurrentCourseEditor: () => CourseEditor;
};

/**
 * Preserve focus across component remounts when a group mode change reloads the activity card.
 */
const pendingFocus = new Set<number>();

/**
 * Track whether the viewport is below the sm breakpoint.
 *
 * @returns Whether the control should render as an icon only button.
 */
const useIconOnly = (): boolean => {
    const mediaQueryList = useMemo(() => globalThis.matchMedia('(max-width: 575.98px)'), []);

    const subscribe = useCallback((onStoreChange: () => void) => {
        mediaQueryList.addEventListener('change', onStoreChange);
        return () => {
            mediaQueryList.removeEventListener('change', onStoreChange);
        };
    }, [mediaQueryList]);

    return useSyncExternalStore(subscribe, () => mediaQueryList.matches);
};

/**
 * Render the activity group mode dropdown.
 *
 * @param props Component props.
 * @returns The rendered dropdown.
 */
export default function GroupModeDropdown({cmid, value, options}: GroupModeDropdownProps) {
    const [open, setOpen] = useState(false);
    const iconOnly = useIconOnly();
    const [current, setCurrent] = useState(value);
    const dropdownReference = useRef<HTMLDivElement>(null);
    const itemReferences = useRef<Array<HTMLButtonElement | null>>([]);

    const getTrigger = () => dropdownReference.current?.querySelector<HTMLButtonElement>('.groupmode-trigger');

    useEffect(() => {
        if (pendingFocus.delete(cmid)) {
            getTrigger()?.focus();
        }
    }, [cmid]);

    useEffect(() => {
        if (!open) {
            return undefined;
        }

        const selectedIndex = options.findIndex(option => option.value === current);
        if (selectedIndex <= 0) {
            return undefined;
        }

        let frame = requestAnimationFrame(() => {
            frame = requestAnimationFrame(() => itemReferences.current[selectedIndex]?.focus());
        });
        return () => {
            cancelAnimationFrame(frame);
        };
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

        const items = itemReferences.current.filter((item): item is HTMLButtonElement => item !== null);
        const index = items.indexOf(document.activeElement as HTMLButtonElement);
        let wrapTo: HTMLButtonElement | null = null;
        if (event.key === 'ArrowDown' && index === items.length - 1) {
            wrapTo = items[0];
        } else if (event.key === 'ArrowUp' && index === 0) {
            wrapTo = items[items.length - 1];
        }

        if (wrapTo) {
            event.preventDefault();
            event.stopPropagation();
            wrapTo.focus();
        }
    };

    const selected = options.find(option => option.value === current) ?? options[0];

    const selectOption = (option: GroupModeOption) => {
        closeMenu();
        if (option.value === current) {
            return;
        }

        const previous = current;
        setCurrent(option.value);
        pendingFocus.add(cmid);

        const pending = new Pending('core_course/local/cm/GroupModeDropdown:select');
        requireAsync<CourseEditorModule>('core_courseformat/courseeditor')
            .then(async ({getCurrentCourseEditor}) => getCurrentCourseEditor().dispatch(option.mutation, [cmid]))
            .catch(() => {
                pendingFocus.delete(cmid);
                setCurrent(previous);
            })
            .finally(() => {
                pending.resolve();
            });
    };

    return (
        <Dropdown
            ref={dropdownReference}
            open={open}
            onOpenChange={setOpen}
            placement='bottom-end'
            trigger={(
                <DropdownTrigger
                    className='groupmode-trigger'
                    label={selected.triggerlabel}
                    appearance='default'
                    size='md'
                    open={open}
                    iconOnly={iconOnly}
                    aria-label={selected.accessiblename}
                    startIcon={(
                        <i
                            className='groupmode-icon'
                            aria-hidden='true'
                            dangerouslySetInnerHTML={{__html: selected.icon}}
                        />
                    )}
                />
            )}
        >
            <div role='none' className='groupmode-menu' onKeyDown={handleKeyDown}>
                {options.map((option, index) => (
                    <DropdownItemSelect
                        key={option.value}
                        ref={(element: HTMLButtonElement | null) => {
                            itemReferences.current[index] = element;
                        }}
                        label={option.label}
                        description={option.description}
                        selected={option.value === current}
                        startIcon={(
                            <i
                                className='groupmode-icon'
                                aria-hidden='true'
                                dangerouslySetInnerHTML={{__html: option.icon}}
                            />
                        )}
                        onClick={() => {
                            selectOption(option);
                        }}
                    />
                ))}
            </div>
        </Dropdown>
    );
}
