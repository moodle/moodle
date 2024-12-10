import{getGlobalAbortSignal as R}from"./abort";import g from"@moodle/lms/core/config";import m from"@moodle/lms/core/pending";/**
 * The core/fetch module allows you to make web service requests to the Moodle REST API.
 *
 * Most methods are available both statically and via an instance method, for example
 * `Fetch.performGet()` and `(new Fetch()).performGet()`.
 *
 * The static perform methods perform immediate individual requests, whilst instance
 * methods are useful for batching requests.
 *
 * By default a Fetch instance will not automatically execute the batch, but it can be configured to do so
 * by passing a value for the `autoBatchTimeout` parameter.
 *
 * The batcher can be executed manually by calling the {@link Fetch#execute} method.
 *
 * Note: In cases where the batcher is executed with a single request the batch endpoint is _not_ used.
 *
 * A helper method, {@link Fetch.getBatcher}, exists to fetch a singleton instance of the class.
 * This singleton is configured to automatically execute batch requests.
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
 *
 * @example <caption>Perform a series of requests, automatically batching them</caption>
 * import Fetch from 'core/fetch';
 *
 * // Execute a single request containing three sub requests.
 * const batcher = Fetch.getBatcher();
 * const actions = await Promise.all([
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'mammal' } }),
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'reptile' } }),
 *     batcher.performDelete('mod_example', `animals/${pig.id}`),
 * ]);
 *
 * @example <caption>Perform a series of GET requests, manually batching them</caption>
 * import Fetch from 'core/fetch';
 *
 * const batcher = new Fetch();
 * const actions = Promise.all([
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'mammal' } }),
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'reptile' } }),
 *     batcher.performDelete('mod_example', `animals/${pig.id}`),
 * ]);
 *
 * batcher.execute();
 *
 * await actions;
 */class y{#t;#e;#s;#r;constructor(e){this.#t=e,this.#e=new Promise((t,s)=>{this.#s=t,this.#r=s})}get request(){return this.#t}get promise(){return this.#e}get reject(){return this.#r}handleResponse(e){e.ok?this.#s(e):this.#r(e.statusText)}}class h{static#t=null;static getBatcher(){return this.#t||(this.#t=new this(50)),this.#t}#e=new Map;#s=null;#r;#o;static async request(e,t,{cachekey:s=null,headers:r={},params:n={},body:o=null,method:l="GET"}={}){const i=new m(`Requesting ${e}/${t} with ${l}`),u=h.#a(h.#u(e),t,{headers:r,params:n,method:l,body:o,cachekey:s}),a=await fetch(u.request);return i.resolve(),u.handleResponse(a),u.promise}static async performGet(e,t,{cachekey:s=null,headers:r={},params:n={}}={}){return this.request(e,t,{cachekey:s,headers:r,params:n,method:"GET"})}static async performHead(e,t,{headers:s={},params:r={}}={}){return this.request(e,t,{headers:s,params:r,method:"HEAD"})}static async performPost(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"POST"})}static async performPut(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"PUT"})}static async performPatch(e,t,{headers:s={},body:r}){return this.request(e,t,{headers:s,body:r,method:"PATCH"})}static async performDelete(e,t,{headers:s={},params:r={},body:n=null}={}){return this.request(e,t,{headers:s,body:n,params:r,method:"DELETE"})}constructor(e=null){this.#o=e,this.#r=g.batchFetchRequests}async performGet(e,t,{cachekey:s=null,headers:r={},params:n={}}={}){return this.#n(e,t,{cachekey:s,headers:r,params:n,method:"GET"})}async performHead(e,t,{headers:s={},params:r={}}={}){return this.#n(e,t,{headers:s,params:r,method:"HEAD"})}async performPost(e,t,{headers:s={},body:r}){return this.#n(e,t,{headers:s,body:r,method:"POST"})}async performPut(e,t,{headers:s={},body:r}){return this.#n(e,t,{headers:s,body:r,method:"PUT"})}async performPatch(e,t,{headers:s={},body:r}){return this.#n(e,t,{headers:s,body:r,method:"PATCH"})}async performDelete(e,t,{headers:s={},params:r={},body:n=null}={}){return this.#n(e,t,{headers:s,body:n,params:r,method:"DELETE"})}async execute(){const e=this.#e;if(this.#e=new Map,e.size===0)return;let t=0;for(const r of e.values())t+=r.size;if(t===1){const r=e.values().next().value;await this.#i(r);return}const s=new m(`core/fetch:execute:${t}`);for(const r of e.values())await this.#i(r);s.resolve()}async#i(e){if(e.size===1){const n=e.values().next().value,o=await fetch(n.request);n.handleResponse(o);return}const t=await fetch(await this.#h(e));if(!t.ok){for(const n of e.values())n.reject(t.statusText);return}const s=await this.#g(t),r=new Map;for(const n of s){const o=this.#d(n),l=o.headers.get("Content-ID")??"";r.set(l,o)}for(const[n,o]of e)r.has(n)?o.handleResponse(r.get(n)):o.reject(`Request failed. No response provided for request ${n} by provider`)}static#u(e){return e.replace(/^core_/u,"")}#l(e,t){if(t===null){let s;do s=this.#p();while(this.#e.get(e)?.has(s));return s}if(this.#e.get(e)?.has(t))throw new Error(`Request with ID ${t} already exists.`);return t}async#n(e,t,{cachekey:s=null,headers:r={},params:n={},body:o=null,method:l="GET",id:i=null}){if(s!==null&&s>1){if(i!==null)throw new Error("A request ID cannot be specified for a request which bypasses the batcher.");return h.request(e,t,{cachekey:s,headers:r,params:n,body:o,method:l})}let u=0;for(const f of this.#e.values())u+=f.size;u>=20&&this.execute();const a=new Headers(r),c=a.has("Authorization")?`authorization:${a.get("Authorization")}`:"cookie",p=this.#l(c,i),d=h.#a(h.#u(e),t,{cachekey:s,headers:r,params:n,method:l,body:o});return this.#e.has(c)||this.#e.set(c,new Map),this.#e.get(c)?.set(p,d),this.#r?this.#o!==null&&this.#c():this.execute(),d.promise}#c(){const e=this.#o;e!==null&&(this.#s&&clearTimeout(this.#s),this.#s=setTimeout(()=>{this.execute()},e))}static#a(e,t,{cachekey:s=null,headers:r={},params:n={},body:o=null,method:l="GET"}){const i=["rest","v2"];s!==null&&s>1&&i.push(`cachekey:${s}`),i.push(e,t);const u=new URL(`${g.apibase}/${i.join("/").replaceAll("//","/")}`),a={method:l,headers:{...r,Accept:"application/json","Content-Type":"application/json",pageparent:g.traceId??""},signal:R()};for(const[c,p]of Object.entries(n))u.searchParams.append(c,p);return o!==null&&(o instanceof FormData?a.body=o:typeof o=="object"?a.body=JSON.stringify(o):a.body=o),new y(new Request(u,a))}async#h(e){const t=this.#p(),s=(i=!1)=>`--${t}${i?"--":""}\r
`,r=()=>[s(),`Content-Type: application/http\r
\r
`],n=await Promise.all([...e].map(async([i,{request:u}])=>{const a=[...r(),`${u.method} ${u.url}\r
`,`Content-Type: ${u.headers.get("Content-Type")}\r
`,`Content-ID: ${i}\r
`];return u.headers.forEach((c,p)=>{p.toLowerCase()!=="content-type"&&p.toLowerCase()!=="content-id"&&a.push(`${p}: ${c}\r
`)}),u.body!==null?[...a,`\r
`,await u.text(),`\r
`]:[...a,`\r
`]})),o=[];for(const{request:i}of e.values())o.push(`# Requesting ${i.method} ${i.url}\r
`);const l=[...o,...n.map(i=>i.join("")),s(!0)];return new Request(`${g.apibase}/$batch`,{body:l.join(""),method:"POST",headers:{"Content-Type":`multipart/mixed;boundary=${t}`},signal:R()})}#d(e){const[,t,...s]=e.split(`\r
\r
`).map(p=>p.trim()),r=s.length>0?s.join(`\r
\r
`).trim():null,n=t.split(`\r
`),o=n.shift()??"",i=/HTTP\/(?<protocol>[^ ]*) (?<status>\d{3}) (?<statusText>.*)$/u.exec(o),{status:u,statusText:a}=i?.groups??{status:void 0,statusText:void 0},c=n.filter(p=>p.includes(":")).map(p=>{const d=p.indexOf(":");return[p.slice(0,d).trim(),p.slice(d+1).trim()]});return new Response(r,{status:Number(u),statusText:a,headers:new Headers(c)})}async#g(e){const t=e.headers.get("Content-Type")??"";if(t.startsWith("multipart/mixed")){const[,s]=t.split(";").map(o=>o.trim()),r=s.replace("boundary=","").trim();return(await e.text()).split(new RegExp(`(?:--${r}(?:--)?(?:\r
)?)`,"gmu")).filter(o=>o.length>0)}throw new Error(`Unknown response type '${t}'`)}#p(){if(typeof crypto<"u"&&typeof crypto.randomUUID=="function")return crypto.randomUUID();let e=Date.now();return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replaceAll(/[xy]/gu,s=>{const r=Math.trunc((e+Math.random()*16)%16);return e=Math.floor(e/16),(s==="x"?r:r%4+8).toString(16)})}}export{h as default};
