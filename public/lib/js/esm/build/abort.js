/**
 * Global Abort Controller used in the Fetch API.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const o=()=>window.globalAbortController.signal,r=()=>{window.globalAbortController?.abort()},l=()=>{window.globalAbortController=new AbortController};l();var t=o;export{r as abortGlobalFetches,t as default,o as getGlobalAbortSignal,l as resetGlobalAbortController};
