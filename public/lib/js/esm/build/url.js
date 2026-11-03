import n from"./config";/**
 * URL utility functions.
 *
 * @module     core/url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */const g=(r,t)=>{let e=n.wwwroot+r;return t.startsWith("/")||(t=`/${t}`),e+=n.slasharguments===0?`?file=${encodeURIComponent(t)}`:t,e},m=(r,t={},e=!1)=>{if(r.startsWith("http:")||r.startsWith("https:")||r.includes("://"))throw new Error("relativeUrl function does not accept absolute urls");r.startsWith("/")||(r=`/${r}`),n.admin!=="admin"&&(r=r.replace(/^\/admin\//u,()=>`/${n.admin}/`));const s={...t};e&&(s.sesskey=n.sesskey);const o=Object.entries(s).map(([c,u])=>[c,String(u)]),i=new URLSearchParams(o).toString();return i!==""?`${n.wwwroot}${r}?${i}`:n.wwwroot+r},a=(r,t)=>M.util.image_url(r,t),f={fileUrl:g,relativeUrl:m,imageUrl:a};var d=f;export{d as default,g as fileUrl,a as imageUrl,m as relativeUrl};
