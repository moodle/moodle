import TextCache from './textcache';
import ServiceProvider from './serviceprovider';
import MathML from './mathml';
import StringManager from './stringmanager';

/**
 * @classdesc
 * This class represents MathType accessible class. Converts MathML to accessible text and manages
 * the associated client-side cache.
 */
export default class Accessibility {
  /**
  * Static property.
  * Accessibility cache, each entry contains a MathML and its correspondent accessibility text.
  * @type {TextCache}
  */
  static get cache() {
    return Accessibility._cache;
  }

  /**
   * Static property setter.
   * Set accessibility cache.
   * @param {TextCahe} value - The property value.
   * @ignore
   */
  static set cache(value) {
    Accessibility._cache = value;
  }

  /**
   * Converts MathML strings to its accessible text representation.
   * @param {String} mathML - MathML to be converted to accessible text.
   * @param {String} [language] - Language of the accessible text. 'en' by default.
   * @param {Array.<String>} [data] - Parameters to send to mathml2accessible service.
   * @return {String} Accessibility text.
   */
  static mathMLToAccessible(mathML, language, data) {
    if (typeof (language) === 'undefined') {
      language = 'en';
    }
    // Check MathML class. If the class is chemistry,
    // we add chemistry to data to force accessibility service
    // to load chemistry grammar.
    if (MathML.containClass(mathML, 'wrs_chemistry')) {
      data.mode = 'chemistry';
    }
    let accessibleText = '';

    if (Accessibility.cache.get(mathML)) {
      accessibleText = Accessibility.cache.get(mathML);
    } else {
      data.service = 'mathml2accessible';
      data.lang = language;
      const accessibleJsonResponse = JSON.parse(ServiceProvider.getService('service', data));
      if (accessibleJsonResponse.status !== 'error') {
        accessibleText = accessibleJsonResponse.result.text;
        Accessibility.cache.populate(mathML, accessibleText);
      } else {
        accessibleText = StringManager.get('error_convert_accessibility');
      }
    }

    return accessibleText;
  }
}

/**
 * Contains an instance of TextCache class to manage the JavaScript accessible cache.
 * Each entry of the cache object contains the MathML and it's correspondent accessibility text.
 * @private
 * @type {TextCache}
 */
Accessibility._cache = new TextCache();
