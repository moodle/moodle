import{useEffect as l}from"react";import{Switch as a}from"@moodlehq/design-system";import{requireAsync as f}from"@moodle/lms/core/amd";import u from"@moodle/lms/core/pending";import{jsx as h}from"react/jsx-runtime";/**
 * The site-wide Edit mode switch, rendered with the design system Switch component.
 *
 * @module     core/EditModeSwitch
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function s({id:e,context:i,pageurl:n,checked:r,label:d}){return l(()=>{let t=!1;const o=new u("core/EditModeSwitch:init");return f("core/edit_switch").then(c=>{t||c.init(e)}).finally(()=>{o.resolve()}),()=>{t=!0}},[e]),h(a,{id:e,name:"setmode",variant:"enable",labelSide:"start",label:d,defaultChecked:r,"data-context":i,"data-pageurl":n})}export{s as default};
