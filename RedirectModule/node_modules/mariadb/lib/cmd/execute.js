//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Parser = require('./parser');
const Errors = require('../misc/errors');
const BinaryEncoder = require('./encoder/binary-encoder');
const FieldType = require('../const/field-type');

/**
 * Protocol COM_STMT_EXECUTE
 * see : https://mariadb.com/kb/en/com_stmt_execute/
 */
class Execute extends Parser {
  constructor(resolve, reject, connOpts, cmdParam, prepare) {
    super(resolve, reject, connOpts, cmdParam);
    this.encoder = new BinaryEncoder(this.opts);
    this.binary = true;
    this.prepare = prepare;
    this.canSkipMeta = true;
  }

  /**
   * Send COM_QUERY
   *
   * @param out   output writer
   * @param opts  connection options
   * @param info  connection information
   */
  start(out, opts, info) {
    this.onPacketReceive = this.readResponsePacket;
    this.values = [];

    if (this.opts.namedPlaceholders && this.prepare._placeHolderIndex) {
      // using named placeholders, so change values accordingly
      this.values = new Array(this.prepare.parameterCount);
      if (this.initialValues) {
        for (let i = 0; i < this.prepare._placeHolderIndex.length; i++) {
          this.values[i] = this.initialValues[this.prepare._placeHolderIndex[i]];
        }
      }
    } else {
      if (this.initialValues)
        this.values = Array.isArray(this.initialValues) ? this.initialValues : [this.initialValues];
    }

    if (!this.validateParameters(info)) return;

    // send long data using COM_STMT_SEND_LONG_DATA
    this.longDataStep = false; // send long data
    for (let i = 0; i < this.prepare.parameterCount; i++) {
      const value = this.values[i];
      if (
        value != null &&
        ((typeof value === 'object' && typeof value.pipe === 'function' && typeof value.read === 'function') ||
          Buffer.isBuffer(value))
      ) {
        if (opts.logger.query)
          opts.logger.query(
            `EXECUTE: (${this.prepare.id}) sql: ${opts.logger.logParam ? this.displaySql() : this.sql}`
          );
        if (!this.longDataStep) {
          this.longDataStep = true;
          this.registerStreamSendEvent(out, info);
          this.currentParam = i;
        }
        this.sendComStmtLongData(out, info, value);
        return;
      }
    }

    if (!this.longDataStep) {
      // no stream parameter, so can send directly
      if (opts.logger.query)
        opts.logger.query(`EXECUTE: (${this.prepare.id}) sql: ${opts.logger.logParam ? this.displaySql() : this.sql}`);
      this.sendComStmtExecute(out, info);
    }
  }

  /**
   * Validate that parameters exists and are defined.
   *
   * @param info        connection info
   * @returns {boolean} return false if any error occur.
   */
  validateParameters(info) {
    //validate parameter size.
    if (this.prepare.parameterCount > this.values.length) {
      this.sendCancelled(
        `Parameter at position ${this.values.length} is not set\\nsql: ${this.displaySql()}`,
        Errors.ER_MISSING_PARAMETER,
        info
      );
      return false;
    }

    //validate parameter is defined.
    for (let i = 0; i < this.prepare.parameterCount; i++) {
      if (this.opts.namedPlaceholders && this.prepare._placeHolderIndex && this.values[i] === undefined) {
        let errMsg = `Parameter named ${this.prepare._placeHolderIndex[i]} is not set`;
        if (this.prepare._placeHolderIndex.length < this.prepare.parameterCount) {
          errMsg = `Command expect ${this.prepare.parameterCount} parameters, but found only ${this.prepare._placeHolderIndex.length} named parameters. You probably use question mark in place of named parameters`;
        }
        this.sendCancelled(errMsg, Errors.ER_PARAMETER_UNDEFINED, info);
        return false;
      }

      // special check for GEOJSON that can be null even if object is not
      if (
        this.values[i] &&
        this.values[i].type != null &&
        [
          'Point',
          'LineString',
          'Polygon',
          'MultiPoint',
          'MultiLineString',
          'MultiPolygon',
          'GeometryCollection'
        ].includes(this.values[i].type)
      ) {
        const geoBuff = BinaryEncoder.getBufferFromGeometryValue(this.values[i]);
        if (geoBuff == null) {
          this.values[i] = null;
        } else {
          this.values[i] = Buffer.concat([
            Buffer.from([0, 0, 0, 0]), // SRID
            geoBuff // WKB
          ]);
        }
      }
    }
    return true;
  }

  sendComStmtLongData(out, info, value) {
    out.startPacket(this);
    out.writeInt8(0x18);
    out.writeInt32(this.prepare.id);
    out.writeInt16(this.currentParam);

    if (Buffer.isBuffer(value)) {
      out.writeBuffer(value, 0, value.length);
      out.flush();
      this.currentParam++;
      return this.paramWritten();
    }
    this.sending = true;

    // streaming
    value.on('data', function (chunk) {
      out.writeBuffer(chunk, 0, chunk.length);
    });

    value.on(
      'end',
      function () {
        out.flush();
        this.currentParam++;
        this.paramWritten();
      }.bind(this)
    );
  }

  /**
   * Send a COM_STMT_EXECUTE
   * @param out
   * @param info
   */
  sendComStmtExecute(out, info) {
    const parameterCount = this.prepare.parameterCount;

    let nullCount = Math.floor((parameterCount + 7) / 8);
    const nullBitsBuffer = Buffer.alloc(nullCount);
    for (let i = 0; i < parameterCount; i++) {
      if (this.values[i] == null) {
        nullBitsBuffer[Math.floor(i / 8)] |= 1 << i % 8;
      }
    }

    out.startPacket(this);
    out.writeInt8(0x17); // COM_STMT_EXECUTE
    out.writeInt32(this.prepare.id); // Statement id
    out.writeInt8(0); // no cursor flag
    out.writeInt32(1); // 1 command
    out.writeBuffer(nullBitsBuffer, 0, nullCount); // null buffer
    out.writeInt8(1); // always send type to server

    // send types
    for (let i = 0; i < parameterCount; i++) {
      const val = this.values[i];
      if (val != null) {
        switch (typeof val) {
          case 'boolean':
            out.writeInt8(FieldType.TINY);
            break;
          case 'bigint':
            if (val >= 2n ** 63n) {
              out.writeInt8(FieldType.NEWDECIMAL);
            } else {
              out.writeInt8(FieldType.BIGINT);
            }
            break;
          case 'number':
            // additional verification, to permit query without type,
            // like 'SELECT ?' returning same type of value
            if (Number.isSafeInteger(val) && val >= -2147483648 && val < 2147483647) {
              out.writeInt8(FieldType.INT);
              break;
            }
            out.writeInt8(FieldType.DOUBLE);
            break;
          case 'string':
            out.writeInt8(FieldType.VAR_STRING);
            break;
          case 'object':
            if (val instanceof Date) {
              out.writeInt8(FieldType.DATETIME);
            } else if (Buffer.isBuffer(val)) {
              out.writeInt8(FieldType.BLOB);
            } else if (typeof val.toSqlString === 'function') {
              out.writeInt8(FieldType.VAR_STRING);
            } else if (typeof val.pipe === 'function' && typeof val.read === 'function') {
              out.writeInt8(FieldType.BLOB);
            } else {
              out.writeInt8(FieldType.VAR_STRING);
            }
            break;
          default:
            out.writeInt8(FieldType.BLOB);
            break;
        }
      } else {
        out.writeInt8(FieldType.VAR_STRING);
      }
      out.writeInt8(0);
    }

    //********************************************
    // send not null / not streaming values
    //********************************************
    for (let i = 0; i < parameterCount; i++) {
      const value = this.values[i];
      if (
        value != null &&
        !(typeof value === 'object' && typeof value.pipe === 'function' && typeof value.read === 'function') &&
        !Buffer.isBuffer(value)
      ) {
        this.encoder.writeParam(out, value, this.opts, info);
      }
    }
    out.flush();
    this.sending = false;
    this.emit('send_end');
  }

  /**
   * Define params events.
   * Each parameter indicate that he is written to socket,
   * emitting event so next stream parameter can be written.
   */
  registerStreamSendEvent(out, info) {
    // note : Implementation use recursive calls, but stack won't never get near v8 max call stack size
    //since event launched for stream parameter only
    this.paramWritten = function () {
      if (this.longDataStep) {
        for (; this.currentParam < this.prepare.parameterCount; this.currentParam++) {
          const value = this.values[this.currentParam];
          if (
            (value != null &&
              typeof value === 'object' &&
              typeof value.pipe === 'function' &&
              typeof value.read === 'function') ||
            Buffer.isBuffer(value)
          ) {
            this.sendComStmtLongData(out, info, value);
            return;
          }
        }
        this.longDataStep = false; // all streams have been send
      }

      if (!this.longDataStep) {
        this.sendComStmtExecute(out, info);
      }
    }.bind(this);
  }
}

module.exports = Execute;
