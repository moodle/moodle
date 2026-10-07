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
const isNodeActive = /* @__PURE__ */ __name((node) => node.active || node.children.some((child) => isNodeActive(child)), "isNodeActive");
const withActiveHref = /* @__PURE__ */ __name((nodes, activeHref) => nodes.map((node) => ({
  ...node,
  active: node.href === activeHref,
  children: withActiveHref(node.children, activeHref)
})), "withActiveHref");
const hasNodeWithHref = /* @__PURE__ */ __name((nodes, href) => nodes.some((node) => node.href === href || hasNodeWithHref(node.children, href)), "hasNodeWithHref");
const RESERVED_ATTRIBUTE_NAMES = /* @__PURE__ */ new Set(["id", "class", "disabled"]);
const toAttributeRecord = /* @__PURE__ */ __name((attributes = []) => Object.fromEntries(attributes.filter(({ name }) => !RESERVED_ATTRIBUTE_NAMES.has(name)).map(({ name, value }) => [name, String(value)])), "toAttributeRecord");
const resolveGlobalFunction = /* @__PURE__ */ __name((path) => {
  let value = globalThis;
  for (const key of path.split(".")) {
    value = value?.[key];
  }
  return typeof value === "function" ? value : void 0;
}, "resolveGlobalFunction");
const hasBindableActions = /* @__PURE__ */ __name((item) => (item.id ?? "") !== "" && (item.actions?.length ?? 0) > 0, "hasBindableActions");
const useActionLinkBehavior = /* @__PURE__ */ __name((items) => {
  const actionSignature = useMemo(
    () => JSON.stringify(items.filter((item) => hasBindableActions(item)).map((item) => [item.id, item.actions.map((action) => [action.event, action.jsfunction, action.jsfunctionargs])])),
    [items]
  );
  useEffect(() => {
    const nodesWithActions = items.filter((item) => hasBindableActions(item));
    if (nodesWithActions.length === 0) {
      return void 0;
    }
    const cleanups = [];
    for (const item of nodesWithActions) {
      const element = document.querySelector(`#${CSS.escape(item.id)}`);
      if (!element) {
        continue;
      }
      for (const action of item.actions) {
        const function_ = resolveGlobalFunction(action.jsfunction);
        if (function_ !== void 0) {
          const { jsfunctionargs } = action;
          const args = typeof jsfunctionargs === "string" && jsfunctionargs !== "" ? JSON.parse(jsfunctionargs) : void 0;
          const listener = /* @__PURE__ */ __name((event) => {
            function_(event, args);
          }, "listener");
          element.addEventListener(action.event, listener);
          cleanups.push(() => {
            element.removeEventListener(action.event, listener);
          });
        }
      }
    }
    return () => {
      for (const cleanup of cleanups) {
        cleanup();
      }
    };
  }, [actionSignature]);
}, "useActionLinkBehavior");
const keepParentMenuOpen = /* @__PURE__ */ __name((event) => {
  event.stopPropagation();
}, "keepParentMenuOpen");
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
          lineNumber: 259,
          columnNumber: 13
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "dropdown-menu", id: menuId, role: istablist ? "none" : "menu", "aria-labelledby": toggleId, children: /* @__PURE__ */ jsxDEV(DropdownItems, { items: node.children, istablist }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 278,
        columnNumber: 17
      }, this) }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 277,
        columnNumber: 13
      }, this)
    ] }, void 0, true, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 258,
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
        lineNumber: 308,
        columnNumber: 28
      }, this);
    }
    if (submenus && item.showchildreninsubmenu && item.children.length > 0) {
      return /* @__PURE__ */ jsxDEV(DropdownSubmenu, { node: item, istablist }, item.key, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 312,
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
        lineNumber: 321,
        columnNumber: 21
      },
      this
    );
  }) }, void 0, false, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 305,
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
            lineNumber: 421,
            columnNumber: 30
          }, this),
          /* @__PURE__ */ jsxDEV("span", { className: "mds-nav-pill__label", dangerouslySetInnerHTML: { __html: label } }, void 0, false, {
            fileName: "public/lib/js/esm/src/nav/Nav.tsx",
            lineNumber: 423,
            columnNumber: 17
          }, this)
        ]
      },
      void 0,
      true,
      {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 405,
        columnNumber: 13
      },
      this
    ),
    menu
  ] }, void 0, true, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 404,
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
        lineNumber: 453,
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
        lineNumber: 475,
        columnNumber: 17
      }, this) }, void 0, false, {
        fileName: "public/lib/js/esm/src/nav/Nav.tsx",
        lineNumber: 474,
        columnNumber: 13
      }, this)
    },
    void 0,
    false,
    {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 468,
      columnNumber: 9
    },
    this
  );
}
__name(SubmenuTrigger, "SubmenuTrigger");
const stampMenuItemRole = /* @__PURE__ */ __name((element) => {
  element?.setAttribute("role", "menuitem");
}, "stampMenuItemRole");
const renderPill = /* @__PURE__ */ __name((item, istablist) => {
  if (item.showchildreninsubmenu && item.children.length > 0) {
    return /* @__PURE__ */ jsxDEV(SubmenuTrigger, { node: item, istablist }, void 0, false, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 509,
      columnNumber: 16
    });
  }
  if (istablist) {
    return /* @__PURE__ */ jsxDEV(TabPill, { node: item }, void 0, false, {
      fileName: "public/lib/js/esm/src/nav/Nav.tsx",
      lineNumber: 513,
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
      lineNumber: 518,
      columnNumber: 9
    }
  );
}, "renderPill");
const MEASURED_CLASS = "secondarynav-measured";
function Nav({ items, morelabel, istablist, navbarstyle, measuredclass = MEASURED_CLASS, navlabel }) {
  const menuReference = useRef(null);
  const [activeOverrideHref, setActiveOverrideHref] = useState(() => {
    if (!istablist) {
      return null;
    }
    const { hash } = globalThis.location;
    return hash !== "" && hasNodeWithHref(items, hash) ? hash : null;
  });
  useEffect(() => {
    if (!istablist) {
      return void 0;
    }
    const handleShown = /* @__PURE__ */ __name((event) => {
      const { target } = event;
      if (!(target instanceof HTMLElement) || !menuReference.current?.contains(target)) {
        return;
      }
      const href = target.getAttribute("href");
      if (href !== null && !["", "#"].includes(href)) {
        setActiveOverrideHref(href);
      }
    }, "handleShown");
    document.addEventListener("shown.bs.tab", handleShown);
    return () => {
      document.removeEventListener("shown.bs.tab", handleShown);
    };
  }, [istablist]);
  const effectiveItems = istablist && activeOverrideHref !== null ? withActiveHref(items, activeOverrideHref) : items;
  const toplevel = effectiveItems.filter((item) => !item.divider);
  const forced = toplevel.filter((item) => item.forceintomoremenu);
  const rest = toplevel.filter((item) => !item.forceintomoremenu);
  const landmarkReference = useRef(null);
  const [autoOverflowCount, setAutoOverflowCount] = useState(0);
  const [measured, setMeasured] = useState(false);
  const [, forceRemeasure] = useState(0);
  const stepsReference = useRef(0);
  const lastActionReference = useRef(null);
  const shrinkExhaustedReference = useRef(false);
  const itemsKey = items.map((item) => item.key).join(" ");
  const previousItemsKeyReference = useRef(itemsKey);
  useEffect(() => {
    if (!menuReference.current) {
      return void 0;
    }
    let cancelled = false;
    void requireAsync("core/menu_navigation").then((menuNavigation) => {
      if (!cancelled && menuReference.current) {
        menuNavigation(menuReference.current);
      }
      return void 0;
    });
    return () => {
      cancelled = true;
    };
  }, []);
  useLayoutEffect(() => {
    if (previousItemsKeyReference.current !== itemsKey) {
      previousItemsKeyReference.current = itemsKey;
      stepsReference.current = 0;
      lastActionReference.current = null;
      shrinkExhaustedReference.current = false;
      if (autoOverflowCount !== 0) {
        setAutoOverflowCount(0);
        return;
      }
    }
    const menu2 = menuReference.current;
    const container = (landmarkReference.current ?? menu2)?.parentElement;
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
      if (lastActionReference.current === "shrink") {
        lastActionReference.current = null;
        shrinkExhaustedReference.current = true;
        if (stepsReference.current < bound) {
          stepsReference.current += 1;
          setAutoOverflowCount((count) => count + 1);
          return;
        }
      } else if (autoOverflowCount < rest.length && stepsReference.current < bound) {
        stepsReference.current += 1;
        lastActionReference.current = "grow";
        setAutoOverflowCount((count) => count + 1);
        return;
      }
      reveal();
      setMeasured(true);
      return;
    }
    if (autoOverflowCount > 0 && !shrinkExhaustedReference.current && stepsReference.current < bound) {
      stepsReference.current += 1;
      lastActionReference.current = "shrink";
      setAutoOverflowCount((count) => Math.max(count - 1, 0));
      return;
    }
    lastActionReference.current = null;
    reveal();
    setMeasured(true);
  });
  useEffect(() => {
    const remeasure = /* @__PURE__ */ __name(() => {
      stepsReference.current = 0;
      lastActionReference.current = null;
      shrinkExhaustedReference.current = false;
      forceRemeasure((tick) => tick + 1);
    }, "remeasure");
    window.addEventListener("resize", remeasure);
    const container = (landmarkReference.current ?? menuReference.current)?.parentElement;
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
    const keys = /* @__PURE__ */ new Set(["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown", "Home", "End", " "]);
    const handleKeyDown = /* @__PURE__ */ __name((event) => {
      const { target } = event;
      if (!keys.has(event.key) || !(target instanceof HTMLElement) || target.closest(".dropdown-menu")) {
        return;
      }
      if (target.matches('[data-bs-toggle="dropdown"]') && !["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)) {
        return;
      }
      const stops = [...menuReference.current?.querySelectorAll(':scope > li > a[role="tab"]') ?? []].filter((stop) => !stop.closest(".d-none"));
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
    globalThis.addEventListener("keydown", handleKeyDown, { capture: true });
    return () => {
      globalThis.removeEventListener("keydown", handleKeyDown, true);
    };
  }, [istablist]);
  const menu = /* @__PURE__ */ jsxDEV(
    "ul",
    {
      ref: menuReference,
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
              lineNumber: 844,
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
            children: /* @__PURE__ */ jsxDEV(PillDropdownToggle, { label: morelabel, selected: overflow.some((node) => isNodeActive(node)), istablist, children: /* @__PURE__ */ jsxDEV("div", { className: "dropdown-menu dropdown-menu-start", "data-region": "moredropdown", children: /* @__PURE__ */ jsxDEV(DropdownItems, { items: overflow, istablist, submenus: true }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 859,
              columnNumber: 25
            }, this) }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 858,
              columnNumber: 21
            }, this) }, void 0, false, {
              fileName: "public/lib/js/esm/src/nav/Nav.tsx",
              lineNumber: 857,
              columnNumber: 17
            }, this)
          },
          void 0,
          false,
          {
            fileName: "public/lib/js/esm/src/nav/Nav.tsx",
            lineNumber: 853,
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
      lineNumber: 836,
      columnNumber: 9
    },
    this
  );
  if (navlabel === void 0 || navlabel === "") {
    return menu;
  }
  return /* @__PURE__ */ jsxDEV("nav", { ref: landmarkReference, "aria-label": navlabel, children: menu }, void 0, false, {
    fileName: "public/lib/js/esm/src/nav/Nav.tsx",
    lineNumber: 879,
    columnNumber: 9
  }, this);
}
__name(Nav, "Nav");
export {
  Nav as default
};
//# sourceMappingURL=Nav.dev.js.map
