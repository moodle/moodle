import React from "react";

export const AppLoading = () => {
  return (
    <div className="block_xp-react-loading">
      <div className="xp-grid xp-grid-cols-2 xp-gap-4 xp-animate-pulse">
        <div className="xp-col-span-2 xp-bg-gray-100 xp-rounded xp-h-4"></div>
        <div className="xp-bg-gray-100 xp-rounded xp-h-4"></div>
        <div className="xp-bg-gray-100 xp-rounded xp-h-4"></div>
      </div>
    </div>
  );
};
