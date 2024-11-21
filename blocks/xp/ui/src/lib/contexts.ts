import { createContext } from "react";

export const makeAddonContextValueFromAppProps = (props: any) => {
  return {
    activated: false,
    enablepromo: true,
    promourl: "https://www.levelup.plus/xp/",
    ...(props?.addon ?? {}),
  };
};

export const AddonContext = createContext({
  activated: false,
  enablepromo: true,
  promourl: "https://www.levelup.plus/xp/", // Local promo page where possible.
});
