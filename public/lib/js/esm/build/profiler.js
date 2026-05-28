var f=Object.defineProperty;var n=(e,r)=>f(e,"name",{value:r,configurable:!0});import{createElement as l,Profiler as p}from"react";var d=n(()=>window.M?.cfg?.jsrev===-1,"isProfilerEnabled"),s=n((e,r,o,i,t,m)=>{d()&&(window.console.groupCollapsed(`[${r}] ${e} - ${o.toFixed(2)}ms`),window.console.table({Component:e,Phase:r,"Duration (ms)":o.toFixed(2),"Base Duration (ms)":i.toFixed(2),"Start Time":t.toFixed(2),"Commit Time":m.toFixed(2)}),o>16&&window.console.warn(`Slow render: ${o.toFixed(2)}ms (target: <16ms for 60fps)`),o>50&&window.console.error(`Very slow render: ${o.toFixed(2)}ms - Consider optimization!`),window.console.groupEnd())},"onRenderCallback"),a=n(()=>d()?s:void 0,"getProfilerCallback");function P(e,r){if(!d())return e;let o=r||e.displayName||e.name||"Component",i=n(t=>l(p,{id:o,onRender:s},l(e,t)),"ProfiledComponent");return i.displayName=`withProfiler(${o})`,i}n(P,"withProfiler");export{a as getProfilerCallback,d as isProfilerEnabled,s as onRenderCallback,P as withProfiler};
/**
 * Shared React Profiler helpers.
 *
 * @module     core/profiler
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=profiler.js.map
