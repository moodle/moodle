//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';
const Capabilities = require('../../const/capabilities');

/**
 * Send SSL Request packet.
 * see : https://mariadb.com/kb/en/library/1-connecting-connecting/#sslrequest-packet
 *
 * @param cmd     current command
 * @param out     output writer
 * @param info    client information
 * @param opts    connection options
 */
module.exports.send = function sendSSLRequest(cmd, out, info, opts) {
  out.startPacket(cmd);
  out.writeInt32(Number(info.clientCapabilities & BigInt(0xffffffff)));
  out.writeInt32(1024 * 1024 * 1024); // max packet size
  out.writeInt8(opts.collation ? opts.collation.index : info.collation.index);
  for (let i = 0; i < 19; i++) {
    out.writeInt8(0);
  }

  if (info.serverCapabilities & Capabilities.MYSQL) {
    out.writeInt32(0);
  } else {
    out.writeInt32(Number(info.clientCapabilities >> 32n));
  }

  out.flushPacket();
};
