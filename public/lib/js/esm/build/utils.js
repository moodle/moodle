var a=Object.defineProperty;var t=(e,i)=>a(e,"name",{value:i,configurable:!0});import d from"./pending";var p=t((e,i)=>{let u=!1,r=!1,o,n=t(function(...s){if(o=s,u){r=!0;return}e.apply(this,s),u=!0,setTimeout(()=>{let c=r;u=!1,r=!1,c&&n.apply(this,o)},i)},"run");return n},"throttle"),l=new Map,f=t((e,i,{pending:u=!1,cancel:r=!1}={})=>{let o=null,n=t((...s)=>{u&&!l.has(n)&&l.set(n,new d("core/utils:debounce")),o!==null&&clearTimeout(o),o=setTimeout(async()=>{let c=l.get(n);l.delete(n),await e.apply(void 0,s),c?.resolve()},i)},"returnedFunction");return r&&(n.cancel=()=>{l.get(n)?.resolve(),o!==null&&clearTimeout(o)}),n},"debounce"),g=t(e=>e&&e!=="moodle"&&e!=="core"?e:"core","getNormalisedComponent"),m={throttle:p,debounce:f,getNormalisedComponent:g};export{f as debounce,m as default,g as getNormalisedComponent,p as throttle};
/**
 * Utility functions.
 *
 * @module     core/utils
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
//# sourceMappingURL=utils.js.map
