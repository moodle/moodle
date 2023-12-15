//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * Similar to pool cluster with pre-set pattern and selector.
 * Additional method query
 *
 * @param poolCluster    cluster
 * @param patternArg     pre-set pattern
 * @param selectorArg    pre-set selector
 * @constructor
 */
class FilteredCluster {
  #cluster;
  #pattern;
  #selector;

  constructor(poolCluster, patternArg, selectorArg) {
    this.#cluster = poolCluster;
    this.#pattern = patternArg;
    this.#selector = selectorArg;
  }

  /**
   * Get a connection according to previously indicated pattern and selector.
   *
   * @return {Promise}
   */
  getConnection() {
    return this.#cluster.getConnection(this.#pattern, this.#selector);
  }

  /**
   * Execute a text query on one connection from available pools matching pattern
   * in cluster.
   *
   * @param sql   sql command
   * @param value parameter value of sql command (not mandatory)
   * @return {Promise}
   */
  query(sql, value) {
    return this.#cluster
      .getConnection(this.#pattern, this.#selector)
      .then((conn) => {
        return conn
          .query(sql, value)
          .then((res) => {
            conn.release();
            return res;
          })
          .catch((err) => {
            conn.release();
            return Promise.reject(err);
          });
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }

  /**
   * Execute a binary query on one connection from available pools matching pattern
   * in cluster.
   *
   * @param sql   sql command
   * @param value parameter value of sql command (not mandatory)
   * @return {Promise}
   */
  execute(sql, value) {
    return this.#cluster
      .getConnection(this.#pattern, this.#selector)
      .then((conn) => {
        return conn
          .execute(sql, value)
          .then((res) => {
            conn.release();
            return res;
          })
          .catch((err) => {
            conn.release();
            return Promise.reject(err);
          });
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }

  /**
   * Execute a batch on one connection from available pools matching pattern
   * in cluster.
   *
   * @param sql   sql command
   * @param value parameter value of sql command
   * @return {Promise}
   */
  batch(sql, value) {
    return this.#cluster
      .getConnection(this.#pattern, this.#selector)
      .then((conn) => {
        return conn
          .batch(sql, value)
          .then((res) => {
            conn.release();
            return res;
          })
          .catch((err) => {
            conn.release();
            return Promise.reject(err);
          });
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }
}

module.exports = FilteredCluster;
