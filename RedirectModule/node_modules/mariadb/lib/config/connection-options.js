//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Collations = require('../const/collations.js');
const urlFormat = /mariadb:\/\/(([^/@:]+)?(:([^/]+))?@)?(([^/:]+)(:([0-9]+))?)\/([^?]+)(\?(.*))?$/;

/**
 * Default option similar to mysql driver.
 * known differences
 * - no queryFormat option. Permitting client to parse is a security risk. Best is to give SQL + parameters
 *   Only possible Objects are :
 *   - Buffer
 *   - Date
 *   - Object that implement toSqlString function
 *   - JSON object
 * + rowsAsArray (in mysql2) permit to have rows by index, not by name. Avoiding to parsing metadata string => faster
 */
class ConnectionOptions {
  constructor(opts) {
    if (typeof opts === 'string') {
      opts = ConnectionOptions.parse(opts);
    }

    if (!opts) opts = {};
    this.host = opts.host || 'localhost';
    this.port = opts.port || 3306;
    this.keepEof = opts.keepEof || false;
    this.user = opts.user || process.env.USERNAME;
    this.password = opts.password;
    this.database = opts.database;
    this.stream = opts.stream;

    // log
    this.debug = opts.debug || false;
    this.debugCompress = opts.debugCompress || false;
    this.debugLen = opts.debugLen || 256;

    if (opts.logger) {
      if (typeof opts.logger === 'function') {
        this.logger = {
          network: opts.logger,
          query: opts.logger,
          error: opts.logger,
          warning: opts.logger,
          logParam: true
        };
      } else {
        this.logger = {
          network: opts.logger.network,
          query: opts.logger.query,
          error: opts.logger.error,
          warning: opts.logger.warning || console.log,
          logParam: opts.logger.logParam == null ? true : opts.logger.logParam
        };
      }
    } else {
      this.logger = { network: null, query: null, error: null, warning: console.log, logParam: false };
      if ((this.debug || this.debugCompress) && !this.logger.network) {
        this.logger.network = console.log;
      }
    }
    this.debug = !!this.logger.network;

    if (opts.charset && typeof opts.charset === 'string') {
      this.collation = Collations.fromCharset(opts.charset.toLowerCase());
      if (this.collation === undefined) {
        this.collation = Collations.fromName(opts.charset.toUpperCase());
        if (this.collation !== undefined) {
          this.logger.warning(
            "warning: please use option 'collation' " +
              "in replacement of 'charset' when using a collation name ('" +
              opts.charset +
              "')\n" +
              "(collation looks like 'UTF8MB4_UNICODE_CI', charset like 'utf8')."
          );
        } else {
          this.charset = opts.charset;
        }
      }
    } else if (opts.collation && typeof opts.collation === 'string') {
      this.collation = Collations.fromName(opts.collation.toUpperCase());
      if (this.collation === undefined) throw new RangeError("Unknown collation '" + opts.collation + "'");
    } else {
      this.collation = opts.charsetNumber ? Collations.fromIndex(opts.charsetNumber) : undefined;
    }

    // connection options
    this.initSql = opts.initSql;
    this.connectTimeout = opts.connectTimeout === undefined ? 1000 : opts.connectTimeout;
    this.connectAttributes = opts.connectAttributes || false;
    this.compress = opts.compress || false;
    this.rsaPublicKey = opts.rsaPublicKey;
    this.cachingRsaPublicKey = opts.cachingRsaPublicKey;
    this.allowPublicKeyRetrieval = opts.allowPublicKeyRetrieval || false;
    this.forceVersionCheck = opts.forceVersionCheck || false;
    this.maxAllowedPacket = opts.maxAllowedPacket;
    this.permitConnectionWhenExpired = opts.permitConnectionWhenExpired || false;
    this.pipelining = opts.pipelining;
    this.timezone = opts.timezone || 'local';
    this.socketPath = opts.socketPath;
    this.sessionVariables = opts.sessionVariables;
    this.infileStreamFactory = opts.infileStreamFactory;
    this.ssl = opts.ssl;
    if (opts.ssl) {
      if (typeof opts.ssl !== 'boolean' && typeof opts.ssl !== 'string') {
        this.ssl.rejectUnauthorized = opts.ssl.rejectUnauthorized !== false;
      }
    }

    // socket
    this.queryTimeout = opts.queryTimeout === undefined ? 0 : opts.queryTimeout;
    this.socketTimeout = opts.socketTimeout === undefined ? 0 : opts.socketTimeout;
    this.keepAliveDelay = opts.keepAliveDelay === undefined ? 0 : opts.keepAliveDelay;

    this.trace = opts.trace || false;

    // result-set
    this.checkDuplicate = opts.checkDuplicate === undefined ? true : opts.checkDuplicate;
    this.dateStrings = opts.dateStrings || false;
    this.foundRows = opts.foundRows === undefined || opts.foundRows;
    this.metaAsArray = opts.metaAsArray || false;
    this.metaEnumerable = opts.metaEnumerable || false;
    this.multipleStatements = opts.multipleStatements || false;
    this.namedPlaceholders = opts.namedPlaceholders || false;
    this.nestTables = opts.nestTables;
    this.autoJsonMap = opts.autoJsonMap === undefined ? true : opts.autoJsonMap;
    this.bitOneIsBoolean = opts.bitOneIsBoolean === undefined ? true : opts.bitOneIsBoolean;
    this.arrayParenthesis = opts.arrayParenthesis || false;
    this.permitSetMultiParamEntries = opts.permitSetMultiParamEntries || false;
    this.rowsAsArray = opts.rowsAsArray || false;
    this.typeCast = opts.typeCast;
    if (this.typeCast !== undefined && typeof this.typeCast !== 'function') {
      this.typeCast = undefined;
    }
    this.bulk = opts.bulk === undefined || opts.bulk;
    this.checkNumberRange = opts.checkNumberRange || false;

    // coherence check
    if (opts.pipelining === undefined) {
      this.permitLocalInfile = opts.permitLocalInfile || false;
      this.pipelining = !this.permitLocalInfile;
    } else {
      this.pipelining = opts.pipelining;
      if (opts.permitLocalInfile === true && this.pipelining) {
        throw new Error(
          'enabling options `permitLocalInfile` and `pipelining` is not possible, options are incompatible.'
        );
      }
      this.permitLocalInfile = this.pipelining ? false : opts.permitLocalInfile || false;
    }
    this.prepareCacheLength = opts.prepareCacheLength === undefined ? 256 : opts.prepareCacheLength;
    this.restrictedAuth = opts.restrictedAuth;
    if (this.restrictedAuth != null) {
      if (!Array.isArray(this.restrictedAuth)) {
        this.restrictedAuth = this.restrictedAuth.split(',');
      }
    }

    // for compatibility with 2.x version and mysql/mysql2
    this.bigIntAsNumber = opts.bigIntAsNumber || false;
    this.insertIdAsNumber = opts.insertIdAsNumber || false;
    this.decimalAsNumber = opts.decimalAsNumber || false;
    this.supportBigNumbers = opts.supportBigNumbers || false;
    this.bigNumberStrings = opts.bigNumberStrings || false;

    if (this.maxAllowedPacket && !Number.isInteger(this.maxAllowedPacket)) {
      throw new RangeError("maxAllowedPacket must be an integer. was '" + this.maxAllowedPacket + "'");
    }
  }

  /**
   * When parsing from String, correcting type.
   *
   * @param opts options
   * @return {opts}
   */
  static parseOptionDataType(opts) {
    if (opts.bulk) opts.bulk = opts.bulk === 'true';
    if (opts.allowPublicKeyRetrieval) opts.allowPublicKeyRetrieval = opts.allowPublicKeyRetrieval === 'true';

    if (opts.insertIdAsNumber) opts.insertIdAsNumber = opts.insertIdAsNumber === 'true';
    if (opts.decimalAsNumber) opts.decimalAsNumber = opts.decimalAsNumber === 'true';
    if (opts.bigIntAsNumber) opts.bigIntAsNumber = opts.bigIntAsNumber === 'true';
    if (opts.charsetNumber && !isNaN(Number.parseInt(opts.charsetNumber))) {
      opts.charsetNumber = Number.parseInt(opts.charsetNumber);
    }
    if (opts.compress) opts.compress = opts.compress === 'true';
    if (opts.connectAttributes) opts.connectAttributes = JSON.parse(opts.connectAttributes);
    if (opts.connectTimeout) opts.connectTimeout = parseInt(opts.connectTimeout);
    if (opts.keepAliveDelay) opts.keepAliveDelay = parseInt(opts.keepAliveDelay);
    if (opts.socketTimeout) opts.socketTimeout = parseInt(opts.socketTimeout);
    if (opts.dateStrings) opts.dateStrings = opts.dateStrings === 'true';
    if (opts.debug) opts.debug = opts.debug === 'true';
    if (opts.autoJsonMap) opts.autoJsonMap = opts.autoJsonMap === 'true';
    if (opts.arrayParenthesis) opts.arrayParenthesis = opts.arrayParenthesis === 'true';

    if (opts.checkDuplicate) opts.checkDuplicate = opts.checkDuplicate === 'true';
    if (opts.debugCompress) opts.debugCompress = opts.debugCompress === 'true';
    if (opts.debugLen) opts.debugLen = parseInt(opts.debugLen);
    if (opts.prepareCacheLength) opts.prepareCacheLength = parseInt(opts.prepareCacheLength);
    if (opts.queryTimeout) opts.queryTimeout = parseInt(opts.queryTimeout);
    if (opts.foundRows) opts.foundRows = opts.foundRows === 'true';
    if (opts.maxAllowedPacket && !isNaN(Number.parseInt(opts.maxAllowedPacket)))
      opts.maxAllowedPacket = parseInt(opts.maxAllowedPacket);
    if (opts.metaAsArray) opts.metaAsArray = opts.metaAsArray === 'true';
    if (opts.metaEnumerable) opts.metaEnumerable = opts.metaEnumerable === 'true';
    if (opts.multipleStatements) opts.multipleStatements = opts.multipleStatements === 'true';
    if (opts.namedPlaceholders) opts.namedPlaceholders = opts.namedPlaceholders === 'true';
    if (opts.nestTables) opts.nestTables = opts.nestTables === 'true';
    if (opts.permitSetMultiParamEntries) opts.permitSetMultiParamEntries = opts.permitSetMultiParamEntries === 'true';
    if (opts.pipelining) opts.pipelining = opts.pipelining === 'true';
    if (opts.forceVersionCheck) opts.forceVersionCheck = opts.forceVersionCheck === 'true';
    if (opts.rowsAsArray) opts.rowsAsArray = opts.rowsAsArray === 'true';
    if (opts.trace) opts.trace = opts.trace === 'true';
    if (opts.ssl && (opts.ssl === 'true' || opts.ssl === 'false')) opts.ssl = opts.ssl === 'true';
    if (opts.bitOneIsBoolean) opts.bitOneIsBoolean = opts.bitOneIsBoolean === 'true';
    return opts;
  }

  static parse(opts) {
    const matchResults = opts.match(urlFormat);

    if (!matchResults) {
      throw new Error(
        `error parsing connection string '${opts}'. format must be 'mariadb://[<user>[:<password>]@]<host>[:<port>]/[<db>[?<opt1>=<value1>[&<opt2>=<value2>]]]'`
      );
    }
    const options = {
      user: matchResults[2] ? decodeURIComponent(matchResults[2]) : undefined,
      password: matchResults[4] ? decodeURIComponent(matchResults[4]) : undefined,
      host: matchResults[6] ? decodeURIComponent(matchResults[6]) : matchResults[6],
      port: matchResults[8] ? parseInt(matchResults[8]) : undefined,
      database: matchResults[9] ? decodeURIComponent(matchResults[9]) : matchResults[9]
    };

    const variousOptsString = matchResults[11];
    if (variousOptsString) {
      const keyValues = variousOptsString.split('&');
      keyValues.forEach(function (keyVal) {
        const equalIdx = keyVal.indexOf('=');
        if (equalIdx !== 1) {
          let val = keyVal.substring(equalIdx + 1);
          val = val ? decodeURIComponent(val) : undefined;
          options[keyVal.substring(0, equalIdx)] = val;
        }
      });
    }

    return this.parseOptionDataType(options);
  }
}

module.exports = ConnectionOptions;
