/* eslint-disable */
/* Do not edit directly, refer to ui/ folder. */
define(["block_xp/ui-commons-lazy"],() => { return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 791:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  App: () => (/* binding */ App),
  dependencies: () => (/* binding */ dependencies),
  startApp: () => (/* binding */ startApp)
});

// EXTERNAL MODULE: ./node_modules/@headlessui/react/dist/components/menu/menu.js + 13 modules
var menu = __webpack_require__(929);
// EXTERNAL MODULE: ./node_modules/react/index.js
var react = __webpack_require__(540);
// EXTERNAL MODULE: ./node_modules/react-dom/index.js
var react_dom = __webpack_require__(961);
// EXTERNAL MODULE: ./node_modules/react-query/es/index.js
var es = __webpack_require__(942);
;// CONCATENATED MODULE: ./ui/src/lib/contexts.ts

const makeAddonContextValueFromAppProps = (props) => {
    return {
        activated: false,
        enablepromo: true,
        promourl: "https://www.levelup.plus/xp/",
        ...(props?.addon ?? {}),
    };
};
const AddonContext = (0,react.createContext)({
    activated: false,
    enablepromo: true,
    promourl: "https://www.levelup.plus/xp/", // Local promo page where possible.
});

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
const utils_getUniqueId = () => {
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
function imageUrl(name, component) {
    return M.util.image_url(name, component);
}
function isBehatRunning() {
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

;// CONCATENATED MODULE: ./ui/src/lib/hooks.ts




const useAddonActivated = () => {
    return (0,react.useContext)(AddonContext).activated;
};
const useAnchorButtonProps = (onClick) => {
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
const hooks_useDuplicatedActionPreventor = (msDelay = 100) => {
    const ref = (0,react.useRef)();
    return (0,react.useCallback)(() => {
        if (ref.current && ref.current > Date.now() - msDelay) {
            return false;
        }
        ref.current = Date.now();
        return true;
    }, []);
};
const hooks_useModules = (modules) => {
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
    (0,react.useEffect)(() => {
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
    const [id] = useState(getUniqueId());
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
const useStrings = (ids, component = "block_xp") => {
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

;// CONCATENATED MODULE: ./ui/src/components/Addon.tsx



const IfAddonActivatedOrPromoEnabled = ({ children }) => {
    const { activated, enablepromo } = (0,react.useContext)(AddonContext);
    if (!activated && !enablepromo) {
        return null;
    }
    return react.createElement(react.Fragment, null, children);
};
const AddonRequired = () => {
    const { promourl } = (0,react.useContext)(AddonContext);
    const getStr = useStrings(["xpplusrequired", "unlockfeaturewithxpplus"]);
    const handleClick = (e) => e.preventDefault();
    return (react.createElement("a", { href: "#", role: "button", onClick: handleClick, "data-toggle": "popover", "data-placement": "top", "data-container": "body", "data-content": getStr("unlockfeaturewithxpplus", promourl), "data-html": "true", className: "xp-py-1 xp-px-1.5 xp-normal-case xp-text-2xs xp-inline-block xp-bg-black xp-text-white xp-rounded xp-no-underline" }, getStr("xpplusrequired")));
};

;// CONCATENATED MODULE: ./ui/src/components/Modal.tsx



const SaveCancelModal = ({ children, onClose, onSave, show, title, saveButtonText, defaultHeight, large, canSave = true }) => {
    const modalPromise = (0,react.useRef)();
    const modalRef = (0,react.useRef)();
    // In rare instances, we can get double save events. This can happen when we hit enter,
    // and a new event listener is registered while Moodle is still broadcasting its events
    // which is then called, and so we get two events. This wouldn't happen if the modal was
    // not re-rendering, I think.
    const isSavePermitted = hooks_useDuplicatedActionPreventor();
    const { getModule } = hooks_useModules(["core/modal_factory", "core/modal_events"]);
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
    const modalPromise = useRef();
    const modalRef = useRef();
    const [ready, setReady] = useState(false);
    const isDeletePermitted = useDuplicatedActionPreventor();
    const deleteStr = useString("delete", "core");
    const { getModule } = useModules(["core/modal_factory", "core/modal_events"]);
    const getDeleteButton = useCallback(() => {
        if (!modalRef.current)
            return null;
        const node = modalRef.current.getFooter()[0].querySelector('[data-action="save"]');
        return node ?? null;
    }, [modalRef.current]);
    // Create the modal object.
    useEffect(() => {
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
    useEffect(() => {
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
    useEffect(() => {
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
    useEffect(() => {
        if (!modalRef.current || !title)
            return;
        modalRef.current.setTitle(title);
    }, [title, modalRef.current]);
    // Update button.
    useEffect(() => {
        if (!modalRef.current || !deleteStr)
            return;
        const btn = getDeleteButton();
        if (!btn)
            return;
        btn.textContent = deleteStr;
    }, [deleteStr, modalRef.current]);
    return modalRef.current ? ReactDOM.createPortal(children, modalRef.current.getBody()[0].querySelector(".block_xp")) : null;
};
const ModalForm = ({ formClass, formArgs, onClose, onSubmit, title }) => {
    const modalFormRef = useRef();
    const { getModule } = useModules(["core_form/modalform", "core/modal_factory", "core/modal_events"]);
    // Create the modal form.
    useEffect(() => {
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
    useEffect(() => {
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
    useEffect(() => {
        if (!modalFormRef.current)
            return;
        const modal = modalFormRef.current.modal;
        if (!modal)
            return;
        modal.setTitle(title);
    }, [title]);
    return null;
};

;// CONCATENATED MODULE: ./ui/src/components/Str.tsx


const Str = ({ id, component = "block_xp", a }) => {
    const str = hooks_useString(id, component, a);
    return react.createElement(react.Fragment, null, str || "​");
};
/* harmony default export */ const components_Str = (Str);

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
    return react.createElement("textarea", { ...props, className: `xp-m-0 form-control ${className}` });
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
    const minusProps = useAnchorButtonProps(() => {
        if (minDisabled)
            return;
        handleChange(value - step);
    });
    const plusProps = useAnchorButtonProps(() => {
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

;// CONCATENATED MODULE: ./ui/src/components/RadioGroup.tsx

const RadioGroup = ({ items, value, onChange }) => {
    const [uniqid] = (0,react.useState)(() => Math.random().toString(12).slice(2));
    return (react.createElement("div", { className: "xp-space-y-2" }, items.map((item, idx) => (react.createElement("label", { className: "xp-relative xp-flex xp-items-start xp-cursor-pointer xp-m-0 xp-font-normal", key: item.value },
        react.createElement("div", { className: "xp-h-6 xp-flex xp-items-center" },
            react.createElement("input", { type: "radio", "aria-describedby": `xp-radiogroup-${uniqid}-${idx}`, checked: value === item.value, onChange: () => onChange(item.value) })),
        react.createElement("div", { className: "xp-ml-3" },
            react.createElement("div", { className: "xp-font-medium" }, item.label),
            item.desc ? (react.createElement("p", { id: `xp-radiogroup-${uniqid}-${idx}`, className: "xp-text-gray-500 xp-m-0" }, item.desc)) : null))))));
};

;// CONCATENATED MODULE: ./ui/src/lib/constants.ts
const HELP_URL_LEVELS = "https://docs.levelup.plus/xp/docs/levels";

;// CONCATENATED MODULE: ./ui/src/components/BulkEditPoints.tsx







function calculationMethodReducer(state, action) {
    switch (action.type) {
        case "setMethod":
            return { ...state, method: action.payload };
        case "setBase":
            return { ...state, base: Math.max(1, action.payload) };
        case "setIncrement":
            return { ...state, incr: Math.max(0, action.payload) };
        case "setCoef":
            return { ...state, coef: Math.min(5, Math.max(1, action.payload)) };
    }
    return state;
}
function getDefaultBulkEditPointsState(props) {
    return {
        method: props.method || "relative",
        base: Math.max(1, props.base || 120),
        incr: Math.max(0, props.incr || 40),
        coef: Math.min(5, Math.max(props.coef || 1.3)),
    };
}
const BulkEditPoints = ({ method, base, incr, coef, onBaseChange, onCoefChange, onIncrementChange, onMethodChange }) => {
    const getStr = useStrings([
        "basepoints",
        "basepointslineardesc",
        "basepointsrelativedesc",
        "difficulty",
        "difficultyflat",
        "difficultyflatdesc",
        "difficultylinear",
        "difficultylineardesc",
        "difficultylinearincrdesc",
        "difficultypointincrease",
        "difficultyrelative",
        "difficultyrelativedesc",
        "difficultyrelativeincrdesc",
        "documentation",
        "pointsperlevel",
        "recommended",
    ], "block_xp");
    return (react.createElement("div", { className: "xp-space-y-4" },
        react.createElement("div", null,
            react.createElement("div", { className: "xp-mb-2 xp-flex xp-items-start xp-flex-wrap" },
                react.createElement("div", { className: "xp-grow xp-font-bold" }, getStr("difficulty")),
                react.createElement("div", { className: "xp-shrink-0" },
                    react.createElement("a", { href: HELP_URL_LEVELS, target: "_blank", rel: "noopener" }, getStr("documentation")))),
            react.createElement(RadioGroup, { onChange: onMethodChange, value: method, items: [
                    { value: "flat", label: getStr("difficultyflat"), desc: getStr("difficultyflatdesc") },
                    {
                        value: "linear",
                        label: getStr("difficultylinear"),
                        desc: getStr("difficultylineardesc"),
                    },
                    {
                        value: "relative",
                        label: (react.createElement(react.Fragment, null,
                            getStr("difficultyrelative"),
                            react.createElement("div", { className: "badge badge-info xp-ml-2" }, getStr("recommended")))),
                        desc: getStr("difficultyrelativedesc"),
                    },
                ] })),
        react.createElement("div", null,
            react.createElement("p", { className: "xp-font-bold xp-mb-2" },
                react.createElement(components_Str, { id: "settings", component: "core" })),
            method === "flat" ? (react.createElement(react.Fragment, null,
                react.createElement("div", { className: "" },
                    react.createElement("label", { htmlFor: "xp-calc-bp", className: "xp-m-0" },
                        react.createElement(components_Str, { id: "pointsperlevel" })),
                    react.createElement("div", null,
                        react.createElement(NumberInputWithButtons, { value: base, onChange: onBaseChange, min: 1, step: 10, inputProps: { id: "xp-calc-bp", className: "xp-w-24" } }))))) : null,
            method === "linear" ? (react.createElement(react.Fragment, null,
                react.createElement("div", { className: "xp-space-y-2" },
                    react.createElement("div", { className: "" },
                        react.createElement("label", { htmlFor: "xp-calc-bp", className: "xp-m-0" },
                            react.createElement(components_Str, { id: "basepoints" })),
                        react.createElement("div", null,
                            react.createElement(NumberInputWithButtons, { value: base, onChange: onBaseChange, min: 1, step: 10, inputProps: { id: "xp-calc-bp", className: "xp-w-24" } })),
                        react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" }, getStr("basepointslineardesc"))),
                    react.createElement("div", { className: "" },
                        react.createElement("label", { htmlFor: "xp-calc-pi", className: "xp-m-0" }, getStr("difficultypointincrease")),
                        react.createElement("div", null,
                            react.createElement(NumberInputWithButtons, { value: incr, onChange: onIncrementChange, min: 0, inputProps: { id: "xp-calc-pi", className: "xp-w-24" } })),
                        react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" }, getStr("difficultylinearincrdesc")))))) : null,
            method === "relative" ? (react.createElement(react.Fragment, null,
                react.createElement("div", { className: "xp-space-y-2" },
                    react.createElement("div", { className: "" },
                        react.createElement("label", { htmlFor: "xp-calc-bp", className: "xp-m-0" },
                            react.createElement(components_Str, { id: "basepoints" })),
                        react.createElement("div", null,
                            react.createElement(NumberInputWithButtons, { value: base, onChange: onBaseChange, min: 1, step: 10, inputProps: { id: "xp-calc-bp", className: "xp-w-24" } })),
                        react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" }, getStr("basepointsrelativedesc"))),
                    react.createElement("div", { className: "" },
                        react.createElement("label", { htmlFor: "xp-calc-pi", className: "xp-m-0" }, getStr("difficultypointincrease")),
                        react.createElement("div", null,
                            react.createElement(NumberInputWithButtons, { value: Math.floor(coef * 100 - 100), onChange: (p) => onCoefChange(1 + p / 100), min: 0, max: 400, inputProps: { id: "xp-calc-pi", className: "xp-w-24", maxLength: 3 }, suffix: "%" })),
                        react.createElement("p", { className: "xp-text-gray-500 xp-m-0 xp-mt-1" }, getStr("difficultyrelativeincrdesc")))))) : null)));
};
const BulkEditPointsModal = (props) => {
    const [state, dispatch] = (0,react.useReducer)(calculationMethodReducer, props, getDefaultBulkEditPointsState);
    const getStr = useStrings(["quickeditpoints", "apply"], "block_xp");
    const setMethod = (p) => dispatch({ type: "setMethod", payload: p });
    const setIncrement = (p) => dispatch({ type: "setIncrement", payload: p });
    const setBase = (p) => dispatch({ type: "setBase", payload: p });
    const setCoef = (p) => dispatch({ type: "setCoef", payload: p });
    const handleClose = () => {
        dispatch({ type: "reset", payload: getDefaultBulkEditPointsState(props) });
        props.onClose && props.onClose();
    };
    const handleSave = () => {
        props.onSave && props.onSave(state);
    };
    return (react.createElement(SaveCancelModal, { show: props.show, onClose: handleClose, onSave: handleSave, title: getStr("quickeditpoints"), saveButtonText: getStr("apply") },
        react.createElement(BulkEditPoints, { coef: state.coef, base: state.base, incr: state.incr, method: state.method, onBaseChange: setBase, onCoefChange: setCoef, onIncrementChange: setIncrement, onMethodChange: setMethod })));
};

;// CONCATENATED MODULE: ./ui/src/components/Pix.tsx


const Pix = ({ id, component = 'block_xp', className, alt = '' }) => {
    return react.createElement("img", { src: imageUrl(id, component), alt: alt, className: className });
};
/* harmony default export */ const components_Pix = (Pix);

;// CONCATENATED MODULE: ./ui/src/components/Spinner.tsx



const Spinner = ({ className }) => {
    const alt = hooks_useString('loadinghelp', 'core');
    return react.createElement(components_Pix, { id: "y/loading", component: "core", className: className, alt: alt });
};
/* harmony default export */ const components_Spinner = (Spinner);

;// CONCATENATED MODULE: ./ui/src/components/Button.tsx






const CircleButton = ({ className, ...props }) => {
    return (React.createElement("button", { className: classNames("xp-bg-transparent xp-border-0 xp-p-2 xp-flex xp-items-center xp-rounded-full hover:xp-bg-gray-100", className), type: "button", ...props }));
};
const Button = ({ onClick, disabled, children, primary, className, type = "button" }) => {
    const classes = utils_classNames("btn", primary ? "btn-primary" : "btn-default btn-secondary", className);
    return (react.createElement("button", { className: classes, onClick: onClick, disabled: disabled, type: type }, children));
};
const SaveButton = ({ onClick, disabled, label, mutation = {}, statePosition = "after" }) => {
    const getStr = useStrings(["changessaved", "error"], "core");
    const { isLoading, isSuccess, isError } = mutation;
    const isStateBefore = statePosition === "before";
    const state = (react.createElement("div", { className: `xp-w-8 xp-flex ${isStateBefore ? "xp-mr-4 xp-justify-end" : "xp-ml-4"}`, "aria-live": "assertive" },
        isLoading ? react.createElement(components_Spinner, null) : null,
        isSuccess ? react.createElement(components_Pix, { id: "i/valid", component: "core", alt: getStr("changessaved") }) : null,
        isError ? react.createElement(components_Pix, { id: "i/invalid", component: "core", alt: getStr("error") }) : null));
    return (react.createElement("div", { className: "xp-flex xp-items-center" },
        isStateBefore ? state : null,
        react.createElement("div", { className: "" },
            react.createElement(Button, { primary: true, onClick: onClick, disabled: disabled || isLoading }, label || react.createElement(components_Str, { id: "savechanges", component: "core" }))),
        !isStateBefore ? state : null));
};
const AnchorButton = ({ children, onClick, className, ...props }) => {
    const anchorButtonProps = useAnchorButtonProps(onClick);
    return (react.createElement("a", { className: utils_classNames("xp-text-inherit xp-no-underline", className), ...props, ...anchorButtonProps }, children));
};

// EXTERNAL MODULE: ./node_modules/react-animate-height/dist/esm/index.js
var esm = __webpack_require__(968);
;// CONCATENATED MODULE: ./ui/src/components/Expandable.tsx


function Expandable({ expanded, children, id }) {
    return (react.createElement(esm/* default */.A, { id: id, height: expanded ? "auto" : 0, applyInlineTransitions: false, animationStateClasses: {
            animating: "xp-transition-height xp-duration-500",
            static: "xp-transition-height xp-duration-500",
            animatingUp: "",
            animatingDown: "",
            animatingToHeightZero: "",
            animatingToHeightAuto: "",
            animatingToHeightSpecific: "",
            staticHeightZero: "",
            staticHeightAuto: "",
            staticHeightSpecific: "",
        } }, children));
}

;// CONCATENATED MODULE: ./ui/src/components/Icons.tsx

const Bars3BottomLeftIcon = ({ className }) => (react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    react.createElement("path", { fillRule: "evenodd", d: "M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75H12a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z", clipRule: "evenodd" })));
const CheckBadgeIconSolid = ({ className }) => (react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    react.createElement("path", { fillRule: "evenodd", d: "M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z", clipRule: "evenodd" })));
const LanguageIcon = ({ className }) => (react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    react.createElement("path", { fillRule: "evenodd", d: "M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z", clipRule: "evenodd" })));
const PaperAirplaneIconSolid = ({ className }) => (react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", className: className },
    react.createElement("path", { d: "M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" })));
const ChevronLeftIconSolid = ({ className }) => (React.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", fill: "none", viewBox: "0 0 24 24", strokeWidth: 1.5, stroke: "currentColor", className: className },
    React.createElement("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M15.75 19.5 8.25 12l7.5-7.5" })));

;// CONCATENATED MODULE: ./ui/src/components/Level.tsx




const getLevelHtml = (level, small, medium) => {
    const label = getString("levelx", "block_xp", level.level);
    const classes = `block_xp-level level-${level.level} ${small ? "small" : medium ? "medium" : ""}`;
    if (level.badgeurl) {
        return `
      <div class="${classes + " level-badge"}" aria-label="${escapeHtml(label)}">
        <img src="${escapeHtml(level.badgeurl)}" alt="${escapeHtml(label)}" />
      </div>
    `;
    }
    return `
    <div class="${classes}" aria-label="${escapeHtml(label)}">
      ${level.level}
    </div>
  `;
};
const Level = (0,react.forwardRef)(({ level, small, medium }, ref) => {
    const label = hooks_useString("levelx", "block_xp", level.level);
    const classes = "block_xp-level level-" + level.level + (small ? " small" : medium ? " medium" : "");
    if (level.badgeurl) {
        return (react.createElement("div", { className: classes + " level-badge", "aria-label": label, ref: ref },
            react.createElement("img", { src: level.badgeurl, alt: label })));
    }
    return (react.createElement("div", { className: classes, "aria-label": label, ref: ref }, level.level));
});
/* harmony default export */ const components_Level = (Level);

;// CONCATENATED MODULE: ./ui/src/components/Tooltip.tsx


const Tooltip = ({ children, content }) => {
    const ref = react.useRef(null);
    (0,react.useEffect)(() => {
        const $ = getModule("jquery");
        if (!$ || !ref.current || !$(ref.current).tooltip) {
            return;
        }
        ref.current.setAttribute("data-container", "body");
        ref.current.setAttribute("title", content);
        $(ref.current).tooltip("enable");
        return () => {
            // There is extra caution here, double checking whether the reference still exists,
            // and is still bound to the tooltip function, and that the tooltip function does
            // not throw an exception. This is to mitigate themes that redeclare Bootstrap and
            // end-up causing troubles.
            if (!ref.current || !$(ref.current).tooltip) {
                return;
            }
            try {
                $(ref.current).tooltip("dispose");
            }
            catch (e) {
                try {
                    $(ref.current).tooltip("destroy");
                }
                catch (e) { }
            }
        };
    }, [content]);
    return (0,react.cloneElement)(children, { ref });
};

;// CONCATENATED MODULE: ./ui/src/lib/levels.ts
function computeRequiredPoints(level, base, coef) {
    if (level <= 1) {
        return 0;
    }
    else if (level == 2) {
        return base;
    }
    if (coef <= 1) {
        return base * (level - 1);
    }
    return Math.round(base * ((1 - Math.pow(coef, level - 1)) / (1 - coef)));
}
function computeRequiredPointsWithMethod(level, method) {
    if (level <= 1) {
        return 0;
    }
    else if (level === 2) {
        return method.base;
    }
    if (method.method === "relative") {
        // Refer to the original method that was algorithmic.
        return computeRequiredPoints(level, method.base, method.coef);
    }
    else if (method.method === "linear") {
        // Each level is worth the base + increment (starting at level 3);
        // Level 1: 0; level 2: 100; Level 3: 210 (100 + (100 + 10)); Level 4: 330 (100 + (100 + 10) + (100 + 10 + 10));
        return (method.base * (level - 1) +
            Array.from({ length: level }).reduce((carry, _, idx) => carry + Math.max(0, idx - 1) * method.incr, 0));
    }
    // Flat method.
    return (level - 1) * method.base;
}
const getLevel = (levels, level) => {
    return levels[Math.max(0, level - 1)];
};
const getMinimumPointsForLevel = (levels, level) => {
    if (level.level === 1 || !levels.length) {
        return 0;
    }
    return getPreviousLevel(levels, level).xprequired + 1;
};
const getMinimumPointsAtLevel = (levels, level) => {
    const l = getLevel(levels, level - 1);
    return l ? l.xprequired + 1 : 0;
};
const getNextLevel = (levels, level, highest = 9999) => {
    let index = Math.min(highest, Math.max(levels.indexOf(level) + 1, 0));
    return levels[index];
};
const getPreviousLevel = (levels, level) => {
    return levels[Math.max(levels.indexOf(level) - 1, 0)];
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

;// CONCATENATED MODULE: ./ui/src/levels.tsx





















var BADGE_TYPE;
(function (BADGE_TYPE) {
    BADGE_TYPE[BADGE_TYPE["Site"] = 1] = "Site";
    BADGE_TYPE[BADGE_TYPE["Course"] = 2] = "Course";
})(BADGE_TYPE || (BADGE_TYPE = {}));
const optionsStates = [
    {
        id: "name",
        Icon: LanguageIcon,
        yes: "hasname",
        no: "hasnoname",
        checker: (level) => level.name && level.name.trim().length > 0,
    },
    {
        id: "description",
        Icon: Bars3BottomLeftIcon,
        yes: "hasdescription",
        no: "hasnodescription",
        checker: (level) => level.description && level.description.trim().length > 0,
    },
    {
        id: "popupmessage",
        Icon: PaperAirplaneIconSolid,
        yes: "haspopupmessage",
        no: "hasnopopupmessage",
        checker: (level) => level.popupmessage && level.popupmessage.trim().length > 0,
    },
    {
        id: "badgeaward",
        Icon: CheckBadgeIconSolid,
        yes: "hasbadgeaward",
        no: "hasnobadgeaward",
        checker: (level) => Boolean(level.badgeawardid),
    },
];
const optionsStatesStringIds = optionsStates.map((o) => o.yes).concat(optionsStates.map((o) => o.no));
const getInitialState = ({ levelsInfo }) => {
    return {
        algo: {
            ...levelsInfo.algo,
            method: levelsInfo.algo.method || "relative",
            incr: levelsInfo.algo.incr || 30,
        },
        levels: levelsInfo.levels.map((level) => ({ ...level })),
        nblevels: levelsInfo.levels.length,
        pendingSave: false,
    };
};
const markPendingSave = (state) => {
    return { ...state, pendingSave: true };
};
const updateLevelPoints = (state) => {
    return {
        ...state,
        levels: state.levels.reduce((carry, level, i) => {
            return carry.concat([
                { ...level, xprequired: Math.max(level.xprequired, getMinimumPointsForLevel(carry.concat([level]), level)) },
            ]);
        }, []),
    };
};
const reducer = (state, [action, payload]) => {
    let nextLevel;
    switch (action) {
        case "bulkEdit":
            return markPendingSave({
                ...state,
                algo: payload,
                levels: state.levels.map((level) => ({
                    ...level,
                    xprequired: computeRequiredPointsWithMethod(level.level, payload),
                })),
            });
        case "levelDescChange":
            return markPendingSave({
                ...state,
                levels: state.levels.map((level) => {
                    if (level !== payload.level) {
                        return level;
                    }
                    return { ...level, description: stripTags(payload.desc) || null };
                }),
            });
        case "levelNameChange":
            return markPendingSave({
                ...state,
                levels: state.levels.map((level) => {
                    if (level !== payload.level) {
                        return level;
                    }
                    return { ...level, name: stripTags(payload.name) || null };
                }),
            });
        case "levelBadgeAwardIdChange":
            return markPendingSave({
                ...state,
                levels: state.levels.map((level) => {
                    if (level !== payload.level) {
                        return level;
                    }
                    return { ...level, badgeawardid: payload.badgeawardid || null };
                }),
            });
        case "levelPopupMessageChange":
            return markPendingSave({
                ...state,
                levels: state.levels.map((level) => {
                    if (level !== payload.level) {
                        return level;
                    }
                    return { ...level, popupmessage: payload.popupmessage || null };
                }),
            });
        case "levelPointsChange":
            nextLevel = getNextLevel(state.levels, payload.level, state.nblevels);
            if (isNaN(payload.points) || payload.points <= 2 || payload.points >= Infinity) {
                return state;
            }
            else if (payload.points <= getPreviousLevel(state.levels, payload.level).xprequired) {
                return state;
            }
            return markPendingSave(updateLevelPoints({
                ...state,
                levels: state.levels.map((level) => {
                    if (level !== payload.level) {
                        return level;
                    }
                    return { ...level, xprequired: payload.points };
                }),
            }));
        case "nbLevelsChange":
            if (typeof payload?.n === "undefined" || isNaN(payload.n) || payload.n < 2 || payload.n > 99) {
                return state;
            }
            return markPendingSave({
                ...state,
                nblevels: payload.n,
                levels: state.levels.concat(Array.from({ length: Math.max(0, payload.n - state.levels.length) }).map((_, i) => {
                    const l = state.levels.length + i + 1;
                    return {
                        level: l,
                        name: null,
                        description: null,
                        badgeurl: (payload?.defaultBadgeUrls || {})[l] || null,
                        xprequired: computeRequiredPointsWithMethod(l, state.algo),
                    };
                })),
            });
        case "markSaved":
            return {
                ...state,
                pendingSave: false,
            };
    }
    return state;
};
const OptionField = ({ label, children, note, xpPlusRequired, }) => {
    return (react.createElement("div", null,
        react.createElement("label", { className: "xp-m-0 xp-block xp-font-normal" },
            react.createElement("div", { className: "xp-flex" },
                react.createElement("div", { className: "xp-grow xp-uppercase xp-text-xs" }, label),
                react.createElement("div", null, xpPlusRequired ? react.createElement(AddonRequired, null) : null)),
            react.createElement("div", { className: "xp-mt-1" }, children)),
        note ? react.createElement("div", { className: "xp-text-gray-500 xp-mt-1" }, note) : null));
};
const showLevelUpNotificationPreview = async (level, prevLevel) => {
    const PopupModule = await getModuleAsync("block_xp/popup-notification");
    PopupModule.show({
        courseid: 0,
        levelnum: level.level,
        levelname: level.name,
        levelbadge: getLevelHtml(level),
        prevlevelbadge: getLevelHtml(prevLevel),
        message: level.popupmessage,
    });
};
const App = ({ courseId, levelsInfo, resetToDefaultsUrl, defaultBadgeUrls, badges = [] }) => {
    const hasXpPlus = useAddonActivated();
    const [state, dispatch] = (0,react.useReducer)(reducer, { levelsInfo }, getInitialState);
    const levels = state.levels.slice(0, state.nblevels);
    const [expanded, setExpanded] = react.useState([]);
    const [bulkEdit, setBulkEdit] = react.useState(false);
    const getStr = useStrings(optionsStatesStringIds.concat(["levelssaved", "unknownbadgea", "levelx", "previewpopupnotification"]));
    const getBadgeStr = useStrings(["coursebadges", "sitebadges"], "core_badges");
    const getCoreStr = useStrings(["other", "none"], "core");
    useUnloadCheck(state.pendingSave);
    // Prepare the save mutation.
    const mutation = (0,es.useMutation)(() => {
        // An falsy course ID means admin config.
        const method = courseId ? "block_xp_set_levels_info" : "block_xp_set_default_levels_info";
        return ajaxRequest(method, {
            courseid: courseId ? courseId : undefined,
            levels: levels.map((level) => {
                const { level: levelnum, xprequired, ...metadata } = level;
                return {
                    level: levelnum,
                    xprequired: xprequired,
                    metadata: Object.entries(metadata).reduce((carry, [name, value]) => carry.concat([{ name, value }]), []),
                };
            }),
            algo: state.algo,
        });
    });
    // Reset mutation after success.
    (0,react.useEffect)(() => {
        if (!mutation.isSuccess) {
            return;
        }
        const t = setTimeout(() => {
            mutation.reset();
        }, 2500);
        return () => clearTimeout(t);
    });
    const siteBadges = (0,react.useMemo)(() => badges.filter((b) => b.type === BADGE_TYPE.Site).sort((a, b) => a.name.localeCompare(b.name)), [badges]);
    const courseBadges = (0,react.useMemo)(() => badges.filter((b) => b.type === BADGE_TYPE.Course).sort((a, b) => a.name.localeCompare(b.name)), [badges]);
    const allExpanded = expanded.length === levels.length;
    const handleCollapseExpandAll = () => {
        setExpanded(allExpanded ? [] : levels.map((l) => l.level));
    };
    const handleSave = () => {
        mutation.mutate(undefined, {
            onSuccess: () => {
                const Toast = getModule("core/toast");
                Toast && Toast.add(getStr("levelssaved"));
                dispatch(["markSaved", true]);
            },
        });
    };
    const handleBulkEdit = (state) => {
        dispatch(["bulkEdit", state]);
    };
    const handleNumLevelsChange = (n) => {
        dispatch(["nbLevelsChange", { n, defaultBadgeUrls }]);
    };
    const handleLevelDescChange = (level, desc) => {
        if (level.description === desc)
            return;
        dispatch(["levelDescChange", { level, desc: desc }]);
    };
    const handleLevelNameChange = (level, name) => {
        if (level.name === name)
            return;
        dispatch(["levelNameChange", { level, name: name }]);
    };
    const handleXpChange = (level, xp) => {
        if (level.xprequired === xp)
            return;
        dispatch(["levelPointsChange", { level, points: xp }]);
    };
    return (react.createElement("div", { className: "xp-flex xp-flex-col" },
        react.createElement("div", { className: "xp-mb-4 xp-flex xp-items-end xp-justify-end xp-flex-wrap xp-gap-4" },
            react.createElement("div", { className: "xp-flex xp-flex-1 xp-gap-4 xp-items-end xp-flex-wrap" },
                react.createElement("div", { className: "" },
                    react.createElement("label", { htmlFor: "label-x", className: "xp-block xp-m-0" },
                        react.createElement(components_Str, { id: "numberoflevels" })),
                    react.createElement(NumberInputWithButtons, { value: state.nblevels, onChange: handleNumLevelsChange, min: 2, max: 99, inputProps: { id: "label-x", maxLength: 2 } })),
                react.createElement("div", { className: "" },
                    react.createElement(Button, { onClick: () => setBulkEdit(true) },
                        react.createElement(components_Str, { id: "quickeditpoints" })),
                    react.createElement(BulkEditPointsModal, { show: bulkEdit, onClose: () => setBulkEdit(false), onSave: handleBulkEdit, method: state.algo.method, coef: state.algo.coef, base: state.algo.base, incr: state.algo.incr }))),
            react.createElement("div", { className: "xp-flex xp-gap-1" },
                react.createElement(SaveButton, { statePosition: "before", onClick: handleSave, mutation: mutation, disabled: !state.pendingSave || mutation.isLoading }),
                react.createElement(menu/* Menu */.W, { as: "div", className: "xp-relative xp-inline-block xp-text-left" },
                    react.createElement("div", null,
                        react.createElement(menu/* Menu */.W.Button, { className: "xp-text-inherit xp-bg-transparent xp-border-0 xp-p-2 xp-flex xp-items-center xp-rounded-full hover:xp-bg-gray-100" },
                            react.createElement("span", { className: "sr-only" },
                                react.createElement(components_Str, { id: "options", component: "core" })),
                            react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 20 20", fill: "currentColor", className: "xp-w-5 xp-h-5", "aria-hidden": "true" },
                                react.createElement("path", { d: "M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" })))),
                    react.createElement(menu/* Menu */.W.Items, { className: "xp-absolute xp-right-0 xp-z-10 xp-mt-2 xp-w-56 xp-origin-top-right xp-rounded-md xp-bg-white xp-border xp-border-solid xp-border-gray-300 xp-shadow-sm xp-divide-y xp-divide-gray-100" },
                        react.createElement("div", { className: "xp-py-1" },
                            react.createElement(menu/* Menu */.W.Item, null, ({ active, close }) => (react.createElement("a", { href: "#", role: "button", onClick: (e) => {
                                    e.preventDefault();
                                    handleCollapseExpandAll();
                                    close();
                                }, className: utils_classNames(active ? "xp-bg-gray-100" : null, "xp-text-inherit xp-block xp-px-6 xp-py-1 xp-no-underline") }, allExpanded ? react.createElement(components_Str, { id: "collapseall", component: "core" }) : react.createElement(components_Str, { id: "expandall", component: "core" })))),
                            react.createElement(menu/* Menu */.W.Item, null, ({ active, close }) => (react.createElement("a", { href: HELP_URL_LEVELS, target: "_blank", rel: "noopener", className: utils_classNames(active ? "xp-bg-gray-100" : null, "xp-text-inherit xp-block xp-px-6 xp-py-1 xp-no-underline") },
                                react.createElement(components_Str, { id: "documentation" }))))),
                        resetToDefaultsUrl ? (react.createElement("div", { className: "xp-py-1" },
                            react.createElement(menu/* Menu */.W.Item, null, ({ active, close }) => (react.createElement("a", { href: resetToDefaultsUrl, className: utils_classNames(active ? "xp-bg-gray-100" : null, "xp-text-red-600 xp-block xp-px-6 xp-py-1 xp-no-underline") },
                                react.createElement(components_Str, { id: "resettodefaults" })))))) : null)))),
        react.createElement("div", { className: "xp-flex xp-flex-col xp-flex-1 xp-gap-4" }, Array.from({ length: state.nblevels }).map((_, idx) => {
            const level = levels[idx] || { level: idx + 1, xprequired: 0 };
            const prevLevel = levels[idx - 1];
            const nextLevel = levels[idx + 1];
            const pointsInLevel = nextLevel ? nextLevel.xprequired - level.xprequired : 0;
            const isExpanded = expanded.includes(level.level);
            let optionStates = level.level <= 1
                ? optionsStates.filter((o) => ["name", "description", courseId ? null : "badgeawardid"].includes(o.id))
                : optionsStates;
            optionStates = optionStates.concat(Array.from({ length: Math.max(0, optionsStates.length - optionStates.length) }).map((_) => null));
            const isBadgeValueMissing = levelsInfo.levels[idx]?.badgeawardid && !badges.find((b) => b.id === levelsInfo.levels[idx].badgeawardid);
            const handleBadgeAwardIdChange = (e) => {
                dispatch(["levelBadgeAwardIdChange", { level, badgeawardid: parseInt(e.target.value, 10) || null }]);
            };
            const handlePopupMessageChange = (e) => {
                dispatch(["levelPopupMessageChange", { level, popupmessage: e.target.value }]);
            };
            return (react.createElement(react.Fragment, { key: `l${level.level}` },
                react.createElement("div", { className: "xp-relative xp-min-h-28 xp-rounded-lg xp-border xp-border-solid xp-border-gray-200 xp-p-3 xp-overflow-hidden" },
                    react.createElement("div", { className: "xp-absolute xp--top-4 xp--left-8 xp-text-[10rem] xp-text-gray-50 xp-leading-none xp-pointer-events-none" }, level.level),
                    react.createElement("div", { className: "xp-flex xp-items-center xp-flex-grow xp-gap-4 sm:xp-gap-8 xp-flex-col sm:xp-flex-row xp-relative" },
                        react.createElement("div", { className: "xp-flex-0" },
                            react.createElement(Tooltip, { content: getStr("levelx", level.level) },
                                react.createElement(components_Level, { level: level }))),
                        react.createElement("div", { className: "xp-shrink-0 xp-basis-auto sm:xp-basis-52 sm:xp--mt-3.5" },
                            react.createElement("div", { className: "xp-grid xp-grid-cols-2" },
                                react.createElement("label", { className: "xp-m-0 xp-flex xp-items-end xp-text-xs xp-font-normal xp-uppercase", htmlFor: `xp-level-${level.level}-start` },
                                    react.createElement(components_Str, { id: "levelpointsstart" })),
                                react.createElement("label", { className: "xp-m-0 xp-flex xp-items-end xp-text-xs xp-font-normal xp-uppercase", htmlFor: `xp-level-${level.level}-length` },
                                    react.createElement(components_Str, { id: "levelpointslength" }))),
                            react.createElement("div", { className: "xp-grid xp-grid-cols-2 xp-border xp-border-solid xp-border-gray-300 xp-rounded" },
                                react.createElement("div", null,
                                    react.createElement(NumInput, { value: level.xprequired, onChange: (xp) => handleXpChange(level, xp), disabled: level.level <= 1, className: "xp-h-full xp-min-w-[4ch] xp-w-full xp-rounded-none xp-rounded-l xp-border-0 xp-relative focus:xp-z-10", id: `xp-level-${level.level}-start` })),
                                react.createElement("div", { className: "" },
                                    react.createElement("div", { className: "xp-relative xp-w-full x-h-full" },
                                        react.createElement("div", { className: "xp-pointer-events-none xp-absolute xp-inset-y-0 xp-left-0 xp-flex xp-items-center xp-pl-2 xp-z-20" },
                                            react.createElement("span", { className: "xp-text-gray-500" }, "+")),
                                        react.createElement(NumInput, { value: pointsInLevel, onChange: (xp) => handleXpChange(nextLevel, level.xprequired + xp), disabled: pointsInLevel <= 0, className: "xp-h-full xp-min-w-[4ch] xp-w-full xp-border-0 xp-rounded-none xp-border-l xp-border-gray-300 xp-rounded-r xp-pl-6 xp-relative focus:xp-z-10", id: `xp-level-${level.level}-length` }))))),
                        react.createElement("div", { className: "xp-flex xp-grow xp-items-center xp-justify-center  xp-gap-4" }, optionStates.map((o, idx) => {
                            if (!o) {
                                return react.createElement("div", { key: idx, className: "xp-w-6 xp-h-6 xp-hidden sm:xp-block" });
                            }
                            const state = o.checker(level);
                            const label = getStr(state ? o.yes : o.no);
                            return (react.createElement(Tooltip, { content: label, key: idx },
                                react.createElement("div", { className: utils_classNames("xp-w-6 xp-h-6", !state ? "xp-text-gray-300" : null) },
                                    react.createElement("span", { className: "xp-sr-only" }, label),
                                    react.createElement(o.Icon, { className: "xp-w-full xp-h-full" }))));
                        })),
                        react.createElement("div", { className: "xp-flex-0 sm:xp--mr-3" },
                            react.createElement(AnchorButton, { "aria-expanded": isExpanded, "aria-controls": `xp-level-${level.level}-options`, onClick: () => {
                                    setExpanded(isExpanded ? expanded.filter((e) => e != level.level) : [level.level, ...expanded]);
                                }, className: "xp-p-2 xp-inline-block sm:xp-mr-1" },
                                react.createElement("span", { className: "xp-sr-only" }, isExpanded ? react.createElement(components_Str, { id: "collapse", component: "core" }) : react.createElement(components_Str, { id: "expand", component: "core" })),
                                react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", fill: "none", viewBox: "0 0 24 24", strokeWidth: 1.5, stroke: "currentColor", className: utils_classNames("xp-w-6 xp-h-6 xp-transition-transform xp-duration-300", isExpanded ? "xp-rotate-90" : null) },
                                    react.createElement("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M8.25 4.5l7.5 7.5-7.5 7.5" }))))),
                    react.createElement(Expandable, { expanded: isExpanded, id: `xp-level-${level.level}-options` },
                        react.createElement("div", { className: utils_classNames("sm:xp-ml-[100px] sm:xp-pl-8 xp-space-y-4") },
                            react.createElement("div", { className: "xp-flex xp-items-end xp-gap-4" },
                                react.createElement("div", { className: "xp-flex-1" },
                                    react.createElement(OptionField, { label: react.createElement(components_Str, { id: "name" }) },
                                        react.createElement(components_Input, { className: "xp-min-w-48 x-w-full sm:xp-w-2/3 xp-max-w-full", onBlur: (e) => handleLevelNameChange(level, e.target.value), defaultValue: level.name || "", maxLength: 40, type: "text" }))),
                                prevLevel ? (react.createElement("div", { className: "xp-mb-1.5 xp-h-6 xp-w-6" },
                                    react.createElement(Tooltip, { content: getStr("previewpopupnotification") },
                                        react.createElement("div", null,
                                            react.createElement(AnchorButton, { onClick: () => showLevelUpNotificationPreview(level, prevLevel) },
                                                react.createElement("span", { className: "xp-sr-only" }, getStr("previewpopupnotification")),
                                                react.createElement("svg", { xmlns: "http://www.w3.org/2000/svg", fill: "none", viewBox: "0 0 24 24", strokeWidth: 1.5, stroke: "currentColor", className: "xp-w-6 xp-h-6" },
                                                    react.createElement("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Zm-3 0a.375.375 0 1 1-.53 0L9 2.845l.265.265Zm6 0a.375.375 0 1 1-.53 0L15 2.845l.265.265Z" }))))))) : null),
                            react.createElement(OptionField, { label: react.createElement(components_Str, { id: "description" }), note: react.createElement(components_Str, { id: "leveldescriptiondesc" }) },
                                react.createElement(Textarea, { className: "xp-w-full", onBlur: (e) => handleLevelDescChange(level, e.target.value), defaultValue: level.description || "", maxLength: 280, rows: 2 })),
                            react.createElement(IfAddonActivatedOrPromoEnabled, null, level.level > 1 ? (react.createElement(react.Fragment, null,
                                react.createElement(OptionField, { label: react.createElement(components_Str, { id: "popupnotificationmessage" }), note: react.createElement(components_Str, { id: "popupnotificationmessagedesc" }), xpPlusRequired: !hasXpPlus },
                                    react.createElement(Textarea, { className: "xp-w-full", onBlur: handlePopupMessageChange, defaultValue: level.popupmessage || "", maxLength: 280, rows: 2, disabled: !hasXpPlus })),
                                react.createElement(OptionField, { label: react.createElement(components_Str, { id: "badgeaward" }), note: react.createElement(components_Str, { id: "badgeawarddesc" }), xpPlusRequired: !hasXpPlus }, courseId ? (react.createElement(Select, { disabled: !hasXpPlus, className: "xp-max-w-full xp-w-auto", value: level.badgeawardid ?? "", onChange: handleBadgeAwardIdChange },
                                    react.createElement("option", null, getCoreStr("none")),
                                    courseBadges.length ? (react.createElement("optgroup", { label: getBadgeStr("coursebadges") }, courseBadges.map((b) => (react.createElement("option", { value: b.id, key: b.id }, b.name))))) : null,
                                    siteBadges.length ? (react.createElement("optgroup", { label: getBadgeStr("sitebadges") }, siteBadges.map((b) => (react.createElement("option", { value: b.id, key: b.id }, b.name))))) : null,
                                    isBadgeValueMissing ? (react.createElement("optgroup", { label: getCoreStr("other") },
                                        react.createElement("option", { value: level.badgeawardid || "" }, getStr("unknownbadgea", level.badgeawardid)))) : null)) : (react.createElement("div", { className: "alert alert-info xp-m-0" },
                                    react.createElement(components_Str, { id: "cannotbesetindefaults" })))))) : (react.createElement("div", null,
                                react.createElement("div", { className: "xp-text-sm xp-text-gray-500 xp-italic" },
                                    react.createElement(components_Str, { id: "levelupoptionsunavailableforlevelone" }))))))))));
        })),
        react.createElement("div", { className: "xp-flex xp-flex-1 xp-gap-4 xp-items-start xp-flex-wrap xp-mt-4" },
            react.createElement("div", { className: "xp-grow" },
                react.createElement(Button, { onClick: () => handleNumLevelsChange(state.nblevels + 1) },
                    react.createElement(components_Str, { id: "addlevel" }))),
            react.createElement("div", { className: "" },
                react.createElement(SaveButton, { statePosition: "before", onClick: handleSave, mutation: mutation, disabled: !state.pendingSave || mutation.isLoading })))));
};
function startApp(node, props) {
    react_dom.render(react.createElement(AddonContext.Provider, { value: makeAddonContextValueFromAppProps(props) },
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
/******/ 		__webpack_require__.j = 251;
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
/******/ 			251: 0
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
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, [224], () => (__webpack_require__(791)))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});;