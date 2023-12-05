//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Collations = require('../const/collations.js');
const FieldType = require('../const/field-type');
const FieldDetails = require('../const/field-detail');
const Capabilities = require('../const/capabilities');

// noinspection JSBitwiseOperatorUsage
/**
 * Column definition
 * see https://mariadb.com/kb/en/library/resultset/#column-definition-packet
 */
class ColumnDef {
  #stringParser;
  constructor(packet, info, skipName) {
    this.#stringParser = skipName ? new StringParser(packet) : new StringParserWithName(packet);
    if (info.clientCapabilities & Capabilities.MARIADB_CLIENT_EXTENDED_TYPE_INFO) {
      const len = packet.readUnsignedLength();
      if (len > 0) {
        const subPacket = packet.subPacketLengthEncoded(len);
        while (subPacket.remaining()) {
          switch (subPacket.readUInt8()) {
            case 0:
              this.dataTypeName = subPacket.readAsciiStringLengthEncoded();
              break;

            case 1:
              this.dataTypeFormat = subPacket.readAsciiStringLengthEncoded();
              break;

            default:
              subPacket.skip(subPacket.readUnsignedLength());
              break;
          }
        }
      }
    }

    packet.skip(1); // length of fixed fields
    this.collation = Collations.fromIndex(packet.readUInt16());
    this.columnLength = packet.readUInt32();
    this.columnType = packet.readUInt8();
    this.flags = packet.readUInt16();
    this.scale = packet.readUInt8();
    this.type = FieldType.TYPES[this.columnType];
  }

  __getDefaultGeomVal() {
    if (this.dataTypeName) {
      switch (this.dataTypeName) {
        case 'point':
          return { type: 'Point' };
        case 'linestring':
          return { type: 'LineString' };
        case 'polygon':
          return { type: 'Polygon' };
        case 'multipoint':
          return { type: 'MultiPoint' };
        case 'multilinestring':
          return { type: 'MultiLineString' };
        case 'multipolygon':
          return { type: 'MultiPolygon' };
        default:
          return { type: this.dataTypeName };
      }
    }
    return null;
  }

  db() {
    return this.#stringParser.db();
  }

  schema() {
    return this.#stringParser.schema();
  }

  table() {
    return this.#stringParser.table();
  }

  orgTable() {
    return this.#stringParser.orgTable();
  }

  name() {
    return this.#stringParser.name();
  }

  orgName() {
    return this.#stringParser.orgName();
  }

  signed() {
    return (this.flags & FieldDetails.UNSIGNED) === 0;
  }

  isSet() {
    return (this.flags & FieldDetails.SET) !== 0;
  }
}

/**
 * String parser.
 * This object permits to avoid listing all private information to metadata object.
 */

class BaseStringParser {
  constructor(readFct, saveBuf) {
    this.buf = saveBuf;
    this.readString = readFct;
  }

  _readIdentifier(skip) {
    let pos = 0;
    while (skip-- > 0) {
      const type = this.buf[pos++] & 0xff;
      pos += type < 0xfb ? type : 2 + this.buf[pos] + this.buf[pos + 1] * 2 ** 8;
    }

    let len;
    const type = this.buf[pos++] & 0xff;
    len = type < 0xfb ? type : this.buf[pos++] + this.buf[pos++] * 2 ** 8;

    return this.readString(this.buf, pos, len);
  }

  name() {
    return this._readIdentifier(3);
  }

  db() {
    return this._readIdentifier(0);
  }

  schema() {
    return this.db();
  }

  table() {
    return this._readIdentifier(1);
  }

  orgTable() {
    return this._readIdentifier(2);
  }

  orgName() {
    return this._readIdentifier(4);
  }
}

class StringParser extends BaseStringParser {
  constructor(packet) {
    packet.skip(4); // skip 'def'
    const initPos = packet.pos;
    packet.skip(packet.readMetadataLength()); //schema
    packet.skip(packet.readMetadataLength()); //table alias
    packet.skip(packet.readMetadataLength()); //table
    packet.skip(packet.readMetadataLength()); //column alias
    packet.skip(packet.readMetadataLength()); //column

    const len = packet.pos - initPos;
    const saveBuf = Buffer.allocUnsafe(packet.pos - initPos);
    for (let i = 0; i < len; i++) saveBuf[i] = packet.buf[initPos + i];

    super(packet.readString.bind(packet), saveBuf);
  }
}

/**
 * String parser.
 * This object permits to avoid listing all private information to metadata object.
 */
class StringParserWithName extends BaseStringParser {
  colName;
  constructor(packet) {
    packet.skip(4); // skip 'def'
    const initPos = packet.pos;
    packet.skip(packet.readMetadataLength()); //schema
    packet.skip(packet.readMetadataLength()); //table alias
    packet.skip(packet.readMetadataLength()); //table
    const colName = packet.readStringLengthEncoded(); //column alias
    packet.skip(packet.readMetadataLength()); //column

    const len = packet.pos - initPos;
    const saveBuf = Buffer.allocUnsafe(packet.pos - initPos);
    for (let i = 0; i < len; i++) saveBuf[i] = packet.buf[initPos + i];

    super(packet.readString.bind(packet), saveBuf);
    this.colName = colName;
  }

  name() {
    return this.colName;
  }
}

module.exports = ColumnDef;
