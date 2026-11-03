import{useState as i,useEffect as g}from"react";import{getString as n}from"@moodle/lms/core/stringUtils";/**
 * Shared aria-label fetching for the day-filter and view-selector dropdowns:
 * a button label plus a per-option label, each composed from two language strings.
 *
 * @module     block_timeline/common/useAriaLabels
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function u(o,r,l){const[a,s]=i(""),[b,m]=i({});return g(()=>{n(o,"block_timeline").then(s);for(const e of l)n(e.labelKey,e.labelComponent??"block_timeline").then(async t=>n(r,"block_timeline",t)).then(t=>{m(c=>({...c,[e.name]:t}))})},[]),{buttonLabel:a,itemLabels:b}}export{u as useAriaLabels};
