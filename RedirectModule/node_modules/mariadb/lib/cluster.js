//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const ClusterOptions = require('./config/cluster-options');
const PoolOptions = require('./config/pool-options');
const PoolCallback = require('./pool-callback');
const PoolPromise = require('./pool-promise');
const FilteredCluster = require('./filtered-cluster');
const EventEmitter = require('events');

/**
 * Create a new Cluster.
 * Cluster handle pools with patterns and handle failover / distributed load
 * according to selectors (round-robin / random / ordered )
 *
 * @param args      cluster arguments. see pool-cluster-options.
 * @constructor
 */
class Cluster extends EventEmitter {
  #opts;
  #nodes = {};
  #cachedPatterns = {};
  #nodeCounter = 0;

  constructor(args) {
    super();
    this.#opts = new ClusterOptions(args);
  }

  /**
   * Add a new pool node to cluster.
   *
   * @param id      identifier
   * @param config  pool configuration
   */
  add(id, config) {
    let identifier;
    if (typeof id === 'string' || id instanceof String) {
      identifier = id;
      if (this.#nodes[identifier]) throw new Error(`Node identifier '${identifier}' already exist !`);
    } else {
      identifier = 'PoolNode-' + this.#nodeCounter++;
      config = id;
    }
    const options = new PoolOptions(config);
    this.#nodes[identifier] = this._createPool(options);
  }

  /**
   * End cluster (and underlying pools).
   *
   * @return {Promise<any[]>}
   */
  end() {
    const cluster = this;
    this.#cachedPatterns = {};
    const poolEndPromise = [];
    Object.keys(this.#nodes).forEach((pool) => {
      const res = cluster.#nodes[pool].end();
      if (res) poolEndPromise.push(res);
    });
    this.#nodes = null;
    return Promise.all(poolEndPromise);
  }

  of(pattern, selector) {
    return new FilteredCluster(this, pattern, selector);
  }

  /**
   * Remove nodes according to pattern.
   *
   * @param pattern  pattern
   */
  remove(pattern) {
    if (!pattern) throw new Error('pattern parameter in Cluster.remove(pattern)  is mandatory');

    const regex = RegExp(pattern);
    Object.keys(this.#nodes).forEach(
      function (key) {
        if (regex.test(key)) {
          this.#nodes[key].end();
          delete this.#nodes[key];
          this.#cachedPatterns = {};
        }
      }.bind(this)
    );
  }

  /**
   * Get connection from available pools matching pattern, according to selector
   *
   * @param pattern       pattern filter (not mandatory)
   * @param selector      node selector ('RR','RANDOM' or 'ORDER')
   * @return {Promise}
   */
  getConnection(pattern, selector) {
    return this._getConnection(pattern, selector, undefined, undefined, undefined);
  }

  /**
   * Force using callback methods.
   */
  _setCallback() {
    this.getConnection = this._getConnectionCallback;
    this._createPool = this._createPoolCallback;
  }

  /**
   * Get connection from available pools matching pattern, according to selector
   * with additional parameter to avoid reusing failing node
   *
   * @param pattern       pattern filter (not mandatory)
   * @param selector      node selector ('RR','RANDOM' or 'ORDER')
   * @param avoidNodeKey  failing node
   * @param lastError     last error
   * @param remainingRetry remaining possible retry
   * @return {Promise}
   * @private
   */
  _getConnection(pattern, selector, remainingRetry, avoidNodeKey, lastError) {
    const matchingNodeList = this._matchingNodes(pattern || /^/);

    if (matchingNodeList.length === 0) {
      if (Object.keys(this.#nodes).length === 0 && !lastError) {
        return Promise.reject(
          new Error('No node have been added to cluster or nodes have been removed due to too much connection error')
        );
      }
      if (avoidNodeKey === undefined) return Promise.reject(new Error(`No node found for pattern '${pattern}'`));
      const errMsg = `No Connection available for '${pattern}'${
        lastError ? '. Last connection error was: ' + lastError.message : ''
      }`;
      return Promise.reject(new Error(errMsg));
    }

    if (remainingRetry === undefined) remainingRetry = matchingNodeList.length;
    const retry = --remainingRetry >= 0 ? this._getConnection.bind(this, pattern, selector, remainingRetry) : null;

    try {
      const nodeKey = this._selectPool(matchingNodeList, selector, avoidNodeKey);
      return this._handleConnectionError(matchingNodeList, nodeKey, retry);
    } catch (e) {
      return Promise.reject(e);
    }
  }

  _createPool(options) {
    const pool = new PoolPromise(options);
    pool.on('error', (err) => {});
    return pool;
  }

  _createPoolCallback(options) {
    const pool = new PoolCallback(options);
    pool.on('error', (err) => {});
    return pool;
  }

  /**
   * Get connection from available pools matching pattern, according to selector
   * with additional parameter to avoid reusing failing node
   *
   * @param pattern       pattern filter (not mandatory)
   * @param selector      node selector ('RR','RANDOM' or 'ORDER')
   * @param callback      callback function
   * @param avoidNodeKey  failing node
   * @param lastError     last error
   * @private
   */
  _getConnectionCallback(pattern, selector, callback, avoidNodeKey, lastError) {
    const matchingNodeList = this._matchingNodes(pattern || /^/);

    if (matchingNodeList.length === 0) {
      if (Object.keys(this.#nodes).length === 0 && !lastError) {
        callback(
          new Error('No node have been added to cluster or nodes have been removed due to too much connection error')
        );
        return;
      }

      if (avoidNodeKey === undefined) callback(new Error(`No node found for pattern '${pattern}'`));
      const errMsg = `No Connection available for '${pattern}'${
        lastError ? '. Last connection error was: ' + lastError.message : ''
      }`;
      callback(new Error(errMsg));
      return;
    }

    const retry = this._getConnectionCallback.bind(this, pattern, selector, callback);
    try {
      const nodeKey = this._selectPool(matchingNodeList, selector, avoidNodeKey);
      this._handleConnectionCallbackError(matchingNodeList, nodeKey, retry, callback);
    } catch (e) {
      callback(e);
    }
  }

  /**
   * Selecting nodes according to pattern.
   *
   * @param pattern pattern
   * @return {*}
   * @private
   */
  _matchingNodes(pattern) {
    if (this.#cachedPatterns[pattern]) return this.#cachedPatterns[pattern];

    const regex = RegExp(pattern);
    const matchingNodeList = [];
    Object.keys(this.#nodes).forEach((key) => {
      if (regex.test(key)) {
        matchingNodeList.push(key);
      }
    });

    this.#cachedPatterns[pattern] = matchingNodeList;
    return matchingNodeList;
  }

  /**
   * Select next node to be chosen in nodeList according to selector and failed nodes.
   *
   * @param nodeList        current node list
   * @param selectorParam   selector
   * @param avoidNodeKey    last failing node to avoid selecting this one.
   * @return {Promise}
   * @private
   */
  _selectPool(nodeList, selectorParam, avoidNodeKey) {
    const selector = selectorParam || this.#opts.defaultSelector;

    let selectorFct;
    switch (selector) {
      case 'RR':
        selectorFct = roundRobinSelector;
        break;

      case 'RANDOM':
        selectorFct = randomSelector;
        break;

      case 'ORDER':
        selectorFct = orderedSelector;
        break;

      default:
        throw new Error(`Wrong selector value '${selector}'. Possible values are 'RR','RANDOM' or 'ORDER'`);
    }

    let nodeIdx = 0;
    let nodeKey = selectorFct(nodeList, nodeIdx);
    // first loop : search for node not blacklisted AND not the avoided key
    while (
      (avoidNodeKey === nodeKey ||
        (this.#nodes[nodeKey].blacklistedUntil && this.#nodes[nodeKey].blacklistedUntil > Date.now())) &&
      nodeIdx < nodeList.length - 1
    ) {
      nodeIdx++;
      nodeKey = selectorFct(nodeList, nodeIdx);
    }

    if (avoidNodeKey === nodeKey) {
      // second loop, search even in blacklisted node in order to choose a different node than to be avoided
      nodeIdx = 0;
      while (avoidNodeKey === nodeKey && nodeIdx < nodeList.length - 1) {
        nodeIdx++;
        nodeKey = selectorFct(nodeList, nodeIdx);
      }
    }

    return nodeKey;
  }

  /**
   * Connect, or if fail handle retry / set timeout error
   *
   * @param nodeList    current node list
   * @param nodeKey     node name to connect
   * @param retryFct    retry function
   * @return {Promise}
   * @private
   */
  _handleConnectionError(nodeList, nodeKey, retryFct) {
    const cluster = this;
    const node = this.#nodes[nodeKey];
    return node
      .getConnection()
      .then((conn) => {
        node.blacklistedUntil = null;
        node.errorCount = 0;
        return Promise.resolve(conn);
      })
      .catch((err) => {
        node.errorCount = node.errorCount ? node.errorCount + 1 : 1;
        node.blacklistedUntil = Date.now() + cluster.#opts.restoreNodeTimeout;
        if (
          cluster.#opts.removeNodeErrorCount &&
          node.errorCount >= cluster.#opts.removeNodeErrorCount &&
          cluster.#nodes[nodeKey]
        ) {
          delete cluster.#nodes[nodeKey];
          cluster.#cachedPatterns = {};
          delete nodeList.lastRrIdx;
          setImmediate(cluster.emit.bind(cluster, 'remove', nodeKey));

          //remove node from configuration if not already removed
          node.end().catch((err) => {
            // dismiss error
          });
        }

        if (nodeList.length !== 0 && cluster.#opts.canRetry && retryFct) {
          return retryFct(nodeKey, err);
        }
        return Promise.reject(err);
      });
  }

  /**
   * Connect, or if fail handle retry / set timeout error
   *
   * @param nodeList    current node list
   * @param nodeKey     node name to connect
   * @param retryFct    retry function
   * @param callback    callback function
   * @private
   */
  _handleConnectionCallbackError(nodeList, nodeKey, retryFct, callback) {
    const cluster = this;
    const node = this.#nodes[nodeKey];
    node.getConnection((err, conn) => {
      if (err) {
        node.errorCount = node.errorCount ? node.errorCount + 1 : 1;
        node.blacklistedUntil = Date.now() + cluster.#opts.restoreNodeTimeout;
        if (
          cluster.#opts.removeNodeErrorCount &&
          node.errorCount >= cluster.#opts.removeNodeErrorCount &&
          cluster.#nodes[nodeKey]
        ) {
          delete cluster.#nodes[nodeKey];
          cluster.#cachedPatterns = {};
          delete nodeList.lastRrIdx;
          setImmediate(cluster.emit.bind(cluster, 'remove', nodeKey));

          //remove node from configuration if not already removed
          node.end(() => {
            //dismiss error
          });
        }

        if (nodeList.length !== 0 && cluster.#opts.canRetry && retryFct) {
          return retryFct(nodeKey, err);
        }

        callback(err);
      } else {
        node.errorCount = 0;
        callback(null, conn);
      }
    });
  }

  //*****************************************************************
  // internal public testing methods
  //*****************************************************************

  get __tests() {
    return new TestMethods(this.#nodes);
  }
}

class TestMethods {
  #nodes;

  constructor(nodes) {
    this.#nodes = nodes;
  }
  getNodes() {
    return this.#nodes;
  }
}

/**
 * Round robin selector: using nodes one after the other.
 *
 * @param nodeList  node list
 * @return {String}
 */
const roundRobinSelector = (nodeList) => {
  let lastRoundRobin = nodeList.lastRrIdx;
  if (lastRoundRobin === undefined) lastRoundRobin = -1;
  if (++lastRoundRobin >= nodeList.length) lastRoundRobin = 0;
  nodeList.lastRrIdx = lastRoundRobin;
  return nodeList[lastRoundRobin];
};

/**
 * Random selector: use a random node.
 *
 * @param nodeList  node list
 * @return {String}
 */
const randomSelector = (nodeList) => {
  let randomIdx = Math.floor(Math.random() * nodeList.length);
  return nodeList[randomIdx];
};

/**
 * Ordered selector: always use the nodes in sequence, unless failing.
 *
 * @param nodeList  node list
 * @param retry     sequence number if last node is tagged has failing
 * @return {String}
 */
const orderedSelector = (nodeList, retry) => {
  return nodeList[retry];
};

module.exports = Cluster;
