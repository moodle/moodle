var g=Object.defineProperty;var l=(s,t)=>g(s,"name",{value:t,configurable:!0});import r from"./config";var o=class s{static{l(this,"Storage")}#t;#r;#i;#e;#s;constructor(t){this.#t=t,this.#r=this.#o();let e=`${r.wwwroot}/${r.jsrev}`;this.#i=`${s.hashString(e)}/`,this.#e=`${s.hashString(r.wwwroot)}/jsrev`,this.#s=`${s.hashString(r.wwwroot)}/currentlogin`,this.#h()}#o(){if(r.jsrev===-1||typeof this.#t>"u")return!1;let t="test";try{return this.#t===null?!1:(this.#t.setItem(t,"1"),this.#t.removeItem(t),!0)}catch{return!1}}#n(t){return this.#i+t}#h(){if(!this.#r)return;let t=this.#t.getItem(this.#e);if(t===null?this.#t.setItem(this.#e,String(r.jsrev)):String(r.jsrev)!==t&&(this.#t.clear(),this.#t.setItem(this.#e,String(r.jsrev))),r.currentlogin!==null){let e=this.#t.getItem(this.#s);e!==null&&e!==String(r.currentlogin)&&(this.#t.clear(),this.#t.setItem(this.#e,String(r.jsrev))),this.#t.setItem(this.#s,String(r.currentlogin))}}static hashString(t){let e=0;for(let h=0;h<t.length;h++)e=(e<<5)-e+t.charCodeAt(h),e|=0;return e}get(t){return this.#r?this.#t.getItem(this.#n(t)):null}set(t,e){if(!this.#r)return!1;try{this.#t.setItem(this.#n(t),e)}catch{return!1}return!0}clean(){this.#t.clear()}},i=new o(window.localStorage),n=new o(window.sessionStorage),u={get:i.get.bind(i),set:i.set.bind(i),default:i},f={get:n.get.bind(n),set:n.set.bind(n),default:n},d=o;export{d as default,u as localStore,f as sessionStore};
/**
 * Wrap an instance of the browser's local or session storage to handle
 * cache expiry, key namespacing and other helpful things.
 *
 * @module     core/Storage
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=Storage.js.map
