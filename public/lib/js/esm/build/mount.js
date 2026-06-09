import{createElement as p,Profiler as a}from"react";import{createRoot as c}from"react-dom/client";import{isProfilerEnabled as l,onRenderCallback as s}from"@moodle/lms/core/profiler";/**
 * Shared React mount helper with optional profiling support.
 *
 * Use this for mounting React roots so profiling behavior is consistent
 * across autoinit and manually-initialised entrypoints.
 *
 * @module     core/mount
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const o=new WeakMap;function E(e,t,i,u={}){const d=u.id||t.displayName||t.name||"ReactApp";let n=p(t,i);l()&&(n=p(a,{id:d,onRender:s},n));const r=c(e);r.render(n);const m=()=>{r.unmount()};return o.set(e,m),m}function P(e){const t=o.get(e);t&&(t(),o.delete(e))}export{E as mountReactApp,P as unmountReactApp};
