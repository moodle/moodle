//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const FieldType = require('../../const/field-type');
const Errors = require('../../misc/errors');

class TextDecoder {
  static castWrapper(column, packet, opts, nullBitmap, index) {
    column.string = () => packet.readStringLengthEncoded();
    column.buffer = () => packet.readBufferLengthEncoded();
    column.float = () => packet.readFloatLengthCoded();
    column.tiny = () => packet.readIntLengthEncoded();
    column.short = () => packet.readIntLengthEncoded();
    column.int = () => packet.readIntLengthEncoded();
    column.long = () => packet.readBigIntLengthEncoded();
    column.decimal = () => packet.readDecimalLengthEncoded();
    column.date = () => packet.readDate(opts);
    column.datetime = () => packet.readDateTime();

    column.geometry = () => {
      let defaultVal = null;
      if (column.dataTypeName) {
        switch (column.dataTypeName) {
          case 'point':
            defaultVal = { type: 'Point' };
            break;
          case 'linestring':
            defaultVal = { type: 'LineString' };
            break;
          case 'polygon':
            defaultVal = { type: 'Polygon' };
            break;
          case 'multipoint':
            defaultVal = { type: 'MultiPoint' };
            break;
          case 'multilinestring':
            defaultVal = { type: 'MultiLineString' };
            break;
          case 'multipolygon':
            defaultVal = { type: 'MultiPolygon' };
            break;
          default:
            defaultVal = { type: column.dataTypeName };
            break;
        }
      }

      return packet.readGeometry(defaultVal);
    };
  }
  static parser(col, opts) {
    // set reader function read(col, packet, index, nullBitmap, opts, throwUnexpectedError)
    // this permit for multi-row result-set to avoid resolving type parsing each data.

    switch (col.columnType) {
      case FieldType.TINY:
      case FieldType.SHORT:
      case FieldType.INT:
      case FieldType.INT24:
      case FieldType.YEAR:
        return readIntLengthEncoded;

      case FieldType.FLOAT:
      case FieldType.DOUBLE:
        return readFloatLengthCoded;

      case FieldType.BIGINT:
        if (opts.bigIntAsNumber || opts.supportBigNumbers) return readBigIntAsNumberLengthCoded;
        return readBigIntLengthCoded;

      case FieldType.DECIMAL:
      case FieldType.NEWDECIMAL:
        return col.scale === 0 ? readDecimalAsIntLengthCoded : readDecimalLengthCoded;

      case FieldType.DATE:
        return readDate;

      case FieldType.DATETIME:
      case FieldType.TIMESTAMP:
        return readTimestamp;

      case FieldType.TIME:
        return readAsciiStringLengthEncoded;

      case FieldType.GEOMETRY:
        let defaultVal = col.__getDefaultGeomVal();
        return readGeometry.bind(null, defaultVal);

      case FieldType.JSON:
        //for mysql only => parse string as JSON object
        return readJson;

      case FieldType.BIT:
        if (col.columnLength === 1 && opts.bitOneIsBoolean) {
          return readBitAsBoolean;
        }
        return readBufferLengthEncoded;

      default:
        if (col.dataTypeFormat && col.dataTypeFormat === 'json' && opts.autoJsonMap) {
          return readJson;
        }
        if (col.collation.index === 63) {
          return readBufferLengthEncoded;
        }
        if (col.isSet()) {
          return readSet;
        }
        return readStringLengthEncoded;
    }
  }
}

module.exports = TextDecoder;

const readGeometry = (defaultVal, packet, opts, throwUnexpectedError) => packet.readGeometry(defaultVal);

const readIntLengthEncoded = (packet, opts, throwUnexpectedError) => packet.readIntLengthEncoded();
const readStringLengthEncoded = (packet, opts, throwUnexpectedError) => packet.readStringLengthEncoded();
const readFloatLengthCoded = (packet, opts, throwUnexpectedError) => packet.readFloatLengthCoded();
const readBigIntLengthCoded = (packet, opts, throwUnexpectedError) => packet.readBigIntLengthEncoded();

const readBigIntAsNumberLengthCoded = (packet, opts, throwUnexpectedError) => {
  const len = packet.readUnsignedLength();
  if (len === null) return null;
  if (len < 16) {
    const val = packet._atoi(len);
    if (opts.supportBigNumbers && opts.bigNumberStrings) {
      return '' + val;
    }
    return val;
  }

  const val = packet.readBigIntFromLen(len);
  if (opts.bigIntAsNumber && opts.checkNumberRange && !Number.isSafeInteger(Number(val))) {
    return throwUnexpectedError(
      `value ${val} can't safely be converted to number`,
      false,
      null,
      '42000',
      Errors.ER_PARSING_PRECISION
    );
  }
  if (opts.supportBigNumbers && (opts.bigNumberStrings || !Number.isSafeInteger(Number(val)))) {
    return val.toString();
  }
  return Number(val);
};

const readDecimalAsIntLengthCoded = (packet, opts, throwUnexpectedError) => {
  const valDec = packet.readDecimalLengthEncoded();
  if (valDec != null && (opts.decimalAsNumber || opts.supportBigNumbers)) {
    if (opts.decimalAsNumber && opts.checkNumberRange && !Number.isSafeInteger(Number(valDec))) {
      return throwUnexpectedError(
        `value ${valDec} can't safely be converted to number`,
        false,
        null,
        '42000',
        Errors.ER_PARSING_PRECISION
      );
    }
    if (opts.supportBigNumbers && (opts.bigNumberStrings || !Number.isSafeInteger(Number(valDec)))) {
      return valDec.toString();
    }
    return Number(valDec);
  }
  return valDec;
};
const readDecimalLengthCoded = (packet, opts, throwUnexpectedError) => {
  const valDec = packet.readDecimalLengthEncoded();
  if (valDec != null && (opts.decimalAsNumber || opts.supportBigNumbers)) {
    if (opts.supportBigNumbers && (opts.bigNumberStrings || !Number.isSafeInteger(Number(valDec)))) {
      return valDec.toString();
    }
    return Number(valDec);
  }
  return valDec;
};
const readDate = (packet, opts, throwUnexpectedError) => {
  if (opts.dateStrings) {
    return packet.readAsciiStringLengthEncoded();
  }
  return packet.readDate();
};
const readTimestamp = (packet, opts, throwUnexpectedError) => {
  if (opts.dateStrings) {
    return packet.readAsciiStringLengthEncoded();
  }
  return packet.readDateTime();
};
const readAsciiStringLengthEncoded = (packet, opts, throwUnexpectedError) => packet.readAsciiStringLengthEncoded();
const readBitAsBoolean = (packet, opts, throwUnexpectedError) => {
  const val = packet.readBufferLengthEncoded();
  return val == null ? null : val[0] === 1;
};
const readBufferLengthEncoded = (packet, opts, throwUnexpectedError) => packet.readBufferLengthEncoded();
const readJson = (packet, opts, throwUnexpectedError) => JSON.parse(packet.readStringLengthEncoded());
const readSet = (packet, opts, throwUnexpectedError) => {
  const string = packet.readStringLengthEncoded();
  return string == null ? null : string === '' ? [] : string.split(',');
};
