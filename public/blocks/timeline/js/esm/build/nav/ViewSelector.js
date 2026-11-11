import{useAriaLabels as b}from"../common/useAriaLabels";import{useComposedLabel as p}from"../common/useComposedLabel";import l from"@moodle/lms/core/String";import{jsx as a,jsxs as w}from"react/jsx-runtime";/**
 * Sort-order (dates / courses) selector for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-view-selector.mustache template, except for the
 * ARIA roles: this is a dropdown of two sort options, so it uses the menu pattern that DayFilter
 * and Bootstrap's own dropdown JS already implement, rather than the tablist the legacy template
 * declared but never wired up.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const t=[{name:"sortbydates",labelKey:"sortbydates"},{name:"sortbycourses",labelKey:"sortbycourses"}];function u({activeOrder:o,onChange:s}){const n="menusortby",{buttonLabel:r,itemLabels:m}=b("ariaviewselector","ariaviewselectoroption",t),i=t.find(e=>e.name===o)??t[0],d=p("ariaviewselectorbutton",i.labelKey);return w("div",{"data-region":"view-selector",className:"dropdown mb-1",children:[a("button",{type:"button",className:"btn btn-outline-secondary dropdown-toggle icon-no-margin","data-bs-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"false","aria-label":d,"aria-controls":n,title:r,children:a("span",{"data-active-item-text":"",children:a(l,{identifier:i.labelKey,component:"block_timeline"})})}),a("div",{id:n,role:"menu","aria-label":r,className:"dropdown-menu dropdown-menu-end","data-show-active-item":"",children:t.map(e=>a("a",{className:`dropdown-item${o===e.name?" active dropdown-item-active":""}`,href:"#","data-filtername":e.name,"aria-current":o===e.name?"true":void 0,"aria-label":m[e.name],role:"menuitem",onClick:c=>{c.preventDefault(),s(e.name)},children:a(l,{identifier:e.labelKey,component:"block_timeline"})},e.name))})]})}export{u as default};
