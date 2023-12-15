//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

/**
 * Prepare result wrapper
 * This permit to ensure that cache can be close only one time cache.
 */
class PrepareWrapper {
  #closed = false;
  #cacheWrapper;
  #prepare;
  #conn;

  constructor(cacheWrapper, prepare) {
    this.#cacheWrapper = cacheWrapper;
    this.#prepare = prepare;
    this.#conn = prepare.conn;
    this.execute = this.#prepare.execute;
    this.executeStream = this.#prepare.executeStream;
  }
  get conn() {
    return this.#conn;
  }

  get id() {
    return this.#prepare.id;
  }

  get parameterCount() {
    return this.#prepare.parameterCount;
  }

  get _placeHolderIndex() {
    return this.#prepare._placeHolderIndex;
  }

  get columns() {
    return this.#prepare.columns;
  }

  set columns(columns) {
    this.#prepare.columns = columns;
  }
  get database() {
    return this.#prepare.database;
  }

  get query() {
    return this.#prepare.query;
  }

  isClose() {
    return this.#closed;
  }

  close() {
    if (!this.#closed) {
      this.#closed = true;
      this.#cacheWrapper.decrementUse();
    }
  }

  toString() {
    return 'PrepareWrapper{closed:' + this.#closed + ',cache:' + this.#cacheWrapper + '}';
  }
}

module.exports = PrepareWrapper;
