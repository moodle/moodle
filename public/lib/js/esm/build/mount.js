var c=Object.defineProperty;var n=(e,t)=>c(e,"name",{value:t,configurable:!0});import{createElement as i,Profiler as l}from"react";import{createRoot as s}from"react-dom/client";import{isProfilerEnabled as f,onRenderCallback as y}from"@moodle/lms/core/profiler";var r=new WeakMap;function b(e,t,u,d={}){let a=d.id||t.displayName||t.name||"ReactApp",o=i(t,u);f()&&(o=i(l,{id:a,onRender:y},o));let m=s(e);m.render(o);let p=n(()=>{m.unmount()},"unmount");return r.set(e,p),p}n(b,"mountReactApp");function v(e){let t=r.get(e);t&&(t(),r.delete(e))}n(v,"unmountReactApp");export{b as mountReactApp,v as unmountReactApp};
/**
 * Shared React mount helper with optional profiling support.
 *
 * Use this for mounting React roots so profiling behavior is consistent
 * across autoinit and manually-initialised entrypoints.
 *
 * @module     core/mount
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=mount.js.map
