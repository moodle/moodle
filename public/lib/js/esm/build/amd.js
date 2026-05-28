var i=Object.defineProperty;var u=(n,r)=>i(n,"name",{value:r,configurable:!0});function t(n){return new Promise((r,e)=>{requirejs([n],o=>r(o),e)})}u(t,"requireAsync");function w(n){return new Promise((r,e)=>{requirejs(n,(...o)=>r(o),e)})}u(w,"requireManyAsync");export{t as requireAsync,w as requireManyAsync};
/**
 * Promise-based AMD module loader.
 *
 * @module     core/amd
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=amd.js.map
