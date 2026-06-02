import{requireAsync as e}from"@moodle/lms/core/amd";/**
 * ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @module     core/String
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const n=new Map,a=(r,t="core",i)=>{const s=`${t}::${r}::${JSON.stringify(i)}`;return n.has(s)||n.set(s,e("core/str").then(g=>g.get_string(r,t,i))),n.get(s)},c=async r=>{(await e("core/str")).cache_strings(r)};export{c as cacheStrings,a as getString};
