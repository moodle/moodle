//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';
const CommandParameter = require('../../command-parameter');
const Errors = require('../../misc/errors');
const ExecuteStream = require('../execute-stream');

/**
 * Prepare result
 * see https://mariadb.com/kb/en/com_stmt_prepare/#com_stmt_prepare_ok
 */
class PrepareResultPacket {
  #conn;
  constructor(statementId, parameterCount, columns, database, sql, placeHolderIndex, conn) {
    this.id = statementId;
    this.parameterCount = parameterCount;
    this.columns = columns;
    this.database = database;
    this.query = sql;
    this.closed = false;
    this._placeHolderIndex = placeHolderIndex;
    this.#conn = conn;
  }

  get conn() {
    return this.#conn;
  }

  execute(values, opts, cb, stack) {
    let _opts = opts,
      _cb = cb;

    if (typeof _opts === 'function') {
      _cb = _opts;
      _opts = undefined;
    }

    if (this.isClose()) {
      const error = Errors.createError(
        `Execute fails, prepare command as already been closed`,
        Errors.ER_PREPARE_CLOSED,
        null,
        '22000',
        this.query
      );

      if (!_cb) {
        return Promise.reject(error);
      } else {
        _cb(error);
        return;
      }
    }

    const cmdParam = new CommandParameter(this.query, values, _opts, _cb);
    if (stack) cmdParam.stack = stack;
    const conn = this.conn;
    const promise = new Promise((resolve, reject) => conn.executePromise.call(conn, cmdParam, this, resolve, reject));
    if (!_cb) {
      return promise;
    } else {
      promise
        .then((res) => {
          if (_cb) _cb(null, res, null);
        })
        .catch(_cb || function (err) {});
    }
  }

  executeStream(values, opts, cb, stack) {
    let _opts = opts,
      _cb = cb;

    if (typeof _opts === 'function') {
      _cb = _opts;
      _opts = undefined;
    }

    if (this.isClose()) {
      const error = Errors.createError(
        `Execute fails, prepare command as already been closed`,
        Errors.ER_PREPARE_CLOSED,
        null,
        '22000',
        this.query
      );

      if (!_cb) {
        throw error;
      } else {
        _cb(error);
        return;
      }
    }

    const cmdParam = new CommandParameter(this.query, values, _opts, _cb);
    if (stack) cmdParam.stack = stack;

    const cmd = new ExecuteStream(cmdParam, this.conn.opts, this, this.conn.socket);
    if (this.conn.opts.logger.error) cmd.on('error', this.conn.opts.logger.error);
    this.conn.addCommand(cmd);
    return cmd.inStream;
  }

  isClose() {
    return this.closed;
  }

  close() {
    if (!this.closed) {
      this.closed = true;
      this.#conn.emit('close_prepare', this);
    }
  }
  toString() {
    return 'Prepare{closed:' + this.closed + '}';
  }
}

module.exports = PrepareResultPacket;
