var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Wrap an instance of the browser's local or session storage to handle
 * cache expiry, key namespacing and other helpful things.
 *
 * @module     core/Storage
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import config from "./config";
class Storage {
  static {
    __name(this, "Storage");
  }
  #storage;
  #supported;
  #prefix;
  #jsrevPrefix;
  #loginPrefix;
  /**
   * @param storage The underlying Storage instance (e.g. `window.localStorage`).
   */
  constructor(storage) {
    this.#storage = storage;
    this.#supported = this.#detectSupport();
    const hashSource = `${config.wwwroot}/${config.jsrev}`;
    this.#prefix = `${Storage.hashString(hashSource)}/`;
    this.#jsrevPrefix = `${Storage.hashString(config.wwwroot)}/jsrev`;
    this.#loginPrefix = `${Storage.hashString(config.wwwroot)}/currentlogin`;
    this.#validateCache();
  }
  /**
   * Check if the browser supports the type of storage.
   */
  #detectSupport() {
    if (config.jsrev === -1) {
      return false;
    }
    if (typeof this.#storage === "undefined") {
      return false;
    }
    const testKey = "test";
    try {
      if (this.#storage === null) {
        return false;
      }
      this.#storage.setItem(testKey, "1");
      this.#storage.removeItem(testKey);
      return true;
    } catch {
      return false;
    }
  }
  /**
   * Add a unique prefix to all keys so multiple moodle sites do not share caches.
   */
  #prefixKey(key) {
    return this.#prefix + key;
  }
  /**
   * Check the current jsrev version and user login, clearing the cache if either has changed.
   */
  #validateCache() {
    if (!this.#supported) {
      return;
    }
    const cacheVersion = this.#storage.getItem(this.#jsrevPrefix);
    if (cacheVersion === null) {
      this.#storage.setItem(this.#jsrevPrefix, String(config.jsrev));
    } else if (String(config.jsrev) !== cacheVersion) {
      this.#storage.clear();
      this.#storage.setItem(this.#jsrevPrefix, String(config.jsrev));
    }
    if (config.currentlogin !== null) {
      const storedLogin = this.#storage.getItem(this.#loginPrefix);
      if (storedLogin !== null && storedLogin !== String(config.currentlogin)) {
        this.#storage.clear();
        this.#storage.setItem(this.#jsrevPrefix, String(config.jsrev));
      }
      this.#storage.setItem(this.#loginPrefix, String(config.currentlogin));
    }
  }
  /**
   * Hash a string, used to make shorter key prefixes.
   *
   * @param source The string to hash
   * @returns A 32-bit integer hash
   */
  static hashString(source) {
    let hash = 0;
    for (let i = 0; i < source.length; i++) {
      hash = (hash << 5) - hash + source.charCodeAt(i);
      hash |= 0;
    }
    return hash;
  }
  /**
   * Get a value from storage.
   *
   * @param key The cache key to check.
   * @returns The cached string, or `null` if not found or storage is unsupported.
   */
  get(key) {
    if (!this.#supported) {
      return null;
    }
    return this.#storage.getItem(this.#prefixKey(key));
  }
  /**
   * Set a value in storage.
   *
   * @param key The cache key to set.
   * @param value The value to set.
   * @returns `true` on success, `false` if storage is unsupported or full.
   */
  set(key, value) {
    if (!this.#supported) {
      return false;
    }
    try {
      this.#storage.setItem(this.#prefixKey(key), value);
    } catch {
      return false;
    }
    return true;
  }
  /**
   * Clear all items from the underlying storage.
   */
  clean() {
    this.#storage.clear();
  }
}
const internalLocalStore = new Storage(window.localStorage);
const internalSessionStore = new Storage(window.sessionStorage);
const localStore = {
  get: internalLocalStore.get.bind(internalLocalStore),
  set: internalLocalStore.set.bind(internalLocalStore),
  "default": internalLocalStore
};
const sessionStore = {
  get: internalSessionStore.get.bind(internalSessionStore),
  set: internalSessionStore.set.bind(internalSessionStore),
  "default": internalSessionStore
};
var Storage_default = Storage;
export {
  Storage_default as default,
  localStore,
  sessionStore
};
//# sourceMappingURL=Storage.dev.js.map
