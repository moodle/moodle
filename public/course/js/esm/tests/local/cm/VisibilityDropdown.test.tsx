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
 * Tests for core_course/local/cm/VisibilityDropdown.
 *
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {cloneElement, forwardRef, type ReactElement, type ReactNode, type Ref} from 'react';
import {act, fireEvent, render} from '@testing-library/react';
import VisibilityDropdown from '@moodle/lms/core_course/local/cm/VisibilityDropdown';

// Mock the ESM-only design system for the CommonJS test bundle.
jest.mock('@moodlehq/design-system', () => ({
    Dropdown: forwardRef(({trigger, open, onOpenChange, children}: {
        trigger: ReactElement<{onClick?: () => void}>;
        open: boolean;
        onOpenChange: (open: boolean) => void;
        children: ReactNode;
    }, ref: Ref<HTMLDivElement>) => (
        <div ref={ref}>
            {cloneElement(trigger, {onClick: () => onOpenChange(!open), 'aria-expanded': open} as object)}
            {open && <div role="menu">{children}</div>}
        </div>
    )),
    DropdownTrigger: forwardRef<HTMLButtonElement, {
        label: string;
        startIcon?: ReactElement;
        open?: boolean;
        iconOnly?: boolean;
        [key: string]: unknown;
    }>(({label, startIcon, open, iconOnly, ...rest}, ref) => (
        <button ref={ref} type="button" aria-expanded={!!open} {...rest}>
            {startIcon}
            {iconOnly ? null : <span>{label}</span>}
        </button>
    )),
    DropdownItemSelect: forwardRef<HTMLButtonElement, {
        label: string;
        description?: string;
        selected?: boolean;
        startIcon?: ReactElement;
        [key: string]: unknown;
    }>(({label, selected, startIcon, description, ...rest}, ref) => (
        <button ref={ref} type="button" role="menuitemradio" aria-checked={selected} {...rest}>
            {startIcon}{label}{description}
        </button>
    )),
}), {virtual: true});

const dispatch = jest.fn(() => Promise.resolve());

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

/** The trigger label and accessible name of the current value in PROPS. */
const CURRENT_LABEL = 'Hidden';
const CURRENT_NAME = 'Availability: Hidden from students';

const PROPS = {
    cmid: 42,
    value: 'hide',
    options: [
        {
            value: 'show', label: 'Show on course page', description: 'Available', icon: '', mutation: 'cmShow',
            state: 'show', triggerlabel: 'Available', accessiblename: 'Availability: Available on course page',
        },
        {
            value: 'hide', label: 'Hide on course page', description: 'Not available', icon: '', mutation: 'cmHide',
            state: 'hide', triggerlabel: 'Hidden', accessiblename: 'Availability: Hidden from students',
        },
        {
            value: 'stealth', label: 'Make available', description: 'With a link', icon: '', mutation: 'cmStealth',
            state: 'stealth', triggerlabel: 'Not shown', accessiblename: 'Availability: Available but not shown on course page',
        },
    ],
};

/**
 * Flush the promise chain started when an option is selected.
 */
const flushPromises = async() => {
    await act(async() => {
        await new Promise((resolve) => setTimeout(resolve, 0));
    });
};

describe('core_course/local/cm/VisibilityDropdown', () => {
    beforeEach(() => {
        dispatch.mockClear();
        mockMatchMedia(false);
        mockAmdModule('core_courseformat/courseeditor', {
            getCurrentCourseEditor: () => ({dispatch}),
        });
    });

    it('names the trigger with the feature and the value, and shows the label', () => {
        const {getByRole, getByText} = render(<VisibilityDropdown {...PROPS} />);

        const trigger = getByRole('button', {name: CURRENT_NAME});
        expect(trigger).toHaveAttribute('data-visibility', 'hide');
        expect(trigger).toContainElement(getByText(CURRENT_LABEL));
    });

    it('drops the trigger label below the sm breakpoint, keeping the accessible name', () => {
        mockMatchMedia(true);
        const {getByRole, queryByText} = render(<VisibilityDropdown {...PROPS} />);

        expect(queryByText(CURRENT_LABEL)).toBeNull();
        expect(getByRole('button', {name: CURRENT_NAME})).toBeInTheDocument();
    });

    it('restores the trigger label when the viewport grows past the sm breakpoint', () => {
        const setBelowSm = mockMatchMedia(true);
        const {getByText, queryByText} = render(<VisibilityDropdown {...PROPS} />);
        expect(queryByText(CURRENT_LABEL)).toBeNull();

        act(() => setBelowSm(false));

        expect(getByText(CURRENT_LABEL)).toBeInTheDocument();
    });

    it('reports which option is selected', () => {
        const {getByRole, getAllByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));

        const checked = getAllByRole('menuitemradio').map((item) => item.getAttribute('aria-checked'));
        expect(checked).toEqual(['false', 'true', 'false']);
    });

    it('dispatches the option mutation for the course module and closes the menu', async() => {
        const {getByRole, queryByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        fireEvent.click(getByRole('menuitemradio', {name: /Show on course page/}));
        await flushPromises();

        expect(dispatch).toHaveBeenCalledWith('cmShow', [42]);
        expect(queryByRole('menu')).toBeNull();
        expect(getByRole('button', {name: 'Availability: Available on course page'})).toHaveFocus();
    });

    it('shows the chosen value straight away, without waiting for the card to reload', () => {
        const {getByRole, getByText} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        fireEvent.click(getByRole('menuitemradio', {name: /Show on course page/}));

        const trigger = getByRole('button', {name: 'Availability: Available on course page'});
        expect(trigger).toHaveAttribute('data-visibility', 'show');
        expect(trigger).toContainElement(getByText('Available'));
    });

    it('restores the previous value when the change fails', async() => {
        dispatch.mockImplementationOnce(() => Promise.reject(new Error('Failed')));
        const {getByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        fireEvent.click(getByRole('menuitemradio', {name: /Show on course page/}));
        await flushPromises();

        expect(getByRole('button', {name: CURRENT_NAME})).toHaveAttribute('data-visibility', 'hide');
    });

    it('dispatches the current value again so the change is announced', async() => {
        const {getByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        fireEvent.click(getByRole('menuitemradio', {name: /Hide on course page/}));
        await flushPromises();

        expect(dispatch).toHaveBeenCalledWith('cmHide', [42]);
    });

    it('returns the focus to the reloaded dropdown after a value change', async() => {
        const {getByRole, unmount} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        fireEvent.click(getByRole('menuitemradio', {name: /Show on course page/}));
        await flushPromises();
        unmount();
        (document.activeElement as HTMLElement | null)?.blur();

        const reloaded = render(
            <VisibilityDropdown {...PROPS} value="show" />
        );

        expect(reloaded.getByRole('button', {name: 'Availability: Available on course page'})).toHaveFocus();
    });

    it('wraps Up and Down at the ends of the menu', () => {
        const {getByRole, getAllByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        const items = getAllByRole('menuitemradio');

        items[2].focus();
        fireEvent.keyDown(items[2], {key: 'ArrowDown'});
        expect(items[0]).toHaveFocus();

        fireEvent.keyDown(items[0], {key: 'ArrowUp'});
        expect(items[2]).toHaveFocus();
    });

    it('closes on Tab and returns the focus to the dropdown', () => {
        const {getByRole, getAllByRole, queryByRole} = render(<VisibilityDropdown {...PROPS} />);

        fireEvent.click(getByRole('button', {name: CURRENT_NAME}));
        const items = getAllByRole('menuitemradio');
        items[0].focus();
        fireEvent.keyDown(items[0], {key: 'Tab'});

        expect(queryByRole('menu')).toBeNull();
        expect(getByRole('button', {name: CURRENT_NAME})).toHaveFocus();
    });
});
