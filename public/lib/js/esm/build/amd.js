/**
 * Promise-based AMD module loader.
 *
 * @module     core/amd
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */async function s(n){return new Promise((r,e)=>{requirejs([n],o=>{r(o)},e)})}async function u(n){return new Promise((r,e)=>{requirejs(n,(...o)=>{r(o)},e)})}export{s as requireAsync,u as requireManyAsync};
