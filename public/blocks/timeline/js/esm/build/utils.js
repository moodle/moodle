import{fetchMany as m}from"@moodle/lms/core/ajax";import{getString as s}from"@moodle/lms/core/stringUtils";import u from"@moodle/lms/core/config";/**
 * Shared calendar utilities for the Timeline block.
 *
 * @module     block_timeline/utils
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const i=86400;function l(r,e){return{starttime:r+e.daysoffset*i,endtime:e.dayslimit!==null?r+e.dayslimit*i:null}}function c(r){const e=new Map;for(const n of r){const t=n.timeusermidnight;e.has(t)||e.set(t,[]),e.get(t).push(n)}return Array.from(e.entries()).sort(([n],[t])=>n-t).map(([n,t])=>({dayTimestamp:n,events:t}))}function y(r,e,n){return r.filter(t=>t.eventtype==="open"||t.eventtype==="opensubmission"?t.timeusermidnight>e:!n||t.overdue)}async function g(r){const e=[...new Set(r)];if(e.length===0)return new Map;const n=await s("strftimedaydate","langconfig"),[t]=await m([{methodname:"core_get_user_dates",args:{contextid:u.contextid??1,timestamps:e.map(a=>({timestamp:a,format:n}))}}]);return new Map(e.map((a,o)=>[a,t.dates[o]]))}export{i as SECONDS_IN_DAY,l as computeTimeRange,y as filterEvents,g as getFormattedDays,c as groupByDay};
