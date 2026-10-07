/**
 * A logging module providing level-filtered console output.
 *
 * Each log method accepts an optional `source` parameter which, when provided,
 * prefixes the message with `"source: message"` for easier filtering.
 *
 * @module     core/log
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const n={TRACE:0,DEBUG:1,INFO:2,WARN:3,ERROR:4,SILENT:5},g={[n.TRACE]:"trace",[n.DEBUG]:"debug",[n.INFO]:"info",[n.WARN]:"warn",[n.ERROR]:"error"};let t=n.WARN,v=n.WARN;function u(e){return typeof e=="string"?n[e.toUpperCase()]??n.WARN:e}function s(e,o){const i=String(e);return o===void 0||o===""?i:`${o}: ${i}`}function l(e,o,i){if(e<t)return;const r=g[e];r!==void 0&&console[r](s(o,i))}function f(e){t=u(e)}function d(){return t}function c(e){v=u(e)}function a(){t=v}function R(){t=n.TRACE}function N(){t=n.SILENT}function A(e){e.level!==void 0&&f(e.level)}function p(e,o){l(n.TRACE,e,o)}function L(e,o){l(n.DEBUG,e,o)}function E(e,o){l(n.INFO,e,o)}function m(e,o){l(n.WARN,e,o)}function w(e,o){l(n.ERROR,e,o)}const k={levels:n,trace:p,debug:L,info:E,warn:m,error:w,log:L,setLevel:f,getLevel:d,setDefaultLevel:c,resetLevel:a,enableAll:R,disableAll:N,setConfig:A};var b=k;export{b as default,n as levels};
