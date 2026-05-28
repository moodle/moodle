import { activityIconNames, activityIconRegistry } from "./activityIconRegistry.js";
import { useEffect, useState } from "react";
import { jsx } from "react/jsx-runtime";
//#region components/activity-icon/ActivityIcon.tsx
var iconGlob = /* @__PURE__ */ Object.assign({
	"./assets/assignment.svg": () => import("./assets/assignment.js"),
	"./assets/bigbluebutton.svg": () => import("./assets/bigbluebutton.js"),
	"./assets/book.svg": () => import("./assets/book.js"),
	"./assets/chat.svg": () => import("./assets/chat.js"),
	"./assets/choice.svg": () => import("./assets/choice.js"),
	"./assets/database.svg": () => import("./assets/database.js"),
	"./assets/external-tool.svg": () => import("./assets/external-tool.js"),
	"./assets/feedback.svg": () => import("./assets/feedback.js"),
	"./assets/file-ai.svg": () => import("./assets/file-ai.js"),
	"./assets/file-archive.svg": () => import("./assets/file-archive.js"),
	"./assets/file-audio.svg": () => import("./assets/file-audio.js"),
	"./assets/file-code.svg": () => import("./assets/file-code.js"),
	"./assets/file-database.svg": () => import("./assets/file-database.js"),
	"./assets/file-doc.svg": () => import("./assets/file-doc.js"),
	"./assets/file-draw.svg": () => import("./assets/file-draw.js"),
	"./assets/file-eps.svg": () => import("./assets/file-eps.js"),
	"./assets/file-epub.svg": () => import("./assets/file-epub.js"),
	"./assets/file-flash.svg": () => import("./assets/file-flash.js"),
	"./assets/file-folder.svg": () => import("./assets/file-folder.js"),
	"./assets/file-gif.svg": () => import("./assets/file-gif.js"),
	"./assets/file-graphic.svg": () => import("./assets/file-graphic.js"),
	"./assets/file-h5p.svg": () => import("./assets/file-h5p.js"),
	"./assets/file-image.svg": () => import("./assets/file-image.js"),
	"./assets/file-isf-flowchart.svg": () => import("./assets/file-isf-flowchart.js"),
	"./assets/file-json.svg": () => import("./assets/file-json.js"),
	"./assets/file-math.svg": () => import("./assets/file-math.js"),
	"./assets/file-moodle.svg": () => import("./assets/file-moodle.js"),
	"./assets/file-oth.svg": () => import("./assets/file-oth.js"),
	"./assets/file-pdf.svg": () => import("./assets/file-pdf.js"),
	"./assets/file-plain-text.svg": () => import("./assets/file-plain-text.js"),
	"./assets/file-ppt.svg": () => import("./assets/file-ppt.js"),
	"./assets/file-presentation.svg": () => import("./assets/file-presentation.js"),
	"./assets/file-psd.svg": () => import("./assets/file-psd.js"),
	"./assets/file-pub.svg": () => import("./assets/file-pub.js"),
	"./assets/file-source-code.svg": () => import("./assets/file-source-code.js"),
	"./assets/file-spreadsheet.svg": () => import("./assets/file-spreadsheet.js"),
	"./assets/file-text-editor.svg": () => import("./assets/file-text-editor.js"),
	"./assets/file-unknown.svg": () => import("./assets/file-unknown.js"),
	"./assets/file-video.svg": () => import("./assets/file-video.js"),
	"./assets/file-xls.svg": () => import("./assets/file-xls.js"),
	"./assets/file.svg": () => import("./assets/file.js"),
	"./assets/folder.svg": () => import("./assets/folder.js"),
	"./assets/forum.svg": () => import("./assets/forum.js"),
	"./assets/glossary.svg": () => import("./assets/glossary.js"),
	"./assets/h5p.svg": () => import("./assets/h5p.js"),
	"./assets/ims-package.svg": () => import("./assets/ims-package.js"),
	"./assets/lesson.svg": () => import("./assets/lesson.js"),
	"./assets/page.svg": () => import("./assets/page.js"),
	"./assets/quiz.svg": () => import("./assets/quiz.js"),
	"./assets/scorm-package.svg": () => import("./assets/scorm-package.js"),
	"./assets/subsection.svg": () => import("./assets/subsection.js"),
	"./assets/survey.svg": () => import("./assets/survey.js"),
	"./assets/text-and-media.svg": () => import("./assets/text-and-media.js"),
	"./assets/url.svg": () => import("./assets/url.js"),
	"./assets/wiki.svg": () => import("./assets/wiki.js"),
	"./assets/workshop.svg": () => import("./assets/workshop.js")
});
function loadIconSrc(fileName) {
	const loader = iconGlob[`./assets/${fileName}.svg`];
	if (!loader) return Promise.reject(/* @__PURE__ */ new Error(`[MDS ActivityIcon] Icon file "${fileName}.svg" not found in assets.`));
	return loader().then((mod) => mod.default);
}
var allowedVariants = [
	"none",
	"default",
	"large"
];
var allowedSizes = [
	"sm",
	"md",
	"lg",
	"xl"
];
var iconLookupFallback = "file-unknown";
var ActivityIcon = ({ icon, alt = "", variant, size, className, ...props }) => {
	const normalizedIcon = icon?.toLowerCase();
	const hasValidIcon = normalizedIcon !== void 0 && normalizedIcon in activityIconRegistry;
	if (!hasValidIcon) {
		const invalidIconMessage = `[MDS ActivityIcon] Invalid icon "${icon}". Allowed: ${activityIconNames.join(", ")}`;
		console.error(`${invalidIconMessage}. Falling back to "${iconLookupFallback}" placeholder.`);
	}
	const resolvedIcon = hasValidIcon ? normalizedIcon : iconLookupFallback;
	const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "default";
	const resolvedSize = size && allowedSizes.includes(size) ? size : "md";
	const resolvedCategory = activityIconRegistry[resolvedIcon].category;
	const [iconSrc, setIconSrc] = useState(void 0);
	useEffect(() => {
		let isMounted = true;
		const { fileName } = activityIconRegistry[resolvedIcon];
		loadIconSrc(fileName).then((src) => {
			if (isMounted) setIconSrc(src);
		}).catch(() => {
			if (isMounted) setIconSrc(void 0);
		});
		return () => {
			isMounted = false;
		};
	}, [resolvedIcon]);
	const classes = [
		"mds-activity-icon",
		`mds-activity-icon--${resolvedVariant}`,
		`mds-activity-icon--size-${resolvedSize}`,
		`mds-activity-icon--category-${resolvedCategory}`
	];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("span", {
		className: classes.join(" "),
		...props,
		children: /* @__PURE__ */ jsx("img", {
			alt,
			className: "mds-activity-icon__asset",
			src: iconSrc
		})
	});
};
//#endregion
export { ActivityIcon };

//# sourceMappingURL=ActivityIcon.js.map