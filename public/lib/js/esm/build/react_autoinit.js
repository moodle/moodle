import{isProfilerEnabled as l}from"@moodle/lms/core/profiler";import{mountReactApp as m,unmountReactApp as f}from"@moodle/lms/core/mount";import p from"@moodle/lms/core/pending";/**
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
 */const a="[data-react-component]",c=new WeakMap,n=l(),M=async()=>{if(document.readyState==="loading")return new Promise(t=>{document.addEventListener("DOMContentLoaded",()=>{t()},{once:!0})})},v=t=>{const e=t.dataset.reactProps;if(e===void 0||e==="")return{};try{const o=JSON.parse(e);return typeof o!="object"||o===null?{}:o}catch(o){return console.error("[react_autoinit] invalid JSON",e,o),{}}},g=async t=>{if(!t.startsWith("@moodle/lms/")){console.error("[react_autoinit] Invalid component format, expected @moodle/lms/<component>/<path>:",t);return}try{return n&&console.log(`[react_autoinit] Loading: ${t}`),await import(t)}catch(e){console.error(`[react_autoinit] Failed to import: ${t}`,e);return}},y=(t,e,o)=>{const r=t.dataset.reactComponent??"Unknown",i=m(t,e,o,{id:r});c.set(t,i)},s=async t=>{if(t.dataset.reactMounted!==void 0||t.dataset.reactMounting!==void 0)return;t.dataset.reactMounting="1";const e=t.dataset.reactComponent;if(e===void 0||e===""){delete t.dataset.reactMounting;return}const o=new p(`reactAutoInit:${e}`);try{const r=await g(e);if(!r){console.warn("[react_autoinit] Component not found:",e);return}const i=r.default;if(!i){console.warn("[react_autoinit] Module has no default export:",e);return}y(t,i,v(t)),t.dataset.reactMounted="1",n&&console.log(`[react_autoinit] Mounted via default: ${e}`)}catch(r){console.error("[react_autoinit] Mount failed:",e,r)}finally{delete t.dataset.reactMounting,o.resolve()}},d=t=>{const e=c.get(t)??(()=>{f(t)});try{e(),n&&console.log(`[react_autoinit] Unmounted: ${t.dataset.reactComponent}`)}catch(o){console.error("[react_autoinit] Error unmounting:",o)}c.delete(t),delete t.dataset.reactMounted,delete t.dataset.reactMounting},E=t=>{const e=t.querySelectorAll(a);n&&e.length>0&&console.log(`[react_autoinit] Found ${e.length} component(s) to mount`);for(const o of e)s(o)},P=t=>{if(t instanceof HTMLElement){t.matches(a)&&(n&&console.log("[react_autoinit] New component detected"),s(t));for(const e of t.querySelectorAll(a))s(e)}},h=t=>{if(t instanceof HTMLElement){t.matches(a)&&d(t);for(const e of t.querySelectorAll(a))d(e)}},w=()=>{const t=new MutationObserver(e=>{for(const o of e)o.addedNodes.forEach(P),o.removedNodes.forEach(h)});return t.observe(document.documentElement,{childList:!0,subtree:!0}),t};let u=null;const C=async()=>{await M(),n&&console.log("[react_autoinit] Initializing (profiling enabled)..."),u===null&&(u=w()),n&&console.log("[react_autoinit] MutationObserver active"),E(document)};C();
