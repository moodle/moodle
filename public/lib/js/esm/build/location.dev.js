var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Browser location utilities.
 *
 * Provides a mockable abstraction over `window.location` for navigation actions.
 *
 * @module     core/location
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function redirect(url) {
  window.location.assign(url);
}
__name(redirect, "redirect");
export {
  redirect
};
//# sourceMappingURL=location.dev.js.map
