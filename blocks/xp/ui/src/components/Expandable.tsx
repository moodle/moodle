import React from "react";
import AnimateHeight from "react-animate-height";

export default function Expandable({ expanded, children, id }: { expanded?: boolean; children: React.ReactNode; id?: string }) {
  return (
    <AnimateHeight
      id={id}
      height={expanded ? "auto" : 0}
      applyInlineTransitions={false}
      animationStateClasses={{
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
      }}
    >
      {children}
    </AnimateHeight>
  );
}
