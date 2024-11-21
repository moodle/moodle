import React, { cloneElement, useEffect } from "react";
import { getModule } from "../lib/moodle";

export const Tooltip: React.FC<{ children: React.ReactElement; content: string }> = ({ children, content }) => {
  const ref = React.useRef<HTMLElement | null>(null);

  useEffect(() => {
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
      } catch (e) {
        try {
          $(ref.current).tooltip("destroy");
        } catch (e) {}
      }
    };
  }, [content]);

  return cloneElement(children, { ref });
};
