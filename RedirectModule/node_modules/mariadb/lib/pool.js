//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const { EventEmitter } = require('events');

const Queue = require('denque');
const Errors = require('./misc/errors');
const Utils = require('./misc/utils');
const Connection = require('./connection');
const CommandParameter = require('./command-parameter');

class Pool extends EventEmitter {
  opts;
  #closed = false;
  #connectionInCreation = false;
  #errorCreatingConnection = null;
  #idleConnections = new Queue();
  #activeConnections = {};
  #requests = new Queue();
  #unusedConnectionRemoverId;
  #requestTimeoutId;
  #connErrorNumber = 0;
  #initialized = false;
  _sizeHandlerTimeout;

  constructor(options) {
    super();
    this.opts = options;

    this.on('_idle', this._requestsHandler);
    this.on('validateSize', this._sizeHandler);
    this._sizeHandler();
  }

  //*****************************************************************
  // pool automatic handlers
  //*****************************************************************

  _doCreateConnection(resolve, reject, timeoutEnd) {
    this._createConnection()
      .then((conn) => {
        if (this.#closed) {
          conn.forceEnd(
            null,
            () => {},
            () => {}
          );
          reject(
            new Errors.createFatalError(
              'Cannot create new connection to pool, pool closed',
              Errors.ER_ADD_CONNECTION_CLOSED_POOL
            )
          );
          return;
        }

        conn.lastUse = Date.now();
        const nativeDestroy = conn.destroy.bind(conn);
        const pool = this;

        conn.destroy = function () {
          pool._endLeak(conn);
          delete pool.#activeConnections[conn.threadId];
          nativeDestroy();
          pool.emit('validateSize');
        };

        conn.once('error', function () {
          let idx = 0;
          let currConn;
          pool._endLeak(conn);
          delete pool.#activeConnections[conn.threadId];
          while ((currConn = pool.#idleConnections.peekAt(idx))) {
            if (currConn === conn) {
              pool.#idleConnections.removeOne(idx);
              continue;
            }
            //since connection did have an error, other waiting connection might too
            //forcing validation when borrowed next time, even if "minDelayValidation" is not reached.
            currConn.lastUse = Math.min(currConn.lastUse, Date.now() - pool.opts.minDelayValidation);
            idx++;
          }
          setTimeout(() => {
            if (!pool.#requests.isEmpty()) {
              pool._sizeHandler();
            }
          }, 0);
        });

        this.#idleConnections.push(conn);
        this.#connectionInCreation = false;
        this.emit('_idle');
        this.emit('connection', conn);
        resolve(conn);
      })
      .catch((err) => {
        //if timeout is reached or authentication fail return error
        if (
          this.#closed ||
          (err.errno && (err.errno === 1524 || err.errno === 1045 || err.errno === 1698)) ||
          timeoutEnd < Date.now()
        ) {
          err.message = err.message + this._errorMsgAddon();
          reject(err);
          return;
        }
        setTimeout(this._doCreateConnection.bind(this, resolve, reject, timeoutEnd), 500);
      });
  }

  _destroy(conn) {
    this._endLeak(conn);
    delete this.#activeConnections[conn.threadId];
    conn.lastUse = Date.now();
    conn.forceEnd(
      null,
      () => {},
      () => {}
    );

    if (this.totalConnections() === 0) {
      this._stopReaping();
    }

    this.emit('validateSize');
  }

  release(conn) {
    // ensure releasing only once
    if (this.#activeConnections[conn.threadId]) {
      this._endLeak(conn);
      this.#activeConnections[conn.threadId] = null;
      conn.lastUse = Date.now();

      if (this.#closed) {
        conn.forceEnd(
          null,
          () => {},
          () => {}
        );
      } else if (conn.isValid()) {
        this.emit('release', conn);
        this.#idleConnections.push(conn);
        process.nextTick(this.emit.bind(this, '_idle'));
      } else {
        this.emit('validateSize');
      }
    }
  }

  _checkLeak(conn) {
    conn.lastUse = Date.now();
    conn.leaked = false;
    conn.leakProcess = setTimeout(
      (conn) => {
        conn.leaked = true;
        conn.opts.logger.warning(
          `A possible connection leak on the thread ${
            conn.info.threadId
          } (the connection not returned to the pool since ${
            Date.now() - conn.lastUse
          } ms). Has the connection.release() been called ?` + this._errorMsgAddon()
        );
      },
      this.opts.leakDetectionTimeout,
      conn
    );
  }

  _endLeak(conn) {
    if (conn.leakProcess) {
      clearTimeout(conn.leakProcess);
      conn.leakProcess = null;
      if (conn.leaked) {
        conn.opts.logger.warning(
          `Previous possible leak connection with thread ${conn.info.threadId} was returned to pool`
        );
      }
    }
  }

  /**
   * Permit to remove idle connection if unused for some time.
   */
  _startReaping() {
    if (!this.#unusedConnectionRemoverId && this.opts.idleTimeout > 0) {
      this.#unusedConnectionRemoverId = setInterval(this._reaper.bind(this), 500);
    }
  }

  _stopReaping() {
    if (this.#unusedConnectionRemoverId && this.totalConnections() === 0) {
      clearInterval(this.#unusedConnectionRemoverId);
    }
  }

  _reaper() {
    const idleTimeRemoval = Date.now() - this.opts.idleTimeout * 1000;
    let maxRemoval = Math.max(0, this.#idleConnections.length - this.opts.minimumIdle);
    while (maxRemoval > 0) {
      const conn = this.#idleConnections.peek();
      maxRemoval--;
      if (conn && conn.lastUse < idleTimeRemoval) {
        this.#idleConnections.shift();
        conn.forceEnd(
          null,
          () => {},
          () => {}
        );
        continue;
      }
      break;
    }

    if (this.totalConnections() === 0) {
      this._stopReaping();
    }
    this.emit('validateSize');
  }

  _shouldCreateMoreConnections() {
    return (
      !this.#connectionInCreation &&
      this.#idleConnections.length < this.opts.minimumIdle &&
      this.totalConnections() < this.opts.connectionLimit &&
      !this.#closed
    );
  }

  /**
   * Grow pool connections until reaching connection limit.
   */
  _sizeHandler() {
    if (this._shouldCreateMoreConnections() && !this._sizeHandlerTimeout) {
      this.#connectionInCreation = true;
      setImmediate(
        function () {
          const timeoutEnd = Date.now() + this.opts.initializationTimeout;
          new Promise((resolve, reject) => {
            this._doCreateConnection(resolve, reject, timeoutEnd);
          })
            .then(() => {
              this.#initialized = true;
              this.#errorCreatingConnection = null;
              this.#connErrorNumber = 0;
              if (this._shouldCreateMoreConnections()) {
                this.emit('validateSize');
              }
              this._startReaping();
            })
            .catch((err) => {
              this.#connectionInCreation = false;
              if (!this.#closed) {
                if (!this.#initialized) {
                  err.message = 'Error during pool initialization: ' + err.message;
                } else {
                  err.message = 'Pool fails to create connection: ' + err.message;
                }
                this.#errorCreatingConnection = err;
                this.emit('error', err);

                //delay next try
                this._sizeHandlerTimeout = setTimeout(
                  function () {
                    this._sizeHandlerTimeout = null;
                    if (!this.#requests.isEmpty()) {
                      this._sizeHandler();
                    }
                  }.bind(this),
                  Math.min(++this.#connErrorNumber * 500, 10000)
                );
              }
            });
        }.bind(this)
      );
    }
  }

  /**
   * Launch next waiting task request if available connections.
   */
  _requestsHandler() {
    clearTimeout(this.#requestTimeoutId);
    this.#requestTimeoutId = null;
    const request = this.#requests.shift();
    if (request) {
      const conn = this.#idleConnections.shift();
      if (conn) {
        if (this.opts.leakDetectionTimeout > 0) this._checkLeak(conn);
        this.emit('acquire', conn);
        this.#activeConnections[conn.threadId] = conn;
        request.resolver(conn);
      } else {
        this.#requests.unshift(request);
      }
      this._requestTimeoutHandler();
    }
  }

  _hasIdleConnection() {
    return !this.#idleConnections.isEmpty();
  }

  /**
   * Return an idle Connection.
   * If connection has not been used for some time ( minDelayValidation), validate connection status.
   *
   * @returns {Promise<Connection>} connection of null of no valid idle connection.
   */
  async _doAcquire() {
    if (!this._hasIdleConnection() || this.#closed) return Promise.reject();
    let conn;
    let mustRecheckSize = false;
    while ((conn = this.#idleConnections.shift()) != null) {
      //just check connection state first
      if (conn.isValid()) {
        this.#activeConnections[conn.threadId] = conn;
        //if not used for some time, validate connection with a COM_PING
        if (this.opts.minDelayValidation <= 0 || Date.now() - conn.lastUse > this.opts.minDelayValidation) {
          try {
            const cmdParam = new CommandParameter(null, null, { timeout: this.opts.pingTimeout });
            await new Promise(conn.ping.bind(conn, cmdParam));
          } catch (e) {
            delete this.#activeConnections[conn.threadId];
            continue;
          }
        }
        if (this.opts.leakDetectionTimeout > 0) this._checkLeak(conn);
        if (mustRecheckSize) setImmediate(this.emit.bind(this, 'validateSize'));
        return Promise.resolve(conn);
      }
      mustRecheckSize = true;
    }
    setImmediate(this.emit.bind(this, 'validateSize'));
    return Promise.reject();
  }

  _requestTimeoutHandler() {
    //handle next Timer
    this.#requestTimeoutId = null;
    const currTime = Date.now();
    let request;
    while ((request = this.#requests.peekFront())) {
      if (request.timeout <= currTime) {
        this.#requests.shift();

        let err = Errors.createError(
          `retrieve connection from pool timeout after ${Math.abs(
            Date.now() - (request.timeout - this.opts.acquireTimeout)
          )}ms${this._errorMsgAddon()}`,
          Errors.ER_GET_CONNECTION_TIMEOUT,
          null,
          'HY000',
          null,
          false,
          request.stack
        );

        // in order to provide more information when configuration is wrong / server is down
        if (this.activeConnections() === 0 && this.#errorCreatingConnection) {
          const errConnMsg = this.#errorCreatingConnection.message.split('\n')[0];
          err.message = err.message + `\n    connection error: ${errConnMsg}`;
        }
        request.reject(err);
      } else {
        this.#requestTimeoutId = setTimeout(this._requestTimeoutHandler.bind(this), request.timeout - currTime);
        return;
      }
    }
  }

  /**
   * Search info object of an existing connection. to know server type and version.
   * @returns information object if connection available.
   */
  _searchInfo() {
    let info = null;
    let conn = this.#idleConnections.get(0);

    if (!conn) {
      for (const threadId in Object.keys(this.#activeConnections)) {
        conn = this.#activeConnections[threadId];
        if (!conn) {
          break;
        }
      }
    }

    if (conn) {
      info = conn.info;
    }
    return info;
  }

  _rejectTask(task, err) {
    clearTimeout(this.#requestTimeoutId);
    this.#requestTimeoutId = null;
    task.reject(err);
    this._requestTimeoutHandler();
  }

  async _createConnection() {
    const conn = new Connection(this.opts.connOptions);
    await conn.connect();
    const pool = this;
    conn.forceEnd = conn.end;
    conn.release = function (resolve) {
      if (pool.#closed || !conn.isValid()) {
        pool._destroy(conn);
        resolve();
        return;
      }
      if (pool.opts.noControlAfterUse) {
        pool.release(conn);
        resolve();
        return;
      }
      //if server permit it, reset the connection, or rollback only if not
      // COM_RESET_CONNECTION exist since mysql 5.7.3 and mariadb 10.2.4
      // but not possible to use it with mysql waiting for https://bugs.mysql.com/bug.php?id=97633 correction.
      // and mariadb only since https://jira.mariadb.org/browse/MDEV-18281
      let revertFunction;
      if (
        pool.opts.resetAfterUse &&
        conn.info.isMariaDB() &&
        ((conn.info.serverVersion.minor === 2 && conn.info.hasMinVersion(10, 2, 22)) ||
          conn.info.hasMinVersion(10, 3, 13))
      ) {
        revertFunction = conn.reset.bind(conn, new CommandParameter());
      } else revertFunction = conn.changeTransaction.bind(conn, new CommandParameter('ROLLBACK'));

      new Promise(revertFunction).then(pool.release.bind(pool, conn), pool._destroy.bind(pool, conn)).finally(resolve);
    };
    conn.end = conn.release;
    return conn;
  }

  _leakedConnections() {
    let counter = 0;
    for (const connection of Object.values(this.#activeConnections)) {
      if (connection && connection.leaked) counter++;
    }
    return counter;
  }

  _errorMsgAddon() {
    if (this.opts.leakDetectionTimeout > 0) {
      return `\n    (pool connections: active=${this.activeConnections()} idle=${this.idleConnections()} leak=${this._leakedConnections()} limit=${
        this.opts.connectionLimit
      })`;
    }
    return `\n    (pool connections: active=${this.activeConnections()} idle=${this.idleConnections()} limit=${
      this.opts.connectionLimit
    })`;
  }

  //*****************************************************************
  // public methods
  //*****************************************************************

  get closed() {
    return this.#closed;
  }

  /**
   * Get current total connection number.
   * @return {number}
   */
  totalConnections() {
    return this.activeConnections() + this.idleConnections();
  }

  /**
   * Get current active connections.
   * @return {number}
   */
  activeConnections() {
    let counter = 0;
    for (const connection of Object.values(this.#activeConnections)) {
      if (connection) counter++;
    }
    return counter;
  }

  /**
   * Get current idle connection number.
   * @return {number}
   */
  idleConnections() {
    return this.#idleConnections.length;
  }

  /**
   * Get current stacked connection request.
   * @return {number}
   */
  taskQueueSize() {
    return this.#requests.length;
  }

  escape(value) {
    return Utils.escape(this.opts.connOptions, this._searchInfo(), value);
  }

  escapeId(value) {
    return Utils.escapeId(this.opts.connOptions, this._searchInfo(), value);
  }

  //*****************************************************************
  // promise methods
  //*****************************************************************

  /**
   * Retrieve a connection from pool.
   * Create a new one, if limit is not reached.
   * wait until acquireTimeout.
   * @param cmdParam for stackTrace error
   * @return {Promise}
   */
  getConnection(cmdParam) {
    if (this.#closed) {
      return Promise.reject(
        Errors.createError('pool is closed', Errors.ER_POOL_ALREADY_CLOSED, null, 'HY000', null, false, cmdParam.stack)
      );
    }
    return this._doAcquire().then(
      (conn) => {
        // connection is available. process task
        this.emit('acquire', conn);
        return conn;
      },
      () => {
        if (this.#closed) {
          throw Errors.createError(
            'Cannot add request to pool, pool is closed',
            Errors.ER_POOL_ALREADY_CLOSED,
            null,
            'HY000',
            null,
            false,
            cmdParam.stack
          );
        }
        // no idle connection available
        // create a new connection if limit is not reached
        setImmediate(this.emit.bind(this, 'validateSize'));
        return new Promise(
          function (resolver, rejecter) {
            // stack request
            setImmediate(this.emit.bind(this, 'enqueue'));
            const request = new Request(Date.now() + this.opts.acquireTimeout, cmdParam.stack, resolver, rejecter);
            this.#requests.push(request);
            if (!this.#requestTimeoutId) {
              this.#requestTimeoutId = setTimeout(this._requestTimeoutHandler.bind(this), this.opts.acquireTimeout);
            }
          }.bind(this)
        );
      }
    );
  }

  /**
   * Close all connection in pool
   * Ends in multiple step :
   * - close idle connections
   * - ensure that no new request is possible
   *   (active connection release are automatically closed on release)
   * - if remaining, after 10 seconds, close remaining active connections
   *
   * @return Promise
   */
  end() {
    if (this.#closed) {
      return Promise.reject(Errors.createError('pool is already closed', Errors.ER_POOL_ALREADY_CLOSED));
    }
    this.#closed = true;
    clearInterval(this.#unusedConnectionRemoverId);
    clearInterval(this._sizeHandlerTimeout);
    const cmdParam = new CommandParameter();
    if (this.opts.trace) Error.captureStackTrace(cmdParam);
    //close unused connections
    const idleConnectionsEndings = [];
    let conn;
    while ((conn = this.#idleConnections.shift())) {
      idleConnectionsEndings.push(new Promise(conn.forceEnd.bind(conn, cmdParam)));
    }

    clearTimeout(this.#requestTimeoutId);
    this.#requestTimeoutId = null;

    //reject all waiting task
    if (!this.#requests.isEmpty()) {
      const err = Errors.createError(
        'pool is ending, connection request aborted',
        Errors.ER_CLOSING_POOL,
        null,
        'HY000',
        null,
        false,
        cmdParam.stack
      );
      let task;
      while ((task = this.#requests.shift())) {
        task.reject(err);
      }
    }
    const pool = this;
    return Promise.all(idleConnectionsEndings).then(async () => {
      if (pool.activeConnections() > 0) {
        // wait up to 10 seconds, that active connection are released
        let remaining = 100;
        while (remaining-- > 0) {
          if (pool.activeConnections() > 0) {
            await new Promise((res) => setTimeout(() => res(), 100));
          }
        }

        // force close any remaining active connections
        for (const connection of Object.values(pool.#activeConnections)) {
          if (connection) connection.destroy();
        }
      }
      return Promise.resolve();
    });
  }
}

class Request {
  constructor(timeout, stack, resolver, rejecter) {
    this.timeout = timeout;
    this.stack = stack;
    this.resolver = resolver;
    this.rejecter = rejecter;
  }

  reject(err) {
    process.nextTick(this.rejecter, err);
  }
}

module.exports = Pool;
