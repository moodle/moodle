import d from"./pending";/**
 * Utility functions.
 *
 * @module     core/utils
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */const a=(n,r)=>{let t=!1,i=!1,o;const e=function(...s){if(o=s,t){i=!0;return}n.apply(this,s),t=!0,setTimeout(()=>{const l=i;t=!1,i=!1,l&&e.apply(this,o)},r)};return e},u=new Map,f=(n,r,{pending:t=!1,cancel:i=!1}={})=>{let o=null;const e=(...s)=>{t&&!u.has(e)&&u.set(e,new d("core/utils:debounce")),o!==null&&clearTimeout(o);const l=async()=>{const c=u.get(e);u.delete(e),await n(...s),c?.resolve()};o=setTimeout(()=>{l()},r)};return i&&(e.cancel=()=>{u.get(e)?.resolve(),o!==null&&clearTimeout(o)}),e},p=n=>n!=null&&!["","moodle","core"].includes(n)?n:"core",g={throttle:a,debounce:f,getNormalisedComponent:p};var m=g;export{f as debounce,m as default,p as getNormalisedComponent,a as throttle};
