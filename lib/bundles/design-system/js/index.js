import { jsx } from "react/jsx-runtime";
const allowedVariants = [
  "primary",
  "secondary",
  "danger",
  "outline-primary",
  "outline-secondary",
  "outline-danger"
];
const Button = ({
  label,
  variant,
  size,
  className,
  type = "button",
  ...props
}) => {
  const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "primary";
  const classes = ["mds-btn", "btn", `btn-${resolvedVariant}`];
  if (size) {
    classes.push(`btn-${size}`);
  }
  if (className) {
    classes.push(className);
  }
  return /* @__PURE__ */ jsx("button", { className: classes.join(" "), type, ...props, children: label });
};
export {
  Button
};
//# sourceMappingURL=index.js.map
