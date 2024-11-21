import React, { Children, createContext, useCallback, useEffect, useRef, useState } from "react";
import { getModule } from "../lib/moodle";
import { ChevronLeftIconSolid } from "./Icons";
import Str from "./Str";
import { CircleButton } from "./Button";
import { useString } from "../lib/hooks";

const slideClasses = "xp-absolute xp-inset-0 xp-transform-gpu xp-transition-transform xp-duration-300";
const slideNextClasses = `${slideClasses} xp-translate-x-full`;
const slidePrevClasses = `${slideClasses} xp--translate-x-full`;

export const Slider = ({ children: rawChildren, index }: { index: number; children: React.ReactNode[] }) => {
  const [internalIndex, setInternalIndex] = useState(index);
  const slidesRef = useRef<(HTMLDivElement | null)[]>([]);
  const children = Children.toArray(rawChildren).filter(Boolean);
  const nSlides = children.length;

  // When the number of slides changes.
  useEffect(() => {
    slidesRef.current = slidesRef.current.slice(0, nSlides);
  }, [nSlides]);

  // Effects when the current slide changes.
  useEffect(() => {
    const Aria = getModule("core/aria");
    slidesRef.current.forEach((item, i) => {
      if (i === internalIndex) {
        Aria.unhide(item);
        item?.focus();
      } else {
        Aria.hide(item);
      }
    });
  }, [internalIndex]);

  // When the index changes, update the local one. We do this to let a child render the slide that
  // we should transition to before we update the internal index that would render the child instantly.
  // This allows for the number of slides to be dynamically created by the parent.
  useEffect(() => {
    setInternalIndex(index);
  }, [index]);

  return (
    <div className="xp-w-full xp-h-full xp-overflow-hidden xp-relative">
      {Children.map(children, (child, i) => {
        const isActive = i === internalIndex;
        const isPast = i < internalIndex;
        return (
          <div
            ref={(el) => (slidesRef.current[i] = el)}
            className={isActive ? slideClasses : isPast ? slidePrevClasses : slideNextClasses}
          >
            {/* Firefox requires the vertical scroll to be in the child element, else something odd happens. */}
            {child}
          </div>
        );
      })}
    </div>
  );
};

export const SliderTester = () => {
  const [index, setIndex] = useState(0);
  return (
    <div>
      <div className="xp-h-[500px] xp-w-full">
        <Slider index={index}>
          <Slide>
            Slide 1<div className="xp-w-4" style={{ height: "1000px" }}></div>
          </Slide>
          <Slide>
            Slide 2<div className="xp-w-4" style={{ height: "100px" }}></div>
          </Slide>
          <Slide>
            Slide 3<div className="xp-w-4" style={{ height: "2000px" }}></div>
          </Slide>
          <Slide>
            Slide 4<div className="xp-w-4" style={{ height: "500px" }}></div>
          </Slide>
          <Slide>
            Slide 5<div className="xp-w-4" style={{ height: "1500px" }}></div>
          </Slide>
        </Slider>
      </div>
      <button onClick={() => setIndex((i) => Math.max(0, Math.min(5 - 1, i - 1)))}>Prev</button>
      <button onClick={() => setIndex((i) => Math.max(0, Math.min(5 - 1, i + 1)))}>Next</button>
    </div>
  );
};

export const Slide = ({
  children,
  header,
  footer,
}: {
  children: React.ReactNode;
  header?: React.ReactNode;
  footer?: React.ReactNode;
}) => {
  /* Firefox requires the vertical scroll to be in the child element, else something odd happens. */
  return (
    <div className="xp-w-full xp-h-full xp-flex xp-flex-col">
      {header}
      <div className="xp-flex xp-flex-col xp-grow xp-overflow-y-auto">{children}</div>
      {footer}
    </div>
  );
};

export const SlideHeader = ({
  children,
  title,
  hasBack,
  onBack,
}: {
  children?: React.ReactNode;
  hasBack?: boolean;
  onBack?: () => void;
  title?: React.ReactNode;
}) => {
  return (
    <div className="xp-mb-2">
      <div className="xp-flex xp-flex-row xp-items-center xp-gap-4">
        {hasBack ? (
          <div className="shrink-0 xp-grow-0">
            <CircleButton onClick={onBack} type="button" className="xp--mr-2">
              <ChevronLeftIconSolid className="xp-h-6 xp-w-6" />
              <span className="xp-sr-only">
                <Str id="back" component="core" />
              </span>
            </CircleButton>
          </div>
        ) : null}
        <div className="xp-flex-1 xp-text-lg xp-font-bold">{title}</div>
      </div>
      {children}
    </div>
  );
};

export const SlideHeaderWithFilter = ({
  hasBack,
  onBack,
  onFilterChange,
  filterValue,
  filterPlaceholder,
  title,
}: {
  hasBack?: boolean;
  onBack?: () => void;
  onFilterChange?: (value: string) => void;
  filterValue?: string;
  filterPlaceholder?: string;
  title?: React.ReactNode;
}) => {
  const filterStr = useString("filterellipsis");
  const handleChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      onFilterChange && onFilterChange(e.currentTarget.value || "");
    },
    [onFilterChange]
  );

  return (
    <SlideHeader hasBack={hasBack} onBack={onBack} title={title}>
      <div className="xp-mt-0.5">
        <input
          className="form-control xp-w-full"
          type="text"
          value={filterValue || ""}
          placeholder={filterPlaceholder || filterStr}
          onChange={handleChange}
        />
      </div>
    </SlideHeader>
  );
};
