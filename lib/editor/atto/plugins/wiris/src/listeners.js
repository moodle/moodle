/**
 * This object represents a custom listener.
 * @typedef {Object} Listener
 * @property {String} Listener.eventName - The listener name.
 * @property {Function} Listener.callback - The listener callback function.
 */

export default class Listeners {
  /**
   * @classdesc
   * This class represents a custom listeners manager.
   * @constructs
   */
  constructor() {
    /**
     * Array containing all custom listeners.
     * @type {Object[]}
     */
    this.listeners = [];
  }


  /**
   * Add a listener to Listener class.
   * @param {Object} listener - A listener object.
   */
  add(listener) {
    this.listeners.push(listener);
  }

  /**
   * Fires MathType event listeners
   * @param {String} eventName - event name
   * @param {Event} event - event object.
   * @return {boolean} false if event has been prevented. true otherwise.
   */
  fire(eventName, event) {
    for (let i = 0; i < this.listeners.length && !event.cancelled; i += 1) {
      if (this.listeners[i].eventName === eventName) {
        // Calling listener.
        this.listeners[i].callback(event);
      }
    }
    return event.defaultPrevented;
  }

  /**
   * Creates a new listener object.
   * @param {string} eventName - Event name.
   * @param {Object} callback - Callback function.
   * @returns {object} the listener object.
   */
  static newListener(eventName, callback) {
    const listener = {};
    listener.eventName = eventName;
    listener.callback = callback;
    return listener;
  }
}
