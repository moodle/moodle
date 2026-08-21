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

import {render, act} from '@testing-library/react';
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
    // everything else, including the container it is measured against, a single row.
    Object.defineProperty(HTMLElement.prototype, 'offsetHeight', {
        configurable: true,
        get(this: HTMLElement) {
            if (!this.matches('ul.more-nav')) {
                return ROW_HEIGHT;
            }
            const shown = this.querySelectorAll(':scope > li:not(.d-none)').length;
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
 * @returns The render result container.
 */
const renderItems = (items: NavNode[], capacity: number, istablist = false): HTMLElement => {
    rowCapacity = capacity;
    return render(
        <Nav items={items} morelabel="More" istablist={istablist} />,
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
