//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

const PluginAuth = require('./plugin-auth');
const fs = require('fs');
const Errors = require('../../../misc/errors');
const Sha256PasswordAuth = require('./sha256-password-auth');

const State = {
  INIT: 'INIT',
  FAST_AUTH_RESULT: 'FAST_AUTH_RESULT',
  REQUEST_SERVER_KEY: 'REQUEST_SERVER_KEY',
  SEND_AUTH: 'SEND_AUTH'
};

/**
 * Use caching Sha2 password authentication
 */
class CachingSha2PasswordAuth extends PluginAuth {
  constructor(packSeq, compressPackSeq, pluginData, cmdParam, reject, multiAuthResolver) {
    super(cmdParam, multiAuthResolver, reject);
    this.multiAuthResolver = multiAuthResolver;
    this.pluginData = pluginData;
    this.sequenceNo = packSeq;
    this.compressSequenceNo = compressPackSeq;
    this.counter = 0;
    this.state = State.INIT;
  }

  start(out, opts, info) {
    this.exchange(this.pluginData, out, opts, info);
    this.onPacketReceive = this.response;
  }

  exchange(packet, out, opts, info) {
    switch (this.state) {
      case State.INIT:
        const truncatedSeed = this.pluginData.slice(0, this.pluginData.length - 1);
        const encPwd = Sha256PasswordAuth.encryptSha256Password(opts.password, truncatedSeed);
        out.startPacket(this);
        if (encPwd.length > 0) {
          out.writeBuffer(encPwd, 0, encPwd.length);
          out.flushPacket();
        } else {
          out.writeEmptyPacket(true);
        }
        this.state = State.FAST_AUTH_RESULT;
        return;

      case State.FAST_AUTH_RESULT:
        // length encoded numeric : 0x01 0x03/0x04
        const fastAuthResult = packet[1];
        switch (fastAuthResult) {
          case 0x03:
            // success authentication
            // an OK_Packet will follow
            return;

          case 0x04:
            if (opts.ssl) {
              // using SSL, so sending password in clear
              out.startPacket(this);
              out.writeString(opts.password);
              out.writeInt8(0);
              out.flushPacket();
              return;
            }

            // retrieve public key from configuration or from server
            if (opts.cachingRsaPublicKey) {
              try {
                let key = opts.cachingRsaPublicKey;
                if (!key.includes('-----BEGIN')) {
                  // rsaPublicKey contain path
                  key = fs.readFileSync(key, 'utf8');
                }
                this.publicKey = Sha256PasswordAuth.retrievePublicKey(key);
              } catch (err) {
                return this.throwError(err, info);
              }
              // send Sha256Password Packet
              Sha256PasswordAuth.sendSha256PwdPacket(this, this.pluginData, this.publicKey, opts.password, out);
            } else {
              if (!opts.allowPublicKeyRetrieval) {
                return this.throwError(
                  Errors.createFatalError(
                    'RSA public key is not available client side. Either set option `cachingRsaPublicKey` to indicate' +
                      ' public key path, or allow public key retrieval with option `allowPublicKeyRetrieval`',
                    Errors.ER_CANNOT_RETRIEVE_RSA_KEY,
                    info
                  ),
                  info
                );
              }
              this.state = State.REQUEST_SERVER_KEY;
              // ask caching public Key Retrieval
              out.startPacket(this);
              out.writeInt8(0x02);
              out.flushPacket();
            }
            return;
        }

      case State.REQUEST_SERVER_KEY:
        this.publicKey = Sha256PasswordAuth.retrievePublicKey(packet.toString(undefined, 1));
        this.state = State.SEND_AUTH;
        Sha256PasswordAuth.sendSha256PwdPacket(this, this.pluginData, this.publicKey, opts.password, out);
        return;
    }
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

module.exports = CachingSha2PasswordAuth;
