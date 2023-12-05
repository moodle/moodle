//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const ZLib = require('zlib');
const Utils = require('../misc/utils');

/**
 * MySQL packet parser
 * see : https://mariadb.com/kb/en/library/0-packet/
 */
class CompressionInputStream {
  constructor(reader, receiveQueue, opts, info) {
    this.reader = reader;
    this.receiveQueue = receiveQueue;
    this.info = info;
    this.opts = opts;
    this.header = Buffer.allocUnsafe(7);
    this.headerLen = 0;
    this.compressPacketLen = null;
    this.packetLen = null;
    this.remainingLen = null;

    this.parts = null;
    this.partsTotalLen = 0;
  }

  receivePacket(chunk) {
    let cmd = this.currentCmd();
    if (this.opts.debugCompress) {
      this.opts.logger.network(
        `<== conn:${this.info.threadId ? this.info.threadId : -1} ${
          cmd
            ? cmd.onPacketReceive
              ? cmd.constructor.name + '.' + cmd.onPacketReceive.name
              : cmd.constructor.name
            : 'no command'
        } (compress)\n${Utils.log(this.opts, chunk, 0, chunk.length, this.header)}`
      );
    }
    if (cmd) cmd.compressSequenceNo = this.header[3];
    const unCompressLen = this.header[4] | (this.header[5] << 8) | (this.header[6] << 16);
    if (unCompressLen === 0) {
      this.reader.onData(chunk);
    } else {
      //use synchronous inflating, to ensure FIFO packet order
      const unCompressChunk = ZLib.inflateSync(chunk);
      this.reader.onData(unCompressChunk);
    }
  }

  currentCmd() {
    let cmd;
    while ((cmd = this.receiveQueue.peek())) {
      if (cmd.onPacketReceive) return cmd;
      this.receiveQueue.shift();
    }
    return null;
  }

  resetHeader() {
    this.remainingLen = null;
    this.headerLen = 0;
  }

  onData(chunk) {
    let pos = 0;
    let length;
    const chunkLen = chunk.length;

    do {
      if (this.remainingLen) {
        length = this.remainingLen;
      } else if (this.headerLen === 0 && chunkLen - pos >= 7) {
        this.header[0] = chunk[pos];
        this.header[1] = chunk[pos + 1];
        this.header[2] = chunk[pos + 2];
        this.header[3] = chunk[pos + 3];
        this.header[4] = chunk[pos + 4];
        this.header[5] = chunk[pos + 5];
        this.header[6] = chunk[pos + 6];
        this.headerLen = 7;
        pos += 7;
        this.compressPacketLen = this.header[0] + (this.header[1] << 8) + (this.header[2] << 16);
        this.packetLen = this.header[4] | (this.header[5] << 8) | (this.header[6] << 16);
        if (this.packetLen === 0) this.packetLen = this.compressPacketLen;
        length = this.compressPacketLen;
      } else {
        length = null;
        while (chunkLen - pos > 0) {
          this.header[this.headerLen++] = chunk[pos++];
          if (this.headerLen === 7) {
            this.compressPacketLen = this.header[0] + (this.header[1] << 8) + (this.header[2] << 16);
            this.packetLen = this.header[4] | (this.header[5] << 8) | (this.header[6] << 16);
            if (this.packetLen === 0) this.packetLen = this.compressPacketLen;
            length = this.compressPacketLen;
            break;
          }
        }
      }

      if (length) {
        if (chunkLen - pos >= length) {
          const buf = chunk.subarray(pos, pos + length);
          pos += length;
          if (this.parts) {
            this.parts.push(buf);
            this.partsTotalLen += length;

            if (this.compressPacketLen < 0xffffff) {
              let buf = Buffer.concat(this.parts, this.partsTotalLen);
              this.parts = null;
              this.receivePacket(buf);
            }
          } else {
            if (this.compressPacketLen < 0xffffff) {
              this.receivePacket(buf);
            } else {
              this.parts = [buf];
              this.partsTotalLen = length;
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

module.exports = CompressionInputStream;
