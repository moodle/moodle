import React, { useCallback, useContext, useEffect, useMemo, useRef, useState } from "react";
import { getModuleAsync, getString, hasString, isBehatRunning, loadString, loadStrings } from "./moodle";
import { AddonContext } from "./contexts";
import { getUniqueId } from "./utils";

export const useAddonActivated = () => {
  return useContext(AddonContext).activated;
};

export const useAnchorButtonProps = (onClick: () => void) => {
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
export const useDuplicatedActionPreventor = (msDelay = 100) => {
  const ref = useRef<number>();
  return useCallback(() => {
    if (ref.current && ref.current > Date.now() - msDelay) {
      return false;
    }
    ref.current = Date.now();
    return true
  }, []);
}

export const useModules = (modules: string[]) => {
  const modulesPromise = useRef<Promise<any>>();
  const modulesRef = useRef<Record<string, any>>();
  const [ready, setReady] = useState(false);

  useEffect(() => {
    if (modulesRef.current) return;

    if (!modulesPromise.current) {
      modulesPromise.current = Promise.all(modules.map((module) => getModuleAsync(module)));
    }

    let cancelled = false;
    modulesPromise.current.then((loadedModles) => {
      if (cancelled) return;

      modulesRef.current = modules.reduce((acc, module, i) => {
        acc[module] = loadedModles[i];
        return acc;
      }, {} as Record<string, any>);

      setReady(true);
    });
    return () => {
      cancelled = true;
    };
  });

  const getModule = useCallback(
    (module: string) => {
      if (!modulesRef.current) return null;
      return modulesRef.current[module] ?? null;
    },
    [ready, modulesRef.current]
  );

  return {
    getModule,
  };
};

export const useNumericInputProps = (value: number, onChange: (n: number) => void) => {
  const valueAsString = value.toString();
  const [externalValue, setExternalValue] = useState(valueAsString);
  const [internalValue, setInternalValue] = useState(externalValue);

  useEffect(() => {
    if (valueAsString !== externalValue) {
      setExternalValue(valueAsString);
      setInternalValue(valueAsString);
    }
  });

  const handleBlur = (e: React.FocusEvent<HTMLInputElement>) => {
    const v = parseInt(internalValue, 10) || 0;
    setExternalValue(v.toString());
    onChange(v);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setInternalValue(e.target.value.replace(/[^0-9]/, ""));
  };

  return {
    value: internalValue,
    onChange: handleChange,
    onBlur: handleBlur,
  };
};

export const useRoleButtonListeners = (onClick: () => void) => {
  const handleClick = (e: React.MouseEvent<HTMLElement>) => {
    e.preventDefault();
    onClick();
  };
  const handleKeyDown = (e: React.KeyboardEvent<HTMLElement>) => {
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

export const useUnloadCheck = (isDirty: boolean) => {
  const str = useString("changesmadereallygoaway", "core");

  useEffect(() => {
    const fn = (e: BeforeUnloadEvent) => {
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

export const useUniqueId = () => {
  const [id] = useState(getUniqueId());
  return id;
};

export const useString = (id: string, component: string = "block_xp", a?: any) => {
  const wasKnownAtMount = useMemo(() => hasString(id, component), [id, component]);
  const [isLoaded, setLoaded] = useState(false);

  // When the string changes, remove the promise.
  useEffect(() => {
    setLoaded(false);
  }, [id, component]);

  // Load the string when it is unknown.
  useEffect(() => {
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
      } catch (err) {}
    })();

    return () => {
      cancelled = true;
    };
  });

  return hasString(id, component) ? getString(id, component, a) : "​";
};

export const useStrings = <T extends string>(ids: T[], component: string = "block_xp") => {
  const idsForKey = ids.join(",");
  const allKnownAtMount = useMemo(() => ids.every((id) => hasString(id, component)), [idsForKey, component]);
  const [isLoaded, setLoaded] = useState(false);

  // When the string changes, remove the promise.
  useEffect(() => {
    setLoaded(false);
  }, [idsForKey, component]);

  // Load the string when it is unknown.
  useEffect(() => {
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
      } catch (err) {}
    })();

    return () => {
      cancelled = true;
    };
  });

  return (id: T, a?: any): string => (hasString(id, component) ? getString(id, component, a) : "​");
};
