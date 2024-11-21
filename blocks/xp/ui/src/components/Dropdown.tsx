import React from "react";
import { Menu } from "@headlessui/react";
import { classNames } from "../lib/utils";

type DropdownProps = {
  buttonLabel: React.ReactNode;
  items: (
    | {
        id: string;
        divider: true;
      }
    | {
        id: string;
        label: React.ReactNode;
        danger?: boolean;
        props: Omit<React.DetailedHTMLProps<React.AnchorHTMLAttributes<HTMLAnchorElement>, HTMLAnchorElement>, "className">;
      }
  )[];
};

export const Dropdown = ({ buttonLabel, items }: DropdownProps) => {
  return (
    <Menu as="div" className="dropdown">
      <Menu.Button className="btn btn-link btn-icon icon-size-3 rounded-circle">
        <i className="fa fa-ellipsis-v text-dark py-2" aria-hidden="true"></i>
        <span className="xp-sr-only">{buttonLabel}</span>
      </Menu.Button>
      <Menu.Items className="dropdown-menu dropdown-menu-right xp-block">
        {items.map((item) => {
          if ("divider" in item) {
            return <div key={item.id} className="dropdown-divider" />;
          }
          return (
            <Menu.Item key={item.id}>
              {({ active }) => (
                <a {...item.props} className={classNames("dropdown-item", item.danger ? "text-danger" : null)}>
                  {item.label}
                </a>
              )}
            </Menu.Item>
          );
        })}
      </Menu.Items>
    </Menu>
  );
};
