//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Utils = require('../misc/utils');
const ZLib = require('zlib');

//increase by level to avoid buffer copy.
const SMALL_BUFFER_SIZE = 2048;
const MEDIUM_BUFFER_SIZE = 131072; //128k
const LARGE_BUFFER_SIZE = 1048576; //1M
const MAX_BUFFER_SIZE = 16777222; //16M + 7

/**
/**
 * MySQL compression filter.
 * see https://mariadb.com/kb/en/library/0-packet/#compressed-packet
 */
class CompressionOutputStream {
  /**
   * Constructor
   *
   * @param socket    current socket
   * @param opts      current connection options
   * @param info      current connection information
   * @constructor
   */
  constructor(socket, opts, info) {
    this.info = info;
    this.opts = opts;
    this.pos = 7;
    this.header = Buffer.allocUnsafe(7);
    this.buf = Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    this.writer = (buffer) => {
      socket.write(buffer);
    };
  }

  growBuffer(len) {
    let newCapacity;
    if (len + this.pos < MEDIUM_BUFFER_SIZE) {
      newCapacity = MEDIUM_BUFFER_SIZE;
    } else if (len + this.pos < LARGE_BUFFER_SIZE) {
      newCapacity = LARGE_BUFFER_SIZE;
    } else newCapacity = MAX_BUFFER_SIZE;

    let newBuf = Buffer.allocUnsafe(newCapacity);
    this.buf.copy(newBuf, 0, 0, this.pos);
    this.buf = newBuf;
  }

  writeBuf(arr, cmd) {
    let off = 0,
      len = arr.length;
    if (arr instanceof Uint8Array) {
      arr = Buffer.from(arr);
    }
    if (len > this.buf.length - this.pos) {
      if (this.buf.length !== MAX_BUFFER_SIZE) {
        this.growBuffer(len);
      }

      //max buffer size
      if (len > this.buf.length - this.pos) {
        //not enough space in buffer, will stream :
        // fill buffer and flush until all data are snd
        let remainingLen = len;

        while (true) {
          //filling buffer
          let lenToFillBuffer = Math.min(MAX_BUFFER_SIZE - this.pos, remainingLen);
          arr.copy(this.buf, this.pos, off, off + lenToFillBuffer);
          remainingLen -= lenToFillBuffer;
          off += lenToFillBuffer;
          this.pos += lenToFillBuffer;

          if (remainingLen === 0) return;
          this.flush(false, cmd, remainingLen);
        }
      }
    }
    arr.copy(this.buf, this.pos, off, off + len);
    this.pos += len;
  }

  /**
   * Flush the internal buffer.
   */
  flush(cmdEnd, cmd, remainingLen) {
    if (this.pos < 1536) {
      //*******************************************************************************
      // small packet, no compression
      //*******************************************************************************

      this.buf[0] = this.pos - 7;
      this.buf[1] = (this.pos - 7) >>> 8;
      this.buf[2] = (this.pos - 7) >>> 16;
      this.buf[3] = ++cmd.compressSequenceNo;
      this.buf[4] = 0;
      this.buf[5] = 0;
      this.buf[6] = 0;

      if (this.opts.debugCompress) {
        this.opts.logger.network(
          `==> conn:${this.info.threadId ? this.info.threadId : -1} ${
            cmd ? cmd.constructor.name + '(0,' + this.pos + ')' : 'unknown'
          } (compress)\n${Utils.log(this.opts, this.buf, 0, this.pos)}`
        );
      }

      this.writer(this.buf.subarray(0, this.pos));
    } else {
      //*******************************************************************************
      // compressing packet
      //*******************************************************************************
      //use synchronous inflating, to ensure FIFO packet order
      const compressChunk = ZLib.deflateSync(this.buf.subarray(7, this.pos));
      const compressChunkLen = compressChunk.length;

      this.header[0] = compressChunkLen;
      this.header[1] = compressChunkLen >>> 8;
      this.header[2] = compressChunkLen >>> 16;
      this.header[3] = ++cmd.compressSequenceNo;
      this.header[4] = this.pos - 7;
      this.header[5] = (this.pos - 7) >>> 8;
      this.header[6] = (this.pos - 7) >>> 16;

      if (this.opts.debugCompress) {
        this.opts.logger.network(
          `==> conn:${this.info.threadId ? this.info.threadId : -1} ${
            cmd ? cmd.constructor.name + '(0,' + this.pos + '=>' + compressChunkLen + ')' : 'unknown'
          } (compress)\n${Utils.log(this.opts, compressChunk, 0, compressChunkLen, this.header)}`
        );
      }

      this.writer(this.header);
      this.writer(compressChunk);
      if (cmdEnd && compressChunkLen === MAX_BUFFER_SIZE) this.writeEmptyPacket(cmd);
      this.header = Buffer.allocUnsafe(7);
    }
    this.buf = remainingLen
      ? CompressionOutputStream.allocateBuffer(remainingLen)
      : Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    this.pos = 7;
  }

  static allocateBuffer(len) {
    if (len + 4 < SMALL_BUFFER_SIZE) {
      return Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    } else if (len + 4 < MEDIUM_BUFFER_SIZE) {
      return Buffer.allocUnsafe(MEDIUM_BUFFER_SIZE);
    } else if (len + 4 < LARGE_BUFFER_SIZE) {
      return Buffer.allocUnsafe(LARGE_BUFFER_SIZE);
    }
    return Buffer.allocUnsafe(MAX_BUFFER_SIZE);
  }

  writeEmptyPacket(cmd) {
    const emptyBuf = Buffer.from([0x00, 0x00, 0x00, cmd.compressSequenceNo, 0x00, 0x00, 0x00]);

    if (this.opts.debugCompress) {
      this.opts.logger.network(
        `==> conn:${this.info.threadId ? this.info.threadId : -1} ${
          cmd ? cmd.constructor.name + '(0,' + this.pos + ')' : 'unknown'
        } (compress)\n${Utils.log(this.opts, emptyBuf, 0, 7)}`
      );
    }

    this.writer(emptyBuf);
  }
}

module.exports = CompressionOutputStream;
