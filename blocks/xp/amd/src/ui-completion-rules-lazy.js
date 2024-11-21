/* eslint-disable */
/* Do not edit directly, refer to ui/ folder. */
define(["block_xp/ui-commons-lazy"],() => { return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 913:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  App: () => (/* binding */ App),
  dependencies: () => (/* binding */ dependencies),
  startApp: () => (/* binding */ startApp)
});

// EXTERNAL MODULE: ./node_modules/@headlessui/react/dist/components/tabs/tabs.js + 2 modules
var tabs = __webpack_require__(848);
// EXTERNAL MODULE: ./node_modules/react/index.js
var react = __webpack_require__(540);
// EXTERNAL MODULE: ./node_modules/react-dom/index.js
var react_dom = __webpack_require__(961);
// EXTERNAL MODULE: ./node_modules/react-query/es/index.js
var es = __webpack_require__(942);
// EXTERNAL MODULE: ./node_modules/@headlessui/react/dist/components/menu/menu.js + 13 modules
var menu = __webpack_require__(929);
;// CONCATENATED MODULE: ./ui/src/lib/utils.ts
const utils_classNames = (...args) => args.filter(Boolean).join(" ");
const fifoCache = (maxItems = 128) => {
    let items = {};
    let keys = [];
    const purge = () => {
        if (keys.length > maxItems) {
            const idx = Math.max(0, keys.length - maxItems);
            keys.slice(0, idx).forEach((key) => {
                delete items[key];
            });
            keys = keys.slice(idx);
        }
    };
    return {
        set: (key, value) => {
            items[key] = value;
            keys.push(key);
            purge();
        },
        get: (key) => {
            return items[key];
        },
    };
};
let uniqueId = 0;
const getUniqueId = () => {
    return `xp-${Date.now()}-${uniqueId++}`;
};
const stripTags = (html) => {
    var tmp = document.createElement("div");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
};
const escapeCharMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
};
const escapeHtml = (text) => {
    return text.replace(/[&<>"']/g, function (m) { return escapeCharMap[m]; });
};

;// CONCATENATED MODULE: ./ui/src/components/Dropdown.tsx



const Dropdown = ({ buttonLabel, items }) => {
    return (react.createElement(menu/* Menu */.W, { as: "div", className: "dropdown" },
        react.createElement(menu/* Menu */.W.Button, { className: "btn btn-link btn-icon icon-size-3 rounded-circle" },
            react.createElement("i", { className: "fa fa-ellipsis-v text-dark py-2", "aria-hidden": "true" }),
            react.createElement("span", { className: "xp-sr-only" }, buttonLabel)),
        react.createElement(menu/* Menu */.W.Items, { className: "dropdown-menu dropdown-menu-right xp-block" }, items.map((item) => {
            if ("divider" in item) {
                return react.createElement("div", { key: item.id, className: "dropdown-divider" });
            }
            return (react.createElement(menu/* Menu */.W.Item, { key: item.id }, ({ active }) => (react.createElement("a", { ...item.props, className: utils_classNames("dropdown-item", item.danger ? "text-danger" : null) }, item.label))));
        }))));
};

;// CONCATENATED MODULE: ./ui/src/components/Loading.tsx

const AppLoading = () => {
    return (react.createElement("div", { className: "block_xp-react-loading" },
        react.createElement("div", { className: "xp-grid xp-grid-cols-2 xp-gap-4 xp-animate-pulse" },
            react.createElement("div", { className: "xp-col-span-2 xp-bg-gray-100 xp-rounded xp-h-4" }),
            react.createElement("div", { className: "xp-bg-gray-100 xp-rounded xp-h-4" }),
            react.createElement("div", { className: "xp-bg-gray-100 xp-rounded xp-h-4" }))));
};

;// CONCATENATED MODULE: ./ui/src/lib/moodle.ts

const M = window.M;
const modules = {};
/**
 * List of modules that we currently depend on statically.
 *
 * Preferrably, modules should be loaded with getModuleAsync, which
 * does not require their definition to be declared in our apps.
 */
const commonStaticModulesToDependOn = [
    "core/notification",
    "core/aria",
    "?core/toast",
    "jquery",
];
async function ajaxRequest(method, args) {
    const Ajax = await getModuleAsync("core/ajax");
    return Ajax.call([{
            methodname: method,
            args,
        }])[0];
}
function getString(id, component, a) {
    return M.util.get_string(id, component, a);
}
function getUrl(uri) {
    if (uri[0] != "/") {
        uri = "/" + uri;
    }
    return M.cfg.wwwroot + uri;
}
function hasString(id, component) {
    return typeof M.str[component] !== "undefined" && typeof M.str[component][id] !== "undefined";
}
function getModule(name) {
    return modules[name];
}
async function getModuleAsync(amd) {
    if (modules[amd]) {
        return modules[amd];
    }
    return new Promise((resolve, reject) => {
        // @ts-ignore
        window.require([amd], (mod) => {
            modules[amd] = mod;
            resolve(mod);
        }, reject);
    });
}
function moodle_imageUrl(name, component) {
    return M.util.image_url(name, component);
}
function moodle_isBehatRunning() {
    return M.cfg.behatsiterunning;
}
let loadStringCache = fifoCache(64);
async function loadString(id, component) {
    const cacheKey = `${id}/${component}`;
    let promise = loadStringCache.get(cacheKey);
    if (!promise) {
        const Str = await getModuleAsync("core/str");
        promise = Str.get_string(id, component);
        loadStringCache.set(cacheKey, promise);
    }
    return await promise;
}
async function loadStrings(ids, component) {
    const cacheKey = `${ids.join(",")}/${component}`;
    let promise = loadStringCache.get(cacheKey);
    if (!promise) {
        const Str = await getModuleAsync("core/str");
        promise = Str.get_strings(ids.map((id) => ({ key: id, component })));
        loadStringCache.set(cacheKey, promise);
    }
    return await promise;
}
const makeDependenciesDefinition = (names) => {
    let optional = [];
    const list = names.map((name) => {
        const isOptional = name.charAt(0) === "?";
        const module = isOptional ? name.substring(1) : name;
        if (isOptional) {
            optional.push(module);
        }
        return module;
    });
    return {
        list,
        optional,
        loader: (mods) => {
            mods.forEach((mod, i) => {
                setModule(list[i], mod);
            });
        },
    };
};
function setModule(name, mod) {
    modules[name] = mod;
}

;// CONCATENATED MODULE: ./ui/src/lib/contexts.ts

const makeAddonContextValueFromAppProps = (props) => {
    return {
        activated: false,
        enablepromo: true,
        promourl: "https://www.levelup.plus/xp/",
        ...(props?.addon ?? {}),
    };
};
const contexts_AddonContext = (0,react.createContext)({
    activated: false,
    enablepromo: true,
    promourl: "https://www.levelup.plus/xp/", // Local promo page where possible.
});

;// CONCATENATED MODULE: ./ui/src/lib/hooks.ts




const useAddonActivated = () => {
    return useContext(AddonContext).activated;
};
const hooks_useAnchorButtonProps = (onClick) => {
    const listeners = useRoleButtonListeners(onClick);
    return {
        href: "#",
        role: "button",
        ...listeners,
    };
};
/**
 * Duplication check hook.
 *
 * Usage:
 *
 * const isActionPermitted = useDuplicatedActionPreventor();
 * useEffect(() => {
 *    if (!isActionPermitted()) return;
 * })
 */
const useDuplicatedActionPreventor = (msDelay = 100) => {
    const ref = (0,react.useRef)();
    return (0,react.useCallback)(() => {
        if (ref.current && ref.current > Date.now() - msDelay) {
            return false;
        }
        ref.current = Date.now();
        return true;
    }, []);
};
const useModules = (modules) => {
    const modulesPromise = (0,react.useRef)();
    const modulesRef = (0,react.useRef)();
    const [ready, setReady] = (0,react.useState)(false);
    (0,react.useEffect)(() => {
        if (modulesRef.current)
            return;
        if (!modulesPromise.current) {
            modulesPromise.current = Promise.all(modules.map((module) => getModuleAsync(module)));
        }
        let cancelled = false;
        modulesPromise.current.then((loadedModles) => {
            if (cancelled)
                return;
            modulesRef.current = modules.reduce((acc, module, i) => {
                acc[module] = loadedModles[i];
                return acc;
            }, {});
            setReady(true);
        });
        return () => {
            cancelled = true;
        };
    });
    const getModule = (0,react.useCallback)((module) => {
        if (!modulesRef.current)
            return null;
        return modulesRef.current[module] ?? null;
    }, [ready, modulesRef.current]);
    return {
        getModule,
    };
};
const hooks_useNumericInputProps = (value, onChange) => {
    const valueAsString = value.toString();
    const [externalValue, setExternalValue] = (0,react.useState)(valueAsString);
    const [internalValue, setInternalValue] = (0,react.useState)(externalValue);
    (0,react.useEffect)(() => {
        if (valueAsString !== externalValue) {
            setExternalValue(valueAsString);
            setInternalValue(valueAsString);
        }
    });
    const handleBlur = (e) => {
        const v = parseInt(internalValue, 10) || 0;
        setExternalValue(v.toString());
        onChange(v);
    };
    const handleChange = (e) => {
        setInternalValue(e.target.value.replace(/[^0-9]/, ""));
    };
    return {
        value: internalValue,
        onChange: handleChange,
        onBlur: handleBlur,
    };
};
const useRoleButtonListeners = (onClick) => {
    const handleClick = (e) => {
        e.preventDefault();
        onClick();
    };
    const handleKeyDown = (e) => {
        if (e.key !== " " && e.key !== "Enter") {
            return;
        }
        e.preventDefault();
        onClick();
    };
    return {
        onClick: handleClick,
        onKeyDown: handleKeyDown,
    };
};
const useUnloadCheck = (isDirty) => {
    const str = hooks_useString("changesmadereallygoaway", "core");
    useEffect(() => {
        const fn = (e) => {
            if (!isDirty || isBehatRunning()) {
                return;
            }
            e.preventDefault();
            e.returnValue = str;
            return str;
        };
        window.addEventListener("beforeunload", fn);
        return () => {
            window.removeEventListener("beforeunload", fn);
        };
    });
};
const useUniqueId = () => {
    const [id] = (0,react.useState)(getUniqueId());
    return id;
};
const hooks_useString = (id, component = "block_xp", a) => {
    const wasKnownAtMount = (0,react.useMemo)(() => hasString(id, component), [id, component]);
    const [isLoaded, setLoaded] = (0,react.useState)(false);
    // When the string changes, remove the promise.
    (0,react.useEffect)(() => {
        setLoaded(false);
    }, [id, component]);
    // Load the string when it is unknown.
    (0,react.useEffect)(() => {
        if (wasKnownAtMount || isLoaded) {
            return;
        }
        let cancelled = false;
        (async () => {
            try {
                await loadString(id, component);
                if (!cancelled) {
                    setLoaded(true);
                }
            }
            catch (err) { }
        })();
        return () => {
            cancelled = true;
        };
    });
    return hasString(id, component) ? getString(id, component, a) : "​";
};
const hooks_useStrings = (ids, component = "block_xp") => {
    const idsForKey = ids.join(",");
    const allKnownAtMount = (0,react.useMemo)(() => ids.every((id) => hasString(id, component)), [idsForKey, component]);
    const [isLoaded, setLoaded] = (0,react.useState)(false);
    // When the string changes, remove the promise.
    (0,react.useEffect)(() => {
        setLoaded(false);
    }, [idsForKey, component]);
    // Load the string when it is unknown.
    (0,react.useEffect)(() => {
        if (allKnownAtMount || isLoaded) {
            return;
        }
        let cancelled = false;
        (async () => {
            try {
                await loadStrings(ids, component);
                if (!cancelled) {
                    setLoaded(true);
                }
            }
            catch (err) { }
        })();
        return () => {
            cancelled = true;
        };
    });
    return (id, a) => (hasString(id, component) ? getString(id, component, a) : "​");
};

;// CONCATENATED MODULE: ./ui/src/components/Modal.tsx



const SaveCancelModal = ({ children, onClose, onSave, show, title, saveButtonText, defaultHeight, large, canSave = true }) => {
    const modalPromise = (0,react.useRef)();
    const modalRef = (0,react.useRef)();
    // In rare instances, we can get double save events. This can happen when we hit enter,
    // and a new event listener is registered while Moodle is still broadcasting its events
    // which is then called, and so we get two events. This wouldn't happen if the modal was
    // not re-rendering, I think.
    const isSavePermitted = useDuplicatedActionPreventor();
    const { getModule } = useModules(["core/modal_factory", "core/modal_events"]);
    const [ready, setReady] = (0,react.useState)(false);
    const getSaveButton = (0,react.useCallback)(() => {
        if (!modalRef.current)
            return null;
        const node = modalRef.current.getFooter()[0].querySelector('[data-action="save"]');
        return node ?? null;
    }, [modalRef.current]);
    const setSaveButtonText = (text) => {
        const saveBtn = getSaveButton();
        if (!saveBtn || !text)
            return;
        saveBtn.textContent = text;
    };
    const setButtonAttribute = (attr, value) => {
        const saveBtn = getSaveButton();
        if (!saveBtn || !attr)
            return;
        if (value === null || typeof value === "undefined" || value === false) {
            saveBtn.removeAttribute(attr);
        }
        else {
            saveBtn.setAttribute(attr, value);
        }
    };
    // Create the modal object.
    (0,react.useEffect)(() => {
        let cancelled = false;
        if (modalRef.current)
            return;
        const ModalFactory = getModule("core/modal_factory");
        if (!ModalFactory)
            return;
        if (!modalPromise.current) {
            modalPromise.current = ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: title,
                large: large,
                body: `<div class='block_xp' style='${defaultHeight ? `height: ${defaultHeight}px` : ""}'></div>`,
            });
        }
        modalPromise.current.then((modal) => {
            if (cancelled)
                return;
            modalRef.current = modal;
            if (saveButtonText) {
                setSaveButtonText(saveButtonText);
            }
            setReady(true); // State update to force re-render.
            if (show) {
                modal.show();
            }
        });
        return () => {
            cancelled = true;
        };
    });
    // Attach event listeners.
    (0,react.useEffect)(() => {
        const modal = modalRef.current;
        if (!modal)
            return;
        const ModalEvents = getModule("core/modal_events");
        if (!ModalEvents)
            return;
        const root = modal.getRoot();
        const handleSave = (e) => {
            if (!isSavePermitted())
                return;
            onSave && onSave(e);
        };
        const handleClose = () => {
            onClose && onClose();
        };
        // Keep the React node height in sync with the modal body to avoid for the modal
        // to become scrollable. This is required because our current modal content is
        // absolute and thus requires a hardcoded height.
        const updateReactNodeHeight = () => {
            const body = modal.getBody()[0];
            const reactNode = body ? body.querySelector(".block_xp") : null;
            if (!body || !reactNode) {
                return;
            }
            const height = body.clientHeight - (parseFloat(getComputedStyle(body).paddingTop) + parseFloat(getComputedStyle(body).paddingBottom));
            reactNode.style.height = `${height}px`;
        };
        const attachResize = () => {
            window.addEventListener("resize", updateReactNodeHeight);
        };
        root.on(ModalEvents.save, handleSave);
        root.on(ModalEvents.hidden, handleClose);
        root.on(ModalEvents.shown, attachResize);
        return () => {
            root.off(ModalEvents.save, handleSave);
            root.off(ModalEvents.hidden, handleClose);
            root.off(ModalEvents.shown, attachResize);
            window.removeEventListener("resize", updateReactNodeHeight);
        };
    });
    // Update visibility.
    (0,react.useEffect)(() => {
        if (!modalRef.current)
            return;
        if (show) {
            modalRef.current.show();
        }
        else {
            modalRef.current.hide();
        }
    }, [show, modalRef.current]);
    // Update title.
    (0,react.useEffect)(() => {
        if (!modalRef.current || !title)
            return;
        modalRef.current.setTitle(title);
    }, [title, modalRef.current]);
    // Update save button text.
    (0,react.useEffect)(() => {
        setSaveButtonText(saveButtonText);
    }, [saveButtonText, modalRef.current]);
    // Update the save button status.
    (0,react.useEffect)(() => {
        setButtonAttribute("disabled", !canSave);
    }, [canSave, modalRef.current]);
    return modalRef.current ? react_dom.createPortal(children, modalRef.current.getBody()[0].querySelector(".block_xp")) : null;
};
const DeleteModal = ({ children, onClose, onDelete, show, title }) => {
    const modalPromise = (0,react.useRef)();
    const modalRef = (0,react.useRef)();
    const [ready, setReady] = (0,react.useState)(false);
    const isDeletePermitted = useDuplicatedActionPreventor();
    const deleteStr = hooks_useString("delete", "core");
    const { getModule } = useModules(["core/modal_factory", "core/modal_events"]);
    const getDeleteButton = (0,react.useCallback)(() => {
        if (!modalRef.current)
            return null;
        const node = modalRef.current.getFooter()[0].querySelector('[data-action="save"]');
        return node ?? null;
    }, [modalRef.current]);
    // Create the modal object.
    (0,react.useEffect)(() => {
        let cancelled = false;
        if (modalRef.current)
            return;
        const ModalFactory = getModule("core/modal_factory");
        if (!ModalFactory)
            return;
        if (!modalPromise.current) {
            modalPromise.current = ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL, // We use save_cancel as delete_cancel is only in 4.2.
                title: title,
                body: `<div class='block_xp'></div>`,
            });
        }
        modalPromise.current.then((modal) => {
            if (cancelled)
                return;
            modalRef.current = modal;
            const deleteButton = getDeleteButton();
            if (deleteButton) {
                if (deleteStr) {
                    deleteButton.textContent = deleteStr;
                }
                deleteButton.classList.add("btn-danger");
            }
            setReady(true); // State update to force re-render.
            if (show) {
                modal.show();
            }
        });
        return () => {
            cancelled = true;
        };
    });
    // Attach event listeners.
    (0,react.useEffect)(() => {
        const modal = modalRef.current;
        if (!modal)
            return;
        const ModalEvents = getModule("core/modal_events");
        if (!ModalEvents)
            return;
        const root = modal.getRoot();
        const handleSave = (e) => {
            if (!isDeletePermitted())
                return;
            onDelete && onDelete(e);
        };
        const handleClose = () => {
            onClose && onClose();
        };
        root.on(ModalEvents.save, handleSave);
        root.on(ModalEvents.hidden, handleClose);
        return () => {
            root.off(ModalEvents.save, handleSave);
            root.off(ModalEvents.hidden, handleClose);
        };
    });
    // Update visibility.
    (0,react.useEffect)(() => {
        if (!modalRef.current)
            return;
        if (show) {
            modalRef.current.show();
        }
        else {
            modalRef.current.hide();
        }
    }, [show, modalRef.current]);
    // Update title.
    (0,react.useEffect)(() => {
        if (!modalRef.current || !title)
            return;
        modalRef.current.setTitle(title);
    }, [title, modalRef.current]);
    // Update button.
    (0,react.useEffect)(() => {
        if (!modalRef.current || !deleteStr)
            return;
        const btn = getDeleteButton();
        if (!btn)
            return;
        btn.textContent = deleteStr;
    }, [deleteStr, modalRef.current]);
    return modalRef.current ? react_dom.createPortal(children, modalRef.current.getBody()[0].querySelector(".block_xp")) : null;
};
const ModalForm = ({ formClass, formArgs, onClose, onSubmit, title }) => {
    const modalFormRef = (0,react.useRef)();
    const { getModule } = useModules(["core_form/modalform", "core/modal_factory", "core/modal_events"]);
    // Create the modal form.
    (0,react.useEffect)(() => {
        if (modalFormRef.current)
            return;
        const ModalForm = getModule("core_form/modalform");
        if (!ModalForm)
            return;
        modalFormRef.current = new ModalForm({
            formClass: formClass,
            args: formArgs ?? {},
            modalConfig: {
                title,
            },
        });
        modalFormRef.current.show();
    });
    // Attach event listeners.
    (0,react.useEffect)(() => {
        const modalForm = modalFormRef.current;
        if (!modalForm)
            return;
        const ModalForm = getModule("core_form/modalform");
        const ModalEvents = getModule("core/modal_events");
        if (!ModalForm || !ModalEvents)
            return;
        const handleLoaded = () => {
            const root = modalForm.modal.getRoot();
            root[0].classList.add("block_xp");
            // Register the onClose event.
            root.on(ModalEvents.hidden, handleClose);
        };
        const handleSubmit = () => {
            onSubmit && onSubmit();
        };
        const handleClose = () => {
            onClose && onClose();
        };
        modalForm.addEventListener(modalForm.events.LOADED, handleLoaded);
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, handleSubmit);
        modalForm.addEventListener(modalForm.events.CANCEL_BUTTON_PRESSED, handleClose);
        return () => {
            const modalForm = modalFormRef.current;
            const root = modalForm?.modal?.getRoot();
            const rootEl = root?.[0];
            rootEl?.removeEventListener(modalForm.events.LOADED, handleLoaded);
            rootEl?.removeEventListener(modalForm.events.FORM_SUBMITTED, handleSubmit);
            rootEl?.removeEventListener(modalForm.events.CANCEL_BUTTON_PRESSED, handleClose);
            root.off(ModalEvents.hidden, handleClose);
        };
    });
    (0,react.useEffect)(() => {
        if (!modalFormRef.current)
            return;
        const modal = modalFormRef.current.modal;
        if (!modal)
            return;
        modal.setTitle(title);
    }, [title]);
    return null;
};

;// CONCATENATED MODULE: ./ui/src/components/Notification.tsx

const NotificationError = ({ children }) => {
    return react.createElement("div", { className: "alert alert-danger" }, children);
};

;// CONCATENATED MODULE: ./ui/src/components/Pix.tsx


const Pix_Pix = ({ id, component = 'block_xp', className, alt = '' }) => {
    return React.createElement("img", { src: imageUrl(id, component), alt: alt, className: className });
};
/* harmony default export */ const components_Pix = ((/* unused pure expression or super */ null && (Pix_Pix)));

;// CONCATENATED MODULE: ./ui/src/components/Spinner.tsx



const Spinner_Spinner = ({ className }) => {
    const alt = useString('loadinghelp', 'core');
    return React.createElement(Pix, { id: "y/loading", component: "core", className: className, alt: alt });
};
/* harmony default export */ const components_Spinner = ((/* unused pure expression or super */ null && (Spinner_Spinner)));

;// CONCATENATED MODULE: ./ui/src/components/Str.tsx


const Str_Str = ({ id, component = "block_xp", a }) => {
    const str = hooks_useString(id, component, a);
    return react.createElement(react.Fragment, null, str || "​");
};
/* harmony default export */ const components_Str = (Str_Str);

;// CONCATENATED MODULE: ./ui/src/components/Button.tsx






const CircleButton = ({ className, ...props }) => {
    return (react.createElement("button", { className: utils_classNames("xp-bg-transparent xp-border-0 xp-p-2 xp-flex xp-items-center xp-rounded-full hover:xp-bg-gray-100", className), type: "button", ...props }));
};
const Button = ({ onClick, disabled, children, primary, className, type = "button" }) => {
    const classes = utils_classNames("btn", primary ? "btn-primary" : "btn-default btn-secondary", className);
    return (react.createElement("button", { className: classes, onClick: onClick, disabled: disabled, type: type }, children));
};
const SaveButton = ({ onClick, disabled, label, mutation = {}, statePosition = "after" }) => {
    const getStr = useStrings(["changessaved", "error"], "core");
    const { isLoading, isSuccess, isError } = mutation;
    const isStateBefore = statePosition === "before";
    const state = (React.createElement("div", { className: `xp-w-8 xp-flex ${isStateBefore ? "xp-mr-4 xp-justify-end" : "xp-ml-4"}`, "aria-live": "assertive" },
        isLoading ? React.createElement(Spinner, null) : null,
        isSuccess ? React.createElement(Pix, { id: "i/valid", component: "core", alt: getStr("changessaved") }) : null,
        isError ? React.createElement(Pix, { id: "i/invalid", component: "core", alt: getStr("error") }) : null));
    return (React.createElement("div", { className: "xp-flex xp-items-center" },
        isStateBefore ? state : null,
        React.createElement("div", { className: "" },
            React.createElement(Button, { primary: true, onClick: onClick, disabled: disabled || isLoading }, label || React.createElement(Str, { id: "savechanges", component: "core" }))),
        !isStateBefore ? state : null));
};
const AnchorButton = ({ children, onClick, className, ...props }) => {
    const anchorButtonProps = useAnchorButtonProps(onClick);
    return (React.createElement("a", { className: classNames("xp-text-inherit xp-no-underline", className), ...props, ...anchorButtonProps }, children));
};

;// CONCATENATED MODULE: ./ui/src/components/Input.tsx

const Input = ({ className = '', ...props }) => {
    /** Apply those classes for normalised styling across themes and versions. */
    return react.createElement("input", { ...props, className: `xp-m-0 form-control ${className}` });
};
const Select = ({ className = '', ...props }) => {
    /** Apply those classes for normalised styling across themes and versions. */
    return react.createElement("select", { ...props, className: `xp-m-0 xp-max-w-auto form-control ${className}` });
};
const Textarea = ({ className = '', ...props }) => {
    /** Apply those classes for normalised styling across themes and versions. */
    return React.createElement("textarea", { ...props, className: `xp-m-0 form-control ${className}` });
};
/* harmony default export */ const components_Input = (Input);

;// CONCATENATED MODULE: ./ui/src/components/NumberInput.tsx




const NumInput = ({ className, value, onChange, selectOnFocus, ...props }) => {
    const inputProps = hooks_useNumericInputProps(value, onChange);
    const handleFocus = (e) => {
        if (!selectOnFocus)
            return;
        e.currentTarget.select();
    };
    return react.createElement(components_Input, { type: "text", ...inputProps, className: className, onFocus: handleFocus, ...props });
};
const PlainNumberInput = ({ value, onChange, selectOnFocus, ...props }) => {
    const inputProps = useNumericInputProps(value, onChange);
    const handleFocus = (e) => {
        if (!selectOnFocus)
            return;
        e.currentTarget.select();
    };
    return React.createElement("input", { type: "text", ...inputProps, onFocus: handleFocus, ...props });
};
const NumberInputWithButtons = ({ onChange, value, min, max, suffix, step = 1, inputProps }) => {
    const hasMin = typeof min !== "undefined";
    const hasMax = typeof max !== "undefined";
    const minDisabled = hasMin && min >= value;
    const maxDisabled = hasMax && max <= value;
    const minusProps = hooks_useAnchorButtonProps(() => {
        if (minDisabled)
            return;
        handleChange(value - step);
    });
    const plusProps = hooks_useAnchorButtonProps(() => {
        if (maxDisabled)
            return;
        handleChange(value + step);
    });
    const handleChange = (n) => {
        let final = n;
        if (hasMin) {
            final = Math.max(min, final);
        }
        if (hasMax) {
            final = Math.min(max, final);
        }
        onChange(final);
    };
    const { className: inputClassName, ...remainingInputProps } = inputProps ?? {};
    const allInputProps = {
        className: utils_classNames("xp-h-auto xp-border-0 xp-text-center xp-rounded-none focus:xp-z-10", suffix ? "xp-pr-6" : null, inputClassName || "xp-w-16"),
        ...remainingInputProps,
    };
    return (react.createElement("div", { className: "xp-inline-flex xp-rounded xp-border xp-border-solid xp-border-gray-300" },
        react.createElement("a", { ...minusProps, className: utils_classNames("xp-flex-0 xp-border-0 xp-border-gray-300 xp-border-solid xp-border-r xp-rounded-l xp-py-0.5 xp-px-1 xp-flex xp-items-center xp-justify-center", "focus:xp-z-10", minDisabled ? "xp-bg-gray-100 xp-cursor-pointer xp-text-gray-500" : "xp-bg-white xp-text-inherit") },
            react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 20 20", fill: "currentColor", className: "xp-w-5 xp-h-5" },
                react.createElement("path", { fillRule: "evenodd", d: "M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z", clipRule: "evenodd" }))),
        react.createElement("div", { className: "xp-flex-1 xp-relative" },
            react.createElement(NumInput, { onChange: handleChange, value: value, ...allInputProps }),
            suffix ? (react.createElement("div", { className: "xp-pointer-events-none xp-absolute xp-inset-y-0 xp-right-0 xp-flex xp-items-center xp-pr-2" },
                react.createElement("span", { className: "xp-text-gray-500" }, suffix))) : null),
        react.createElement("a", { ...plusProps, className: utils_classNames("xp-flex-0 xp-border-0 xp-border-gray-300 xp-border-solid xp-border-l xp-rounded-r xp-py-0.5 xp-px-1 xp-flex xp-items-center xp-justify-center", "focus:xp-z-10", maxDisabled ? "xp-bg-gray-100 xp-cursor-pointer xp-text-gray-500" : "xp-bg-white xp-text-inherit") },
            react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 20 20", fill: "currentColor", className: "xp-w-5 xp-h-5" },
                react.createElement("path", { d: "M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" })))));
};

;// CONCATENATED MODULE: ./ui/src/components/ResourceList.tsx




const ListEntry = ({ resource, onSelect }) => {
    if (resource.type === "header") {
        return react.createElement(ListEntryHeader, { label: resource.label });
    }
    return (react.createElement(ListEntryItem, { label: resource.label, description: resource.description, isavailable: resource.isavailable, onSelect: onSelect }));
};
const ListEntryItem = ({ label, description, isavailable = true, onSelect }) => {
    const headingId = useUniqueId();
    const buttonListeners = useRoleButtonListeners(onSelect);
    const disabledOpacityClass = `${!isavailable ? "xp-opacity-60 group-focus:xp-opacity-100 group-hover:xp-opacity-100" : ""}`;
    return (react.createElement("div", { className: "xp-p-[0.2rem] xp-relative xp-group focus:xp-z-10 hover:xp-bg-gray-100" },
        react.createElement("div", { tabIndex: 0, role: "button", "aria-describedby": headingId, className: "xp-px-1.5 xp-py-0.5", ...buttonListeners },
            react.createElement("div", { id: headingId, className: `xp-flex` },
                react.createElement("div", { className: utils_classNames(disabledOpacityClass, "xp-text-medium", description ? "xp-text-xl" : "xp-text-base") }, label),
                !isavailable ? (react.createElement("div", { className: "xp-ml-2" },
                    react.createElement("span", { className: "badge badge-pill badge-warning" },
                        react.createElement(components_Str, { id: "unavailable" })))) : null),
            description ? (react.createElement("div", { className: utils_classNames(disabledOpacityClass, "xp-text-gray-500"), dangerouslySetInnerHTML: { __html: description } })) : null)));
};
const ListEntryHeader = ({ label }) => {
    return (react.createElement("div", { className: "xp-px-[0.2rem] xp-bg-gray-200 xp-mt-2 first:xp-mt-0 xp-sticky xp-top-0 xp-z-10" },
        react.createElement("div", { className: "xp-px-1.5 xp-py-1 xp-text-sm xp-leading-tight xp-font-bold" }, label)));
};
const PlainResourceList = ({ resources, onSelect, emptyContent, }) => {
    if (!resources.length) {
        return react.createElement(react.Fragment, null, emptyContent || react.createElement(EmptyResult, null));
    }
    return (react.createElement("div", { className: "xp-flex-1 xp-divide-y xp-divide-gray-200" }, resources.map((o) => {
        return react.createElement(ListEntry, { key: `${o.type || ""}${o.name}`, resource: o, onSelect: () => onSelect && onSelect(o) });
    })));
};
const LoadingResourceList = () => {
    return (react.createElement("div", { className: "xp-flex-1" },
        react.createElement("div", { className: "xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2" }),
        react.createElement("div", { className: "xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2" }),
        react.createElement("div", { className: "xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2" }),
        react.createElement("div", { className: "xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2" }),
        react.createElement("div", { className: "xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2" })));
};
const EmptyResult = ({ message, content }) => {
    return (react.createElement("div", { className: "xp-flex-1 xp-flex xp-flex-col xp-items-center xp-justify-center xp-text-center" },
        react.createElement("div", null, message || react.createElement(components_Str, { id: "noneareavailable" })),
        content ? react.createElement("div", { className: "xp-my-2" }, content) : null));
};

;// CONCATENATED MODULE: ./ui/src/components/Icons.tsx

const Bars3BottomLeftIcon = ({ className }) => (React.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    React.createElement("path", { fillRule: "evenodd", d: "M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75H12a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z", clipRule: "evenodd" })));
const CheckBadgeIconSolid = ({ className }) => (React.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    React.createElement("path", { fillRule: "evenodd", d: "M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z", clipRule: "evenodd" })));
const LanguageIcon = ({ className }) => (React.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    React.createElement("path", { fillRule: "evenodd", d: "M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z", clipRule: "evenodd" })));
const PaperAirplaneIconSolid = ({ className }) => (React.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    React.createElement("path", { d: "M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" })));
const ChevronLeftIconSolid = ({ className }) => (react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", fill: "none", viewBox: "0 0 24 24", strokeWidth: 1.5, stroke: "currentColor", className: className },
    react.createElement("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M15.75 19.5 8.25 12l7.5-7.5" })));

;// CONCATENATED MODULE: ./ui/src/components/Slider.tsx






const slideClasses = "xp-absolute xp-inset-0 xp-transform-gpu xp-transition-transform xp-duration-300";
const slideNextClasses = `${slideClasses} xp-translate-x-full`;
const slidePrevClasses = `${slideClasses} xp--translate-x-full`;
const Slider = ({ children: rawChildren, index }) => {
    const [internalIndex, setInternalIndex] = (0,react.useState)(index);
    const slidesRef = (0,react.useRef)([]);
    const children = react.Children.toArray(rawChildren).filter(Boolean);
    const nSlides = children.length;
    // When the number of slides changes.
    (0,react.useEffect)(() => {
        slidesRef.current = slidesRef.current.slice(0, nSlides);
    }, [nSlides]);
    // Effects when the current slide changes.
    (0,react.useEffect)(() => {
        const Aria = getModule("core/aria");
        slidesRef.current.forEach((item, i) => {
            if (i === internalIndex) {
                Aria.unhide(item);
                item?.focus();
            }
            else {
                Aria.hide(item);
            }
        });
    }, [internalIndex]);
    // When the index changes, update the local one. We do this to let a child render the slide that
    // we should transition to before we update the internal index that would render the child instantly.
    // This allows for the number of slides to be dynamically created by the parent.
    (0,react.useEffect)(() => {
        setInternalIndex(index);
    }, [index]);
    return (react.createElement("div", { className: "xp-w-full xp-h-full xp-overflow-hidden xp-relative" }, react.Children.map(children, (child, i) => {
        const isActive = i === internalIndex;
        const isPast = i < internalIndex;
        return (react.createElement("div", { ref: (el) => (slidesRef.current[i] = el), className: isActive ? slideClasses : isPast ? slidePrevClasses : slideNextClasses }, child));
    })));
};
const SliderTester = () => {
    const [index, setIndex] = useState(0);
    return (React.createElement("div", null,
        React.createElement("div", { className: "xp-h-[500px] xp-w-full" },
            React.createElement(Slider, { index: index },
                React.createElement(Slide, null,
                    "Slide 1",
                    React.createElement("div", { className: "xp-w-4", style: { height: "1000px" } })),
                React.createElement(Slide, null,
                    "Slide 2",
                    React.createElement("div", { className: "xp-w-4", style: { height: "100px" } })),
                React.createElement(Slide, null,
                    "Slide 3",
                    React.createElement("div", { className: "xp-w-4", style: { height: "2000px" } })),
                React.createElement(Slide, null,
                    "Slide 4",
                    React.createElement("div", { className: "xp-w-4", style: { height: "500px" } })),
                React.createElement(Slide, null,
                    "Slide 5",
                    React.createElement("div", { className: "xp-w-4", style: { height: "1500px" } })))),
        React.createElement("button", { onClick: () => setIndex((i) => Math.max(0, Math.min(5 - 1, i - 1))) }, "Prev"),
        React.createElement("button", { onClick: () => setIndex((i) => Math.max(0, Math.min(5 - 1, i + 1))) }, "Next")));
};
const Slide = ({ children, header, footer, }) => {
    /* Firefox requires the vertical scroll to be in the child element, else something odd happens. */
    return (react.createElement("div", { className: "xp-w-full xp-h-full xp-flex xp-flex-col" },
        header,
        react.createElement("div", { className: "xp-flex xp-flex-col xp-grow xp-overflow-y-auto" }, children),
        footer));
};
const SlideHeader = ({ children, title, hasBack, onBack, }) => {
    return (react.createElement("div", { className: "xp-mb-2" },
        react.createElement("div", { className: "xp-flex xp-flex-row xp-items-center xp-gap-4" },
            hasBack ? (react.createElement("div", { className: "shrink-0 xp-grow-0" },
                react.createElement(CircleButton, { onClick: onBack, type: "button", className: "xp--mr-2" },
                    react.createElement(ChevronLeftIconSolid, { className: "xp-h-6 xp-w-6" }),
                    react.createElement("span", { className: "xp-sr-only" },
                        react.createElement(components_Str, { id: "back", component: "core" }))))) : null,
            react.createElement("div", { className: "xp-flex-1 xp-text-lg xp-font-bold" }, title)),
        children));
};
const SlideHeaderWithFilter = ({ hasBack, onBack, onFilterChange, filterValue, filterPlaceholder, title, }) => {
    const filterStr = hooks_useString("filterellipsis");
    const handleChange = (0,react.useCallback)((e) => {
        onFilterChange && onFilterChange(e.currentTarget.value || "");
    }, [onFilterChange]);
    return (react.createElement(SlideHeader, { hasBack: hasBack, onBack: onBack, title: title },
        react.createElement("div", { className: "xp-mt-0.5" },
            react.createElement("input", { className: "form-control xp-w-full", type: "text", value: filterValue || "", placeholder: filterPlaceholder || filterStr, onChange: handleChange }))));
};

;// CONCATENATED MODULE: ./ui/src/components/RuleWizard.tsx











const CmResourceList = ({ courseId, filterTerm, onSelect, resetFilterTerm, options = {} }) => {
    const query = (0,es.useQuery)(["cm-resource-list", courseId, options], async () => {
        const Ajax = await getModuleAsync("core/ajax");
        return (await Ajax.call([
            {
                methodname: "block_xp_search_modules",
                args: { courseid: courseId, query: "*", options },
            },
        ])[0]);
    });
    const resources = (0,react.useMemo)(() => {
        const normalisedFilterTerm = (filterTerm || "").trim().toLowerCase();
        const data = query.data || [];
        return data.reduce((carry, section, idx) => {
            const modules = normalisedFilterTerm === ""
                ? section.modules
                : section.modules.filter((module) => {
                    return module.name.includes(normalisedFilterTerm);
                });
            if (!modules.length) {
                return carry;
            }
            // Only show headers if we have multiple sections.
            if (data.length > 1) {
                carry.push({ name: idx, label: section.name, type: "header" });
            }
            modules.forEach((module) => {
                carry.push({ name: module.cmid, label: module.name });
            });
            return carry;
        }, []);
    }, [query.data, filterTerm]);
    if (!query.isSuccess || query.isLoading) {
        return react.createElement(LoadingResourceList, null);
    }
    return (react.createElement(PlainResourceList, { resources: resources, onSelect: (r) => onSelect(r.name), emptyContent: react.createElement(EmptyResult, { message: react.createElement(components_Str, { id: "nothingmatchesfilter" }), content: resetFilterTerm ? (react.createElement(Button, { onClick: resetFilterTerm },
                react.createElement(components_Str, { id: "clearfilter" }))) : null }) }));
};
const CmResourceListSlide = ({ courseId, onSelect, hasBack, onBack, cmListOptions, }) => {
    const [filterTerm, setFilterTerm] = (0,react.useState)("");
    return (react.createElement(Slide, { header: react.createElement(SlideHeaderWithFilter, { filterValue: filterTerm, onFilterChange: setFilterTerm, hasBack: hasBack, onBack: onBack, title: react.createElement(components_Str, { id: "rulefiltercm" }) }) },
        react.createElement(CmResourceList, { options: cmListOptions, courseId: courseId, onSelect: onSelect, filterTerm: filterTerm, resetFilterTerm: () => setFilterTerm("") })));
};
const SectionResourceList = ({ courseId, onSelect, options = {} }) => {
    const query = (0,es.useQuery)(["section-resource-list", courseId, options], async () => ajaxRequest("block_xp_get_sections", { courseid: courseId, options }));
    const resources = (0,react.useMemo)(() => {
        const data = query.data || [];
        return data.reduce((carry, section, idx) => {
            carry.push({ name: section.number, label: section.name });
            return carry;
        }, []);
    }, [query.data]);
    if (!query.isSuccess || query.isLoading) {
        return react.createElement(LoadingResourceList, null);
    }
    return (react.createElement(PlainResourceList, { resources: resources, onSelect: (r) => onSelect(r.name), emptyContent: react.createElement(EmptyResult, { message: react.createElement(components_Str, { id: "nothingmatchesfilter" }) }) }));
};
const CmNameSlide = ({ onBack, config, setConfig, }) => {
    const defaultValue = 1;
    const getStr = hooks_useStrings(["rule:eq", "rule:contains", "rulefiltercmname"]);
    return (react.createElement(Slide, { header: react.createElement(SlideHeader, { hasBack: true, onBack: onBack, title: getStr("rulefiltercmname") }) },
        react.createElement("div", { className: "xp-mb-4" },
            react.createElement("label", { htmlFor: "xp-rule-cmname-name", className: "xp-m-0" },
                react.createElement(components_Str, { id: "activityname" })),
            react.createElement("div", { className: "xp-flex xp-gap-2" },
                react.createElement(Select, { value: config.filterint1, onChange: (e) => setConfig({ filterint1: parseInt(e.currentTarget.value, 10) || 0 }), defaultValue: defaultValue.toString(), className: "xp-w-auto" },
                    react.createElement("option", { value: "1" }, getStr("rule:contains")),
                    react.createElement("option", { value: "0" }, getStr("rule:eq"))),
                react.createElement(components_Input, { id: "xp-rule-cmname-name", value: config.filterchar1 || "", onChange: (e) => setConfig({ filterchar1: e.currentTarget.value, filterint1: config.filterint1 ?? defaultValue }), maxLength: 255 })),
            react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" },
                react.createElement(components_Str, { id: "activityname_help" }))),
        react.createElement(PointsToAwardInput, { config: config, setConfig: setConfig })));
};
const anyFilterMethodStuff = {
    getSlide: () => null,
    hasSlide: false,
    isConfigValid: () => true,
    isSlideRequiringSubmit: false,
};
const filterMethoStuff = {
    cm: {
        getSlide: (props) => (react.createElement(CmResourceListSlide, { cmListOptions: {
                completionenabled: props.method.scopeoptions?.completionenabled,
                type: props.method.scopeoptions?.type,
            }, hasBack: props.hasBack, onBack: props.onBack, courseId: props.courseId, onSelect: (cmid) => {
                props.setConfig({ filtercmid: cmid });
                props.onContinue();
            } })),
        hasSlide: true,
        isConfigValid: () => true,
        isSlideRequiringSubmit: false,
    },
    cmname: {
        getSlide: (props) => react.createElement(CmNameSlide, { onBack: props.onBack, config: props.config, setConfig: props.setConfig }),
        hasSlide: true,
        isConfigValid: (config) => [0, 1].includes(config?.filterint1) &&
            typeof config.filterchar1 === "string" &&
            config.filterchar1.trim() !== "" &&
            typeof config?.points === "number" &&
            !isNaN(config.points),
        isSlideRequiringSubmit: true,
        collectsPoints: true,
    },
    section: {
        getSlide: (props) => (react.createElement(Slide, { header: react.createElement(SlideHeader, { hasBack: props.hasBack, onBack: props.onBack, title: react.createElement(components_Str, { id: "rulefiltersection" }) }) },
            react.createElement(SectionResourceList, { courseId: props.courseId, onSelect: (num) => {
                    props.setConfig({ filterint1: num });
                    props.onContinue();
                } }))),
        hasSlide: true,
        isConfigValid: () => true,
        isSlideRequiringSubmit: false,
    },
    any: anyFilterMethodStuff,
    anycm: anyFilterMethodStuff,
    anycourse: anyFilterMethodStuff,
    anysection: anyFilterMethodStuff,
    thiscourse: anyFilterMethodStuff,
};
const defaultConfig = { points: 10 };
const RuleWizardModal = (props) => {
    const getStr = hooks_useStrings(["addacondition"]);
    const getCoreStr = hooks_useStrings(["continue", "save"], "core");
    const [selectedMethod, setSelectedMethod] = (0,react.useState)(null);
    const [config, setConfig] = (0,react.useState)(defaultConfig);
    const [index, setIndex] = (0,react.useState)(0);
    const handleSelected = (method) => {
        if (!(method in filterMethoStuff)) {
            return;
        }
        setSelectedMethod(method);
        setIndex(1);
    };
    const handleIndexChange = (rawIndex) => {
        const newIndex = Math.max(0, rawIndex);
        if (newIndex === 0) {
            setConfig(defaultConfig);
        }
        setIndex(newIndex);
    };
    const handleAddToConfig = (data) => {
        setConfig({ ...config, ...data });
    };
    const handleBack = () => {
        handleIndexChange(index - 1);
    };
    const resetState = (0,react.useCallback)(() => {
        setSelectedMethod(null);
        setConfig(defaultConfig);
        setIndex(0);
    }, []);
    (0,react.useEffect)(() => {
        // Reset the state when the modal is closed/hidden.
        if (!props.show) {
            resetState();
        }
    }, [props.show]);
    const methodStuff = selectedMethod ? filterMethoStuff[selectedMethod] ?? null : null;
    const isLastStep = index === (methodStuff?.hasSlide && !methodStuff?.collectsPoints ? 2 : 1);
    const isStepContinue = !isLastStep;
    const isStepValid = methodStuff && index === 1 ? methodStuff?.isConfigValid(config) : true;
    const isStepRequiringButton = Boolean(methodStuff?.isSlideRequiringSubmit);
    const canClickSaveButton = (isLastStep || isStepRequiringButton) && isStepValid;
    const handleSave = (e) => {
        e.preventDefault();
        if (!canClickSaveButton) {
            return;
        }
        if (isStepContinue) {
            handleIndexChange(index + 1);
            return;
        }
        if (!selectedMethod) {
            return;
        }
        props.onSave({ filter: selectedMethod, config });
    };
    const handleClose = () => {
        props.onClose();
    };
    const methodSlide = selectedMethod
        ? methodStuff?.getSlide({
            setConfig: handleAddToConfig,
            config,
            hasBack: index === 1,
            onBack: handleBack,
            onContinue: () => handleIndexChange(index + 1),
            method: props.method,
            courseId: props.courseid,
        })
        : null;
    const sortedFilters = (0,react.useMemo)(() => {
        return props.filters.sort((a, b) => {
            const wa = a.weight ?? null;
            const wb = b.weight ?? null;
            if (wa === wb)
                return 0;
            if (wa === null || wb === null)
                return 1; // Always show null last.
            if (wa === 0)
                return -1; // Always show the 0 weight first.
            return wb - wa; // Descending order.
        });
    }, [props.filters]);
    return (react.createElement(SaveCancelModal, { show: props.show, large: true, defaultHeight: 500, canSave: canClickSaveButton, onSave: handleSave, onClose: handleClose, saveButtonText: isStepContinue ? getCoreStr("continue") : getCoreStr("save"), title: getStr("addacondition") },
        react.createElement(Slider, { index: index },
            react.createElement(Slide, null,
                react.createElement(PlainResourceList, { onSelect: (r) => handleSelected(r.name), resources: sortedFilters })),
            selectedMethod && methodSlide ? methodSlide : null,
            selectedMethod && !methodStuff?.collectsPoints ? (react.createElement(Slide, { header: react.createElement(SlideHeader, { hasBack: true, onBack: handleBack, title: methodStuff?.hasSlide ? react.createElement(components_Str, { id: "pointstoaward" }) : react.createElement(components_Str, { id: `rulefilter${selectedMethod}` }) }) },
                react.createElement("div", { className: "" },
                    react.createElement(PointsToAwardInput, { config: config, setConfig: setConfig })))) : null)));
};
const PointsToAwardInput = ({ setConfig, config, }) => {
    return (react.createElement("div", null,
        react.createElement("label", { htmlFor: "xp-rule-pointstoaward", className: "xp-m-0" },
            react.createElement(components_Str, { id: "pointstoaward" })),
        react.createElement("div", null,
            react.createElement(NumberInputWithButtons, { value: config.points, onChange: (points) => setConfig({ ...config, points }), min: 0, max: 9999999, inputProps: { id: "xp-rule-pointstoaward", className: "xp-w-24", selectOnFocus: true } })),
        react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" },
            react.createElement(components_Str, { id: "pointstoaward_help" }))));
};

// EXTERNAL MODULE: ./node_modules/react-query/es/core/queryClient.js + 4 modules
var queryClient = __webpack_require__(98);
;// CONCATENATED MODULE: ./ui/src/lib/query.ts


const query_queryClient = new queryClient/* QueryClient */.E({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60,
            onError: (err) => getModule("core/notification").exception(err),
        },
        mutations: {
            onError: (err) => getModule("core/notification").exception(err),
        },
    },
});

;// CONCATENATED MODULE: ./ui/src/lib/types.ts
var ContextLevel;
(function (ContextLevel) {
    ContextLevel[ContextLevel["System"] = 10] = "System";
    ContextLevel[ContextLevel["User"] = 30] = "User";
    ContextLevel[ContextLevel["CourseCategory"] = 40] = "CourseCategory";
    ContextLevel[ContextLevel["Course"] = 50] = "Course";
    ContextLevel[ContextLevel["Module"] = 70] = "Module";
})(ContextLevel || (ContextLevel = {}));
;
;

;// CONCATENATED MODULE: ./ui/src/completion-rules.tsx
















const useRules = (contextid, types, childcontextid) => {
    return (0,es.useQuery)(["rules", contextid, types, childcontextid], async () => {
        const data = await ajaxRequest("block_xp_get_rules", {
            contextid,
            types,
            childcontextid,
        });
        return data.map((data) => ({
            ...data,
            method: data.typename,
            filter: data.filtername,
        }));
    });
};
const useAddRuleMutation = (contextid, childcontextid, { onSuccess }) => {
    return (0,es.useMutation)(async ({ method, filter, config }) => {
        return ajaxRequest("block_xp_create_rule", {
            contextid,
            childcontextid: childcontextid ?? 0,
            points: config.points ?? 0,
            type: {
                name: method,
                char1: config.typechar1 ?? null,
            },
            filter: {
                name: filter,
                courseid: config.filtercourseid ?? null,
                cmid: config.filtercmid ?? null,
                int1: config.filterint1 ?? null,
                char1: config.filterchar1 ?? null,
            },
        });
    }, {
        onSuccess,
    });
};
const useDeleteRuleMutation = () => {
    return (0,es.useMutation)(async ({ id }) => {
        return ajaxRequest("block_xp_delete_rule", { id });
    });
};
const AppContext = react.createContext({
    rules: [],
    addRule: () => { },
    editRule: (id) => { },
    removeRule: (id) => { },
});
const availableRuleTypes = ["cm_completion", "section_completion", "course_completion"];
const guessMethodFromLocation = () => {
    return Math.max(0, availableRuleTypes.indexOf((window.location.hash ?? "").replace(/^#/, "")));
};
const updateLocationFromMethodIndex = (idx) => {
    const hash = "#" + availableRuleTypes[idx];
    window.location.hash = hash;
};
const App = (props) => {
    const queryClient = (0,es.useQueryClient)();
    const [selectedTabIndex, setSelectedTabIndex] = (0,react.useState)(guessMethodFromLocation);
    const [optimisticallyDeleted, setOptimisticallyDeleted] = (0,react.useState)([]);
    const childcontextid = props.childcontext?.id ?? null;
    const currentCourseId = props.childcontext?.contextlevel === ContextLevel.Course ? props.childcontext.instanceid : props.world.courseid;
    const [isAdding, setIsAdding] = (0,react.useState)(false);
    const [isEditing, setIsEditing] = (0,react.useState)(null);
    const [isDeleting, setIsDeleting] = (0,react.useState)(null);
    const getStr = hooks_useStrings(["deletecondition", "editcondition", "ruleadded"]);
    const rulesQuery = useRules(props.world.contextid, availableRuleTypes, childcontextid);
    const addRuleMutation = useAddRuleMutation(props.world.contextid, childcontextid, {
        onSuccess: () => {
            invalidateCurrentQuery();
            setIsAdding(false);
            const Toast = getModule("core/toast");
            Toast && Toast.add(getStr("ruleadded"));
        },
    });
    const deleteRuleMutation = useDeleteRuleMutation();
    const handleSelectedTabIndexChange = (idx) => {
        setSelectedTabIndex(idx);
        setIsAdding(false);
        setIsEditing(null);
        setIsDeleting(null);
        updateLocationFromMethodIndex(idx);
    };
    const currentRuleType = availableRuleTypes[selectedTabIndex];
    const rules = (0,react.useMemo)(() => {
        return (rulesQuery.data || []).filter((r) => !optimisticallyDeleted.includes(r.id));
    }, [rulesQuery.data, optimisticallyDeleted]);
    const invalidateCurrentQuery = (0,react.useCallback)(() => {
        queryClient.invalidateQueries(["rules", props.world.contextid, availableRuleTypes, childcontextid]);
    }, [queryClient, props]);
    const ruleTypesByName = (0,react.useMemo)(() => {
        return props.ruletypes.reduce((acc, ruletype) => {
            acc[ruletype.name] = ruletype;
            return acc;
        }, {});
    }, [props.ruletypes]);
    const groupedRules = (0,react.useMemo)(() => {
        return rules.reduce((acc, rule) => {
            if (!acc[rule.method]) {
                acc[rule.method] = [];
            }
            acc[rule.method].push(rule);
            return acc;
        }, {});
    }, [rules]);
    const currentMethodFilters = (0,react.useMemo)(() => {
        if (!ruleTypesByName[currentRuleType]?.filters) {
            return [];
        }
        return props.rulefilters
            .filter((filter) => ruleTypesByName[currentRuleType].filters.includes(filter.name))
            .filter((filter) => filter.ismultipleallowed || !groupedRules[currentRuleType]?.some((rule) => rule.filter === filter.name));
    }, [currentRuleType, props.rulefilters, groupedRules]);
    const canAdd = true;
    const showAddBtnInTabs = canAdd && groupedRules[currentRuleType]?.length;
    if (rulesQuery.isLoading || rulesQuery.isError) {
        return react.createElement(AppLoading, null);
    }
    return (react.createElement(AppContext.Provider, { value: {
            rules,
            addRule: () => setIsAdding(true),
            editRule: (ruleId) => setIsEditing(ruleId),
            removeRule: (ruleId) => setIsDeleting(ruleId),
        } },
        react.createElement("div", null,
            react.createElement(tabs/* Tab */.o.Group, { selectedIndex: selectedTabIndex, onChange: handleSelectedTabIndexChange },
                react.createElement(tabs/* Tab */.o.List, { as: "div", className: "nav nav-tabs" },
                    react.createElement(tabs/* Tab */.o, { as: react.Fragment }, ({ selected }) => (react.createElement("button", { className: utils_classNames("nav-item nav-link", selected ? "active" : null) },
                        react.createElement(components_Str, { id: "activity", component: "core" })))),
                    react.createElement(tabs/* Tab */.o, { as: react.Fragment }, ({ selected }) => (react.createElement("button", { className: utils_classNames("nav-item nav-link", selected ? "active" : null) },
                        react.createElement(components_Str, { id: "section", component: "core" })))),
                    react.createElement(tabs/* Tab */.o, { as: react.Fragment }, ({ selected }) => (react.createElement("button", { className: utils_classNames("nav-item nav-link", selected ? "active" : null) },
                        react.createElement(components_Str, { id: "course", component: "core" })))),
                    react.createElement("div", { className: "xp-flex-1 xp-flex xp-justify-end xp-items-center" }, showAddBtnInTabs ? (react.createElement("button", { className: "btn btn-primary btn-sm", onClick: () => setIsAdding(true) },
                        react.createElement(components_Str, { id: "add", component: "core" }))) : null)),
                react.createElement(tabs/* Tab */.o.Panels, { className: "xp-mt-4" },
                    react.createElement(tabs/* Tab */.o.Panel, null, "cm_completion" in ruleTypesByName ? (react.createElement(CompletionRules, { rules: groupedRules.cm_completion, type: ruleTypesByName["cm_completion"], filters: props.rulefilters })) : (react.createElement(NotificationError, null,
                        react.createElement(components_Str, { id: "unknowntypea", a: "cm_completion" })))),
                    react.createElement(tabs/* Tab */.o.Panel, null, "section_completion" in ruleTypesByName ? (react.createElement(CompletionRules, { rules: groupedRules.section_completion, type: ruleTypesByName["section_completion"], filters: props.rulefilters })) : (react.createElement(NotificationError, null,
                        react.createElement(components_Str, { id: "unknowntype", a: "section_completion" })))),
                    react.createElement(tabs/* Tab */.o.Panel, null, "course_completion" in ruleTypesByName ? (react.createElement(CompletionRules, { rules: groupedRules.course_completion, type: ruleTypesByName["course_completion"], filters: props.rulefilters })) : (react.createElement(NotificationError, null,
                        react.createElement(components_Str, { id: "unknowntype", a: "course_completion" }))))))),
        react.createElement(RuleWizardModal, { show: isAdding, courseid: currentCourseId, contextlevel: props.world.contextlevel, method: ruleTypesByName[currentRuleType], filters: currentMethodFilters, onClose: () => setIsAdding(false), onSave: ({ filter, config }) => {
                addRuleMutation.mutate({ method: currentRuleType, filter, config });
            } }),
        react.createElement(DeleteModal, { show: isDeleting !== null, onClose: () => setIsDeleting(null), onDelete: () => {
                if (!isDeleting)
                    return;
                setOptimisticallyDeleted([...optimisticallyDeleted, isDeleting]);
                deleteRuleMutation.mutate({ id: isDeleting }, {
                    onError: () => {
                        setOptimisticallyDeleted(optimisticallyDeleted.filter((id) => id !== isDeleting));
                    },
                    onSuccess: () => {
                        invalidateCurrentQuery();
                    },
                    onSettled: () => {
                        setIsDeleting(null);
                    },
                });
            }, title: getStr("deletecondition") },
            react.createElement(components_Str, { id: "areyousure", component: "core" })),
        isEditing ? (react.createElement(ModalForm, { formClass: "block_xp\\form\\rule", formArgs: { id: isEditing }, title: getStr("editcondition"), onClose: () => setIsEditing(null), onSubmit: () => {
                setIsEditing(null);
                invalidateCurrentQuery();
            } })) : null));
};
const groupRulesByFilter = (rules) => {
    if (!rules)
        return [];
    const filterNames = rules.map((rule) => rule.filter).filter((value, index, self) => self.indexOf(value) === index);
    return filterNames
        .map((filterName) => {
        const rulesForFilter = rules.filter((rule) => rule.filter === filterName);
        return { filter: filterName, rules: rulesForFilter };
    })
        .filter((group) => group.rules.length > 0);
};
const NoRulesZeroState = ({ onClick }) => {
    return (react.createElement("div", { className: "xp-rounded xp-border-dashed xp-border-2 xp-p-4 xp-py-6 xp-text-center xp-border-gray-200" },
        react.createElement("div", { className: "xp-text-xl xp-font-bold xp-mb-4" },
            react.createElement(components_Str, { id: "noconditionsyet" })),
        react.createElement("div", null,
            react.createElement(components_Str, { id: "noconditionsyetintro" })),
        react.createElement("div", { className: "xp-mt-4" },
            react.createElement("button", { className: "btn btn-primary", onClick: onClick },
                react.createElement(components_Str, { id: "add", component: "core" })))));
};
const CompletionRules = ({ rules, type, filters }) => {
    const filteredRules = (0,react.useMemo)(() => rules?.filter((r) => type.filters.includes(r.filter) && filters.find((f) => f.name === r.filter)), [rules, type.filters]);
    const groupedRules = (0,react.useMemo)(() => groupRulesByFilter(filteredRules), [filteredRules]);
    const { addRule, removeRule, editRule } = react.useContext(AppContext);
    const handleAddClick = () => {
        addRule();
    };
    if (!filteredRules?.length) {
        return react.createElement(NoRulesZeroState, { onClick: handleAddClick });
    }
    return (react.createElement("div", { className: "xp-space-y-4" }, groupedRules.map(({ filter, rules }) => {
        const ruleFilter = filters.find((f) => f.name === filter);
        if (!ruleFilter)
            return null;
        return (react.createElement(RulesSection, { key: filter, title: ruleFilter?.label, description: ruleFilter?.description }, rules.map((rule) => {
            return (react.createElement(Rule, { key: rule.id, points: rule.points, label: rule.label, onDelete: () => removeRule(rule.id), onEdit: () => editRule(rule.id) }));
        })));
    })));
};
const RulesSection = ({ children, title, description, }) => {
    return (react.createElement("div", null,
        react.createElement("h5", { className: "xp-font-bold xp-m-0 xp-mb-1 xp-text-base" }, title),
        react.createElement("p", { className: "xp-mb-2 xp-text-sm xp-text-gray-500 xp-m-0" }, description),
        react.createElement("div", { className: "[&>div]:xp-border-0 [&>div]:xp-border-b [&>div]:xp-border-solid [&>div]:xp-border-gray-200" }, children)));
};
const Rule = ({ points, label, onDelete, onEdit, }) => {
    return (react.createElement("div", { className: "" },
        react.createElement("div", { className: "xp-flex xp-gap-2" },
            react.createElement("div", { className: "xp-shrink-0 xp-flex xp-items-center" },
                react.createElement("div", { className: utils_classNames("xp-min-w-[86px] xp-text-center xp-rounded xp-px-2 xp-py-0.5 xp-font-bold xp-tracking-wide", !points ? "xp-bg-gray-200" : "xp-bg-blue-100") }, points !== null ? `${points != 0 ? "+" : ""}${points}` : "-")),
            react.createElement("div", { className: "xp-grow xp-flex xp-items-center" },
                react.createElement("div", { className: "xp-grow" }, label)),
            react.createElement("div", { className: "xp-shrink-0" },
                react.createElement(RuleDropdown, { onDelete: onDelete, onEdit: onEdit })))));
};
const RuleDropdown = ({ onEdit, onDelete }) => {
    const deleteProps = hooks_useAnchorButtonProps(onDelete);
    const editProps = hooks_useAnchorButtonProps(onEdit);
    return (react.createElement(Dropdown, { buttonLabel: react.createElement(components_Str, { id: "options", component: "core" }), items: [
            { id: "edit", label: react.createElement(components_Str, { id: "edit", component: "core" }), props: editProps },
            { id: "delete", label: react.createElement(components_Str, { id: "delete", component: "core" }), props: deleteProps, danger: true },
        ] }));
};
function startApp(node, props) {
    react_dom.render(react.createElement(contexts_AddonContext.Provider, { value: makeAddonContextValueFromAppProps(props) },
        react.createElement(es.QueryClientProvider, { client: query_queryClient },
            react.createElement(App, { ...props }))), node);
}
const dependencies = makeDependenciesDefinition(commonStaticModulesToDependOn);



/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/create fake namespace object */
/******/ 	(() => {
/******/ 		var getProto = Object.getPrototypeOf ? (obj) => (Object.getPrototypeOf(obj)) : (obj) => (obj.__proto__);
/******/ 		var leafPrototypes;
/******/ 		// create a fake namespace object
/******/ 		// mode & 1: value is a module id, require it
/******/ 		// mode & 2: merge all properties of value into the ns
/******/ 		// mode & 4: return value when already ns object
/******/ 		// mode & 16: return value when it's Promise-like
/******/ 		// mode & 8|1: behave like require
/******/ 		__webpack_require__.t = function(value, mode) {
/******/ 			if(mode & 1) value = this(value);
/******/ 			if(mode & 8) return value;
/******/ 			if(typeof value === 'object' && value) {
/******/ 				if((mode & 4) && value.__esModule) return value;
/******/ 				if((mode & 16) && typeof value.then === 'function') return value;
/******/ 			}
/******/ 			var ns = Object.create(null);
/******/ 			__webpack_require__.r(ns);
/******/ 			var def = {};
/******/ 			leafPrototypes = leafPrototypes || [null, getProto({}), getProto([]), getProto(getProto)];
/******/ 			for(var current = mode & 2 && value; typeof current == 'object' && !~leafPrototypes.indexOf(current); current = getProto(current)) {
/******/ 				Object.getOwnPropertyNames(current).forEach((key) => (def[key] = () => (value[key])));
/******/ 			}
/******/ 			def['default'] = () => (value);
/******/ 			__webpack_require__.d(ns, def);
/******/ 			return ns;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/runtimeId */
/******/ 	(() => {
/******/ 		__webpack_require__.j = 834;
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			834: 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkblock_xp"] = globalThis["webpackChunkblock_xp"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, [224], () => (__webpack_require__(913)))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});;