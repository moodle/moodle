import p from"@moodle/lms/core/config";import{getString as f}from"@moodle/lms/core/String";import{requireAsync as c}from"@moodle/lms/core/amd";/**
 * The core/deprecated module allows you to mark things as deprecated and warn appropriately.
 *
 * It emits a console error for non-final deprecations, or throws an Error for final ones.
 * When developer debugging is enabled (or running under Behat), a toast notification is
 * also displayed via core/notification.
 *
 * @module     core/deprecated
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example
 * import emitDeprecation from '@moodle/lms/core/deprecated';
 *
 * emitDeprecation('myFunction', {
 *     replacement: 'myNewFunction',
 *     since: '5.0',
 *     mdl: 'MDL-12345',
 * });
 */const h=(r,t,s,i,o,e)=>{const n=[];return n.push("Deprecation: "),t?n.push(t):n.push(`${r} has been deprecated`),i!==null&&n.push(` since ${i}`),n.push("."),o&&n.push(` ${o}`),s&&n.push(` Please use ${s} instead.`),e&&n.push(` See ${e} for more information.`),n.join("")},d=(r,t,s,i,o,e)=>{const n=[];if(n.push("<h2>Deprecation</h2>"),t?n.push(`<p>${t}`):n.push(`<p><code>${r}</code> is deprecated`),i!==null&&n.push(` since ${i}`),n.push(".</p>"),o&&n.push(`<p>${o}</p>`),s&&n.push(`<p>Please use <code>${s}</code> instead.</p>`),e){const l=`https://moodle.atlassian.net/browse/${e}`;n.push(`<p>See <a href="${l}" target="_blank" rel="noopener noreferrer">${e}</a> for more information.</p>`)}return n.join("")},m=r=>(p.deprecationignorelist||[]).includes(r),$=()=>!!(p.developerdebug||document.querySelector("body.behat-site"));function b(r,{alternativeNotice:t=null,replacement:s=null,since:i=null,reason:o=null,mdl:e=null,final:n=!1,emit:l=!0}={}){if(s===null&&o===null&&e===null)throw new Error("You must provide at least one of replacement, reason or mdl when marking something as deprecated.");const u=h(r,t,s,i,o,e);if((n||$())&&(n||l&&!m(r))){const a=d(r,t,s,i,o,e);c("core/notification").then(g=>g.alert("Deprecation Warning",a,f("ok")))}if(n)throw new Error(u);console.error(u)}export{b as default};
