/**
 * Typed access to the Moodle page configuration (`M.cfg`).
 *
 * This module exposes the same `M.cfg` object that is injected into every Moodle page
 * by the server-side renderer, but with a full TypeScript interface so that consuming
 * modules get autocompletion and compile-time type safety.
 *
 * The default export is the live `M.cfg` object — mutations made by tests or other code
 * are immediately visible to every consumer.
 *
 * @module     core/config
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */const e=M.cfg;var n=e;const r=e.jsrev!==-1;export{n as default,r as isJSCachingEnabled};
