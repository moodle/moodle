/**
 * A logging module providing level-filtered console output.
 *
 * Each log method accepts an optional `source` parameter which, when provided,
 * prefixes the message with `"source: message"` for easier filtering.
 *
 * @module     core/log
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const n={TRACE:0,DEBUG:1,INFO:2,WARN:3,ERROR:4,SILENT:5},f={[n.TRACE]:"trace",[n.DEBUG]:"debug",[n.INFO]:"info",[n.WARN]:"warn",[n.ERROR]:"error"};let t=n.WARN,v=n.WARN;function u(e){if(typeof e=="string"){const o=e.toUpperCase();return o in n?n[o]:n.WARN}return e}function s(e,o){const i=String(e);return o?`${o}: ${i}`:i}function r(e,o,i){if(e<t)return;const l=f[e];l&&console[l](s(o,i))}function g(e){t=u(e)}function c(){return t}function a(e){v=u(e)}function d(){t=v}function R(){t=n.TRACE}function N(){t=n.SILENT}function p(e){typeof e.level<"u"&&g(e.level)}function A(e,o){r(n.TRACE,e,o)}function L(e,o){r(n.DEBUG,e,o)}function E(e,o){r(n.INFO,e,o)}function m(e,o){r(n.WARN,e,o)}function w(e,o){r(n.ERROR,e,o)}const k={levels:n,trace:A,debug:L,info:E,warn:m,error:w,log:L,setLevel:g,getLevel:c,setDefaultLevel:a,resetLevel:d,enableAll:R,disableAll:N,setConfig:p};var y=k;export{y as default,n as levels};
