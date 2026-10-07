import{createElement as d,Profiler as f}from"react";/**
 * Shared React Profiler helpers.
 *
 * @module     core/profiler
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const m=(n,o,e)=>[o,n.displayName,n.name].find(r=>r!==void 0&&r!=="")??e,i=()=>globalThis.M?.cfg?.jsrev===-1,s=(n,o,e,t,r,l)=>{i()&&(console.groupCollapsed(`[${o}] ${n} - ${e.toFixed(2)}ms`),console.table({Component:n,Phase:o,"Duration (ms)":e.toFixed(2),"Base Duration (ms)":t.toFixed(2),"Start Time":r.toFixed(2),"Commit Time":l.toFixed(2)}),e>16&&console.warn(`Slow render: ${e.toFixed(2)}ms (target: <16ms for 60fps)`),e>50&&console.error(`Very slow render: ${e.toFixed(2)}ms - Consider optimization!`),console.groupEnd())},c=()=>i()?s:void 0;function a(n,o){if(!i())return n;const e=m(n,o,"Component"),t=r=>d(f,{id:e,onRender:s},d(n,r));return t.displayName=`withProfiler(${e})`,t}export{m as getComponentId,c as getProfilerCallback,i as isProfilerEnabled,s as onRenderCallback,a as withProfiler};
