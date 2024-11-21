import React, { useContext } from "react";
import { AddonContext } from "../lib/contexts";
import { useStrings } from "../lib/hooks";

export const IfAddonActivatedOrPromoEnabled: React.FC = ({ children }) => {
  const { activated, enablepromo } = useContext(AddonContext);
  if (!activated && !enablepromo) {
    return null;
  }
  return <>{children}</>;
};

export const AddonRequired = () => {
  const { promourl } = useContext(AddonContext);
  const getStr = useStrings(["xpplusrequired", "unlockfeaturewithxpplus"]);
  const handleClick = (e: React.MouseEvent<HTMLAnchorElement>) => e.preventDefault();
  return (
    <a
      href="#"
      role="button"
      onClick={handleClick} /** Older popovers cause a scroll up. */
      data-toggle="popover"
      data-placement="top"
      data-container="body"
      data-content={getStr("unlockfeaturewithxpplus", promourl)}
      data-html="true"
      className="xp-py-1 xp-px-1.5 xp-normal-case xp-text-2xs xp-inline-block xp-bg-black xp-text-white xp-rounded xp-no-underline"
    >
      {getStr("xpplusrequired")}
    </a>
  );
};
