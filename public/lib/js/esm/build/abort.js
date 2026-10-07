/**
 * Global Abort Controller used in the Fetch API.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const o=()=>globalThis.globalAbortController.signal,r=()=>{globalThis.globalAbortController?.abort()},l=()=>{globalThis.globalAbortController=new AbortController};l();var t=o;export{r as abortGlobalFetches,t as default,o as getGlobalAbortSignal,l as resetGlobalAbortController};
