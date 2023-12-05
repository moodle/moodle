//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const PacketNodeEncoded = require('./packet-node-encoded');
const PacketIconvEncoded = require('./packet-node-iconv');
const Collations = require('../const/collations');
const Utils = require('../misc/utils');

/**
 * MySQL packet parser
 * see : https://mariadb.com/kb/en/library/0-packet/
 */
class PacketInputStream {
  constructor(unexpectedPacket, receiveQueue, out, opts, info) {
    this.unexpectedPacket = unexpectedPacket;
    this.opts = opts;
    this.receiveQueue = receiveQueue;
    this.info = info;
    this.out = out;

    //in case packet is not complete
    this.header = Buffer.allocUnsafe(4);
    this.headerLen = 0;
    this.packetLen = null;
    this.remainingLen = null;

    this.parts = null;
    this.partsTotalLen = 0;
    this.changeEncoding(this.opts.collation ? this.opts.collation : Collations.fromIndex(224));
    this.changeDebug(this.opts.debug);
    this.opts.on('collation', this.changeEncoding.bind(this));
    this.opts.on('debug', this.changeDebug.bind(this));
  }

  changeEncoding(collation) {
    this.encoding = collation.charset;
    this.packet = Buffer.isEncoding(this.encoding)
      ? new PacketNodeEncoded(this.encoding)
      : new PacketIconvEncoded(this.encoding);
  }

  changeDebug(debug) {
    this.receivePacket = debug ? this.receivePacketDebug : this.receivePacketBasic;
  }

  receivePacketDebug(packet) {
    let cmd = this.currentCmd();
    this.header[0] = this.packetLen & 0xff;
    this.header[1] = (this.packetLen >> 8) & 0xff;
    this.header[2] = (this.packetLen >> 16) & 0xff;
    this.header[3] = this.sequenceNo;
    if (packet) {
      this.opts.logger.network(
        `<== conn:${this.info.threadId ? this.info.threadId : -1} ${
          cmd
            ? cmd.onPacketReceive
              ? cmd.constructor.name + '.' + cmd.onPacketReceive.name
              : cmd.constructor.name
            : 'no command'
        } (${packet.pos},${packet.end})\n${Utils.log(this.opts, packet.buf, packet.pos, packet.end, this.header)}`
      );
    }

    if (!cmd) {
      this.unexpectedPacket(packet);
      return;
    }

    cmd.sequenceNo = this.sequenceNo;
    cmd.onPacketReceive(packet, this.out, this.opts, this.info);
    if (!cmd.onPacketReceive) {
      this.receiveQueue.shift();
    }
  }

  receivePacketBasic(packet) {
    let cmd = this.currentCmd();
    if (!cmd) {
      this.unexpectedPacket(packet);
      return;
    }
    cmd.sequenceNo = this.sequenceNo;
    cmd.onPacketReceive(packet, this.out, this.opts, this.info);
    if (!cmd.onPacketReceive) this.receiveQueue.shift();
  }

  resetHeader() {
    this.remainingLen = null;
    this.headerLen = 0;
  }

  currentCmd() {
    let cmd;
    while ((cmd = this.receiveQueue.peek())) {
      if (cmd.onPacketReceive) return cmd;
      this.receiveQueue.shift();
    }
    return null;
  }

  onData(chunk) {
    let pos = 0;
    let length;
    const chunkLen = chunk.length;

    do {
      //read header
      if (this.remainingLen) {
        length = this.remainingLen;
      } else if (this.headerLen === 0 && chunkLen - pos >= 4) {
        this.packetLen = chunk[pos] + (chunk[pos + 1] << 8) + (chunk[pos + 2] << 16);
        this.sequenceNo = chunk[pos + 3];
        pos += 4;
        length = this.packetLen;
      } else {
        length = null;
        while (chunkLen - pos > 0) {
          this.header[this.headerLen++] = chunk[pos++];
          if (this.headerLen === 4) {
            this.packetLen = this.header[0] + (this.header[1] << 8) + (this.header[2] << 16);
            this.sequenceNo = this.header[3];
            length = this.packetLen;
            break;
          }
        }
      }

      if (length) {
        if (chunkLen - pos >= length) {
          pos += length;
          if (!this.parts) {
            if (this.packetLen < 0xffffff) {
              this.receivePacket(this.packet.update(chunk, pos - length, pos));
              // fast path, knowing there is no parts
              // loop can be simplified until reaching the end of the packet.
              while (pos + 4 < chunkLen) {
                this.packetLen = chunk[pos] + (chunk[pos + 1] << 8) + (chunk[pos + 2] << 16);
                this.sequenceNo = chunk[pos + 3];
                pos += 4;
                if (chunkLen - pos >= this.packetLen) {
                  pos += this.packetLen;
                  if (this.packetLen < 0xffffff) {
                    this.receivePacket(this.packet.update(chunk, pos - this.packetLen, pos));
                  } else {
                    this.parts = [chunk.subarray(pos - this.packetLen, pos)];
                    this.partsTotalLen = this.packetLen;
                    break;
                  }
                } else {
                  const buf = chunk.subarray(pos, chunkLen);
                  if (!this.parts) {
                    this.parts = [buf];
                    this.partsTotalLen = chunkLen - pos;
                  } else {
                    this.parts.push(buf);
                    this.partsTotalLen += chunkLen - pos;
                  }
                  this.remainingLen = this.packetLen - (chunkLen - pos);
                  return;
                }
              }
            } else {
              this.parts = [chunk.subarray(pos - length, pos)];
              this.partsTotalLen = length;
            }
          } else {
            this.parts.push(chunk.subarray(pos - length, pos));
            this.partsTotalLen += length;

            if (this.packetLen < 0xffffff) {
              let buf = Buffer.concat(this.parts, this.partsTotalLen);
              this.parts = null;
              this.receivePacket(this.packet.update(buf, 0, this.partsTotalLen));
            }
          }
          this.resetHeader();
        } else {
          const buf = chunk.subarray(pos, chunkLen);
          if (!this.parts) {
            this.parts = [buf];
            this.partsTotalLen = chunkLen - pos;
          } else {
            this.parts.push(buf);
            this.partsTotalLen += chunkLen - pos;
          }
          this.remainingLen = length - (chunkLen - pos);
          return;
        }
      }
    } while (pos < chunkLen);
  }
}

module.exports = PacketInputStream;
