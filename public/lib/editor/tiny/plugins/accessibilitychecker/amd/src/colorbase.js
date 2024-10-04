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

/*
 * @package    tiny_accessibilitychecker
 * @copyright  2022, Stevani Andolo  <stevani@hotmail.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class {

    REGEX_HEX = /^#?([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})(\ufffe)?/;
    REGEX_HEX3 = /^#?([\da-fA-F]{1})([\da-fA-F]{1})([\da-fA-F]{1})(\ufffe)?/;
    REGEX_RGB = /rgba?\(([\d]{1,3}), ?([\d]{1,3}), ?([\d]{1,3}),? ?([.\d]*)?\)/;

    TYPES = {
        HEX: 'hex',
        RGB: 'rgb',
        RGBA: 'rgba'
    };

    KEYWORDS = {
        black: '000',
        silver: 'c0c0c0',
        gray: '808080',
        white: 'fff',
        maroon: '800000',
        red: 'f00',
        purple: '800080',
        fuchsia: 'f0f',
        green: '008000',
        lime: '0f0',
        olive: '808000',
        yellow: 'ff0',
        navy: '000080',
        blue: '00f',
        teal: '008080',
        aqua: '0ff'
    };

    STR_HEX = '#{*}{*}{*}';
    STR_RGB = 'rgb({*}, {*}, {*})';
    STR_RGBA = 'rgba({*}, {*}, {*}, {*})';

    toHex = (str) => {
        var clr = this._convertTo(str, 'hex'),
            isTransparent = clr.toLowerCase() === 'transparent';

        if (clr.charAt(0) !== '#' && !isTransparent) {
            clr = '#' + clr;
        }

        return isTransparent ? clr.toLowerCase() : clr.toUpperCase();
    };

    toRGB = (str) => {
        var clr = this._convertTo(str, 'rgb');
        return clr.toLowerCase();
    };

    toRGBA = (str) => {
        var clr = this._convertTo(str, 'rgba');
        return clr.toLowerCase();
    };

    toArray = (str) => {
        // Parse with regex and return "matches" array.
        var type = this.findType(str).toUpperCase(),
            regex,
            arr,
            length,
            lastItem;

        if (type === 'HEX' && str.length < 5) {
            type = 'HEX3';
        }

        if (type.charAt(type.length - 1) === 'A') {
            type = type.slice(0, -1);
        }

        regex = this._getRightValue('REGEX_' + type);

        if (regex) {
            arr = regex.exec(str) || [];
            length = arr.length;

            if (length) {
                arr.shift();
                length--;

                if (type === 'HEX3') {
                    arr[0] += arr[0];
                    arr[1] += arr[1];
                    arr[2] += arr[2];
                }
                lastItem = arr[length - 1];

                if (!lastItem) {
                    arr[length - 1] = 1;
                }
            }
        }
        return arr;
    };

    fromArray = (arr, template) => {
        arr = arr.concat();

        if (typeof template === 'undefined') {
            return arr.join(', ');
        }

        var replace = '{*}';
        template = this._getRightValue('STR_' + template.toUpperCase());

        if (arr.length === 3 && template.match(/\{\*\}/g).length === 4) {
            arr.push(1);
        }

        while (template.indexOf(replace) >= 0 && arr.length > 0) {
            template = template.replace(replace, arr.shift());
        }
        return template;
    };

    findType = (str) => {
        if (this.KEYWORDS[str]) {
            return 'keyword';
        }

        var index = str.indexOf('('),
            key;

        if (index > 0) {
            key = str.substr(0, index);
        }

        if (key && this.TYPES[key.toUpperCase()]) {
            return this.TYPES[key.toUpperCase()];
        }
        return 'hex';
    };

    _getAlpha = (clr) => {
        var alpha,
            arr = this.toArray(clr);

        if (arr.length > 3) {
            alpha = arr.pop();
        }
        return +alpha || 1;
    };

    _keywordToHex = (clr) => {
        var keyword = this.KEYWORDS[clr];

        if (keyword) {
            return keyword;
        }
        return keyword;
    };

    _convertTo = (clr, to) => {
        if (clr === 'transparent') {
            return clr;
        }

        var from = this.findType(clr),
            originalTo = to,
            needsAlpha,
            alpha,
            method,
            ucTo;

        if (from === 'keyword') {
            clr = this._keywordToHex(clr);
            from = 'hex';
        }

        if (from === 'hex' && clr.length < 5) {
            if (clr.charAt(0) === '#') {
                clr = clr.substr(1);
            }

            clr = '#' + clr.charAt(0) + clr.charAt(0) +
                        clr.charAt(1) + clr.charAt(1) +
                        clr.charAt(2) + clr.charAt(2);
        }

        if (from === to) {
            return clr;
        }

        if (from.charAt(from.length - 1) === 'a') {
            from = from.slice(0, -1);
        }

        needsAlpha = (to.charAt(to.length - 1) === 'a');
        if (needsAlpha) {
            to = to.slice(0, -1);
            alpha = this._getAlpha(clr);
        }

        ucTo = to.charAt(0).toUpperCase() + to.substr(1).toLowerCase();
        method = window['_' + from + 'To' + ucTo];

        // Check to see if need conversion to rgb first.
        // Check to see if there is a direct conversion method.
        // Convertions are: hex <-> rgb <-> hsl.
        if (!method) {
            if (from !== 'rgb' && to !== 'rgb') {
                clr = window['_' + from + 'ToRgb'](clr);
                from = 'rgb';
                method = window['_' + from + 'To' + ucTo];
            }
        }

        if (method) {
            clr = ((method)(clr, needsAlpha));
        }

        // Process clr from arrays to strings after conversions if alpha is needed.
        if (needsAlpha) {
            if (!Array.isArray(clr)) {
                clr = this.toArray(clr);
            }
            clr.push(alpha);
            clr = this.fromArray(clr, originalTo.toUpperCase());
        }
        return clr;
    };

    _hexToRgb = (str, array) => {
        var r, g, b;

        /* jshint bitwise:false */
        if (str.charAt(0) === '#') {
            str = str.substr(1);
        }

        /* eslint no-bitwise: */
        str = parseInt(str, 16);
        r = str >> 16;
        g = str >> 8 & 0xFF;
        b = str & 0xFF;

        if (array) {
            return [r, g, b];
        }
        return 'rgb(' + r + ', ' + g + ', ' + b + ')';
    };

    _rgbToHex = (str) => {
        /* jshint bitwise:false */
        var rgb = this.toArray(str),
            hex = rgb[2] | (rgb[1] << 8) | (rgb[0] << 16);

        hex = (+hex).toString(16);

        while (hex.length < 6) {
            hex = '0' + hex;
        }
        return '#' + hex;
    };

    _getRightValue = (string) => {
        let regex = null;
        if (string === 'REGEX_RGB') {
            regex = this.REGEX_RGB;
        } else if (string === 'REGEX_HEX') {
            regex = this.REGEX_HEX;
        } else if (string === 'REGEX_HEX3') {
            regex = this.REGEX_HEX3;
        } else if (string === 'STR_HEX') {
            regex = this.STR_HEX;
        } else if (string === 'STR_RGB') {
            regex = this.STR_RGB;
        } else if (string === 'STR_RGBA') {
            regex = this.STR_RGBA;
        }
        return regex;
    };
}
