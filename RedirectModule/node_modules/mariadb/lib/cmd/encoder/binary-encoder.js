//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

class BinaryEncoder {
  /**
   * Write (and escape) current parameter value to output writer
   *
   * @param out     output writer
   * @param value   current parameter
   * @param opts    connection options
   * @param info    connection information
   */
  writeParam(out, value, opts, info) {
    // GEOJSON are not checked, because change to null/Buffer on parameter validation
    switch (typeof value) {
      case 'boolean':
        out.writeInt8(value ? 0x01 : 0x00);
        break;
      case 'bigint':
        if (value >= 2n ** 63n) {
          out.writeLengthEncodedString(value.toString());
        } else {
          out.writeBigInt(value);
        }
        break;

      case 'number':
        // additional verification, to permit query without type,
        // like 'SELECT ?' returning same type of value
        if (Number.isSafeInteger(value) && value >= -2147483648 && value < 2147483647) {
          out.writeInt32(value);
          break;
        }
        out.writeDouble(value);
        break;
      case 'string':
        out.writeLengthEncodedString(value);
        break;
      case 'object':
        if (value instanceof Date) {
          out.writeBinaryDate(value);
        } else if (Buffer.isBuffer(value)) {
          out.writeLengthEncodedBuffer(value);
        } else if (typeof value.toSqlString === 'function') {
          out.writeLengthEncodedString(String(value.toSqlString()));
        } else {
          out.writeLengthEncodedString(JSON.stringify(value));
        }
        break;
      default:
        out.writeLengthEncodedBuffer(value);
    }
  }

  static getBufferFromGeometryValue(value, headerType) {
    let geoBuff;
    let pos;
    let type;
    if (!headerType) {
      switch (value.type) {
        case 'Point':
          geoBuff = Buffer.allocUnsafe(21);
          geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
          geoBuff.writeInt32LE(1, 1); //wkbPoint
          if (
            value.coordinates &&
            Array.isArray(value.coordinates) &&
            value.coordinates.length >= 2 &&
            !isNaN(value.coordinates[0]) &&
            !isNaN(value.coordinates[1])
          ) {
            geoBuff.writeDoubleLE(value.coordinates[0], 5); //X
            geoBuff.writeDoubleLE(value.coordinates[1], 13); //Y
            return geoBuff;
          } else {
            return null;
          }

        case 'LineString':
          if (value.coordinates && Array.isArray(value.coordinates)) {
            const pointNumber = value.coordinates.length;
            geoBuff = Buffer.allocUnsafe(9 + 16 * pointNumber);
            geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
            geoBuff.writeInt32LE(2, 1); //wkbLineString
            geoBuff.writeInt32LE(pointNumber, 5);
            for (let i = 0; i < pointNumber; i++) {
              if (
                value.coordinates[i] &&
                Array.isArray(value.coordinates[i]) &&
                value.coordinates[i].length >= 2 &&
                !isNaN(value.coordinates[i][0]) &&
                !isNaN(value.coordinates[i][1])
              ) {
                geoBuff.writeDoubleLE(value.coordinates[i][0], 9 + 16 * i); //X
                geoBuff.writeDoubleLE(value.coordinates[i][1], 17 + 16 * i); //Y
              } else {
                return null;
              }
            }
            return geoBuff;
          } else {
            return null;
          }

        case 'Polygon':
          if (value.coordinates && Array.isArray(value.coordinates)) {
            const numRings = value.coordinates.length;
            let size = 0;
            for (let i = 0; i < numRings; i++) {
              size += 4 + 16 * value.coordinates[i].length;
            }
            geoBuff = Buffer.allocUnsafe(9 + size);
            geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
            geoBuff.writeInt32LE(3, 1); //wkbPolygon
            geoBuff.writeInt32LE(numRings, 5);
            pos = 9;
            for (let i = 0; i < numRings; i++) {
              const lineString = value.coordinates[i];
              if (lineString && Array.isArray(lineString)) {
                geoBuff.writeInt32LE(lineString.length, pos);
                pos += 4;
                for (let j = 0; j < lineString.length; j++) {
                  if (
                    lineString[j] &&
                    Array.isArray(lineString[j]) &&
                    lineString[j].length >= 2 &&
                    !isNaN(lineString[j][0]) &&
                    !isNaN(lineString[j][1])
                  ) {
                    geoBuff.writeDoubleLE(lineString[j][0], pos); //X
                    geoBuff.writeDoubleLE(lineString[j][1], pos + 8); //Y
                    pos += 16;
                  } else {
                    return null;
                  }
                }
              }
            }
            return geoBuff;
          } else {
            return null;
          }

        case 'MultiPoint':
          type = 'MultiPoint';
          geoBuff = Buffer.allocUnsafe(9);
          geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
          geoBuff.writeInt32LE(4, 1); //wkbMultiPoint
          break;

        case 'MultiLineString':
          type = 'MultiLineString';
          geoBuff = Buffer.allocUnsafe(9);
          geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
          geoBuff.writeInt32LE(5, 1); //wkbMultiLineString
          break;

        case 'MultiPolygon':
          type = 'MultiPolygon';
          geoBuff = Buffer.allocUnsafe(9);
          geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
          geoBuff.writeInt32LE(6, 1); //wkbMultiPolygon
          break;

        case 'GeometryCollection':
          geoBuff = Buffer.allocUnsafe(9);
          geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
          geoBuff.writeInt32LE(7, 1); //wkbGeometryCollection

          if (value.geometries && Array.isArray(value.geometries)) {
            const coordinateLength = value.geometries.length;
            const subArrays = [geoBuff];
            for (let i = 0; i < coordinateLength; i++) {
              const tmpBuf = this.getBufferFromGeometryValue(value.geometries[i]);
              if (tmpBuf == null) break;
              subArrays.push(tmpBuf);
            }
            geoBuff.writeInt32LE(subArrays.length - 1, 5);
            return Buffer.concat(subArrays);
          } else {
            geoBuff.writeInt32LE(0, 5);
            return geoBuff;
          }
        default:
          return null;
      }
      if (value.coordinates && Array.isArray(value.coordinates)) {
        const coordinateLength = value.coordinates.length;
        const subArrays = [geoBuff];
        for (let i = 0; i < coordinateLength; i++) {
          const tmpBuf = this.getBufferFromGeometryValue(value.coordinates[i], type);
          if (tmpBuf == null) break;
          subArrays.push(tmpBuf);
        }
        geoBuff.writeInt32LE(subArrays.length - 1, 5);
        return Buffer.concat(subArrays);
      } else {
        geoBuff.writeInt32LE(0, 5);
        return geoBuff;
      }
    } else {
      switch (headerType) {
        case 'MultiPoint':
          if (value && Array.isArray(value) && value.length >= 2 && !isNaN(value[0]) && !isNaN(value[1])) {
            geoBuff = Buffer.allocUnsafe(21);
            geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
            geoBuff.writeInt32LE(1, 1); //wkbPoint
            geoBuff.writeDoubleLE(value[0], 5); //X
            geoBuff.writeDoubleLE(value[1], 13); //Y
            return geoBuff;
          }
          return null;

        case 'MultiLineString':
          if (value && Array.isArray(value)) {
            const pointNumber = value.length;
            geoBuff = Buffer.allocUnsafe(9 + 16 * pointNumber);
            geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
            geoBuff.writeInt32LE(2, 1); //wkbLineString
            geoBuff.writeInt32LE(pointNumber, 5);
            for (let i = 0; i < pointNumber; i++) {
              if (
                value[i] &&
                Array.isArray(value[i]) &&
                value[i].length >= 2 &&
                !isNaN(value[i][0]) &&
                !isNaN(value[i][1])
              ) {
                geoBuff.writeDoubleLE(value[i][0], 9 + 16 * i); //X
                geoBuff.writeDoubleLE(value[i][1], 17 + 16 * i); //Y
              } else {
                return null;
              }
            }
            return geoBuff;
          }
          return null;

        case 'MultiPolygon':
          if (value && Array.isArray(value)) {
            const numRings = value.length;
            let size = 0;
            for (let i = 0; i < numRings; i++) {
              size += 4 + 16 * value[i].length;
            }
            geoBuff = Buffer.allocUnsafe(9 + size);
            geoBuff.writeInt8(0x01, 0); //LITTLE ENDIAN
            geoBuff.writeInt32LE(3, 1); //wkbPolygon
            geoBuff.writeInt32LE(numRings, 5);
            pos = 9;
            for (let i = 0; i < numRings; i++) {
              const lineString = value[i];
              if (lineString && Array.isArray(lineString)) {
                geoBuff.writeInt32LE(lineString.length, pos);
                pos += 4;
                for (let j = 0; j < lineString.length; j++) {
                  if (
                    lineString[j] &&
                    Array.isArray(lineString[j]) &&
                    lineString[j].length >= 2 &&
                    !isNaN(lineString[j][0]) &&
                    !isNaN(lineString[j][1])
                  ) {
                    geoBuff.writeDoubleLE(lineString[j][0], pos); //X
                    geoBuff.writeDoubleLE(lineString[j][1], pos + 8); //Y
                    pos += 16;
                  } else {
                    return null;
                  }
                }
              }
            }
            return geoBuff;
          }
          return null;
      }
      return null;
    }
  }
}

module.exports = BinaryEncoder;
