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
 * Tests for core_course/GroupModeDropdown, the activity group mode control.
 *
 * @copyright  2026 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {act, fireEvent, render, screen, waitFor} from '@testing-library/react';
import GroupModeDropdown from '@moodle/lms/core_course/local/cm/GroupModeDropdown';

// @moodlehq/design-system is ESM only, so Jest cannot resolve it from a CommonJS test bundle.
// The stand-ins below carry the parts of the real components' DOM contract these tests rely on:
// a button trigger that reports its expanded state, and menuitemradio options that report which
// one is selected. The tests are about this component's own wiring, not the design system's
// internal rendering.
jest.mock('@moodlehq/design-system', () => {
    const {forwardRef} = require('react');
    return {
        Dropdown: forwardRef(({open, onOpenChange, trigger, children}: {
            open?: boolean;
            onOpenChange?: (open: boolean) => void;
            trigger?: React.ReactElement<{onClick?: () => void}>;
            children?: React.ReactNode;
        }, ref: React.Ref<HTMLDivElement>) => (
            <div className="mds-dropdown" ref={ref}>
                {/* The real Dropdown owns the open state through floating-ui's useClick. */}
                {trigger ? require('react').cloneElement(trigger, {onClick: () => onOpenChange?.(!open)}) : null}
                {open ? <div role="menu">{children}</div> : null}
            </div>
        )),
        DropdownTrigger: ({label, startIcon, open, iconOnly, ...rest}: {
            label: string; startIcon?: React.ReactNode; open?: boolean; iconOnly?: boolean;
            [key: string]: unknown;
        }) => (
            <button type="button" aria-expanded={!!open} {...rest}>
                {startIcon}
                {iconOnly ? null : <span>{label}</span>}
            </button>
        ),
        DropdownItemSelect: forwardRef(({label, description, selected, startIcon, ...rest}: {
            label: string; description?: string; selected?: boolean; startIcon?: React.ReactNode;
            [key: string]: unknown;
        }, ref: React.Ref<HTMLButtonElement>) => (
            <button type="button" role="menuitemradio" aria-checked={!!selected} ref={ref} {...rest}>
                {startIcon}
                <span>{label}</span>
                {description ? <span>{description}</span> : null}
            </button>
        )),
    };
}, {virtual: true});

const dispatch = jest.fn();

/**
 * Stand in for window.matchMedia.
 *
 * @param matches Whether the viewport is below the sm breakpoint.
 * @returns A callback that flips the result and notifies listeners.
 */
const mockMatchMedia = (matches: boolean) => {
    const listeners = new Set<() => void>();
    const mediaQueryList = {
        matches,
        addEventListener: (_event: string, listener: () => void) => {
            listeners.add(listener);
        },
        removeEventListener: (_event: string, listener: () => void) => {
            listeners.delete(listener);
        },
    };
    window.matchMedia = jest.fn().mockReturnValue(mediaQueryList) as unknown as typeof window.matchMedia;
    return (next: boolean) => {
        mediaQueryList.matches = next;
        listeners.forEach((listener) => listener());
    };
};


const PROPS = {
    cmid: 42,
    value: 1,
    options: [
        {
            value: 0,
            label: 'No groups',
            triggerlabel: 'No groups',
            accessiblename: 'Group mode: No groups',
            icon: '<i class="icon fa fa-users-slash"></i>',
            mutation: 'cmNoGroups',
        },
        {
            value: 1,
            label: 'Separate groups',
            triggerlabel: 'Separate',
            accessiblename: 'Group mode: Separate groups',
            description: 'Students are divided into groups and can only see their group\'s work.',
            icon: '<i class="icon fa fa-users-between-lines"></i>',
            mutation: 'cmSeparateGroups',
        },
        {
            value: 2,
            label: 'Visible groups',
            triggerlabel: 'Visible',
            accessiblename: 'Group mode: Visible groups',
            description: 'Students are divided into groups, but can see the work of other groups.',
            icon: '<i class="icon fa fa-users"></i>',
            mutation: 'cmVisibleGroups',
        },
    ],
};

describe('core_course/GroupModeDropdown', () => {
    beforeEach(() => {
        dispatch.mockClear();
        // Every test but the icon only ones runs at a width above the sm breakpoint.
        mockMatchMedia(false);
        // .jest/globalSetup.ts mocks @moodle/lms/core/amd for every test, so the AMD modules the
        // component requires are registered against that shared registry.
        mockAmdModule('core_courseformat/courseeditor', {
            getCurrentCourseEditor: () => ({dispatch}),
        });
    });

    it('shows the current value as the trigger label', () => {
        render(<GroupModeDropdown {...PROPS} />);

        expect(screen.getByRole('button', {name: 'Group mode: Separate groups'})).toBeInTheDocument();
        expect(screen.getByText('Separate')).toBeInTheDocument();
    });

    it('renders the control for the no groups value too', () => {
        render(<GroupModeDropdown {...PROPS} value={0} />);

        expect(screen.getByRole('button', {name: 'Group mode: No groups'})).toBeInTheDocument();
    });

    it('drops the trigger label below the sm breakpoint, keeping the accessible name', () => {
        mockMatchMedia(true);
        render(<GroupModeDropdown {...PROPS} />);

        // The breakpoint is known on the first render, so the label is never shown at this width.
        expect(screen.queryByText('Separate')).not.toBeInTheDocument();
        expect(screen.getByRole('button', {name: 'Group mode: Separate groups'})).toBeInTheDocument();
    });

    it('restores the trigger label when the viewport grows past the sm breakpoint', () => {
        const setbelowsm = mockMatchMedia(true);
        render(<GroupModeDropdown {...PROPS} />);
        expect(screen.queryByText('Separate')).not.toBeInTheDocument();

        act(() => setbelowsm(false));

        expect(screen.getByText('Separate')).toBeInTheDocument();
    });

    it('states the feature and the value in the accessible name', () => {
        render(<GroupModeDropdown {...PROPS} value={2} />);

        const trigger = screen.getByRole('button', {name: 'Group mode: Visible groups'});
        // WCAG 2.5.3: the accessible name must contain the visible label.
        expect(trigger.getAttribute('aria-label')).toContain('Visible groups');
    });

    it('lists every option, marking the current one as selected', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));

        const options = screen.getAllByRole('menuitemradio');
        expect(options).toHaveLength(3);
        expect(options[0]).toHaveAttribute('aria-checked', 'false');
        expect(options[1]).toHaveAttribute('aria-checked', 'true');
        expect(options[2]).toHaveAttribute('aria-checked', 'false');
    });

    it('dispatches the matching mutation', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));
        fireEvent.click(screen.getByRole('menuitemradio', {name: /Visible groups/}));

        // The new value is announced by the course format watching the state, so that the Group
        // mode action menu announces it too. This component only dispatches.
        await waitFor(() => expect(dispatch).toHaveBeenCalledWith('cmVisibleGroups', [42]));
    });

    it('updates the label without waiting for the card to reload', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));
        fireEvent.click(screen.getByRole('menuitemradio', {name: /No groups/}));

        expect(screen.getByRole('button', {name: 'Group mode: No groups'})).toBeInTheDocument();
    });

    it('opens on the current value rather than the first option', async() => {
        render(<GroupModeDropdown {...PROPS} value={2} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Visible groups'}));

        const options = screen.getAllByRole('menuitemradio');
        await waitFor(() => expect(document.activeElement).toBe(options[2]));
    });

    it('wraps the arrow keys around the ends of the menu', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));
        const options = screen.getAllByRole('menuitemradio');

        options[0].focus();
        fireEvent.keyDown(options[0], {key: 'ArrowUp'});
        expect(document.activeElement).toBe(options[2]);

        options[2].focus();
        fireEvent.keyDown(options[2], {key: 'ArrowDown'});
        expect(document.activeElement).toBe(options[0]);
    });

    it('closes on Tab and returns focus to the control', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        const trigger = screen.getByRole('button', {name: 'Group mode: Separate groups'});
        fireEvent.click(trigger);
        const options = screen.getAllByRole('menuitemradio');

        options[0].focus();
        fireEvent.keyDown(options[0], {key: 'Tab'});

        await waitFor(() => expect(screen.queryByRole('menu')).not.toBeInTheDocument());
        expect(document.activeElement).toBe(trigger);
    });

    it('returns focus to the control when the card reloads after a change', async() => {
        const {unmount} = render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));
        fireEvent.click(screen.getByRole('menuitemradio', {name: /Visible groups/}));
        await waitFor(() => expect(dispatch).toHaveBeenCalled());

        // The card reload replaces the component with one rendering the new value.
        unmount();
        render(<GroupModeDropdown {...PROPS} value={2} />);

        await waitFor(() => expect(document.activeElement)
            .toBe(screen.getByRole('button', {name: 'Group mode: Visible groups'})));
    });

    it('does not dispatch when the current value is chosen again', async() => {
        render(<GroupModeDropdown {...PROPS} />);
        fireEvent.click(screen.getByRole('button', {name: 'Group mode: Separate groups'}));
        fireEvent.click(screen.getByRole('menuitemradio', {name: /Separate groups/}));

        await waitFor(() => expect(screen.queryByRole('menu')).not.toBeInTheDocument());
        expect(dispatch).not.toHaveBeenCalled();
    });
});
