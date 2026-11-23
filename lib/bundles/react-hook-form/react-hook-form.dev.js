// node_modules/react-hook-form/dist/index.esm.mjs
import React from "react";
var isCheckBoxInput = (element) => element.type === "checkbox";
var isFileInput = (element) => element.type === "file";
var isDateObject = (value) => value instanceof Date;
var isNullOrUndefined = (value) => value == null;
var isObjectType = (value) => typeof value === "object";
var isObject = (value) => !isNullOrUndefined(value) && !Array.isArray(value) && isObjectType(value) && !isDateObject(value);
var getEventValue = (event) => isObject(event) && event.target ? isCheckBoxInput(event.target) ? event.target.checked : isFileInput(event.target) ? event.target.files : event.target.value : event;
var FIELD_PATH_RE = /[.[\]'"]/;
var stringToPath = (input) => input.split(FIELD_PATH_RE).filter(Boolean);
function getNullAncestorValue(control, name) {
  const segments = stringToPath(name);
  let formValues = control._formValues;
  let defaultValues = control._defaultValues;
  for (let i = 0; i < segments.length - 1; i++) {
    const key = segments[i];
    formValues = formValues == null ? formValues : formValues[key];
    defaultValues = defaultValues == null ? defaultValues : defaultValues[key];
    if (formValues === null || defaultValues === null) {
      return null;
    }
  }
  return void 0;
}
var isNameInFieldArray = (names, name) => name.split(".").some((part, index, arr) => !isNaN(Number(part)) && names.has(arr.slice(0, index).join(".")));
var isWeb = typeof window !== "undefined" && typeof window.HTMLElement !== "undefined" && typeof document !== "undefined";
function cloneObject(data) {
  if (data === null || typeof data !== "object") {
    return data;
  }
  if (data instanceof Date) {
    return new Date(data);
  }
  const isBlobInstance = typeof Blob !== "undefined" && data instanceof Blob;
  const isFileListInstance = typeof FileList !== "undefined" && data instanceof FileList;
  if (isWeb && (isBlobInstance || isFileListInstance)) {
    return data;
  }
  const isArray = Array.isArray(data);
  const prototype = Object.getPrototypeOf(data);
  if (!isArray && prototype && prototype.constructor !== Object) {
    return data;
  }
  const copy = isArray ? new Array(data.length) : Object.create(prototype);
  for (const key in data) {
    if (Object.prototype.hasOwnProperty.call(data, key)) {
      copy[key] = cloneObject(data[key]);
    }
  }
  return copy;
}
var EVENTS = {
  BLUR: "blur",
  FOCUS_OUT: "focusout",
  CHANGE: "change",
  SUBMIT: "submit",
  TRIGGER: "trigger",
  VALID: "valid"
};
var VALIDATION_MODE = {
  onBlur: "onBlur",
  onChange: "onChange",
  onSubmit: "onSubmit",
  onTouched: "onTouched",
  all: "all"
};
var INPUT_VALIDATION_RULES = {
  max: "max",
  min: "min",
  maxLength: "maxLength",
  minLength: "minLength",
  pattern: "pattern",
  required: "required",
  validate: "validate"
};
var REGISTER_VALIDATION_RULES = [
  INPUT_VALIDATION_RULES.required,
  INPUT_VALIDATION_RULES.min,
  INPUT_VALIDATION_RULES.max,
  INPUT_VALIDATION_RULES.minLength,
  INPUT_VALIDATION_RULES.maxLength,
  INPUT_VALIDATION_RULES.pattern,
  INPUT_VALIDATION_RULES.validate
];
var FORM_ERROR_TYPE = "form";
var ROOT_ERROR_TYPE = "root";
var PROTOTYPE_KEYWORDS = ["__proto__", "constructor", "prototype"];
var IS_KEY_RE = /^\w*$/;
var isKey = (value) => IS_KEY_RE.test(value);
var isUndefined = (val) => val === void 0;
var get = (object, path, defaultValue) => {
  if (!path || !isObject(object)) {
    return defaultValue;
  }
  const paths = isKey(path) ? [path] : stringToPath(path);
  if (paths.some((key) => PROTOTYPE_KEYWORDS.includes(key))) {
    return defaultValue;
  }
  const result = paths.reduce((result2, key) => {
    return isNullOrUndefined(result2) ? void 0 : result2[key];
  }, object);
  return isUndefined(result) || result === object ? isUndefined(object[path]) ? defaultValue : object[path] : result;
};
var isBoolean = (value) => typeof value === "boolean";
var isFunction = (value) => typeof value === "function";
var set = (object, path, value) => {
  let index = -1;
  const tempPath = isKey(path) ? [path] : stringToPath(path);
  const length = tempPath.length;
  const lastIndex = length - 1;
  while (++index < length) {
    const key = tempPath[index];
    let newValue = value;
    if (index !== lastIndex) {
      const objValue = object[key];
      newValue = isObject(objValue) || Array.isArray(objValue) ? objValue : !isNaN(+tempPath[index + 1]) ? [] : {};
    }
    if (PROTOTYPE_KEYWORDS.includes(key)) {
      return;
    }
    object[key] = newValue;
    object = object[key];
  }
};
var HookFormControlContext = React.createContext(null);
HookFormControlContext.displayName = "HookFormControlContext";
var useFormControlContext = () => React.useContext(HookFormControlContext);
var getProxyFormState = (formState, control, localProxyFormState, isRoot = true) => {
  const result = {};
  for (const key in formState) {
    Object.defineProperty(result, key, {
      get: () => {
        const _key = key;
        if (control._proxyFormState[_key] !== VALIDATION_MODE.all) {
          control._proxyFormState[_key] = !isRoot || VALIDATION_MODE.all;
        }
        localProxyFormState && (localProxyFormState[_key] = true);
        return formState[_key];
      }
    });
  }
  return result;
};
var useIsomorphicLayoutEffect = typeof window !== "undefined" ? React.useLayoutEffect : React.useEffect;
var isPlainObject = (tempObject) => {
  const prototypeCopy = tempObject.constructor && tempObject.constructor.prototype;
  return isObject(prototypeCopy) && prototypeCopy.hasOwnProperty("isPrototypeOf");
};
var isPrimitive = (value) => isNullOrUndefined(value) || !isObjectType(value);
var isEmptyObjectWithCustomPrototype = (object, keys) => keys.length === 0 && !Array.isArray(object) && Object.getPrototypeOf(object) !== null && !isPlainObject(object);
function deepEqual(object1, object2, isErrorObject = false, visited = /* @__PURE__ */ new WeakMap()) {
  if (object1 === object2) {
    return true;
  }
  if (isPrimitive(object1) || isPrimitive(object2)) {
    return Object.is(object1, object2);
  }
  if (isDateObject(object1) && isDateObject(object2)) {
    return Object.is(object1.getTime(), object2.getTime());
  }
  if (Array.isArray(object1) && Array.isArray(object2) && object1.length !== object2.length) {
    return false;
  }
  const keys1 = Object.keys(object1);
  const keys2 = Object.keys(object2);
  if (keys1.length !== keys2.length) {
    return false;
  }
  if (isEmptyObjectWithCustomPrototype(object1, keys1) || isEmptyObjectWithCustomPrototype(object2, keys2)) {
    return Object.is(object1, object2);
  }
  if (!keys1.length && Array.isArray(object1) !== Array.isArray(object2)) {
    return false;
  }
  const visitedPairs = visited.get(object1);
  if (visitedPairs && visitedPairs.has(object2)) {
    return true;
  }
  if (visitedPairs) {
    visitedPairs.add(object2);
  } else {
    const ws = /* @__PURE__ */ new WeakSet();
    ws.add(object2);
    visited.set(object1, ws);
  }
  for (const key of keys1) {
    const val1 = object1[key];
    if (!Object.prototype.hasOwnProperty.call(object2, key)) {
      return false;
    }
    if (!isErrorObject || key !== "ref") {
      const val2 = object2[key];
      if (isDateObject(val1) && isDateObject(val2) || (isObject(val1) || Array.isArray(val1)) && (isObject(val2) || Array.isArray(val2)) ? !deepEqual(val1, val2, isErrorObject, visited) : !Object.is(val1, val2)) {
        return false;
      }
    }
  }
  return true;
}
var cloneFormState = ({ defaultValues, ...formState }) => ({ ...cloneObject(formState), defaultValues });
function useResyncOnReconnect(getInitialValue, clone = cloneObject) {
  const _connected = React.useRef(false);
  const _initialized = React.useRef(false);
  const _prevValue = React.useRef(void 0);
  const _renderCount = React.useRef(0);
  _renderCount.current++;
  if (!_initialized.current && getInitialValue) {
    _initialized.current = true;
    _prevValue.current = clone(getInitialValue());
  }
  const resyncIfNeeded = React.useCallback((enabled, getCurrentValue, setValue) => {
    if (enabled && (_connected.current || _initialized.current && _renderCount.current > 1)) {
      const currentValue = getCurrentValue();
      if (!deepEqual(_prevValue.current, currentValue)) {
        setValue(currentValue);
      }
    }
    _connected.current = true;
  }, []);
  const snapshot = React.useCallback((enabled, getCurrentValue) => {
    if (enabled) {
      _prevValue.current = clone(getCurrentValue());
    }
  }, [clone]);
  return { resyncIfNeeded, snapshot };
}
function useFormState(props) {
  const formControl = useFormControlContext();
  const { control = formControl, disabled, name, exact } = props || {};
  const getCurrentFormState = () => ({
    ...control._formState,
    defaultValues: control._defaultValues
  });
  const [formState, updateFormState] = React.useState(getCurrentFormState);
  const _localProxyFormState = React.useRef({
    isDirty: false,
    isLoading: false,
    dirtyFields: false,
    touchedFields: false,
    validatingFields: false,
    isValidating: false,
    isValid: false,
    errors: false
  });
  const { resyncIfNeeded, snapshot } = useResyncOnReconnect(getCurrentFormState, cloneFormState);
  useIsomorphicLayoutEffect(() => {
    resyncIfNeeded(!disabled, getCurrentFormState, updateFormState);
    const unsubscribe = control._subscribe({
      name,
      formState: _localProxyFormState.current,
      exact,
      callback: (formState2) => {
        !disabled && updateFormState({
          ...control._formState,
          ...formState2,
          defaultValues: control._defaultValues
        });
      }
    });
    return () => {
      unsubscribe();
      snapshot(!disabled, getCurrentFormState);
    };
  }, [control, name, disabled, exact, resyncIfNeeded, snapshot]);
  React.useEffect(() => {
    _localProxyFormState.current.isValid && control._setValid(true);
  }, [control]);
  return React.useMemo(() => getProxyFormState(formState, control, _localProxyFormState.current, false), [formState, control]);
}
var isString = (value) => typeof value === "string";
var generateWatchOutput = (names, _names, formValues, isGlobal, defaultValue) => {
  if (isString(names)) {
    isGlobal && _names.watch.add(names);
    return get(formValues, names, defaultValue);
  }
  if (Array.isArray(names)) {
    return names.map((fieldName) => (isGlobal && _names.watch.add(fieldName), get(formValues, fieldName, get(defaultValue, fieldName))));
  }
  isGlobal && (_names.watchAll = true);
  return formValues;
};
function useWatch(props) {
  const formControl = useFormControlContext();
  const { control = formControl, name, defaultValue, disabled, exact, compute } = props || {};
  const _defaultValue = React.useRef(defaultValue);
  const _compute = React.useRef(compute);
  const _prevControl = React.useRef(control);
  const _prevName = React.useRef(name);
  _compute.current = compute;
  const getInitialOutput = () => {
    const defaultValue2 = control._getWatch(name, _defaultValue.current);
    return _compute.current ? _compute.current(defaultValue2) : defaultValue2;
  };
  const [value, updateValue] = React.useState(getInitialOutput);
  const _computeFormValues = React.useRef(value);
  const getCurrentOutput = React.useCallback((values) => {
    const formValues = generateWatchOutput(name, control._names, values || control._formValues, false, _defaultValue.current);
    return _compute.current ? _compute.current(formValues) : formValues;
  }, [control._formValues, control._names, name]);
  const refreshValue = React.useCallback((values) => {
    if (!disabled) {
      const formValues = generateWatchOutput(name, control._names, values || control._formValues, false, _defaultValue.current);
      if (_compute.current) {
        const computedFormValues = _compute.current(formValues);
        if (!deepEqual(computedFormValues, _computeFormValues.current)) {
          updateValue(computedFormValues);
          _computeFormValues.current = computedFormValues;
        }
      } else {
        updateValue(formValues);
        _computeFormValues.current = formValues;
      }
    }
  }, [control._formValues, control._names, disabled, name]);
  const { resyncIfNeeded, snapshot } = useResyncOnReconnect(getInitialOutput);
  const _refreshValue = React.useRef(refreshValue);
  _refreshValue.current = refreshValue;
  const _getCurrentOutput = React.useRef(getCurrentOutput);
  _getCurrentOutput.current = getCurrentOutput;
  useIsomorphicLayoutEffect(() => {
    if (_prevControl.current !== control || !deepEqual(_prevName.current, name)) {
      _prevControl.current = control;
      _prevName.current = name;
      _refreshValue.current();
    } else {
      resyncIfNeeded(!disabled, () => _getCurrentOutput.current(), (currentValue) => {
        const nextValue = cloneObject(currentValue);
        updateValue(nextValue);
        _computeFormValues.current = nextValue;
      });
    }
    const unsubscribe = control._subscribe({
      name,
      formState: {
        values: true
      },
      exact,
      callback: (formState) => {
        _refreshValue.current(formState.values);
      }
    });
    return () => {
      unsubscribe();
      snapshot(!disabled, () => _getCurrentOutput.current());
    };
  }, [control, exact, name, disabled, resyncIfNeeded, snapshot]);
  React.useEffect(() => control._removeUnmounted());
  const controlChanged = _prevControl.current !== control;
  const prevName = _prevName.current;
  const nameChanged = React.useMemo(() => !controlChanged && !deepEqual(prevName, name), [controlChanged, name, prevName]);
  if (controlChanged || nameChanged) {
    _defaultValue.current = defaultValue;
  }
  const shouldReturnImmediate = !disabled && (controlChanged || nameChanged);
  return shouldReturnImmediate ? getCurrentOutput() : value;
}
function useController(props) {
  const formControl = useFormControlContext();
  const { name, disabled, control = formControl, shouldUnregister, defaultValue, exact = true } = props;
  const isArrayField = isNameInFieldArray(control._names.array, name);
  const defaultValueMemo = React.useMemo(() => {
    const resolved = get(control._formValues, name, get(control._defaultValues, name, defaultValue));
    return isUndefined(resolved) ? getNullAncestorValue(control, name) : resolved;
  }, [control, name, defaultValue]);
  const value = useWatch({
    control,
    name,
    defaultValue: defaultValueMemo,
    exact
  });
  const formState = useFormState({
    control,
    name,
    exact
  });
  const _props = React.useRef(props);
  const _shouldUnregister = React.useRef(shouldUnregister);
  const _proxyRef = React.useRef(null);
  const _registeredName = React.useRef(void 0);
  const _registerProps = React.useRef(control.register(name, {
    ...props.rules,
    value: _registeredName.current === name && !get(control._fields, name) ? void 0 : value,
    ...isBoolean(props.disabled) ? { disabled: props.disabled } : {}
  }));
  _registeredName.current = name;
  _props.current = props;
  const fieldState = React.useMemo(() => Object.defineProperties({}, {
    invalid: {
      enumerable: true,
      get: () => !!get(formState.errors, name)
    },
    isDirty: {
      enumerable: true,
      get: () => !!get(formState.dirtyFields, name)
    },
    isTouched: {
      enumerable: true,
      get: () => !!get(formState.touchedFields, name)
    },
    isValidating: {
      enumerable: true,
      get: () => !!get(formState.validatingFields, name)
    },
    error: {
      enumerable: true,
      get: () => get(formState.errors, name)
    }
  }), [formState, name]);
  const onChange = React.useCallback((event) => {
    const value2 = getEventValue(event);
    if (!get(control._fields, name)) {
      _registerProps.current = control.register(name, {
        ..._props.current.rules,
        value: value2
      });
    }
    return _registerProps.current.onChange({
      target: {
        value: getEventValue(event),
        name
      },
      type: EVENTS.CHANGE
    });
  }, [name, control]);
  const onBlur = React.useCallback(() => {
    const value2 = get(control._formValues, name);
    if (!get(control._fields, name)) {
      _registerProps.current = control.register(name, {
        ..._props.current.rules,
        value: value2
      });
    }
    return _registerProps.current.onBlur({
      target: {
        value: value2,
        name
      },
      type: EVENTS.BLUR
    });
  }, [name, control]);
  const ref = React.useCallback((elm) => {
    if (elm) {
      _proxyRef.current = {
        focus: () => isFunction(elm.focus) && elm.focus(),
        select: () => isFunction(elm.select) && elm.select(),
        setCustomValidity: (message) => isFunction(elm.setCustomValidity) && elm.setCustomValidity(message),
        reportValidity: () => isFunction(elm.reportValidity) && elm.reportValidity()
      };
    }
    const field2 = get(control._fields, name);
    if (field2 && field2._f && elm) {
      field2._f.ref = _proxyRef.current;
      field2._f._c = true;
    }
  }, [control._fields, name]);
  const field = React.useMemo(() => ({
    name,
    value,
    ...isBoolean(disabled) || formState.disabled ? { disabled: formState.disabled || disabled } : {},
    onChange,
    onBlur,
    ref
  }), [name, disabled, formState.disabled, onChange, onBlur, ref, value]);
  React.useEffect(() => {
    _shouldUnregister.current = shouldUnregister;
    if (control._options.shouldUnregister || shouldUnregister) {
      const value2 = cloneObject(get(shouldUnregister ? control._defaultValues : control._options.values || control._defaultValues, name, get(control._options.defaultValues, name, _props.current.defaultValue)));
      set(control._defaultValues, name, value2);
      if (isUndefined(get(control._formValues, name))) {
        set(control._formValues, name, value2);
      }
    } else if (!isArrayField && !isUndefined(_props.current.defaultValue) && isUndefined(get(control._defaultValues, name))) {
      set(control._defaultValues, name, cloneObject(_props.current.defaultValue));
      control._subjects.state.next({
        defaultValues: control._defaultValues
      });
    }
  }, [control, name, isArrayField, shouldUnregister]);
  React.useEffect(() => {
    _registerProps.current = control.register(name, {
      ..._props.current.rules,
      ...isBoolean(_props.current.disabled) ? { disabled: _props.current.disabled } : {}
    });
    const updateMounted = (name2, value2) => {
      const field2 = get(control._fields, name2);
      if (field2 && field2._f) {
        field2._f.mount = value2;
        field2._f._c = value2;
      }
    };
    updateMounted(name, true);
    !isArrayField && control.register(name, {
      ..._props.current.rules,
      ...isBoolean(_props.current.disabled) ? { disabled: _props.current.disabled } : {}
    });
    if (_proxyRef.current) {
      const field2 = get(control._fields, name);
      if (field2 && field2._f) {
        field2._f.ref = _proxyRef.current;
      }
    }
    return () => {
      const shouldUnregisterField = control._options.shouldUnregister || _shouldUnregister.current;
      (isArrayField ? shouldUnregisterField && !control._state.action : shouldUnregisterField) ? control.unregister(name) : updateMounted(name, false);
    };
  }, [name, control, isArrayField]);
  React.useEffect(() => {
    control._setDisabledField({
      disabled,
      name
    });
  }, [disabled, name, control]);
  return React.useMemo(() => ({
    field,
    formState,
    fieldState
  }), [field, formState, fieldState]);
}
var Controller = (props) => props.render(useController(props));
var ErrorMessage = ({ as, control, name, render }) => {
  const { errors } = useFormState({
    control,
    name
  });
  const error = get(errors, name);
  if (!error) {
    return null;
  }
  const message = error.message || "";
  if (render) {
    return render({ message, messages: error.types });
  }
  return React.createElement(as || React.Fragment, null, message);
};
var generateId = () => typeof crypto !== "undefined" && crypto.randomUUID ? crypto.randomUUID() : "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, (c) => {
  const r = Math.random() * 16 | 0;
  return (c === "x" ? r : r & 3 | 8).toString(16);
});
var getFocusFieldName = (name, index, options = {}) => options.shouldFocus || isUndefined(options.shouldFocus) ? options.focusName || `${name}.${isUndefined(options.focusIndex) ? index : options.focusIndex}.` : "";
var getValidationModes = (mode) => ({
  isOnSubmit: !mode || mode === VALIDATION_MODE.onSubmit,
  isOnBlur: mode === VALIDATION_MODE.onBlur,
  isOnChange: mode === VALIDATION_MODE.onChange,
  isOnAll: mode === VALIDATION_MODE.all,
  isOnTouch: mode === VALIDATION_MODE.onTouched
});
var isWatched = (name, _names, isBlurEvent) => {
  if (isBlurEvent)
    return false;
  if (_names.watchAll || _names.watch.has(name))
    return true;
  for (const watchName of _names.watch) {
    if (name.startsWith(watchName) && name.charAt(watchName.length) === ".")
      return true;
  }
  return false;
};
var iterateFieldsByAction = (fields, action, fieldsNames) => {
  var _a;
  for (const key of fieldsNames || Object.keys(fields)) {
    if (key === "_f") {
      continue;
    }
    const field = fieldsNames ? get(fields, key) : fields[key];
    if (field) {
      const { _f } = field;
      if (_f) {
        const groupRef = (_a = _f.refs) === null || _a === void 0 ? void 0 : _a.find((ref) => !ref.disabled);
        if (groupRef && action(groupRef, _f.name)) {
          return true;
        } else if (_f.ref && action(_f.ref, _f.name)) {
          return true;
        } else {
          if (iterateFieldsByAction(field, action)) {
            return true;
          }
        }
      } else if (isObject(field) || Array.isArray(field)) {
        if (iterateFieldsByAction(field, action)) {
          return true;
        }
      }
    }
  }
  return;
};
var updateFieldArrayRootError = (errors, error, name) => {
  const existingErrors = get(errors, name);
  const fieldArrayErrors = Array.isArray(existingErrors) ? existingErrors : [];
  set(fieldArrayErrors, ROOT_ERROR_TYPE, get(error[name], ROOT_ERROR_TYPE, error[name]));
  set(errors, name, fieldArrayErrors);
  return errors;
};
var isEmptyObject = (value) => isObject(value) && !Object.keys(value).length;
var isHTMLElement = (value) => {
  if (!isWeb) {
    return false;
  }
  const owner = value ? value.ownerDocument : 0;
  return value instanceof (owner && owner.defaultView ? owner.defaultView.HTMLElement : HTMLElement);
};
var isRadioInput = (element) => element.type === "radio";
var isRegex = (value) => value instanceof RegExp;
var appendErrors = (name, validateAllFieldCriteria, errors, type, message) => validateAllFieldCriteria ? {
  ...errors[name],
  types: {
    ...errors[name] && errors[name].types ? errors[name].types : {},
    [type]: message || true
  }
} : {};
var defaultResult = {
  value: false,
  isValid: false
};
var validResult = { value: true, isValid: true };
var getCheckboxValue = (options) => {
  if (!Array.isArray(options)) {
    return defaultResult;
  }
  if (options.length > 1) {
    const values = options.filter((option2) => option2 && option2.checked && !option2.disabled).map((option2) => option2.value);
    return { value: values, isValid: !!values.length };
  }
  const option = options[0];
  if (!option || !option.checked || option.disabled) {
    return defaultResult;
  }
  if (!option.attributes || !("value" in option.attributes)) {
    return validResult;
  }
  return isUndefined(option.value) || option.value === "" ? validResult : { value: option.value, isValid: true };
};
var defaultReturn = {
  isValid: false,
  value: null
};
var getRadioValue = (options) => Array.isArray(options) ? options.reduce((previous, option) => option && option.checked && !option.disabled ? {
  isValid: true,
  value: option.value
} : previous, defaultReturn) : defaultReturn;
function getValidateError(result, ref, type = "validate") {
  if (isString(result) || Array.isArray(result) && !!result.length && result.every(isString) || isBoolean(result) && !result) {
    return {
      type,
      message: isString(result) ? result : "",
      ref
    };
  }
}
var getValueAndMessage = (validationData) => isObject(validationData) && !isRegex(validationData) ? validationData : {
  value: validationData,
  message: ""
};
var validateField = async (field, disabledFieldNames, formValues, validateAllFieldCriteria, shouldUseNativeValidation, isFieldArray) => {
  const { ref, refs, required, maxLength, minLength, min, max, pattern, validate, name, valueAsNumber, mount, _c } = field._f;
  const inputValue = get(formValues, name);
  if (!mount || disabledFieldNames.has(name)) {
    return {};
  }
  const inputRef = refs ? refs[0] : ref;
  const setCustomValidity = (message) => {
    if (shouldUseNativeValidation && inputRef.reportValidity) {
      const validityMessage = isBoolean(message) ? "" : message || "";
      if (refs) {
        refs.forEach((ref2) => isFunction(ref2.setCustomValidity) && ref2.setCustomValidity(validityMessage));
      } else {
        inputRef.setCustomValidity(validityMessage);
      }
      inputRef.reportValidity();
    }
  };
  const error = {};
  const isRadio = !_c && isRadioInput(ref);
  const isCheckBox = !_c && isCheckBoxInput(ref);
  const isRadioOrCheckbox2 = isRadio || isCheckBox;
  const isEmpty = (valueAsNumber || isFileInput(ref)) && isUndefined(ref.value) && isUndefined(inputValue) || isHTMLElement(ref) && ref.value === "" && !_c || inputValue === "" || Array.isArray(inputValue) && !inputValue.length;
  const appendErrorsCurry = appendErrors.bind(null, name, validateAllFieldCriteria, error);
  const getMinMaxMessage = (exceedMax, maxLengthMessage, minLengthMessage, maxType = INPUT_VALIDATION_RULES.maxLength, minType = INPUT_VALIDATION_RULES.minLength) => {
    const message = exceedMax ? maxLengthMessage : minLengthMessage;
    error[name] = {
      type: exceedMax ? maxType : minType,
      message,
      ref,
      ...appendErrorsCurry(exceedMax ? maxType : minType, message)
    };
  };
  if (isFieldArray ? !Array.isArray(inputValue) || !inputValue.length : required && (!isRadioOrCheckbox2 && (isEmpty || isNullOrUndefined(inputValue)) || isBoolean(inputValue) && !inputValue || isCheckBox && !getCheckboxValue(refs).isValid || isRadio && !getRadioValue(refs).isValid)) {
    const { value, message } = isString(required) ? { value: !!required, message: required } : getValueAndMessage(required);
    if (value) {
      error[name] = {
        type: INPUT_VALIDATION_RULES.required,
        message,
        ref: inputRef,
        ...appendErrorsCurry(INPUT_VALIDATION_RULES.required, message)
      };
      if (!validateAllFieldCriteria) {
        setCustomValidity(message);
        return error;
      }
    }
  }
  if (!isEmpty && (!isNullOrUndefined(min) || !isNullOrUndefined(max))) {
    let exceedMax;
    let exceedMin;
    const maxOutput = getValueAndMessage(max);
    const minOutput = getValueAndMessage(min);
    if (!isNullOrUndefined(inputValue) && !isDateObject(inputValue) && !isNaN(inputValue)) {
      const valueNumber = !_c && ref.valueAsNumber || (inputValue ? +inputValue : inputValue);
      if (!isNullOrUndefined(maxOutput.value)) {
        exceedMax = valueNumber > maxOutput.value;
      }
      if (!isNullOrUndefined(minOutput.value)) {
        exceedMin = valueNumber < minOutput.value;
      }
    } else {
      const valueDate = !_c && ref.valueAsDate || new Date(inputValue);
      const convertTimeToDate = (time) => /* @__PURE__ */ new Date((/* @__PURE__ */ new Date()).toDateString() + " " + time);
      const isTime = ref.type == "time";
      const isWeek = ref.type == "week";
      if (isString(maxOutput.value) && inputValue) {
        exceedMax = isTime ? convertTimeToDate(inputValue) > convertTimeToDate(maxOutput.value) : isWeek ? inputValue > maxOutput.value : valueDate > new Date(maxOutput.value);
      }
      if (isString(minOutput.value) && inputValue) {
        exceedMin = isTime ? convertTimeToDate(inputValue) < convertTimeToDate(minOutput.value) : isWeek ? inputValue < minOutput.value : valueDate < new Date(minOutput.value);
      }
    }
    if (exceedMax || exceedMin) {
      getMinMaxMessage(!!exceedMax, maxOutput.message, minOutput.message, INPUT_VALIDATION_RULES.max, INPUT_VALIDATION_RULES.min);
      if (!validateAllFieldCriteria) {
        setCustomValidity(error[name].message);
        return error;
      }
    }
  }
  if ((!isNullOrUndefined(maxLength) || !isNullOrUndefined(minLength)) && !isEmpty && (isString(inputValue) || isFieldArray && Array.isArray(inputValue))) {
    const maxLengthOutput = getValueAndMessage(maxLength);
    const minLengthOutput = getValueAndMessage(minLength);
    const exceedMax = !isNullOrUndefined(maxLengthOutput.value) && inputValue.length > +maxLengthOutput.value;
    const exceedMin = !isNullOrUndefined(minLengthOutput.value) && inputValue.length < +minLengthOutput.value;
    if (exceedMax || exceedMin) {
      getMinMaxMessage(exceedMax, maxLengthOutput.message, minLengthOutput.message);
      if (!validateAllFieldCriteria) {
        setCustomValidity(error[name].message);
        return error;
      }
    }
  }
  if (pattern && !isEmpty && isString(inputValue)) {
    const { value: patternValue, message } = getValueAndMessage(pattern);
    if (isRegex(patternValue) && !inputValue.match(patternValue)) {
      error[name] = {
        type: INPUT_VALIDATION_RULES.pattern,
        message,
        ref,
        ...appendErrorsCurry(INPUT_VALIDATION_RULES.pattern, message)
      };
      if (!validateAllFieldCriteria) {
        setCustomValidity(message);
        return error;
      }
    }
  }
  if (validate) {
    if (isFunction(validate)) {
      const result = await validate(inputValue, formValues);
      const validateError = getValidateError(result, inputRef);
      if (validateError) {
        error[name] = {
          ...validateError,
          ...appendErrorsCurry(INPUT_VALIDATION_RULES.validate, validateError.message)
        };
        if (!validateAllFieldCriteria) {
          setCustomValidity(validateError.message);
          return error;
        }
      }
    } else if (isObject(validate)) {
      let validationResult = {};
      for (const key in validate) {
        if (!isEmptyObject(validationResult) && !validateAllFieldCriteria) {
          break;
        }
        const validateError = getValidateError(await validate[key](inputValue, formValues), inputRef, key);
        if (validateError) {
          validationResult = {
            ...validateError,
            ...appendErrorsCurry(key, validateError.message)
          };
          if (!validateAllFieldCriteria) {
            setCustomValidity(validateError.message);
          }
          if (validateAllFieldCriteria) {
            error[name] = validationResult;
          }
        }
      }
      if (!isEmptyObject(validationResult)) {
        error[name] = {
          ref: inputRef,
          ...validationResult
        };
        if (!validateAllFieldCriteria) {
          return error;
        }
      }
    }
  }
  const fieldError = error[name];
  setCustomValidity(fieldError ? fieldError.message : true);
  return error;
};
var convertToArrayPayload = (value) => Array.isArray(value) ? value : [value];
var appendAt = (data, value) => [
  ...data,
  ...convertToArrayPayload(value)
];
var fillEmptyArray = (value) => Array.isArray(value) ? value.map(() => void 0) : void 0;
function insert(data, index, value) {
  return [
    ...data.slice(0, index),
    ...convertToArrayPayload(value),
    ...data.slice(index)
  ];
}
var moveArrayAt = (data, from, to) => {
  if (!Array.isArray(data)) {
    return [];
  }
  if (isUndefined(data[to])) {
    data[to] = void 0;
  }
  data.splice(to, 0, data.splice(from, 1)[0]);
  return data;
};
var prependAt = (data, value) => [
  ...convertToArrayPayload(value),
  ...convertToArrayPayload(data)
];
var compact = (value) => Array.isArray(value) ? value.filter(Boolean) : [];
function removeAtIndexes(data, indexes) {
  let i = 0;
  const temp = [...data];
  for (const index of indexes) {
    temp.splice(index - i, 1);
    i++;
  }
  return compact(temp).length ? temp : [];
}
var removeArrayAt = (data, index) => isUndefined(index) ? [] : removeAtIndexes(data, [...new Set(convertToArrayPayload(index))].sort((a, b) => a - b));
var swapArrayAt = (data, indexA, indexB) => {
  [data[indexA], data[indexB]] = [data[indexB], data[indexA]];
};
function baseGet(object, updatePath) {
  const length = updatePath.length - 1;
  let index = 0;
  while (index < length) {
    if (isNullOrUndefined(object)) {
      object = void 0;
      break;
    }
    object = object[updatePath[index]];
    index++;
  }
  return object;
}
function isEmptyArray(obj) {
  for (const key in obj) {
    if (obj.hasOwnProperty(key) && !isUndefined(obj[key])) {
      return false;
    }
  }
  return true;
}
function unset(object, path) {
  if (!isObject(object) && !Array.isArray(object)) {
    return object;
  }
  if (isString(path) && Object.prototype.hasOwnProperty.call(object, path)) {
    delete object[path];
    return object;
  }
  const paths = Array.isArray(path) ? path : isKey(path) ? [path] : stringToPath(path);
  if (paths.some((segment) => PROTOTYPE_KEYWORDS.includes(String(segment)))) {
    return object;
  }
  const childObject = paths.length === 1 ? object : baseGet(object, paths);
  const index = paths.length - 1;
  const key = paths[index];
  if (childObject && (isObject(childObject) || Array.isArray(childObject))) {
    delete childObject[key];
  }
  if (index !== 0 && (isObject(childObject) && isEmptyObject(childObject) || Array.isArray(childObject) && isEmptyArray(childObject))) {
    unset(object, paths.slice(0, -1));
  }
  return object;
}
var updateAt = (fieldValues, index, value) => {
  fieldValues[index] = value;
  return fieldValues;
};
function useFieldArray(props) {
  const formControl = useFormControlContext();
  const { control = formControl, name, keyName = "id", disabled, shouldUnregister, rules } = props;
  const getCurrentFieldArray = () => control._getFieldArray(name);
  const [fields, setFields] = React.useState(getCurrentFieldArray);
  const ids = React.useRef(control._getFieldArray(name).map(generateId));
  const _actioned = React.useRef(false);
  const { resyncIfNeeded, snapshot } = useResyncOnReconnect(getCurrentFieldArray);
  const _prevControl = React.useRef(control);
  const _prevName = React.useRef(name);
  const _rules = React.useRef({ control, name, rules });
  if (!disabled) {
    control._names.array.add(name);
  }
  React.useMemo(() => {
    if (!disabled && fields) {
      if (rules || _rules.current.rules && _rules.current.control === control && _rules.current.name === name) {
        control.register(name, rules);
      }
      _rules.current = { control, name, rules };
    }
  }, [control, name, fields, rules, disabled]);
  useIsomorphicLayoutEffect(() => {
    if (disabled) {
      return;
    }
    if (_prevControl.current === control && _prevName.current === name) {
      resyncIfNeeded(true, getCurrentFieldArray, (fieldValues) => {
        setFields(fieldValues);
        ids.current = fieldValues.map(generateId);
      });
    } else {
      _prevControl.current = control;
      _prevName.current = name;
      const fieldValues = getCurrentFieldArray();
      if (!deepEqual(fields, fieldValues)) {
        setFields(fieldValues);
        ids.current = fieldValues.map(generateId);
      }
      snapshot(true, getCurrentFieldArray);
    }
    const unsubscribe = control._subjects.array.subscribe({
      next: ({ values, name: fieldArrayName }) => {
        if (fieldArrayName === name || !fieldArrayName) {
          const fieldValues = get(values, name);
          if (Array.isArray(fieldValues)) {
            setFields(fieldValues);
            ids.current = fieldValues.map(generateId);
          } else if (!fieldArrayName) {
            setFields([]);
            ids.current = [];
          }
        }
      }
    }).unsubscribe;
    return () => {
      unsubscribe();
      snapshot(true, getCurrentFieldArray);
    };
  }, [control, name, disabled, resyncIfNeeded, snapshot]);
  const updateValues = React.useCallback((updatedFieldArrayValues) => {
    _actioned.current = true;
    control._setFieldArray(name, updatedFieldArrayValues);
  }, [control, name]);
  const append = (value, options) => {
    if (disabled) {
      return;
    }
    const appendValue = convertToArrayPayload(cloneObject(value));
    const updatedFieldArrayValues = appendAt(control._getFieldArray(name), appendValue);
    control._names.focus = getFocusFieldName(name, updatedFieldArrayValues.length - 1, options);
    ids.current = appendAt(ids.current, appendValue.map(generateId));
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    control._setFieldArray(name, updatedFieldArrayValues, appendAt, {
      argA: fillEmptyArray(value)
    });
  };
  const prepend = (value, options) => {
    if (disabled) {
      return;
    }
    const prependValue = convertToArrayPayload(cloneObject(value));
    const updatedFieldArrayValues = prependAt(control._getFieldArray(name), prependValue);
    control._names.focus = getFocusFieldName(name, 0, options);
    ids.current = prependAt(ids.current, prependValue.map(generateId));
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    control._setFieldArray(name, updatedFieldArrayValues, prependAt, {
      argA: fillEmptyArray(value)
    });
  };
  const remove = (index) => {
    if (disabled) {
      return;
    }
    const updatedFieldArrayValues = removeArrayAt(control._getFieldArray(name), index);
    ids.current = removeArrayAt(ids.current, index);
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    !Array.isArray(get(control._fields, name)) && set(control._fields, name, void 0);
    control._setFieldArray(name, updatedFieldArrayValues, removeArrayAt, {
      argA: index
    });
  };
  const insert$1 = (index, value, options) => {
    if (disabled) {
      return;
    }
    const insertValue = convertToArrayPayload(cloneObject(value));
    const updatedFieldArrayValues = insert(control._getFieldArray(name), index, insertValue);
    control._names.focus = getFocusFieldName(name, index, options);
    ids.current = insert(ids.current, index, insertValue.map(generateId));
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    control._setFieldArray(name, updatedFieldArrayValues, insert, {
      argA: index,
      argB: fillEmptyArray(value)
    });
  };
  const swap = (indexA, indexB) => {
    if (disabled) {
      return;
    }
    const updatedFieldArrayValues = control._getFieldArray(name);
    swapArrayAt(updatedFieldArrayValues, indexA, indexB);
    swapArrayAt(ids.current, indexA, indexB);
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    control._setFieldArray(name, updatedFieldArrayValues, swapArrayAt, {
      argA: indexA,
      argB: indexB
    }, false);
  };
  const move = (from, to) => {
    if (disabled) {
      return;
    }
    const updatedFieldArrayValues = control._getFieldArray(name);
    moveArrayAt(updatedFieldArrayValues, from, to);
    moveArrayAt(ids.current, from, to);
    updateValues(updatedFieldArrayValues);
    setFields(updatedFieldArrayValues);
    control._setFieldArray(name, updatedFieldArrayValues, moveArrayAt, {
      argA: from,
      argB: to
    }, false);
  };
  const update = (index, value) => {
    if (disabled) {
      return;
    }
    const updateValue = cloneObject(value);
    const updatedFieldArrayValues = updateAt(control._getFieldArray(name), index, updateValue);
    ids.current = [...updatedFieldArrayValues].map((item, i) => !item || i === index ? generateId() : ids.current[i]);
    updateValues(updatedFieldArrayValues);
    setFields([...updatedFieldArrayValues]);
    control._setFieldArray(name, updatedFieldArrayValues, updateAt, {
      argA: index,
      argB: fillEmptyArray(value)
    });
  };
  const replace = (value) => {
    if (disabled) {
      return;
    }
    const updatedFieldArrayValues = convertToArrayPayload(cloneObject(value));
    ids.current = updatedFieldArrayValues.map(generateId);
    updateValues([...updatedFieldArrayValues]);
    setFields([...updatedFieldArrayValues]);
    control._setFieldArray(name, [...updatedFieldArrayValues], (data) => Array.isArray(data) ? data.slice(0, updatedFieldArrayValues.length) : data, {});
  };
  React.useEffect(() => {
    if (disabled) {
      control._state.actionArrayLengths.delete(name);
      return;
    }
    control._state.action = false;
    control._state.actionArrayLengths.delete(name);
    isWatched(name, control._names) && control._subjects.state.next({
      ...control._formState
    });
    const validationModes = getValidationModes(control._options.mode);
    const reValidateModes = getValidationModes(control._options.reValidateMode);
    if (_actioned.current && (!validationModes.isOnSubmit || control._formState.isSubmitted) && !(control._formState.isSubmitted && reValidateModes.isOnSubmit) && (!validationModes.isOnBlur || control._formState.isSubmitted && reValidateModes.isOnChange)) {
      const fieldArrayValues = get(control._formValues, name);
      if (control._options.resolver) {
        control._runSchema([name]).then((result) => {
          control._updateIsValidating([name]);
          if (fieldArrayValues !== get(control._formValues, name)) {
            return;
          }
          const error = get(result.errors, name);
          if (!deepEqual(get(control._formState.errors, name), error, true)) {
            if (error) {
              isObject(error) && !Object.keys(error).some((key) => !Number.isNaN(+key)) ? updateFieldArrayRootError(control._formState.errors, { [name]: error }, name) : set(control._formState.errors, name, error);
            } else {
              unset(control._formState.errors, name);
            }
            control._formState.errors = { ...control._formState.errors };
            control._subjects.state.next({
              errors: control._formState.errors
            });
          }
        });
      } else {
        const field = get(control._fields, name);
        if (field && field._f) {
          validateField(field, control._names.disabled, control._formValues, control._options.criteriaMode === VALIDATION_MODE.all, control._options.shouldUseNativeValidation, true).then((error) => {
            if (fieldArrayValues !== get(control._formValues, name)) {
              return;
            }
            if (!isEmptyObject(error)) {
              updateFieldArrayRootError(control._formState.errors, error, name);
              control._formState.errors = { ...control._formState.errors };
              control._subjects.state.next({
                errors: control._formState.errors
              });
            } else {
              const existingError = get(control._formState.errors, name);
              if (existingError && existingError[ROOT_ERROR_TYPE]) {
                unset(control._formState.errors, `${name}.${ROOT_ERROR_TYPE}`);
                control._formState.errors = { ...control._formState.errors };
                control._subjects.state.next({
                  errors: control._formState.errors
                });
              }
            }
          });
        }
      }
    }
    _actioned.current && control._subjects.state.next({
      name,
      values: cloneObject(control._formValues)
    });
    control._names.focus && iterateFieldsByAction(control._fields, (ref, key) => {
      if (control._names.focus && key.startsWith(control._names.focus) && ref.focus) {
        ref.focus();
        return 1;
      }
      return;
    });
    control._names.focus = "";
    control._setValid();
    _actioned.current = false;
  }, [fields, name, control, disabled]);
  React.useEffect(() => {
    if (!disabled) {
      isUndefined(get(control._formValues, name)) && control._setFieldArray(name);
    }
    return () => {
      control._state.actionArrayLengths.delete(name);
      control._state.actionArrayLengths.size || (control._state.action = false);
      if (disabled) {
        return;
      }
      const shouldKeepFieldArrayValues = !(control._options.shouldUnregister || shouldUnregister);
      const updateMounted = (name2, value) => {
        const field = get(control._fields, name2);
        if (field && field._f) {
          field._f.mount = value;
        }
      };
      if (_actioned.current && shouldKeepFieldArrayValues) {
        control._subjects.state.next({
          name,
          values: cloneObject(control._formValues)
        });
      }
      if (!shouldKeepFieldArrayValues && control._state.action && [...control._state.actionArrayLengths.keys()].some((arrayName) => name.startsWith(arrayName + "."))) {
        return;
      }
      shouldKeepFieldArrayValues ? updateMounted(name, false) : control.unregister(name);
    };
  }, [name, control, keyName, shouldUnregister, disabled]);
  return {
    swap: React.useCallback(swap, [updateValues, name, control, disabled]),
    move: React.useCallback(move, [updateValues, name, control, disabled]),
    prepend: React.useCallback(prepend, [
      updateValues,
      name,
      control,
      disabled
    ]),
    append: React.useCallback(append, [updateValues, name, control, disabled]),
    remove: React.useCallback(remove, [updateValues, name, control, disabled]),
    insert: React.useCallback(insert$1, [updateValues, name, control, disabled]),
    update: React.useCallback(update, [updateValues, name, control, disabled]),
    replace: React.useCallback(replace, [
      updateValues,
      name,
      control,
      disabled
    ]),
    fields: React.useMemo(() => fields.map((field, index) => ({
      ...field,
      ...isBoolean(disabled) ? { disabled } : {},
      [keyName]: ids.current[index] || generateId()
    })), [fields, keyName, disabled])
  };
}
var FieldArray = (props) => props.render(useFieldArray(props));
var isFileLike = (value) => typeof Blob !== "undefined" && value instanceof Blob || typeof File !== "undefined" && value instanceof File;
var isFileListLike = (value) => typeof FileList !== "undefined" && value instanceof FileList;
var flatten = (obj) => {
  const output = {};
  for (const key of Object.keys(obj)) {
    if (isObjectType(obj[key]) && obj[key] !== null && !isDateObject(obj[key]) && !isFileLike(obj[key]) && !isFileListLike(obj[key])) {
      const nested = flatten(obj[key]);
      for (const nestedKey of Object.keys(nested)) {
        output[`${key}.${nestedKey}`] = nested[nestedKey];
      }
    } else {
      output[key] = obj[key];
    }
  }
  return output;
};
function jsonToFormData(json) {
  const result = new FormData();
  const flattenFormValues = flatten(json);
  for (const key in flattenFormValues) {
    const value = flattenFormValues[key];
    if (isUndefined(value)) {
      continue;
    }
    if (typeof FileList !== "undefined" && value instanceof FileList) {
      for (let index = 0; index < value.length; index++) {
        const file = value[index];
        file && result.append(key, file);
      }
      continue;
    }
    result.append(key, value);
  }
  return result;
}
function safeJSONStringify(value) {
  try {
    return JSON.stringify(value);
  } catch (_a) {
    return "";
  }
}
function noop() {
}
var POST_REQUEST = "post";
function defaultValidateStatus(status) {
  return status >= 200 && status < 300;
}
function Form(props) {
  const formControl = useFormControlContext();
  const [mounted, setMounted] = React.useState(false);
  const { control = formControl, onSubmit = noop, children, action, method = POST_REQUEST, headers, encType, onError = noop, render, onSuccess = noop, validateStatus = defaultValidateStatus, ...rest } = props;
  const handleSubmit = React.useMemo(() => {
    return control.handleSubmit(async (data, event) => {
      const formData = jsonToFormData(data);
      const formDataJson = safeJSONStringify(data);
      if (onSubmit) {
        await onSubmit({
          data,
          event,
          method,
          formData,
          formDataJson
        });
      }
      if (isString(action)) {
        try {
          const isMultipart = (value) => /^\s*multipart\/form-data\s*(;|$)/i.test(value);
          const contentType = encType && !isMultipart(encType) ? encType : void 0;
          const hasContentType = (type) => contentType ? type.test(contentType) : headers && Object.entries(headers).some(([name, value]) => name.toLowerCase() === "content-type" && type.test(value)) || encType && type.test(encType);
          let body = formData;
          if (hasContentType(/json/i)) {
            body = formDataJson;
          } else if (hasContentType(/^\s*application\/x-www-form-urlencoded\s*(;|$)/i)) {
            body = new URLSearchParams(Array.from(formData, ([name, value]) => [
              name,
              isString(value) ? value : value.name
            ]));
          } else if (hasContentType(/^\s*text\/plain\s*(;|$)/i)) {
            body = Array.from(formData, ([name, value]) => `${name}=${isString(value) ? value : value.name}\r
`).join("");
          }
          const response = await fetch(action, {
            method,
            headers: {
              ...Object.fromEntries(Object.entries(headers || {}).filter(([name, value]) => name.toLowerCase() !== "content-type" || !contentType && !isMultipart(value))),
              ...contentType && { "Content-Type": contentType }
            },
            body
          });
          if (response && !validateStatus(response.status)) {
            onError({ response });
            return { type: String(response.status) };
          } else {
            onSuccess({ response });
          }
        } catch (error) {
          onError({ error });
          return { type: "" };
        }
      }
      if (isFunction(action)) {
        try {
          await action(formData);
        } catch (error) {
          onError({ error });
          return { type: "" };
        }
      }
      return;
    });
  }, [
    control,
    onSubmit,
    method,
    action,
    headers,
    encType,
    validateStatus,
    onError,
    onSuccess
  ]);
  const submit = React.useCallback(async (event) => {
    const err = await handleSubmit(event);
    if (err && control) {
      control._subjects.state.next({ isSubmitSuccessful: false });
      control.setError("root.server", err);
    }
  }, [handleSubmit, control]);
  React.useEffect(() => {
    setMounted(true);
  }, []);
  if (render) {
    return render({ submit });
  }
  return React.createElement("form", { noValidate: mounted, action, ...!isFunction(action) && { method, encType }, onSubmit: submit, ...rest }, children);
}
var FormState = ({ control, disabled, exact, name, render }) => render(useFormState({ control, name, disabled, exact }));
var FormStateSubscribe = FormState;
var createSubject = () => {
  let _observers = /* @__PURE__ */ new Set();
  let _iterationCount = 0;
  const next = (value) => {
    const observers = _observers;
    _iterationCount++;
    for (const observer of observers) {
      observer.next && observer.next(value);
    }
    observers === _observers && _iterationCount--;
  };
  const subscribe = (observer) => {
    _observers.add(observer);
    return {
      unsubscribe: () => {
        if (_iterationCount) {
          _observers = new Set(_observers);
          _iterationCount = 0;
        }
        _observers.delete(observer);
      }
    };
  };
  const unsubscribe = () => {
    _observers = /* @__PURE__ */ new Set();
    _iterationCount = 0;
  };
  return {
    get observers() {
      return Array.from(_observers);
    },
    next,
    subscribe,
    unsubscribe
  };
};
function extractFormValues(fieldsState, formValues) {
  const values = Array.isArray(fieldsState) ? [] : {};
  for (const key in fieldsState) {
    if (Object.prototype.hasOwnProperty.call(fieldsState, key)) {
      const fieldState = fieldsState[key];
      const fieldValue = formValues[key];
      if (fieldState && (isObject(fieldState) || Array.isArray(fieldState)) && fieldValue) {
        values[key] = extractFormValues(fieldState, fieldValue);
      } else if (fieldsState[key]) {
        values[key] = fieldValue;
      }
    }
  }
  return values;
}
var hasOwn = (value, key) => value !== null && isObjectType(value) && Object.prototype.hasOwnProperty.call(value, key);
var has = (object, path) => {
  if (!path) {
    return false;
  }
  let result = object;
  for (const key of isKey(path) ? [path] : stringToPath(path)) {
    if (!hasOwn(result, key)) {
      return hasOwn(object, path);
    }
    result = result[key];
  }
  return true;
};
var isMultipleSelect = (element) => element.type === `select-multiple`;
var isSelectEdited = (select) => {
  const options = [...select.options];
  return !select.multiple && select.size < 2 && !options.some((option) => option.defaultSelected) ? select.selectedIndex !== options.findIndex((option) => {
    var _a;
    return !option.disabled && !((_a = option.parentElement) === null || _a === void 0 ? void 0 : _a.matches("optgroup[disabled]"));
  }) : options.some((option) => option.selected !== option.defaultSelected);
};
var isEdited = (ref) => isCheckBoxInput(ref) ? ref.checked !== ref.defaultChecked : ref.type === "select-one" || isMultipleSelect(ref) ? isSelectEdited(ref) : !isRadioInput(ref) && ref.type !== "range" && ref.type !== "color" && "defaultValue" in ref && ref.value !== ref.defaultValue;
var isRadioOrCheckbox = (ref) => isRadioInput(ref) || isCheckBoxInput(ref);
var live = (ref) => isHTMLElement(ref) && ref.isConnected;
function isDirtyContainer(value) {
  return Array.isArray(value) || isObject(value);
}
function collectDirtyFieldNames(dirtyTree, cachedDirtyFields, prefix = "", names = []) {
  for (const key in dirtyTree) {
    const path = prefix ? `${prefix}.${key}` : key;
    const value = dirtyTree[key];
    if (isDirtyContainer(value) && isDirtyContainer(get(cachedDirtyFields, path))) {
      collectDirtyFieldNames(value, cachedDirtyFields, path, names);
    } else if (value) {
      names.push(path);
    }
  }
  return names;
}
var objectHasFunction = (data) => {
  for (const key in data) {
    if (isFunction(data[key])) {
      return true;
    }
  }
  return false;
};
function isTraversable(value) {
  return Array.isArray(value) || isObject(value) && !objectHasFunction(value);
}
function isRegisteredLeaf(fieldRef) {
  return !!(fieldRef && "_f" in fieldRef);
}
function isEmptyDirtyContainer(value) {
  return Array.isArray(value) ? !value.some((item) => !isUndefined(item)) : !Object.keys(value).length;
}
function clearDirtyField(container, key) {
  if (Array.isArray(container)) {
    container[key] = void 0;
  } else {
    delete container[key];
  }
}
function markFieldsDirty(data, fields = {}, fieldRefs) {
  for (const key in data) {
    const value = data[key];
    const fieldRef = fieldRefs && fieldRefs[key];
    if (isTraversable(value) && (!Array.isArray(value) || !isRegisteredLeaf(fieldRef))) {
      fields[key] = Array.isArray(value) ? [] : {};
      markFieldsDirty(value, fields[key], fieldRef);
      if (isEmptyDirtyContainer(fields[key])) {
        clearDirtyField(fields, key);
      }
    } else if (!isUndefined(value)) {
      fields[key] = true;
    }
  }
  return fields;
}
function getDirtyFields(data, formValues, dirtyFieldsFromValues, fieldRefs) {
  if (!dirtyFieldsFromValues) {
    dirtyFieldsFromValues = markFieldsDirty(formValues, Array.isArray(isNullOrUndefined(formValues) ? data : formValues) ? [] : {}, fieldRefs);
  }
  for (const key in data) {
    const value = data[key];
    const fieldRef = fieldRefs && fieldRefs[key];
    if (isTraversable(value) && (!Array.isArray(value) || !isRegisteredLeaf(fieldRef))) {
      if (isUndefined(formValues) || isPrimitive(dirtyFieldsFromValues[key])) {
        dirtyFieldsFromValues[key] = markFieldsDirty(value, Array.isArray(value) ? [] : {}, fieldRef);
      } else {
        getDirtyFields(value, isNullOrUndefined(formValues) ? {} : formValues[key], dirtyFieldsFromValues[key], fieldRef);
      }
      if (isEmptyDirtyContainer(dirtyFieldsFromValues[key])) {
        clearDirtyField(dirtyFieldsFromValues, key);
      }
    } else if (deepEqual(value, isNullOrUndefined(formValues) ? void 0 : formValues[key])) {
      clearDirtyField(dirtyFieldsFromValues, key);
    } else {
      dirtyFieldsFromValues[key] = true;
    }
  }
  return dirtyFieldsFromValues;
}
var getFieldArrayItemNames = (names, name) => {
  const segments = name.split(".");
  const matches = [];
  let prefix = segments[0];
  for (let i = 1; i < segments.length; prefix += "." + segments[i++]) {
    if (!isNaN(+segments[i]) && names.has(prefix)) {
      matches.push(`${prefix}.${segments[i]}`);
    }
  }
  return matches;
};
var getFieldValueAs = (value, { valueAsNumber, valueAsDate, setValueAs }) => isUndefined(value) ? value : valueAsNumber ? value === "" ? NaN : value ? +value : value : valueAsDate && isString(value) ? new Date(value) : setValueAs ? setValueAs(value) : value;
function getFieldValue(_f) {
  const ref = _f.ref;
  if (isFileInput(ref)) {
    return ref.files;
  }
  if (isRadioInput(ref)) {
    return getRadioValue(_f.refs).value;
  }
  if (isMultipleSelect(ref)) {
    return [...ref.selectedOptions].filter((option) => {
      var _a;
      return !option.disabled && !((_a = option.closest) === null || _a === void 0 ? void 0 : _a.call(option, "optgroup[disabled]"));
    }).map(({ value }) => value);
  }
  if (isCheckBoxInput(ref)) {
    return getCheckboxValue(_f.refs).value;
  }
  return getFieldValueAs(ref.value, _f);
}
var getResolverOptions = (fieldsNames, _fields, criteriaMode, shouldUseNativeValidation) => {
  const fields = {};
  for (const name of fieldsNames) {
    const field = get(_fields, name);
    field && set(fields, name, cloneObject(field._f));
  }
  return {
    criteriaMode,
    names: [...fieldsNames],
    fields,
    shouldUseNativeValidation
  };
};
var getRuleValue = (rule) => isUndefined(rule) ? rule : isRegex(rule) ? rule.source : isObject(rule) ? isRegex(rule.value) ? rule.value.source : rule.value : rule;
var ASYNC_FUNCTION = "AsyncFunction";
var hasPromiseValidation = (fieldReference) => {
  if (!fieldReference || !fieldReference.validate)
    return false;
  if (isFunction(fieldReference.validate)) {
    return fieldReference.validate.constructor.name === ASYNC_FUNCTION;
  }
  if (isObject(fieldReference.validate)) {
    for (const key in fieldReference.validate) {
      if (fieldReference.validate[key].constructor.name === ASYNC_FUNCTION) {
        return true;
      }
    }
  }
  return false;
};
var hasValidation = (options) => options.mount && (options.required || !isUndefined(options.required) && options.required !== false || !isUndefined(options.min) || !isUndefined(options.max) || !isUndefined(options.maxLength) || !isUndefined(options.minLength) || options.pattern || options.validate);
function schemaErrorLookup(errors, _fields, name) {
  const error = get(errors, name);
  if (error && (error.type || error.message) || Array.isArray(error)) {
    return {
      error,
      name
    };
  }
  const names = name.split(".");
  while (names.length) {
    const fieldName = names.join(".");
    const field = get(_fields, fieldName);
    const foundError = get(errors, fieldName);
    if (field && !Array.isArray(field) && name !== fieldName) {
      return { name };
    }
    if (foundError && foundError.type) {
      return {
        name: fieldName,
        error: foundError
      };
    }
    if (foundError && foundError.root && foundError.root.type) {
      return {
        name: `${fieldName}.root`,
        error: foundError.root
      };
    }
    names.pop();
  }
  return {
    name
  };
}
var shouldRenderFormState = (formStateData, _proxyFormState, isRoot) => {
  const keys = Object.keys(formStateData).filter((key) => key !== "name");
  return !keys.length || isRoot && keys.length >= Object.keys(_proxyFormState).length || keys.find((key) => _proxyFormState[key] === (!isRoot || VALIDATION_MODE.all));
};
var isNameMatch = (currentName, signalName, exact) => exact ? currentName === signalName || currentName.startsWith(signalName + ".") : currentName.startsWith(signalName) || signalName.startsWith(currentName);
var shouldSubscribeByName = (name, signalName, exact) => {
  if (!name || !signalName || name === signalName) {
    return true;
  }
  if (!Array.isArray(name)) {
    return isNameMatch(name, signalName, exact);
  }
  for (const currentName of name) {
    if (currentName && isNameMatch(currentName, signalName, exact)) {
      return true;
    }
  }
  return false;
};
var skipValidation = (isBlurEvent, isTouched, isSubmitted, reValidateMode, mode) => {
  if (mode.isOnAll) {
    return false;
  } else if (!isSubmitted && mode.isOnTouch) {
    return !(isTouched || isBlurEvent);
  } else if (isSubmitted ? reValidateMode.isOnBlur : mode.isOnBlur) {
    return !isBlurEvent;
  } else if (isSubmitted ? reValidateMode.isOnChange : mode.isOnChange) {
    return isBlurEvent;
  }
  return true;
};
var unsetEmptyArray = (ref, name) => {
  const array = get(ref, name);
  !compact(array).length && !(array && array.root) && unset(ref, name);
};
var defaultOptions = {
  mode: VALIDATION_MODE.onSubmit,
  reValidateMode: VALIDATION_MODE.onChange,
  shouldFocusError: true
};
var replaceDirtyFieldsInPlace = (dirtyFields, nextDirtyFields) => {
  for (const key in dirtyFields) {
    delete dirtyFields[key];
  }
  Object.assign(dirtyFields, nextDirtyFields);
};
var DEFAULT_FORM_STATE = {
  submitCount: 0,
  isDirty: false,
  isReady: false,
  isValidating: false,
  isSubmitted: false,
  isSubmitting: false,
  isSubmitSuccessful: false,
  isValid: false,
  touchedFields: {},
  dirtyFields: {},
  validatingFields: {}
};
function createFormControl(props = {}) {
  let _options = {
    ...defaultOptions,
    ...props
  };
  let _formState = {
    ...cloneObject(DEFAULT_FORM_STATE),
    isLoading: isFunction(_options.defaultValues),
    errors: cloneObject(_options.errors) || {},
    disabled: _options.disabled || false
  };
  let _fields = {};
  let _defaultValues = isObject(_options.defaultValues) || isObject(_options.values) ? cloneObject(_options.defaultValues || _options.values) || {} : {};
  let _formValues = _options.shouldUnregister ? {} : cloneObject(_defaultValues);
  let _state = {
    action: false,
    actionArrayLengths: /* @__PURE__ */ new Map(),
    mount: false,
    watch: false,
    keepIsValid: false
  };
  let _dirtyFieldsStale = false;
  let _names = {
    mount: /* @__PURE__ */ new Set(),
    disabled: /* @__PURE__ */ new Set(),
    unMount: /* @__PURE__ */ new Set(),
    array: /* @__PURE__ */ new Set(),
    watch: /* @__PURE__ */ new Set(),
    registerName: /* @__PURE__ */ new Set()
  };
  const delayErrorCallbacks = {};
  const timers = {};
  const _valuesSubscribers = /* @__PURE__ */ new Set();
  let _validationModeBeforeSubmit = getValidationModes(_options.mode);
  let _validationModeAfterSubmit = getValidationModes(_options.reValidateMode);
  const defaultProxyFormState = {
    isDirty: false,
    dirtyFields: false,
    validatingFields: false,
    touchedFields: false,
    isValidating: false,
    isValid: false,
    errors: false
  };
  const _proxyFormState = {
    ...defaultProxyFormState
  };
  const _proxySubscribeFormState = {};
  const _isTracked = (...keys) => keys.some((key) => _proxyFormState[key] || _proxySubscribeFormState[key]);
  const _hasValuesSubscriber = (name) => {
    for (const subscriber of _valuesSubscribers) {
      if (shouldSubscribeByName(subscriber.name, name, subscriber.exact)) {
        return true;
      }
    }
    return false;
  };
  const _subjects = {
    array: createSubject(),
    state: createSubject()
  };
  let _setValidCallId = 0;
  let _validateFormCallId = 0;
  let _resetCallId = 0;
  let _registerCallId = 0;
  const _registerCallIds = {};
  let shouldDisplayAllAssociatedErrors = _options.criteriaMode === VALIDATION_MODE.all;
  const debounce = (name, callback) => (wait) => {
    clearTimeout(timers[name]);
    timers[name] = setTimeout(callback, wait);
  };
  const cancelDelayedError = (name) => {
    clearTimeout(timers[name]);
    delete timers[name];
    delete delayErrorCallbacks[name];
  };
  const cancelDelayedErrorTree = (name) => {
    cancelDelayedError(name);
    const prefix = `${name}.`;
    for (const key of Object.keys(delayErrorCallbacks)) {
      key.startsWith(prefix) && cancelDelayedError(key);
    }
  };
  const _setValid = async (shouldUpdateValid) => {
    if (_state.keepIsValid) {
      return;
    }
    if (!_options.disabled && (_isTracked("isValid") || shouldUpdateValid)) {
      const callId = ++_setValidCallId;
      const resetCallId = _resetCallId;
      let isValid;
      if (_options.resolver) {
        isValid = isEmptyObject((await _runSchema()).errors);
        callId === _setValidCallId && _updateIsValidating();
      } else {
        isValid = await executeBuiltInValidation({
          fields: _fields,
          onlyCheckValid: true,
          eventType: EVENTS.VALID
        });
      }
      if (callId !== _setValidCallId) {
        return;
      }
      if (resetCallId !== _resetCallId) {
        return _setValid(shouldUpdateValid);
      }
      if (isValid !== _formState.isValid) {
        _subjects.state.next({
          isValid
        });
      }
    }
  };
  const _updateValidatingFields = (names, isValidating) => {
    const fieldNames = [...names];
    if (!fieldNames.some((name) => name && (isValidating ? get(_formState.validatingFields, name) !== true : has(_formState.validatingFields, name)))) {
      return;
    }
    const validatingFields = cloneObject(_formState.validatingFields);
    for (const name of fieldNames) {
      if (name) {
        isValidating ? set(validatingFields, name, true) : unset(validatingFields, name);
      }
    }
    if (!deepEqual(_formState.validatingFields, validatingFields)) {
      _formState.validatingFields = validatingFields;
    }
  };
  const _updateIsValidating = (names, isValidating) => {
    if (!_options.disabled && _isTracked("isValidating", "validatingFields")) {
      _updateValidatingFields(names || _names.mount, isValidating);
      _subjects.state.next({
        validatingFields: _formState.validatingFields,
        isValidating: !isEmptyObject(_formState.validatingFields)
      });
    }
  };
  const _updateDirtyFields = () => {
    _formState.dirtyFields = getDirtyFields(_defaultValues, _formValues, void 0, _fields);
  };
  const _setFieldArray = (name, values = [], method, args, shouldSetValues = true, shouldUpdateFieldsAndState = true) => {
    if (args && method && !_options.disabled) {
      _state.action = true;
      _state.keepIsValid = false;
      const field = get(_fields, name);
      const rootField = field && field._f;
      const fields = rootField ? Object.assign([], field) : field;
      if (!_state.actionArrayLengths.has(name)) {
        _state.actionArrayLengths.set(name, Array.isArray(fields) ? fields.length : 0);
      }
      if (shouldUpdateFieldsAndState && Array.isArray(fields)) {
        const fieldValues = method(fields, args.argA, args.argB) || fields;
        rootField ? set(_fields, name, { ...fieldValues, _f: rootField }) : shouldSetValues && set(_fields, name, fieldValues);
      }
      const fieldArrayErrors = get(_formState.errors, name);
      if (shouldUpdateFieldsAndState && Array.isArray(fieldArrayErrors)) {
        const rootError = fieldArrayErrors.root;
        const errors = method(fieldArrayErrors, args.argA, args.argB) || fieldArrayErrors;
        if (rootError) {
          errors.root = rootError;
        }
        shouldSetValues && set(_formState.errors, name, errors);
        unsetEmptyArray(_formState.errors, name);
        _formState.errors = { ..._formState.errors };
      }
      const touchedFieldsArray = get(_formState.touchedFields, name);
      const shouldUpdateTouchedFields = shouldUpdateFieldsAndState && Array.isArray(touchedFieldsArray);
      if (shouldUpdateTouchedFields) {
        const touchedFields = method(touchedFieldsArray, args.argA, args.argB);
        shouldSetValues && set(_formState.touchedFields, name, touchedFields);
      }
      const dirtyFieldsArray = get(_formState.dirtyFields, name);
      if (shouldUpdateFieldsAndState && Array.isArray(dirtyFieldsArray)) {
        const dirtyFields = method(dirtyFieldsArray, args.argA, args.argB) || dirtyFieldsArray;
        shouldSetValues && set(_formState.dirtyFields, name, dirtyFields);
      }
      if (_isTracked("dirtyFields")) {
        _updateDirtyFields();
      } else {
        _dirtyFieldsStale = true;
      }
      _subjects.state.next({
        name,
        isDirty: _getDirty(name, values),
        dirtyFields: _formState.dirtyFields,
        ...shouldUpdateTouchedFields && {
          touchedFields: _formState.touchedFields
        },
        errors: _formState.errors,
        isValid: _formState.isValid
      });
    } else {
      set(_formValues, name, values);
    }
  };
  const updateErrors = (name, error) => {
    set(_formState.errors, name, error);
    _formState.errors = { ..._formState.errors };
    _subjects.state.next({
      errors: _formState.errors
    });
  };
  const _setErrors = (errors) => {
    Object.keys(delayErrorCallbacks).forEach(cancelDelayedError);
    const hasErrors = !isEmptyObject(errors);
    _formState.errors = cloneObject(errors);
    _subjects.state.next({
      errors: _formState.errors,
      ...hasErrors ? { isValid: false } : {}
    });
    !hasErrors && _state.mount && _setValid();
  };
  const toPath = (name) => isKey(name) ? [name] : stringToPath(name);
  const hasExplicitNullIntermediate = (name) => {
    const segments = toPath(name);
    let formValues = _formValues;
    let defaultValues = _defaultValues;
    for (let i = 0; i < segments.length - 1; i++) {
      const key = segments[i];
      formValues = isNullOrUndefined(formValues) ? formValues : formValues[key];
      defaultValues = isNullOrUndefined(defaultValues) ? defaultValues : defaultValues[key];
      if (formValues === null && defaultValues !== null) {
        return true;
      }
    }
    return false;
  };
  const isStaleArrayField = (name) => {
    if (!_state.actionArrayLengths.size) {
      return false;
    }
    const segments = toPath(name);
    let node = _formValues;
    let path = "";
    let ownerDepth = -1;
    let ownerPreActionLength = 0;
    for (let i = 0; i < segments.length; i++) {
      if (isNullOrUndefined(node)) {
        return false;
      }
      const key = segments[i];
      path = path ? `${path}.${key}` : key;
      if (Array.isArray(node) && +key >= node.length) {
        return ownerDepth === -1 ? false : i === ownerDepth ? +key < ownerPreActionLength : true;
      }
      if (_state.actionArrayLengths.has(path)) {
        ownerDepth = i + 1;
        ownerPreActionLength = _state.actionArrayLengths.get(path);
      }
      node = node[key];
      if (isUndefined(node) && ownerDepth !== -1 && i > ownerDepth && +segments[ownerDepth] < ownerPreActionLength) {
        return true;
      }
    }
    return false;
  };
  const isPastArrayEnd = (name) => {
    let node = _formValues;
    for (const key of toPath(name)) {
      if (Array.isArray(node) && +key >= node.length) {
        return true;
      }
      node = node && node[key];
    }
    return false;
  };
  const updateValidAndValue = (name, shouldSkipSetValueAs, value, ref) => {
    const field = get(_fields, name);
    if (field) {
      if (hasExplicitNullIntermediate(name) || isStaleArrayField(name)) {
        return;
      }
      const wasUnsetInFormValues = isUndefined(get(_formValues, name));
      const defaultValue = get(_formValues, name, isUndefined(value) ? get(_defaultValues, name) : value);
      const isEditedBeforeReady = !!ref && !_formState.isReady && isEdited(ref);
      if (isUndefined(defaultValue) || ref && ref.defaultChecked || isEditedBeforeReady || shouldSkipSetValueAs) {
        const fieldValue = shouldSkipSetValueAs ? defaultValue : getFieldValue(field._f);
        if (!isUndefined(fieldValue) || !isPastArrayEnd(name)) {
          if (_isTracked("dirtyFields") && !deepEqual(get(_formValues, name), fieldValue)) {
            _dirtyFieldsStale = true;
          }
          set(_formValues, name, fieldValue);
          if (isEditedBeforeReady) {
            updateTouchAndDirty(name, fieldValue);
          }
        }
      } else {
        setFieldValue(name, defaultValue);
      }
      if (_state.mount && !_state.action) {
        if (_options.resolver && _isTracked("isValidating", "validatingFields")) {
          Promise.resolve().then(() => _setValid());
        } else {
          _setValid();
        }
        if (wasUnsetInFormValues && _formState.isDirty && _isTracked("isDirty")) {
          const isDirty = _getDirty();
          if (!isDirty) {
            _formState.isDirty = false;
            _subjects.state.next({ ..._formState });
          }
        }
        if (_options.shouldUnregister && wasUnsetInFormValues && !isUndefined(get(_formValues, name)) && isWatched(name, _names)) {
          _state.watch = true;
        }
      }
    }
  };
  const updateTouchAndDirty = (name, fieldValue, isBlurEvent, shouldDirty, shouldRender) => {
    let shouldUpdateField = false;
    let isPreviousDirty = false;
    const output = {
      name
    };
    if (!_options.disabled || shouldDirty === true) {
      if (!isBlurEvent || shouldDirty) {
        const isCurrentFieldPristine = deepEqual(get(_defaultValues, name), fieldValue);
        if (_isTracked("isDirty")) {
          isPreviousDirty = _formState.isDirty;
          _formState.isDirty = output.isDirty = !isCurrentFieldPristine || _getDirty();
          shouldUpdateField = isPreviousDirty !== output.isDirty;
        }
        isPreviousDirty = !!get(_formState.dirtyFields, name);
        if (isCurrentFieldPristine) {
          unset(_formState.dirtyFields, name);
        } else {
          const defaultFieldValue = get(_defaultValues, name);
          const field = get(_fields, name);
          const dirtyValue = !(field && field._f) && (isObject(defaultFieldValue) || Array.isArray(defaultFieldValue)) ? getDirtyFields(defaultFieldValue, fieldValue, void 0, field) : true;
          set(_formState.dirtyFields, name, dirtyValue);
        }
        let didResyncDirtyFields = false;
        if (_dirtyFieldsStale || isCurrentFieldPristine && (_isTracked("isDirty") ? _formState.isDirty : _getDirty())) {
          _dirtyFieldsStale = false;
          const nextDirtyFields = getDirtyFields(_defaultValues, _formValues, void 0, _fields);
          if (!deepEqual(_formState.dirtyFields, nextDirtyFields)) {
            didResyncDirtyFields = true;
            replaceDirtyFieldsInPlace(_formState.dirtyFields, nextDirtyFields);
          }
        }
        output.dirtyFields = _formState.dirtyFields;
        shouldUpdateField = shouldUpdateField || _isTracked("dirtyFields") && (didResyncDirtyFields || isPreviousDirty !== !isCurrentFieldPristine);
      }
      if (isBlurEvent) {
        const isPreviousFieldTouched = has(_formState.touchedFields, name) ? get(_formState.touchedFields, name) : void 0;
        if (!isPreviousFieldTouched) {
          set(_formState.touchedFields, name, isBlurEvent);
          output.touchedFields = _formState.touchedFields;
          shouldUpdateField = shouldUpdateField || _isTracked("touchedFields") && isPreviousFieldTouched !== isBlurEvent;
        }
      }
      shouldUpdateField && shouldRender && _subjects.state.next(output);
    }
    return shouldUpdateField ? output : {};
  };
  const shouldRenderByError = (name, isValid, error, fieldState) => {
    const previousFieldError = get(_formState.errors, name);
    const shouldUpdateValid = _isTracked("isValid") && isBoolean(isValid) && _formState.isValid !== isValid;
    if (_options.delayError && error) {
      delayErrorCallbacks[name] = debounce(name, () => updateErrors(name, error));
      delayErrorCallbacks[name](_options.delayError);
    } else {
      cancelDelayedError(name);
      error ? set(_formState.errors, name, error) : unset(_formState.errors, name);
      _formState.errors = { ..._formState.errors };
    }
    if ((error ? !deepEqual(previousFieldError, error, true) : previousFieldError) || !isEmptyObject(fieldState) || shouldUpdateValid) {
      const updatedFormState = {
        ...fieldState,
        ...shouldUpdateValid && isBoolean(isValid) ? { isValid } : {},
        errors: _formState.errors,
        name
      };
      _subjects.state.next(updatedFormState);
    }
  };
  const _runSchema = async (name) => {
    _updateIsValidating(name, true);
    return await _options.resolver(_formValues, _options.context, getResolverOptions(name || _names.mount, _fields, _options.criteriaMode, _options.shouldUseNativeValidation));
  };
  const executeSchemaAndUpdateState = async (names) => {
    const resetCallId = _resetCallId;
    const registerCallId = _registerCallId;
    const registeredNames = names ? names.filter((name) => get(_fields, name)) : [];
    const { errors } = await _runSchema(names);
    if (resetCallId !== _resetCallId) {
      return errors;
    }
    _updateIsValidating(names);
    if (names) {
      for (const name of names) {
        if (registeredNames.includes(name) && (!get(_fields, name) || _registerCallIds[name] > registerCallId)) {
          unset(errors, name);
          continue;
        }
        const error = get(errors, name);
        cancelDelayedErrorTree(name);
        const isFieldArrayRootError = _names.array.has(name) && isObject(error) && !Object.keys(error).some((key) => !Number.isNaN(Number(key)));
        const field = get(_fields, name);
        const hasNestedFields = isObject(field) && Object.keys(field).some((key) => key !== "_f");
        isFieldArrayRootError ? updateFieldArrayRootError(_formState.errors, { [name]: error }, name) : error && (error.type || error.message) || Array.isArray(error) || isObject(error) && hasNestedFields ? set(_formState.errors, name, error) : unset(_formState.errors, name);
      }
      _formState.errors = { ..._formState.errors };
    } else {
      Object.keys(delayErrorCallbacks).forEach(cancelDelayedError);
      _formState.errors = errors;
    }
    return errors;
  };
  const validateForm = async ({ name, eventType, onlyCheckValid }) => {
    if (_options.validate) {
      const resetCallId = _resetCallId;
      const validateFormCallId = ++_validateFormCallId;
      const result = await _options.validate({
        formValues: _formValues,
        formState: _formState,
        name,
        eventType
      });
      if (resetCallId !== _resetCallId || eventType !== EVENTS.SUBMIT && validateFormCallId !== _validateFormCallId) {
        return true;
      }
      if (onlyCheckValid && (isObject(result) ? Object.values(result).some(Boolean) : isString(result) || !result)) {
        return false;
      }
      if (isObject(result)) {
        let isValid = true;
        clearErrors(FORM_ERROR_TYPE);
        for (const key in result) {
          const error = result[key];
          if (error) {
            isValid = false;
            setError(`${FORM_ERROR_TYPE}.${key}`, {
              message: isString(error.message) ? error.message : "",
              type: error.type || INPUT_VALIDATION_RULES.validate
            });
          }
        }
        return isValid;
      } else if (isString(result) || !result) {
        clearErrors(FORM_ERROR_TYPE);
        setError(FORM_ERROR_TYPE, {
          message: result || "",
          type: INPUT_VALIDATION_RULES.validate
        });
        return false;
      } else {
        clearErrors(FORM_ERROR_TYPE);
        return true;
      }
    }
    return true;
  };
  const executeBuiltInValidation = async ({ fields, onlyCheckValid, name, eventType, context = {
    valid: true,
    runRootValidation: false
  } }) => {
    const resetCallId = _resetCallId;
    if (_options.validate && !context.runRootValidation) {
      context.runRootValidation = true;
      const result = await validateForm({
        name,
        eventType,
        onlyCheckValid
      });
      if (!result) {
        context.valid = false;
        if (onlyCheckValid) {
          return context.valid;
        }
      }
    }
    for (const name2 in fields) {
      const field = fields[name2];
      if (field) {
        const { _f, ...fieldValue } = field;
        if (_f) {
          const isFieldArrayRoot = _names.array.has(_f.name);
          const isPromiseFunction = field._f && hasPromiseValidation(field._f);
          const shouldTrackIsValidatingState = _isTracked("isValidating", "validatingFields");
          if (isPromiseFunction && shouldTrackIsValidatingState) {
            _updateIsValidating([_f.name], true);
          }
          const registerCallId = _registerCallId;
          const fieldError = await validateField(field, _names.disabled, _formValues, shouldDisplayAllAssociatedErrors, _options.shouldUseNativeValidation && !onlyCheckValid, isFieldArrayRoot);
          if (resetCallId !== _resetCallId) {
            return context.valid;
          }
          if (isPromiseFunction && shouldTrackIsValidatingState) {
            _updateIsValidating([_f.name]);
          }
          if (!get(_fields, _f.name) || _registerCallIds[_f.name] > registerCallId) {
            continue;
          }
          if (fieldError[_f.name]) {
            context.valid = false;
            if (onlyCheckValid) {
              break;
            }
          }
          if (!onlyCheckValid) {
            cancelDelayedError(_f.name);
            get(fieldError, _f.name) ? isFieldArrayRoot ? updateFieldArrayRootError(_formState.errors, fieldError, _f.name) : set(_formState.errors, _f.name, fieldError[_f.name]) : unset(_formState.errors, _f.name);
          }
          if (_options.shouldUseNativeValidation && fieldError[_f.name]) {
            break;
          }
        }
        !isEmptyObject(fieldValue) && await executeBuiltInValidation({
          context,
          onlyCheckValid,
          fields: fieldValue,
          name: name2,
          eventType
        });
      }
    }
    return context.valid;
  };
  const _removeUnmounted = () => {
    for (const name of _names.unMount) {
      const field = get(_fields, name);
      field && (field._f.refs ? field._f.refs.every((ref) => !live(ref)) : !live(field._f.ref)) && unregister(name);
    }
    _names.unMount = /* @__PURE__ */ new Set();
  };
  const _getDirty = (name, data) => (name && !isUndefined(data) && set(_formValues, name, data), !deepEqual(_state.mount ? _formValues : _defaultValues, _defaultValues));
  const _getWatch = (names, defaultValue, isGlobal) => {
    const values = _state.mount ? _formValues : isUndefined(defaultValue) || !isUndefined(names) ? _defaultValues : defaultValue;
    return generateWatchOutput(names, _names, isUndefined(names) ? { ...values } : values, isGlobal, defaultValue);
  };
  const _getFieldArray = (name) => compact(get(_state.mount ? _formValues : _defaultValues, name, _options.shouldUnregister ? get(_defaultValues, name, []) : []));
  const setFieldValue = (name, value, options = {}, skipClone = false, skipRender = false, skipValueRender = false) => {
    const field = get(_fields, name);
    let fieldValue = value;
    if (field) {
      const fieldReference = field._f;
      if (fieldReference) {
        !fieldReference.disabled && set(_formValues, name, getFieldValueAs(value, fieldReference));
        fieldValue = isHTMLElement(fieldReference.ref) && isNullOrUndefined(value) ? "" : value;
        if (isMultipleSelect(fieldReference.ref)) {
          [...fieldReference.ref.options].forEach((optionRef) => optionRef.selected = fieldValue.includes(optionRef.value));
        } else if (fieldReference.refs) {
          if (isCheckBoxInput(fieldReference.ref)) {
            fieldReference.refs.forEach((checkboxRef) => {
              if (!checkboxRef.defaultChecked || !checkboxRef.disabled) {
                if (Array.isArray(fieldValue)) {
                  checkboxRef.checked = !isUndefined(checkboxRef.value) && fieldValue.includes(checkboxRef.value);
                } else {
                  checkboxRef.checked = fieldValue === checkboxRef.value || !!fieldValue;
                }
              }
            });
          } else {
            fieldReference.refs.forEach((radioRef) => radioRef.checked = radioRef.value === fieldValue);
          }
        } else if (isFileInput(fieldReference.ref)) {
          fieldReference.ref.value = "";
        } else {
          fieldReference.ref.value = fieldValue;
          if (!fieldReference.ref.type && !skipRender && !skipValueRender) {
            _subjects.state.next({
              name,
              values: skipClone ? _formValues : _hasValuesSubscriber(name) ? cloneObject(_formValues) : void 0
            });
          }
        }
      }
    }
    (options.shouldDirty || options.shouldTouch) && updateTouchAndDirty(name, field && field._f && !field._f.disabled && (field._f.valueAsNumber || field._f.valueAsDate || field._f.setValueAs) ? getFieldValueAs(value, field._f) : fieldValue, options.shouldTouch, options.shouldDirty, !skipRender);
    options.shouldValidate && trigger(name, {
      delayError: options.delayError
    });
    if (options.shouldValidate && field && field._f && field._f.deps && (!Array.isArray(field._f.deps) || field._f.deps.length > 0)) {
      trigger(field._f.deps);
    }
  };
  const setFieldValues = (name, value, options, skipClone = false, skipRender = false, skipValueRender = false) => {
    for (const fieldKey in value) {
      if (!Object.prototype.hasOwnProperty.call(value, fieldKey)) {
        continue;
      }
      const fieldValue = value[fieldKey];
      const fieldName = name + "." + fieldKey;
      const field = get(_fields, fieldName);
      if (_names.array.has(fieldName)) {
        _subjects.array.next({
          name: fieldName,
          values: skipClone ? _formValues : cloneObject(_formValues)
        });
      }
      (_names.array.has(name) || isObject(fieldValue) || field && !field._f) && !isDateObject(fieldValue) ? setFieldValues(fieldName, fieldValue, options, skipClone, skipRender, skipValueRender) : setFieldValue(fieldName, fieldValue, options, skipClone, skipRender, skipValueRender);
    }
  };
  const _setValue = (name, value, options, skipClone, skipStateEmit = false) => {
    const field = get(_fields, name);
    const isFieldArray = _names.array.has(name);
    const cloneValue = skipClone ? value : cloneObject(value);
    const previousValue = get(_formValues, name);
    const isValueUnchanged = deepEqual(previousValue, cloneValue);
    if (!isValueUnchanged) {
      set(_formValues, name, cloneValue);
    }
    if (isFieldArray) {
      _subjects.array.next({
        name,
        values: skipClone ? _formValues : cloneObject(_formValues)
      });
      if (_isTracked("isDirty", "dirtyFields") && options.shouldDirty) {
        _updateDirtyFields();
        _formState.isDirty = _getDirty(name, cloneValue);
        if (!skipStateEmit) {
          _subjects.state.next({
            name,
            dirtyFields: _formState.dirtyFields,
            isDirty: _formState.isDirty
          });
        }
      }
      options.shouldValidate && trigger(name, {
        delayError: options.delayError
      });
    } else {
      const isEmpty = Array.isArray(cloneValue) && !cloneValue.length || isEmptyObject(cloneValue);
      const skipValueRender = !isValueUnchanged && !skipStateEmit;
      if (!field || field._f || isNullOrUndefined(cloneValue) || isEmpty) {
        setFieldValue(name, cloneValue, options, skipClone, skipStateEmit, skipValueRender);
      } else {
        setFieldValues(name, cloneValue, options, skipClone, skipStateEmit, skipValueRender);
      }
    }
    if (!isValueUnchanged && !skipStateEmit) {
      const watched = isWatched(name, _names);
      const emittedName = _state.mount || watched ? name : void 0;
      const fieldArrayItemNames = isFieldArray ? [] : getFieldArrayItemNames(_names.array, name);
      const values = skipClone ? _formValues : _hasValuesSubscriber(emittedName) || fieldArrayItemNames.some(_hasValuesSubscriber) ? cloneObject(_formValues) : void 0;
      _subjects.state.next({
        ...watched && _formState,
        name: emittedName,
        values
      });
      for (const itemName of fieldArrayItemNames) {
        _subjects.state.next({
          name: itemName,
          values: values && cloneObject(_formValues)
        });
      }
    }
  };
  const setValue = (name, value, options = {}) => _setValue(name, value, options, false);
  const setValues = (formValues, options = {}) => {
    const updatedFormValues = isFunction(formValues) ? formValues(_formValues) : formValues;
    if (!deepEqual(_formValues, updatedFormValues) || options.shouldValidate || options.shouldDirty || options.shouldTouch) {
      _formValues = {
        ..._formValues,
        ...updatedFormValues
      };
      for (const fieldName of /* @__PURE__ */ new Set([..._names.mount, ..._names.array])) {
        if (has(updatedFormValues, fieldName)) {
          _setValue(fieldName, get(updatedFormValues, fieldName), options, true, true);
        }
      }
      if (options.shouldDirty && _isTracked("isDirty", "dirtyFields")) {
        for (const name of Object.keys(updatedFormValues)) {
          updateTouchAndDirty(name, _formValues[name], false, true);
        }
      }
      _subjects.state.next({
        ..._formState,
        name: void 0,
        type: void 0,
        ..._valuesSubscribers.size ? { values: _formValues } : {}
      });
      if (options.shouldValidate) {
        _setValid();
      }
    }
  };
  const onChange = async (event) => {
    _state.mount = true;
    const target = event.target;
    let name = target.name;
    let isFieldValueUpdated = true;
    const field = get(_fields, name);
    const registerCallId = _registerCallId;
    const _updateIsFieldValueUpdated = (fieldValue) => {
      isFieldValueUpdated = !!get(_fields, name) && !(_registerCallIds[name] > registerCallId) && deepEqual(fieldValue, get(_formValues, name, fieldValue));
    };
    if (field) {
      _state.keepIsValid = false;
      let error;
      let isValid;
      let shouldFlushError = false;
      const fieldValue = target.type ? getFieldValue(field._f) : getEventValue(event);
      const isBlurEvent = event.type === EVENTS.BLUR || event.type === EVENTS.FOCUS_OUT;
      const hasNoValidationEffect = !hasValidation(field._f) && !_options.validate && !_options.resolver && !get(_formState.errors, name) && !field._f.deps;
      const shouldSkipValidation = hasNoValidationEffect || skipValidation(isBlurEvent, get(_formState.touchedFields, name), _formState.isSubmitted, _validationModeAfterSubmit, _validationModeBeforeSubmit);
      const watched = isWatched(name, _names, isBlurEvent);
      set(_formValues, name, cloneObject(fieldValue));
      if (isBlurEvent) {
        if (!target || !target.readOnly) {
          field._f.onBlur && field._f.onBlur(event);
          if (Object.prototype.hasOwnProperty.call(delayErrorCallbacks, name)) {
            shouldFlushError = true;
            delayErrorCallbacks[name](0);
          }
        }
      } else if (field._f.onChange) {
        field._f.onChange(event);
      }
      const fieldState = updateTouchAndDirty(name, fieldValue, isBlurEvent);
      const shouldRender = !isEmptyObject(fieldState) || watched;
      !isBlurEvent && _subjects.state.next({
        name,
        type: event.type,
        ..._hasValuesSubscriber(name) ? { values: cloneObject(_formValues) } : {}
      });
      if (shouldSkipValidation) {
        if ((!hasNoValidationEffect || !_formState.isValid) && _isTracked("isValid")) {
          if (_options.mode === "onBlur") {
            if (isBlurEvent) {
              _setValid();
            }
          } else if (!isBlurEvent) {
            _setValid();
          }
        }
        return shouldRender && _subjects.state.next({ name, ...watched ? {} : fieldState });
      }
      if (!_options.resolver && _options.validate) {
        await validateForm({
          name,
          eventType: event.type
        });
      }
      !isBlurEvent && watched && _subjects.state.next({ ..._formState });
      if (_options.resolver) {
        const resetCallId = _resetCallId;
        const { errors } = await _runSchema([name]);
        if (resetCallId !== _resetCallId) {
          return;
        }
        _updateIsValidating([name]);
        _updateIsFieldValueUpdated(fieldValue);
        if (!isFieldValueUpdated) {
          !isEmptyObject(fieldState) && _subjects.state.next(fieldState);
          return;
        }
        const previousErrorLookupResult = schemaErrorLookup(_formState.errors, _fields, name);
        const errorLookupResult = schemaErrorLookup(errors, _fields, previousErrorLookupResult.name || name);
        error = errorLookupResult.error;
        name = errorLookupResult.name;
        isValid = isEmptyObject(errors);
      } else {
        const resetCallId = _resetCallId;
        const setValidCallId = ++_setValidCallId;
        _updateIsValidating([name], true);
        error = (await validateField(field, _names.disabled, _formValues, shouldDisplayAllAssociatedErrors, _options.shouldUseNativeValidation))[name];
        if (resetCallId !== _resetCallId) {
          return;
        }
        _updateIsValidating([name]);
        _updateIsFieldValueUpdated(fieldValue);
        if (isFieldValueUpdated) {
          if (error) {
            isValid = false;
          } else if (_isTracked("isValid")) {
            isValid = await executeBuiltInValidation({
              fields: _fields,
              onlyCheckValid: true,
              name,
              eventType: event.type
            });
            if (resetCallId !== _resetCallId) {
              return;
            }
            _updateIsFieldValueUpdated(fieldValue);
            if (setValidCallId !== _setValidCallId) {
              isValid = void 0;
            }
          }
        }
      }
      if (isFieldValueUpdated) {
        field._f.deps && (!Array.isArray(field._f.deps) || field._f.deps.length > 0) && trigger(field._f.deps);
        shouldRenderByError(name, isValid, error, fieldState);
        shouldFlushError && Object.prototype.hasOwnProperty.call(delayErrorCallbacks, name) && delayErrorCallbacks[name](0);
      }
    }
  };
  const _focusInput = (ref, key) => {
    if (get(_formState.errors, key) && ref.focus) {
      ref.focus();
      return 1;
    }
    return;
  };
  const trigger = async (name, options = {}) => {
    _state.keepIsValid = false;
    let isValid;
    let validationResult;
    const fieldNames = convertToArrayPayload(name);
    const resetCallId = _resetCallId;
    if (_options.resolver) {
      const errors = await executeSchemaAndUpdateState(isUndefined(name) ? name : fieldNames);
      isValid = isEmptyObject(errors);
      validationResult = name ? !fieldNames.some((name2) => get(errors, name2)) : isValid;
    } else if (name) {
      validationResult = (await Promise.all(fieldNames.map(async (fieldName) => {
        const field = get(_fields, fieldName);
        return await executeBuiltInValidation({
          fields: field && field._f ? { [fieldName]: field } : field,
          eventType: EVENTS.TRIGGER
        });
      }))).every(Boolean);
      !(!validationResult && !_formState.isValid) && _setValid();
    } else {
      validationResult = isValid = await executeBuiltInValidation({
        fields: _fields,
        name,
        eventType: EVENTS.TRIGGER
      });
    }
    if (resetCallId !== _resetCallId) {
      return validationResult;
    }
    if (options.delayError && _options.delayError && isString(name)) {
      const error = get(_formState.errors, name);
      if (error) {
        unset(_formState.errors, name);
        delayErrorCallbacks[name] = debounce(name, () => updateErrors(name, error));
        delayErrorCallbacks[name](_options.delayError);
      } else {
        cancelDelayedError(name);
      }
    }
    if (options.shouldTouch) {
      const touchField = (fieldName) => {
        !_names.array.has(fieldName) && set(_formState.touchedFields, fieldName, true);
      };
      for (const fieldName of name ? fieldNames : _names.mount) {
        const field = get(_fields, fieldName);
        isObject(field) || Array.isArray(field) ? iterateFieldsByAction(_fields, (_, key) => touchField(key), [
          fieldName
        ]) : touchField(fieldName);
      }
    }
    _formState.errors = { ..._formState.errors };
    _subjects.state.next({
      ...!isString(name) || _isTracked("isValid") && isValid !== _formState.isValid ? {} : { name },
      ..._options.resolver || !name ? { isValid } : {},
      ...options.shouldTouch && _isTracked("touchedFields") ? { touchedFields: _formState.touchedFields } : {},
      errors: _formState.errors
    });
    options.shouldFocus && !validationResult && iterateFieldsByAction(_fields, _focusInput, name ? fieldNames : _names.mount);
    return validationResult;
  };
  const getValues = ((fieldNames, config) => {
    let values = isUndefined(fieldNames) && !config ? { ..._state.mount ? _formValues : _defaultValues } : _state.mount ? _formValues : _defaultValues;
    if (config) {
      values = extractFormValues(config.dirtyFields ? _formState.dirtyFields : _formState.touchedFields, values);
    }
    return isUndefined(fieldNames) ? values : isString(fieldNames) ? get(values, fieldNames) : fieldNames.map((name) => get(values, name));
  });
  const getErrors = ((fieldNames) => isUndefined(fieldNames) ? { ..._formState.errors } : isString(fieldNames) ? get(_formState.errors, fieldNames) : fieldNames.map((name) => get(_formState.errors, name)));
  const getFieldState = (name, formState) => {
    const targetFormState = formState || _formState;
    const error = get(targetFormState.errors, name);
    return {
      invalid: !!error,
      isDirty: !!get(targetFormState.dirtyFields, name),
      error,
      isValidating: !!get(targetFormState.validatingFields, name),
      isTouched: !!get(targetFormState.touchedFields, name)
    };
  };
  const clearErrors = (name) => {
    const names = name ? convertToArrayPayload(name) : void 0;
    if (names) {
      names.forEach((inputName) => {
        cancelDelayedErrorTree(inputName);
        unset(_formState.errors, inputName);
        _formState.errors = { ..._formState.errors };
        _subjects.state.next({
          name: inputName,
          errors: _formState.errors
        });
      });
    } else {
      Object.keys(delayErrorCallbacks).forEach(cancelDelayedError);
      _formState.errors = {};
      _subjects.state.next({
        errors: _formState.errors
      });
    }
  };
  const setError = (name, error, options) => {
    var _a;
    cancelDelayedErrorTree(name);
    const field = get(_fields, name, { _f: {} });
    const ref = field._f && field._f.ref;
    const currentError = get(_formState.errors, name) || {};
    const { ref: currentRef, message, type, types, ...restOfErrorTree } = currentError;
    set(_formState.errors, name, {
      ...restOfErrorTree,
      ...error,
      ref
    });
    _formState.errors = { ..._formState.errors };
    _subjects.state.next({
      name,
      errors: _formState.errors,
      isValid: false
    });
    if (options && options.shouldFocus) {
      const fieldRef = ((_a = field._f) === null || _a === void 0 ? void 0 : _a.refs) ? field._f.refs.find((ref2) => !ref2.disabled) || field._f.refs[0] : ref;
      fieldRef && fieldRef.focus && fieldRef.focus();
    }
  };
  const watch = (name, defaultValue) => {
    if (isFunction(name)) {
      const valuesSubscriber = {};
      _valuesSubscribers.add(valuesSubscriber);
      const { unsubscribe } = _subjects.state.subscribe({
        next: (payload) => "values" in payload && name(payload.values || _getWatch(void 0, defaultValue), payload)
      });
      return {
        unsubscribe: () => {
          _valuesSubscribers.delete(valuesSubscriber);
          unsubscribe();
        }
      };
    }
    return _getWatch(name, defaultValue, true);
  };
  const _subscribe = (props2) => {
    const valuesSubscriber = { name: props2.name, exact: props2.exact };
    props2.formState && props2.formState.values && _valuesSubscribers.add(valuesSubscriber);
    const { unsubscribe } = _subjects.state.subscribe({
      next: (formState) => {
        if (shouldSubscribeByName(props2.name, formState.name, props2.exact) && shouldRenderFormState(formState, props2.formState || _proxyFormState, props2.reRenderRoot)) {
          const values = formState.values || { ..._formValues };
          props2.callback({
            ..._formState,
            ...formState,
            validatingFields: _formState.validatingFields,
            isValidating: _formState.isValidating,
            values,
            defaultValues: _defaultValues
          });
        }
      }
    });
    return () => {
      _valuesSubscribers.delete(valuesSubscriber);
      unsubscribe();
    };
  };
  const subscribe = (props2) => {
    _state.mount = true;
    const trackedKeys = Object.keys(props2.formState || {}).filter((key) => props2.formState && props2.formState[key]);
    for (const key of trackedKeys) {
      _proxySubscribeFormState[key] = (_proxySubscribeFormState[key] || 0) + 1;
    }
    const unsubscribe = _subscribe({
      ...props2,
      formState: {
        ...defaultProxyFormState,
        ...props2.formState
      }
    });
    return () => {
      for (const key of trackedKeys.splice(0)) {
        _proxySubscribeFormState[key]--;
      }
      unsubscribe();
    };
  };
  const unregister = (name, options = {}) => {
    const requestedNames = name ? convertToArrayPayload(name) : [..._names.mount];
    const fieldNames = new Set(requestedNames);
    if (name) {
      for (const fieldName of _names.mount) {
        if (requestedNames.some((parent) => fieldName.startsWith(`${parent}.`))) {
          fieldNames.add(fieldName);
        }
      }
    }
    if (!options.keepIsValidating) {
      _updateValidatingFields(fieldNames);
    }
    for (const fieldName of fieldNames) {
      _names.mount.delete(fieldName);
      _names.array.delete(fieldName);
      _names.disabled.delete(fieldName);
      unset(_fields, fieldName);
      !options.keepValue && unset(_formValues, fieldName);
      if (!options.keepError) {
        cancelDelayedErrorTree(fieldName);
        unset(_formState.errors, fieldName);
      }
      !options.keepDirty && unset(_formState.dirtyFields, fieldName);
      !options.keepTouched && unset(_formState.touchedFields, fieldName);
      !_options.shouldUnregister && !options.keepDefaultValue && unset(_defaultValues, fieldName);
    }
    if (!options.keepError) {
      _formState.errors = { ..._formState.errors };
    }
    _valuesSubscribers.size && _subjects.state.next({
      values: cloneObject(_formValues)
    });
    _subjects.state.next({
      ..._formState,
      ...options.keepDirty ? {} : { isDirty: _getDirty() },
      ...options.keepIsValidating ? {} : { isValidating: !isEmptyObject(_formState.validatingFields) }
    });
    !options.keepIsValid && _setValid();
  };
  const _setDisabledField = ({ disabled, name }) => {
    if (isBoolean(disabled) && _state.mount || !!disabled || _names.disabled.has(name)) {
      const wasDisabled = _names.disabled.has(name);
      const isDisabled = !!disabled;
      const disabledStateChanged = wasDisabled !== isDisabled;
      disabled ? _names.disabled.add(name) : _names.disabled.delete(name);
      disabledStateChanged && _state.mount && !_state.action && _setValid();
    }
  };
  const register = (name, options = {}) => {
    let field = get(_fields, name);
    const disabledIsDefined = isBoolean(options.disabled) || isBoolean(_options.disabled);
    const shouldRevalidateRemount = !_names.registerName.has(name) && field && field._f && !field._f.mount;
    if (!field || !field._f) {
      _registerCallIds[name] = ++_registerCallId;
    }
    set(_fields, name, {
      ...field || {},
      _f: {
        ...field && field._f ? field._f : { ref: { name } },
        name,
        mount: true,
        ...options
      }
    });
    _names.mount.add(name);
    if (field && field._f) {
      const nextField = get(_fields, name);
      for (const rule of REGISTER_VALIDATION_RULES) {
        if (!(rule in options)) {
          delete nextField._f[rule];
        }
      }
    }
    if (field && !shouldRevalidateRemount) {
      _setDisabledField({
        disabled: isBoolean(options.disabled) ? options.disabled : _options.disabled,
        name
      });
    } else {
      updateValidAndValue(name, true, options.value);
    }
    return {
      ...disabledIsDefined ? { disabled: options.disabled || _options.disabled } : {},
      ..._options.progressive ? {
        required: !!getRuleValue(options.required),
        min: getRuleValue(options.min),
        max: getRuleValue(options.max),
        minLength: getRuleValue(options.minLength),
        maxLength: getRuleValue(options.maxLength),
        pattern: getRuleValue(options.pattern)
      } : {},
      name,
      onChange,
      onBlur: onChange,
      ref: (ref) => {
        if (ref) {
          _names.registerName.add(name);
          register(name, options);
          _names.registerName.delete(name);
          field = get(_fields, name);
          const fieldRef = isUndefined(ref.value) ? ref.querySelectorAll ? ref.querySelectorAll("input,select,textarea")[0] || ref : ref : ref;
          const radioOrCheckbox = isRadioOrCheckbox(fieldRef);
          const refs = field._f.refs || [];
          if (radioOrCheckbox ? refs.find((option) => option === fieldRef) : fieldRef === field._f.ref) {
            return;
          }
          const newField = {
            ...field._f
          };
          if (radioOrCheckbox) {
            newField.refs = [
              ...refs.filter(live),
              fieldRef,
              ...Array.isArray(get(_defaultValues, name)) ? [{}] : []
            ];
            newField.ref = { type: fieldRef.type, name };
          } else {
            newField.ref = fieldRef;
            delete newField.refs;
          }
          set(_fields, name, {
            _f: newField
          });
          updateValidAndValue(name, false, void 0, fieldRef);
        } else {
          field = get(_fields, name, {});
          if (field._f) {
            field._f.mount = false;
          }
          (_options.shouldUnregister || options.shouldUnregister) && !(isNameInFieldArray(_names.array, name) && _state.action) && _names.unMount.add(name);
        }
      }
    };
  };
  const _focusError = () => _options.shouldFocusError && !_options.shouldUseNativeValidation && iterateFieldsByAction(_fields, _focusInput, _names.mount);
  const _disableForm = (disabled) => {
    if (isBoolean(disabled)) {
      _subjects.state.next({ disabled });
      iterateFieldsByAction(_fields, (ref, name) => {
        const currentField = get(_fields, name);
        if (currentField) {
          ref.disabled = currentField._f.disabled || disabled;
          if (Array.isArray(currentField._f.refs)) {
            currentField._f.refs.forEach((inputRef) => {
              inputRef.disabled = currentField._f.disabled || disabled;
            });
          }
        }
      }, 0);
    }
  };
  const handleSubmit = (onValid, onInvalid) => async (e) => {
    let result = void 0;
    let onValidError = void 0;
    let onValidFailed = false;
    if (e) {
      e.preventDefault && e.preventDefault();
      e.persist && e.persist();
    }
    let fieldValues = cloneObject(_formValues);
    _subjects.state.next({
      isSubmitting: true
    });
    const resetCallId = _resetCallId;
    const onSubmitError = (error) => {
      if (resetCallId === _resetCallId) {
        _subjects.state.next({ isSubmitting: false });
      }
      throw error;
    };
    if (_options.resolver) {
      let validationResult;
      try {
        validationResult = await _runSchema();
      } catch (error) {
        onSubmitError(error);
      }
      const { errors, values } = validationResult;
      if (resetCallId !== _resetCallId) {
        return;
      }
      _updateIsValidating();
      Object.keys(delayErrorCallbacks).forEach(cancelDelayedError);
      _formState.errors = errors;
      fieldValues = cloneObject(values);
    } else {
      try {
        await executeBuiltInValidation({
          fields: _fields,
          eventType: EVENTS.SUBMIT
        });
      } catch (error) {
        onSubmitError(error);
      }
      if (resetCallId !== _resetCallId) {
        return;
      }
      unset(_formState.errors, ROOT_ERROR_TYPE);
      _formState.errors = { ..._formState.errors };
    }
    if (_names.disabled.size && (isObject(fieldValues) || Array.isArray(fieldValues))) {
      for (const name of _names.disabled) {
        unset(fieldValues, name);
      }
    }
    if (isEmptyObject(_formState.errors)) {
      _subjects.state.next({
        errors: {}
      });
      try {
        result = await onValid(fieldValues, e);
      } catch (error) {
        onValidFailed = true;
        onValidError = error;
      }
    } else {
      if (onInvalid) {
        try {
          await onInvalid({ ..._formState.errors }, e);
        } catch (error) {
          onSubmitError(error);
        }
      }
      if (resetCallId === _resetCallId) {
        _focusError();
        setTimeout(_focusError);
      }
    }
    if (resetCallId === _resetCallId) {
      _subjects.state.next({
        isSubmitted: true,
        isSubmitting: false,
        isSubmitSuccessful: isEmptyObject(_formState.errors) && !onValidFailed,
        submitCount: _formState.submitCount + 1,
        errors: _formState.errors
      });
    }
    if (onValidFailed) {
      throw onValidError;
    }
    return result;
  };
  const resetField = (name, options = {}) => {
    if (get(_fields, name)) {
      _updateValidatingFields([name]);
      if (isUndefined(options.defaultValue)) {
        setValue(name, cloneObject(get(_defaultValues, name)));
      } else {
        setValue(name, options.defaultValue);
        _defaultValues = cloneObject(_defaultValues);
        set(_defaultValues, name, cloneObject(options.defaultValue));
      }
      if (!options.keepTouched) {
        unset(_formState.touchedFields, name);
      }
      if (!options.keepDirty) {
        unset(_formState.dirtyFields, name);
        _formState.isDirty = !isUndefined(options.defaultValue) ? _getDirty(name, cloneObject(get(_defaultValues, name))) : _getDirty();
      }
      if (!options.keepError) {
        cancelDelayedErrorTree(name);
        unset(_formState.errors, name);
        _formState.errors = { ..._formState.errors };
        _setValid();
      }
      _subjects.state.next({
        ..._formState,
        isValidating: !isEmptyObject(_formState.validatingFields)
      });
    }
  };
  const _reset = (formValues, keepStateOptions = {}) => {
    _resetCallId++;
    const updatedValues = formValues ? cloneObject(formValues) : _defaultValues;
    const cloneUpdatedValues = cloneObject(updatedValues);
    const isEmptyResetValues = isEmptyObject(formValues);
    const values = cloneUpdatedValues;
    const fieldRefs = _fields;
    Object.keys(delayErrorCallbacks).forEach(cancelDelayedError);
    if (!keepStateOptions.keepDefaultValues) {
      _defaultValues = updatedValues;
    }
    if (!keepStateOptions.keepValues) {
      if (keepStateOptions.keepDirtyValues) {
        const fieldsToCheck = /* @__PURE__ */ new Set([
          ..._names.mount,
          ...collectDirtyFieldNames(getDirtyFields(_defaultValues, _formValues, void 0, fieldRefs), _formState.dirtyFields)
        ]);
        for (const fieldName of fieldsToCheck) {
          const isDirty = get(_formState.dirtyFields, fieldName);
          const existingValue = get(_formValues, fieldName);
          const newValue = get(values, fieldName);
          if (isDirty && !isUndefined(existingValue)) {
            set(values, fieldName, existingValue);
          } else if (!isDirty && !isUndefined(newValue)) {
            setValue(fieldName, newValue);
          }
        }
      } else {
        if (isWeb && isUndefined(formValues)) {
          for (const name of _names.mount) {
            const field = get(_fields, name);
            if (field && field._f) {
              const fieldReference = Array.isArray(field._f.refs) ? field._f.refs[0] : field._f.ref;
              if (isHTMLElement(fieldReference)) {
                const form = fieldReference.closest("form");
                if (form) {
                  form.reset();
                  break;
                }
              }
            }
          }
        }
        if (keepStateOptions.keepFieldsRef) {
          for (const fieldName of _names.mount) {
            setValue(fieldName, get(values, fieldName));
          }
        } else {
          _fields = {};
        }
      }
      if (_options.shouldUnregister) {
        _formValues = keepStateOptions.keepDefaultValues ? cloneObject(_defaultValues) : {};
        if (keepStateOptions.keepFieldsRef) {
          for (const fieldName of _names.mount) {
            set(_formValues, fieldName, get(values, fieldName));
          }
        }
      } else {
        _formValues = cloneObject(values);
      }
      _subjects.array.next({
        values: { ...values }
      });
      _subjects.state.next({
        name: void 0,
        type: void 0,
        values: { ...values }
      });
    }
    _names = {
      mount: keepStateOptions.keepDirtyValues || keepStateOptions.keepFieldsRef ? _names.mount : /* @__PURE__ */ new Set(),
      unMount: /* @__PURE__ */ new Set(),
      array: /* @__PURE__ */ new Set(),
      registerName: /* @__PURE__ */ new Set(),
      disabled: keepStateOptions.keepValues || keepStateOptions.keepDirtyValues || keepStateOptions.keepFieldsRef ? new Set([..._names.disabled].filter((name) => get(_fields, name))) : /* @__PURE__ */ new Set(),
      watch: /* @__PURE__ */ new Set(),
      watchAll: false,
      focus: ""
    };
    _state.mount = !_proxyFormState.isValid || !!keepStateOptions.keepIsValid || !!keepStateOptions.keepDirtyValues || !_options.shouldUnregister && !isEmptyObject(values);
    _state.watch = !!_options.shouldUnregister;
    _state.keepIsValid = !!keepStateOptions.keepIsValid;
    _state.action = false;
    _state.actionArrayLengths.clear();
    _dirtyFieldsStale = false;
    if (!keepStateOptions.keepErrors) {
      _formState.errors = {};
    }
    _subjects.state.next({
      submitCount: keepStateOptions.keepSubmitCount ? _formState.submitCount : 0,
      isDirty: isEmptyResetValues ? false : keepStateOptions.keepDirty ? _formState.isDirty : keepStateOptions.keepValues ? _getDirty() : !!(keepStateOptions.keepDefaultValues && formValues && !deepEqual(formValues, _defaultValues)),
      isSubmitted: keepStateOptions.keepIsSubmitted ? _formState.isSubmitted : false,
      dirtyFields: isEmptyResetValues ? {} : keepStateOptions.keepDirtyValues ? keepStateOptions.keepDefaultValues && _formValues ? getDirtyFields(_defaultValues, _formValues, void 0, fieldRefs) : _formState.dirtyFields : keepStateOptions.keepDefaultValues && formValues ? getDirtyFields(_defaultValues, formValues, void 0, fieldRefs) : keepStateOptions.keepDirty ? _formState.dirtyFields : keepStateOptions.keepValues ? getDirtyFields(_defaultValues, _formValues, void 0, fieldRefs) : {},
      touchedFields: keepStateOptions.keepTouched ? _formState.touchedFields : {},
      ...!keepStateOptions.keepIsValidating && (_formState.isValidating || !isEmptyObject(_formState.validatingFields)) ? { validatingFields: {}, isValidating: false } : null,
      errors: keepStateOptions.keepErrors ? _formState.errors : {},
      isSubmitSuccessful: keepStateOptions.keepIsSubmitSuccessful ? _formState.isSubmitSuccessful : false,
      isSubmitting: false,
      defaultValues: _defaultValues
    });
  };
  const reset = (formValues, keepStateOptions) => _reset(isFunction(formValues) ? formValues(_formValues) : formValues, {
    ..._options.resetOptions,
    ...keepStateOptions
  });
  const setFocus = (name, options = {}) => {
    const field = get(_fields, name);
    const fieldReference = field && field._f;
    if (fieldReference) {
      const fieldRef = fieldReference.refs ? fieldReference.refs.find((ref) => !ref.disabled) || fieldReference.refs[0] : fieldReference.ref;
      if (fieldRef.focus) {
        setTimeout(() => {
          fieldRef.focus();
          options.shouldSelect && isFunction(fieldRef.select) && fieldRef.select();
        });
      }
    }
  };
  const _setFormState = (updatedFormState) => {
    const { name, type, values, ...formState } = updatedFormState;
    if (Object.keys(formState).length) {
      _formState = {
        ..._formState,
        ...formState
      };
    }
  };
  _subjects.state.subscribe({ next: _setFormState });
  const _resetDefaultValues = () => isFunction(_options.defaultValues) && _options.defaultValues().then((values) => {
    reset(values, _options.resetOptions);
    _subjects.state.next({
      isLoading: false
    });
  });
  const resetDefaultValues = (values, options = {}) => {
    _defaultValues = cloneObject(values);
    if (!options.keepDirty) {
      const newDirtyFields = getDirtyFields(_defaultValues, _formValues, void 0, _fields);
      _formState.dirtyFields = newDirtyFields;
      _formState.isDirty = !isEmptyObject(newDirtyFields);
    }
    if (!options.keepIsValid) {
      _setValid();
    }
    _subjects.state.next({
      ..._formState,
      defaultValues: _defaultValues
    });
  };
  const methods = {
    control: {
      register,
      unregister,
      getFieldState,
      handleSubmit,
      setError,
      _subscribe,
      _runSchema,
      _updateIsValidating,
      _focusError,
      _getWatch,
      _getDirty,
      _setValid,
      _setFieldArray,
      _setDisabledField,
      _setErrors,
      _getFieldArray,
      _reset,
      _resetDefaultValues,
      _removeUnmounted,
      _disableForm,
      _subjects,
      _proxyFormState,
      get _fields() {
        return _fields;
      },
      get _formValues() {
        return _formValues;
      },
      get _state() {
        return _state;
      },
      set _state(value) {
        _state = value;
      },
      get _defaultValues() {
        return _defaultValues;
      },
      get _names() {
        return _names;
      },
      set _names(value) {
        _names = value;
      },
      get _formState() {
        return _formState;
      },
      get _options() {
        return _options;
      },
      set _options(value) {
        _options = {
          ..._options,
          ...value
        };
        _validationModeBeforeSubmit = getValidationModes(_options.mode);
        _validationModeAfterSubmit = getValidationModes(_options.reValidateMode);
        shouldDisplayAllAssociatedErrors = _options.criteriaMode === VALIDATION_MODE.all;
      }
    },
    subscribe,
    trigger,
    register,
    handleSubmit,
    watch,
    setValue,
    setValues,
    getValues,
    getErrors,
    reset,
    resetField,
    resetDefaultValues,
    clearErrors,
    unregister,
    setError,
    setFocus,
    getFieldState
  };
  return {
    ...methods,
    formControl: methods
  };
}
var RESET_OPTIONS = {
  mode: defaultOptions.mode,
  reValidateMode: defaultOptions.reValidateMode,
  shouldFocusError: defaultOptions.shouldFocusError,
  resolver: void 0,
  criteriaMode: void 0,
  delayError: void 0,
  shouldUnregister: void 0,
  shouldUseNativeValidation: void 0,
  context: void 0,
  progressive: void 0,
  resetOptions: void 0
};
function useForm(props = {}) {
  const _formControl = React.useRef(void 0);
  const _values = React.useRef(void 0);
  const _formControlProp = React.useRef(props.formControl);
  const _hadDisabled = React.useRef(false);
  const _hadValidate = React.useRef(!!props.validate);
  const _hadErrors = React.useRef(!!props.errors);
  const _isMounted = React.useRef(false);
  const [formState, updateFormState] = React.useState(() => ({
    ...cloneObject(DEFAULT_FORM_STATE),
    isLoading: isFunction(props.defaultValues),
    errors: props.errors || {},
    disabled: props.disabled || false,
    defaultValues: isFunction(props.defaultValues) ? void 0 : props.defaultValues
  }));
  if (!_formControl.current || props.formControl && _formControlProp.current !== props.formControl) {
    _formControlProp.current = props.formControl;
    _hadDisabled.current = false;
    if (props.formControl) {
      _formControl.current = {
        ...props.formControl,
        formState
      };
      if (props.defaultValues && !isFunction(props.defaultValues)) {
        props.formControl.reset(props.defaultValues, props.resetOptions);
      }
    } else {
      const { formControl, ...rest } = createFormControl(props);
      _formControl.current = {
        ...rest,
        formState
      };
    }
  }
  const control = _formControl.current.control;
  const previousOptions = control._options;
  const removedOptions = {};
  for (const key in RESET_OPTIONS) {
    if (key in previousOptions && !(key in props)) {
      removedOptions[key] = RESET_OPTIONS[key];
    }
  }
  if (_hadDisabled.current && !props.disabled) {
    removedOptions.disabled = false;
  }
  control._options = {
    ...removedOptions,
    ...props,
    validate: props.validate
  };
  const getCurrentFormState = () => ({
    ...control._formState,
    defaultValues: control._defaultValues
  });
  const { resyncIfNeeded, snapshot } = useResyncOnReconnect(getCurrentFormState, cloneFormState);
  useIsomorphicLayoutEffect(() => {
    resyncIfNeeded(true, getCurrentFormState, updateFormState);
    const unsubscribe = control._subscribe({
      formState: control._proxyFormState,
      callback: () => updateFormState({
        ...control._formState,
        defaultValues: control._defaultValues
      }),
      reRenderRoot: true
    });
    updateFormState((data) => ({
      ...data,
      isReady: true
    }));
    control._formState.isReady = true;
    control._subjects.state.next({ isReady: true });
    return () => {
      unsubscribe();
      snapshot(true, getCurrentFormState);
    };
  }, [control, resyncIfNeeded, snapshot]);
  React.useEffect(() => {
    control._disableForm(_hadDisabled.current && !props.disabled ? false : props.disabled);
    _hadDisabled.current = !!props.disabled;
  }, [control, props.disabled]);
  React.useEffect(() => {
    if (props.mode) {
      control._options.mode = props.mode;
    }
    if (props.reValidateMode) {
      control._options.reValidateMode = props.reValidateMode;
    }
  }, [control, props.mode, props.reValidateMode]);
  React.useEffect(() => {
    if (props.errors) {
      control._setErrors(props.errors);
      control._focusError();
    } else if (_hadErrors.current) {
      control._setErrors({});
    }
    _hadErrors.current = !!props.errors;
  }, [control, props.errors]);
  React.useEffect(() => {
    if (_hadValidate.current && !props.validate) {
      _formControl.current && _formControl.current.clearErrors(FORM_ERROR_TYPE);
    }
    _hadValidate.current = !!props.validate;
  }, [props.validate]);
  React.useEffect(() => {
    props.shouldUnregister && control._subjects.state.next({
      values: control._getWatch()
    });
  }, [control, props.shouldUnregister]);
  React.useEffect(() => {
    if (control._proxyFormState.isDirty) {
      const isDirty = control._getDirty();
      if (isDirty !== formState.isDirty) {
        control._subjects.state.next({
          isDirty
        });
      }
    }
  }, [control, formState.isDirty]);
  React.useEffect(() => {
    if (props.values && !deepEqual(props.values, _values.current)) {
      control._reset(props.values, {
        keepFieldsRef: true,
        ...control._options.resetOptions
      });
      if (!(control._options.resetOptions && control._options.resetOptions.keepIsValid)) {
        control._setValid();
      }
      _values.current = props.values;
      updateFormState((state) => ({ ...state }));
    } else {
      control._resetDefaultValues();
    }
  }, [control, props.values]);
  React.useEffect(() => {
    if (!control._state.mount || !_isMounted.current && !props.values) {
      control._setValid();
      control._state.mount = true;
    }
    _isMounted.current = true;
    if (control._state.watch) {
      control._state.watch = false;
      control._subjects.state.next({ ...control._formState });
    }
    control._removeUnmounted();
  });
  _formControl.current.formState = React.useMemo(() => getProxyFormState(formState, control), [control, formState]);
  return _formControl.current;
}
var HookFormContext = React.createContext(null);
HookFormContext.displayName = "HookFormContext";
var useFormContext = () => React.useContext(HookFormContext);
var FormProvider = ({ children, watch, getValues, getErrors, getFieldState, setError, clearErrors, setValue, setValues, trigger, formState, resetField, reset, resetDefaultValues, handleSubmit, unregister, control, register, setFocus, subscribe }) => {
  const memoizedValue = React.useMemo(() => ({
    watch,
    getValues,
    getErrors,
    getFieldState,
    setError,
    clearErrors,
    setValue,
    setValues,
    trigger,
    formState,
    resetField,
    reset,
    resetDefaultValues,
    handleSubmit,
    unregister,
    control,
    register,
    setFocus,
    subscribe
  }), [
    clearErrors,
    control,
    formState,
    getErrors,
    getFieldState,
    getValues,
    handleSubmit,
    register,
    reset,
    resetDefaultValues,
    resetField,
    setError,
    setFocus,
    setValue,
    setValues,
    subscribe,
    trigger,
    unregister,
    watch
  ]);
  return React.createElement(
    HookFormContext.Provider,
    { value: memoizedValue },
    React.createElement(HookFormControlContext.Provider, { value: memoizedValue.control }, children)
  );
};
var Watch = (props) => props.render(useWatch({ name: props.names, ...props }));
export {
  Controller,
  ErrorMessage,
  FieldArray,
  Form,
  FormProvider,
  FormState,
  FormStateSubscribe,
  Watch,
  appendErrors,
  createFormControl,
  get,
  set,
  useController,
  useFieldArray,
  useForm,
  useFormContext,
  useFormState,
  useWatch
};
//# sourceMappingURL=react-hook-form.dev.js.map
