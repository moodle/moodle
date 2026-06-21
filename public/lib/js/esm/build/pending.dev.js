var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
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
 */
class Pending {
  static {
    __name(this, "Pending");
  }
  #internalPromise;
  /**
   * Register a pending operation with Moodle's Behat integration.
   *
   * @param key A descriptive identifier for debugging.
   */
  static pending(key) {
    M.util.js_pending(key);
  }
  /**
   * Mark a pending operation as complete.
   *
   * @param key The same identifier that was passed to {@link Pending.pending}.
   */
  static complete(key) {
    M.util.js_complete(key);
  }
  /**
   * Request a new pendingPromise for later resolution.
   *
   * When the action you are performing is complete, simply call `resolve` on the returned Promise.
   *
   * @param pendingKey An identifier to help in debugging.
   * @returns A Promise with `resolve` and `reject` methods attached.
   */
  constructor(pendingKey = "pendingPromise") {
    let resolver;
    let rejector;
    this.#internalPromise = Pending.Promise((resolve, reject) => {
      resolver = resolve;
      rejector = reject;
    }, pendingKey);
    this.resolve = resolver;
    this.reject = rejector;
  }
  then(onfulfilled, onrejected) {
    return this.#internalPromise.then(onfulfilled, onrejected);
  }
  /**
   * Attaches a callback for only the rejection of the Promise.
   * @param onrejected The callback to execute when the Promise is rejected.
   * @returns A Promise for the completion of the callback.
   */
  catch(onrejected) {
    return this.#internalPromise.catch(onrejected);
  }
  /**
   * Create a new Pending Promise with the same interface as a native Promise.
   *
   * @param fn A callable which takes the resolve and reject arguments as in a native Promise constructor.
   * @param pendingKey An identifier to help in debugging.
   * @returns A Promise that marks the pending operation as complete when resolved.
   * @since Moodle 4.2
   *
   * @example
   * import Pending from 'core/pending';
   * import {getString} from 'core/str';
   *
   * export const init = () => {
   *     Pending.Promise((resolve, reject) => {
   *         getString('ok')
   *             .then(okay => {
   *                 window.console.log(okay);
   *                 return okay;
   *             })
   *             .then(resolve)
   *             .catch(reject);
   *     }, 'mod_myexample/setup:init');
   * };
   */
  static Promise(fn, pendingKey = "pendingPromise") {
    const resolver = new Promise((resolve, reject) => {
      Pending.pending(pendingKey);
      fn(resolve, reject);
    });
    resolver.then(() => {
      Pending.complete(pendingKey);
      return;
    }).catch(() => {
    });
    return resolver;
  }
}
export {
  Pending as default
};
//# sourceMappingURL=pending.dev.js.map
