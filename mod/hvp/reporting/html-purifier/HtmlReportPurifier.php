<?php

/**
 * Class HtmlPurifier
 * Purify html
 *
 * XSS filters copied from drupal 7 common.inc. Some modifications done to
 * replace Drupal one-liner functions with corresponding flat PHP.
 */
class HtmlReportPurifier {

  /**
   * Filters HTML to prevent cross-site-scripting (XSS) vulnerabilities.
   *
   * Based on kses by Ulf Harnhammar, see http://sourceforge.net/projects/kses.
   * For examples of various XSS attacks, see: http://ha.ckers.org/xss.html.
   *
   * This code does four things:
   * - Removes characters and constructs that can trick browsers.
   * - Makes sure all HTML entities are well-formed.
   * - Makes sure all HTML tags and attributes are well-formed.
   * - Makes sure no HTML tags contain URLs with a disallowed protocol (e.g.
   *   javascript:).
   *
   * @param $string
   *   The string with raw HTML in it. It will be stripped of everything that can
   *   cause an XSS attack.
   * @param array $allowed_tags
   *   An array of allowed tags.
   *
   * @param bool $allowedStyles
   *
   * @return mixed|string An XSS safe version of $string, or an empty string if $string is not
   * An XSS safe version of $string, or an empty string if $string is not
   * valid UTF-8.
   * @ingroup sanitation
   */
  public static function filter_xss($string, $allowed_tags = array(
      'a', 'b', 'br', 'code', 'col', 'colgroup', 'dd', 'div', 'dl',
      'dt', 'em', 'figcaption', 'figure', 'footer', 'h1', 'h2', 'h3',
      'h4', 'h5', 'h6', 'header', 'hgroup', 'i', 'img', 'ins', 'li',
      'menu', 'meter', 'nav', 'ol', 'p', 's', 'section', 'span', 'strong',
      'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th',
      'thead', 'time', 'tr', 'tt', 'u', 'ul'), $allowedStyles = FALSE) {

    $stylePatterns = false;
    if ($allowedStyles) {
      $stylePatterns = array();
      $stylePatterns[] = '/^color: *(#[a-f0-9]{3}[a-f0-9]{3}?|rgba?\([0-9, ]+\)) *;?$/i';
      $stylePatterns[] = '/^background-color: *(#[a-f0-9]{3}[a-f0-9]{3}?|rgba?\([0-9, ]+\)) *;?$/i';
      $stylePatterns[] = '/^(height:\:?[0-9]{1,8}px; width:\:?[0-9]{1,8}px|width\:?[0-9]{1,8}px|height:500px)?$/i';
      $stylePatterns[] = '/^width\:?[0-9]{1,8}%?$/i';
      $stylePatterns[] = '/^height\:?[0-9]{1,8}%?$/i';
      $stylePatterns[] = '/^font-size:\:?[0-9]{1,8}px$/i';
    }

    if (strlen($string) == 0) {
      return $string;
    }
    // Only operate on valid UTF-8 strings. This is necessary to prevent cross
    // site scripting issues on Internet Explorer 6. (Line copied from
    // drupal_validate_utf8)
    if (preg_match('/^./us', $string) != 1) {
      return '';
    }

    // Store the text format.
    self::_filter_xss_split($allowed_tags, TRUE, $stylePatterns);
    // Remove NULL characters (ignored by some browsers).
    $string = str_replace(chr(0), '', $string);
    // Remove Netscape 4 JS entities.
    $string = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);

    // Defuse all HTML entities.
    $string = str_replace('&', '&amp;', $string);
    // Change back only well-formed entities in our whitelist:
    // Decimal numeric entities.
    $string = preg_replace('/&amp;#([0-9]+;)/', '&#\1', $string);
    // Hexadecimal numeric entities.
    $string = preg_replace('/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $string);
    // Named entities.
    $string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $string);
    return preg_replace_callback('%
      (
      <(?=[^a-zA-Z!/])  # a lone <
      |                 # or
      <!--.*?-->        # a comment
      |                 # or
      <[^>]*(>|$)       # a string that starts with a <, up until the > or the end of the string
      |                 # or
      >                 # just a >
      )%x', 'self::_filter_xss_split', $string);
  }

  /**
   * Processes an HTML tag.
   *
   * @param $m
   *   An array with various meaning depending on the value of $store.
   *   If $store is TRUE then the array contains the allowed tags.
   *   If $store is FALSE then the array has one element, the HTML tag to process.
   * @param bool $store
   *   Whether to store $m.
   * @param bool $allowedStyles Allow styles
   *
   * @return string If the element isn't allowed, an empty string. Otherwise, the cleaned up
   * If the element isn't allowed, an empty string. Otherwise, the cleaned up
   * version of the HTML element.
   */
  private static function _filter_xss_split($m, $store = FALSE, $n = FALSE) {
    static $allowed_html;
    static $allowed_styles;

    if ($store) {
      $allowed_html = array_flip($m);
      $allowed_styles = $n;
      return $allowed_html;
    }

    $string = $m[1];

    if (substr($string, 0, 1) != '<') {
      // We matched a lone ">" character.
      return '&gt;';
    }
    elseif (strlen($string) == 1) {
      // We matched a lone "<" character.
      return '&lt;';
    }

    if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9\-]+)\s*([^>]*)>?|(<!--.*?-->)$%', $string, $matches)) {
      // Seriously malformed.
      return '';
    }

    $slash    = trim($matches[1]);
    $elem     = &$matches[2];
    $attrList = &$matches[3];
    $comment  = &$matches[4];

    if ($comment) {
      $elem = '!--';
    }

    if (!isset($allowed_html[strtolower($elem)])) {
      // Disallowed HTML element.
      return '';
    }

    if ($comment) {
      return $comment;
    }

    if ($slash != '') {
      return "</$elem>";
    }

    // Is there a closing XHTML slash at the end of the attributes?
    $attrList    = preg_replace('%(\s?)/\s*$%', '\1', $attrList, -1, $count);
    $xhtml_slash = $count ? ' /' : '';

    // Clean up attributes.

    $attr2 = implode(' ', self::_filter_xss_attributes($attrList, $allowed_styles));
    $attr2 = preg_replace('/[<>]/', '', $attr2);
    $attr2 = strlen($attr2) ? ' ' . $attr2 : '';

    return "<$elem$attr2$xhtml_slash>";
  }

  /**
   * Processes a string of HTML attributes.
   *
   * @param $attr
   * @param array|bool|object $allowedStyles
   *
   * @return array Cleaned up version of the HTML attributes.
   * Cleaned up version of the HTML attributes.
   */
  private static function _filter_xss_attributes($attr, $allowedStyles = FALSE) {
    $attrArr  = array();
    $mode     = 0;
    $attrName = '';
    $skip     = FALSE;

    while (strlen($attr) != 0) {
      // Was the last operation successful?
      $working = 0;
      switch ($mode) {
        case 0:
          // Attribute name, href for instance.
          if (preg_match('/^([-a-zA-Z]+)/', $attr, $match)) {
            $attrName = strtolower($match[1]);
            $skip = (
              $attrName == 'style' ||
              substr($attrName, 0, 2) == 'on' ||
              substr($attrName, 0, 1) == '-' ||
              // Ignore long attributes to avoid unnecessary processing overhead.
              strlen($attrName) > 96
            );
            $working  = $mode = 1;
            $attr     = preg_replace('/^[-a-zA-Z]+/', '', $attr);
          }
          break;

        case 1:
          // Equals sign or valueless ("selected").
          if (preg_match('/^\s*=\s*/', $attr)) {
            $working = 1;
            $mode    = 2;
            $attr    = preg_replace('/^\s*=\s*/', '', $attr);
            break;
          }

          if (preg_match('/^\s+/', $attr)) {
            $working = 1;
            $mode    = 0;
            if (!$skip) {
              $attrArr[] = $attrName;
            }
            $attr = preg_replace('/^\s+/', '', $attr);
          }
          break;

        case 2:
          // Attribute value, a URL after href= for instance.
          if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match)) {
            if ($allowedStyles && $attrName === 'style') {
              // Allow certain styles
              foreach ($allowedStyles as $pattern) {
                if (preg_match($pattern, $match[1])) {
                  // All patterns are start to end patterns, and CKEditor adds one span per style
                  $attrArr[] = 'style="' . $match[1] . '"';
                  break;
                }
              }
              break;
            }

            $thisVal = self::filter_xss_bad_protocol($match[1]);

            if (!$skip) {
              $attrArr[] = "$attrName=\"$thisVal\"";
            }
            $working = 1;
            $mode    = 0;
            $attr    = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
            break;
          }

          if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match)) {
            $thisVal = self::filter_xss_bad_protocol($match[1]);

            if (!$skip) {
              $attrArr[] = "$attrName='$thisVal'";
            }
            $working = 1;
            $mode    = 0;
            $attr    = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
            break;
          }

          if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match)) {
            $thisVal = self::filter_xss_bad_protocol($match[1]);

            if (!$skip) {
              $attrArr[] = "$attrName=\"$thisVal\"";
            }
            $working = 1;
            $mode    = 0;
            $attr    = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
          }
          break;
      }

      if ($working == 0) {
        // Not well formed; remove and try again.
        $attr = preg_replace('/
          ^
          (
          "[^"]*("|$)     # - a string that starts with a double quote, up until the next double quote or the end of the string
          |               # or
          \'[^\']*(\'|$)| # - a string that starts with a quote, up until the next quote or the end of the string
          |               # or
          \S              # - a non-whitespace character
          )*              # any number of the above three
          \s*             # any number of whitespaces
          /x', '', $attr);
        $mode = 0;
      }
    }

    // The attribute list ends with a valueless attribute like "selected".
    if ($mode == 1 && !$skip) {
      $attrArr[] = $attrName;
    }
    return $attrArr;
  }

  /**
   * Processes an HTML attribute value and strips dangerous protocols from URLs.
   *
   * @param $string
   *   The string with the attribute value.
   * @param bool $decode
   *   (deprecated) Whether to decode entities in the $string. Set to FALSE if the
   *   $string is in plain text, TRUE otherwise. Defaults to TRUE. This parameter
   *   is deprecated and will be removed in Drupal 8. To process a plain-text URI,
   *   call _strip_dangerous_protocols() or check_url() instead.
   *
   * @return string Cleaned up and HTML-escaped version of $string.
   * Cleaned up and HTML-escaped version of $string.
   */
  private static function filter_xss_bad_protocol($string, $decode = TRUE) {
    // Get the plain text representation of the attribute value (i.e. its meaning).
    // @todo Remove the $decode parameter in Drupal 8, and always assume an HTML
    //   string that needs decoding.
    if ($decode) {
      $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars(self::_strip_dangerous_protocols($string), ENT_QUOTES, 'UTF-8', FALSE);
  }

  /**
   * Strips dangerous protocols (e.g. 'javascript:') from a URI.
   *
   * This function must be called for all URIs within user-entered input prior
   * to being output to an HTML attribute value. It is often called as part of
   * check_url() or filter_xss(), but those functions return an HTML-encoded
   * string, so this function can be called independently when the output needs to
   * be a plain-text string for passing to t(), l(), drupal_attributes(), or
   * another function that will call check_plain() separately.
   *
   * @param $uri
   *   A plain-text URI that might contain dangerous protocols.
   *
   * @return string A plain-text URI stripped of dangerous protocols. As with all plain-text
   * A plain-text URI stripped of dangerous protocols. As with all plain-text
   * strings, this return value must not be output to an HTML page without
   * check_plain() being called on it. However, it can be passed to functions
   * expecting plain-text strings.
   * @see check_url()
   */
  private static function _strip_dangerous_protocols($uri) {
    static $allowed_protocols;

    if (!isset($allowed_protocols)) {
      $allowed_protocols = array_flip(array('ftp', 'http', 'https', 'mailto'));
    }

    // Iteratively remove any invalid protocol found.
    do {
      $before   = $uri;
      $colonPos = strpos($uri, ':');
      if ($colonPos > 0) {
        // We found a colon, possibly a protocol. Verify.
        $protocol = substr($uri, 0, $colonPos);
        // If a colon is preceded by a slash, question mark or hash, it cannot
        // possibly be part of the URL scheme. This must be a relative URL, which
        // inherits the (safe) protocol of the base document.
        if (preg_match('![/?#]!', $protocol)) {
          break;
        }
        // Check if this is a disallowed protocol. Per RFC2616, section 3.2.3
        // (URI Comparison) scheme comparison must be case-insensitive.
        if (!isset($allowed_protocols[strtolower($protocol)])) {
          $uri = substr($uri, $colonPos + 1);
        }
      }
    } while ($before != $uri);

    return $uri;
  }
}
