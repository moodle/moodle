import{isProfilerEnabled as f}from"@moodle/lms/core/profiler";import{mountReactApp as w,unmountReactApp as p}from"@moodle/lms/core/mount";var i="[data-react-component]",s="reactMounted",o="reactMounting",d=new WeakMap,a=f(),E=()=>document.readyState==="loading"?new Promise(t=>document.addEventListener("DOMContentLoaded",t,{once:!0})):Promise.resolve(),M=t=>{let e=t.getAttribute("data-react-props");if(!e)return{};try{return JSON.parse(e)}catch(n){return window.console.error("[react_autoinit] invalid JSON",e,n),{}}},g=async t=>{if(!t)return null;if(!t.startsWith("@moodle/lms/"))return window.console.error("[react_autoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",t),null;try{return a&&window.console.log(`[react_autoinit] Loading: ${t}`),await import(t)}catch(e){return window.console.error(`[react_autoinit] Failed to import: ${t}`,e),null}},h=(t,e,n)=>{let c=t.getAttribute("data-react-component")||"Unknown",r=w(t,e,n,{id:c});d.set(t,r)},l=async t=>{if(t.dataset[s]||t.dataset[o])return;t.dataset[o]="1";let e=t.getAttribute("data-react-component");if(!e){delete t.dataset[o];return}let n=await g(e);if(!n){window.console.warn("[react_autoinit] Component not found:",e),delete t.dataset[o];return}let c=n.default;if(!c){window.console.warn("[react_autoinit] Module has no default export:",e),delete t.dataset[o];return}try{let r=M(t);h(t,c,r),t.dataset[s]="1",a&&window.console.log(`[react_autoinit] Mounted via default: ${e}`)}catch(r){window.console.error("[react_autoinit] Mount failed:",e,r)}finally{delete t.dataset[o]}},u=t=>{let e=d.get(t)??(()=>p(t));if(e){try{if(e(),a){let n=t.getAttribute("data-react-component");window.console.log(`[react_autoinit] Unmounted: ${n}`)}}catch(n){window.console.error("[react_autoinit] Error unmounting:",n)}d.delete(t)}delete t.dataset[s],delete t.dataset[o]},y=t=>{let e=t.querySelectorAll(i);a&&e.length>0&&window.console.log(`[react_autoinit] Found ${e.length} component(s) to mount`);for(let n of e)l(n)},L=t=>{t instanceof Element&&(t.matches?.(i)&&(a&&window.console.log("[react_autoinit] New component detected"),l(t)),t.querySelectorAll?.(i).forEach(l))},v=t=>{t instanceof Element&&(t.matches?.(i)&&u(t),t.querySelectorAll?.(i).forEach(u))},_=()=>{let t=new MutationObserver(e=>{e.forEach(n=>{n.addedNodes?.forEach(L),n.removedNodes?.forEach(v)})});return t.observe(document.documentElement,{childList:!0,subtree:!0}),t},m=null,b=async()=>{await E(),a&&window.console.log("[react_autoinit] Initializing (profiling enabled)..."),m||(m=_(),a&&window.console.log("[react_autoinit] MutationObserver active")),y(document)};b();
/**
 * Auto-init shim for Mustache React helper components.
 *
 * Scans the DOM for elements with the `data-react-component` attribute and
 * mounts the matching React component into each one. A MutationObserver watches
 * for dynamically injected content (AJAX, fragments) so components are mounted
 * and unmounted automatically without any additional initialiser call.
 *
 * The expected DOM contract is:
 * ```html
 *   <div
 *     data-react-component="@mod_book/viewer"
 *     data-react-props='{"title":"My Book"}'
 *   ></div>
 * ```
 *
 * @module     core/react_autoinit
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=react_autoinit.js.map
