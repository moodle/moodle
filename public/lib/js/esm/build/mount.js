import{createElement as m,Profiler as c}from"react";import{createRoot as s}from"react-dom/client";import{getComponentId as a,isProfilerEnabled as l,onRenderCallback as f}from"@moodle/lms/core/profiler";/**
 * Shared React mount helper with optional profiling support.
 *
 * Use this for mounting React roots so profiling behavior is consistent
 * across autoinit and manually-initialised entrypoints.
 *
 * @module     core/mount
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const o=new WeakMap;function P(t,n,i,u={}){const d=a(n,u.id,"ReactApp");let e=m(n,i);l()&&(e=m(c,{id:d,onRender:f},e));const p=s(t);p.render(e);const r=()=>{p.unmount()};return o.set(t,r),r}function g(t){const n=o.get(t);n&&(n(),o.delete(t))}export{P as mountReactApp,g as unmountReactApp};
