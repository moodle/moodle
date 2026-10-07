import l from"@moodle/lms/core/config";import{getString as f}from"@moodle/lms/core/stringUtils";import{requireAsync as d}from"@moodle/lms/core/amd";/**
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
 */const t=n=>typeof n=="string"&&n!=="",h=({thing:n,alternativeNotice:o,replacement:i,since:a,reason:s,mdl:r})=>{const e=["Deprecation: "];return t(o)?e.push(o):e.push(`${n} has been deprecated`),t(a)&&e.push(` since ${a}`),e.push("."),t(s)&&e.push(` ${s}`),t(i)&&e.push(` Please use ${i} instead.`),t(r)&&e.push(` See ${r} for more information.`),e.join("")},m=({thing:n,alternativeNotice:o,replacement:i,since:a,reason:s,mdl:r})=>{const e=["<h2>Deprecation</h2>"];if(t(o)?e.push(`<p>${o}`):e.push(`<p><code>${n}</code> is deprecated`),a!==null&&e.push(` since ${a}`),e.push(".</p>"),t(s)&&e.push(`<p>${s}</p>`),t(i)&&e.push(`<p>Please use <code>${i}</code> instead.</p>`),t(r)){const p=`https://moodle.atlassian.net/browse/${r}`;e.push(`<p>See <a href="${p}" target="_blank" rel="noopener noreferrer">${r}</a> for more information.</p>`)}return e.join("")},$=n=>l.deprecationignorelist.includes(n),D=()=>!!(l.developerdebug||document.querySelector("body.behat-site"));function b(n,{alternativeNotice:o,replacement:i,since:a,reason:s,mdl:r,final:e=!1,emit:p=!0}={}){if(!t(i)&&!t(s)&&!t(r))throw new Error("You must provide at least one of replacement, reason or mdl when marking something as deprecated.");const c={thing:n,alternativeNotice:o,replacement:i,since:a,reason:s,mdl:r},u=h(c);if((e||D())&&(e||p&&!$(n))&&d("core/notification").then(g=>{g.alert("Deprecation Warning",m(c),f("ok"))}),e)throw new Error(u);console.error(u)}export{b as default};
