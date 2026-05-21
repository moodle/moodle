var a=Object.defineProperty;var n=(r,t)=>a(r,"name",{value:t,configurable:!0});import{requireAsync as g}from"@moodle/lms/core/amd";var i=new Map,u=n((r,t="core",e)=>{let s=`${t}::${r}::${JSON.stringify(e)}`;return i.has(s)||i.set(s,g("core/str").then(o=>o.get_string(r,t,e))),i.get(s)},"getString"),p=n(async r=>{(await g("core/str")).cache_strings(r)},"cacheStrings");export{p as cacheStrings,u as getString};
/**
 * ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @module     core/String
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=String.js.map
