//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

let ConnOptions = require('./connection-options');

class PoolOptions {
  constructor(opts) {
    if (typeof opts === 'string') {
      opts = ConnOptions.parse(opts);

      //set data type
      if (opts.acquireTimeout) opts.acquireTimeout = parseInt(opts.acquireTimeout);
      if (opts.connectionLimit) opts.connectionLimit = parseInt(opts.connectionLimit);
      if (opts.idleTimeout) opts.idleTimeout = parseInt(opts.idleTimeout);
      if (opts.leakDetectionTimeout) opts.leakDetectionTimeout = parseInt(opts.leakDetectionTimeout);
      if (opts.initializationTimeout) opts.initializationTimeout = parseInt(opts.initializationTimeout);
      if (opts.minDelayValidation) opts.minDelayValidation = parseInt(opts.minDelayValidation);
      if (opts.minimumIdle) opts.minimumIdle = parseInt(opts.minimumIdle);
      if (opts.noControlAfterUse) opts.noControlAfterUse = opts.noControlAfterUse === 'true';
      if (opts.resetAfterUse) opts.resetAfterUse = opts.resetAfterUse === 'true';
      if (opts.pingTimeout) opts.pingTimeout = parseInt(opts.pingTimeout);
    }

    this.acquireTimeout = opts.acquireTimeout === undefined ? 10000 : opts.acquireTimeout;
    this.connectionLimit = opts.connectionLimit === undefined ? 10 : opts.connectionLimit;
    this.idleTimeout = opts.idleTimeout === undefined ? 1800 : opts.idleTimeout;
    this.leakDetectionTimeout = opts.leakDetectionTimeout || 0;
    this.initializationTimeout = opts.initializationTimeout === undefined ? 30000 : opts.initializationTimeout;
    this.minDelayValidation = opts.minDelayValidation === undefined ? 500 : opts.minDelayValidation;
    this.minimumIdle =
      opts.minimumIdle === undefined ? this.connectionLimit : Math.min(opts.minimumIdle, this.connectionLimit);
    this.noControlAfterUse = opts.noControlAfterUse || false;
    this.resetAfterUse = opts.resetAfterUse || false;
    this.pingTimeout = opts.pingTimeout || 250;
    this.connOptions = new ConnOptions(opts);

    if (this.acquireTimeout > 0 && this.connOptions.connectTimeout > this.acquireTimeout) {
      this.connOptions.connectTimeout = this.acquireTimeout;
    }
  }
}

module.exports = PoolOptions;
