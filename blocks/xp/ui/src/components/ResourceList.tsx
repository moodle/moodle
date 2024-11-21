import React, { ReactNode } from "react";
import { useRoleButtonListeners, useUniqueId } from "../lib/hooks";
import { Resource } from "../lib/types";
import { classNames } from "../lib/utils";
import Str from "./Str";

type ResourceListProps<T extends Resource> = { resources: T[]; onSelect?: (r: T) => void };

const ListEntry = <T extends Resource>({ resource, onSelect }: { resource: T; onSelect: () => void }) => {
  if (resource.type === "header") {
    return <ListEntryHeader label={resource.label} />;
  }
  return (
    <ListEntryItem
      label={resource.label}
      description={resource.description}
      isavailable={resource.isavailable}
      onSelect={onSelect}
    />
  );
};

const ListEntryItem: React.FC<{
  label: string;
  description?: string;
  isavailable?: boolean;
  onSelect: () => void;
}> = ({ label, description, isavailable = true, onSelect }) => {
  const headingId = useUniqueId();
  const buttonListeners = useRoleButtonListeners(onSelect);
  const disabledOpacityClass = `${!isavailable ? "xp-opacity-60 group-focus:xp-opacity-100 group-hover:xp-opacity-100" : ""}`;

  return (
    <div className="xp-p-[0.2rem] xp-relative xp-group focus:xp-z-10 hover:xp-bg-gray-100">
      <div tabIndex={0} role="button" aria-describedby={headingId} className="xp-px-1.5 xp-py-0.5" {...buttonListeners}>
        <div id={headingId} className={`xp-flex`}>
          <div className={classNames(disabledOpacityClass, "xp-text-medium", description ? "xp-text-xl" : "xp-text-base")}>
            {label}
          </div>
          {!isavailable ? (
            <div className="xp-ml-2">
              <span className="badge badge-pill badge-warning">
                <Str id="unavailable" />
              </span>
            </div>
          ) : null}
        </div>
        {description ? (
          <div className={classNames(disabledOpacityClass, "xp-text-gray-500")} dangerouslySetInnerHTML={{ __html: description }} />
        ) : null}
      </div>
    </div>
  );
};

const ListEntryHeader = ({ label }: { label: string }) => {
  return (
    <div className="xp-px-[0.2rem] xp-bg-gray-200 xp-mt-2 first:xp-mt-0 xp-sticky xp-top-0 xp-z-10">
      <div className="xp-px-1.5 xp-py-1 xp-text-sm xp-leading-tight xp-font-bold">{label}</div>
    </div>
  );
};

export const PlainResourceList = <T extends Resource>({
  resources,
  onSelect,
  emptyContent,
}: ResourceListProps<T> & { emptyContent?: ReactNode }) => {
  if (!resources.length) {
    return <>{emptyContent || <EmptyResult />}</>;
  }
  return (
    <div className="xp-flex-1 xp-divide-y xp-divide-gray-200">
      {resources.map((o) => {
        return <ListEntry<T> key={`${o.type || ""}${o.name}`} resource={o} onSelect={() => onSelect && onSelect(o)} />;
      })}
    </div>
  );
};

export const LoadingResourceList = () => {
  return (
    <div className="xp-flex-1">
      <div className="xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2"></div>
      <div className="xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2"></div>
      <div className="xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2"></div>
      <div className="xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2"></div>
      <div className="xp-h-8 xp-w-full xp-bg-gray-100 xp-mb-2"></div>
    </div>
  );
};

export const EmptyResult: React.FC<{
  message?: ReactNode;
  content?: ReactNode;
}> = ({ message, content }) => {
  return (
    <div className="xp-flex-1 xp-flex xp-flex-col xp-items-center xp-justify-center xp-text-center">
      <div>{message || <Str id="noneareavailable" />}</div>
      {content ? <div className="xp-my-2">{content}</div> : null}
    </div>
  );
};
