import React, { ButtonHTMLAttributes } from "react";
import { useAnchorButtonProps, useStrings } from "../lib/hooks";
import Pix from "./Pix";
import Spinner from "./Spinner";
import Str from "./Str";
import { classNames } from "../lib/utils";

export const CircleButton = ({ className, ...props }: React.ButtonHTMLAttributes<HTMLButtonElement>) => {
  return (
    <button
      className={classNames(
        "xp-bg-transparent xp-border-0 xp-p-2 xp-flex xp-items-center xp-rounded-full hover:xp-bg-gray-100",
        className
      )}
      type="button"
      {...props}
    />
  );
};

export const Button: React.FC<{
  disabled?: boolean;
  onClick?: () => void;
  label?: string;
  primary?: boolean;
  className?: React.ButtonHTMLAttributes<HTMLButtonElement>["className"];
  type?: React.ButtonHTMLAttributes<HTMLButtonElement>["type"];
}> = ({ onClick, disabled, children, primary, className, type = "button" }) => {
  const classes = classNames("btn", primary ? "btn-primary" : "btn-default btn-secondary", className);
  return (
    <button className={classes} onClick={onClick} disabled={disabled} type={type}>
      {children}
    </button>
  );
};

export const SaveButton: React.FC<{
  mutation?: any;
  disabled?: boolean;
  onClick?: () => void;
  label?: string;
  statePosition?: "before" | "after";
}> = ({ onClick, disabled, label, mutation = {}, statePosition = "after" }) => {
  const getStr = useStrings(["changessaved", "error"], "core");
  const { isLoading, isSuccess, isError } = mutation;
  const isStateBefore = statePosition === "before";

  const state = (
    <div className={`xp-w-8 xp-flex ${isStateBefore ? "xp-mr-4 xp-justify-end" : "xp-ml-4"}`} aria-live="assertive">
      {isLoading ? <Spinner /> : null}
      {isSuccess ? <Pix id="i/valid" component="core" alt={getStr("changessaved")} /> : null}
      {isError ? <Pix id="i/invalid" component="core" alt={getStr("error")} /> : null}
    </div>
  );

  return (
    <div className="xp-flex xp-items-center">
      {isStateBefore ? state : null}
      <div className="">
        <Button primary onClick={onClick} disabled={disabled || isLoading}>
          {label || <Str id="savechanges" component="core" />}
        </Button>
      </div>
      {!isStateBefore ? state : null}
    </div>
  );
};

export const AnchorButton: React.FC<{ onClick: () => void } & React.AnchorHTMLAttributes<HTMLAnchorElement>> = ({
  children,
  onClick,
  className,
  ...props
}) => {
  const anchorButtonProps = useAnchorButtonProps(onClick);
  return (
    <a className={classNames("xp-text-inherit xp-no-underline", className)} {...props} {...anchorButtonProps}>
      {children}
    </a>
  );
};
