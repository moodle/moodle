/**
 * Shared calendar utilities for the Timeline block.
 *
 * @module     block_timeline/common/utils
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const a=86400;function i(r,t){return{starttime:r+t.daysoffset*86400,endtime:t.dayslimit!==null?r+t.dayslimit*86400:null}}function o(r){const t=new Map;for(const n of r){const e=n.timeusermidnight;t.has(e)||t.set(e,[]),t.get(e).push(n)}return Array.from(t.entries()).sort(([n],[e])=>n-e).map(([n,e])=>({dayTimestamp:n,events:e}))}function u(r,t,n){return r.filter(e=>e.eventtype==="open"||e.eventtype==="opensubmission"?e.timeusermidnight>t:!n||e.overdue)}export{a as SECONDS_IN_DAY,i as computeTimeRange,u as filterEvents,o as groupByDay};
