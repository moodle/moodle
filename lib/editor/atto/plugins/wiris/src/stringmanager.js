import translations from '../lang/strings.json';
/**
 * This class represents a string manager. It's used to load localized strings.
 */
export default class StringManager {
  constructor() {
    throw new Error('Static class StringManager can not be instantiated.');
  }

  /**
   * Returns the associated value of certain string key. If the associated value
   * doesn't exits returns the original key.
   * @param {string} key - string key
   * @returns {string} correspondent value. If doesn't exists original key.
   */
  static get(key) {
    let { language } = this;

    if (!(language in this.strings)) {
      console.warn(`Unknown language ${language} set in StringManager.`);
      language = 'en';
    }

    if (!(key in this.strings[language])) {
      console.warn(`Unknown key ${key} in StringManager.`);
      return key;
    }

    return this.strings[language][key];
  }
}

/**
 * Dictionary of dictionaries:
 * Key: language code
 * Value: Key: id of the string
 *        Value: translation of the string
 */
StringManager.strings = translations;

/**
 * Language of the translations; English by default
 */
StringManager.language = 'en';
