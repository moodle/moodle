//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Errors = require('../misc/errors');

/**
 * Object to easily parse buffer.
 * Packet are MUTABLE (buffer are changed, to avoid massive packet object creation).
 * Use clone() in case immutability is required
 *
 */
class Packet {
  update(buf, pos, end) {
    this.buf = buf;
    this.pos = pos;
    this.end = end;
    return this;
  }

  skip(n) {
    this.pos += n;
  }

  readGeometry(defaultVal) {
    const geoBuf = this.readBufferLengthEncoded();
    if (geoBuf === null || geoBuf.length === 0) {
      return defaultVal;
    }
    let geoPos = 4;
    return readGeometryObject(false);

    function parseCoordinates(byteOrder) {
      geoPos += 16;
      const x = byteOrder ? geoBuf.readDoubleLE(geoPos - 16) : geoBuf.readDoubleBE(geoPos - 16);
      const y = byteOrder ? geoBuf.readDoubleLE(geoPos - 8) : geoBuf.readDoubleBE(geoPos - 8);
      return [x, y];
    }

    function readGeometryObject(inner) {
      const byteOrder = geoBuf[geoPos++];
      const wkbType = byteOrder ? geoBuf.readInt32LE(geoPos) : geoBuf.readInt32BE(geoPos);
      geoPos += 4;
      switch (wkbType) {
        case 1: //wkbPoint
          const coords = parseCoordinates(byteOrder);

          if (inner) return coords;
          return {
            type: 'Point',
            coordinates: coords
          };

        case 2: //wkbLineString
          const pointNumber = byteOrder ? geoBuf.readInt32LE(geoPos) : geoBuf.readInt32BE(geoPos);
          geoPos += 4;
          let coordinates = [];
          for (let i = 0; i < pointNumber; i++) {
            coordinates.push(parseCoordinates(byteOrder));
          }
          if (inner) return coordinates;
          return {
            type: 'LineString',
            coordinates: coordinates
          };

        case 3: //wkbPolygon
          let polygonCoordinates = [];
          const numRings = byteOrder ? geoBuf.readInt32LE(geoPos) : geoBuf.readInt32BE(geoPos);
          geoPos += 4;
          for (let ring = 0; ring < numRings; ring++) {
            const pointNumber = byteOrder ? geoBuf.readInt32LE(geoPos) : geoBuf.readInt32BE(geoPos);
            geoPos += 4;
            let linesCoordinates = [];
            for (let i = 0; i < pointNumber; i++) {
              linesCoordinates.push(parseCoordinates(byteOrder));
            }
            polygonCoordinates.push(linesCoordinates);
          }

          if (inner) return polygonCoordinates;
          return {
            type: 'Polygon',
            coordinates: polygonCoordinates
          };

        case 4: //wkbMultiPoint
          return {
            type: 'MultiPoint',
            coordinates: parseGeomArray(byteOrder, true)
          };

        case 5: //wkbMultiLineString
          return {
            type: 'MultiLineString',
            coordinates: parseGeomArray(byteOrder, true)
          };
        case 6: //wkbMultiPolygon
          return {
            type: 'MultiPolygon',
            coordinates: parseGeomArray(byteOrder, true)
          };
        case 7: //wkbGeometryCollection
          return {
            type: 'GeometryCollection',
            geometries: parseGeomArray(byteOrder, false)
          };
      }
      return null;
    }

    function parseGeomArray(byteOrder, inner) {
      let coordinates = [];
      const number = byteOrder ? geoBuf.readInt32LE(geoPos) : geoBuf.readInt32BE(geoPos);
      geoPos += 4;
      for (let i = 0; i < number; i++) {
        coordinates.push(readGeometryObject(inner));
      }
      return coordinates;
    }
  }

  peek() {
    return this.buf[this.pos];
  }

  remaining() {
    return this.end - this.pos > 0;
  }

  readInt8() {
    const val = this.buf[this.pos++];
    return val | ((val & (2 ** 7)) * 0x1fffffe);
  }

  readUInt8() {
    return this.buf[this.pos++];
  }

  readInt16() {
    const first = this.buf[this.pos++];
    const last = this.buf[this.pos++];
    const val = first + last * 2 ** 8;
    return val | ((val & (2 ** 15)) * 0x1fffe);
  }

  readUInt16() {
    return this.buf[this.pos++] + this.buf[this.pos++] * 2 ** 8;
  }

  readInt24() {
    const first = this.buf[this.pos];
    const last = this.buf[this.pos + 2];
    const val = first + this.buf[this.pos + 1] * 2 ** 8 + last * 2 ** 16;
    this.pos += 3;
    return val | ((val & (2 ** 23)) * 0x1fe);
  }

  readUInt24() {
    return this.buf[this.pos++] + this.buf[this.pos++] * 2 ** 8 + this.buf[this.pos++] * 2 ** 16;
  }

  readUInt32() {
    return (
      this.buf[this.pos++] +
      this.buf[this.pos++] * 2 ** 8 +
      this.buf[this.pos++] * 2 ** 16 +
      this.buf[this.pos++] * 2 ** 24
    );
  }

  readInt32() {
    return (
      this.buf[this.pos++] +
      this.buf[this.pos++] * 2 ** 8 +
      this.buf[this.pos++] * 2 ** 16 +
      (this.buf[this.pos++] << 24)
    );
  }

  readBigInt64() {
    const val = this.buf.readBigInt64LE(this.pos);
    this.pos += 8;
    return val;
  }

  readBigUInt64() {
    const val = this.buf.readBigUInt64LE(this.pos);
    this.pos += 8;
    return val;
  }

  /**
   * Metadata are length encoded, but cannot have length > 256, so simplified readUnsignedLength
   * @returns {number}
   */
  readMetadataLength() {
    const type = this.buf[this.pos++] & 0xff;
    if (type < 0xfb) return type;
    return this.readUInt16();
  }

  readUnsignedLength() {
    const type = this.buf[this.pos++] & 0xff;
    if (type < 0xfb) return type;
    switch (type) {
      case 0xfb:
        return null;
      case 0xfc:
        return this.readUInt16();
      case 0xfd:
        return this.readUInt24();
      case 0xfe:
        // limitation to BigInt signed value
        return Number(this.readBigInt64());
    }
  }

  readBuffer(len) {
    this.pos += len;
    return this.buf.subarray(this.pos - len, this.pos);
  }

  readBufferRemaining() {
    let b = this.buf.subarray(this.pos, this.end);
    this.pos = this.end;
    return b;
  }

  readBufferLengthEncoded() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    this.pos += len;
    return this.buf.subarray(this.pos - len, this.pos);
  }

  readStringNullEnded() {
    let initialPosition = this.pos;
    let cnt = 0;
    while (this.remaining() > 0 && this.buf[this.pos++] !== 0) {
      cnt++;
    }
    return this.buf.toString(undefined, initialPosition, initialPosition + cnt);
  }

  readSignedLengthBigInt() {
    const type = this.buf[this.pos++];
    switch (type) {
      // null test is not used for now, since only used for reading insertId
      // case 0xfb:
      //   return null;
      case 0xfc:
        return BigInt(this.readUInt16());
      case 0xfd:
        return BigInt(this.readUInt24());
      case 0xfe:
        return this.readBigInt64();
      default:
        return BigInt(type);
    }
  }

  readAsciiStringLengthEncoded() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    this.pos += len;
    return this.buf.toString('ascii', this.pos - len, this.pos);
  }

  readStringLengthEncoded() {
    throw new Error('code is normally superseded by Node encoder or Iconv depending on charset used');
  }

  readBigIntLengthEncoded() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    return this.readBigIntFromLen(len);
  }

  readBigIntFromLen(len) {
    // fast-path: if length encoded is < to 16, value is in safe integer range
    // atoi
    if (len < 16) {
      return BigInt(this._atoi(len));
    }

    // atoll
    let result = 0n;
    let negate = false;
    let begin = this.pos;

    if (len > 0 && this.buf[begin] === 45) {
      //minus sign
      negate = true;
      begin++;
    }
    for (; begin < this.pos + len; begin++) {
      result = result * 10n + BigInt(this.buf[begin] - 48);
    }
    this.pos += len;
    return negate ? -1n * result : result;
  }

  readDecimalLengthEncoded() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    this.pos += len;
    return this.buf.toString('ascii', this.pos - len, this.pos);
  }

  readDate() {
    const len = this.readUnsignedLength();
    if (len === null) return null;

    let res = [];
    let value = 0;
    let initPos = this.pos;
    this.pos += len;
    while (initPos < this.pos) {
      const char = this.buf[initPos++];
      if (char === 45) {
        //minus separator
        res.push(value);
        value = 0;
      } else {
        value = value * 10 + char - 48;
      }
    }
    res.push(value);

    //handle zero-date as null
    if (res[0] === 0 && res[1] === 0 && res[2] === 0) return null;

    return new Date(res[0], res[1] - 1, res[2]);
  }

  readBinaryDate(opts) {
    const len = this.buf[this.pos++];
    let year = 0;
    let month = 0;
    let day = 0;
    if (len > 0) {
      year = this.readInt16();
      if (len > 2) {
        month = this.readUInt8() - 1;
        if (len > 3) {
          day = this.readUInt8();
        }
      }
    }
    if (year === 0 && month === 0 && day === 0) return opts.dateStrings ? '0000-00-00' : null;
    if (opts.dateStrings) {
      return `${appendZero(year, 4)}-${appendZero(month + 1, 2)}-${appendZero(day, 2)}`;
    }
    //handle zero-date as null
    return new Date(year, month, day);
  }

  readDateTime() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    this.pos += len;
    const str = this.buf.toString('ascii', this.pos - len, this.pos);
    if (str.startsWith('0000-00-00 00:00:00')) return null;
    return new Date(str);
  }

  readBinaryDateTime() {
    const len = this.buf[this.pos++];
    let year = 0;
    let month = 0;
    let day = 0;
    let hour = 0;
    let min = 0;
    let sec = 0;
    let microSec = 0;

    if (len > 0) {
      year = this.readInt16();
      if (len > 2) {
        month = this.readUInt8();
        if (len > 3) {
          day = this.readUInt8();
          if (len > 4) {
            hour = this.readUInt8();
            min = this.readUInt8();
            sec = this.readUInt8();
            if (len > 7) {
              microSec = this.readUInt32();
            }
          }
        }
      }
    }

    //handle zero-date as null
    if (year === 0 && month === 0 && day === 0 && hour === 0 && min === 0 && sec === 0 && microSec === 0) return null;
    return new Date(year, month - 1, day, hour, min, sec, microSec / 1000);
  }

  readBinaryDateTimeAsString(scale) {
    const len = this.buf[this.pos++];
    let year = 0;
    let month = 0;
    let day = 0;
    let hour = 0;
    let min = 0;
    let sec = 0;
    let microSec = 0;

    if (len > 0) {
      year = this.readInt16();
      if (len > 2) {
        month = this.readUInt8();
        if (len > 3) {
          day = this.readUInt8();
          if (len > 4) {
            hour = this.readUInt8();
            min = this.readUInt8();
            sec = this.readUInt8();
            if (len > 7) {
              microSec = this.readUInt32();
            }
          }
        }
      }
    }

    //handle zero-date as null
    if (year === 0 && month === 0 && day === 0 && hour === 0 && min === 0 && sec === 0 && microSec === 0)
      return '0000-00-00 00:00:00' + (scale > 0 ? '.000000'.substring(0, scale + 1) : '');

    return (
      appendZero(year, 4) +
      '-' +
      appendZero(month, 2) +
      '-' +
      appendZero(day, 2) +
      ' ' +
      appendZero(hour, 2) +
      ':' +
      appendZero(min, 2) +
      ':' +
      appendZero(sec, 2) +
      (microSec > 0
        ? scale > 0
          ? '.' + appendZero(microSec, 6).substring(0, scale)
          : '.' + appendZero(microSec, 6)
        : scale > 0
        ? '.' + appendZero(microSec, 6).substring(0, scale)
        : '')
    );
  }

  readBinaryTime() {
    const len = this.buf[this.pos++];
    const negate = this.buf[this.pos++] === 1;
    const hour = this.readUInt32() * 24 + this.readUInt8();
    const min = this.readUInt8();
    const sec = this.readUInt8();
    let microSec = 0;
    if (len > 8) {
      microSec = this.readUInt32();
    }
    let val = appendZero(hour, 2) + ':' + appendZero(min, 2) + ':' + appendZero(sec, 2);
    if (microSec > 0) {
      val += '.' + appendZero(microSec, 6);
    }
    if (negate) return '-' + val;
    return val;
  }

  readFloat() {
    const val = this.buf.readFloatLE(this.pos);
    this.pos += 4;
    return val;
  }

  readDouble() {
    const val = this.buf.readDoubleLE(this.pos);
    this.pos += 8;
    return val;
  }

  readIntLengthEncoded() {
    const len = this.buf[this.pos++] & 0xff;
    if (len < 0xfb) return this._atoi(len);
    switch (len) {
      case 0xfb:
        return null;
      case 0xfc:
        return this._atoi(this.readUInt16());
      case 0xfd:
        return this._atoi(this.readUInt24());
      case 0xfe:
        // limitation to BigInt signed value
        return this._atoi(Number(this.readBigInt64()));
    }
  }

  _atoi(len) {
    let result = 0;
    let negate = false;
    let begin = this.pos;

    if (len > 0 && this.buf[begin] === 45) {
      //minus sign
      negate = true;
      begin++;
    }
    for (; begin < this.pos + len; begin++) {
      result = result * 10 + (this.buf[begin] - 48);
    }
    this.pos += len;
    return negate ? -1 * result : result;
  }

  readFloatLengthCoded() {
    const len = this.readUnsignedLength();
    if (len === null) return null;
    this.pos += len;
    return +this.buf.toString('ascii', this.pos - len, this.pos);
  }

  skipLengthCodedNumber() {
    const type = this.buf[this.pos++] & 0xff;
    switch (type) {
      case 251:
        return;
      case 252:
        this.pos += 2 + (0xffff & ((this.buf[this.pos] & 0xff) + ((this.buf[this.pos + 1] & 0xff) << 8)));
        return;
      case 253:
        this.pos +=
          3 +
          (0xffffff &
            ((this.buf[this.pos] & 0xff) +
              ((this.buf[this.pos + 1] & 0xff) << 8) +
              ((this.buf[this.pos + 2] & 0xff) << 16)));
        return;
      case 254:
        this.pos += 8 + Number(this.buf.readBigUInt64LE(this.pos));
        return;
      default:
        this.pos += type;
        return;
    }
  }

  length() {
    return this.end - this.pos;
  }

  subPacketLengthEncoded(len) {}

  /**
   * Parse ERR_Packet : https://mariadb.com/kb/en/library/err_packet/
   *
   * @param info              current connection info
   * @param sql               command sql
   * @param stack             additional stack trace
   * @returns {Error}
   */
  readError(info, sql, stack) {
    this.skip(1);
    let errno = this.readUInt16();
    let sqlState;
    let msg;
    // check '#'
    if (this.peek() === 0x23) {
      // skip '#'
      this.skip(6);
      sqlState = this.buf.toString(undefined, this.pos - 5, this.pos);
      msg = this.readStringNullEnded();
    } else {
      // pre 4.1 format
      sqlState = 'HY000';
      msg = this.buf.toString(undefined, this.pos, this.end);
    }
    let fatal = sqlState.startsWith('08') || sqlState === '70100';
    return Errors.createError(msg, errno, info, sqlState, sql, fatal, stack);
  }
}

const appendZero = (val, len) => {
  let st = val.toString();
  while (st.length < len) {
    st = '0' + st;
  }
  return st;
};

module.exports = Packet;
