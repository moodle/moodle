import{requireAsync as i}from"@moodle/lms/core/amd";/**
 * ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @module     core/String
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const t=new Map,a=(r,s="core",e)=>{const n=`${s}::${r}::${JSON.stringify(e)}`;return t.has(n)||t.set(n,i("core/str").then(g=>g.get_string(r,s,e))),t.get(n)},c=()=>t.clear(),m=async r=>{(await i("core/str")).cache_strings(r)};export{m as cacheStrings,a as getString,c as resetStringCache};
