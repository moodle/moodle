import g from"@moodle/lms/core/config";import m from"@moodle/lms/core/pending";import{getGlobalAbortSignal as d}from"./abort";/**
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
 */class q{#e;#t;#s;#r;constructor(e){this.#e=e,this.#t=new Promise((s,t)=>{this.#s=s,this.#r=t})}get request(){return this.#e}get promise(){return this.#t}handleResponse(e){e.ok?this.#s(e):this.#r(e.statusText)}}class R{static async request(e,s,{cachekey:t=null,headers:r={},params:i={},body:n=null,method:p="GET"}={}){const u=new m(`Requesting ${e}/${s} with ${p}`),o=R.#t(R.#e(e),s,{headers:r,params:i,method:p,body:n,cachekey:t}),a=await fetch(o.request);return u.resolve(),o.handleResponse(a),o.promise}static performGet(e,s,{cachekey:t=null,headers:r={},params:i={}}={}){return this.request(e,s,{cachekey:t,headers:r,params:i,method:"GET"})}static performHead(e,s,{headers:t={},params:r={}}={}){return this.request(e,s,{headers:t,params:r,method:"HEAD"})}static performPost(e,s,{headers:t={},body:r}){return this.request(e,s,{headers:t,body:r,method:"POST"})}static performPut(e,s,{headers:t={},body:r}){return this.request(e,s,{headers:t,body:r,method:"PUT"})}static performPatch(e,s,{headers:t={},body:r}){return this.request(e,s,{headers:t,body:r,method:"PATCH"})}static performDelete(e,s,{headers:t={},params:r={},body:i=null}={}){return this.request(e,s,{headers:t,body:i,params:r,method:"DELETE"})}static#e(e){return e.replace(/^core_/,"")}static#t(e,s,{cachekey:t=null,headers:r={},params:i={},body:n=null,method:p="GET"}){const u=["rest","v2"];t&&t>1&&u.push(`cachekey:${t}`),u.push(e,s);const o=new URL(`${g.apibase}/${u.join("/").replaceAll("//","/")}`),a={method:p,headers:{...r,Accept:"application/json","Content-Type":"application/json",pageparent:g.traceId||""},signal:d()};return Object.entries(i).forEach(([c,l])=>{o.searchParams.append(c,l)}),n&&(n instanceof FormData?a.body=n:typeof n=="object"?a.body=JSON.stringify(n):a.body=n),new q(new Request(o,a))}}export{R as default};
