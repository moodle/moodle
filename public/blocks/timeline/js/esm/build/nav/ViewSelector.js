import{useAriaLabels as b}from"../common/useAriaLabels";import{useComposedLabel as p}from"../common/useComposedLabel";import l from"@moodle/lms/core/String";import{jsx as t,jsxs as w}from"react/jsx-runtime";/**
 * Sort-order (dates / courses) selector for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-view-selector.mustache template, except for the
 * ARIA roles: this is a dropdown of two sort options, so it uses the menu pattern that DayFilter
 * and Bootstrap's own dropdown JS already implement, rather than the tablist the legacy template
 * declared but never wired up.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const a=[{name:"sortbydates",labelKey:"sortbydates"},{name:"sortbycourses",labelKey:"sortbycourses"}];function u({activeOrder:n,onChange:s}){const o="menusortby",{buttonLabel:r,itemLabels:m}=b("ariaviewselector","ariaviewselectoroption",a),i=a.find(e=>e.name===n)??a[0],d=p("ariaviewselectorbutton",i.labelKey);return w("div",{"data-region":"view-selector",className:"dropdown mb-1",children:[t("button",{type:"button",className:"btn btn-outline-secondary dropdown-toggle icon-no-margin","data-bs-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"false","aria-label":d,"aria-controls":o,title:r,children:t("span",{"data-active-item-text":"",children:t(l,{identifier:i.labelKey,component:"block_timeline",children:""})})}),t("div",{id:o,role:"menu","aria-label":r,className:"dropdown-menu dropdown-menu-end","data-show-active-item":"",children:a.map(e=>t("a",{className:`dropdown-item${n===e.name?" active dropdown-item-active":""}`,href:"#","data-filtername":e.name,"aria-current":n===e.name?"true":void 0,"aria-label":m[e.name],role:"menuitem",onClick:c=>{c.preventDefault(),s(e.name)},children:t(l,{identifier:e.labelKey,component:"block_timeline",children:""})},e.name))})]})}export{u as default};
