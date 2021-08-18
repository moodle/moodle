/* eslint-disable no-bitwise */
import MathML from './mathml';
import Configuration from './configuration';
import Latex from './latex';
import Constants from './constants';
import StringManager from './stringmanager';

/**
 * This class represents an utility class.
 */
export default class Util {
  /**
   * Fires an event in a target.
   * @param {EventTarget} eventTarget - target where event should be fired.
   * @param {string} eventName event to fire.
   * @static
   */
  static fireEvent(eventTarget, eventName) {
    if (document.createEvent) {
      const eventObject = document.createEvent('HTMLEvents');
      eventObject.initEvent(eventName, true, true);
      return !eventTarget.dispatchEvent(eventObject);
    }

    const eventObject = document.createEventObject();
    return eventTarget.fireEvent(`on${eventName}`, eventObject);
  }

  /**
   * Cross-browser addEventListener/attachEvent function.
   * @param {EventTarget} eventTarget - target to add the event.
   * @param {string} eventName - specifies the type of event.
   * @param {Function} callBackFunction - callback function.
   * @static
   */
  static addEvent(eventTarget, eventName, callBackFunction) {
    if (eventTarget.addEventListener) {
      eventTarget.addEventListener(eventName, callBackFunction, true);
    } else if (eventTarget.attachEvent) {
      // Backwards compatibility.
      eventTarget.attachEvent(`on${eventName}`, callBackFunction);
    }
  }

  /**
   * Cross-browser removeEventListener/detachEvent function.
   * @param {EventTarget} eventTarget - target to add the event.
   * @param {string} eventName - specifies the type of event.
   * @param {Function} callBackFunction - function to remove from the event target.
   * @static
   */
  static removeEvent(eventTarget, eventName, callBackFunction) {
    if (eventTarget.removeEventListener) {
      eventTarget.removeEventListener(eventName, callBackFunction, true);
    } else if (eventTarget.detachEvent) {
      eventTarget.detachEvent(`on${eventName}`, callBackFunction);
    }
  }

  /**
   * Adds the a callback function, for a certain event target, to the following event types:
   * - dblclick
   * - mousedown
   * - mouseup
   * @param {EventTarget} eventTarget - event target.
   * @param {Function} doubleClickHandler - function to run when on dblclick event.
   * @param {Function} mousedownHandler - function to run when on mousedown event.
   * @param {Function} mouseupHandler - function to run when on mouseup event.
   * @static
   */
  static addElementEvents(eventTarget, doubleClickHandler, mousedownHandler, mouseupHandler) {
    if (doubleClickHandler) {
      Util.addEvent(eventTarget, 'dblclick', (event) => {
        const realEvent = (event) || window.event;
        const element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
        doubleClickHandler(element, realEvent);
      });
    }

    if (mousedownHandler) {
      Util.addEvent(eventTarget, 'mousedown', (event) => {
        const realEvent = (event) || window.event;
        const element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
        mousedownHandler(element, realEvent);
      });
    }

    if (mouseupHandler) {
      Util.addEvent(eventTarget, 'mouseup', (event) => {
        const realEvent = (event) || window.event;
        const element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
        mouseupHandler(element, realEvent);
      });
    }
  }

  /**
   * Adds a class name to a HTMLElement.
   * @param {HTMLElement} element - the HTML element.
   * @param {string} className - the class name.
   * @static
   */
  static addClass(element, className) {
    if (!Util.containsClass(element, className)) {
      element.className += ` ${className}`;
    }
  }

  /**
   * Checks if a HTMLElement contains a certain class.
   * @param {HTMLElement} element - the HTML element.
   * @param {string} className - the className.
   * @returns {boolean} true if the HTMLElement contains the class name. false otherwise.
   * @static
   */
  static containsClass(element, className) {
    if (element == null || !('className' in element)) {
      return false;
    }

    const currentClasses = element.className.split(' ');

    for (let i = currentClasses.length - 1; i >= 0; i -= 1) {
      if (currentClasses[i] === className) {
        return true;
      }
    }

    return false;
  }

  /**
   * Remove a certain class for a HTMLElement.
   * @param {HTMLElement} element - the HTML element.
   * @param {string} className - the class name.
   * @static
   */
  static removeClass(element, className) {
    let newClassName = '';
    const classes = element.className.split(' ');

    for (let i = 0; i < classes.length; i += 1) {
      if (classes[i] !== className) {
        newClassName += `${classes[i]} `;
      }
    }
    element.className = newClassName.trim();
  }

  /**
   * Converts old xml initial text attribute (with «») to the correct one(with §lt;§gt;). It's
   * used to parse old applets.
   * @param {string} text - string containing safeXml characters
   * @returns {string} a string with safeXml characters parsed.
   * @static
   */
  static convertOldXmlinitialtextAttribute(text) {
    // Used to fix a bug with Cas imported from Moodle 1.9 to Moodle 2.x.
    // This could be removed in future.
    const val = 'value=';

    const xitpos = text.indexOf('xmlinitialtext');
    const valpos = text.indexOf(val, xitpos);
    const quote = text.charAt(valpos + val.length);
    const startquote = valpos + val.length + 1;
    const endquote = text.indexOf(quote, startquote);

    const value = text.substring(startquote, endquote);

    let newvalue = value.split('«').join('§lt;');
    newvalue = newvalue.split('»').join('§gt;');
    newvalue = newvalue.split('&').join('§');
    newvalue = newvalue.split('¨').join('§quot;');

    text = text.split(value).join(newvalue);
    return text;
  }

  /**
   * Cross-browser solution for creating new elements.
   * @param {string} tagName - tag name of the wished element.
   * @param {Object} attributes - an object where each key is a wished
   * attribute name and each value is its value.
   * @param {Object} [creator] - if supplied, this function will use
   * the "createElement" method from this param. Otherwise
   * document will be used as creator.
   * @returns {Element} The DOM element with the specified attributes assigned.
   * @static
   */
  static createElement(tagName, attributes, creator) {
    if (attributes === undefined) {
      attributes = {};
    }

    if (creator === undefined) {
      creator = document;
    }

    let element;

    /*
    * Internet Explorer fix:
    * If you create a new object dynamically, you can't set a non-standard attribute.
    * For example, you can't set the "src" attribute on an "applet" object.
    * Other browsers will throw an exception and will run the standard code.
    */
    try {
      let html = `<${tagName}`;

      Object.keys(attributes).forEach((attributeName) => {
        html += ` ${attributeName}="${Util.htmlEntities(attributes[attributeName])}"`;
      });
      html += '>';
      element = creator.createElement(html);
    } catch (e) {
      element = creator.createElement(tagName);
      Object.keys(attributes).forEach((attributeName) => {
        element.setAttribute(attributeName, attributes[attributeName]);
      });
    }
    return element;
  }

  /**
   * Creates new HTML from it's HTML code as string.
   * @param {string} objectCode - html code
   * @returns {Element} the HTML element.
   * @static
   */
  static createObject(objectCode, creator) {
    if (creator === undefined) {
      creator = document;
    }

    // Internet Explorer can't include "param" tag when is setting an innerHTML property.
    objectCode = objectCode.split('<applet ').join('<span wirisObject="WirisApplet" ').split('<APPLET ').join('<span wirisObject="WirisApplet" '); // It is a 'span' because 'span' objects can contain 'br' nodes.
    objectCode = objectCode.split('</applet>').join('</span>').split('</APPLET>').join('</span>');

    objectCode = objectCode.split('<param ').join('<br wirisObject="WirisParam" ').split('<PARAM ').join('<br wirisObject="WirisParam" '); // It is a 'br' because 'br' can't contain nodes.
    objectCode = objectCode.split('</param>').join('</br>').split('</PARAM>').join('</br>');

    const container = Util.createElement('div', {}, creator);
    container.innerHTML = objectCode;

    function recursiveParamsFix(object) {
      if (object.getAttribute && object.getAttribute('wirisObject') === 'WirisParam') {
        const attributesParsed = {};

        for (let i = 0; i < object.attributes.length; i += 1) {
          if (object.attributes[i].nodeValue !== null) {
            attributesParsed[object.attributes[i].nodeName] = object.attributes[i].nodeValue;
          }
        }

        const param = Util.createElement('param', attributesParsed, creator);

        // IE fix.
        if (param.NAME) {
          param.name = param.NAME;
          param.value = param.VALUE;
        }

        param.removeAttribute('wirisObject');
        object.parentNode.replaceChild(param, object);
      } else if (object.getAttribute && object.getAttribute('wirisObject') === 'WirisApplet') {
        const attributesParsed = {};

        for (let i = 0; i < object.attributes.length; i += 1) {
          if (object.attributes[i].nodeValue !== null) {
            attributesParsed[object.attributes[i].nodeName] = object.attributes[i].nodeValue;
          }
        }

        const applet = Util.createElement('applet', attributesParsed, creator);
        applet.removeAttribute('wirisObject');

        for (let i = 0; i < object.childNodes.length; i += 1) {
          recursiveParamsFix(object.childNodes[i]);

          if (object.childNodes[i].nodeName.toLowerCase() === 'param') {
            applet.appendChild(object.childNodes[i]);
            i -= 1; // When we insert the object child into the applet, object loses one child.
          }
        }

        object.parentNode.replaceChild(applet, object);
      } else {
        for (let i = 0; i < object.childNodes.length; i += 1) {
          recursiveParamsFix(object.childNodes[i]);
        }
      }
    }

    recursiveParamsFix(container);
    return container.firstChild;
  }

  /**
   * Converts an Element to its HTML code.
   * @param {Element} element - entry element.
   * @return {string} the HTML code of the input element.
   * @static
   */
  static createObjectCode(element) {
    // In case that the image was not created, the object can be null or undefined.
    if (typeof element === 'undefined' || element === null) {
      return null;
    }

    if (element.nodeType === 1) { // ELEMENT_NODE.
      let output = `<${element.tagName}`;

      for (let i = 0; i < element.attributes.length; i += 1) {
        if (element.attributes[i].specified) {
          output += ` ${element.attributes[i].name}="${Util.htmlEntities(element.attributes[i].value)}"`;
        }
      }

      if (element.childNodes.length > 0) {
        output += '>';

        for (let i = 0; i < element.childNodes.length; i += 1) {
          output += Util.createObject(element.childNodes[i]);
        }

        output += `</${element.tagName}>`;
      } else if (element.nodeName === 'DIV' || element.nodeName === 'SCRIPT') {
        output += `></${element.tagName}>`;
      } else {
        output += '/>';
      }

      return output;
    }

    if (element.nodeType === 3) { // TEXT_NODE.
      return Util.htmlEntities(element.nodeValue);
    }

    return '';
  }

  /**
   * Concatenates two URL paths.
   * @param {string} path1 - first URL path
   * @param {string} path2 - second URL path
   * @returns {string} new URL.
   */
  static concatenateUrl(path1, path2) {
    let separator = '';
    if ((path1.indexOf('/') !== path1.length) && (path2.indexOf('/') !== 0)) {
      separator = '/';
    }
    return (path1 + separator + path2).replace(/([^:]\/)\/+/g, '$1');
  }

  /**
   * Parses a text and replaces all HTML special characters by their correspondent entities.
   * @param {string} input - text to be parsed.
   * @returns {string} the input text with all their special characters replaced by their entities.
   * @static
   */
  static htmlEntities(input) {
    return input.split('&').join('&amp;').split('<').join('&lt;')
      .split('>')
      .join('&gt;')
      .split('"')
      .join('&quot;');
  }

  /**
   * Parses a text and replaces all the HTML entities by their characters.
   * @param {string} input - text to be parsed
   * @returns {string} the input text with all their entities replaced by characters.
   * @static
   */
  static htmlEntitiesDecode(input) {
    // Textarea element decodes when inner html is set.
    const textarea = document.createElement('textarea');
    textarea.innerHTML = input;
    return textarea.value;
  }

  /**
   * Returns a cross-browser http request.
   * @return {object} httpRequest request object.
   * @returns {XMLHttpRequest|ActiveXObject} the proper request object.
   */
  static createHttpRequest() {
    const currentPath = window.location.toString().substr(0, window.location.toString().lastIndexOf('/') + 1);
    if (currentPath.substr(0, 7) === 'file://') {
      throw StringManager.get('exception_cross_site');
    }

    if (typeof XMLHttpRequest !== 'undefined') {
      return new XMLHttpRequest();
    }

    try {
      return new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
      try {
        return new ActiveXObject('Microsoft.XMLHTTP');
      } catch (oc) {
        return null;
      }
    }
  }

  /**
   * Converts a hash to a HTTP query.
   * @param {Object[]} properties - a key/value object.
   * @returns {string} a HTTP query containing all the key value pairs with
   * all the special characters replaced by their entities.
   * @static
   */
  static httpBuildQuery(properties) {
    let result = '';

    Object.keys(properties).forEach((i) => {
      if (properties[i] != null) {
        result += `${Util.urlEncode(i)}=${Util.urlEncode(properties[i])}&`;
      }
    });

    // Deleting last '&' empty character.
    if (result.substring(result.length - 1) === '&') {
      result = result.substring(0, result.length - 1);
    }

    return result;
  }

  /**
   * Convert a hash to string sorting keys to get a deterministic output
   * @param {Object[]} hash - a key/value object.
   * @returns {string} a string with the form key1=value1...keyn=valuen
   * @static
   */
  static propertiesToString(hash) {
    // 1. Sort keys. We sort the keys because we want a deterministic output.
    const keys = [];
    Object.keys(hash).forEach((key) => {
      if (Object.prototype.hasOwnProperty.call(hash, key)) {
        keys.push(key);
      }
    });

    const n = keys.length;
    for (let i = 0; i < n; i += 1) {
      for (let j = i + 1; j < n; j += 1) {
        const s1 = keys[i];
        const s2 = keys[j];
        if (Util.compareStrings(s1, s2) > 0) {
          // Swap.
          keys[i] = s2;
          keys[j] = s1;
        }
      }
    }

    // 2. Generate output.
    let output = '';
    for (let i = 0; i < n; i += 1) {
      const key = keys[i];
      output += key;
      output += '=';
      let value = hash[key];
      value = value.replace('\\', '\\\\');
      value = value.replace('\n', '\\n');
      value = value.replace('\r', '\\r');
      value = value.replace('\t', '\\t');

      output += value;
      output += '\n';
    }
    return output;
  }

  /**
   * Compare two strings using charCodeAt method
   * @param {string} a - first string to compare.
   * @param {string} b - second string to compare.
   * @returns {number} the difference between a and b
   * @static
   */
  static compareStrings(a, b) {
    let i;
    const an = a.length;
    const bn = b.length;
    const n = (an > bn) ? bn : an;
    for (i = 0; i < n; i += 1) {
      const c = Util.fixedCharCodeAt(a, i) - Util.fixedCharCodeAt(b, i);
      if (c !== 0) {
        return c;
      }
    }
    return a.length - b.length;
  }

  /**
   * Fix charCodeAt() JavaScript function to handle non-Basic-Multilingual-Plane characters.
   * @param {string} string - input string
   * @param {number} idx - an integer greater than or equal to 0
   * and less than the length of the string
   * @returns {number} an integer representing the UTF-16 code of the string at the given index.
   * @static
   */
  static fixedCharCodeAt(string, idx) {
    idx = idx || 0;
    const code = string.charCodeAt(idx);
    let hi;
    let low;

    /* High surrogate (could change last hex to 0xDB7F to treat high
    private surrogates as single characters) */

    if (code >= 0xD800 && code <= 0xDBFF) {
      hi = code;
      low = string.charCodeAt(idx + 1);
      if (Number.isNaN(low)) {
        throw StringManager.get('exception_high_surrogate');
      }
      return ((hi - 0xD800) * 0x400) + (low - 0xDC00) + 0x10000;
    }

    if (code >= 0xDC00 && code <= 0xDFFF) { // Low surrogate.
      /* We return false to allow loops to skip this iteration since should have
      already handled high surrogate above in the previous iteration. */
      return false;
    }
    return code;
  }

  /**
   * Returns an URL with it's query params converted into array.
   * @param {string} url - input URL.
   * @returns {Object[]} an array containing all URL query params.
   * @static
   */
  static urlToAssArray(url) {
    let i;
    i = url.indexOf('?');
    if (i > 0) {
      const query = url.substring(i + 1);
      const ss = query.split('&');
      const h = {};
      for (i = 0; i < ss.length; i += 1) {
        const s = ss[i];
        const kv = s.split('=');
        if (kv.length > 1) {
          h[kv[0]] = decodeURIComponent(kv[1].replace(/\+/g, ' '));
        }
      }
      return h;
    }
    return {};
  }

  /**
   * Returns an encoded URL by replacing each instance of certain characters by
   * one, two, three or four escape sequences using encodeURIComponent method.
   * !'()* . will not be encoded.
   *
   * @param {string} clearString - URL string to be encoded
   * @returns {string} URL with it's special characters replaced.
   * @static
   */
  static urlEncode(clearString) {
    let output = '';
    // Method encodeURIComponent doesn't encode !'()*~ .
    output = encodeURIComponent(clearString);
    return output;
  }

  // TODO: To parser?
  /**
   * Converts the HTML of a image into the output code that WIRIS must return.
   * By default returns the MathML stored on data-mahml attribute (if imgCode is a formula)
   * or the Wiriscas attribute of a WIRIS applet.
   * @param {string} imgCode - the html code from a formula or a CAS image.
   * @param {boolean} convertToXml - true if the image should be converted to XML.
   * @param {boolean} convertToSafeXml - true if the image should be converted to safeXML.
   * @returns {string} the XML or safeXML of a WIRIS image.
   * @static
   */
  static getWIRISImageOutput(imgCode, convertToXml, convertToSafeXml) {
    const imgObject = Util.createObject(imgCode);
    if (imgObject) {
      if (imgObject.className === Configuration.get('imageClassName') || imgObject.getAttribute(Configuration.get('imageMathmlAttribute'))) {
        if (!convertToXml) {
          return imgCode;
        }

        let xmlCode = imgObject.getAttribute(Configuration.get('imageMathmlAttribute'));
        if (!Configuration.get('saveHandTraces')) {
          xmlCode = MathML.removeSemanticsOcurrences(xmlCode, Constants.safeXmlCharacters);
        }

        if (xmlCode == null) {
          xmlCode = imgObject.getAttribute('alt');
        }

        if (!convertToSafeXml) {
          xmlCode = MathML.safeXmlDecode(xmlCode);
        }

        return xmlCode;
      }
      if (imgObject.className === Configuration.get('CASClassName')) {
        let appletCode = imgObject.getAttribute(Configuration.get('CASMathmlAttribute'));
        appletCode = MathML.safeXmlDecode(appletCode);
        const appletObject = Util.createObject(appletCode);
        appletObject.setAttribute('src', imgObject.src);
        let appletCodeToBeInserted = Util.createObjectCode(appletObject);

        if (convertToSafeXml) {
          appletCodeToBeInserted = MathML.safeXmlEncode(appletCodeToBeInserted);
        }

        return appletCodeToBeInserted;
      }
    }

    return imgCode;
  }

  /**
   * Gets the node length in characters.
   * @param {Node} node - HTML node.
   * @returns {number} node length.
   * @static
   */
  static getNodeLength(node) {
    const staticNodeLengths = {
      IMG: 1,
      BR: 1,
    };

    if (node.nodeType === 3) { // TEXT_NODE.
      return node.nodeValue.length;
    }

    if (node.nodeType === 1) { // ELEMENT_NODE.
      let length = staticNodeLengths[node.nodeName.toUpperCase()];

      if (length === undefined) {
        length = 0;
      }

      for (let i = 0; i < node.childNodes.length; i += 1) {
        length += Util.getNodeLength(node.childNodes[i]);
      }
      return length;
    }
    return 0;
  }

  /**
   * Gets a selected node or text from an editable HTMLElement.
   * If the caret is on a text node, concatenates it with all the previous and next text nodes.
   * @param {HTMLElement} target - the editable HTMLElement.
   * @param {boolean} isIframe  - specifies if the target is an iframe or not
   * @param {boolean} forceGetSelection - if true, ignores IE system to get
   * the current selection and uses window.getSelection()
   * @returns {object} an object with the 'node' key set if the item is an
   * element or the keys 'node' and 'caretPosition' if the element is text.
   * @static
   */
  static getSelectedItem(target, isIframe, forceGetSelection) {
    let windowTarget;

    if (isIframe) {
      windowTarget = target.contentWindow;
      windowTarget.focus();
    } else {
      windowTarget = window;
      target.focus();
    }

    if (document.selection && !forceGetSelection) {
      const range = windowTarget.document.selection.createRange();

      if (range.parentElement) {
        if (range.htmlText.length > 0) {
          if (range.text.length === 0) {
            return Util.getSelectedItem(target, isIframe, true);
          }

          return null;
        }

        windowTarget.document.execCommand('InsertImage', false, '#');
        let temporalObject = range.parentElement();

        if (temporalObject.nodeName.toUpperCase() !== 'IMG') {
          // IE9 fix: parentElement() does not return the IMG node,
          // returns the parent DIV node. In IE < 9, pasteHTML does not work well.
          range.pasteHTML('<span id="wrs_openEditorWindow_temporalObject"></span>');
          temporalObject = windowTarget.document.getElementById('wrs_openEditorWindow_temporalObject');
        }

        let node;
        let caretPosition;

        if (temporalObject.nextSibling && temporalObject.nextSibling.nodeType === 3) { // TEXT_NODE.
          node = temporalObject.nextSibling;
          caretPosition = 0;
        } else if (temporalObject.previousSibling
          && temporalObject.previousSibling.nodeType === 3) {
          node = temporalObject.previousSibling;
          caretPosition = node.nodeValue.length;
        } else {
          node = windowTarget.document.createTextNode('');
          temporalObject.parentNode.insertBefore(node, temporalObject);
          caretPosition = 0;
        }

        temporalObject.parentNode.removeChild(temporalObject);

        return {
          node,
          caretPosition,
        };
      }

      if (range.length > 1) {
        return null;
      }

      return {
        node: range.item(0),
      };
    }

    if (windowTarget.getSelection) {
      let range;
      const selection = windowTarget.getSelection();

      try {
        range = selection.getRangeAt(0);
      } catch (e) {
        range = windowTarget.document.createRange();
      }

      const node = range.startContainer;

      if (node.nodeType === 3) { // TEXT_NODE.
        return {
          node,
          caretPosition: range.startOffset,
        };
      }

      if (node !== range.endContainer) {
        return null;
      }

      if (node.nodeType === 1) { // ELEMENT_NODE.
        const position = range.startOffset;

        if (node.childNodes[position]) {
          return {
            node: node.childNodes[position],
          };
        }
      }
    }

    return null;
  }

  /**
   * Returns null if there isn't any item or if it is malformed.
   * Otherwise returns an object containing the node with the MathML image
   * and the cursor position inside the textarea.
   * @param {HTMLTextAreaElement} textarea - textarea element.
   * @returns {Object} An object containing the node, the index of the
   * beginning  of the selected text, caret position and the start and end position of the
   * text node.
   * @static
   */
  static getSelectedItemOnTextarea(textarea) {
    const textNode = document.createTextNode(textarea.value);
    const textNodeValues = Latex.getLatexFromTextNode(textNode, textarea.selectionStart);
    if (textNodeValues === null) {
      return null;
    }

    return {
      node: textNode,
      caretPosition: textarea.selectionStart,
      startPosition: textNodeValues.startPosition,
      endPosition: textNodeValues.endPosition,
    };
  }

  /**
   * Looks for elements that match the given name in a HTML code string.
   * Important: this function is very concrete for WIRIS code.
   * It takes as preconditions lots of behaviors that are not the general case.
   * @param {string} code -  HTML code.
   * @param {string} name - element name.
   * @param {boolean} autoClosed - true if the elements are autoClosed.
   * @return {Object[]} an object containing all HTML elements of code matching the name argument.
   * @static
   */
  static getElementsByNameFromString(code, name, autoClosed) {
    const elements = [];
    code = code.toLowerCase();
    name = name.toLowerCase();
    let start = code.indexOf(`<${name} `);

    while (start !== -1) { // Look for nodes.
      let endString;

      if (autoClosed) {
        endString = '>';
      } else {
        endString = `</${name}>`;
      }

      let end = code.indexOf(endString, start);

      if (end !== -1) {
        end += endString.length;
        elements.push({
          start,
          end,
        });
      } else {
        end = start + 1;
      }

      start = code.indexOf(`<${name} `, end);
    }

    return elements;
  }

  /**
   * Returns the numeric value of a base64 character.
   * @param  {string} character - base64 character.
   * @returns {number} base64 character numeric value.
   * @static
   */
  static decode64(character) {
    const PLUS = '+'.charCodeAt(0);
    const SLASH = '/'.charCodeAt(0);
    const NUMBER = '0'.charCodeAt(0);
    const LOWER = 'a'.charCodeAt(0);
    const UPPER = 'A'.charCodeAt(0);
    const PLUS_URL_SAFE = '-'.charCodeAt(0);
    const SLASH_URL_SAFE = '_'.charCodeAt(0);
    const code = character.charCodeAt(0);

    if (code === PLUS || code === PLUS_URL_SAFE) {
      return 62; // Char '+'.
    }
    if (code === SLASH || code === SLASH_URL_SAFE) {
      return 63; // Char '/'.
    }
    if (code < NUMBER) {
      return -1; // No match.
    }
    if (code < NUMBER + 10) {
      return code - NUMBER + 26 + 26;
    }
    if (code < UPPER + 26) {
      return code - UPPER;
    }
    if (code < LOWER + 26) {
      return code - LOWER + 26;
    }

    return null;
  }

  /**
   * Converts a base64 string to a array of bytes.
   * @param {string} b64String - base64 string.
   * @param {number} length - dimension of byte array (by default whole string).
   * @return {Object[]} the resultant byte array.
   * @static
   */
  static b64ToByteArray(b64String, length) {
    let tmp;

    if (b64String.length % 4 > 0) {
      throw new Error('Invalid string. Length must be a multiple of 4'); // Tipped base64. Length is fixed.
    }

    const arr = [];

    let l;
    let placeHolders;
    if (!length) { // All b64String string.
      if (b64String.charAt(b64String.length - 2) === '=') {
        placeHolders = 2;
      } else if (b64String.charAt(b64String.length - 1) === '=') {
        placeHolders = 1;
      } else {
        placeHolders = 0;
      }
      l = placeHolders > 0 ? b64String.length - 4 : b64String.length;
    } else {
      l = length;
    }

    let i;
    for (i = 0; i < l; i += 4) {
      // Ignoring code checker standards (bitewise operators).
      // See https://tracker.moodle.org/browse/CONTRIB-5862 for further information.
      // @codingStandardsIgnoreStart
      // eslint-disable-next-line max-len
      tmp = (Util.decode64(b64String.charAt(i)) << 18) | (Util.decode64(b64String.charAt(i + 1)) << 12) | (Util.decode64(b64String.charAt(i + 2)) << 6) | Util.decode64(b64String.charAt(i + 3));

      arr.push((tmp >> 16) & 0xFF);
      arr.push((tmp >> 8) & 0xFF);
      arr.push(tmp & 0xFF);
      // @codingStandardsIgnoreEnd
    }

    if (placeHolders) {
      if (placeHolders === 2) {
        // Ignoring code checker standards (bitewise operators).
        // @codingStandardsIgnoreStart
        // eslint-disable-next-line max-len
        tmp = (Util.decode64(b64String.charAt(i)) << 2) | (Util.decode64(b64String.charAt(i + 1)) >> 4);
        arr.push(tmp & 0xFF);
      } else if (placeHolders === 1) {
        // eslint-disable-next-line max-len
        tmp = (Util.decode64(b64String.charAt(i)) << 10) | (Util.decode64(b64String.charAt(i + 1)) << 4) | (Util.decode64(b64String.charAt(i + 2)) >> 2);
        arr.push((tmp >> 8) & 0xFF);
        arr.push(tmp & 0xFF);
        // @codingStandardsIgnoreEnd
      }
    }
    return arr;
  }

  /**
   * Returns the first 32-bit signed integer from a byte array.
   * @param {Object[]} bytes - array of bytes.
   * @returns {number} the 32-bit signed integer.
   * @static
   */
  static readInt32(bytes) {
    if (bytes.length < 4) {
      return false;
    }
    const int32 = bytes.splice(0, 4);
    // @codingStandardsIgnoreStart
    // eslint-disable-next-line no-mixed-operators
    return (int32[0] << 24 | int32[1] << 16 | int32[2] << 8 | int32[3] << 0);
    // @codingStandardsIgnoreEnd
  }

  /**
   * Read the first byte from a byte array.
   * @param {Object} bytes - input byte array.
   * @returns {number} first byte of the byte array.
   * @static
   */
  static readByte(bytes) {
    // @codingStandardsIgnoreStart
    return bytes.shift() << 0;
    // @codingStandardsIgnoreEnd
  }

  /**
   * Read an arbitrary number of bytes, from a fixed position on a byte array.
   * @param  {Object[]} bytes - byte array.
   * @param  {number} pos - start position.
   * @param  {number} len - number of bytes to read.
   * @returns {Object[]} the byte array.
   * @static
   */
  static readBytes(bytes, pos, len) {
    return bytes.splice(pos, len);
  }

  /**
   * Inserts or modifies formulas or CAS on a textarea.
   * @param {HTMLTextAreaElement} textarea - textarea target.
   * @param {string} text - text to add in the textarea. For example, to add the link to the image,
   * call this function as (textarea, Parser.createImageSrc(mathml));
   * @static
   */
  static updateTextArea(textarea, text) {
    if (textarea && text) {
      textarea.focus();

      if (textarea.selectionStart != null) {
        const { selectionEnd } = textarea;
        const selectionStart = textarea.value.substring(0, textarea.selectionStart);
        const selectionEndSub = textarea.value.substring(selectionEnd, textarea.value.length);
        textarea.value = selectionStart + text + selectionEndSub;
        textarea.selectionEnd = selectionEnd + text.length;
      } else {
        const selection = document.selection.createRange();
        selection.text = text;
      }
    }
  }

  /**
   * Modifies existing formula on a textarea.
   * @param {HTMLTextAreaElement} textarea - text area target.
   * @param {string} text - text to add in the textarea. For example, if you want to add the link
   * to the image,you can call this function as
   * Util.updateTextarea(textarea, Parser.createImageSrc(mathml));
   * @param {number} start - beginning index from textarea where it needs to be replaced by text.
   * @param {number} end - ending index from textarea where it needs to be replaced by text
   * @static
   */
  static updateExistingTextOnTextarea(textarea, text, start, end) {
    textarea.focus();
    const textareaStart = textarea.value.substring(0, start);
    textarea.value = textareaStart + text + textarea.value.substring(end, textarea.value.length);
    textarea.selectionEnd = start + text.length;
  }

  /**
   * Add a parameter with it's correspondent value to an URL (GET).
   * @param {string} path - URL path
   * @param {string} parameter - parameter
   * @param {string} value - value
   * @static
   */
  static addArgument(path, parameter, value) {
    let sep;
    if (path.indexOf('?') > 0) {
      sep = '&';
    } else {
      sep = '?';
    }
    return `${path + sep + parameter}=${value}`;
  }
}
