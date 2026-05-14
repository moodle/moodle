var s=Object.defineProperty;var t=(e,n)=>s(e,"name",{value:n,configurable:!0});var o={TRACE:0,DEBUG:1,INFO:2,WARN:3,ERROR:4,SILENT:5},c={[o.TRACE]:"trace",[o.DEBUG]:"debug",[o.INFO]:"info",[o.WARN]:"warn",[o.ERROR]:"error"},r=o.WARN,u=o.WARN;function g(e){if(typeof e=="string"){let n=e.toUpperCase();return n in o?o[n]:o.WARN}return e}t(g,"resolveLevel");function a(e,n){let l=String(e);return n?`${n}: ${l}`:l}t(a,"formatMessage");function i(e,n,l){if(e<r)return;let L=c[e];L&&console[L](a(n,l))}t(i,"logAtLevel");function f(e){r=g(e)}t(f,"setLevel");function d(){return r}t(d,"getLevel");function R(e){u=g(e)}t(R,"setDefaultLevel");function N(){r=u}t(N,"resetLevel");function p(){r=o.TRACE}t(p,"enableAll");function A(){r=o.SILENT}t(A,"disableAll");function E(e){typeof e.level<"u"&&f(e.level)}t(E,"setConfig");function m(e,n){i(o.TRACE,e,n)}t(m,"trace");function v(e,n){i(o.DEBUG,e,n)}t(v,"debug");function w(e,n){i(o.INFO,e,n)}t(w,"info");function k(e,n){i(o.WARN,e,n)}t(k,"warn");function y(e,n){i(o.ERROR,e,n)}t(y,"error");var b={levels:o,trace:m,debug:v,info:w,warn:k,error:y,log:v,setLevel:f,getLevel:d,setDefaultLevel:R,resetLevel:N,enableAll:p,disableAll:A,setConfig:E},O=b;export{O as default,o as levels};
/**
 * A logging module providing level-filtered console output.
 *
 * Each log method accepts an optional `source` parameter which, when provided,
 * prefixes the message with `"source: message"` for easier filtering.
 *
 * @module     core/log
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//# sourceMappingURL=log.js.map
