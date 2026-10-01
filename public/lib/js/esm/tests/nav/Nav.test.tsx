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
 * Tests for the overflow ("More" menu) behaviour of core/nav/Nav, the shared navigation engine.
 *
 * @copyright  2026 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render, act, fireEvent} from '@testing-library/react';
import {type Ref} from 'react';
import Nav, {type NavNode} from '@moodle/lms/core/nav/Nav';

// @moodlehq/design-system is ESM only, so Jest cannot resolve it from a CommonJS test bundle.
// Stand NavPill in with markup carrying the classes these tests rely on: what is under test is
// which items end up as pills and which end up in the "More" dropdown, not the pill's rendering.
//
// Two details of the real component are reproduced deliberately, because Nav works around them:
// it forwards its ref to the anchor, and it drops any caller-supplied role.
jest.mock('@moodlehq/design-system', () => ({
    NavPill: ({label, href, ref}: {label: string; href: string; ref?: Ref<HTMLAnchorElement>}) => (
        <a ref={ref} className="mds-nav-pill" href={href} role={undefined}>
            <span className="mds-nav-pill__label">{label}</span>
        </a>
    ),
}), {virtual: true});

/** Height, in fake pixels, of a single row of navigation items. */
const ROW_HEIGHT = 40;

/**
 * How many <li> elements fit on one row of the nav bar. Stands in for the container width in
 * jsdom, which does no layout at all: see the offsetHeight stub below.
 */
let rowCapacity = 10;

/** The ResizeObserver callbacks registered by the component, so tests can fire them by hand. */
let resizeObserverCallbacks: ResizeObserverCallback[] = [];

/**
 * Build a simple set of top-level nodes.
 *
 * @param labels The text of each node.
 * @returns The nodes.
 */
const makeItems = (labels: string[]): NavNode[] => labels.map((text) => ({
    key: text.toLowerCase(),
    text,
    href: `/${text.toLowerCase()}`,
    active: false,
    forceintomoremenu: false,
    showchildreninsubmenu: false,
    children: [],
}));

const ITEMS = makeItems(['Home', 'Dashboard', 'My courses', 'Reports', 'Badges', 'Competencies']);

/**
 * Build a node whose children belong in a submenu, as a custom menu parent item or a secondary
 * navigation branch node (showchildreninsubmenu) does.
 *
 * @param text The parent node's text.
 * @param childlabels The text of each child.
 * @returns The node.
 */
const makeSubmenuItem = (text: string, childlabels: string[]): NavNode => ({
    ...makeItems([text])[0],
    // A custom menu parent only groups its children, so it has no url of its own.
    href: null,
    showchildreninsubmenu: true,
    children: makeItems(childlabels),
});

/**
 * The labels currently rendered as top-level pills, i.e. not collapsed into the "More" menu.
 *
 * @param container The render container.
 * @returns The visible pill labels.
 */
const visibleLabels = (container: HTMLElement): string[] => Array.from(
    container.querySelectorAll('ul.more-nav > li:not(.dropdownmoremenu) .mds-nav-pill__label'),
).map((node) => node.textContent ?? '');

/**
 * The labels currently inside the "More" dropdown.
 *
 * @param container The render container.
 * @returns The overflowed labels.
 */
const overflowLabels = (container: HTMLElement): string[] => Array.from(
    container.querySelectorAll(
        '[data-region="moredropdown"] > .dropdown-item, [data-region="moredropdown"] > .dropdown-submenu > .dropdown-item',
    ),
).map((node) => node.textContent ?? '');

/**
 * The labels inside the nested submenu of a given overflowed item.
 *
 * @param container The render container.
 * @param text The overflowed parent item's text.
 * @returns The submenu's item labels.
 */
const submenuLabels = (container: HTMLElement, text: string): string[] => {
    const submenu = Array.from(
        container.querySelectorAll('[data-region="moredropdown"] > .dropdown-submenu'),
    ).find((node) => node.querySelector('.dropdown-toggle')?.textContent === text);

    return Array.from(submenu?.querySelectorAll('.dropdown-menu > .dropdown-item') ?? [])
        .map((node) => node.textContent ?? '');
};

beforeEach(() => {
    rowCapacity = 10;
    resizeObserverCallbacks = [];

    mockAmdModule('core/menu_navigation', jest.fn() as unknown as object);

    // JSDom performs no layout, so every offsetHeight is 0 and the component could never detect a
    // wrap. Stub it: the <ul> reports two rows once it holds more <li> children than fit, and
    // everything else, including the mount point it is measured against, a single row.
    //
    // The landmark <nav> is the exception: it is a plain block wrapper which grows with the menu
    // inside it, as in a real browser, where only the mount point's height is fixed by CSS. That
    // is why the measurement must resolve its container past the landmark rather than from the
    // <ul>'s immediate parent, and modelling it here is what makes that observable in a test.
    Object.defineProperty(HTMLElement.prototype, 'offsetHeight', {
        configurable: true,
        get(this: HTMLElement) {
            const menu = this.matches('nav') ? this.querySelector(':scope > ul.more-nav') : this;
            if (!menu?.matches('ul.more-nav')) {
                return ROW_HEIGHT;
            }
            const shown = menu.querySelectorAll(':scope > li:not(.d-none)').length;
            return shown > rowCapacity ? ROW_HEIGHT * 2 : ROW_HEIGHT;
        },
    });

    // JSDom has no ResizeObserver. Record the callbacks so a container resize can be simulated.
    (window as unknown as {ResizeObserver: unknown}).ResizeObserver = class {
        constructor(callback: ResizeObserverCallback) {
            resizeObserverCallbacks.push(callback);
        }

        observe() {
            // Nothing to do: tests fire the recorded callbacks directly.
        }

        disconnect() {
            // Nothing to do.
        }

        unobserve() {
            // Nothing to do.
        }
    };
});

afterEach(() => {
    delete (HTMLElement.prototype as Partial<HTMLElement>).offsetHeight;
    delete (window as Partial<Window & typeof globalThis>).ResizeObserver;
});

/**
 * Render the given nodes at the given row capacity.
 *
 * @param items The top-level nodes.
 * @param capacity How many items fit on one row.
 * @param istablist Whether to render as an ARIA tablist.
 * @param navlabel Accessible name for the navigation landmark, or undefined for no landmark.
 * @returns The render result container.
 */
const renderItems = (
    items: NavNode[],
    capacity: number,
    istablist = false,
    navlabel?: string,
): HTMLElement => {
    rowCapacity = capacity;
    return render(
        <Nav items={items} morelabel="More" istablist={istablist} navlabel={navlabel} />,
    ).container;
};

/**
 * Render the navigation at the given row capacity.
 *
 * @param capacity How many items fit on one row.
 * @returns The render result container.
 */
const renderNav = (capacity: number): HTMLElement => renderItems(ITEMS, capacity);

/** Fire the recorded ResizeObserver callbacks, as the browser would when the container resizes. */
const fireContainerResize = () => act(() => {
    resizeObserverCallbacks.forEach((callback) => callback([], {} as ResizeObserver));
});

/** Fire a window resize event, as the browser does when the viewport changes. */
const fireWindowResize = () => act(() => {
    window.dispatchEvent(new Event('resize'));
});

describe('@moodle/lms/core/nav/Nav overflow', () => {
    it('shows every item when they all fit', () => {
        const container = renderNav(10);

        expect(visibleLabels(container)).toEqual(ITEMS.map((item) => item.text));
        expect(container.querySelector('.dropdownmoremenu')).toHaveClass('d-none');
    });

    it('collapses items into the More menu when the container shrinks', () => {
        const container = renderNav(10);

        rowCapacity = 4;
        fireContainerResize();

        // Three pills plus the "More" toggle fill the four available slots.
        expect(visibleLabels(container)).toEqual(['Home', 'Dashboard', 'My courses']);
        expect(overflowLabels(container)).toEqual(['Reports', 'Badges', 'Competencies']);
    });

    it('restores items from the More menu when the container grows', () => {
        const container = renderNav(4);
        expect(overflowLabels(container)).toEqual(['Reports', 'Badges', 'Competencies']);

        rowCapacity = 10;
        fireContainerResize();

        expect(visibleLabels(container)).toEqual(ITEMS.map((item) => item.text));
    });

    // The primary navigation's mount point is a shrink-to-fit flex item, so once items have
    // collapsed into "More" it is only as wide as what is left: widening the viewport resizes the
    // container by nothing and fires no observer callback.
    it('restores items from the More menu on a viewport resize that leaves the container untouched', () => {
        const container = renderNav(4);
        expect(overflowLabels(container)).toEqual(['Reports', 'Badges', 'Competencies']);

        rowCapacity = 10;
        fireWindowResize();

        expect(visibleLabels(container)).toEqual(ITEMS.map((item) => item.text));
    });
});

// The menu carries its own landmark semantics, rather than relying on whoever mounts it to wrap
// it in one, so that the React and NonJS paths cannot drift apart the way they did in MDL-87830.
// It is opt-in: the primary navigation already sits inside the navbar's landmark and must not
// nest a second one inside it.
describe('@moodle/lms/core/nav/Nav landmark', () => {
    it('renders a bare menu when no landmark name is given', () => {
        const container = renderItems(ITEMS, 10);

        expect(container.querySelector('nav')).toBeNull();
        expect(container.querySelector(':scope > ul.more-nav')).not.toBeNull();
    });

    it('wraps the menu in a navigation landmark named by navlabel', () => {
        const container = renderItems(ITEMS, 10, false, 'Course menu');

        const landmark = container.querySelector('nav');
        expect(landmark).not.toBeNull();
        expect(landmark).toHaveAttribute('aria-label', 'Course menu');
        expect(landmark?.querySelector(':scope > ul.more-nav')).not.toBeNull();
    });

    it('does not nest a second landmark inside the first', () => {
        const container = renderItems(ITEMS, 10, false, 'Course menu');

        expect(container.querySelectorAll('nav')).toHaveLength(1);
    });

    // The overflow measurement sizes the menu against the mount point, whose height is fixed. With
    // a landmark in place the <ul>'s parent is the landmark, which grows with the menu, so
    // measuring against it would report "fits" forever and nothing would ever collapse into More.
    it('still collapses items into the More menu when a landmark is rendered', () => {
        const container = renderItems(ITEMS, 10, false, 'Course menu');
        expect(visibleLabels(container)).toEqual(ITEMS.map((item) => item.text));

        rowCapacity = 4;
        fireContainerResize();

        expect(visibleLabels(container)).toEqual(['Home', 'Dashboard', 'My courses']);
        expect(overflowLabels(container)).toEqual(['Reports', 'Badges', 'Competencies']);
    });
});

// A custom menu parent item and a navigation branch node both arrive with
// showchildreninsubmenu set, and become a dropdown as a top-level pill. Their children have to
// stay reachable once that pill is collapsed into "More", as they did in legacy moremenu.js.
describe('@moodle/lms/core/nav/Nav submenus in the More menu', () => {
    const SUBMENU_ITEMS: NavNode[] = [
        ...makeItems(['Home', 'Dashboard']),
        makeSubmenuItem('Courses', ['All courses', 'Course search']),
        makeItems(['Mobile app'])[0],
    ];

    it('renders a top-level submenu node as a dropdown alongside the other pills', () => {
        const container = renderItems(SUBMENU_ITEMS, 10);

        expect(visibleLabels(container)).toEqual(['Home', 'Dashboard', 'Courses', 'Mobile app']);
        const submenu = container.querySelector('ul.more-nav > li.dropdown:not(.dropdownmoremenu)');
        expect(submenu?.querySelector('.dropdown-toggle')).toHaveTextContent('Courses');
        expect(Array.from(submenu?.querySelectorAll('.dropdown-menu > .dropdown-item') ?? [])
            .map((node) => node.textContent)).toEqual(['All courses', 'Course search']);
    });

    it('keeps a collapsed submenu node\'s children reachable inside the More menu', () => {
        const container = renderItems(SUBMENU_ITEMS, 2);

        expect(overflowLabels(container)).toEqual(['Dashboard', 'Courses', 'Mobile app']);
        expect(submenuLabels(container, 'Courses')).toEqual(['All courses', 'Course search']);
    });

    it('gives the collapsed submenu its own dropdown toggle, wired to its menu', () => {
        const container = renderItems(SUBMENU_ITEMS, 2);

        const submenu = container.querySelector('[data-region="moredropdown"] > .dropdown-submenu');
        const toggle = submenu?.querySelector('a.dropdown-item.dropdown-toggle');
        const menu = submenu?.querySelector('.dropdown-menu');

        expect(toggle).toHaveAttribute('data-bs-toggle', 'dropdown');
        expect(toggle).toHaveAttribute('aria-haspopup', 'true');
        expect(toggle).toHaveAttribute('aria-expanded', 'false');
        expect(menu).toHaveAttribute('role', 'menu');
        expect(menu).toHaveAttribute('aria-labelledby', toggle?.getAttribute('id'));
        expect(toggle).toHaveAttribute('aria-controls', menu?.getAttribute('id'));
    });

    // Bootstrap closes an open dropdown on any click that reaches the document, so without this
    // the "More" menu would shut the moment a nested submenu toggle inside it was clicked.
    it('stops a click inside the collapsed submenu from closing the More menu', () => {
        const container = renderItems(SUBMENU_ITEMS, 2);
        const documentClicks = jest.fn();
        document.addEventListener('click', documentClicks);

        const toggle = container.querySelector<HTMLElement>(
            '[data-region="moredropdown"] > .dropdown-submenu > .dropdown-item',
        );
        expect(toggle).not.toBeNull();
        act(() => {
            toggle!.click();
        });

        expect(documentClicks).not.toHaveBeenCalled();
        document.removeEventListener('click', documentClicks);
    });

    // Legacy moremenu_children.mustache rendered a node's title on the dropdown-toggle of a node
    // with children just as it did on a plain link, so both shapes of that toggle must carry it.
    it('keeps a submenu node\'s tooltip on its toggle, as a pill and once collapsed', () => {
        const items = [
            ...makeItems(['Home', 'Dashboard']),
            {...makeSubmenuItem('Courses', ['All courses']), title: 'Browse the course catalogue'},
        ];

        const pills = renderItems(items, 10);
        expect(pills.querySelector('ul.more-nav > li.dropdown .dropdown-toggle'))
            .toHaveAttribute('title', 'Browse the course catalogue');

        const collapsed = renderItems(items, 2);
        expect(collapsed.querySelector('[data-region="moredropdown"] > .dropdown-submenu > .dropdown-item'))
            .toHaveAttribute('title', 'Browse the course catalogue');
    });

    // Every item owned by the <ul role="menubar"> must be a menuitem: the <li> is role="none", so
    // a roleless pill anchor is a critical axe aria-required-children violation, and
    // core/menu_navigation keys its arrow-key handling off the role too.
    it('gives every top-level pill a role, including the "More" toggle', () => {
        const container = renderItems(makeItems(['Home', 'Dashboard', 'My courses']), 10);

        const roles = Array.from(container.querySelectorAll('ul.more-nav > li > a'))
            .map((node) => node.getAttribute('role'));
        expect(roles).toEqual(['menuitem', 'menuitem', 'menuitem', 'menuitem']);
    });

    it('keeps a tooltip on a tab pill', () => {
        const items = [{...makeItems(['Home'])[0], title: 'Site home'}];
        const container = renderItems(items, 10, true);

        expect(container.querySelector('ul.more-nav > li:not(.dropdownmoremenu) .mds-nav-pill'))
            .toHaveAttribute('title', 'Site home');
    });

    it('renders a divider between a submenu node\'s children in the More menu', () => {
        const items = [
            ...makeItems(['Home', 'Dashboard']),
            {
                ...makeSubmenuItem('Courses', ['All courses']),
                children: [...makeItems(['All courses']), {...makeItems(['FAQ'])[0], divider: true}],
            },
        ];
        const container = renderItems(items, 2);

        expect(submenuLabels(container, 'Courses')).toEqual(['All courses']);
        expect(container.querySelectorAll('.dropdown-submenu .dropdown-divider')).toHaveLength(1);
    });
});

// Nodes such as "Print book"/"Print chapter" carry a popup_action, exported as an action_link
// action rather than a real href behaviour. This used to be re-registered via YUI's Y.on
// (see MDL-89776); these tests confirm the plain-DOM replacement without ever mocking core/yui,
// so an unmocked requireAsync('core/yui') call would fail the test.
describe('@moodle/lms/core/nav/Nav action link behaviour', () => {
    afterEach(() => {
        delete (window as unknown as {openpopup?: unknown}).openpopup;
    });

    /**
     * Build a top-level node carrying a popup_action-style action_link action, as
     * booktool_print_extend_settings_navigation() exports for "Print book"/"Print chapter".
     *
     * @param text The node's label.
     * @param args The jsfunctionargs payload, JSON-encoded as component_action::export_for_template() does.
     * @returns The node.
     */
    const makeActionLinkItem = (text: string, args: Record<string, unknown>): NavNode => {
        const id = `${text.toLowerCase().replace(/\s+/g, '-')}-link`;
        return {
            ...makeItems([text])[0],
            id,
            // Print book/Print chapter are always force_into_more_menu()'d server-side, so their
            // action_link only ever renders as a DropdownItems <a>, which is the only renderPill
            // path that puts item.id onto the DOM node (NavPill has no id prop).
            forceintomoremenu: true,
            actions: [{id, event: 'click', jsfunction: 'openpopup', jsfunctionargs: JSON.stringify(args)}],
        };
    };

    it('binds a node\'s action_link action to its element without loading core/yui', () => {
        const openpopup = jest.fn();
        (window as unknown as {openpopup: unknown}).openpopup = openpopup;

        const items = [makeActionLinkItem('Print book', {url: '/mod/book/tool/print/index.php?id=1', name: 'popup'})];
        renderItems(items, 10);

        const link = document.getElementById('print-book-link');
        expect(link).not.toBeNull();

        act(() => {
            link!.dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true}));
        });

        expect(openpopup).toHaveBeenCalledTimes(1);
        expect(openpopup.mock.calls[0][1]).toEqual({url: '/mod/book/tool/print/index.php?id=1', name: 'popup'});
    });

    it('removes the action_link listener when the node is unmounted', () => {
        const openpopup = jest.fn();
        (window as unknown as {openpopup: unknown}).openpopup = openpopup;

        const items = [makeActionLinkItem('Print book', {url: '/mod/book/tool/print/index.php?id=1'})];
        const {unmount} = render(<Nav items={items} morelabel="More" istablist={false} />);

        const link = document.getElementById('print-book-link');
        expect(link).not.toBeNull();

        unmount();

        expect(() => act(() => {
            link!.dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true}));
        })).not.toThrow();
        expect(openpopup).not.toHaveBeenCalled();
    });

    it('does not rebind the action_link listener when items is a new array with the same content', () => {
        const openpopup = jest.fn();
        (window as unknown as {openpopup: unknown}).openpopup = openpopup;

        const items = [makeActionLinkItem('Print book', {url: '/mod/book/tool/print/index.php?id=1'})];
        const {rerender} = render(<Nav items={items} morelabel="More" istablist={false} />);

        const link = document.getElementById('print-book-link')!;
        const addSpy = jest.spyOn(link, 'addEventListener');
        const removeSpy = jest.spyOn(link, 'removeEventListener');

        // Callers such as the overflow measurement loop rebuild `items` as a fresh array on
        // every render, even when nothing has actually changed. A new array with equivalent
        // content must not tear down and rebind the listener.
        act(() => {
            rerender(<Nav items={[...items]} morelabel="More" istablist={false} />);
        });

        expect(addSpy).not.toHaveBeenCalled();
        expect(removeSpy).not.toHaveBeenCalled();

        act(() => {
            link.dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true}));
        });
        expect(openpopup).toHaveBeenCalledTimes(1);
    });
});

// Bootstrap's Tab component only fires "shown.bs.tab" (and manages aria-selected/tabindex) for an element whose role is exactly
// "tab". An overflowed tablist item keeps its data-bs-toggle="tab", so without a matching role="tab" it still switches panes on
// click but never fires "shown.bs.tab", and theme_boost/loader's tab listener (which updates the URL anchor) never runs.
describe('@moodle/lms/core/nav/Nav istablist overflow', () => {
    it('gives an overflowed tablist item role="tab" alongside data-bs-toggle="tab"', () => {
        const container = renderItems(ITEMS, 4, true);

        const overflowed = container.querySelector('[data-region="moredropdown"] > .dropdown-item');
        expect(overflowed).toHaveAttribute('role', 'tab');
        expect(overflowed).toHaveAttribute('data-bs-toggle', 'tab');
    });

    it('does not wrap overflowed tabs in a role="menu" container', () => {
        const container = renderItems(ITEMS, 4, true);

        // A menu may only own menuitems, so the tabs stay owned by the enclosing tablist.
        expect(container.querySelector('[data-region="moredropdown"]')).toHaveAttribute('role', 'none');
        expect(container.querySelector('[role="menu"]')).toBeNull();
    });

    describe('arrow-key movement', () => {
        const tabs = (container: HTMLElement) =>
            Array.from(container.querySelectorAll<HTMLElement>('ul > li > a[role="tab"]'));

        it('moves from the last visible tab to the More toggle, not into the closed menu', () => {
            const container = renderItems(ITEMS, 4, true);
            const stops = tabs(container);
            const documentKeydown = jest.fn();
            // Bootstrap listens in the capture phase on document, so spy on the same phase.
            document.addEventListener('keydown', documentKeydown, true);
            const clicked = jest.fn();
            container.querySelectorAll('[data-region="moredropdown"] > a').forEach((a) => a.addEventListener('click', clicked));

            const last = stops[stops.length - 2];
            last.focus();
            fireEvent.keyDown(last, {key: 'ArrowRight'});

            document.removeEventListener('keydown', documentKeydown, true);
            expect(document.activeElement).toBe(stops[stops.length - 1]);
            expect(stops[stops.length - 1]).toHaveClass('dropdown-toggle');
            expect(clicked).not.toHaveBeenCalled();
            // Bootstrap's delegated Tab handler must not see it.
            expect(documentKeydown).not.toHaveBeenCalled();
        });

        it('moves focus along the bar without activating tabs, until Space or Enter is used', () => {
            const container = renderItems(ITEMS, 4, true);
            const stops = tabs(container);
            const clicked = jest.fn();
            stops[stops.length - 2].addEventListener('click', clicked);

            const toggle = stops[stops.length - 1];
            toggle.focus();
            fireEvent.keyDown(toggle, {key: 'ArrowLeft'});

            expect(document.activeElement).toBe(stops[stops.length - 2]);
            expect(clicked).not.toHaveBeenCalled();

            fireEvent.keyDown(stops[stops.length - 2], {key: ' '});
            expect(clicked).toHaveBeenCalledTimes(1);
        });

        it('leaves Space on the More toggle to open its menu', () => {
            const container = renderItems(ITEMS, 4, true);
            const stops = tabs(container);
            const toggle = stops[stops.length - 1];
            const clicked = jest.fn();
            toggle.addEventListener('click', clicked);
            toggle.focus();

            fireEvent.keyDown(toggle, {key: ' '});

            expect(clicked).not.toHaveBeenCalled();
        });

        it('wraps from the More toggle to the first tab and supports Home and End', () => {
            const container = renderItems(ITEMS, 4, true);
            const stops = tabs(container);
            const toggle = stops[stops.length - 1];

            toggle.focus();
            fireEvent.keyDown(toggle, {key: 'ArrowRight'});
            expect(document.activeElement).toBe(stops[0]);

            fireEvent.keyDown(stops[0], {key: 'End'});
            expect(document.activeElement).toBe(toggle);

            fireEvent.keyDown(toggle, {key: 'Home'});
            expect(document.activeElement).toBe(stops[0]);
        });

        it('skips the More toggle when nothing has overflowed', () => {
            const container = renderItems(ITEMS, ITEMS.length, true);
            const stops = tabs(container).filter((stop) => !stop.closest('.d-none'));

            stops[stops.length - 1].focus();
            fireEvent.keyDown(stops[stops.length - 1], {key: 'ArrowRight'});

            expect(document.activeElement).toBe(stops[0]);
        });

        it('leaves Up and Down on the More toggle to Bootstrap so they can open the menu', () => {
            const container = renderItems(ITEMS, 4, true);
            const stops = tabs(container);
            const toggle = stops[stops.length - 1];
            toggle.focus();

            fireEvent.keyDown(toggle, {key: 'ArrowDown'});

            expect(document.activeElement).toBe(toggle);
        });

        it('leaves keys pressed inside the dropdown menu to Bootstrap', () => {
            const container = renderItems(ITEMS, 4, true);
            const item = container.querySelector<HTMLElement>('[data-region="moredropdown"] > a');
            const documentKeydown = jest.fn();
            // Bootstrap listens in the capture phase on document, so spy on the same phase.
            document.addEventListener('keydown', documentKeydown, true);

            fireEvent.keyDown(item!, {key: 'ArrowDown'});

            document.removeEventListener('keydown', documentKeydown, true);
            expect(documentKeydown).toHaveBeenCalled();
        });
    });

    it('keeps role="menu" on the More dropdown of a non-tablist nav', () => {
        const container = renderItems(ITEMS, 4, false);

        expect(container.querySelector('[data-region="moredropdown"]')).toHaveAttribute('role', 'menu');
    });

    it('keeps role="menuitem" and no data-bs-toggle for a non-tablist overflowed item', () => {
        const container = renderItems(ITEMS, 4, false);

        const overflowed = container.querySelector('[data-region="moredropdown"] > .dropdown-item');
        expect(overflowed).toHaveAttribute('role', 'menuitem');
        expect(overflowed).not.toHaveAttribute('data-bs-toggle');
    });

    it('marks the active overflowed tablist item aria-selected, not aria-current', () => {
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Badges'}));
        const container = renderItems(items, 4, true);

        const badges = Array.from(container.querySelectorAll('[data-region="moredropdown"] > .dropdown-item'))
            .find((node) => node.textContent === 'Badges');
        expect(badges).toHaveAttribute('aria-selected', 'true');
        expect(badges).not.toHaveAttribute('aria-current');
    });

    it('marks the active overflowed non-tablist item aria-current, not aria-selected', () => {
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Badges'}));
        const container = renderItems(items, 4, false);

        const badges = Array.from(container.querySelectorAll('[data-region="moredropdown"] > .dropdown-item'))
            .find((node) => node.textContent === 'Badges');
        expect(badges).toHaveAttribute('aria-current', 'page');
        expect(badges).not.toHaveAttribute('aria-selected');
    });
});

// The server computes `active` once, from the page's URL, which can't reflect a tab Bootstrap
// later activates client-side. Without this sync, the pane Bootstrap controls and the pill React highlights can disagree.
describe('@moodle/lms/core/nav/Nav shown.bs.tab sync', () => {
    it('moves the selected pill to whichever tab Bootstrap reports as shown', () => {
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Home'}));
        const container = renderItems(items, 10, true);

        const homePill = container.querySelector('a[href="/home"]');
        const dashboardPill = container.querySelector('a[href="/dashboard"]');
        expect(homePill).toHaveClass('active');
        expect(dashboardPill).not.toHaveClass('active');

        act(() => {
            dashboardPill!.dispatchEvent(new Event('shown.bs.tab', {bubbles: true}));
        });

        expect(homePill).not.toHaveClass('active');
        expect(dashboardPill).toHaveClass('active');
    });

    it('marks the More toggle selected when Bootstrap activates an overflowed tab', () => {
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Home'}));
        const container = renderItems(items, 4, true);

        const badgesItem = Array.from(container.querySelectorAll('[data-region="moredropdown"] .dropdown-item'))
            .find((node) => node.textContent === 'Badges') as HTMLElement;

        act(() => {
            badgesItem.dispatchEvent(new Event('shown.bs.tab', {bubbles: true}));
        });

        const moreToggle = container.querySelector('.dropdownmoremenu .mds-nav-pill');
        expect(moreToggle).toHaveClass('mds-nav-pill--selected');
        expect(badgesItem).toHaveAttribute('aria-selected', 'true');
    });

    it('ignores shown.bs.tab from outside this nav\'s own menu', () => {
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Home'}));
        const container = renderItems(items, 10, true);

        const outsider = document.createElement('a');
        outsider.setAttribute('href', '/dashboard');
        document.body.appendChild(outsider);

        act(() => {
            outsider.dispatchEvent(new Event('shown.bs.tab', {bubbles: true}));
        });

        expect(container.querySelector('a[href="/home"]')).toHaveClass('active');
        expect(container.querySelector('a[href="/dashboard"]')).not.toHaveClass('active');

        document.body.removeChild(outsider);
    });
});

// The browser never sends location.hash to the server, so the server-computed `active` flags
// always point at the default tab regardless of it. theme_boost/loader clicks the tab matching
// the hash on load, but that click's first shown.bs.tab event can fire before this component's
// own listener has mounted to catch it (e.g. while the tablist is still settling its overflow
// measurement). Reading the hash directly on mount does not depend on winning that race.
describe('@moodle/lms/core/nav/Nav initial hash', () => {
    afterEach(() => {
        window.location.hash = '';
    });

    // Real istablist hrefs are fragment identifiers (e.g. "#linkdevelopment", matching the pane
    // Bootstrap's Tab component switches to), the same form window.location.hash takes.
    const HASH_ITEMS = ITEMS.map((item) => ({...item, href: item.href.replace('/', '#')}));

    it('highlights the pill matching location.hash from the very first render', () => {
        window.location.hash = 'dashboard';
        const items = HASH_ITEMS.map((item) => ({...item, active: item.text === 'Home'}));
        const container = renderItems(items, 10, true);

        expect(container.querySelector('a[href="#home"]')).not.toHaveClass('active');
        expect(container.querySelector('a[href="#dashboard"]')).toHaveClass('active');
    });

    it('keeps the server-computed active pill when location.hash matches nothing in this nav', () => {
        window.location.hash = 'does-not-exist';
        const items = HASH_ITEMS.map((item) => ({...item, active: item.text === 'Home'}));
        const container = renderItems(items, 10, true);

        expect(container.querySelector('a[href="#home"]')).toHaveClass('active');
    });

    it('ignores location.hash for a non-tablist nav', () => {
        window.location.hash = '/dashboard';
        const items = ITEMS.map((item) => ({...item, active: item.text === 'Badges'}));
        const container = renderItems(items, 4, false);

        const badges = Array.from(container.querySelectorAll('[data-region="moredropdown"] > .dropdown-item'))
            .find((node) => node.textContent === 'Badges');
        expect(badges).toHaveClass('active');
    });
});
