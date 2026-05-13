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
import { getString } from "@moodle/lms/core/String";
import { requireAsync } from "@moodle/lms/core/amd";
const getMessage = /* @__PURE__ */ __name((thing, alternativeNotice, replacement, since, reason, mdl) => {
  const parts = [];
  parts.push("Deprecation: ");
  if (alternativeNotice) {
    parts.push(alternativeNotice);
  } else {
    parts.push(`${thing} has been deprecated`);
  }
  if (since !== null) {
    parts.push(` since ${since}`);
  }
  parts.push(".");
  if (reason) {
    parts.push(` ${reason}`);
  }
  if (replacement) {
    parts.push(` Please use ${replacement} instead.`);
  }
  if (mdl) {
    parts.push(` See ${mdl} for more information.`);
  }
  return parts.join("");
}, "getMessage");
const getHTMLMessage = /* @__PURE__ */ __name((thing, alternativeNotice, replacement, since, reason, mdl) => {
  const parts = [];
  parts.push("<h2>Deprecation</h2>");
  if (alternativeNotice) {
    parts.push(`<p>${alternativeNotice}`);
  } else {
    parts.push(`<p><code>${thing}</code> is deprecated`);
  }
  if (since !== null) {
    parts.push(` since ${since}`);
  }
  parts.push(".</p>");
  if (reason) {
    parts.push(`<p>${reason}</p>`);
  }
  if (replacement) {
    parts.push(`<p>Please use <code>${replacement}</code> instead.</p>`);
  }
  if (mdl) {
    const url = `https://moodle.atlassian.net/browse/${mdl}`;
    parts.push(
      `<p>See <a href="${url}" target="_blank" rel="noopener noreferrer">${mdl}</a> for more information.</p>`
    );
  }
  return parts.join("");
}, "getHTMLMessage");
const isIgnored = /* @__PURE__ */ __name((thing) => {
  const ignored = config.deprecationignorelist || [];
  return ignored.includes(thing);
}, "isIgnored");
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
  alternativeNotice = null,
  replacement = null,
  since = null,
  reason = null,
  mdl = null,
  final = false,
  emit = true
} = {}) {
  if (replacement === null && reason === null && mdl === null) {
    throw new Error(
      "You must provide at least one of replacement, reason or mdl when marking something as deprecated."
    );
  }
  const message = getMessage(thing, alternativeNotice, replacement, since, reason, mdl);
  if (final || canEmit()) {
    if (final || emit && !isIgnored(thing)) {
      const htmlMessage = getHTMLMessage(thing, alternativeNotice, replacement, since, reason, mdl);
      requireAsync("core/notification").then((notification) => {
        return notification.alert("Deprecation Warning", htmlMessage, getString("ok"));
      });
    }
  }
  if (final) {
    throw new Error(message);
  } else {
    console.error(message);
  }
}
__name(emitDeprecation, "emitDeprecation");
export {
  emitDeprecation as default
};
//# sourceMappingURL=deprecated.dev.js.map
