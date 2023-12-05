//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const EventEmitter = require('events');
const Queue = require('denque');
const Net = require('net');
const PacketInputStream = require('./io/packet-input-stream');
const PacketOutputStream = require('./io/packet-output-stream');
const CompressionInputStream = require('./io/compression-input-stream');
const CompressionOutputStream = require('./io/compression-output-stream');
const ServerStatus = require('./const/server-status');
const ConnectionInformation = require('./misc/connection-information');
const tls = require('tls');
const Errors = require('./misc/errors');
const Utils = require('./misc/utils');
const Capabilities = require('./const/capabilities');

/*commands*/
const Authentication = require('./cmd/handshake/authentication');
const Quit = require('./cmd/quit');
const Ping = require('./cmd/ping');
const Reset = require('./cmd/reset');
const Query = require('./cmd/query');
const Prepare = require('./cmd/prepare');
const OkPacket = require('./cmd/class/ok-packet');
const Execute = require('./cmd/execute');
const ClosePrepare = require('./cmd/close-prepare');
const BatchBulk = require('./cmd/batch-bulk');
const ChangeUser = require('./cmd/change-user');
const { Status } = require('./const/connection_status');
const CommandParameter = require('./command-parameter');
const LruPrepareCache = require('./lru-prepare-cache');
const fsPromises = require('fs').promises;
const Parse = require('./misc/parse');
const Collations = require('./const/collations');

const convertFixedTime = function (tz, conn) {
  if (tz === 'UTC' || tz === 'Etc/UTC' || tz === 'Z' || tz === 'Etc/GMT') {
    return '+00:00';
  } else if (tz.startsWith('Etc/GMT') || tz.startsWith('GMT')) {
    let tzdiff;
    let negate;

    // strangely Etc/GMT+8 = GMT-08:00 = offset -8
    if (tz.startsWith('Etc/GMT')) {
      tzdiff = tz.substring(7);
      negate = !tzdiff.startsWith('-');
    } else {
      tzdiff = tz.substring(3);
      negate = tzdiff.startsWith('-');
    }
    let diff = parseInt(tzdiff.substring(1));
    if (isNaN(diff)) {
      throw Errors.createFatalError(
        `Automatic timezone setting fails. wrong Server timezone '${tz}' conversion to +/-HH:00 conversion.`,
        Errors.ER_WRONG_AUTO_TIMEZONE,
        conn.info
      );
    }
    return (negate ? '-' : '+') + (diff >= 10 ? diff : '0' + diff) + ':00';
  }
  return tz;
};

/**
 * New Connection instance.
 *
 * @param options    connection options
 * @returns Connection instance
 * @constructor
 * @fires Connection#connect
 * @fires Connection#end
 * @fires Connection#error
 *
 */
class Connection extends EventEmitter {
  opts;
  sendQueue = new Queue();
  receiveQueue = new Queue();
  waitingAuthenticationQueue = new Queue();
  status = Status.NOT_CONNECTED;
  socket = null;
  timeout = null;
  addCommand;
  streamOut;
  streamIn;
  info;
  prepareCache;

  constructor(options) {
    super();

    this.opts = Object.assign(new EventEmitter(), options);
    this.info = new ConnectionInformation(this.opts);
    this.prepareCache =
      this.opts.prepareCacheLength > 0 ? new LruPrepareCache(this.info, this.opts.prepareCacheLength) : null;
    this.addCommand = this.addCommandQueue;
    this.streamOut = new PacketOutputStream(this.opts, this.info);
    this.streamIn = new PacketInputStream(
      this.unexpectedPacket.bind(this),
      this.receiveQueue,
      this.streamOut,
      this.opts,
      this.info
    );

    this.on('close_prepare', this._closePrepare.bind(this));
    this.escape = Utils.escape.bind(this, this.opts, this.info);
    this.escapeId = Utils.escapeId.bind(this, this.opts, this.info);
  }

  //*****************************************************************
  // public methods
  //*****************************************************************

  /**
   * Connect event
   *
   * @returns {Promise} promise
   */
  connect() {
    const conn = this;
    this.status = Status.CONNECTING;
    const authenticationParam = new CommandParameter(null, null, this.opts, null);
    return new Promise(function (resolve, reject) {
      conn.connectRejectFct = reject;
      conn.connectResolveFct = resolve;
      // add a handshake to msg queue
      const authentication = new Authentication(
        authenticationParam,
        conn.authSucceedHandler.bind(conn),
        conn.authFailHandler.bind(conn),
        conn.createSecureContext.bind(conn),
        conn.getSocket.bind(conn)
      );
      Error.captureStackTrace(authentication);

      authentication.once('end', () => {
        conn.receiveQueue.shift();
        // conn.info.collation might not be initialized
        // in case of handshake throwing error
        if (!conn.opts.collation && conn.info.collation) {
          conn.opts.emit('collation', conn.info.collation);
        }
        process.nextTick(conn.nextSendCmd.bind(conn));
      });

      conn.receiveQueue.push(authentication);
      conn.streamInitSocket.call(conn);
    });
  }

  executePromise(cmdParam, prepare, resolve, reject) {
    const cmd = new Execute(resolve, this._logAndReject.bind(this, reject), this.opts, cmdParam, prepare);
    this.addCommand(cmd);
  }

  batch(cmdParam) {
    if (!cmdParam.sql) {
      const err = Errors.createError(
        'sql parameter is mandatory',
        Errors.ER_UNDEFINED_SQL,
        this.info,
        'HY000',
        null,
        false,
        cmdParam.stack
      );
      if (this.opts.logger.error) this.opts.logger.error(err);
      return Promise.reject(err);
    }
    if (!cmdParam.values) {
      const err = Errors.createError(
        'Batch must have values set',
        Errors.ER_BATCH_WITH_NO_VALUES,
        this.info,
        'HY000',
        cmdParam.sql,
        false,
        cmdParam.stack
      );
      if (this.opts.logger.error) this.opts.logger.error(err);
      return Promise.reject(err);
    }

    return new Promise(this.prepare.bind(this, cmdParam)).then((prepare) => {
      const usePlaceHolder = (cmdParam.opts && cmdParam.opts.namedPlaceholders) || this.opts.namedPlaceholders;
      let vals;
      if (Array.isArray(cmdParam.values)) {
        if (usePlaceHolder) {
          vals = cmdParam.values;
        } else if (Array.isArray(cmdParam.values[0])) {
          vals = cmdParam.values;
        } else if (prepare.parameterCount === 1) {
          vals = [];
          for (let i = 0; i < cmdParam.values.length; i++) {
            vals.push([cmdParam.values[i]]);
          }
        } else {
          vals = [cmdParam.values];
        }
      } else {
        vals = [[cmdParam.values]];
      }
      cmdParam.values = vals;
      let useBulk = this._canUseBulk(vals, cmdParam.opts);
      if (useBulk) {
        return new Promise(this.executeBulkPromise.bind(this, cmdParam, prepare, this.opts));
      } else {
        const executes = [];
        const cmdOpt = Object.assign({}, this.opts, cmdParam.opts);
        for (let i = 0; i < vals.length; i++) {
          executes.push(prepare.execute(vals[i], cmdParam.opts, null, cmdParam.stack));
        }
        return Promise.all(executes)
          .then(
            function (res) {
              if (cmdParam.opts && cmdParam.opts.fullResult) {
                return Promise.resolve(res);
              } else {
                // aggregate results
                let firstResult = res[0];
                if (cmdOpt.metaAsArray) firstResult = firstResult[0];
                if (firstResult instanceof OkPacket) {
                  let affectedRows = 0;
                  const insertId = firstResult.insertId;
                  const warningStatus = firstResult.warningStatus;
                  if (cmdOpt.metaAsArray) {
                    for (let i = 0; i < res.length; i++) {
                      affectedRows += res[i][0].affectedRows;
                    }
                    return Promise.resolve([new OkPacket(affectedRows, insertId, warningStatus), []]);
                  } else {
                    for (let i = 0; i < res.length; i++) {
                      affectedRows += res[i].affectedRows;
                    }
                    return Promise.resolve(new OkPacket(affectedRows, insertId, warningStatus));
                  }
                } else {
                  // results have result-set. example :'INSERT ... RETURNING'
                  // aggregate results
                  if (cmdOpt.metaAsArray) {
                    const rs = [];
                    res.forEach((row) => {
                      rs.push(...row[0]);
                    });
                    return Promise.resolve([rs, res[0][1]]);
                  } else {
                    const rs = [];
                    res.forEach((row) => {
                      rs.push(...row);
                    });
                    Object.defineProperty(rs, 'meta', {
                      value: res[0].meta,
                      writable: true,
                      enumerable: this.opts.metaEnumerable
                    });
                    return Promise.resolve(rs);
                  }
                }
              }
            }.bind(this)
          )
          .finally(() => prepare.close());
      }
    });
  }

  executeBulkPromise(cmdParam, prepare, opts, resolve, reject) {
    const cmd = new BatchBulk(
      (res) => {
        prepare.close();
        return resolve(res);
      },
      function (err) {
        prepare.close();
        if (opts.logger.error) opts.logger.error(err);
        reject(err);
      },
      opts,
      prepare,
      cmdParam
    );
    this.addCommand(cmd);
  }

  /**
   * Send an empty MySQL packet to ensure connection is active, and reset @@wait_timeout
   * @param cmdParam command context
   * @param resolve success function
   * @param reject rejection function
   */
  ping(cmdParam, resolve, reject) {
    if (cmdParam.opts && cmdParam.opts.timeout) {
      if (cmdParam.opts.timeout < 0) {
        const err = Errors.createError(
          'Ping cannot have negative timeout value',
          Errors.ER_BAD_PARAMETER_VALUE,
          this.info,
          '0A000'
        );
        if (this.opts.logger.error) this.opts.logger.error(err);
        reject(err);
        return;
      }
      let tOut = setTimeout(
        function () {
          tOut = undefined;
          const err = Errors.createFatalError('Ping timeout', Errors.ER_PING_TIMEOUT, this.info, '0A000');
          if (this.opts.logger.error) this.opts.logger.error(err);
          // close connection
          this.addCommand = this.addCommandDisabled;
          clearTimeout(this.timeout);
          if (this.status !== Status.CLOSING && this.status !== Status.CLOSED) {
            this.sendQueue.clear();
            this.status = Status.CLOSED;
            this.socket.destroy();
          }
          this.clear();
          reject(err);
        }.bind(this),
        cmdParam.opts.timeout
      );
      this.addCommand(
        new Ping(
          cmdParam,
          () => {
            if (tOut) {
              clearTimeout(tOut);
              resolve();
            }
          },
          (err) => {
            if (this.opts.logger.error) this.opts.logger.error(err);
            clearTimeout(tOut);
            reject(err);
          }
        )
      );
      return;
    }
    this.addCommand(new Ping(cmdParam, resolve, reject));
  }

  /**
   * Send a reset command that will
   * - rollback any open transaction
   * - reset transaction isolation level
   * - reset session variables
   * - delete user variables
   * - remove temporary tables
   * - remove all PREPARE statement
   */
  reset(cmdParam, resolve, reject) {
    if (
      (this.info.isMariaDB() && this.info.hasMinVersion(10, 2, 4)) ||
      (!this.info.isMariaDB() && this.info.hasMinVersion(5, 7, 3))
    ) {
      const conn = this;
      const resetCmd = new Reset(
        cmdParam,
        () => {
          conn.prepareCache.reset();
          let prom = Promise.resolve();
          // re-execute init query / session query timeout
          prom
            .then(conn.handleCharset.bind(conn))
            .then(conn.handleTimezone.bind(conn))
            .then(conn.executeInitQuery.bind(conn))
            .then(conn.executeSessionTimeout.bind(conn))
            .then(resolve)
            .catch(reject);
        },
        reject
      );
      this.addCommand(resetCmd);
      return;
    }

    const err = new Error(
      `Reset command not permitted for server ${this.info.serverVersion.raw} (requires server MariaDB version 10.2.4+ or MySQL 5.7.3+)`
    );
    err.stack = cmdParam.stack;
    if (this.opts.logger.error) this.opts.logger.error(err);
    reject(err);
  }

  /**
   * Indicates the state of the connection as the driver knows it
   * @returns {boolean}
   */
  isValid() {
    return this.status === Status.CONNECTED;
  }

  /**
   * Terminate connection gracefully.
   */
  end(cmdParam, resolve, reject) {
    this.addCommand = this.addCommandDisabled;
    clearTimeout(this.timeout);

    if (this.status < Status.CLOSING && this.status !== Status.NOT_CONNECTED) {
      this.status = Status.CLOSING;
      const ended = () => {
        this.status = Status.CLOSED;
        this.socket.destroy();
        this.socket.unref();
        this.clear();
        this.receiveQueue.clear();
        resolve();
      };
      const quitCmd = new Quit(cmdParam, ended, ended);
      this.sendQueue.push(quitCmd);
      this.receiveQueue.push(quitCmd);
      if (this.sendQueue.length === 1) {
        process.nextTick(this.nextSendCmd.bind(this));
      }
    } else resolve();
  }

  /**
   * Force connection termination by closing the underlying socket and killing server process if any.
   */
  destroy() {
    this.addCommand = this.addCommandDisabled;
    clearTimeout(this.timeout);
    if (this.status < Status.CLOSING) {
      this.status = Status.CLOSING;
      this.sendQueue.clear();
      if (this.receiveQueue.length > 0) {
        //socket is closed, but server may still be processing a huge select
        //only possibility is to kill process by another thread
        //TODO reuse a pool connection to avoid connection creation
        const self = this;

        // relying on IP in place of DNS to ensure using same server
        const remoteAddress = this.socket.remoteAddress;
        const connOption = remoteAddress ? Object.assign({}, this.opts, { host: remoteAddress }) : this.opts;

        const killCon = new Connection(connOption);
        killCon
          .connect()
          .then(() => {
            //*************************************************
            //kill connection
            //*************************************************
            new Promise(killCon.query.bind(killCon, { sql: `KILL ${self.info.threadId}` })).finally((err) => {
              const destroyError = Errors.createFatalError(
                'Connection destroyed, command was killed',
                Errors.ER_CMD_NOT_EXECUTED_DESTROYED,
                self.info
              );
              if (self.opts.logger.error) self.opts.logger.error(destroyError);
              self.socketErrorDispatchToQueries(destroyError);
              if (self.socket) {
                const sok = self.socket;
                process.nextTick(() => {
                  sok.destroy();
                });
              }
              self.status = Status.CLOSED;
              self.clear();
              new Promise(killCon.end.bind(killCon)).catch(() => {});
            });
          })
          .catch(() => {
            //*************************************************
            //failing to create a kill connection, end normally
            //*************************************************
            const ended = () => {
              let sock = self.socket;
              self.clear();
              self.status = Status.CLOSED;
              sock.destroy();
              self.receiveQueue.clear();
            };
            const quitCmd = new Quit(ended, ended);
            self.sendQueue.push(quitCmd);
            self.receiveQueue.push(quitCmd);
            if (self.sendQueue.length === 1) {
              process.nextTick(self.nextSendCmd.bind(self));
            }
          });
      } else {
        this.status = Status.CLOSED;
        this.socket.destroy();
        this.clear();
      }
    }
  }

  pause() {
    this.socket.pause();
  }

  resume() {
    this.socket.resume();
  }

  format(sql, values) {
    const err = Errors.createError(
      '"Connection.format intentionally not implemented. please use Connection.query(sql, values), it will be more secure and faster',
      Errors.ER_NOT_IMPLEMENTED_FORMAT,
      this.info,
      '0A000'
    );
    if (this.opts.logger.error) this.opts.logger.error(err);
    throw err;
  }

  //*****************************************************************
  // additional public methods
  //*****************************************************************

  /**
   * return current connected server version information.
   *
   * @returns {*}
   */
  serverVersion() {
    if (!this.info.serverVersion) {
      const err = new Error('cannot know if server information until connection is established');
      if (this.opts.logger.error) this.opts.logger.error(err);
      throw err;
    }

    return this.info.serverVersion.raw;
  }

  /**
   * Change option "debug" during connection.
   * @param val   debug value
   */
  debug(val) {
    if (typeof val === 'boolean') {
      if (val && !this.opts.logger.network) this.opts.logger.network = console.log;
    } else if (typeof val === 'function') {
      this.opts.logger.network = val;
    }
    this.opts.emit('debug', val);
  }

  debugCompress(val) {
    if (val) {
      if (typeof val === 'boolean') {
        this.opts.debugCompress = val;
        if (val && !this.opts.logger.network) this.opts.logger.network = console.log;
      } else if (typeof val === 'function') {
        this.opts.debugCompress = true;
        this.opts.logger.network = val;
      }
    } else this.opts.debugCompress = false;
  }

  //*****************************************************************
  // internal public testing methods
  //*****************************************************************

  get __tests() {
    return new TestMethods(this.info.collation, this.socket);
  }

  //*****************************************************************
  // internal methods
  //*****************************************************************

  /**
   * Use multiple COM_STMT_EXECUTE or COM_STMT_BULK_EXECUTE
   *
   * @param values current batch values
   * @param _options batch option
   * @return {boolean} indicating if can use bulk command
   */
  _canUseBulk(values, _options) {
    if (_options && _options.fullResult) return false;
    // not using info.isMariaDB() directly in case of callback use,
    // without connection being completely finished.
    if (
      this.info.serverVersion &&
      this.info.serverVersion.mariaDb &&
      this.info.hasMinVersion(10, 2, 7) &&
      this.opts.bulk &&
      (this.info.serverCapabilities & Capabilities.MARIADB_CLIENT_STMT_BULK_OPERATIONS) > 0n
    ) {
      //ensure that there is no stream object
      if (values !== undefined) {
        if (!this.opts.namedPlaceholders) {
          //ensure that all parameters have same length
          //single array is considered as an array of single element.
          const paramLen = Array.isArray(values[0]) ? values[0].length : values[0] ? 1 : 0;
          if (paramLen === 0) return false;
          for (let r = 0; r < values.length; r++) {
            let row = values[r];
            if (!Array.isArray(row)) row = [row];
            if (paramLen !== row.length) {
              return false;
            }
            // streaming data not permitted
            for (let j = 0; j < paramLen; j++) {
              const val = row[j];
              if (
                val != null &&
                typeof val === 'object' &&
                typeof val.pipe === 'function' &&
                typeof val.read === 'function'
              ) {
                return false;
              }
            }
          }
        } else {
          for (let r = 0; r < values.length; r++) {
            let row = values[r];
            const keys = Object.keys(row);
            for (let j = 0; j < keys.length; j++) {
              const val = row[keys[j]];
              if (
                val != null &&
                typeof val === 'object' &&
                typeof val.pipe === 'function' &&
                typeof val.read === 'function'
              ) {
                return false;
              }
            }
          }
        }
      }
      return true;
    }
    return false;
  }

  executeSessionVariableQuery() {
    if (this.opts.sessionVariables) {
      const values = [];
      let sessionQuery = 'set ';
      let keys = Object.keys(this.opts.sessionVariables);
      if (keys.length > 0) {
        for (let k = 0; k < keys.length; ++k) {
          sessionQuery += (k !== 0 ? ',' : '') + '@@' + keys[k].replace(/[^a-z0-9_]/gi, '') + '=?';
          values.push(this.opts.sessionVariables[keys[k]]);
        }

        return new Promise(this.query.bind(this, new CommandParameter(sessionQuery, values))).catch((initialErr) => {
          const err = Errors.createFatalError(
            `Error setting session variable (value ${JSON.stringify(this.opts.sessionVariables)}). Error: ${
              initialErr.message
            }`,
            Errors.ER_SETTING_SESSION_ERROR,
            this.info,
            '08S01',
            sessionQuery
          );
          if (this.opts.logger.error) this.opts.logger.error(err);
          return Promise.reject(err);
        });
      }
    }
    return Promise.resolve();
  }

  /**
   * set charset to utf8
   * @returns {Promise<void>}
   * @private
   */
  handleCharset() {
    if (this.opts.collation) {
      if (this.opts.collation.index < 255) return Promise.resolve();
      const charset =
        this.opts.collation.charset === 'utf8' && this.opts.collation.maxLength === 4
          ? 'utf8mb4'
          : this.opts.collation.charset;
      return new Promise(
        this.query.bind(this, new CommandParameter(`SET NAMES ${charset} COLLATE ${this.opts.collation.name}`))
      );
    }

    // MXS-4635: server can some information directly on first Ok_Packet, like not truncated collation
    // in this case, avoid useless SET NAMES utf8mb4 command
    if (!this.opts.charset && this.info.collation.charset === 'utf8' && this.info.collation.maxLength === 4) {
      return Promise.resolve();
    }

    return new Promise(
      this.query.bind(this, new CommandParameter(`SET NAMES ${this.opts.charset ? this.opts.charset : 'utf8mb4'}`))
    );
  }

  /**
   * Asking server timezone if not set in case of 'auto'
   * @returns {Promise<void>}
   * @private
   */
  handleTimezone() {
    const conn = this;
    if (this.opts.timezone === 'local') this.opts.timezone = undefined;
    if (this.opts.timezone === 'auto') {
      return new Promise(
        this.query.bind(this, new CommandParameter('SELECT @@system_time_zone stz, @@time_zone tz'))
      ).then((res) => {
        const serverTimezone = res[0].tz === 'SYSTEM' ? res[0].stz : res[0].tz;
        const localTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (serverTimezone === localTz || convertFixedTime(serverTimezone, conn) === convertFixedTime(localTz, conn)) {
          //server timezone is identical to client tz, skipping setting
          this.opts.timezone = localTz;
          return Promise.resolve();
        }
        return this._setSessionTimezone(convertFixedTime(localTz, conn));
      });
    }

    if (this.opts.timezone) {
      return this._setSessionTimezone(convertFixedTime(this.opts.timezone, conn));
    }
    return Promise.resolve();
  }

  _setSessionTimezone(tz) {
    return new Promise(this.query.bind(this, new CommandParameter('SET time_zone=?', [tz]))).catch((err) => {
      const er = Errors.createFatalError(
        `setting timezone '${tz}' fails on server.\n look at https://mariadb.com/kb/en/mysql_tzinfo_to_sql/ to load IANA timezone. `,
        Errors.ER_WRONG_IANA_TIMEZONE,
        this.info
      );
      if (this.opts.logger.error) this.opts.logger.error(er);
      return Promise.reject(er);
    });
  }

  checkServerVersion() {
    if (!this.opts.forceVersionCheck) {
      return Promise.resolve();
    }
    return new Promise(this.query.bind(this, new CommandParameter('SELECT @@VERSION AS v'))).then(
      function (res) {
        this.info.serverVersion.raw = res[0].v;
        this.info.serverVersion.mariaDb = this.info.serverVersion.raw.includes('MariaDB');
        ConnectionInformation.parseVersionString(this.info);
        return Promise.resolve();
      }.bind(this)
    );
  }

  executeInitQuery() {
    if (this.opts.initSql) {
      const initialArr = Array.isArray(this.opts.initSql) ? this.opts.initSql : [this.opts.initSql];
      const initialPromises = [];
      initialArr.forEach((sql) => {
        initialPromises.push(new Promise(this.query.bind(this, new CommandParameter(sql))));
      });

      return Promise.all(initialPromises).catch((initialErr) => {
        const err = Errors.createFatalError(
          `Error executing initial sql command: ${initialErr.message}`,
          Errors.ER_INITIAL_SQL_ERROR,
          this.info
        );
        if (this.opts.logger.error) this.opts.logger.error(err);
        return Promise.reject(err);
      });
    }
    return Promise.resolve();
  }

  executeSessionTimeout() {
    if (this.opts.queryTimeout) {
      if (this.info.isMariaDB() && this.info.hasMinVersion(10, 1, 2)) {
        const query = `SET max_statement_time=${this.opts.queryTimeout / 1000}`;
        new Promise(this.query.bind(this, new CommandParameter(query))).catch(
          function (initialErr) {
            const err = Errors.createFatalError(
              `Error setting session queryTimeout: ${initialErr.message}`,
              Errors.ER_INITIAL_TIMEOUT_ERROR,
              this.info,
              '08S01',
              query
            );
            if (this.opts.logger.error) this.opts.logger.error(err);
            return Promise.reject(err);
          }.bind(this)
        );
      } else {
        const err = Errors.createError(
          `Can only use queryTimeout for MariaDB server after 10.1.1. queryTimeout value: ${this.opts.queryTimeout}`,
          Errors.ER_TIMEOUT_NOT_SUPPORTED,
          this.info,
          'HY000',
          this.opts.queryTimeout
        );
        if (this.opts.logger.error) this.opts.logger.error(err);
        return Promise.reject(err);
      }
    }
    return Promise.resolve();
  }

  getSocket() {
    return this.socket;
  }

  /**
   * Initialize socket and associate events.
   * @private
   */
  streamInitSocket() {
    if (this.opts.connectTimeout) {
      this.timeout = setTimeout(this.connectTimeoutReached.bind(this), this.opts.connectTimeout, Date.now());
    }
    if (this.opts.socketPath) {
      this.socket = Net.connect(this.opts.socketPath);
    } else if (this.opts.stream) {
      if (typeof this.opts.stream === 'function') {
        const tmpSocket = this.opts.stream(
          function (err, stream) {
            if (err) {
              this.authFailHandler(err);
              return;
            }
            this.socket = stream ? stream : Net.connect(this.opts.port, this.opts.host);
            this.socketInit();
          }.bind(this)
        );
        if (tmpSocket) {
          this.socket = tmpSocket;
          this.socketInit();
        }
      } else {
        this.authFailHandler(
          Errors.createError(
            'stream option is not a function. stream must be a function with (error, callback) parameter',
            Errors.ER_BAD_PARAMETER_VALUE,
            this.info
          )
        );
      }
      return;
    } else {
      this.socket = Net.connect(this.opts.port, this.opts.host);
      this.socket.setNoDelay(true);
    }
    this.socketInit();
  }

  socketInit() {
    this.socket.on('data', this.streamIn.onData.bind(this.streamIn));
    this.socket.on('error', this.socketErrorHandler.bind(this));
    this.socket.on('end', this.socketErrorHandler.bind(this));
    this.socket.on(
      'connect',
      function () {
        if (this.status === Status.CONNECTING) {
          this.status = Status.AUTHENTICATING;
          this.socket.setTimeout(this.opts.socketTimeout, this.socketTimeoutReached.bind(this));
          this.socket.setNoDelay(true);

          // keep alive for socket. This won't reset server wait_timeout use pool option idleTimeout for that
          if (this.opts.keepAliveDelay) {
            this.socket.setKeepAlive(true, this.opts.keepAliveDelay);
          }
        }
      }.bind(this)
    );

    this.socket.writeBuf = (buf) => this.socket.write(buf);
    this.socket.flush = () => {};
    this.streamOut.setStream(this.socket);
  }

  /**
   * Authentication success result handler.
   *
   * @private
   */
  authSucceedHandler() {
    //enable packet compression according to option
    if (this.opts.compress) {
      if (this.info.serverCapabilities & Capabilities.COMPRESS) {
        this.streamOut.setStream(new CompressionOutputStream(this.socket, this.opts, this.info));
        this.streamIn = new CompressionInputStream(this.streamIn, this.receiveQueue, this.opts, this.info);
        this.socket.removeAllListeners('data');
        this.socket.on('data', this.streamIn.onData.bind(this.streamIn));
      } else if (this.opts.logger.error) {
        this.opts.logger.error(
          Errors.createError(
            "connection is configured to use packet compression, but the server doesn't have this capability",
            Errors.ER_COMPRESSION_NOT_SUPPORTED,
            this.info
          )
        );
      }
    }

    this.addCommand = this.opts.pipelining ? this.addCommandEnablePipeline : this.addCommandEnable;
    const conn = this;
    this.status = Status.INIT_CMD;
    this.executeSessionVariableQuery()
      .then(conn.handleCharset.bind(conn))
      .then(this.handleTimezone.bind(this))
      .then(this.checkServerVersion.bind(this))
      .then(this.executeInitQuery.bind(this))
      .then(this.executeSessionTimeout.bind(this))
      .then(() => {
        clearTimeout(this.timeout);
        conn.status = Status.CONNECTED;
        process.nextTick(conn.connectResolveFct, conn);

        const commands = conn.waitingAuthenticationQueue.toArray();
        commands.forEach((cmd) => {
          conn.addCommand(cmd);
        });
        conn.waitingAuthenticationQueue = null;

        conn.connectRejectFct = null;
        conn.connectResolveFct = null;
      })
      .catch((err) => {
        if (!err.fatal) {
          const res = () => {
            conn.authFailHandler.call(conn, err);
          };
          conn.end(res, res);
        } else {
          conn.authFailHandler.call(conn, err);
        }
        return Promise.reject(err);
      });
  }

  /**
   * Authentication failed result handler.
   *
   * @private
   */
  authFailHandler(err) {
    clearTimeout(this.timeout);
    if (this.connectRejectFct) {
      if (this.opts.logger.error) this.opts.logger.error(err);
      //remove handshake command
      this.receiveQueue.shift();
      this.fatalError(err, true);

      process.nextTick(this.connectRejectFct, err);
      this.connectRejectFct = null;
    }
  }

  /**
   * Create TLS socket and associate events.
   *
   * @param callback  callback function when done
   * @private
   */
  createSecureContext(callback) {
    const sslOption = Object.assign({}, this.opts.ssl, {
      servername: this.opts.host,
      socket: this.socket
    });

    try {
      const secureSocket = tls.connect(sslOption, callback);

      secureSocket.on('data', this.streamIn.onData.bind(this.streamIn));
      secureSocket.on('error', this.socketErrorHandler.bind(this));
      secureSocket.on('end', this.socketErrorHandler.bind(this));
      secureSocket.writeBuf = (buf) => secureSocket.write(buf);
      secureSocket.flush = () => {};

      this.socket.removeAllListeners('data');
      this.socket = secureSocket;

      this.streamOut.setStream(secureSocket);
    } catch (err) {
      this.socketErrorHandler(err);
    }
  }

  /**
   * Handle packet when no packet is expected.
   * (there can be an ERROR packet send by server/proxy to inform that connection is ending).
   *
   * @param packet  packet
   * @private
   */
  unexpectedPacket(packet) {
    if (packet && packet.peek() === 0xff) {
      //can receive unexpected error packet from server/proxy
      //to inform that connection is closed (usually by timeout)
      let err = packet.readError(this.info);
      if (err.fatal && this.status < Status.CLOSING) {
        this.emit('error', err);
        if (this.opts.logger.error) this.opts.logger.error(err);
        this.end(
          () => {},
          () => {}
        );
      }
    } else if (this.status < Status.CLOSING) {
      const err = Errors.createFatalError(
        `receiving packet from server without active commands\nconn:${this.info.threadId ? this.info.threadId : -1}(${
          packet.pos
        },${packet.end})\n${Utils.log(this.opts, packet.buf, packet.pos, packet.end)}`,
        Errors.ER_UNEXPECTED_PACKET,
        this.info
      );
      if (this.opts.logger.error) this.opts.logger.error(err);
      this.emit('error', err);
      this.destroy();
    }
  }

  /**
   * Handle connection timeout.
   *
   * @private
   */
  connectTimeoutReached(initialConnectionTime) {
    this.timeout = null;
    const handshake = this.receiveQueue.peekFront();
    const err = Errors.createFatalError(
      `Connection timeout: failed to create socket after ${Date.now() - initialConnectionTime}ms`,
      Errors.ER_CONNECTION_TIMEOUT,
      this.info,
      '08S01',
      null,
      handshake ? handshake.stack : null
    );
    if (this.opts.logger.error) this.opts.logger.error(err);
    this.authFailHandler(err);
  }

  /**
   * Handle socket timeout.
   *
   * @private
   */
  socketTimeoutReached() {
    const err = Errors.createFatalError('socket timeout', Errors.ER_SOCKET_TIMEOUT, this.info);
    if (this.opts.logger.error) this.opts.logger.error(err);
    this.fatalError(err, true);
  }

  /**
   * Add command to waiting queue until authentication.
   *
   * @param cmd         command
   * @private
   */
  addCommandQueue(cmd) {
    this.waitingAuthenticationQueue.push(cmd);
  }

  /**
   * Add command to command sending and receiving queue.
   *
   * @param cmd         command
   * @private
   */
  addCommandEnable(cmd) {
    cmd.once('end', this._sendNextCmdImmediate.bind(this));

    //send immediately only if no current active receiver
    if (this.sendQueue.isEmpty() && this.receiveQueue.isEmpty()) {
      this.receiveQueue.push(cmd);
      cmd.start(this.streamOut, this.opts, this.info);
    } else {
      this.receiveQueue.push(cmd);
      this.sendQueue.push(cmd);
    }
  }

  /**
   * Add command to command sending and receiving queue using pipelining
   *
   * @param cmd         command
   * @private
   */
  addCommandEnablePipeline(cmd) {
    cmd.once('send_end', this._sendNextCmdImmediate.bind(this));

    this.receiveQueue.push(cmd);
    if (this.sendQueue.isEmpty()) {
      cmd.start(this.streamOut, this.opts, this.info);
      if (cmd.sending) {
        this.sendQueue.push(cmd);
        cmd.prependOnceListener('send_end', this.sendQueue.shift.bind(this.sendQueue));
      }
    } else {
      this.sendQueue.push(cmd);
    }
  }

  /**
   * Replacing command when connection is closing or closed to send a proper error message.
   *
   * @param cmd         command
   * @private
   */
  addCommandDisabled(cmd) {
    const err = cmd.throwNewError(
      'Cannot execute new commands: connection closed',
      true,
      this.info,
      '08S01',
      Errors.ER_CMD_CONNECTION_CLOSED
    );
    if (this.opts.logger.error) this.opts.logger.error(err);
  }

  /**
   * Handle socket error.
   *
   * @param err               socket error
   * @private
   */
  socketErrorHandler(err) {
    if (this.status >= Status.CLOSING) return;
    if (this.socket) {
      this.socket.writeBuf = () => {};
      this.socket.flush = () => {};
    }

    //socket has been ended without error
    if (!err) {
      err = Errors.createFatalError(
        'socket has unexpectedly been closed',
        Errors.ER_SOCKET_UNEXPECTED_CLOSE,
        this.info
      );
    } else {
      err.fatal = true;
      err.sqlState = 'HY000';
    }

    switch (this.status) {
      case Status.CONNECTING:
      case Status.AUTHENTICATING:
        const currentCmd = this.receiveQueue.peekFront();
        if (currentCmd && currentCmd.stack && err) {
          err.stack += '\n From event:\n' + currentCmd.stack.substring(currentCmd.stack.indexOf('\n') + 1);
        }
        this.authFailHandler(err);
        break;

      default:
        this.fatalError(err, false);
    }
  }

  /**
   * Fatal unexpected error : closing connection, and throw exception.
   */
  fatalError(err, avoidThrowError) {
    if (this.status >= Status.CLOSING) {
      this.socketErrorDispatchToQueries(err);
      return;
    }
    const mustThrowError = this.status !== Status.CONNECTING;
    this.status = Status.CLOSING;

    //prevent executing new commands
    this.addCommand = this.addCommandDisabled;

    if (this.socket) {
      this.socket.removeAllListeners('error');
      this.socket.removeAllListeners('timeout');
      this.socket.removeAllListeners('close');
      this.socket.removeAllListeners('data');
      if (!this.socket.destroyed) this.socket.destroy();
      this.socket = undefined;
    }
    this.status = Status.CLOSED;

    const errorThrownByCmd = this.socketErrorDispatchToQueries(err);
    if (mustThrowError) {
      if (this.opts.logger.error) this.opts.logger.error(err);
      if (this.listenerCount('error') > 0) {
        this.emit('error', err);
        this.emit('end');
        this.clear();
      } else {
        this.emit('end');
        this.clear();
        //error will be thrown if no error listener and no command did throw the exception
        if (!avoidThrowError && !errorThrownByCmd) throw err;
      }
    } else {
      this.clear();
    }
  }

  /**
   * Dispatch fatal error to current running queries.
   *
   * @param err        the fatal error
   * @return {boolean} return if error has been relayed to queries
   */
  socketErrorDispatchToQueries(err) {
    let receiveCmd;
    let errorThrownByCmd = false;
    while ((receiveCmd = this.receiveQueue.shift())) {
      if (receiveCmd && receiveCmd.onPacketReceive) {
        errorThrownByCmd = true;
        setImmediate(receiveCmd.throwError.bind(receiveCmd, err, this.info));
      }
    }
    return errorThrownByCmd;
  }

  /**
   * Will send next command in queue if any.
   *
   * @private
   */
  nextSendCmd() {
    let sendCmd;
    if ((sendCmd = this.sendQueue.shift())) {
      if (sendCmd.sending) {
        this.sendQueue.unshift(sendCmd);
      } else {
        sendCmd.start(this.streamOut, this.opts, this.info);
        if (sendCmd.sending) {
          this.sendQueue.unshift(sendCmd);
          sendCmd.prependOnceListener('send_end', this.sendQueue.shift.bind(this.sendQueue));
        }
      }
    }
  }

  /**
   * Change transaction state.
   *
   * @param cmdParam command parameter
   * @param resolve success function to call
   * @param reject error function to call
   * @private
   */
  changeTransaction(cmdParam, resolve, reject) {
    //if command in progress, driver cannot rely on status and must execute query
    if (this.status >= Status.CLOSING) {
      const err = Errors.createFatalError(
        'Cannot execute new commands: connection closed',
        Errors.ER_CMD_CONNECTION_CLOSED,
        this.info,
        '08S01',
        cmdParam.sql
      );
      if (this.opts.logger.error) this.opts.logger.error(err);
      reject(err);
      return;
    }

    //Command in progress => must execute query
    //or if no command in progress, can rely on status to know if query is needed
    if (this.receiveQueue.peekFront() || this.info.status & ServerStatus.STATUS_IN_TRANS) {
      const cmd = new Query(
        resolve,
        (err) => {
          if (this.opts.logger.error) this.opts.logger.error(err);
          reject(err);
        },
        this.opts,
        cmdParam
      );
      this.addCommand(cmd);
    } else resolve();
  }

  changeUser(cmdParam, resolve, reject) {
    if (!this.info.isMariaDB()) {
      const err = Errors.createError(
        'method changeUser not available for MySQL server due to Bug #83472',
        Errors.ER_MYSQL_CHANGE_USER_BUG,
        this.info,
        '0A000'
      );
      if (this.opts.logger.error) this.opts.logger.error(err);
      reject(err);
      return;
    }
    if (this.status < Status.CLOSING) {
      this.addCommand = this.addCommandEnable;
    }
    let conn = this;
    if (cmdParam.opts && cmdParam.opts.collation && typeof cmdParam.opts.collation === 'string') {
      const val = cmdParam.opts.collation.toUpperCase();
      cmdParam.opts.collation = Collations.fromName(cmdParam.opts.collation.toUpperCase());
      if (cmdParam.opts.collation === undefined) return reject(new RangeError(`Unknown collation '${val}'`));
    }

    this.addCommand(
      new ChangeUser(
        cmdParam,
        this.opts,
        (res) => {
          if (conn.status < Status.CLOSING && conn.opts.pipelining) conn.addCommand = conn.addCommandEnablePipeline;
          if (cmdParam.opts && cmdParam.opts.collation) conn.opts.collation = cmdParam.opts.collation;
          conn
            .handleCharset()
            .then(() => {
              if (cmdParam.opts && cmdParam.opts.collation) {
                conn.info.collation = cmdParam.opts.collation;
                conn.opts.emit('collation', cmdParam.opts.collation);
              }
              resolve(res);
            })
            .catch((err) => {
              const res = () => conn.authFailHandler.call(conn, err);
              if (!err.fatal) {
                conn.end(res, res);
              } else {
                res();
              }
              reject(err);
            });
        },
        this.authFailHandler.bind(this, reject),
        this.getSocket.bind(this)
      )
    );
  }

  query(cmdParam, resolve, reject) {
    if (!cmdParam.sql)
      return reject(
        Errors.createError(
          'sql parameter is mandatory',
          Errors.ER_UNDEFINED_SQL,
          this.info,
          'HY000',
          null,
          false,
          cmdParam.stack
        )
      );
    const cmd = new Query(
      resolve,
      (err) => {
        if (this.opts.logger.error) this.opts.logger.error(err);
        reject(err);
      },
      this.opts,
      cmdParam
    );
    this.addCommand(cmd);
  }

  prepare(cmdParam, resolve, reject) {
    if (!cmdParam.sql)
      return reject(Errors.createError('sql parameter is mandatory', Errors.ER_UNDEFINED_SQL, this.info, 'HY000'));
    if (this.prepareCache && (this.sendQueue.isEmpty() || !this.receiveQueue.peekFront())) {
      // no command in queue, database is then considered ok, and cache can be search right now
      const cachedPrepare = this.prepareCache.get(cmdParam.sql);
      if (cachedPrepare) {
        return resolve(cachedPrepare);
      }
    }

    const cmd = new Prepare(
      resolve,
      (err) => {
        if (this.opts.logger.error) this.opts.logger.error(err);
        reject(err);
      },
      this.opts,
      cmdParam,
      this
    );
    this.addCommand(cmd);
  }

  importFile(cmdParam, resolve, reject) {
    const conn = this;
    if (!cmdParam || !cmdParam.file) {
      return reject(
        Errors.createError(
          'SQL file parameter is mandatory',
          Errors.ER_MISSING_SQL_PARAMETER,
          conn.info,
          'HY000',
          null,
          false,
          cmdParam.stack
        )
      );
    }

    const prevAddCommand = this.addCommand.bind(conn);

    this.waitingAuthenticationQueue = new Queue();
    this.addCommand = this.addCommandQueue;
    const tmpQuery = function (sql, resolve, reject) {
      const cmd = new Query(
        resolve,
        (err) => {
          if (conn.opts.logger.error) conn.opts.logger.error(err);
          reject(err);
        },
        conn.opts,
        new CommandParameter(sql, null, {})
      );
      prevAddCommand(cmd);
    };

    let prevDatabase = null;
    return (
      cmdParam.skipDbCheck ? Promise.resolve() : new Promise(tmpQuery.bind(conn, 'SELECT DATABASE() as db'))
    ).then((res) => {
      prevDatabase = res ? res[0].db : null;
      if (
        (cmdParam.skipDbCheck && !conn.opts.database) ||
        (!cmdParam.skipDbCheck && !cmdParam.database && !prevDatabase)
      ) {
        return reject(
          Errors.createError(
            'Database parameter is not set and no database is selected',
            Errors.ER_MISSING_DATABASE_PARAMETER,
            conn.info,
            'HY000',
            null,
            false,
            cmdParam.stack
          )
        );
      }
      const searchDbPromise = cmdParam.database
        ? new Promise(tmpQuery.bind(conn, `USE \`${cmdParam.database.replace(/`/gi, '``')}\``))
        : Promise.resolve();
      return searchDbPromise.then(() => {
        const endingFunction = () => {
          if (conn.status < Status.CLOSING) {
            conn.addCommand = conn.addCommandEnable.bind(conn);
            if (conn.status < Status.CLOSING && conn.opts.pipelining) {
              conn.addCommand = conn.addCommandEnablePipeline.bind(conn);
            }
            const commands = conn.waitingAuthenticationQueue.toArray();
            commands.forEach((cmd) => conn.addCommand(cmd));
            conn.waitingAuthenticationQueue = null;
          }
        };
        return fsPromises
          .open(cmdParam.file, 'r')
          .then(async (fd) => {
            const buf = {
              buffer: Buffer.allocUnsafe(16384),
              offset: 0,
              end: 0
            };

            const queryPromises = [];
            let cmdError = null;
            while (!cmdError) {
              try {
                const res = await fd.read(buf.buffer, buf.end, buf.buffer.length - buf.end, null);
                if (res.bytesRead == 0) {
                  // end of file reached.
                  fd.close().catch(() => {});
                  if (cmdError) {
                    endingFunction();
                    reject(cmdError);
                    return;
                  }
                  await Promise.allSettled(queryPromises)
                    .then(() => {
                      if (!cmdParam.skipDbCheck && cmdParam.database && cmdParam.database != prevDatabase) {
                        return new Promise(tmpQuery.bind(conn, `USE \`${prevDatabase.replace(/`/gi, '``')}\``));
                      }
                      return Promise.resolve();
                    })
                    .then(() => {
                      endingFunction();
                      if (cmdError) {
                        reject(cmdError);
                      }
                      resolve();
                    })
                    .catch((err) => {
                      endingFunction();
                      reject(err);
                    });
                  return;
                } else {
                  buf.end += res.bytesRead;
                  const queries = Parse.parseQueries(buf);
                  const queryIntermediatePromise = queries.flatMap((element) => {
                    return new Promise(tmpQuery.bind(conn, element)).catch((err) => {
                      cmdError = err;
                    });
                  });

                  queryPromises.push(...queryIntermediatePromise);
                  if (buf.offset == buf.end) {
                    buf.offset = 0;
                    buf.end = 0;
                  } else {
                    // ensure that buffer can at least read 8k bytes,
                    // either by copying remaining data on used part or growing buffer
                    if (buf.offset > 8192) {
                      // reuse buffer, copying remaining data begin of buffer
                      buf.buffer.copy(buf.buffer, 0, buf.offset, buf.end);
                      buf.end -= buf.offset;
                      buf.offset = 0;
                    } else if (buf.buffer.length - buf.end < 8192) {
                      // grow buffer
                      const tmpBuf = Buffer.allocUnsafe(buf.buffer.length << 1);
                      buf.buffer.copy(tmpBuf, 0, buf.offset, buf.end);
                      buf.buffer = tmpBuf;
                      buf.end -= buf.offset;
                      buf.offset = 0;
                    }
                  }
                }
              } catch (e) {
                fd.close().catch(() => {});
                endingFunction();
                Promise.allSettled(queryPromises).catch(() => {});
                return reject(
                  Errors.createError(
                    e.message,
                    Errors.ER_SQL_FILE_ERROR,
                    conn.info,
                    'HY000',
                    null,
                    false,
                    cmdParam.stack
                  )
                );
              }
            }
            if (cmdError) {
              endingFunction();
              reject(cmdError);
            }
          })
          .catch((err) => {
            endingFunction();
            if (err.code === 'ENOENT') {
              return reject(
                Errors.createError(
                  `SQL file parameter '${cmdParam.file}' doesn't exists`,
                  Errors.ER_MISSING_SQL_FILE,
                  conn.info,
                  'HY000',
                  null,
                  false,
                  cmdParam.stack
                )
              );
            }
            return reject(
              Errors.createError(err.message, Errors.ER_SQL_FILE_ERROR, conn.info, 'HY000', null, false, cmdParam.stack)
            );
          });
      });
    });
  }

  /**
   * Clearing connection variables when ending.
   *
   * @private
   */
  clear() {
    this.sendQueue.clear();
    this.opts.removeAllListeners();
    this.streamOut = undefined;
    this.socket = undefined;
  }

  get threadId() {
    return this.info ? this.info.threadId : null;
  }

  _sendNextCmdImmediate() {
    if (!this.sendQueue.isEmpty()) {
      setImmediate(this.nextSendCmd.bind(this));
    }
  }

  _closePrepare(prepareResultPacket) {
    this.addCommand(
      new ClosePrepare(
        new CommandParameter(null, null, null, null),
        () => {},
        () => {},
        prepareResultPacket
      )
    );
  }

  _logAndReject(reject, err) {
    if (this.opts.logger.error) this.opts.logger.error(err);
    reject(err);
  }
}

class TestMethods {
  #collation;
  #socket;

  constructor(collation, socket) {
    this.#collation = collation;
    this.#socket = socket;
  }

  getCollation() {
    return this.#collation;
  }

  getSocket() {
    return this.#socket;
  }
}

module.exports = Connection;
