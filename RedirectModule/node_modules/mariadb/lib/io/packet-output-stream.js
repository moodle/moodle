//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Iconv = require('iconv-lite');
const Utils = require('../misc/utils');
const Errors = require('../misc/errors');
const Collations = require('../const/collations');

const QUOTE = 0x27;
const DBL_QUOTE = 0x22;
const ZERO_BYTE = 0x00;
const SLASH = 0x5c;

//increase by level to avoid buffer copy.
const SMALL_BUFFER_SIZE = 256;
const MEDIUM_BUFFER_SIZE = 16384; //16k
const LARGE_BUFFER_SIZE = 131072; //128k
const BIG_BUFFER_SIZE = 1048576; //1M
const MAX_BUFFER_SIZE = 16777219; //16M + 4
const CHARS_GLOBAL_REGEXP = /[\0\"\'\\\b\n\r\t\u001A]/g; // eslint-disable-line no-control-regex

/**
 * MySQL packet builder.
 *
 * @param opts    options
 * @param info    connection info
 * @constructor
 */
class PacketOutputStream {
  constructor(opts, info) {
    this.opts = opts;
    this.info = info;
    this.pos = 4;
    this.markPos = -1;
    this.bufContainDataAfterMark = false;
    this.cmdLength = 0;
    this.buf = Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    this.maxAllowedPacket = opts.maxAllowedPacket || 4194304;
    this.maxPacketLength = Math.min(MAX_BUFFER_SIZE, this.maxAllowedPacket + 4);

    this.changeEncoding(this.opts.collation ? this.opts.collation : Collations.fromIndex(224));
    this.changeDebug(this.opts.debug);

    this.opts.on('collation', this.changeEncoding.bind(this));
    this.opts.on('debug', this.changeDebug.bind(this));
  }

  changeEncoding(collation) {
    this.encoding = collation.charset;
    if (this.encoding === 'utf8') {
      this.writeString = this.writeDefaultBufferString;
      this.encodeString = this.encodeNodeString;
      this.writeLengthEncodedString = this.writeDefaultBufferLengthEncodedString;
      this.writeStringEscapeQuote = this.writeUtf8StringEscapeQuote;
    } else if (Buffer.isEncoding(this.encoding)) {
      this.writeString = this.writeDefaultBufferString;
      this.encodeString = this.encodeNodeString;
      this.writeLengthEncodedString = this.writeDefaultBufferLengthEncodedString;
      this.writeStringEscapeQuote = this.writeDefaultStringEscapeQuote;
    } else {
      this.writeString = this.writeDefaultIconvString;
      this.encodeString = this.encodeIconvString;
      this.writeLengthEncodedString = this.writeDefaultIconvLengthEncodedString;
      this.writeStringEscapeQuote = this.writeDefaultStringEscapeQuote;
    }
  }

  changeDebug(debug) {
    this.debug = debug;
    this.flushBuffer = debug ? this.flushBufferDebug : this.flushBufferBasic;
    this.fastFlush = debug ? this.fastFlushDebug : this.fastFlushBasic;
  }

  setStream(stream) {
    this.stream = stream;
  }

  growBuffer(len) {
    let newCapacity;
    if (len + this.pos < MEDIUM_BUFFER_SIZE) {
      newCapacity = MEDIUM_BUFFER_SIZE;
    } else if (len + this.pos < LARGE_BUFFER_SIZE) {
      newCapacity = LARGE_BUFFER_SIZE;
    } else if (len + this.pos < BIG_BUFFER_SIZE) {
      newCapacity = BIG_BUFFER_SIZE;
    } else {
      newCapacity = MAX_BUFFER_SIZE;
    }

    if (len + this.pos > newCapacity) {
      if (this.markPos !== -1) {
        // buf is > 16M with mark.
        // flush until mark, reset pos at beginning
        this.flushBufferStopAtMark();

        if (len + this.pos <= this.buf.length) {
          return;
        }
        this.growBuffer(len);
      }
    }

    let newBuf = Buffer.allocUnsafe(newCapacity);
    this.buf.copy(newBuf, 0, 0, this.pos);
    this.buf = newBuf;
  }

  mark() {
    this.markPos = this.pos;
  }

  isMarked() {
    return this.markPos !== -1;
  }

  hasFlushed() {
    return this.cmd.sequenceNo !== -1;
  }

  bufIsDataAfterMark() {
    return this.bufContainDataAfterMark;
  }

  bufIsAfterMaxPacketLength() {
    return this.pos > this.maxPacketLength;
  }

  /**
   * Reset mark flag and send bytes after mark flag.
   *
   * @return buffer after mark flag
   */
  resetMark() {
    this.pos = this.markPos;
    this.markPos = -1;
    if (this.bufContainDataAfterMark) {
      const data = Buffer.allocUnsafe(this.pos - 4);
      this.buf.copy(data, 0, 4, this.pos);
      this.cmd.sequenceNo = -1;
      this.cmd.compressSequenceNo = -1;
      this.bufContainDataAfterMark = false;
      return data;
    }
    return null;
  }

  /**
   * Send packet to socket.
   *
   * @throws IOException if socket error occur.
   */
  flush() {
    this.flushBuffer(true, 0);
    this.buf = Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    this.cmd.sequenceNo = -1;
    this.cmd.compressSequenceNo = -1;
    this.cmdLength = 0;
    this.markPos = -1;
  }

  flushPacket() {
    this.flushBuffer(false, 0);
    this.buf = Buffer.allocUnsafe(SMALL_BUFFER_SIZE);
    this.cmdLength = 0;
    this.markPos = -1;
  }

  startPacket(cmd) {
    this.cmd = cmd;
    this.pos = 4;
  }

  writeInt8(value) {
    if (this.pos + 1 >= this.buf.length) {
      if (this.pos >= MAX_BUFFER_SIZE && !this.bufContainDataAfterMark) {
        //buffer is more than a Packet, must flushBuffer()
        this.flushBuffer(false, 1);
      } else this.growBuffer(1);
    }
    this.buf[this.pos++] = value;
  }

  writeInt16(value) {
    if (this.pos + 2 >= this.buf.length) {
      let b = Buffer.allocUnsafe(2);
      b[0] = value;
      b[1] = value >>> 8;
      this.writeBuffer(b, 0, 2);
      return;
    }
    this.buf[this.pos] = value;
    this.buf[this.pos + 1] = value >> 8;
    this.pos += 2;
  }

  writeInt16AtPos(initPos) {
    this.buf[initPos] = this.pos - initPos - 2;
    this.buf[initPos + 1] = (this.pos - initPos - 2) >> 8;
  }

  writeInt24(value) {
    if (this.pos + 3 >= this.buf.length) {
      //not enough space remaining
      let arr = Buffer.allocUnsafe(3);
      arr[0] = value;
      arr[1] = value >> 8;
      arr[2] = value >> 16;
      this.writeBuffer(arr, 0, 3);
      return;
    }

    this.buf[this.pos] = value;
    this.buf[this.pos + 1] = value >> 8;
    this.buf[this.pos + 2] = value >> 16;
    this.pos += 3;
  }

  writeInt32(value) {
    if (this.pos + 4 >= this.buf.length) {
      //not enough space remaining
      let arr = Buffer.allocUnsafe(4);
      arr.writeInt32LE(value, 0);
      this.writeBuffer(arr, 0, 4);
      return;
    }

    this.buf[this.pos] = value;
    this.buf[this.pos + 1] = value >> 8;
    this.buf[this.pos + 2] = value >> 16;
    this.buf[this.pos + 3] = value >> 24;
    this.pos += 4;
  }

  writeBigInt(value) {
    if (this.pos + 8 >= this.buf.length) {
      //not enough space remaining
      let arr = Buffer.allocUnsafe(8);
      arr.writeBigInt64LE(value, 0);
      this.writeBuffer(arr, 0, 8);
      return;
    }
    this.buf.writeBigInt64LE(value, this.pos);
    this.pos += 8;
  }

  writeDouble(value) {
    if (this.pos + 8 >= this.buf.length) {
      //not enough space remaining
      let arr = Buffer.allocUnsafe(8);
      arr.writeDoubleLE(value, 0);
      this.writeBuffer(arr, 0, 8);
      return;
    }
    this.buf.writeDoubleLE(value, this.pos);
    this.pos += 8;
  }

  writeLengthCoded(len) {
    if (len < 0xfb) {
      this.writeInt8(len);
      return;
    }

    if (len < 65536) {
      //max length is len < 0xffff
      this.writeInt8(0xfc);
      this.writeInt16(len);
    } else if (len < 16777216) {
      this.writeInt8(0xfd);
      this.writeInt24(len);
    } else {
      this.writeInt8(0xfe);
      this.writeBigInt(BigInt(len));
    }
  }

  writeBuffer(arr, off, len) {
    if (len > this.buf.length - this.pos) {
      if (this.buf.length !== MAX_BUFFER_SIZE) {
        this.growBuffer(len);
      }

      //max buffer size
      if (len > this.buf.length - this.pos) {
        if (this.markPos !== -1) {
          this.growBuffer(len);
          if (this.markPos !== -1) {
            this.flushBufferStopAtMark();
          }
        }

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
            this.flushBuffer(false, remainingLen);
          }
        }
      }
    }

    // node.js copy is fast only when copying big buffer.
    // quick array copy is multiple time faster for small copy
    if (len > 50) {
      arr.copy(this.buf, this.pos, off, off + len);
      this.pos += len;
    } else {
      for (let i = 0; i < len; i++) {
        this.buf[this.pos++] = arr[off + i];
      }
    }
  }

  /**
   * Write ascii string to socket (no escaping)
   *
   * @param str                string
   */
  writeStringAscii(str) {
    let len = str.length;

    //not enough space remaining
    if (len >= this.buf.length - this.pos) {
      let strBuf = Buffer.from(str, 'ascii');
      this.writeBuffer(strBuf, 0, strBuf.length);
      return;
    }

    for (let off = 0; off < len; ) {
      this.buf[this.pos++] = str.charCodeAt(off++);
    }
  }

  writeLengthEncodedBuffer(buffer) {
    const len = buffer.length;
    this.writeLengthCoded(len);
    this.writeBuffer(buffer, 0, len);
  }

  writeUtf8StringEscapeQuote(str) {
    const charsLength = str.length;

    //not enough space remaining
    if (charsLength * 3 + 2 >= this.buf.length - this.pos) {
      const arr = Buffer.from(str, 'utf8');
      this.writeInt8(QUOTE);
      this.writeBufferEscape(arr);
      this.writeInt8(QUOTE);
      return;
    }

    //create UTF-8 byte array
    //since javascript char are internally using UTF-16 using surrogate's pattern, 4 bytes unicode characters will
    //represent 2 characters : example "\uD83C\uDFA4" = 🎤 unicode 8 "no microphones"
    //so max size is 3 * charLength
    //(escape characters are 1 byte encoded, so length might only be 2 when escaped)
    // + 2 for the quotes for text protocol
    let charsOffset = 0;
    let currChar;
    this.buf[this.pos++] = QUOTE;
    //quick loop if only ASCII chars for faster escape
    for (; charsOffset < charsLength && (currChar = str.charCodeAt(charsOffset)) < 0x80; charsOffset++) {
      if (currChar === SLASH || currChar === QUOTE || currChar === ZERO_BYTE || currChar === DBL_QUOTE) {
        this.buf[this.pos++] = SLASH;
      }
      this.buf[this.pos++] = currChar;
    }

    //if quick loop not finished
    while (charsOffset < charsLength) {
      currChar = str.charCodeAt(charsOffset++);
      if (currChar < 0x80) {
        if (currChar === SLASH || currChar === QUOTE || currChar === ZERO_BYTE || currChar === DBL_QUOTE) {
          this.buf[this.pos++] = SLASH;
        }
        this.buf[this.pos++] = currChar;
      } else if (currChar < 0x800) {
        this.buf[this.pos++] = 0xc0 | (currChar >> 6);
        this.buf[this.pos++] = 0x80 | (currChar & 0x3f);
      } else if (currChar >= 0xd800 && currChar < 0xe000) {
        //reserved for surrogate - see https://en.wikipedia.org/wiki/UTF-16
        if (currChar < 0xdc00) {
          //is high surrogate
          if (charsOffset + 1 > charsLength) {
            this.buf[this.pos++] = 0x3f;
          } else {
            const nextChar = str.charCodeAt(charsOffset);
            if (nextChar >= 0xdc00 && nextChar < 0xe000) {
              //is low surrogate
              const surrogatePairs = (currChar << 10) + nextChar + (0x010000 - (0xd800 << 10) - 0xdc00);
              this.buf[this.pos++] = 0xf0 | (surrogatePairs >> 18);
              this.buf[this.pos++] = 0x80 | ((surrogatePairs >> 12) & 0x3f);
              this.buf[this.pos++] = 0x80 | ((surrogatePairs >> 6) & 0x3f);
              this.buf[this.pos++] = 0x80 | (surrogatePairs & 0x3f);
              charsOffset++;
            } else {
              //must have low surrogate
              this.buf[this.pos++] = 0x3f;
            }
          }
        } else {
          //low surrogate without high surrogate before
          this.buf[this.pos++] = 0x3f;
        }
      } else {
        this.buf[this.pos++] = 0xe0 | (currChar >> 12);
        this.buf[this.pos++] = 0x80 | ((currChar >> 6) & 0x3f);
        this.buf[this.pos++] = 0x80 | (currChar & 0x3f);
      }
    }
    this.buf[this.pos++] = QUOTE;
  }

  encodeIconvString(str) {
    return Iconv.encode(str, this.encoding);
  }

  encodeNodeString(str) {
    return Buffer.from(str, this.encoding);
  }

  writeDefaultBufferString(str) {
    //javascript use UCS-2 or UTF-16 string internal representation
    //that means that string to byte will be a maximum of * 3
    // (4 bytes utf-8 are represented on 2 UTF-16 characters)
    if (str.length * 3 < this.buf.length - this.pos) {
      this.pos += this.buf.write(str, this.pos, this.encoding);
      return;
    }

    //checking real length
    let byteLength = Buffer.byteLength(str, this.encoding);
    if (byteLength > this.buf.length - this.pos) {
      if (this.buf.length < MAX_BUFFER_SIZE) {
        this.growBuffer(byteLength);
      }
      if (byteLength > this.buf.length - this.pos) {
        //not enough space in buffer, will stream :
        let strBuf = Buffer.from(str, this.encoding);
        this.writeBuffer(strBuf, 0, strBuf.length);
        return;
      }
    }
    this.pos += this.buf.write(str, this.pos, this.encoding);
  }

  writeDefaultBufferLengthEncodedString(str) {
    //javascript use UCS-2 or UTF-16 string internal representation
    //that means that string to byte will be a maximum of * 3
    // (4 bytes utf-8 are represented on 2 UTF-16 characters)
    //checking real length
    let byteLength = Buffer.byteLength(str, this.encoding);
    this.writeLengthCoded(byteLength);

    if (byteLength > this.buf.length - this.pos) {
      if (this.buf.length < MAX_BUFFER_SIZE) {
        this.growBuffer(byteLength);
      }
      if (byteLength > this.buf.length - this.pos) {
        //not enough space in buffer, will stream :
        let strBuf = Buffer.from(str, this.encoding);
        this.writeBuffer(strBuf, 0, strBuf.length);
        return;
      }
    }
    this.pos += this.buf.write(str, this.pos, this.encoding);
  }

  writeDefaultIconvString(str) {
    let buf = Iconv.encode(str, this.encoding);
    this.writeBuffer(buf, 0, buf.length);
  }

  writeDefaultIconvLengthEncodedString(str) {
    let buf = Iconv.encode(str, this.encoding);
    this.writeLengthCoded(buf.length);
    this.writeBuffer(buf, 0, buf.length);
  }

  /**
   * Parameters need to be properly escaped :
   * following characters are to be escaped by "\" :
   * - \0
   * - \\
   * - \'
   * - \"
   * regex split part of string writing part, and escaping special char.
   * Those chars are <= 7f meaning that this will work even with multi-byte encoding
   *
   * @param str string to escape.
   */
  writeDefaultStringEscapeQuote(str) {
    this.writeInt8(QUOTE);
    let match;
    let lastIndex = 0;
    while ((match = CHARS_GLOBAL_REGEXP.exec(str)) !== null) {
      this.writeString(str.slice(lastIndex, match.index));
      this.writeInt8(SLASH);
      this.writeInt8(match[0].charCodeAt(0));
      lastIndex = CHARS_GLOBAL_REGEXP.lastIndex;
    }

    if (lastIndex === 0) {
      // Nothing was escaped
      this.writeString(str);
      this.writeInt8(QUOTE);
      return;
    }

    if (lastIndex < str.length) {
      this.writeString(str.slice(lastIndex));
    }
    this.writeInt8(QUOTE);
  }

  writeBinaryDate(date) {
    const year = date.getFullYear();
    const mon = date.getMonth() + 1;
    const day = date.getDate();
    const hour = date.getHours();
    const min = date.getMinutes();
    const sec = date.getSeconds();
    const ms = date.getMilliseconds();

    let len = ms === 0 ? 7 : 11;
    //not enough space remaining
    if (len + 1 > this.buf.length - this.pos) {
      let tmpBuf = Buffer.allocUnsafe(len + 1);

      tmpBuf[0] = len;
      tmpBuf[1] = year;
      tmpBuf[2] = year >>> 8;
      tmpBuf[3] = mon;
      tmpBuf[4] = day;
      tmpBuf[5] = hour;
      tmpBuf[6] = min;
      tmpBuf[7] = sec;
      if (ms !== 0) {
        const micro = ms * 1000;
        tmpBuf[8] = micro;
        tmpBuf[9] = micro >>> 8;
        tmpBuf[10] = micro >>> 16;
        tmpBuf[11] = micro >>> 24;
      }

      this.writeBuffer(tmpBuf, 0, len + 1);
      return;
    }

    this.buf[this.pos] = len;
    this.buf[this.pos + 1] = year;
    this.buf[this.pos + 2] = year >>> 8;
    this.buf[this.pos + 3] = mon;
    this.buf[this.pos + 4] = day;
    this.buf[this.pos + 5] = hour;
    this.buf[this.pos + 6] = min;
    this.buf[this.pos + 7] = sec;

    if (ms !== 0) {
      const micro = ms * 1000;
      this.buf[this.pos + 8] = micro;
      this.buf[this.pos + 9] = micro >>> 8;
      this.buf[this.pos + 10] = micro >>> 16;
      this.buf[this.pos + 11] = micro >>> 24;
    }
    this.pos += len + 1;
  }

  writeBufferEscape(val) {
    let valLen = val.length;
    if (valLen * 2 > this.buf.length - this.pos) {
      //makes buffer bigger (up to 16M)
      if (this.buf.length !== MAX_BUFFER_SIZE) this.growBuffer(valLen * 2);

      //data may still be bigger than buffer.
      //must flush buffer when full (and reset position to 4)
      if (valLen * 2 > this.buf.length - this.pos) {
        //not enough space in buffer, will fill buffer
        for (let i = 0; i < valLen; i++) {
          switch (val[i]) {
            case QUOTE:
            case SLASH:
            case DBL_QUOTE:
            case ZERO_BYTE:
              if (this.pos >= this.buf.length) this.flushBuffer(false, (valLen - i) * 2);
              this.buf[this.pos++] = SLASH; //add escape slash
          }
          if (this.pos >= this.buf.length) this.flushBuffer(false, (valLen - i) * 2);
          this.buf[this.pos++] = val[i];
        }
        return;
      }
    }

    //sure to have enough place to use buffer directly
    for (let i = 0; i < valLen; i++) {
      switch (val[i]) {
        case QUOTE:
        case SLASH:
        case DBL_QUOTE:
        case ZERO_BYTE:
          this.buf[this.pos++] = SLASH; //add escape slash
      }
      this.buf[this.pos++] = val[i];
    }
  }

  /**
   * Count query size. If query size is greater than max_allowed_packet and nothing has been already
   * send, throw an exception to avoid having the connection closed.
   *
   * @param length additional length to query size
   * @param info current connection information
   * @throws Error if query has not to be send.
   */
  checkMaxAllowedLength(length, info) {
    if (this.cmdLength + length >= this.maxAllowedPacket) {
      // launch exception only if no packet has been send.
      return Errors.createFatalError(
        `query size (${this.cmdLength + length}) is >= to max_allowed_packet (${this.maxAllowedPacket})`,
        Errors.ER_MAX_ALLOWED_PACKET,
        info
      );
    }
    return null;
  }

  /**
   * Indicate if buffer contain any data.
   * @returns {boolean}
   */
  isEmpty() {
    return this.pos <= 4;
  }

  /**
   * Flush the internal buffer.
   */
  flushBufferDebug(commandEnd, remainingLen) {
    if (this.pos > 4) {
      this.buf[0] = this.pos - 4;
      this.buf[1] = (this.pos - 4) >>> 8;
      this.buf[2] = (this.pos - 4) >>> 16;
      this.buf[3] = ++this.cmd.sequenceNo;
      this.stream.writeBuf(this.buf.subarray(0, this.pos), this.cmd);
      this.stream.flush(true, this.cmd);
      this.cmdLength += this.pos - 4;

      this.opts.logger.network(
        `==> conn:${this.info.threadId ? this.info.threadId : -1} ${
          this.cmd.constructor.name + '(0,' + this.pos + ')'
        }\n${Utils.log(this.opts, this.buf, 0, this.pos)}`
      );

      if (commandEnd && this.pos === MAX_BUFFER_SIZE) {
        //if last packet fill the max size, must send an empty com to indicate that command end.
        this.writeEmptyPacket();
      }
      this.pos = 4;
    }
  }

  /**
   * Flush to last mark.
   */
  flushBufferStopAtMark() {
    const end = this.pos;
    this.pos = this.markPos;
    const tmpBuf = Buffer.allocUnsafe(Math.max(SMALL_BUFFER_SIZE, end + 4 - this.pos));
    this.buf.copy(tmpBuf, 4, this.markPos, end);
    this.flushBuffer(true, end - this.pos);
    this.cmdLength = 0;
    this.buf = tmpBuf;
    this.pos = 4 + end - this.markPos;
    this.markPos = -1;
    this.bufContainDataAfterMark = true;
  }

  flushBufferBasic(commandEnd, remainingLen) {
    this.buf[0] = this.pos - 4;
    this.buf[1] = (this.pos - 4) >>> 8;
    this.buf[2] = (this.pos - 4) >>> 16;
    this.buf[3] = ++this.cmd.sequenceNo;
    this.stream.writeBuf(this.buf.subarray(0, this.pos), this.cmd);
    this.stream.flush(true, this.cmd);
    this.cmdLength += this.pos - 4;
    if (commandEnd && this.pos === MAX_BUFFER_SIZE) {
      //if last packet fill the max size, must send an empty com to indicate that command end.
      this.writeEmptyPacket();
    }
    this.pos = 4;
  }

  fastFlushDebug(cmd, packet) {
    this.stream.writeBuf(packet, cmd);
    this.stream.flush(true, cmd);
    this.cmdLength += packet.length;

    this.opts.logger.network(
      `==> conn:${this.info.threadId ? this.info.threadId : -1} ${
        cmd.constructor.name + '(0,' + packet.length + ')'
      }\n${Utils.log(this.opts, packet, 0, packet.length)}`
    );
    this.cmdLength = 0;
    this.markPos = -1;
  }

  fastFlushBasic(cmd, packet) {
    this.stream.writeBuf(packet, cmd);
    this.stream.flush(true, cmd);
    this.cmdLength = 0;
    this.markPos = -1;
  }

  writeEmptyPacket() {
    const emptyBuf = Buffer.from([0x00, 0x00, 0x00, ++this.cmd.sequenceNo]);

    if (this.debug) {
      this.opts.logger.network(
        `==> conn:${this.info.threadId ? this.info.threadId : -1} ${this.cmd.constructor.name}(0,4)\n${Utils.log(
          this.opts,
          emptyBuf,
          0,
          4
        )}`
      );
    }

    this.stream.writeBuf(emptyBuf, this.cmd);
    this.stream.flush(true, this.cmd);
    this.cmdLength = 0;
  }
}

module.exports = PacketOutputStream;
