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
 * Helper functions that come from the theme.js of the TinyMCE and that are
 * heavily used by the colorswat.js.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const hasProto = (v, constructor, predicate) => {
  if (predicate(v, constructor.prototype)) {
    return true;
  } else {
    return v.constructor?.name === constructor.name;
  }
};
export const typeOf = x => {
  const t = typeof x;
  if (x === null) {
    return 'null';
  } else if (t === 'object' && Array.isArray(x)) {
    return 'array';
  } else if (t === 'object' && hasProto(x, String, (o, proto) => proto.isPrototypeOf(o))) {
    return 'string';
  } else {
    return t;
  }
};
export const eq$1 = t => a => t === a;
export const isType$1 = type => value => typeOf(value) === type;
export const isString = isType$1('string');
export const isArray = isType$1('array');
export const isUndefined = eq$1(undefined);
export const isNullable = a => a === null || a === undefined;
export const isNonNullable = a => !isNullable(a);
export const isArrayOf = (value, pred) => {
  if (isArray(value)) {
    for (let i = 0, len = value.length; i < len; ++i) {
      if (!pred(value[i])) {
        return false;
      }
    }
    return true;
  }
  return false;
};
export const nativeIndexOf = Array.prototype.indexOf;
export const rawIndexOf = (ts, t) => nativeIndexOf.call(ts, t);
export const indexOf = (xs, x) => {
  const r = rawIndexOf(xs, x);
  return r === -1 ? Optional.none() : Optional.some(r);
};
export const noop = () => {
  // Do nothing.
};

export const contains$1 = (str, substr, start = 0, end) => {
  const idx = str.indexOf(substr, start);
  if (idx !== -1) {
    return isUndefined(end) ? true : idx + substr.length <= end;
  } else {
    return false;
  }
};
export const removeFromStart = (str, numChars) => {
  return str.substring(numChars);
};

export const toHex = component => {
  const hex = component.toString(16);
  return (hex.length === 1 ? '0' + hex : hex).toUpperCase();
};
export const fromRgba = rgbaColour => {
  const value = toHex(rgbaColour.red) + toHex(rgbaColour.green) + toHex(rgbaColour.blue) + toHex(rgbaColour.alpha);
  return hexColour(value);
};
export const rgbRegex = /^\s*rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)\s*$/i;
export const rgbaRegex = /^\s*rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d?(?:\.\d+)?)\s*\)\s*$/i;
export const fromStringValues = (red, green, blue, alpha) => {
  const r = parseInt(red, 10);
  const g = parseInt(green, 10);
  const b = parseInt(blue, 10);
  const a = parseFloat(alpha);
  return rgbaColour(r, g, b, a);
};
export const fromString = rgbaString => {
  if (rgbaString === 'transparent') {
    return Optional.some(rgbaColour(0, 0, 0, 0));
  }
  const rgbMatch = rgbRegex.exec(rgbaString);
  if (rgbMatch !== null) {
    return Optional.some(fromStringValues(rgbMatch[1], rgbMatch[2], rgbMatch[3], '1'));
  }
  const rgbaMatch = rgbaRegex.exec(rgbaString);
  if (rgbaMatch !== null) {
    return Optional.some(fromStringValues(rgbaMatch[1], rgbaMatch[2], rgbaMatch[3], rgbaMatch[4]));
  }
  return Optional.none();
};
export const removeLeading = (str, prefix) => {
  return startsWith(str, prefix) ? removeFromStart(str, prefix.length) : str;
};

export const checkRange = (str, substr, start) =>
  substr === '' || str.length >= substr.length && str.substr(start, start + substr.length) === substr;

export const hexColour = value => ({value});
export const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
export const longformRegex = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i;
export const longformAlphaRegex = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i;
export const isHexString = hex => shorthandRegex.test(hex) || longformRegex.test(hex) || longformAlphaRegex.test(hex);
export const normalizeHex = hex => removeLeading(hex, '#').toUpperCase();
export const fromString$1 = hex => isHexString(hex) ? Optional.some({value: normalizeHex(hex)}) : Optional.none();
export const startsWith = (str, prefix) => {
  return checkRange(str, prefix, 0);
};
export const anyToHex = color => fromString$1(color).orThunk(() => fromString(color).map(fromRgba)).getOrThunk(() => {
  const canvas = document.createElement('canvas');
  canvas.height = 1;
  canvas.width = 1;
  const canvasContext = canvas.getContext('2d');
  canvasContext.clearRect(0, 0, canvas.width, canvas.height);
  canvasContext.fillStyle = '#FFFFFF';
  canvasContext.fillStyle = color;
  canvasContext.fillRect(0, 0, 1, 1);
  const rgba = canvasContext.getImageData(0, 0, 1, 1).data;
  const r = rgba[0];
  const g = rgba[1];
  const b = rgba[2];
  const a = rgba[3];
  return fromRgba(rgbaColour(r, g, b, a));
});
export const rgbaColour = (red, green, blue, alpha) => ({
  red,
  green,
  blue,
  alpha
});

export const mapColors = colorMap => {
  const colors = [];
  for (let i = 0; i < colorMap.length; i += 2) {
    colors.push({
      text: colorMap[i + 1],
      value: '#' + anyToHex(colorMap[i]).value,
      type: 'choiceitem'
    });
  }
  return colors;
};

export class Optional {
  constructor(tag, value) {
    this.tag = tag;
    this.value = value;
  }

  static some(value) {
    return new Optional(true, value);
  }

  static none() {
    return Optional.singletonNone;
  }

  fold(onNone, onSome) {
    if (this.tag) {
      return onSome(this.value);
    } else {
      return onNone();
    }
  }

  isSome() {
    return this.tag;
  }

  isNone() {
    return !this.tag;
  }

  map(mapper) {
    if (this.tag) {
      return Optional.some(mapper(this.value));
    } else {
      return Optional.none();
    }
  }

  bind(binder) {
    if (this.tag) {
      return binder(this.value);
    } else {
      return Optional.none();
    }
  }

  exists(predicate) {
    return this.tag && predicate(this.value);
  }

  forall(predicate) {
    return !this.tag || predicate(this.value);
  }

  filter(predicate) {
    if (!this.tag || predicate(this.value)) {
      return this;
    } else {
      return Optional.none();
    }
  }

  getOr(replacement) {
    return this.tag ? this.value : replacement;
  }

  or(replacement) {
    return this.tag ? this : replacement;
  }

  getOrThunk(thunk) {
    return this.tag ? this.value : thunk();
  }

  orThunk(thunk) {
    return this.tag ? this : thunk();
  }

  getOrDie(message) {
    if (!this.tag) {
      throw new Error(message ?? 'Called getOrDie on None');
    } else {
      return this.value;
    }
  }

  static from(value) {
    return isNonNullable(value) ? Optional.some(value) : Optional.none();
  }

  getOrNull() {
    return this.tag ? this.value : null;
  }

  getOrUndefined() {
    return this.value;
  }

  each(worker) {
    if (this.tag) {
      worker(this.value);
    }
  }

  toArray() {
    return this.tag ? [this.value] : [];
  }

  toString() {
    return this.tag ? `some(${this.value})` : 'none()';
  }
}

Optional.singletonNone = new Optional(false);