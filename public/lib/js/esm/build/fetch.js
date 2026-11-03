import{getGlobalAbortSignal as d}from"./abort";import R from"@moodle/lms/core/config";import m from"@moodle/lms/core/pending";/**
 * The core/fetch module allows you to make web service requests to the Moodle REST API.
 *
 * @module     core/fetch
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Perform a single GET request</caption>
 * import Fetch from 'core/fetch';
 *
 * const result = Fetch.performGet('mod_example', 'animals', { params: { type: 'mammal' } });
 *
 * result.then((response) => {
 *    // Do something with the Response object.
 * })
 * .catch((error) => {
 *     // Handle the error
 * });
 */class q{#e;#s;#t;#r;constructor(e){this.#e=e,this.#s=new Promise((t,s)=>{this.#t=t,this.#r=s})}get request(){return this.#e}get promise(){return this.#s}handleResponse(e){e.ok?this.#t(e):this.#r(e.statusText)}}class l{static async request(e,t,{cachekey:s=null,headers:r={},params:i={},body:n=null,method:p="GET"}={}){const u=new m(`Requesting ${e}/${t} with ${p}`),o=l.#s(l.#e(e),t,{headers:r,params:i,method:p,body:n,cachekey:s}),a=await fetch(o.request);return u.resolve(),o.handleResponse(a),o.promise}static async performGet(e,t,{cachekey:s=null,headers:r={},params:i={}}={}){return this.request(e,t,{cachekey:s,headers:r,params:i,method:"GET"})}static async performHead(e,t,{headers:s={},params:r={}}={}){return this.request(e,t,{headers:s,params:r,method:"HEAD"})}static async performPost(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"POST"})}static async performPut(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"PUT"})}static async performPatch(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"PATCH"})}static async performDelete(e,t,{headers:s={},params:r={},body:i=null}={}){return this.request(e,t,{headers:s,body:i,params:r,method:"DELETE"})}static#e(e){return e.replace(/^core_/u,"")}static#s(e,t,{cachekey:s=null,headers:r={},params:i={},body:n=null,method:p="GET"}){const u=["rest","v2"];s!==null&&s>1&&u.push(`cachekey:${s}`),u.push(e,t);const o=new URL(`${R.apibase}/${u.join("/").replaceAll("//","/")}`),a={method:p,headers:{...r,Accept:"application/json","Content-Type":"application/json",pageparent:R.traceId??""},signal:d()};for(const[c,g]of Object.entries(i))o.searchParams.append(c,g);return n!==null&&(n instanceof FormData?a.body=n:typeof n=="object"?a.body=JSON.stringify(n):a.body=n),new q(new Request(o,a))}}export{l as default};
