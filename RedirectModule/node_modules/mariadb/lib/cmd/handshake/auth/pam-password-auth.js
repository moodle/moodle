//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

const PluginAuth = require('./plugin-auth');

/**
 * Use PAM authentication
 */
class PamPasswordAuth extends PluginAuth {
  constructor(packSeq, compressPackSeq, pluginData, cmdParam, reject, multiAuthResolver) {
    super(cmdParam, multiAuthResolver, reject);
    this.pluginData = pluginData;
    this.sequenceNo = packSeq;
    this.compressSequenceNo = compressPackSeq;
    this.counter = 0;
    this.multiAuthResolver = multiAuthResolver;
  }

  start(out, opts, info) {
    this.exchange(this.pluginData, out, opts, info);
    this.onPacketReceive = this.response;
  }

  exchange(buffer, out, opts, info) {
    //conversation is :
    // - first byte is information tell if question is a password (4) or clear text (2).
    // - other bytes are the question to user

    out.startPacket(this);

    let pwd;
    if (Array.isArray(opts.password)) {
      pwd = opts.password[this.counter];
      this.counter++;
    } else {
      pwd = opts.password;
    }

    if (pwd) out.writeString(pwd);
    out.writeInt8(0);
    out.flushPacket();
  }

  response(packet, out, opts, info) {
    const marker = packet.peek();
    switch (marker) {
      //*********************************************************************************************************
      //* OK_Packet and Err_Packet ending packet
      //*********************************************************************************************************
      case 0x00:
      case 0xff:
        this.emit('send_end');
        return this.multiAuthResolver(packet, out, opts, info);

      default:
        let promptData = packet.readBuffer();
        this.exchange(promptData, out, opts, info);
        this.onPacketReceive = this.response;
    }
  }
}

module.exports = PamPasswordAuth;
