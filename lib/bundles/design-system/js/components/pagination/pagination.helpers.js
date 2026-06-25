import { useEffect, useState } from "react";
var allowedVariants = ["full", "grouped"];
var VIEWPORT_BREAKPOINT_MEDIA_QUERIES = [
	"(min-width: 576px)",
	"(min-width: 768px)",
	"(min-width: 992px)"
];
var defaultPageLabelFormatter = (page) => `Page ${page}`;
/**
* Returns how many page-number slots to show based on the current viewport width.
* Returns null when the viewport is too narrow for even a minimal 5-item row,
* which signals an auto-collapse to grouped appearance.
*
* Thresholds align with MDS / Bootstrap 5 breakpoints:
*   xxs  < 576 px → null  (grouped, no page numbers)
*   sm  ≥ 576 px →    5
*   md  ≥ 768 px →    7
*   lg  ≥ 992 px →    9
*/
function getViewportMaxVisible() {
	if (typeof window === "undefined") return 9;
	const w = window.innerWidth;
	if (w >= 992) return 9;
	if (w >= 768) return 7;
	if (w >= 576) return 5;
	return null;
}
/**
* Calculate which page numbers to display given the current page, total pages,
* and the maximum number of visible slots (boundaries + ellipses + center pages).
*
* Slot accounting for any maxVisible M:
*   Near start / near end (1 ellipsis): 1 boundary + M-3 center + 1 ellipsis + 1 boundary
*   Middle (2 ellipses):                1 boundary + 1 ellipsis + M-4 center + 1 ellipsis + 1 boundary
*/
function calculateVisiblePageNumbers(currentPage, totalPages, maxVisible) {
	if (totalPages <= maxVisible) return {
		showBoundaryPages: false,
		showLeftEllipsis: false,
		showRightEllipsis: false,
		pageNumbers: Array.from({ length: totalPages }, (_, i) => i + 1)
	};
	const middleForSingle = maxVisible - 3;
	const middleForDouble = maxVisible - 4;
	const halfMiddle = Math.floor(middleForDouble / 2);
	const nearStartThreshold = 1 + middleForSingle - halfMiddle;
	const nearEndThreshold = totalPages - nearStartThreshold + 1;
	if (currentPage <= nearStartThreshold) {
		const startPage = 2;
		const endPage = 1 + middleForSingle;
		return {
			showBoundaryPages: true,
			showLeftEllipsis: false,
			showRightEllipsis: true,
			pageNumbers: Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
		};
	}
	if (currentPage >= nearEndThreshold) {
		const endPage = totalPages - 1;
		const startPage = endPage - middleForSingle + 1;
		return {
			showBoundaryPages: true,
			showLeftEllipsis: true,
			showRightEllipsis: false,
			pageNumbers: Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
		};
	}
	const startPage = currentPage - halfMiddle;
	const endPage = currentPage + middleForDouble - halfMiddle - 1;
	return {
		showBoundaryPages: true,
		showLeftEllipsis: true,
		showRightEllipsis: true,
		pageNumbers: Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
	};
}
function sanitizePositiveInteger(value, propName, fallbackValue) {
	if (!Number.isFinite(value)) return fallbackValue;
	const normalizedValue = Math.trunc(value);
	if (normalizedValue < 1 || normalizedValue !== value) return Math.max(1, normalizedValue);
	return normalizedValue;
}
function addMediaQueryChangeListener(mediaQueryList, listener) {
	if (typeof mediaQueryList.addEventListener === "function") {
		mediaQueryList.addEventListener("change", listener);
		return;
	}
	mediaQueryList.addListener(listener);
}
function removeMediaQueryChangeListener(mediaQueryList, listener) {
	if (typeof mediaQueryList.removeEventListener === "function") {
		mediaQueryList.removeEventListener("change", listener);
		return;
	}
	mediaQueryList.removeListener(listener);
}
function useViewportMaxVisible() {
	const [viewportMaxVisible, setViewportMaxVisible] = useState(getViewportMaxVisible);
	useEffect(() => {
		if (typeof window === "undefined") return;
		const handler = () => setViewportMaxVisible(getViewportMaxVisible());
		if (typeof window.matchMedia !== "function") {
			window.addEventListener("resize", handler);
			return () => window.removeEventListener("resize", handler);
		}
		const mediaQueryLists = VIEWPORT_BREAKPOINT_MEDIA_QUERIES.map((query) => window.matchMedia(query));
		mediaQueryLists.forEach((mediaQueryList) => {
			addMediaQueryChangeListener(mediaQueryList, handler);
		});
		return () => {
			mediaQueryLists.forEach((mediaQueryList) => {
				removeMediaQueryChangeListener(mediaQueryList, handler);
			});
		};
	}, []);
	return viewportMaxVisible;
}
function resolvePaginationVariant(variant) {
	return allowedVariants.includes(variant) ? variant : "full";
}
function resolvePageLabelFormatter(pageLabelFormatter) {
	if (pageLabelFormatter !== void 0 && typeof pageLabelFormatter !== "function");
	return typeof pageLabelFormatter === "function" ? pageLabelFormatter : defaultPageLabelFormatter;
}
function resolvePaginationInputs(variant, pageLabelFormatter, totalPages, currentPage) {
	return {
		resolvedVariant: resolvePaginationVariant(variant),
		resolvedPageLabelFormatter: resolvePageLabelFormatter(pageLabelFormatter),
		sanitizedTotalPages: sanitizePositiveInteger(totalPages, "totalPages", 1),
		sanitizedCurrentPage: sanitizePositiveInteger(currentPage, "currentPage", 1)
	};
}
//#endregion
export { calculateVisiblePageNumbers, resolvePaginationInputs, useViewportMaxVisible };

//# sourceMappingURL=pagination.helpers.js.map