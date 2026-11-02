import{isProfilerEnabled as w}from"@moodle/lms/core/profiler";import{mountReactApp as p,unmountReactApp as E}from"@moodle/lms/core/mount";import A from"@moodle/lms/core/pending";/**
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
 * @module     core/reactAutoinit
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const i="[data-react-component]",s="reactMounted",a="reactMounting",d=new WeakMap,o=w(),g=()=>document.readyState==="loading"?new Promise(t=>document.addEventListener("DOMContentLoaded",t,{once:!0})):Promise.resolve(),M=t=>{const e=t.getAttribute("data-react-props");if(!e)return{};try{return JSON.parse(e)}catch(n){return window.console.error("[reactAutoinit] invalid JSON",e,n),{}}},h=async t=>{if(!t)return null;if(!t.startsWith("@moodle/lms/"))return window.console.error("[reactAutoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",t),null;try{return o&&window.console.log(`[reactAutoinit] Loading: ${t}`),await import(t)}catch(e){return window.console.error(`[reactAutoinit] Failed to import: ${t}`,e),null}},v=(t,e,n)=>{const r=t.getAttribute("data-react-component")||"Unknown",c=p(t,e,n,{id:r});d.set(t,c)},l=async t=>{if(t.dataset[s]||t.dataset[a])return;t.dataset[a]="1";const e=t.getAttribute("data-react-component");if(!e){delete t.dataset[a];return}const n=new A(`reactAutoInit:${e}`);try{const r=await h(e);if(!r){window.console.warn("[reactAutoinit] Component not found:",e);return}const c=r.default;if(!c){window.console.warn("[reactAutoinit] Module has no default export:",e);return}const f=M(t);v(t,c,f),t.dataset[s]="1",o&&window.console.log(`[reactAutoinit] Mounted via default: ${e}`)}catch(r){window.console.error("[reactAutoinit] Mount failed:",e,r)}finally{delete t.dataset[a],n.resolve()}},u=t=>{const e=d.get(t)??(()=>E(t));if(e){try{if(e(),o){const n=t.getAttribute("data-react-component");window.console.log(`[reactAutoinit] Unmounted: ${n}`)}}catch(n){window.console.error("[reactAutoinit] Error unmounting:",n)}d.delete(t)}delete t.dataset[s],delete t.dataset[a]},y=t=>{const e=t.querySelectorAll(i);o&&e.length>0&&window.console.log(`[reactAutoinit] Found ${e.length} component(s) to mount`);for(const n of e)l(n)},b=t=>{t instanceof Element&&(t.matches?.(i)&&(o&&window.console.log("[reactAutoinit] New component detected"),l(t)),t.querySelectorAll?.(i).forEach(l))},L=t=>{t instanceof Element&&(t.matches?.(i)&&u(t),t.querySelectorAll?.(i).forEach(u))},O=()=>{const t=new MutationObserver(e=>{e.forEach(n=>{n.addedNodes?.forEach(b),n.removedNodes?.forEach(L)})});return t.observe(document.documentElement,{childList:!0,subtree:!0}),t};let m=null;const T=async()=>{await g(),o&&window.console.log("[reactAutoinit] Initializing (profiling enabled)..."),m||(m=O(),o&&window.console.log("[reactAutoinit] MutationObserver active")),y(document)};T();
