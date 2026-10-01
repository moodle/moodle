var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * Shared navigation pill engine, used by the secondary navigation (and, via
 * core/nav/PrimaryNav, the primary navigation).
 *
 * @module     core/nav/Nav
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @copyright  2026 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
  Fragment as Fragment2,
  cloneElement,
  isValidElement,
  useEffect,
  useId,
  useLayoutEffect,
  useMemo,
  useRef,
  useState
} from "react";
import { NavPill } from "@moodlehq/design-system";
import { requireAsync } from "@moodle/lms/core/amd";
const isNodeActive = /* @__PURE__ */ __name((node) => node.active || node.children.some(isNodeActive), "isNodeActive");
const withActiveHref = /* @__PURE__ */ __name((nodes, activeHref) => nodes.map((node) => ({
  ...node,
  active: node.href === activeHref,
  children: withActiveHref(node.children, activeHref)
})), "withActiveHref");
const hasNodeWithHref = /* @__PURE__ */ __name((nodes, href) => nodes.some((node) => node.href === href || hasNodeWithHref(node.children, href)), "hasNodeWithHref");
const RESERVED_ATTRIBUTE_NAMES = /* @__PURE__ */ new Set(["id", "class", "disabled"]);
const toAttributeRecord = /* @__PURE__ */ __name((attributes = []) => Object.fromEntries(
  attributes.filter(({ name }) => !RESERVED_ATTRIBUTE_NAMES.has(name)).map(({ name, value }) => [name, String(value)])
), "toAttributeRecord");
const resolveGlobalFunction = /* @__PURE__ */ __name((path) => path.split(".").reduce((obj, key) => obj?.[key], window), "resolveGlobalFunction");
const useActionLinkBehavior = /* @__PURE__ */ __name((items) => {
  const actionSignature = useMemo(
    () => JSON.stringify(
      items.filter((item) => item.id && item.actions?.length).map((item) => [item.id, item.actions.map(
        (action) => [action.event, action.jsfunction, action.jsfunctionargs]
      )])
    ),
    [items]
  );
  useEffect(() => {
    const nodesWithActions = items.filter((item) => item.id && item.actions?.length);
    if (nodesWithActions.length === 0) {
      return void 0;
    }
    const cleanups = [];
    nodesWithActions.forEach((item) => {
      const el = document.getElementById(item.id);
      if (!el) {
        return;
      }
      item.actions.forEach((action) => {
        const fn = resolveGlobalFunction(action.jsfunction);
        if (!fn) {
          return;
        }
        const args = action.jsfunctionargs ? JSON.parse(action.jsfunctionargs) : void 0;
        const listener = /* @__PURE__ */ __name((event) => fn(event, args), "listener");
        el.addEventListener(action.event, listener);
        cleanups.push(() => el.removeEventListener(action.event, listener));
      });
    });
    return () => {
      cleanups.forEach((cleanup) => cleanup());
    };
  }, [actionSignature]);
}, "useActionLinkBehavior");
const keepParentMenuOpen = /* @__PURE__ */ __name((event) => event.stopPropagation(), "keepParentMenuOpen");
function DropdownSubmenu({ node, istablist = false }) {
  const id = useId();
  const toggleId = `${id}-toggle`;
  const menuId = `${id}-menu`;
  return (
    // The wrapper only exists to give Bootstrap's dropdown JS a container, so role="none"
    // keeps the toggle a valid child of the enclosing role="menu", as the legacy <li> did.
    /* @__PURE__ */ jsxDEV("div", { className: "dropdown dropdown-submenu", role: "none", onClickCapture: keepParentMenuOpen, children: [
      /* @__PURE__ */ jsxDEV(
        "a",
        {
          id: toggleId,
          className: `dropdown-item dropdown-toggle${isNodeActive(node) ? " active" : ""}`,
          href: "#",
          title: node.title ?? void 0,
          role: "menuitem",
          "data-bs-toggle": "dropdown",
          "data-bs-display": "static",
          "aria-haspopup": "true",
          "aria-expanded": "false",
          "aria-controls": menuId,
          "aria-current": isNodeActive(node) ? "page" : void 0,
          children: node.text
        },
        void 0,
        false,
        {
          fileName: "public/lib/js/esm/src/nav/Nav.tsx",
          lineNumber: 228,
          columnNumber: 13
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "dropdown-menu", id: menuId, role: istablist ? "none" : "menu", "aria-labelledby": toggleId, children: /* @__PURE__ */ jsxDEV(DropdownItems, { items: node.children, istablist }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 247,
        columnNumber: 17
      }, this) }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 246,
        columnNumber: 13
      }, this)
    ] }, void 0, true, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 227,
      columnNumber: 9
    }, this)
  );
}
__name(DropdownSubmenu, "DropdownSubmenu");
function DropdownItems({ items, istablist = false, submenus = false }) {
  useActionLinkBehavior(items);
  return /* @__PURE__ */ jsxDEV(Fragment, { children: items.map((item) => {
    if (item.divider) {
      return /* @__PURE__ */ jsxDEV("div", { className: "dropdown-divider", role: "separator" }, item.key, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 279,
        columnNumber: 28
      }, this);
    }
    if (submenus && item.showchildreninsubmenu && item.children.length > 0) {
      return /* @__PURE__ */ jsxDEV(DropdownSubmenu, { node: item, istablist }, item.key, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 283,
        columnNumber: 28
      }, this);
    }
    let ariaSelected;
    if (istablist) {
      ariaSelected = item.active ? "true" : "false";
    }
    return /* @__PURE__ */ jsxDEV(
      "a",
      {
        id: item.id ?? void 0,
        className: `dropdown-item${item.active ? " active" : ""}`,
        href: item.href ?? "#",
        title: item.title ?? void 0,
        "aria-current": !istablist && item.active ? "page" : void 0,
        "aria-selected": ariaSelected,
        role: istablist ? "tab" : "menuitem",
        "data-bs-toggle": istablist ? "tab" : void 0,
        "data-text": istablist ? item.text : void 0,
        "data-disableactive": "true",
        ...toAttributeRecord(item.attributes),
        dangerouslySetInnerHTML: { __html: item.text }
      },
      item.key,
      false,
      {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 292,
        columnNumber: 21
      },
      this
    );
  }) }, void 0, false, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 276,
    columnNumber: 9
  }, this);
}
__name(DropdownItems, "DropdownItems");
function PillDropdownToggle({ label, selected, title, istablist = false, children }) {
  const classes = ["mds-nav-pill", "dropdown-toggle", selected ? "mds-nav-pill--selected" : null].filter(Boolean).join(" ");
  const id = useId();
  const toggleId = `${id}-toggle`;
  const menuId = `${id}-menu`;
  const menu = isValidElement(children) ? cloneElement(children, {
    id: menuId,
    // A role="menu" may only own menuitems, but an istablist dropdown holds role="tab"
    // items (see DropdownItems). role="none" keeps the container out of the accessibility
    // tree so those tabs stay owned by the enclosing role="tablist".
    role: istablist ? "none" : "menu",
    "aria-labelledby": toggleId
  }) : children;
  return /* @__PURE__ */ jsxDEV(Fragment2, { children: [
    /* @__PURE__ */ jsxDEV(
      "a",
      {
        href: "#",
        id: toggleId,
        className: classes,
        title,
        role: istablist ? "tab" : "menuitem",
        "data-bs-toggle": "dropdown",
        "aria-haspopup": "true",
        "aria-expanded": "false",
        "aria-controls": menuId,
        "aria-current": selected ? "page" : void 0,
        tabIndex: selected ? 0 : -1,
        children: [
          selected && /* @__PURE__ */ jsxDEV("span", { className: "mds-nav-pill__indicator", "aria-hidden": "true" }, void 0, false, {
            fileName: "public/lib/js/esm/src/nav/Nav.tsx",
            lineNumber: 394,
            columnNumber: 30
          }, this),
          /* @__PURE__ */ jsxDEV("span", { className: "mds-nav-pill__label", dangerouslySetInnerHTML: { __html: label } }, void 0, false, {
            fileName: "public/lib/js/esm/src/nav/Nav.tsx",
            lineNumber: 396,
            columnNumber: 17
          }, this)
        ]
      },
      void 0,
      true,
      {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 378,
        columnNumber: 13
      },
      this
    ),
    menu
  ] }, void 0, true, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 377,
    columnNumber: 9
  }, this);
}
__name(PillDropdownToggle, "PillDropdownToggle");
function TabPill({ node }) {
  const selected = isNodeActive(node);
  return /* @__PURE__ */ jsxDEV(
    "a",
    {
      href: node.href ?? "#",
      className: `mds-nav-pill${selected ? " active" : ""}`,
      title: node.title ?? void 0,
      role: "tab",
      "data-bs-toggle": "tab",
      "data-text": node.text,
      "data-disableactive": "true",
      "aria-selected": selected ? "true" : "false",
      tabIndex: selected ? 0 : -1,
      children: /* @__PURE__ */ jsxDEV("span", { className: "mds-nav-pill__label", dangerouslySetInnerHTML: { __html: node.text } }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 426,
        columnNumber: 13
      }, this)
    },
    void 0,
    false,
    {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 414,
      columnNumber: 9
    },
    this
  );
}
__name(TabPill, "TabPill");
function SubmenuTrigger({ node, istablist = false }) {
  return /* @__PURE__ */ jsxDEV(
    PillDropdownToggle,
    {
      label: node.text,
      selected: isNodeActive(node),
      title: node.title ?? void 0,
      istablist,
      children: /* @__PURE__ */ jsxDEV("div", { className: "dropdown-menu", children: /* @__PURE__ */ jsxDEV(DropdownItems, { items: node.children, istablist }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 448,
        columnNumber: 17
      }, this) }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 447,
        columnNumber: 13
      }, this)
    },
    void 0,
    false,
    {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 441,
      columnNumber: 9
    },
    this
  );
}
__name(SubmenuTrigger, "SubmenuTrigger");
const stampMenuItemRole = /* @__PURE__ */ __name((el) => {
  el?.setAttribute("role", "menuitem");
}, "stampMenuItemRole");
const renderPill = /* @__PURE__ */ __name((item, istablist) => {
  if (item.showchildreninsubmenu && item.children.length > 0) {
    return /* @__PURE__ */ jsxDEV(SubmenuTrigger, { node: item, istablist }, void 0, false, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 482,
      columnNumber: 16
    });
  }
  if (istablist) {
    return /* @__PURE__ */ jsxDEV(TabPill, { node: item }, void 0, false, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 485,
      columnNumber: 16
    });
  }
  const selected = isNodeActive(item);
  return /* @__PURE__ */ jsxDEV(
    NavPill,
    {
      ref: stampMenuItemRole,
      label: item.text,
      href: item.href ?? "#",
      title: item.title ?? void 0,
      selected,
      tabIndex: selected ? 0 : -1,
      "data-disableactive": "true"
    },
    void 0,
    false,
    {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 489,
      columnNumber: 9
    }
  );
}, "renderPill");
const MEASURED_CLASS = "secondarynav-measured";
function Nav({ items, morelabel, istablist, navbarstyle, measuredclass = MEASURED_CLASS, navlabel }) {
  const menuRef = useRef(null);
  const [activeOverrideHref, setActiveOverrideHref] = useState(() => {
    if (!istablist) {
      return null;
    }
    const hash = window.location.hash;
    return hash && hasNodeWithHref(items, hash) ? hash : null;
  });
  useEffect(() => {
    if (!istablist) {
      return void 0;
    }
    const handleShown = /* @__PURE__ */ __name((event) => {
      const target = event.target;
      if (!(target instanceof HTMLElement) || !menuRef.current?.contains(target)) {
        return;
      }
      const href = target.getAttribute("href");
      if (href && href !== "#") {
        setActiveOverrideHref(href);
      }
    }, "handleShown");
    document.addEventListener("shown.bs.tab", handleShown);
    return () => document.removeEventListener("shown.bs.tab", handleShown);
  }, [istablist]);
  const effectiveItems = istablist && activeOverrideHref ? withActiveHref(items, activeOverrideHref) : items;
  const toplevel = effectiveItems.filter((item) => !item.divider);
  const forced = toplevel.filter((item) => item.forceintomoremenu);
  const rest = toplevel.filter((item) => !item.forceintomoremenu);
  const landmarkRef = useRef(null);
  const [autoOverflowCount, setAutoOverflowCount] = useState(0);
  const [measured, setMeasured] = useState(false);
  const [, forceRemeasure] = useState(0);
  const stepsRef = useRef(0);
  const lastActionRef = useRef(null);
  const shrinkExhaustedRef = useRef(false);
  const itemsKey = items.map((item) => item.key).join(" ");
  const prevItemsKeyRef = useRef(itemsKey);
  useEffect(() => {
    if (!menuRef.current) {
      return void 0;
    }
    let cancelled = false;
    requireAsync("core/menu_navigation").then((menuNavigation) => {
      if (!cancelled && menuRef.current) {
        menuNavigation(menuRef.current);
      }
      return void 0;
    });
    return () => {
      cancelled = true;
    };
  }, []);
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
    const menu2 = menuRef.current;
    const container = (landmarkRef.current ?? menu2)?.parentElement;
    if (!menu2 || !container) {
      setMeasured(true);
      return;
    }
    const reveal = /* @__PURE__ */ __name(() => {
      if (!measured) {
        container.classList.add(measuredclass);
      }
    }, "reveal");
    const wrapped = menu2.offsetHeight > container.offsetHeight;
    const bound = 2 * rest.length + 2;
    if (wrapped) {
      if (lastActionRef.current === "shrink") {
        lastActionRef.current = null;
        shrinkExhaustedRef.current = true;
        if (stepsRef.current < bound) {
          stepsRef.current += 1;
          setAutoOverflowCount((count) => count + 1);
          return;
        }
      } else if (autoOverflowCount < rest.length && stepsRef.current < bound) {
        stepsRef.current += 1;
        lastActionRef.current = "grow";
        setAutoOverflowCount((count) => count + 1);
        return;
      }
      reveal();
      setMeasured(true);
      return;
    }
    if (autoOverflowCount > 0 && !shrinkExhaustedRef.current && stepsRef.current < bound) {
      stepsRef.current += 1;
      lastActionRef.current = "shrink";
      setAutoOverflowCount((count) => Math.max(count - 1, 0));
      return;
    }
    lastActionRef.current = null;
    reveal();
    setMeasured(true);
  });
  useEffect(() => {
    const remeasure = /* @__PURE__ */ __name(() => {
      stepsRef.current = 0;
      lastActionRef.current = null;
      shrinkExhaustedRef.current = false;
      forceRemeasure((tick) => tick + 1);
    }, "remeasure");
    window.addEventListener("resize", remeasure);
    const container = (landmarkRef.current ?? menuRef.current)?.parentElement;
    let observer = null;
    if (container && typeof ResizeObserver !== "undefined") {
      observer = new ResizeObserver(remeasure);
      observer.observe(container);
    }
    return () => {
      window.removeEventListener("resize", remeasure);
      observer?.disconnect();
    };
  }, []);
  const visibleCount = Math.max(rest.length - autoOverflowCount, 0);
  const visible = rest.slice(0, visibleCount);
  const overflow = [...rest.slice(visibleCount), ...forced];
  const itemRole = "none";
  useEffect(() => {
    if (!istablist) {
      return void 0;
    }
    const keys = ["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown", "Home", "End", " "];
    const handleKeyDown = /* @__PURE__ */ __name((event) => {
      const target = event.target;
      if (!keys.includes(event.key) || !(target instanceof HTMLElement) || target.closest(".dropdown-menu")) {
        return;
      }
      if (target.matches('[data-bs-toggle="dropdown"]') && !["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)) {
        return;
      }
      const stops = Array.from(menuRef.current?.querySelectorAll(':scope > li > a[role="tab"]') ?? []).filter((stop) => !stop.closest(".d-none"));
      const index = stops.indexOf(target);
      if (index === -1) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      if (event.key === " ") {
        target.click();
        return;
      }
      let next;
      if (event.key === "Home") {
        next = stops[0];
      } else if (event.key === "End") {
        next = stops[stops.length - 1];
      } else {
        const step = event.key === "ArrowRight" || event.key === "ArrowDown" ? 1 : -1;
        next = stops[(index + step + stops.length) % stops.length];
      }
      next.focus();
    }, "handleKeyDown");
    window.addEventListener("keydown", handleKeyDown, true);
    return () => window.removeEventListener("keydown", handleKeyDown, true);
  }, [istablist]);
  const menu = /* @__PURE__ */ jsxDEV(
    "ul",
    {
      ref: menuRef,
      className: ["nav", "more-nav", navbarstyle].filter(Boolean).join(" "),
      role: istablist ? "tablist" : "menubar",
      children: [
        visible.map((item) => {
          const isSubmenuTrigger = item.showchildreninsubmenu && item.children.length > 0;
          return /* @__PURE__ */ jsxDEV(
            "li",
            {
              role: itemRole,
              className: `nav-item d-flex align-items-center${isSubmenuTrigger ? " dropdown" : ""}`,
              children: renderPill(item, istablist)
            },
            item.key,
            false,
            {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 809,
              columnNumber: 21
            },
            this
          );
        }),
        /* @__PURE__ */ jsxDEV(
          "li",
          {
            role: itemRole,
            className: `nav-item d-flex align-items-center dropdown dropdownmoremenu${overflow.length === 0 ? " d-none" : ""}`,
            children: /* @__PURE__ */ jsxDEV(PillDropdownToggle, { label: morelabel, selected: overflow.some(isNodeActive), istablist, children: /* @__PURE__ */ jsxDEV("div", { className: "dropdown-menu dropdown-menu-start", "data-region": "moredropdown", children: /* @__PURE__ */ jsxDEV(DropdownItems, { items: overflow, istablist, submenus: true }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 824,
              columnNumber: 25
            }, this) }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 823,
              columnNumber: 21
            }, this) }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 822,
              columnNumber: 17
            }, this)
          },
          void 0,
          false,
          {
            fileName: "public/lib/js/esm/src/nav/Nav.tsx",
            lineNumber: 818,
            columnNumber: 13
          },
          this
        )
      ]
    },
    void 0,
    true,
    {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 801,
      columnNumber: 9
    },
    this
  );
  if (!navlabel) {
    return menu;
  }
  return /* @__PURE__ */ jsxDEV("nav", { ref: landmarkRef, "aria-label": navlabel, children: menu }, void 0, false, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 844,
    columnNumber: 9
  }, this);
}
__name(Nav, "Nav");
export {
  Nav as default
};
//# sourceMappingURL=Nav.dev.js.map
