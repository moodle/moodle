import React, { useEffect, useState } from "react";
import { useString } from "../lib/hooks";

const Str = ({ id, component = "block_xp", a }: { id: string; component?: string; a?: any }) => {
  const str = useString(id, component, a);
  return <>{str || "​"}</>;
};

export default Str;
