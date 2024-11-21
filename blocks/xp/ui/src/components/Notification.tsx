import React from "react";

export const NotificationError = ({ children }: { children: React.ReactNode }) => {
  return <div className="alert alert-danger">{children}</div>;
};
