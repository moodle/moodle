// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Description of import/upgrade into Moodle:
 * 1.) Download from https://github.com/pathable/truncate
 * 2.) Copy jquery.truncate.js into lib/amd/src/truncate.js
 * 3.) Edit truncate.js to return the $.truncate function as truncate
 * 4.) Apply Moodle changes from git commit 7172b33e241c4d42cff01f78bf8570408f43fdc2
 */

/**
 * Module for text truncation.
 *
 * Implementation provided by Pathable (thanks!).
 * See: https://github.com/pathable/truncate
 *
 * @module     core/truncate
 * @copyright  2017 Pathable
 *             2017 Mathias Bynens
 *             2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

  // Matches trailing non-space characters.
  var chop = /(\s*\S+|\s)$/;

  // Matches the first word in the string.
  var start = /^(\S*)/;

  // Matches any space characters.
  var space = /\s/;

  // Special thanks to Mathias Bynens for the multi-byte char
  // implementation. Much love.
  // see: https://github.com/mathiasbynens/String.prototype.at/blob/master/at.js
  var charLengthAt = function(text, position) {
    var string = String(text);
    var size = string.length;
    // `ToInteger`
    var index = position ? Number(position) : 0;
    if (index != index) { // better `isNaN`
      index = 0;
    }
    // Account for out-of-bounds indices
    // The odd lower bound is because the ToInteger operation is
    // going to round `n` to `0` for `-1 < n <= 0`.
    if (index <= -1 || index >= size) {
      return '';
    }
    // Second half of `ToInteger`
    index = index | 0;
    // Get the first code unit and code unit value
    var cuFirst = string.charCodeAt(index);
    var cuSecond;
    var nextIndex = index + 1;
    var len = 1;
    if ( // Check if it’s the start of a surrogate pair.
      cuFirst >= 0xD800 && cuFirst <= 0xDBFF && // high surrogate
      size > nextIndex // there is a next code unit
    ) {
      cuSecond = string.charCodeAt(nextIndex);
      if (cuSecond >= 0xDC00 && cuSecond <= 0xDFFF) { // low surrogate
        len = 2;
      }
    }
    return len;
  };

  var lengthMultiByte = function(text) {
    var count = 0;

    for (var i = 0; i < text.length; i += charLengthAt(text, i)) {
      count++;
    }

    return count;
  };

  var getSliceLength = function(text, amount) {
    if (!text.length) {
      return 0;
    }

    var length = 0;
    var count = 0;

    do {
      length += charLengthAt(text, length);
      count++;
    } while (length < text.length && count < amount);

    return length;
  };

  // Return a truncated html string.  Delegates to $.fn.truncate.
  $.truncate = function(html, options) {
    return $('<div></div>').append(html).truncate(options).html();
  };

  // Truncate the contents of an element in place.
  $.fn.truncate = function(options) {
    if (!isNaN(parseFloat(options))) options = {length: options};
    var o = $.extend({}, $.truncate.defaults, options);

    return this.each(function() {
      var self = $(this);

      if (o.noBreaks) self.find('br').replaceWith(' ');

      var ellipsisLength = o.ellipsis.length;
      var text = self.text();
      var textLength = lengthMultiByte(text);
      var excess = textLength - o.length + ellipsisLength;

      if (textLength < o.length) return;
      if (o.stripTags) self.text(text);

      // Chop off any partial words if appropriate.
      if (o.words && excess > 0) {
        var sliced = text.slice(0, getSliceLength(text, o.length - ellipsisLength) + 1);
        var replaced = sliced.replace(chop, '');
        var truncated = lengthMultiByte(replaced);
        var oneWord = sliced.match(space) ? false : true;

        if (o.keepFirstWord && truncated === 0) {
          excess = textLength - lengthMultiByte(start.exec(text)[0]) - ellipsisLength;
        } else if (oneWord && truncated === 0) {
          excess = textLength - o.length + ellipsisLength;
        } else {
          excess = textLength - truncated - 1;
        }
      }

      // The requested length is larger than the text. No need for ellipsis.
      if (excess > textLength) {
        excess = textLength - o.length;
      }

      if (excess < 0 || !excess && !o.truncated) return;

      // Iterate over each child node in reverse, removing excess text.
      $.each(self.contents().get().reverse(), function(i, el) {
        var $el = $(el);
        var text = $el.text();
        var length = lengthMultiByte(text);

        // If the text is longer than the excess, remove the node and continue.
        if (length <= excess) {
          o.truncated = true;
          excess -= length;
          $el.remove();
          return;
        }

        // Remove the excess text and append the ellipsis.
        if (el.nodeType === 3) {
          var splitAmount = length - excess;
          splitAmount = splitAmount >= 0 ? getSliceLength(text, splitAmount) : 0;
          $(el.splitText(splitAmount)).replaceWith(o.ellipsis);
          return false;
        }

        // Recursively truncate child nodes.
        $el.truncate($.extend(o, {length: length - excess + ellipsisLength}));
        return false;
      });
    });
  };

  $.truncate.defaults = {

    // Strip all html elements, leaving only plain text.
    stripTags: false,

    // Only truncate at word boundaries.
    words: false,

    // When 'words' is active, keeps the first word in the string
    // even if it's longer than a target length.
    keepFirstWord: false,

    // Replace instances of <br> with a single space.
    noBreaks: false,

    // The maximum length of the truncated html.
    length: Infinity,

    // The character to use as the ellipsis.  The word joiner (U+2060) can be
    // used to prevent a hanging ellipsis, but displays incorrectly in Chrome
    // on Windows 7.
    // http://code.google.com/p/chromium/issues/detail?id=68323
    //ellipsis: '\u2026' // '\u2060\u2026'
    ellipsis: '\u2026' // '\u2060\u2026'

  };

    return {
        truncate: $.truncate,
    };
});
