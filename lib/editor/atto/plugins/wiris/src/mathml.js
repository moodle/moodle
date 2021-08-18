import Constants from './constants';
import Util from './util';

/**
 * @classdesc
 * This class represents a class to manage MathML objects.
 */
export default class MathML {
  /**
   * Checks if the mathml at position i is inside an HTML attribute or not.
   * @param {string} content - a string containing MathML code.
   * @param {number} i -  search index.
   * @return {boolean} true if is inside an HTML attribute. false otherwise.
   */
  static isMathmlInAttribute(content, i) {
    // Regex =
    // '^[\'"][\\s]*=[\\s]*[\\w-]+([\\s]*("[^"]*"|\'[^\']*\')[\\s]*
    // =[\\s]*[\\w-]+[\\s]*)*[\\s]+gmi<';
    const mathAtt = '[\'"][\\s]*=[\\s]*[\\w-]+'; // "=att OR '=att
    const attContent = '"[^"]*"|\'[^\']*\''; // "blabla" OR 'blabla'
    const att = `[\\s]*(${attContent})[\\s]*=[\\s]*[\\w-]+[\\s]*`; // "blabla"=att OR 'blabla'=att
    const atts = `('${att}')*`; // "blabla"=att1 "blabla"=att2
    const regex = `^${mathAtt}${atts}[\\s]+gmi<`; // "=att "blabla"=att1 "blabla"=att2 gmi< .
    const expression = new RegExp(regex);

    const actualContent = content.substring(0, i);
    const reversed = actualContent.split('').reverse().join('');
    const exists = expression.test(reversed);

    return exists;
  }

  /**
   * Decodes an encoded MathML with standard XML tags.
   * We use these entities because IE doesn't support html entities
   * on its attributes sometimes. Yes, sometimes.
   * @param {string} input - string to be decoded.
   * @return {string} decoded string.
   */
  static safeXmlDecode(input) {
    let { tagOpener } = Constants.safeXmlCharactersEntities;
    let { tagCloser } = Constants.safeXmlCharactersEntities;
    let { doubleQuote } = Constants.safeXmlCharactersEntities;
    let { realDoubleQuote } = Constants.safeXmlCharactersEntities;
    // Decoding entities.
    input = input.split(tagOpener).join(Constants.safeXmlCharacters.tagOpener);
    input = input.split(tagCloser).join(Constants.safeXmlCharacters.tagCloser);
    input = input.split(doubleQuote).join(Constants.safeXmlCharacters.doubleQuote);
    // Added to fix problem due to import from 1.9.x.
    input = input.split(realDoubleQuote).join(Constants.safeXmlCharacters.realDoubleQuote);

    // Blackboard.
    const { ltElement } = Constants.safeBadBlackboardCharacters;
    const { gtElement } = Constants.safeBadBlackboardCharacters;
    const { ampElement } = Constants.safeBadBlackboardCharacters;
    if ('_wrs_blackboard' in window && window._wrs_blackboard) {
      input = input.split(ltElement).join(Constants.safeGoodBlackboardCharacters.ltElement);
      input = input.split(gtElement).join(Constants.safeGoodBlackboardCharacters.gtElement);
      input = input.split(ampElement).join(Constants.safeGoodBlackboardCharacters.ampElement);
    }

    ({ tagOpener } = Constants.safeXmlCharacters);
    ({ tagCloser } = Constants.safeXmlCharacters);
    ({ doubleQuote } = Constants.safeXmlCharacters);
    ({ realDoubleQuote } = Constants.safeXmlCharacters);
    const { ampersand } = Constants.safeXmlCharacters;
    const { quote } = Constants.safeXmlCharacters;

    // Decoding characters.
    input = input.split(tagOpener).join(Constants.xmlCharacters.tagOpener);
    input = input.split(tagCloser).join(Constants.xmlCharacters.tagCloser);
    input = input.split(doubleQuote).join(Constants.xmlCharacters.doubleQuote);
    input = input.split(ampersand).join(Constants.xmlCharacters.ampersand);
    input = input.split(quote).join(Constants.xmlCharacters.quote);

    // We are replacing $ by & when its part of an entity for retrocompatibility.
    // Now, the standard is replace § by &.
    let returnValue = '';
    let currentEntity = null;

    for (let i = 0; i < input.length; i += 1) {
      const character = input.charAt(i);
      if (currentEntity == null) {
        if (character === '$') {
          currentEntity = '';
        } else {
          returnValue += character;
        }
      } else if (character === ';') {
        returnValue += `&${currentEntity}`;
        currentEntity = null;
      } else if (character.match(/([a-zA-Z0-9#._-] | '-')/)) { // Character is part of an entity.
        currentEntity += character;
      } else {
        returnValue += `$${currentEntity}`; // Is not an entity.
        currentEntity = null;
        i -= 1; // Parse again the current character.
      }
    }

    return returnValue;
  }

  /**
   * Encodes a MathML with standard XML tags to a MMathML encoded with safe XML tags.
   * We use these entities because IE doesn't support html entities on its attributes sometimes.
   * @param {string} input - input string to be encoded
   * @returns {string} encoded string.
   */
  static safeXmlEncode(input) {
    const { tagOpener } = Constants.xmlCharacters;
    const { tagCloser } = Constants.xmlCharacters;
    const { doubleQuote } = Constants.xmlCharacters;
    const { ampersand } = Constants.xmlCharacters;
    const { quote } = Constants.xmlCharacters;

    input = input.split(tagOpener).join(Constants.safeXmlCharacters.tagOpener);
    input = input.split(tagCloser).join(Constants.safeXmlCharacters.tagCloser);
    input = input.split(doubleQuote).join(Constants.safeXmlCharacters.doubleQuote);
    input = input.split(ampersand).join(Constants.safeXmlCharacters.ampersand);
    input = input.split(quote).join(Constants.safeXmlCharacters.quote);

    return input;
  }

  /**
   * Converts special symbols (> 128) to entities and replaces all textual
   * entities by its number entities.
   * @param {string} mathml - MathML string containing - or not - special symbols
   * @returns {string} MathML with all textual entities replaced.
   */
  static mathMLEntities(mathml) {
    let toReturn = '';

    for (let i = 0; i < mathml.length; i += 1) {
      const character = mathml.charAt(i);

      // Parsing > 128 characters.
      if (mathml.codePointAt(i) > 128) {
        toReturn += `&#${mathml.codePointAt(i)};`;
        // For UTF-32 characters we need to move the index one position.
        if (mathml.codePointAt(i) > 0xffff) {
          i += 1;
        }
      } else if (character === '&') {
        const end = mathml.indexOf(';', i + 1);
        if (end >= 0) {
          const container = document.createElement('span');
          container.innerHTML = mathml.substring(i, end + 1);
          toReturn += `&#${Util.fixedCharCodeAt((container.textContent || container.innerText), 0)};`;
          i = end;
        } else {
          toReturn += character;
        }
      } else {
        toReturn += character;
      }
    }

    return toReturn;
  }

  /**
   * Add a custom editor name with the prefix wrs_ to a MathML class attribute.
   * @param {string} mathml - a MathML string created with a custom editor, like chemistry.
   * @param {string} customEditor - custom editor name.
   * @returns {string} MathML string with his class containing the editor toolbar string.
   */
  static addCustomEditorClassAttribute(mathml, customEditor) {
    let toReturn = '';

    const start = mathml.indexOf('<math');
    if (start === 0) {
      const end = mathml.indexOf('>');
      if (mathml.indexOf('class') === -1) {
        // Adding custom editor type.
        toReturn = `${mathml.substr(start, end)} class="wrs_${customEditor}">`;
        toReturn += mathml.substr(end + 1, mathml.length);
        return toReturn;
      }
    }
    return mathml;
  }

  /**
   * Remove a custom editor name from the MathML class attribute.
   * @param {string} mathml - a MathML string.
   * @param {string} customEditor - custom editor name.
   * @returns {string} The input MathML without customEditor name in his class.
   */
  static removeCustomEditorClassAttribute(mathml, customEditor) {
    // Discard MathML without the specified class.
    if (mathml.indexOf('class') === -1 || mathml.indexOf(`wrs_${customEditor}`) === -1) {
      return mathml;
    }

    // Trivial case: class attribute value equal to editor name. Then
    // class attribute is removed.
    if (mathml.indexOf(`class="wrs_${customEditor}"`) !== -1) {
      return mathml.replace(`class="wrs_${customEditor}"`, '');
    }

    // Non Trivial case: class attribute contains editor name.
    return mathml.replace(`wrs_${customEditor}`, '');
  }

  /**
   * Adds annotation tag in MathML element.
   * @param {String} mathml - valid MathML.
   * @param {String} content - value to put inside annotation tag.
   * @param {String} annotationEncoding - annotation encoding.
   * @returns {String} - 'mathml' with an annotation that contains
   * 'content' and encoding 'encoding'.
   */
  static addAnnotation(mathml, content, annotationEncoding) {
    // If contains annotation, also contains semantics tag.
    const containsAnnotation = mathml.indexOf('<annotation');

    let mathmlWithAnnotation = '';
    if (containsAnnotation !== -1) {
      const closeSemanticsIndex = mathml.indexOf('</semantics>');
      mathmlWithAnnotation = `${mathml.substring(0, closeSemanticsIndex)}<annotation encoding="${annotationEncoding}">${content}</annotation>${mathml.substring(closeSemanticsIndex)}`;
    } else if (MathML.isEmpty(mathml)) {
      const endIndexInline = mathml.indexOf('/>');
      const endIndexNonInline = mathml.indexOf('>');
      const endIndex = endIndexNonInline === endIndexInline ? endIndexInline : endIndexNonInline;
      mathmlWithAnnotation = `${mathml.substring(0, endIndex)}><semantics><annotation encoding="${annotationEncoding}">${content}</annotation></semantics></math>`;
    } else {
      const beginMathMLContent = mathml.indexOf('>') + 1;
      const endMathmlContent = mathml.lastIndexOf('</math>');
      const mathmlContent = mathml.substring(beginMathMLContent, endMathmlContent);
      mathmlWithAnnotation = `${mathml.substring(0, beginMathMLContent)}<semantics>${mathmlContent}<annotation encoding="${annotationEncoding}">${content}</annotation></semantics></math>`;
    }

    return mathmlWithAnnotation;
  }

  /**
   * Removes specific annotation tag in MathML element.
   * In case of remove the unique annotation, also is removed semantics tag.
   * @param {String} mathml - valid MathML.
   * @param {String} annotationEncoding - annotation encoding to remove.
   * @returns {String} - 'mathml' without the annotation encoding specified.
   */
  static removeAnnotation(mathml, annotationEncoding) {
    let mathmlWithoutAnnotation = mathml;
    const openAnnotationTag = `<annotation encoding="${annotationEncoding}">`;
    const closeAnnotationTag = '</annotation>';
    const startAnnotationIndex = mathml.indexOf(openAnnotationTag);
    if (startAnnotationIndex !== -1) {
      let differentAnnotationFound = false;
      let differentAnnotationIndex = mathml.indexOf('<annotation');
      while (differentAnnotationIndex !== -1) {
        if (differentAnnotationIndex !== startAnnotationIndex) {
          differentAnnotationFound = true;
        }
        differentAnnotationIndex = mathml.indexOf('<annotation', differentAnnotationIndex + 1);
      }

      if (differentAnnotationFound) {
        const closeIndex = mathml.indexOf(closeAnnotationTag, startAnnotationIndex);
        const endAnnotationIndex = closeIndex + closeAnnotationTag.length;
        const startIndex = mathml.substring(0, startAnnotationIndex);
        mathmlWithoutAnnotation = startIndex + mathml.substring(endAnnotationIndex);
      } else {
        mathmlWithoutAnnotation = MathML.removeSemantics(mathml);
      }
    }

    return mathmlWithoutAnnotation;
  }

  /**
   * Removes semantics tag to mathml.
   * @param {string} mathml - MathML string.
   * @returns {string} - 'mathml' without semantics tag.
   */
  static removeSemantics(mathml) {
    const mathTagEnd = '</math>';
    const openSemantics = '<semantics>';
    const openAnnotation = '<annotation';

    let mathmlWithoutSemantics = mathml;
    const startSemantics = mathml.indexOf(openSemantics);
    if (startSemantics !== -1) {
      const startAnnotation = mathml.indexOf(openAnnotation, startSemantics + openSemantics.length);
      if (startAnnotation !== -1) {
        mathmlWithoutSemantics = mathml.substring(0, startSemantics)
        + mathml.substring(startSemantics + openSemantics.length, startAnnotation) + mathTagEnd;
      }
    }

    return mathmlWithoutSemantics;
  }

  /**
   * Transforms all xml mathml ocurrences that contain semantics to the same
   * xml mathml ocurrences without semantics.
   * @param {string} text - string that can contain xml mathml ocurrences.
   * @param {Constants} [characters] - Constant object containing xmlCharacters
   * or safeXmlCharacters relation.
   * xmlCharacters by default.
   * @returns {string} - 'text' with all xml mathml ocurrences without annotation tag.
   */
  static removeSemanticsOcurrences(text, characters = Constants.xmlCharacters) {
    const mathTagStart = `${characters.tagOpener}math`;
    const mathTagEnd = `${characters.tagOpener}/math${characters.tagCloser}`;
    const mathTagEndline = `/${characters.tagCloser}`;
    const { tagCloser } = characters;
    const semanticsTagStart = `${characters.tagOpener}semantics${characters.tagCloser}`;
    const annotationTagStart = `${characters.tagOpener}annotation encoding=`;

    let output = '';
    let start = text.indexOf(mathTagStart);
    let end = 0;
    while (start !== -1) {
      output += text.substring(end, start);

      // MathML can be written as '<math></math>' or '<math />'.
      const mathTagEndIndex = text.indexOf(mathTagEnd, start);
      const mathTagEndlineIndex = text.indexOf(mathTagEndline, start);
      const firstTagCloser = text.indexOf(tagCloser, start);
      if (mathTagEndIndex !== -1) {
        end = mathTagEndIndex;
      } else if (mathTagEndlineIndex === firstTagCloser - 1) {
        end = mathTagEndlineIndex;
      }

      const semanticsIndex = text.indexOf(semanticsTagStart, start);
      if (semanticsIndex !== -1) {
        const mmlTagStart = text.substring(start, semanticsIndex);
        const annotationIndex = text.indexOf(annotationTagStart, start);
        if (annotationIndex !== -1) {
          const startIndex = semanticsIndex + semanticsTagStart.length;
          const mmlContent = text.substring(startIndex, annotationIndex);
          output += mmlTagStart + mmlContent + mathTagEnd;
          start = text.indexOf(mathTagStart, start + mathTagStart.length);
          end += mathTagEnd.length;
        } else {
          end = start;
          start = text.indexOf(mathTagStart, start + mathTagStart.length);
        }
      } else {
        end = start;
        start = text.indexOf(mathTagStart, start + mathTagStart.length);
      }
    }

    output += text.substring(end, text.length);
    return output;
  }

  /**
   * Returns true if a MathML contains a certain class.
   * @param {string} mathML - input MathML.
   * @param {string} className - className.
   * @returns {boolean} true if the input MathML contains the input class.
   * false otherwise.
   * @static
   */
  static containClass(mathML, className) {
    const classIndex = mathML.indexOf('class');
    if (classIndex === -1) {
      return false;
    }
    const classTagEndIndex = mathML.indexOf('>', classIndex);
    const classTag = mathML.substring(classIndex, classTagEndIndex);
    if (classTag.indexOf(className) !== -1) {
      return true;
    }
    return false;
  }

  /**
   * Returns true if mathml is empty. Otherwise, false.
   * @param {string} mathml - valid MathML with standard XML tags.
   * @returns {boolean} - true if mathml is empty. Otherwise, false.
   */
  static isEmpty(mathml) {
    // MathML can have the shape <math></math> or '<math />'.
    const closeTag = '>';
    const closeTagInline = '/>';
    const firstCloseTagIndex = mathml.indexOf(closeTag);
    const firstCloseTagInlineIndex = mathml.indexOf(closeTagInline);
    let empty = false;
    // MathML is always empty in the second shape.
    if (firstCloseTagInlineIndex !== -1) {
      if (firstCloseTagInlineIndex === firstCloseTagIndex - 1) {
        empty = true;
      }
    }

    // MathML is always empty in the first shape when there aren't elements
    // between math tags.
    if (!empty) {
      const mathTagEndRegex = new RegExp('</(.+:)?math>');
      const mathTagEndArray = mathTagEndRegex.exec(mathml);
      if (mathTagEndArray) {
        empty = firstCloseTagIndex + 1 === mathTagEndArray.index;
      }
    }

    return empty;
  }
}
