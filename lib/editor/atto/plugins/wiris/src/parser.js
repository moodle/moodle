import Util from './util';
import Latex from './latex';
import MathML from './mathml';
import Image from './image';
import Accessibility from './accessibility';
import ServiceProvider from './serviceprovider';
import Configuration from './configuration';
import Constants from './constants';
// eslint-disable-next-line no-unused-vars
import md5 from './md5';

/**
 * @classdesc
 * This class represent a MahML parser. Converts MathML into formulas depending on the
 * image format (SVG, PNG, base64) and the save mode (XML, safeXML, Image) configured
 * in the backend.
 */
export default class Parser {
  /**
   * Converts a MathML string to an img element.
   * @param {Document} creator - Document object to call createElement method.
   * @param {string} mathml - MathML code
   * @param {Object[]} wirisProperties - object containing WIRIS custom properties
   * @param {language} language - custom language for accessibility.
   * @returns {HTMLImageElement} the formula image corresponding to initial MathML string.
   * @static
   */
  static mathmlToImgObject(creator, mathml, wirisProperties, language) {
    const imgObject = creator.createElement('img');
    imgObject.align = 'middle';
    imgObject.style.maxWidth = 'none';
    const data = wirisProperties || {};

    data.mml = mathml;
    data.lang = language;
    // Request metrics of the generated image.
    data.metrics = 'true';
    data.centerbaseline = 'false';


    // Full base64 method (edit & save).
    if (Configuration.get('saveMode') === 'base64' && Configuration.get('editMode') === 'default') {
      data.base64 = true;
    }

    // Render js params: _wrs_int_wirisProperties contains some js render params.
    // Since MathML can support render params, js params should be send only to editor.

    imgObject.className = Configuration.get('imageClassName');

    if (mathml.indexOf('class="') !== -1) {
      // We check here if the MathML has been created from a customEditor (such chemistry)
      // to add custom editor name attribute to img object (if necessary).
      let mathmlSubstring = mathml.substring(mathml.indexOf('class="') + 'class="'.length, mathml.length);
      mathmlSubstring = mathmlSubstring.substring(0, mathmlSubstring.indexOf('"'));
      mathmlSubstring = mathmlSubstring.substring(4, mathmlSubstring.length);
      imgObject.setAttribute(Configuration.get('imageCustomEditorName'), mathmlSubstring);
    }

    // Performance enabled.
    if (Configuration.get('wirisPluginPerformance') && (Configuration.get('saveMode') === 'xml' || Configuration.get('saveMode') === 'safeXml')) {
      let result = JSON.parse(Parser.createShowImageSrc(data, language));
      if (result.status === 'warning') {
        // POST call.
        // if the mathml is malformed, this function will throw an exception.
        try {
          result = JSON.parse(ServiceProvider.getService('showimage', data));
        } catch (e) {
          return null;
        }
      }
      ({ result } = result);
      if (result.format === 'png') {
        imgObject.src = `data:image/png;base64,${result.content}`;
      } else {
        imgObject.src = `data:image/svg+xml;charset=utf8,${Util.urlEncode(result.content)}`;
      }
      imgObject.setAttribute(Configuration.get('imageMathmlAttribute'), MathML.safeXmlEncode(mathml));
      Image.setImgSize(imgObject, result.content, true);

      if (Configuration.get('enableAccessibility')) {
        if (typeof result.alt === 'undefined') {
          imgObject.alt = Accessibility.mathMLToAccessible(mathml, language, data);
        } else {
          imgObject.alt = result.alt;
        }
      }
    } else {
      const result = Parser.createImageSrc(mathml, data);
      imgObject.setAttribute(Configuration.get('imageMathmlAttribute'), MathML.safeXmlEncode(mathml));
      imgObject.src = result;
      Image.setImgSize(imgObject, result, Configuration.get('saveMode') === 'base64' && Configuration.get('editMode') === 'default');
      if (Configuration.get('enableAccessibility')) {
        imgObject.alt = Accessibility.mathMLToAccessible(mathml, language, data);
      }
    }

    if (typeof Parser.observer !== 'undefined') {
      Parser.observer.observe(imgObject);
    }

    // Role math https://www.w3.org/TR/wai-aria/roles#math.
    imgObject.setAttribute('role', 'math');
    return imgObject;
  }

  /**
   * Returns the source to showimage service by calling createimage service. The
   * output of the createimage service is a URL path pointing to showimage service.
   * This method is called when performance is disabled.
   * @param {string} mathml - MathML code.
   * @param {Object[]} data - data object containing service parameters.
   * @returns {string} the showimage path.
   */
  static createImageSrc(mathml, data) {
    // Full base64 method (edit & save).
    if (Configuration.get('saveMode') === 'base64' && Configuration.get('editMode') === 'default') {
      data.base64 = true;
    }

    let result = ServiceProvider.getService('createimage', data);

    if (result.indexOf('@BASE@') !== -1) {
      // Replacing '@BASE@' with the base URL of createimage.
      const baseParts = ServiceProvider.getServicePath('createimage').split('/');
      baseParts.pop();
      result = result.split('@BASE@').join(baseParts.join('/'));
    }

    return result;
  }

  /**
   * Parses initial HTML code. If the HTML contains data generated by WIRIS,
   * this data would be converted as following:
   * <pre>
   * MathML code: Image containing the corresponding MathML formulas.
   * MathML code with LaTeX annotation : LaTeX string.
   * </pre>
   * @param {string} code - HTML code containing MathML data.
   * @param {string} language - language to create image alt text.
   * @returns {string} HTML code with the original MathML converted into LaTeX and images.
   */
  static initParse(code, language) {
    /* Note: The code inside this function has been inverted.
    If you invert again the code then you cannot use correctly LaTeX
    in Moodle.
    */
    code = Parser.initParseSaveMode(code, language);
    return Parser.initParseEditMode(code);
  }

  /**
   * Parses initial HTML code depending on the save mode. Transforms all MathML
   * occurrences for it's correspondent image or LaTeX.
   * @param {string} code - HTML code to be parsed
   * @param {string} language - language to create image alt text.
   * @returns {string} HTML code parsed.
   */
  static initParseSaveMode(code, language) {
    if (Configuration.get('saveMode')) {
      // Converting XML to tags.
      code = Latex.parseMathmlToLatex(code, Constants.safeXmlCharacters);
      code = Latex.parseMathmlToLatex(code, Constants.xmlCharacters);
      code = Parser.parseMathmlToImg(code, Constants.safeXmlCharacters, language);
      code = Parser.parseMathmlToImg(code, Constants.xmlCharacters, language);
      if (Configuration.get('saveMode') === 'base64' && Configuration.get('editMode') === 'image') {
        code = Parser.codeImgTransform(code, 'base642showimage');
      }
    }
    return code;
  }

  /**
   * Parses initial HTML code depending on the edit mode.
   * If 'latex' parseMode is enabled all MathML containing an annotation with encoding='LaTeX' will
   * be converted into a LaTeX string instead of an image.
   * @param {string} code - HTML code containing MathML.
   * @returns {string} parsed HTML code.
   */
  static initParseEditMode(code) {
    if (Configuration.get('parseModes').indexOf('latex') !== -1) {
      const imgList = Util.getElementsByNameFromString(code, 'img', true);
      const token = 'encoding="LaTeX">';
      // While replacing images with latex, the indexes of the found images changes
      // respecting the original code, so this carry is needed.
      let carry = 0;

      for (let i = 0; i < imgList.length; i += 1) {
        const imgCode = code.substring(imgList[i].start + carry, imgList[i].end + carry);

        if (imgCode.indexOf(` class="${Configuration.get('imageClassName')}"`) !== -1) {
          let mathmlStartToken = ` ${Configuration.get('imageMathmlAttribute')}="`;
          let mathmlStart = imgCode.indexOf(mathmlStartToken);

          if (mathmlStart === -1) {
            mathmlStartToken = ' alt="';
            mathmlStart = imgCode.indexOf(mathmlStartToken);
          }

          if (mathmlStart !== -1) {
            mathmlStart += mathmlStartToken.length;
            const mathmlEnd = imgCode.indexOf('"', mathmlStart);
            const mathml = MathML.safeXmlDecode(imgCode.substring(mathmlStart, mathmlEnd));
            let latexStartPosition = mathml.indexOf(token);

            if (latexStartPosition !== -1) {
              latexStartPosition += token.length;
              const latexEndPosition = mathml.indexOf('</annotation>', latexStartPosition);
              const latex = mathml.substring(latexStartPosition, latexEndPosition);

              const replaceText = `$$${Util.htmlEntitiesDecode(latex)}$$`;
              const start = code.substring(0, imgList[i].start + carry);
              const end = code.substring(imgList[i].end + carry);
              code = start + replaceText + end;
              carry += replaceText.length - (imgList[i].end - imgList[i].start);
            }
          }
        }
      }
    }

    return code;
  }

  /**
   * Parses end HTML code. The end HTML code is HTML code with embedded images
   * or LaTeX formulas created with MathType. <br>
   * By default this method converts the formula images and LaTeX strings in MathML. <br>
   * If image mode is enabled the images will not be converted into MathML. For further information see {@link http://www.wiris.com/plugins/docs/full-mathml-mode}.
   * @param {string} code - HTML to be parsed
   * @returns {string} the HTML code parsed.
   */
  static endParse(code) {
    // Transform LaTeX ocurrences to MathML elements.
    const codeEndParsedEditMode = Parser.endParseEditMode(code);
    // Transform img elements to MathML elements.
    const codeEndParseSaveMode = Parser.endParseSaveMode(codeEndParsedEditMode);
    return codeEndParseSaveMode;
  }

  /**
   * Parses end HTML code depending on the edit mode.
   * - LaTeX is an enabled parse mode, all LaTeX occurrences will be converted into MathML.
   * @param {string} code - HTML code to be parsed.
   * @returns {string} HTML code parsed.
   */
  static endParseEditMode(code) {
    // Converting LaTeX to images.
    if (Configuration.get('parseModes').indexOf('latex') !== -1) {
      let output = '';
      let endPosition = 0;
      let startPosition = code.indexOf('$$');
      while (startPosition !== -1) {
        output += code.substring(endPosition, startPosition);
        endPosition = code.indexOf('$$', startPosition + 2);

        if (endPosition !== -1) {
          // Before, it was a condition here to execute the next codelines
          // 'latex.indexOf('<') == -1'.
          // We don't know why it was used, but seems to have a conflict with
          // latex formulas that contains '<'.
          const latex = code.substring(startPosition + 2, endPosition);
          const decodedLatex = Util.htmlEntitiesDecode(latex);
          let mathml = Latex.getMathMLFromLatex(decodedLatex, true);
          if (!Configuration.get('saveHandTraces')) {
            // Remove hand traces.
            mathml = MathML.removeAnnotation(mathml, 'application/json');
          }
          output += mathml;
          endPosition += 2;
        } else {
          output += '$$';
          endPosition = startPosition + 2;
        }

        startPosition = code.indexOf('$$', endPosition);
      }

      output += code.substring(endPosition, code.length);
      code = output;
    }

    return code;
  }

  /**
   * Parses end HTML code depending on the save mode. Converts all
   * images into the element determined by the save mode:
   * - xml: Parses images formulas into MathML.
   * - safeXml: Parses images formulas into safeMAthML
   * - base64: Parses images into base64 images.
   * - image: Parse images into images (no parsing)
   * @param {string} code - HTML code to be parsed
   * @returns {string} HTML code parsed.
   */
  static endParseSaveMode(code) {
    if (Configuration.get('saveMode')) {
      if (Configuration.get('saveMode') === 'safeXml') {
        code = Parser.codeImgTransform(code, 'img2mathml');
      } else if (Configuration.get('saveMode') === 'xml') {
        code = Parser.codeImgTransform(code, 'img2mathml');
      } else if (Configuration.get('saveMode') === 'base64' && Configuration.get('editMode') === 'image') {
        code = Parser.codeImgTransform(code, 'img264');
      }
    }

    return code;
  }

  /**
   * Returns the result to call showimage service with the formula md5 as parameter.
   *  The result could be:
   * - {'status' : warning'} : The image associated to the MathML md5 is not in cache.
   * - {'status' : 'ok' ...} : The image associated to the MathML md5 is in cache.
   * @param {Object[]} data - object containing showimage service parameters.
   * @param {string} language - string containing the language of the formula.
   * @returns {Object} JSON object containing showimage response.
   */
  static createShowImageSrc(data, language) {
    const dataMd5 = [];
    const renderParams = ['mml', 'color', 'centerbaseline', 'zoom', 'dpi', 'fontSize', 'fontFamily', 'defaultStretchy', 'backgroundColor', 'format'];
    renderParams.forEach((key) => {
      const param = renderParams[key];
      if (typeof data[param] !== 'undefined') {
        dataMd5[param] = data[param];
      }
    });
    // Data variables to get.
    const dataObject = {};
    Object.keys(data).forEach((key) => {
      // We don't need mathml in this request we try to get cached.
      // Only need the formula md5 calculated before.
      if (key !== 'mml') {
        dataObject[key] = data[key];
      }
    });

    dataObject.formula = com.wiris.js.JsPluginTools.md5encode(Util.propertiesToString(dataMd5));
    dataObject.lang = (typeof language === 'undefined') ? 'en' : language;
    dataObject.version = Configuration.get('version');

    const result = ServiceProvider.getService('showimage', Util.httpBuildQuery(dataObject), true);
    return result;
  }

  /**
   * Transform html img tags inside a html code to mathml, base64 img tags (i.e with base64 on src)
   * or showimage img tags (i.e with showimage.php on src)
   * @param  {string} code - HTML code
   * @param  {string} mode - base642showimage or img2mathml or img264 transform.
   * @returns {string} html - code transformed.
   */
  static codeImgTransform(code, mode) {
    let output = '';
    let endPosition = 0;
    const pattern = /<img/gi;
    const patternLength = pattern.source.length;

    while (pattern.test(code)) {
      const startPosition = pattern.lastIndex - patternLength;
      output += code.substring(endPosition, startPosition);

      let i = startPosition + 1;

      while (i < code.length && endPosition <= startPosition) {
        const character = code.charAt(i);

        if (character === '"' || character === '\'') {
          const characterNextPosition = code.indexOf(character, i + 1);

          if (characterNextPosition === -1) {
            i = code.length; // End while.
          } else {
            i = characterNextPosition;
          }
        } else if (character === '>') {
          endPosition = i + 1;
        }

        i += 1;
      }

      if (endPosition < startPosition) { // The img tag is stripped.
        output += code.substring(startPosition, code.length);
        return output;
      }
      let imgCode = code.substring(startPosition, endPosition);
      const imgObject = Util.createObject(imgCode);
      let xmlCode = imgObject.getAttribute(Configuration.get('imageMathmlAttribute'));
      let convertToXml;
      let convertToSafeXml;

      if (mode === 'base642showimage') {
        if (xmlCode == null) {
          xmlCode = imgObject.getAttribute('alt');
        }
        xmlCode = MathML.safeXmlDecode(xmlCode);
        imgCode = Parser.mathmlToImgObject(document, xmlCode, null, null);
        output += Util.createObjectCode(imgCode);
      } else if (mode === 'img2mathml') {
        if (Configuration.get('saveMode')) {
          if (Configuration.get('saveMode') === 'safeXml') {
            convertToXml = true;
            convertToSafeXml = true;
          } else if (Configuration.get('saveMode') === 'xml') {
            convertToXml = true;
            convertToSafeXml = false;
          }
        }
        output += Util.getWIRISImageOutput(imgCode, convertToXml, convertToSafeXml);
      } else if (mode === 'img264') {
        if (xmlCode === null) {
          xmlCode = imgObject.getAttribute('alt');
        }
        xmlCode = MathML.safeXmlDecode(xmlCode);

        const properties = {};
        properties.base64 = 'true';
        imgCode = Parser.mathmlToImgObject(document, xmlCode, properties, null);
        // Metrics.
        Image.setImgSize(imgCode, imgCode.src, true);
        output += Util.createObjectCode(imgCode);
      }
    }
    output += code.substring(endPosition, code.length);
    return output;
  }

  /**
   * Converts all occurrences of MathML to the corresponding image.
   * @param {string} content - string with valid MathML code.
   * The MathML code doesn't contain semantics.
   * @param {Constants} characters - Constant object containing xmlCharacters
   * or safeXmlCharacters relation.
   * @param {string} language - a valid language code
   * in order to generate formula accessibility.
   * @returns {string} The input string with all the MathML
   * occurrences replaced by the corresponding image.
   */
  static parseMathmlToImg(content, characters, language) {
    let output = '';
    const mathTagBegin = `${characters.tagOpener}math`;
    const mathTagEnd = `${characters.tagOpener}/math${characters.tagCloser}`;
    let start = content.indexOf(mathTagBegin);
    let end = 0;

    while (start !== -1) {
      output += content.substring(end, start);
      // Avoid WIRIS images to be parsed.
      const imageMathmlAtrribute = content.indexOf(Configuration.get('imageMathmlAttribute'));
      end = content.indexOf(mathTagEnd, start);

      if (end === -1) {
        end = content.length - 1;
      } else if (imageMathmlAtrribute !== -1) {
        // First close tag of img attribute
        // If a mathmlAttribute exists should be inside a img tag.
        end += content.indexOf('/>', start);
      } else {
        end += mathTagEnd.length;
      }

      if (!MathML.isMathmlInAttribute(content, start) && imageMathmlAtrribute === -1) {
        let mathml = content.substring(start, end);
        mathml = (characters.id === Constants.safeXmlCharacters.id)
          ? MathML.safeXmlDecode(mathml)
          : MathML.mathMLEntities(mathml);
        output += Util.createObjectCode(Parser.mathmlToImgObject(document, mathml, null, language));
      } else {
        output += content.substring(start, end);
      }

      start = content.indexOf(mathTagBegin, end);
    }

    output += content.substring(end, content.length);
    return output;
  }
}

// Mutation observers to avoid wiris image formulas class be removed.
if (typeof MutationObserver !== 'undefined') {
  const mutationObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.oldValue === Configuration.get('imageClassName')
        && mutation.attributeName === 'class'
        && mutation.target.className.indexOf(Configuration.get('imageClassName')) === -1) {
        mutation.target.className = Configuration.get('imageClassName');
      }
    });
  });

  Parser.observer = Object.create(mutationObserver);
  Parser.observer.Config = { attributes: true, attributeOldValue: true };
  // We use own default config.
  Parser.observer.observe = function name(target) {
    Object.getPrototypeOf(this).observe(target, this.Config);
  };
}
