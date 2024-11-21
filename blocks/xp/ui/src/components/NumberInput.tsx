import React, { useEffect, useRef } from "react";
import { useAnchorButtonProps, useNumericInputProps } from "../lib/hooks";
import { classNames } from "../lib/utils";
import Input from "./Input";

export const NumInput: React.FC<
  Omit<React.HTMLProps<HTMLInputElement>, "onChange" | "value"> & {
    onChange: (n: number) => void;
    selectOnFocus?: boolean;
    value: number;
  }
> = ({ className, value, onChange, selectOnFocus, ...props }) => {
  const inputProps = useNumericInputProps(value, onChange);

  const handleFocus = (e: React.FocusEvent<HTMLInputElement>) => {
    if (!selectOnFocus) return;
    e.currentTarget.select();
  };

  return <Input type="text" {...inputProps} className={className} onFocus={handleFocus} {...props} />;
};

export const PlainNumberInput: React.FC<
  Omit<React.HTMLProps<HTMLInputElement>, "onChange" | "value"> & {
    onChange: (n: number) => void;
    selectOnFocus?: boolean;
    value: number;
  }
> = ({ value, onChange, selectOnFocus, ...props }) => {
  const inputProps = useNumericInputProps(value, onChange);

  const handleFocus = (e: React.FocusEvent<HTMLInputElement>) => {
    if (!selectOnFocus) return;
    e.currentTarget.select();
  };

  return <input type="text" {...inputProps} onFocus={handleFocus} {...props} />;
};

export const NumberInputWithButtons: React.FC<{
  value: number;
  onChange: (n: number) => void;
  min?: number;
  max?: number;
  step?: number;
  suffix?: string;
  inputProps?: Omit<React.HTMLProps<HTMLInputElement>, "value" | "onChange"> & { selectOnFocus?: boolean };
}> = ({ onChange, value, min, max, suffix, step = 1, inputProps }) => {
  const hasMin = typeof min !== "undefined";
  const hasMax = typeof max !== "undefined";
  const minDisabled = hasMin && min >= value;
  const maxDisabled = hasMax && max <= value;

  const minusProps = useAnchorButtonProps(() => {
    if (minDisabled) return;
    handleChange(value - step);
  });
  const plusProps = useAnchorButtonProps(() => {
    if (maxDisabled) return;
    handleChange(value + step);
  });

  const handleChange = (n: number) => {
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
    className: classNames(
      "xp-h-auto xp-border-0 xp-text-center xp-rounded-none focus:xp-z-10",
      suffix ? "xp-pr-6" : null,
      inputClassName || "xp-w-16"
    ),
    ...remainingInputProps,
  };

  return (
    <div className="xp-inline-flex xp-rounded xp-border xp-border-solid xp-border-gray-300">
      <a
        {...minusProps}
        className={classNames(
          "xp-flex-0 xp-border-0 xp-border-gray-300 xp-border-solid xp-border-r xp-rounded-l xp-py-0.5 xp-px-1 xp-flex xp-items-center xp-justify-center",
          "focus:xp-z-10",
          minDisabled ? "xp-bg-gray-100 xp-cursor-pointer xp-text-gray-500" : "xp-bg-white xp-text-inherit"
        )}
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="xp-w-5 xp-h-5">
          <path fillRule="evenodd" d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z" clipRule="evenodd" />
        </svg>
      </a>
      <div className="xp-flex-1 xp-relative">
        <NumInput onChange={handleChange} value={value} {...allInputProps} />
        {suffix ? (
          <div className="xp-pointer-events-none xp-absolute xp-inset-y-0 xp-right-0 xp-flex xp-items-center xp-pr-2">
            <span className="xp-text-gray-500">{suffix}</span>
          </div>
        ) : null}
      </div>
      <a
        {...plusProps}
        className={classNames(
          "xp-flex-0 xp-border-0 xp-border-gray-300 xp-border-solid xp-border-l xp-rounded-r xp-py-0.5 xp-px-1 xp-flex xp-items-center xp-justify-center",
          "focus:xp-z-10",
          maxDisabled ? "xp-bg-gray-100 xp-cursor-pointer xp-text-gray-500" : "xp-bg-white xp-text-inherit"
        )}
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="xp-w-5 xp-h-5">
          <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
        </svg>
      </a>
    </div>
  );
};
