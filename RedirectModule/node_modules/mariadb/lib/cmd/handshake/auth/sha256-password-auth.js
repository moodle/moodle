//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

const PluginAuth = require('./plugin-auth');
const fs = require('fs');
const crypto = require('crypto');
const Errors = require('../../../misc/errors');
const Crypto = require('crypto');

/**
 * Use Sha256 authentication
 */
class Sha256PasswordAuth extends PluginAuth {
  constructor(packSeq, compressPackSeq, pluginData, cmdParam, reject, multiAuthResolver) {
    super(cmdParam, multiAuthResolver, reject);
    this.pluginData = pluginData;
    this.sequenceNo = packSeq;
    this.compressSequenceNo = compressPackSeq;
    this.counter = 0;
    this.counter = 0;
    this.initialState = true;
    this.multiAuthResolver = multiAuthResolver;
  }

  start(out, opts, info) {
    this.exchange(this.pluginData, out, opts, info);
    this.onPacketReceive = this.response;
  }

  exchange(buffer, out, opts, info) {
    if (this.initialState) {
      if (!opts.password) {
        out.startPacket(this);
        out.writeEmptyPacket(true);
        return;
      } else if (opts.ssl) {
        // using SSL, so sending password in clear
        out.startPacket(this);
        if (opts.password) {
          out.writeString(opts.password);
        }
        out.writeInt8(0);
        out.flushPacket();
        return;
      } else {
        // retrieve public key from configuration or from server
        if (opts.rsaPublicKey) {
          try {
            let key = opts.rsaPublicKey;
            if (!key.includes('-----BEGIN')) {
              // rsaPublicKey contain path
              key = fs.readFileSync(key, 'utf8');
            }
            this.publicKey = Sha256PasswordAuth.retrievePublicKey(key);
          } catch (err) {
            return this.throwError(err, info);
          }
        } else {
          if (!opts.allowPublicKeyRetrieval) {
            return this.throwError(
              Errors.createFatalError(
                'RSA public key is not available client side. Either set option `rsaPublicKey` to indicate' +
                  ' public key path, or allow public key retrieval with option `allowPublicKeyRetrieval`',
                Errors.ER_CANNOT_RETRIEVE_RSA_KEY,
                info
              ),
              info
            );
          }
          this.initialState = false;

          // ask public Key Retrieval
          out.startPacket(this);
          out.writeInt8(0x01);
          out.flushPacket();
          return;
        }
      }

      // send Sha256Password Packet
      Sha256PasswordAuth.sendSha256PwdPacket(this, this.pluginData, this.publicKey, opts.password, out);
    } else {
      // has request public key
      this.publicKey = Sha256PasswordAuth.retrievePublicKey(buffer.toString('utf8', 1));
      Sha256PasswordAuth.sendSha256PwdPacket(this, this.pluginData, this.publicKey, opts.password, out);
    }
  }

  static retrievePublicKey(key) {
    return key.replace('(-+BEGIN PUBLIC KEY-+\\r?\\n|\\n?-+END PUBLIC KEY-+\\r?\\n?)', '');
  }

  static sendSha256PwdPacket(cmd, pluginData, publicKey, password, out) {
    const truncatedSeed = pluginData.slice(0, pluginData.length - 1);
    out.startPacket(cmd);
    const enc = Sha256PasswordAuth.encrypt(truncatedSeed, password, publicKey);
    out.writeBuffer(enc, 0, enc.length);
    out.flushPacket();
  }

  static encryptSha256Password(password, seed) {
    if (!password) return Buffer.alloc(0);

    let hash = Crypto.createHash('sha256');
    let stage1 = hash.update(password, 'utf8').digest();
    hash = Crypto.createHash('sha256');

    let stage2 = hash.update(stage1).digest();
    hash = Crypto.createHash('sha256');

    // order is different than sha 1 !!!!!
    hash.update(stage2);
    hash.update(seed);

    let digest = hash.digest();
    let returnBytes = Buffer.allocUnsafe(digest.length);
    for (let i = 0; i < digest.length; i++) {
      returnBytes[i] = stage1[i] ^ digest[i];
    }
    return returnBytes;
  }

  // encrypt password with public key
  static encrypt(seed, password, publicKey) {
    const nullFinishedPwd = Buffer.from(password + '\0');
    const xorBytes = Buffer.allocUnsafe(nullFinishedPwd.length);
    const seedLength = seed.length;
    for (let i = 0; i < xorBytes.length; i++) {
      xorBytes[i] = nullFinishedPwd[i] ^ seed[i % seedLength];
    }
    return crypto.publicEncrypt({ key: publicKey, padding: crypto.constants.RSA_PKCS1_OAEP_PADDING }, xorBytes);
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
        let promptData = packet.readBufferRemaining();
        this.exchange(promptData, out, opts, info);
        this.onPacketReceive = this.response;
    }
  }
}

module.exports = Sha256PasswordAuth;
