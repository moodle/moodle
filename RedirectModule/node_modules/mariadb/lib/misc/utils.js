//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';
const hexArray = '0123456789ABCDEF'.split('');
const Errors = require('../misc/errors');
const Iconv = require('iconv-lite');
const TextEncoder = require('../cmd/encoder/text-encoder');

/**
 * Write bytes/hexadecimal value of a byte array to a string.
 * String output example :
 * 38 00 00 00 03 63 72 65  61 74 65 20 74 61 62 6C     8....create tabl
 * 65 20 42 6C 6F 62 54 65  73 74 63 6C 6F 62 74 65     e BlobTestclobte
 * 73 74 32 20 28 73 74 72  6D 20 74 65 78 74 29 20     st2 (strm text)
 * 43 48 41 52 53 45 54 20  75 74 66 38                 CHARSET utf8
 */
module.exports.log = function (opts, buf, off, end, header) {
  let out = [];
  if (!buf) return '';
  if (off === undefined || off === null) off = 0;
  if (end === undefined || end === null) end = buf.length;
  let asciiValue = new Array(16);
  asciiValue[8] = ' ';

  let useHeader = header !== undefined;
  let offset = off || 0;
  const maxLgh = Math.min(useHeader ? opts.debugLen - header.length : opts.debugLen, end - offset);
  const isLimited = end - offset > maxLgh;
  let byteValue;
  let posHexa = 0;
  let pos = 0;

  out.push(
    '+--------------------------------------------------+\n' +
      '|  0  1  2  3  4  5  6  7   8  9  a  b  c  d  e  f |\n' +
      '+--------------------------------------------------+------------------+\n'
  );

  if (useHeader) {
    while (pos < header.length) {
      if (posHexa === 0) out.push('| ');
      byteValue = header[pos++] & 0xff;
      out.push(hexArray[byteValue >>> 4], hexArray[byteValue & 0x0f], ' ');
      asciiValue[posHexa++] = byteValue > 31 && byteValue < 127 ? String.fromCharCode(byteValue) : '.';
      if (posHexa === 8) out.push(' ');
    }
  }

  pos = offset;
  while (pos < maxLgh + offset) {
    if (posHexa === 0) out.push('| ');
    byteValue = buf[pos] & 0xff;

    out.push(hexArray[byteValue >>> 4], hexArray[byteValue & 0x0f], ' ');

    asciiValue[posHexa++] = byteValue > 31 && byteValue < 127 ? String.fromCharCode(byteValue) : '.';

    if (posHexa === 8) out.push(' ');
    if (posHexa === 16) {
      out.push('| ', asciiValue.join(''), ' |\n');
      posHexa = 0;
    }
    pos++;
  }

  let remaining = posHexa;
  if (remaining > 0) {
    if (remaining < 8) {
      for (; remaining < 8; remaining++) {
        out.push('   ');
        asciiValue[posHexa++] = ' ';
      }
      out.push(' ');
    }

    for (; remaining < 16; remaining++) {
      out.push('   ');
      asciiValue[posHexa++] = ' ';
    }

    out.push('| ', asciiValue.join(''), isLimited ? ' |...\n' : ' |\n');
  } else if (isLimited) {
    out[out.length - 1] = ' |...\n';
  }
  out.push('+--------------------------------------------------+------------------+\n');
  return out.join('');
};

module.exports.escapeId = (opts, info, value) => {
  if (!value || value === '') {
    throw Errors.createError('Cannot escape empty ID value', Errors.ER_NULL_ESCAPEID, info, '0A000');
  }
  if (value.includes('\u0000')) {
    throw Errors.createError(
      'Cannot escape ID with null character (u0000)',
      Errors.ER_NULL_CHAR_ESCAPEID,
      info,
      '0A000'
    );
  }

  // always return escaped value, even when there is no special characters
  // to permit working with reserved words
  return '`' + value.replace(/`/g, '``') + '`';
};

const escapeParameters = (opts, info, value) => {
  if (value === undefined || value === null) return 'NULL';

  switch (typeof value) {
    case 'boolean':
      return value ? 'true' : 'false';
    case 'bigint':
    case 'number':
      return '' + value;
    case 'object':
      if (value instanceof Date) {
        return TextEncoder.getLocalDate(value);
      } else if (Buffer.isBuffer(value)) {
        let stValue;
        if (Buffer.isEncoding(info.collation.charset)) {
          stValue = value.toString(info.collation.charset, 0, value.length);
        } else {
          stValue = Iconv.decode(value, info.collation.charset);
        }
        return "_binary'" + escapeString(stValue) + "'";
      } else if (typeof value.toSqlString === 'function') {
        return "'" + escapeString(String(value.toSqlString())) + "'";
      } else if (Array.isArray(value)) {
        let out = opts.arrayParenthesis ? '(' : '';
        for (let i = 0; i < value.length; i++) {
          if (i !== 0) out += ',';
          out += escapeParameters(opts, info, value[i]);
        }
        if (opts.arrayParenthesis) out += ')';
        return out;
      } else {
        if (
          value.type != null &&
          [
            'Point',
            'LineString',
            'Polygon',
            'MultiPoint',
            'MultiLineString',
            'MultiPolygon',
            'GeometryCollection'
          ].includes(value.type)
        ) {
          //GeoJSON format.
          let prefix =
            info &&
            ((info.isMariaDB() && info.hasMinVersion(10, 1, 4)) || (!info.isMariaDB() && info.hasMinVersion(5, 7, 6)))
              ? 'ST_'
              : '';
          switch (value.type) {
            case 'Point':
              return prefix + "PointFromText('POINT(" + TextEncoder.geoPointToString(value.coordinates) + ")')";

            case 'LineString':
              return (
                prefix + "LineFromText('LINESTRING(" + TextEncoder.geoArrayPointToString(value.coordinates) + ")')"
              );

            case 'Polygon':
              return (
                prefix + "PolygonFromText('POLYGON(" + TextEncoder.geoMultiArrayPointToString(value.coordinates) + ")')"
              );

            case 'MultiPoint':
              return (
                prefix +
                "MULTIPOINTFROMTEXT('MULTIPOINT(" +
                TextEncoder.geoArrayPointToString(value.coordinates) +
                ")')"
              );

            case 'MultiLineString':
              return (
                prefix +
                "MLineFromText('MULTILINESTRING(" +
                TextEncoder.geoMultiArrayPointToString(value.coordinates) +
                ")')"
              );

            case 'MultiPolygon':
              return (
                prefix + "MPolyFromText('MULTIPOLYGON(" + TextEncoder.geoMultiPolygonToString(value.coordinates) + ")')"
              );

            case 'GeometryCollection':
              return (
                prefix +
                "GeomCollFromText('GEOMETRYCOLLECTION(" +
                TextEncoder.geometricCollectionToString(value.geometries) +
                ")')"
              );
          }
        } else {
          if (opts.permitSetMultiParamEntries) {
            let out = '';
            let first = true;
            for (let key in value) {
              const val = value[key];
              if (typeof val === 'function') continue;
              if (first) {
                first = false;
              } else {
                out += ',';
              }
              out += '`' + key + '`=';
              out += this.escape(opts, info, val);
            }
            if (out === '') return "'" + escapeString(JSON.stringify(value)) + "'";
            return out;
          } else {
            return "'" + escapeString(JSON.stringify(value)) + "'";
          }
        }
      }
    default:
      return "'" + escapeString(value) + "'";
  }
};

// see https://mariadb.com/kb/en/library/string-literals/
const LITTERAL_ESCAPE = {
  '\u0000': '\\0',
  "'": "\\'",
  '"': '\\"',
  '\b': '\\b',
  '\n': '\\n',
  '\r': '\\r',
  '\t': '\\t',
  '\u001A': '\\Z',
  '\\': '\\\\'
};

const escapeString = (val) => {
  const pattern = /[\u0000'"\b\n\r\t\u001A\\]/g;

  let offset = 0;
  let escaped = '';
  let match;

  while ((match = pattern.exec(val))) {
    escaped += val.substring(offset, match.index);
    escaped += LITTERAL_ESCAPE[match[0]];
    offset = pattern.lastIndex;
  }

  if (offset === 0) {
    return val;
  }

  if (offset < val.length) {
    escaped += val.substring(offset);
  }

  return escaped;
};

module.exports.escape = escapeParameters;
