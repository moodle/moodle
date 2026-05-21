var w=Object.defineProperty;var o=(t,e)=>w(t,"name",{value:e,configurable:!0});import{isProfilerEnabled as p}from"@moodle/lms/core/profiler";import{mountReactApp as E,unmountReactApp as M}from"@moodle/lms/core/mount";var c="[data-react-component]",d="reactMounted",a="reactMounting",l=new WeakMap,r=p(),g=o(()=>document.readyState==="loading"?new Promise(t=>document.addEventListener("DOMContentLoaded",t,{once:!0})):Promise.resolve(),"domReady"),h=o(t=>{let e=t.getAttribute("data-react-props");if(!e)return{};try{return JSON.parse(e)}catch(n){return window.console.error("[react_autoinit] invalid JSON",e,n),{}}},"parseProps"),y=o(async t=>{if(!t)return null;if(!t.startsWith("@moodle/lms/"))return window.console.error("[react_autoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",t),null;try{return r&&window.console.log(`[react_autoinit] Loading: ${t}`),await import(t)}catch(e){return window.console.error(`[react_autoinit] Failed to import: ${t}`,e),null}},"resolveComponent"),L=o((t,e,n)=>{let s=t.getAttribute("data-react-component")||"Unknown",i=E(t,e,n,{id:s});l.set(t,i)},"mountReactComponent"),u=o(async t=>{if(t.dataset[d]||t.dataset[a])return;t.dataset[a]="1";let e=t.getAttribute("data-react-component");if(!e){delete t.dataset[a];return}let n=await y(e);if(!n){window.console.warn("[react_autoinit] Component not found:",e),delete t.dataset[a];return}let s=n.default;if(!s){window.console.warn("[react_autoinit] Module has no default export:",e),delete t.dataset[a];return}try{let i=h(t);L(t,s,i),t.dataset[d]="1",r&&window.console.log(`[react_autoinit] Mounted via default: ${e}`)}catch(i){window.console.error("[react_autoinit] Mount failed:",e,i)}finally{delete t.dataset[a]}},"mountOne"),m=o(t=>{let e=l.get(t)??(()=>M(t));if(e){try{if(e(),r){let n=t.getAttribute("data-react-component");window.console.log(`[react_autoinit] Unmounted: ${n}`)}}catch(n){window.console.error("[react_autoinit] Error unmounting:",n)}l.delete(t)}delete t.dataset[d],delete t.dataset[a]},"unmountOne"),v=o(t=>{let e=t.querySelectorAll(c);r&&e.length>0&&window.console.log(`[react_autoinit] Found ${e.length} component(s) to mount`);for(let n of e)u(n)},"scanAndMount"),_=o(t=>{t instanceof Element&&(t.matches?.(c)&&(r&&window.console.log("[react_autoinit] New component detected"),u(t)),t.querySelectorAll?.(c).forEach(u))},"handleAddedNode"),b=o(t=>{t instanceof Element&&(t.matches?.(c)&&m(t),t.querySelectorAll?.(c).forEach(m))},"handleRemovedNode"),A=o(()=>{let t=new MutationObserver(e=>{e.forEach(n=>{n.addedNodes?.forEach(_),n.removedNodes?.forEach(b)})});return t.observe(document.documentElement,{childList:!0,subtree:!0}),t},"installObserver"),f=null,T=o(async()=>{await g(),r&&window.console.log("[react_autoinit] Initializing (profiling enabled)..."),f||(f=A(),r&&window.console.log("[react_autoinit] MutationObserver active")),v(document)},"init");T();
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
