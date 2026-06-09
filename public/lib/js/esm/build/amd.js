/**
 * Promise-based AMD module loader.
 *
 * @module     core/amd
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function u(n){return new Promise((r,e)=>{requirejs([n],o=>r(o),e)})}function i(n){return new Promise((r,e)=>{requirejs(n,(...o)=>r(o),e)})}export{u as requireAsync,i as requireManyAsync};
