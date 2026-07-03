import "./assets/image-fallback.js";
import { forwardRef, useEffect, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/avatar/Avatar.tsx
var allowedSizes = [
	"xs",
	"sm",
	"md",
	"lg",
	"xl",
	"xxl"
];
var Avatar = forwardRef(({ size, imageSrc, alt, initials, className, ...props }, ref) => {
	const [imgFailed, setImgFailed] = useState(false);
	useEffect(() => {
		setImgFailed(false);
	}, [imageSrc]);
	const sizeValue = size && allowedSizes.includes(size) ? size : "md";
	const showImage = !!imageSrc && !imgFailed;
	const resolvedInitials = (initials ?? "").trim().slice(0, 2);
	const showFallback = !showImage && (!resolvedInitials || sizeValue === "xs" || sizeValue === "sm");
	const classes = ["mds-avatar", `mds-avatar--${sizeValue}`];
	if (showImage) classes.push("mds-avatar--has-image");
	if (className) classes.push(className);
	const spanAriaLabel = !showImage && alt ? alt : void 0;
	return /* @__PURE__ */ jsxs("span", {
		ref,
		className: classes.join(" "),
		role: spanAriaLabel ? "img" : void 0,
		"aria-label": spanAriaLabel,
		...props,
		children: [
			/* @__PURE__ */ jsx("span", {
				className: "mds-avatar__initials",
				"aria-hidden": showImage || !!spanAriaLabel,
				children: resolvedInitials
			}),
			showFallback && /* @__PURE__ */ jsx("img", {
				className: "mds-avatar__image",
				src: "data:image/svg+xml,%3csvg%20width='96'%20height='96'%20viewBox='0%200%2096%2096'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M0%200H96V96H0V0Z'%20fill='%23E9ECEF'/%3e%3cpath%20d='M48%2017C60.1503%2017%2070%2026.8497%2070%2039C70%2048.0129%2064.5795%2055.7583%2056.8213%2059.1582C71.3221%2063.0434%2082%2076.2735%2082%2092C82%2093.3534%2081.9184%2094.688%2081.7646%2096H14.2354C14.0816%2094.688%2014%2093.3534%2014%2092C14%2076.2739%2024.6774%2063.0437%2039.1777%2059.1582C31.4199%2055.7582%2026%2048.0126%2026%2039C26%2026.8497%2035.8497%2017%2048%2017Z'%20fill='white'/%3e%3c/svg%3e",
				alt: "",
				"aria-hidden": true
			}),
			showImage && /* @__PURE__ */ jsx("img", {
				className: "mds-avatar__image",
				src: imageSrc,
				alt: alt ?? "",
				onError: () => setImgFailed(true)
			})
		]
	});
});
Avatar.displayName = "Avatar";
//#endregion
export { Avatar };

//# sourceMappingURL=Avatar.js.map