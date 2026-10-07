var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * The core/deprecated module allows you to mark things as deprecated and warn appropriately.
 *
 * It emits a console error for non-final deprecations, or throws an Error for final ones.
 * When developer debugging is enabled (or running under Behat), a toast notification is
 * also displayed via core/notification.
 *
 * @module     core/deprecated
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example
 * import emitDeprecation from '@moodle/lms/core/deprecated';
 *
 * emitDeprecation('myFunction', {
 *     replacement: 'myNewFunction',
 *     since: '5.0',
 *     mdl: 'MDL-12345',
 * });
 */
import config from "@moodle/lms/core/config";
import { getString } from "@moodle/lms/core/stringUtils";
import { requireAsync } from "@moodle/lms/core/amd";
const hasText = /* @__PURE__ */ __name((value) => typeof value === "string" && value !== "", "hasText");
const getMessage = /* @__PURE__ */ __name(({ thing, alternativeNotice, replacement, since, reason, mdl }) => {
  const parts = ["Deprecation: "];
  if (hasText(alternativeNotice)) {
    parts.push(alternativeNotice);
  } else {
    parts.push(`${thing} has been deprecated`);
  }
  if (hasText(since)) {
    parts.push(` since ${since}`);
  }
  parts.push(".");
  if (hasText(reason)) {
    parts.push(` ${reason}`);
  }
  if (hasText(replacement)) {
    parts.push(` Please use ${replacement} instead.`);
  }
  if (hasText(mdl)) {
    parts.push(` See ${mdl} for more information.`);
  }
  return parts.join("");
}, "getMessage");
const getHtmlMessage = /* @__PURE__ */ __name(({ thing, alternativeNotice, replacement, since, reason, mdl }) => {
  const parts = ["<h2>Deprecation</h2>"];
  if (hasText(alternativeNotice)) {
    parts.push(`<p>${alternativeNotice}`);
  } else {
    parts.push(`<p><code>${thing}</code> is deprecated`);
  }
  if (since !== null) {
    parts.push(` since ${since}`);
  }
  parts.push(".</p>");
  if (hasText(reason)) {
    parts.push(`<p>${reason}</p>`);
  }
  if (hasText(replacement)) {
    parts.push(`<p>Please use <code>${replacement}</code> instead.</p>`);
  }
  if (hasText(mdl)) {
    const url = `https://moodle.atlassian.net/browse/${mdl}`;
    parts.push(`<p>See <a href="${url}" target="_blank" rel="noopener noreferrer">${mdl}</a> for more information.</p>`);
  }
  return parts.join("");
}, "getHtmlMessage");
const isIgnored = /* @__PURE__ */ __name((thing) => config.deprecationignorelist.includes(thing), "isIgnored");
const canEmit = /* @__PURE__ */ __name(() => {
  if (config.developerdebug) {
    return true;
  }
  if (document.querySelector("body.behat-site")) {
    return true;
  }
  return false;
}, "canEmit");
function emitDeprecation(thing, {
  alternativeNotice,
  replacement,
  since,
  reason,
  mdl,
  final = false,
  emit = true
} = {}) {
  if (!hasText(replacement) && !hasText(reason) && !hasText(mdl)) {
    throw new Error("You must provide at least one of replacement, reason or mdl when marking something as deprecated.");
  }
  const details = {
    thing,
    alternativeNotice,
    replacement,
    since,
    reason,
    mdl
  };
  const message = getMessage(details);
  if ((final || canEmit()) && (final || emit && !isIgnored(thing))) {
    void requireAsync("core/notification").then((notification) => {
      void notification.alert("Deprecation Warning", getHtmlMessage(details), getString("ok"));
    });
  }
  if (final) {
    throw new Error(message);
  }
  console.error(message);
}
__name(emitDeprecation, "emitDeprecation");
export {
  emitDeprecation as default
};
//# sourceMappingURL=deprecated.dev.js.map
