/**
 * A helper used to inform Behat that an operation is in progress and that Behat must wait for it to complete.
 *
 * This is useful in cases where the user interface may be updated and take some time to change — for example
 * where applying a transition.
 *
 * This data is used by Behat, but may also be consumed by other locations too.
 *
 * By informing Behat that an action is about to happen, and then that it is complete, allows
 * Behat to wait for that completion and avoid random failures in automated testing.
 *
 * Note: It is recommended that a descriptive key be used to aid in debugging where possible, but this is optional.
 *
 * @module     core/pending
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */class r{static pending(e){M.util.js_pending(e)}static complete(e){M.util.js_complete(e)}static Promise(e,s="pendingPromise"){const i=new Promise((t,o)=>{this.pending(s),e(t,o)});return i.then(()=>{this.complete(s)}).catch(()=>{}),i}#e;constructor(e="pendingPromise"){let s,i;this.#e=r.Promise((t,o)=>{s=t,i=o},e),this.resolve=s,this.reject=i}async then(e,s){return this.#e.then(e,s)}async catch(e){return this.#e.catch(e)}}export{r as default};
