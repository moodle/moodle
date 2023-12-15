//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Cluster = require('./cluster');

/**
 * Create a new Cluster.
 * Cluster handle pools with patterns and handle failover / distributed load
 * according to selectors (round-robin / random / ordered )
 *
 * @param args      cluster arguments. see pool-cluster-options.
 * @constructor
 */
class ClusterCallback {
  #cluster;
  constructor(args) {
    this.#cluster = new Cluster(args);
    this.#cluster._setCallback();
    this.on = this.#cluster.on.bind(this.#cluster);
    this.once = this.#cluster.once.bind(this.#cluster);
  }

  /**
   * End cluster (and underlying pools).
   *
   * @param callback - not mandatory
   */
  end(callback) {
    if (callback && typeof callback !== 'function') {
      throw new Error('callback parameter must be a function');
    }
    const endingFct = callback ? callback : () => {};

    this.#cluster
      .end()
      .then(() => {
        endingFct();
      })
      .catch(endingFct);
  }

  /**
   * Get connection from available pools matching pattern, according to selector
   *
   * @param pattern       pattern filter (not mandatory)
   * @param selector      node selector ('RR','RANDOM' or 'ORDER')
   * @param callback      callback function
   */
  getConnection(pattern, selector, callback) {
    let pat = pattern,
      sel = selector,
      cal = callback;
    if (typeof pattern === 'function') {
      pat = null;
      sel = null;
      cal = pattern;
    } else if (typeof selector === 'function') {
      sel = null;
      cal = selector;
    }
    const endingFct = cal ? cal : (err, conn) => {};
    this.#cluster.getConnection(pat, sel, endingFct);
  }

  add(id, config) {
    this.#cluster.add(id, config);
  }

  of(pattern, selector) {
    return this.#cluster.of(pattern, selector);
  }

  remove(pattern) {
    this.#cluster.remove(pattern);
  }

  get __tests() {
    return this.#cluster.__tests;
  }
}

module.exports = ClusterCallback;
