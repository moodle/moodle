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
 * Shared navigation pill engine, used by the secondary navigation (and, via
 * core/nav/PrimaryNav, the primary navigation).
 *
 * @module     core/nav/Nav
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @copyright  2026 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Fragment, cloneElement, isValidElement, useEffect, useId, useLayoutEffect, useRef, useState,
    type ReactElement, type ReactNode} from 'react';
import {NavPill} from '@moodlehq/design-system';
import {requireAsync} from '@moodle/lms/core/amd';

export interface NavActionLinkAction {
    id: string;
    event: string;
    jsfunction: string;
    jsfunctionargs?: string | false;
}

export interface NavNode {
    key: string;
    text: string;
    href: string | null;
    active: boolean;
    forceintomoremenu: boolean;
    showchildreninsubmenu: boolean;
    children: NavNode[];
    id?: string | null;
    title?: string | null;
    attributes?: {name: string; value: unknown}[];
    actions?: NavActionLinkAction[];
    /**
     * A separator rather than a real item. Only ever set on children, and rendered as a
     * Bootstrap dropdown-divider, matching legacy moremenu_children.mustache.
     */
    divider?: boolean;
}

export interface NavProps {
    items: NavNode[];
    morelabel: string;
    istablist: boolean;
    /** Extra class for the <ul>, e.g. "navbar-nav" for the primary navigation. */
    navbarstyle?: string;
    /** Class added to the mount point once the overflow split has settled. */
    measuredclass?: string;
}

/**
 * Check whether a node, or any of its descendants, is the currently active node.
 *
 * Used to highlight a submenu's trigger pill when the active page is one of its children.
 *
 * @param node The node to check.
 * @returns True if the node or one of its descendants is active.
 */
const isNodeActive = (node: NavNode): boolean =>
    node.active || node.children.some(isNodeActive);

/**
 * Attribute names excluded from toAttributeRecord()'s output because they're already handled
 * explicitly by DropdownItems (id, via item.id).
 */
const RESERVED_ATTRIBUTE_NAMES = new Set(['id', 'class', 'disabled']);

/**
 * Convert a node's exported action_link attributes into a plain DOM attribute record.
 *
 * @param attributes The {name, value} pairs from more_menu.php's actionattributes() export.
 * @returns A record suitable for spreading onto a JSX element.
 */
const toAttributeRecord = (attributes: {name: string; value: unknown}[] = []): Record<string, string> =>
    Object.fromEntries(
        attributes
            .filter(({name}) => !RESERVED_ATTRIBUTE_NAMES.has(name))
            .map(({name, value}) => [name, String(value)]),
    );

/**
 * Resolve a dotted global function reference (e.g. "openpopup", "M.util.someFunction") to the
 * actual function, mirroring how core/actions.mustache inserts {{{jsfunction}}} as raw JS.
 *
 * @param path The dotted path to resolve from `window`.
 * @returns The resolved function, or undefined if any segment of the path is missing.
 */
const resolveGlobalFunction = (path: string): ((...args: unknown[]) => void) | undefined =>
    path.split('.').reduce<any>((obj, key) => obj?.[key], window as unknown as Record<string, unknown>);

/**
 * Minimal shape of the YUI `Y` instance this module needs: just `Y.on`, used to re-register
 * component_actions exactly as core/actions.mustache's {{#js}} block did server-side.
 */
type YuiLike = {on: (...args: unknown[]) => void};

/**
 * Re-registers each item's action_link actions (e.g. popup_action) via Y.on, exactly as
 * core/actions.mustache's {{#js}} block used to do server-side. Needed because the React export
 * only carries href/attributes, not arbitrary inline JS: nodes like "Print book"/"Print chapter"
 * rely on a popup_action to open a new window, and lose that behaviour without this.
 *
 * @param items The nodes to scan for actions.
 */
const useActionLinkBehavior = (items: NavNode[]): void => {
    useEffect(() => {
        const nodesWithActions = items.filter((item) => item.id && item.actions?.length);
        if (nodesWithActions.length === 0) {
            return undefined;
        }

        let cancelled = false;

        requireAsync<YuiLike>('core/yui').then((Y) => {
            if (cancelled) {
                return undefined;
            }
            nodesWithActions.forEach((item) => {
                const el = document.getElementById(item.id!);
                if (!el || el.dataset.actionLinkBound === '1') {
                    return;
                }
                let boundAny = false;
                item.actions!.forEach((action) => {
                    const fn = resolveGlobalFunction(action.jsfunction);
                    if (!fn) {
                        return;
                    }
                    const args = action.jsfunctionargs ? JSON.parse(action.jsfunctionargs) : undefined;
                    Y.on(action.event, fn, `#${item.id}`, null, args);
                    boundAny = true;
                });
                if (boundAny) {
                    el.dataset.actionLinkBound = '1';
                }
            });
            return undefined;
        });

        return () => {
            cancelled = true;
        };
    }, [items]);
};

/**
 * Keeps the enclosing "More" dropdown open when a nested submenu inside it is clicked, as legacy
 * moremenu.js did with a capturing listener on each nested .dropdown. Bootstrap's own toggle
 * handler is delegated from the document in the capture phase, so it has already opened the
 * submenu by the time this runs; only its bubble-phase clearMenus() is cut off.
 *
 * @param event The click event.
 */
const keepParentMenuOpen = (event: MouseEvent): void => event.stopPropagation();

/**
 * A submenu nested inside the "More" dropdown, for a node whose children belong in a submenu but
 * which has itself been collapsed into the overflow. Legacy moremenu.js moved the whole
 * <li class="dropdown"> into the "More" dropdown rather than flattening it, because such a node
 * has no url of its own: a plain link would be a dead entry with its children unreachable.
 *
 * @param props Component props.
 * @param props.node The collapsed node whose children belong in a submenu.
 * @param props.istablist Whether the navigation is rendered as an ARIA tablist.
 * @returns The rendered submenu toggle and dropdown.
 */
function DropdownSubmenu({node, istablist = false}: {node: NavNode; istablist?: boolean}) {
    const id = useId();
    const toggleId = `${id}-toggle`;
    const menuId = `${id}-menu`;

    return (
        // The wrapper only exists to give Bootstrap's dropdown JS a container, so role="none"
        // keeps the toggle a valid child of the enclosing role="menu", as the legacy <li> did.
        <div className="dropdown dropdown-submenu" role="none" onClickCapture={keepParentMenuOpen}>
            <a
                id={toggleId}
                className={`dropdown-item dropdown-toggle${isNodeActive(node) ? ' active' : ''}`}
                href="#"
                title={node.title ?? undefined}
                role="menuitem"
                data-bs-toggle="dropdown"
                // Bootstrap only skips Popper's absolute positioning by itself inside a .navbar,
                // so ask for it explicitly: the submenu expands in place (see moremenu.scss)
                // rather than floating over the "More" menu it belongs to.
                data-bs-display="static"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls={menuId}
                aria-current={isNodeActive(node) ? 'page' : undefined}
            >
                {node.text}
            </a>
            <div className="dropdown-menu" id={menuId} role="menu" aria-labelledby={toggleId}>
                <DropdownItems items={node.children} istablist={istablist} />
            </div>
        </div>
    );
}

/**
 * Plain dropdown-item links for the "More" overflow menu and submenu dropdowns.
 *
 * @moodlehq/design-system has no menu/dropdown component yet, so this reuses Bootstrap's
 * dropdown-item markup (matching legacy moremenu_children.mustache) rather than NavPill, which
 * is designed for the top-level tab bar only.
 *
 * @param props Component props.
 * @param props.items The nodes to render as dropdown items.
 * @param props.istablist Whether these items belong to an istablist nav's top-level overflow
 *                        dropdown (as opposed to a SubmenuTrigger's nested dropdown).
 * @param props.submenus Whether a node whose children belong in a submenu renders as a nested
 *                        submenu rather than a plain link. Only set for the "More" dropdown
 *                        itself: legacy moremenu_children.mustache went no deeper either.
 * @returns The rendered dropdown items.
 */
function DropdownItems(
    {items, istablist = false, submenus = false}:
    {items: NavNode[]; istablist?: boolean; submenus?: boolean},
) {
    useActionLinkBehavior(items);

    return (
        <>
            {items.map((item) => {
                if (item.divider) {
                    return <div key={item.key} className="dropdown-divider" role="separator" />;
                }

                if (submenus && item.showchildreninsubmenu && item.children.length > 0) {
                    return <DropdownSubmenu key={item.key} node={item} istablist={istablist} />;
                }

                return (
                    <a
                        key={item.key}
                        id={item.id ?? undefined}
                        className={`dropdown-item${item.active ? ' active' : ''}`}
                        href={item.href ?? '#'}
                        title={item.title ?? undefined}
                        aria-current={item.active ? 'page' : undefined}
                        role="menuitem"
                        data-bs-toggle={istablist ? 'tab' : undefined}
                        data-text={istablist ? item.text : undefined}
                        // React owns this item's active state via item.active above regardless of
                        // istablist: without this, core/menu_navigation's legacy click handler
                        // forces aria-current onto whichever dropdown item was clicked even when
                        // the click doesn't actually navigate anywhere (e.g. a stubbed-out href in
                        // tests, or any other no-op link).
                        data-disableactive="true"
                        {...toAttributeRecord(item.attributes)}
                        // Item.text is exported raw HTML (e.g. an icon plus a visually-hidden "opens in
                        // a new window" span), matching the legacy moremenu_children.mustache's unescaped {{{text}}}.
                        // A plain JSX child would have React auto-escape that markup instead of rendering it.
                        dangerouslySetInnerHTML={{__html: item.text}}
                    />
                );
            })}
        </>
    );
}

/**
 * A dropdown-toggle styled to match NavPill's own markup (indicator dot + label span), for
 * visual consistency with the pills either side of it. @moodlehq/design-system has no
 * menu/dropdown-trigger component, so this reuses NavPill's CSS classes directly on a plain
 * Bootstrap dropdown-toggle anchor.
 *
 * Clones the `id`/`role="menu"`/`aria-labelledby` attributes onto the passed-in dropdown-menu
 * element (matching the legacy moremenu_children.mustache's toggle/menu ARIA wiring), rather
 * than requiring every caller to generate and pass down a matching id itself.
 *
 * Renders the toggle and menu as siblings (no wrapping element of its own): legacy
 * moremenu_children.mustache puts Bootstrap's "dropdown" class directly on the enclosing `<li>`
 * rather than on an extra wrapper `<div>`, so the toggle `<a>` stays a *direct* child of its
 * `<li>`. Several Behat helpers (e.g. behat_navigation::select_on_administration_page()) rely on
 * that `<li>/<a>` structure; callers must add the `dropdown` class to their own `<li>` themselves.
 *
 * @param props Component props.
 * @param props.label The visible label for the toggle.
 * @param props.selected Whether one of the dropdown's own items is currently active.
 * @param props.title The toggle's tooltip, when the node carries one that differs from its label.
 * @param props.istablist Whether the toggle sits inside an `istablist` nav. When true, the toggle
 *                        gets `role="tab"` to be a valid tablist child; when false it gets
 *                        `role="menuitem"` to be a valid child of the top-level `<ul
 *                        role="menubar">`. (NavPill's plain leaf items get the same treatment from
 *                        stampMenuItemRole below, since @moodlehq/design-system's NavPillProps
 *                        type omits `role` and the component always sets its own.)
 * @param props.children The dropdown menu to render alongside the toggle.
 * @returns The rendered dropdown toggle and menu.
 */
function PillDropdownToggle(
    {label, selected, title, istablist = false, children}:
    {label: string; selected: boolean; title?: string; istablist?: boolean; children: ReactNode},
) {
    const classes = ['mds-nav-pill', 'dropdown-toggle', selected ? 'mds-nav-pill--selected' : null]
        .filter(Boolean)
        .join(' ');

    const id = useId();
    const toggleId = `${id}-toggle`;
    const menuId = `${id}-menu`;

    const menu = isValidElement(children)
        ? cloneElement(children as ReactElement<{id?: string; role?: string; 'aria-labelledby'?: string}>, {
            id: menuId,
            role: 'menu',
            'aria-labelledby': toggleId,
        })
        : children;

    return (
        <Fragment>
            <a
                href="#"
                id={toggleId}
                className={classes}
                title={title}
                role={istablist ? 'tab' : 'menuitem'}
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls={menuId}
                aria-current={selected ? 'page' : undefined}
                // Roving tabindex, matching legacy moremenu_children.mustache/moremenu.js: the
                // toggle is only a native Tab stop while it owns the active item (a submenu child,
                // or an overflowed top-level item for the "More" toggle).
                tabIndex={selected ? 0 : -1}
            >
                {selected && <span className="mds-nav-pill__indicator" aria-hidden="true" />}
                {/* Label may carry raw HTML (see DropdownItems' dangerouslySetInnerHTML above). */}
                <span className="mds-nav-pill__label" dangerouslySetInnerHTML={{__html: label}} />
            </a>
            {menu}
        </Fragment>
    );
}

/**
 * A tab pill rendered for an `istablist` nav (e.g. admin/search.php's site-admin category tabs).
 *
 * @param props Component props.
 * @param props.node The node to render as a tab.
 * @returns The rendered tab.
 */
function TabPill({node}: {node: NavNode}) {
    const selected = isNodeActive(node);

    return (
        <a
            href={node.href ?? '#'}
            className={`mds-nav-pill${selected ? ' active' : ''}`}
            title={node.title ?? undefined}
            role="tab"
            data-bs-toggle="tab"
            data-text={node.text}
            data-disableactive="true"
            aria-selected={selected ? 'true' : 'false'}
            tabIndex={selected ? 0 : -1}
        >
            {/* Node.text may carry raw HTML (see DropdownItems' dangerouslySetInnerHTML above). */}
            <span className="mds-nav-pill__label" dangerouslySetInnerHTML={{__html: node.text}} />
        </a>
    );
}

/**
 * A dropdown trigger for a node whose children should render in a submenu.
 *
 * @param props Component props.
 * @param props.node The node whose children should render in a submenu.
 * @param props.istablist Whether the nav is rendered as an ARIA tablist.
 * @returns The rendered submenu trigger and dropdown.
 */
function SubmenuTrigger({node, istablist = false}: {node: NavNode; istablist?: boolean}) {
    return (
        <PillDropdownToggle
            label={node.text}
            selected={isNodeActive(node)}
            title={node.title ?? undefined}
            istablist={istablist}
        >
            <div className="dropdown-menu">
                <DropdownItems items={node.children} istablist={istablist} />
            </div>
        </PillDropdownToggle>
    );
}

/**
 * Ref callback stamping role="menuitem" onto a NavPill's anchor.
 *
 * A NavPill cannot be given a role: @moodlehq/design-system's NavPillProps omits `role`, and the
 * component overwrites whatever is spread in with its own value, which is undefined unless the
 * pill is disabled. Its <li> is role="none", so the roleless anchor is exposed as an owned child
 * of the enclosing <ul role="menubar"> — a critical axe aria-required-children violation
 * ("Element has children which are not allowed"), and leaves the pills announced as plain links
 * rather than as the menu items core/menu_navigation drives with the arrow keys.
 *
 * So set it on the DOM node instead, until NavPill accepts a role of its own. React never undoes
 * this: `role` is undefined in both the previous and the next props, so it is never diffed.
 *
 * @param el The pill's anchor, or null once it is unmounted.
 */
const stampMenuItemRole = (el: HTMLAnchorElement | null): void => {
    el?.setAttribute('role', 'menuitem');
};

/**
 * Render the appropriate pill for a visible top-level node.
 *
 * @param item The node to render.
 * @param istablist Whether the nav is rendered as an ARIA tablist.
 * @returns The rendered pill.
 */
const renderPill = (item: NavNode, istablist: boolean) => {
    if (item.showchildreninsubmenu && item.children.length > 0) {
        return <SubmenuTrigger node={item} istablist={istablist} />;
    }
    if (istablist) {
        return <TabPill node={item} />;
    }
    const selected = isNodeActive(item);
    return (
        <NavPill
            ref={stampMenuItemRole}
            label={item.text}
            href={item.href ?? '#'}
            title={item.title ?? undefined}
            selected={selected}
            // Roving tabindex, matching legacy moremenu_children.mustache's
            // {{^isactive}}tabindex="-1"{{/isactive}}: exactly one top-level item is ever a native
            // Tab stop, so keyboard users Tab past the whole bar in one step and rely on
            // core/menu_navigation's arrow-key handling to move within it.
            tabIndex={selected ? 0 : -1}
            // React owns this pill's active state via the `selected` prop above; without this,
            // core/menu_navigation's legacy click handler forces aria-current onto the clicked
            // pill regardless of whether the click actually navigates anywhere (e.g. a stubbed-out
            // href in tests, or any other no-op link), leaving the old and new active items both
            // (or neither) marked correctly. TabPill and DropdownItems' istablist items already opt
            // out the same way.
            data-disableactive="true"
        />
    );
};

/**
 * Default for the `measuredclass` prop: the CSS class toggled on the React mount-point container
 * once the overflow split has settled.
 */
const MEASURED_CLASS = 'secondarynav-measured';

/**
 * Root component for the navigation pill.
 *
 * @param props Component props.
 * @param props.items The top-level navigation nodes.
 * @param props.morelabel The localised label for the "More" overflow dropdown.
 * @param props.istablist Whether the nav is rendered as an ARIA tablist.
 * @param props.navbarstyle Extra class for the <ul>, e.g. "navbar-nav" for the primary navigation.
 * @param props.measuredclass Class added to the mount point once the overflow split has settled.
 * @returns The rendered navigation pill.
 */
export default function Nav(
    {items, morelabel, istablist, navbarstyle, measuredclass = MEASURED_CLASS}: NavProps,
) {
    // Dividers are a dropdown-only concept (see DropdownItems); the server side export already
    // drops them at the top level, but guard here too so one could never render as a bare pill.
    const toplevel = items.filter((item) => !item.divider);
    const forced = toplevel.filter((item) => item.forceintomoremenu);
    const rest = toplevel.filter((item) => !item.forceintomoremenu);

    const menuRef = useRef<HTMLUListElement>(null);
    const [autoOverflowCount, setAutoOverflowCount] = useState(0);
    const [measured, setMeasured] = useState(false);

    // Forces a re-render (and a fresh measurement pass) when the ResizeObserver fires but
    // autoOverflowCount doesn't itself need to change, e.g. the container grew and the current
    // split still fits.
    const [, forceRemeasure] = useState(0);

    // Bounded convergence loop bookkeeping for the measurement effect below. Refs, not state,
    // because they must never themselves trigger a re-render. Reset at the start of each fresh
    // "settle cycle": when `items` changes (see itemsKey below) or the container is resized.
    // - stepsRef: steps taken this cycle, capped at `2 * rest.length + 2` (see effect body) so the
    //   loop can never run forever.
    // - lastActionRef: what the last step did ('grow' | 'shrink' | null), to tell a genuine "still
    //   doesn't fit, grow more" apart from "that shrink attempt didn't pay off, revert it".
    // - shrinkExhaustedRef: once a shrink attempt is reverted this cycle, don't try another until
    //   the next cycle, otherwise it would just bounce between the two states forever.
    const stepsRef = useRef(0);
    const lastActionRef = useRef<'grow' | 'shrink' | null>(null);
    const shrinkExhaustedRef = useRef(false);

    // Lets the measurement effect (which has no dependency array) detect an `items` prop change
    // from inside itself.
    const itemsKey = items.map((item) => item.key).join(' ');
    const prevItemsKeyRef = useRef(itemsKey);

    // Keyboard arrow-key/Home/End navigation, via the existing core/menu_navigation module.
    // Bootstrap's data-bs-toggle="tab"/"dropdown" APIs handle panel-switching/opening separately.
    // Legacy moremenu.js called this unconditionally for every nav, not just istablist ones: it's
    // also what makes Space (not just Enter) activate a pill/menuitem.
    useEffect(() => {
        if (!menuRef.current) {
            return undefined;
        }

        let cancelled = false;

        requireAsync<(menu: HTMLElement) => void>('core/menu_navigation').then((menuNavigation) => {
            if (!cancelled && menuRef.current) {
                menuNavigation(menuRef.current);
            }
            return undefined;
        });

        return () => {
            cancelled = true;
        };
    }, []);

    // Measures the real DOM after every commit (deliberately no dependency array) and nudges
    // autoOverflowCount by one step per pass until the split converges. Runs pre-paint, so
    // intermediate states are never visible; measuredclass/opacity cover the first convergence.
    useLayoutEffect(() => {
        if (prevItemsKeyRef.current !== itemsKey) {
            prevItemsKeyRef.current = itemsKey;
            stepsRef.current = 0;
            lastActionRef.current = null;
            shrinkExhaustedRef.current = false;
            if (autoOverflowCount !== 0) {
                setAutoOverflowCount(0);
                return;
            }
        }

        const menu = menuRef.current;
        const container = menu?.parentElement;
        if (!menu || !container) {
            // Fail open: show the tab bar unmeasured rather than hide it forever.
            setMeasured(true);
            return;
        }

        const reveal = () => {
            if (!measured) {
                container.classList.add(measuredclass);
            }
        };

        const wrapped = menu.offsetHeight > container.offsetHeight;

        // 2 * rest.length + 2 covers the worst case of growing all the way from 0, or shrinking
        // all the way back down plus one revert step, so this is guaranteed to stop.
        const bound = (2 * rest.length) + 2;

        if (wrapped) {
            if (lastActionRef.current === 'shrink') {
                // The previous shrink didn't pay off: revert it, don't try shrinking again this cycle.
                lastActionRef.current = null;
                shrinkExhaustedRef.current = true;
                if (stepsRef.current < bound) {
                    stepsRef.current += 1;
                    setAutoOverflowCount((count) => count + 1);
                    return;
                }
            } else if (autoOverflowCount < rest.length && stepsRef.current < bound) {
                stepsRef.current += 1;
                lastActionRef.current = 'grow';
                setAutoOverflowCount((count) => count + 1);
                return;
            }

            reveal();
            setMeasured(true);
            return;
        }

        if (autoOverflowCount > 0 && !shrinkExhaustedRef.current && stepsRef.current < bound) {
            // Try recovering one item back out of overflow, in case there's now room for it.
            stepsRef.current += 1;
            lastActionRef.current = 'shrink';
            setAutoOverflowCount((count) => Math.max(count - 1, 0));
            return;
        }

        lastActionRef.current = null;
        reveal();
        setMeasured(true);
    });

    // Re-measures whenever the space available to the menu may have changed. Both triggers are
    // needed:
    //
    // - The ResizeObserver catches width changes that don't come from the viewport, e.g. a drawer
    //   opening beside the navigation. It observes the container, not the <ul>, so it doesn't
    //   fire from the measurement effect's own re-renders.
    // - The window resize listener, as legacy moremenu.js used, catches viewport changes that
    //   leave the container's box untouched. The primary navigation's mount point is a
    //   shrink-to-fit flex item, so once items have collapsed into "More" it is only as wide as
    //   what's left: widening the window resizes it by nothing, and they would never come back out.
    useEffect(() => {
        const remeasure = () => {
            stepsRef.current = 0;
            lastActionRef.current = null;
            shrinkExhaustedRef.current = false;
            forceRemeasure((tick) => tick + 1);
        };

        window.addEventListener('resize', remeasure);

        const container = menuRef.current?.parentElement;
        let observer: ResizeObserver | null = null;
        if (container && typeof ResizeObserver !== 'undefined') {
            observer = new ResizeObserver(remeasure);
            observer.observe(container);
        }

        return () => {
            window.removeEventListener('resize', remeasure);
            observer?.disconnect();
        };
    }, []);

    const visibleCount = Math.max(rest.length - autoOverflowCount, 0);
    const visible = rest.slice(0, visibleCount);
    // Auto-collapsed items first, then always-forced ones, matching legacy moremenu.js ordering.
    const overflow = [...rest.slice(visibleCount), ...forced];

    // Matches secondarymoremenu.mustache's NonJS fallback: every <li> is role="none" regardless
    // of istablist, and the <ul> itself is "menubar" rather than roleless when not a tablist, so
    // that Behat helpers like behat_navigation::select_on_administration_page() (which look up
    // //ul[@role='menubar']/li/a[...] for non-tablist navs) keep working under JS.
    const itemRole = 'none';

    return (
        <ul
            ref={menuRef}
            className={['nav', 'more-nav', navbarstyle].filter(Boolean).join(' ')}
            role={istablist ? 'tablist' : 'menubar'}
        >
            {visible.map((item) => {
                const isSubmenuTrigger = item.showchildreninsubmenu && item.children.length > 0;
                return (
                    <li
                        key={item.key}
                        role={itemRole}
                        className={`nav-item d-flex align-items-center${isSubmenuTrigger ? ' dropdown' : ''}`}
                    >
                        {renderPill(item, istablist)}
                    </li>
                );
            })}
            <li
                role={itemRole}
                className={`nav-item d-flex align-items-center dropdown dropdownmoremenu${overflow.length === 0 ? ' d-none' : ''}`}
            >
                <PillDropdownToggle label={morelabel} selected={overflow.some(isNodeActive)} istablist={istablist}>
                    <div className="dropdown-menu dropdown-menu-start" data-region="moredropdown">
                        <DropdownItems items={overflow} istablist={istablist} submenus />
                    </div>
                </PillDropdownToggle>
            </li>
        </ul>
    );
}
