//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';
const Parser = require('./parser');
const Parse = require('../misc/parse');
const BinaryEncoder = require('./encoder/binary-encoder');
const PrepareCacheWrapper = require('./class/prepare-cache-wrapper');
const PrepareResult = require('./class/prepare-result-packet');
const ServerStatus = require('../const/server-status');
const Errors = require('../misc/errors');
const ColumnDefinition = require('./column-definition');

/**
 * send a COM_STMT_PREPARE: permits sending a prepare packet
 * see https://mariadb.com/kb/en/com_stmt_prepare/
 */
class Prepare extends Parser {
  constructor(resolve, reject, connOpts, cmdParam, conn) {
    super(resolve, reject, connOpts, cmdParam);
    this.encoder = new BinaryEncoder(this.opts);
    this.binary = true;
    this.conn = conn;
  }

  /**
   * Send COM_STMT_PREPARE
   *
   * @param out   output writer
   * @param opts  connection options
   * @param info  connection information
   */
  start(out, opts, info) {
    // check in cache if enabled
    if (this.conn.prepareCache) {
      let cachedPrepare = this.conn.prepareCache.get(this.sql);
      if (cachedPrepare) {
        this.emit('send_end');
        return this.successEnd(cachedPrepare);
      }
    }
    if (opts.logger.query) opts.logger.query(`PREPARE: ${this.sql}`);
    this.onPacketReceive = this.readPrepareResultPacket;

    if (this.opts.namedPlaceholders) {
      const res = Parse.searchPlaceholder(this.sql);
      this.sql = res.sql;
      this.placeHolderIndex = res.placeHolderIndex;
    }

    out.startPacket(this);
    out.writeInt8(0x16);
    out.writeString(this.sql);
    out.flush();
    this.emit('send_end');
  }

  successPrepare(info, opts) {
    let prepare = new PrepareResult(
      this.statementId,
      this.parameterCount,
      this._columns,
      info.database,
      this.sql,
      this.placeHolderIndex,
      this.conn
    );

    if (this.conn.prepareCache) {
      let cached = new PrepareCacheWrapper(prepare);
      this.conn.prepareCache.set(this.sql, cached);
      return this.successEnd(cached.incrementUse());
    }
    return this.successEnd(prepare);
  }

  /**
   * Read COM_STMT_PREPARE response Packet.
   * see https://mariadb.com/kb/en/library/com_stmt_prepare/#com_stmt_prepare-response
   *
   * @param packet    COM_STMT_PREPARE_OK packet
   * @param opts      connection options
   * @param info      connection information
   * @param out       output writer
   * @returns {*}     null or {Result.readResponsePacket} in case of multi-result-set
   */
  readPrepareResultPacket(packet, out, opts, info) {
    switch (packet.peek()) {
      //*********************************************************************************************************
      //* PREPARE response
      //*********************************************************************************************************
      case 0x00:
        packet.skip(1); //skip header
        this.statementId = packet.readInt32();
        this.columnNo = packet.readUInt16();
        this.parameterCount = packet.readUInt16();
        this._parameterNo = this.parameterCount;
        this._columns = [];
        if (this._parameterNo > 0) return (this.onPacketReceive = this.skipPrepareParameterPacket);
        if (this.columnNo > 0) return (this.onPacketReceive = this.readPrepareColumnsPacket);
        return this.successPrepare(info, opts);

      //*********************************************************************************************************
      //* ERROR response
      //*********************************************************************************************************
      case 0xff:
        const err = packet.readError(info, this.displaySql(), this.stack);
        //force in transaction status, since query will have created a transaction if autocommit is off
        //goal is to avoid unnecessary COMMIT/ROLLBACK.
        info.status |= ServerStatus.STATUS_IN_TRANS;
        this.onPacketReceive = this.readResponsePacket;
        return this.throwError(err, info);

      //*********************************************************************************************************
      //* Unexpected response
      //*********************************************************************************************************
      default:
        info.status |= ServerStatus.STATUS_IN_TRANS;
        this.onPacketReceive = this.readResponsePacket;
        return this.throwError(Errors.ER_UNEXPECTED_PACKET, info);
    }
  }

  readPrepareColumnsPacket(packet, out, opts, info) {
    this.columnNo--;
    this._columns.push(new ColumnDefinition(packet, info, opts.rowsAsArray));
    if (this.columnNo === 0) {
      if (info.eofDeprecated) {
        return this.successPrepare(info, opts);
      }
      return (this.onPacketReceive = this.skipEofPacket);
    }
  }

  skipEofPacket(packet, out, opts, info) {
    if (this.columnNo > 0) return (this.onPacketReceive = this.readPrepareColumnsPacket);
    return this.successPrepare(info, opts);
  }

  skipPrepareParameterPacket(packet, out, opts, info) {
    this._parameterNo--;
    if (this._parameterNo === 0) {
      if (info.eofDeprecated) {
        if (this.columnNo > 0) return (this.onPacketReceive = this.readPrepareColumnsPacket);
        return this.successPrepare(info, opts);
      }
      return (this.onPacketReceive = this.skipEofPacket);
    }
  }

  /**
   * Display current SQL with parameters (truncated if too big)
   *
   * @returns {string}
   */
  displaySql() {
    if (this.opts) {
      if (this.sql.length > this.opts.debugLen) {
        return this.sql.substring(0, this.opts.debugLen) + '...';
      }
    }
    return this.sql;
  }
}

module.exports = Prepare;
