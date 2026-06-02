/**
 * ESM wrapper for the core/fetch AMD module.
 *
 * @module     core/fetch
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { requireAsync } from "@moodle/lms/core/amd";
var fetch_default = await requireAsync("core/fetch");
export {
  fetch_default as default
};
//# sourceMappingURL=fetch.dev.js.map
