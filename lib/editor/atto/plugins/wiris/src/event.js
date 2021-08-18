export default class Event {
  /**
   * @classdesc
   * This class represents a custom event. Events should be fired by the {@link Listener} class.
   *
   * ```js
   *  let customEvent = new Event();
   *  customEvent.properties = {};
   *
   *  let listeners = new Listeners();
   *  listeners.newListener(eventName, callback);
   *
   *  listeners.fire(eventName, customEvent) *
   * ```
   * @constructs
   */
  constructor() {
    /**
     * Indicates if the event should be cancelled.
     * @type {Boolean}
     */

    this.cancelled = false;
    /**
     * Indicates if the event should be prevented.
     * @type {Boolean}
     */
    this.defaultPrevented = false;
  }

  /**
   * Cancels the event.
   */
  cancel() {
    this.cancelled = true;
  }

  /**
   * Prevents the default action.
   */
  preventDefault() {
    this.defaultPrevented = true;
  }
}
