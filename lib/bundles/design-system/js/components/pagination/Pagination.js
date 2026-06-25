import { calculateVisiblePageNumbers, resolvePaginationInputs, useViewportMaxVisible } from "./pagination.helpers.js";
import { useEffect, useMemo, useRef, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/pagination/Pagination.tsx
var MAX_VISIBLE_ELEMENTS = 9;
var Pagination = ({ totalPages, currentPage, onPageChange, ariaLabel = "Pagination", previousPageLabel = "Previous page", nextPageLabel = "Next page", pageLabelFormatter, variant = "full", disabled = false, className, ...props }) => {
	const { resolvedVariant, resolvedPageLabelFormatter, sanitizedTotalPages, sanitizedCurrentPage } = resolvePaginationInputs(variant, pageLabelFormatter, totalPages, currentPage);
	const viewportMaxVisible = useViewportMaxVisible();
	const [pendingCurrentPage, setPendingCurrentPage] = useState(null);
	const adaptiveResult = resolvedVariant === "full" ? viewportMaxVisible : MAX_VISIBLE_ELEMENTS;
	const effectiveVariant = resolvedVariant === "full" && adaptiveResult === null ? "grouped" : resolvedVariant;
	const maxVisible = adaptiveResult ?? MAX_VISIBLE_ELEMENTS;
	const validCurrentPage = Math.max(1, Math.min(sanitizedCurrentPage, sanitizedTotalPages));
	const previousValidCurrentPageRef = useRef(validCurrentPage);
	useEffect(() => {
		const didControlledPageChange = previousValidCurrentPageRef.current !== validCurrentPage;
		if (pendingCurrentPage !== null && (pendingCurrentPage === validCurrentPage || didControlledPageChange)) setPendingCurrentPage(null);
		previousValidCurrentPageRef.current = validCurrentPage;
	}, [validCurrentPage, pendingCurrentPage]);
	const visualCurrentPage = pendingCurrentPage ?? validCurrentPage;
	const canGoPrevious = visualCurrentPage > 1;
	const canGoNext = visualCurrentPage < sanitizedTotalPages;
	const { showBoundaryPages, pageNumbers, showLeftEllipsis, showRightEllipsis } = useMemo(() => calculateVisiblePageNumbers(visualCurrentPage, sanitizedTotalPages, maxVisible), [
		visualCurrentPage,
		sanitizedTotalPages,
		maxVisible
	]);
	if (sanitizedTotalPages < 2) return null;
	const handlePageChange = (page) => {
		if (page !== visualCurrentPage && page >= 1 && page <= sanitizedTotalPages) {
			setPendingCurrentPage(page);
			onPageChange(page);
		}
	};
	const classes = ["mds-pagination", `mds-pagination--${effectiveVariant}`];
	if (className) classes.push(className);
	const renderPageButton = (page) => /* @__PURE__ */ jsx("button", {
		type: "button",
		className: "mds-pagination__page",
		onClick: () => handlePageChange(page),
		disabled,
		"aria-label": resolvedPageLabelFormatter(page),
		"aria-current": page === visualCurrentPage ? "page" : void 0,
		"data-current": page === visualCurrentPage,
		children: page
	}, page);
	return /* @__PURE__ */ jsxs("nav", {
		className: classes.join(" "),
		"aria-label": ariaLabel,
		...props,
		children: [
			/* @__PURE__ */ jsx("button", {
				type: "button",
				className: "mds-pagination__button mds-pagination__button--prev",
				onClick: () => handlePageChange(visualCurrentPage - 1),
				disabled: disabled || !canGoPrevious,
				"aria-label": previousPageLabel,
				tabIndex: !disabled && canGoPrevious ? 0 : -1,
				children: /* @__PURE__ */ jsx("i", {
					className: "fa-solid fa-chevron-left",
					"aria-hidden": "true"
				})
			}),
			effectiveVariant === "full" && /* @__PURE__ */ jsxs("div", {
				className: "mds-pagination__pages",
				children: [
					showBoundaryPages && renderPageButton(1),
					showLeftEllipsis && /* @__PURE__ */ jsx("span", {
						className: "mds-pagination__ellipsis",
						"aria-hidden": "true",
						children: "…"
					}, "left-ellipsis"),
					pageNumbers.map(renderPageButton),
					showRightEllipsis && /* @__PURE__ */ jsx("span", {
						className: "mds-pagination__ellipsis",
						"aria-hidden": "true",
						children: "…"
					}, "right-ellipsis"),
					showBoundaryPages && renderPageButton(sanitizedTotalPages)
				]
			}),
			/* @__PURE__ */ jsx("button", {
				type: "button",
				className: "mds-pagination__button mds-pagination__button--next",
				onClick: () => handlePageChange(visualCurrentPage + 1),
				disabled: disabled || !canGoNext,
				"aria-label": nextPageLabel,
				tabIndex: !disabled && canGoNext ? 0 : -1,
				children: /* @__PURE__ */ jsx("i", {
					className: "fa-solid fa-chevron-right",
					"aria-hidden": "true"
				})
			})
		]
	});
};
//#endregion
export { Pagination };

//# sourceMappingURL=Pagination.js.map