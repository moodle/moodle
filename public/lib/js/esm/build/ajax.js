var i=Object.defineProperty;var r=(e,n)=>i(e,"name",{value:n,configurable:!0});import{requireAsync as l}from"@moodle/lms/core/amd";function x(e){return typeof e=="object"&&e!==null&&"message"in e&&"errorcode"in e}r(x,"isMoodleAjaxError");var s=await l("core/ajax");function u(e){return new Promise((n,o)=>{e.then(n,o)})}r(u,"toNativePromise");function d(e,n=!0,o=!0,t=!1){let[a]=s.call([e],n,o,t);return u(a)}r(d,"fetchOne");function j(e,n=!0,o=!0,t=!1){return Promise.all(s.call(e,n,o,t).map(a=>u(a)))}r(j,"fetchMany");export{j as fetchMany,d as fetchOne,x as isMoodleAjaxError};
/**
 * ESM wrapper for the core/ajax AMD module.
 *
 * @module     core/ajax
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=ajax.js.map
