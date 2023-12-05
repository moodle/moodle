//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Command = require('./command');

/**
 * Close prepared statement
 * see https://mariadb.com/kb/en/3-binary-protocol-prepared-statements-com_stmt_close/
 */
class ClosePrepare extends Command {
  constructor(cmdParam, resolve, reject, prepare) {
    super(cmdParam, resolve, reject);
    this.prepare = prepare;
  }

  start(out, opts, info) {
    if (opts.logger.query) opts.logger.query(`CLOSE PREPARE: (${this.prepare.id}) ${this.prepare.query}`);
    const closeCmd = new Uint8Array([
      5,
      0,
      0,
      0,
      0x19,
      this.prepare.id,
      this.prepare.id >> 8,
      this.prepare.id >> 16,
      this.prepare.id >> 24
    ]);
    out.fastFlush(this, closeCmd);
    this.onPacketReceive = null;
    this.emit('send_end');
    this.emit('end');
  }
}

module.exports = ClosePrepare;
