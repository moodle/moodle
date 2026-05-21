import{createElement as d,Profiler as m}from"react";var t=()=>window.M?.cfg?.jsrev===-1,l=(o,r,e,n,i,s)=>{t()&&(window.console.groupCollapsed(`[${r}] ${o} - ${e.toFixed(2)}ms`),window.console.table({Component:o,Phase:r,"Duration (ms)":e.toFixed(2),"Base Duration (ms)":n.toFixed(2),"Start Time":i.toFixed(2),"Commit Time":s.toFixed(2)}),e>16&&window.console.warn(`Slow render: ${e.toFixed(2)}ms (target: <16ms for 60fps)`),e>50&&window.console.error(`Very slow render: ${e.toFixed(2)}ms - Consider optimization!`),window.console.groupEnd())},p=()=>t()?l:void 0;function w(o,r){if(!t())return o;let e=r||o.displayName||o.name||"Component",n=i=>d(m,{id:e,onRender:l},d(o,i));return n.displayName=`withProfiler(${e})`,n}export{p as getProfilerCallback,t as isProfilerEnabled,l as onRenderCallback,w as withProfiler};
/**
 * Shared React Profiler helpers.
 *
 * @module     core/profiler
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=profiler.js.map
