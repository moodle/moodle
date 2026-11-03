import{useEffect as s,useState as l}from"react";import{ActivityIcon as r}from"@moodlehq/design-system";import{jsx as u}from"react/jsx-runtime";/**
 * The icon of a course activity card, rendered with the design system ActivityIcon.
 *
 * Mounted from core_courseformat/local/content/cm/cmicon, which keeps a server-rendered
 * fallback inside the mount point until this component takes over. Keep the size of that
 * fallback, including its size on small screens, in sync with this component.
 *
 * @module     core_course/ActivityIcon
 * @copyright  2026 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const o="(max-width: 575.98px)",m=()=>{const[i,n]=l(()=>globalThis.matchMedia(o).matches);return s(()=>{const t=globalThis.matchMedia(o),e=c=>{n(c.matches)};return n(t.matches),t.addEventListener("change",e),()=>{t.removeEventListener("change",e)}},[]),i};function d({icon:i,alt:n="",container:t="default",size:e="md",mobileSize:c=e}){const a=m();return u(r,{icon:i,alt:n,container:t,size:a?c:e})}export{d as default};
