var c=Object.defineProperty;var i=(n,r)=>c(n,"name",{value:r,configurable:!0});import e from"./config";var f=i((n,r)=>{let t=e.wwwroot+n;return r.charAt(0)!=="/"&&(r=`/${r}`),e.slasharguments?t+=r:t+=`?file=${encodeURIComponent(r)}`,t},"fileUrl"),g=i((n,r={},t=!1)=>{if(n.indexOf("http:")===0||n.indexOf("https:")===0||n.indexOf("://")>=0)throw new Error("relativeUrl function does not accept absolute urls");n.charAt(0)!=="/"&&(n=`/${n}`),e.admin!=="admin"&&(n=n.replace(/^\/admin\//,`/${e.admin}/`));let o={...r};t&&(o.sesskey=e.sesskey);let s=new URLSearchParams(Object.entries(o).map(([m,u])=>[m,String(u)])).toString();return s!==""?`${e.wwwroot}${n}?${s}`:e.wwwroot+n},"relativeUrl"),d=i((n,r)=>M.util.image_url(n,r),"imageUrl"),l={fileUrl:f,relativeUrl:g,imageUrl:d};export{l as default,f as fileUrl,d as imageUrl,g as relativeUrl};
/**
 * URL utility functions.
 *
 * @module     core/url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
//# sourceMappingURL=url.js.map
