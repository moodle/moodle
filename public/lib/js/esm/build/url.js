import e from"./config";/**
 * URL utility functions.
 *
 * @module     core/url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */const u=(n,r)=>{let t=e.wwwroot+n;return r.charAt(0)!=="/"&&(r=`/${r}`),e.slasharguments?t+=r:t+=`?file=${encodeURIComponent(r)}`,t},c=(n,r={},t=!1)=>{if(n.indexOf("http:")===0||n.indexOf("https:")===0||n.indexOf("://")>=0)throw new Error("relativeUrl function does not accept absolute urls");n.charAt(0)!=="/"&&(n=`/${n}`),e.admin!=="admin"&&(n=n.replace(/^\/admin\//,`/${e.admin}/`));const i={...r};t&&(i.sesskey=e.sesskey);const o=new URLSearchParams(Object.entries(i).map(([s,m])=>[s,String(m)])).toString();return o!==""?`${e.wwwroot}${n}?${o}`:e.wwwroot+n},f=(n,r)=>M.util.image_url(n,r);var d={fileUrl:u,relativeUrl:c,imageUrl:f};export{d as default,u as fileUrl,f as imageUrl,c as relativeUrl};
